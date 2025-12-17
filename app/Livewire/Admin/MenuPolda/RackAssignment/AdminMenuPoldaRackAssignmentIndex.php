<?php

namespace App\Livewire\Admin\MenuPolda\RackAssignment;

use App\Models\MenuPolda\RackAssignment\RackAssignment;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPoldaRackAssignmentIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $perPage = 10;
    public bool $showDeleteModal = false;
    public ?string $rackAssignmentId = null;

    public function render()
    {
        $query = RackAssignment::with(['rackAssignmentDetails', 'regionalPolice'])
            ->where('is_active', true);

        // Role-based filtering
        $user = auth()->user();
        if ($user->hasRole('Polda')) {
            $query->where('regional_police_id', $user->regional_police_id)->whereNull('police_station_id');
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

        $rackAssignments = $query->latest('date')->paginate($this->perPage);

        // Debug: check if component is rendering
        // dd('Component rendering', $rackAssignments->count());

        return view('livewire.admin.menu-polda.rack-assignment.admin-menu-polda-rack-assignment-index', [
            'rackAssignments' => $rackAssignments
        ])->layout('components.layouts.main.app');
    }

    public function openDeleteModal($id)
    {
        $this->rackAssignmentId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->rackAssignmentId = null;
    }

    public function delete()
    {
        if ($this->rackAssignmentId) {
            $rackAssignment = RackAssignment::find($this->rackAssignmentId);
            if ($rackAssignment) {
                $rackAssignment->delete();
                session()->flash('success', 'Data rack assignment berhasil dihapus.');
            }
        }
        $this->closeModal();
    }
}
