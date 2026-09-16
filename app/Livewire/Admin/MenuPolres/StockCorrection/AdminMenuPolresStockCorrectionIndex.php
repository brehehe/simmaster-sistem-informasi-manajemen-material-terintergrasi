<?php

namespace App\Livewire\Admin\MenuPolres\StockCorrection;

use App\Models\Police\PoliceStation;
use App\Models\Rack\Rack;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class AdminMenuPolresStockCorrectionIndex extends Component
{
    use WithPagination;

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    #[Url]
    public $policeStationId = '';

    public $search = '';
    public $perPage = 10;

    // Modal Edit State
    public bool $isOpenModal = false;
    public ?string $selectedStockDetailId = null;
    public string $selectedTypeName = '';
    public ?string $selectedTypeId = null;
    public ?string $selectedTypeDetailId = null;
    public ?string $code = '';
    public ?string $number_serial_first = '';
    public ?string $number_serial_second = '';
    public $quantity = 0;
    public ?string $rack_id = null;
    public ?string $description = '';
    public $availableTypeDetails = [];
    public $availableRacks = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'typeId' => ['except' => ''],
        'typeDetailId' => ['except' => ''],
        'policeStationId' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeId()
    {
        $this->resetPage();
    }

    public function updatingTypeDetailId()
    {
        $this->resetPage();
    }

    public function updatingPoliceStationId()
    {
        $this->resetPage();
    }

    public function edit(string $id)
    {
        $user = Auth::user();

        $query = StockDetail::with(['type', 'typeDetail', 'rack', 'policeStation'])
            ->where('is_active', true)
            ->whereNotNull('police_station_id');

        if (!$user->hasRole('Admin')) {
            $query->where('police_station_id', $user->police_station_id);
        }

        $detail = $query->findOrFail($id);

        $this->selectedStockDetailId = $detail->id;
        $this->selectedTypeId = $detail->type_id;
        $this->selectedTypeName = $detail->type?->name ?? '-';
        $this->selectedTypeDetailId = $detail->type_detail_id;
        $this->code = $detail->code ?? '';
        $this->number_serial_first = $detail->number_serial_first ?? '';
        $this->number_serial_second = $detail->number_serial_second ?? '';
        $this->quantity = (float) $detail->quantity;
        $this->rack_id = $detail->rack_id;
        $this->description = $detail->description ?? '';

        // Load available type details for this material type
        $this->availableTypeDetails = TypeDetail::where('type_id', $detail->type_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Load available racks for this police station
        $this->availableRacks = Rack::where('police_station_id', $detail->police_station_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $this->isOpenModal = true;
    }

    public function closeModal()
    {
        $this->reset([
            'isOpenModal',
            'selectedStockDetailId',
            'selectedTypeId',
            'selectedTypeName',
            'selectedTypeDetailId',
            'code',
            'number_serial_first',
            'number_serial_second',
            'quantity',
            'rack_id',
            'description',
            'availableTypeDetails',
            'availableRacks',
        ]);
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate([
            'code' => 'nullable|string|max:50',
            'number_serial_first' => 'nullable|string|max:50',
            'number_serial_second' => 'nullable|string|max:50',
            'selectedTypeDetailId' => 'nullable|exists:type_details,id',
            'quantity' => 'required|numeric|min:0',
            'rack_id' => 'nullable|exists:racks,id',
            'description' => 'nullable|string|max:500',
        ], [
            'quantity.required' => 'Jumlah stok wajib diisi.',
            'quantity.numeric' => 'Jumlah stok harus berupa angka.',
            'quantity.min' => 'Jumlah stok minimal 0.',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $query = StockDetail::with('stock')
                ->where('is_active', true)
                ->whereNotNull('police_station_id');

            if (!$user->hasRole('Admin')) {
                $query->where('police_station_id', $user->police_station_id);
            }

            $detail = $query->findOrFail($this->selectedStockDetailId);

            $oldQuantity = (float) $detail->quantity;
            $newQuantity = (float) $this->quantity;
            $oldTypeDetailId = $detail->type_detail_id;
            $newTypeDetailId = !empty($this->selectedTypeDetailId) ? $this->selectedTypeDetailId : null;

            // Handle type_detail_id change or quantity change in parent Stock
            if ($oldTypeDetailId !== $newTypeDetailId) {
                // 1. Deduct old quantity from old parent Stock
                if ($detail->stock) {
                    $oldStock = $detail->stock;
                    $oldStock->quantity = max(0, (float) $oldStock->quantity - $oldQuantity);
                    $oldStock->save();
                }

                // 2. Find or create new parent Stock for the new type_detail_id
                $newStock = Stock::where('type_id', $detail->type_id)
                    ->where('type_detail_id', $newTypeDetailId)
                    ->where('police_station_id', $detail->police_station_id)
                    ->whereNull('regional_police_id')
                    ->first();

                if (!$newStock) {
                    $newStock = Stock::create([
                        'type_id' => $detail->type_id,
                        'type_detail_id' => $newTypeDetailId,
                        'regional_police_id' => null,
                        'police_station_id' => $detail->police_station_id,
                        'quantity' => $newQuantity,
                        'is_active' => true,
                    ]);
                } else {
                    $newStock->quantity = (float) $newStock->quantity + $newQuantity;
                    $newStock->save();
                }

                // Re-link detail to new parent Stock
                $detail->stock_id = $newStock->id;
                $detail->type_detail_id = $newTypeDetailId;
            } elseif ($oldQuantity !== $newQuantity) {
                // Same type_detail_id, but quantity changed
                if ($detail->stock) {
                    $qtyDiff = $newQuantity - $oldQuantity;
                    $parentStock = $detail->stock;
                    $parentStock->quantity = max(0, (float) $parentStock->quantity + $qtyDiff);
                    $parentStock->save();
                }
            }

            // Update detail attributes
            $detail->code = !empty($this->code) ? trim($this->code) : null;
            $detail->number_serial_first = !empty($this->number_serial_first) ? trim($this->number_serial_first) : null;
            $detail->number_serial_second = !empty($this->number_serial_second) ? trim($this->number_serial_second) : null;
            $detail->quantity = $newQuantity;
            $detail->rack_id = !empty($this->rack_id) ? $this->rack_id : null;
            $detail->description = !empty($this->description) ? trim($this->description) : null;
            $detail->save();

            DB::commit();

            session()->flash('success', 'Nomor seri dan detail material Polres berhasil diperbarui.');
            $this->closeModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Dropdown data
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

        // Base Query: StockDetail Polres
        $query = StockDetail::query()
            ->with(['type', 'typeDetail', 'rack', 'policeStation'])
            ->where('is_active', true)
            ->whereNotNull('police_station_id')
            ->where('quantity', '>', 0);

        if ($user->hasRole('Admin')) {
            if ($this->policeStationId) {
                $query->where('police_station_id', $this->policeStationId);
            }
        } else {
            $query->where('police_station_id', $user->police_station_id);
        }

        if ($user->userType && !empty($user->userType->types)) {
            $query->whereIn('type_id', $user->userType->types);
        }

        if ($this->typeId) {
            $query->where('type_id', $this->typeId);
        }

        if ($this->typeDetailId) {
            $query->where('type_detail_id', $this->typeDetailId);
        }

        if ($this->search) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhere('number_serial_first', 'ilike', "%{$search}%")
                    ->orWhere('number_serial_second', 'ilike', "%{$search}%")
                    ->orWhereHas('type', fn($t) => $t->where('name', 'ilike', "%{$search}%"))
                    ->orWhereHas('typeDetail', fn($td) => $td->where('name', 'ilike', "%{$search}%"))
                    ->orWhereHas('rack', fn($r) => $r->where('name', 'ilike', "%{$search}%"));
            });
        }

        $stockDetails = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        return view('livewire.admin.menu-polres.stock-correction.admin-menu-polres-stock-correction-index', [
            'stockDetails' => $stockDetails,
            'policeStations' => $policeStations,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
            'isOpenModal' => $this->isOpenModal,
        ])->layout('components.layouts.main.app');
    }
}
