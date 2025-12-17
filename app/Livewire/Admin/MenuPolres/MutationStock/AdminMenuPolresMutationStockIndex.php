<?php

namespace App\Livewire\Admin\MenuPolres\MutationStock;

use App\Models\Models\MenuPolda\MutationStock\MutationStock;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPolresMutationStockIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public string $statusFilter = '';
    public int $perPage = 10;
    public bool $showDeleteModal = false;
    public ?string $mutationId = null;

    public function render()
    {
        $user = auth()->user();

        $query = MutationStock::with([
            'senderRegionalPolice',
            'senderPoliceStation',
            'receiverRegionalPolice',
            'receiverPoliceStation',
            'mutationStockDetails'
        ])->where('is_active', true);

        // Filter: current police station as sender OR receiver
        $query->where(function ($q) use ($user) {
            $q->where('sender_police_station_id', $user->police_station_id);
        });

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Date filtering
        if ($this->startDate) {
            $query->whereDate('mutation_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('mutation_date', '<=', $this->endDate);
        }

        $mutations = $query->latest('mutation_date')->paginate($this->perPage);

        return view('livewire.admin.menu-polres.mutation-stock.admin-menu-polres-mutation-stock-index', [
            'mutations' => $mutations,
        ])->layout('components.layouts.main.app');
    }

    public function openDeleteModal($id)
    {
        $this->mutationId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->mutationId = null;
    }

    public function delete()
    {
        if ($this->mutationId) {
            $mutation = MutationStock::find($this->mutationId);
            if ($mutation && $mutation->status === 'draft') {
                $mutation->delete();
                session()->flash('success', 'Mutasi stock berhasil dihapus.');
            } else {
                session()->flash('error', 'Hanya mutasi dengan status draft yang bisa dihapus.');
            }
        }
        $this->closeModal();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
}
