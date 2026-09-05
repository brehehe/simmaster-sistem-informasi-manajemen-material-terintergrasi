<?php

namespace Database\Seeders;

use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Rack\Rack;
use Illuminate\Database\Seeder;

class DemoDraftSppmSeeder extends Seeder
{
    /**
     * Run the database seeds for testing draft SPPMs.
     */
    public function run(): void
    {
        $polda = RegionalPolice::firstOrCreate(
            ['name' => 'Polda Jatim'],
            ['code' => 'POLDA-JATIM', 'is_active' => true]
        );

        // Ambik atau buat Polres Surabaya, Mojokerto, Gresik
        $polresSby = PoliceStation::firstOrCreate(
            ['name' => 'Polrestabes Surabaya'],
            ['code' => 'POLRES-SBY', 'regional_police_id' => $polda->id, 'is_active' => true]
        );

        $polresMjk = PoliceStation::firstOrCreate(
            ['name' => 'Polres Mojokerto Kota'],
            ['code' => 'POLRES-MJK', 'regional_police_id' => $polda->id, 'is_active' => true]
        );

        $polresGrk = PoliceStation::firstOrCreate(
            ['name' => 'Polres Gresik'],
            ['code' => 'POLRES-GRK', 'regional_police_id' => $polda->id, 'is_active' => true]
        );

        // Ambil stok detail & rak
        $stockDetail = StockDetail::first();
        $rack = Rack::first();
        $type = Type::first();
        $typeDetail = TypeDetail::first();

        // 1. SPPM Draft Surabaya
        $shp1 = MaterialShipment::updateOrCreate(
            ['code' => 'SHP-JATIM-20260805-001'],
            [
                'shipment_date' => now()->toDateString(),
                'status' => 'draft',
                'sender_regional_police_id' => $polda->id,
                'receiver_police_station_id' => $polresSby->id,
                'notes' => 'Pengiriman Material SIM Card & STNK Tahap I',
                'picker_name' => null,
                'picker_rank' => null,
                'picker_position' => null,
                'picker_signature' => null,
                'picker_photo' => null,
                'picked_at' => null,
                'is_active' => true,
            ]
        );

        MaterialShipmentDetail::updateOrCreate(
            ['material_shipment_id' => $shp1->id, 'code' => 'SIM-CARD'],
            [
                'stock_detail_id' => $stockDetail?->id,
                'rack_id' => $rack?->id,
                'type_id' => $type?->id,
                'type_detail_id' => $typeDetail?->id,
                'number_serial_first' => 'SBY-SIM-0001',
                'number_serial_second' => 'SBY-SIM-1000',
                'quantity' => 1000,
                'notes' => 'SIM Card R2',
                'is_active' => true,
            ]
        );

        // 2. SPPM Draft Mojokerto
        $shp2 = MaterialShipment::updateOrCreate(
            ['code' => 'SHP-JATIM-20260805-002'],
            [
                'shipment_date' => now()->toDateString(),
                'status' => 'draft',
                'sender_regional_police_id' => $polda->id,
                'receiver_police_station_id' => $polresMjk->id,
                'notes' => 'Distribusi Plat TNKB R2 & R4 ke Polres Mojokerto Kota',
                'picker_name' => null,
                'picker_rank' => null,
                'picker_position' => null,
                'picker_signature' => null,
                'picker_photo' => null,
                'picked_at' => null,
                'is_active' => true,
            ]
        );

        MaterialShipmentDetail::updateOrCreate(
            ['material_shipment_id' => $shp2->id, 'code' => 'TNKB-R2'],
            [
                'stock_detail_id' => $stockDetail?->id,
                'rack_id' => $rack?->id,
                'type_id' => $type?->id,
                'type_detail_id' => $typeDetail?->id,
                'number_serial_first' => 'MJK-TNKB-0001',
                'number_serial_second' => 'MJK-TNKB-0500',
                'quantity' => 500,
                'notes' => 'Plat TNKB Hitam R2',
                'is_active' => true,
            ]
        );

        // 3. SPPM Draft Gresik
        $shp3 = MaterialShipment::updateOrCreate(
            ['code' => 'SHP-JATIM-20260805-003'],
            [
                'shipment_date' => now()->toDateString(),
                'status' => 'draft',
                'sender_regional_police_id' => $polda->id,
                'receiver_police_station_id' => $polresGrk->id,
                'notes' => 'Pengiriman Material BPKB & STNK ke Polres Gresik',
                'picker_name' => null,
                'picker_rank' => null,
                'picker_position' => null,
                'picker_signature' => null,
                'picker_photo' => null,
                'picked_at' => null,
                'is_active' => true,
            ]
        );

        MaterialShipmentDetail::updateOrCreate(
            ['material_shipment_id' => $shp3->id, 'code' => 'BPKB-R4'],
            [
                'stock_detail_id' => $stockDetail?->id,
                'rack_id' => $rack?->id,
                'type_id' => $type?->id,
                'type_detail_id' => $typeDetail?->id,
                'number_serial_first' => 'GRK-BPKB-0001',
                'number_serial_second' => 'GRK-BPKB-0300',
                'quantity' => 300,
                'notes' => 'Material Buku BPKB R4',
                'is_active' => true,
            ]
        );
    }
}
