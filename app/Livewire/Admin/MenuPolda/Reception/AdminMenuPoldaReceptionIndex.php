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

    // Detail Modal
    public bool $showDetailModal = false;
    public $selectedReceptionDetails = [];
    public ?Reception $selectedReception = null;

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
        $this->showDetailModal = false;
        $this->receptionId = null;
        $this->selectedReception = null;
        $this->selectedReceptionDetails = [];
    }

    public function viewDetail($id)
    {
        $this->selectedReception = Reception::with([
            'regionalPolice', 
            'policeStation', 
            'typeMaterial',
            'receptionDetails.receptionDetailItems.typeDetail',
            'receptionDetails.receptionDetailItems.service',
            'receptionDetails.receptionDetailItems.serviceDetail'
        ])->findOrFail($id);
        
        // Extract all the items from the single details wrapper
        $this->selectedReceptionDetails = $this->selectedReception->receptionDetails->flatMap(function($detail) {
            return $detail->receptionDetailItems;
        });

        $this->showDetailModal = true;
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

            // Cascade delete manually: items first, then details
            foreach($reception->receptionDetails as $detail) {
                // Delete the new flattened breakdown items
                $detail->receptionDetailItems()->delete();
                $detail->delete();
            }

            // Then delete the main record
            $reception->delete();

            DB::commit();

            session()->flash('success', 'Data penerimaan barang berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function exportBappmPdf($id)
    {
        $reception = Reception::with([
            'regionalPolice', 
            'policeStation', 
            'typeMaterial',
            'receptionDetails.receptionDetailItems.type',
            'receptionDetails.receptionDetailItems.typeDetail',
            'receptionDetails.receptionDetailItems.service',
            'receptionDetails.receptionDetailItems.serviceDetail'
        ])->findOrFail($id);

        $groupedItems = $reception->getGroupedItems();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.menu-polda.reception.admin-menu-polda-reception-print', [
            'reception' => $reception,
            'receptionDetails' => $groupedItems,
            'isPdf' => true,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'BAPPM-' . str_replace('/', '_', $reception->code) . '.pdf');
    }

    public function exportBappmExcel($id)
    {
        $reception = Reception::with([
            'regionalPolice', 
            'policeStation', 
            'typeMaterial',
            'receptionDetails.receptionDetailItems.type',
            'receptionDetails.receptionDetailItems.typeDetail',
            'receptionDetails.receptionDetailItems.service',
            'receptionDetails.receptionDetailItems.serviceDetail'
        ])->findOrFail($id);

        $fileName = 'BAPPM-' . str_replace('/', '_', $reception->code) . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\BappmExport($reception),
            $fileName
        );
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

        $receptions = Reception::query()
            ->select('receptions.*')
            ->with(['regionalPolice', 'policeStation', 'typeMaterial', 'receptionDetails'])
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
                                ->orWhereHas('typeMaterial', function($t) use ($word) {
                                    $t->where('name', 'like', "%{$word}%");
                                })
                                ->orWhereHas('receptionDetails.receptionDetailItems', function($rdi) use ($word) {
                                    $rdi->where('item_code', 'ilike', "%{$word}%")
                                        ->orWhere('number_serial_first', 'ilike', "%{$word}%")
                                        ->orWhere('number_serial_second', 'ilike', "%{$word}%")
                                        ->orWhereHas('typeDetail', function($td) use ($word) {
                                            $td->where('name', 'ilike', "%{$word}%");
                                        })
                                        ->orWhereHas('service', function($s) use ($word) {
                                            $s->where('name', 'ilike', "%{$word}%");
                                        });
                                });
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
                $query->where('receptions.type_id', $this->typeId);
            })
            ->when($this->typeDetailId, function ($query) {
                $query->whereHas('receptionDetails.receptionDetailItems', function($q) {
                    $q->where('type_detail_id', $this->typeDetailId);
                });
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

        return view('livewire.admin.menu-polda.reception.admin-menu-polda-reception-index', [
            'regionalPolices' => $regionalPolices,
            'receptions' => $receptions,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
        ])->layout('components.layouts.main.app');
    }
}
