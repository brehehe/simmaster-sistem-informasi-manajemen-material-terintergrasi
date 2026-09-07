<?php

namespace App\Models\Message;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_read' => 'boolean',
        'is_active' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function senderRegionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class, 'sender_regional_police_id');
    }

    public function senderPoliceStation()
    {
        return $this->belongsTo(PoliceStation::class, 'sender_police_station_id');
    }

    public function receiverRegionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class, 'receiver_regional_police_id');
    }

    public function receiverPoliceStation()
    {
        return $this->belongsTo(PoliceStation::class, 'receiver_police_station_id');
    }

    public static function generateCode(): string
    {
        return \App\Services\CodeGeneratorService::generate(self::class, 'MSG', 4);
    }
}
