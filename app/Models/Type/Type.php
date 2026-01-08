<?php

namespace App\Models\Type;

use App\Models\Service\Service;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function typeDetails()
    {
        return $this->hasMany(TypeDetail::class);
    }

    public function stocks()
    {
        return $this->hasMany(\App\Models\Stock\Stock::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
