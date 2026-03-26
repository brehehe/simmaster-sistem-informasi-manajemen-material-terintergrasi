<?php

namespace App\Models\Stock;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Rack\Rack;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockDetail extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($stockDetail) {
            // Ensure only one owner type is set
            if ($stockDetail->regional_police_id && $stockDetail->police_station_id) {
                throw new \Exception('StockDetail cannot belong to both Polda and Polres');
            }

            if (!$stockDetail->regional_police_id && !$stockDetail->police_station_id) {
                throw new \Exception('StockDetail must belong to either Polda or Polres');
            }
        });
    }

    // Scopes
    public function scopePolda($query, $regionalPoliceId = null)
    {
        $query->whereNotNull('regional_police_id')
            ->whereNull('police_station_id');

        if ($regionalPoliceId) {
            $query->where('regional_police_id', $regionalPoliceId);
        }

        return $query;
    }

    public function scopePolres($query, $policeStationId = null)
    {
        $query->whereNull('regional_police_id')
            ->whereNotNull('police_station_id');

        if ($policeStationId) {
            $query->where('police_station_id', $policeStationId);
        }

        return $query;
    }

    // Helper methods
    public function isPolda(): bool
    {
        return $this->regional_police_id !== null;
    }

    public function isPolres(): bool
    {
        return $this->police_station_id !== null;
    }

    // Relationships
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function typeDetail()
    {
        return $this->belongsTo(TypeDetail::class);
    }

    public function regionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class);
    }

    public function policeStation()
    {
        return $this->belongsTo(PoliceStation::class);
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }

    public function service()
    {
        return $this->belongsTo(\App\Models\Service\Service::class);
    }

    public function serviceDetail()
    {
        return $this->belongsTo(\App\Models\Service\ServiceDetail::class);
    }
}
