<?php

namespace App\Models\MenuPolda\RackAssignment;

use App\Models\Rack\Rack;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RackAssignmentDetail extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function rackAssignment()
    {
        return $this->belongsTo(RackAssignment::class);
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

    public function fromRack()
    {
        return $this->belongsTo(Rack::class, 'from_rack_id');
    }

    public function toRack()
    {
        return $this->belongsTo(Rack::class, 'to_rack_id');
    }
}
