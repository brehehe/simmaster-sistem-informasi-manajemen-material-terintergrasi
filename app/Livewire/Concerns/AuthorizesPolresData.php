<?php

namespace App\Livewire\Concerns;

trait AuthorizesPolresData
{
    protected function authorizePolresMenu(): void
    {
        abort_unless(auth()->user()?->hasRole(['Admin', 'Polres']), 403);
    }

    protected function authorizePoliceStation(?string $policeStationId): void
    {
        $user = auth()->user();

        abort_unless(
            $user?->hasRole('Admin')
            || ($user?->hasRole('Polres') && $policeStationId === $user->police_station_id),
            403
        );
    }

    protected function policeStationForUser(?string $requestedPoliceStationId = null): ?string
    {
        $user = auth()->user();

        return $user?->hasRole('Admin')
            ? $requestedPoliceStationId
            : $user?->police_station_id;
    }
}
