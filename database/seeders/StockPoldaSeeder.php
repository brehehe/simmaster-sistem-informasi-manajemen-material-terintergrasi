<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Police\RegionalPolice;
use App\Models\Rack\Rack;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use App\Models\StockOpname\StockOpname;
use App\Models\StockOpname\StockOpnameDetail;
use App\Models\User;

class StockPoldaSeeder extends Seeder
{
    /**
     * Run the database seeds for Stock Opname Polda Jatim per 4 September 2026.
     * Based on official SBST Stock Opname documents:
     * 1. Stock Opname Materiel Utama SBST (4 September 2026)
     * 2. Stock Opname Materiel Pendukung SBST (4 September 2026)
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $this->command->info('Memulai seeding Stock Polda berdasarkan data 4 September 2026...');

            // 1. Dapatkan Regional Police (Polda Jatim)
            $polda = RegionalPolice::where('name', 'ilike', '%Polda%')->first() ?? RegionalPolice::first();
            if (!$polda) {
                $this->command->error('Regional Police (Polda) tidak ditemukan!');
                return;
            }
            $this->command->info("Polda target: {$polda->name} (ID: {$polda->id})");

            // 2. Pastikan Type Utama Aktif & Sesuai
            // Restore E-BPKB jika soft-deleted
            $ebpkbType = Type::withTrashed()->where('name', 'E-BPKB')->first();
            if ($ebpkbType) {
                if ($ebpkbType->trashed()) {
                    $ebpkbType->restore();
                    $this->command->info('Tipe E-BPKB berhasil di-restore.');
                }
                $ebpkbType->update(['is_with_serial_number' => true, 'is_active' => true]);
            } else {
                $ebpkbType = Type::create([
                    'id' => Str::uuid()->toString(),
                    'name' => 'E-BPKB',
                    'is_with_serial_number' => true,
                    'price' => 0,
                    'is_active' => true,
                ]);
            }

            // SIM CARD / SIM
            $simType = Type::where('name', 'SIM CARD')->first() 
                ?? Type::where('name', 'SIM')->first();
            if ($simType) {
                $simType->update(['name' => 'SIM CARD', 'is_with_serial_number' => true, 'is_active' => true]);
            } else {
                $simType = Type::create([
                    'id' => Str::uuid()->toString(),
                    'name' => 'SIM CARD',
                    'is_with_serial_number' => true,
                    'price' => 0,
                    'is_active' => true,
                ]);
            }

            // STNK
            $stnkType = Type::where('name', 'STNK')->first();
            if ($stnkType) {
                $stnkType->update(['is_with_serial_number' => true, 'is_active' => true]);
            }

            // STCK
            $stckType = Type::where('name', 'STCK')->first();
            if ($stckType) {
                $stckType->update(['is_with_serial_number' => true, 'is_active' => true]);
            }

            // BPKB
            $bpkbType = Type::where('name', 'BPKB')->first();
            if ($bpkbType) {
                $bpkbType->update(['is_with_serial_number' => true, 'is_active' => true]);
            }

            // MUTASI
            $mutasiType = Type::where('name', 'MUTASI')->first();

            // 3. Mapping Rak di Gudang Polda
            $poldaRacks = Rack::where('regional_police_id', $polda->id)->get();
            $getRack = function (string $namePattern, ?string $exactName = null) use ($poldaRacks, $polda) {
                if ($exactName) {
                    $found = $poldaRacks->firstWhere('name', $exactName);
                    if ($found) return $found;
                }

                $rack = $poldaRacks->first(function ($r) use ($namePattern) {
                    return stripos($r->name, $namePattern) !== false;
                });

                if (!$rack) {
                    $rack = Rack::firstOrCreate(
                        ['name' => $namePattern, 'regional_police_id' => $polda->id],
                        ['description' => 'Rak Gudang ' . $namePattern, 'is_active' => true]
                    );
                }
                return $rack;
            };

            $rackSim = $getRack('SIM CARD', 'Rak 1 SIM CARD');
            $rackStnk = $getRack('STNK', 'Rak 2 STNK');
            $rackStck = $getRack('STCK', 'Rak 3 STCK');
            $rackEbpkb = $getRack('E-BPKB', 'Rak 4 E-BPKB');
            $rackBpkb = $poldaRacks->firstWhere('name', 'Rak 5 BPKB') 
                ?? $poldaRacks->first(fn($r) => stripos($r->name, 'E-BPKB') === false && stripos($r->name, 'BPKB') !== false)
                ?? $getRack('BPKB');
            $rackMutasi = $getRack('MUTASI', 'Rak 6 MUTASI');

            // 4. Bersihkan stok Polda Jatim yang lama agar tidak terjadi duplikasi atau relasi usang
            $this->command->info('Membersihkan record Stock dan StockDetail lama milik Polda Jatim...');
            StockDetail::where('regional_police_id', $polda->id)->delete();
            Stock::where('regional_police_id', $polda->id)->delete();

            // 5. Data Materiel Utama SBST (Tanggal 4 September 2026)
            $materielUtama = [
                // 1. SIM CARD (Total Sisa: 3.025.000)
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
                // 2. STNK (Total Sisa: 1.904.000)
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
                // 3. STCK (Total Sisa: 2.100.000)
                [
                    'type' => $stckType,
                    'rack' => $rackStck,
                    'items' => [
                        ['code' => '', 'first' => '06.760.001', 'second' => '07.360.000', 'qty' => 600000],
                        ['code' => '', 'first' => '02.573.001', 'second' => '03.023.000', 'qty' => 450000],
                        ['code' => '', 'first' => '07.710.001', 'second' => '07.760.000', 'qty' => 50000],
                        ['code' => '', 'first' => '06.960.000', 'second' => '07.360.000', 'qty' => 400000],
                        ['code' => '', 'first' => '02.749.001', 'second' => '03.349.000', 'qty' => 600000],
                    ],
                ],
                // 4. E - BPKB (Total Sisa: 436.000)
                [
                    'type' => $ebpkbType,
                    'rack' => $rackEbpkb,
                    'items' => [
                        ['code' => 'AA.', 'first' => '01.268.001', 'second' => '01.416.000', 'qty' => 148000],
                        ['code' => 'AB.', 'first' => '00.415.376', 'second' => '00.511.375', 'qty' => 96000],
                        ['code' => 'AC.', 'first' => '00.973.751', 'second' => '01.165.750', 'qty' => 192000],
                    ],
                ],
                // 5. BPKB (Total Sisa: 486.000)
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
            ];

            $this->command->info('Memasukkan Stock Materiel Utama...');
            foreach ($materielUtama as $group) {
                $type = $group['type'];
                if (!$type) continue;
                $rack = $group['rack'];

                $groupTotalQty = collect($group['items'])->sum('qty');

                // Buat Master Stock Record untuk Utama (type_detail_id = null)
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

                // Buat StockDetail untuk setiap rentang serial number
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
                        'description' => "Stock opname seri {$item['code']} {$item['first']} - {$item['second']}",
                        'is_active' => true,
                    ]);
                }
            }

            // 6. Data Materiel Pendukung SBST (Tanggal 4 September 2026)
            $materielPendukung = [
                // 1. PENDUKUNG SIM
                [
                    'parent_type' => $simType,
                    'rack' => $rackSim,
                    'items' => [
                        ['name' => 'YMCKT', 'qty' => 3025000],
                        ['name' => 'PRINT LAMINASI', 'qty' => 3025000],
                        ['name' => 'FORMULIR', 'qty' => 3025000],
                    ],
                ],
                // 2. PENDUKUNG STNK
                [
                    'parent_type' => $stnkType,
                    'rack' => $rackStnk,
                    'items' => [
                        ['name' => 'SPRKB STNK', 'qty' => 1904000],
                        ['name' => 'BLANGKO CEK FISIK', 'qty' => 700000],
                    ],
                ],
                // 3. PENDUKUNG BPKB & E-BPKB
                [
                    'parent_type' => $bpkbType,
                    'rack' => $rackBpkb,
                    'items' => [
                        ['name' => 'SPRKB E-BPKB', 'qty' => 192000],
                    ],
                ],
                [
                    'parent_type' => $ebpkbType,
                    'rack' => $rackEbpkb,
                    'items' => [
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
                        ['name' => 'TANDA BUKTI PEMBAYARAN MUTASI', 'qty' => 200000],
                    ],
                ],
            ];

            $this->command->info('Memasukkan Stock Materiel Pendukung...');
            foreach ($materielPendukung as $group) {
                $type = $group['parent_type'];
                if (!$type) continue;
                $rack = $group['rack'];

                foreach ($group['items'] as $item) {
                    // Cari TypeDetail yang sesuai (atau cari berdasarkan nama dan parent type)
                    $typeDetail = TypeDetail::where('type_id', $type->id)
                        ->where('name', $item['name'])
                        ->first();

                    if (!$typeDetail) {
                        // Coba cari alternatif (misal reader rfid e-bpkb atau lainnya)
                        $typeDetail = TypeDetail::where('name', $item['name'])->first();
                    }

                    if (!$typeDetail) {
                        $typeDetail = TypeDetail::create([
                            'id' => Str::uuid()->toString(),
                            'type_id' => $type->id,
                            'name' => $item['name'],
                            'is_active' => true,
                        ]);
                    }

                    // Buat Master Stock Record untuk Pendukung
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

                    // Buat StockDetail record untuk Pendukung
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

            // 7. Catat Dokumen Resmi Stock Opname ke tabel stock_opnames & stock_opname_details
            $adminUser = User::role('Admin')->first() ?? User::first();
            $soCode = 'SO-POLDAJATIM-20260904-001';

            // Hapus SO lama dengan code yang sama jika ada
            $existingSo = StockOpname::where('code', $soCode)->first();
            if ($existingSo) {
                $existingSo->stockOpnameDetails()->forceDelete();
                $existingSo->forceDelete();
            }

            $stockOpname = StockOpname::create([
                'id' => Str::uuid()->toString(),
                'code' => $soCode,
                'opname_date' => '2026-09-04',
                'regional_police_id' => $polda->id,
                'police_station_id' => null,
                'status' => 'completed',
                'notes' => 'Stock Opname Materiel Utama & Pendukung SBST per 4 September 2026 (Penghitung: BRIGADIR DIO FARIZKA HABIBUN HAQ, Mengetahui: IPDA KUKUH KURNIAWAN, S.H. / PAMIN SIE FASMAT)',
                'checked_by' => $adminUser?->id,
                'approved_by' => $adminUser?->id,
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

            DB::commit();
            $this->command->info('StockPoldaSeeder berhasil dijalankan!');
            $this->command->info("Total StockDetail Polda: {$poldaStockDetails->count()} baris.");

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('Gagal menjalankan StockPoldaSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
