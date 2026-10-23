<?php

namespace App\Services;

use App\Models\Target\Target;
use App\Models\Target\TargetDetail;
use App\Models\Target\TargetPnbpItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
     * Dapatkan daftar tahun target yang tersedia (dari Target DB dan file Excel).
     */
    public function getAvailableYears(): array
    {
        $dbYears = Target::distinct()->pluck('year')->filter()->map(fn($y) => (int)$y)->toArray();
        $pnbpYears = TargetPnbpItem::distinct()->pluck('year')->filter()->map(fn($y) => (int)$y)->toArray();

        // Scan files di public/ dan public/excel/
        $fileYears = [];
        $files = array_merge(
            glob(public_path('*CAPAIAN*TARGET*.xlsx')) ?: [],
            glob(public_path('excel/*CAPAIAN*TARGET*.xlsx')) ?: []
        );
        foreach ($files as $file) {
            if (preg_match('/20\d{2}/', basename($file), $m)) {
                $fileYears[] = (int) $m[0];
            }
        }

        $currentYear = (int) date('Y');
        $defaultYears = [$currentYear - 2, $currentYear - 1, $currentYear, $currentYear + 1, 2026];
        $all = array_unique(array_merge($dbYears, $pnbpYears, $fileYears, $defaultYears));
        rsort($all);

        return array_values($all);
    }

    /**
     * Cari path file Excel Capaian Target PNBP per tahun.
     */
    public function resolveExcelPath(?string $customPath = null, int $year = 2026, bool $throwIfNotFound = false): ?string
    {
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        $candidates = [
            public_path("CAPAIAN TARGET PNBP {$year} (1).xlsx"),
            public_path("CAPAIAN TARGET PNBP {$year}.xlsx"),
            public_path("excel/CAPAIAN TARGET PNBP {$year} (1).xlsx"),
            public_path("excel/CAPAIAN TARGET PNBP {$year}.xlsx"),
            public_path("CAPAIAN_TARGET_PNBP_{$year}.xlsx"),
        ];

        if ($year === 2026) {
            $candidates[] = public_path(self::DEFAULT_FILENAME);
            $candidates[] = public_path('excel/' . self::DEFAULT_FILENAME);
            $candidates[] = base_path(self::DEFAULT_FILENAME);
        }

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $matches = array_merge(
            glob(public_path("*CAPAIAN*TARGET*{$year}*.xlsx")) ?: [],
            glob(public_path("excel/*CAPAIAN*TARGET*{$year}*.xlsx")) ?: []
        );

        if (! empty($matches)) {
            return $matches[0];
        }

        if ($year === 2026) {
            $fallback2026 = array_merge(
                glob(public_path('*CAPAIAN*TARGET*.xlsx')) ?: [],
                glob(public_path('excel/*CAPAIAN*TARGET*.xlsx')) ?: []
            );
            if (! empty($fallback2026)) {
                return $fallback2026[0];
            }
        }

        if ($throwIfNotFound) {
            throw new \RuntimeException("File Excel Capaian Target PNBP Tahun {$year} tidak ditemukan di folder public/");
        }

        return null;
    }

    /**
     * Dapatkan cache key unik berbasis file path, tahun, dan modification time.
     */
    public function getCacheKey(?string $filePath = null, int $year = 2026): string
    {
        $path = $this->resolveExcelPath($filePath, $year, false);
        $mtime = ($path && file_exists($path)) ? filemtime($path) : 0;
        return 'pnbp_capaian_data_v4_' . $year . '_' . md5(($path ?: 'no_file') . '_' . $mtime);
    }

    /**
     * Bersihkan cache capaian untuk tahun tertentu.
     */
    public function clearCache(?string $filePath = null, int $year = 2026): void
    {
        $key = $this->getCacheKey($filePath, $year);
        Cache::forget($key);
        Cache::forget('pnbp_capaian_data_v3_' . $year);
    }

    /**
     * Daftar ringkasan default jika belum ada data/file excel.
     */
    public function getEmptyMonthlySummaries(int $year): array
    {
        $monthLabels = [
            'JANUARI' => "Januari {$year}",
            'FEB'     => "Februari {$year}",
            'MARET'   => "Maret {$year}",
            'APRIL'   => "April {$year}",
            'MEI'     => "Mei {$year}",
            'JUNI'    => "Juni {$year}",
            'JULI'    => "Juli {$year}",
            'AGUSTUS' => "Agustus {$year}",
            'SEPT'    => "September {$year}",
            'OKT'     => "Oktober {$year}",
            'NOV'     => "November {$year}",
            'DES'     => "Desember {$year}",
            'REKAP'   => "Rekap Tahunan {$year}",
        ];

        $summaries = [];
        foreach (self::SHEETS as $index => $sheetName) {
            $summaries[] = [
                'index'              => $index + 1,
                'sheet_name'         => $sheetName,
                'label'              => $monthLabels[$sheetName] ?? $sheetName,
                'volume_target'      => 0,
                'target_rp'          => 0,
                'volume_realization' => 0,
                'realization_rp'     => 0,
                'percentage'         => 0,
                'status'             => 'Belum Ada Data',
            ];
        }

        return $summaries;
    }

    /**
     * Parse / Load seluruh sheet capaian per tahun.
     * Menggunakan Database (TargetPnbpItem) sebagai single source of truth dengan sinkronisasi Excel.
     */
    public function getParsedDataCached(?string $filePath = null, int $year = 2026, bool $forceRefresh = false): array
    {
        $cacheKey = $this->getCacheKey($filePath, $year);

        if (! $forceRefresh) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && ! empty($cached['monthly_summaries'])) {
                return $cached;
            }
        }

        $hasDbItems = TargetPnbpItem::where('year', $year)->exists();

        // Jika DB sudah ada dan bukan force refresh, langsung bangun dari database
        if ($hasDbItems && ! $forceRefresh) {
            $payload = $this->buildDataFromDatabase($filePath, $year);
            Cache::put($cacheKey, $payload, now()->addDays(30));
            return $payload;
        }

        // Jika belum ada di DB atau force refresh: coba baca dari file Excel
        $path = $this->resolveExcelPath($filePath, $year, false);
        if ($path && file_exists($path)) {
            $payload = $this->importExcelIntoDatabase($path, $year);
            Cache::put($cacheKey, $payload, now()->addDays(30));
            return $payload;
        }

        // Jika tidak ada file Excel dan DB kosong: seed default template Ditlantas
        if (! $hasDbItems) {
            $this->seedDefaultTemplateForYear($year);
        }

        $payload = $this->buildDataFromDatabase($filePath, $year);
        Cache::put($cacheKey, $payload, now()->addDays(30));
        return $payload;
    }

    /**
     * Bangun data payload tampilan dari tabel database target_pnbp_items.
     */
    public function buildDataFromDatabase(?string $filePath = null, int $year = 2026): array
    {
        $path = $this->resolveExcelPath($filePath, $year, false);

        $monthLabels = [
            'JANUARI' => "Januari {$year}",
            'FEB'     => "Februari {$year}",
            'MARET'   => "Maret {$year}",
            'APRIL'   => "April {$year}",
            'MEI'     => "Mei {$year}",
            'JUNI'    => "Juni {$year}",
            'JULI'    => "Juli {$year}",
            'AGUSTUS' => "Agustus {$year}",
            'SEPT'    => "September {$year}",
            'OKT'     => "Oktober {$year}",
            'NOV'     => "November {$year}",
            'DES'     => "Desember {$year}",
            'REKAP'   => "Rekap Tahunan {$year}",
        ];

        $allDbItems = TargetPnbpItem::where('year', $year)
            ->orderBy('row_index')
            ->orderBy('created_at')
            ->get();

        $itemsBySheet = $allDbItems->groupBy('sheet_name');
        $monthlySummaries = [];
        $sheetDetailsMap  = [];

        foreach (self::SHEETS as $index => $sheetName) {
            $sheetItems = $itemsBySheet->get($sheetName, collect());
            $totalItem = $sheetItems->firstWhere('level', 0);

            $volTgt  = $totalItem ? (float) $totalItem->volume_target : (float) $sheetItems->where('level', 1)->sum('volume_target');
            $tgtRp   = $totalItem ? (float) $totalItem->target_rp : (float) $sheetItems->where('level', 1)->sum('target_rp');
            $volReal = $totalItem ? (float) $totalItem->volume_realization : (float) $sheetItems->where('level', 1)->sum('volume_realization');
            $realRp  = $totalItem ? (float) $totalItem->realization_rp : (float) $sheetItems->where('level', 1)->sum('realization_rp');
            $pct     = $tgtRp > 0 ? round(($realRp / $tgtRp) * 100, 2) : 0;
            $status  = ($volReal > 0 || $realRp > 0) ? 'Terealisasi' : 'Belum Ada Data';

            $monthlySummaries[] = [
                'index'              => $index + 1,
                'sheet_name'         => $sheetName,
                'label'              => $monthLabels[$sheetName] ?? $sheetName,
                'volume_target'      => $volTgt,
                'target_rp'          => $tgtRp,
                'volume_realization' => $volReal,
                'realization_rp'     => $realRp,
                'percentage'         => $pct,
                'status'             => $status,
            ];

            $itemsArray = [];
            foreach ($sheetItems as $it) {
                $itemsArray[] = [
                    'id'                 => $it->id,
                    'row'                => $it->row_index,
                    'no'                 => $it->no,
                    'name'               => $it->name,
                    'level'              => (int) $it->level,
                    'tarif'              => (float) $it->tarif,
                    'volume_target'      => (float) $it->volume_target,
                    'target_rp'          => (float) $it->target_rp,
                    'volume_realization' => (float) $it->volume_realization,
                    'realization_rp'     => (float) $it->realization_rp,
                    'percentage'         => (float) $it->percentage,
                ];
            }

            $sheetDetailsMap[$sheetName] = [
                'sheet_name' => $sheetName,
                'items'      => $itemsArray,
            ];
        }

        return [
            'file'              => $path ? basename($path) : "Database ARMASTER TA {$year}",
            'path'              => $path,
            'year'              => $year,
            'has_file'          => (bool) $path,
            'mtime'             => ($path && file_exists($path)) ? filemtime($path) : time(),
            'monthly_summaries' => $monthlySummaries,
            'sheet_details'     => $sheetDetailsMap,
        ];
    }

    /**
     * Import seluruh sheet dari file Excel ke dalam database target_pnbp_items.
     */
    public function importExcelIntoDatabase(string $path, int $year): array
    {
        $target = Target::firstOrCreate(
            ['year' => $year],
            [
                'name'        => "Target Ditlantas {$year}",
                'year'        => $year,
                'description' => "Target Renbut Materiel dan Capaian Realisasi PNBP Ditlantas & Polres Jajaran TA {$year} (Tersambung ke " . basename($path) . ")",
                'is_active'   => true,
            ]
        );

        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        DB::transaction(function () use ($spreadsheet, $target, $year) {
            TargetPnbpItem::where('target_id', $target->id)->forceDelete();

            foreach (self::SHEETS as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                if (! $sheet) {
                    continue;
                }

                foreach (self::CATEGORY_ROWS as $rowNum => $meta) {
                    $rowVolTgt  = (float) $this->getNumericCellValue($sheet, "I{$rowNum}");
                    $tarif      = (float) $this->getNumericCellValue($sheet, "J{$rowNum}");
                    $rowTgtRp   = (float) $this->getNumericCellValue($sheet, "K{$rowNum}");
                    $rowVolReal = (float) $this->getNumericCellValue($sheet, "L{$rowNum}");
                    $rowRealRp  = (float) $this->getNumericCellValue($sheet, "N{$rowNum}");
                    $rowPct     = $rowTgtRp > 0 ? round(($rowRealRp / $rowTgtRp) * 100, 2) : 0;

                    if ($tarif == 0) {
                        $tarifReal = (float) $this->getNumericCellValue($sheet, "M{$rowNum}");
                        if ($tarifReal > 0) {
                            $tarif = $tarifReal;
                        }
                    }

                    TargetPnbpItem::create([
                        'target_id'          => $target->id,
                        'year'               => $year,
                        'sheet_name'         => $sheetName,
                        'row_index'          => $rowNum,
                        'no'                 => $meta['no'],
                        'name'               => $meta['name'],
                        'level'              => $meta['level'],
                        'tarif'              => $tarif,
                        'volume_target'      => $rowVolTgt,
                        'target_rp'          => $rowTgtRp,
                        'volume_realization' => $rowVolReal,
                        'realization_rp'     => $rowRealRp,
                        'percentage'         => $rowPct,
                    ]);
                }
            }
        });

        return $this->buildDataFromDatabase($path, $year);
    }

    /**
     * Seed struktur template Ditlantas default jika tahun belum memiliki file Excel.
     */
    public function seedDefaultTemplateForYear(int $year): void
    {
        $target = Target::firstOrCreate(
            ['year' => $year],
            [
                'name'        => "Target Ditlantas {$year}",
                'year'        => $year,
                'description' => "Target Renbut Materiel dan Capaian Realisasi PNBP Ditlantas & Polres Jajaran TA {$year}",
                'is_active'   => true,
            ]
        );

        DB::transaction(function () use ($target, $year) {
            foreach (self::SHEETS as $sheetName) {
                foreach (self::CATEGORY_ROWS as $rowNum => $meta) {
                    TargetPnbpItem::create([
                        'target_id'          => $target->id,
                        'year'               => $year,
                        'sheet_name'         => $sheetName,
                        'row_index'          => $rowNum,
                        'no'                 => $meta['no'],
                        'name'               => $meta['name'],
                        'level'              => $meta['level'],
                        'tarif'              => 0,
                        'volume_target'      => 0,
                        'target_rp'          => 0,
                        'volume_realization' => 0,
                        'realization_rp'     => 0,
                        'percentage'         => 0,
                    ]);
                }
            }
        });
    }

    /**
     * Tambah item materiil penerimaan PNBP baru.
     */
    public function createItem(array $data, int $year, string $sheetName = 'JANUARI', bool $applyToAllMonths = false): TargetPnbpItem
    {
        $target = Target::firstOrCreate(
            ['year' => $year],
            [
                'name'        => "Target Ditlantas {$year}",
                'year'        => $year,
                'description' => "Target Renbut Materiel dan Capaian Realisasi PNBP Ditlantas & Polres Jajaran TA {$year}",
                'is_active'   => true,
            ]
        );

        $sheets = $applyToAllMonths ? self::SHEETS : [$sheetName];
        $createdItem = null;

        DB::transaction(function () use ($sheets, $data, $target, $year, &$createdItem) {
            foreach ($sheets as $sName) {
                $maxRow = (int) TargetPnbpItem::where('year', $year)->where('sheet_name', $sName)->max('row_index');
                $rowIndex = $maxRow > 0 ? $maxRow + 1 : 100;

                $tarif   = (float) ($data['tarif'] ?? 0);
                $volTgt  = (float) ($data['volume_target'] ?? 0);
                $volReal = (float) ($data['volume_realization'] ?? 0);
                $level   = (int) ($data['level'] ?? 2);

                if ($level > 1) {
                    $tgtRp  = $volTgt * $tarif;
                    $realRp = $volReal * $tarif;
                    $pct    = $tgtRp > 0 ? round(($realRp / $tgtRp) * 100, 2) : 0;
                } else {
                    $tgtRp  = (float) ($data['target_rp'] ?? 0);
                    $realRp = (float) ($data['realization_rp'] ?? 0);
                    $pct    = $tgtRp > 0 ? round(($realRp / $tgtRp) * 100, 2) : 0;
                }

                $item = TargetPnbpItem::create([
                    'target_id'          => $target->id,
                    'year'               => $year,
                    'sheet_name'         => $sName,
                    'row_index'          => $rowIndex,
                    'no'                 => $data['no'] ?? null,
                    'name'               => $data['name'] ?? '',
                    'level'              => $level,
                    'tarif'              => $tarif,
                    'volume_target'      => $volTgt,
                    'target_rp'          => $tgtRp,
                    'volume_realization' => $volReal,
                    'realization_rp'     => $realRp,
                    'percentage'         => $pct,
                ]);

                if (! $createdItem) {
                    $createdItem = $item;
                }

                $this->recalculateSheetTotals($year, $sName);
            }
        });

        $this->clearCache(null, $year);

        return $createdItem;
    }

    /**
     * Update item materiil penerimaan PNBP.
     */
    public function updateItem(string $id, array $data): TargetPnbpItem
    {
        $item = TargetPnbpItem::findOrFail($id);

        $tarif   = isset($data['tarif']) ? (float) $data['tarif'] : (float) $item->tarif;
        $volTgt  = isset($data['volume_target']) ? (float) $data['volume_target'] : (float) $item->volume_target;
        $volReal = isset($data['volume_realization']) ? (float) $data['volume_realization'] : (float) $item->volume_realization;
        $level   = isset($data['level']) ? (int) $data['level'] : (int) $item->level;

        if ($level > 1 || $tarif > 0) {
            $tgtRp  = $volTgt * $tarif;
            $realRp = $volReal * $tarif;
            $pct    = $tgtRp > 0 ? round(($realRp / $tgtRp) * 100, 2) : 0;
        } else {
            $tgtRp  = (float) ($data['target_rp'] ?? $item->target_rp);
            $realRp = (float) ($data['realization_rp'] ?? $item->realization_rp);
            $pct    = $tgtRp > 0 ? round(($realRp / $tgtRp) * 100, 2) : 0;
        }

        $item->update([
            'no'                 => $data['no'] ?? $item->no,
            'name'               => $data['name'] ?? $item->name,
            'level'              => $level,
            'tarif'              => $tarif,
            'volume_target'      => $volTgt,
            'target_rp'          => $tgtRp,
            'volume_realization' => $volReal,
            'realization_rp'     => $realRp,
            'percentage'         => $pct,
        ]);

        $this->recalculateSheetTotals($item->year, $item->sheet_name);
        $this->clearCache(null, $item->year);

        return $item;
    }

    /**
     * Hapus item materiil penerimaan PNBP.
     */
    public function deleteItem(string $id): void
    {
        $item = TargetPnbpItem::findOrFail($id);
        $year = $item->year;
        $sheet = $item->sheet_name;

        $item->delete();

        $this->recalculateSheetTotals($year, $sheet);
        $this->clearCache(null, $year);
    }

    /**
     * Hitung ulang total header grup dan row total untuk sheet tertentu.
     */
    public function recalculateSheetTotals(int $year, string $sheetName): void
    {
        $items = TargetPnbpItem::where('year', $year)
            ->where('sheet_name', $sheetName)
            ->orderBy('row_index')
            ->orderBy('created_at')
            ->get();

        if ($items->isEmpty()) {
            return;
        }

        // Update Level 2 sub-headers dari Level 3 children
        $level2Parents = [
            38 => [39, 40],
            41 => [42, 43],
            58 => [59, 60],
            61 => [62, 63],
            86 => [87, 88],
            89 => [90, 91],
            92 => [93, 94],
            95 => [96, 97],
        ];

        foreach ($level2Parents as $pRow => $cRows) {
            $parentItem = $items->firstWhere('row_index', $pRow);
            if ($parentItem) {
                $children = $items->whereIn('row_index', $cRows);
                $volReal = $children->sum('volume_realization');
                $realRp  = $children->sum('realization_rp');
                $volTgt  = $children->sum('volume_target');
                $tgtRp   = $children->sum('target_rp');
                $pct     = $tgtRp > 0 ? round(($realRp / $tgtRp) * 100, 2) : 0;

                $parentItem->update([
                    'volume_target'      => $volTgt,
                    'target_rp'          => $tgtRp,
                    'volume_realization' => $volReal,
                    'realization_rp'     => $realRp,
                    'percentage'         => $pct,
                ]);
            }
        }

        // Refresh items setelah Level 2 di-update
        $items = TargetPnbpItem::where('year', $year)
            ->where('sheet_name', $sheetName)
            ->orderBy('row_index')
            ->orderBy('created_at')
            ->get();

        $currentHeader = null;
        $headerChildren = [];

        foreach ($items as $item) {
            if ($item->level === 1) {
                if ($currentHeader) {
                    $this->updateHeaderFromChildren($currentHeader, $headerChildren);
                }
                $currentHeader = $item;
                $headerChildren = [];
            } elseif ($item->level > 1 && $currentHeader) {
                $headerChildren[] = $item;
            }
        }
        if ($currentHeader) {
            $this->updateHeaderFromChildren($currentHeader, $headerChildren);
        }

        // Hitung ulang Baris TOTAL (Level 0)
        $items = TargetPnbpItem::where('year', $year)
            ->where('sheet_name', $sheetName)
            ->get();

        $headers = $items->where('level', 1);
        $totalRow = $items->firstWhere('level', 0);

        $totalVolTgt  = $headers->sum('volume_target');
        $totalTgtRp   = $headers->sum('target_rp');
        $totalVolReal = $headers->sum('volume_realization');
        $totalRealRp  = $headers->sum('realization_rp');
        $totalPct     = $totalTgtRp > 0 ? round(($totalRealRp / $totalTgtRp) * 100, 2) : 0;

        if ($totalRow) {
            $totalRow->update([
                'volume_target'      => $totalVolTgt,
                'target_rp'          => $totalTgtRp,
                'volume_realization' => $totalVolReal,
                'realization_rp'     => $totalRealRp,
                'percentage'         => $totalPct,
            ]);
        }

        // Sinkronisasi akumulasi REKAP jika perubahan terjadi di sheet bulanan
        if ($sheetName !== 'REKAP') {
            $this->recalculateRekapSheet($year);
        }

        $this->clearCache(null, $year);
    }

    /**
     * Update Level 1 header dari kumpulan leaf children-nya.
     */
    protected function updateHeaderFromChildren(TargetPnbpItem $header, array $children): void
    {
        if (empty($children)) {
            return;
        }

        $hasLevel3 = collect($children)->contains('level', 3);
        if ($hasLevel3) {
            $billable = collect($children)->filter(function ($c) {
                return $c->level === 3 || ($c->level === 2 && (float)$c->tarif > 0);
            });
        } else {
            $billable = collect($children);
        }

        $volTgt  = $billable->sum('volume_target');
        $tgtRp   = $billable->sum('target_rp');
        $volReal = $billable->sum('volume_realization');
        $realRp  = $billable->sum('realization_rp');
        $pct     = $tgtRp > 0 ? round(($realRp / $tgtRp) * 100, 2) : 0;

        $header->update([
            'volume_target'      => $volTgt,
            'target_rp'          => $tgtRp,
            'volume_realization' => $volReal,
            'realization_rp'     => $realRp,
            'percentage'         => $pct,
        ]);
    }

    /**
     * Hitung ulang akumulasi sheet REKAP dari sheet JANUARI s/d DESEMBER.
     */
    public function recalculateRekapSheet(int $year): void
    {
        $months = array_diff(self::SHEETS, ['REKAP']);
        $rekapItems = TargetPnbpItem::where('year', $year)
            ->where('sheet_name', 'REKAP')
            ->get();

        if ($rekapItems->isEmpty()) {
            return;
        }

        $monthItems = TargetPnbpItem::where('year', $year)
            ->whereIn('sheet_name', $months)
            ->get();

        $level2Parents = [
            38 => [39, 40],
            41 => [42, 43],
            58 => [59, 60],
            61 => [62, 63],
            86 => [87, 88],
            89 => [90, 91],
            92 => [93, 94],
            95 => [96, 97],
        ];

        foreach ($rekapItems as $rItem) {
            if ($rItem->level === 0 || array_key_exists($rItem->row_index, $level2Parents)) {
                continue;
            }

            $matchingMonths = $monthItems->where('row_index', $rItem->row_index);
            if ($matchingMonths->isNotEmpty()) {
                $totalRealVol = $matchingMonths->sum('volume_realization');
                $totalRealRp  = $matchingMonths->sum('realization_rp');
                $tgtRp = (float) $rItem->target_rp;
                $pct = $tgtRp > 0 ? round(($totalRealRp / $tgtRp) * 100, 2) : 0;

                $rItem->update([
                    'volume_realization' => $totalRealVol,
                    'realization_rp'     => $totalRealRp,
                    'percentage'         => $pct,
                ]);
            }
        }

        // Update Level 2 sub-headers di REKAP dari Level 3
        $rekapItems = TargetPnbpItem::where('year', $year)
            ->where('sheet_name', 'REKAP')
            ->get();

        foreach ($level2Parents as $pRow => $cRows) {
            $parentItem = $rekapItems->firstWhere('row_index', $pRow);
            if ($parentItem) {
                $children = $rekapItems->whereIn('row_index', $cRows);
                $volReal = $children->sum('volume_realization');
                $realRp  = $children->sum('realization_rp');
                $tgtRp   = (float) $parentItem->target_rp;
                $pct     = $tgtRp > 0 ? round(($realRp / $tgtRp) * 100, 2) : 0;

                $parentItem->update([
                    'volume_realization' => $volReal,
                    'realization_rp'     => $realRp,
                    'percentage'         => $pct,
                ]);
            }
        }

        // Update headers dan TOTAL sheet REKAP
        $items = TargetPnbpItem::where('year', $year)
            ->where('sheet_name', 'REKAP')
            ->orderBy('row_index')
            ->orderBy('created_at')
            ->get();

        $currentHeader = null;
        $headerChildren = [];
        foreach ($items as $item) {
            if ($item->level === 1) {
                if ($currentHeader) {
                    $this->updateHeaderFromChildren($currentHeader, $headerChildren);
                }
                $currentHeader = $item;
                $headerChildren = [];
            } elseif ($item->level > 1 && $currentHeader) {
                $headerChildren[] = $item;
            }
        }
        if ($currentHeader) {
            $this->updateHeaderFromChildren($currentHeader, $headerChildren);
        }

        $items = TargetPnbpItem::where('year', $year)->where('sheet_name', 'REKAP')->get();
        $headers = $items->where('level', 1);
        $totalRow = $items->firstWhere('level', 0);
        if ($totalRow) {
            $totalVolTgt  = $headers->sum('volume_target');
            $totalTgtRp   = $headers->sum('target_rp');
            $totalVolReal = $headers->sum('volume_realization');
            $totalRealRp  = $headers->sum('realization_rp');
            $totalPct     = $totalTgtRp > 0 ? round(($totalRealRp / $totalTgtRp) * 100, 2) : 0;

            $totalRow->update([
                'volume_target'      => $totalVolTgt,
                'target_rp'          => $totalTgtRp,
                'volume_realization' => $totalVolReal,
                'realization_rp'     => $totalRealRp,
                'percentage'         => $totalPct,
            ]);
        }
    }

    /**
     * Resolusi indeks baris TargetPnbpItem berdasarkan hierarki type, service, dan detail material usage.
     */
    public function resolveRowIndexFromUsageItem(object $it): ?int
    {
        $typeName = strtoupper((string) ($it->type_name ?? ''));
        $serviceName = strtoupper((string) ($it->service_name ?? ''));
        $subName = strtoupper((string) ($it->service_detail_name ?? ''));
        $all = $typeName . ' ' . $serviceName . ' ' . $subName;

        // 1. SIM Baru
        if (str_contains($typeName, 'SIM') && (str_contains($all, 'BARU') || str_contains($serviceName, 'BARU'))) {
            if (str_contains($all, 'SIM A')) return 14;
            if (str_contains($all, 'SIM B II') || str_contains($all, 'SIM B2')) return 16;
            if (str_contains($all, 'SIM B I') || str_contains($all, 'SIM B1') || str_contains($all, 'SIM B')) return 15;
            if (str_contains($all, 'SIM C II') || str_contains($all, 'SIM C2')) return 19;
            if (str_contains($all, 'SIM C I') || str_contains($all, 'SIM C1')) return 18;
            if (str_contains($all, 'SIM C')) return 17;
            if (str_contains($all, 'SIM D I') || str_contains($all, 'SIM D1')) return 21;
            if (str_contains($all, 'SIM D')) return 20;
            if (str_contains($all, 'INTERNA')) return 22;
            return 17;
        }

        // 2. SIM Perpanjangan / Perubahan / Default SIM
        if (str_contains($typeName, 'SIM')) {
            if (str_contains($all, 'SIM A')) return 25;
            if (str_contains($all, 'SIM B II') || str_contains($all, 'SIM B2')) return 27;
            if (str_contains($all, 'SIM B I') || str_contains($all, 'SIM B1') || str_contains($all, 'SIM B')) return 26;
            if (str_contains($all, 'SIM C II') || str_contains($all, 'SIM C2')) return 30;
            if (str_contains($all, 'SIM C I') || str_contains($all, 'SIM C1')) return 29;
            if (str_contains($all, 'SIM C')) return 28;
            if (str_contains($all, 'SIM D I') || str_contains($all, 'SIM D1')) return 32;
            if (str_contains($all, 'SIM D')) return 31;
            if (str_contains($all, 'INTERNA')) return 33;
            return 28;
        }

        // 3. SKUKP
        if (str_contains($typeName, 'SKUKP') || str_contains($all, 'SKUKP')) {
            return 35;
        }

        // 4. STNK
        if (str_contains($typeName, 'STNK') && !str_contains($typeName, 'STNK-LBN')) {
            $isR4 = str_contains($all, 'RODA 4') || str_contains($all, 'R4') || str_contains($all, 'LEBIH');
            $isBaru = str_contains($all, 'BARU');
            if ($isR4) return $isBaru ? 42 : 43;
            return $isBaru ? 39 : 40;
        }

        // 5. TNKB
        if (str_contains($typeName, 'TNKB') && !str_contains($typeName, 'TNKB-LBN')) {
            $isR4 = str_contains($typeName, 'R4') || str_contains($all, 'RODA 4') || str_contains($typeName, 'PILIHAN');
            return $isR4 ? 47 : 46;
        }

        // 6. STCK
        if (str_contains($typeName, 'STCK') || str_contains($serviceName, 'STCK')) {
            $isR4 = str_contains($all, 'RODA 4') || str_contains($all, 'R4');
            return $isR4 ? 51 : 50;
        }

        // 7. TCKB
        if (str_contains($typeName, 'TCKB') || str_contains($serviceName, 'TCKB')) {
            $isR4 = str_contains($all, 'RODA 4') || str_contains($all, 'R4');
            return $isR4 ? 55 : 54;
        }

        // 8. BPKB
        if (str_contains($typeName, 'BPKB') || str_contains($serviceName, 'BPKB')) {
            $isR4 = str_contains($all, 'RODA 4') || str_contains($all, 'R4') || str_contains($typeName, 'E-BPKB');
            $isGanti = str_contains($all, 'GANTI');
            if ($isR4) return $isGanti ? 63 : 62;
            return $isGanti ? 60 : 59;
        }

        // 9. MUTASI
        if (str_contains($typeName, 'MUTASI') || str_contains($serviceName, 'MUTASI')) {
            $isR4 = str_contains($all, 'RODA 4') || str_contains($all, 'R4');
            return $isR4 ? 67 : 66;
        }

        // 10. STNK-LBN
        if (str_contains($typeName, 'STNK-LBN')) return 69;

        // 11. TNKB-LBN
        if (str_contains($typeName, 'TNKB-LBN')) return 77;

        // 12. NRKB NOPIL (Kategori 12 diisi manual pada tabel capaian PNBP karena tidak ada matreg fisik)
        return null;
    }

    /**
     * Sinkronisasi capaian realisasi bulan tertentu dari inputan transaksi material_usages ARMASTER.
     */
    public function syncMonthlyRealizationFromUsage(int $year = 2026, int $month = 10): array
    {
        $monthSheetMap = [
            1 => 'JANUARI', 2 => 'FEB', 3 => 'MARET', 4 => 'APRIL',
            5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
            9 => 'SEPT', 10 => 'OKT', 11 => 'NOV', 12 => 'DES',
        ];

        $sheetName = $monthSheetMap[$month] ?? null;
        if (! $sheetName) {
            return ['status' => 'error', 'message' => "Bulan {$month} tidak valid."];
        }

        $items = DB::table('material_usage_detail_items as mudi')
            ->join('material_usages as mu', 'mudi.material_usage_id', '=', 'mu.id')
            ->leftJoin('types as t', 'mudi.type_id', '=', 't.id')
            ->leftJoin('type_details as td', 'mudi.type_detail_id', '=', 'td.id')
            ->leftJoin('services as s', 'mudi.service_id', '=', 's.id')
            ->leftJoin('service_details as sd', 'mudi.service_detail_id', '=', 'sd.id')
            ->whereYear('mu.date', $year)
            ->whereMonth('mu.date', $month)
            ->whereNull('mu.deleted_at')
            ->where('mudi.quantity', '>', 0)
            ->selectRaw('
                t.name as type_name,
                td.name as type_detail_name,
                s.name as service_name,
                sd.name as service_detail_name,
                COALESCE(sd.price, s.price, td.price, t.price, 0) as price,
                SUM(mudi.quantity) as total_qty,
                SUM(mudi.quantity * COALESCE(sd.price, s.price, td.price, t.price, 0)) as total_rp
            ')
            ->groupBy('t.name', 'td.name', 's.name', 'sd.name', 'sd.price', 's.price', 'td.price', 't.price')
            ->get();

        $rowTotals = [];
        foreach ($items as $it) {
            $row = $this->resolveRowIndexFromUsageItem($it);
            if ($row) {
                if (! isset($rowTotals[$row])) {
                    $rowTotals[$row] = ['qty' => 0.0, 'rp' => 0.0];
                }
                $rowTotals[$row]['qty'] += (float) $it->total_qty;
                $rowTotals[$row]['rp']  += (float) $it->total_rp;
            }
        }

        $sheetItems = TargetPnbpItem::where('year', $year)
            ->where('sheet_name', $sheetName)
            ->get();

        if ($sheetItems->isEmpty()) {
            return ['status' => 'error', 'message' => "Sheet {$sheetName} belum ada di TargetPnbpItem TA {$year}."];
        }

        $level2Parents = [
            38 => [39, 40],
            41 => [42, 43],
            58 => [59, 60],
            61 => [62, 63],
            86 => [87, 88],
            89 => [90, 91],
            92 => [93, 94],
            95 => [96, 97],
        ];

        foreach ($sheetItems as $sItem) {
            // Lewati baris TOTAL (level 0)
            if ($sItem->level === 0) {
                continue;
            }

            // Lewati Group Header (level 1) yang memiliki sub-kategori,
            // tetapi JANGAN lewati baris level 1 tunggal tanpa children (seperti Row 35 SKUKP, Row 69 STNK-LBN, Row 77 TNKB-LBN)
            if ($sItem->level === 1 && ! in_array($sItem->row_index, [35, 69, 77], true)) {
                continue;
            }

            // Lewati sub-header level 2 yang merupakan parent dari level 3 (dihitung otomatis dari children)
            if (array_key_exists($sItem->row_index, $level2Parents)) {
                continue;
            }

            // JANGAN TIMPA BARIS NOPIL (rows 85-97)
            // Kategori 12 (Penerbitan NRKB Pilihan) adalah tabel manual yang diinput oleh user karena tidak ada matreg fisik.
            // Nilai realisasi manual Nopil tidak boleh di-reset kembali ke 0 saat sinkronisasi material usage.
            if ($sItem->row_index >= 85 && $sItem->row_index <= 97) {
                continue;
            }

            $rIdx = $sItem->row_index;
            $volReal = isset($rowTotals[$rIdx]) ? $rowTotals[$rIdx]['qty'] : 0.0;
            $realRp  = isset($rowTotals[$rIdx]) ? $rowTotals[$rIdx]['rp'] : 0.0;
            $tgtRp   = (float) $sItem->target_rp;
            $pct     = $tgtRp > 0 ? round(($realRp / $tgtRp) * 100, 2) : 0.0;

            $sItem->update([
                'volume_realization' => $volReal,
                'realization_rp'     => $realRp,
                'percentage'         => $pct,
            ]);
        }

        // Recalculate sheet totals (mengupdate Level 2, Level 1 headers, Level 0, dan REKAP)
        $this->recalculateSheetTotals($year, $sheetName);
        $this->clearCache(null, $year);

        return [
            'status'       => 'success',
            'year'         => $year,
            'month'        => $month,
            'sheet_name'   => $sheetName,
            'total_volume' => array_sum(array_column($rowTotals, 'qty')),
            'total_rp'     => array_sum(array_column($rowTotals, 'rp')),
        ];
    }

    /**
     * Auto-sync semua bulan berjalan (Oktober s/d Desember) yang memiliki transaksi material usage.
     */
    public function autoSyncActiveUsageMonths(int $year = 2026, bool $force = false): void
    {
        $cacheKey = "pnbp_last_synced_mu_ts_{$year}";
        $latestUsageUpdate = DB::table('material_usages')
            ->whereYear('date', $year)
            ->whereNull('deleted_at')
            ->max('updated_at');

        $lastSynced = Cache::get($cacheKey);

        if ($force || ! $lastSynced || $lastSynced !== $latestUsageUpdate) {
            $months = DB::table('material_usages')
                ->whereYear('date', $year)
                ->whereNull('deleted_at')
                ->selectRaw('DISTINCT EXTRACT(MONTH FROM date) as m')
                ->pluck('m')
                ->map(fn($m) => (int)$m)
                ->filter(fn($m) => $m >= 10);

            foreach ($months as $month) {
                $this->syncMonthlyRealizationFromUsage($year, $month);
            }

            if ($latestUsageUpdate) {
                Cache::put($cacheKey, $latestUsageUpdate, 3600);
            }
        }
    }

    /**
     * Dapatkan ringkasan capaian per sheet bulanan dari Cache per tahun.
     */
    public function getMonthlySummary(?string $filePath = null, int $year = 2026): array
    {
        return $this->getParsedDataCached($filePath, $year)['monthly_summaries'] ?? [];
    }

    /**
     * Dapatkan rincian baris per jenis penerimaan untuk sheet tertentu per tahun.
     */
    public function getSheetDetails(string $sheetName, ?string $filePath = null, int $year = 2026): array
    {
        $normalizedName = strtoupper(trim($sheetName));

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
        $data = $this->getParsedDataCached($filePath, $year);

        if (isset($data['sheet_details'][$targetSheetName])) {
            return $data['sheet_details'][$targetSheetName];
        }

        return [
            'sheet_name' => $targetSheetName,
            'items'      => [],
        ];
    }

    /**
     * Hubungkan dan sinkronisasi dengan tabel Target di database per tahun.
     */
    public function syncTargetWithDatabase(?string $filePath = null, int $year = 2026, bool $forceRefresh = false): array
    {
        $path = $this->resolveExcelPath($filePath, $year, false);

        $target = Target::firstOrCreate(
            ['year' => $year],
            [
                'name'        => "Target Ditlantas {$year}",
                'year'        => $year,
                'description' => "Target Renbut Materiel dan Capaian Realisasi PNBP Ditlantas & Polres Jajaran TA {$year}",
                'is_active'   => true,
            ]
        );

        if ($path && ($forceRefresh || ! str_contains((string) $target->description, basename($path)))) {
            $target->update([
                'is_active'   => true,
                'description' => "Target Renbut Materiel dan Capaian Realisasi PNBP Ditlantas & Polres Jajaran TA {$year} (Tersambung ke " . basename($path) . ")",
            ]);
        }

        $dbDetailsStats = Cache::remember("target_details_stats_{$target->id}", 3600, function () use ($target) {
            return [
                'total_qty' => (float) TargetDetail::where('target_id', $target->id)->sum('quantity'),
                'count'     => TargetDetail::where('target_id', $target->id)->count(),
            ];
        });
        $dbTotalQty = $dbDetailsStats['total_qty'];
        $dbDetailsCount = $dbDetailsStats['count'];

        // Auto-sync capaian bulanan dari inputan material usage di ARMASTER (Oktober dst)
        $this->autoSyncActiveUsageMonths($year, $forceRefresh);

        // Ambil data rekap dari database/cache
        $rekap = $this->getSheetDetails('REKAP', $path, $year);
        $totalRow = collect($rekap['items'] ?? [])->firstWhere('level', 0) ?: collect($rekap['items'] ?? [])->firstWhere('row', 99);

        $excelTargetQty = $totalRow['volume_target'] ?? 0;
        $excelTargetRp  = $totalRow['target_rp'] ?? 0;
        $excelRealQty   = $totalRow['volume_realization'] ?? 0;
        $excelRealRp    = $totalRow['realization_rp'] ?? 0;
        $excelPct       = $totalRow['percentage'] ?? 0;

        $isQuantityAligned = $excelTargetQty > 0 ? (abs($dbTotalQty - $excelTargetQty) < 1) : false;

        return [
            'year'               => $year,
            'target_model'       => $target,
            'has_excel_file'     => ! empty($path),
            'excel_file'         => $path ? basename($path) : "Database ARMASTER TA {$year}",
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
            $cleaned = preg_replace('/[^-0-9.]/', '', str_replace(',', '', $val));
            if (is_numeric($cleaned)) {
                return (float) $cleaned;
            }
        }

        return 0.0;
    }
}
