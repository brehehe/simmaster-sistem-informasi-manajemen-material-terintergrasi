<?php

namespace App\Services;

use App\Models\Stock\StockDetail;
use App\Models\Stock\Stock;
use App\Models\Stock\HistoryStock;
use App\Models\StockOpname\StockOpname;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockAdjustmentService
{
    public function authorizeOwner(User $user, string $scope, string $owner): void
    {
        abort_unless(in_array($scope, ['polda', 'polres'], true), 403);
        $field = $scope === 'polda' ? 'regional_police_id' : 'police_station_id';
        abort_unless($owner !== '' && ($user->hasRole('Admin') ||
            ($user->hasRole(ucfirst($scope)) && (string) $user->$field === $owner)), 403);
    }

    public function stocks(User $user, string $scope, string $owner)
    {
        $this->authorizeOwner($user, $scope, $owner);
        return StockDetail::query()->where('is_active', true)
            ->where($scope === 'polda' ? 'regional_police_id' : 'police_station_id', $owner)
            ->whereNull($scope === 'polda' ? 'police_station_id' : 'regional_police_id');
    }

    public function apply(User $user, string $scope, string $owner, string $stockId, string $expected, string $quantity, string $reason, string $token): StockOpname
    {
        $this->authorizeOwner($user, $scope, $owner);
        validator(compact('quantity', 'expected', 'reason', 'token'), [
            'quantity' => 'required|numeric|decimal:0,2|between:-999999999999,999999999999',
            'expected' => 'required|numeric', 'reason' => 'required|string|min:5|max:1000', 'token' => 'required|uuid',
        ])->validate();

        return DB::transaction(function () use ($user, $scope, $owner, $stockId, $expected, $quantity, $reason, $token) {
            $detail = $this->stocks($user, $scope, $owner)->whereKey($stockId)->lockForUpdate()->firstOrFail();
            // Re-check after locking: concurrent requests for this form return the same receipt.
            $existing = StockOpname::withTrashed()->find($token);
            if ($existing) {
                abort_unless((string) $existing->checked_by === (string) $user->id &&
                    $existing->stockOpnameDetails()->where('stock_detail_id', $stockId)->exists(), 403);
                return $existing;
            }
            if (round((float) $detail->quantity * 100) !== round((float) $expected * 100)) {
                throw ValidationException::withMessages(['quantity' => 'Stok telah berubah. Pilih ulang material untuk memuat saldo terbaru sebelum menyimpan.']);
            }
            $difference = round((float) $quantity - (float) $detail->quantity, 2);
            if ($difference == 0) {
                throw ValidationException::withMessages(['quantity' => 'Saldo akhir sama dengan saldo saat ini. Tidak ada penyesuaian.']);
            }
            $stock = Stock::whereKey($detail->stock_id)->lockForUpdate()->firstOrFail();
            abort_unless($stock->police_station_id === $detail->police_station_id && $stock->regional_police_id === $detail->regional_police_id, 403);
            $record = new StockOpname([
                'code' => 'ADJ-'.$token, 'opname_date' => today()->toDateString(),
                'regional_police_id' => $detail->regional_police_id, 'police_station_id' => $detail->police_station_id,
                'status' => 'approved', 'notes' => 'Penyesuaian saldo: '.$reason,
                'checked_by' => $user->id, 'approved_by' => $user->id, 'approved_at' => now(), 'is_active' => true,
            ]);
            $record->id = $token;
            $record->save();
            $record->stockOpnameDetails()->create([
                'stock_detail_id' => $detail->id, 'type_id' => $detail->type_id, 'type_detail_id' => $detail->type_detail_id,
                'rack_id' => $detail->rack_id, 'code' => $detail->code ?? '',
                'number_serial_first' => $detail->number_serial_first, 'number_serial_second' => $detail->number_serial_second,
                'system_quantity' => $detail->quantity, 'physical_quantity' => $quantity, 'notes' => $reason, 'is_active' => true,
            ]);
            HistoryStock::create([
                'code' => HistoryStock::generateCode(), 'type_id' => $detail->type_id, 'type_detail_id' => $detail->type_detail_id,
                'regional_police_id' => $detail->regional_police_id, 'police_station_id' => $detail->police_station_id,
                'rack_id' => $detail->rack_id, 'date' => today()->toDateString(),
                'serial_number' => trim($detail->code.' '.$detail->number_serial_first.' '.$detail->number_serial_second),
                'status_type' => $difference > 0 ? 'in' : 'out', 'quantity' => abs($difference),
                'description' => $record->code.' | Saldo '.$detail->quantity.' → '.$quantity.' | '.$reason, 'is_active' => true,
            ]);
            $detail->update(['quantity' => $quantity]);
            $stock->increment('quantity', $difference);
            return $record;
        }, 3);
    }
}
