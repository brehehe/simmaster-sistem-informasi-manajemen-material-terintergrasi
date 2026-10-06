<?php

function projectFile(string $path): string
{
    $contents = file_get_contents(__DIR__.'/../../'.$path);

    if ($contents === false) {
        throw new RuntimeException("Cannot read {$path}");
    }

    return $contents;
}

it('registers canonical edit routes and keeps legacy edit links', function () {
    $routes = projectFile('routes/web.php');

    foreach ([
        'mutation-stock',
        'last-stock',
        'rack-assignment',
        'material-usage',
        'material-damage',
        'material-subsidy',
        'stock-opname',
    ] as $menu) {
        expect($routes)
            ->toContain("menu-polres/{$menu}/{id}/edit")
            ->toContain("menu-polres/{$menu}/edit/{id}");
    }
});

it('keeps the existing damage and rack forms editable in edit mode', function () {
    foreach ([
        'resources/views/livewire/admin/menu-polres/material-damage/detail/admin-menu-polres-material-damage-detail-index.blade.php',
        'resources/views/livewire/admin/menu-polres/rack-assignment/detail/admin-menu-polres-rack-assignment-detail-index.blade.php',
    ] as $view) {
        $contents = projectFile($view);

        expect($contents)
            ->not->toContain('@disabled($isEditMode)')
            ->not->toContain('@if (!$isEditMode)')
            ->toContain('wire:click="save"')
            ->toContain('Simpan Perubahan');
    }
});

it('guards editable polres transactions by role and police station ownership', function () {
    $concern = projectFile('app/Livewire/Concerns/AuthorizesPolresData.php');

    expect($concern)
        ->toContain("hasRole(['Admin', 'Polres'])")
        ->toContain('$policeStationId === $user->police_station_id');

    foreach ([
        'app/Livewire/Admin/MenuPolres/MaterialDamage/Detail/AdminMenuPolresMaterialDamageDetailIndex.php',
        'app/Livewire/Admin/MenuPolres/RackAssignment/Detail/AdminMenuPolresRackAssignmentDetailIndex.php',
        'app/Livewire/Admin/MenuPolres/MaterialSubsidy/Detail/AdminMenuPolresMaterialSubsidyDetailIndex.php',
        'app/Livewire/Admin/MenuPolres/MutationStock/Detail/AdminMenuPolresMutationStockDetailIndex.php',
        'app/Livewire/Admin/MenuPolres/StockOpname/Edit/AdminMenuPolresStockOpnameEditIndex.php',
    ] as $component) {
        $contents = projectFile($component);

        expect($contents)
            ->toContain('use AuthorizesPolresData;')
            ->toContain('authorizePoliceStation(');
    }
});
