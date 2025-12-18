<?php

namespace App\Livewire\Admin\Report\ReceptionRegionalPolice;

use App\Models\Reception\Reception;
use App\Models\Police\RegionalPolice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminReportReceptionRegionalPoliceIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterPolda = '';
    public $startDate = '';
    public $endDate = '';

    public function getReceptionsProperty()
    {
        $query = Reception::with([
            'regionalPolice',
            'policeStation',
            'receptionDetails.typeDetail'
        ])
            ->where('is_active', true);

        // Role-based filtering
        if (Auth::user()->hasRole('Polda')) {
            $query->where('regional_police_id', Auth::user()->regional_police_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('regionalPolice', function ($polda) {
                        $polda->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Polda filter
        if ($this->filterPolda) {
            $query->where('regional_police_id', $this->filterPolda);
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

    public function getRegionalPolicesProperty()
    {
        return RegionalPolice::where('is_active', true)->orderBy('name')->get();
    }

    // Summary statistics
    public function getTotalReceptionsProperty()
    {
        return $this->receptions->total();
    }

    public function getTotalItemsProperty()
    {
        $receptionIds = $this->receptions->pluck('id');

        return \DB::table('reception_details')
            ->whereIn('reception_id', $receptionIds)
            ->sum('quantity');
    }

    public function getTodayReceptionsProperty()
    {
        $query = Reception::where('is_active', true)
            ->whereDate('date', today());

        if (Auth::user()->hasRole('Polda')) {
            $query->where('regional_police_id', Auth::user()->regional_police_id);
        }

        return $query->count();
    }

    public function getThisMonthReceptionsProperty()
    {
        $query = Reception::where('is_active', true)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);

        if (Auth::user()->hasRole('Polda')) {
            $query->where('regional_police_id', Auth::user()->regional_police_id);
        }

        return $query->count();
    }

public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterPolda()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.report.reception-regional-police.admin-report-reception-regional-police-index', [
            'receptions' => $this->receptions,
        ])
            ->layout('components.layouts.main.app');
    }
}
