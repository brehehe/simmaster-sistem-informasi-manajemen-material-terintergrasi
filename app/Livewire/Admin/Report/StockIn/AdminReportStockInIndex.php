<?php

namespace App\Livewire\Admin\Report\StockIn;

use App\Models\Stock\HistoryStock;
use App\Models\Type\Type;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AdminReportStockInIndex extends Component
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

    public function getStockInsProperty()
    {
        $query = HistoryStock::with([
            'type',
            'typeDetail',
            'regionalPolice',
            'policeStation',
            'rack'
        ])
            ->where('is_active', true)
            ->where('status_type', 'in'); // Only incoming stock

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
        return $this->stockIns->total();
    }

    public function getTotalUnitsProperty()
    {
        return HistoryStock::where('is_active', true)
            ->where('status_type', 'in')
            ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
            ->sum('quantity');
    }

    public function getTodayTransactionsProperty()
    {
        return HistoryStock::where('is_active', true)
            ->where('status_type', 'in')
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
        return view('livewire.admin.report.stock-in.admin-report-stock-in-index', [
            'stockIns' => $this->stockIns,
        ])
            ->layout('components.layouts.main.app');
    }
}
