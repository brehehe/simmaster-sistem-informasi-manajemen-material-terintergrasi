<?php

use App\Livewire\Admin\Master\Target\Detail\AdminMasterTargetDetailIndex;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Target\Target;
use App\Models\Target\TargetDetail;
use App\Models\Type\Type;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

it('renders target index page for admin', function () {
    Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignRole('Admin');

    $this->actingAs($user)
        ->get(route('master.target'))
        ->assertSuccessful()
        ->assertSee('Manajemen Target');
});

it('saves target details matrix', function () {
    Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignRole('Admin');

    $regional = RegionalPolice::create([
        'name' => 'Polda Test',
        'is_active' => true,
    ]);

    PoliceStation::create([
        'regional_police_id' => $regional->id,
        'name' => 'Polres Test',
        'is_active' => true,
    ]);

    Type::create([
        'name' => 'SIM BARU',
        'is_active' => true,
    ]);

    $component = Livewire::actingAs($user)
        ->test(AdminMasterTargetDetailIndex::class);

    $rows = $component->get('rows');
    $types = $component->get('types');

    $matrix = [];
    foreach ($rows as $row) {
        foreach ($types as $type) {
            $matrix[$row['key']][$type['id']] = 0;
        }
    }

    $matrix[$rows[0]['key']][$types[0]['id']] = 1200000;

    $component
        ->set('year', 2026)
        ->set('matrix', $matrix)
        ->call('save');

    expect(Target::where('name', 'Target Ditlantas 2026')->exists())->toBeTrue();

    $target = Target::where('name', 'Target Ditlantas 2026')->first();
    expect($target)->not->toBeNull();

    $this->assertDatabaseHas('target_details', [
        'target_id' => $target->id,
        'type_id' => $types[0]['id'],
        'quantity' => 1200000,
    ]);

    expect(TargetDetail::where('target_id', $target->id)->count())->toBe(1);
});
