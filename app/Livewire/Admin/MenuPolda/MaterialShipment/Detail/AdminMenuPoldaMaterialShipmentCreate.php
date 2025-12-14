<?php

namespace App\Livewire\Admin\MenuPolda\MaterialShipment\Detail;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
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
    public array $details = [];

    public function mount($id = null)
    {
        $this->shipment_date = now()->format('Y-m-d');

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
        $shipment = MaterialShipment::with('materialShipmentDetails')->findOrFail($id);

        $this->code = $shipment->code;
        $this->shipment_date = Carbon::parse($shipment->shipment_date)->format('Y-m-d');
        $this->regional_police_id = $shipment?->sender_regional_police_id;
        $this->receiver_police_station_id = $shipment->receiver_police_station_id;
        $this->notes = $shipment->notes ?? '';

        foreach ($shipment->materialShipmentDetails as $detail) {
            $stockDetail = $detail->stockDetail;
            $this->details[] = [
                'stock_detail_id' => $detail->stock_detail_id,
                'type_id' => $detail->type_id,
                'type_detail_id' => $detail->type_detail_id,
                'code' => $detail->code,
                'number_serial_first' => $detail->number_serial_first,
                'number_serial_second' => $detail->number_serial_second,
                'quantity' => $detail->quantity,
                'available_quantity' => $stockDetail ? $stockDetail->quantity : 0,
                'notes' => $detail->notes ?? '',
            ];
        }
    }

    public function addDetail()
    {
        $this->details[] = [
            'stock_detail_id' => '',
            'type_id' => '',
            'type_detail_id' => '',
            'code' => '',
            'number_serial_first' => '',
            'number_serial_second' => '',
            'quantity' => 1,
            'available_quantity' => 0,
            'notes' => '',
        ];
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details);
    }

    public function updatedDetails($value, $key)
    {
        // Parse key: details.0.stock_detail_id -> [0, stock_detail_id]
        $parts = explode('.', $key);
        if (count($parts) === 2 && $parts[1] === 'stock_detail_id') {
            $index = $parts[0];
            if (!empty($value)) {
                $stockDetail = StockDetail::with(['type', 'typeDetail'])->find($value);
                if ($stockDetail) {
                    $this->details[$index]['type_id'] = $stockDetail->type_id;
                    $this->details[$index]['type_detail_id'] = $stockDetail->type_detail_id;
                    $this->details[$index]['code'] = $stockDetail->code;
                    $this->details[$index]['number_serial_first'] = $stockDetail->number_serial_first ?? '';
                    $this->details[$index]['number_serial_second'] = $stockDetail->number_serial_second ?? '';
                    $this->details[$index]['available_quantity'] = $stockDetail->quantity;

                    // Reset quantity if exceeds available
                    if ($this->details[$index]['quantity'] > $stockDetail->quantity) {
                        $this->details[$index]['quantity'] = $stockDetail->quantity;
                    }
                }
            }
        }
    }

    public function save($ship = false)
    {
        $this->validate([
            'shipment_date' => 'required|date',
            'receiver_police_station_id' => 'required|exists:police_stations,id',
            'details' => 'required|array|min:1',
            'details.*.stock_detail_id' => 'required|exists:stock_details,id',
            'details.*.quantity' => 'required|numeric|min:1',
        ], [
            'receiver_police_station_id.required' => 'Polres tujuan harus dipilih',
            'details.required' => 'Minimal harus ada 1 item',
            'details.*.stock_detail_id.required' => 'Stock harus dipilih',
            'details.*.quantity.required' => 'Quantity harus diisi',
            'details.*.quantity.min' => 'Quantity minimal 1',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($ship) {
                $user = auth()->user();

                // Create or update shipment
                if ($this->isEditMode) {
                    $shipment = MaterialShipment::findOrFail($this->shipmentId);

                    // Only allow edit if status is draft
                    if ($shipment->status !== 'draft') {
                        throw new \Exception('Hanya pengiriman dengan status draft yang bisa diedit');
                    }

                    $shipment->update([
                        'shipment_date' => $this->shipment_date,
                        'receiver_police_station_id' => $this->receiver_police_station_id,
                        'notes' => $this->notes,
                    ]);

                    // Delete old details
                    $shipment->materialShipmentDetails()->delete();
                } else {
                    $shipment = MaterialShipment::create([
                        'code' => $this->code,
                        'shipment_date' => $this->shipment_date,
                        'status' => 'draft',
                        'sender_regional_police_id' => $this->regional_police_id,
                        'receiver_police_station_id' => $this->receiver_police_station_id,
                        'notes' => $this->notes,
                        'is_active' => true,
                    ]);
                }

                // Create details
                foreach ($this->details as $detail) {
                    $stockDetail = StockDetail::with(['type', 'typeDetail', 'rack'])->find($detail['stock_detail_id']);

                    // Validate stock detail exists
                    if (!$stockDetail) {
                        throw new \Exception("Stock detail tidak ditemukan");
                    }

                    // Skip if type_detail_id is NULL (invalid data)
                    if (!$stockDetail->type_id) {
                        throw new \Exception("Stock '{$stockDetail->code}' tidak bisa digunakan karena data tidak lengkap. Silakan pilih stock lain.");
                    }

                    // Validate quantity
                    if ($detail['quantity'] > $stockDetail->quantity) {
                        throw new \Exception("Quantity untuk {$stockDetail->code} melebihi stock tersedia");
                    }

                    MaterialShipmentDetail::create([
                        'material_shipment_id' => $shipment->id,
                        'stock_detail_id' => $detail['stock_detail_id'],
                        'rack_id' => $stockDetail->rack_id,
                        'type_id' => $stockDetail->type_id,
                        'type_detail_id' => $stockDetail->type_detail_id,
                        'code' => $stockDetail->code ?? '',
                        'number_serial_first' => $stockDetail->number_serial_first ?? '',
                        'number_serial_second' => $stockDetail->number_serial_second ?? '',
                        'quantity' => $detail['quantity'],
                        'notes' => $detail['notes'] ?? '',
                        'is_active' => true,
                    ]);
                }

                // If ship, mark as shipped
                if ($ship) {
                    $shipment->markAsShipped();
                    session()->flash('success', 'Pengiriman berhasil dikirim. Stock akan dikurangi setelah Polres mengkonfirmasi penerimaan.');
                } else {
                    session()->flash('success', $this->isEditMode ? 'Pengiriman berhasil diupdate.' : 'Pengiriman berhasil disimpan sebagai draft.');
                }
            });

            return redirect()->route('menu-polda.material-shipment');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = auth()->user();

        // Get police stations under this Polda
        $policeStations = $this->regional_police_id ? PoliceStation::where('regional_police_id', $this->regional_police_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get() : collect();

        // Get available stock for this Polda
        $stockDetailsRaw = $this->regional_police_id ? StockDetail::with(['type', 'typeDetail', 'rack'])
            ->where('quantity', '>', 0)
            ->where('is_active', true)
            ->where('regional_police_id', $this->regional_police_id)
            // ->whereNotNull('type_id')
            // ->whereNotNull('type_detail_id')
            ->orderBy('code')
            ->get() : collect();

        // Group stock details by identical attributes and sum quantities
        $stockDetails = $stockDetailsRaw->groupBy(function ($item) {
            // Group key: all differentiating fields concatenated
            return implode('|', [
                $item->type_id ?? '',
                $item->type_detail_id ?? '',
                $item->rack_id ?? '',
                $item->code ?? '',
                $item->number_serial_first ?? '',
                $item->number_serial_second ?? '',
            ]);
        })->map(function ($group) {
            // Take first item as representative
            $first = $group->first();

            // Sum all quantities in this group
            $totalQuantity = $group->sum('quantity');

            // Create a virtual grouped stock detail
            return (object) [
                'id' => $first->id,  // Use first ID for reference
                'stock_id' => $first->stock_id,
                'type_id' => $first->type_id,
                'type_detail_id' => $first->type_detail_id,
                'regional_police_id' => $first->regional_police_id,
                'police_station_id' => $first->police_station_id,
                'rack_id' => $first->rack_id,
                'code' => $first->code,
                'number_serial_first' => $first->number_serial_first,
                'number_serial_second' => $first->number_serial_second,
                'quantity' => $totalQuantity,  // ✅ AGGREGATED quantity
                'type' => $first->type,
                'typeDetail' => $first->typeDetail,
                'rack' => $first->rack,
                'grouped_ids' => $group->pluck('id')->toArray(),  // All IDs in this group
            ];
        })->values();


        $shipment = $this->isEditMode ? MaterialShipment::find($this->shipmentId) : null;

        return view('livewire.admin.menu-polda.material-shipment.detail.admin-menu-polda-material-shipment-create', [
            'policeStations' => $policeStations,
            'stockDetails' => $stockDetails,
            'shipment' => $shipment,
            'regionalPolices' => RegionalPolice::get(),
        ])->layout('components.layouts.main.app');
    }
}
