<?php

namespace App\Livewire\Admin\Report\StockOut;

use App\Models\Stock\HistoryStock;
use App\Models\Type\Type;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AdminReportStockOutIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterType = '';
    public $startDate = '';
    public $endDate = '';

public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }   

    public function getStockOutsProperty()
    {
        $query = HistoryStock::with([
            'type',
            'typeDetail',
            'regionalPolice',
            'policeStation',
            'rack'
        ])
            ->where('is_active', true)
            ->where('status_type', 'out'); // Only outgoing stock

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
                    ->orWhere('serial_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('type', function ($type) {
                        $type->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Type filter
        if ($this->filterType) {
            $query->where('type_id', $this->filterType);
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

    public function getTypesProperty()
    {
        return Type::where('is_active', true)->orderBy('name')->get();
    }

    // Summary statistics
    public function getTotalTransactionsProperty()
    {
        return $this->stockOuts->total();
    }

    public function getTotalUnitsProperty()
    {
        // Sum absolute value karena quantity negative untuk 'out'
        return abs(HistoryStock::where('is_active', true)
            ->where('status_type', 'out')
            ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
            ->sum('quantity'));
    }

    public function getTodayTransactionsProperty()
    {
        return HistoryStock::where('is_active', true)
            ->where('status_type', 'out')
            ->whereDate('date', today())
            ->count();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.report.stock-out.admin-report-stock-out-index', [
            'stockOuts' => $this->stockOuts,
        ])
            ->layout('components.layouts.main.app');
    }
}
