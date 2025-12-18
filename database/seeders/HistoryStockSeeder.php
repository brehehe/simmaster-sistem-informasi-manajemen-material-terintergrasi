<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HistoryStockSeeder extends Seeder
{
    public function run(): void
    {
        $lastStocks = DB::table('last_stocks')->pluck('id')->toArray();
        $lastStockDetails = DB::table('last_stock_details')->pluck('id')->toArray();
        $types = DB::table('types')->pluck('id')->toArray();
        $typeDetails = DB::table('type_details')->pluck('id')->toArray();
        $racks = DB::table('racks')->pluck('id')->toArray();
        $regionalPolice = DB::table('regional_police')->pluck('id')->toArray();
        $policeStations = DB::table('police_stations')->pluck('id')->toArray();

        $historyStocks = [];
        $statusTypes = ['in', 'first', 'last', 'out'];

        for ($i = 1; $i <= 500; $i++) {
            $isRegional = rand(0, 1);

            $historyStocks[] = [
                'id' => Str::uuid()->toString(),
                'code' => 'HS-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'last_stock_id' => rand(0, 1) ? $lastStocks[array_rand($lastStocks)] : null,
                'last_stock_detail_id' => rand(0, 1) ? $lastStockDetails[array_rand($lastStockDetails)] : null,
                'type_id' => rand(0, 1) ? $types[array_rand($types)] : null,
                'type_detail_id' => rand(0, 1) ? $typeDetails[array_rand($typeDetails)] : null,
                'regional_police_id' => $isRegional ? $regionalPolice[array_rand($regionalPolice)] : null,
                'police_station_id' => !$isRegional ? $policeStations[array_rand($policeStations)] : null,
                'rack_id' => rand(0, 1) ? $racks[array_rand($racks)] : null,
                'date' => now()->subDays(rand(0, 365))->format('Y-m-d'),
                'serial_number' => 'HSN-' . rand(10000, 99999),
                'status_type' => $statusTypes[array_rand($statusTypes)],
                'quantity' => rand(1, 100),
                'description' => 'History stock data ' . $i,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($historyStocks, 100) as $chunk) {
            DB::table('history_stocks')->insert($chunk);
        }
    }
}
