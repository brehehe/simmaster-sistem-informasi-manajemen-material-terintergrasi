<?php

namespace App\Models\LastStock;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Rack\Rack;

class LastStockDetail extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function lastStock()
    {
        return $this->belongsTo(LastStock::class);
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
