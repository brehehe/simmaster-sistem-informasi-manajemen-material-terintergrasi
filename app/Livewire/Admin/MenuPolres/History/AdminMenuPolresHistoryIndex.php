<?php

namespace App\Livewire\Admin\MenuPolres\History;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock\HistoryStock;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Police\RegionalPolice;
use App\Models\Police\PoliceStation;

class AdminMenuPolresHistoryIndex extends Component
{
     use WithPagination;

    public $search = '';
    public $filterStatusType = '';
    public $filterTypeId = '';
    public $filterTypeDetailId = '';
    public $filterRegionalPoliceId = '';
    public $filterPoliceStationId = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatusType' => ['except' => ''],
        'filterTypeId' => ['except' => ''],
        'filterTypeDetailId' => ['except' => ''],
        'filterRegionalPoliceId' => ['except' => ''],
        'filterPoliceStationId' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterTypeId()
    {
        $this->resetPage();
    }

    public function updatingFilterTypeDetailId()
    {
        $this->resetPage();
    }

    public function updatingFilterRegionalPoliceId()
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

        $query = HistoryStock::with(['lastStock', 'lastStockDetail', 'type', 'typeDetail', 'regionalPolice', 'policeStation', 'rack'])
            ->where('is_active', true);

        // Filter by Type Detail ID
        if ($this->filterTypeDetailId) {
            $query->where('type_detail_id', $this->filterTypeDetailId);
        }

        // Role-based filtering
        if ($user->hasRole('Polda')) {
            $query->where('regional_police_id', $user->regional_police_id)->whereNull('police_station_id');
        } elseif ($user->hasRole('Polres')) {
            $query->where('police_station_id', $user->police_station_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                ->orWhereHas('type', function ($tq) {
                    $tq->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('typeDetail', function ($tdq) {
                    $tdq->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Filter by History Type (in/out/last/first)
        if ($this->filterStatusType) {
            $query->where('status_type', $this->filterStatusType);
        }

        // Filter by Type ID
        if ($this->filterTypeId) {
            $query->where('type_id', $this->filterTypeId);
        }

        $historyStocks = $query->latest()->paginate($this->perPage);

        // Dropdown data
        $types = Type::where('is_active', true)->orderBy('name')->get();
        $typeDetails = $this->filterTypeId ? TypeDetail::where('type_id', $this->filterTypeId)->where('is_active', true)->orderBy('name')->get() : [];

        return view('livewire.admin.menu-polres.history.admin-menu-polres-history-index', [
            'historyStocks' => $historyStocks,
            'types' => $types,
            'typeDetails' => $typeDetails,
        ])->layout('components.layouts.main.app');
    }
}
