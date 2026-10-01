<?php

namespace App\Services;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\Police\PoliceStation;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ReportingComplianceService
{
    public const TIMEZONE = 'Asia/Jakarta';

    public function status(string $date, ?string $submittedAt, ?CarbonImmutable $now = null): array
    {
        $day = CarbonImmutable::parse($date, self::TIMEZONE)->startOfDay();
        $submitted = $submittedAt ? CarbonImmutable::parse($submittedAt, config('app.timezone'))->setTimezone(self::TIMEZONE) : null;
        $reference = ($submitted ?? $now ?? CarbonImmutable::now(self::TIMEZONE))->setTimezone(self::TIMEZONE)->startOfDay();
        $delay = max(0, (int) $day->diffInDays($reference, false));
        $color = $delay >= 3 ? 'red' : ($delay >= 1 ? 'yellow' : ($submitted ? 'green' : 'pending'));

        return [
            'color' => $color,
            'delay' => $delay,
            'submitted_at' => $submitted?->format('d/m/Y H:i').'',
            'label' => $submitted ? ($delay === 0 ? 'Tertib' : 'Terlambat '.$delay.' hari') : 'Belum input',
            'reported' => $submitted !== null,
        ];
    }

    public static function sources(): array
    {
        return [
            'usage' => ['label' => 'Pemakaian', 'model' => MaterialUsage::class, 'date' => 'date', 'relation' => 'materialUsageDetails'],
            'opening' => ['label' => 'Stok awal', 'model' => \App\Models\LastStock\LastStock::class, 'date' => 'date', 'relation' => 'lastStockDetails'],
            'reception' => ['label' => 'Penerimaan', 'model' => \App\Models\Reception\Reception::class, 'date' => 'date', 'relation' => 'receptionDetails'],
            'damage' => ['label' => 'Material rusak', 'model' => \App\Models\MenuPolda\MaterialDamage\MaterialDamage::class, 'date' => 'date', 'relation' => 'materialDamageDetails'],
            'subsidy' => ['label' => 'Subsidi', 'model' => \App\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy::class, 'date' => 'subsidy_date', 'relation' => 'materialSubsidyDetails'],
            'mutation' => ['label' => 'Mutasi keluar', 'model' => \App\Models\MenuPolda\MutationStock\MutationStock::class, 'date' => 'mutation_date', 'relation' => 'mutationStockDetails', 'polres' => 'sender_police_station_id', 'polda' => 'sender_regional_police_id'],
            'shipment' => ['label' => 'Pengiriman', 'model' => \App\Models\MenuPolda\MaterialShipment\MaterialShipment::class, 'date' => 'shipment_date', 'relation' => 'materialShipmentDetails', 'polres' => null, 'polda' => 'sender_regional_police_id'],
            'opname' => ['label' => 'Stok opname', 'model' => \App\Models\StockOpname\StockOpname::class, 'date' => 'opname_date', 'relation' => 'stockOpnameDetails'],
            'rack' => ['label' => 'Penempatan rak', 'model' => \App\Models\MenuPolda\RackAssignment\RackAssignment::class, 'date' => 'date', 'relation' => 'rackAssignmentDetails'],
        ];
    }

    public function summarize(string $date, array $inputs, string $source = 'all'): array
    {
        $latest = null;
        $count = 0;
        foreach ($inputs as $input) {
            $count += $input['count'];
            if ($input['submitted_at'] && ($latest === null || $input['submitted_at'] > $latest)) {
                $latest = $input['submitted_at'];
            }
        }
        $result = $this->status($date, $latest);
        // Event-based documents do not have a daily reporting obligation.
        if (! $latest && ! in_array($source, ['all', 'usage'], true)) {
            $result['color'] = 'pending';
            $result['label'] = 'Tidak ada input';
        }

        return array_merge($result, ['report_count' => $count, 'inputs' => $inputs]);
    }

