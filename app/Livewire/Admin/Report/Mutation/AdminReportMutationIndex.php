<?php

namespace App\Livewire\Admin\Report\Mutation;

use App\Models\Models\MenuPolda\MutationStock\MutationStock;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AdminReportMutationIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterStatus = '';
    public $startDate = '';
    public $endDate = '';

public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    public function getMutationsProperty()
    {
        $query = MutationStock::with([
            'senderRegionalPolice',
            'senderPoliceStation',
            'receiverRegionalPolice',
            'receiverPoliceStation',
            'mutationStockDetails.typeDetail'
        ])
            ->where('is_active', true);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%')
                    ->orWhereHas('senderRegionalPolice', function ($polda) {
                        $polda->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('senderPoliceStation', function ($polres) {
                        $polres->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('receiverRegionalPolice', function ($polda) {
                        $polda->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('receiverPoliceStation', function ($polres) {
                        $polres->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if(Auth::user()->hasRole('Polda')) {
            $query->where('regional_police_id', Auth::user()->regional_police_id)->whereNull('police_station_id');
        }

        if(Auth::user()->hasRole('Polres')) {
            $query->where('police_station_id', Auth::user()->police_station_id);
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Date range
        if ($this->startDate) {
            $query->whereDate('mutation_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('mutation_date', '<=', $this->endDate);
        }

        return $query->latest('mutation_date')->paginate($this->perPage);
    }

    public function getStatusesProperty()
    {
        return [
            'draft' => 'Draft',
            'sent' => 'Dikirim',
            'received' => 'Diterima',
            'rejected' => 'Ditolak',
        ];
    }

    // Summary statistics
    public function getTotalMutationsProperty()
    {
        return $this->mutations->total();
    }

    public function getSentCountProperty()
    {
        return MutationStock::where('is_active', true)
            ->where('status', 'sent')
            ->when($this->startDate, fn($q) => $q->whereDate('mutation_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('mutation_date', '<=', $this->endDate))
            ->count();
    }

    public function getReceivedCountProperty()
    {
        return MutationStock::where('is_active', true)
            ->where('status', 'received')
            ->when($this->startDate, fn($q) => $q->whereDate('mutation_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('mutation_date', '<=', $this->endDate))
            ->count();
    }

    public function getTotalUnitsProperty()
    {
        $mutationIds = $this->mutations->pluck('id');

        return \DB::table('mutation_stock_details')
            ->whereIn('mutation_stock_id', $mutationIds)
            ->sum('quantity');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.report.mutation.admin-report-mutation-index', [
            'mutations' => $this->mutations,
        ])
            ->layout('components.layouts.main.app');
    }
}
