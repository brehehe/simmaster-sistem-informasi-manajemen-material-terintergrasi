<?php

namespace App\Livewire\Admin\Report\StockOpname;

use App\Models\StockOpname\StockOpname;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use Livewire\Component;
use Livewire\WithPagination;

class AdminReportStockOpnameIndex extends Component
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

    public function getOpnamesProperty()
    {
        $query = StockOpname::with([
            'regionalPolice',
            'policeStation',
            'stockOpnameDetails.typeDetail'
        ])
            ->where('is_active', true);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%')
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
            $query->whereDate('opname_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('opname_date', '<=', $this->endDate);
        }

        return $query->latest('opname_date')->paginate($this->perPage);
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
            'draft' => 'Draft',
            'completed' => 'Selesai',
            'approved' => 'Disetujui',
        ];
    }

    // Summary statistics
    public function getTotalOpnamesProperty()
    {
        return $this->opnames->total();
    }

    public function getCompletedCountProperty()
    {
        return StockOpname::where('is_active', true)
            ->where('status', 'completed')
            ->when($this->startDate, fn($q) => $q->whereDate('opname_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('opname_date', '<=', $this->endDate))
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
        return view('livewire.admin.report.stock-opname.admin-report-stock-opname-index', [
            'opnames' => $this->opnames,
        ])
            ->layout('components.layouts.main.app');
    }
}
