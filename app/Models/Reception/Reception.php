<?php

namespace App\Models\Reception;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reception extends Model
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

    public function receptionDetails()
    {
        return $this->hasMany(ReceptionDetail::class);
    }

    /**
     * Generate unique code for Reception
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

        return 'RC-' . $date . '-' . $newNumber;
    }
}
