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
                // Rack seeder - must run before stock seeders
                // RackSeeder::class,
                // Stock seeders - core tables first
                // StockSeeder::class,
                // StockDetailSeeder::class,
                // Last stock tables
                // LastStockSeeder::class,
                // LastStockDetailSeeder::class,
                // History stock
                // HistoryStockSeeder::class,
                // Transaction tables
                // ReceptionSeeder::class,
                // ReceptionDetailSeeder::class,
                // RackAssignmentSeeder::class,
                // RackAssignmentDetailSeeder::class,
            MaterialUsageSeeder::class,
            MaterialUsageDetailSeeder::class,
            // MaterialDamageSeeder::class,
            // MaterialDamageDetailSeeder::class,
            // MaterialShipmentSeeder::class,
            // MaterialShipmentDetailSeeder::class,
            // StockOpnameSeeder::class,
            // StockOpnameDetailSeeder::class,
            // MutationStockSeeder::class,
            // MutationStockDetailSeeder::class,
        ]);
    }
}
