<?php

namespace App\Policies;

use App\Models\StockOpname\StockOpname;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockOpnamePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Admin', 'Polda', 'Polres']);
    }

    public function view(User $user, StockOpname $stockOpname): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Polda')) {
            return $user->regional_police_id === $stockOpname->regional_police_id;
        }

        if ($user->hasRole('Polres')) {
            return $user->police_station_id === $stockOpname->police_station_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'Polda', 'Polres']);
    }

    public function update(User $user, StockOpname $stockOpname): bool
    {
        if ($stockOpname->status !== 'draft') {
            return false;
        }

        return $this->view($user, $stockOpname);
    }

    public function approve(User $user, StockOpname $stockOpname): bool
    {
        if ($stockOpname->status !== 'completed') {
            return false;
        }

        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Polda') && $user->regional_police_id === $stockOpname->regional_police_id) {
            return true;
        }

        if ($user->hasRole('Polres') && $user->police_station_id === $stockOpname->police_station_id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, StockOpname $stockOpname): bool
    {
        if ($stockOpname->status !== 'draft') {
            return false;
        }

        return $this->view($user, $stockOpname);
    }
}
