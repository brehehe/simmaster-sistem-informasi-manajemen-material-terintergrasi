<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaterialShipmentDetailSeeder extends Seeder
{
    public function run(): void
    {
        $materialShipments = DB::table('material_shipments')->pluck('id')->toArray();
        $stockDetails = DB::table('stock_details')->pluck('id')->toArray();
        $types = DB::table('types')->pluck('id')->toArray();
        $typeDetails = DB::table('type_details')->pluck('id')->toArray();
        $racks = DB::table('racks')->pluck('id')->toArray();

        $materialShipmentDetails = [];

        for ($i = 1; $i <= 500; $i++) {
            $materialShipmentDetails[] = [
                'id' => Str::uuid()->toString(),
                'material_shipment_id' => $materialShipments[array_rand($materialShipments)],
                'stock_detail_id' => rand(0, 1) ? $stockDetails[array_rand($stockDetails)] : null,
                'type_id' => rand(0, 1) ? $types[array_rand($types)] : null,
                'type_detail_id' => rand(0, 1) ? $typeDetails[array_rand($typeDetails)] : null,
                'rack_id' => rand(0, 1) ? $racks[array_rand($racks)] : null,
                'code' => 'MSD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'number_serial_first' => 'MSSN1-' . rand(1000, 9999),
                'number_serial_second' => 'MSSN2-' . rand(1000, 9999),
                'quantity' => rand(1, 100),
                'notes' => 'Material shipment detail notes ' . $i,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($materialShipmentDetails, 100) as $chunk) {
            DB::table('material_shipment_details')->insert($chunk);
        }
    }
}
