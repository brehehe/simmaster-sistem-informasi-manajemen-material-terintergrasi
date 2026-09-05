<?php

namespace App\Policies;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaterialUsagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Admin', 'Polda', 'Polres']);
    }

    public function view(User $user, MaterialUsage $materialUsage): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Polda')) {
            return $user->regional_police_id === $materialUsage->regional_police_id;
        }

        if ($user->hasRole('Polres')) {
            return $user->police_station_id === $materialUsage->police_station_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'Polda', 'Polres']);
    }

    public function update(User $user, MaterialUsage $materialUsage): bool
    {
        return $this->view($user, $materialUsage);
    }

    public function delete(User $user, MaterialUsage $materialUsage): bool
    {
        return $this->view($user, $materialUsage);
    }
}
