<?php

namespace App\Models\Spatie;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends SpatieRole
{
    use HasUuids, SoftDeletes;
    protected $guarded = ['id'];
}
