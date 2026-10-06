<?php

namespace App\Livewire\Admin\MenuPolres\MutationStock;

use App\Livewire\Concerns\AuthorizesPolresData;
use App\Models\MenuPolda\MutationStock\MutationStock;
use App\Models\Police\PoliceStation;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPolresMutationStockIndex extends Component
{
    use AuthorizesPolresData;
    use WithPagination;

    public string $search = '';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public string $statusFilter = '';

    public string $policeStationId = '';

    public int $perPage = 10;

    public bool $showDeleteModal = false;

    public ?string $mutationId = null;

    public function boot(): void
    {
        $this->authorizePolresMenu();
    }

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    public function render()
    {
        $user = auth()->user();

        $query = MutationStock::with([
            'senderRegionalPolice',
            'senderPoliceStation',
            'receiverRegionalPolice',
            'receiverPoliceStation',
            'mutationStockDetails',
        ])->where('is_active', true);

        if ($user->hasRole('Admin')) {
            if ($this->policeStationId) {
                $query->where('sender_police_station_id', $this->policeStationId);
            }
        } else {
            $query->where('sender_police_station_id', $user->police_station_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'ilike', '%'.$this->search.'%')
                    ->orWhere('notes', 'ilike', '%'.$this->search.'%');
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
            'policeStations' => $user->hasRole('Admin')
                ? PoliceStation::where('is_active', true)->orderBy('name')->get()
                : collect(),
        ])->layout('components.layouts.main.app');
    }

    public function openDeleteModal($id)
    {
        $mutation = MutationStock::findOrFail($id);
        $this->authorizePoliceStation($mutation->sender_police_station_id);
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
            if ($mutation) {
                $this->authorizePoliceStation($mutation->sender_police_station_id);
            }
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
