<?php

namespace App\Livewire\Admin\MenuPolda\MaterialDamage;

use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Police\RegionalPolice;
use Illuminate\Support\Facades\DB;

class AdminMenuPoldaMaterialDamageIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $perPage = 10;
    
    // Delete Modal
    public bool $showDeleteModal = false;
    public ?string $materialDamageId = null;

    // Detail Modal
    public bool $showDetailModal = false;
    public ?string $selectedId = null;

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

    public function openDeleteModal($id)
    {
        $this->materialDamageId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->showDetailModal = false;
        $this->materialDamageId = null;
        $this->selectedId = null;
    }

    public function viewDetail($id)
    {
        $this->selectedId = $id;
        $this->showDetailModal = true;
    }

    public function delete()
    {
        if ($this->materialDamageId) {
            $materialDamage = MaterialDamage::find($this->materialDamageId);
            if ($materialDamage) {
                // To be exact, we should revert stock deductions if deleting damage? 
                // Usually deletion is just soft delete. In SIMMASTER, most transactions are soft deleted.
                $materialDamage->delete();
                session()->flash('success', 'Data material damage berhasil dihapus.');
            }
        }
        $this->closeModal();
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

        $query = MaterialDamage::query()
            ->with(['regionalPolice', 'policeStation', 'materialDamageDetails.type'])
            ->where('is_active', true);

        // Role-based filtering
        if ($user->hasRole('Polda')) {
            $query->where('regional_police_id', $user->regional_police_id);
        }

        // Apply filters
        if ($this->regionalPoliceId) {
            $query->where('regional_police_id', $this->regionalPoliceId);
        }

        if ($this->typeId) {
            $query->whereHas('materialDamageDetails', function($q) {
                $q->where('type_id', $this->typeId);
            });
        }

        if ($this->typeDetailId) {
            $query->whereHas('materialDamageDetails', function($q) {
                $q->where('type_detail_id', $this->typeDetailId);
            });
        }

        // Search
        if ($this->search) {
            $keywords = preg_split('/\s+/', trim($this->search));
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word) {
                        $sub->where('code', 'ilike', "%{$word}%")
                            ->orWhere('description', 'ilike', "%{$word}%")
                            ->orWhereHas('materialDamageDetails', function($d) use ($word) {
                                $d->where('item_code', 'ilike', "%{$word}%")
                                  ->orWhere('number_serial_first', 'ilike', "%{$word}%")
                                  ->orWhere('number_serial_second', 'ilike', "%{$word}%")
                                  ->orWhereHas('type', function($t) use ($word) {
                                      $t->where('name', 'ilike', "%{$word}%");
                                  })
                                  ->orWhereHas('typeDetail', function($td) use ($word) {
                                      $td->where('name', 'ilike', "%{$word}%");
                                  });
                            });
                    });
                }
            });
        }

        // Date filtering
        if ($this->startDate) {
            $query->whereDate('date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        $materialDamages = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $selectedMaterialDamage = null;
        if ($this->selectedId) {
            $selectedMaterialDamage = MaterialDamage::with([
                'regionalPolice', 
                'policeStation', 
                'materialDamageDetails.type',
                'materialDamageDetails.typeDetail',
                'materialDamageDetails.stockDetail.service',
                'materialDamageDetails.stockDetail.serviceDetail'
            ])->find($this->selectedId);
        }

        return view('livewire.admin.menu-polda.material-damage.admin-menu-polda-material-damage-index', [
            'materialDamages' => $materialDamages,
            'regionalPolices' => $regionalPolices,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
            'selectedMaterialDamage' => $selectedMaterialDamage,
        ])->layout('components.layouts.main.app');
    }
}

