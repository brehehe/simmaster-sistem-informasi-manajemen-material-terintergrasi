<?php

namespace App\Livewire\Admin\MenuPolres\StockOpname\Edit;

use App\Livewire\Concerns\AuthorizesPolresData;
use App\Models\StockOpname\StockOpname;
use App\Models\StockOpname\StockOpnameDetail;
use DB;
use Livewire\Component;

class AdminMenuPolresStockOpnameEditIndex extends Component
{
    use AuthorizesPolresData;

    public StockOpname $opname;

    public $opname_date;

    public $notes;

    public $stockDetails = [];

    protected $rules = [
        'opname_date' => 'required|date',
        'stockDetails.*.physical_quantity' => 'required|numeric|min:0',
        'stockDetails.*.notes' => 'nullable|string',
    ];

    public function boot(): void
    {
        $this->authorizePolresMenu();
    }

    public function mount($id)
    {
        $this->opname = StockOpname::with([
            'stockOpnameDetails',
            'policeStation',
        ])->findOrFail($id);
        $this->authorizePoliceStation($this->opname->police_station_id);

        // Check if opname is draft
        if ($this->opname->status !== 'draft') {
            session()->flash('error', 'Hanya stock opname dengan status draft yang bisa diedit.');

            return $this->redirect(route('menu-polres.stock-opname'), navigate: true);
        }

        $this->opname_date = $this->opname->opname_date->format('Y-m-d');
        $this->notes = $this->opname->notes;

        // Load existing stock opname details
        $this->stockDetails = $this->opname->stockOpnameDetails->map(function ($detail) {
            return [
                'id' => $detail->id,
                'stock_detail_id' => $detail->stock_detail_id,
                'type_id' => $detail->type_id,
                'type_name' => $detail->type->name ?? '-',
                'type_detail_id' => $detail->type_detail_id,
                'type_detail_name' => $detail->typeDetail->name ?? '-',
                'rack_id' => $detail->rack_id,
                'rack_name' => $detail->rack->name ?? '-',
                'code' => $detail->code,
                'number_serial_first' => $detail->number_serial_first,
                'number_serial_second' => $detail->number_serial_second,
                'system_quantity' => $detail->system_quantity,
                'physical_quantity' => $detail->physical_quantity,
                'difference' => $detail->difference,
                'notes' => $detail->notes,
            ];
        })->toArray();
    }

    public function updatedStockDetails($value, $key)
    {
        // Auto-calculate difference when physical quantity changes
        if (strpos($key, '.physical_quantity') !== false) {
            $index = explode('.', $key)[0];
            $physical = floatval($this->stockDetails[$index]['physical_quantity'] ?? 0);
            $system = floatval($this->stockDetails[$index]['system_quantity'] ?? 0);
            $this->stockDetails[$index]['difference'] = $physical - $system;
        }
    }

    public function update()
    {
        $this->validate();

        // Re-check status
        $this->opname->refresh();
        $this->authorizePoliceStation($this->opname->police_station_id);
        if ($this->opname->status !== 'draft') {
            session()->flash('error', 'Hanya stock opname dengan status draft yang bisa diedit.');

            return $this->redirect(route('menu-polres.stock-opname'), navigate: true);
        }

        DB::transaction(function () {
            // Update stock opname
            $this->opname->update([
                'opname_date' => $this->opname_date,
                'notes' => $this->notes,
            ]);

            // Update stock opname details
            foreach ($this->stockDetails as $detail) {
                $storedDetail = StockOpnameDetail::where('id', $detail['id'])
                    ->where('stock_opname_id', $this->opname->id)
                    ->first();

                if (! $storedDetail) {
                    throw new \RuntimeException('Rincian stock opname tidak valid.');
                }

                $physicalQuantity = (float) $detail['physical_quantity'];
                $storedDetail->update([
                    'physical_quantity' => $physicalQuantity,
                    'difference' => $physicalQuantity - (float) $storedDetail->system_quantity,
                    'notes' => $detail['notes'],
                ]);
            }

            session()->flash('success', 'Stock opname berhasil diupdate.');
        });

        return $this->redirect(route('menu-polres.stock-opname'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.menu-polres.stock-opname.edit.admin-menu-polres-stock-opname-edit-index')
            ->layout('components.layouts.main.app');
    }
}
