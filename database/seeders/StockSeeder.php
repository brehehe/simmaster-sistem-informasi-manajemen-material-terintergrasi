<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $types = DB::table('types')->pluck('id')->toArray();
        $typeDetails = DB::table('type_details')->pluck('id')->toArray();
        $regionalPolice = DB::table('regional_police')->pluck('id')->toArray();
        $policeStations = DB::table('police_stations')->pluck('id')->toArray();

        $stocks = [];

        for ($i = 1; $i <= 500; $i++) {
            $isRegional = rand(0, 1);

            $stocks[] = [
                'id' => Str::uuid()->toString(),
                'type_id' => $types[array_rand($types)],
                'type_detail_id' => rand(0, 1) ? $typeDetails[array_rand($typeDetails)] : null,
                'regional_police_id' => $isRegional ? $regionalPolice[array_rand($regionalPolice)] : null,
                'police_station_id' => !$isRegional ? $policeStations[array_rand($policeStations)] : null,
                'quantity' => rand(10, 1000),
                'description' => 'Stock data ' . $i,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($stocks, 100) as $chunk) {
            DB::table('stocks')->insert($chunk);
        }
    }
}
