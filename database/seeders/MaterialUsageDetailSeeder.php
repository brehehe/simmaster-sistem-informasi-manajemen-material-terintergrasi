<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaterialUsageDetailSeeder extends Seeder
{
    public function run(): void
    {
        $materialUsages = DB::table('material_usages')->pluck('id')->toArray();
        $stockDetails = DB::table('stock_details')->pluck('id')->toArray();
        $types = DB::table('types')->pluck('id')->toArray();
        $typeDetails = DB::table('type_details')->pluck('id')->toArray();
        $racks = DB::table('racks')->pluck('id')->toArray();

        $materialUsageDetails = [];
        $usageTypes = ['training', 'operation', 'maintenance', 'testing', 'other'];

        for ($i = 1; $i <= 500; $i++) {
            $materialUsageDetails[] = [
                'id' => Str::uuid()->toString(),
                'material_usage_id' => $materialUsages[array_rand($materialUsages)],
                'stock_detail_id' => rand(0, 1) ? $stockDetails[array_rand($stockDetails)] : null,
                'type_id' => rand(0, 1) ? $types[array_rand($types)] : null,
                'type_detail_id' => rand(0, 1) ? $typeDetails[array_rand($typeDetails)] : null,
                'rack_id' => rand(0, 1) ? $racks[array_rand($racks)] : null,
                'item_code' => 'MUDIC-' . rand(1000, 9999),
                'number_serial_first' => 'MUDSN1-' . rand(1000, 9999),
                'number_serial_second' => 'MUDSN2-' . rand(1000, 9999),
                'quantity' => rand(1, 50),
                'usage_type' => $usageTypes[array_rand($usageTypes)],
                'description' => 'Material usage detail data ' . $i,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($materialUsageDetails, 100) as $chunk) {
            DB::table('material_usage_details')->insert($chunk);
        }
    }
}
