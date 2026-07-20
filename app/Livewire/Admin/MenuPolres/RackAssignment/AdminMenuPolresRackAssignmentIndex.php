<?php

namespace App\Livewire\Admin\MenuPolres\RackAssignment;

use App\Models\MenuPolda\RackAssignment\RackAssignment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Police\PoliceStation;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;

class AdminMenuPolresRackAssignmentIndex extends Component
{
    use WithPagination;

    public string $search = '';

    #[Url]
    public $policeStationId = '';

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    #[Url]
    public $startDate = '';

    #[Url]
    public $endDate = '';

    #[Url]
    public $perPage = 10;

    public $showDeleteModal = false;
    public $rackAssignmentId = null;

    public function render()
    {
        $user = auth()->user();

        // Load filter options
        $policeStations = [];
        if ($user->hasRole('Admin')) {
            $policeStations = PoliceStation::orderBy('name')->get();
        }

        $allTypes = Type::query();
        if ($user->userType && !empty($user->userType->types)) {
            $allTypes->whereIn('id', $user->userType->types);
        }
        $allTypes = $allTypes->orderBy('name')->get();

        $typeDetails = [];
        if ($this->typeId) {
            $typeDetails = TypeDetail::where('type_id', $this->typeId)->orderBy('name')->get();
        } else {
             $tdQuery = TypeDetail::query();
             if ($user->userType && !empty($user->userType->types)) {
                 $tdQuery->whereIn('type_id', $user->userType->types);
             }
             $typeDetails = $tdQuery->orderBy('name')->get();
        }


        $query = \App\Models\MenuPolda\RackAssignment\RackAssignmentDetail::query()
            ->select('rack_assignment_details.*')
            ->join('rack_assignments', 'rack_assignment_details.rack_assignment_id', '=', 'rack_assignments.id')
            ->join('types', 'rack_assignment_details.type_id', '=', 'types.id')
            ->leftJoin('type_details', 'rack_assignment_details.type_detail_id', '=', 'type_details.id')
            ->with(['rackAssignment', 'rackAssignment.policeStation', 'rackAssignment.regionalPolice', 'type', 'typeDetail'])
            ->where('rack_assignments.is_active', true);

        if ($user->userType && !empty($user->userType->types)) {
            $query->whereIn('rack_assignment_details.type_id', $user->userType->types);
        }

        // Role-based filtering
        if ($user->hasRole('Admin')) {
            if ($this->policeStationId) {
                $query->where('rack_assignments.police_station_id', $this->policeStationId);
            }
        } else {
             $query->where('rack_assignments.police_station_id', $user->police_station_id);
        }

        // Type Filter
        if ($this->typeId) {
            $query->where('rack_assignment_details.type_id', $this->typeId);
        }

        // Type Detail Filter
        if ($this->typeDetailId) {
            $query->where('rack_assignment_details.type_detail_id', $this->typeDetailId);
        }

        // Search
        if ($this->search) {
             $keywords = preg_split('/\s+/', trim($this->search));
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word) {
                        $sub->where('rack_assignments.code', 'ilike', "%{$word}%")
                            ->orWhere('types.name', 'ilike', "%{$word}%")
                            ->orWhere('type_details.name', 'ilike', "%{$word}%")
                            ->orWhere('rack_assignment_details.item_code', 'ilike', "%{$word}%")
                            ->orWhere('rack_assignment_details.number_serial_first', 'ilike', "%{$word}%")
                            ->orWhere('rack_assignment_details.number_serial_second', 'ilike', "%{$word}%")
                            ->orWhere('rack_assignment_details.description', 'ilike', "%{$word}%");
                    });
                }
            });
        }

        // Date filtering
        if ($this->startDate) {
            $query->whereDate('rack_assignments.date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('rack_assignments.date', '<=', $this->endDate);
        }

        $rackAssignments = $query->orderBy('rack_assignments.date', 'desc')
             ->orderBy('rack_assignments.created_at', 'desc')
             ->paginate($this->perPage);

        return view('livewire.admin.menu-polres.rack-assignment.admin-menu-polres-rack-assignment-index', [
            'rackAssignments' => $rackAssignments,
            'policeStations' => $policeStations,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails
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
