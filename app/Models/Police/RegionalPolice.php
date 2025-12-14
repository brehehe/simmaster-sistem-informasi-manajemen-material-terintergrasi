<?php

namespace App\Models\Police;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegionalPolice extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'regional_police';
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function policeStations() {
        return $this->hasMany(PoliceStation::class);
    }
}
