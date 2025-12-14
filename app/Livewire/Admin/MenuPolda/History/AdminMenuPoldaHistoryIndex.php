<?php

namespace App\Livewire\Admin\MenuPolda\History;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock\HistoryStock;
use App\Models\Type\Type;
use App\Models\Police\RegionalPolice;
use App\Models\Police\PoliceStation;

class AdminMenuPoldaHistoryIndex extends Component
{
     use WithPagination;

    public $search = '';
    public $filterStatusType = '';
    public $filterTypeId = '';
    public $filterRegionalPoliceId = '';
    public $filterPoliceStationId = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatusType' => ['except' => ''],
        'filterTypeId' => ['except' => ''],
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

        return view('livewire.admin.menu-polda.history.admin-menu-polda-history-index', [
            'historyStocks' => $historyStocks,
            'types' => $types,
        ])->layout('components.layouts.main.app');
    }
}
