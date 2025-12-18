<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LastStockDetailSeeder extends Seeder
{
    public function run(): void
    {
        $lastStocks = DB::table('last_stocks')->pluck('id')->toArray();
        $types = DB::table('types')->pluck('id')->toArray();
        $typeDetails = DB::table('type_details')->pluck('id')->toArray();
        $racks = DB::table('racks')->pluck('id')->toArray();

        $lastStockDetails = [];

        for ($i = 1; $i <= 500; $i++) {
            $lastStockDetails[] = [
                'id' => Str::uuid()->toString(),
                'last_stock_id' => $lastStocks[array_rand($lastStocks)],
                'type_id' => rand(0, 1) ? $types[array_rand($types)] : null,
                'type_detail_id' => rand(0, 1) ? $typeDetails[array_rand($typeDetails)] : null,
                'rack_id' => rand(0, 1) ? $racks[array_rand($racks)] : null,
                'code' => 'LSD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'number_serial_first' => 'LSN1-' . rand(1000, 9999),
                'number_serial_second' => 'LSN2-' . rand(1000, 9999),
                'quantity' => rand(1, 100),
                'description' => 'Last stock detail data ' . $i,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($lastStockDetails, 100) as $chunk) {
            DB::table('last_stock_details')->insert($chunk);
        }
    }
}
