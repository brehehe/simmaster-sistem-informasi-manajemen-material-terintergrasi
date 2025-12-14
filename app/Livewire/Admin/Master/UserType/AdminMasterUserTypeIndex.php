<?php

namespace App\Livewire\Admin\Master\UserType;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User\UserType;
use App\Models\Type\Type;

class AdminMasterUserTypeIndex extends Component
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
    public ?string $userTypeId = null;
    public string $name = '';
    public ?string $description = null;
    public array $types = [];
    public bool $is_active = true;

    // Dropdown Data
    public $allTypes = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        $this->loadDropdownData();
    }

    public function loadDropdownData()
    {
        $this->allTypes = Type::where('is_active', true)->orderBy('name')->get();
    }

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
        $this->userTypeId = $id;

        $userType = UserType::findOrFail($id);
        $this->name = $userType->name;
        $this->description = $userType->description;
        $this->types = $userType->types ?? [];
        $this->is_active = $userType->is_active;

        $this->showModal = true;
    }

    public function openDeleteModal($id)
    {
        $this->userTypeId = $id;
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
        $this->userTypeId = null;
        $this->name = '';
        $this->description = null;
        $this->types = [];
        $this->is_active = true;
        $this->resetValidation();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'types' => 'nullable|array',
            'types.*' => 'exists:types,id',
            'is_active' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'types.*.exists' => 'Salah satu type tidak valid.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEditMode) {
                $userType = UserType::findOrFail($this->userTypeId);
                $userType->name = $this->name;
                $userType->description = $this->description;
                $userType->types = $this->types;
                $userType->is_active = $this->is_active;
                $userType->save();

                session()->flash('success', 'Tipe User berhasil diperbarui.');
            } else {
                UserType::create([
                    'name' => $this->name,
                    'description' => $this->description,
                    'types' => $this->types,
                    'is_active' => $this->is_active,
                ]);

                session()->flash('success', 'Tipe User berhasil ditambahkan.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $userType = UserType::findOrFail($this->userTypeId);
            $userType->delete();

            session()->flash('success', 'Tipe User berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Helper method to get type names by IDs
    public function getTypeNames($typeIds)
    {
        if (empty($typeIds)) {
            return collect();
        }
        return Type::whereIn('id', $typeIds)->pluck('name');
    }

    public function render()
    {
        $userTypes = UserType::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.master.user-type.admin-master-user-type-index', [
            'userTypes' => $userTypes,
        ])->layout('components.layouts.main.app');
    }
}
