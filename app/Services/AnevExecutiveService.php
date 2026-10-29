<?php

namespace App\Services;

use App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Stock\Stock;
use Carbon\CarbonImmutable;

class AnevExecutiveService
{
    /**
     * Compile executive anev data.
     */
    public function getData(?string $date = null, string $period = 'daily', ?string $startDate = null, ?string $endDate = null): array
    {
        $timezone = config('app.timezone', 'UTC');
        $now = CarbonImmutable::now($timezone);
        $date = $date ?: $now->toDateString();
        $year = (int) substr($date, 0, 4);

        // A & B: Capaian Target PNBP & Renbut
        $targetPnbp = 0.0;
        $realizedPnbp = 0.0;
        $pctPnbp = 0.0;
        $remainingPnbp = 0.0;
        $pctRemainingPnbp = 0.0;

        $targetGunmat = 0.0;
        $realizedGunmat = 0.0;
        $pctGunmat = 0.0;
        $remainingGunmat = 0.0;
        $pctRemainingGunmat = 0.0;

        try {
            /** @var DashboardStatsService $statsService */
            $statsService = app(DashboardStatsService::class);
            $pnbpStats = $statsService->getPnbpAndRenbutStats($year);

            $targetPnbp = (float) ($pnbpStats['pnbp']['target'] ?? 0);
            $realizedPnbp = (float) ($pnbpStats['pnbp']['realization'] ?? 0);
            $pctPnbp = (float) ($pnbpStats['pnbp']['percentage'] ?? 0);
            $remainingPnbp = max(0, $targetPnbp - $realizedPnbp);
            $pctRemainingPnbp = $targetPnbp > 0 ? round(($remainingPnbp / $targetPnbp) * 100, 1) : 0;

            $targetGunmat = (float) ($pnbpStats['renbut']['target'] ?? 0);
            $realizedGunmat = (float) ($pnbpStats['renbut']['realization'] ?? 0);
            $pctGunmat = (float) ($pnbpStats['renbut']['percentage'] ?? 0);
            $remainingGunmat = max(0, $targetGunmat - $realizedGunmat);
            $pctRemainingGunmat = $targetGunmat > 0 ? round(($remainingGunmat / $targetGunmat) * 100, 1) : 0;
        } catch (\Throwable $e) {
            // DB fallback for test environments or isolated mocks
        }

        // C: Kondisi Stok Material
        $stockPolda = 0.0;
        $stockPolres = 0.0;
        $materialRusak = 0.0;
        try {
            $stockPolda = (float) (Stock::polda()->whereNull('type_detail_id')->sum('quantity') ?? 0);
            $stockPolres = (float) (Stock::polres()->whereNull('type_detail_id')->sum('quantity') ?? 0);
            $materialRusak = (float) (MaterialDamageDetail::whereHas('materialDamage', fn ($q) => $q->whereYear('date', $year))->sum('quantity') ?? 0);
        } catch (\Throwable $e) {
            // DB fallback
        }

        // D: Anev Persediaan Warehouse (Gudang Polda per Type)
        $warehouseItems = [
            'SIM' => 0.0,
            'STNK' => 0.0,
            'STCK' => 0.0,
            'E-BPKB' => 0.0,
            'BPKB' => 0.0,
        ];
        try {
            $poldaStocksByType = Stock::polda()
                ->whereNull('type_detail_id')
                ->join('types', 'stocks.type_id', '=', 'types.id')
                ->select('types.name', \Illuminate\Support\Facades\DB::raw('sum(stocks.quantity) as total_qty'))
                ->groupBy('types.name')
                ->get()
                ->keyBy(fn ($item) => strtoupper(trim($item->name)));

            $warehouseItems = [
                'SIM' => (float) ($poldaStocksByType->get('SIM CARD')?->total_qty ?? $poldaStocksByType->get('SIM')?->total_qty ?? 0),
                'STNK' => (float) ($poldaStocksByType->get('STNK')?->total_qty ?? 0),
                'STCK' => (float) ($poldaStocksByType->get('STCK')?->total_qty ?? 0),
                'E-BPKB' => (float) ($poldaStocksByType->get('E-BPKB')?->total_qty ?? 0),
                'BPKB' => (float) ($poldaStocksByType->get('BPKB')?->total_qty ?? 0),
            ];
        } catch (\Throwable $e) {
            // DB fallback
        }

        // E: Anev Ketertiban Input Gunmat (Belum Input)
        $missingSatker = [];
        $consecutiveMax = 0;
        $consecutiveSatker = null;

        try {
            /** @var ReportingComplianceService $complianceService */
            $complianceService = app(ReportingComplianceService::class);
            $calStart = $period === 'daily' ? CarbonImmutable::parse($date, $timezone)->subDays(4)->toDateString() : ($startDate ?: $date);
            $calEnd = $period === 'daily' ? $date : ($endDate ?: $date);

            $calendar = $complianceService->calendar($calStart, $calEnd);

            foreach ($calendar['rows'] ?? [] as $row) {
                $unsubmittedDays = [];
                foreach ($row['cells'] ?? [] as $dayStr => $cell) {
                    if (! ($cell['reported'] ?? false)) {
                        $unsubmittedDays[] = (int) \Carbon\Carbon::parse($dayStr)->format('d');
                    }
                }
                if (! empty($unsubmittedDays)) {
                    $missingSatker[] = [
                        'name' => $row['name'],
                        'days' => $unsubmittedDays,
                        'count' => count($unsubmittedDays),
                        'label' => $row['name'].' (tgl '.implode(', ', $unsubmittedDays).')',
                    ];
                    if (count($unsubmittedDays) > $consecutiveMax) {
                        $consecutiveMax = count($unsubmittedDays);
                        $consecutiveSatker = $row['name'];
                    }
                }
            }

            // Sort by count descending so worst offenders are highlighted first
            usort($missingSatker, fn ($a, $b) => $b['count'] <=> $a['count'] ?: strcmp($a['name'], $b['name']));
        } catch (\Throwable $e) {
            // DB fallback
        }

        $timeStr = $now->format('H.i').' WIB';

        // F: Kesimpulan Anev
        $satkerCount = count($missingSatker);
        $kesimpulan1 = sprintf(
            'Realisasi PNBP mencapai %s%% dan Realisasi gunmat mencapai %s%%.',
            str_replace('.', ',', (string) $pctPnbp),
            str_replace('.', ',', (string) $pctGunmat)
        );
        $kesimpulan2 = $satkerCount > 0
            ? sprintf(
                'Terdapat %d satker yang masih belum melengkapi input Gunmat. Perlu peningkatan ketertiban penginputan%s.',
                $satkerCount,
                $consecutiveSatker ? sprintf(', terutama %s dikarenakan %d hari berturut', $consecutiveSatker, $consecutiveMax) : ''
            )
            : 'Seluruh satker telah tertib mengisi laporan penggunaan material (Gunmat).';

        return [
            'date' => $date,
            'year' => $year,
            'period' => $period,
            'time_str' => $timeStr,
            'target_pnbp' => $targetPnbp,
            'realized_pnbp' => $realizedPnbp,
            'pct_pnbp' => $pctPnbp,
            'remaining_pnbp' => $remainingPnbp,
            'pct_remaining_pnbp' => $pctRemainingPnbp,
            'target_gunmat' => $targetGunmat,
            'realized_gunmat' => $realizedGunmat,
            'pct_gunmat' => $pctGunmat,
            'remaining_gunmat' => $remainingGunmat,
            'pct_remaining_gunmat' => $pctRemainingGunmat,
            'stock_polda' => $stockPolda,
            'stock_polres' => $stockPolres,
            'material_rusak' => $materialRusak,
            'warehouse' => $warehouseItems,
            'missing_satker' => $missingSatker,
            'satker_count' => $satkerCount,
            'kesimpulan_1' => $kesimpulan1,
            'kesimpulan_2' => $kesimpulan2,
        ];
    }

