<?php

namespace App\Livewire\Admin\Report\MaterialDamage;

use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class AdminReportMaterialDamageIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    #[Url]
    public $regionalPoliceId = '';

    #[Url]
    public $policeStationId = '';

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    #[Url]
    public $filterStatus = ''; // Renaming consistent with usage but keeping explicit name for clarity if needed

    public $startDate = '';
    public $endDate = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'regionalPoliceId' => ['except' => ''],
        'policeStationId' => ['except' => ''],
        'typeId' => ['except' => ''],
        'typeDetailId' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    public function getDamagesProperty()
    {
        $query = MaterialDamageDetail::query()
            ->with([
                'materialDamage.regionalPolice',
                'materialDamage.policeStation',
                'type',
                'typeDetail'
            ])
            ->where('material_damage_details.is_active', true);

        // Join to filter by material_damages table columns
        $query->join('material_damages', 'material_damage_details.material_damage_id', '=', 'material_damages.id')
              ->select('material_damage_details.*');

        // Role filtering
        if(Auth::user()->hasRole('Polda')) {
            $query->where('material_damages.regional_police_id', Auth::user()->regional_police_id)
                  ->whereNull('material_damages.police_station_id');
        }

        if(Auth::user()->hasRole('Polres')) {
            $query->where('material_damages.police_station_id', Auth::user()->police_station_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('material_damages.code', 'ilike', '%' . $this->search . '%')
                    ->orWhere('material_damages.description', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('materialDamage.regionalPolice', function ($polda) {
                        $polda->where('name', 'ilike', '%' . $this->search . '%');
                    })
                    ->orWhereHas('materialDamage.policeStation', function ($polres) {
                        $polres->where('name', 'ilike', '%' . $this->search . '%');
                    })
                    ->orWhereHas('type', function ($t) {
                        $t->where('name', 'ilike', '%' . $this->search . '%');
                    })
                    ->orWhereHas('typeDetail', function ($td) {
                        $td->where('name', 'ilike', '%' . $this->search . '%');
                    });
            });
        }

        // Filters
        if ($this->regionalPoliceId) {
            $query->where('material_damages.regional_police_id', $this->regionalPoliceId);
        }

        if ($this->policeStationId) {
            $query->where('material_damages.police_station_id', $this->policeStationId);
        }

        if ($this->typeId) {
            $query->where('material_damage_details.type_id', $this->typeId);
        }

        if ($this->typeDetailId) {
            $query->where('material_damage_details.type_detail_id', $this->typeDetailId);
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where('material_damages.status', $this->filterStatus);
        }

        // Date range
        if ($this->startDate) {
            $query->whereDate('material_damages.date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('material_damages.date', '<=', $this->endDate);
        }

        return $query->latest('material_damages.date')->paginate($this->perPage);
    }

    public function getRegionalPolicesProperty()
    {
        return RegionalPolice::where('is_active', true)->orderBy('name')->get();
    }

    public function getPoliceStationsProperty()
    {
        return PoliceStation::where('is_active', true)->orderBy('name')->get();
    }

    public function getAllTypesProperty()
    {
        return Type::orderBy('name')->get();
    }

    public function getStatusesProperty()
    {
        return [
            'reported' => 'Dilaporkan',
            'under_review' => 'Dalam Pemeriksaan',
            'approved' => 'Disetujui',
            'disposed' => 'Dimusnahkan',
        ];
    }

    // Summary statistics
    public function getTotalDamagesProperty()
    {
        // Total rows/items
        return $this->damages->total();
    }

    public function getTotalUnitsProperty()
    {
        // Replicating filter logic for sum
        $query = MaterialDamageDetail::query()
            ->join('material_damages', 'material_damage_details.material_damage_id', '=', 'material_damages.id')
            ->where('material_damage_details.is_active', true);

         if ($this->regionalPoliceId) $query->where('material_damages.regional_police_id', $this->regionalPoliceId);
         if ($this->policeStationId) $query->where('material_damages.police_station_id', $this->policeStationId);
         if ($this->typeId) $query->where('material_damage_details.type_id', $this->typeId);
         if ($this->typeDetailId) $query->where('material_damage_details.type_detail_id', $this->typeDetailId);
         if ($this->filterStatus) $query->where('material_damages.status', $this->filterStatus);
         if ($this->startDate) $query->whereDate('material_damages.date', '>=', $this->startDate);
         if ($this->endDate) $query->whereDate('material_damages.date', '<=', $this->endDate);

         return $query->sum('material_damage_details.quantity');
    }

    public function getTodayDamagesProperty()
    {
        return MaterialDamageDetail::where('is_active', true)
             ->whereHas('materialDamage', function($q) {
                $q->whereDate('date', today());
            })
            ->count();
    }

    public function updatedSearch()
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
        $this->typeDetailId = '';
    }

    public function updatedTypeDetailId()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $typeDetails = [];
        if ($this->typeId) {
            $typeDetails = TypeDetail::where('type_id', $this->typeId)->orderBy('name')->get();
        } else {
            $typeDetails = TypeDetail::orderBy('name')->get();
        }

        return view('livewire.admin.report.material-damage.admin-report-material-damage-index', [
            'damages' => $this->damages,
            'typeDetails' => $typeDetails,
        ])
            ->layout('components.layouts.main.app');
    }
}
