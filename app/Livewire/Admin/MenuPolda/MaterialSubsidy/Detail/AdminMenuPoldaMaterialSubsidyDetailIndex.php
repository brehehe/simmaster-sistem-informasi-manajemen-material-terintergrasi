<?php

namespace App\Livewire\Admin\MenuPolda\MaterialSubsidy\Detail;

use App\Models\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy;
use App\Models\Models\MenuPolda\MaterialSubsidy\MaterialSubsidyDetail;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Police\RegionalPolice;
use Livewire\Component;

class AdminMenuPoldaMaterialSubsidyDetailIndex extends Component
{
    // Header fields
    public ?string $subsidyId = null;
    public string $subsidyDate = '';
    public string $recipientName = '';
    public string $recipientDescription = '';
    public string $notes = '';
    public string $regionalPoliceId = '';

    // Items
    public array $items = [];

    // Available type & type details
    public $types = [];
    public $allTypeDetails = [];
    public $availableStocks = [];

    public function mount(?string $id = null): void
    {
        $this->subsidyDate = now()->format('Y-m-d');
        $this->types = Type::where('is_active', true)->orderBy('name')->get();
        $this->allTypeDetails = TypeDetail::where('is_active', true)->orderBy('name')->get();

        $user = auth()->user();
        if ($user->hasRole('Polda')) {
            $this->regionalPoliceId = $user->regional_police_id ?? '';
        }

        // Load available Polda stocks for item dropdowns
        $this->loadAvailableStocks();

        if ($id) {
            $this->subsidyId = $id;
            $this->loadExistingSubsidy($id);
        } else {
            $this->addItem();
        }
    }

    private function loadAvailableStocks(): void
    {
        $policeId = $this->regionalPoliceId ?: auth()->user()->regional_police_id;
        $this->availableStocks = StockDetail::with(['type', 'typeDetail', 'stock'])
            ->whereHas('stock', fn($q) => $q->whereNotNull('regional_police_id')->whereNull('police_station_id')
                ->when($policeId, fn($sq) => $sq->where('regional_police_id', $policeId))
            )
            ->where('quantity', '>', 0)
            ->get()
            ->map(fn($sd) => [
                'id'           => $sd->id,
                'type_id'      => $sd->type_id,
                'type_detail_id' => $sd->type_detail_id,
                'type_name'    => $sd->type?->name ?? '-',
                'detail_name'  => $sd->typeDetail?->name ?? '-',
                'quantity'     => $sd->quantity,
            ])->toArray();
    }

    private function loadExistingSubsidy(string $id): void
    {
        $subsidy = MaterialSubsidy::with(['materialSubsidyDetails.type', 'materialSubsidyDetails.typeDetail'])->find($id);
        if (!$subsidy || $subsidy->status !== 'draft') {
            session()->flash('error', 'Data tidak ditemukan atau sudah dikonfirmasi.');
            redirect()->route('menu-polda.material-subsidy');
            return;
        }

        $this->subsidyDate         = $subsidy->subsidy_date->format('Y-m-d');
        $this->recipientName       = $subsidy->recipient_name;
        $this->recipientDescription = $subsidy->recipient_description ?? '';
        $this->notes               = $subsidy->notes ?? '';
        $this->regionalPoliceId    = $subsidy->regional_police_id;

        $this->items = $subsidy->materialSubsidyDetails->map(fn($d) => [
            'type_id'        => $d->type_id,
            'type_detail_id' => $d->type_detail_id ?? '',
            'stock_detail_id' => $d->stock_detail_id ?? '',
            'quantity'       => $d->quantity,
            'notes'          => $d->notes ?? '',
        ])->toArray();

        if (empty($this->items)) {
            $this->addItem();
        }
    }

