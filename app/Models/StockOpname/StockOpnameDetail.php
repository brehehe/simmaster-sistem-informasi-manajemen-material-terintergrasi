<?php

namespace App\Models\StockOpname;

use App\Models\Rack\Rack;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpnameDetail extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'stock_opname_id',
        'stock_detail_id',
        'type_id',
        'type_detail_id',
        'rack_id',
        'code',
        'number_serial_first',
        'number_serial_second',
        'system_quantity',
        'physical_quantity',
        'difference',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'system_quantity' => 'decimal:2',
        'physical_quantity' => 'decimal:2',
        'difference' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Automatically calculate difference when physical_quantity changes
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->difference = $model->physical_quantity - $model->system_quantity;
        });
    }

    /**
     * Get difference percentage
     */
    public function getDifferencePercentageAttribute()
    {
        if ($this->system_quantity == 0) {
            return 0;
        }

        return round(($this->difference / $this->system_quantity) * 100, 2);
    }

    /**
     * Check if surplus
     */
    public function getIsSurplusAttribute()
    {
        return $this->difference > 0;
    }

    /**
     * Check if shortage
     */
    public function getIsShortageAttribute()
    {
        return $this->difference < 0;
    }

    /**
     * Relationships
     */
    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
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
