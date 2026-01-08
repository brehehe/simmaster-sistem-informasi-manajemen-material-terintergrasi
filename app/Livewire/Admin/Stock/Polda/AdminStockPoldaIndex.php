<?php

namespace App\Livewire\Admin\Stock\Polda;

use App\Models\Police\RegionalPolice;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class AdminStockPoldaIndex extends Component
{
    use WithPagination;

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    #[Url]
    public $regionalPoliceId = '';

    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'typeId' => ['except' => ''],
        'typeDetailId' => ['except' => ''],
        'regionalPoliceId' => ['except' => ''],
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

    public function updatingRegionalPoliceId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        // Load filter options
        $regionalPolices = [];
        if ($user->hasRole('Admin')) {
            $regionalPolices = RegionalPolice::orderBy('name')->get();
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
            ->whereNotNull('regional_police_id');

        // ================= ROLE & POLICE STATION FILTER =================
        if ($user->hasRole('Admin')) {
             if ($this->regionalPoliceId) {
                $query->where('regional_police_id', $this->regionalPoliceId);
            }
        } else {
            $query->where('regional_police_id', $user->regional_police_id);
        }

        if ($user->userType && !empty($user->userType->types)) {
            $query->whereIn('type_id', $user->userType->types);
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
                regional_police_id,
                code,
                number_serial_first,
                number_serial_second,
                SUM(quantity) as total_quantity
            ')
            ->groupBy(
                'type_id',
                'type_detail_id',
                'rack_id',
                'regional_police_id',
                'code',
                'number_serial_first',
                'number_serial_second'
            )
            ->with(['type', 'typeDetail', 'rack', 'regionalPolice'])
            ->orderBy('type_id')
            ->get();

        // ================= GROUP PER TYPE & POLICE STATION =================
        $groupedStocks = $stockDetails
            ->groupBy(function ($item) {
                return $item->regional_police_id . '-' . $item->type_id;
            })
            ->map(function ($items) {
                return [
                    'regionalPolice'  => $items->first()->regionalPolice,
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

        return view('livewire.admin.stock.polda.admin-stock-polda-index', [
            'stocks'      => $stocks,
            'regionalPolices' => $regionalPolices,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
        ])->layout('components.layouts.main.app');
    }

}
