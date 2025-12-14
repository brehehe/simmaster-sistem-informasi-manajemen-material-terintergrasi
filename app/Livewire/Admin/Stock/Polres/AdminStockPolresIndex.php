<?php

namespace App\Livewire\Admin\Stock\Polres;

use App\Models\Police\PoliceStation;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AdminStockPolresIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterTypeId = '';
    public $filterPoliceStationId = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterTypeId' => ['except' => ''],
        'filterPoliceStationId' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterTypeId()
    {
        $this->resetPage();
    }

    public function updatingFilterPoliceStationId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        // Get stock details grouped by type
        $query = StockDetail::with(['type', 'typeDetail', 'rack', 'regionalPolice', 'policeStation'])
            ->whereNotNull('police_station_id') // Stock Polres level
            ->where('is_active', true);

        // Role-based filtering
        if ($user->hasRole('Polres')) {
            $query->where('police_station_id', $user->police_station_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('type', function ($tq) {
                    $tq->where('name', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('typeDetail', function ($tdq) {
                        $tdq->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('rack', function ($rq) {
                        $rq->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Filter by Type
        if ($this->filterTypeId) {
            $query->where('type_id', $this->filterTypeId);
        }

        // Filter by Police Station
        if ($this->filterPoliceStationId) {
            $query->where('police_station_id', $this->filterPoliceStationId);
        }

        // Get stock details and group by type_id
        $stockDetails = $query->orderBy('type_id')->get();

        // Group by type
        $groupedStocks = $stockDetails->groupBy('type_id')->map(function ($items, $typeId) {
            $type = $items->first()->type;
            $totalQuantity = $items->sum('quantity');

            return [
                'type' => $type,
                'total_quantity' => $totalQuantity,
                'details' => $items,
            ];
        });

        // Manual pagination
        $page = $this->getPage();
        $perPage = $this->perPage;
        $total = $groupedStocks->count();

        $paginatedStocks = $groupedStocks->slice(($page - 1) * $perPage, $perPage)->values();

        // Create pagination manually
        $stocks = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedStocks,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Dropdown data
        $types = Type::where('is_active', true)->orderBy('name')->get();
        $policeStations = PoliceStation::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.stock.polres.admin-stock-polres-index', [
            'stocks' => $stocks,
            'types' => $types,
            'policeStations' => $policeStations,
        ])->layout('components.layouts.main.app');
    }
}
