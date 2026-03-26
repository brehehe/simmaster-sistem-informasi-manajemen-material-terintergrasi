<?php

namespace App\Models\Reception;

use App\Models\Rack\Rack;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceptionDetail extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function reception()
    {
        return $this->belongsTo(Reception::class);
    }

    public function receptionDetailItems()
    {
        return $this->hasMany(ReceptionDetailItem::class);
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
