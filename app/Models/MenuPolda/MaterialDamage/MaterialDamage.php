<?php

namespace App\Models\MenuPolda\MaterialDamage;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialDamage extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    // Header has many details
    public function materialDamageDetails()
    {
        return $this->hasMany(MaterialDamageDetail::class);
    }

    public function regionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class);
    }

    public function policeStation()
    {
        return $this->belongsTo(PoliceStation::class);
    }

    public static function generateCode(): string
    {
        return \App\Services\CodeGeneratorService::generate(self::class, 'MD', 4);
    }
}
