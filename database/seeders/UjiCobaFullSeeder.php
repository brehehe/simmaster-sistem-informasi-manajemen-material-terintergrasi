<?php

namespace Database\Seeders;

use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail;
use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail;
use App\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy;
use App\Models\MenuPolda\MaterialSubsidy\MaterialSubsidyDetail;
use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use App\Models\MenuPolda\MutationStock\MutationStock;
use App\Models\MenuPolda\MutationStock\MutationStockDetail;
use App\Models\MenuPolda\RackAssignment\RackAssignment;
use App\Models\MenuPolda\RackAssignment\RackAssignmentDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Rack\Rack;
use App\Models\Reception\Reception;
use App\Models\Reception\ReceptionDetail;
use App\Models\Reception\ReceptionDetailItem;
use App\Models\Spatie\Role;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\HistoryStockDetail;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use App\Models\StockOpname\StockOpname;
use App\Models\StockOpname\StockOpnameDetail;
use App\Models\Target\Target;
use App\Models\Target\TargetDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\User;
use App\Models\User\UserType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UjiCobaFullSeeder extends Seeder
{
    /**
     * Master Seeder untuk persiapan sesi Uji Coba:
     * 1. Stock Polda: Hanya materiil utama (SIM CARD, STNK, STCK, E-BPKB, BPKB = 7.951.000)
     *    sesuai Berita Acara Stock Opname 4 September 2026.
     * 2. Target 2026: Di-load lengkap dari TARGET PNBP DAN MATREG PER POLRES 2026 (1).xlsx.
     * 3. Stock Polres: Dikosongkan (0 unit) untuk diisi oleh Polres hari ini.
     * 4. Data Dashboard Material Rusak & Transaksi Dummy: Dibersihkan total (clear).
     * 5. Akun Pengguna:
     *    - Polres: Hanya akun BAMAT (dan akun utama polres), tanpa akun BAUR.
     *    - Polda: Akun BAMAT Polda, SAMSAT Polda, dan SIE (Sie Fasmat, STNK, BPKB, SIM, TNKB).
     */
    public function run(): void
    {
        $this->command->info('===========================================================');
        $this->command->info(' MEMULAI SEEDER FULL PERSIAPAN UJI COBA (SIMMASTER SBST)   ');
        $this->command->info('===========================================================');

        DB::beginTransaction();

        try {
            // =========================================================================
            // 1. BERSIHKAN SEMUA DATA TRANSAKSI DUMMY & KERUSAKAN KEMARIN (CLEAR TOTAL)
            // =========================================================================
            $this->command->info('[1/6] Membersihkan data dummy material rusak, transaksi & riwayat...');

            DB::table('material_damage_details')->delete();
            DB::table('material_damages')->delete();
            DB::table('material_usage_details')->delete();
            DB::table('material_usages')->delete();
            DB::table('material_shipment_details')->delete();
            DB::table('material_shipments')->delete();
            DB::table('rack_assignment_details')->delete();
            DB::table('rack_assignments')->delete();
            DB::table('mutation_stock_details')->delete();
            DB::table('mutation_stocks')->delete();
            DB::table('material_subsidy_details')->delete();
            DB::table('material_subsidies')->delete();
            DB::table('reception_detail_items')->delete();
            DB::table('reception_details')->delete();
            DB::table('receptions')->delete();
            DB::table('history_stock_details')->delete();
            DB::table('history_stocks')->delete();
            DB::table('stock_opname_details')->delete();
            DB::table('stock_opnames')->delete();

            // Kosongkan seluruh stock lama (baik Polda maupun Polres) untuk dibuild ulang bersih
            DB::table('stock_details')->delete();
            DB::table('stocks')->delete();

            $this->command->info('-> Transaksi dummy & kerusakan berhasil dibersihkan.');

            // =========================================================================
            // 2. PASTIKAN ROLES & TIPE MATERIEL AKTIF BESERTA TARIF PNBP RESMI
            // =========================================================================
            $this->command->info('[2/6] Memperbarui tipe materiel dan tarif PNBP...');

            Role::firstOrCreate(['name' => 'Admin']);
            Role::firstOrCreate(['name' => 'Polda']);
            Role::firstOrCreate(['name' => 'Polres']);
            Role::firstOrCreate(['name' => 'Warehouse']);

            // Tarif PNBP unit rata-rata sesuai target Excel 2026 & PP PNBP No. 76/2020:
            $materialConfigs = [
                'SIM CARD' => ['is_serial' => true, 'price' => 86732.29],
                'STNK' => ['is_serial' => true, 'price' => 121218.32],
                'BPKB' => ['is_serial' => true, 'price' => 253238.13],
                'E-BPKB' => ['is_serial' => true, 'price' => 253238.13],
                'STCK' => ['is_serial' => true, 'price' => 29241.17],
                'MUTASI' => ['is_serial' => false, 'price' => 201348.80],
                'TNKB REG' => ['is_serial' => false, 'price' => 68597.72],
                'TNKB R2 PUTIH' => ['is_serial' => false, 'price' => 60000.00],
                'TNKB R4 PUTIH' => ['is_serial' => false, 'price' => 100000.00],
                'TCKB R2' => ['is_serial' => false, 'price' => 60000.00],
                'TCKB R4' => ['is_serial' => false, 'price' => 100000.00],
                'TNKB LISTRIK' => ['is_serial' => false, 'price' => 68597.72],
                'NRKB NOPIL' => ['is_serial' => false, 'price' => 8291666.67],
                'NRKB NOPIL LISTRIK' => ['is_serial' => false, 'price' => 8291666.67],
            ];

            foreach ($materialConfigs as $typeName => $cfg) {
                $type = Type::withTrashed()->where('name', $typeName)->first();
                if ($type) {
                    if ($type->trashed()) {
                        $type->restore();
                    }
                    $type->update([
                        'is_with_serial_number' => $cfg['is_serial'],
                        'price' => $cfg['price'],
                        'is_active' => true,
                    ]);
                } else {
                    Type::create([
                        'id' => Str::uuid()->toString(),
                        'name' => $typeName,
                        'is_with_serial_number' => $cfg['is_serial'],
                        'price' => $cfg['price'],
                        'is_active' => true,
                    ]);
                }
            }

            // =========================================================================
            // 3. SEED TARGET 2026 (RENBUT MATERIEL & TARGET PNBP)
            // =========================================================================
            $this->command->info('[3/6] Memasukkan Target 2026 dari Excel...');
            $this->seedTarget2026();

            // =========================================================================
            // 4. SEED STOK POLDA (STOCK OPNAME FISIK 4 SEPTEMBER 2026)
            // =========================================================================
            $this->command->info('[4/6] Memasukkan Stok Fisik Polda Jatim per 4 September 2026...');
            $this->seedStockPolda();

            // =========================================================================
            // 5. PASTIKAN STOK POLRES KOSONG (0 UNIT)
            // =========================================================================
            $this->command->info('[5/6] Mengosongkan stok seluruh Polres untuk uji coba hari ini...');
            StockDetail::whereNotNull('police_station_id')->delete();
            Stock::whereNotNull('police_station_id')->delete();
            $this->command->info('-> Stok Polres dipastikan KOSONG (0 unit).');

            // =========================================================================
            // 6. PERBARUI STRUKTUR AKUN PENGGUNA (POLRES: BAMAT AJA + POLDA: SAMSAT & SIE)
            // =========================================================================
            $this->command->info('[6/6] Memperbarui akun login (BAMAT Polres & Polda Samsat/Sie)...');
            $this->seedUserAccounts();

            DB::commit();

            $this->command->info('===========================================================');
            $this->command->info(' SEEDER FULL PERSIAPAN UJI COBA BERHASIL DISELESAIKAN!     ');
            $this->command->info('===========================================================');

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('GAGAL MENJALANKAN UjiCobaFullSeeder: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Memasukkan Target Renbut & PNBP 2026 dari sheet Excel resmi: MATERIEL TAHUN 2026.
     * Total Renbut Matreg: 19.337.001 unit.
     * Total Target PNBP: Rp 2.210.588.370.000.
     */
    protected function seedTarget2026(): void
    {
        $year = 2026;
        $target = Target::updateOrCreate(
            ['year' => $year],
            [
                'name' => "Target Ditlantas {$year}",
                'year' => $year,
                'description' => "Target Renbut Materiel dan PNBP Ditlantas & Polres Jajaran TA {$year}",
                'is_active' => true,
            ]
        );

        TargetDetail::where('target_id', $target->id)->delete();

        $excelPath = public_path('TARGET PNBP DAN MATREG PER POLRES 2026 (1).xlsx');
        if (!file_exists($excelPath)) {
            $excelPath = public_path('excel/TARGET PNBP DAN MATREG PER POLRES 2026 (1).xlsx');
        }

        if (!file_exists($excelPath)) {
            $this->command->warn('File Excel target tidak ditemukan.');
            return;
        }

        $reader = IOFactory::createReaderForFile($excelPath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($excelPath);

        $sheetMat = $spreadsheet->getSheetByName('MATERIEL TAHUN 2026');
        if (!$sheetMat) {
            $this->command->error('Sheet MATERIEL TAHUN 2026 tidak ditemukan di file Excel!');
            return;
        }

        $polda = RegionalPolice::where('name', 'like', '%Polda%')->first() ?? RegionalPolice::first();
        $stations = PoliceStation::all();

        // Siapkan Type models
        $typeSim = Type::firstOrCreate(['name' => 'SIM CARD'], ['is_with_serial_number' => true, 'price' => 86732.29, 'is_active' => true]);
        $typeStnk = Type::firstOrCreate(['name' => 'STNK'], ['is_with_serial_number' => true, 'price' => 121218.32, 'is_active' => true]);
        $typeBpkb = Type::firstOrCreate(['name' => 'BPKB'], ['is_with_serial_number' => true, 'price' => 253238.13, 'is_active' => true]);
        $typeStck = Type::firstOrCreate(['name' => 'STCK'], ['is_with_serial_number' => true, 'price' => 29241.17, 'is_active' => true]);
        $typeMutasi = Type::firstOrCreate(['name' => 'MUTASI'], ['is_with_serial_number' => false, 'price' => 201348.80, 'is_active' => true]);
        $typeTnkbR2 = Type::firstOrCreate(['name' => 'TNKB R2 PUTIH'], ['is_with_serial_number' => false, 'price' => 60000.00, 'is_active' => true]);
        $typeTnkbR4 = Type::firstOrCreate(['name' => 'TNKB R4 PUTIH'], ['is_with_serial_number' => false, 'price' => 100000.00, 'is_active' => true]);
        $typeTckbR2 = Type::firstOrCreate(['name' => 'TCKB R2'], ['is_with_serial_number' => false, 'price' => 60000.00, 'is_active' => true]);
        $typeTckbR4 = Type::firstOrCreate(['name' => 'TCKB R4'], ['is_with_serial_number' => false, 'price' => 100000.00, 'is_active' => true]);
        $typeNrkb = Type::firstOrCreate(['name' => 'NRKB NOPIL'], ['is_with_serial_number' => false, 'price' => 8291666.67, 'is_active' => true]);
        $typeSkukp = Type::firstOrCreate(['name' => 'SKUKP'], ['is_with_serial_number' => false, 'price' => 50000.00, 'is_active' => true]);

        $stationRows = [
            14 => 'Polrestabes Surabaya',
            15 => 'Polresta Sidoarjo',
            16 => 'Polres Gresik',
            17 => 'Polres Mojokerto Kota',
            18 => 'Polres Mojokerto',
            19 => 'Polresta Malang Kota',
            20 => 'Polres Malang',
            21 => 'Polres Batu',
            22 => 'Polres Probolinggo Kota',
            23 => 'Polres Probolinggo',
            24 => 'Polres Pasuruan Kota',
            25 => 'Polres Pasuruan',
            26 => 'Polres Lumajang',
            27 => 'Polres Bondowoso',
            28 => 'Polres Situbondo',
            29 => 'Polres Jember',
            30 => 'Polresta Banyuwangi',
            31 => 'Polres Jombang',
            32 => 'Polres Kediri Kota',
            33 => 'Polres Kediri',
            34 => 'Polres Blitar Kota',
            35 => 'Polres Blitar',
            36 => 'Polres Tulungagung',
            37 => 'Polres Nganjuk',
            38 => 'Polres Trenggalek',
            39 => 'Polres Madiun Kota',
            40 => 'Polres Madiun',
            41 => 'Polres Ngawi',
            42 => 'Polres Magetan',
            43 => 'Polres Ponorogo',
            44 => 'Polres Pacitan',
            45 => 'Polres Bojonegoro',
            46 => 'Polres Tuban',
            47 => 'Polres Lamongan',
            48 => 'Polres Pamekasan',
            49 => 'Polres Bangkalan',
            50 => 'Polres Sampang',
            51 => 'Polres Sumenep',
        ];

        $count = 0;

        for ($r = 13; $r <= 51; $r++) {
            if ($r === 13) {
                $regionalId = $polda?->id;
                $stationId = null;
                $label = $polda?->name ?? 'Polda Jatim';
            } else {
                $stationName = $stationRows[$r] ?? null;
                $station = $stations->firstWhere('name', $stationName);
                if (!$station) continue;

                $regionalId = $station->regional_police_id;
                $stationId = $station->id;
                $label = $station->name;
            }

            $simBaru = (float)str_replace(',', '', (string)$sheetMat->getCell("C$r")->getFormattedValue());
            $simPerp = (float)str_replace(',', '', (string)$sheetMat->getCell("D$r")->getFormattedValue());
            $stnkQty = (float)str_replace(',', '', (string)$sheetMat->getCell("E$r")->getFormattedValue());
            $bpkbQty = (float)str_replace(',', '', (string)$sheetMat->getCell("F$r")->getFormattedValue());
            $stckQty = (float)str_replace(',', '', (string)$sheetMat->getCell("G$r")->getFormattedValue());
            $mutasiQty = (float)str_replace(',', '', (string)$sheetMat->getCell("H$r")->getFormattedValue());
            $skukpQty = (float)str_replace(',', '', (string)$sheetMat->getCell("I$r")->getFormattedValue());
            $tnkbQty = (float)str_replace(',', '', (string)$sheetMat->getCell("J$r")->getFormattedValue());
            $tckbQty = (float)str_replace(',', '', (string)$sheetMat->getCell("K$r")->getFormattedValue());
            $nrkbQty = (float)str_replace(',', '', (string)$sheetMat->getCell("L$r")->getFormattedValue());

            $tnkbR2Qty = round($tnkbQty * (4710342 / 6000000));
            $tnkbR4Qty = $tnkbQty - $tnkbR2Qty;

            $tckbR2Qty = round($tckbQty * (61880 / 75000));
            $tckbR4Qty = $tckbQty - $tckbR2Qty;

            $locationTargets = [
                ['type' => $typeSim, 'qty' => ($simBaru + $simPerp)],
                ['type' => $typeStnk, 'qty' => $stnkQty],
                ['type' => $typeBpkb, 'qty' => $bpkbQty],
                ['type' => $typeStck, 'qty' => $stckQty],
                ['type' => $typeMutasi, 'qty' => $mutasiQty],
                ['type' => $typeTnkbR2, 'qty' => $tnkbR2Qty],
                ['type' => $typeTnkbR4, 'qty' => $tnkbR4Qty],
                ['type' => $typeTckbR2, 'qty' => $tckbR2Qty],
                ['type' => $typeTckbR4, 'qty' => $tckbR4Qty],
                ['type' => $typeNrkb, 'qty' => $nrkbQty],
                ['type' => $typeSkukp, 'qty' => $skukpQty],
            ];

            foreach ($locationTargets as $lt) {
                TargetDetail::create([
                    'id' => Str::uuid()->toString(),
                    'name' => $label,
                    'target_id' => $target->id,
                    'regional_police_id' => $regionalId,
                    'police_station_id' => $stationId,
                    'type_id' => $lt['type']->id,
                    'type_detail_id' => null,
                    'quantity' => $lt['qty'],
                    'description' => $target->description,
                    'is_active' => true,
                ]);
                $count++;
            }
        }

        // Pastikan Polres lain di DB yang belum ada di target (e.g. Tanjung Perak) memiliki record 0
        $existingStationIds = TargetDetail::where('target_id', $target->id)->whereNotNull('police_station_id')->pluck('police_station_id')->unique()->toArray();
        $otherStations = $stations->whereNotIn('id', $existingStationIds);
        $allTypes = Type::all();

        foreach ($otherStations as $st) {
            foreach ($allTypes as $tp) {
                TargetDetail::create([
                    'id' => Str::uuid()->toString(),
                    'name' => $st->name,
                    'target_id' => $target->id,
                    'regional_police_id' => $st->regional_police_id,
                    'police_station_id' => $st->id,
                    'type_id' => $tp->id,
                    'type_detail_id' => null,
                    'quantity' => 0,
                    'description' => $target->description,
                    'is_active' => true,
                ]);
                $count++;
            }
        }

        $this->command->info("-> Berhasil memasukkan {$count} baris TargetDetail (Total Renbut: 19.337.001 unit, PNBP: Rp 2,21 Triliun).");
    }

    /**
     * Memasukkan Stok Fisik Polda Jatim per 4 September 2026.
     * Materiel Utama: Total 7.951.000 unit (SIM Card, STNK, STCK, E-BPKB, BPKB)
     * Materiel Pendukung: Formulir, Laminasi, Ribbon, Blangko Cek Fisik, dll.
     */
    protected function seedStockPolda(): void
    {
        $polda = RegionalPolice::where('name', 'like', '%Polda%')->first() ?? RegionalPolice::first();
        if (!$polda) {
            $this->command->error('Regional Police (Polda) tidak ditemukan!');
            return;
        }

        $simType = Type::where('name', 'SIM CARD')->first() ?? Type::where('name', 'SIM')->first();
        $stnkType = Type::where('name', 'STNK')->first();
        $stckType = Type::where('name', 'STCK')->first();
        $ebpkbType = Type::where('name', 'E-BPKB')->first();
        $bpkbType = Type::where('name', 'BPKB')->first();
        $mutasiType = Type::where('name', 'MUTASI')->first();
        $tnkbRegType = Type::firstOrCreate(['name' => 'TNKB REG'], ['is_with_serial_number' => false, 'price' => 60000.00, 'is_active' => true]);
        $tnkbListrikType = Type::firstOrCreate(['name' => 'TNKB LISTRIK'], ['is_with_serial_number' => false, 'price' => 60000.00, 'is_active' => true]);
        $nrkbNopilType = Type::firstOrCreate(['name' => 'NRKB NOPIL'], ['is_with_serial_number' => false, 'price' => 5000000.00, 'is_active' => true]);
        $nrkbListrikType = Type::firstOrCreate(['name' => 'NRKB NOPIL LISTRIK'], ['is_with_serial_number' => false, 'price' => 5000000.00, 'is_active' => true]);

        // Racks di Gudang Polda
        $poldaRacks = Rack::where('regional_police_id', $polda->id)->get();
        $getRack = function (string $name) use ($poldaRacks, $polda) {
            $found = $poldaRacks->first(fn($r) => stripos($r->name, $name) !== false);
            if (!$found) {
                $found = Rack::create([
                    'id' => Str::uuid()->toString(),
                    'name' => "Rak {$name}",
                    'regional_police_id' => $polda->id,
                    'description' => "Rak Penyimpanan Gudang {$name}",
                    'is_active' => true,
                ]);
            }
            return $found;
        };

        $rackSim = $getRack('SIM CARD');
        $rackStnk = $getRack('STNK');
        $rackStck = $getRack('STCK');
        $rackEbpkb = $getRack('E-BPKB');
        $rackBpkb = $poldaRacks->first(fn($r) => stripos($r->name, 'E-BPKB') === false && stripos($r->name, 'BPKB') !== false) ?? $getRack('BPKB');
        $rackMutasi = $getRack('MUTASI');
        $rackTnkbReg = $getRack('TNKB REG');
        $rackTnkbListrik = $getRack('TNKB LISTRIK');
        $rackNrkbNopil = $getRack('NRKB NOPIL');
        $rackNrkbListrik = $getRack('NRKB LISTRIK');

        // Materiel Utama SBST (Dokumen 1: Total 7.951.000 Unit PNBP Aktif)
        $materielUtama = [
            // 1. SIM CARD (Total: 3.025.000)
            [
                'type' => $simType,
                'rack' => $rackSim,
                'items' => [
                    ['code' => 'O.', 'first' => '12.402.001', 'second' => '12.517.000', 'qty' => 115000],
                    ['code' => 'P.', 'first' => '01.140.001', 'second' => '01.640.000', 'qty' => 500000],
                    ['code' => 'P.', 'first' => '06.958.501', 'second' => '07.915.000', 'qty' => 956500],
                    ['code' => 'P.', 'first' => '14.600.001', 'second' => '15.400.000', 'qty' => 800000],
                    ['code' => 'P.', 'first' => '15.421.501', 'second' => '16.075.000', 'qty' => 653500],
                ],
            ],
            // 2. STNK (Total: 1.904.000)
            [
                'type' => $stnkType,
                'rack' => $rackStnk,
                'items' => [
                    ['code' => 'J.', 'first' => '13.870.001', 'second' => '13.934.000', 'qty' => 64000],
                    ['code' => 'J.', 'first' => '15.934.001', 'second' => '16.434.000', 'qty' => 500000],
                    ['code' => 'J.', 'first' => '17.614.001', 'second' => '17.914.000', 'qty' => 300000],
                    ['code' => 'J.', 'first' => '18.414.001', 'second' => '18.614.000', 'qty' => 200000],
                    ['code' => 'J.', 'first' => '19.514.001', 'second' => '19.814.000', 'qty' => 300000],
                    ['code' => 'J.', 'first' => '21.398.001', 'second' => '21.938.000', 'qty' => 540000],
                ],
            ],
            // 3. STCK (Total: 2.100.000)
            [
                'type' => $stckType,
                'rack' => $rackStck,
                'items' => [
                    ['code' => '', 'first' => '06.790.001', 'second' => '07.360.000', 'qty' => 600000],
                    ['code' => '', 'first' => '02.573.001', 'second' => '03.023.000', 'qty' => 450000],
                    ['code' => '', 'first' => '07.710.001', 'second' => '07.760.000', 'qty' => 50000],
                    ['code' => '', 'first' => '06.960.000', 'second' => '07.360.000', 'qty' => 400000],
                    ['code' => '', 'first' => '02.749.001', 'second' => '03.349.000', 'qty' => 600000],
                ],
            ],
            // 4. E-BPKB (Total: 436.000)
            [
                'type' => $ebpkbType,
                'rack' => $rackEbpkb,
                'items' => [
                    ['code' => 'AA.', 'first' => '01.268.001', 'second' => '01.416.000', 'qty' => 148000],
                    ['code' => 'AB.', 'first' => '00.415.376', 'second' => '00.511.375', 'qty' => 96000],
                    ['code' => 'AC.', 'first' => '00.973.751', 'second' => '01.165.750', 'qty' => 192000],
                ],
            ],
            // 5. BPKB (Total: 486.000)
            [
                'type' => $bpkbType,
                'rack' => $rackBpkb,
                'items' => [
                    ['code' => 'X.', 'first' => '00.922.001', 'second' => '00.973.000', 'qty' => 51000],
                    ['code' => 'X.', 'first' => '03.270.001', 'second' => '03.390.000', 'qty' => 120000],
                    ['code' => 'X.', 'first' => '03.585.001', 'second' => '03.780.000', 'qty' => 195000],
                    ['code' => 'X.', 'first' => '04.170.001', 'second' => '04.290.000', 'qty' => 120000],
                ],
            ],
            // 6. MUTASI (Total: 0)
            [
                'type' => $mutasiType,
                'rack' => $rackMutasi,
                'items' => [
                    ['code' => null, 'first' => null, 'second' => null, 'qty' => 0],
                ],
            ],
            // 7. TNKB REG (Total: 0)
            [
                'type' => $tnkbRegType,
                'rack' => $rackTnkbReg,
                'items' => [
                    ['code' => null, 'first' => null, 'second' => null, 'qty' => 0],
                ],
            ],
            // 8. TNKB LISTRIK (Total: 0)
            [
                'type' => $tnkbListrikType,
                'rack' => $rackTnkbListrik,
                'items' => [
                    ['code' => null, 'first' => null, 'second' => null, 'qty' => 0],
                ],
            ],
            // 9. NRKB NOPIL (Total: 0)
            [
                'type' => $nrkbNopilType,
                'rack' => $rackNrkbNopil,
                'items' => [
                    ['code' => null, 'first' => null, 'second' => null, 'qty' => 0],
                ],
            ],
            // 10. NRKB NOPIL LISTRIK (Total: 0)
            [
                'type' => $nrkbListrikType,
                'rack' => $rackNrkbListrik,
                'items' => [
                    ['code' => null, 'first' => null, 'second' => null, 'qty' => 0],
                ],
            ],
        ];

        foreach ($materielUtama as $group) {
            $type = $group['type'];
            if (!$type) continue;
            $rack = $group['rack'];
            $groupTotalQty = collect($group['items'])->sum('qty');

            // Header Master Stock untuk Materiel Utama (type_detail_id = null)
            $stock = Stock::create([
                'id' => Str::uuid()->toString(),
                'type_id' => $type->id,
                'type_detail_id' => null,
                'regional_police_id' => $polda->id,
                'police_station_id' => null,
                'quantity' => $groupTotalQty,
                'description' => "Stock Materiel Utama {$type->name} per 4 September 2026",
                'is_active' => true,
            ]);

            foreach ($group['items'] as $item) {
                StockDetail::create([
                    'id' => Str::uuid()->toString(),
                    'stock_id' => $stock->id,
                    'type_id' => $type->id,
                    'type_detail_id' => null,
                    'regional_police_id' => $polda->id,
                    'police_station_id' => null,
                    'rack_id' => $rack?->id,
                    'code' => $item['code'],
                    'number_serial_first' => $item['first'],
                    'number_serial_second' => $item['second'],
                    'quantity' => $item['qty'],
                    'description' => $item['first'] ? "Stock seri {$item['code']} {$item['first']} - {$item['second']}" : "Stock {$type->name} fisik per 4 September 2026",
                    'is_active' => true,
                ]);
            }
        }

        // Materiel Pendukung SBST (Dokumen 2: Lengkap seluruh item termasuk yang berstatus NIHIL)
        $materielPendukung = [
            // 1. PENDUKUNG SIM
            [
                'parent_type' => $simType,
                'rack' => $rackSim,
                'items' => [
                    ['name' => 'YMCKT', 'qty' => 3025000],
                    ['name' => 'PRINT LAMINASI', 'qty' => 3025000],
                    ['name' => 'FORMULIR', 'qty' => 3025000],
                    ['name' => 'KWITANSI SIM', 'qty' => 0],
                    ['name' => 'AVIS LEMBAR JAWABAN TEORI', 'qty' => 0],
                    ['name' => 'LEMBAR UJIAN PRAKTEK SIM A/B 1/D', 'qty' => 0],
                    ['name' => 'LEMBAR UJIAN PRAKTEK SIM B I/II', 'qty' => 0],
                    ['name' => 'LEMBAR UJIAN PRAKTEK SIM C/D.I', 'qty' => 0],
                    ['name' => 'LEMBAR UJIAN PRAKTEK SIM A UMUM', 'qty' => 0],
                    ['name' => 'LEMBAR UJIAN PRAKTEK SIM B I UMUM', 'qty' => 0],
                    ['name' => 'LEMBAR UJIAN PRAKTEK SIM B II UMUM', 'qty' => 0],
                    ['name' => 'LEAFLET PETUNJUK LALU LINTAS', 'qty' => 0],
                    ['name' => 'MAP ARSIP SIM', 'qty' => 0],
                    ['name' => 'BUKU REGISTER SIM', 'qty' => 0],
                ],
            ],
            // 2. PENDUKUNG STNK
            [
                'parent_type' => $stnkType,
                'rack' => $rackStnk,
                'items' => [
                    ['name' => 'SPRKB STNK', 'qty' => 1904000],
                    ['name' => 'BLANGKO CEK FISIK', 'qty' => 700000],
                    ['name' => 'MAP ARSIP STNK', 'qty' => 0],
                    ['name' => 'DOMPET PLASTIK STNK', 'qty' => 0],
                    ['name' => 'BUKU REG INDUK RANMOR', 'qty' => 0],
                    ['name' => 'BUKU PERPANJANG STNK', 'qty' => 0],
                    ['name' => 'BUKU TERBIT STNK HILANG/RUSAK', 'qty' => 0],
                    ['name' => 'BUKU REGISTRASI SERAH STNK', 'qty' => 0],
                    ['name' => 'TINTA COLOR CAIR 001', 'qty' => 0],
                    ['name' => 'PITA EPSON LX 310', 'qty' => 0],
                    ['name' => 'PRINTER', 'qty' => 0],
                    ['name' => 'RIBBON CATRIDGE LQ2190', 'qty' => 0],
                    ['name' => 'RIBBON CATRIDGE LX 310', 'qty' => 0],
                    ['name' => 'TINTA PRINTER L 4150/001', 'qty' => 0],
                ],
            ],
            // 3. PENDUKUNG BPKB & E-BPKB
            [
                'parent_type' => $bpkbType,
                'rack' => $rackBpkb,
                'items' => [
                    ['name' => 'SPRKB BPKB', 'qty' => 0],
                    ['name' => 'SPRKB E-BPKB', 'qty' => 192000],
                    ['name' => 'BLANGKO CEK FISIK BPKB', 'qty' => 0],
                    ['name' => 'BLANGKO CEK FISIK E-BPKB', 'qty' => 0],
                    ['name' => 'KWITANSI BPKB', 'qty' => 0],
                    ['name' => 'KI BIRU (R2)', 'qty' => 0],
                    ['name' => 'KI KUNING (MOBIL BARANG)', 'qty' => 0],
                    ['name' => 'KI KUNING E-BPKB (MOBIL BARANG)', 'qty' => 0],
                    ['name' => 'KI MERAH (BUS)', 'qty' => 0],
                    ['name' => 'KI PUTIH (MOBIL KHUSUS)', 'qty' => 0],
                    ['name' => 'KI HIJAU (MOBIL PENUMPANG)', 'qty' => 0],
                    ['name' => 'BUKU REG BPKB BARU', 'qty' => 0],
                    ['name' => 'BUKU REG RUBAH MUTASI KELUAR', 'qty' => 0],
                    ['name' => 'RIBBON CATRIDGE EPSON PLQ 20/30', 'qty' => 0],
                    ['name' => 'RIBBON THERMAL CUSTOM', 'qty' => 0],
                    ['name' => 'RIBBON EPSON INKJET PRINTER L-1300', 'qty' => 0],
                    ['name' => 'RIBBON TALLY T5040', 'qty' => 0],
                    ['name' => 'ROLL STICKER/KERTAS LABEL BARCODE GK 420 T', 'qty' => 0],
                    ['name' => 'RIBBON PREMIUM WAX', 'qty' => 0],
                    ['name' => 'RIBBON TALLY T2348', 'qty' => 0],
                    ['name' => 'ZEBRA RIBBON', 'qty' => 0],
                    ['name' => 'PRINTER SCANER', 'qty' => 0],
                    ['name' => 'RIBBON PRINTER E-BPKB', 'qty' => 7],
                    ['name' => 'READER RFID E-BPKB', 'qty' => 2],
                ],
            ],
            // 4. PENDUKUNG STCK
            [
                'parent_type' => $stckType,
                'rack' => $rackStck,
                'items' => [
                    ['name' => 'FORMULIR STCK', 'qty' => 2100000],
                    ['name' => 'MAP STCK', 'qty' => 0],
                    ['name' => 'BLANGKO CEK FISIK STCK', 'qty' => 0],
                    ['name' => 'KWITANSI STCK', 'qty' => 0],
                    ['name' => 'SAMPUL PLASTIK STCK', 'qty' => 0],
                ],
            ],
            // 5. PENDUKUNG MUTASI
            [
                'parent_type' => $mutasiType,
                'rack' => $rackMutasi,
                'items' => [
                    ['name' => 'SURAT PENGANTAR BERKAS MUTASI', 'qty' => 200000],
                    ['name' => 'SURAT KETERANGAN PENGGANTI STNK', 'qty' => 200000],
                    ['name' => 'DAFTAR KELENGKAPAN DOKUMEN MUTASI', 'qty' => 200000],
                    ['name' => 'TANDA BUKTI DAFTAR & AMBIL MUTASI', 'qty' => 200000],
                    ['name' => 'BUKU PENERIMAAN MUTASI LUAR DAERAH', 'qty' => 0],
                    ['name' => 'BUKU PENGELUARAN MUTASI LUAR DAERAH', 'qty' => 0],
                    ['name' => 'AMPLOP PENYIMPANAN BERKAS MUTASI', 'qty' => 0],
                    ['name' => 'TANDA BUKTI PEMBAYARAN MUTASI', 'qty' => 200000],
                    ['name' => 'BLANGKO STIKER CEK FISIK', 'qty' => 0],
                    ['name' => 'FORM BA HASIL CEK FISIK', 'qty' => 0],
                    ['name' => 'RIBBON CATRIDGE', 'qty' => 0],
                ],
            ],
            // 6. HOT STAMPING FOIL NOPIL
            [
                'parent_type' => $nrkbNopilType,
                'rack' => $rackNrkbNopil,
                'items' => [
                    ['name' => 'HOT STAMPING FOIL NOPIL', 'qty' => 0],
                ],
            ],
        ];

        foreach ($materielPendukung as $group) {
            $type = $group['parent_type'];
            if (!$type) continue;
            $rack = $group['rack'];

            foreach ($group['items'] as $item) {
                $typeDetail = TypeDetail::where('type_id', $type->id)
                    ->where('name', $item['name'])
                    ->first()
                    ?? TypeDetail::where('name', $item['name'])->first();

                if (!$typeDetail) {
                    $typeDetail = TypeDetail::create([
                        'id' => Str::uuid()->toString(),
                        'type_id' => $type->id,
                        'name' => $item['name'],
                        'is_active' => true,
                    ]);
                }

                $stock = Stock::create([
                    'id' => Str::uuid()->toString(),
                    'type_id' => $type->id,
                    'type_detail_id' => $typeDetail->id,
                    'regional_police_id' => $polda->id,
                    'police_station_id' => null,
                    'quantity' => $item['qty'],
                    'description' => "Stock Materiel Pendukung {$item['name']} per 4 September 2026",
                    'is_active' => true,
                ]);

                StockDetail::create([
                    'id' => Str::uuid()->toString(),
                    'stock_id' => $stock->id,
                    'type_id' => $type->id,
                    'type_detail_id' => $typeDetail->id,
                    'regional_police_id' => $polda->id,
                    'police_station_id' => null,
                    'rack_id' => $rack?->id,
                    'code' => null,
                    'number_serial_first' => null,
                    'number_serial_second' => null,
                    'quantity' => $item['qty'],
                    'description' => "Stock pendukung {$item['name']} per 4 September 2026",
                    'is_active' => true,
                ]);
            }
        }

        // Petugas Resmi Penandatangan Berita Acara Stock Opname 4 September 2026
        $password = Hash::make('password');
        $penghitung = User::updateOrCreate(
            ['email' => 'dio-farizka@sbst.test'],
            [
                'name' => 'BRIGADIR DIO FARIZKA HABIBUN HAQ',
                'password' => $password,
                'regional_police_id' => $polda->id,
                'level_menu' => 1,
            ]
        );
        $penghitung->assignRole('Polda');

        $pamin = User::updateOrCreate(
            ['email' => 'kukuh-kurniawan@sbst.test'],
            [
                'name' => 'IPDA KUKUH KURNIAWAN, S.H.',
                'password' => $password,
                'regional_police_id' => $polda->id,
                'level_menu' => 1,
            ]
        );
        $pamin->assignRole('Polda');

        // Catat dokumen resmi Stock Opname
        $soCode = 'SO-POLDAJATIM-20260904-001';

        $stockOpname = StockOpname::create([
            'id' => Str::uuid()->toString(),
            'code' => $soCode,
            'opname_date' => '2026-09-04',
            'regional_police_id' => $polda->id,
            'police_station_id' => null,
            'status' => 'completed',
            'notes' => 'Stock Opname Materiel Utama & Pendukung SBST per 4 September 2026 (Penghitung: BRIGADIR DIO FARIZKA HABIBUN HAQ / NRP 95090221, Mengetahui: IPDA KUKUH KURNIAWAN, S.H. / NRP 84051624 - PAMIN SIE FASMAT)',
            'checked_by' => $penghitung->id,
            'approved_by' => $pamin->id,
            'approved_at' => Carbon::parse('2026-09-04 08:30:00'),
            'is_active' => true,
        ]);

        $poldaStockDetails = StockDetail::where('regional_police_id', $polda->id)->get();
        foreach ($poldaStockDetails as $sd) {
            StockOpnameDetail::create([
                'id' => Str::uuid()->toString(),
                'stock_opname_id' => $stockOpname->id,
                'stock_detail_id' => $sd->id,
                'type_id' => $sd->type_id,
                'type_detail_id' => $sd->type_detail_id,
                'rack_id' => $sd->rack_id,
                'code' => $sd->code ?? '',
                'number_serial_first' => $sd->number_serial_first,
                'number_serial_second' => $sd->number_serial_second,
                'system_quantity' => $sd->quantity,
                'physical_quantity' => $sd->quantity,
                'difference' => 0,
                'notes' => 'Sesuai fisik 4 September 2026',
                'is_active' => true,
            ]);
        }

        $this->command->info('-> Stok Polda berhasil dimasukkan lengkap (Materiel Utama: 7.951.000 unit + Seluruh Materiel Pendukung).');
    }

    /**
     * Memperbarui akun login:
     * - Polres: Hanya akun BAMAT (dan akun induk polres), menghapus akun BAUR.
     * - Polda: Akun BAMAT Polda, SAMSAT Polda, dan SIE (Fasmat, STNK, BPKB, SIM, TNKB).
     */
    protected function seedUserAccounts(): void
    {
        $password = Hash::make('password');
        $allTypeIds = Type::pluck('id')->toArray();
        $getTypeIds = fn(array $names) => Type::whereIn('name', $names)->pluck('id')->toArray();

        // 1. Definisikan User Types
        $userTypeDefinitions = [
            'BAMAT' => ['types' => $allTypeIds, 'level' => 1, 'desc' => 'Bintara Administrasi Materiel SBST'],
            'SAMSAT POLDA' => ['types' => $getTypeIds(['STNK', 'TNKB REG', 'TNKB R2 PUTIH', 'TNKB R4 PUTIH', 'BPKB', 'E-BPKB', 'STCK']), 'level' => 2, 'desc' => 'Pelayanan Samsat Ditlantas Polda Jatim'],
            'SIE FASMAT' => ['types' => $allTypeIds, 'level' => 2, 'desc' => 'Seksi Fasilitas Materiel SBST Ditlantas'],
            'SIE STNK' => ['types' => $getTypeIds(['STNK', 'STCK']), 'level' => 2, 'desc' => 'Seksi STNK Ditlantas Polda Jatim'],
            'SIE BPKB' => ['types' => $getTypeIds(['E-BPKB', 'BPKB', 'MUTASI']), 'level' => 2, 'desc' => 'Seksi BPKB Ditlantas Polda Jatim'],
            'SIE SIM' => ['types' => $getTypeIds(['SIM CARD']), 'level' => 2, 'desc' => 'Seksi SIM Ditlantas Polda Jatim'],
            'SIE TNKB' => ['types' => $getTypeIds(['TNKB REG', 'TNKB LISTRIK', 'NRKB NOPIL', 'NRKB NOPIL LISTRIK', 'TNKB R2 PUTIH', 'TNKB R4 PUTIH', 'TCKB R2', 'TCKB R4']), 'level' => 2, 'desc' => 'Seksi TNKB Ditlantas Polda Jatim'],
        ];

        $userTypeModels = [];
        foreach ($userTypeDefinitions as $utName => $utData) {
            $userTypeModels[$utName] = UserType::updateOrCreate(
                ['name' => $utName],
                [
                    'types' => $utData['types'],
                    'level_user' => $utData['level'],
                    'description' => $utData['desc'],
                    'is_active' => true,
                ]
            );
        }

        // Hapus akun-akun BAUR Polres yang tidak terpakai
        $deletedBaurCount = User::where('email', 'like', 'baur%')->delete();
        $this->command->info("-> Menghapus {$deletedBaurCount} akun BAUR yang tidak digunakan.");

        $polda = RegionalPolice::where('name', 'like', '%Polda%')->first() ?? RegionalPolice::first();

        // 2. Akun-akun di Tingkat Polda
        if ($polda) {
            // Admin SIMMASTER
            $admin = User::firstOrCreate(
                ['email' => 'admin@gmail.com'],
                [
                    'name' => 'Admin ARMASTER',
                    'password' => $password,
                    'level_menu' => 1,
                ]
            );
            $admin->assignRole('Admin');

            // Polda Jatim Induk
            $poldaUser = User::updateOrCreate(
                ['email' => 'polda-jatim@sbst.test'],
                [
                    'name' => 'Polda Jatim',
                    'password' => $password,
                    'regional_police_id' => $polda->id,
                    'police_station_id' => null,
                    'user_type_id' => null,
                    'level_menu' => 1,
                ]
            );
            $poldaUser->syncRoles(['Polda']);

            // BAMAT Polda Jatim
            $bamatPolda = User::updateOrCreate(
                ['email' => 'bamat-polda-jatim@sbst.test'],
                [
                    'name' => 'BAMAT Polda Jatim',
                    'password' => $password,
                    'regional_police_id' => $polda->id,
                    'police_station_id' => null,
                    'user_type_id' => $userTypeModels['BAMAT']->id,
                    'level_menu' => 2,
                ]
            );
            $bamatPolda->syncRoles(['Polda']);

            // SAMSAT Polda Jatim
            $samsatPolda = User::updateOrCreate(
                ['email' => 'samsat-polda-jatim@sbst.test'],
                [
                    'name' => 'SAMSAT Polda Jatim',
                    'password' => $password,
                    'regional_police_id' => $polda->id,
                    'police_station_id' => null,
                    'user_type_id' => $userTypeModels['SAMSAT POLDA']->id,
                    'level_menu' => 2,
                ]
            );
            $samsatPolda->syncRoles(['Polda']);

            // Seksi-seksi di Polda (Sie Fasmat, Sie STNK, Sie BPKB, Sie SIM, Sie TNKB)
            $sieList = [
                'SIE FASMAT' => 'sie-fasmat-polda-jatim@sbst.test',
                'SIE STNK' => 'sie-stnk-polda-jatim@sbst.test',
                'SIE BPKB' => 'sie-bpkb-polda-jatim@sbst.test',
                'SIE SIM' => 'sie-sim-polda-jatim@sbst.test',
                'SIE TNKB' => 'sie-tnkb-polda-jatim@sbst.test',
            ];

            foreach ($sieList as $sieName => $sieEmail) {
                $sieUser = User::updateOrCreate(
                    ['email' => $sieEmail],
                    [
                        'name' => "{$sieName} Polda Jatim",
                        'password' => $password,
                        'regional_police_id' => $polda->id,
                        'police_station_id' => null,
                        'user_type_id' => $userTypeModels[$sieName]->id,
                        'level_menu' => 2,
                    ]
                );
                $sieUser->syncRoles(['Polda']);
            }
        }

        // 3. Akun-akun di Tingkat Polres (BAMAT AJA + Akun Induk Polres)
        $stations = PoliceStation::all();
        $bamatPolresCount = 0;

        foreach ($stations as $station) {
            $stationSlug = Str::slug($station->name);

            // Akun BAMAT Polres
            $bamatEmail = "bamat-{$stationSlug}@sbst.test";
            $bamatUser = User::updateOrCreate(
                ['email' => $bamatEmail],
                [
                    'name' => "BAMAT {$station->name}",
                    'password' => $password,
                    'regional_police_id' => $station->regional_police_id,
                    'police_station_id' => $station->id,
                    'user_type_id' => $userTypeModels['BAMAT']->id,
                    'level_menu' => 2,
                ]
            );
            $bamatUser->syncRoles(['Polres']);

            // Akun Induk Polres
            $stationEmail = "{$stationSlug}@sbst.test";
            $stationUser = User::updateOrCreate(
                ['email' => $stationEmail],
                [
                    'name' => $station->name,
                    'password' => $password,
                    'regional_police_id' => $station->regional_police_id,
                    'police_station_id' => $station->id,
                    'user_type_id' => null,
                    'level_menu' => 1,
                ]
            );
            $stationUser->syncRoles(['Polres']);

            $bamatPolresCount++;
        }

        $this->command->info("-> Berhasil membuat/memperbarui akun BAMAT untuk {$bamatPolresCount} Polres.");
        $this->command->info('-> Akun Polda: BAMAT Polda, SAMSAT Polda Jatim, Sie Fasmat, Sie STNK, Sie BPKB, Sie SIM, Sie TNKB.');
    }
}
