<?php

namespace App\Services;

use App\Models\LastStock\LastStock;
use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail;
use App\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy;
use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use App\Models\Police\RegionalPolice;
use App\Models\Rack\Rack;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\Stock;
use App\Models\StockOpname\StockOpname;
use App\Models\Target\Target;
use App\Models\Target\TargetDetail;
use App\Models\Type\Type;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * Get daily PNBP (Rp) and Gunmat (Units) trend for a given month and year.
     */
    public function getDailyPnbpGunmatTrend(?int $month = null, ?int $year = null): array
    {
        $month = $month ?: now()->month;
        $year = $year ?: now()->year;

        $carbonDate = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $carbonDate->daysInMonth;
        $daysLabels = [];
        $pnbpData = [];
        $gunmatData = [];

        $isSqlite = DB::getDriverName() === 'sqlite';
        $dayExpr = $isSqlite ? "CAST(strftime('%d', material_usages.date) AS INTEGER)" : "EXTRACT(DAY FROM material_usages.date)";

        $usages = MaterialUsageDetail::query()
            ->select(
                DB::raw("{$dayExpr} as day_num"),
                DB::raw('SUM(material_usage_details.quantity * COALESCE(types.price, 0)) as total_pnbp'),
                DB::raw('SUM(material_usage_details.quantity) as total_gunmat')
            )
            ->join('material_usages', 'material_usage_details.material_usage_id', '=', 'material_usages.id')
            ->leftJoin('types', 'material_usage_details.type_id', '=', 'types.id')
            ->whereYear('material_usages.date', $year)
            ->whereMonth('material_usages.date', $month)
            ->groupBy(DB::raw($dayExpr))
            ->get()
            ->keyBy(fn($item) => (int)$item->day_num);

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $daysLabels[] = "Tgl {$day}";
            $usage = $usages->get($day);
            if ($usage) {
                $pnbpData[] = (float)$usage->total_pnbp;
                $gunmatData[] = (float)$usage->total_gunmat;
            } else {
                $pnbpData[] = 0;
                $gunmatData[] = 0;
            }
        }

        return [
            'labels' => $daysLabels,
            'pnbp' => $pnbpData,
            'gunmat' => $gunmatData,
            'month_name' => $carbonDate->locale('id')->isoFormat('MMMM Y'),
        ];
    }

    /**
     * Get 12-month historical stock trend using a SINGLE optimized query (replaces 12 loop queries).
     */
    public function getHistoryStockTrend(): array
    {
        $startDate = now()->subMonths(11)->startOfMonth();
        $isSqlite = DB::getDriverName() === 'sqlite';
        $yrExpr = $isSqlite ? "CAST(strftime('%Y', date) AS INTEGER)" : "EXTRACT(YEAR FROM date)";
        $moExpr = $isSqlite ? "CAST(strftime('%m', date) AS INTEGER)" : "EXTRACT(MONTH FROM date)";

        $rows = HistoryStock::query()
            ->select(
                DB::raw("{$yrExpr} as yr"),
                DB::raw("{$moExpr} as mo"),
                DB::raw('SUM(ABS(quantity)) as total_qty')
            )
            ->where('date', '>=', $startDate)
            ->groupBy(DB::raw($yrExpr), DB::raw($moExpr))
            ->get()
            ->keyBy(fn($r) => ((int)$r->yr) . '-' . ((int)$r->mo));

        $labels = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->year . '-' . $date->month;
            $labels[] = $date->locale('id')->format('M');
            $data[] = (float)($rows->get($key)?->total_qty ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get 6-month material movement (in vs out) using a SINGLE grouped query (replaces 12 loop queries).
     */
    public function getMaterialMovement(): array
    {
        $startDate = now()->subMonths(5)->startOfMonth();
        $isSqlite = DB::getDriverName() === 'sqlite';
        $yrExpr = $isSqlite ? "CAST(strftime('%Y', date) AS INTEGER)" : "EXTRACT(YEAR FROM date)";
        $moExpr = $isSqlite ? "CAST(strftime('%m', date) AS INTEGER)" : "EXTRACT(MONTH FROM date)";

        $rows = HistoryStock::query()
            ->select(
                DB::raw("{$yrExpr} as yr"),
                DB::raw("{$moExpr} as mo"),
                'status_type',
                DB::raw('SUM(quantity) as total_qty')
            )
            ->where('date', '>=', $startDate)
            ->whereIn('status_type', ['in', 'out'])
            ->groupBy(DB::raw($yrExpr), DB::raw($moExpr), 'status_type')
            ->get()
            ->groupBy(fn($r) => ((int)$r->yr) . '-' . ((int)$r->mo));

        $labels = [];
        $dataIn = [];
        $dataOut = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->year . '-' . $date->month;
            $monthGroup = $rows->get($key, collect());

            $in = (float)($monthGroup->firstWhere('status_type', 'in')?->total_qty ?? 0);
            $out = (float)($monthGroup->firstWhere('status_type', 'out')?->total_qty ?? 0);

            $labels[] = $date->locale('id')->format('M Y');
            $dataIn[] = $in;
            $dataOut[] = abs($out);
        }

        return [
            'labels' => $labels,
            'in' => $dataIn,
            'out' => $dataOut,
        ];
    }

    /**
     * Get Polda warehouse racks with stock items and run-out prediction.
     */
    public function getWarehouseRacks(): Collection
    {
        return Rack::query()
            ->whereNotNull('regional_police_id')
            ->whereNull('police_station_id')
            ->where('is_active', true)
            ->with(['stockDetails.type'])
            ->orderBy('name')
            ->get()
            ->map(function ($rack) {
                $items = $rack->stockDetails->groupBy('type_id')->map(function ($details) {
                    $first = $details->first();
                    return [
                        'name' => $first->type?->name ?? 'Unknown',
                        'quantity' => (int)$details->sum('quantity'),
                    ];
                })->values();

                $totalQty = (int)$items->sum('quantity');

                // Prediction: Total Stok / Daily Rate
                $dailyRate = max(1, round($totalQty > 200 ? $totalQty / 45 : 10));
                $daysUntilDepleted = $totalQty > 0 ? (int)ceil($totalQty / $dailyRate) : 0;
                $prediksiText = $totalQty === 0
                    ? 'Stok Habis'
                    : ($daysUntilDepleted > 60 ? '~' . round($daysUntilDepleted / 30) . ' Bulan' : '~' . $daysUntilDepleted . ' Hari');

                return [
                    'id' => $rack->id,
                    'name' => $rack->name,
                    'description' => $rack->description,
                    'items' => $items,
                    'total_quantity' => $totalQty,
                    'prediksi_habis' => $prediksiText,
                    'days_left' => $daysUntilDepleted,
                ];
            });
    }

    /**
     * Get PNBP and Renbut statistics.
     */
    public function getPnbpAndRenbutStats(?int $year = null): array
    {
        $currentYear = $year ?: now()->year;
        $activeTarget = Target::where('is_active', true)->where('year', $currentYear)->first()
            ?: Target::where('is_active', true)->orderByDesc('year')->first();

        if ($activeTarget) {
            $targetPNBP = TargetDetail::where('target_id', $activeTarget->id)
                ->join('types', 'target_details.type_id', '=', 'types.id')
                ->sum(DB::raw('target_details.quantity * COALESCE(types.price, 0)')) ?? 0;

            $targetRenbut = TargetDetail::where('target_id', $activeTarget->id)->sum('quantity') ?? 0;
        } else {
            $targetPNBP = 0;
            $targetRenbut = 0;
        }

        $realizedPNBP = MaterialUsageDetail::whereHas('materialUsage', fn($q) => $q->whereYear('date', $currentYear))
            ->join('types', 'material_usage_details.type_id', '=', 'types.id')
            ->sum(DB::raw('material_usage_details.quantity * COALESCE(types.price, 0)')) ?? 0;

        $realizedGunmat = MaterialUsageDetail::whereHas('materialUsage', fn($q) => $q->whereYear('date', $currentYear))
            ->sum('quantity') ?? 0;

        return [
            'target_year' => $activeTarget?->year ?? $currentYear,
            'pnbp' => [
                'target' => (float)$targetPNBP,
                'realization' => (float)$realizedPNBP,
                'percentage' => $targetPNBP > 0 ? round(($realizedPNBP / $targetPNBP) * 100, 1) : 0,
            ],
            'renbut' => [
                'target' => (float)$targetRenbut,
                'realization' => (float)$realizedGunmat,
                'percentage' => $targetRenbut > 0 ? round(($realizedGunmat / $targetRenbut) * 100, 1) : 0,
            ],
        ];
    }

    /**
     * Get stock opname status counts.
     */
    public function getStockOpnameStats(): array
    {
        return [
            'draft' => StockOpname::draft()->count(),
            'completed' => StockOpname::completed()->count(),
            'approved' => StockOpname::approved()->count(),
            'total' => StockOpname::count(),
        ];
    }

    /**
     * Get top 5 type distribution.
     */
    public function getTypeDistribution(): array
    {
        $types = Type::withCount('stocks')
            ->orderBy('stocks_count', 'DESC')
            ->take(5)
            ->get();

        return [
            'labels' => $types->pluck('name')->toArray(),
            'data' => $types->pluck('stocks_count')->toArray(),
        ];
    }

    /**
     * Get top 5 regional stock statistics.
     */
    public function getRegionalStats(): array
    {
        $regionals = RegionalPolice::select('regional_police.id', 'regional_police.name')
            ->leftJoin('stocks', 'regional_police.id', '=', 'stocks.regional_police_id')
            ->groupBy('regional_police.id', 'regional_police.name')
            ->selectRaw('COALESCE(SUM(stocks.quantity), 0) as total_stock')
            ->orderBy('total_stock', 'DESC')
            ->take(5)
            ->get();

        return [
            'labels' => $regionals->pluck('name')->toArray(),
            'data' => $regionals->pluck('total_stock')->toArray(),
        ];
    }

    /**
     * Get stock percentage and count distribution between Polda and Polres.
     */
    public function getStockDistribution(): array
    {
        $stockPolda = (float)(Stock::polda()->whereNull('type_detail_id')->sum('quantity') ?? 0);
        $stockPolres = (float)(Stock::polres()->whereNull('type_detail_id')->sum('quantity') ?? 0);
        $total = $stockPolda + $stockPolres;

        return [
            'polda' => $total > 0 ? round(($stockPolda / $total) * 100) : 0,
            'polres' => $total > 0 ? round(($stockPolres / $total) * 100) : 0,
            'polda_count' => $stockPolda,
            'polres_count' => $stockPolres,
        ];
    }

    /**
     * Get stock grouped by police station for chart visualization.
     */
    public function getStockPerLocation(): Collection
    {
        return Stock::query()
            ->select('police_station_id', DB::raw('SUM(quantity) as total_stock'))
            ->whereNotNull('police_station_id')
            ->with(['policeStation:id,name'])
            ->groupBy('police_station_id')
            ->orderByDesc('total_stock')
            ->take(10)
            ->get();
    }
}
