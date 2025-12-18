<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockOpnameSeeder extends Seeder
{
    public function run(): void
    {
        $regionalPolice = DB::table('regional_police')->pluck('id')->toArray();
        $policeStations = DB::table('police_stations')->pluck('id')->toArray();
        $users = DB::table('users')->pluck('id')->toArray();

        $stockOpnames = [];
        $statuses = ['draft', 'completed', 'approved'];

        for ($i = 1; $i <= 500; $i++) {
            $isRegional = rand(0, 1);
            $status = $statuses[array_rand($statuses)];

            $checkedBy = null;
            $approvedBy = null;
            $approvedAt = null;

            if ($status === 'completed' || $status === 'approved') {
                $checkedBy = $users[array_rand($users)];
            }

            if ($status === 'approved') {
                $approvedBy = $users[array_rand($users)];
                $approvedAt = now()->subDays(rand(0, 30));
            }

            $stockOpnames[] = [
                'id' => Str::uuid()->toString(),
                'code' => 'SO-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'opname_date' => now()->subDays(rand(0, 365))->format('Y-m-d'),
                'regional_police_id' => $isRegional ? $regionalPolice[array_rand($regionalPolice)] : null,
                'police_station_id' => !$isRegional ? $policeStations[array_rand($policeStations)] : null,
                'status' => $status,
                'notes' => 'Stock opname notes ' . $i,
                'checked_by' => $checkedBy,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($stockOpnames, 100) as $chunk) {
            DB::table('stock_opnames')->insert($chunk);
        }
    }
}