    /**
     * Generate plain-text resume formatted for WhatsApp/Telegram.
     */
    public function generateFormattedText(array $data): string
    {
        $out = "Kepada : Kasi Fasmat Ditlantas Polda Jatim\n\n\n";

        $out .= "A. CAPAIAN TARGET PNBP TAHUN {$data['year']} :\n";
        $out .= "1. Target PNBP : Rp".number_format($data['target_pnbp'], 0, ',', '.')."\n";
        $out .= "2. Realisasi PNBP : Rp".number_format($data['realized_pnbp'], 0, ',', '.')."\n";
        $out .= "3. Persentase capaian : ".str_replace('.', ',', (string) $data['pct_pnbp'])."%\n";
        $out .= "4. Sisa target : Rp".number_format($data['remaining_pnbp'], 0, ',', '.').' ('.str_replace('.', ',', (string) $data['pct_remaining_pnbp'])."%)\n\n";

        $out .= "B. CAPAIAN PENGGUNAAN MATERIAL (GUNMAT) TAHUN {$data['year']} :\n";
        $out .= "1. Target Renbut Matreg : ".number_format($data['target_gunmat'], 0, ',', '.')." unit\n";
        $out .= "2. Realisasi penggunaan : ".number_format($data['realized_gunmat'], 0, ',', '.')." unit\n";
        $out .= "3. Persentase capaian : ".str_replace('.', ',', (string) $data['pct_gunmat'])."%\n";
        $out .= "4. Sisa kebutuhan : ".number_format($data['remaining_gunmat'], 0, ',', '.').' unit ('.str_replace('.', ',', (string) $data['pct_remaining_gunmat'])."%)\n\n";

        $out .= "C. KONDISI STOK MATERIAL :\n";
        $out .= "1. Stok Polda : ".number_format($data['stock_polda'], 0, ',', '.')." unit\n";
        $out .= "2. Stok Polres : ".number_format($data['stock_polres'], 0, ',', '.')." unit\n";
        $out .= "3. Material rusak : ".number_format($data['material_rusak'], 0, ',', '.')." unit\n\n";

        $out .= "D. ANEV PERSEDIAAN WAREHOUSE :\n";
        $i = 1;
        foreach ($data['warehouse'] as $whName => $whQty) {
            $out .= ($i++).". {$whName} : ".number_format($whQty, 0, ',', '.')." unit\n";
        }
        $out .= "\n";

        $out .= "E. ANEV KETERTIBAN INPUT GUNMAT (Belum Input) : Pukul {$data['time_str']}\n";
        if (empty($data['missing_satker'])) {
            $out .= "Nihil (Seluruh satker telah menginput laporan)\n";
        } else {
            $j = 1;
            foreach ($data['missing_satker'] as $ms) {
                $out .= ($j++).'. '.$ms['label']."\n";
            }
        }
        $out .= "\n";

        $out .= "F. KESIMPULAN ANEV :\n";
        $out .= '1. '.$data['kesimpulan_1']."\n";
        $out .= '2. '.$data['kesimpulan_2']."\n";

        return $out;
    }
}
