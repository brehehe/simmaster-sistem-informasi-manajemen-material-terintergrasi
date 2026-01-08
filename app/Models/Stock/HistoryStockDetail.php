<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryStockDetail extends Model
{
    use HasUuids, SoftDeletes;
    protected $guarded = ['id'];
}
