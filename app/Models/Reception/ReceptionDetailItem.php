<?php

namespace App\Models\Reception;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceptionDetailItem extends Model
{
    use HasUuids, SoftDeletes;
    protected $guarded = ['id'];

    public function reception()
    {
        return $this->belongsTo(Reception::class);
    }

    public function receptionDetail()
    {
        return $this->belongsTo(ReceptionDetail::class);
    }

    public function service()
    {
        return $this->belongsTo(\App\Models\Service\Service::class);
    }

    public function serviceDetail()
    {
        return $this->belongsTo(\App\Models\Service\ServiceDetail::class);
    }

    public function type()
    {
        return $this->belongsTo(\App\Models\Type\Type::class);
    }

    public function typeDetail()
    {
        return $this->belongsTo(\App\Models\Type\TypeDetail::class);
    }

    public function rack()
    {
        return $this->belongsTo(\App\Models\Rack\Rack::class);
    }
}
