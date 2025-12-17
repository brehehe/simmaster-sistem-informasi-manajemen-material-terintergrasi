<?php

namespace App\Livewire\Admin\MenuPolres\LastStock;

use App\Models\LastStock\LastStock;
use App\Models\Police\PoliceStation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPolresLastStockIndex extends Component
{
    use WithPagination;

    // Search & Filter
    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $perPage = 10;

    // Delete Modal
    public bool $showDeleteModal = false;
    public ?string $lastStockId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
        'perPage' => ['except' => 10],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function openDeleteModal($id)
    {
        $this->lastStockId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->lastStockId = null;
    }

    public function delete()
    {
        try {
            $lastStock = LastStock::findOrFail($this->lastStockId);

            // Delete all details first
            $lastStock->lastStockDetails()->delete();

            // Then delete the main record
            $lastStock->delete();

            session()->flash('success', 'Data stok terakhir berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();

        $lastStocks = LastStock::query()
            ->with(['regionalPolice', 'policeStation'])
            ->withCount('lastStockDetails')
            // Role-based filtering
            ->when($user->hasRole('Polres'), function ($query) use ($user) {
                $query->where('police_station_id', $user->police_station_id);
            })
            // Search
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            // Date filter
            ->when($this->startDate, function ($query) {
                $query->whereDate('date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('date', '<=', $this->endDate);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.menu-polres.last-stock.admin-menu-polres-last-stock-index', [
            'lastStocks' => $lastStocks,
        ])->layout('components.layouts.main.app');
    }
}
