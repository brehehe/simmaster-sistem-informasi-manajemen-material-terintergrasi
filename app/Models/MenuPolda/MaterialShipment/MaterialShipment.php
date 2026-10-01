<?php

namespace App\Models\MenuPolda\MaterialShipment;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MaterialShipment extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'shipment_date' => 'date',
        'shipped_at' => 'datetime',
        'received_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function senderRegionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class, 'sender_regional_police_id');
    }

    public function receiverPoliceStation()
    {
        return $this->belongsTo(PoliceStation::class, 'receiver_police_station_id');
    }

    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function materialShipmentDetails()
    {
        return $this->hasMany(MaterialShipmentDetail::class);
    }

    /**
     * Generate unique shipment code
     */
    public static function generateCode($regionalPoliceId = null)
    {
        $date = now()->format('Ymd');
        $prefix = 'SHP';

        if ($regionalPoliceId) {
            $regionalPolice = RegionalPolice::withTrashed()->find($regionalPoliceId);
            if ($regionalPolice) {
                $name = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $regionalPolice->name), 0, 3));
                $prefix .= '-'.$name;
            }
        }

        $fullPrefix = $prefix.'-'.$date.'-';
        $existingCodes = self::withTrashed()
            ->where('code', 'ilike', $fullPrefix.'%')
            ->pluck('code')
            ->map(function ($c) {
                if (preg_match('/-(\d+)$/', trim((string) $c), $matches)) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->filter()
            ->toArray();

        $nextNumber = ! empty($existingCodes) ? (max($existingCodes) + 1) : 1;
        $code = $fullPrefix.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        while (self::withTrashed()->where('code', $code)->exists()) {
            $nextNumber++;
            $code = $fullPrefix.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    /**
     * Mark shipment as shipped
     */
    public function markAsShipped()
    {
        DB::transaction(function () {
            $locked = self::whereKey($this->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== 'draft') {
                throw new \RuntimeException('Hanya draft dapat dikirim.');
            }
            app(\App\Services\ShipmentStockService::class)->deduct($locked);
            $locked->update(['status' => 'shipped', 'shipped_at' => now()]);
        });
        $this->refresh();
    }

    public function recordPicking(User $user, array $data): void
    {
        abort_unless($user->hasRole(['Admin', 'Polda', 'Warehouse']) && $user->can('view', $this), 403);
        validator($data, [
            'picker_name' => 'required|string|max:200', 'picker_rank' => 'required|string|max:100',
            'picker_position' => 'required|string|max:200', 'picker_signature' => ['required', 'string', 'max:1000000', 'regex:~^data:image/png;base64,[A-Za-z0-9+/=]+$~'],
        ])->validate();
        $bytes = base64_decode(explode(',', $data['picker_signature'], 2)[1], true);
        if (! $bytes || ! @getimagesizefromstring($bytes)) {
            throw new \RuntimeException('Tanda tangan tidak valid.');
        }
        DB::transaction(function () use ($data) {
            $shipment = self::whereKey($this->id)->lockForUpdate()->firstOrFail();
            if (! in_array($shipment->status, ['draft', 'shipped']) || $shipment->picked_at) {
                throw new \RuntimeException('SPPM sudah diproses warehouse atau diterima.');
            }
            app(\App\Services\ShipmentStockService::class)->deduct($shipment);
            $shipment->update(array_intersect_key($data, array_flip(['picker_name', 'picker_rank', 'picker_position', 'picker_signature', 'picker_photo'])) + [
                'status' => 'shipped', 'shipped_at' => $shipment->shipped_at ?? now(), 'picked_at' => now(),
            ]);
        });
        $this->refresh();
    }

    /**
     * Mark shipment as received
     */
    public function markAsReceived(User $user)
    {
        DB::transaction(function () use ($user) {
            $locked = self::whereKey($this->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== 'shipped' || ! $locked->picked_at) {
                throw new \RuntimeException('SPPM harus dipindai warehouse sebelum diterima.');
            }
            if (! $user->hasRole('Admin') && $user->police_station_id !== $locked->receiver_police_station_id) {
                abort(403);
            }
            PoliceStation::whereKey($locked->receiver_police_station_id)->lockForUpdate()->firstOrFail();
            // Legacy dispatched documents may not have deducted stock yet.
            app(\App\Services\ShipmentStockService::class)->deduct($locked);
            $this->setRawAttributes($locked->getAttributes());
            $this->unsetRelation('materialShipmentDetails');
            foreach ($this->materialShipmentDetails as $detail) {
                $poldaStockDetail = $detail->stock_detail_id ? StockDetail::find($detail->stock_detail_id) : null;

                // 2. ADD stock to Polres
                $polresStock = Stock::firstOrCreate(
                    [
                        'police_station_id' => $this->receiver_police_station_id,
                        'type_id' => $detail->type_id,
                        'type_detail_id' => $detail->type_detail_id,
                        'service_id' => $poldaStockDetail?->service_id,
                        'service_detail_id' => $poldaStockDetail?->service_detail_id,
                    ],
                    [
                        'quantity' => 0,
                        'regional_police_id' => null,
                        'is_active' => true,
                    ]
                );

                $polresStock->increment('quantity', $detail->quantity);

                StockDetail::create([
                    'stock_id' => $polresStock->id,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'service_id' => $poldaStockDetail?->service_id,
                    'service_detail_id' => $poldaStockDetail?->service_detail_id,
                    'police_station_id' => $this->receiver_police_station_id,
                    'regional_police_id' => null,
                    'rack_id' => null,
                    'code' => $detail->code,
                    'number_serial_first' => $detail->number_serial_first,
                    'number_serial_second' => $detail->number_serial_second,
                    'quantity' => $detail->quantity,
                    'description' => "Received from Polda shipment: {$this->code}",
                    'is_active' => true,
                ]);

                // 3. Create history records
                HistoryStock::create([
                    'code' => HistoryStock::generateCode(),
                    'material_shipment_id' => $this->id,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'service_id' => $poldaStockDetail?->service_id,
                    'service_detail_id' => $poldaStockDetail?->service_detail_id,
                    'regional_police_id' => null,
                    'police_station_id' => $this->receiver_police_station_id,
                    'date' => now(),
                    'status_type' => 'in',
                    'quantity' => $detail->quantity,
                    'description' => "Received from Polda shipment: {$this->code}",
                    'is_active' => true,
                ]);
            }

            $this->status = 'received';
            $this->received_at = now();
            $this->received_by = $user->id;
            $this->save();
        });
    }

    /**
     * Delete shipment with stock reversal and without creating new history records.
     */
    public function deleteWithStockReversal(): void
    {
        if ($this->status !== 'draft' || $this->stock_deducted_at) {
            throw new \RuntimeException('SPPM yang sudah dikirim tidak dapat dihapus. Gunakan koreksi stok dengan jejak audit.');
        }
        DB::transaction(function () {
            $locked = self::whereKey($this->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== 'draft' || $locked->stock_deducted_at) {
                throw new \RuntimeException('SPPM sudah diproses.');
            }
            // Cleanup any uploaded picking photo
            if ($this->picker_photo && Storage::disk('public')->exists($this->picker_photo)) {
                Storage::disk('public')->delete($this->picker_photo);
            }

            // Delete shipment details and shipment header
            $this->materialShipmentDetails()->forceDelete();
            $this->forceDelete();
        });
    }
}
