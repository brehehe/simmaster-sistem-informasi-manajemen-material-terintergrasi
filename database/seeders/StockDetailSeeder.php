<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockDetailSeeder extends Seeder
{
    public function run(): void
    {
        $stocks = DB::table('stocks')->pluck('id')->toArray();
        $types = DB::table('types')->pluck('id')->toArray();
        $typeDetails = DB::table('type_details')->pluck('id')->toArray();
        $racks = DB::table('racks')->pluck('id')->toArray();
        $regionalPolice = DB::table('regional_police')->pluck('id')->toArray();
        $policeStations = DB::table('police_stations')->pluck('id')->toArray();

        $stockDetails = [];

        for ($i = 1; $i <= 500; $i++) {
            $isRegional = rand(0, 1);

            $stockDetails[] = [
                'id' => Str::uuid()->toString(),
                'stock_id' => $stocks[array_rand($stocks)],
                'type_id' => $types[array_rand($types)],
                'type_detail_id' => rand(0, 1) ? $typeDetails[array_rand($typeDetails)] : null,
                'regional_police_id' => $isRegional ? $regionalPolice[array_rand($regionalPolice)] : null,
                'police_station_id' => !$isRegional ? $policeStations[array_rand($policeStations)] : null,
                'rack_id' => rand(0, 1) ? $racks[array_rand($racks)] : null,
                'code' => 'SD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'number_serial_first' => 'SN1-' . rand(1000, 9999),
                'number_serial_second' => 'SN2-' . rand(1000, 9999),
                'quantity' => rand(1, 100),
                'description' => 'Stock detail data ' . $i,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($stockDetails, 100) as $chunk) {
            DB::table('stock_details')->insert($chunk);
        }
    }
}
