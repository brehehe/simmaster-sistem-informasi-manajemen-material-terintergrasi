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
    public string $code = ''; // Nomor SPPM
    public string $name = '';
    public string $date = ''; // Tanggal BAPPM
    public string $type = '';
    public ?string $typeId = null;
    public bool $is_type_detail = false;
    public bool $is_with_serial_number = false;
    public ?string $regionalPoliceId = null;
    public ?string $policeStationId = null;
    public ?string $description = null;
    public bool $is_active = true;

    // BAPPM & SPPM Fields
    public string $sppm_date = '';
    public string $bappm_number = '';
    
    // Commission members
    public string $commission_member_1_name = '';
    public string $commission_member_1_rank = '';
    public string $commission_member_1_nip = '';
    public string $commission_member_1_position = '';

    public string $commission_member_2_name = '';
    public string $commission_member_2_rank = '';
    public string $commission_member_2_nip = '';
    public string $commission_member_2_position = '';

    public string $commission_member_3_name = '';
    public string $commission_member_3_rank = '';
    public string $commission_member_3_nip = '';
    public string $commission_member_3_position = '';

    // Kasi Fasmat
    public string $kasi_fasmat_name = '';
    public string $kasi_fasmat_rank = '';
    public string $kasi_fasmat_nip = '';

    // Ordonatur
    public string $ordonatur_name = '';
    public string $ordonatur_rank = '';

    // Detail Items
    public array $details = [];
    public int $detailCounter = 0;
    public $services = []; 
    public array $supportingMaterials = [];

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

            $this->sppm_date = $reception->sppm_date ? $reception->sppm_date->format('Y-m-d') : '';
            $this->bappm_number = $reception->bappm_number ?? '';
            $this->commission_member_1_name = $reception->commission_member_1_name ?? '';
            $this->commission_member_1_rank = $reception->commission_member_1_rank ?? '';
            $this->commission_member_1_nip = $reception->commission_member_1_nip ?? '';
            $this->commission_member_1_position = $reception->commission_member_1_position ?? '';
            $this->commission_member_2_name = $reception->commission_member_2_name ?? '';
            $this->commission_member_2_rank = $reception->commission_member_2_rank ?? '';
            $this->commission_member_2_nip = $reception->commission_member_2_nip ?? '';
            $this->commission_member_2_position = $reception->commission_member_2_position ?? '';
            $this->commission_member_3_name = $reception->commission_member_3_name ?? '';
            $this->commission_member_3_rank = $reception->commission_member_3_rank ?? '';
            $this->commission_member_3_nip = $reception->commission_member_3_nip ?? '';
            $this->commission_member_3_position = $reception->commission_member_3_position ?? '';
            $this->kasi_fasmat_name = $reception->kasi_fasmat_name ?? '';
            $this->kasi_fasmat_rank = $reception->kasi_fasmat_rank ?? '';
            $this->kasi_fasmat_nip = $reception->kasi_fasmat_nip ?? '';
            $this->ordonatur_name = $reception->ordonatur_name ?? '';
            $this->ordonatur_rank = $reception->ordonatur_rank ?? '';
            
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
                    // Only load as details if the item belongs to the main type (not supporting)
                    if ($item->type_id === $this->typeId) {
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
            }
            if (empty($this->details)) {
                $this->addDetail();
            }

            // Load supporting materials
            $this->loadSupportingMaterials($this->typeId);
        } else {
            // Create mode
            $this->code = ''; // Users input this manually (Nomor SPPM)
            $this->date = now()->format('Y-m-d');
            $this->sppm_date = now()->format('Y-m-d');
            
            // Generate auto BAPPM number template
            $year = date('Y');
            $monthRoman = $this->getRomanMonth(date('n'));
            $this->bappm_number = 'BAPPM /       /' . $monthRoman . '/' . $year . '/Ditlantas';

            // Set default komisi & pejabat TNKB
            $this->commission_member_1_name = 'YANTO MULYANTO P, S.H., S.I.K., M.H., M.Si.';
            $this->commission_member_1_rank = 'AKBP';
            $this->commission_member_1_nip = '86052014';
            $this->commission_member_1_position = 'KASUBDIT REGIDENT';

            $this->commission_member_2_name = 'MADE DAMENDRA, S.H.';
            $this->commission_member_2_rank = 'IPTU';
            $this->commission_member_2_nip = '84071376';
            $this->commission_member_2_position = 'PAMIN I FASMAT SBST';

            $this->commission_member_3_name = 'PUJI ISWANTO, S.H.';
            $this->commission_member_3_rank = 'AIPTU';
            $this->commission_member_3_nip = '76080941';
            $this->commission_member_3_position = 'BAUR FASMAT SBST';

            $this->kasi_fasmat_name = 'AYIP RIZAL, S.E., M.M.';
            $this->kasi_fasmat_rank = 'KOMPOL';
            $this->kasi_fasmat_nip = '84091823';

            $this->ordonatur_name = 'IWAN SAKTIADI, S.I.K., M.M., M.Si';
            $this->ordonatur_rank = 'BRIGADIR JENDERAL POLISI';

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

    private function getRomanMonth($month)
    {
        $map = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        return $map[$month] ?? 'I';
    }

    public function loadSupportingMaterials($typeId)
    {
        if (!$typeId) {
            $this->supportingMaterials = [];
            return;
        }

        $children = Type::where('parent_id', $typeId)->where('is_active', true)->orderBy('name')->get();
        
        $existingItems = [];
        if ($this->isEditMode) {
            $reception = Reception::with(['receptionDetails.receptionDetailItems'])->find($this->receptionId);
            if ($reception) {
                foreach ($reception->receptionDetails as $detail) {
                    foreach ($detail->receptionDetailItems as $item) {
                        if ($item->type_id && $item->type_id !== $typeId) {
                            $existingItems[$item->type_id] = $item;
                        }
                    }
                }
            }
        }

        $this->supportingMaterials = [];
        foreach ($children as $child) {
            $existing = $existingItems[$child->id] ?? null;
            $this->supportingMaterials[] = [
                'type_id' => $child->id,
                'name' => $child->name,
                'quantity' => $existing ? (float)$existing->quantity : 0,
                'description' => $existing ? $existing->description : '',
            ];
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
            $this->loadSupportingMaterials($value);
        } else {
            $this->is_type_detail = false;
            $this->is_with_serial_number = false;
            $this->typeDetails = [];
            $this->services = [];
            $this->supportingMaterials = [];
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
            'code' => 'required|string|max:255|unique:receptions,code,' . ($this->receptionId ?? 'NULL') . ',id',
            'name' => 'nullable|string|max:255',
            'date' => 'required|date',
            'sppm_date' => 'required|date',
            'bappm_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:stock-awal,penerimaan',
            'typeId' => 'required|exists:types,id',
            'is_active' => 'boolean',

            // Commission members
            'commission_member_1_name' => 'nullable|string|max:255',
            'commission_member_1_rank' => 'nullable|string|max:255',
            'commission_member_1_nip' => 'nullable|string|max:255',
            'commission_member_1_position' => 'nullable|string|max:255',
            'commission_member_2_name' => 'nullable|string|max:255',
            'commission_member_2_rank' => 'nullable|string|max:255',
            'commission_member_2_nip' => 'nullable|string|max:255',
            'commission_member_2_position' => 'nullable|string|max:255',
            'commission_member_3_name' => 'nullable|string|max:255',
            'commission_member_3_rank' => 'nullable|string|max:255',
            'commission_member_3_nip' => 'nullable|string|max:255',
            'commission_member_3_position' => 'nullable|string|max:255',

            // Pejabat
            'kasi_fasmat_name' => 'nullable|string|max:255',
            'kasi_fasmat_rank' => 'nullable|string|max:255',
            'kasi_fasmat_nip' => 'nullable|string|max:255',
            'ordonatur_name' => 'nullable|string|max:255',
            'ordonatur_rank' => 'nullable|string|max:255',

            // Flat array validation structure
            'details.*.type_detail_id' => 'nullable|exists:type_details,id',
            'details.*.service_id' => 'nullable|exists:services,id',
            'details.*.service_detail_id' => 'nullable|exists:service_details,id',
            'details.*.quantity' => 'nullable|numeric|min:0',
            'details.*.code' => 'nullable|string|max:255',
            'details.*.number_serial_first' => 'nullable|string|max:255',
            'details.*.number_serial_second' => 'nullable|string|max:255',

            // Supporting materials validation
            'supportingMaterials.*.quantity' => 'nullable|numeric|min:0',
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
            'code.required' => 'Nomor SPPM wajib diisi.',
            'code.unique' => 'Nomor SPPM sudah terdaftar.',
            'name.required' => 'Nama wajib diisi.',
            'date.required' => 'Tanggal BAPPM wajib diisi.',
            'sppm_date.required' => 'Tanggal SPPM wajib diisi.',
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
                'sppm_date' => $this->sppm_date ?: null,
                'bappm_number' => $this->bappm_number ?: null,
                'type' => $this->type,
                'type_id' => $this->typeId,
                'regional_police_id' => $this->regionalPoliceId,
                'police_station_id' => $this->policeStationId,
                'description' => $this->description,
                'is_active' => $this->is_active,

                // Commission members
                'commission_member_1_name' => $this->commission_member_1_name ?: null,
                'commission_member_1_rank' => $this->commission_member_1_rank ?: null,
                'commission_member_1_nip' => $this->commission_member_1_nip ?: null,
                'commission_member_1_position' => $this->commission_member_1_position ?: null,

                'commission_member_2_name' => $this->commission_member_2_name ?: null,
                'commission_member_2_rank' => $this->commission_member_2_rank ?: null,
                'commission_member_2_nip' => $this->commission_member_2_nip ?: null,
                'commission_member_2_position' => $this->commission_member_2_position ?: null,

                'commission_member_3_name' => $this->commission_member_3_name ?: null,
                'commission_member_3_rank' => $this->commission_member_3_rank ?: null,
                'commission_member_3_nip' => $this->commission_member_3_nip ?: null,
                'commission_member_3_position' => $this->commission_member_3_position ?: null,

                // Kasi Fasmat
                'kasi_fasmat_name' => $this->kasi_fasmat_name ?: null,
                'kasi_fasmat_rank' => $this->kasi_fasmat_rank ?: null,
                'kasi_fasmat_nip' => $this->kasi_fasmat_nip ?: null,

                // Ordonatur
                'ordonatur_name' => $this->ordonatur_name ?: null,
                'ordonatur_rank' => $this->ordonatur_rank ?: null,
            ];

            if ($this->isEditMode) {
                // Update existing record
                $reception = Reception::findOrFail($this->receptionId);
                $data['code'] = $this->code;
                $reception->update($data);

                // Delete existing details and let StockService revert stocks
                $stockService = new StockService();
                $stockService->deleteReceptionStock($reception);
                
                $reception->receptionDetails()->delete();
            } else {
                // Create new record
                $data['code'] = $this->code;
                $reception = Reception::create($data);
            }

            // Create a single ReceptionDetail parent for this reception
            $totalQuantity = collect($this->details)->sum('quantity') + collect($this->supportingMaterials)->sum('quantity');
            $receptionDetail = ReceptionDetail::create([
                'reception_id' => $reception->id,
                'type_id' => $this->typeId ?: null,
                'type_detail_id' => null, 
                'code' => null,
                'number_serial_first' => null,
                'number_serial_second' => null,
                'quantity' => $totalQuantity,
                'description' => $this->description ?? '',
                'is_active' => true,
            ]);

            // Create main detail items
            foreach ($this->details as $payload) {
                if (($payload['quantity'] ?? 0) > 0) {
                    $payload['type_id'] = $this->typeId;
                    $this->createDetailItemAndHistory($reception, $receptionDetail, $payload);
                }
            }

            // Create supporting detail items
            foreach ($this->supportingMaterials as $payload) {
                if (($payload['quantity'] ?? 0) > 0) {
                    $this->createDetailItemAndHistory($reception, $receptionDetail, $payload);
                }
            }

            // Process stock updates and history
            $stockService = new StockService();
            $reception->load('receptionDetails.receptionDetailItems'); // Reload to get fresh details
            $stockService->processReception($reception);

            DB::commit();

            session()->flash('success', $this->isEditMode ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
            return $this->redirect(route('menu-polda.reception'), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function createDetailItemAndHistory($reception, $receptionDetail, $payload)
    {
        $quantity = $payload['quantity'] ?? 0;
        if ($quantity <= 0) return;

        $typeId = !empty($payload['type_id']) ? $payload['type_id'] : $this->typeId;
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
            'type_id' => $typeId ?: null,
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
            'type_id' => $typeId ?: null,
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
