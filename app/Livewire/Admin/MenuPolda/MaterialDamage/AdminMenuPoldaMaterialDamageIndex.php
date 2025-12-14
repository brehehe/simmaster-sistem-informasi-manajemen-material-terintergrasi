<?php

namespace App\Livewire\Admin\MenuPolda\MaterialDamage;

use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPoldaMaterialDamageIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $perPage = 10;
    public bool $showDeleteModal = false;
    public ?string $materialDamageId = null;

    public function render()
    {
        $query = MaterialDamage::with(['materialDamageDetails', 'regionalPolice'])
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

        $materialDamages = $query->latest('date')->paginate($this->perPage);

        return view('livewire.admin.menu-polda.material-damage.admin-menu-polda-material-damage-index', [
            'materialDamages' => $materialDamages
        ])->layout('components.layouts.main.app');
    }

    public function openDeleteModal($id)
    {
        $this->materialDamageId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->materialDamageId = null;
    }

    public function delete()
    {
        if ($this->materialDamageId) {
            $materialDamage = MaterialDamage::find($this->materialDamageId);
            if ($materialDamage) {
                $materialDamage->delete();
                session()->flash('success', 'Data material damage berhasil dihapus.');
            }
        }
        $this->closeModal();
    }
}
