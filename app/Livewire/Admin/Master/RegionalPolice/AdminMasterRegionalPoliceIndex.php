<?php

namespace App\Livewire\Admin\Master\RegionalPolice;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Police\RegionalPolice;

class AdminMasterRegionalPoliceIndex extends Component
{
    use WithPagination;

    // Search & Pagination
    public string $search = '';
    public int $perPage = 5;

    // Modal State
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditMode = false;

    // Form Data
    public ?string $regionalPoliceId = null;
    public string $name = '';
    public ?string $description = null;
    public bool $is_active = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->regionalPoliceId = $id;

        $regionalPolice = RegionalPolice::findOrFail($id);
        $this->name = $regionalPolice->name;
        $this->description = $regionalPolice->description;
        $this->is_active = $regionalPolice->is_active;

        $this->showModal = true;
    }

    public function openDeleteModal($id)
    {
        $this->regionalPoliceId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->regionalPoliceId = null;
        $this->name = '';
        $this->description = null;
        $this->is_active = true;
        $this->resetValidation();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEditMode) {
                $regionalPolice = RegionalPolice::findOrFail($this->regionalPoliceId);
                $regionalPolice->name = $this->name;
                $regionalPolice->description = $this->description;
                $regionalPolice->is_active = $this->is_active;
                $regionalPolice->save();

                session()->flash('success', 'Polda berhasil diperbarui.');
            } else {
                RegionalPolice::create([
                    'name' => $this->name,
                    'description' => $this->description,
                    'is_active' => $this->is_active,
                ]);

                session()->flash('success', 'Polda berhasil ditambahkan.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $regionalPolice = RegionalPolice::findOrFail($this->regionalPoliceId);
            $regionalPolice->delete();

            session()->flash('success', 'Polda berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $regionalPolices = RegionalPolice::query()
            ->withCount('policeStations')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.master.regional-police.admin-master-regional-police-index', [
            'regionalPolices' => $regionalPolices,
        ])->layout('components.layouts.main.app');
    }
}
