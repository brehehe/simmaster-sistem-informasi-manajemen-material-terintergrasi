<?php

namespace App\Livewire\Admin\MenuPolda\MaterialUsage;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPoldaMaterialUsageIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $perPage = 10;
    public bool $showDeleteModal = false;
    public ?string $materialUsageId = null;

    public function render()
    {
        $query = MaterialUsage::with(['materialUsageDetails', 'regionalPolice'])
            ->where('is_active', true);

        // Role-based filtering
        $user = auth()->user();
        if ($user->hasRole('Polda')) {
            $query->where('regional_police_id', $user->regional_police_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Date filtering
        if ($this->startDate) {
            $query->whereDate('date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        $materialUsages = $query->latest('date')->paginate($this->perPage);

        return view('livewire.admin.menu-polda.material-usage.admin-menu-polda-material-usage-index', [
            'materialUsages' => $materialUsages
        ])->layout('components.layouts.main.app');
    }

    public function openDeleteModal($id)
    {
        $this->materialUsageId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->materialUsageId = null;
    }

    public function delete()
    {
        if ($this->materialUsageId) {
            $materialUsage = MaterialUsage::find($this->materialUsageId);
            if ($materialUsage) {
                $materialUsage->delete();
                session()->flash('success', 'Data material usage berhasil dihapus.');
            }
        }
        $this->closeModal();
    }
}
