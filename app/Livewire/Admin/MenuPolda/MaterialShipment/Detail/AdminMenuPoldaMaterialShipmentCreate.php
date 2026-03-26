<?php

namespace App\Livewire\Admin\MenuPolda\MaterialShipment\Detail;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Service\Service;
use App\Models\Service\ServiceDetail;
use Carbon\Carbon;
use Livewire\Component;

class AdminMenuPoldaMaterialShipmentCreate extends Component
{
    public ?string $shipmentId = null;
    public bool $isEditMode = false;
    public string $code = '';
    public string $shipment_date = '';
    public ?string $regional_police_id = null;
    public string $receiver_police_station_id = '';
    public string $notes = '';
    public string $shipmentStatus = '';
    
    public array $details = [];
    public ?string $typeId = null; // Global type filter
    public array $stockOptions = []; // Per-row stock options

    // Computed properties for UI flags
    public bool $is_type_detail = false;
    public bool $is_with_serial_number = false;

    // Dropdown data
    public $typeDetails = [];
    public $services = [];
    public $regionalPolices = [];
    public function mount($id = null)
    {
        $this->shipment_date = now()->format('Y-m-d');
        $this->regionalPolices = RegionalPolice::where('is_active', true)->orderBy('name')->get();

        if ($id) {
            $this->shipmentId = $id;
            $this->isEditMode = true;
            $this->loadShipment($id);
        } else {
            $user = auth()->user();
            $this->code = MaterialShipment::generateCode($user?->regional_police_id);
            $this->regional_police_id = $user?->regional_police_id;
            $this->addDetail();
        }
    }

    protected function loadShipment($id)
    {
        $shipment = MaterialShipment::with('materialShipmentDetails.stockDetail')->findOrFail($id);

        $this->code = $shipment->code;
        $this->shipment_date = Carbon::parse($shipment->shipment_date)->format('Y-m-d');
        $this->regional_police_id = $shipment?->sender_regional_police_id;
        $this->receiver_police_station_id = $shipment->receiver_police_station_id;
        $this->notes = $shipment->notes ?? '';
        $this->shipmentStatus = $shipment->status;

        if ($shipment->materialShipmentDetails->isNotEmpty()) {
            $this->typeId = $shipment->materialShipmentDetails[0]->type_id;
            $this->loadTypeData($this->typeId);
        }

        foreach ($shipment->materialShipmentDetails as $index => $detail) {
            $stockDetail = $detail->stockDetail;
            $this->details[] = [
                'stock_detail_id' => $detail->stock_detail_id,
                'type_id' => $detail->type_id,
                'type_detail_id' => $detail->type_detail_id,
                'service_id' => $stockDetail?->service_id,
                'service_detail_id' => $stockDetail?->service_detail_id,
                'selected_stock_key' => $this->generateStockKey((object)[
                    'code' => $detail->code,
                    'number_serial_first' => $detail->number_serial_first,
                    'number_serial_second' => $detail->number_serial_second
                ]),
                'quantity' => (int) $detail->quantity,
                'available_quantity' => $stockDetail ? ($stockDetail->quantity + $detail->quantity) : (int) $detail->quantity,
                'notes' => $detail->notes ?? '',
            ];
            $this->loadStockOptions($index);
        }
    }

