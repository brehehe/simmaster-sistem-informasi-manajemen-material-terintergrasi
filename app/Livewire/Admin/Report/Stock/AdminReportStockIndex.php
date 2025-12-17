<?php

namespace App\Livewire\Admin\Report\Stock;

use App\Models\Stock\Stock;
use App\Models\Type\Type;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AdminReportStockIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterType = '';

    public function getStocksProperty()
    {
        $query = Stock::with([
            'type',
            'typeDetail',
            'regionalPolice',
            'policeStation'
        ])
            ->where('is_active', true)
            ->where('quantity', '>', 0); // Only show stocks with quantity

        if(Auth::user()->hasRole('Polda')) {
            $query->where('regional_police_id', Auth::user()->regional_police_id)->whereNull('police_station_id');
        }

        if(Auth::user()->hasRole('Polres')) {
            $query->where('police_station_id', Auth::user()->police_station_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('type', function ($type) {
                        $type->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('type Detail', function ($detail) {
                        $detail->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('regionalPolice', function ($polda) {
                        $polda->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('policeStation', function ($polres) {
                        $polres->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Type filter
        if ($this->filterType) {
            $query->where('type_id', $this->filterType);
        }

        return $query->orderBy('quantity', 'desc')->paginate($this->perPage);
    }

    public function getTypesProperty()
    {
        return Type::where('is_active', true)->orderBy('name')->get();
    }

    // Summary statistics
    public function getTotalItemsProperty()
    {
        return Stock::where('is_active', true)
            ->where('quantity', '>', 0)
            ->count();
    }

    public function getTotalUnitsProperty()
    {
        return Stock::where('is_active', true)
            ->sum('quantity');
    }

    public function getTotalTypesProperty()
    {
        return Stock::where('is_active', true)
            ->where('quantity', '>', 0)
            ->distinct('type_id')
            ->count('type_id');
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
        return view('livewire.admin.report.stock.admin-report-stock-index', [
            'stocks' => $this->stocks,
        ])
            ->layout('components.layouts.main.app');
    }
}
