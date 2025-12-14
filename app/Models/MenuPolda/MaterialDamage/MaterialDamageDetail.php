<?php

namespace App\Models\MenuPolda\MaterialDamage;

use App\Models\Rack\Rack;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialDamageDetail extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function materialDamage()
    {
        return $this->belongsTo(MaterialDamage::class);
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
        return $this->belongsTo(TypeDetail::class);
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }
}
