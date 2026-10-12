<?php

namespace App\Console\Commands;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetailItem;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CleanSeptemberMaterialUsageCommand extends Command
{
    protected $signature = 'material:clean-september {--dry-run : Simulasi tanpa mengubah data} {--force : Jalankan langsung tanpa konfirmasi}';
    protected $description = 'Hapus transaksi pemakaian material 7-30 September 2026, kembalikan stok, dan bersihkan history stock';

    public function handle(): int
    {
        $startDate = '2026-09-07';
        $endDate = '2026-09-30';
        $isDryRun = (bool) $this->option('dry-run');

        $this->info("Memeriksa data pemakaian material rentang {$startDate} s/d {$endDate}...");

        // 1. Dapatkan daftar ID material_usages pada rentang tanggal tersebut
        $activeUsageIds = MaterialUsage::whereBetween('date', [$startDate, $endDate])->pluck('id')->all();
        $trashedUsageIds = MaterialUsage::onlyTrashed()->whereBetween('date', [$startDate, $endDate])->pluck('id')->all();
        $allUsageIds = array_values(array_unique(array_merge($activeUsageIds, $trashedUsageIds)));

        $totalActive = count($activeUsageIds);
        $totalTrashed = count($trashedUsageIds);
        $totalAll = count($allUsageIds);

        $this->line("Ditemukan:");
        $this->line("- Material Usage Aktif   : {$totalActive}");
        $this->line("- Material Usage Trashed : {$totalTrashed}");
        $this->line("- Total Header Terpilih  : {$totalAll}");

        if ($totalAll === 0) {
            $this->info("Tidak ada data Material Usage pada rentang tanggal {$startDate} s/d {$endDate}. Selesai.");
            return 0;
        }

        // 2. Hitung detail pemakaian yang perlu di-restore stoknya (hanya dari usage aktif yang belum soft-deleted)
        $detailAggregates = DB::table('material_usage_details')
            ->select('stock_detail_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereIn('material_usage_id', $activeUsageIds)
            ->whereNull('deleted_at')
            ->whereNotNull('stock_detail_id')
            ->where('quantity', '>', 0)
            ->groupBy('stock_detail_id')
            ->get();

        $totalRestoredQty = $detailAggregates->sum('total_qty');
        $this->line("- Total Kuantitas yang akan di-restore ke Stok: " . number_format($totalRestoredQty, 2));

        // 3. Hitung history stock yang akan dihapus
        $historyCount = HistoryStock::whereIn('material_usage_id', $allUsageIds)->count();
        $this->line("- Riwayat Stok (HistoryStock) terkait: {$historyCount}");

        // Verifikasi batas aman:
        $countBeforeSept7 = MaterialUsage::withTrashed()->where('date', '<', $startDate)->count();
        $countFromOct1 = MaterialUsage::withTrashed()->where('date', '>=', '2026-10-01')->count();
        $this->line("Batas Keamanan:");
        $this->line("- Data sebelum 7 Sept 2026 (tetap aman): {$countBeforeSept7}");
        $this->line("- Data mulai 1 Okt 2026 (tetap aman)   : {$countFromOct1}");

        if ($isDryRun) {
            $this->warn("[DRY-RUN] Mode simulasi selesai. Tidak ada perubahan data dilakukan.");
            return 0;
        }

        if (! $this->option('force') && ! $this->confirm("Apakah Anda yakin ingin menghapus {$totalAll} material usage dan mengembalikan stok sebesar {$totalRestoredQty}?")) {
            $this->warn("Dibatalkan oleh pengguna.");
            return 1;
        }

        $this->info("Memulai proses restore stok dan pembersihan database dalam transaksi...");

        DB::transaction(function () use ($allUsageIds, $detailAggregates) {
            // STEP A: Restore Stok
            $this->line("1. Mengembalikan stok material (StockDetail & Stock)...");
            $stockDetails = StockDetail::whereIn('id', $detailAggregates->pluck('stock_detail_id'))->get()->keyBy('id');
            $stockUpdates = [];

            foreach ($detailAggregates as $agg) {
                $qty = (float) $agg->total_qty;
                if ($qty <= 0) {
                    continue;
                }

                $stockDetail = $stockDetails->get($agg->stock_detail_id);
                if ($stockDetail) {
                    $stockDetail->increment('quantity', $qty);
                    $stockId = $stockDetail->stock_id;
                    if ($stockId) {
                        $stockUpdates[$stockId] = ($stockUpdates[$stockId] ?? 0) + $qty;
                    }
                }
            }

            foreach ($stockUpdates as $stockId => $qty) {
                Stock::where('id', $stockId)->increment('quantity', $qty);
            }

            // STEP B: Hapus HistoryStock
            $this->line("2. Menghapus riwayat HistoryStock...");
            foreach (array_chunk($allUsageIds, 500) as $chunk) {
                DB::table('history_stocks')->whereIn('material_usage_id', $chunk)->delete();
            }

            // STEP C: Hapus MaterialUsageDetailItems
            $this->line("3. Menghapus MaterialUsageDetailItems...");
            foreach (array_chunk($allUsageIds, 500) as $chunk) {
                DB::table('material_usage_detail_items')->whereIn('material_usage_id', $chunk)->delete();
            }

            // STEP D: Hapus MaterialUsageDetails
            $this->line("4. Menghapus MaterialUsageDetails...");
            foreach (array_chunk($allUsageIds, 500) as $chunk) {
                DB::table('material_usage_details')->whereIn('material_usage_id', $chunk)->delete();
            }

            // STEP E: Hapus MaterialUsages
            $this->line("5. Menghapus MaterialUsages...");
            foreach (array_chunk($allUsageIds, 500) as $chunk) {
                DB::table('material_usages')->whereIn('id', $chunk)->delete();
            }
        });

        // Clear relevant cache
        Cache::flush();

        $this->info("SUKSES: Seluruh inputan transaksi pemakaian material 7-30 September 2026 berhasil dibersihkan dan stok berhasil dipulihkan!");

        // Verifikasi akhir
        $remainingSept = MaterialUsage::withTrashed()->whereBetween('date', [$startDate, $endDate])->count();
        $remainingOct = MaterialUsage::where('date', '>=', '2026-10-01')->count();
        $this->line("Verifikasi Akhir:");
        $this->line("- Sisa pemakaian 7-30 Sept: {$remainingSept} (Harus 0)");
        $this->line("- Pemakaian mulai 1 Okt    : {$remainingOct} (Tetap utuh)");

        return 0;
    }
}
