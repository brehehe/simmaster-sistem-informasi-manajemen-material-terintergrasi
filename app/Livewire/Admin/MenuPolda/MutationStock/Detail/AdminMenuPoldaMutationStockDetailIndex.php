<?php

namespace App\Livewire\Admin\MenuPolda\MutationStock\Detail;

use App\Models\Models\MenuPolda\MutationStock\MutationStock;
use App\Models\Models\MenuPolda\MutationStock\MutationStockDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Carbon\Carbon;
use Livewire\Component;

class AdminMenuPoldaMutationStockDetailIndex extends Component
{
    public ?string $mutationId = null;
    public bool $isEditMode = false;
    public string $code = '';
    public string $mutation_date = '';

    // Sender fields (flexible - can be Polda or Polres)
    public ?string $sender_regional_police_id = null;
    public ?string $sender_police_station_id = null;
    public string $sender_type = 'polda'; // 'polda' or 'polres'

    // Receiver fields (flexible - can be Polda or Polres)
    public ?string $receiver_regional_police_id = null;
    public ?string $receiver_police_station_id = null;
    public string $receiver_type = 'polres'; // 'polda' or 'polres'

    public string $notes = '';
    public array $details = [];

    public function mount($id = null)
    {
        $this->mutation_date = now()->format('Y-m-d');
        $user = auth()->user();

        if ($id) {
            $this->mutationId = $id;
            $this->isEditMode = true;
            $this->loadMutation($id);
        } else {
            // Determine sender based on user role
            if ($user->hasRole('Polda')) {
                $this->sender_type = 'polda';
                $this->sender_regional_police_id = $user->regional_police_id;
            } elseif ($user->hasRole('Polres')) {
                $this->sender_type = 'polres';
                $this->sender_police_station_id = $user->police_station_id;
            }

            $this->code = MutationStock::generateCode(
                $this->sender_regional_police_id,
                $this->sender_police_station_id
            );
            $this->addDetail();
        }
    }

