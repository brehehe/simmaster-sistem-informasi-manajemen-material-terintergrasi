<?php

namespace App\Models\Models\MenuPolda\MutationStock;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MutationStock extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'mutation_date' => 'date',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function senderRegionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class, 'sender_regional_police_id');
    }

    public function senderPoliceStation()
    {
        return $this->belongsTo(PoliceStation::class, 'sender_police_station_id');
    }

    public function receiverRegionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class, 'receiver_regional_police_id');
    }

    public function receiverPoliceStation()
    {
        return $this->belongsTo(PoliceStation::class, 'receiver_police_station_id');
    }

    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function mutationStockDetails()
    {
        return $this->hasMany(MutationStockDetail::class);
    }

    // Business Methods

    /**
     * Generate unique mutation code
     */
    public static function generateCode($senderRegionalPoliceId = null, $senderPoliceStationId = null)
    {
        $date = now()->format('Ymd');
        $prefix = 'MUT';

        if ($senderRegionalPoliceId) {
            $regionalPolice = RegionalPolice::find($senderRegionalPoliceId);
            if ($regionalPolice) {
                $name = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $regionalPolice->name), 0, 3));
                $prefix .= '-' . $name;
            }
        } elseif ($senderPoliceStationId) {
            $policeStation = PoliceStation::find($senderPoliceStationId);
            if ($policeStation) {
                $name = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $policeStation->name), 0, 3));
                $prefix .= '-' . $name;
            }
        }

        $count = self::whereDate('created_at', today())->count() + 1;

        return $prefix . '-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Mark mutation as sent (NO stock deduction yet)
     */
    public function markAsSent()
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Only draft mutations can be marked as sent.');
        }

        $this->status = 'sent';
        $this->sent_at = now();
        $this->save();
    }

    /**
     * Mark mutation as received
     * DEDUCT stock from sender AND ADD stock to receiver
     */
    public function markAsReceived(User $user)
    {
        if ($this->status !== 'sent') {
            throw new \Exception('Only sent mutations can be marked as received.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
            $this->status = 'received';
            $this->received_at = now();
            $this->received_by = $user->id;
            $this->save();

            foreach ($this->mutationStockDetails as $detail) {
                // 1. DEDUCT from sender stock
                $stockDetail = $detail->stockDetail;
                if ($stockDetail) {
                    $stockDetail->quantity -= $detail->quantity;
                    $stockDetail->save();

                    $stock = $stockDetail->stock;
                    if ($stock) {
                        $stock->quantity -= $detail->quantity;
                        $stock->save();
                    }

                    // Create history for sender (negative = out)
                    HistoryStock::create([
                        'code' => HistoryStock::generateCode(),
                        'last_stock_id' => null,
                        'last_stock_detail_id' => $stockDetail->id,
                        'type_id' => $detail->type_id,
                        'type_detail_id' => $detail->type_detail_id,
                        'regional_police_id' => $this->sender_regional_police_id,
                        'police_station_id' => $this->sender_police_station_id,
                        'rack_id' => $stockDetail->rack_id,
                        'date' => now(),
                        'serial_number' => trim(($detail->code ?? '') . ' ' . ($detail->number_serial_first ?? '') . ' ' . ($detail->number_serial_second ?? '')),
                        'status_type' => 'out',
                        'quantity' => -$detail->quantity,
                        'description' => 'Mutasi stock keluar (Kode: ' . $this->code . ')',
                        'is_active' => true,
                    ]);
                }

                // 2. ADD to receiver stock
                $receiverStock = Stock::firstOrCreate([
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'police_station_id' => $this->receiver_police_station_id,
                    'regional_police_id' => $this->receiver_regional_police_id,
                ], [
                    'quantity' => 0,
                    'is_active' => true,
                ]);

                $receiverStock->quantity += $detail->quantity;
                $receiverStock->save();

                // Create StockDetail at receiver WITHOUT rack
                StockDetail::create([
                    'stock_id' => $receiverStock->id,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'code' => $detail->code,
                    'number_serial_first' => $detail->number_serial_first,
                    'number_serial_second' => $detail->number_serial_second,
                    'quantity' => $detail->quantity,
                    'rack_id' => null, // NO RACK initially
                    'police_station_id' => $this->receiver_police_station_id,
                    'regional_police_id' => $this->receiver_regional_police_id,
                    'is_active' => true,
                ]);

                // Create history for receiver (positive = in)
                HistoryStock::create([
                    'code' => HistoryStock::generateCode(),
                    'last_stock_id' => null,
                    'last_stock_detail_id' => null,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'regional_police_id' => $this->receiver_regional_police_id,
                    'police_station_id' => $this->receiver_police_station_id,
                    'rack_id' => null,
                    'date' => now(),
                    'serial_number' => trim(($detail->code ?? '') . ' ' . ($detail->number_serial_first ?? '') . ' ' . ($detail->number_serial_second ?? '')),
                    'status_type' => 'in',
                    'quantity' => $detail->quantity,
                    'description' => 'Mutasi stock masuk (Kode: ' . $this->code . ')',
                    'is_active' => true,
                ]);
            }
        });
    }

    /**
     * Get sender name
     */
    public function getSenderName()
    {
        if ($this->senderRegionalPolice) {
            return $this->senderRegionalPolice->name;
        }
        if ($this->senderPoliceStation) {
            return $this->senderPoliceStation->name;
        }
        return 'Unknown';
    }

    /**
     * Get receiver name
     */
    public function getReceiverName()
    {
        if ($this->receiverRegionalPolice) {
            return $this->receiverRegionalPolice->name;
        }
        if ($this->receiverPoliceStation) {
            return $this->receiverPoliceStation->name;
        }
        return 'Unknown';
    }
}
