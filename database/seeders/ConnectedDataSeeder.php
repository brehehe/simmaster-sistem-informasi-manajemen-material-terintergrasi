<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Police\RegionalPolice;
use App\Models\Police\PoliceStation;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Rack\Rack;
use App\Models\Reception\Reception;
use App\Models\Reception\ReceptionDetail;
use App\Models\Reception\ReceptionDetailItem;
use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail;
use App\Models\MenuPolda\RackAssignment\RackAssignment;
use App\Models\MenuPolda\RackAssignment\RackAssignmentDetail;
use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail;
use App\Models\StockOpname\StockOpname;
use App\Models\StockOpname\StockOpnameDetail;
use App\Models\Models\MenuPolda\MutationStock\MutationStock;
use App\Models\Models\MenuPolda\MutationStock\MutationStockDetail;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\HistoryStockDetail;
use App\Models\User;
use App\Services\StockService;

class ConnectedDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stockService = new StockService();

        // 1. Clean up transaction and stock tables first (deleting in order of dependency)
        $this->command->info('Cleaning up transaction and stock tables...');
        
        DB::table('mutation_stock_details')->delete();
        DB::table('mutation_stocks')->delete();
        DB::table('stock_opname_details')->delete();
        DB::table('stock_opnames')->delete();
        DB::table('material_damage_details')->delete();
        DB::table('material_damages')->delete();
        DB::table('material_usage_details')->delete();
        DB::table('material_usages')->delete();
        DB::table('material_shipment_details')->delete();
        DB::table('material_shipments')->delete();
        DB::table('rack_assignment_details')->delete();
        DB::table('rack_assignments')->delete();
        DB::table('history_stock_details')->delete();
        DB::table('history_stocks')->delete();
        DB::table('stock_details')->delete();
        DB::table('stocks')->delete();
        DB::table('reception_detail_items')->delete();
        DB::table('reception_details')->delete();
        DB::table('receptions')->delete();

        // 2. Fetch master data
        $poldas = RegionalPolice::where('is_active', true)->get();
        $types = Type::where('is_active', true)->get();
        
        $simCardType = $types->firstWhere('name', 'SIM CARD');
        $stnkType = $types->firstWhere('name', 'STNK');
        $tnkbRegType = $types->firstWhere('name', 'TNKB REG');
        
        $ymcktType = $types->firstWhere('name', 'YMCKT');
        $laminasiType = $types->firstWhere('name', 'LAMINASI');

        if (!$simCardType || !$stnkType || !$tnkbRegType) {
            $this->command->error('Missing essential material types! Make sure TypeSeeder is run first.');
            return;
        }

        foreach ($poldas as $polda) {
            $poldaCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $polda->name), 0, 5));
            $this->command->info("Seeding data for Polda: {$polda->name} (Code: {$poldaCode})");

            // Racks at Polda
            $poldaRacks = Rack::where('regional_police_id', $polda->id)
                ->whereNull('police_station_id')
                ->where('is_active', true)
                ->get();
            
            if ($poldaRacks->isEmpty()) {
                $this->command->warn("No racks found for Polda {$polda->name}. Skipping rack assignment details.");
            }

            // ----------------------------------------------------
            // STEP A: Polda Receives Material (Receptions / BAPPM)
            // ----------------------------------------------------
            
            // Reception 1: SIM CARD + YMCKT + LAMINASI
            $rec1 = Reception::create([
                'code' => "SPPM/001/{$poldaCode}/2026/KORLANTAS",
                'name' => 'Penerimaan SIM Card Tahap I ' . $polda->name,
                'date' => Carbon::now()->subDays(30)->format('Y-m-d'),
                'sppm_date' => Carbon::now()->subDays(32)->format('Y-m-d'),
                'bappm_number' => "BAPPM/001/{$poldaCode}/V/2026/Ditlantas",
                'type' => 'penerimaan',
                'type_id' => $simCardType->id,
                'regional_police_id' => $polda->id,
                'police_station_id' => null,
                'description' => 'Berita Acara Penerimaan SIM Card Utama & Pendukung',
                'commission_member_1_name' => 'YANTO MULYANTO P, S.H., S.I.K., M.H., M.Si.',
                'commission_member_1_rank' => 'AKBP',
                'commission_member_1_nip' => '86052014',
                'commission_member_1_position' => 'KASUBDIT REGIDENT',
                'commission_member_2_name' => 'MADE DAMENDRA, S.H.',
                'commission_member_2_rank' => 'IPTU',
                'commission_member_2_nip' => '84071376',
                'commission_member_2_position' => 'PAMIN I FASMAT SBST',
                'commission_member_3_name' => 'PUJI ISWANTO, S.H.',
                'commission_member_3_rank' => 'AIPTU',
                'commission_member_3_nip' => '76080941',
                'commission_member_3_position' => 'BAUR FASMAT SBST',
                'kasi_fasmat_name' => 'AYIP RIZAL, S.E., M.M.',
                'kasi_fasmat_rank' => 'KOMPOL',
                'kasi_fasmat_nip' => '84091823',
                'ordonatur_name' => 'IWAN SAKTIADI, S.I.K., M.M., M.Si',
                'ordonatur_rank' => 'BRIGADIR JENDERAL POLISI',
                'is_active' => true,
            ]);

            $rd1 = ReceptionDetail::create([
                'reception_id' => $rec1->id,
                'type_id' => $simCardType->id,
                'quantity' => 10007,
                'is_active' => true,
            ]);

            // Item 1: SIM CARD (Main)
            $item1 = ReceptionDetailItem::create([
                'reception_id' => $rec1->id,
                'reception_detail_id' => $rd1->id,
                'type_id' => $simCardType->id,
                'item_code' => 'SIMC',
                'number_serial_first' => 'SIM-00001',
                'number_serial_second' => 'SIM-10000',
                'quantity' => 10000,
                'description' => 'SIM Card Utama',
                'is_active' => true,
            ]);
            HistoryStockDetail::create([
                'code' => 'HSD-' . Str::random(8),
                'reception_detail_item_id' => $item1->id,
                'type_id' => $simCardType->id,
                'regional_police_id' => $polda->id,
                'date' => $rec1->date,
                'serial_number' => 'SIMC SIM-00001 SIM-10000',
                'status_type' => 'in',
                'quantity' => 10000,
                'is_active' => true,
            ]);

            // Item 2: YMCKT (Supporting)
            if ($ymcktType) {
                $item2 = ReceptionDetailItem::create([
                    'reception_id' => $rec1->id,
                    'reception_detail_id' => $rd1->id,
                    'type_id' => $ymcktType->id,
                    'item_code' => 'YMCKT',
                    'quantity' => 5,
                    'description' => 'Ribbon YMCKT Pendukung',
                    'is_active' => true,
                ]);
                HistoryStockDetail::create([
                    'code' => 'HSD-' . Str::random(8),
                    'reception_detail_item_id' => $item2->id,
                    'type_id' => $ymcktType->id,
                    'regional_police_id' => $polda->id,
                    'date' => $rec1->date,
                    'serial_number' => 'YMCKT',
                    'status_type' => 'in',
                    'quantity' => 5,
                    'is_active' => true,
                ]);
            }

            // Item 3: LAMINASI (Supporting)
            if ($laminasiType) {
                $item3 = ReceptionDetailItem::create([
                    'reception_id' => $rec1->id,
                    'reception_detail_id' => $rd1->id,
                    'type_id' => $laminasiType->id,
                    'item_code' => 'LAMINASI',
                    'quantity' => 2,
                    'description' => 'Laminating Film Pendukung',
                    'is_active' => true,
                ]);
                HistoryStockDetail::create([
                    'code' => 'HSD-' . Str::random(8),
                    'reception_detail_item_id' => $item3->id,
                    'type_id' => $laminasiType->id,
                    'regional_police_id' => $polda->id,
                    'date' => $rec1->date,
                    'serial_number' => 'LAMINASI',
                    'status_type' => 'in',
                    'quantity' => 2,
                    'is_active' => true,
                ]);
            }

            $stockService->processReception($rec1);

            // Reception 2: STNK
            $rec2 = Reception::create([
                'code' => "SPPM/002/{$poldaCode}/2026/KORLANTAS",
                'name' => 'Penerimaan Blangko STNK ' . $polda->name,
                'date' => Carbon::now()->subDays(25)->format('Y-m-d'),
                'sppm_date' => Carbon::now()->subDays(28)->format('Y-m-d'),
                'bappm_number' => "BAPPM/002/{$poldaCode}/V/2026/Ditlantas",
                'type' => 'penerimaan',
                'type_id' => $stnkType->id,
                'regional_police_id' => $polda->id,
                'police_station_id' => null,
                'description' => 'Berita Acara Penerimaan Blangko STNK',
                'commission_member_1_name' => 'YANTO MULYANTO P, S.H., S.I.K., M.H., M.Si.',
                'commission_member_1_rank' => 'AKBP',
                'commission_member_1_nip' => '86052014',
                'commission_member_1_position' => 'KASUBDIT REGIDENT',
                'commission_member_2_name' => 'MADE DAMENDRA, S.H.',
                'commission_member_2_rank' => 'IPTU',
                'commission_member_2_nip' => '84071376',
                'commission_member_2_position' => 'PAMIN I FASMAT SBST',
                'commission_member_3_name' => 'PUJI ISWANTO, S.H.',
                'commission_member_3_rank' => 'AIPTU',
                'commission_member_3_nip' => '76080941',
                'commission_member_3_position' => 'BAUR FASMAT SBST',
                'kasi_fasmat_name' => 'AYIP RIZAL, S.E., M.M.',
                'kasi_fasmat_rank' => 'KOMPOL',
                'kasi_fasmat_nip' => '84091823',
                'ordonatur_name' => 'IWAN SAKTIADI, S.I.K., M.M., M.Si',
                'ordonatur_rank' => 'BRIGADIR JENDERAL POLISI',
                'is_active' => true,
            ]);

            $rd2 = ReceptionDetail::create([
                'reception_id' => $rec2->id,
                'type_id' => $stnkType->id,
                'quantity' => 5000,
                'is_active' => true,
            ]);

            $itemStnk = ReceptionDetailItem::create([
                'reception_id' => $rec2->id,
                'reception_detail_id' => $rd2->id,
                'type_id' => $stnkType->id,
                'item_code' => 'STNK',
                'number_serial_first' => 'STN-00001',
                'number_serial_second' => 'STN-05000',
                'quantity' => 5000,
                'description' => 'Blangko STNK Utama',
                'is_active' => true,
            ]);
            HistoryStockDetail::create([
                'code' => 'HSD-' . Str::random(8),
                'reception_detail_item_id' => $itemStnk->id,
                'type_id' => $stnkType->id,
                'regional_police_id' => $polda->id,
                'date' => $rec2->date,
                'serial_number' => 'STNK STN-00001 STN-05000',
                'status_type' => 'in',
                'quantity' => 5000,
                'is_active' => true,
            ]);

            $stockService->processReception($rec2);

            // Reception 3: TNKB REG (With Type Details)
            $rec3 = Reception::create([
                'code' => "SPPM/003/{$poldaCode}/2026/KORLANTAS",
                'name' => 'Penerimaan Plat TNKB REG ' . $polda->name,
                'date' => Carbon::now()->subDays(20)->format('Y-m-d'),
                'sppm_date' => Carbon::now()->subDays(22)->format('Y-m-d'),
                'bappm_number' => "BAPPM/003/{$poldaCode}/V/2026/Ditlantas",
                'type' => 'penerimaan',
                'type_id' => $tnkbRegType->id,
                'regional_police_id' => $polda->id,
                'police_station_id' => null,
                'description' => 'Berita Acara Penerimaan Plat Nomor TNKB REG',
                'commission_member_1_name' => 'YANTO MULYANTO P, S.H., S.I.K., M.H., M.Si.',
                'commission_member_1_rank' => 'AKBP',
                'commission_member_1_nip' => '86052014',
                'commission_member_1_position' => 'KASUBDIT REGIDENT',
                'commission_member_2_name' => 'MADE DAMENDRA, S.H.',
                'commission_member_2_rank' => 'IPTU',
                'commission_member_2_nip' => '84071376',
                'commission_member_2_position' => 'PAMIN I FASMAT SBST',
                'commission_member_3_name' => 'PUJI ISWANTO, S.H.',
                'commission_member_3_rank' => 'AIPTU',
                'commission_member_3_nip' => '76080941',
                'commission_member_3_position' => 'BAUR FASMAT SBST',
                'kasi_fasmat_name' => 'AYIP RIZAL, S.E., M.M.',
                'kasi_fasmat_rank' => 'KOMPOL',
                'kasi_fasmat_nip' => '84091823',
                'ordonatur_name' => 'IWAN SAKTIADI, S.I.K., M.M., M.Si',
                'ordonatur_rank' => 'BRIGADIR JENDERAL POLISI',
                'is_active' => true,
            ]);

            $tnkbDetails = TypeDetail::where('type_id', $tnkbRegType->id)->get();
            $rd3 = ReceptionDetail::create([
                'reception_id' => $rec3->id,
                'type_id' => $tnkbRegType->id,
                'quantity' => $tnkbDetails->count() * 1000,
                'is_active' => true,
            ]);

            foreach ($tnkbDetails as $detail) {
                $itemTnkb = ReceptionDetailItem::create([
                    'reception_id' => $rec3->id,
                    'reception_detail_id' => $rd3->id,
                    'type_id' => $tnkbRegType->id,
                    'type_detail_id' => $detail->id,
                    'item_code' => 'TNKB',
                    'quantity' => 1000,
                    'description' => 'Plat TNKB REG ' . $detail->name,
                    'is_active' => true,
                ]);

                HistoryStockDetail::create([
                    'code' => 'HSD-' . Str::random(8),
                    'reception_detail_item_id' => $itemTnkb->id,
                    'type_id' => $tnkbRegType->id,
                    'type_detail_id' => $detail->id,
                    'regional_police_id' => $polda->id,
                    'date' => $rec3->date,
                    'serial_number' => 'TNKB ' . $detail->name,
                    'status_type' => 'in',
                    'quantity' => 1000,
                    'is_active' => true,
                ]);
            }

            $stockService->processReception($rec3);

            // ----------------------------------------------------
            // STEP B: Polda Assigns Stock to Racks (RackAssignment)
            // ----------------------------------------------------
            if ($poldaRacks->isNotEmpty()) {
                $poldaRackA = $poldaRacks->first();
                $poldaRackB = $poldaRacks->count() > 1 ? $poldaRacks->get(1) : $poldaRackA;

                $rackAssign = RackAssignment::create([
                    'code' => 'RA-' . $poldaCode . '-' . rand(100, 999),
                    'date' => Carbon::now()->subDays(18)->format('Y-m-d'),
                    'regional_police_id' => $polda->id,
                    'police_station_id' => null,
                    'description' => 'Penataan awal ke Rak Gudang Polda',
                    'is_active' => true,
                ]);

                // Find Polda stock details currently without rack
                $poldaStocksNoRack = StockDetail::where('regional_police_id', $polda->id)
                    ->whereNull('police_station_id')
                    ->whereNull('rack_id')
                    ->where('quantity', '>', 0)
                    ->get();

                foreach ($poldaStocksNoRack as $sd) {
                    $targetRack = ($sd->type_id === $simCardType->id) ? $poldaRackA->id : $poldaRackB->id;
                    
                    RackAssignmentDetail::create([
                        'rack_assignment_id' => $rackAssign->id,
                        'stock_detail_id' => $sd->id,
                        'type_id' => $sd->type_id,
                        'type_detail_id' => $sd->type_detail_id,
                        'from_rack_id' => null,
                        'to_rack_id' => $targetRack,
                        'item_code' => $sd->code ?? '',
                        'number_serial_first' => $sd->number_serial_first ?? '',
                        'number_serial_second' => $sd->number_serial_second ?? '',
                        'quantity' => $sd->quantity,
                        'description' => 'Pindahan ke ' . ($sd->type_id === $simCardType->id ? $poldaRackA->name : $poldaRackB->name),
                        'is_active' => true,
                    ]);
                }

                $stockService->processRackAssignment($rackAssign);
            }

            // ----------------------------------------------------
            // STEP C: Polda Distributes to Polres (MaterialShipments)
            // ----------------------------------------------------
            $polresStations = PoliceStation::where('regional_police_id', $polda->id)
                ->where('is_active', true)
                ->get();

            foreach ($polresStations as $polres) {
                $polresCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $polres->name), 0, 5));

                // Get Polres user to mark receipt
                $polresUser = User::role('Polres')->where('police_station_id', $polres->id)->first() 
                    ?? User::where('police_station_id', $polres->id)->first() 
                    ?? User::first();

                // Find Polda rack stock details to ship
                $poldaRackStocks = StockDetail::where('regional_police_id', $polda->id)
                    ->whereNull('police_station_id')
                    ->whereNotNull('rack_id')
                    ->where('quantity', '>', 0)
                    ->get();

                if ($poldaRackStocks->isEmpty()) {
                    continue;
                }

                // Shipment 1: Shipped & Received
                $shipment = MaterialShipment::create([
                    'code' => 'SHP-' . $poldaCode . '-' . $polresCode . '-' . substr($polres->id, -6) . '-26',
                    'shipment_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                    'status' => 'draft',
                    'sender_regional_police_id' => $polda->id,
                    'receiver_police_station_id' => $polres->id,
                    'notes' => 'Droping Material Rutin Bulanan',
                    'is_active' => true,
                ]);

                foreach ($poldaRackStocks as $sd) {
                    $shipQty = ($sd->type_id === $simCardType->id) ? 1000 : 500;
                    if ($sd->quantity < $shipQty) {
                        $shipQty = $sd->quantity;
                    }

                    if ($shipQty <= 0) continue;

                    MaterialShipmentDetail::create([
                        'material_shipment_id' => $shipment->id,
                        'stock_detail_id' => $sd->id,
                        'rack_id' => $sd->rack_id,
                        'type_id' => $sd->type_id,
                        'type_detail_id' => $sd->type_detail_id,
                        'code' => $sd->code ?? '',
                        'number_serial_first' => $sd->number_serial_first ?? '',
                        'number_serial_second' => $sd->number_serial_second ?? '',
                        'quantity' => $shipQty,
                        'notes' => 'Pengiriman ' . ($sd->typeDetail->name ?? $sd->type->name),
                        'is_active' => true,
                    ]);
                }

                // Process shipment transitions
                $shipment->markAsShipped();
                if ($polresUser) {
                    $shipment->markAsReceived($polresUser);
                }

                // ----------------------------------------------------
                // STEP D: Polres Assigns Received Stock to Polres Racks
                // ----------------------------------------------------
                $polresRacks = Rack::where('police_station_id', $polres->id)
                    ->where('is_active', true)
                    ->get();

                if ($polresRacks->isNotEmpty()) {
                    $polresRack = $polresRacks->first();

                    $polresRackAssign = RackAssignment::create([
                        'code' => 'RA-' . $polresCode . '-' . rand(100, 999),
                        'date' => Carbon::now()->subDays(8)->format('Y-m-d'),
                        'regional_police_id' => null,
                        'police_station_id' => $polres->id,
                        'description' => 'Pemasukan material kiriman Polda ke Rak Polres',
                        'is_active' => true,
                    ]);

                    // Find Polres stocks currently without rack
                    $polresStocksNoRack = StockDetail::where('police_station_id', $polres->id)
                        ->whereNull('rack_id')
                        ->where('quantity', '>', 0)
                        ->get();

                    foreach ($polresStocksNoRack as $sd) {
                        RackAssignmentDetail::create([
                            'rack_assignment_id' => $polresRackAssign->id,
                            'stock_detail_id' => $sd->id,
                            'type_id' => $sd->type_id,
                            'type_detail_id' => $sd->type_detail_id,
                            'from_rack_id' => null,
                            'to_rack_id' => $polresRack->id,
                            'item_code' => $sd->code ?? '',
                            'number_serial_first' => $sd->number_serial_first ?? '',
                            'number_serial_second' => $sd->number_serial_second ?? '',
                            'quantity' => $sd->quantity,
                            'description' => 'Penataan ke Rak Polres ' . $polresRack->name,
                            'is_active' => true,
                        ]);
                    }

                    $stockService->processRackAssignment($polresRackAssign);
                }

                // ----------------------------------------------------
                // STEP E: Polres Uses Material (MaterialUsage)
                // ----------------------------------------------------
                $polresRackStocks = StockDetail::where('police_station_id', $polres->id)
                    ->whereNotNull('rack_id')
                    ->where('quantity', '>', 0)
                    ->get();

                if ($polresRackStocks->isNotEmpty()) {
                    $usage = MaterialUsage::create([
                        'code' => 'USG-' . $polresCode . '-' . rand(1000, 9999),
                        'date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                        'police_station_id' => $polres->id,
                        'description' => 'Penggunaan materiil untuk pelayanan registrasi harian',
                        'is_active' => true,
                    ]);

                    foreach ($polresRackStocks as $sd) {
                        $useQty = ($sd->type_id === $simCardType->id) ? 12 : 8;
                        if ($sd->quantity < $useQty) {
                            $useQty = $sd->quantity;
                        }

                        if ($useQty <= 0) continue;

                        MaterialUsageDetail::create([
                            'material_usage_id' => $usage->id,
                            'stock_detail_id' => $sd->id,
                            'type_id' => $sd->type_id,
                            'type_detail_id' => $sd->type_detail_id,
                            'rack_id' => $sd->rack_id,
                            'item_code' => $sd->code ?? '',
                            'number_serial_first' => $sd->number_serial_first ?? '',
                            'number_serial_second' => $sd->number_serial_second ?? '',
                            'quantity' => $useQty,
                            'usage_type' => 'used',
                            'description' => 'Pelayanan pemohon SIM/STNK/TNKB baru',
                            'is_active' => true,
                        ]);
                    }

                    $stockService->processMaterialUsage($usage);
                }

                // ----------------------------------------------------
                // STEP F: Polres Damaged Material (MaterialDamage)
                // ----------------------------------------------------
                $polresRackStocksFresh = StockDetail::where('police_station_id', $polres->id)
                    ->whereNotNull('rack_id')
                    ->where('quantity', '>', 0)
                    ->get();

                if ($polresRackStocksFresh->isNotEmpty()) {
                    $damage = MaterialDamage::create([
                        'code' => 'DMG-' . $polresCode . '-' . rand(1000, 9999),
                        'date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                        'police_station_id' => $polres->id,
                        'description' => 'Laporan material rusak dan cacat produksi',
                        'is_active' => true,
                    ]);

                    foreach ($polresRackStocksFresh as $sd) {
                        $dmgQty = 1;
                        if ($sd->quantity < $dmgQty) {
                            $dmgQty = $sd->quantity;
                        }

                        if ($dmgQty <= 0) continue;

                        MaterialDamageDetail::create([
                            'material_damage_id' => $damage->id,
                            'stock_detail_id' => $sd->id,
                            'type_id' => $sd->type_id,
                            'type_detail_id' => $sd->type_detail_id,
                            'rack_id' => $sd->rack_id,
                            'item_code' => $sd->code ?? '',
                            'number_serial_first' => $sd->number_serial_first ?? '',
                            'number_serial_second' => $sd->number_serial_second ?? '',
                            'quantity' => $dmgQty,
                            'damage_type' => 'damaged',
                            'reason' => 'Cacat cetak/pita ribbon terputus saat pencetakan',
                            'description' => 'Rusak internal',
                            'is_active' => true,
                        ]);
                    }

                    $stockService->processMaterialDamage($damage);
                }
            }
        }

        // 8. Seed Stock Opnames (Audit)
        $this->command->info('Seeding mock Stock Opnames...');
        $allPolreses = PoliceStation::all();
        $adminUser = User::role('Admin')->first() ?? User::first();
        
        foreach ($allPolreses->take(3) as $polres) {
            $polresCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $polres->name), 0, 5));
            $so = StockOpname::create([
                'code' => 'SO-' . $polresCode . '-' . substr($polres->id, -6) . '-' . date('Ymd'),
                'opname_date' => Carbon::now()->format('Y-m-d'),
                'police_station_id' => $polres->id,
                'status' => 'completed',
                'notes' => 'Stock opname rutin bulanan Polres',
                'checked_by' => $adminUser?->id,
                'is_active' => true,
            ]);

            $polresStocks = StockDetail::where('police_station_id', $polres->id)->get();
            foreach ($polresStocks as $sd) {
                StockOpnameDetail::create([
                    'stock_opname_id' => $so->id,
                    'stock_detail_id' => $sd->id,
                    'type_id' => $sd->type_id,
                    'type_detail_id' => $sd->type_detail_id,
                    'rack_id' => $sd->rack_id,
                    'code' => $sd->code ?? '',
                    'number_serial_first' => $sd->number_serial_first ?? '',
                    'number_serial_second' => $sd->number_serial_second ?? '',
                    'system_quantity' => $sd->quantity,
                    'physical_quantity' => $sd->quantity,
                    'difference' => 0,
                    'notes' => 'Cocok',
                    'is_active' => true,
                ]);
            }
        }

        // 9. Seed Mutations (Optional mock records)
        $this->command->info('Seeding mock Mutations...');
        if ($poldas->count() >= 1 && $allPolreses->count() >= 2) {
            $sender = $allPolreses->get(0);
            $receiver = $allPolreses->get(1);

            $mut = MutationStock::create([
                'code' => 'MUT-2026-0001',
                'mutation_date' => Carbon::now()->format('Y-m-d'),
                'status' => 'received',
                'sender_police_station_id' => $sender->id,
                'receiver_police_station_id' => $receiver->id,
                'notes' => 'Pinjam pakai material mendesak',
                'sent_at' => Carbon::now()->subDays(1),
                'received_at' => Carbon::now(),
                'received_by' => $adminUser?->id,
                'is_active' => true,
            ]);

            $senderStocks = StockDetail::where('police_station_id', $sender->id)->get();
            foreach ($senderStocks->take(2) as $sd) {
                MutationStockDetail::create([
                    'mutation_stock_id' => $mut->id,
                    'stock_detail_id' => $sd->id,
                    'type_id' => $sd->type_id,
                    'type_detail_id' => $sd->type_detail_id,
                    'code' => $sd->code ?? '',
                    'number_serial_first' => $sd->number_serial_first ?? '',
                    'number_serial_second' => $sd->number_serial_second ?? '',
                    'quantity' => min(10, $sd->quantity),
                    'notes' => 'Mutasi pinjam',
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('ConnectedDataSeeder completed successfully!');
    }
}
