<?php

namespace App\Livewire\Admin\Master\Target;

use App\Models\Target\Target;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMasterTargetIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public int $perPage = 10;

    #[Url]
    public ?string $selectedYear = null;

    public array $availableYears = [];

    // Modal States
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditMode = false;

    // Form Fields
    public ?string $targetId = null;
    public string $name = '';
    public int $year;
    public ?string $description = null;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'year'        => 'required|integer|min:2000|max:2100',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'boolean',
        ];
    }

    public function mount(): void
    {
        $this->loadYears();

        // Default filter ke tahun sekarang jika belum diset
        if ($this->selectedYear === null) {
            $this->selectedYear = (string) now()->format('Y');
        }

        $this->year = (int) ($this->selectedYear ?: now()->format('Y'));
    }

    public function loadYears(): void
    {
        $dbYears = Target::distinct()->pluck('year')->filter()->map(fn($y) => (int)$y)->toArray();
        $currentYear = (int) now()->format('Y');
        $defaults = [$currentYear - 2, $currentYear - 1, $currentYear, $currentYear + 1, $currentYear + 2];
        $all = array_unique(array_merge($dbYears, $defaults));
        rsort($all);
        $this->availableYears = array_values($all);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedYear(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->isEditMode = false;
        $this->targetId = null;
        $this->year = (int) ($this->selectedYear ?: now()->format('Y'));
        $this->name = "Target Ditlantas {$this->year}";
        $this->description = "Target Renbut Materiel dan Capaian Realisasi PNBP Ditlantas & Polres Jajaran TA {$this->year}";
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $this->resetValidation();
        $this->isEditMode = true;
        $this->targetId = $id;

        $target = Target::findOrFail($id);
        $this->name = $target->name;
        $this->year = (int) $target->year;
        $this->description = $target->description;
        $this->is_active = (bool) $target->is_active;

        $this->showModal = true;
    }

    public function openDeleteModal(string $id): void
    {
        $this->targetId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->targetId = null;
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

        $this->loadYears();
        $this->closeModal();
    }

    public function delete(): void
    {
        if (! $this->targetId) {
            return;
        }

        $target = Target::findOrFail($this->targetId);
        $name = $target->name;

        // Cascade delete child details if exists
        $target->targetDetails()->delete();
        $target->delete();

        session()->flash('success', "Target '{$name}' berhasil dihapus.");
        $this->closeDeleteModal();
        $this->loadYears();
    }

    public function render(): \Illuminate\View\View
    {
        $query = Target::query();

        if ($this->selectedYear && $this->selectedYear !== 'all') {
            $query->where('year', $this->selectedYear);
        }

        if ($this->search) {
            $search = strtolower($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhere('year', 'ilike', "%{$search}%");
            });
        }

        $targets = $query->orderBy('year', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.master.target.admin-master-target-index', [
            'targets'        => $targets,
            'availableYears' => $this->availableYears,
        ])->layout('components.layouts.main.app');
    }
}
