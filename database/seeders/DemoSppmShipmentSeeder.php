<?php

namespace Database\Seeders;

use App\Models\Message\Message;
use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Spatie\Role;
use App\Models\Stock\StockDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSppmShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds for demo SPPM shipments & Polres accounts.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $polda = RegionalPolice::firstOrCreate(
            ['name' => 'Polda Jatim'],
            ['code' => 'POLDA-JATIM', 'is_active' => true]
        );

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin ARMASTER',
                'password' => $password,
                'level_menu' => 1,
                'is_active' => true,
            ]
        );

        // 5 Target Polres Data
        $targetPolresList = [
            [
                'station_name' => 'Polrestabes Surabaya',
                'account_name' => 'Polrestabes Surabaya',
                'email' => 'polrestabes-surabaya@sbst.test',
                'sppm_code' => 'SPPM/SHP-20260721-SBY',
                'serial_first' => 'SBY-2026-0001',
                'serial_second' => 'SBY-2026-1000',
                'quantity' => 1000,
                'notes' => 'Pengiriman Materiel SIM Card 1.000 Unit ke Polrestabes Surabaya',
            ],
            [
                'station_name' => 'Polres Gresik',
                'account_name' => 'Polres Gresik',
                'email' => 'polres-gresik@sbst.test',
                'sppm_code' => 'SPPM/SHP-20260721-GRK',
                'serial_first' => 'GRK-2026-0001',
                'serial_second' => 'GRK-2026-0500',
                'quantity' => 500,
                'notes' => 'Pengiriman Materiel STNK 500 Unit ke Polres Gresik',
            ],
            [
                'station_name' => 'Polres Mojokerto Kota',
                'account_name' => 'Polres Mojokerto Kota',
                'email' => 'polres-mojokerto-kota@sbst.test',
                'sppm_code' => 'SPPM/SHP-20260721-MJK',
                'serial_first' => 'MJK-2026-0001',
                'serial_second' => 'MJK-2026-0350',
                'quantity' => 350,
                'notes' => 'Pengiriman Materiel TNKB REG 350 Unit ke Polres Mojokerto Kota',
            ],
            [
                'station_name' => 'Polres Mojokerto',
                'account_name' => 'Polres Mojokerto',
                'email' => 'polres-mojokerto@sbst.test',
                'sppm_code' => 'SPPM/SHP-20260721-MOJ',
                'serial_first' => 'MOJ-2026-0001',
                'serial_second' => 'MOJ-2026-0400',
                'quantity' => 400,
                'notes' => 'Pengiriman Materiel BPKB 400 Unit ke Polres Mojokerto',
            ],
            [
                'station_name' => 'Polres Jombang',
                'account_name' => 'Polres Jombang',
                'email' => 'polres-jombang@sbst.test',
                'sppm_code' => 'SPPM/SHP-20260721-JBG',
                'serial_first' => 'JBG-2026-0001',
                'serial_second' => 'JBG-2026-0600',
                'quantity' => 600,
                'notes' => 'Pengiriman Materiel SIM Card 600 Unit ke Polres Jombang',
            ],
        ];

        // Fetch a base stock detail from Polda
        $baseStockDetail = StockDetail::where('regional_police_id', $polda->id)->first()
            ?? StockDetail::first();

        foreach ($targetPolresList as $idx => $item) {
            // 1. Ensure PoliceStation
            $station = PoliceStation::firstOrCreate(
                ['name' => $item['station_name']],
                ['regional_police_id' => $polda->id, 'is_active' => true]
            );

            // 2. Ensure User Account
            $user = User::where('email', $item['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $item['account_name'],
                    'email' => $item['email'],
                    'password' => $password,
                    'police_station_id' => $station->id,
                    'regional_police_id' => $polda->id,
                    'level_menu' => 3,
                    'is_active' => true,
                ]);
            } else {
                $user->update([
                    'police_station_id' => $station->id,
                    'regional_police_id' => $polda->id,
                    'password' => $password,
                ]);
            }

            // Assign Polres Role
            if (!$user->hasRole('Polres')) {
                $user->assignRole('Polres');
            }

            // 3. Create Demo SPPM Shipment (Status: shipped)
            $shipment = MaterialShipment::where('code', $item['sppm_code'])->first();
            if (!$shipment) {
                $shipment = MaterialShipment::create([
                    'code' => $item['sppm_code'],
                    'shipment_date' => now()->format('Y-m-d'),
                    'status' => 'shipped',
                    'shipped_at' => now(),
                    'sender_regional_police_id' => $polda->id,
                    'receiver_police_station_id' => $station->id,
                    'notes' => $item['notes'],
                    'is_active' => true,
                ]);

                if ($baseStockDetail) {
                    MaterialShipmentDetail::create([
                        'material_shipment_id' => $shipment->id,
                        'stock_detail_id' => $baseStockDetail->id,
                        'type_id' => $baseStockDetail->type_id,
                        'type_detail_id' => $baseStockDetail->type_detail_id,
                        'code' => $item['sppm_code'],
                        'number_serial_first' => $item['serial_first'],
                        'number_serial_second' => $item['serial_second'],
                        'quantity' => $item['quantity'],
                        'notes' => $item['notes'],
                        'is_active' => true,
                    ]);
                }
            }

            // 4. Create Inbox Message (Message) for Polres
            $existingMessage = Message::where('receiver_police_station_id', $station->id)
                ->where('subject', 'like', '%' . $item['sppm_code'] . '%')
                ->first();

            if (!$existingMessage) {
                Message::create([
                    'code' => 'MSG-' . date('Ymd') . '-' . str_pad($idx + 10, 4, '0', STR_PAD_LEFT),
                    'sender_id' => $adminUser->id,
                    'sender_regional_police_id' => $polda->id,
                    'receiver_type' => 'polres',
                    'receiver_police_station_id' => $station->id,
                    'category' => 'general_info',
                    'subject' => '📱 SPPM Berkode QR: ' . $item['sppm_code'],
                    'message' => "Telah diterbitkan Surat Perintah Pengeluaran Materiel (SPPM) nomor {$item['sppm_code']} dari Polda Jatim untuk {$station->name}. Silakan lakukan verifikasi dan konfirmasi penerimaan barang.",
                    'is_read' => false,
                    'is_active' => true,
                ]);
            }
        }
    }
}
