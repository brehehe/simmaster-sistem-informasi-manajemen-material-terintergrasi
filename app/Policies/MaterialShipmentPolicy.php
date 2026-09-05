<?php

namespace App\Policies;

use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaterialShipmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Admin', 'Polda', 'Polres', 'Warehouse']);
    }

    public function view(User $user, MaterialShipment $shipment): bool
    {
        if ($user->hasRole('Admin') || $user->hasRole('Warehouse')) {
            return true;
        }

        if ($user->hasRole('Polda')) {
            return $user->regional_police_id === $shipment->sender_regional_police_id;
        }

        if ($user->hasRole('Polres')) {
            return $user->police_station_id === $shipment->receiver_police_station_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'Polda']);
    }

    public function update(User $user, MaterialShipment $shipment): bool
    {
        if ($shipment->status !== 'draft') {
            return false;
        }

        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Polda')) {
            return $user->regional_police_id === $shipment->sender_regional_police_id;
        }

        return false;
    }

    public function delete(User $user, MaterialShipment $shipment): bool
    {
        if ($shipment->status !== 'draft') {
            return false;
        }

        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Polda')) {
            return $user->regional_police_id === $shipment->sender_regional_police_id;
        }

        return false;
    }
}
