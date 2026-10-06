<?php

namespace App\Livewire\Admin\MenuPolres\StockOpname\Create;

use App\Livewire\Concerns\AuthorizesPolresData;
use App\Models\Police\PoliceStation;
use App\Models\Stock\StockDetail;
use App\Models\StockOpname\StockOpname;
use Livewire\Component;

class AdminMenuPolresStockOpnameCreateIndex extends Component
{
    use AuthorizesPolresData;

    public $police_station_id = '';

    public $opname_date = '';

    public $notes = '';

    public $stocksLoaded = false;

    public $stockDetails = [];

    public $policeStations = [];

    protected $rules = [
        'police_station_id' => 'required|exists:police_stations,id',
        'opname_date' => 'required|date',
        'stockDetails.*.physical_quantity' => 'required|numeric|min:0',
        'stockDetails.*.notes' => 'nullable|string',
    ];

    public function boot(): void
    {
        $this->authorizePolresMenu();
    }

    public function mount()
    {
        $this->opname_date = today()->format('Y-m-d');
        $this->police_station_id = $this->policeStationForUser();
        $this->policeStations = PoliceStation::where('is_active', true)->orderBy('name')->get();
    }

    public function updatedPoliceStationId(): void
    {
        $this->stocksLoaded = false;
        $this->stockDetails = [];
    }

    public function loadStock()
    {
        if (! $this->police_station_id) {
            session()->flash('error', 'Police station tidak ditemukan.');

            return;
        }

        $this->authorizePoliceStation($this->police_station_id);

        if (! PoliceStation::whereKey($this->police_station_id)->where('is_active', true)->exists()) {
            session()->flash('error', 'Police station tidak aktif atau tidak ditemukan.');

            return;
        }

        $query = StockDetail::with(['type', 'typeDetail', 'rack', 'policeStation'])
            ->where('is_active', true)
            ->where('police_station_id', $this->police_station_id)
            ->whereNull('regional_police_id');

        $stocks = $query->orderBy('type_id')
            ->orderBy('type_detail_id')
            ->get();

        if ($stocks->isEmpty()) {
            session()->flash('error', 'Tidak ada stock yang ditemukan untuk police station Anda.');

            return;
        }

        // Format stock details for form input
        $this->stockDetails = $stocks->map(function ($stock) {
            return [
                'stock_detail_id' => $stock->id,
                'type_id' => $stock->type_id,
                'type_name' => $stock->type->name ?? '-',
                'type_detail_id' => $stock->type_detail_id,
                'type_detail_name' => $stock->typeDetail->name ?? '-',
                'rack_id' => $stock->rack_id,
                'rack_name' => $stock->rack->name ?? '-',
                'code' => $stock->code,
                'number_serial_first' => $stock->number_serial_first,
                'number_serial_second' => $stock->number_serial_second,
                'system_quantity' => $stock->quantity,
                'physical_quantity' => $stock->quantity, // Default to system quantity
                'difference' => 0,
                'notes' => '',
            ];
        })->toArray();

        $this->stocksLoaded = true;
        session()->flash('success', 'Stock berhasil dimuat. Silakan input physical quantity.');
    }

    public function updatedStockDetails($value, $key)
    {
        // Auto-calculate difference when physical quantity changes
        if (strpos($key, 'physical_quantity') !== false) {
            $index = explode('.', $key)[0];
            $physical = floatval($this->stockDetails[$index]['physical_quantity'] ?? 0);
            $system = floatval($this->stockDetails[$index]['system_quantity'] ?? 0);
            $this->stockDetails[$index]['difference'] = $physical - $system;
        }
    }

    public function save()
    {
        if (auth()->user()->hasRole('Polres')) {
            $this->police_station_id = auth()->user()->police_station_id;
        }
        $this->validate();
        $this->authorizePoliceStation($this->police_station_id);

        if (! $this->stocksLoaded || empty($this->stockDetails)) {
            session()->flash('error', 'Silakan load stock terlebih dahulu sebelum menyimpan.');

            return;
        }

        foreach ($this->stockDetails as $index => $detail) {
            $stockDetail = StockDetail::with(['type', 'typeDetail', 'rack'])
                ->whereKey($detail['stock_detail_id'] ?? null)
                ->where('police_station_id', $this->police_station_id)
                ->where('is_active', true)
                ->first();

            if (! $stockDetail) {
                $this->addError("stockDetails.{$index}.physical_quantity", 'Stok tidak valid untuk Polres yang dipilih.');

                return;
            }

            $physicalQuantity = (float) ($detail['physical_quantity'] ?? 0);
            $this->stockDetails[$index] = array_merge($detail, [
                'type_id' => $stockDetail->type_id,
                'type_detail_id' => $stockDetail->type_detail_id,
                'rack_id' => $stockDetail->rack_id,
                'code' => $stockDetail->code,
                'number_serial_first' => $stockDetail->number_serial_first,
                'number_serial_second' => $stockDetail->number_serial_second,
                'system_quantity' => (float) $stockDetail->quantity,
                'difference' => $physicalQuantity - (float) $stockDetail->quantity,
            ]);
        }

        try {
            $code = StockOpname::generateCode(false); // false = polres

            $headerData = [
                'code' => $code,
                'opname_date' => $this->opname_date,
                'regional_police_id' => null,
                'police_station_id' => $this->police_station_id,
                'status' => 'draft',
                'notes' => $this->notes,
                'checked_by' => auth()->id(),
                'is_active' => true,
            ];

            $opname = \App\Actions\MenuPolda\CreateStockOpnameAction::run(
                $headerData,
                $this->stockDetails
            );

            session()->flash('success', 'Stock opname berhasil dibuat dengan kode: '.$opname->code);

            return $this->redirect(route('menu-polres.stock-opname'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.menu-polres.stock-opname.create.admin-menu-polres-stock-opname-create-index')
            ->layout('components.layouts.main.app');
    }
}
