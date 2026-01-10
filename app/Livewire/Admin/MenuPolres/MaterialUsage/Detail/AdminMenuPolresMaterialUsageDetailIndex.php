<?php

namespace App\Livewire\Admin\MenuPolres\MaterialUsage\Detail;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetailItem;
use App\Models\Police\PoliceStation;
use App\Models\Rack\Rack;
use App\Models\Service\Service;
use App\Models\Stock\HistoryStockDetail;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminMenuPolresMaterialUsageDetailIndex extends Component
{
    public ?string $materialUsageId = null;
    public bool $isEditMode = false;

    // Header fields
    public string $code = '';
    public ?string $date = null;
    public ?string $policeStationId = null;
    public string $description = '';

    // Details array (batch)
    public $details = [];

    // Dropdown data
    public $stockDetails = [];
    public $types = [];
    public $typeDetails = [];
    public $racks = [];
    public $policeStations = [];
    protected $services = [];  // Services grouped by type_id
    protected $serviceDetails = [];  // Service details grouped by service_id

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
            $this->policeStationId = auth()->user()->police_station_id;
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
        $this->policeStationId = $materialUsage->police_station_id;
        $this->description = $materialUsage->description ?? '';

        // Load racks and stocks for this polres
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
        $this->policeStations = PoliceStation::where('is_active', true)->orderBy('name')->get();

        $user = auth()->user();
        if ($user->hasRole('Polres')) {
            $this->policeStationId = $user->police_station_id;
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

        // Calculate total quantity if service items are updated
        if ($field === 'service_items') {
            $this->calculateTotalQuantity($index);
        }
    }

    protected function calculateTotalQuantity($index)
    {
        $total = 0;
        if (isset($this->details[$index]['service_items']) && is_array($this->details[$index]['service_items'])) {
            foreach ($this->details[$index]['service_items'] as $serviceData) {
                if (is_array($serviceData)) {
                    // Check for direct quantity
                    if (isset($serviceData['quantity'])) {
                        $total += (float) $serviceData['quantity'];
                    }

                    // Check for nested details
                    foreach ($serviceData as $value) {
                         // sub-items are arrays with 'quantity'
                         if (is_array($value) && isset($value['quantity'])) {
                              $total += (float) $value['quantity'];
                         }
                    }
                }
            }
        }

        $available = $this->details[$index]['available_quantity'] ?? 0;

        if ($total > $available) {
            $this->addError("details.{$index}.quantity", "Jumlah total ({$total}) melebihi stok tersedia ({$available}).");
        } else {
             $this->resetErrorBag("details.{$index}.quantity");
        }

        $this->details[$index]['quantity'] = $total;
    }

    public function updatedPoliceStationId()
    {
        $this->loadStockDetails();
        $this->loadRacks();
    }

    public function loadStockDetails()
    {
        $query = StockDetail::with(['type', 'typeDetail', 'rack'])
            ->where('is_active', true)
            ->where('quantity', '>', 0);

        // Filter by UserType authorization
        $user = auth()->user();
        if ($user->userType && !empty($user->userType->types)) {
            $query->whereIn('type_id', $user->userType->types);
        }

        if ($this->policeStationId) {
            $query->where('police_station_id', $this->policeStationId);
        }

        $this->stockDetails = $query->get();
    }

    public function loadRacks()
    {
        $query = Rack::where('is_active', true);

        if ($this->policeStationId) {
            $query->where('police_station_id', $this->policeStationId);
        }

        $this->racks = $query->orderBy('name')->get();
    }

    protected function rules()
    {
        return [
            'code' => 'required|string|max:255',
            'date' => 'required|date',
            'policeStationId' => 'required|exists:police_stations,id',
            'details' => 'required|array|min:1',
            'details.*.stock_detail_id' => 'required|exists:stock_details,id',
            'details.*.quantity' => 'required|numeric|min:0',
            'details.*.usage_type' => 'required|string',
        ];
    }

    public function save()
    {
        $this->validate();

        // Custom validation for stock availability
        $hasError = false;
        foreach ($this->details as $index => $detail) {
            $quantity = (float) $detail['quantity'];
            $available = (float) ($detail['available_quantity'] ?? 0);

            if ($quantity > $available) {
                $this->addError("details.{$index}.quantity", "Jumlah total ({$quantity}) melebihi stok tersedia ({$available}).");
                $hasError = true;
            }
        }

        if ($hasError) {
            return;
        }

        try {
            DB::transaction(function () {
                $headerData = [
                    'code' => $this->code,
                    'date' => $this->date,
                    'police_station_id' => $this->policeStationId,
                    'description' => $this->description,
                    'is_active' => true,
                ];

                if ($this->isEditMode) {
                    $materialUsage = MaterialUsage::findOrFail($this->materialUsageId);
                    $materialUsage->update($headerData);
                    $materialUsage->materialUsageDetails()->delete();
                } else {
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
                        'quantity' => $detail['quantity'],
                        'usage_type' => $detail['usage_type'],
                        'description' => $detail['description'] ?? '',
                        'is_active' => true,
                    ]);

                    // Process service items if they exist
                    if (!empty($detail['service_items'])) {
                        $this->processServiceItems($materialUsage, $materialUsageDetail, $detail);
                    }
                }

                $this->stockService->processMaterialUsage($materialUsage);

                session()->flash('success', $this->isEditMode ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
            });

            return $this->redirect(route('menu-polres.material-usage'), navigate: true);
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

        // Get police_station for this material usage
        $policeStation = \App\Models\Police\PoliceStation::find($materialUsage->police_station_id);

        // Create HistoryStockDetail
        HistoryStockDetail::create([
            'code' => $materialUsage->code . '-' . uniqid(),
            'material_usage_detail_item_id' => $detailItem->id,
            'type_id' => $detail['type_id'],
            'type_detail_id' => $detail['type_detail_id'],
            'service_id' => $serviceId,
            'service_detail_id' => $serviceDetailId,
            'regional_police_id' => $policeStation->regional_police_id ?? null,
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
        return view('livewire.admin.menu-polres.material-usage.detail.admin-menu-polres-material-usage-detail-index')
            ->layout('components.layouts.main.app');
    }
}
