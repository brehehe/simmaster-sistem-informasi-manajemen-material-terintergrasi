<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaterialUsageSeeder extends Seeder
{
    public function run(): void
    {
        $regionalPolice = DB::table('regional_police')->pluck('id')->toArray();
        $policeStations = DB::table('police_stations')->pluck('id')->toArray();

        $materialUsages = [];

        for ($i = 1; $i <= 500; $i++) {
            $isRegional = rand(0, 1);

            $materialUsages[] = [
                'id' => Str::uuid()->toString(),
                'code' => 'MU-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'regional_police_id' => $isRegional ? $regionalPolice[array_rand($regionalPolice)] : null,
                'police_station_id' => !$isRegional ? $policeStations[array_rand($policeStations)] : null,
                'date' => now()->subDays(rand(0, 365))->format('Y-m-d'),
                'description' => 'Material usage data ' . $i,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($materialUsages, 100) as $chunk) {
            DB::table('material_usages')->insert($chunk);
        }
    }
}
