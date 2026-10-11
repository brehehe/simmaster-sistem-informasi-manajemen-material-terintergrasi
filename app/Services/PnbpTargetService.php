<?php

namespace App\Services;

use App\Models\Target\Target;
use App\Models\Target\TargetDetail;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PnbpTargetService
{
    public const DEFAULT_FILENAME = 'CAPAIAN TARGET PNBP 2026 (1).xlsx';

    public const SHEETS = [
        'JANUARI',
        'FEB',
        'MARET',
        'APRIL',
        'MEI',
        'JUNI',
        'JULI',
        'AGUSTUS',
        'SEPT',
        'OKT',
        'NOV',
        'DES',
        'REKAP',
    ];

    public const CATEGORY_ROWS = [
        13 => ['no' => '1', 'name' => 'Pengujian untuk penerbitan SIM baru', 'level' => 1],
        14 => ['no' => '  a.', 'name' => 'Penerbitan SIM A', 'level' => 2],
        15 => ['no' => '  b.', 'name' => 'Penerbitan SIM B I', 'level' => 2],
        16 => ['no' => '  c.', 'name' => 'Penerbitan SIM B II', 'level' => 2],
        17 => ['no' => '  d.', 'name' => 'Penerbitan SIM C', 'level' => 2],
        18 => ['no' => '  e.', 'name' => 'Penerbitan SIM C I', 'level' => 2],
        19 => ['no' => '  f.', 'name' => 'Penerbitan SIM C II', 'level' => 2],
        20 => ['no' => '  g.', 'name' => 'Penerbitan SIM D', 'level' => 2],
        21 => ['no' => '  h.', 'name' => 'Penerbitan SIM D I', 'level' => 2],
        22 => ['no' => '  i.', 'name' => 'Pembuatan SIM International', 'level' => 2],

        24 => ['no' => '2', 'name' => 'Penerbitan Perpanjangan SIM', 'level' => 1],
        25 => ['no' => '  a.', 'name' => 'Penerbitan SIM A', 'level' => 2],
        26 => ['no' => '  b.', 'name' => 'Penerbitan SIM B I', 'level' => 2],
        27 => ['no' => '  c.', 'name' => 'Penerbitan SIM B II', 'level' => 2],
        28 => ['no' => '  d.', 'name' => 'Penerbitan SIM C', 'level' => 2],
        29 => ['no' => '  e.', 'name' => 'Penerbitan SIM C I', 'level' => 2],
        30 => ['no' => '  f.', 'name' => 'Penerbitan SIM C II', 'level' => 2],
        31 => ['no' => '  g.', 'name' => 'Penerbitan SIM D', 'level' => 2],
        32 => ['no' => '  h.', 'name' => 'Penerbitan SIM D I', 'level' => 2],
        33 => ['no' => '  i.', 'name' => 'Penerbitan SIM Internasional', 'level' => 2],

        35 => ['no' => '3', 'name' => 'Penerbitan SKUKP', 'level' => 1],

        37 => ['no' => '4', 'name' => 'Penerbitan STNK', 'level' => 1],
        38 => ['no' => '  a.', 'name' => 'Kendaraan Bermotor Roda 2 atau 3', 'level' => 2],
        39 => ['no' => '    1)', 'name' => 'Baru', 'level' => 3],
        40 => ['no' => '    2)', 'name' => 'Perpanjangan', 'level' => 3],
        41 => ['no' => '  b.', 'name' => 'Kendaraan Bermotor Roda 4 atau Lebih', 'level' => 2],
        42 => ['no' => '    1)', 'name' => 'Baru', 'level' => 3],
        43 => ['no' => '    2)', 'name' => 'Perpanjangan', 'level' => 3],

        45 => ['no' => '5', 'name' => 'Penerbitan TNKB', 'level' => 1],
        46 => ['no' => '  a.', 'name' => 'Kendaraan Bermotor Roda 2 atau 3', 'level' => 2],
        47 => ['no' => '  b.', 'name' => 'Kendaraan Bermotor Roda 4 atau Lebih', 'level' => 2],

        49 => ['no' => '6', 'name' => 'Penerbitan STCK', 'level' => 1],
        50 => ['no' => '  a.', 'name' => 'Kendaraan Bermotor Roda 2 atau 3', 'level' => 2],
        51 => ['no' => '  b.', 'name' => 'Kendaraan Bermotor Roda 4 atau Lebih', 'level' => 2],

        53 => ['no' => '7', 'name' => 'Penerbitan TCKB', 'level' => 1],
        54 => ['no' => '  a.', 'name' => 'Kendaraan Bermotor Roda 2 atau 3', 'level' => 2],
        55 => ['no' => '  b.', 'name' => 'Kendaraan Bermotor Roda 4 atau Lebih', 'level' => 2],

        57 => ['no' => '8', 'name' => 'Penerbitan BPKB', 'level' => 1],
        58 => ['no' => '  a.', 'name' => 'Kendaraan Bermotor Roda 2 atau 3', 'level' => 2],
        59 => ['no' => '    1)', 'name' => 'Baru', 'level' => 3],
        60 => ['no' => '    2)', 'name' => 'Ganti Kepemilikan', 'level' => 3],
        61 => ['no' => '  b.', 'name' => 'Kendaraan Bermotor Roda 4 atau Lebih', 'level' => 2],
        62 => ['no' => '    1)', 'name' => 'Baru', 'level' => 3],
        63 => ['no' => '    2)', 'name' => 'Ganti Kepemilikan', 'level' => 3],

        65 => ['no' => '9', 'name' => 'Penerbitan Surat Mutasi Luar Daerah', 'level' => 1],
        66 => ['no' => '  a.', 'name' => 'Kendaraan Bermotor Roda 2 atau 3', 'level' => 2],
        67 => ['no' => '  b.', 'name' => 'Kendaraan Bermotor Roda 4 atau Lebih', 'level' => 2],

        69 => ['no' => '10', 'name' => 'Penerbitan STNK-LBN', 'level' => 1],
        77 => ['no' => '11', 'name' => 'Penerbitan TNKB-LBN', 'level' => 1],

        85 => ['no' => '12', 'name' => 'Penerbitan NRKB Pilihan', 'level' => 1],
        86 => ['no' => '  a.', 'name' => 'NRKB Pilihan 1 Angka', 'level' => 2],
        87 => ['no' => '    1)', 'name' => 'Tanpa Huruf Belakang (Blank)', 'level' => 3],
        88 => ['no' => '    2)', 'name' => 'Ada Huruf Belakang', 'level' => 3],
        89 => ['no' => '  b.', 'name' => 'NRKB Pilihan 2 Angka', 'level' => 2],
        90 => ['no' => '    1)', 'name' => 'Tanpa Huruf Belakang (Blank)', 'level' => 3],
        91 => ['no' => '    2)', 'name' => 'Ada Huruf Belakang', 'level' => 3],
        92 => ['no' => '  c.', 'name' => 'NRKB Pilihan 3 Angka', 'level' => 2],
        93 => ['no' => '    1)', 'name' => 'Tanpa Huruf Belakang (Blank)', 'level' => 3],
        94 => ['no' => '    2)', 'name' => 'Ada Huruf Belakang', 'level' => 3],
        95 => ['no' => '  d.', 'name' => 'NRKB Pilihan 4 Angka', 'level' => 2],
        96 => ['no' => '    1)', 'name' => 'Tanpa Huruf Belakang (Blank)', 'level' => 3],
        97 => ['no' => '    2)', 'name' => 'Ada Huruf Belakang', 'level' => 3],

        99 => ['no' => 'TOTAL', 'name' => 'TOTAL TARGET PNBP', 'level' => 0],
    ];

    /**
     * Cari path file Excel Capaian Target PNBP 2026.
     */
    public function resolveExcelPath(?string $customPath = null): string
    {
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        $candidates = [
            public_path(self::DEFAULT_FILENAME),
            public_path('excel/' . self::DEFAULT_FILENAME),
            base_path(self::DEFAULT_FILENAME),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Cari file dengan pola *CAPAIAN*TARGET*.xlsx
        $matches = array_merge(
            glob(public_path('*CAPAIAN*TARGET*.xlsx')) ?: [],
            glob(public_path('excel/*CAPAIAN*TARGET*.xlsx')) ?: []
        );

        if (! empty($matches)) {
            return $matches[0];
        }

        throw new \RuntimeException("File Excel Capaian Target PNBP 2026 tidak ditemukan di folder public/");
    }

    /**
     * Load spreadsheet instance.
     */
    public function loadSpreadsheet(?string $filePath = null): Spreadsheet
    {
        $path = $this->resolveExcelPath($filePath);
        $reader = IOFactory::createReaderForFile($path);
        return $reader->load($path);
    }

    /**
     * Dapatkan ringkasan capaian per sheet bulanan (Januari s/d Desember + Rekap).
     */
    public function getMonthlySummary(?string $filePath = null): array
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $summaries = [];

        $monthLabels = [
            'JANUARI' => 'Januari 2026',
            'FEB'     => 'Februari 2026',
            'MARET'   => 'Maret 2026',
            'APRIL'   => 'April 2026',
            'MEI'     => 'Mei 2026',
            'JUNI'    => 'Juni 2026',
            'JULI'    => 'Juli 2026',
            'AGUSTUS' => 'Agustus 2026',
            'SEPT'    => 'September 2026',
            'OKT'     => 'Oktober 2026',
            'NOV'     => 'November 2026',
            'DES'     => 'Desember 2026',
            'REKAP'   => 'Rekap Tahunan 2026',
        ];

        foreach (self::SHEETS as $index => $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (! $sheet) {
                continue;
            }

            // Total baris 99
            $volTgt = (float) $this->getNumericCellValue($sheet, 'I99');
            $tgtRp  = (float) $this->getNumericCellValue($sheet, 'K99');
            $volReal = (float) $this->getNumericCellValue($sheet, 'L99');
            $realRp  = (float) $this->getNumericCellValue($sheet, 'N99');

            $pct = $tgtRp > 0 ? round(($realRp / $tgtRp) * 100, 2) : 0;
            $status = ($volReal > 0 || $realRp > 0) ? 'Terealisasi' : 'Belum Ada Data';

            $summaries[] = [
                'index'             => $index + 1,
                'sheet_name'        => $sheetName,
                'label'             => $monthLabels[$sheetName] ?? $sheetName,
                'volume_target'     => $volTgt,
                'target_rp'         => $tgtRp,
                'volume_realization'=> $volReal,
                'realization_rp'    => $realRp,
                'percentage'        => $pct,
                'status'            => $status,
            ];
        }

        return $summaries;
    }

    /**
     * Dapatkan rincian baris per jenis penerimaan untuk sheet tertentu.
     */
    public function getSheetDetails(string $sheetName, ?string $filePath = null): array
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $normalizedName = strtoupper(trim($sheetName));

        // Mapping alias nama sheet
        $aliasMap = [
            '1' => 'JANUARI', 'JAN' => 'JANUARI', 'JANUARI' => 'JANUARI',
            '2' => 'FEB', 'PEB' => 'FEB', 'FEBRUARI' => 'FEB', 'FEB' => 'FEB',
            '3' => 'MARET', 'MAR' => 'MARET', 'MARET' => 'MARET',
            '4' => 'APRIL', 'APR' => 'APRIL', 'APRIL' => 'APRIL',
            '5' => 'MEI', 'MAY' => 'MEI', 'MEI' => 'MEI',
            '6' => 'JUNI', 'JUN' => 'JUNI', 'JUNI' => 'JUNI',
            '7' => 'JULI', 'JUL' => 'JULI', 'JULI' => 'JULI',
            '8' => 'AGUSTUS', 'AGU' => 'AGUSTUS', 'AGT' => 'AGUSTUS', 'AGUSTUS' => 'AGUSTUS',
            '9' => 'SEPT', 'SEP' => 'SEPT', 'SEPTEMBER' => 'SEPT', 'SEPT' => 'SEPT',
            '10' => 'OKT', 'OKTOBER' => 'OKT', 'OCT' => 'OKT', 'OKT' => 'OKT',
            '11' => 'NOV', 'NOP' => 'NOV', 'NOVEMBER' => 'NOV', 'NOV' => 'NOV',
            '12' => 'DES', 'DESEMBER' => 'DES', 'DEC' => 'DES', 'DES' => 'DES',
            'REKAP' => 'REKAP', 'ALL' => 'REKAP', 'TOTAL' => 'REKAP',
        ];

        $targetSheetName = $aliasMap[$normalizedName] ?? $normalizedName;
        $sheet = $spreadsheet->getSheetByName($targetSheetName);

        if (! $sheet) {
            throw new \InvalidArgumentException("Sheet '{$sheetName}' tidak ditemukan dalam file Excel.");
        }

        $items = [];

        foreach (self::CATEGORY_ROWS as $rowNum => $meta) {
            $volTgt   = (float) $this->getNumericCellValue($sheet, "I{$rowNum}");
            $tarif    = (float) $this->getNumericCellValue($sheet, "J{$rowNum}");
            $tgtRp    = (float) $this->getNumericCellValue($sheet, "K{$rowNum}");
            $volReal  = (float) $this->getNumericCellValue($sheet, "L{$rowNum}");
            $realRp   = (float) $this->getNumericCellValue($sheet, "N{$rowNum}");
            $pct      = $tgtRp > 0 ? round(($realRp / $tgtRp) * 100, 2) : 0;

            // Jika tarif 0 tapi ada di cell M
            if ($tarif == 0) {
                $tarifReal = (float) $this->getNumericCellValue($sheet, "M{$rowNum}");
                if ($tarifReal > 0) {
                    $tarif = $tarifReal;
                }
            }

            $items[] = [
                'row'               => $rowNum,
                'no'                => $meta['no'],
                'name'              => $meta['name'],
                'level'             => $meta['level'],
                'tarif'             => $tarif,
                'volume_target'     => $volTgt,
                'target_rp'         => $tgtRp,
                'volume_realization'=> $volReal,
                'realization_rp'    => $realRp,
                'percentage'        => $pct,
            ];
        }

        return [
            'sheet_name' => $targetSheetName,
            'items'      => $items,
        ];
    }

    /**
     * Hubungkan dan sinkronisasi dengan tabel Target di database.
     */
    public function syncTargetWithDatabase(?string $filePath = null): array
    {
        $path = $this->resolveExcelPath($filePath);
        $year = 2026;

        // Ambil atau buat Target tahun 2026
        $target = Target::firstOrCreate(
            ['year' => $year],
            [
                'name'        => "Target Ditlantas {$year}",
                'year'        => $year,
                'description' => "Target Renbut Materiel dan Capaian Realisasi PNBP Ditlantas & Polres Jajaran TA {$year}",
                'is_active'   => true,
            ]
        );

        // Pastikan aktif dan perbarui deskripsi
        $target->update([
            'is_active'   => true,
            'description' => "Target Renbut Materiel dan Capaian Realisasi PNBP Ditlantas & Polres Jajaran TA {$year} (Tersambung ke " . basename($path) . ")",
        ]);

        $dbTotalQty = (float) TargetDetail::where('target_id', $target->id)->sum('quantity');
        $dbDetailsCount = TargetDetail::where('target_id', $target->id)->count();

        // Ambil rekap dari Excel
        $rekap = $this->getSheetDetails('REKAP', $path);
        $totalRow = collect($rekap['items'])->firstWhere('row', 99);

        $excelTargetQty = $totalRow['volume_target'] ?? 0;
        $excelTargetRp  = $totalRow['target_rp'] ?? 0;
        $excelRealQty    = $totalRow['volume_realization'] ?? 0;
        $excelRealRp     = $totalRow['realization_rp'] ?? 0;
        $excelPct        = $totalRow['percentage'] ?? 0;

        $isQuantityAligned = abs($dbTotalQty - $excelTargetQty) < 1;

        return [
            'target_model'       => $target,
            'excel_file'         => basename($path),
            'excel_path'         => $path,
            'db_total_quantity'  => $dbTotalQty,
            'db_details_count'   => $dbDetailsCount,
            'excel_target_qty'   => $excelTargetQty,
            'excel_target_rp'    => $excelTargetRp,
            'excel_real_qty'     => $excelRealQty,
            'excel_real_rp'      => $excelRealRp,
            'excel_percentage'   => $excelPct,
            'is_aligned'         => $isQuantityAligned,
        ];
    }

    /**
     * Helper mengambil nilai numeric dari cell formula/value.
     */
    protected function getNumericCellValue($sheet, string $coordinate): float
    {
        try {
            $val = $sheet->getCell($coordinate)->getCalculatedValue();
        } catch (\Throwable $e) {
            $val = $sheet->getCell($coordinate)->getValue();
        }

        if (is_numeric($val)) {
            return (float) $val;
        }

        if (is_string($val)) {
            // Bersihkan format titik, koma, spasi
            $cleaned = preg_replace('/[^-0-9.]/', '', str_replace(',', '', $val));
            if (is_numeric($cleaned)) {
                return (float) $cleaned;
            }
        }

        return 0.0;
    }
}