    public function addItem(): void
    {
        $this->items[] = [
            'type_id'        => '',
            'type_detail_id' => '',
            'stock_detail_id' => '',
            'quantity'       => 1,
            'notes'          => '',
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function getTypeDetailsForItem(int $index): array
    {
        $typeId = $this->items[$index]['type_id'] ?? '';
        if (!$typeId) return [];
        return TypeDetail::where('type_id', $typeId)->where('is_active', true)->orderBy('name')->get()
            ->map(fn($td) => ['id' => $td->id, 'name' => $td->name])->toArray();
    }

    public function getAvailableStockForItem(int $index): int
    {
        $typeId = $this->items[$index]['type_id'] ?? '';
        $typeDetailId = $this->items[$index]['type_detail_id'] ?? null;
        $policeId = $this->regionalPoliceId ?: auth()->user()->regional_police_id;

        return StockDetail::whereHas('stock', fn($q) =>
                $q->where('regional_police_id', $policeId)->whereNull('police_station_id'))
            ->where('type_id', $typeId)
            ->when($typeDetailId, fn($q) => $q->where('type_detail_id', $typeDetailId))
            ->sum('quantity');
    }

    public function save(): void
    {
        $this->validate([
            'subsidyDate'          => 'required|date',
            'recipientName'        => 'required|string|max:255',
            'recipientDescription' => 'nullable|string',
            'notes'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.type_id'      => 'required|exists:types,id',
            'items.*.quantity'     => 'required|integer|min:1',
        ], [
            'recipientName.required' => 'Nama penerima wajib diisi.',
            'items.required'         => 'Minimal satu item material harus ditambahkan.',
            'items.*.type_id.required' => 'Jenis material wajib dipilih.',
            'items.*.quantity.min'   => 'Jumlah minimal 1.',
        ]);

        $user = auth()->user();
        $policeId = $this->regionalPoliceId ?: $user->regional_police_id;

        if (!$policeId) {
            session()->flash('error', 'Polda tidak ditemukan. Hubungi administrator.');
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($policeId) {
                if ($this->subsidyId) {
                    $subsidy = MaterialSubsidy::find($this->subsidyId);
                    $subsidy->update([
                        'subsidy_date'          => $this->subsidyDate,
                        'recipient_name'        => $this->recipientName,
                        'recipient_description' => $this->recipientDescription ?: null,
                        'notes'                 => $this->notes ?: null,
                        'regional_police_id'    => $policeId,
                    ]);
                    $subsidy->materialSubsidyDetails()->delete();
                } else {
                    $subsidy = MaterialSubsidy::create([
                        'code'                  => MaterialSubsidy::generateCode($policeId),
                        'subsidy_date'          => $this->subsidyDate,
                        'status'                => 'draft',
                        'regional_police_id'    => $policeId,
                        'recipient_name'        => $this->recipientName,
                        'recipient_description' => $this->recipientDescription ?: null,
                        'notes'                 => $this->notes ?: null,
                        'is_active'             => true,
                    ]);
                }

                foreach ($this->items as $item) {
                    MaterialSubsidyDetail::create([
                        'material_subsidy_id' => $subsidy->id,
                        'type_id'             => $item['type_id'],
                        'type_detail_id'      => $item['type_detail_id'] ?: null,
                        'stock_detail_id'     => $item['stock_detail_id'] ?: null,
                        'quantity'            => $item['quantity'],
                        'notes'               => $item['notes'] ?: null,
                    ]);
                }
            });

            session()->flash('success', 'Subsidi material berhasil disimpan.');
            redirect()->route('menu-polda.material-subsidy');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $regionalPolices = RegionalPolice::where('is_active', true)->orderBy('name')->get();
        $typeDetails = [];
        foreach ($this->items as $index => $item) {
            $typeDetails[$index] = $this->getTypeDetailsForItem($index);
        }

        return view('livewire.admin.menu-polda.material-subsidy.detail.admin-menu-polda-material-subsidy-detail-index', [
            'types'          => $this->types,
            'typeDetails'    => $typeDetails,
            'regionalPolices' => $regionalPolices,
            'availableStocks' => $this->availableStocks,
        ])->layout('components.layouts.main.app');
    }
}
