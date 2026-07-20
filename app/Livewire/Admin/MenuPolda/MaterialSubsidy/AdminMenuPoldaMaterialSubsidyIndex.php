<?php

namespace App\Livewire\Admin\MenuPolda\MaterialSubsidy;

use App\Models\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy;
use App\Models\Police\RegionalPolice;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPoldaMaterialSubsidyIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public string $statusFilter = '';
    public string $poldaFilter = '';
    public int $perPage = 10;

    public bool $showDeleteModal = false;
    public bool $showDetailModal = false;
    public bool $showConfirmModal = false;

    public ?string $subsidyId = null;
    public $selectedSubsidy = null;

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPoldaFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $query = MaterialSubsidy::with([
            'regionalPolice',
            'confirmedByUser',
            'materialSubsidyDetails',
            'materialSubsidyDetails.type',
            'materialSubsidyDetails.typeDetail',
        ])->where('is_active', true);

        // Role-based: Polda only sees their own
        if ($user->hasRole('Polda')) {
            $query->where('regional_police_id', $user->regional_police_id);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('recipient_name', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->poldaFilter) {
            $query->where('regional_police_id', $this->poldaFilter);
        }

        if ($this->startDate) {
            $query->whereDate('subsidy_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('subsidy_date', '<=', $this->endDate);
        }

        $subsidies = $query->latest('subsidy_date')->paginate($this->perPage);

        $regionalPolices = RegionalPolice::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.menu-polda.material-subsidy.admin-menu-polda-material-subsidy-index', [
            'subsidies'      => $subsidies,
            'regionalPolices' => $regionalPolices,
        ])->layout('components.layouts.main.app');
    }

    public function viewDetail(string $id): void
    {
        $this->subsidyId = $id;
        $this->selectedSubsidy = MaterialSubsidy::with([
            'regionalPolice',
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
            session()->flash('success', 'Subsidi material berhasil dikonfirmasi. Stok Polda telah dikurangi.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengkonfirmasi: ' . $e->getMessage());
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

    public function delete(): void
    {
        if (!$this->subsidyId) {
            return;
        }

        $subsidy = MaterialSubsidy::find($this->subsidyId);
        if ($subsidy && $subsidy->status === 'draft') {
            $subsidy->delete();
            session()->flash('success', 'Subsidi material berhasil dihapus.');
        } else {
            session()->flash('error', 'Hanya subsidi dengan status draft yang bisa dihapus.');
        }

        $this->closeDeleteModal();
    }
}
