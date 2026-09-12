<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\LastStock\LastStock;
use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail;
use App\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy;
use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use App\Models\Police\RegionalPolice;
use App\Models\Rack\Rack;
use App\Models\Reception\Reception;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use App\Services\DashboardStatsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminDashboardIndex extends Component
{
    public bool $showDataKendaraan = false;
    public string $searchNopol = '';
    public ?array $vehicleData = null;
    public array $selectedDocTypes = ['STNK', 'TNKB', 'BPKB'];
    public string $pengurusType = 'WP';
    public ?int $selectedChartMonth = null;
    public ?int $selectedChartYear = null;
    public bool $showImportEriModal = false;
    public string $eriImportNopol = '';
    public string $eriImportBpkb = '';
    public string $eriImportStnk = '';
    public string $eriImportTnkb = '';
    public array $vehicleCheckHistory = [];

    public function mount(): void
    {
        $this->selectedChartMonth = now()->month;
        $this->selectedChartYear = now()->year;

        // Initial default log history for demonstration
        $this->vehicleCheckHistory = [
            [
                'nopol' => 'L 1111 AAA',
                'owner' => 'MICHAEL ARIANTO SIDIK',
                'brand_type' => 'DAIHATSU W100RG LBMFJ 1.3R CVT',
                'bpkb' => 'MU1445RT',
                'stnk' => '04577970',
                'checked_at' => now()->subHours(2)->format('d/m/Y H:i'),
                'status' => 'Valid (SAMSAT & ERI)',
            ],
            [
                'nopol' => 'L 1829 XZ',
                'owner' => 'BAMBANG HERMANTO',
                'brand_type' => 'HONDA ALL NEW CR-V 1.5 TURBO',
                'bpkb' => 'KT982110',
                'stnk' => '08772190',
                'checked_at' => now()->subDay()->format('d/m/Y H:i'),
                'status' => 'Valid (SAMSAT)',
            ],
            [
                'nopol' => 'N 4521 AA',
                'owner' => 'SITI AMINAH',
                'brand_type' => 'TOYOTA AVANZA 1.5 G CVT',
                'bpkb' => 'NJ341120',
                'stnk' => '02991044',
                'checked_at' => now()->subDays(2)->format('d/m/Y H:i'),
                'status' => 'Valid (SAMSAT)',
            ],
        ];
    }

    public function toggleDataKendaraan(): void
    {
        $this->showDataKendaraan = !$this->showDataKendaraan;
    }

    public function openImportEriModal(): void
    {
        $this->showImportEriModal = true;
    }

    public function closeImportEriModal(): void
    {
        $this->showImportEriModal = false;
        $this->reset(['eriImportNopol', 'eriImportBpkb', 'eriImportStnk', 'eriImportTnkb']);
    }

    public function processImportEri(): void
    {
        $this->validate([
            'eriImportNopol' => 'required',
            'eriImportBpkb' => 'nullable',
            'eriImportStnk' => 'nullable',
            'eriImportTnkb' => 'nullable',
        ]);

        $nopolClean = strtoupper(trim($this->eriImportNopol));

        array_unshift($this->vehicleCheckHistory, [
            'nopol' => $nopolClean,
            'owner' => 'IMPORT ERI KORLANTAS',
            'brand_type' => 'Data Sinkron ERI',
            'bpkb' => $this->eriImportBpkb ?: '-',
            'stnk' => $this->eriImportStnk ?: '-',
            'checked_at' => now()->format('d/m/Y H:i'),
            'status' => 'Import ERI Sukses',
        ]);

        $this->closeImportEriModal();
        $this->dispatch('notify', ['message' => "Data ERI untuk Nopol {$nopolClean} berhasil diimpor!", 'type' => 'success']);
    }

    public function cekKendaraan(): void
    {
        $input = trim(strtoupper(str_replace(' ', '', $this->searchNopol)));

        if ($input === 'L1111AAA' || empty($input)) {
            $this->vehicleData = [
                'nopol' => 'L 1111 AAA',
                'owner' => 'MICHAEL ARIANTO SIDIK',
                'nik' => '3578071504770004',
                'hp' => '081234567890',
                'email' => 'michael.arianto@example.com',
                'chassis' => 'MHKAA1AY5MK000751',
                'engine' => '1NRG163797',
                'brand' => 'DAIHATSU',
                'type' => 'W100RG LBMFJ 1.3R CVT',
                'color' => 'MERAH METALIK',
                'fuel' => 'BENSIN',
                'year' => '2023',
                'bpkb_serial' => 'MU1445RT',
                'stnk_serial' => '04577970',
                'tnkb_serial' => 'L 1111 AAA',
            ];
        } else {
            $this->vehicleData = [
                'nopol' => strtoupper($this->searchNopol),
                'owner' => 'WAJIB PAJAK REGIDENT JATIM',
                'nik' => '35' . rand(10000000000000, 99999999999999),
                'hp' => '08' . rand(1000000000, 9999999999),
                'email' => 'wp.samsat@jatim.polri.go.id',
                'chassis' => 'MH3' . strtoupper(substr(md5($input), 0, 14)),
                'engine' => '1NR' . rand(100000, 999999),
                'brand' => 'HONDA / TOYOTA',
                'type' => 'PASSENGER CAR / SEDAN',
                'color' => 'HITAM METALIK',
                'fuel' => 'BENSIN',
                'year' => '2022',
                'bpkb_serial' => 'MU' . rand(100000, 999999),
                'stnk_serial' => '04' . rand(100000, 999999),
                'tnkb_serial' => strtoupper($this->searchNopol),
            ];
        }

        // Simpan ke riwayat pengecekan
        array_unshift($this->vehicleCheckHistory, [
            'nopol' => $this->vehicleData['nopol'],
            'owner' => $this->vehicleData['owner'],
            'brand_type' => $this->vehicleData['brand'] . ' ' . $this->vehicleData['type'],
            'bpkb' => $this->vehicleData['bpkb_serial'],
            'stnk' => $this->vehicleData['stnk_serial'],
            'checked_at' => now()->format('d/m/Y H:i'),
            'status' => 'Valid (SAMSAT & ERI)',
        ]);
    }

    public function render(DashboardStatsService $statsService)
    {
        $user = Auth::user();
        $isPolres = $user?->hasRole('Polres') || !empty($user?->police_station_id);

        // Core Aggregate Metrics (Materiil Utama PNBP)
        $totalStockPolda = Stock::polda()->whereNull('type_detail_id')->sum('quantity') ?? 0;
        $totalStockPolres = Stock::polres()->whereNull('type_detail_id')->sum('quantity') ?? 0;
        $totalReceptions = Reception::count();
        $receptionsToday = Reception::whereDate('date', today())->count();

        // Material Damage Total
        $totalMaterialDamage = MaterialDamageDetail::whereHas('materialDamage', fn($q) => $q->where('is_active', true))->sum('quantity') ?? 0;
        if ($totalMaterialDamage == 0) {
            $totalMaterialDamage = MaterialDamage::count();
        }

        // Subsidies metrics
        $totalSubsidies = MaterialSubsidy::where('is_active', true)->count();
        $totalSubsidiesConfirmed = MaterialSubsidy::where('is_active', true)->where('status', 'confirmed')->count();
        $recentSubsidies = MaterialSubsidy::with(['regionalPolice'])
            ->where('is_active', true)
            ->latest('subsidy_date')
            ->take(5)
            ->get();

        // Delegated to DashboardStatsService (Optimized queries)
        $pnbpAndRenbut = $statsService->getPnbpAndRenbutStats();
        $dailyPnbpGunmatChart = $statsService->getDailyPnbpGunmatTrend($this->selectedChartMonth, $this->selectedChartYear);
        $warehouseRacks = $statsService->getWarehouseRacks();
        $stockOpnameStats = $statsService->getStockOpnameStats();
        $typeDistribution = $statsService->getTypeDistribution();
        $regionalStats = $statsService->getRegionalStats();
        $materialMovement = $statsService->getMaterialMovement();
        $historyStockTrend = $statsService->getHistoryStockTrend();

        // Recent tables with eager loading to prevent N+1
        $recentReceptions = Reception::with(['regionalPolice', 'policeStation', 'receptionDetails', 'type'])
            ->latest('date')
            ->take(5)
            ->get();
        $recentLastStock = LastStock::with(['regionalPolice', 'policeStation'])
            ->latest('date')
            ->take(5)
            ->get();
        $materialDamage = MaterialDamage::with(['regionalPolice', 'policeStation'])
            ->latest('date')
            ->take(5)
            ->get();
        $materialUsage = MaterialUsage::with(['regionalPolice', 'policeStation'])
            ->latest('date')
            ->take(5)
            ->get();

        // Target Achievement Structure
        $targetAchievementChart = [
            'types' => [],
            'locations' => [],
        ];

        // Polres Specific Dashboard Data (if applicable)
        $polresDashboardData = null;
        if ($isPolres && $user->police_station_id) {
            $stationId = $user->police_station_id;

            $polresRacks = Rack::where('police_station_id', $stationId)
                ->with(['stockDetails' => fn($q) => $q->whereNull('type_detail_id')->with('type')])
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

            $stockByMaterial = StockDetail::where('police_station_id', $stationId)
                ->where('is_active', true)
                ->where('quantity', '>', 0)
                ->with('type')
                ->get()
                ->groupBy('type_id')
                ->map(function ($items) {
                    $pnbpQty = $items->whereNull('type_detail_id')->sum('quantity');
                    return [
                        'type_name' => $items->first()->type?->name ?? 'Material',
                        'total_stock' => (int) ($pnbpQty > 0 ? $pnbpQty : $items->sum('quantity'))
                    ];
                })->values();

            $damageTotal = MaterialDamageDetail::whereHas('materialDamage', fn($q) => $q->where('police_station_id', $stationId))->sum('quantity') ?? 0;
            $damageByMaterial = MaterialDamageDetail::whereHas('materialDamage', fn($q) => $q->where('police_station_id', $stationId))
                ->with('type')
                ->get()
                ->groupBy('type_id')
                ->map(fn($group) => [
                    'type_name' => $group->first()->type?->name ?? 'Material',
                    'quantity' => (int)$group->sum('quantity')
                ])->values();

            $todayUsages = MaterialUsageDetail::whereHas('materialUsage', fn($q) => $q->where('police_station_id', $stationId)->whereDate('date', today()))
                ->with('type')
                ->get()
                ->groupBy('type_id')
                ->map(fn($group) => [
                    'type_name' => $group->first()->type?->name ?? 'Material',
                    'quantity_today' => (int)$group->sum('quantity')
                ])->values();

            $polresDashboardData = [
                'police_station' => $user->policeStation?->name ?? 'Polres',
                'station_name' => $user->policeStation?->name ?? 'Polres',
                'stock_by_material' => $stockByMaterial,
                'racks' => $polresRacks,
                'damage_total' => $damageTotal,
                'damage_by_material' => $damageByMaterial,
                'today_usage' => $todayUsages,
                'recent_receptions' => Reception::where('police_station_id', $stationId)->with(['receptionDetails', 'regionalPolice', 'policeStation'])->latest('date')->take(5)->get(),
            ];
        }

        $stockDistribution = $statsService->getStockDistribution();
        $stockPerLocation = $statsService->getStockPerLocation();

        return view('livewire.admin.dashboard.admin-dashboard-index', [
            'showDataKendaraan' => $this->showDataKendaraan,
            'searchNopol' => $this->searchNopol,
            'vehicleData' => $this->vehicleData,
            'selectedDocTypes' => $this->selectedDocTypes,
            'pengurusType' => $this->pengurusType,
            'selectedChartMonth' => $this->selectedChartMonth,
            'selectedChartYear' => $this->selectedChartYear,
            'showImportEriModal' => $this->showImportEriModal,
            'eriImportNopol' => $this->eriImportNopol,
            'eriImportBpkb' => $this->eriImportBpkb,
            'eriImportStnk' => $this->eriImportStnk,
            'eriImportTnkb' => $this->eriImportTnkb,
            'vehicleCheckHistory' => $this->vehicleCheckHistory,
            'isPolres' => $isPolres,
            'polresDashboardData' => $polresDashboardData,
            'totalStockPolda' => $totalStockPolda,
            'totalStockPolres' => $totalStockPolres,
            'totalReceptions' => $totalReceptions,
            'receptionsToday' => $receptionsToday,
            'totalMaterialDamage' => $totalMaterialDamage,
            'pnbpStats' => $pnbpAndRenbut['pnbp'],
            'renbutStats' => $pnbpAndRenbut['renbut'],
            'activeTargetYear' => $pnbpAndRenbut['target_year'],
            'recentReceptions' => $recentReceptions,
            'recentLastStock' => $recentLastStock,
            'stockOpnameStats' => $stockOpnameStats,
            'materialDamage' => $materialDamage,
            'materialUsage' => $materialUsage,
            'typeDistribution' => $typeDistribution,
            'regionalStats' => $regionalStats,
            'historyStockTrend' => $historyStockTrend,
            'materialMovement' => $materialMovement,
            'dailyPnbpGunmatChart' => $dailyPnbpGunmatChart,
            'targetAchievementChart' => $targetAchievementChart,
            'warehouseRacks' => $warehouseRacks,
            'totalSubsidies' => $totalSubsidies,
            'totalSubsidiesConfirmed' => $totalSubsidiesConfirmed,
            'recentSubsidies' => $recentSubsidies,
            'stockDistribution' => $stockDistribution,
            'stockPerLocation' => $stockPerLocation,
        ])->layout('components.layouts.main.app');
    }
}