    protected function loadTypeData($typeId)
    {
        if (!$typeId) {
            $this->is_type_detail = false;
            $this->is_with_serial_number = false;
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

    private function generateStockKey($item)
    {
        $code = !empty($item->code ?? $item['code'] ?? '') ? ($item->code ?? $item['code']) : '-';
        $sn1  = !empty($item->number_serial_first ?? $item['number_serial_first'] ?? '') ? ($item->number_serial_first ?? $item['number_serial_first']) : '-';
        $sn2  = !empty($item->number_serial_second ?? $item['number_serial_second'] ?? '') ? ($item->number_serial_second ?? $item['number_serial_second']) : '-';
        return "{$code} | {$sn1} | {$sn2}";
    }

    public function updatedRegionalPoliceId($value)
    {
        $this->receiver_police_station_id = '';
        foreach ($this->details as $index => $detail) {
            $this->details[$index]['selected_stock_key'] = '';
            $this->details[$index]['stock_detail_id'] = '';
            $this->details[$index]['available_quantity'] = 0;
            $this->loadStockOptions($index);
        }
        $this->dispatch('refreshAllSelectize');
    }

    public function updatedTypeId($value)
    {
        $this->loadTypeData($value);
        
        // Clear existing details when type changes, match MaterialDamage behavior
        $this->details = [];
        $this->addDetail();
    }

    public function addDetail()
    {
        $this->details[] = [
            'type_id' => $this->typeId,
            'type_detail_id' => '',
            'service_id' => '',
            'service_detail_id' => '',
            'selected_stock_key' => '',
            'stock_detail_id' => '',
            'quantity' => 1,
            'available_quantity' => 0,
            'notes' => '',
        ];
        $this->loadStockOptions(count($this->details) - 1);
    }

    public function loadStockOptions($index)
    {
        if (!$this->regional_police_id || !$this->typeId || !isset($this->details[$index])) {
            $this->stockOptions[$index] = [];
            return;
        }

        $detail = $this->details[$index];

        $query = StockDetail::where('regional_police_id', $this->regional_police_id)
            ->where('type_id', $this->typeId)
            ->where('is_active', true);

        // Include items with quantity > 0 OR the currently selected item (for edit mode)
        $query->where(function($q) use ($detail) {
            $q->where('quantity', '>', 0);
            if (!empty($detail['stock_detail_id'])) {
                $q->orWhere('id', $detail['stock_detail_id']);
            }
        });

        // Cascading filter: only filter by field if actually selected.
        // If service_id is set but service_detail_id is empty → filter by service_id AND whereNull(service_detail_id)
        // to avoid double-counting stock allocated to specific sub-services.
        if (!empty($detail['type_detail_id'])) {
            $query->where('type_detail_id', $detail['type_detail_id']);
        }

        if (!empty($detail['service_id'])) {
            $query->where('service_id', $detail['service_id']);
            if (empty($detail['service_detail_id'])) {
                // No sub-service selected → only stock at the service level (service_detail_id = NULL)
                $query->whereNull('service_detail_id');
            } else {
                $query->where('service_detail_id', $detail['service_detail_id']);
            }
        }


        $stocks = $query->get();

        $this->stockOptions[$index] = $stocks->groupBy(function($item) {
            return $this->generateStockKey($item);
        })->map(function($group) {
            $first = $group->first();
            return [
                'key' => $this->generateStockKey($first),
                'quantity' => (int) $group->sum('quantity'),
                'stock_detail_id' => $first->id
            ];
        })->values()->toArray();

        // For non-serial-number types: auto-set available_quantity and stock_detail_id
        // since the user doesn't pick a specific item
        if (!$this->is_with_serial_number) {
            $totalQty = (int) $stocks->sum('quantity');
            $this->details[$index]['available_quantity'] = $totalQty;
            if ($stocks->count() === 1) {
                $this->details[$index]['stock_detail_id'] = $stocks->first()->id;
            }
        }
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        unset($this->stockOptions[$index]);
        $this->details = array_values($this->details);
        $this->stockOptions = array_values($this->stockOptions);
    }

    public function updatedDetails($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) !== 2) return;
        [$index, $field] = $parts;

        // Auto-fill type_detail_id when service_id is selected
        if ($field === 'service_id' && $value) {
            $service = collect($this->services)->firstWhere('id', $value);
            $typeDetailId = data_get($service, 'type_detail_id');
            if ($typeDetailId) {
                $this->details[$index]['type_detail_id'] = $typeDetailId;
            }
            $this->details[$index]['service_detail_id'] = '';
        }

        // Clear service if type_detail changes and service doesn't belong to it
        if ($field === 'type_detail_id') {
            $serviceId = $this->details[$index]['service_id'] ?? '';
            if ($serviceId) {
                $service = collect($this->services)->firstWhere('id', $serviceId);
                $svcTypeDetailId = data_get($service, 'type_detail_id');
                if ($svcTypeDetailId !== null && $svcTypeDetailId != $value) {
                    $this->details[$index]['service_id'] = '';
                    $this->details[$index]['service_detail_id'] = '';
                }
            }
        }

        // When cascading filters change, reset stock selection and reload options
        if (in_array($field, ['type_detail_id', 'service_id', 'service_detail_id'])) {
            $this->details[$index]['selected_stock_key'] = '';
            $this->details[$index]['stock_detail_id'] = '';
            $this->details[$index]['available_quantity'] = 0;
            $this->loadStockOptions($index);
        }

        // When stock key is selected, sync stock_detail_id and available_quantity
        if ($field === 'selected_stock_key') {
            $option = collect($this->stockOptions[$index] ?? [])->where('key', $value)->first();
            if ($option) {
                $this->details[$index]['stock_detail_id'] = $option['stock_detail_id'];
                $this->details[$index]['available_quantity'] = (int) $option['quantity'];
            } else {
                $this->details[$index]['stock_detail_id'] = '';
                $this->details[$index]['available_quantity'] = 0;
            }
        }
    }

