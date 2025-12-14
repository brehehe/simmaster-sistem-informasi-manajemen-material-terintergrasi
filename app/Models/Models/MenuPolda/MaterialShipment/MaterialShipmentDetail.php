<?php

namespace App\Models\Models\MenuPolda\MaterialShipment;

use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialShipmentDetail extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function materialShipment()
    {
        return $this->belongsTo(MaterialShipment::class);
    }

    public function stockDetail()
    {
        return $this->belongsTo(StockDetail::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function typeDetail()
    {
        return $this->belongsTo(TypeDetail::class, 'type_detail_id');
    }

    public function rack()
    {
        return $this->belongsTo(\App\Models\Rack\Rack::class);
    }
}
