<?php

namespace App\Livewire\Admin\MenuPolres\LastStock;

use App\Models\LastStock\LastStock;
use App\Models\Police\PoliceStation;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Services\StockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPolresLastStockIndex extends Component
{
    use WithPagination;

    // Search & Filter
    public string $search = '';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public int $perPage = 10;

    #[Url]
    public $policeStationId = '';

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    // Delete Modal
    public bool $showDeleteModal = false;

    public ?string $lastStockId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
        'perPage' => ['except' => 10],
        'policeStationId' => ['except' => ''],
        'typeId' => ['except' => ''],
        'typeDetailId' => ['except' => ''],
    ];

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole(['Admin', 'Polres']), 403);
    }

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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedPoliceStationId()
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
        $this->lastStockId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->lastStockId = null;
    }

    public function delete(StockService $stockService)
    {
        $lastStock = LastStock::with('lastStockDetails')->findOrFail($this->lastStockId);
        abort_unless(
            auth()->user()->hasRole('Admin')
            || $lastStock->police_station_id === auth()->user()->police_station_id,
            403
        );

        try {
            DB::beginTransaction();

            // Revert stock & history
            $stockService->deleteLastStock($lastStock);

            // Delete all details first
            $lastStock->lastStockDetails()->delete();

            // Then delete the main record
            $lastStock->delete();

            DB::commit();

            session()->flash('success', 'Data stok awal berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        $allowedTypes = $user->hasRole('Admin')
            ? null
            : \App\Services\StockAdjustmentService::expandMaterialTypes($user->userType?->types);

        // Load filter options
        $policeStations = [];
        if ($user->hasRole('Admin')) {
            $policeStations = PoliceStation::orderBy('name')->get();
        }

        $allTypes = Type::query();
        if ($allowedTypes) {
            $allTypes->whereIn('id', $allowedTypes);
        }
        $allTypes = $allTypes->orderBy('name')->get();

        $typeDetails = [];
        if ($this->typeId) {
            $typeDetails = TypeDetail::where('type_id', $this->typeId)->orderBy('name')->get();
        } else {
            $tdQuery = TypeDetail::query();
            if ($allowedTypes) {
                $tdQuery->whereIn('type_id', $allowedTypes);
            }
            $typeDetails = $tdQuery->orderBy('name')->get();
        }

        $query = \App\Models\LastStock\LastStockDetail::query()
            ->select('last_stock_details.*')
            ->join('last_stocks', 'last_stock_details.last_stock_id', '=', 'last_stocks.id')
            ->join('types', 'last_stock_details.type_id', '=', 'types.id')
            ->leftJoin('type_details', 'last_stock_details.type_detail_id', '=', 'type_details.id')
            ->with(['lastStock', 'lastStock.regionalPolice', 'lastStock.policeStation', 'type', 'typeDetail']);

        // Role-based filtering
        if ($user->hasRole('Admin')) {
            if ($this->policeStationId) {
                $query->where('last_stocks.police_station_id', $this->policeStationId);
            }
        } else {
            $query->where('last_stocks.police_station_id', $user->police_station_id);
        }

        if ($allowedTypes) {
            $query->whereIn('last_stock_details.type_id', $allowedTypes);
        }

        // Type Filter
        if ($this->typeId) {
            $query->where('last_stock_details.type_id', $this->typeId);
        }

        // Type Detail Filter
        if ($this->typeDetailId) {
            $query->where('last_stock_details.type_detail_id', $this->typeDetailId);
        }

        // Search
        if ($this->search) {
            $keywords = preg_split('/\s+/', trim($this->search));
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word) {
                        $sub->where('last_stocks.code', 'ilike', "%{$word}%")
                            ->orWhere('types.name', 'ilike', "%{$word}%")
                            ->orWhere('type_details.name', 'ilike', "%{$word}%")
                            ->orWhere('last_stock_details.item_code', 'ilike', "%{$word}%")
                            ->orWhere('last_stock_details.number_serial_first', 'ilike', "%{$word}%")
                            ->orWhere('last_stock_details.number_serial_second', 'ilike', "%{$word}%")
                            ->orWhere('last_stock_details.description', 'ilike', "%{$word}%");
                    });
                }
            });
        }

        // Date filter
        if ($this->startDate) {
            $query->whereDate('last_stocks.date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('last_stocks.date', '<=', $this->endDate);
        }

        $lastStocks = $query->orderBy('last_stocks.date', 'desc')
            ->orderBy('last_stocks.created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.menu-polres.last-stock.admin-menu-polres-last-stock-index', [
            'lastStocks' => $lastStocks,
            'policeStations' => $policeStations,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
        ])->layout('components.layouts.main.app');
    }
}
