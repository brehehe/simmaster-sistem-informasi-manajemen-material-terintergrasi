<?php

namespace App\Livewire\Admin\StockOpname;

use App\Models\StockOpname\StockOpname;
use App\Models\StockOpname\StockOpnameDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminStockOpnameEdit extends Component
{
    public StockOpname $opname;
    public $opname_date;
    public $notes;
    public $stockDetails = [];

    protected $rules = [
        'opname_date' => 'required|date',
        'stockDetails.*.physical_quantity' => 'required|numeric|min:0',
        'stockDetails.*.notes' => 'nullable|string',
    ];

    public function mount($id)
    {
        $this->opname = StockOpname::with([
            'stockOpnameDetails',
            'regionalPolice',
            'policeStation',
        ])->findOrFail($id);

        // Check if opname is draft
        if ($this->opname->status !== 'draft') {
            session()->flash('error', 'Hanya stock opname dengan status draft yang bisa diedit.');
            return redirect()->route('admin.stock-opname');
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
        if ($this->opname->status !== 'draft') {
            session()->flash('error', 'Hanya stock opname dengan status draft yang bisa diedit.');
            return redirect()->route('admin.stock-opname');
        }

        DB::transaction(function () {
            // Update stock opname
            $this->opname->update([
                'opname_date' => $this->opname_date,
                'notes' => $this->notes,
            ]);

            // Update stock opname details
            foreach ($this->stockDetails as $detail) {
                StockOpnameDetail::where('id', $detail['id'])->update([
                    'physical_quantity' => $detail['physical_quantity'],
                    'difference' => $detail['difference'],
                    'notes' => $detail['notes'],
                ]);
            }

            session()->flash('success', 'Stock opname berhasil diupdate.');
        });

        return redirect()->route('admin.stock-opname');
    }

    public function render()
    {
        return view('livewire.admin.stock-opname.admin-stock-opname-edit')->layout('components.layouts.main.app');
    }
}
