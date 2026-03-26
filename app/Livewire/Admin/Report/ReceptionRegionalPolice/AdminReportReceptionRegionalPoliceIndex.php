<?php

namespace App\Livewire\Admin\Report\ReceptionRegionalPolice;

use App\Exports\ReceptionRegionalPoliceExport;
use App\Models\Police\RegionalPolice;
use App\Models\Reception\Reception;
use App\Models\Reception\ReceptionDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Services\StockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class AdminReportReceptionRegionalPoliceIndex extends Component
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
            session()->flash('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function getBaseQueryProperty()
    {
        $user = Auth::user();

        $query = ReceptionDetail::query()
            ->select('reception_details.*')
            ->join('receptions', 'reception_details.reception_id', '=', 'receptions.id')
            ->join('types', 'reception_details.type_id', '=', 'types.id')
            ->leftJoin('type_details', 'reception_details.type_detail_id', '=', 'type_details.id')
            ->with(['reception', 'reception.regionalPolice', 'reception.policeStation', 'type', 'typeDetail'])
            // Role-based filtering
            ->when($user->hasRole('Polda'), function ($query) use ($user) {
                $query->where('receptions.regional_police_id', $user->regional_police_id);
            })
            // User Type filtering (Requested)
            ->when($user->userType && ! empty($user->userType->types), function ($query) use ($user) {
                $query->whereIn('reception_details.type_id', $user->userType->types);
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
            });

        return $query;
    }

    public function getReceptionsProperty()
    {
        return $this->baseQuery
            ->orderBy('receptions.date', 'desc')
            ->orderBy('receptions.created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function getTotalReceptionsProperty()
    {
        return $this->baseQuery->count();
    }

    public function getTotalQuantityProperty()
    {
        // Clone query to avoid modifying base instance if it were a builder object reused (though getter returns new)
        // But getBaseQueryProperty returns a new builder each time.
        // Actually property access caches? No, Livewire properties cached?
        // Computed properties in Livewire 3 are cached for the request.
        // If I use `get...Property`, it is standard Laravel accessor or Livewire computed.
        // In Livewire v3 `#[Computed]` is used. Here it is v3 syntax `get...Property` (legacy computed).
        // It should be cached.
        // But `->count()` executes.
        // `->sum()` executes.
        // If I call `getBaseQueryProperty` multiple times, and it returns a Builder,
        // calling `paginate` modifies it? No, query builder is mutable.
        // So I should clone it.

        return (clone $this->baseQuery)->sum('reception_details.quantity');
    }

    public function getTotalTodayReceptionsProperty()
    {
        return (clone $this->baseQuery)
            ->whereDate('receptions.date', today())
            ->count();
    }

    public function exportExcel()
    {
        $query = clone $this->baseQuery;
        $fileName = 'laporan-penerimaan-barang-'.now()->format('Y-m-d-His').'.xlsx';

        return Excel::download(new ReceptionRegionalPoliceExport($query), $fileName);
    }

    public function exportPdf()
    {
        $data = (clone $this->baseQuery)
            ->orderBy('receptions.date', 'desc')
            ->orderBy('receptions.created_at', 'desc')
            ->get();

        $filters = [
            'search' => $this->search,
            'regionalPolice' => $this->regionalPoliceId ? RegionalPolice::find($this->regionalPoliceId)?->name : null,
            'type' => $this->type,
            'material' => $this->typeId ? Type::find($this->typeId)?->name : null,
            'materialDetail' => $this->typeDetailId ? TypeDetail::find($this->typeDetailId)?->name : null,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

        $pdf = Pdf::loadView('exports.pdf.reception-regional-police', [
            'data' => $data,
            'filters' => $filters,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-penerimaan-barang-'.now()->format('Y-m-d-His').'.pdf');
    }

    public function render()
    {
        $regionalPolices = RegionalPolice::select('id', 'name')->get();

        $allTypes = Type::query()->orderBy('name')->get();

        $typeDetails = [];
        if ($this->typeId) {
            $typeDetails = TypeDetail::where('type_id', $this->typeId)->orderBy('name')->get();
        } else {
            $typeDetails = TypeDetail::query()->orderBy('name')->get();
        }

        return view('livewire.admin.report.reception-regional-police.admin-report-reception-regional-police-index', [
            'regionalPolices' => $regionalPolices,
            'receptions' => $this->receptions,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
        ])->layout('components.layouts.main.app');
    }
}
