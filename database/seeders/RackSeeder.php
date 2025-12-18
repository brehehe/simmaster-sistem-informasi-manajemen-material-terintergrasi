<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RackSeeder extends Seeder
{
    public function run(): void
    {
        $regionalPolice = DB::table('regional_police')->pluck('id')->toArray();
        $policeStations = DB::table('police_stations')->pluck('id')->toArray();

        $racks = [];

        // Create racks for each regional police
        foreach ($regionalPolice as $index => $regionalPoliceId) {
            for ($i = 1; $i <= 20; $i++) {
                $racks[] = [
                    'id' => Str::uuid()->toString(),
                    'name' => 'Polda Rack ' . chr(65 + ($i - 1) % 26) . '-' . (ceil($i / 26)),
                    'regional_police_id' => $regionalPoliceId,
                    'police_station_id' => null,
                    'description' => 'Rack for Regional Police',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Create racks for each police station
        foreach ($policeStations as $index => $policeStationId) {
            for ($i = 1; $i <= 15; $i++) {
                $racks[] = [
                    'id' => Str::uuid()->toString(),
                    'name' => 'Polres Rack ' . chr(65 + ($i - 1) % 26) . '-' . (ceil($i / 26)),
                    'regional_police_id' => null,
                    'police_station_id' => $policeStationId,
                    'description' => 'Rack for Police Station',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach (array_chunk($racks, 100) as $chunk) {
            DB::table('racks')->insert($chunk);
        }
    }
}
