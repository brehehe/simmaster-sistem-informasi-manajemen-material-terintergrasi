<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\LastStock\LastStock;
use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Reception\Reception;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\Stock;
use App\Models\StockOpname\StockOpname;
use App\Models\Type\Type;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminDashboardIndex extends Component
{
    public function render()
    {
        // Statistics
        $totalReceptions = Reception::count();
        $totalStockPolda = Stock::polda()->sum('quantity') ?? 0;
        $totalStockPolres = Stock::polres()->sum('quantity') ?? 0;
        $receptionsToday = Reception::whereDate('date', today()->toDateString())->count();

        // Monthly History Stock Trend (last 12 months)
        $historyStockTrend = $this->getHistoryStockTrend();

        // Stock Distribution (Polda vs Polres)
        $stockDistribution = $this->getStockDistribution();

        // Recent Receptions (last 5)
        $recentReceptions = Reception::with(['regionalPolice', 'policeStation', 'receptionDetails'])
            ->latest('date')
            ->take(5)
            ->get();

        // Stock per Location (Top 5 Polres)
        $stockPerLocation = Stock::select('police_station_id', DB::raw('SUM(quantity) as total_stock'))
            ->whereNotNull('police_station_id')
            ->groupBy('police_station_id')
            ->orderBy('total_stock', 'DESC')
            ->take(5)
            ->with('policeStation')
            ->get();

        // Calculate percentage change from last month
        $lastMonthReceptions = Reception::whereMonth('date', now()->subMonth()->month)
            ->whereYear('date', now()->subMonth()->year)
            ->count();
        $currentMonthReceptions = Reception::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        $percentageChange = $lastMonthReceptions > 0
            ? round((($currentMonthReceptions - $lastMonthReceptions) / $lastMonthReceptions) * 100, 1)
            : 0;

        // NEW: StockOpname Statistics
        $stockOpnameStats = $this->getStockOpnameStats();

        // NEW: LastStock Data
        $recentLastStock = LastStock::with(['regionalPolice', 'policeStation'])
            ->latest('date')
            ->take(5)
            ->get();

        // NEW: Material Damage & Usage
        $materialDamage = MaterialDamage::with(['regionalPolice', 'policeStation'])
            ->latest('date')
            ->take(5)
            ->get();
        $materialUsage = MaterialUsage::with(['regionalPolice', 'policeStation'])
            ->latest('date')
            ->take(5)
            ->get();

        // NEW: Type Distribution
        $typeDistribution = $this->getTypeDistribution();

        // NEW: Regional Statistics
        $regionalStats = $this->getRegionalStats();

        // NEW: Monthly Material Movement
        $materialMovement = $this->getMaterialMovement();

        return view('livewire.admin.dashboard.admin-dashboard-index', [
            'totalReceptions' => $totalReceptions,
            'totalStockPolda' => $totalStockPolda,
            'totalStockPolres' => $totalStockPolres,
            'receptionsToday' => $receptionsToday,
            'historyStockTrend' => $historyStockTrend,
            'stockDistribution' => $stockDistribution,
            'recentReceptions' => $recentReceptions,
            'stockPerLocation' => $stockPerLocation,
            'percentageChange' => $percentageChange,
            // New data
            'stockOpnameStats' => $stockOpnameStats,
            'recentLastStock' => $recentLastStock,
            'materialDamage' => $materialDamage,
            'materialUsage' => $materialUsage,
            'typeDistribution' => $typeDistribution,
            'regionalStats' => $regionalStats,
            'materialMovement' => $materialMovement,
        ])->layout('components.layouts.main.app');
    }

    private function getHistoryStockTrend()
    {
        $data = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            // Sum of history stock quantity for this month
            $total = HistoryStock::whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('quantity') ?? 0;

            $labels[] = $date->locale('id')->format('M');
            $data[] = $total;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getStockDistribution()
    {
        $stockPolda = Stock::polda()->sum('quantity') ?? 0;
        $stockPolres = Stock::polres()->sum('quantity') ?? 0;

        $total = $stockPolda + $stockPolres;

        return [
            'polda' => $total > 0 ? round(($stockPolda / $total) * 100) : 0,
            'polres' => $total > 0 ? round(($stockPolres / $total) * 100) : 0,
            'polda_count' => $stockPolda,
            'polres_count' => $stockPolres,
        ];
    }

    private function getStockOpnameStats()
    {
        return [
            'draft' => StockOpname::draft()->count(),
            'completed' => StockOpname::completed()->count(),
            'approved' => StockOpname::approved()->count(),
            'total' => StockOpname::count(),
        ];
    }

    private function getTypeDistribution()
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

    private function getRegionalStats()
    {
        $regionals = RegionalPolice::select('id', 'name')
            ->withCount(['stocks as total_stock' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity), 0)'));
            }])
            ->orderBy('total_stock', 'DESC')
            ->take(5)
            ->get();

        return [
            'labels' => $regionals->pluck('name')->toArray(),
            'data' => $regionals->pluck('total_stock')->toArray(),
        ];
    }

    private function getMaterialMovement()
    {
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $in = HistoryStock::whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->where('status_type', 'in')
                ->sum('quantity') ?? 0;

            $out = HistoryStock::whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->where('status_type', 'out')
                ->sum('quantity') ?? 0;

            $labels[] = $date->locale('id')->format('M Y');
            $data['in'][] = $in;
            $data['out'][] = abs($out);
        }

        return [
            'labels' => $labels,
            'in' => $data['in'],
            'out' => $data['out'],
        ];
    }
}
