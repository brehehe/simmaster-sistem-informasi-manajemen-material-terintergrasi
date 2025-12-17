<?php

namespace App\Livewire\Admin\MenuPolres\MaterialUsage\Detail;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\Police\PoliceStation;
use App\Models\Rack\Rack;
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
        $materialUsage = MaterialUsage::with('materialUsageDetails')->findOrFail($this->materialUsageId);

        $this->code = $materialUsage->code;
        $this->date = $materialUsage->date->format('Y-m-d');
        $this->policeStationId = $materialUsage->police_station_id;
        $this->description = $materialUsage->description ?? '';

        // Load racks and stocks for this polda
        $this->loadRacks();
        $this->loadStockDetails();

        foreach ($materialUsage->materialUsageDetails as $detail) {
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
            ];
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
            'usage_type' => '',
            'description' => '',
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
            }
        }
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
                    $materialUsage->materialUsageDetails()->create([
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
                }

                $this->stockService->processMaterialUsage($materialUsage);

                session()->flash('success', $this->isEditMode ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
            });

            return $this->redirect(route('menu-polda.material-usage'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.menu-polda.material-usage.detail.admin-menu-polda-material-usage-detail-index')
            ->layout('components.layouts.main.app');
    }
}
