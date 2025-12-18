<?php

namespace App\Livewire\Admin\Report\MaterialDamage;

use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AdminReportMaterialDamageIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterLocation = '';
    public $filterStatus = '';
    public $startDate = '';
    public $endDate = '';

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    public function getDamagesProperty()
    {
        $query = MaterialDamage::with([
            'regionalPolice',
            'policeStation',
            'materialDamageDetails.typeDetail'
        ])
            ->where('is_active', true);

        if(Auth::user()->hasRole('Polda')) {
            $query->where('regional_police_id', Auth::user()->regional_police_id)->whereNull('police_station_id');
        }

        if(Auth::user()->hasRole('Polres')) {
            $query->where('police_station_id', Auth::user()->police_station_id);
        }

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

        // Location filter
        if ($this->filterLocation) {
            $query->where(function ($q) {
                $q->where('regional_police_id', $this->filterLocation)
                    ->orWhere('police_station_id', $this->filterLocation);
            });
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Date range
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
        return $this->damages->total();
    }

    public function getTotalUnitsProperty()
    {
        $damageIds = $this->damages->pluck('id');

        return \DB::table('material_damage_details')
            ->whereIn('material_damage_id', $damageIds)
            ->sum('quantity');
    }

    public function getTodayDamagesProperty()
    {
        return MaterialDamage::where('is_active', true)
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

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.report.material-damage.admin-report-material-damage-index', [
            'damages' => $this->damages,
        ])
            ->layout('components.layouts.main.app');
    }
}
