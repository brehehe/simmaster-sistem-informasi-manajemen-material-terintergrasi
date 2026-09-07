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
    public string $status = 'reported';
    public string $description = '';
    public string $officerName = '';
    public string $officerRank = '';

    public function toJSON()
    {
        return [];
    }

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
        if ($user->hasRole('Polres') || !empty($user->police_station_id)) {
            $this->policeStationId = $user->police_station_id;
        }
        // Pre-fill officer name from user
        if (!$this->isEditMode) {
            $this->officerName = $user->name ?? '';
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
        $this->status = $materialDamage->status ?? 'reported';
        $this->description = $materialDamage->description ?? '';
        // Parse officer info from description if stored there
        $this->officerName = $materialDamage->officer_name ?? '';
        $this->officerRank = $materialDamage->officer_rank ?? '';

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

        if ($field === 'service_id' && $value) {
            $service = collect($this->services)->firstWhere('id', $value);
            $typeDetailId = data_get($service, 'type_detail_id');
            if ($typeDetailId) {
                $this->details[$index]['type_detail_id'] = $typeDetailId;
            }
            $this->details[$index]['service_detail_id'] = '';
            $this->loadStockOptions($index);
        }

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

        // Auto-select if only 1 stock option available and not selected
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
            'status' => 'required|in:reported,under_review,approved,disposed',
            'details' => 'required|array|min:1',
            'details.*.stock_detail_id' => 'required|exists:stock_details,id',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.damage_type' => 'required|in:damaged,lost',
            'details.*.reason' => 'required|string|max:500',
        ];
    }

    protected $messages = [
        'date.required' => 'Tanggal laporan berita acara wajib diisi.',
        'policeStationId.required' => 'Polres wajib dipilih.',
        'typeId.required' => 'Material utama wajib dipilih.',
        'status.required' => 'Status laporan wajib dipilih.',
        'details.required' => 'Minimal harus ada 1 item material rusak/hilang.',
        'details.min' => 'Minimal harus ada 1 item material rusak/hilang.',
        'details.*.stock_detail_id.required' => 'Stok barang / nomor seri wajib dipilih dari stok yang tersedia.',
        'details.*.stock_detail_id.exists' => 'Stok barang tidak valid atau sudah habis.',
        'details.*.quantity.required' => 'Jumlah barang rusak/hilang wajib diisi.',
        'details.*.quantity.numeric' => 'Jumlah harus berupa angka.',
        'details.*.quantity.min' => 'Jumlah minimal 1 unit.',
        'details.*.damage_type.required' => 'Status kondisi (rusak/hilang) wajib dipilih.',
        'details.*.reason.required' => 'Alasan / kronologi kerusakan wajib diisi.',
    ];

    public function save()
    {
        $this->validate();

        $hasError = false;
        $stockUsedCounts = [];

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
                    $this->addError("details.{$index}.stock_detail_id", "Total jumlah untuk stok ini melebihi stok yang ada.");
                    $hasError = true;
                }
            }
        }

        if ($hasError) {
            session()->flash('error', 'Silakan periksa kembali isian formulir. Ada data yang belum sesuai.');
            return;
        }

        try {
            DB::transaction(function () {
                $headerData = [
                    'code' => $this->code,
                    'date' => $this->date,
                    'police_station_id' => $this->policeStationId,
                    'status' => $this->status,
                    'description' => $this->description,
                    'officer_name' => $this->officerName,
                    'officer_rank' => $this->officerRank,
                    'is_active' => true,
                ];

                if ($this->isEditMode) {
                    $materialDamage = MaterialDamage::findOrFail($this->materialDamageId);
                    $this->stockService->deleteMaterialDamage($materialDamage);
                    $materialDamage->update($headerData);
                    $materialDamage->materialDamageDetails()->delete();
                } else {
                    if (empty($headerData['code']) || MaterialDamage::withTrashed()->where('code', $headerData['code'])->exists()) {
                        $headerData['code'] = MaterialDamage::generateCode();
                    }
                    $materialDamage = MaterialDamage::create($headerData);
                }

                foreach ($this->details as $detail) {
                    $stockDetail = StockDetail::findOrFail($detail['stock_detail_id']);

                    $materialDamage->materialDamageDetails()->create([
                        'stock_detail_id' => $stockDetail->id,
                        'type_id' => $this->typeId,
                        'type_detail_id' => !empty($detail['type_detail_id']) ? $detail['type_detail_id'] : null,
                        'rack_id' => $stockDetail->rack_id,
                        'item_code' => $detail['item_code'] ?? ($stockDetail->code ?? ''),
                        'number_serial_first' => $detail['number_serial_first'] ?? ($stockDetail->number_serial_first ?? ''),
                        'number_serial_second' => $detail['number_serial_second'] ?? ($stockDetail->number_serial_second ?? ''),
                        'quantity' => (float)$detail['quantity'],
                        'damage_type' => $detail['damage_type'] ?? 'damaged',
                        'reason' => $detail['reason'] ?? '',
                        'description' => $detail['notes'] ?? ($detail['description'] ?? ''),
                        'is_active' => true,
                    ]);
                }

                $materialDamage->load('materialDamageDetails');
                $this->stockService->processMaterialDamage($materialDamage);

                session()->flash('success', $this->isEditMode ? 'Data BA Material Rusak berhasil diperbarui.' : 'BA Material Rusak berhasil disimpan dan stok telah berkurang otomatis.');
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
