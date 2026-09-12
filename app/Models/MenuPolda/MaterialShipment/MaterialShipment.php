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
                $prefix .= '-' . $name;
            }
        }

        $fullPrefix = $prefix . '-' . $date . '-';
        $existingCodes = self::withTrashed()
            ->where('code', 'ilike', $fullPrefix . '%')
            ->pluck('code')
            ->map(function ($c) {
                if (preg_match('/-(\d+)$/', trim((string)$c), $matches)) {
                    return (int) $matches[1];
                }
                return 0;
            })
            ->filter()
            ->toArray();

        $nextNumber = !empty($existingCodes) ? (max($existingCodes) + 1) : 1;
        $code = $fullPrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        while (self::withTrashed()->where('code', $code)->exists()) {
            $nextNumber++;
            $code = $fullPrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    /**
     * Mark shipment as shipped
     */
    public function markAsShipped()
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Only draft shipments can be marked as shipped.');
        }

        $this->status = 'shipped';
        $this->shipped_at = now();
        $this->save();
    }

    /**
     * Mark shipment as received
     */
    public function markAsReceived(User $user)
    {
        if ($this->status !== 'shipped') {
            throw new \Exception('Only shipped shipments can be marked as received.');
        }

        DB::transaction(function () use ($user) {
            foreach ($this->materialShipmentDetails as $detail) {
                // 1. DEDUCT stock from Polda
                $poldaStock = Stock::where('regional_police_id', $this->sender_regional_police_id)
                    ->where('type_id', $detail->type_id)
                    ->where('type_detail_id', $detail->type_detail_id)
                    ->first();

                if ($poldaStock) {
                    $poldaStock->quantity -= $detail->quantity;
                    $poldaStock->save();
                }

                if ($detail->stock_detail_id) {
                    $poldaStockDetail = StockDetail::find($detail->stock_detail_id);
                    if ($poldaStockDetail) {
                        $poldaStockDetail->quantity -= $detail->quantity;
                        $poldaStockDetail->save();
                    }
                }

                // 2. ADD stock to Polres
                $polresStock = Stock::firstOrCreate(
                    [
                        'police_station_id' => $this->receiver_police_station_id,
                        'type_id' => $detail->type_id,
                        'type_detail_id' => $detail->type_detail_id,
                    ],
                    [
                        'quantity' => 0,
                        'regional_police_id' => null,
                        'is_active' => true,
                    ]
                );

                $polresStock->quantity += $detail->quantity;
                $polresStock->save();

                $poldaStockDetail = $detail->stock_detail_id ? StockDetail::find($detail->stock_detail_id) : null;

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
                    'regional_police_id' => $this->sender_regional_police_id,
                    'police_station_id' => null,
                    'date' => now(),
                    'status_type' => 'out',
                    'quantity' => -$detail->quantity,
                    'description' => "Shipment to Polres ({$this->receiverPoliceStation?->name}): {$this->code}",
                    'is_active' => true,
                ]);

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
        DB::transaction(function () {
            // Revert stock if shipment was received by Polres
            if ($this->status === 'received') {
                foreach ($this->materialShipmentDetails as $detail) {
                    // 1. Return deducted stock back to Polda
                    $poldaStock = Stock::where('regional_police_id', $this->sender_regional_police_id)
                        ->where('type_id', $detail->type_id)
                        ->where(function ($q) use ($detail) {
                            if ($detail->type_detail_id) {
                                $q->where('type_detail_id', $detail->type_detail_id);
                            } else {
                                $q->whereNull('type_detail_id');
                            }
                        })
                        ->first();

                    if ($poldaStock) {
                        $poldaStock->quantity += $detail->quantity;
                        $poldaStock->save();
                    }

                    if ($detail->stock_detail_id) {
                        $poldaStockDetail = StockDetail::find($detail->stock_detail_id);
                        if ($poldaStockDetail) {
                            $poldaStockDetail->quantity += $detail->quantity;
                            $poldaStockDetail->save();
                        }
                    }

                    // 2. Deduct added stock from Polres
                    $polresStock = Stock::where('police_station_id', $this->receiver_police_station_id)
                        ->where('type_id', $detail->type_id)
                        ->where(function ($q) use ($detail) {
                            if ($detail->type_detail_id) {
                                $q->where('type_detail_id', $detail->type_detail_id);
                            } else {
                                $q->whereNull('type_detail_id');
                            }
                        })
                        ->first();

                    if ($polresStock) {
                        $polresStock->quantity = max(0, $polresStock->quantity - $detail->quantity);
                        $polresStock->save();
                    }

                    // Remove the stock detail that was created at Polres on receive
                    StockDetail::where('police_station_id', $this->receiver_police_station_id)
                        ->where('type_id', $detail->type_id)
                        ->where(function ($q) use ($detail) {
                            if ($detail->type_detail_id) {
                                $q->where('type_detail_id', $detail->type_detail_id);
                            } else {
                                $q->whereNull('type_detail_id');
                            }
                        })
                        ->where('description', 'like', "%{$this->code}%")
                        ->forceDelete();
                }

                // Delete any HistoryStock associated with this shipment code (clean reversal without creating new history)
                HistoryStock::where('description', 'like', "%{$this->code}%")->forceDelete();
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