    protected function loadMutation($id)
    {
        $mutation = MutationStock::with('mutationStockDetails')->findOrFail($id);

        $this->code = $mutation->code;
        $this->mutation_date = Carbon::parse($mutation->mutation_date)->format('Y-m-d');
        $this->notes = $mutation->notes ?? '';

        // Load sender
        if ($mutation->sender_regional_police_id) {
            $this->sender_type = 'polda';
            $this->sender_regional_police_id = $mutation->sender_regional_police_id;
        } else {
            $this->sender_type = 'polres';
            $this->sender_police_station_id = $mutation->sender_police_station_id;
        }

        // Load receiver
        if ($mutation->receiver_regional_police_id) {
            $this->receiver_type = 'polda';
            $this->receiver_regional_police_id = $mutation->receiver_regional_police_id;
        } else {
            $this->receiver_type = 'polres';
            $this->receiver_police_station_id = $mutation->receiver_police_station_id;
        }

        foreach ($mutation->mutationStockDetails as $detail) {
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

    public function updatedSenderType()
    {
        // Reset sender fields when type changes
        $this->sender_regional_police_id = null;
        $this->sender_police_station_id = null;
        $this->details = [];
        $this->addDetail();
    }

    public function updatedSenderRegionalPoliceId()
    {
        $this->details = [];
        $this->addDetail();
    }

    public function updatedSenderPoliceStationId()
    {
        $this->details = [];
        $this->addDetail();
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

    public function save($send = false)
    {
        $this->validate([
            'mutation_date' => 'required|date',
            'sender_type' => 'required|in:polda,polres',
            'receiver_type' => 'required|in:polda,polres',
            'details' => 'required|array|min:1',
            'details.*.stock_detail_id' => 'required|exists:stock_details,id',
            'details.*.quantity' => 'required|numeric|min:1',
        ], [
            'details.required' => 'Minimal harus ada 1 item',
            'details.*.stock_detail_id.required' => 'Stock harus dipilih',
            'details.*.quantity.required' => 'Quantity harus diisi',
            'details.*.quantity.min' => 'Quantity minimal 1',
        ]);

        // Validate sender selection
        if ($this->sender_type === 'polda' && !$this->sender_regional_police_id) {
            session()->flash('error', 'Polda pengirim harus dipilih');
            return;
        }
        if ($this->sender_type === 'polres' && !$this->sender_police_station_id) {
            session()->flash('error', 'Polres pengirim harus dipilih');
            return;
        }

        // Validate receiver selection
        if ($this->receiver_type === 'polda' && !$this->receiver_regional_police_id) {
            session()->flash('error', 'Polda penerima harus dipilih');
            return;
        }
        if ($this->receiver_type === 'polres' && !$this->receiver_police_station_id) {
            session()->flash('error', 'Polres penerima harus dipilih');
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($send) {
                // Create or update mutation
                if ($this->isEditMode) {
                    $mutation = MutationStock::findOrFail($this->mutationId);

                    if ($mutation->status !== 'draft') {
                        throw new \Exception('Hanya mutasi dengan status draft yang bisa diedit');
                    }

                    $mutation->update([
                        'mutation_date' => $this->mutation_date,
                        'sender_regional_police_id' => $this->sender_type === 'polda' ? $this->sender_regional_police_id : null,
                        'sender_police_station_id' => $this->sender_type === 'polres' ? $this->sender_police_station_id : null,
                        'receiver_regional_police_id' => $this->receiver_type === 'polda' ? $this->receiver_regional_police_id : null,
                        'receiver_police_station_id' => $this->receiver_type === 'polres' ? $this->receiver_police_station_id : null,
                        'notes' => $this->notes,
                    ]);

                    // Delete old details
                    $mutation->mutationStockDetails()->delete();
                } else {
                    $mutation = MutationStock::create([
                        'code' => $this->code,
                        'mutation_date' => $this->mutation_date,
                        'status' => 'draft',
                        'sender_regional_police_id' => $this->sender_type === 'polda' ? $this->sender_regional_police_id : null,
                        'sender_police_station_id' => $this->sender_type === 'polres' ? $this->sender_police_station_id : null,
                        'receiver_regional_police_id' => $this->receiver_type === 'polda' ? $this->receiver_regional_police_id : null,
                        'receiver_police_station_id' => $this->receiver_type === 'polres' ? $this->receiver_police_station_id : null,
                        'notes' => $this->notes,
                        'is_active' => true,
                    ]);
                }

                // Create details
                foreach ($this->details as $detail) {
                    $stockDetail = StockDetail::with(['type', 'typeDetail'])->find($detail['stock_detail_id']);

                    if (!$stockDetail) {
                        throw new \Exception("Stock detail tidak ditemukan");
                    }

                    // Validate quantity
                    if ($detail['quantity'] > $stockDetail->quantity) {
                        throw new \Exception("Quantity untuk {$stockDetail->code} melebihi stock tersedia");
                    }

                    MutationStockDetail::create([
                        'mutation_stock_id' => $mutation->id,
                        'stock_detail_id' => $detail['stock_detail_id'],
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

                // If send, mark as sent
                if ($send) {
                    $mutation->markAsSent();
                    session()->flash('success', 'Mutasi stock berhasil dikirim. Stock akan dikurangi setelah penerima mengkonfirmasi.');
                } else {
                    session()->flash('success', $this->isEditMode ? 'Mutasi stock berhasil diupdate.' : 'Mutasi stock berhasil disimpan sebagai draft.');
                }
            });

            return $this->redirect(route('menu-polda.mutation-stock'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = auth()->user();

        // Get available locations
        $regionalPolices = RegionalPolice::where('is_active', true)->orderBy('name')->get();
        $policeStations = PoliceStation::where('is_active', true)->orderBy('name')->get();

        // Get available stock based on sender
        $stockDetailsRaw = collect();

        if ($this->sender_type === 'polda' && $this->sender_regional_police_id) {
            $stockDetailsRaw = StockDetail::with(['type', 'typeDetail', 'rack'])
                ->where('quantity', '>', 0)
                ->where('is_active', true)
                ->where('regional_police_id', $this->sender_regional_police_id)
                ->orderBy('code')
                ->get();
        } elseif ($this->sender_type === 'polres' && $this->sender_police_station_id) {
            $stockDetailsRaw = StockDetail::with(['type', 'typeDetail', 'rack'])
                ->where('quantity', '>', 0)
                ->where('is_active', true)
                ->where('police_station_id', $this->sender_police_station_id)
                ->orderBy('code')
                ->get();
        }

        // Group stock details by identical attributes
        $stockDetails = $stockDetailsRaw->groupBy(function ($item) {
            return implode('|', [
                $item->type_id ?? '',
                $item->type_detail_id ?? '',
                $item->rack_id ?? '',
                $item->code ?? '',
                $item->number_serial_first ?? '',
                $item->number_serial_second ?? '',
            ]);
        })->map(function ($group) {
            $first = $group->first();
            $totalQuantity = $group->sum('quantity');

            return (object) [
                'id' => $first->id,
                'stock_id' => $first->stock_id,
                'type_id' => $first->type_id,
                'type_detail_id' => $first->type_detail_id,
                'regional_police_id' => $first->regional_police_id,
                'police_station_id' => $first->police_station_id,
                'rack_id' => $first->rack_id,
                'code' => $first->code,
                'number_serial_first' => $first->number_serial_first,
                'number_serial_second' => $first->number_serial_second,
                'quantity' => $totalQuantity,
                'type' => $first->type,
                'typeDetail' => $first->typeDetail,
                'rack' => $first->rack,
                'grouped_ids' => $group->pluck('id')->toArray(),
            ];
        })->values();

        $mutation = $this->isEditMode ? MutationStock::find($this->mutationId) : null;

        return view('livewire.admin.menu-polda.mutation-stock.detail.admin-menu-polda-mutation-stock-detail-index', [
            'regionalPolices' => $regionalPolices,
            'policeStations' => $policeStations,
            'stockDetails' => $stockDetails,
            'mutation' => $mutation,
        ])->layout('components.layouts.main.app');
    }
}
