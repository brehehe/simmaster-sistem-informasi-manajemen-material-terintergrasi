<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LastStock\LastStock;
use App\Models\LastStock\LastStockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Rack\Rack;
use App\Models\Police\RegionalPolice;
use App\Models\Police\PoliceStation;

class HistoryStock extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function lastStock()
    {
        return $this->belongsTo(LastStock::class);
    }

    public function lastStockDetail()
    {
        return $this->belongsTo(LastStockDetail::class);
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

    /**
     * Generate unique code for HistoryStock
     */
    public static function generateCode()
    {
        $date = now()->format('Ymd');
        $microtime = now()->format('His'); // HHMMSS
        $micro = substr((string) microtime(true), -6, 4); // Get 4 digits from microseconds

        return 'HS-' . $date . '-' . $microtime . '-' . $micro;
    }
}
