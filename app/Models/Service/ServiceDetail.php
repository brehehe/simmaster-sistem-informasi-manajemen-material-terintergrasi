<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceDetail extends Model
{
    use HasUuids, SoftDeletes;
    protected $guarded = ['id'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
