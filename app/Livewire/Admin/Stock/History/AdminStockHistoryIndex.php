<?php

namespace App\Livewire\Admin\Stock\History;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock\HistoryStock;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Police\RegionalPolice;
use App\Models\Police\PoliceStation;
use Livewire\Attributes\Url;

class AdminStockHistoryIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    #[Url]
    public $statusType = '';

    #[Url]
    public $regionalPoliceId = '';

    #[Url]
    public $policeStationId = '';

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusType' => ['except' => ''],
        'regionalPoliceId' => ['except' => ''],
        'policeStationId' => ['except' => ''],
        'typeId' => ['except' => ''],
        'typeDetailId' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusType()
    {
        $this->resetPage();
    }

    public function updatedRegionalPoliceId()
    {
        $this->resetPage();
    }

    public function updatedPoliceStationId()
    {
        $this->resetPage();
    }

    public function updatedTypeId()
    {
        $this->resetPage();
    }

    public function updatedTypeDetailId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        // Load filter options
        $regionalPolices = [];
        if ($user->hasRole('Admin')) {
            $regionalPolices = RegionalPolice::orderBy('name')->get();
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

        $query = HistoryStock::with(['lastStock', 'lastStockDetail', 'type', 'typeDetail', 'regionalPolice', 'policeStation', 'rack'])
            ->where('is_active', true);

        if ($user->userType && !empty($user->userType->types)) {
            $query->whereIn('type_id', $user->userType->types);
        }

        // Role-based filtering & Police Station Filter
        if ($user->hasRole('Polda')) {
            $query->where('regional_police_id', $user->regional_police_id)->whereNull('police_station_id');
        } elseif ($user->hasRole('Polres')) {
            $query->where('police_station_id', $user->police_station_id);
        } elseif ($user->hasRole('Admin')) {
            if ($this->regionalPoliceId) {
                $query->where('regional_police_id', $this->regionalPoliceId);
            }
            if ($this->policeStationId) {
                $query->where('police_station_id', $this->policeStationId);
            }
        }

        // Filter by Type ID
        if ($this->typeId) {
            $query->where('type_id', $this->typeId);
        }

        // Filter by Type Detail ID
        if ($this->typeDetailId) {
            $query->where('type_detail_id', $this->typeDetailId);
        }

        // Filter by History Type (in/out/last/first)
        if ($this->statusType) {
            $query->where('status_type', $this->statusType);
        }

        // Search
        if ($this->search) {
             $keywords = preg_split('/\s+/', trim($this->search));
             $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word) {
                        $sub->where('code', 'ilike', "%{$word}%")
                            ->orWhereHas('type', function ($tq) use ($word) {
                                $tq->where('name', 'ilike', "%{$word}%");
                            })
                            ->orWhereHas('typeDetail', function ($tdq) use ($word) {
                                $tdq->where('name', 'ilike', "%{$word}%");
                            })
                            ->orWhere('description', 'ilike', "%{$word}%")
                            ->orWhere('serial_number', 'ilike', "%{$word}%");
                    });
                }
            });
        }

        $historyStocks = $query->latest()->paginate($this->perPage);

        return view('livewire.admin.stock.history.admin-stock-history-index', [
            'historyStocks' => $historyStocks,
            'regionalPolices' => $regionalPolices,
            'policeStations' => $policeStations,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
        ])->layout('components.layouts.main.app');
    }
}
