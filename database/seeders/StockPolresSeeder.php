<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Police\PoliceStation;
use App\Models\Type\Type;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;

class StockPolresSeeder extends Seeder
{
    /**
     * Run the database seeds for Stock Polres-Polres Jajaran Polda Jatim.
     * Based on official data from: TARGET PNBP DAN MATREG PER POLRES 2026 (1).xlsx
     * Uses the 2026 material requirement (Renbut Materiel) as initial stock for each Polres.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $this->command->info('Memulai seeding Stock Polres-Polres berdasarkan data Excel 2026...');

            // 1. Temukan File Excel
            $excelPath = public_path('TARGET PNBP DAN MATREG PER POLRES 2026 (1).xlsx');
            if (!file_exists($excelPath)) {
                $excelPath = public_path('excel/TARGET PNBP DAN MATREG PER POLRES 2026 (1).xlsx');
            }
            if (!file_exists($excelPath)) {
                $files = array_merge(
                    glob(public_path('*TARGET*.xlsx')) ?: [],
                    glob(public_path('excel/*TARGET*.xlsx')) ?: []
                );
                if (!empty($files)) {
                    $excelPath = $files[0];
                }
            }

            if (!file_exists($excelPath)) {
                $this->command->error("File Excel tidak ditemukan: {$excelPath}");
                return;
            }

            $this->command->info("Membaca file Excel: {$excelPath}");

            $reader = IOFactory::createReaderForFile($excelPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($excelPath);

            // 2. Mapping Sheet Polres ke Nama PoliceStation di Database
            $sheetMapping = [
                'TABES' => 'Polrestabes Surabaya',
                'TA SDA' => 'Polresta Sidoarjo',
                'GRESIK' => 'Polres Gresik',
                'MJK KOTA' => 'Polres Mojokerto Kota',
                'MOJOKERTO' => 'Polres Mojokerto',
                'TA MLNG KOTA' => 'Polresta Malang Kota',
                'MLNG' => 'Polres Malang',
                'BATU' => 'Polres Batu',
                'PROB KOTA' => 'Polres Probolinggo Kota',
                'PROB' => 'Polres Probolinggo',
                'PAS KOTA ' => 'Polres Pasuruan Kota',
                'PASRN' => 'Polres Pasuruan',
                'LMJG' => 'Polres Lumajang',
                'BDWSO' => 'Polres Bondowoso',
                'STBND' => 'Polres Situbondo',
                'JMBR' => 'Polres Jember',
                'TA BWI' => 'Polresta Banyuwangi',
                'JMBNG' => 'Polres Jombang',
                'KDR KOTA' => 'Polres Kediri Kota',
                'KDR' => 'Polres Kediri',
                'BLITR KOTA' => 'Polres Blitar Kota',
                'BLITR' => 'Polres Blitar',
                'TLNGAGNG' => 'Polres Tulungagung',
                'NGJK' => 'Polres Nganjuk',
                'TRNGGLK' => 'Polres Trenggalek',
                'MDN KOTA' => 'Polres Madiun Kota',
                'MDN' => 'Polres Madiun',
                'NGAWI' => 'Polres Ngawi',
                'MGTN' => 'Polres Magetan',
                'PNRG' => 'Polres Ponorogo',
                'PCTN' => 'Polres Pacitan',
                'BJNG' => 'Polres Bojonegoro',
                'TBN' => 'Polres Tuban',
                'LMNG' => 'Polres Lamongan',
                'PMKSN' => 'Polres Pamekasan',
                'BGKLN' => 'Polres Bangkalan',
                'SMPNG' => 'Polres Sampang',
                'SMNP' => 'Polres Sumenep',
            ];

            // 3. Bersihkan Stock Lama Milik Polres (yang berisi data mock / null type)
            $this->command->info('Membersihkan data Stock lama milik seluruh Polres...');
            StockDetail::whereNotNull('police_station_id')->delete();
            Stock::whereNotNull('police_station_id')->delete();

            $stations = PoliceStation::all();
            $types = Type::where('is_active', true)->get();

            $totalStationsSeeded = 0;
            $totalDetailsSeeded = 0;
            $stationValues = [];

            // 4. Ekstrak Data dari Masing-Masing Sheet Polres
            foreach ($sheetMapping as $sheetName => $stationName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                if (!$sheet) continue;

                $station = $stations->firstWhere('name', $stationName);
                if (!$station) {
                    $this->command->warn("Police Station '{$stationName}' tidak ditemukan di database.");
                    continue;
                }

                // 1. SIM (SIM Baru + SIM Perpanjang)
                $simBaru = 0;
                for ($r = 13; $r <= 21; $r++) {
                    $val = $sheet->getCell('G' . $r)->getValue();
                    if (is_numeric($val)) $simBaru += (float) $val;
                }
                $simPerpanjang = 0;
                for ($r = 24; $r <= 32; $r++) {
                    $val = $sheet->getCell('G' . $r)->getValue();
                    if (is_numeric($val)) $simPerpanjang += (float) $val;
                }
                $simTotal = $simBaru + $simPerpanjang;

                // 2. STNK
                $stnk = 0;
                foreach ([38, 39, 41, 42] as $r) {
                    $val = $sheet->getCell('G' . $r)->getValue();
                    $valI = $sheet->getCell('I' . $r)->getValue();
                    $valH = $sheet->getCell('H' . $r)->getValue();
                    if (is_numeric($val)) {
                        $stnk += (float) $val;
                    } elseif (is_numeric($valI) && is_numeric($valH) && $valH > 0) {
                        $stnk += ((float) $valI / (float) $valH);
                    }
                }

                // 3. BPKB
                $bpkb = 0;
                foreach ([58, 59, 61, 62] as $r) {
                    $val = $sheet->getCell('G' . $r)->getValue();
                    if (is_numeric($val)) $bpkb += (float) $val;
                }

                // 4. STCK
                $stck = 0;
                foreach ([45, 46] as $r) {
                    $val = $sheet->getCell('G' . $r)->getValue();
                    $valI = $sheet->getCell('I' . $r)->getValue();
                    $valH = $sheet->getCell('H' . $r)->getValue();
                    if (is_numeric($val)) {
                        $stck += (float) $val;
                    } elseif (is_numeric($valI) && is_numeric($valH) && $valH > 0) {
                        $stck += ((float) $valI / (float) $valH);
                    }
                }

                // 5. MUTASI
                $mutasi = 0;
                foreach ([65, 66] as $r) {
                    $val = $sheet->getCell('G' . $r)->getValue();
                    $valI = $sheet->getCell('I' . $r)->getValue();
                    $valH = $sheet->getCell('H' . $r)->getValue();
                    if (is_numeric($val)) {
                        $mutasi += (float) $val;
                    } elseif (is_numeric($valI) && is_numeric($valH) && $valH > 0) {
                        $mutasi += ((float) $valI / (float) $valH);
                    }
                }

                // 6. TNKB R2 & R4
                $tnkbR2 = 0;
                $val = $sheet->getCell('G49')->getValue();
                $valI = $sheet->getCell('I49')->getValue();
                $valH = $sheet->getCell('H49')->getValue();
                if (is_numeric($val)) {
                    $tnkbR2 += (float) $val;
                } elseif (is_numeric($valI) && is_numeric($valH) && $valH > 0) {
                    $tnkbR2 += ((float) $valI / (float) $valH);
                }

                $tnkbR4 = 0;
                $val = $sheet->getCell('G50')->getValue();
                $valI = $sheet->getCell('I50')->getValue();
                $valH = $sheet->getCell('H50')->getValue();
                if (is_numeric($val)) {
                    $tnkbR4 += (float) $val;
                } elseif (is_numeric($valI) && is_numeric($valH) && $valH > 0) {
                    $tnkbR4 += ((float) $valI / (float) $valH);
                }

                // 7. TCKB R2 & R4
                $tckbR2 = 0;
                $val = $sheet->getCell('G53')->getValue();
                if (is_numeric($val)) $tckbR2 += (float) $val;

                $tckbR4 = 0;
                $val = $sheet->getCell('G54')->getValue();
                if (is_numeric($val)) $tckbR4 += (float) $val;

                $stationValues[$station->id] = [
                    'sim' => $simTotal,
                    'stnk' => $stnk,
                    'bpkb' => $bpkb,
                    'stck' => $stck,
                    'mutasi' => $mutasi,
                    'tnkbR2' => $tnkbR2,
                    'tnkbR4' => $tnkbR4,
                    'tckbR2' => $tckbR2,
                    'tckbR4' => $tckbR4,
                ];
            }

            // 5. Tangani Station yang tidak ada di Sheet (misal: Polres Pelabuhan Tanjung Perak)
            // Ambil rata-rata dari polres jajaran Surabaya/Sidoarjo
            $avgSim = (float) collect($stationValues)->avg('sim');
            $avgStnk = (float) collect($stationValues)->avg('stnk');
            $avgBpkb = (float) collect($stationValues)->avg('bpkb');
            $avgStck = (float) collect($stationValues)->avg('stck');
            $avgMutasi = (float) collect($stationValues)->avg('mutasi');
            $avgTnkbR2 = (float) collect($stationValues)->avg('tnkbR2');
            $avgTnkbR4 = (float) collect($stationValues)->avg('tnkbR4');
            $avgTckbR2 = (float) collect($stationValues)->avg('tckbR2');
            $avgTckbR4 = (float) collect($stationValues)->avg('tckbR4');

            foreach ($stations as $st) {
                if (!isset($stationValues[$st->id])) {
                    $stationValues[$st->id] = [
                        'sim' => round($avgSim * 0.4),
                        'stnk' => round($avgStnk * 0.4),
                        'bpkb' => round($avgBpkb * 0.4),
                        'stck' => round($avgStck * 0.4),
                        'mutasi' => round($avgMutasi * 0.4),
                        'tnkbR2' => round($avgTnkbR2 * 0.4),
                        'tnkbR4' => round($avgTnkbR4 * 0.4),
                        'tckbR2' => round($avgTckbR2 * 0.4),
                        'tckbR4' => round($avgTckbR4 * 0.4),
                    ];
                }
            }

            // 6. Masukkan Data ke Tabel `stocks` dan `stock_details`
            foreach ($stations as $st) {
                $vals = $stationValues[$st->id] ?? null;
                if (!$vals) continue;

                foreach ($types as $type) {
                    $typeName = strtoupper(trim($type->name));
                    $qty = 0;

                    if ($typeName === 'SIM' || $typeName === 'SIM CARD') {
                        $qty = $vals['sim'];
                    } elseif ($typeName === 'STNK') {
                        $qty = $vals['stnk'];
                    } elseif ($typeName === 'BPKB') {
                        $qty = $vals['bpkb'];
                    } elseif ($typeName === 'E-BPKB') {
                        $qty = 0;
                    } elseif ($typeName === 'STCK') {
                        $qty = $vals['stck'];
                    } elseif ($typeName === 'MUTASI') {
                        $qty = $vals['mutasi'];
                    } elseif (str_contains($typeName, 'TCKB R2')) {
                        $qty = str_contains($typeName, 'LISTRIK') ? 0 : $vals['tckbR2'];
                    } elseif (str_contains($typeName, 'TCKB R4')) {
                        $qty = str_contains($typeName, 'LISTRIK') ? 0 : $vals['tckbR4'];
                    } elseif ($typeName === 'TNKB REG') {
                        $qty = $vals['tnkbR2'] + $vals['tnkbR4'];
                    } elseif ($typeName === 'TNKB LISTRIK') {
                        $qty = 0;
                    } elseif (str_contains($typeName, 'TNKB R2 PUTIH')) {
                        $qty = str_contains($typeName, 'LISTRIK') ? 0 : $vals['tnkbR2'];
                    } elseif (str_contains($typeName, 'TNKB R4 PUTIH')) {
                        $qty = str_contains($typeName, 'LISTRIK') ? 0 : $vals['tnkbR4'];
                    }

                    if ($qty <= 0) continue;

                    // Buat Stock Header
                    $stock = Stock::create([
                        'id' => Str::uuid()->toString(),
                        'type_id' => $type->id,
                        'type_detail_id' => null,
                        'regional_police_id' => null,
                        'police_station_id' => $st->id,
                        'quantity' => $qty,
                        'description' => "Stok Awal Alokasi {$type->name} {$st->name} TA 2026",
                        'is_active' => true,
                    ]);

                    // Buat Stock Detail
                    StockDetail::create([
                        'id' => Str::uuid()->toString(),
                        'stock_id' => $stock->id,
                        'type_id' => $type->id,
                        'type_detail_id' => null,
                        'regional_police_id' => null,
                        'police_station_id' => $st->id,
                        'rack_id' => null,
                        'code' => null,
                        'number_serial_first' => null,
                        'number_serial_second' => null,
                        'quantity' => $qty,
                        'description' => "Stok Awal Alokasi {$type->name} TA 2026",
                        'is_active' => true,
                    ]);

                    $totalDetailsSeeded++;
                }

                $totalStationsSeeded++;
            }

            DB::commit();

            $this->command->info("StockPolresSeeder berhasil dijalankan!");
            $this->command->info("Total {$totalStationsSeeded} Polres berhasil di-seed dengan {$totalDetailsSeeded} data stok material.");

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('Gagal menjalankan StockPolresSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
