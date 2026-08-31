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
        $prefix = 'RA-' . $date . '-';

        $lastRecord = self::withTrashed()
            ->where('code', 'LIKE', $prefix . '%')
            ->orderBy('code', 'DESC')
            ->first();

        $nextNumber = 1;
        if ($lastRecord && preg_match('/-(\d+)$/', $lastRecord->code, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        $code = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Safeguard collision loop to guarantee absolute uniqueness
        while (self::withTrashed()->where('code', $code)->exists()) {
            $nextNumber++;
            $code = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return $code;
    }
}
