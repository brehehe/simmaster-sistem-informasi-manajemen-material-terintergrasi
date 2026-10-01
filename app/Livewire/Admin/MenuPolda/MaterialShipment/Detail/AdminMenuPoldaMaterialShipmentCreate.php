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

    public string $number = '';

    public string $shipment_date = '';

    public string $regional_police_id = '';

    public string $receiver_police_station_id = '';

    public string $notes = '';

    public array $details = [];

    public function boot(): void
    {
        $this->authorize('create', MaterialShipment::class);
    }

    public function mount($id = null): void
    {
        $this->shipment_date = now('Asia/Jakarta')->toDateString();
        $this->regional_police_id = auth()->user()->regional_police_id ?? '';
        if ($id) {
            $shipment = MaterialShipment::with('materialShipmentDetails')->findOrFail($id);
            $this->authorize('update', $shipment);
            $this->shipmentId = $id;
            $this->number = preg_match('~^SPPM/(\d+)/VII/LOG\.3\.6\.7\./\d{4}$~', $shipment->code, $m) ? $m[1] : '';
            $this->shipment_date = $shipment->shipment_date->toDateString();
            $this->regional_police_id = $shipment->sender_regional_police_id;
            $this->receiver_police_station_id = $shipment->receiver_police_station_id;
            $this->notes = $shipment->notes ?? '';
            foreach ($shipment->materialShipmentDetails as $detail) {
                $this->details[] = $detail->only(['stock_detail_id', 'number_serial_first', 'number_serial_second', 'quantity', 'notes']);
            }
        } else {
            $this->addDetail();
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

        return StockDetail::with(['type', 'typeDetail', 'service', 'serviceDetail'])->where('regional_police_id', $region)
            ->whereNull('police_station_id')->where('is_active', true)->where('quantity', '>', 0)->orderBy('created_at')->get();
    }

    public function save($ship = false)
    {
        $this->validate([
            'number' => ['required', 'regex:/^[0-9]+$/', 'max:20'], 'shipment_date' => 'required|date_format:Y-m-d',
            'regional_police_id' => 'required|exists:regional_police,id', 'receiver_police_station_id' => 'required|exists:police_stations,id',
            'details' => 'required|array|min:1', 'details.*.stock_detail_id' => 'required|exists:stock_details,id',
            'details.*.quantity' => 'required|integer|min:1', 'details.*.number_serial_first' => 'nullable|string|max:100',
            'details.*.number_serial_second' => 'nullable|string|max:100', 'notes' => 'nullable|string|max:2000',
        ]);
        try {
            CreateMaterialShipmentAction::run([
                'code' => 'SPPM/'.$this->number.'/VII/LOG.3.6.7./'.substr($this->shipment_date, 0, 4),
                'shipment_date' => $this->shipment_date, 'status' => 'draft',
                'sender_regional_police_id' => $this->regional_police_id, 'receiver_police_station_id' => $this->receiver_police_station_id,
                'notes' => $this->notes, 'is_active' => true,
            ], $this->details, (bool) $ship, $this->shipmentId);
            session()->flash('success', 'SPPM tersimpan dan dapat diakses Polres serta dipindai warehouse.');

            return $this->redirect(route('menu-polda.material-shipment'), navigate: true);
        } catch (\Exception $e) {
            $this->addError('details', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.menu-polda.material-shipment.detail.admin-menu-polda-material-shipment-create', [
            'stocks' => $this->stocks(), 'regions' => RegionalPolice::where('is_active', true)->orderBy('name')->get(),
            'stations' => PoliceStation::where('regional_police_id', $this->regional_police_id)->where('is_active', true)->orderBy('name')->get(),
        ])->layout('components.layouts.main.app');
    }
}
