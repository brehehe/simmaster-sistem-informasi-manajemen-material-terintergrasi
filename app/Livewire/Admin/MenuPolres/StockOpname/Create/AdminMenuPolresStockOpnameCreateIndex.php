<?php

namespace App\Livewire\Admin\MenuPolres\StockOpname\Create;

use App\Models\Stock\StockDetail;
use App\Models\StockOpname\StockOpname;
use App\Models\StockOpname\StockOpnameDetail;
use DB;
use Livewire\Component;

class AdminMenuPolresStockOpnameCreateIndex extends Component
{
    public $police_station_id = '';
    public $opname_date = '';
    public $notes = '';
    public $stocksLoaded = false;
    public $stockDetails = [];

    protected $rules = [
        'opname_date' => 'required|date',
        'stockDetails.*.physical_quantity' => 'required|numeric|min:0',
        'stockDetails.*.notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->opname_date = today()->format('Y-m-d');
        $this->police_station_id = auth()->user()->police_station_id;
    }

    public function loadStock()
    {
        if (!$this->police_station_id) {
            session()->flash('error', 'Police station tidak ditemukan.');
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
        $this->validate();

        if (!$this->stocksLoaded || empty($this->stockDetails)) {
            session()->flash('error', 'Silakan load stock terlebih dahulu sebelum menyimpan.');
            return;
        }

        DB::transaction(function () {
            // Generate code
            $code = StockOpname::generateCode(false); // false = polres

            // Create stock opname
            $opname = StockOpname::create([
                'code' => $code,
                'opname_date' => $this->opname_date,
                'regional_police_id' => null,
                'police_station_id' => $this->police_station_id,
                'status' => 'draft',
                'notes' => $this->notes,
                'checked_by' => auth()->id(),
                'is_active' => true,
            ]);

            // Create stock opname details
            foreach ($this->stockDetails as $detail) {
                StockOpnameDetail::create([
                    'stock_opname_id' => $opname->id,
                    'stock_detail_id' => $detail['stock_detail_id'],
                    'type_id' => $detail['type_id'],
                    'type_detail_id' => $detail['type_detail_id'],
                    'rack_id' => $detail['rack_id'],
                    'code' => $detail['code'],
                    'number_serial_first' => $detail['number_serial_first'],
                    'number_serial_second' => $detail['number_serial_second'],
                    'system_quantity' => $detail['system_quantity'],
                    'physical_quantity' => $detail['physical_quantity'],
                    'difference' => $detail['difference'],
                    'notes' => $detail['notes'],
                    'is_active' => true,
                ]);
            }

            session()->flash('success', 'Stock opname berhasil dibuat dengan kode: ' . $code);
        });

        return $this->redirect(route('menu-polres.stock-opname'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.menu-polres.stock-opname.create.admin-menu-polres-stock-opname-create-index')
            ->layout('components.layouts.main.app');
    }
}
