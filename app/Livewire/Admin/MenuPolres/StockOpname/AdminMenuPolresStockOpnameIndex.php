<?php

namespace App\Livewire\Admin\MenuPolres\StockOpname;

use App\Models\StockOpname\StockOpname;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPolresStockOpnameIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $startDate = '';
    public $endDate = '';
    public $showDeleteModal = false;
    public $deleteId = '';

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
        $query = StockOpname::with(['policeStation', 'checkedByUser', 'approvedByUser'])
            ->where('police_station_id', auth()->user()->police_station_id)
            ->orderBy('created_at', 'desc');

        // Search by code
        if ($this->search) {
            $query->where('code', 'like', '%' . $this->search . '%');
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
        ])->layout('components.layouts.main.app');
    }
}
