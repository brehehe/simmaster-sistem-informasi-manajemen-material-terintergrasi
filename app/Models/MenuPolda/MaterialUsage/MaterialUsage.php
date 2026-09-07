<?php

namespace App\Models\MenuPolda\MaterialUsage;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialUsage extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    // Header has many details
    public function materialUsageDetails()
    {
        return $this->hasMany(MaterialUsageDetail::class);
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
        return \App\Services\CodeGeneratorService::generate(self::class, 'MU', 4);
    }
}
