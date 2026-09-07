<?php

namespace App\Models\MenuPolda\MaterialSubsidy;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\StockDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MaterialSubsidy extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'subsidy_date'   => 'date',
        'confirmed_at'   => 'datetime',
        'is_active'      => 'boolean',
    ];

    public function regionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class, 'regional_police_id');
    }

    public function policeStation()
    {
        return $this->belongsTo(PoliceStation::class, 'police_station_id');
    }

    public function confirmedByUser()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function materialSubsidyDetails()
    {
        return $this->hasMany(MaterialSubsidyDetail::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public static function generateCode($regionalPoliceId = null): string
    {
        $date = now()->format('Ymd');
        $prefix = 'SUB';

        if ($regionalPoliceId) {
            $regionalPolice = RegionalPolice::withTrashed()->find($regionalPoliceId);
            if ($regionalPolice) {
                $name = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $regionalPolice->name), 0, 3));
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

    public function confirm(User $user): void
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Hanya subsidi dengan status draft yang dapat dikonfirmasi.');
        }

        DB::transaction(function () use ($user) {
            $this->status       = 'confirmed';
            $this->confirmed_at = now();
            $this->confirmed_by = $user->id;
            $this->save();

            $this->load('materialSubsidyDetails');

            foreach ($this->materialSubsidyDetails as $detail) {
                $stockDetail = $detail->stock_detail_id
                    ? StockDetail::find($detail->stock_detail_id)
                    : StockDetail::where('type_id', $detail->type_id)
                        ->when($detail->type_detail_id, fn($q) => $q->where('type_detail_id', $detail->type_detail_id))
                        ->whereHas('stock', function ($q) {
                            if ($this->police_station_id) {
                                $q->where('police_station_id', $this->police_station_id);
                            } else {
                                $q->where('regional_police_id', $this->regional_police_id)->whereNull('police_station_id');
                            }
                        })
                        ->where('quantity', '>', 0)
                        ->first();

                if ($stockDetail) {
                    $stockDetail->quantity = max(0, $stockDetail->quantity - $detail->quantity);
                    $stockDetail->save();

                    $stock = $stockDetail->stock;
                    if ($stock) {
                        $stock->quantity = max(0, $stock->quantity - $detail->quantity);
                        $stock->save();
                    }

                    if (!$detail->stock_detail_id) {
                        $detail->stock_detail_id = $stockDetail->id;
                        $detail->save();
                    }

                    HistoryStock::create([
                        'code'                 => HistoryStock::generateCode(),
                        'last_stock_id'        => $stockDetail->stock_id ?? null,
                        'last_stock_detail_id' => $stockDetail->id,
                        'type_id'              => $detail->type_id,
                        'type_detail_id'       => $detail->type_detail_id,
                        'regional_police_id'   => $this->police_station_id ? null : $this->regional_police_id,
                        'police_station_id'    => $this->police_station_id,
                        'rack_id'              => $stockDetail->rack_id ?? null,
                        'date'                 => now(),
                        'serial_number'        => null,
                        'status_type'          => 'out',
                        'quantity'             => -abs($detail->quantity),
                        'description'          => 'Subsidi Silang ke ' . $this->recipient_name . ' (Kode: ' . $this->code . ')',
                        'is_active'            => true,
                    ]);
                }
            }
        });
    }
}
