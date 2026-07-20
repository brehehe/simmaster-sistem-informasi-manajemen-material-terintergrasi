<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\LastStock\LastStock;
use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use App\Models\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy;
use App\Models\Police\RegionalPolice;
use App\Models\Reception\Reception;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\Stock;
use App\Models\StockOpname\StockOpname;
use App\Models\Target\Target;
use App\Models\Target\TargetDetail;
use App\Models\Type\Type;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminDashboardIndex extends Component
{
    public $showDataKendaraan = false;
    public $searchNopol = '';
    public $vehicleData = null;
    public $selectedDocTypes = ['STNK', 'TNKB', 'BPKB'];
    public $pengurusType = 'WP';

    public function toggleDataKendaraan()
    {
        $this->showDataKendaraan = !$this->showDataKendaraan;
    }

    public function cekKendaraan()
    {
        if (trim(strtoupper($this->searchNopol)) === 'L1111AAA') {
            $this->vehicleData = [
                'owner' => 'MICHAEL ARIANTO SIDIK',
                'nik' => '3578071504770004',
                'hp' => '08xxx',
                'chassis' => 'MHKAA1AY5MK000751',
                'engine' => '1NRG163797',
                'brand' => 'DAIHATSU',
                'type' => 'W100RG LBMFJ 1.3R CVT',
                'color' => 'MERAH METALIK',
                'bpkb_serial' => 'MU1445RT',
                'stnk_serial' => '04577970',
            ];
        } else {
            $this->vehicleData = null;
            $this->dispatch('notify', ['message' => 'Data tidak ditemukan', 'type' => 'error']);
        }
    }

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

        // Subsidi Material Statistics
        $totalSubsidies = MaterialSubsidy::where('is_active', true)->count();
        $totalSubsidiesConfirmed = MaterialSubsidy::where('is_active', true)->where('status', 'confirmed')->count();
        $recentSubsidies = MaterialSubsidy::with(['regionalPolice'])
            ->where('is_active', true)
            ->latest('subsidy_date')
            ->take(5)
            ->get();

        // NEW: Type Distribution
        $typeDistribution = $this->getTypeDistribution();

        // NEW: Regional Statistics
        $regionalStats = $this->getRegionalStats();

        // NEW: Monthly Material Movement
        $materialMovement = $this->getMaterialMovement();

        // NEW: Target vs Pencapaian (rotating per lokasi)
        $targetAchievementChart = $this->getTargetAchievementChart();

        // Target and Year for calculations
        $activeTarget = Target::where('is_active', true)->orderByDesc('year')->first();
        $currentYear = now()->year;

        // PNBP Stats (Revenue calculation: quantity * price)
        if ($activeTarget) {
            $targetPNBP = TargetDetail::where('target_id', $activeTarget->id)
                ->join('types', 'target_details.type_id', '=', 'types.id')
                ->sum(DB::raw('target_details.quantity * types.price')) ?? 0;
            
            $targetRenbut = TargetDetail::where('target_id', $activeTarget->id)->sum('quantity') ?? 0;
        } else {
            $targetPNBP = 0;
            $targetRenbut = 0;
        }

        $realizedPNBP = MaterialUsageDetail::whereHas('materialUsage', fn($q) => $q->whereYear('date', $currentYear))
            ->join('types', 'material_usage_details.type_id', '=', 'types.id')
            ->sum(DB::raw('material_usage_details.quantity * types.price')) ?? 0;

        $realizedGunmat = MaterialUsageDetail::whereHas('materialUsage', fn($q) => $q->whereYear('date', $currentYear))
            ->sum('quantity') ?? 0;

        $pnbpStats = [
            'target' => (float)$targetPNBP,
            'realization' => (float)$realizedPNBP,
            'percentage' => $targetPNBP > 0 ? round(($realizedPNBP / $targetPNBP) * 100, 1) : 0,
        ];

        $renbutStats = [
            'target' => (float)$targetRenbut,
            'realization' => (float)$realizedGunmat,
            'percentage' => $targetRenbut > 0 ? round(($realizedGunmat / $targetRenbut) * 100, 1) : 0,
        ];

        $warehouseRacks = \App\Models\Rack\Rack::whereNotNull('regional_police_id')
            ->whereNull('police_station_id')
            ->with(['stockDetails.type'])
            ->get()
            ->map(function ($rack) {
                $items = $rack->stockDetails->groupBy('type_id')->map(function ($details) {
                    $first = $details->first();
                    return [
                        'name' => $first->type?->name ?? 'Unknown',
                        'quantity' => (int)$details->sum('quantity'),
                    ];
                })->values();

                return [
                    'name' => $rack->name,
                    'description' => $rack->description,
                    'items' => $items,
                    'total_quantity' => $items->sum('quantity'),
                ];
            });

        // Polres Specific Dashboard Data
        $user = auth()->user();
        $isPolres = $user->hasRole('Polres') || !empty($user->police_station_id);
        $polresDashboardData = null;

        if ($isPolres) {
            $stationId = $user->police_station_id;

            $polresRacks = \App\Models\Rack\Rack::where('police_station_id', $stationId)
                ->with(['stockDetails.type'])
                ->get()
                ->map(function ($rack) {
                    $items = $rack->stockDetails->groupBy('type_id')->map(function ($details) {
                        $first = $details->first();
                        return [
                            'name' => $first->type?->name ?? 'Unknown',
                            'quantity' => (int) $details->sum('quantity'),
                        ];
                    })->values();

                    return [
                        'name' => $rack->name,
                        'description' => $rack->description,
                        'items' => $items,
                        'total_quantity' => $items->sum('quantity'),
                    ];
                });

            $polresStockByMaterial = \App\Models\Stock\StockDetail::where('police_station_id', $stationId)
                ->where('is_active', true)
                ->with('type')
                ->get()
                ->groupBy('type_id')
                ->map(function($details) {
                    $first = $details->first();
                    return [
                        'type_name' => $first->type?->name ?? 'Material',
                        'total_stock' => (int) $details->sum('quantity'),
                    ];
                })->values();

            $todayUsageByMaterial = MaterialUsageDetail::whereHas('materialUsage', function($q) use ($stationId) {
                    $q->where('police_station_id', $stationId)
                      ->whereDate('date', today());
                })
                ->with('type')
                ->get()
                ->groupBy('type_id')
                ->map(function($details) {
                    $first = $details->first();
                    return [
                        'type_name' => $first->type?->name ?? 'Material',
                        'quantity_today' => (int) $details->sum('quantity'),
                    ];
                })->values();

            $polresMaterialDamageTotal = \App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail::whereHas('materialDamage', function($q) use ($stationId) {
                    $q->where('police_station_id', $stationId);
                })->sum('quantity') ?? 0;

            $polresMaterialDamageByMaterial = \App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail::whereHas('materialDamage', function($q) use ($stationId) {
                    $q->where('police_station_id', $stationId);
                })
                ->with('type')
                ->get()
                ->groupBy('type_id')
                ->map(function($details) {
                    $first = $details->first();
                    return [
                        'type_name' => $first->type?->name ?? 'Material',
                        'quantity' => (int) $details->sum('quantity'),
                    ];
                })->values();

            $recentPolresReceptions = Reception::where('police_station_id', $stationId)
                ->with(['receptionDetails.type'])
                ->latest('date')
                ->take(5)
                ->get();

            $polresDashboardData = [
                'police_station' => $user->policeStation?->name ?? 'Polres',
                'racks' => $polresRacks,
                'stock_by_material' => $polresStockByMaterial,
                'today_usage' => $todayUsageByMaterial,
                'damage_total' => $polresMaterialDamageTotal,
                'damage_by_material' => $polresMaterialDamageByMaterial,
                'recent_receptions' => $recentPolresReceptions,
            ];
        }

        return view('livewire.admin.dashboard.admin-dashboard-index', [
            'isPolres' => $isPolres,
            'polresDashboardData' => $polresDashboardData,
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
            'targetAchievementChart' => $targetAchievementChart,
            'pnbpStats' => $pnbpStats,
            'renbutStats' => $renbutStats,
            'activeTargetYear' => $activeTarget ? $activeTarget->year : now()->year,
            'warehouseRacks' => $warehouseRacks,
            'totalSubsidies' => $totalSubsidies,
            'totalSubsidiesConfirmed' => $totalSubsidiesConfirmed,
            'recentSubsidies' => $recentSubsidies,
        ])->layout('components.layouts.main.app');
    }

    private function getHistoryStockTrend()
    {
        $data = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            // Sum of ABSOLUTE history stock quantity for this month (regardless of in/out)
            $total = HistoryStock::whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum(DB::raw('ABS(quantity)')) ?? 0;

            $labels[] = $date->locale('id')->format('M');
            $data[] = abs($total);
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

    private function getTargetAchievementChart(): array
    {
        $target = Target::query()
            ->where('is_active', true)
            ->orderByDesc('year')
            ->first();

        if (! $target) {
            return [
                'types' => [],
                'locations' => [],
            ];
        }

        $types = Type::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $locations = [];
        $regionalPolice = RegionalPolice::query()
            ->with(['policeStations' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        foreach ($regionalPolice as $regional) {
            $locations[] = [
                'key' => 'regional_'.$regional->id,
                'label' => $regional->name,
                'regional_police_id' => $regional->id,
                'police_station_id' => null,
            ];

            foreach ($regional->policeStations as $station) {
                $locations[] = [
                    'key' => 'station_'.$station->id,
                    'label' => $station->name,
                    'regional_police_id' => $station->regional_police_id,
                    'police_station_id' => $station->id,
                ];
            }
        }

        $usageYear = now()->year;
        $usageMonth = now()->month;

        $targets = TargetDetail::query()
            ->select('regional_police_id', 'police_station_id', 'type_id', DB::raw('SUM(quantity) as total'))
            ->where('target_id', $target->id)
            ->whereNotNull('type_id')
            ->groupBy('regional_police_id', 'police_station_id', 'type_id')
            ->get()
            ->mapWithKeys(function ($row) {
                $key = $row->police_station_id
                    ? 'station_'.$row->police_station_id
                    : 'regional_'.$row->regional_police_id;

                return [$key.'|'.$row->type_id => (float) $row->total];
            });

        $actuals = MaterialUsageDetail::query()
            ->select('material_usages.regional_police_id', 'material_usages.police_station_id', 'material_usage_details.type_id', DB::raw('SUM(material_usage_details.quantity) as total'))
            ->join('material_usages', 'material_usage_details.material_usage_id', '=', 'material_usages.id')
            ->whereYear('material_usages.date', $usageYear)
            // ->whereMonth('material_usages.date', $usageMonth)
            ->whereNotNull('material_usage_details.type_id')
            ->groupBy('material_usages.regional_police_id', 'material_usages.police_station_id', 'material_usage_details.type_id')
            ->get()
            ->mapWithKeys(function ($row) {
                $key = $row->police_station_id
                    ? 'station_'.$row->police_station_id
                    : 'regional_'.$row->regional_police_id;

                return [$key.'|'.$row->type_id => (float) $row->total];
            });

        $typeLabels = $types->pluck('name')->toArray();

        $locationPayload = [];

        foreach ($locations as $location) {
            $targetValues = [];
            $actualValues = [];

            foreach ($types as $type) {
                $mapKey = $location['key'].'|'.$type->id;
                $targetValues[] = $targets[$mapKey] ?? 0;
                $actualValues[] = $actuals[$mapKey] ?? 0;
            }

            $locationPayload[] = [
                'label' => $location['label'],
                'target' => $targetValues,
                'actual' => $actualValues,
            ];
        }

        return [
            'types' => $typeLabels,
            'locations' => $locationPayload,
        ];
    }
}
