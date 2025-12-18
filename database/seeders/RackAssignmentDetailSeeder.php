<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RackAssignmentDetailSeeder extends Seeder
{
    public function run(): void
    {
        $rackAssignments = DB::table('rack_assignments')->pluck('id')->toArray();
        $stockDetails = DB::table('stock_details')->pluck('id')->toArray();
        $types = DB::table('types')->pluck('id')->toArray();
        $typeDetails = DB::table('type_details')->pluck('id')->toArray();
        $racks = DB::table('racks')->pluck('id')->toArray();

        $rackAssignmentDetails = [];

        for ($i = 1; $i <= 500; $i++) {
            $rackAssignmentDetails[] = [
                'id' => Str::uuid()->toString(),
                'rack_assignment_id' => $rackAssignments[array_rand($rackAssignments)],
                'stock_detail_id' => rand(0, 1) ? $stockDetails[array_rand($stockDetails)] : null,
                'type_id' => rand(0, 1) ? $types[array_rand($types)] : null,
                'type_detail_id' => rand(0, 1) ? $typeDetails[array_rand($typeDetails)] : null,
                'from_rack_id' => rand(0, 1) ? $racks[array_rand($racks)] : null,
                'to_rack_id' => $racks[array_rand($racks)],
                'item_code' => 'RADIC-' . rand(1000, 9999),
                'number_serial_first' => 'RADSN1-' . rand(1000, 9999),
                'number_serial_second' => 'RADSN2-' . rand(1000, 9999),
                'quantity' => rand(1, 50),
                'description' => 'Rack assignment detail data ' . $i,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($rackAssignmentDetails, 100) as $chunk) {
            DB::table('rack_assignment_details')->insert($chunk);
        }
    }
}
