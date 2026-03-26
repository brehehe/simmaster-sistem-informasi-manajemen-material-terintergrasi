<?php

namespace App\Livewire\Admin\MenuPolda\MaterialDamage\Detail;

use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\Police\RegionalPolice;
use App\Models\Police\PoliceStation;
use App\Models\Rack\Rack;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Service\Service;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdminMenuPoldaMaterialDamageDetailIndex extends Component
{
    public ?string $materialDamageId = null;
    public bool $isEditMode = false;

    // Header fields
    public string $code = '';
    public ?string $date = null;
    public ?string $regionalPoliceId = null;
    public ?string $policeStationId = null;
    public string $description = '';
    public ?string $typeId = null; // Adding typeId for global material

    // Computed properties for UI flags
    public bool $is_type_detail = false;
    public bool $is_with_serial_number = false;

    // Details array (batch)
    public $details = [];

    // Dropdown data
    public $types = [];
    public $typeDetails = [];
    public $services = [];
    public $racks = [];
    public $regionalPolices = [];

    protected StockService $stockService;

    public function boot(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function mount($id = null)
    {
        $this->materialDamageId = $id;
        $this->isEditMode = $id !== null;

        $this->loadDropdownData();

        if ($this->isEditMode) {
            $this->loadMaterialDamage();
        } else {
            $this->code = MaterialDamage::generateCode();
            $this->date = now()->format('Y-m-d');
            
            $user = Auth::user();
            if ($user->hasRole('Polda')) {
                $this->regionalPoliceId = $user->regional_police_id;
            } elseif ($user->hasRole('Polres')) {
                $this->policeStationId = $user->police_station_id;
            }

            $this->addDetail();
        }
    }

    protected function loadDropdownData()
    {
        $this->types = Type::where('is_active', true)->orderBy('name')->get();
        $this->regionalPolices = RegionalPolice::where('is_active', true)->orderBy('name')->get();
        // Assuming racks aren't heavily used if we auto-map from stock. We'll leave it out of the grid unless needed.
    }

    protected function loadTypeData($typeId)
    {
        if (!$typeId) {
            $this->typeDetails = collect();
            $this->services = collect();
            return;
        }

        $typeRef = Type::find($typeId);
        $this->is_type_detail = $typeRef ? $typeRef->typeDetails->isNotEmpty() : false;
        $this->is_with_serial_number = $typeRef ? $typeRef->is_with_serial_number : false;
        
        $this->typeDetails = TypeDetail::where('type_id', $typeId)->where('is_active', true)->orderBy('name')->get();
        $this->services = Service::with('details')
            ->where('is_active', true)
            ->where(function($q) use ($typeId) {
                $q->where('type_id', $typeId)
                  ->orWhereIn('type_detail_id', TypeDetail::where('type_id', $typeId)->pluck('id'));
            })
            ->orderBy('name')
            ->get();        
    }

    public function updatedTypeId($value)
    {
        if ($value) {
            $this->loadTypeData($value);
        } else {
            $this->is_type_detail = false;
            $this->is_with_serial_number = false;
            $this->typeDetails = [];
            $this->services = [];
        }

        // Clear existing details when type changes
        $this->details = [];
        $this->addDetail();
        
        // Trigger stock check for all details
        foreach ($this->details as $index => $detail) {
            $this->checkAvailableStock($index);
        }
    }

    protected function loadMaterialDamage()
    {
        $materialDamage = MaterialDamage::with('materialDamageDetails.stockDetail')->findOrFail($this->materialDamageId);

        $this->code = $materialDamage->code;
        $this->date = $materialDamage->date->format('Y-m-d');
        $this->regionalPoliceId = $materialDamage->regional_police_id;
        $this->policeStationId = $materialDamage->police_station_id;
        $this->description = $materialDamage->description ?? '';

        // Derive typeId from the first detail (since MaterialDamage header doesn't have it natively)
        if ($materialDamage->materialDamageDetails->isNotEmpty()) {
            $firstDetail = $materialDamage->materialDamageDetails->first();
            $this->typeId = $firstDetail->type_id;
            $this->loadTypeData($this->typeId);
        }

        foreach ($materialDamage->materialDamageDetails as $index => $detail) {
            $stock = $detail->stockDetail;
            $this->details[] = [
                'type_detail_id' => $detail->type_detail_id ?? '',
                'service_id' => $stock ? ($stock->service_id ?? '') : '',
                'service_detail_id' => $stock ? ($stock->service_detail_id ?? '') : '',
                'item_code' => $detail->item_code ?? '',
                'number_serial_first' => $detail->number_serial_first ?? '',
                'number_serial_second' => $detail->number_serial_second ?? '',
                'quantity' => (float)$detail->quantity,
                'damage_type' => $detail->damage_type ?? 'damaged',
                'reason' => $detail->reason ?? '',
                'available_quantity' => $stock ? $stock->quantity : 0, 
                'available_stocks' => [],
                'selected_stock_key' => $this->generateStockKey([
                    'code' => $detail->item_code,
                    'number_serial_first' => $detail->number_serial_first,
                    'number_serial_second' => $detail->number_serial_second
                ]),
            ];
            $this->loadStockOptions($index);
        }

        if (empty($this->details)) {
            $this->addDetail();
        }
    }

    public function updated($propertyName)
    {
        // Auto-fill type_detail_id when service_id is selected
        if (preg_match('/^details\.(\d+)\.service_id$/', $propertyName, $matches)) {
            $index = $matches[1];
            $serviceId = $this->details[$index]['service_id'];
            
            if ($serviceId) {
                $service = collect($this->services)->firstWhere('id', $serviceId);
                $typeDetailId = data_get($service, 'type_detail_id');
                
                if ($typeDetailId) {
                    $this->details[$index]['type_detail_id'] = $typeDetailId;
                }
                
                $this->details[$index]['service_detail_id'] = '';
            }
        }
        
        // Clear service_id if type_detail_id changes and current service doesn't belong to it
        if (preg_match('/^details\.(\d+)\.type_detail_id$/', $propertyName, $matches)) {
            $index = $matches[1];
            $typeDetailId = $this->details[$index]['type_detail_id'];
            $serviceId = $this->details[$index]['service_id'];
            
            if ($serviceId) {
                $service = collect($this->services)->firstWhere('id', $serviceId);
                $svcTypeDetailId = data_get($service, 'type_detail_id');
                
                if ($svcTypeDetailId !== null && $svcTypeDetailId != $typeDetailId) {
                    $this->details[$index]['service_id'] = '';
                    $this->details[$index]['service_detail_id'] = '';
                }
            }
        }

        // Auto check available stock quantity when any component field changes
        if (preg_match('/^details\.(\d+)\.(type_detail_id|service_id|service_detail_id|item_code|number_serial_first|number_serial_second)$/', $propertyName, $matches)) {
            $index = $matches[1];
            $this->checkAvailableStock($index);
        }

        // Reset dependent fields and Load stock options (Code, SN1, SN2) when parent components change
        if (preg_match('/^details\.(\d+)\.(type_detail_id|service_id|service_detail_id)$/', $propertyName, $matches)) {
            $index = $matches[1];
            $this->details[$index]['item_code'] = '';
            $this->details[$index]['number_serial_first'] = '';
            $this->details[$index]['number_serial_second'] = '';
            $this->details[$index]['selected_stock_key'] = '';
            $this->loadStockOptions($index);
        }

        if (preg_match('/^details\.(\d+)\.selected_stock_key$/', $propertyName, $matches)) {
            $index = $matches[1];
            $key = $this->details[$index]['selected_stock_key'];
            
            if ($key) {
                // Split back the parts or find the stock
                // Format used: "Code | SN1 | SN2"
                $parts = explode(' | ', $key);
                $this->details[$index]['item_code'] = $parts[0] !== '-' ? $parts[0] : '';
                $this->details[$index]['number_serial_first'] = isset($parts[1]) && $parts[1] !== '-' ? $parts[1] : '';
                $this->details[$index]['number_serial_second'] = isset($parts[2]) && $parts[2] !== '-' ? $parts[2] : '';
            } else {
                $this->details[$index]['item_code'] = '';
                $this->details[$index]['number_serial_first'] = '';
                $this->details[$index]['number_serial_second'] = '';
            }
            
            $this->checkAvailableStock($index);
        }
    }

    public function loadStockOptions($index)
    {
        if (!$this->typeId || (!$this->regionalPoliceId && !Auth::user()->hasRole('Polda'))) return;

        $detail = $this->details[$index];
        
        $query = StockDetail::where('type_id', $this->typeId)
            ->where('is_active', true);

        if ($this->policeStationId) {
            $query->where('police_station_id', $this->policeStationId);
        } elseif ($this->regionalPoliceId) {
            $query->where('regional_police_id', $this->regionalPoliceId)
                  ->whereNull('police_station_id');
        }

        // Apply filters for Type Detail and Service
        foreach (['type_detail_id', 'service_id'] as $field) {
            $val = !empty($detail[$field]) ? $detail[$field] : null;
            if ($val !== null) {
                $query->where($field, $val);
            }
        }

        // Apply filters for Code and SN1 to filter subsequent selects
        if (!empty($detail['item_code'])) {
            $query->where('code', $detail['item_code']);
        }
        if (!empty($detail['number_serial_first'])) {
            $query->where('number_serial_first', $detail['number_serial_first']);
        }

        $stocks = $query->get(['code', 'number_serial_first', 'number_serial_second', 'quantity']);

        $options = [];
        foreach ($stocks as $stock) {
            $key = $this->generateStockKey($stock);
            $options[] = [
                'value' => $key,
                'label' => $key . " (Tersedia: " . number_format($stock->quantity, 0, ',', '.') . ")"
            ];
        }

        $this->details[$index]['available_stocks'] = $options;
        
        $this->dispatch('stock-options-updated', index: $index);
    }

    protected function generateStockKey($stock)
    {
        $code = !empty($stock['code']) ? $stock['code'] : '-';
        $sn1 = !empty($stock['number_serial_first']) ? $stock['number_serial_first'] : '-';
        $sn2 = !empty($stock['number_serial_second']) ? $stock['number_serial_second'] : '-';
        
        return "{$code} | {$sn1} | {$sn2}";
    }

    public function checkAvailableStock($index)
    {
        if (!$this->typeId || (!$this->regionalPoliceId && !Auth::user()->hasRole('Polda'))) return;

        $detail = $this->details[$index];
        
        $query = StockDetail::where('type_id', $this->typeId)
            ->where('is_active', true);

        if ($this->policeStationId) {
            $query->where('police_station_id', $this->policeStationId);
        } elseif ($this->regionalPoliceId) {
            $query->where('regional_police_id', $this->regionalPoliceId)
                  ->whereNull('police_station_id');
        }

        // Helper for flexible NULL/Empty matches
        $flexibleWhere = function($q, $column, $value, $isUuid = false) {
            $val = !empty($value) ? $value : null;
            if ($val === null) {
                $q->where(function($sq) use ($column, $isUuid) {
                    $sq->whereNull($column);
                    if (!$isUuid) {
                        $sq->orWhere($column, '');
                    }
                });
            } else {
                $q->where($column, $val);
            }
        };

        $flexibleWhere($query, 'type_detail_id', $detail['type_detail_id'], true);
        $flexibleWhere($query, 'service_id', $detail['service_id'], true);
        $flexibleWhere($query, 'service_detail_id', $detail['service_detail_id'], true);
        $flexibleWhere($query, 'code', $detail['item_code']);
        $flexibleWhere($query, 'number_serial_first', $detail['number_serial_first']);
        $flexibleWhere($query, 'number_serial_second', $detail['number_serial_second']);

        $this->details[$index]['available_quantity'] = $query->sum('quantity');
    }

    public function updatedRegionalPoliceId()
    {
        
        // Trigger stock check for all details
        foreach ($this->details as $index => $detail) {
            $this->checkAvailableStock($index);
        }
    }

    public function updatedPoliceStationId()
    {
        
        // Trigger stock check for all details
        foreach ($this->details as $index => $detail) {
            $this->checkAvailableStock($index);
        }
    }

    public function addDetail()
    {
        $this->details[] = [
            'type_detail_id' => '',
            'service_id' => '',
            'service_detail_id' => '',
            'item_code' => '',
            'number_serial_first' => '',
            'number_serial_second' => '',
            'quantity' => 0,
            'available_quantity' => 0,
            'damage_type' => 'damaged',
            'reason' => '',
            'available_stocks' => [],
            'selected_stock_key' => '',
        ];
        
        $index = count($this->details) - 1;
        $this->loadStockOptions($index);
    }

    public function removeDetail($index)
    {
        if (count($this->details) > 1) {
            unset($this->details[$index]);
            $this->details = array_values($this->details);
        }
    }

    public function save()
    {
        // Manual validation for dynamic stock checking
        $this->validate([
            'code' => 'required|string|max:255',
            'date' => 'required|date',
            'typeId' => 'required|exists:types,id',
            'details' => 'required|array|min:1',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.damage_type' => 'required|in:damaged,lost',
            'details.*.reason' => 'required|string',
        ]);

        if (!Auth::user()->hasRole('Polda')) {
            $this->validate(['regionalPoliceId' => 'required|exists:regional_police,id']);
        }

        $mappedStockIds = [];

        // Validate stock quantities before proceeding
        foreach ($this->details as $index => $detail) {
            $this->checkAvailableStock($index);

            if ($this->details[$index]['available_quantity'] <= 0) {
                $this->addError("details.{$index}.quantity", "Stock barang untuk kombinasi ini tidak tersedia atau kosong.");
                return;
            }

            if ($detail['quantity'] > $this->details[$index]['available_quantity']) {
                $this->addError("details.{$index}.quantity", "Kuantitas melebihi stok yang tersedia (" . $this->details[$index]['available_quantity'] . ").");
                return;
            }

            // Retrieve the explicit StockDetail to map it correctly
            $query = StockDetail::where('type_id', $this->typeId)
                ->where('is_active', true);

            if ($this->policeStationId) {
                $query->where('police_station_id', $this->policeStationId);
            } else {
                $query->where('regional_police_id', $this->regionalPoliceId)
                      ->whereNull('police_station_id');
            }

            // Use flexible matching for optional fields (UUIDs)
            foreach (['type_detail_id', 'service_id', 'service_detail_id'] as $field) {
                $val = !empty($detail[$field]) ? $detail[$field] : null;
                if ($val === null) {
                    $query->whereNull($field);
                } else {
                    $query->where($field, $val);
                }
            }

            foreach (['code' => 'item_code', 'number_serial_first' => 'number_serial_first', 'number_serial_second' => 'number_serial_second'] as $dbCol => $arrayKey) {
                $val = !empty($detail[$arrayKey]) ? $detail[$arrayKey] : null;
                if ($val === null) {
                    $query->where(function($q) use ($dbCol) { $q->whereNull($dbCol)->orWhere($dbCol, ''); });
                } else {
                    $query->where($dbCol, $val);
                }
            }

            $stock = $query->first();

            if (!$stock) {
                 $this->addError("details.{$index}.quantity", "Stock tidak ditemukan di database untuk baris ini.");
                 return;
            }

            $mappedStockIds[$index] = $stock->id;
        }

        try {
            DB::transaction(function () use ($mappedStockIds) {
                $headerData = [
                    'code' => $this->code,
                    'date' => $this->date,
                    'regional_police_id' => $this->regionalPoliceId,
                    'police_station_id' => $this->policeStationId,
                    'description' => $this->description,
                    'is_active' => true,
                ];

                if ($this->isEditMode) {
                    $materialDamage = MaterialDamage::findOrFail($this->materialDamageId);
                    $materialDamage->update($headerData);
                    $materialDamage->materialDamageDetails()->delete();
                } else {
                    $materialDamage = MaterialDamage::create($headerData);
                }

                foreach ($this->details as $index => $detail) {
                    $materialDamage->materialDamageDetails()->create([
                        'stock_detail_id' => $mappedStockIds[$index],
                        'type_id' => $this->typeId,
                        'type_detail_id' => !empty($detail['type_detail_id']) ? $detail['type_detail_id'] : null,
                        'rack_id' => null, // Assuming rack is inherently mapped by stock_detail_id, so we ignore it here
                        'item_code' => !empty($detail['item_code']) ? $detail['item_code'] : null,
                        'number_serial_first' => !empty($detail['number_serial_first']) ? $detail['number_serial_first'] : null,
                        'number_serial_second' => !empty($detail['number_serial_second']) ? $detail['number_serial_second'] : null,
                        'quantity' => $detail['quantity'],
                        'damage_type' => $detail['damage_type'],
                        'reason' => $detail['reason'],
                        'description' => $detail['description'] ?? '',
                        'is_active' => true,
                    ]);
                }

                if ($this->isEditMode) {
                    // In edit mode, we'd need to restore old stocks and subtract new. 
                    // To keep it simple, processMaterialDamage handles it cleanly if written defensively, or we do it here.
                    // The stockService->processMaterialDamage might need to know old sums vs new sums.
                }

                $this->stockService->processMaterialDamage($materialDamage);

                session()->flash('success', $this->isEditMode ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
            });

            return $this->redirect(route('menu-polda.material-damage'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        $canSelectRegionalPolice = !$user->hasRole('Polda');

        return view('livewire.admin.menu-polda.material-damage.detail.admin-menu-polda-material-damage-detail-index', [
            'canSelectRegionalPolice' => $canSelectRegionalPolice
        ])->layout('components.layouts.main.app');
    }
}

