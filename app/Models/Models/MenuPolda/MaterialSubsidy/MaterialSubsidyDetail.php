<?php

namespace App\Models\Models\MenuPolda\MaterialSubsidy;

use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MaterialSubsidyDetail extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    public function materialSubsidy()
    {
        return $this->belongsTo(MaterialSubsidy::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function typeDetail()
    {
        return $this->belongsTo(TypeDetail::class, 'type_detail_id');
    }

    public function stockDetail()
    {
        return $this->belongsTo(StockDetail::class, 'stock_detail_id');
    }
}
