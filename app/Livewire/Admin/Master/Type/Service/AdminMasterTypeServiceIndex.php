<?php

namespace App\Livewire\Admin\Master\Type\Service;

use App\Models\Service\Service;
use App\Models\Type\Type;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMasterTypeServiceIndex extends Component
{
    use WithPagination;

    public string $type_id;
    public string $search = '';
    public int $perPage = 5;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditMode = false;

    public ?string $serviceId = null;
    public string $name = '';
    public ?string $description = null;
    public bool $is_active = true;

    protected $queryString = ['search' => ['except' => ''], 'perPage' => ['except' => 10]];

    public function mount($type_id)
    {
        $this->type_id = $type_id;
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->is_active = true;
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->serviceId = $id;
        $service = Service::findOrFail($id);
        $this->name = $service->name;
        $this->description = $service->description;
        $this->is_active = $service->is_active;
        $this->showModal = true;
    }

    public function openDeleteModal($id)
    {
        $this->serviceId = $id;
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
        $this->serviceId = null;
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
                $service = Service::findOrFail($this->serviceId);
                $service->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'is_active' => $this->is_active
                ]);
                session()->flash('success', 'Service berhasil diperbarui.');
            } else {
                Service::create([
                    'type_id' => $this->type_id,
                    'name' => $this->name,
                    'description' => $this->description,
                    'is_active' => $this->is_active
                ]);
                session()->flash('success', 'Service berhasil ditambahkan.');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            Service::findOrFail($this->serviceId)->delete();
            session()->flash('success', 'Service berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $type = Type::findOrFail($this->type_id);
        $services = Service::query()
            ->where('type_id', $this->type_id)
            ->withCount('details')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%'))
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.master.type.service.admin-master-type-service-index', [
            'type' => $type,
            'services' => $services
        ])->layout('components.layouts.main.app');
    }
}
