<?php

namespace App\Livewire\Admin\Master\Target\Capaian;

use App\Models\Target\Target;
use App\Models\Target\TargetPnbpItem;
use App\Services\PnbpTargetService;
use Livewire\Attributes\Url;
use Livewire\Component;

class AdminMasterTargetCapaianIndex extends Component
{
    #[Url]
    public int $selectedYear = 2026;

    #[Url]
    public string $selectedMonth = 'REKAP';

    public ?string $search = '';

    public array $availableYears = [];

    public array $monthlySummaries = [];

    public array $sheetDetails = [];

    public array $syncInfo = [];

    public bool $hasExcelFile = true;

    // Modal States for Target CRUD
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditMode = false;

    // Target Form Fields
    public ?string $targetId = null;
    public string $name = '';
    public int $year;
    public ?string $description = null;
    public bool $is_active = true;

    // Modal States for Item CRUD
    public bool $showItemModal = false;
    public bool $showDeleteItemModal = false;
    public bool $isItemEditMode = false;

    // Item Form Fields
    public ?string $itemId = null;
    public ?string $itemNo = '';
    public string $itemName = '';
    public int $itemLevel = 2; // 1: Header/Kategori, 2: Sub-kategori, 3: Rincian
    public float $itemTarif = 0;
    public float $itemVolumeTarget = 0;
    public float $itemVolumeRealization = 0;
    public bool $applyToAllMonths = false;

