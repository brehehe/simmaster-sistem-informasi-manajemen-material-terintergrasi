<?php

namespace App\Livewire\Warehouse;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\MenuPolda\RackAssignment\RackAssignment;
use App\Models\Police\PoliceStation;
use App\Models\Rack\Rack;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\StockDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WarehouseDisplayIndex extends Component
{
    public string $viewMode = 'all'; // 'all', 'polda', 'polres'
    public ?string $selectedPoliceStationId = null;

    public function toJSON()
    {
        return [];
    }

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->hasRole('Polres')) {
            $this->viewMode = 'polres';
            $this->selectedPoliceStationId = $user->police_station_id;
        }
    }

    public function render()
    {
        $user = Auth::user();

        // 1. QUERY RACKS & STOCK IN RACKS
        $racksQuery = Rack::with([
            'policeStation',
            'regionalPolice',
            'stockDetails.type',
            'stockDetails.typeDetail'
        ])->where('is_active', true);

        if ($user && $user->hasRole('Polres')) {
            $racksQuery->where('police_station_id', $user->police_station_id);
        } elseif ($this->selectedPoliceStationId) {
            $racksQuery->where('police_station_id', $this->selectedPoliceStationId);
        }

        $racks = $racksQuery->orderBy('name')->get()->map(function ($rack) {
            $totalQty = $rack->stockDetails->sum('quantity');
            $typeCount = $rack->stockDetails->pluck('type_id')->unique()->count();
            return [
                'id' => $rack->id,
                'name' => $rack->name,
                'code' => $rack->code ?? $rack->name,
                'location' => $rack->policeStation->name ?? ($rack->regionalPolice->name ?? 'Gudang Utama'),
                'total_quantity' => $totalQty,
                'type_count' => $typeCount,
                'items' => $rack->stockDetails->where('quantity', '>', 0)->values(),
            ];
        });

        // 2. QUERY DISTRIBUTION / SHIPMENT WAITING LIST (KFC Order Board style)
        $shipmentsQuery = MaterialShipment::with([
            'receiverPoliceStation',
            'materialShipmentDetails.type',
            'materialShipmentDetails.typeDetail'
        ])->where('is_active', true);

        if ($user && $user->hasRole('Polres')) {
            $shipmentsQuery->where('receiver_police_station_id', $user->police_station_id);
        } elseif ($this->selectedPoliceStationId) {
            $shipmentsQuery->where('receiver_police_station_id', $this->selectedPoliceStationId);
        }

        $pendingShipments = (clone $shipmentsQuery)
            ->whereIn('status', ['draft', 'sent', 'shipped', 'in_transit'])
            ->latest()
            ->take(8)
            ->get();

        $completedShipments = (clone $shipmentsQuery)
            ->where('status', 'received')
            ->latest('received_at')
            ->take(5)
            ->get();

        // 3. RECENT MOVEMENTS (TODAY)
        $movementsQuery = HistoryStock::with(['type', 'typeDetail', 'rack', 'policeStation'])
            ->where('is_active', true)
            ->whereDate('date', today());

        if ($user && $user->hasRole('Polres')) {
            $movementsQuery->where('police_station_id', $user->police_station_id);
        }

        $recentMovements = $movementsQuery->latest()->take(6)->get();

        // 4. STATS SUMMARY
        $totalStockQty = StockDetail::where('is_active', true)
            ->when($user && $user->hasRole('Polres'), fn($q) => $q->where('police_station_id', $user->police_station_id))
            ->sum('quantity');

        $activeRacksCount = $racks->where('total_quantity', '>', 0)->count();
        $pendingQueueCount = $pendingShipments->count();

        $policeStations = [];
        if ($user && $user->hasRole('Admin')) {
            $policeStations = PoliceStation::where('is_active', true)->orderBy('name')->get();
        }

        return view('livewire.warehouse.warehouse-display-index', [
            'racks' => $racks,
            'pendingShipments' => $pendingShipments,
            'completedShipments' => $completedShipments,
            'recentMovements' => $recentMovements,
            'totalStockQty' => $totalStockQty,
            'activeRacksCount' => $activeRacksCount,
            'pendingQueueCount' => $pendingQueueCount,
            'policeStations' => $policeStations,
        ])->layout('components.layouts.display');
    }
}
