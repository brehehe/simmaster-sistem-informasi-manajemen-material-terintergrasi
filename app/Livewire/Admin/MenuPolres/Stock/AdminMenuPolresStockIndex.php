<?php

namespace App\Livewire\Admin\MenuPolres\Stock;

use App\Models\Police\PoliceStation;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class AdminMenuPolresStockIndex extends Component
{
    use WithPagination;

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    #[Url]
    public $policeStationId = '';

    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'typeId' => ['except' => ''],
        'typeDetailId' => ['except' => ''],
        'policeStationId' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeId()
    {
        $this->resetPage();
    }

    public function updatingTypeDetailId()
    {
        $this->resetPage();
    }

    public function updatingPoliceStationId()
    {
        $this->resetPage();
    }

    public function toJSON()
    {
        return [];
    }

    public function render()
    {
        $user = auth()->user();

        // Load filter options
        $policeStations = [];
        if ($user->hasRole('Admin')) {
            $policeStations = PoliceStation::orderBy('name')->get();
        }

        $allTypes = Type::query();
        if ($user->userType && !empty($user->userType->types)) {
            $allTypes->whereIn('id', $user->userType->types);
        }
        $allTypes = $allTypes->orderBy('name')->get();

        $typeDetails = [];
        if ($this->typeId) {
            $typeDetails = TypeDetail::where('type_id', $this->typeId)->orderBy('name')->get();
        } else {
             $tdQuery = TypeDetail::query();
             if ($user->userType && !empty($user->userType->types)) {
                 $tdQuery->whereIn('type_id', $user->userType->types);
             }
             $typeDetails = $tdQuery->orderBy('name')->get();
        }

        // ================= BASE QUERY =================
        $query = StockDetail::query()
            ->where('is_active', true)
            ->whereNotNull('police_station_id')
            ->where('quantity', '>', 0);

        // ================= ROLE & POLICE STATION FILTER =================
        if ($user->hasRole('Admin')) {
            if ($this->policeStationId) {
                $query->where('police_station_id', $this->policeStationId);
            }
        } else {
            $query->where('police_station_id', $user->police_station_id);
        }

        // ================= SEARCH =================
        if ($this->search) {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('type', fn ($t) =>
                    $t->where('name', 'like', "%{$search}%")
                )
                ->orWhereHas('typeDetail', fn ($td) =>
                    $td->where('name', 'like', "%{$search}%")
                )
                ->orWhereHas('rack', fn ($r) =>
                    $r->where('name', 'like', "%{$search}%")
                );
            });
        }

        // ================= TYPE & TYPE DETAIL FILTER =================
        if ($this->typeId) {
            $query->where('type_id', $this->typeId);
        }

        if ($this->typeDetailId) {
            $query->where('type_detail_id', $this->typeDetailId);
        }

        // ================= AKUMULASI SERIAL =================
        $stockDetails = $query
            ->selectRaw('
                type_id,
                type_detail_id,
                rack_id,
                police_station_id,
                code,
                number_serial_first,
                number_serial_second,
                SUM(quantity) as total_quantity
            ')
            ->groupBy(
                'type_id',
                'type_detail_id',
                'rack_id',
                'police_station_id',
                'code',
                'number_serial_first',
                'number_serial_second'
            )
            ->with(['type', 'typeDetail', 'rack', 'policeStation'])
            ->orderBy('type_id')
            ->get();

        // ================= SUMMARY VARS =================
        $totalStock     = $stockDetails->sum('total_quantity');
        $serializedCount = $stockDetails->filter(fn($d) => $d->number_serial_first || $d->number_serial_second)->count();
        $totalDetailRows = $stockDetails->count();

        // ================= GROUP PER TYPE & POLICE STATION =================
        $groupedStocks = $stockDetails
            ->groupBy(function ($item) {
                return $item->police_station_id . '-' . $item->type_id;
            })
            ->map(function ($items) {
                return [
                    'policeStation'  => $items->first()->policeStation,
                    'type'           => $items->first()->type,
                    'total_quantity' => $items->sum('total_quantity'),
                    'details'        => $items,
                ];
            })
            ->values();

        // ================= PAGINATION =================
        $page    = $this->getPage();
        $perPage = $this->perPage;
        $total   = $groupedStocks->count();

        $stocks = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedStocks->slice(($page - 1) * $perPage, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.admin.menu-polres.stock.admin-menu-polres-stock-index', [
            'stocks'          => $stocks,
            'policeStations'  => $policeStations,
            'allTypes'        => $allTypes,
            'typeDetails'     => $typeDetails,
            'totalStock'      => $totalStock,
            'serializedCount' => $serializedCount,
            'totalDetailRows' => $totalDetailRows,
        ])->layout('components.layouts.main.app');
    }

}
