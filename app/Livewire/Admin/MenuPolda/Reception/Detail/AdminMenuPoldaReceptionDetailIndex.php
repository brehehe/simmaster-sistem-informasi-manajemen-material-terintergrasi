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
    public ?string $regionalPoliceId = null;
    public ?string $policeStationId = null;
    public ?string $description = null;
    public bool $is_active = true;

    // Detail Items
    public array $details = [];
    public int $detailCounter = 0;

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
            $reception = Reception::with('receptionDetails')->findOrFail($id);

            $this->code = $reception->code;
            $this->name = $reception->name;
            $this->date = $reception->date->format('Y-m-d');
            $this->regionalPoliceId = $reception->regional_police_id;
            $this->policeStationId = $reception->police_station_id;
            $this->description = $reception->description;
            $this->is_active = $reception->is_active;

            // Load detail items
            foreach ($reception->receptionDetails as $detail) {
                $this->details[] = [
                    'id' => $detail->id,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    // 'rack_id' => $detail->rack_id,
                    'code' => $detail->code,
                    'number_serial_first' => $detail->number_serial_first,
                    'number_serial_second' => $detail->number_serial_second,
                    'quantity' => $detail->quantity,
                    'description' => $detail->description,
                    'is_active' => $detail->is_active,
                ];
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

            $this->addDetail();
        }
    }

    public function addDetail()
    {
        $this->details[] = [
            'id' => null,
            'type_id' => null,
            'type_detail_id' => null,
            // 'rack_id' => null,
            'code' => '',
            'number_serial_first' => '',
            'number_serial_second' => '',
            'quantity' => 0,
            'description' => '',
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
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'details.*.type_id' => 'nullable|exists:types,id',
            'details.*.type_detail_id' => 'nullable|exists:type_details,id',
            // 'details.*.rack_id' => 'nullable|exists:racks,id',
            'details.*.code' => 'nullable|string|max:255',
            'details.*.number_serial_first' => 'nullable|string|max:255',
            'details.*.number_serial_second' => 'nullable|string|max:255',
            'details.*.quantity' => 'required|numeric|min:0',
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
            'regionalPoliceId.required' => 'Polda wajib dipilih.',
            'policeStationId.required' => 'Polres wajib dipilih.',
            'details.*.quantity.required' => 'Jumlah wajib diisi.',
            'details.*.quantity.min' => 'Jumlah minimal 0.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $data = [
                'name' => $this->name,
                'date' => $this->date,
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

            // Create/Update detail items
            foreach ($this->details as $detail) {
                ReceptionDetail::create([
                    'reception_id' => $reception->id,
                    'type_id' => $detail['type_id'] ?: null,
                    'type_detail_id' => $detail['type_detail_id'] ?: null,
                    // // 'rack_id' => $detail['rack_id'] ?: null,
                    'code' => $detail['code'],
                    'number_serial_first' => $detail['number_serial_first'],
                    'number_serial_second' => $detail['number_serial_second'],
                    'quantity' => $detail['quantity'],
                    'description' => $detail['description'],
                    'is_active' => $detail['is_active'] ?? true,
                ]);
            }

            // Process stock updates and history
            $stockService = new StockService();
            $reception->load('receptionDetails'); // Reload to get fresh details
            $stockService->processReception($reception);

            DB::commit();

            session()->flash('success', $this->isEditMode ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
            return redirect()->route('menu-polda.reception');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
