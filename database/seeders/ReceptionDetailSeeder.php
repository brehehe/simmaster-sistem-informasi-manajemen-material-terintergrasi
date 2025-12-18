<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReceptionDetailSeeder extends Seeder
{
    public function run(): void
    {
        $receptions = DB::table('receptions')->pluck('id')->toArray();
        $types = DB::table('types')->pluck('id')->toArray();
        $typeDetails = DB::table('type_details')->pluck('id')->toArray();
        $racks = DB::table('racks')->pluck('id')->toArray();

        $receptionDetails = [];

        for ($i = 1; $i <= 500; $i++) {
            $receptionDetails[] = [
                'id' => Str::uuid()->toString(),
                'reception_id' => $receptions[array_rand($receptions)],
                'type_id' => rand(0, 1) ? $types[array_rand($types)] : null,
                'type_detail_id' => rand(0, 1) ? $typeDetails[array_rand($typeDetails)] : null,
                'rack_id' => rand(0, 1) ? $racks[array_rand($racks)] : null,
                'code' => 'RCPD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'number_serial_first' => 'RCSN1-' . rand(1000, 9999),
                'number_serial_second' => 'RCSN2-' . rand(1000, 9999),
                'quantity' => rand(1, 100),
                'description' => 'Reception detail data ' . $i,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($receptionDetails, 100) as $chunk) {
            DB::table('reception_details')->insert($chunk);
        }
    }
}
