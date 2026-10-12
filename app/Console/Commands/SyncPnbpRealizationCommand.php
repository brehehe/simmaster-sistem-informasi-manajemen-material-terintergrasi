<?php

namespace App\Console\Commands;

use App\Services\PnbpTargetService;
use Illuminate\Console\Command;

class SyncPnbpRealizationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pnbp:sync-realization
                            {--year=2026 : Tahun target PNBP (default: 2026)}
                            {--month= : Bulan tertentu yang ingin disinkronkan (1-12), atau kosongkan untuk auto-sync semua bulan aktif}
                            {--force : Paksa refresh sinkronisasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi capaian realisasi materiil dan PNBP dari transaksi material_usages ARMASTER ke TargetPnbpItem dan Dashboard';

    /**
     * Execute the console command.
     */
    public function handle(PnbpTargetService $pnbpService): int
    {
        $year = (int) ($this->option('year') ?: 2026);
        $month = $this->option('month') ? (int) $this->option('month') : null;
        $force = (bool) $this->option('force');

        $this->info("=== SINKRONISASI CAPAIAN PNBP & MATERIIL TA {$year} ===");

        if ($month !== null) {
            $this->info("Menyinkronkan bulan ke-{$month}...");
            $res = $pnbpService->syncMonthlyRealizationFromUsage($year, $month);

            if (($res['status'] ?? '') === 'success') {
                $this->info("Berhasil sinkron sheet {$res['sheet_name']}:");
                $this->line(" - Volume: " . number_format($res['total_volume']) . " Unit");
                $this->line(" - Nilai PNBP: Rp " . number_format($res['total_rp']));
            } else {
                $this->error("Gagal: " . ($res['message'] ?? 'Unknown error'));
                return Command::FAILURE;
            }
        } else {
            $this->info("Menjalankan auto-sync bulan aktif...");
            $pnbpService->autoSyncActiveUsageMonths($year, $force);
            $this->info("Auto-sync selesai.");
        }

        // Tampilkan ringkasan REKAP terbaru
        $rekap = $pnbpService->getSheetDetails('REKAP', null, $year);
        $totalRow = collect($rekap['items'] ?? [])->firstWhere('row', 99)
            ?: collect($rekap['items'] ?? [])->firstWhere('level', 0);

        if ($totalRow) {
            $this->newLine();
            $this->info("Ringkasan REKAP Akumulasi Terbaru TA {$year}:");
            $this->line(" - Target Renbut: " . number_format($totalRow['volume_target']) . " Unit");
            $this->line(" - Realisasi Gunmat: " . number_format($totalRow['volume_realization']) . " Unit");
            $this->line(" - Target PNBP: Rp " . number_format($totalRow['target_rp']));
            $this->line(" - Realisasi PNBP: Rp " . number_format($totalRow['realization_rp']) . " ({$totalRow['percentage']}%)");
        }

        return Command::SUCCESS;
    }
}