    public function save($ship = false)
    {
        $this->validate([
            'shipment_date' => 'required|date',
            'receiver_police_station_id' => 'required|exists:police_stations,id',
            'details' => 'required|array|min:1',
            'details.*.stock_detail_id' => 'required',
            'details.*.quantity' => 'required|numeric|min:1',
        ], [
            'receiver_police_station_id.required' => 'Polres tujuan harus dipilih',
            'details.*.stock_detail_id.required' => 'Stock harus dipilih',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($ship) {
                if ($this->isEditMode) {
                    $shipment = MaterialShipment::findOrFail($this->shipmentId);
                    if ($shipment->status !== 'draft') {
                        throw new \Exception('Hanya pengiriman dengan status draft yang bisa diedit');
                    }
                    $shipment->update([
                        'shipment_date' => $this->shipment_date,
                        'receiver_police_station_id' => $this->receiver_police_station_id,
                        'notes' => $this->notes,
                    ]);
                    $shipment->materialShipmentDetails()->delete();
                } else {
                    $shipment = MaterialShipment::create([
                        'code' => $this->code,
                        'shipment_date' => $this->shipment_date,
                        'status' => 'draft',
                        'sender_regional_police_id' => $this->regional_police_id ?: auth()->user()->regional_police_id,
                        'receiver_police_station_id' => $this->receiver_police_station_id,
                        'notes' => $this->notes,
                        'is_active' => true,
                    ]);
                }

                foreach ($this->details as $index => $detail) {
                    $stockDetail = StockDetail::find($detail['stock_detail_id']);
                    if (!$stockDetail) throw new \Exception("Stock detail pada baris " . ($index + 1) . " tidak ditemukan");

                    if ($detail['quantity'] > $detail['available_quantity']) {
                        throw new \Exception("Quantity melebihi stock tersedia pada baris " . ($index + 1));
                    }

                    MaterialShipmentDetail::create([
                        'material_shipment_id' => $shipment->id,
                        'stock_detail_id' => $stockDetail->id,
                        'rack_id' => $stockDetail->rack_id,
                        'type_id' => $detail['type_id'],
                        'type_detail_id' => $detail['type_detail_id'] ?: null,
                        'code' => $stockDetail->code ?? '',
                        'number_serial_first' => $stockDetail->number_serial_first ?? '',
                        'number_serial_second' => $stockDetail->number_serial_second ?? '',
                        'quantity' => $detail['quantity'],
                        'notes' => $detail['notes'] ?? '',
                        'is_active' => true,
                    ]);
                }

                if ($ship) {
                    $shipment->markAsShipped();
                    session()->flash('success', 'Pengiriman berhasil dikirim.');
                } else {
                    session()->flash('success', 'Pengiriman berhasil disimpan.');
                }
            });

            return $this->redirect(route('menu-polda.material-shipment'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function shipDraft()
    {
        try {
            $shipment = MaterialShipment::findOrFail($this->shipmentId);
            if ($shipment->status !== 'draft') {
                session()->flash('error', 'Hanya pengiriman berstatus draft yang bisa dikirim.');
                return;
            }
            $shipment->markAsShipped();
            session()->flash('success', 'Pengiriman berhasil dikirim.');
            return $this->redirect(route('menu-polda.material-shipment'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $policeStations = $this->regional_police_id ? PoliceStation::where('regional_police_id', $this->regional_police_id)
            ->where('is_active', true)->orderBy('name')->get() : collect();

        return view('livewire.admin.menu-polda.material-shipment.detail.admin-menu-polda-material-shipment-create', [
            'policeStations' => $policeStations,
            'regionalPolices' => $this->regionalPolices,
            'types' => Type::orderBy('name')->get(),
            'typeDetails' => $this->typeDetails,
            'services' => $this->services,
        ])->layout('components.layouts.main.app');
    }
}
