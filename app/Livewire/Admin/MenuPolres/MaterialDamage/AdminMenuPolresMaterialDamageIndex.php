<?php

namespace App\Livewire\Admin\MenuPolres\MaterialDamage;

use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Police\PoliceStation;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;

class AdminMenuPolresMaterialDamageIndex extends Component
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
    public $materialDamageId = null;

    public function toJSON()
    {
        return [];
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


        $query = \App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail::query()
            ->select('material_damage_details.*')
            ->join('material_damages', 'material_damage_details.material_damage_id', '=', 'material_damages.id')
            ->join('types', 'material_damage_details.type_id', '=', 'types.id')
            ->leftJoin('type_details', 'material_damage_details.type_detail_id', '=', 'type_details.id')
            ->with(['materialDamage', 'materialDamage.policeStation', 'type', 'typeDetail'])
            ->where('material_damages.is_active', true);

        if ($user->userType && !empty($user->userType->types)) {
            $query->whereIn('material_damage_details.type_id', $user->userType->types);
        }

        // Role-based filtering
        if ($user->hasRole('Admin')) {
            if ($this->policeStationId) {
                $query->where('material_damages.police_station_id', $this->policeStationId);
            }
        } else {
            $query->where('material_damages.police_station_id', $user->police_station_id);
        }

        // Type Filter
        if ($this->typeId) {
            $query->where('material_damage_details.type_id', $this->typeId);
        }

        // Type Detail Filter
        if ($this->typeDetailId) {
            $query->where('material_damage_details.type_detail_id', $this->typeDetailId);
        }

        // Search
        if ($this->search) {
            $keywords = preg_split('/\s+/', trim($this->search));
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word) {
                        $sub->where('material_damages.code', 'ilike', "%{$word}%")
                            ->orWhere('types.name', 'ilike', "%{$word}%")
                            ->orWhere('type_details.name', 'ilike', "%{$word}%")
                            ->orWhere('material_damage_details.damage_type', 'ilike', "%{$word}%")
                            ->orWhere('material_damage_details.reason', 'ilike', "%{$word}%")
                            ->orWhere('material_damage_details.item_code', 'ilike', "%{$word}%")
                            ->orWhere('material_damage_details.number_serial_first', 'ilike', "%{$word}%")
                            ->orWhere('material_damage_details.number_serial_second', 'ilike', "%{$word}%")
                            ->orWhere('material_damage_details.description', 'ilike', "%{$word}%");
                    });
                }
            });
        }

        // Date filtering
        if ($this->startDate) {
            $query->whereDate('material_damages.date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('material_damages.date', '<=', $this->endDate);
        }

        $materialDamages = $query->orderBy('material_damages.date', 'desc')
            ->orderBy('material_damages.created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.menu-polres.material-damage.admin-menu-polres-material-damage-index', [
            'materialDamages' => $materialDamages,
            'policeStations' => $policeStations,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails
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
