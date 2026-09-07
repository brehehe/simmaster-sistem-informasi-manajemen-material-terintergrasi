<?php

namespace App\Models\LastStock;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Police\RegionalPolice;
use App\Models\Police\PoliceStation;

use App\Models\Type\Type;

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

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function lastStockDetails()
    {
        return $this->hasMany(LastStockDetail::class);
    }

    /**
     * Generate unique code for LastStock
     */
    public static function generateCode(): string
    {
        return \App\Services\CodeGeneratorService::generate(self::class, 'LS', 4);
    }
}
