<?php

namespace App\Models\MenuPolda\RackAssignment;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Rack\Rack;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RackAssignment extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    // Header has many details
    public function rackAssignmentDetails()
    {
        return $this->hasMany(RackAssignmentDetail::class);
    }

    public function regionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class);
    }

    public function policeStation()
    {
        return $this->belongsTo(PoliceStation::class);
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

    public static function generateCode()
    {
        $date = now()->format('Ymd');
        $lastRecord = self::whereDate('created_at', '=', now()->toDateString())
            ->latest('created_at')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'RA-' . $date . '-' . $newNumber;
    }
}
