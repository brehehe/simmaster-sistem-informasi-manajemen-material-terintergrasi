<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MutationStockDetailSeeder extends Seeder
{
    public function run(): void
    {
        $mutationStocks = DB::table('mutation_stocks')->pluck('id')->toArray();
        $stockDetails = DB::table('stock_details')->pluck('id')->toArray();
        $types = DB::table('types')->pluck('id')->toArray();
        $typeDetails = DB::table('type_details')->pluck('id')->toArray();

        $mutationStockDetails = [];

        for ($i = 1; $i <= 500; $i++) {
            $mutationStockDetails[] = [
                'id' => Str::uuid()->toString(),
                'mutation_stock_id' => $mutationStocks[array_rand($mutationStocks)],
                'stock_detail_id' => rand(0, 1) ? $stockDetails[array_rand($stockDetails)] : null,
                'type_id' => rand(0, 1) ? $types[array_rand($types)] : null,
                'type_detail_id' => rand(0, 1) ? $typeDetails[array_rand($typeDetails)] : null,
                'code' => 'MTD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'number_serial_first' => 'MTSN1-' . rand(1000, 9999),
                'number_serial_second' => 'MTSN2-' . rand(1000, 9999),
                'quantity' => rand(1, 100),
                'notes' => 'Mutation stock detail notes ' . $i,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($mutationStockDetails, 100) as $chunk) {
            DB::table('mutation_stock_details')->insert($chunk);
        }
    }
}
