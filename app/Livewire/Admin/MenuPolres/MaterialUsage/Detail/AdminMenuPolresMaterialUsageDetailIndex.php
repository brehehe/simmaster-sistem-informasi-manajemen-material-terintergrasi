<?php

namespace App\Livewire\Admin\MenuPolres\MaterialUsage\Detail;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetailItem;
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

class AdminMenuPolresMaterialUsageDetailIndex extends Component
{
    public ?string $materialUsageId = null;
    public bool $isEditMode = false;

    // Header fields
    public string $code = '';
    public ?string $date = null;
    public ?string $policeStationId = null;
    public string $description = '';

    // Global type selector
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
        $this->materialUsageId = $id;
        $this->isEditMode = $id !== null;

        $this->policeStations = PoliceStation::where('is_active', true)->orderBy('name')->get();

        $user = Auth::user();
        if ($user->hasRole('Polres') || !empty($user->police_station_id)) {
            $this->policeStationId = $user->police_station_id;
        }

        if ($this->isEditMode) {
            $this->loadMaterialUsage();
        } else {
            $this->code = MaterialUsage::generateCode();
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
        $this->is_with_serial_number = $typeRef ? (bool) $typeRef->is_with_serial_number : false;

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

    protected function loadMaterialUsage()
    {
        $materialUsage = MaterialUsage::with(['materialUsageDetails.materialUsageDetailItems', 'materialUsageDetails.stockDetail'])->findOrFail($this->materialUsageId);

        $this->code = $materialUsage->code;
        $this->date = $materialUsage->date->format('Y-m-d');
        $this->policeStationId = $materialUsage->police_station_id;
        $this->description = $materialUsage->description ?? '';

        if ($materialUsage->materialUsageDetails->isNotEmpty()) {
            $firstDetail = $materialUsage->materialUsageDetails->first();
            $this->typeId = $firstDetail->type_id;
            $this->loadTypeData($this->typeId);
        }

        foreach ($materialUsage->materialUsageDetails as $index => $detail) {
            $stockDetail = $detail->stockDetail;
            $firstItem = $detail->materialUsageDetailItems->first();

            $availQty = $stockDetail ? (float) $stockDetail->quantity + (float) $detail->quantity : (float) $detail->quantity;

            $this->details[] = [
                'stock_detail_id' => $detail->stock_detail_id ?? '',
                'type_id' => $detail->type_id ?? '',
                'type_detail_id' => $detail->type_detail_id ?? '',
                'service_id' => $firstItem?->service_id ?? '',
                'service_detail_id' => $firstItem?->service_detail_id ?? '',
                'item_code' => $detail->item_code ?? ($stockDetail->code ?? ''),
                'number_serial_first' => $detail->number_serial_first ?? ($stockDetail->number_serial_first ?? ''),
                'number_serial_second' => $detail->number_serial_second ?? ($stockDetail->number_serial_second ?? ''),
                'quantity' => (float) $detail->quantity,
                'available_quantity' => $availQty,
                'usage_type' => $detail->usage_type ?? 'Material Digunakan',
                'description' => $detail->description ?? '',
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
            'item_code' => '',
            'number_serial_first' => '',
            'number_serial_second' => '',
            'quantity' => 1,
            'available_quantity' => 0,
            'usage_type' => 'Material Digunakan',
            'description' => '',
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
            $this->loadStockOptions($index);
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
            $this->loadStockOptions($index);
        }

        // Handle stock_detail_id selection
        if ($field === 'stock_detail_id') {
            $option = collect($this->stockOptions[$index] ?? [])->firstWhere('stock_detail_id', $value);
            if ($option) {
                $this->details[$index]['available_quantity'] = (float) $option['quantity'];
                $this->details[$index]['item_code'] = $option['item_code'] ?? '';
                $this->details[$index]['number_serial_first'] = $option['number_serial_first'] ?? '';
                $this->details[$index]['number_serial_second'] = $option['number_serial_second'] ?? '';
                if (!empty($option['type_detail_id']) && empty($this->details[$index]['type_detail_id'])) {
                    $this->details[$index]['type_detail_id'] = $option['type_detail_id'];
                }
                if (!empty($option['service_id']) && empty($this->details[$index]['service_id'])) {
                    $this->details[$index]['service_id'] = $option['service_id'];
                }
            } else {
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

        $query = StockDetail::with(['rack', 'typeDetail'])
            ->where('police_station_id', $this->policeStationId)
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

        $stocks = $query->orderBy('created_at', 'desc')->get();

        $this->stockOptions[$index] = $stocks->map(function ($s) {
            $rackName = $s->rack ? $s->rack->name : 'Tanpa Rak';
            $serialPart = '';
            if ($s->number_serial_first && $s->number_serial_second) {
                $serialPart = "{$s->number_serial_first} s/d {$s->number_serial_second}";
            } elseif ($s->number_serial_first) {
                $serialPart = $s->number_serial_first;
            } elseif ($s->code) {
                $serialPart = "Kode: {$s->code}";
            } else {
                $serialPart = "Batch " . substr($s->id, 0, 6);
            }

            $label = "{$serialPart} (Stok: " . (int)$s->quantity . " | {$rackName})";

            return [
                'stock_detail_id' => $s->id,
                'quantity' => (float) $s->quantity,
                'item_code' => $s->code ?? '',
                'number_serial_first' => $s->number_serial_first ?? '',
                'number_serial_second' => $s->number_serial_second ?? '',
                'type_detail_id' => $s->type_detail_id,
                'service_id' => $s->service_id,
                'rack_name' => $rackName,
                'label' => $label,
            ];
        })->values()->toArray();

        // Auto-select if only 1 stock option available or non-serial item
        if (count($this->stockOptions[$index]) > 0 && empty($this->details[$index]['stock_detail_id'])) {
            $first = $this->stockOptions[$index][0];
            $this->details[$index]['stock_detail_id'] = $first['stock_detail_id'];
            $this->details[$index]['available_quantity'] = (float) $first['quantity'];
            $this->details[$index]['item_code'] = $first['item_code'];
            $this->details[$index]['number_serial_first'] = $first['number_serial_first'];
            $this->details[$index]['number_serial_second'] = $first['number_serial_second'];
        }
    }

    protected function rules(): array
    {
        return [
            'date' => 'required|date',
            'policeStationId' => 'required|exists:police_stations,id',
            'typeId' => 'required|exists:types,id',
            'details' => 'required|array|min:1',
            'details.*.stock_detail_id' => 'required|exists:stock_details,id',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.usage_type' => 'required|string',
            'details.*.description' => 'nullable|string|max:500',
        ];
    }

    protected $messages = [
        'date.required' => 'Tanggal penggunaan wajib diisi.',
        'date.date' => 'Format tanggal penggunaan tidak valid.',
        'policeStationId.required' => 'Polres wajib dipilih.',
        'policeStationId.exists' => 'Polres yang dipilih tidak valid.',
        'typeId.required' => 'Material utama wajib dipilih.',
        'typeId.exists' => 'Material yang dipilih tidak valid.',
        'details.required' => 'Minimal harus ada 1 detail item material.',
        'details.min' => 'Minimal harus ada 1 detail item material.',
        'details.*.stock_detail_id.required' => 'Stok barang / nomor seri wajib dipilih dari daftar stok yang tersedia.',
        'details.*.stock_detail_id.exists' => 'Stok barang tidak valid atau sudah habis.',
        'details.*.quantity.required' => 'Jumlah material wajib diisi.',
        'details.*.quantity.numeric' => 'Jumlah harus berupa angka.',
        'details.*.quantity.min' => 'Jumlah minimal 1 unit.',
        'details.*.usage_type.required' => 'Jenis penggunaan wajib dipilih.',
        'details.*.description.max' => 'Catatan maksimal 500 karakter.',
    ];

    public function save()
    {
        $this->validate();

        // Custom validation for quantities against available stock
        $stockUsedCounts = [];
        $hasError = false;

        foreach ($this->details as $index => $detail) {
            $stockId = $detail['stock_detail_id'] ?? null;
            $qty = (float)($detail['quantity'] ?? 0);
            $avail = (float)($detail['available_quantity'] ?? 0);

            if ($qty <= 0) {
                $this->addError("details.{$index}.quantity", "Jumlah harus minimal 1.");
                $hasError = true;
            }

            if ($avail > 0 && $qty > $avail) {
                $this->addError("details.{$index}.quantity", "Jumlah ({$qty}) melebihi stok tersedia ({$avail}).");
                $hasError = true;
            }

            if ($stockId) {
                $stockUsedCounts[$stockId] = ($stockUsedCounts[$stockId] ?? 0) + $qty;
                if ($avail > 0 && $stockUsedCounts[$stockId] > $avail) {
                    $this->addError("details.{$index}.stock_detail_id", "Total jumlah untuk stok ini ({$stockUsedCounts[$stockId]}) melebihi stok yang ada ({$avail}).");
                    $hasError = true;
                }
            }
        }

        if ($hasError) {
            session()->flash('error', 'Silakan periksa kembali isian formulir. Ada data yang belum sesuai.');
            return;
        }

        try {
            $headerData = [
                'code' => $this->code,
                'date' => $this->date,
                'police_station_id' => $this->policeStationId,
                'description' => $this->description,
                'is_active' => true,
            ];

            \App\Actions\MenuPolda\CreateMaterialUsageAction::run(
                $headerData,
                $this->details,
                $this->typeId,
                $this->materialUsageId
            );

            session()->flash('success', $this->isEditMode ? 'Data penggunaan material berhasil diperbarui.' : 'Data penggunaan material berhasil disimpan dan stok telah otomatis terpotong.');

            return $this->redirect(route('menu-polres.material-usage-detail'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.menu-polres.material-usage.detail.admin-menu-polres-material-usage-detail-index', [
            'types' => Type::where('is_active', true)->orderBy('name')->get(),
            'typeDetails' => $this->typeDetails,
            'services' => $this->services,
        ])->layout('components.layouts.main.app');
    }
}
