<?php

namespace App\Livewire\Admin\Master\Type;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Type\Type;

class AdminMasterTypeIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 5;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditMode = false;

    public ?string $typeId = null;
    public string $name = '';
    public float $price = 0;
    public ?string $description = null;
    public bool $is_active = true;
    public bool $is_with_serial_number = false;

    protected $queryString = ['search' => ['except' => ''], 'perPage' => ['except' => 10]];

    public function updatedSearch() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->is_active = true;
        $this->is_with_serial_number = false;
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->typeId = $id;
        $type = Type::findOrFail($id);
        $this->name = $type->name;
        $this->price = (float) $type->price;
        $this->description = $type->description;
        $this->is_active = $type->is_active;
        $this->is_with_serial_number = $type->is_with_serial_number;
        $this->showModal = true;
    }

    public function openService($id)
    {
        return $this->redirect(route('master.type.service', ['type_id' => $id]), navigate: true);
    }

    public function openDeleteModal($id)
    {
        $this->typeId = $id;
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
        $this->typeId = null;
        $this->name = '';
        $this->description = null;
        $this->price = 0;
        $this->is_active = true;
        $this->is_with_serial_number = false;
        $this->resetValidation();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_with_serial_number' => 'boolean',
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
                $type = Type::findOrFail($this->typeId);
                $type->update(['name' => $this->name, 'description' => $this->description, 'price' => $this->price, 'is_active' => $this->is_active, 'is_with_serial_number' => $this->is_with_serial_number]);
                session()->flash('success', 'Tipe berhasil diperbarui.');
            } else {
                Type::create(['name' => $this->name, 'description' => $this->description, 'price' => $this->price, 'is_active' => $this->is_active, 'is_with_serial_number' => $this->is_with_serial_number]);
                session()->flash('success', 'Tipe berhasil ditambahkan.');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            Type::findOrFail($this->typeId)->delete();
            session()->flash('success', 'Tipe berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $types = Type::query()
            ->withCount(['typeDetails', 'services'])
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%'))
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.master.type.admin-master-type-index', ['types' => $types])->layout('components.layouts.main.app');
    }
}
