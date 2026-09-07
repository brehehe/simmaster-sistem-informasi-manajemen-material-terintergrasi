<?php

namespace App\Livewire\Admin\MenuPolda\MaterialUsage\Detail;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetailItem;
use App\Models\Police\RegionalPolice;
use App\Models\Rack\Rack;
use App\Models\Service\Service;
use App\Models\Stock\HistoryStockDetail;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminMenuPoldaMaterialUsageDetailIndex extends Component
{
    public ?string $materialUsageId = null;
    public bool $isEditMode = false;

    // Header fields
    public string $code = '';
    public ?string $date = null;
    public ?string $regionalPoliceId = null;
    public ?string $policeStationId = null;
    public string $description = '';

    // Details array (batch)
    public $details = [];

    // Dropdown data
    public $stockDetails = [];
    public $types = [];
    public $typeDetails = [];
    public $racks = [];
    public $regionalPolices = [];
    public $services = [];  // Services grouped by type_id
    public $serviceDetails = [];  // Service details grouped by service_id

    protected StockService $stockService;

    public function boot(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function mount($id = null)
    {
        $this->materialUsageId = $id;
        $this->isEditMode = $id !== null;

        if ($this->isEditMode) {
            $this->loadMaterialUsage();
        } else {
            $this->code = MaterialUsage::generateCode();
            $this->date = now()->format('Y-m-d');
            $this->addDetail();
        }

        $this->loadDropdownData();
    }

    protected function loadMaterialUsage()
    {
        $materialUsage = MaterialUsage::with(['materialUsageDetails.materialUsageDetailItems'])->findOrFail($this->materialUsageId);

        $this->code = $materialUsage->code;
        $this->date = $materialUsage->date->format('Y-m-d');
        $this->regionalPoliceId = $materialUsage->regional_police_id;
        $this->policeStationId = $materialUsage->police_station_id;
        $this->description = $materialUsage->description ?? '';

        // Load racks and stocks for this polda
        $this->loadRacks();
        $this->loadStockDetails();

        foreach ($materialUsage->materialUsageDetails as $detail) {
            $serviceItems = [];

            // Load service items for this detail
            foreach ($detail->materialUsageDetailItems as $item) {
                if ($item->service_detail_id) {
                    // Has service detail (nested structure)
                    $serviceItems[$item->service_id][$item->service_detail_id]['quantity'] = $item->quantity;
                } else {
                    // Direct service (no detail)
                    $serviceItems[$item->service_id]['quantity'] = $item->quantity;
                }
            }

            $this->details[] = [
                'stock_detail_id' => $detail->stock_detail_id,
                'type_id' => $detail->type_id,
                'type_detail_id' => $detail->type_detail_id,
                'rack_id' => $detail->rack_id,
                'item_code' => $detail->item_code,
                'number_serial_first' => $detail->number_serial_first,
                'number_serial_second' => $detail->number_serial_second,
                'quantity' => $detail->quantity,
                'available_quantity' => $detail->quantity,
                'usage_type' => $detail->usage_type,
                'description' => $detail->description ?? '',
                'service_items' => $serviceItems,
            ];

            // Load services for this type
            if ($detail->type_id) {
                $this->loadServicesForType($detail->type_id);
            }
        }
    }

    protected function loadDropdownData()
    {
        $this->types = Type::where('is_active', true)->orderBy('name')->get();
        $this->regionalPolices = RegionalPolice::where('is_active', true)->orderBy('name')->get();

        $user = auth()->user();
        if ($user->hasRole('Polda')) {
            $this->regionalPoliceId = $user->regional_police_id;
            $this->loadRacks();
            $this->loadStockDetails();
        }
    }

    public function addDetail()
    {
        $this->details[] = [
            'stock_detail_id' => '',
            'type_id' => '',
            'type_detail_id' => '',
            'rack_id' => '',
            'item_code' => '',
            'number_serial_first' => '',
            'number_serial_second' => '',
            'quantity' => 0,
            'available_quantity' => 0,
            'usage_type' => 'Material Digunakan',
            'description' => '',
            'service_items' => [],  // Hierarchical service/service detail quantities
        ];
    }

    public function removeDetail($index)
    {
        if (count($this->details) > 1) {
            unset($this->details[$index]);
            $this->details = array_values($this->details);
        }
    }

    public function updatedDetails($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1] ?? null;

        if ($field === 'stock_detail_id' && !empty($value)) {
            $stockDetail = StockDetail::find($value);
            if ($stockDetail) {
                $this->details[$index]['type_id'] = $stockDetail->type_id;
                $this->details[$index]['type_detail_id'] = $stockDetail->type_detail_id;
                $this->details[$index]['rack_id'] = $stockDetail->rack_id;
                $this->details[$index]['item_code'] = $stockDetail->code ?? '';
                $this->details[$index]['number_serial_first'] = $stockDetail->number_serial_first ?? '';
                $this->details[$index]['number_serial_second'] = $stockDetail->number_serial_second ?? '';
                $this->details[$index]['available_quantity'] = $stockDetail->quantity;

                // Load services for this type
                if ($stockDetail->type_id) {
                    $this->loadServicesForType($stockDetail->type_id);
                }
            }
        }

        if ($field === 'service_items') {
            $this->recalculateDetailQuantity($index);
        }
    }

    public function recalculateDetailQuantity($index): void
    {
        if (!isset($this->details[$index])) {
            return;
        }

        $serviceItems = $this->details[$index]['service_items'] ?? [];
        $total = 0;
        $hasServiceInputs = false;

        foreach ($serviceItems as $serviceId => $serviceData) {
            if (is_array($serviceData)) {
                if (isset($serviceData['quantity']) && is_numeric($serviceData['quantity'])) {
                    $hasServiceInputs = true;
                    $total += (float) $serviceData['quantity'];
                } else {
                    foreach ($serviceData as $detailId => $detailData) {
                        if (is_array($detailData) && isset($detailData['quantity']) && is_numeric($detailData['quantity'])) {
                            $hasServiceInputs = true;
                            $total += (float) $detailData['quantity'];
                        }
                    }
                }
            }
        }

        if ($hasServiceInputs && $total > 0) {
            $this->details[$index]['quantity'] = $total;
        }
    }

    public function updatedRegionalPoliceId()
    {
        $this->loadStockDetails();
        $this->loadRacks();
    }

    public function loadStockDetails()
    {
        $query = StockDetail::with(['type', 'typeDetail', 'rack'])
            ->where('is_active', true)
            ->where('quantity', '>', 0);

        if ($this->regionalPoliceId) {
            $query->where('regional_police_id', $this->regionalPoliceId);
        }

        $this->stockDetails = $query->get();
    }

    public function loadRacks()
    {
        $query = Rack::where('is_active', true);

        if ($this->regionalPoliceId) {
            $query->where('regional_police_id', $this->regionalPoliceId)->whereNull('police_station_id');
        }

        $this->racks = $query->orderBy('name')->get();
    }

    protected function rules(): array
    {
        return [
            'code' => 'required|string|max:255',
            'date' => 'required|date',
            'regionalPoliceId' => 'required|exists:regional_police,id',
            'details' => 'required|array|min:1',
            'details.*.stock_detail_id' => 'required|exists:stock_details,id',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.usage_type' => 'required|string',
            'details.*.description' => 'nullable|string|max:500',
        ];
    }

    protected $messages = [
        'code.required' => 'Kode penggunaan wajib diisi.',
        'date.required' => 'Tanggal penggunaan wajib diisi.',
        'date.date' => 'Format tanggal penggunaan tidak valid.',
        'regionalPoliceId.required' => 'Polda wajib dipilih.',
        'regionalPoliceId.exists' => 'Polda yang dipilih tidak valid.',
        'details.required' => 'Minimal harus ada 1 detail item material.',
        'details.min' => 'Minimal harus ada 1 detail item material.',
        'details.*.stock_detail_id.required' => 'Stok barang wajib dipilih dari daftar stok yang tersedia.',
        'details.*.stock_detail_id.exists' => 'Stok barang tidak valid atau sudah habis.',
        'details.*.quantity.required' => 'Jumlah material wajib diisi.',
        'details.*.quantity.numeric' => 'Jumlah harus berupa angka.',
        'details.*.quantity.min' => 'Jumlah minimal 1 unit.',
        'details.*.usage_type.required' => 'Jenis penggunaan wajib dipilih.',
    ];

    public function save()
    {
        // Auto-calculate quantity from service breakdown items if filled
        foreach ($this->details as $index => $detail) {
            $this->recalculateDetailQuantity($index);
        }

        $this->validate();

        // Validate stock availability for each item
        $hasError = false;
        $stockCounts = [];

        foreach ($this->details as $index => $detail) {
            $stockId = $detail['stock_detail_id'] ?? null;
            $qty = (float)($detail['quantity'] ?? 0);
            $stockDetail = StockDetail::find($stockId);
            $available = $stockDetail ? (float)$stockDetail->quantity : 0;

            if ($this->isEditMode) {
                // In edit mode, add back currently saved quantity if modifying same record
                $available = (float)($detail['available_quantity'] ?? $available);
            }

            if ($qty <= 0) {
                $this->addError("details.{$index}.quantity", "Jumlah minimal 1 unit.");
                $hasError = true;
            }

            if ($qty > $available) {
                $this->addError("details.{$index}.quantity", "Jumlah ({$qty}) melebihi stok tersedia ({$available}).");
                $hasError = true;
            }

            if ($stockId) {
                $stockCounts[$stockId] = ($stockCounts[$stockId] ?? 0) + $qty;
                if ($stockCounts[$stockId] > $available) {
                    $this->addError("details.{$index}.stock_detail_id", "Total penggunaan stok ini ({$stockCounts[$stockId]}) melebihi sisa stok ({$available}).");
                    $hasError = true;
                }
            }
        }

        if ($hasError) {
            session()->flash('error', 'Silakan periksa kembali isian formulir. Ada data yang belum sesuai.');
            return;
        }

        try {
            DB::transaction(function () {
                $headerData = [
                    'code' => $this->code,
                    'date' => $this->date,
                    'regional_police_id' => $this->regionalPoliceId,
                    'police_station_id' => $this->policeStationId,
                    'description' => $this->description,
                    'is_active' => true,
                ];

                if ($this->isEditMode) {
                    $materialUsage = MaterialUsage::findOrFail($this->materialUsageId);
                    $this->stockService->deleteMaterialUsage($materialUsage);
                    $materialUsage->update($headerData);
                    foreach ($materialUsage->materialUsageDetails as $oldDetail) {
                        $oldDetail->materialUsageDetailItems()->delete();
                    }
                    $materialUsage->materialUsageDetails()->delete();
                } else {
                    if (empty($headerData['code']) || MaterialUsage::withTrashed()->where('code', $headerData['code'])->exists()) {
                        $headerData['code'] = MaterialUsage::generateCode();
                    }
                    $materialUsage = MaterialUsage::create($headerData);
                }

                foreach ($this->details as $detail) {
                    $materialUsageDetail = $materialUsage->materialUsageDetails()->create([
                        'stock_detail_id' => $detail['stock_detail_id'],
                        'type_id' => $detail['type_id'],
                        'type_detail_id' => $detail['type_detail_id'],
                        'rack_id' => $detail['rack_id'],
                        'item_code' => $detail['item_code'],
                        'number_serial_first' => $detail['number_serial_first'],
                        'number_serial_second' => $detail['number_serial_second'],
                        'quantity' => (float)$detail['quantity'],
                        'usage_type' => $detail['usage_type'],
                        'description' => $detail['description'] ?? '',
                        'is_active' => true,
                    ]);

                    // Process service items if they exist
                    if (!empty($detail['service_items'])) {
                        $this->processServiceItems($materialUsage, $materialUsageDetail, $detail);
                    }
                }

                $materialUsage->load('materialUsageDetails');
                $this->stockService->processMaterialUsage($materialUsage);

                session()->flash('success', $this->isEditMode ? 'Data penggunaan material berhasil diperbarui.' : 'Data penggunaan material berhasil disimpan dan stok telah otomatis terpotong.');
            });

            return $this->redirect(route('menu-polda.material-usage'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function loadServicesForType($typeId)
    {
        if (!isset($this->services[$typeId])) {
            $this->services[$typeId] = Service::where('type_id', $typeId)
                ->where('is_active', true)
                ->withCount('details')
                ->with('details')
                ->orderBy('name')
                ->get();
        }
    }

    protected function processServiceItems($materialUsage, $materialUsageDetail, $detail)
    {
        foreach ($detail['service_items'] as $serviceId => $serviceData) {
            if (is_array($serviceData)) {
                // Check if this has service details (nested structure)
                $hasServiceDetails = false;
                foreach ($serviceData as $key => $value) {
                    if (is_array($value) && isset($value['quantity'])) {
                        $hasServiceDetails = true;
                        // Service Detail level quantity
                        $this->createDetailItemAndHistory(
                            $materialUsage,
                            $materialUsageDetail,
                            $detail,
                            $serviceId,
                            $key,  // service_detail_id
                            $value['quantity']
                        );
                    }
                }

                // If no service details, it's a direct service quantity
                if (!$hasServiceDetails && isset($serviceData['quantity'])) {
                    $this->createDetailItemAndHistory(
                        $materialUsage,
                        $materialUsageDetail,
                        $detail,
                        $serviceId,
                        null,  // no service_detail_id
                        $serviceData['quantity']
                    );
                }
            }
        }
    }

    protected function createDetailItemAndHistory($materialUsage, $materialUsageDetail, $detail, $serviceId, $serviceDetailId, $quantity)
    {
        if ($quantity <= 0) {
            return;
        }

        // Create MaterialUsageDetailItem
        $detailItem = MaterialUsageDetailItem::create([
            'material_usage_id' => $materialUsage->id,
            'material_usage_detail_id' => $materialUsageDetail->id,
            'stock_detail_id' => $detail['stock_detail_id'],
            'service_id' => $serviceId,
            'service_detail_id' => $serviceDetailId,
            'type_id' => $detail['type_id'],
            'type_detail_id' => $detail['type_detail_id'],
            'rack_id' => $detail['rack_id'],
            'item_code' => $detail['item_code'],
            'number_serial_first' => $detail['number_serial_first'],
            'number_serial_second' => $detail['number_serial_second'],
            'quantity' => $quantity,
            'usage_type' => $detail['usage_type'],
            'description' => $detail['description'] ?? '',
            'is_active' => true,
        ]);

        // Create HistoryStockDetail
        HistoryStockDetail::create([
            'code' => HistoryStockDetail::generateCode('MU'),
            'material_usage_detail_item_id' => $detailItem->id,
            'type_id' => $detail['type_id'],
            'type_detail_id' => $detail['type_detail_id'],
            'service_id' => $serviceId,
            'service_detail_id' => $serviceDetailId,
            'regional_police_id' => $materialUsage->police_station_id ? null : $materialUsage->regional_police_id,
            'police_station_id' => $materialUsage->police_station_id,
            'rack_id' => $detail['rack_id'],
            'date' => $materialUsage->date,
            'serial_number' => $detail['number_serial_first'],
            'status_type' => 'out',  // Material usage is an outflow
            'quantity' => $quantity,
            'description' => $detail['description'] ?? '',
            'is_active' => true,
        ]);
    }

    public function getServicesForType($typeId)
    {
        return $this->services[$typeId] ?? [];
    }

    public function hasServices($typeId)
    {
        $this->loadServicesForType($typeId);
        return !empty($this->services[$typeId]) && $this->services[$typeId]->count() > 0;
    }

    public function render()
    {
        return view('livewire.admin.menu-polda.material-usage.detail.admin-menu-polda-material-usage-detail-index')
            ->layout('components.layouts.main.app');
    }
}
