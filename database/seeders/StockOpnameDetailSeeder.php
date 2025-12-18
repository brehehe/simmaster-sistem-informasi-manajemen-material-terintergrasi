<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockOpnameDetailSeeder extends Seeder
{
    public function run(): void
    {
        $stockOpnames = DB::table('stock_opnames')->pluck('id')->toArray();
        $stockDetails = DB::table('stock_details')->pluck('id')->toArray();
        $types = DB::table('types')->pluck('id')->toArray();
        $typeDetails = DB::table('type_details')->pluck('id')->toArray();
        $racks = DB::table('racks')->pluck('id')->toArray();

        $stockOpnameDetails = [];

        for ($i = 1; $i <= 500; $i++) {
            $systemQuantity = rand(10, 100);
            $physicalQuantity = $systemQuantity + rand(-20, 20);
            $difference = $physicalQuantity - $systemQuantity;

            $stockOpnameDetails[] = [
                'id' => Str::uuid()->toString(),
                'stock_opname_id' => $stockOpnames[array_rand($stockOpnames)],
                'stock_detail_id' => rand(0, 1) ? $stockDetails[array_rand($stockDetails)] : null,
                'type_id' => $types[array_rand($types)],
                'type_detail_id' => rand(0, 1) ? $typeDetails[array_rand($typeDetails)] : null,
                'rack_id' => rand(0, 1) ? $racks[array_rand($racks)] : null,
                'code' => 'SOD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'number_serial_first' => 'SOSN1-' . rand(1000, 9999),
                'number_serial_second' => 'SOSN2-' . rand(1000, 9999),
                'system_quantity' => $systemQuantity,
                'physical_quantity' => $physicalQuantity,
                'difference' => $difference,
                'notes' => $difference != 0 ? "Discrepancy found: $difference" : 'No discrepancy',
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($stockOpnameDetails, 100) as $chunk) {
            DB::table('stock_opname_details')->insert($chunk);
        }
    }
}
