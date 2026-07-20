<?php

namespace App\Models\Models\MenuPolda\MaterialSubsidy;

use App\Models\Police\RegionalPolice;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\StockDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialSubsidy extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'subsidy_date'   => 'date',
        'confirmed_at'   => 'datetime',
        'is_active'      => 'boolean',
    ];

    // =====================
    // Relationships
    // =====================

    public function regionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class, 'regional_police_id');
    }

    public function policeStation()
    {
        return $this->belongsTo(\App\Models\Police\PoliceStation::class, 'police_station_id');
    }

    public function confirmedByUser()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function materialSubsidyDetails()
    {
        return $this->hasMany(MaterialSubsidyDetail::class);
    }

    // =====================
    // Scopes
    // =====================

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    // =====================
    // Business Methods
    // =====================

    /**
     * Generate unique subsidy code
     */
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

        $count = self::whereDate('created_at', today())->count() + 1;

        return $prefix . '-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Confirm subsidy — directly deduct Polda stock
     */
    public function confirm(User $user): void
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Hanya subsidi dengan status draft yang dapat dikonfirmasi.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
            $this->status       = 'confirmed';
            $this->confirmed_at = now();
            $this->confirmed_by = $user->id;
            $this->save();

            // Reload details with stock info
            $this->load('materialSubsidyDetails');

            foreach ($this->materialSubsidyDetails as $detail) {
                // Resolve stock_detail_id if not set yet
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
                    // Deduct quantity from stock_detail
                    $stockDetail->quantity = max(0, $stockDetail->quantity - $detail->quantity);
                    $stockDetail->save();

                    // Update parent stock aggregate
                    $stock = $stockDetail->stock;
                    if ($stock) {
                        $stock->quantity = max(0, $stock->quantity - $detail->quantity);
                        $stock->save();
                    }

                    // Save reference back to detail
                    if (!$detail->stock_detail_id) {
                        $detail->stock_detail_id = $stockDetail->id;
                        $detail->save();
                    }

                    // Create HistoryStock (out)
                    HistoryStock::create([
                        'code'                 => HistoryStock::generateCode(),
                        'last_stock_id'        => $stockDetail->stock_id ?? null,
                        'last_stock_detail_id' => $stockDetail->id,
                        'type_id'              => $detail->type_id,
                        'type_detail_id'       => $detail->type_detail_id,
                        'regional_police_id'   => $this->regional_police_id,
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
