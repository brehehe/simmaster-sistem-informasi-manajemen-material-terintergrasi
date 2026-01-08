<?php

namespace App\Livewire\Admin\MenuPolda\MaterialDamage;

use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Police\RegionalPolice;

class AdminMenuPoldaMaterialDamageIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $perPage = 10;
    public bool $showDeleteModal = false;
    public ?string $materialDamageId = null;

    #[Url]
    public ?string $regionalPoliceId = null;

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
        'regionalPoliceId' => ['except' => null],
        'typeId' => ['except' => ''],
        'typeDetailId' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedRegionalPoliceId()
    {
        $this->resetPage();
    }

    public function updatedTypeId()
    {
        $this->resetPage();
    }

    public function updatedTypeDetailId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $regionalPolices = RegionalPolice::select('id', 'name')->get();

        $allTypes = Type::query()->orderBy('name')->get();

        $typeDetails = [];
        if ($this->typeId) {
            $typeDetails = TypeDetail::where('type_id', $this->typeId)->orderBy('name')->get();
        } else {
             $typeDetails = TypeDetail::query()->orderBy('name')->get();
        }

        $query = \App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail::query()
            ->select('material_damage_details.*')
            ->join('material_damages', 'material_damage_details.material_damage_id', '=', 'material_damages.id')
            ->join('types', 'material_damage_details.type_id', '=', 'types.id')
            ->leftJoin('type_details', 'material_damage_details.type_detail_id', '=', 'type_details.id')
            ->with(['materialDamage', 'materialDamage.regionalPolice', 'type', 'typeDetail'])
            ->where('material_damages.is_active', true);

        // Role-based filtering
        if ($user->hasRole('Polda')) {
            $query->where('material_damages.regional_police_id', $user->regional_police_id);
        }

        // Apply filters
        if ($this->regionalPoliceId) {
            $query->where('material_damages.regional_police_id', $this->regionalPoliceId);
        }

        if ($this->typeId) {
            $query->where('material_damage_details.type_id', $this->typeId);
        }

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

        return view('livewire.admin.menu-polda.material-damage.admin-menu-polda-material-damage-index', [
            'materialDamages' => $materialDamages,
            'regionalPolices' => $regionalPolices,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
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
