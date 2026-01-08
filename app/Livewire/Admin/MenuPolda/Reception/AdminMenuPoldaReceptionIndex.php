<?php

namespace App\Livewire\Admin\MenuPolda\Reception;

use App\Models\Police\RegionalPolice;
use App\Models\Reception\Reception;
use App\Models\Reception\ReceptionDetail;
use App\Services\StockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Schema;
use Livewire\WithPagination;
 use Livewire\Attributes\Url;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;

class AdminMenuPoldaReceptionIndex extends Component
{
    use WithPagination;



    // Search & Filter
    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;

    #[Url]
    public ?string $regionalPoliceId = null;

    #[Url]
    public ?string $type = null;

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    public int $perPage = 10;

    // Delete Modal
    public bool $showDeleteModal = false;
    public ?string $receptionId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
        'regionalPoliceId' => ['except' => null],
        'type' => ['except' => null],
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

    public function updatedType()
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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function openDeleteModal($id)
    {
        $this->receptionId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->receptionId = null;
    }

    public function delete(StockService $stockService)
    {
        $idToDelete = $this->receptionId;
        $this->closeModal();

        try {
            DB::beginTransaction();

            $reception = Reception::with('receptionDetails')->findOrFail($idToDelete);

            // Revert stock changes and delete history
            $stockService->deleteReceptionStock($reception);

            // Delete all details first
            $reception->receptionDetails()->delete();

            // Then delete the main record
            $reception->delete();

            DB::commit();

            session()->flash('success', 'Data penerimaan barang berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        $regionalPolices = RegionalPolice::select('id', 'name')->get();

        $allTypes = Type::query()->orderBy('name')->get();

        $typeDetails = [];
        if ($this->typeId) {
            $typeDetails = TypeDetail::where('type_id', $this->typeId)->orderBy('name')->get();
        } else {
             $typeDetails = TypeDetail::query()->orderBy('name')->get();
        }

        $receptions = ReceptionDetail::query()
            ->select('reception_details.*')
            ->join('receptions', 'reception_details.reception_id', '=', 'receptions.id')
            ->join('types', 'reception_details.type_id', '=', 'types.id')
            ->leftJoin('type_details', 'reception_details.type_detail_id', '=', 'type_details.id')
            ->with(['reception','reception.regionalPolice', 'reception.policeStation','type','typeDetail'])
            // Role-based filtering
            ->when($user->hasRole('Polda'), function ($query) use ($user) {
                $query->where('receptions.regional_police_id', $user->regional_police_id);
            })
            // Search
            ->when($this->search, function ($query) {

                $keywords = preg_split('/\s+/', trim($this->search));

                $query->where(function ($q) use ($keywords) {

                    foreach ($keywords as $word) {

                        $q->where(function ($sub) use ($word) {

                            $sub->where('receptions.code', 'ilike', "%{$word}%")
                                ->orWhere('receptions.type', 'ilike', "%{$word}%")
                                ->orWhere('types.name', 'ilike', "%{$word}%")
                                ->orWhere('type_details.name', 'ilike', "%{$word}%")
                                ->orWhere('reception_details.code', 'ilike', "%{$word}%")
                                ->orWhere('reception_details.number_serial_first', 'ilike', "%{$word}%");

                            if (Schema::hasColumn('reception_details', 'number_serial_last')) {
                                $sub->orWhere('reception_details.number_serial_last', 'ilike', "%{$word}%");
                            }

                        });
                    }

                });
            })
            ->when($this->regionalPoliceId, function ($query) {
                $query->where('receptions.regional_police_id', $this->regionalPoliceId);
            })
            ->when($this->type, function ($query) {
                $query->where('receptions.type', $this->type);
            })
            ->when($this->typeId, function ($query) {
                $query->where('reception_details.type_id', $this->typeId);
            })
            ->when($this->typeDetailId, function ($query) {
                $query->where('reception_details.type_detail_id', $this->typeDetailId);
            })
            // Date filter
            ->when($this->startDate, function ($query) {
                $query->whereDate('receptions.date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('receptions.date', '<=', $this->endDate);
            })
            ->orderBy('receptions.date', 'desc')
            ->orderBy('receptions.created_at', 'desc')
            ->paginate($this->perPage);
            // dd($receptions);

        return view('livewire.admin.menu-polda.reception.admin-menu-polda-reception-index', [
            'regionalPolices' => $regionalPolices,
            'receptions' => $receptions,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
        ])->layout('components.layouts.main.app');
    }
}
