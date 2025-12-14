<?php

namespace App\Models\LastStock;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Police\RegionalPolice;
use App\Models\Police\PoliceStation;

class LastStock extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    public function regionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class);
    }

    public function policeStation()
    {
        return $this->belongsTo(PoliceStation::class);
    }

    public function lastStockDetails()
    {
        return $this->hasMany(LastStockDetail::class);
    }

    /**
     * Generate unique code for LastStock
     */
    public static function generateCode()
    {
        $date = now()->format('Ymd');
        $lastRecord = self::whereDate('created_at', now()->toDateString())
            ->latest('created_at')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'LS-' . $date . '-' . $newNumber;
    }
}
