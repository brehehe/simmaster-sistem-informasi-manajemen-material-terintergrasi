<?php

namespace App\Livewire\Admin\MenuPolda\MaterialShipment\Detail;

use App\Actions\MenuPolda\CreateMaterialShipmentAction;
use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Stock\StockDetail;
use App\Services\SerialRangeService;
use Livewire\Attributes\Locked;
use Livewire\Component;

class AdminMenuPoldaMaterialShipmentCreate extends Component
{
    #[Locked]
    public ?string $shipmentId = null;

    public string $code = '';

    public string $defaultCode = '';

    public string $shipment_date = '';

    public string $regional_police_id = '';

    public string $receiver_police_station_id = '';

    public string $notes = '';

    public array $details = [];

    public function boot(): void
    {
        $this->authorize('create', MaterialShipment::class);
    }

    public function getRomanMonth(?string $date = null): string
    {
        $time = strtotime($date ?: $this->shipment_date ?: now('Asia/Jakarta')->toDateString());
        $month = (int) date('n', $time);
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $map[$month] ?? 'X';
    }

    public function getNextNumber(?string $date = null): string
    {
        $year = substr($date ?: $this->shipment_date ?: now('Asia/Jakarta')->toDateString(), 0, 4);
        $codes = MaterialShipment::withTrashed()
            ->where('code', 'like', "SPPM/%/%/LOG.3.6.7./{$year}")
            ->pluck('code');

        $max = 0;
        foreach ($codes as $c) {
            if (preg_match('~^SPPM/(\d+)/~', $c, $m)) {
                $num = (int) $m[1];
                if ($num > $max) {
                    $max = $num;
                }
            }
        }

        return (string) ($max + 1);
    }

    public function generateDefaultCode(?string $date = null): string
    {
        $date = $date ?: $this->shipment_date ?: now('Asia/Jakarta')->toDateString();
        $year = substr($date, 0, 4);
        $monthRoman = $this->getRomanMonth($date);
        $nextNumber = $this->getNextNumber($date);

        return "SPPM/{$nextNumber}/{$monthRoman}/LOG.3.6.7./{$year}";
    }

    public function resetCodeToDefault(): void
    {
        $this->code = $this->generateDefaultCode();
        $this->defaultCode = $this->code;
    }

    public function mount($id = null): void
    {
        $this->shipment_date = now('Asia/Jakarta')->toDateString();
        $this->regional_police_id = auth()->user()->regional_police_id ?? RegionalPolice::where('is_active', true)->value('id') ?? '';

        if ($id) {
            $shipment = MaterialShipment::with('materialShipmentDetails')->findOrFail($id);
            $this->authorize('update', $shipment);
            $this->shipmentId = $id;

            $this->code = $shipment->code;
            $this->defaultCode = $shipment->code;

            $this->shipment_date = $shipment->shipment_date->toDateString();
            $this->regional_police_id = $shipment->sender_regional_police_id;
            $this->receiver_police_station_id = $shipment->receiver_police_station_id;
            $this->notes = $shipment->notes ?? '';
            foreach ($shipment->materialShipmentDetails as $detail) {
                $this->details[] = $detail->only(['stock_detail_id', 'number_serial_first', 'number_serial_second', 'quantity', 'notes']);
            }
        } else {
            $this->code = $this->generateDefaultCode();
            $this->defaultCode = $this->code;
            $this->addDetail();
        }
    }

    public function updatedShipmentDate(): void
    {
        if (! $this->shipmentId && ($this->code === '' || $this->code === $this->defaultCode)) {
            $this->code = $this->generateDefaultCode();
            $this->defaultCode = $this->code;
        }
    }

    public function addDetail(): void
    {
        $this->details[] = ['stock_detail_id' => '', 'number_serial_first' => '', 'number_serial_second' => '', 'quantity' => 1, 'notes' => ''];
    }

