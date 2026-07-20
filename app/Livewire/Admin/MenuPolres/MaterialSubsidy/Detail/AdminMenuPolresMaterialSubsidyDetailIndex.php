<?php

namespace App\Livewire\Admin\MenuPolres\MaterialSubsidy\Detail;

use App\Models\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy;
use App\Models\Models\MenuPolda\MaterialSubsidy\MaterialSubsidyDetail;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Police\PoliceStation;
use Livewire\Component;

class AdminMenuPolresMaterialSubsidyDetailIndex extends Component
{
    public ?string $subsidyId = null;
    public string $subsidyDate = '';
    public string $recipientName = '';
    public string $recipientDescription = '';
    public string $notes = '';
    public ?string $policeStationId = null;

    public array $items = [];

    public $types = [];
    public $allTypeDetails = [];

    public function toJSON()
    {
        return [];
    }

    public function mount(?string $id = null): void
    {
        $this->subsidyDate = now()->format('Y-m-d');
        $this->types = Type::where('is_active', true)->orderBy('name')->get();
        $this->allTypeDetails = TypeDetail::where('is_active', true)->orderBy('name')->get();

        $user = auth()->user();
        if ($user->hasRole('Polres') || !empty($user->police_station_id)) {
            $this->policeStationId = $user->police_station_id;
        }

        if ($id) {
            $this->subsidyId = $id;
            $this->loadExistingSubsidy($id);
        } else {
            $this->addItem();
        }
    }

    private function loadExistingSubsidy(string $id): void
    {
        $subsidy = MaterialSubsidy::with(['materialSubsidyDetails.type', 'materialSubsidyDetails.typeDetail'])->find($id);
        if (!$subsidy || $subsidy->status !== 'draft') {
            session()->flash('error', 'Data tidak ditemukan atau sudah dikonfirmasi.');
            redirect()->route('menu-polres.material-subsidy');
            return;
        }

        $this->subsidyDate          = $subsidy->subsidy_date->format('Y-m-d');
        $this->recipientName        = $subsidy->recipient_name;
        $this->recipientDescription  = $subsidy->recipient_description ?? '';
        $this->notes                = $subsidy->notes ?? '';
        $this->policeStationId     = $subsidy->police_station_id;

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
        $policeStationId = $this->policeStationId ?: auth()->user()->police_station_id;

        if (!$typeId || !$policeStationId) return 0;

        return StockDetail::where('police_station_id', $policeStationId)
            ->where('type_id', $typeId)
            ->when($typeDetailId, fn($q) => $q->where('type_detail_id', $typeDetailId))
            ->where('is_active', true)
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
            'recipientName.required'   => 'Nama penerima / tujuan wajib diisi.',
            'items.required'           => 'Minimal satu item material harus ditambahkan.',
            'items.*.type_id.required' => 'Jenis material wajib dipilih.',
            'items.*.quantity.min'     => 'Jumlah minimal 1.',
        ]);

        $user = auth()->user();
        $policeStationId = $this->policeStationId ?: $user->police_station_id;

        if (!$policeStationId) {
            session()->flash('error', 'Polres tidak ditemukan.');
            return;
        }

        $policeStation = PoliceStation::find($policeStationId);
        $regionalPoliceId = $policeStation?->regional_police_id;

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($policeStationId, $regionalPoliceId) {
                if ($this->subsidyId) {
                    $subsidy = MaterialSubsidy::findOrFail($this->subsidyId);
                    $subsidy->update([
                        'subsidy_date'          => $this->subsidyDate,
                        'recipient_name'        => $this->recipientName,
                        'recipient_description' => $this->recipientDescription,
                        'notes'                => $this->notes,
                    ]);
                    $subsidy->materialSubsidyDetails()->delete();
                } else {
                    $subsidy = MaterialSubsidy::create([
                        'code'                 => MaterialSubsidy::generateCode($regionalPoliceId),
                        'subsidy_date'          => $this->subsidyDate,
                        'status'               => 'draft',
                        'regional_police_id'   => $regionalPoliceId,
                        'police_station_id'    => $policeStationId,
                        'recipient_name'        => $this->recipientName,
                        'recipient_description' => $this->recipientDescription,
                        'notes'                => $this->notes,
                        'is_active'            => true,
                    ]);
                }

                foreach ($this->items as $item) {
                    $subsidy->materialSubsidyDetails()->create([
                        'type_id'        => $item['type_id'],
                        'type_detail_id' => $item['type_detail_id'] ?: null,
                        'stock_detail_id' => $item['stock_detail_id'] ?: null,
                        'quantity'       => $item['quantity'],
                        'notes'          => $item['notes'] ?? '',
                    ]);
                }
            });

            session()->flash('success', $this->subsidyId ? 'Draft subsidi silang berhasil diperbarui.' : 'Draft subsidi silang berhasil disimpan.');
            $this->redirect(route('menu-polres.material-subsidy'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.menu-polres.material-subsidy.detail.admin-menu-polres-material-subsidy-detail-index')
            ->layout('components.layouts.main.app');
    }
}
