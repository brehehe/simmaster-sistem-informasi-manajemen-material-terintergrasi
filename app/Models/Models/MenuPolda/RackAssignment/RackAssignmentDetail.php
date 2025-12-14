<?php

namespace App\Models\Models\MenuPolda\RackAssignment;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RackAssignmentDetail extends Model
{
    use HasUuids, SoftDeletes;
    protected $guarded = ['id'];
}
