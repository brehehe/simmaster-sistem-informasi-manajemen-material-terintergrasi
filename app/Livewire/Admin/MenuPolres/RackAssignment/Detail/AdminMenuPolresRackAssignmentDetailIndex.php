<?php

namespace App\Livewire\Admin\MenuPolres\RackAssignment\Detail;

use App\Models\MenuPolda\RackAssignment\RackAssignment;
use App\Models\Police\PoliceStation;
use App\Models\Rack\Rack;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminMenuPolresRackAssignmentDetailIndex extends Component
{
    public ?string $rackAssignmentId = null;
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
        $this->rackAssignmentId = $id;
        $this->isEditMode = $id !== null;

        if ($this->isEditMode) {
            $this->loadRackAssignment();
        } else {
            $this->code = RackAssignment::generateCode();
            $this->date = now()->format('Y-m-d');
            $this->addDetail(); // Initialize first row
        }

        $this->loadDropdownData();
    }

    protected function loadRackAssignment()
    {
        $rackAssignment = RackAssignment::with('rackAssignmentDetails')->findOrFail($this->rackAssignmentId);

        $this->code = $rackAssignment->code;
        $this->date = $rackAssignment->date->format('Y-m-d');
        $this->policeStationId = $rackAssignment->police_station_id;
        $this->description = $rackAssignment->description ?? '';

        // Load racks and stocks for this polda
        $this->loadRacks();
        $this->loadStockDetails();

        // Load existing details
        foreach ($rackAssignment->rackAssignmentDetails as $detail) {
            $this->details[] = [
                'stock_detail_id' => $detail->stock_detail_id,
                'type_id' => $detail->type_id,
                'type_detail_id' => $detail->type_detail_id,
                'from_rack_id' => $detail->from_rack_id,
                'to_rack_id' => $detail->to_rack_id,
                'item_code' => $detail->item_code,
                'number_serial_first' => $detail->number_serial_first,
                'number_serial_second' => $detail->number_serial_second,
                'quantity' => $detail->quantity,
                'available_quantity' => $detail->quantity, // For edit, show current quantity
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
            $this->loadRacks(); // Load racks for this polda
            $this->loadStockDetails(); // Load stocks for this polda
        }
    }

    public function addDetail()
    {
        $this->details[] = [
            'stock_detail_id' => '',
            'type_id' => '',
            'type_detail_id' => '',
            'from_rack_id' => '',
            'to_rack_id' => '',
            'item_code' => '',
            'number_serial_first' => '',
            'number_serial_second' => '',
            'quantity' => 0,
            'available_quantity' => 0,
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
                $this->details[$index]['from_rack_id'] = $stockDetail->rack_id;
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
            'details.*.to_rack_id' => 'nullable|exists:racks,id', // Allow null for "tanpa rak"
            'details.*.quantity' => 'required|numeric|min:0',
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
                    'police_station_id' => $this->policeStationId,
                    'description' => $this->description,
                    'is_active' => true,
                ];

                if ($this->isEditMode) {
                    $rackAssignment = RackAssignment::findOrFail($this->rackAssignmentId);
                    $rackAssignment->update($headerData);
                    $rackAssignment->rackAssignmentDetails()->delete();
                } else {
                    $rackAssignment = RackAssignment::create($headerData);
                }

                // Create details
                foreach ($this->details as $detail) {
                    $rackAssignment->rackAssignmentDetails()->create([
                        'stock_detail_id' => $detail['stock_detail_id'],
                        'type_id' => $detail['type_id'] ?: null,
                        'type_detail_id' => !empty($detail['type_detail_id']) ? $detail['type_detail_id'] : null,
                        'from_rack_id' => !empty($detail['from_rack_id']) ? $detail['from_rack_id'] : null,
                        'to_rack_id' => !empty($detail['to_rack_id']) ? $detail['to_rack_id'] : null,
                        'item_code' => $detail['item_code'] ?? '',
                        'number_serial_first' => $detail['number_serial_first'] ?? '',
                        'number_serial_second' => $detail['number_serial_second'] ?? '',
                        'quantity' => $detail['quantity'],
                        'description' => $detail['description'] ?? '',
                        'is_active' => true,
                    ]);
                }

                // Process via StockService
                $this->stockService->processRackAssignment($rackAssignment);

                session()->flash('success', $this->isEditMode ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
            });

            return $this->redirect(route('menu-polres.rack-assignment'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.menu-polres.rack-assignment.detail.admin-menu-polres-rack-assignment-detail-index')
            ->layout('components.layouts.main.app');
    }
}
