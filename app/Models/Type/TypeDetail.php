<?php

namespace App\Models\Type;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeDetail extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function type() {
        return $this->belongsTo(Type::class);
    }

    public function services() {
        return $this->hasMany(\App\Models\Service\Service::class);
    }
}
