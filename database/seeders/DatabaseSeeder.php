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
            RackSeeder::class,
            // Full master seeder untuk persiapan uji coba:
            // - Stok Polda fisik 4 Sept 2026 (7.951.000 materiil utama)
            // - Target 2026 resmi dari Excel
            // - Stok Polres kosong (0)
            // - Clear material rusak & dummy
            // - Akun BAMAT Polres + SAMSAT Polda & Sie
            UjiCobaFullSeeder::class,
        ]);
    }
}
