<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaterialShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $regionalPolice = DB::table('regional_police')->pluck('id')->toArray();
        $policeStations = DB::table('police_stations')->pluck('id')->toArray();
        $users = DB::table('users')->pluck('id')->toArray();

        $materialShipments = [];
        $statuses = ['draft', 'shipped', 'received'];

        for ($i = 1; $i <= 500; $i++) {
            $status = $statuses[array_rand($statuses)];
            $shippedAt = null;
            $receivedAt = null;
            $receivedBy = null;

            if ($status === 'shipped' || $status === 'received') {
                $shippedAt = now()->subDays(rand(5, 365));
            }

            if ($status === 'received') {
                $receivedAt = now()->subDays(rand(0, 5));
                $receivedBy = $users[array_rand($users)];
            }

            $materialShipments[] = [
                'id' => Str::uuid()->toString(),
                'code' => 'MS-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'shipment_date' => now()->subDays(rand(0, 365))->format('Y-m-d'),
                'status' => $status,
                'sender_regional_police_id' => $regionalPolice[array_rand($regionalPolice)],
                'receiver_police_station_id' => $policeStations[array_rand($policeStations)],
                'notes' => 'Material shipment notes ' . $i,
                'shipped_at' => $shippedAt,
                'received_at' => $receivedAt,
                'received_by' => $receivedBy,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($materialShipments, 100) as $chunk) {
            DB::table('material_shipments')->insert($chunk);
        }
    }
}
