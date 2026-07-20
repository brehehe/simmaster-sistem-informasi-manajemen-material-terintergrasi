<?php

namespace Database\Seeders;

use App\Models\Spatie\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Database\Seeders\Type\TypeDetailSeeder;
use Database\Seeders\Type\TypeSeeder;
use Database\Seeders\User\UserSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Polda']);
        Role::create(['name' => 'Polres']);

        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Admin ARMASTER',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'level_menu' => 1,
        ]);

        $user->assignRole('Admin');

        $this->call([
            TypeSeeder::class,
            TypeDetailSeeder::class,
            RegionalPoliceSeeder::class,
            PoliceStationSeeder::class,
            TargetSeeder::class,
            UserTypeSeeder::class,
            UserSeeder::class,
            // Racks - must run before stock and transactions
            RackSeeder::class,
            // Connected data seeder that aligns stock, receptions, shipments, usages, etc.
            ConnectedDataSeeder::class,
        ]);
    }
}
