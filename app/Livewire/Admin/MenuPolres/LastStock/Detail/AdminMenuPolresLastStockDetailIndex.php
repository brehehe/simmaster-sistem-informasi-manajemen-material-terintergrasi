<?php

namespace App\Livewire\Admin\MenuPolres\LastStock\Detail;

use App\Models\LastStock\LastStock;
use App\Models\LastStock\LastStockDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Rack\Rack;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Service\Service;
use App\Services\StockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminMenuPolresLastStockDetailIndex extends Component
{
    // Mode
    public ?string $lastStockId = null;
    public bool $isEditMode = false;

    // Main Form
    public string $code = '';
    public string $name = '';
    public string $date = '';
    public ?string $typeId = null; 
    public bool $is_with_serial_number = false;
    public ?string $policeStationId = null;
    public ?string $description = null;
    public bool $is_active = true;

    // Detail Items
    public array $details = [];
    public int $detailCounter = 0;
    public $services = [];

    // Dropdowns Data
    public $policeStations = [];
    public $types = [];
    public $typeDetails = [];
    public $racks = [];

    public function mount($id = null)
    {
        $this->lastStockId = $id;
        $this->isEditMode = !is_null($id);

        $user = Auth::user();

        // Load dropdown data
        $this->policeStations = PoliceStation::where('is_active', true)->orderBy('name')->get();
        $this->types = Type::where('is_active', true)->orderBy('name')->get();

        if ($this->isEditMode) {
            // Edit mode - load existing data
            $lastStock = LastStock::with('lastStockDetails')->findOrFail($id);

            $this->code = $lastStock->code;
            $this->name = $lastStock->name;
            $this->date = $lastStock->date->format('Y-m-d');
            $this->typeId = $lastStock->type_id;
            $this->policeStationId = $lastStock->police_station_id;
            $this->description = $lastStock->description;
            $this->is_active = $lastStock->is_active;

            if ($this->typeId) {
                $typeRef = Type::find($this->typeId);
                $this->is_with_serial_number = $typeRef ? $typeRef->is_with_serial_number : false;
                $this->loadTypeData($this->typeId);
            }

            // Load detail items
            foreach ($lastStock->lastStockDetails as $detail) {
                $this->details[] = [
                    'id' => $detail->id,
                    'type_detail_id' => $detail->type_detail_id ?? '',
                    'service_id' => $detail->service_id ?? '',
                    'service_detail_id' => $detail->service_detail_id ?? '',
                    'rack_id' => $detail->rack_id ?? '',
                    'code' => $detail->code ?? '',
                    'number_serial_first' => $detail->number_serial_first ?? '',
                    'number_serial_second' => $detail->number_serial_second ?? '',
                    'quantity' => (float)$detail->quantity,
                    'is_active' => $detail->is_active,
                ];
            }
        } else {
            // Create mode
            $this->code = LastStock::generateCode();
            $this->date = now()->format('Y-m-d');

            if ($user->hasRole('Polres')) {
                $this->policeStationId = $user->police_station_id;
            }

            $this->addDetail();
        }
        $this->loadRacks();
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
    }

    public function updatedTypeId($value)
    {
        if ($value) {
            $typeRef = Type::find($value);
            $this->is_with_serial_number = $typeRef ? $typeRef->is_with_serial_number : false;
            $this->loadTypeData($value);
        } else {
            $this->is_with_serial_number = false;
            $this->typeDetails = [];
            $this->services = [];
        }

        // Clear existing details when master type changes
        $this->details = [];
        $this->addDetail();
    }

    protected function loadTypeData($typeId)
    {
        if (!$typeId) return;
        
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

    public function addDetail()
    {
        $this->details[] = [
            'id' => null,
            'type_detail_id' => '',
            'service_id' => '',
            'service_detail_id' => '',
            'rack_id' => '',
            'code' => '',
            'number_serial_first' => '',
            'number_serial_second' => '',
            'quantity' => 0,
            'is_active' => true,
        ];
        $this->detailCounter++;
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details);
    }

    public function updatedPoliceStationId($value)
    {
        $this->loadRacks();
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
        $user = Auth::user();

        $rules = [
            'name' => 'nullable|string|max:255',
            'date' => 'required|date',
            'typeId' => 'required|exists:types,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'details.*.type_detail_id' => 'nullable|exists:type_details,id',
            'details.*.service_id' => 'nullable|exists:services,id',
            'details.*.service_detail_id' => 'nullable|exists:service_details,id',
            'details.*.rack_id' => 'nullable|exists:racks,id',
            'details.*.code' => 'nullable|string|max:255',
            'details.*.number_serial_first' => 'nullable|string|max:255',
            'details.*.number_serial_second' => 'nullable|string|max:255',
            'details.*.quantity' => 'required|numeric|min:0',
        ];

        if (!$user->hasRole('Polres')) {
            $rules['policeStationId'] = 'nullable|exists:police_stations,id';
        }

        return $rules;
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $policeStation = null;
            if ($this->policeStationId) {
                $policeStation = PoliceStation::find($this->policeStationId);
            }

            $data = [
                'name' => $this->name,
                'date' => $this->date,
                'type_id' => $this->typeId,
                'regional_police_id' => $policeStation ? $policeStation->regional_police_id : null,
                'police_station_id' => $this->policeStationId,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ];

            if ($this->isEditMode) {
                $lastStock = LastStock::findOrFail($this->lastStockId);
                $lastStock->update($data);
                $lastStock->lastStockDetails()->delete();
            } else {
                $data['code'] = $this->code;
                $lastStock = LastStock::create($data);
            }

            foreach ($this->details as $detail) {
                if (($detail['quantity'] ?? 0) > 0) {
                    LastStockDetail::create([
                        'last_stock_id' => $lastStock->id,
                        'type_id' => $this->typeId,
                        'type_detail_id' => $detail['type_detail_id'] ?: null,
                        'service_id' => $detail['service_id'] ?: null,
                        'service_detail_id' => $detail['service_detail_id'] ?: null,
                        'rack_id' => $detail['rack_id'] ?: null,
                        'code' => $detail['code'],
                        'number_serial_first' => $detail['number_serial_first'],
                        'number_serial_second' => $detail['number_serial_second'],
                        'quantity' => $detail['quantity'],
                        'is_active' => $detail['is_active'] ?? true,
                    ]);
                }
            }

            $stockService = new StockService();
            $lastStock->load('lastStockDetails');
            $stockService->processLastStock($lastStock);

            DB::commit();

            session()->flash('success', $this->isEditMode ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
            return $this->redirect(route('menu-polres.last-stock'), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        $canSelectPoliceStation = !$user->hasRole('Polres');

        return view('livewire.admin.menu-polres.last-stock.detail.admin-menu-polres-last-stock-detail-index', [
            'canSelectPoliceStation' => $canSelectPoliceStation,
        ])->layout('components.layouts.main.app');
    }
}
