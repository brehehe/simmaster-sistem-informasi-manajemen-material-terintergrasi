<?php

namespace App\Livewire\Admin\Report\MaterialUsage;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AdminReportMaterialUsageIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterLocation = '';
    public $filterMaterial = '';
    public $startDate = '';
    public $endDate = '';

    public function getUsagesProperty()
    {
        $query = MaterialUsage::with([
            'regionalPolice',
            'policeStation',
            'materialUsageDetails.typeDetail'
        ])
            ->where('is_active', true);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('regionalPolice', function ($polda) {
                        $polda->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('policeStation', function ($polres) {
                        $polres->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Location filter (Polda or Polres)
        if ($this->filterLocation) {
            $query->where(function ($q) {
                $q->where('regional_police_id', $this->filterLocation)
                    ->orWhere('police_station_id', $this->filterLocation);
            });
        }

        if(Auth::user()->hasRole('Polda')) {
            $query->where('regional_police_id', Auth::user()->regional_police_id)->whereNull('police_station_id');
        }

        if(Auth::user()->hasRole('Polres')) {
            $query->where('police_station_id', Auth::user()->police_station_id);
        }

        // Date range filter
        if ($this->startDate) {
            $query->whereDate('date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        return $query->latest('date')->paginate($this->perPage);
    }

    public function getLocationsProperty()
    {
        $poldas = RegionalPolice::where('is_active', true)->get();
        $polres = PoliceStation::where('is_active', true)->get();

        return $poldas->concat($polres)->sortBy('name');
    }

    // Summary statistics
    public function getTotalUsagesProperty()
    {
        return $this->usages->total();
    }

    public function getTotalUnitsProperty()
    {
        $usageIds = $this->usages->pluck('id');

        return \DB::table('material_usage_details')
            ->whereIn('material_usage_id', $usageIds)
            ->sum('quantity');
    }

    public function getTodayUsagesProperty()
    {
        return MaterialUsage::where('is_active', true)
            ->whereDate('date', today())
            ->count();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterLocation()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.report.material-usage.admin-report-material-usage-index', [
            'usages' => $this->usages,
        ])
            ->layout('components.layouts.main.app');
    }
}