    public function removeDetail($index): void
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details);
    }

    public function updatedRegionalPoliceId(): void
    {
        $this->receiver_police_station_id = '';
        $this->details = [];
        $this->addDetail();
    }

    public function updatedDetails($value, $key): void
    {
        [$index, $field] = explode('.', $key);
        if (! in_array($field, ['stock_detail_id', 'quantity', 'number_serial_first']) || ! isset($this->details[$index])) {
            return;
        }
        $row = &$this->details[$index];
        $stock = $this->stocks()->firstWhere('id', $row['stock_detail_id']);
        if (! $stock) {
            return;
        }
        if ($field === 'stock_detail_id') {
            $row['number_serial_first'] = $stock->number_serial_first ?? '';
        }
        if (! $stock->number_serial_first) {
            $row['number_serial_first'] = '';
            $row['number_serial_second'] = '';

            return;
        }
        if ((int) $row['quantity'] > 0 && $row['number_serial_first']) {
            try {
                $serial = app(SerialRangeService::class);
                $row['number_serial_second'] = $serial->format($serial->parse($row['number_serial_first'])['number'] + (int) $row['quantity'] - 1, $row['number_serial_first']);
            } catch (\InvalidArgumentException $e) {
                $this->addError('details.'.$index.'.number_serial_first', $e->getMessage());
            }
        }
    }

    private function stocks()
    {
        $region = auth()->user()->hasRole('Admin') ? $this->regional_police_id : auth()->user()->regional_police_id;

        if (empty($region)) {
            return collect();
        }

        return StockDetail::with(['type', 'typeDetail', 'service', 'serviceDetail'])->where('regional_police_id', $region)
            ->whereNull('police_station_id')->where('is_active', true)->where('quantity', '>', 0)->orderBy('created_at')->get();
    }

    public function save($ship = false)
    {
        $this->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('material_shipments', 'code')->ignore($this->shipmentId),
            ],
            'shipment_date' => 'required|date_format:Y-m-d',
            'regional_police_id' => 'required|exists:regional_police,id',
            'receiver_police_station_id' => 'required|exists:police_stations,id',
            'details' => 'required|array|min:1',
            'details.*.stock_detail_id' => 'required|exists:stock_details,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.number_serial_first' => 'nullable|string|max:100',
            'details.*.number_serial_second' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
        ], [
            'code.required' => 'Nomor SPPM wajib diisi.',
            'code.unique' => 'Nomor SPPM sudah digunakan.',
            'code.max' => 'Nomor SPPM maksimal 50 karakter.',
            'regional_police_id.required' => 'Polda pengirim wajib dipilih.',
            'receiver_police_station_id.required' => 'Polres tujuan wajib dipilih.',
            'details.required' => 'Minimal satu material harus diisi.',
            'details.min' => 'Minimal satu material harus diisi.',
        ]);

        $code = trim($this->code);

        try {
            CreateMaterialShipmentAction::run([
                'code' => $code,
                'shipment_date' => $this->shipment_date,
                'status' => 'draft',
                'sender_regional_police_id' => $this->regional_police_id,
                'receiver_police_station_id' => $this->receiver_police_station_id,
                'notes' => $this->notes,
                'is_active' => true,
            ], $this->details, (bool) $ship, $this->shipmentId);
            session()->flash('success', 'SPPM tersimpan dan dapat diakses Polres serta dipindai warehouse.');

            return $this->redirect(route('menu-polda.material-shipment'), navigate: true);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Nomor SPPM')) {
                $this->addError('code', $e->getMessage());
            } else {
                $this->addError('details', $e->getMessage());
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.menu-polda.material-shipment.detail.admin-menu-polda-material-shipment-create', [
            'stocks' => $this->stocks(),
            'regions' => RegionalPolice::where('is_active', true)->orderBy('name')->get(),
            'stations' => empty($this->regional_police_id)
                ? collect()
                : PoliceStation::where('regional_police_id', $this->regional_police_id)->where('is_active', true)->orderBy('name')->get(),
        ])->layout('components.layouts.main.app');
    }
}
