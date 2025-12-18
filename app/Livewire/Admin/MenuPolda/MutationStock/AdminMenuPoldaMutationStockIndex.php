<?php

namespace App\Livewire\Admin\MenuPolda\MutationStock;

use App\Models\Models\MenuPolda\MutationStock\MutationStock;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPoldaMutationStockIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public string $statusFilter = '';
    public string $locationFilter = '';
    public int $perPage = 10;
    public bool $showDeleteModal = false;
    public ?string $mutationId = null;

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
            'mutationStockDetails'
        ])->where('is_active', true);

        // Role-based filtering
        if ($user->hasRole('Polda')) {
            // Polda sees mutations they sent or will receive
            $query->where(function ($q) use ($user) {
                $q->where('sender_regional_police_id', $user->regional_police_id)
                    ->orWhere('receiver_regional_police_id', $user->regional_police_id);
            });
        } elseif ($user->hasRole('Polres')) {
            // Polres sees mutations they sent or will receive
            $query->where(function ($q) use ($user) {
                $q->where('sender_police_station_id', $user->police_station_id)
                    ->orWhere('receiver_police_station_id', $user->police_station_id);
            });
        }

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

        // Location filter (either sender or receiver)
        if ($this->locationFilter) {
            $query->where(function ($q) {
                $q->where('sender_regional_police_id', $this->locationFilter);
            });
        }

        // Date filtering
        if ($this->startDate) {
            $query->whereDate('mutation_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('mutation_date', '<=', $this->endDate);
        }

        $mutations = $query->latest('mutation_date')->paginate($this->perPage);

        // Get locations for filter
        $locations = [];
        if ($user->hasRole('Polda')) {
            $locations['polres'] = PoliceStation::where('regional_police_id', $user->regional_police_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
            $locations['polda'] = RegionalPolice::where('id', $user->regional_police_id)
                ->where('is_active', true)
                ->get();
        } elseif ($user->hasRole('Polres')) {
            $locations['polda'] = RegionalPolice::where('is_active', true)
                ->orderBy('name')
                ->get();
            $locations['polres'] = PoliceStation::where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('livewire.admin.menu-polda.mutation-stock.admin-menu-polda-mutation-stock-index', [
            'mutations' => $mutations,
            'locations' => $locations,
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

    public function updatingLocationFilter()
    {
        $this->resetPage();
    }
}
