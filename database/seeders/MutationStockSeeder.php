<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MutationStockSeeder extends Seeder
{
    public function run(): void
    {
        $regionalPolice = DB::table('regional_police')->pluck('id')->toArray();
        $policeStations = DB::table('police_stations')->pluck('id')->toArray();
        $users = DB::table('users')->pluck('id')->toArray();

        $mutationStocks = [];
        $statuses = ['draft', 'sent', 'received'];

        for ($i = 1; $i <= 500; $i++) {
            $status = $statuses[array_rand($statuses)];
            $sentAt = null;
            $receivedAt = null;
            $receivedBy = null;

            if ($status === 'sent' || $status === 'received') {
                $sentAt = now()->subDays(rand(5, 365));
            }

            if ($status === 'received') {
                $receivedAt = now()->subDays(rand(0, 5));
                $receivedBy = $users[array_rand($users)];
            }

            // Randomly choose sender type (Polda or Polres)
            $senderIsRegional = rand(0, 1);
            // Randomly choose receiver type (Polda or Polres)
            $receiverIsRegional = rand(0, 1);

            $mutationStocks[] = [
                'id' => Str::uuid()->toString(),
                'code' => 'MT-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'mutation_date' => now()->subDays(rand(0, 365))->format('Y-m-d'),
                'status' => $status,
                'sender_regional_police_id' => $senderIsRegional ? $regionalPolice[array_rand($regionalPolice)] : null,
                'sender_police_station_id' => !$senderIsRegional ? $policeStations[array_rand($policeStations)] : null,
                'receiver_regional_police_id' => $receiverIsRegional ? $regionalPolice[array_rand($regionalPolice)] : null,
                'receiver_police_station_id' => !$receiverIsRegional ? $policeStations[array_rand($policeStations)] : null,
                'notes' => 'Mutation stock notes ' . $i,
                'sent_at' => $sentAt,
                'received_at' => $receivedAt,
                'received_by' => $receivedBy,
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($mutationStocks, 100) as $chunk) {
            DB::table('mutation_stocks')->insert($chunk);
        }
    }
}