    protected function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'year'        => 'required|integer|min:2000|max:2100',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'boolean',
        ];
    }

    public function mount(PnbpTargetService $service): void
    {
        $this->availableYears = $service->getAvailableYears();

        if (! $this->selectedYear) {
            $this->selectedYear = (int) now()->format('Y');
        }

        if (! in_array($this->selectedYear, $this->availableYears) && ! empty($this->availableYears)) {
            $this->selectedYear = in_array(2026, $this->availableYears) ? 2026 : $this->availableYears[0];
        }

        $this->year = $this->selectedYear;
        $this->loadData($service);
    }

    public function updatedSelectedYear(): void
    {
        $service = app(PnbpTargetService::class);
        $this->setYear($this->selectedYear, $service);
    }

    public function loadData(PnbpTargetService $service): void
    {
        try {
            $parsed = $service->getParsedDataCached(null, $this->selectedYear);
            $this->hasExcelFile = $parsed['has_file'] ?? false;
            $this->monthlySummaries = $parsed['monthly_summaries'] ?? [];
            $this->sheetDetails = $service->getSheetDetails($this->selectedMonth, null, $this->selectedYear);
            $this->syncInfo = $service->syncTargetWithDatabase(null, $this->selectedYear);
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal memuat data Capaian PNBP {$this->selectedYear}: " . $e->getMessage());
        }
    }

    public function setYear(int $year, PnbpTargetService $service): void
    {
        $this->selectedYear = (int) $year;
        $this->selectedMonth = 'REKAP';
        $this->search = '';
        $this->year = $this->selectedYear;
        $this->loadData($service);
    }

    public function setMonth(string $month, PnbpTargetService $service): void
    {
        $this->selectedMonth = strtoupper($month);
        try {
            $this->sheetDetails = $service->getSheetDetails($this->selectedMonth, null, $this->selectedYear);
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal memuat sheet ' . $month . ': ' . $e->getMessage());
        }
    }

    public function syncNow(PnbpTargetService $service): void
    {
        try {
            $service->clearCache(null, $this->selectedYear);
            $service->autoSyncActiveUsageMonths($this->selectedYear, true);
            $this->syncInfo = $service->syncTargetWithDatabase(null, $this->selectedYear, true);
            $this->loadData($service);
            session()->flash('success', "Berhasil menyelaraskan Target TA {$this->selectedYear} dan realisasi transaksi material dengan data capaian PNBP.");
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    // Target CRUD Methods
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->isEditMode = false;
        $this->targetId = null;
        $this->year = $this->selectedYear;
        $this->name = "Target Ditlantas {$this->year}";
        $this->description = "Target Renbut Materiel dan Capaian Realisasi PNBP Ditlantas & Polres Jajaran TA {$this->year}";
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(): void
    {
        $this->resetValidation();
        $this->isEditMode = true;

        $target = Target::where('year', $this->selectedYear)->first();
        if ($target) {
            $this->targetId = $target->id;
            $this->name = $target->name;
            $this->year = (int) $target->year;
            $this->description = $target->description;
            $this->is_active = (bool) $target->is_active;
        } else {
            $this->targetId = null;
            $this->year = $this->selectedYear;
            $this->name = "Target Ditlantas {$this->year}";
            $this->description = "Target Renbut Materiel dan Capaian Realisasi PNBP Ditlantas & Polres Jajaran TA {$this->year}";
            $this->is_active = true;
        }

        $this->showModal = true;
    }

    public function openDeleteModal(): void
    {
        $target = Target::where('year', $this->selectedYear)->first();
        if ($target) {
            $this->targetId = $target->id;
            $this->showDeleteModal = true;
        } else {
            session()->flash('error', "Target tahun {$this->selectedYear} belum tersimpan di database.");
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->isEditMode && $this->targetId) {
            $target = Target::findOrFail($this->targetId);
            $target->update([
                'name'        => $this->name,
                'year'        => $this->year,
                'description' => $this->description,
                'is_active'   => $this->is_active,
            ]);
            session()->flash('success', "Target '{$this->name}' berhasil diperbarui.");
        } else {
            $target = Target::create([
                'name'        => $this->name,
                'year'        => $this->year,
                'description' => $this->description,
                'is_active'   => $this->is_active,
            ]);
            session()->flash('success', "Target '{$this->name}' berhasil ditambahkan.");
        }

        $service = app(PnbpTargetService::class);
        $this->availableYears = $service->getAvailableYears();
        $this->selectedYear = $this->year;
        $this->loadData($service);
        $this->closeModal();
    }

    public function delete(): void
    {
        if (! $this->targetId) {
            return;
        }

        $target = Target::findOrFail($this->targetId);
        $name = $target->name;
        $year = $target->year;

        $target->targetDetails()->delete();
        TargetPnbpItem::where('target_id', $target->id)->delete();
        $target->delete();

        session()->flash('success', "Target '{$name}' (TA {$year}) berhasil dihapus.");
        $this->closeDeleteModal();

        $service = app(PnbpTargetService::class);
        $service->clearCache(null, $year);
        $this->availableYears = $service->getAvailableYears();
        $this->loadData($service);
    }

    // Item CRUD Methods
    public function openCreateItemModal(): void
    {
        $this->resetValidation();
        $this->isItemEditMode = false;
        $this->itemId = null;
        $this->itemNo = '';
        $this->itemName = '';
        $this->itemLevel = 2;
        $this->itemTarif = 0;
        $this->itemVolumeTarget = 0;
        $this->itemVolumeRealization = 0;
        $this->applyToAllMonths = false;
        $this->showItemModal = true;
    }

    public function openEditItemModal(string $id): void
    {
        $this->resetValidation();
        $this->isItemEditMode = true;
        $this->itemId = $id;

        $item = TargetPnbpItem::findOrFail($id);
        $this->itemNo = $item->no ?? '';
        $this->itemName = $item->name;
        $this->itemLevel = (int) $item->level;
        $this->itemTarif = (float) $item->tarif;
        $this->itemVolumeTarget = (float) $item->volume_target;
        $this->itemVolumeRealization = (float) $item->volume_realization;
        $this->applyToAllMonths = false;

        $this->showItemModal = true;
    }

    public function closeItemModal(): void
    {
        $this->showItemModal = false;
        $this->resetValidation();
    }

    public function openDeleteItemModal(string $id): void
    {
        $this->itemId = $id;
        $item = TargetPnbpItem::findOrFail($id);
        $this->itemName = $item->name;
        $this->showDeleteItemModal = true;
    }

    public function closeDeleteItemModal(): void
    {
        $this->showDeleteItemModal = false;
    }

    public function saveItem(): void
    {
        $this->validate([
            'itemName'              => 'required|string|max:255',
            'itemLevel'             => 'required|integer|in:1,2,3',
            'itemTarif'             => 'required|numeric|min:0',
            'itemVolumeTarget'      => 'required|numeric|min:0',
            'itemVolumeRealization' => 'required|numeric|min:0',
        ]);

        $service = app(PnbpTargetService::class);
        $payload = [
            'no'                 => $this->itemNo,
            'name'               => $this->itemName,
            'level'              => $this->itemLevel,
            'tarif'              => $this->itemTarif,
            'volume_target'      => $this->itemVolumeTarget,
            'volume_realization' => $this->itemVolumeRealization,
        ];

        try {
            if ($this->isItemEditMode && $this->itemId) {
                $service->updateItem($this->itemId, $payload);
                session()->flash('success', "Item '{$this->itemName}' berhasil diperbarui.");
            } else {
                $service->createItem($payload, $this->selectedYear, $this->selectedMonth, $this->applyToAllMonths);
                session()->flash('success', "Item '{$this->itemName}' berhasil ditambahkan ke Sheet {$this->selectedMonth}.");
            }

            $this->closeItemModal();
            $this->loadData($service);
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menyimpan item: ' . $e->getMessage());
        }
    }

    public function deleteItem(): void
    {
        if (! $this->itemId) {
            return;
        }

        $service = app(PnbpTargetService::class);
        try {
            $name = $this->itemName;
            $service->deleteItem($this->itemId);
            session()->flash('success', "Item '{$name}' berhasil dihapus.");
            $this->closeDeleteItemModal();
            $this->loadData($service);
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menghapus item: ' . $e->getMessage());
        }
    }

    public function render(): \Illuminate\View\View
    {
        $filteredItems = $this->sheetDetails['items'] ?? [];
        if ($this->search) {
            $search = strtolower($this->search);
            $filteredItems = array_filter($filteredItems, function ($item) use ($search) {
                return str_contains(strtolower($item['name'] ?? ''), $search) ||
                       str_contains(strtolower($item['no'] ?? ''), $search);
            });
        }

        return view('livewire.admin.master.target.capaian.admin-master-target-capaian-index', [
            'filteredItems'    => $filteredItems,
            'monthlySummaries' => $this->monthlySummaries,
            'sheetDetails'     => $this->sheetDetails,
            'syncInfo'         => $this->syncInfo,
            'selectedMonth'    => $this->selectedMonth,
            'selectedYear'     => $this->selectedYear,
            'availableYears'   => $this->availableYears,
            'hasExcelFile'     => $this->hasExcelFile,
        ])->layout('components.layouts.main.app');
    }
}
