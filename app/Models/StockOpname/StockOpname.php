<?php

namespace App\Models\StockOpname;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpname extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'code',
        'opname_date',
        'regional_police_id',
        'police_station_id',
        'status',
        'notes',
        'checked_by',
        'approved_by',
        'approved_at',
        'is_active',
    ];

    protected $casts = [
        'opname_date' => 'date',
        'approved_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Generate unique code for stock opname
     */
    public static function generateCode($isPolda = true): string
    {
        $prefix = $isPolda ? 'SO-POLDA' : 'SO-POLRES';
        $date = today()->format('ymd');

        // Get all codes for today to find the highest number
        $existingCodes = static::where('code', 'like', "{$prefix}-{$date}-%")
            ->pluck('code')
            ->map(function ($code) {
                // Trim whitespace and extract number
                $code = trim($code);
                $parts = explode('-', $code);
                return isset($parts[3]) ? (int) $parts[3] : 0;
            })
            ->filter()
            ->toArray();

        // Find next available number
        $newNumber = 1;
        if (!empty($existingCodes)) {
            $newNumber = max($existingCodes) + 1;
        }

        $code = "{$prefix}-{$date}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        // Double-check uniqueness (safety measure)
        $counter = 0;
        while (static::where('code', $code)->exists() && $counter < 100) {
            $newNumber++;
            $code = "{$prefix}-{$date}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $code;
    }

    /**
     * Mark opname as completed
     */
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();
    }

    /**
     * Approve opname and adjust stock
     */
    public function approveAndAdjustStock(User $user)
    {
        if ($this->status !== 'completed') {
            throw new \Exception('Only completed opnames can be approved.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
            // Update approval info
            $this->status = 'approved';
            $this->approved_by = $user->id;
            $this->approved_at = now();
            $this->save();

            // Adjust stock based on physical quantity
            foreach ($this->stockOpnameDetails as $detail) {
                if ($detail->difference != 0 && $detail->stockDetail) {
                    $stockDetail = $detail->stockDetail;

                    // Adjust quantity
                    $stockDetail->quantity = $detail->physical_quantity;
                    $stockDetail->save();

                    // Update parent stock
                    $stock = $stockDetail->stock;
                    if ($stock) {
                        $stock->quantity += $detail->difference;
                        $stock->save();
                    }

                    // Create history_stock entry for adjustment
                    \App\Models\Stock\HistoryStock::create([
                        'code' => \App\Models\Stock\HistoryStock::generateCode(),
                        'last_stock_id' => null,
                        'last_stock_detail_id' => $stockDetail->id,
                        'type_id' => $detail->type_id,
                        'type_detail_id' => $detail->type_detail_id,
                        'regional_police_id' => $this->regional_police_id,
                        'police_station_id' => $this->police_station_id,
                        'rack_id' => $detail->rack_id,
                        'date' => now(),
                        'serial_number' => trim(($detail->code ?? '') . ' ' . ($detail->number_serial_first ?? '') . ' ' . ($detail->number_serial_second ?? '')),
                        'status_type' => $detail->difference > 0 ? 'in' : 'out',
                        'quantity' => $detail->difference,
                        'description' => 'Stock Opname Adjustment (' . $this->code . '): ' . ($detail->difference > 0 ? 'Surplus' : 'Shortage'),
                        'is_active' => true,
                    ]);
                }
            }
        });
    }

    /**
     * Calculate total difference
     */
    public function getTotalDifferenceAttribute()
    {
        return $this->stockOpnameDetails->sum('difference');
    }

    /**
     * Relationships
     */
    public function stockOpnameDetails()
    {
        return $this->hasMany(StockOpnameDetail::class);
    }

    public function regionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class);
    }

    public function policeStation()
    {
        return $this->belongsTo(PoliceStation::class);
    }

    public function checkedByUser()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopePolda($query, $regionalPoliceId)
    {
        return $query->where('regional_police_id', $regionalPoliceId)
            ->whereNull('police_station_id');
    }

    public function scopePolres($query, $policeStationId)
    {
        return $query->where('police_station_id', $policeStationId)
            ->whereNull('regional_police_id');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
