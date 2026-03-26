<?php

namespace App\Models\Models\MenuPolda\MaterialShipment;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialShipment extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'ship ment_date' => 'date',
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

    // Business Methods

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
                // Extract first 3 letters of regional police name
                $name = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $regionalPolice->name), 0, 3));
                $prefix .= '-' . $name;
            }
        }

        // Count today's shipments for sequence
        $count = self::whereDate('created_at', today())->count() + 1;

        return $prefix . '-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Mark shipment as shipped (NO stock deduction yet)
     * Stock will be deducted when Polres confirms receipt
     */
    public function markAsShipped()
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Only draft shipments can be marked as shipped.');
        }

        // Only update status, NO stock changes
        $this->status = 'shipped';
        $this->shipped_at = now();
        $this->save();
    }

    /**
     * Mark shipment as received
     * DEDUCT stock from Polda AND ADD stock to Polres
     */
    public function markAsReceived(User $user)
    {
        if ($this->status !== 'shipped') {
            throw new \Exception('Only shipped shipments can be marked as received.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
            // Update status
            $this->status = 'received';
            $this->received_at = now();
            $this->received_by = $user->id;
            $this->save();

            foreach ($this->materialShipmentDetails as $detail) {
                // 1. DEDUCT from Polda stock (happening NOW on receive confirmation)
                $stockDetail = $detail->stockDetail;
                if ($stockDetail) {
                    // Deduct from stock_detail
                    $stockDetail->quantity -= $detail->quantity;
                    $stockDetail->save();

                    // Update parent Stock aggregate
                    $stock = $stockDetail->stock;
                    if ($stock) {
                        $stock->quantity -= $detail->quantity;
                        $stock->save();
                    }

                    // Create history_stock entry for Polda (negative = out)
                    HistoryStock::create([
                        'code' => HistoryStock::generateCode(),
                        'last_stock_id' => null,
                        'last_stock_detail_id' => $stockDetail->id,
                        'type_id' => $detail->type_id,
                        'type_detail_id' => $detail->type_detail_id,
                        'regional_police_id' => $this->sender_regional_police_id,
                        'police_station_id' => null,
                        'rack_id' => $stockDetail->rack_id,
                        'date' => now(),
                        'serial_number' => trim(($detail->code ?? '') . ' ' . ($detail->number_serial_first ?? '') . ' ' . ($detail->number_serial_second ?? '')),
                        'status_type' => 'out',
                        'quantity' => -$detail->quantity,
                        'description' => 'Pengiriman material ke ' . $this->receiverPoliceStation->name . ' (Kode: ' . $this->code . ')',
                        'is_active' => true,
                    ]);
                }

                // Resolve service_id / service_detail_id from source stock
                $serviceId = $stockDetail?->service_id ?? null;
                $serviceDetailId = $stockDetail?->service_detail_id ?? null;

                // 2. ADD to Polres stock
                $polresStock = Stock::firstOrCreate([
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'service_id' => $serviceId,
                    'service_detail_id' => $serviceDetailId,
                    'police_station_id' => $this->receiver_police_station_id,
                    'regional_police_id' => null,
                ], [
                    'quantity' => 0,
                    'is_active' => true,
                ]);

                $polresStock->quantity += $detail->quantity;
                $polresStock->save();

                // Create StockDetail at Polres WITHOUT rack
                $newStockDetail = StockDetail::create([
                    'stock_id' => $polresStock->id,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'service_id' => $serviceId,
                    'service_detail_id' => $serviceDetailId,
                    'code' => $detail->code,
                    'number_serial_first' => $detail->number_serial_first,
                    'number_serial_second' => $detail->number_serial_second,
                    'quantity' => $detail->quantity,
                    'rack_id' => null, // NO RACK initially
                    'police_station_id' => $this->receiver_police_station_id,
                    'regional_police_id' => null,
                    'is_active' => true,
                ]);

                // Create history_stock entry for Polres (positive = in)
                HistoryStock::create([
                    'code' => HistoryStock::generateCode(),
                    'last_stock_id' => $polresStock->id,
                    'last_stock_detail_id' => $newStockDetail->id,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'regional_police_id' => null,
                    'police_station_id' => $this->receiver_police_station_id,
                    'rack_id' => null,
                    'date' => now(),
                    'serial_number' => trim(($detail->code ?? '') . ' ' . ($detail->number_serial_first ?? '') . ' ' . ($detail->number_serial_second ?? '')),
                    'status_type' => 'in',
                    'quantity' => $detail->quantity,
                    'description' => 'Penerimaan material dari ' . $this->senderRegionalPolice->name . ' (Kode: ' . $this->code . ')',
                    'is_active' => true,
                ]);
            }
        });
    }

    /**
     * Get barcode as base64 encoded image
     * Note: Requires picqer/php-barcode-generator package
     */
    public function getBarcode()
    {
        // For now, return code as text (barcode library will be added later)
        return $this->code;
    }
}
