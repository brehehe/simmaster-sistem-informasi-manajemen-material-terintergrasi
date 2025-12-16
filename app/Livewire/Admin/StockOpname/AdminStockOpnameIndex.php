<?php

namespace App\Livewire\Admin\StockOpname;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\StockOpname\StockOpname;
use Livewire\Component;
use Livewire\WithPagination;

class AdminStockOpnameIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $ownerTypeFilter = ''; // 'polda', 'polres', or ''
    public $startDate = '';
    public $endDate = '';
    public $showDeleteModal = false;
    public $deleteId = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'ownerTypeFilter' => ['except' => ''],
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

    public function updatingOwnerTypeFilter()
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
        $query = StockOpname::with(['regionalPolice', 'policeStation', 'checkedByUser', 'approvedByUser'])
            ->orderBy('created_at', 'desc');

        // Search by code
        if ($this->search) {
            $query->where('code', 'like', '%' . $this->search . '%');
        }

        // Filter by status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Filter by owner type
        if ($this->ownerTypeFilter === 'polda') {
            $query->whereNotNull('regional_police_id')
                ->whereNull('police_station_id');
        } elseif ($this->ownerTypeFilter === 'polres') {
            $query->whereNull('regional_police_id')
                ->whereNotNull('police_station_id');
        }

        // Date range filter
        if ($this->startDate) {
            $query->whereDate('opname_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('opname_date', '<=', $this->endDate);
        }

        $opnames = $query->paginate(10);

        return view('livewire.admin.stock-opname.admin-stock-opname-index', [
            'opnames' => $opnames,
        ])->layout('components.layouts.main.app');
    }
}
