<?php

namespace App\Services;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\Police\PoliceStation;
use App\Models\Service\Service;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DailyMaterialUsageService
{
    public function catalog(?array $allowedTypes = null): array
    {
        $types = Type::with(['typeDetails' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->where('is_active', true)->when($allowedTypes, fn ($q) => $q->whereIn('id', $allowedTypes))->orderBy('name')->get();
        $services = Service::with(['details' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->where('is_active', true)->orderBy('name')->get();
        $rows = [];
        foreach ($types as $type) {
            $typeServices = $services->filter(fn ($s) => $s->type_id === $type->id || $type->typeDetails->contains('id', $s->type_detail_id));
            foreach ($typeServices as $service) {
                foreach ($service->details->isEmpty() ? [null] : $service->details as $sub) {
                    $row = ['type_id' => $type->id, 'type_detail_id' => $service->type_detail_id,
                        'service_id' => $service->id, 'service_detail_id' => $sub?->id,
                        'label' => $type->name.' / '.$service->name.($sub ? ' / '.$sub->name : ''), 'unit' => $type->unit ?? 'Unit'];
                    $rows[self::key($row)] = $row;
                }
            }
            foreach ($type->typeDetails as $detail) {
                if ($typeServices->contains('type_detail_id', $detail->id)) {
                    continue;
                }
                $row = ['type_id' => $type->id, 'type_detail_id' => $detail->id, 'service_id' => null,
                    'service_detail_id' => null, 'label' => $type->name.' / '.$detail->name, 'unit' => $detail->unit ?? $type->unit ?? 'Unit'];
                $rows[self::key($row)] = $row;
            }
            if ($typeServices->isEmpty() && $type->typeDetails->isEmpty()) {
                $row = ['type_id' => $type->id, 'type_detail_id' => null, 'service_id' => null,
                    'service_detail_id' => null, 'label' => $type->name, 'unit' => $type->unit ?? 'Unit'];
                $rows[self::key($row)] = $row;
            }
        }

        return $rows;
    }

    public static function key(array $row): string
    {
        return implode('_', array_map(fn ($field) => $row[$field] ?? 'none', ['type_id', 'type_detail_id', 'service_id', 'service_detail_id']));
    }

    public function save(User $user, string $stationId, string $date, array $quantities, string $description = '', ?string $id = null): MaterialUsage
    {
        abort_unless($user->hasRole('Admin') || ($user->hasRole('Polres') && $user->police_station_id === $stationId), 403);
        $catalog = $this->catalog($user->hasRole('Admin') ? null : $user->userType?->types);
        $rules = ['date' => 'required|date_format:Y-m-d|before_or_equal:'.now('Asia/Jakarta')->toDateString(), 'quantities' => 'required|array'];
        foreach ($catalog as $key => $row) {
            $rules['quantities.'.$key] = 'required|integer|min:0|max:999999999';
        }
        validator(compact('date', 'quantities'), $rules, ['required' => 'Semua jumlah wajib diisi, termasuk 0 jika tidak digunakan.'])->validate();
        if (array_diff(array_keys($quantities), array_keys($catalog)) || ! $catalog) {
            throw ValidationException::withMessages(['quantities' => 'Daftar material berubah. Muat ulang halaman.']);
        }

        return DB::transaction(function () use ($user, $stationId, $date, $quantities, $description, $id, $catalog) {
            // Serialize reports for this station, including edits and simultaneous submissions.
            PoliceStation::whereKey($stationId)->where('is_active', true)->lockForUpdate()->firstOrFail();
            $usage = $id ? MaterialUsage::whereKey($id)->lockForUpdate()->firstOrFail() : new MaterialUsage;
            if ($id) {
                abort_unless($user->can('update', $usage) && $usage->police_station_id === $stationId, 403);
                foreach ($usage->materialUsageDetails as $oldDetail) {
                    foreach ($oldDetail->materialUsageDetailItems as $oldItem) {
                        if (! isset($catalog[self::key($oldItem->toArray())])) {
                            throw ValidationException::withMessages(['quantities' => 'Laporan memuat material di luar daftar aktif/akses Anda. Hubungi admin untuk koreksi.']);
                        }
                    }
                }
                app(StockService::class)->deleteMaterialUsage($usage);
                foreach ($usage->materialUsageDetails as $detail) {
                    $detail->materialUsageDetailItems()->delete();
                }
                $usage->materialUsageDetails()->delete();
            }
            // A user may report a subset of materials; prevent duplicate coverage for the same day.
            $existing = MaterialUsage::where('police_station_id', $stationId)->whereDate('date', $date)
                ->where('is_active', true)->when($id, fn ($q) => $q->where('id', '!=', $id))
                ->with('materialUsageDetails.materialUsageDetailItems')->get();
            foreach ($existing as $report) {
                foreach ($report->materialUsageDetails as $detail) {
                    foreach ($detail->materialUsageDetailItems as $item) {
                        if (isset($catalog[self::key($item->toArray())])) {
                            throw ValidationException::withMessages(['date' => 'Penggunaan untuk material pada tanggal ini sudah ada. Edit laporan yang sudah tersimpan.']);
                        }
                    }
                }
            }
            $usage->fill(['code' => $id ? $usage->code : MaterialUsage::generateCode(), 'date' => $date,
                'police_station_id' => $stationId, 'regional_police_id' => null, 'description' => $description,
                'is_active' => true, 'reporting_complete' => true, 'reporting_keys' => array_keys($catalog)])->save();
            foreach ($catalog as $key => $row) {
                $remaining = (int) $quantities[$key];
                $stocks = $remaining === 0 ? collect() : StockDetail::where('police_station_id', $stationId)->whereNull('regional_police_id')
                    ->where('type_id', $row['type_id'])->where('is_active', true)->where('quantity', '>', 0)
                    ->where(function ($q) use ($row) {
                        $q->where('type_detail_id', $row['type_detail_id']);
                        if ($row['type_detail_id']) {
                            $q->orWhereNull('type_detail_id');
                        }
                    })
                    ->where(function ($q) use ($row) {
                        $q->where('service_id', $row['service_id']);
                        if ($row['service_id']) {
                            $q->orWhereNull('service_id');
                        }
                    })
                    ->where(function ($q) use ($row) {
                        $q->where('service_detail_id', $row['service_detail_id']);
                        if ($row['service_detail_id']) {
                            $q->orWhereNull('service_detail_id');
                        }
                    })
                    ->orderBy('created_at')->orderBy('id')->lockForUpdate()->get();
                if ($remaining > $stocks->sum('quantity')) {
                    throw ValidationException::withMessages(['quantities.'.$key => 'Stok tidak mencukupi untuk '.$row['label'].'.']);
                }
                $allocations = [];
                if ($remaining === 0) {
                    $allocations[] = [null, 0];
                }
                foreach ($stocks as $stock) {
                    if ($remaining === 0) {
                        break;
                    }
                    $take = min($remaining, (int) $stock->quantity);
                    $allocations[] = [$stock, $take];
                    $remaining -= $take;
                }
                foreach ($allocations as [$stock, $quantity]) {
                    $data = ['type_id' => $row['type_id'], 'type_detail_id' => $row['type_detail_id'],
                        'stock_detail_id' => $stock?->id, 'rack_id' => $stock?->rack_id, 'item_code' => $stock?->code,
                        'number_serial_first' => $stock?->number_serial_first, 'number_serial_second' => $stock?->number_serial_second,
                        'quantity' => $quantity, 'usage_type' => 'Material Digunakan', 'is_active' => true];
                    $detail = $usage->materialUsageDetails()->create($data);
                    $detail->materialUsageDetailItems()->create($data + ['material_usage_id' => $usage->id,
                        'service_id' => $row['service_id'], 'service_detail_id' => $row['service_detail_id']]);
                    // Deduct immediately so another service cannot reuse the same batch capacity.
                    $single = clone $usage;
                    $single->setRelation('materialUsageDetails', collect([$detail]));
                    app(StockService::class)->processMaterialUsage($single);
                }
            }

            return $usage->refresh();
        });
    }
}
