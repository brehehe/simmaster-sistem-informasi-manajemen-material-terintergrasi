<?php

namespace App\Livewire\Admin\MenuPolres\StockOpname;

use App\Livewire\Concerns\AuthorizesPolresData;
use App\Models\Police\PoliceStation;
use App\Models\StockOpname\StockOpname;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPolresStockOpnameIndex extends Component
{
    use AuthorizesPolresData;
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $policeStationId = '';

    public $startDate = '';

    public $endDate = '';

    public $showDeleteModal = false;

    public $deleteId = '';

    public function boot(): void
    {
        $this->authorizePolresMenu();
    }

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openDeleteModal($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = '';
    }

    public function delete()
    {
        $opname = StockOpname::find($this->deleteId);
        if ($opname) {
            $this->authorizePoliceStation($opname->police_station_id);
        }

        if ($opname && $opname->status === 'draft') {
            $opname->delete();
            session()->flash('success', 'Stock opname berhasil dihapus.');
        } else {
            session()->flash('error', 'Hanya stock opname dengan status draft yang bisa dihapus.');
        }

        $this->closeModal();
    }

    public function render()
    {
        $user = auth()->user();
        $query = StockOpname::with(['policeStation', 'checkedByUser', 'approvedByUser'])
            ->whereNotNull('police_station_id')
            ->orderBy('created_at', 'desc');

        if ($user->hasRole('Admin')) {
            if ($this->policeStationId) {
                $query->where('police_station_id', $this->policeStationId);
            }
        } else {
            $query->where('police_station_id', $user->police_station_id);
        }

        // Search by code
        if ($this->search) {
            $query->where('code', 'ilike', '%'.$this->search.'%');
        }

        // Filter by status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Date range filter
        if ($this->startDate) {
            $query->whereDate('opname_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('opname_date', '<=', $this->endDate);
        }

        $opnames = $query->paginate(10);

        return view('livewire.admin.menu-polres.stock-opname.admin-menu-polres-stock-opname-index', [
            'opnames' => $opnames,
            'policeStations' => $user->hasRole('Admin')
                ? PoliceStation::where('is_active', true)->orderBy('name')->get()
                : collect(),
        ])->layout('components.layouts.main.app');
    }
}
