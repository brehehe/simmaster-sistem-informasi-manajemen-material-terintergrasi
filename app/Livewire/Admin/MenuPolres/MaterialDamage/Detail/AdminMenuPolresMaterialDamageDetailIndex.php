<?php

namespace App\Livewire\Admin\MenuPolres\MaterialDamage\Detail;

use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\Police\PoliceStation;
use App\Models\Rack\Rack;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Service\Service;
use App\Services\StockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminMenuPolresMaterialDamageDetailIndex extends Component
{
    public ?string $materialDamageId = null;
    public bool $isEditMode = false;

    // Header fields
    public string $code = '';
    public ?string $date = null;
    public ?string $policeStationId = null;
    public string $status = 'approved';
    public string $description = '';

    // Global material selector
    public ?string $typeId = null;

    // UI Flags
    public bool $is_type_detail = false;
    public bool $is_with_serial_number = false;

    // Details array (batch/flat rows)
    public array $details = [];

    // Per-row stock options
    public array $stockOptions = [];

    // Dropdown data
    public $typeDetails = [];
    public $services = [];
    public $policeStations = [];
    public $racks = [];

    protected StockService $stockService;

    public function boot(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function mount($id = null)
    {
        $this->materialDamageId = $id;
        $this->isEditMode = $id !== null;

        $this->policeStations = PoliceStation::where('is_active', true)->orderBy('name')->get();

        $user = Auth::user();
        if ($user->hasRole('Polres')) {
            $this->policeStationId = $user->police_station_id;
        }

        if ($this->isEditMode) {
            $this->loadMaterialDamage();
        } else {
            $this->code = MaterialDamage::generateCode();
            $this->date = now()->format('Y-m-d');
            $this->addDetail();
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
            ->where(function ($q) use ($typeId) {
                $q->where('type_id', $typeId)
                  ->orWhereIn('type_detail_id', TypeDetail::where('type_id', $typeId)->pluck('id'));
            })
            ->orderBy('name')
            ->get();
    }

    public function updatedTypeId($value)
    {
        $this->loadTypeData($value);
        $this->details = [];
        $this->stockOptions = [];
        $this->addDetail();
    }

    public function updatedPoliceStationId($value)
    {
        foreach ($this->details as $index => $detail) {
            $this->loadStockOptions($index);
        }
    }

    protected function loadMaterialDamage()
    {
        $materialDamage = MaterialDamage::with(['materialDamageDetails.stockDetail'])->findOrFail($this->materialDamageId);

        $this->code = $materialDamage->code;
        $this->date = $materialDamage->date->format('Y-m-d');
        $this->policeStationId = $materialDamage->police_station_id;
        $this->status = $materialDamage->status ?? 'approved';
        $this->description = $materialDamage->description ?? '';

        if ($materialDamage->materialDamageDetails->isNotEmpty()) {
            $firstDetail = $materialDamage->materialDamageDetails->first();
            $this->typeId = $firstDetail->type_id;
            $this->loadTypeData($this->typeId);
        }

        foreach ($materialDamage->materialDamageDetails as $index => $detail) {
            $stockDetail = $detail->stockDetail;

            $stockKey = $this->generateStockKey([
                'code' => $detail->item_code,
                'number_serial_first' => $detail->number_serial_first,
                'number_serial_second' => $detail->number_serial_second,
            ]);

            $this->details[] = [
                'stock_detail_id' => $detail->stock_detail_id ?? '',
                'type_id' => $detail->type_id ?? '',
                'type_detail_id' => $detail->type_detail_id ?? '',
                'service_id' => $stockDetail?->service_id ?? '',
                'service_detail_id' => $stockDetail?->service_detail_id ?? '',
                'selected_stock_key' => $stockKey,
                'item_code' => $detail->item_code ?? '',
                'number_serial_first' => $detail->number_serial_first ?? '',
                'number_serial_second' => $detail->number_serial_second ?? '',
                'quantity' => (float) $detail->quantity,
                'available_quantity' => $stockDetail ? $stockDetail->quantity + (float) $detail->quantity : 0,
                'damage_type' => $detail->damage_type ?? 'damaged',
                'reason' => $detail->reason ?? '',
                'notes' => $detail->description ?? '',
            ];
            $this->loadStockOptions($index);
        }

        if (empty($this->details)) {
            $this->addDetail();
        }
    }

    public function addDetail()
    {
        $this->details[] = [
            'stock_detail_id' => '',
            'type_id' => $this->typeId ?? '',
            'type_detail_id' => '',
            'service_id' => '',
            'service_detail_id' => '',
            'selected_stock_key' => '',
            'item_code' => '',
            'number_serial_first' => '',
            'number_serial_second' => '',
            'quantity' => 0,
            'available_quantity' => 0,
            'damage_type' => 'damaged',
            'reason' => '',
            'notes' => '',
        ];
        $index = count($this->details) - 1;
        $this->loadStockOptions($index);
    }

    public function removeDetail($index)
    {
        if (count($this->details) > 1) {
            unset($this->details[$index]);
            unset($this->stockOptions[$index]);
            $this->details = array_values($this->details);
            $this->stockOptions = array_values($this->stockOptions);
        }
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

        // Clear service if type_detail changes
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

        // Reset and reload stock options
        if (in_array($field, ['type_detail_id', 'service_id', 'service_detail_id'])) {
            $this->details[$index]['selected_stock_key'] = '';
            $this->details[$index]['stock_detail_id'] = '';
            $this->details[$index]['available_quantity'] = 0;
            $this->loadStockOptions($index);
        }

        // Handle stock key selection
        if ($field === 'selected_stock_key') {
            $option = collect($this->stockOptions[$index] ?? [])->where('key', $value)->first();
            if ($option) {
                $this->details[$index]['stock_detail_id'] = $option['stock_detail_id'];
                $this->details[$index]['available_quantity'] = (int) $option['quantity'];
                $this->details[$index]['item_code'] = $option['item_code'] ?? '';
                $this->details[$index]['number_serial_first'] = $option['number_serial_first'] ?? '';
                $this->details[$index]['number_serial_second'] = $option['number_serial_second'] ?? '';
            } else {
                $this->details[$index]['stock_detail_id'] = '';
                $this->details[$index]['available_quantity'] = 0;
            }
        }
    }

    public function loadStockOptions($index)
    {
        if (!$this->typeId || !$this->policeStationId || !isset($this->details[$index])) {
            $this->stockOptions[$index] = [];
            return;
        }

        $detail = $this->details[$index];

        $query = StockDetail::where('police_station_id', $this->policeStationId)
            ->where('type_id', $this->typeId)
            ->where('is_active', true)
            ->where('quantity', '>', 0);

        if (!empty($detail['type_detail_id'])) {
            $query->where('type_detail_id', $detail['type_detail_id']);
        }

        if (!empty($detail['service_id'])) {
            $query->where('service_id', $detail['service_id']);
            if (empty($detail['service_detail_id'])) {
                $query->whereNull('service_detail_id');
            } else {
                $query->where('service_detail_id', $detail['service_detail_id']);
            }
        }

        $stocks = $query->get();

        $this->stockOptions[$index] = $stocks->map(function ($s) {
            return [
                'key' => $this->generateStockKey($s),
                'stock_detail_id' => $s->id,
                'quantity' => (int) $s->quantity,
                'item_code' => $s->code ?? '',
                'number_serial_first' => $s->number_serial_first ?? '',
                'number_serial_second' => $s->number_serial_second ?? '',
            ];
        })->values()->toArray();

        if (!$this->is_with_serial_number) {
            $totalQty = (int) $stocks->sum('quantity');
            $this->details[$index]['available_quantity'] = $totalQty;
            if ($stocks->count() === 1) {
                $this->details[$index]['stock_detail_id'] = $stocks->first()->id;
            }
        }
    }

    protected function generateStockKey($item)
    {
        $code = !empty(data_get($item, 'code')) ? data_get($item, 'code') : '-';
        $sn1 = !empty(data_get($item, 'number_serial_first')) ? data_get($item, 'number_serial_first') : '-';
        $sn2 = !empty(data_get($item, 'number_serial_second')) ? data_get($item, 'number_serial_second') : '-';
        return "{$code} | {$sn1} | {$sn2}";
    }

    public function save()
    {
        $this->validate([
            'code' => 'required|string|max:255',
            'date' => 'required|date',
            'policeStationId' => 'required|exists:police_stations,id',
            'typeId' => 'required|exists:types,id',
            'status' => 'required|in:reported,under_review,approved,disposed',
            'details' => 'required|array|min:1',
            'details.*.stock_detail_id' => 'required|exists:stock_details,id',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.damage_type' => 'required|in:damaged,lost',
            'details.*.reason' => 'required|string',
        ], [
            'typeId.required' => 'Material utama harus dipilih',
            'details.*.stock_detail_id.required' => 'Barang harus dipilih',
            'details.*.quantity.min' => 'Jumlah minimal 1',
        ]);

        try {
            DB::transaction(function () {
                $headerData = [
                    'code' => $this->code,
                    'date' => $this->date,
                    'police_station_id' => $this->policeStationId,
                    'status' => $this->status,
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

                foreach ($this->details as $detail) {
                    $stockDetail = StockDetail::findOrFail($detail['stock_detail_id']);
                    if ($detail['quantity'] > $detail['available_quantity']) {
                        throw new \Exception('Jumlah melebihi stok tersedia.');
                    }

                    $materialDamage->materialDamageDetails()->create([
                        'stock_detail_id' => $stockDetail->id,
                        'type_id' => $this->typeId,
                        'type_detail_id' => $detail['type_detail_id'] ?: null,
                        'rack_id' => $stockDetail->rack_id,
                        'item_code' => $detail['item_code'] ?? '',
                        'number_serial_first' => $detail['number_serial_first'] ?? '',
                        'number_serial_second' => $detail['number_serial_second'] ?? '',
                        'quantity' => $detail['quantity'],
                        'damage_type' => $detail['damage_type'],
                        'reason' => $detail['reason'],
                        'description' => $detail['notes'] ?? '',
                        'is_active' => true,
                    ]);
                }

                $this->stockService->processMaterialDamage($materialDamage);

                session()->flash('success', $this->isEditMode ? 'Data berhasil diperbarui.' : 'Data material rusak berhasil disimpan.');
            });

            return $this->redirect(route('menu-polres.material-damage'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.menu-polres.material-damage.detail.admin-menu-polres-material-damage-detail-index', [
            'types' => Type::where('is_active', true)->orderBy('name')->get(),
            'typeDetails' => $this->typeDetails,
            'services' => $this->services,
        ])->layout('components.layouts.main.app');
    }
}
