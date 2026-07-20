<?php

namespace App\Livewire\Admin\MenuPolres\MaterialSubsidy;

use App\Models\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy;
use App\Models\Police\PoliceStation;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class AdminMenuPolresMaterialSubsidyIndex extends Component
{
    use WithPagination;

    public string $search = '';

    #[Url]
    public ?string $startDate = null;

    #[Url]
    public ?string $endDate = null;

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $policeStationId = '';

    public int $perPage = 10;

    public bool $showDeleteModal = false;
    public bool $showDetailModal = false;
    public bool $showConfirmModal = false;

    public ?string $subsidyId = null;
    public $selectedSubsidy = null;

    public function toJSON()
    {
        return [];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPoliceStationId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $query = MaterialSubsidy::with([
            'regionalPolice',
            'policeStation',
            'confirmedByUser',
            'materialSubsidyDetails',
            'materialSubsidyDetails.type',
            'materialSubsidyDetails.typeDetail',
        ])->where('is_active', true);

        // Role-based filtering
        if ($user->hasRole('Polres') || !empty($user->police_station_id)) {
            $query->where('police_station_id', $user->police_station_id);
        } elseif ($user->hasRole('Admin') && $this->policeStationId) {
            $query->where('police_station_id', $this->policeStationId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'ilike', '%' . $this->search . '%')
                    ->orWhere('recipient_name', 'ilike', '%' . $this->search . '%')
                    ->orWhere('notes', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->startDate) {
            $query->whereDate('subsidy_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('subsidy_date', '<=', $this->endDate);
        }

        $subsidies = $query->latest('subsidy_date')->latest('created_at')->paginate($this->perPage);

        $policeStations = [];
        if ($user->hasRole('Admin')) {
            $policeStations = PoliceStation::where('is_active', true)->orderBy('name')->get();
        }

        // Summary counts
        $totalSubsidies = (clone $query)->count();
        $totalItems = (clone $query)->get()->sum(fn($s) => $s->materialSubsidyDetails->sum('quantity'));

        return view('livewire.admin.menu-polres.material-subsidy.admin-menu-polres-material-subsidy-index', [
            'subsidies'      => $subsidies,
            'policeStations' => $policeStations,
            'totalSubsidies' => $totalSubsidies,
            'totalItems'     => $totalItems,
        ])->layout('components.layouts.main.app');
    }

    public function viewDetail(string $id): void
    {
        $this->subsidyId = $id;
        $this->selectedSubsidy = MaterialSubsidy::with([
            'regionalPolice',
            'policeStation',
            'confirmedByUser',
            'materialSubsidyDetails.type',
            'materialSubsidyDetails.typeDetail',
        ])->find($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedSubsidy = null;
        $this->subsidyId = null;
    }

    public function openConfirmModal(string $id): void
    {
        $this->subsidyId = $id;
        $this->selectedSubsidy = MaterialSubsidy::with([
            'regionalPolice',
            'policeStation',
            'materialSubsidyDetails.type',
            'materialSubsidyDetails.typeDetail',
        ])->find($id);
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->selectedSubsidy = null;
        $this->subsidyId = null;
    }

    public function confirmSubsidy(): void
    {
        if (!$this->subsidyId) {
            return;
        }

        try {
            $subsidy = MaterialSubsidy::find($this->subsidyId);
            if (!$subsidy) {
                session()->flash('error', 'Data subsidi tidak ditemukan.');
                $this->closeConfirmModal();
                return;
            }

            $subsidy->confirm(auth()->user());

            session()->flash('success', 'Subsidi silang berhasil dikonfirmasi! Stok Polres telah berkurang secara otomatis.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengonfirmasi subsidi: ' . $e->getMessage());
        }

        $this->closeConfirmModal();
    }

    public function openDeleteModal(string $id): void
    {
        $this->subsidyId = $id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->subsidyId = null;
    }

    public function deleteSubsidy(): void
    {
        if (!$this->subsidyId) {
            return;
        }

        $subsidy = MaterialSubsidy::find($this->subsidyId);
        if ($subsidy) {
            if ($subsidy->status === 'confirmed') {
                session()->flash('error', 'Subsidi yang sudah dikonfirmasi tidak dapat dihapus.');
            } else {
                $subsidy->materialSubsidyDetails()->delete();
                $subsidy->delete();
                session()->flash('success', 'Data subsidi silang berhasil dihapus.');
            }
        }

        $this->closeDeleteModal();
    }
}
