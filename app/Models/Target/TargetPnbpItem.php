<?php

namespace App\Models\Target;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TargetPnbpItem extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'target_pnbp_items';

    protected $guarded = ['id'];

    protected $casts = [
        'year'               => 'integer',
        'row_index'          => 'integer',
        'level'              => 'integer',
        'tarif'              => 'decimal:2',
        'volume_target'      => 'decimal:2',
        'target_rp'          => 'decimal:2',
        'volume_realization' => 'decimal:2',
        'realization_rp'     => 'decimal:2',
        'percentage'         => 'decimal:2',
    ];

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class, 'target_id');
    }
}
