<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaterialDamageDetailSeeder extends Seeder
{
    public function run(): void
    {
        $materialDamages = DB::table('material_damages')->pluck('id')->toArray();
        $stockDetails = DB::table('stock_details')->pluck('id')->toArray();
        $types = DB::table('types')->pluck('id')->toArray();
        $typeDetails = DB::table('type_details')->pluck('id')->toArray();
        $racks = DB::table('racks')->pluck('id')->toArray();

        $materialDamageDetails = [];
        $damageTypes = ['damaged', 'lost'];
        $reasons = [
            'Broken during operation',
            'Natural wear and tear',
            'Accident',
            'Missing items',
            'Expired',
            'Quality defect',
        ];

        for ($i = 1; $i <= 500; $i++) {
            $materialDamageDetails[] = [
                'id' => Str::uuid()->toString(),
                'material_damage_id' => $materialDamages[array_rand($materialDamages)],
                'stock_detail_id' => rand(0, 1) ? $stockDetails[array_rand($stockDetails)] : null,
                'type_id' => rand(0, 1) ? $types[array_rand($types)] : null,
                'type_detail_id' => rand(0, 1) ? $typeDetails[array_rand($typeDetails)] : null,
                'rack_id' => rand(0, 1) ? $racks[array_rand($racks)] : null,
                'item_code' => 'MDDIC-' . rand(1000, 9999),
                'number_serial_first' => 'MDDSN1-' . rand(1000, 9999),
                'number_serial_second' => 'MDDSN2-' . rand(1000, 9999),
                'quantity' => rand(1, 20),
                'damage_type' => $damageTypes[array_rand($damageTypes)],
                'reason' => $reasons[array_rand($reasons)],
                'description' => 'Material damage detail data ' . $i,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($materialDamageDetails, 100) as $chunk) {
            DB::table('material_damage_details')->insert($chunk);
        }
    }
}
