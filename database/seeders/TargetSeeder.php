<?php

namespace Database\Seeders;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Target\Target;
use App\Models\Target\TargetDetail;
use App\Models\Type\Type;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = 2026;

        // Reset existing target for this year or recreate
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

        // Check for Excel file
        $excelPath = public_path('TARGET PNBP DAN MATREG PER POLRES 2026 (1).xlsx');
        if (! file_exists($excelPath)) {
            $excelPath = public_path('excel/TARGET PNBP DAN MATREG PER POLRES 2026 (1).xlsx');
        }
        if (! file_exists($excelPath)) {
            // Check any matching file in public or public/excel
            $files = array_merge(
                glob(public_path('*TARGET*.xlsx')) ?: [],
                glob(public_path('excel/*TARGET*.xlsx')) ?: []
            );
            if (! empty($files)) {
                $excelPath = $files[0];
            }
        }

        if (! file_exists($excelPath)) {
            $this->command->warn("Excel file not found at {$excelPath}. Running fallback seed.");
            $this->fallbackSeed($target);
            return;
        }

        $this->command->info("Reading Target data from Excel: {$excelPath}");

        $reader = IOFactory::createReaderForFile($excelPath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($excelPath);

        $sheetMapping = [
            'DITLANTAS' => ['type' => 'regional', 'name' => 'Polda Jatim'],
            'TABES' => ['type' => 'station', 'name' => 'Polrestabes Surabaya'],
            'TA SDA' => ['type' => 'station', 'name' => 'Polresta Sidoarjo'],
            'GRESIK' => ['type' => 'station', 'name' => 'Polres Gresik'],
            'MJK KOTA' => ['type' => 'station', 'name' => 'Polres Mojokerto Kota'],
            'MOJOKERTO' => ['type' => 'station', 'name' => 'Polres Mojokerto'],
            'TA MLNG KOTA' => ['type' => 'station', 'name' => 'Polresta Malang Kota'],
            'MLNG' => ['type' => 'station', 'name' => 'Polres Malang'],
            'BATU' => ['type' => 'station', 'name' => 'Polres Batu'],
            'PROB KOTA' => ['type' => 'station', 'name' => 'Polres Probolinggo Kota'],
            'PROB' => ['type' => 'station', 'name' => 'Polres Probolinggo'],
            'PAS KOTA ' => ['type' => 'station', 'name' => 'Polres Pasuruan Kota'],
            'PASRN' => ['type' => 'station', 'name' => 'Polres Pasuruan'],
            'LMJG' => ['type' => 'station', 'name' => 'Polres Lumajang'],
            'BDWSO' => ['type' => 'station', 'name' => 'Polres Bondowoso'],
            'STBND' => ['type' => 'station', 'name' => 'Polres Situbondo'],
            'JMBR' => ['type' => 'station', 'name' => 'Polres Jember'],
            'TA BWI' => ['type' => 'station', 'name' => 'Polresta Banyuwangi'],
            'JMBNG' => ['type' => 'station', 'name' => 'Polres Jombang'],
            'KDR KOTA' => ['type' => 'station', 'name' => 'Polres Kediri Kota'],
            'KDR' => ['type' => 'station', 'name' => 'Polres Kediri'],
            'BLITR KOTA' => ['type' => 'station', 'name' => 'Polres Blitar Kota'],
            'BLITR' => ['type' => 'station', 'name' => 'Polres Blitar'],
            'TLNGAGNG' => ['type' => 'station', 'name' => 'Polres Tulungagung'],
            'NGJK' => ['type' => 'station', 'name' => 'Polres Nganjuk'],
            'TRNGGLK' => ['type' => 'station', 'name' => 'Polres Trenggalek'],
            'MDN KOTA' => ['type' => 'station', 'name' => 'Polres Madiun Kota'],
            'MDN' => ['type' => 'station', 'name' => 'Polres Madiun'],
            'NGAWI' => ['type' => 'station', 'name' => 'Polres Ngawi'],
            'MGTN' => ['type' => 'station', 'name' => 'Polres Magetan'],
            'PNRG' => ['type' => 'station', 'name' => 'Polres Ponorogo'],
            'PCTN' => ['type' => 'station', 'name' => 'Polres Pacitan'],
            'BJNG' => ['type' => 'station', 'name' => 'Polres Bojonegoro'],
            'TBN' => ['type' => 'station', 'name' => 'Polres Tuban'],
            'LMNG' => ['type' => 'station', 'name' => 'Polres Lamongan'],
            'PMKSN' => ['type' => 'station', 'name' => 'Polres Pamekasan'],
            'BGKLN' => ['type' => 'station', 'name' => 'Polres Bangkalan'],
            'SMPNG' => ['type' => 'station', 'name' => 'Polres Sampang'],
            'SMNP' => ['type' => 'station', 'name' => 'Polres Sumenep'],
        ];

        $polda = RegionalPolice::where('is_active', true)->first();
        $stations = PoliceStation::where('is_active', true)->get();
        $types = Type::where('is_active', true)->get();

        $typeMap = [];
        foreach ($types as $t) {
            $typeMap[strtoupper(trim($t->name))] = $t;
        }

        $count = 0;

        foreach ($sheetMapping as $sheetName => $info) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (! $sheet) {
                continue;
            }

            // Identify location
            if ($info['type'] === 'regional') {
                $regionalId = $polda?->id;
                $stationId = null;
                $label = $polda?->name ?? 'Polda Jatim';
            } else {
                $station = $stations->firstWhere('name', $info['name']);
                if (! $station) {
                    continue;
                }
                $regionalId = $station->regional_police_id;
                $stationId = $station->id;
                $label = $station->name;
            }

            // 1. SIM (SIM BARU + SIM PERPANJANG)
            $simBaru = 0;
            for ($r = 13; $r <= 21; $r++) {
                $val = $sheet->getCell('G' . $r)->getValue();
                if (is_numeric($val)) {
                    $simBaru += (float) $val;
                }
            }
            $simPerpanjang = 0;
            for ($r = 24; $r <= 32; $r++) {
                $val = $sheet->getCell('G' . $r)->getValue();
                if (is_numeric($val)) {
                    $simPerpanjang += (float) $val;
                }
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
                if (is_numeric($val)) {
                    $bpkb += (float) $val;
                }
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
            if (is_numeric($val)) {
                $tckbR2 += (float) $val;
            }
            $tckbR4 = 0;
            $val = $sheet->getCell('G54')->getValue();
            if (is_numeric($val)) {
                $tckbR4 += (float) $val;
            }

            // Map values to DB Types
            foreach ($types as $type) {
                $typeName = strtoupper(trim($type->name));
                $qty = 0;

                if ($typeName === 'SIM' || $typeName === 'SIM CARD') {
                    $qty = $simTotal;
                } elseif ($typeName === 'STNK') {
                    $qty = $stnk;
                } elseif ($typeName === 'BPKB') {
                    $qty = $bpkb;
                } elseif ($typeName === 'E-BPKB') {
                    $qty = 0;
                } elseif ($typeName === 'STCK') {
                    $qty = $stck;
                } elseif ($typeName === 'MUTASI') {
                    $qty = $mutasi;
                } elseif (str_contains($typeName, 'TCKB R2')) {
                    $qty = str_contains($typeName, 'LISTRIK') ? 0 : $tckbR2;
                } elseif (str_contains($typeName, 'TCKB R4')) {
                    $qty = str_contains($typeName, 'LISTRIK') ? 0 : $tckbR4;
                } elseif ($typeName === 'TNKB REG') {
                    $qty = $tnkbR2 + $tnkbR4;
                } elseif ($typeName === 'TNKB LISTRIK') {
                    $qty = 0;
                } elseif (str_contains($typeName, 'TNKB R2 PUTIH')) {
                    $qty = str_contains($typeName, 'LISTRIK') ? 0 : $tnkbR2;
                } elseif (str_contains($typeName, 'TNKB R4 PUTIH')) {
                    $qty = str_contains($typeName, 'LISTRIK') ? 0 : $tnkbR4;
                } elseif (str_contains($typeName, 'TNKB')) {
                    $qty = 0;
                }

                TargetDetail::create([
                    'name' => $label,
                    'target_id' => $target->id,
                    'regional_police_id' => $regionalId,
                    'police_station_id' => $stationId,
                    'type_id' => $type->id,
                    'type_detail_id' => null,
                    'quantity' => $qty,
                    'description' => $target->description,
                    'is_active' => true,
                ]);

                $count++;
            }
        }

        // Also check if any station in DB was not in the 38 Excel sheets (e.g. Tanjung Perak)
        foreach ($stations as $st) {
            $existing = TargetDetail::where('target_id', $target->id)
                ->where('police_station_id', $st->id)
                ->exists();

            if (! $existing) {
                foreach ($types as $type) {
                    TargetDetail::create([
                        'name' => $st->name,
                        'target_id' => $target->id,
                        'regional_police_id' => $st->regional_police_id,
                        'police_station_id' => $st->id,
                        'type_id' => $type->id,
                        'type_detail_id' => null,
                        'quantity' => 0,
                        'description' => $target->description,
                        'is_active' => true,
                    ]);
                    $count++;
                }
            }
        }

        $this->command->info("Successfully seeded {$count} TargetDetail records from Excel for Target 2026!");
    }

    private function fallbackSeed(Target $target): void
    {
        $types = Type::where('is_active', true)->get(['id', 'name']);
        $regionalPolice = RegionalPolice::with(['policeStations' => fn ($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->get(['id', 'name']);

        foreach ($regionalPolice as $regional) {
            foreach ($types as $type) {
                TargetDetail::create([
                    'name' => $regional->name,
                    'target_id' => $target->id,
                    'regional_police_id' => $regional->id,
                    'police_station_id' => null,
                    'type_id' => $type->id,
                    'type_detail_id' => null,
                    'quantity' => 0,
                    'description' => $target->description,
                    'is_active' => true,
                ]);
            }

            foreach ($regional->policeStations as $station) {
                foreach ($types as $type) {
                    TargetDetail::create([
                        'name' => $station->name,
                        'target_id' => $target->id,
                        'regional_police_id' => $station->regional_police_id,
                        'police_station_id' => $station->id,
                        'type_id' => $type->id,
                        'type_detail_id' => null,
                        'quantity' => 0,
                        'description' => $target->description,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
