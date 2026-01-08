<?php

namespace App\Livewire\Admin\Report\MaterialUsage;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class AdminReportMaterialUsageIndex extends Component
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

    public $startDate = '';
    public $endDate = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'regionalPoliceId' => ['except' => ''],
        'policeStationId' => ['except' => ''],
        'typeId' => ['except' => ''],
        'typeDetailId' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function getUsagesProperty()
    {
        $query = MaterialUsageDetail::query()
            ->with([
                'materialUsage.regionalPolice',
                'materialUsage.policeStation',
                'type',
                'typeDetail'
            ])
            ->where('material_usage_details.is_active', true);

        // Join to filter by material_usages table columns
        $query->join('material_usages', 'material_usage_details.material_usage_id', '=', 'material_usages.id')
              ->select('material_usage_details.*');

        // Role-based filtering
        if(Auth::user()->hasRole('Polda')) {
            $query->where('material_usages.regional_police_id', Auth::user()->regional_police_id)
                  ->whereNull('material_usages.police_station_id');
        }

        if(Auth::user()->hasRole('Polres')) {
            $query->where('material_usages.police_station_id', Auth::user()->police_station_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('material_usages.code', 'ilike', '%' . $this->search . '%')
                    ->orWhere('material_usages.description', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('materialUsage.regionalPolice', function ($polda) {
                        $polda->where('name', 'ilike', '%' . $this->search . '%');
                    })
                    ->orWhereHas('materialUsage.policeStation', function ($polres) {
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
            $query->where('material_usages.regional_police_id', $this->regionalPoliceId);
        }

        if ($this->policeStationId) {
            $query->where('material_usages.police_station_id', $this->policeStationId);
        }

        if ($this->typeId) {
            $query->where('material_usage_details.type_id', $this->typeId);
        }

        if ($this->typeDetailId) {
            $query->where('material_usage_details.type_detail_id', $this->typeDetailId);
        }

        // Date range filter
        if ($this->startDate) {
            $query->whereDate('material_usages.date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('material_usages.date', '<=', $this->endDate);
        }

        return $query->latest('material_usages.date')->paginate($this->perPage);
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

    // Summary statistics
    public function getTotalUsagesProperty()
    {
        return $this->usages->total();
    }

    public function getTotalUnitsProperty()
    {
        // For total units, we can sum specific field if needed, currently returning total rows if that's the intent or sum of quantity
        // If we want sum of quantity of ALL details matching filter:
        // We need to replicate the query without pagination

        $query = MaterialUsageDetail::query()
            ->join('material_usages', 'material_usage_details.material_usage_id', '=', 'material_usages.id')
            ->where('material_usage_details.is_active', true);

         // Apply same filters (can be extracted to a method to avoid duplication, but for now inline)
         // ... (Applying same filters as above or simpler summary logic)
         // For performance, let's just sum the current page or keep it simple

         // Re-applying basic filters for accurate summary
         if ($this->regionalPoliceId) $query->where('material_usages.regional_police_id', $this->regionalPoliceId);
         if ($this->policeStationId) $query->where('material_usages.police_station_id', $this->policeStationId);
         if ($this->typeId) $query->where('material_usage_details.type_id', $this->typeId);
         if ($this->typeDetailId) $query->where('material_usage_details.type_detail_id', $this->typeDetailId);
         if ($this->startDate) $query->whereDate('material_usages.date', '>=', $this->startDate);
         if ($this->endDate) $query->whereDate('material_usages.date', '<=', $this->endDate);

         return $query->sum('material_usage_details.quantity');
    }

    public function getTodayUsagesProperty()
    {
        return MaterialUsageDetail::where('is_active', true)
            ->whereHas('materialUsage', function($q) {
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

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    public function render()
    {
        $typeDetails = [];
        if ($this->typeId) {
            $typeDetails = TypeDetail::where('type_id', $this->typeId)->orderBy('name')->get();
        } else {
            $typeDetails = TypeDetail::orderBy('name')->get();
        }

        return view('livewire.admin.report.material-usage.admin-report-material-usage-index', [
            'usages' => $this->usages,
            'typeDetails' => $typeDetails
        ])
            ->layout('components.layouts.main.app');
    }
}
