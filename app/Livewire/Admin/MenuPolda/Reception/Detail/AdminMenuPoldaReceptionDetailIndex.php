<?php

namespace App\Livewire\Admin\MenuPolda\Reception\Detail;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Rack\Rack;
use App\Models\Reception\Reception;
use App\Models\Reception\ReceptionDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Services\StockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Service\Service;
use App\Models\Reception\ReceptionDetailItem;
use App\Models\Stock\HistoryStockDetail;
use Livewire\Component;

class AdminMenuPoldaReceptionDetailIndex extends Component
{
    // Mode
    public ?string $receptionId = null;
    public bool $isEditMode = false;

    // Main Form
    public string $code = '';
    public string $name = '';
    public string $date = '';
    public string $type = '';
    public ?string $typeId = null;
    public bool $is_type_detail = false;
    public bool $is_with_serial_number = false;
    public ?string $regionalPoliceId = null;
    public ?string $policeStationId = null;
    public ?string $description = null;
    public bool $is_active = true;

    // Detail Items
    public array $details = [];
    public int $detailCounter = 0;
    public $services = []; 

    // Dropdowns Data
    public $regionalPolices = [];
    public $policeStations = [];
    public $types = [];
    public $typeDetails = [];
    public $racks = [];

    public function mount($id = null)
    {
        $this->receptionId = $id;
        $this->isEditMode = !is_null($id);

        $user = Auth::user();

        // Load dropdown data
        $this->regionalPolices = RegionalPolice::where('is_active', true)->orderBy('name')->get();
        $this->policeStations = PoliceStation::where('is_active', true)->orderBy('name')->get();
        $this->types = Type::where('is_active', true)->orderBy('name')->get();
        // Type details and racks will be loaded dynamically

        if ($this->isEditMode) {
            // Edit mode - load existing data
            $reception = Reception::with(['receptionDetails.receptionDetailItems'])->findOrFail($id);

            $this->code = $reception->code;
            $this->name = $reception->name;
            $this->date = $reception->date->format('Y-m-d');
            $this->type = $reception->type;
            $this->typeId = $reception->type_id;
            
            if ($this->typeId) {
                $typeRef = Type::find($this->typeId);
                $this->is_type_detail = $typeRef ? $typeRef->typeDetails->isNotEmpty() : false;
                $this->is_with_serial_number = $typeRef ? $typeRef->is_with_serial_number : false;
                $this->loadTypeData($this->typeId);
            }

            $this->regionalPoliceId = $reception->regional_police_id;
            $this->policeStationId = $reception->police_station_id;
            $this->description = $reception->description;
            $this->is_active = $reception->is_active;

            // Load detail items as a flat list
            foreach ($reception->receptionDetails as $detail) {
                foreach ($detail->receptionDetailItems as $item) {
                    $this->details[] = [
                        'id' => $item->id, // Use item's ID or leave null if just creating
                        'type_detail_id' => $item->type_detail_id ?? '',
                        'service_id' => $item->service_id ?? '',
                        'service_detail_id' => $item->service_detail_id ?? '',
                        'quantity' => (float)$item->quantity,
                        'code' => $item->item_code ?? '',
                        'number_serial_first' => $item->number_serial_first ?? '',
                        'number_serial_second' => $item->number_serial_second ?? '',
                        'is_active' => true,
                    ];
                }
            }
            if (empty($this->details)) {
                $this->addDetail();
            }
        } else {
            // Create mode
            $this->code = Reception::generateCode();
            $this->date = now()->format('Y-m-d');

            // Role-based: auto-fill regional_police_id for Polda
            if ($user->hasRole('Polda')) {
                $this->regionalPoliceId = $user->regional_police_id;
            }

            if ($user->hasRole('Polres')) {
                $this->policeStationId = $user->police_station_id;
            }

            $this->type = 'penerimaan';

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
                
                // If the selected service is bound to a specific TypeDetail, auto-select it
                if ($typeDetailId) {
                    $this->details[$index]['type_detail_id'] = $typeDetailId;
                }
                
                // Reset service detail
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
                
                // If the currently selected service doesn't match the newly selected type detail
                // Or if it's a global service (null type_detail_id) we can keep it, but UI logic is cleaner if we clear
                if ($svcTypeDetailId !== null && $svcTypeDetailId != $typeDetailId) {
                    $this->details[$index]['service_id'] = '';
                    $this->details[$index]['service_detail_id'] = '';
                }
            }
        }
    }

    public function updatedTypeId($value)
    {
        if ($value) {
            $typeRef = Type::find($value);
            $this->is_type_detail = $typeRef ? $typeRef->typeDetails->isNotEmpty() : false;
            $this->is_with_serial_number = $typeRef ? $typeRef->is_with_serial_number : false;
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
    }

    public function updatedDetails($value, $key) {
        $parts = explode('.', $key);
        $index = $parts[0] ?? null;
        $field = $parts[1] ?? null;
    }
    public function addDetail()
    {
        $this->details[] = [
            'id' => null,
            'type_detail_id' => '', 
            'service_id' => '', 
            'service_detail_id' => '', 
            'quantity' => 0, 
            'code' => '', 
            'number_serial_first' => '', 
            'number_serial_second' => '',
            'is_active' => true,
        ];
        $this->detailCounter++;
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details); // Re-index array
    }

    /**
     * Called when regional_police_id changes
     */
    public function updatedRegionalPoliceId($value)
    {
        // Reset police_station_id when regional_police changes
        $this->policeStationId = null;

        // Reload racks based on regional_police_id
        $this->loadRacks();
    }

    /**
     * Called when police_station_id changes
     */
    public function updatedPoliceStationId($value)
    {
        // Reload racks based on police_station_id
        $this->loadRacks();
    }

    /**
     * Load racks based on regional_police_id or police_station_id
     */
    public function loadRacks()
    {
        $query = Rack::where('is_active', true);

        if ($this->policeStationId) {
            // If police station is selected, show only racks for that police station
            $query->where('police_station_id', $this->policeStationId);
        } elseif ($this->regionalPoliceId) {
            // If only regional police is selected, show racks for that regional police
            $query->where('regional_police_id', $this->regionalPoliceId)
                ->whereNull('police_station_id');
        }

        $this->racks = $query->orderBy('name')->get();
    }

    /**
     * Get filtered type details for a specific type_id
     */
    public function getFilteredTypeDetails($typeId)
    {
        if (!$typeId) {
            return [];
        }

        return TypeDetail::where('type_id', $typeId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get filtered racks for detail items
     */
    public function getRacksForLocation()
    {
        $query = Rack::where('is_active', true);

        if ($this->policeStationId) {
            $query->where('police_station_id', $this->policeStationId);
        } elseif ($this->regionalPoliceId) {
            $query->where('regional_police_id', $this->regionalPoliceId)
                ->whereNull('police_station_id');
        }

        return $query->orderBy('name')->get();
    }

    protected function rules()
    {
        $user = Auth::user();

        $rules = [
            'name' => 'nullable|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'type' => 'required|in:stock-awal,penerimaan',
            'typeId' => 'required|exists:types,id',
            'is_active' => 'boolean',
            // Flat array validation structure
            'details.*.type_detail_id' => 'nullable|exists:type_details,id',
            'details.*.service_id' => 'nullable|exists:services,id',
            'details.*.service_detail_id' => 'nullable|exists:service_details,id',
            'details.*.quantity' => 'nullable|numeric|min:0',
            'details.*.code' => 'nullable|string|max:255',
            'details.*.number_serial_first' => 'nullable|string|max:255',
            'details.*.number_serial_second' => 'nullable|string|max:255',
        ];

        // Admin can select regional_police_id, Polda uses their own
        if (!$user->hasRole('Polda')) {
            $rules['regionalPoliceId'] = 'required|exists:regional_police,id';
        }

        if (!$user->hasRole('Polres')) {
            $rules['policeStationId'] = 'nullable|exists:police_stations,id';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'date.required' => 'Tanggal wajib diisi.',
            'type.required' => 'Tipe wajib dipilih.',
            'typeId.required' => 'Material wajib dipilih.',
            'regionalPoliceId.required' => 'Polda wajib dipilih.',
            'policeStationId.required' => 'Polres wajib dipilih.',
        ];
    }

    protected function loadTypeData($typeId)
    {
        if (!$typeId) {
            $this->typeDetails = collect();
            $this->services = collect();
            return;
        }
        
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

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $data = [
                'name' => $this->name,
                'date' => $this->date,
                'type' => $this->type,
                'type_id' => $this->typeId,
                'regional_police_id' => $this->regionalPoliceId,
                'police_station_id' => $this->policeStationId,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ];

            if ($this->isEditMode) {
                // Update existing record
                $reception = Reception::findOrFail($this->receptionId);
                $reception->update($data);

                // Delete existing details
                $reception->receptionDetails()->delete();
            } else {
                // Create new record
                $data['code'] = $this->code;
                $reception = Reception::create($data);
            }

            // Create a single ReceptionDetail parent for this reception
            $receptionDetail = ReceptionDetail::create([
                'reception_id' => $reception->id,
                'type_id' => $this->typeId ?: null,
                'type_detail_id' => null, 
                'code' => null,
                'number_serial_first' => null,
                'number_serial_second' => null,
                'quantity' => collect($this->details)->sum('quantity'),
                'description' => $this->description ?? '',
                'is_active' => true,
            ]);

            // Create detail items
            foreach ($this->details as $payload) {
                if (($payload['quantity'] ?? 0) > 0) {
                    $this->createDetailItemAndHistory($reception, $receptionDetail, $payload);
                }
            }

            // Process stock updates and history
            $stockService = new StockService();
            $reception->load('receptionDetails'); // Reload to get fresh details
            $stockService->processReception($reception);

            DB::commit();

            session()->flash('success', $this->isEditMode ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
            return $this->redirect(route('menu-polda.reception'), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function processServiceItems($reception, $receptionDetail, $detail)
    {
        foreach ($detail['service_items'] as $itemData) {
            $this->createDetailItemAndHistory(
                $reception, $receptionDetail, $detail, 
                $itemData
            );
        }
    }

    protected function createDetailItemAndHistory($reception, $receptionDetail, $payload)
    {
        $quantity = $payload['quantity'] ?? 0;
        if ($quantity <= 0) return;

        $typeDetailId = !empty($payload['type_detail_id']) ? $payload['type_detail_id'] : null;
        $serviceId = !empty($payload['service_id']) ? $payload['service_id'] : null;
        $serviceDetailId = !empty($payload['service_detail_id']) ? $payload['service_detail_id'] : null;
        $code = $payload['code'] ?? null;
        $sn1 = $payload['number_serial_first'] ?? null;
        $sn2 = $payload['number_serial_second'] ?? null;

        $detailItem = ReceptionDetailItem::create([
            'reception_id' => $reception->id,
            'reception_detail_id' => $receptionDetail->id,
            'service_id' => $serviceId,
            'service_detail_id' => $serviceDetailId,
            'type_id' => $this->typeId ?: null,
            'type_detail_id' => $typeDetailId,
            'item_code' => $code,
            'number_serial_first' => $sn1,
            'number_serial_second' => $sn2,
            'quantity' => $quantity,
            'description' => $receptionDetail->description ?? '',
            'is_active' => true,
        ]);

        $serialText = trim(implode(' ', array_filter([$code, $sn1, $sn2])));

        HistoryStockDetail::create([
            'code' => $reception->code . '-' . uniqid(),
            'reception_detail_item_id' => $detailItem->id,
            'type_id' => $this->typeId ?: null,
            'type_detail_id' => $typeDetailId,
            'service_id' => $serviceId,
            'service_detail_id' => $serviceDetailId,
            'regional_police_id' => $reception->regional_police_id,
            'police_station_id' => $reception->police_station_id,
            'date' => $reception->date,
            'serial_number' => $serialText ?: null,
            'status_type' => 'in',
            'quantity' => $quantity,
            'description' => $receptionDetail->description ?? '',
            'is_active' => true,
        ]);
    }

    public function render()
    {
        $user = Auth::user();
        $canSelectRegionalPolice = !$user->hasRole('Polda');
        $canSelectPoliceStation = !$user->hasRole('Polres');

        return view('livewire.admin.menu-polda.reception.detail.admin-menu-polda-reception-detail-index', [
            'canSelectRegionalPolice' => $canSelectRegionalPolice,
            'canSelectPoliceStation' => $canSelectPoliceStation,
        ])->layout('components.layouts.main.app');
    }
}
