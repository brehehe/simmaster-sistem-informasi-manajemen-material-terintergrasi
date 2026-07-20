<?php

namespace App\Livewire\Admin\MenuPolres\MaterialUsage;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Police\PoliceStation;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;

class AdminMenuPolresMaterialUsageIndex extends Component
{
    use WithPagination;

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

    public $search = '';
    public $perPage = 10;

    public $showDeleteModal = false;
    public $materialUsageId = null;

    public function mount()
    {
        return $this->redirect(route('menu-polres.material-usage.create'), navigate: true);
    }

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


        $query = \App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail::query()
            ->select('material_usage_details.*')
            ->join('material_usages', 'material_usage_details.material_usage_id', '=', 'material_usages.id')
            ->join('types', 'material_usage_details.type_id', '=', 'types.id')
            ->leftJoin('type_details', 'material_usage_details.type_detail_id', '=', 'type_details.id')
            ->with(['materialUsage', 'materialUsage.policeStation', 'type', 'typeDetail'])
            ->where('material_usages.is_active', true);

        if ($user->userType && !empty($user->userType->types)) {
            $query->whereIn('material_usage_details.type_id', $user->userType->types);
        }

        // Role-based filtering
        if ($user->hasRole('Admin')) {
            if ($this->policeStationId) {
                $query->where('material_usages.police_station_id', $this->policeStationId);
            }
        } else {
             $query->where('material_usages.police_station_id', $user->police_station_id);
        }

        // Type Filter
        if ($this->typeId) {
            $query->where('material_usage_details.type_id', $this->typeId);
        }

        // Type Detail Filter
        if ($this->typeDetailId) {
            $query->where('material_usage_details.type_detail_id', $this->typeDetailId);
        }

        // Search
        if ($this->search) {
             $keywords = preg_split('/\s+/', trim($this->search));
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word) {
                        $sub->where('material_usages.code', 'ilike', "%{$word}%")
                            ->orWhere('types.name', 'ilike', "%{$word}%")
                            ->orWhere('type_details.name', 'ilike', "%{$word}%")
                            ->orWhere('material_usage_details.item_code', 'ilike', "%{$word}%")
                            ->orWhere('material_usage_details.number_serial_first', 'ilike', "%{$word}%")
                            ->orWhere('material_usage_details.number_serial_second', 'ilike', "%{$word}%")
                            ->orWhere('material_usage_details.description', 'ilike', "%{$word}%");
                    });
                }
            });
        }

        // Date filtering
        if ($this->startDate) {
            $query->whereDate('material_usages.date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('material_usages.date', '<=', $this->endDate);
        }

        $materialUsages = $query->orderBy('material_usages.date', 'desc')
             ->orderBy('material_usages.created_at', 'desc')
             ->paginate($this->perPage);

        return view('livewire.admin.menu-polres.material-usage.admin-menu-polres-material-usage-index', [
            'materialUsages' => $materialUsages,
            'policeStations' => $policeStations,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails
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
