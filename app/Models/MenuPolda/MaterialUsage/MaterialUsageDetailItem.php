<?php

namespace App\Models\MenuPolda\MaterialUsage;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialUsageDetailItem extends Model
{
    use HasUuids, SoftDeletes;
    protected $guarded = ['id'];

    public function materialUsage()
    {
        return $this->belongsTo(MaterialUsage::class);
    }

    public function materialUsageDetail()
    {
        return $this->belongsTo(MaterialUsageDetail::class);
    }

    public function stockDetail()
    {
        return $this->belongsTo(\App\Models\Stock\StockDetail::class);
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