    public function rows(string $date, string $source = 'all'): Collection
    {
        $sources = self::sources();
        if ($source !== 'all') {
            if (! isset($sources[$source])) {
                throw new \InvalidArgumentException('Jenis input tidak valid.');
            }
            $sources = [$source => $sources[$source]];
        }
        $reports = [];
        foreach ($sources as $key => $definition) {
            $polres = array_key_exists('polres', $definition) ? $definition['polres'] : 'police_station_id';
            $polda = $definition['polda'] ?? 'regional_police_id';
            $query = $definition['model']::query()
                ->where($definition['date'], $date)->where('is_active', true)
                ->whereHas($definition['relation'], function ($query) use ($key) {
                    if ($key === 'usage') {
                        $query->where('is_active', true);
                    }
                })
                ->selectRaw(($polres ? $polres : 'NULL').' as station_id, '.$polda.' as region_id, MAX(created_at) as submitted_at, COUNT(*) as report_count')
                ->groupBy($polda);
            if ($polres) {
                $query->groupBy($polres);
            }
            foreach ($query->get() as $report) {
                $owner = $report->station_id ? 'polres:'.$report->station_id : 'polda:'.$report->region_id;
                $reports[$owner][$key] = ['count' => (int) $report->report_count, 'submitted_at' => $report->submitted_at];
            }
        }
        $units = \App\Models\Police\RegionalPolice::where('is_active', true)->orderBy('name')->get(['id', 'name'])
            ->map(fn ($unit) => ['id' => 'polda:'.$unit->id, 'name' => $unit->name]);
        $units = $units->concat(PoliceStation::where('is_active', true)->orderBy('name')->get(['id', 'name'])
            ->map(fn ($unit) => ['id' => 'polres:'.$unit->id, 'name' => $unit->name]));

        return $units->map(function ($unit) use ($reports, $date, $source) {
            return array_merge($unit, $this->summarize($date, $reports[$unit['id']] ?? [], $source));
        });
    }

    public function calendar(string $start, string $end): array
    {
        $first = CarbonImmutable::parse($start, self::TIMEZONE);
        $last = CarbonImmutable::parse($end, self::TIMEZONE);
        if ($first->gt($last) || $first->diffInDays($last) > 365) {
            throw new \InvalidArgumentException('Rentang maksimal 366 hari.');
        }
        $days = [];
        for ($day = $first; $day->lte($last); $day = $day->addDay()) {
            $days[] = $day->toDateString();
        }
        $required = array_keys(app(DailyMaterialUsageService::class)->catalog());
        $reports = MaterialUsage::whereBetween('date', [$start, $end])->where('is_active', true)->whereNotNull('police_station_id')
            ->get(['police_station_id', 'date'])->mapWithKeys(fn ($r) => [$r->police_station_id.':'.$r->date->toDateString() => true]);
        $coverage = [];
        $items = \Illuminate\Support\Facades\DB::table('material_usage_detail_items as items')
            ->join('material_usages as reports', 'reports.id', '=', 'items.material_usage_id')
            ->join('material_usage_details as details', 'details.id', '=', 'items.material_usage_detail_id')
            ->whereBetween('reports.date', [$start, $end])->whereNotNull('reports.police_station_id')
            ->whereNull('reports.deleted_at')->where('reports.is_active', true)
            ->whereNull('details.deleted_at')->where('details.is_active', true)
            ->whereNull('items.deleted_at')->where('items.is_active', true)->where('items.quantity', '>=', 0)
            ->select(['reports.police_station_id', 'reports.date', 'items.type_id', 'items.type_detail_id', 'items.service_id', 'items.service_detail_id'])
            ->distinct()->cursor();
        foreach ($items as $item) {
            $coverage[$item->police_station_id.':'.$item->date][DailyMaterialUsageService::key((array) $item)] = true;
        }
        $rows = PoliceStation::where('is_active', true)->orderBy('name')->get()->map(function ($station) use ($reports, $coverage, $days, $required) {
            $cells = [];
            foreach ($days as $day) {
                $ownerDay = $station->id.':'.$day;
                $reported = isset($reports[$ownerDay]);
                $complete = count($required) > 0 && ! array_diff($required, array_keys($coverage[$ownerDay] ?? []));
                $cells[$day] = ['reported' => $reported, 'complete' => $complete,
                    'label' => $complete ? 'Lengkap' : ($reported ? 'Sudah input, material belum lengkap' : 'Belum input')];
            }

            return ['id' => $station->id, 'name' => $station->name, 'cells' => $cells];
        });

        return ['days' => $days, 'rows' => $rows];
    }
}
