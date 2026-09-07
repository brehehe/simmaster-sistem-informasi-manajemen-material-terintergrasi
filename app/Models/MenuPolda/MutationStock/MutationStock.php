<?php

namespace App\Models\MenuPolda\MutationStock;

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

    public static function generateCode($senderRegionalPoliceId = null, $senderPoliceStationId = null)
    {
        $date = now()->format('Ymd');
        $prefix = 'MUT';

        if ($senderRegionalPoliceId) {
            $regionalPolice = RegionalPolice::withTrashed()->find($senderRegionalPoliceId);
            if ($regionalPolice) {
                $name = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $regionalPolice->name), 0, 3));
                $prefix .= '-' . $name;
            }
        } elseif ($senderPoliceStationId) {
            $policeStation = PoliceStation::withTrashed()->find($senderPoliceStationId);
            if ($policeStation) {
                $name = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $policeStation->name), 0, 3));
                $prefix .= '-' . $name;
            }
        }

        $fullPrefix = $prefix . '-' . $date . '-';
        $lastRecord = self::withTrashed()
            ->where('code', 'like', $fullPrefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastRecord && preg_match('/-(\d+)$/', $lastRecord->code, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        $code = $fullPrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        while (self::withTrashed()->where('code', $code)->exists()) {
            $nextNumber++;
            $code = $fullPrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function markAsSent()
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Only draft mutations can be marked as sent.');
        }

        $this->status = 'sent';
        $this->sent_at = now();
        $this->save();
    }

    public function markAsReceived(User $user)
    {
        if ($this->status !== 'sent') {
            throw new \Exception('Only sent mutations can be marked as received.');
        }

        DB::transaction(function () use ($user) {
            $this->status = 'received';
            $this->received_at = now();
            $this->received_by = $user->id;
            $this->save();

            $senderRegId = $this->sender_police_station_id ? null : $this->sender_regional_police_id;
            $receiverRegId = $this->receiver_police_station_id ? null : $this->receiver_regional_police_id;

            foreach ($this->mutationStockDetails as $detail) {
                $stockDetail = $detail->stockDetail;
                if ($stockDetail) {
                    $stockDetail->quantity -= $detail->quantity;
                    $stockDetail->save();

                    $stock = $stockDetail->stock;
                    if ($stock) {
                        $stock->quantity -= $detail->quantity;
                        $stock->save();
                    }

                    HistoryStock::create([
                        'code' => HistoryStock::generateCode(),
                        'last_stock_id' => null,
                        'last_stock_detail_id' => $stockDetail->id,
                        'type_id' => $detail->type_id,
                        'type_detail_id' => $detail->type_detail_id,
                        'regional_police_id' => $senderRegId,
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

                $receiverStock = Stock::firstOrCreate([
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'police_station_id' => $this->receiver_police_station_id,
                    'regional_police_id' => $receiverRegId,
                ], [
                    'quantity' => 0,
                    'is_active' => true,
                ]);

                $receiverStock->quantity += $detail->quantity;
                $receiverStock->save();

                StockDetail::create([
                    'stock_id' => $receiverStock->id,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'code' => $detail->code,
                    'number_serial_first' => $detail->number_serial_first,
                    'number_serial_second' => $detail->number_serial_second,
                    'quantity' => $detail->quantity,
                    'rack_id' => null,
                    'police_station_id' => $this->receiver_police_station_id,
                    'regional_police_id' => $receiverRegId,
                    'is_active' => true,
                ]);

                HistoryStock::create([
                    'code' => HistoryStock::generateCode(),
                    'last_stock_id' => null,
                    'last_stock_detail_id' => null,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'regional_police_id' => $receiverRegId,
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
