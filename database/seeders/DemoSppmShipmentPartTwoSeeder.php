<?php

namespace Database\Seeders;

use App\Models\Message\Message;
use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Stock\StockDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSppmShipmentPartTwoSeeder extends Seeder
{
    /**
     * Run the database seeds for demo SPPM shipments & Polres accounts (Part 2).
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

        // 6 New Target Polres Data
        $targetPolresList = [
            [
                'station_name' => 'Polres Kediri Kota',
                'account_name' => 'Polres Kediri Kota',
                'email' => 'polres-kediri-kota@sbst.test',
                'sppm_code' => 'SPPM/SHP-20260721-KDK',
                'serial_first' => 'KDK-2026-0001',
                'serial_second' => 'KDK-2026-0300',
                'quantity' => 300,
                'notes' => 'Pengiriman Materiel SIM Card 300 Unit ke Polres Kediri Kota',
            ],
            [
                'station_name' => 'Polres Kediri',
                'account_name' => 'Polres Kediri',
                'email' => 'polres-kediri@sbst.test',
                'sppm_code' => 'SPPM/SHP-20260721-KDB',
                'serial_first' => 'KDB-2026-0001',
                'serial_second' => 'KDB-2026-0450',
                'quantity' => 450,
                'notes' => 'Pengiriman Materiel STNK 450 Unit ke Polres Kediri',
            ],
            [
                'station_name' => 'Polres Blitar Kota',
                'account_name' => 'Polres Blitar Kota',
                'email' => 'polres-blitar-kota@sbst.test',
                'sppm_code' => 'SPPM/SHP-20260721-BLK',
                'serial_first' => 'BLK-2026-0001',
                'serial_second' => 'BLK-2026-0200',
                'quantity' => 200,
                'notes' => 'Pengiriman Materiel BPKB 200 Unit ke Polres Blitar Kota',
            ],
            [
                'station_name' => 'Polres Blitar',
                'account_name' => 'Polres Blitar',
                'email' => 'polres-blitar@sbst.test',
                'sppm_code' => 'SPPM/SHP-20260721-BLB',
                'serial_first' => 'BLB-2026-0001',
                'serial_second' => 'BLB-2026-0350',
                'quantity' => 350,
                'notes' => 'Pengiriman Materiel TNKB REG 350 Unit ke Polres Blitar',
            ],
            [
                'station_name' => 'Polres Trenggalek',
                'account_name' => 'Polres Trenggalek',
                'email' => 'polres-trenggalek@sbst.test',
                'sppm_code' => 'SPPM/SHP-20260721-TRG',
                'serial_first' => 'TRG-2026-0001',
                'serial_second' => 'TRG-2026-0250',
                'quantity' => 250,
                'notes' => 'Pengiriman Materiel SIM Card 250 Unit ke Polres Trenggalek',
            ],
            [
                'station_name' => 'Polres Tulungagung',
                'account_name' => 'Polres Tulungagung',
                'email' => 'polres-tulungagung@sbst.test',
                'sppm_code' => 'SPPM/SHP-20260721-TLG',
                'serial_first' => 'TLG-2026-0001',
                'serial_second' => 'TLG-2026-0400',
                'quantity' => 400,
                'notes' => 'Pengiriman Materiel STNK 400 Unit ke Polres Tulungagung',
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
                    'code' => 'MSG-' . date('Ymd') . '-' . str_pad($idx + 30, 4, '0', STR_PAD_LEFT),
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
