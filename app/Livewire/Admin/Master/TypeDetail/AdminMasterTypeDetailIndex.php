<?php

namespace App\Livewire\Admin\Master\TypeDetail;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Type\TypeDetail;
use App\Models\Type\Type;

class AdminMasterTypeDetailIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 5;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditMode = false;

    public ?string $typeDetailId = null;
    public ?string $type_id = null;
    public string $name = '';
    public ?string $description = null;
    public bool $is_active = true;

    public $types = [];

    protected $queryString = ['search' => ['except' => ''], 'perPage' => ['except' => 10]];

    public function mount() { $this->types = Type::where('is_active', true)->orderBy('name')->get(); }
    public function updatedSearch() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

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
        $this->typeDetailId = $id;
        $detail = TypeDetail::findOrFail($id);
        $this->type_id = $detail->type_id;
        $this->name = $detail->name;
        $this->description = $detail->description;
        $this->is_active = $detail->is_active;
        $this->showModal = true;
    }

    public function openDeleteModal($id)
    {
        $this->typeDetailId = $id;
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
        $this->typeDetailId = null;
        $this->type_id = null;
        $this->name = '';
        $this->description = null;
        $this->is_active = true;
        $this->resetValidation();
    }

    protected function rules()
    {
        return [
            'type_id' => 'required|exists:types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'type_id.required' => 'Tipe wajib dipilih.',
            'name.required' => 'Nama wajib diisi.',
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isEditMode) {
                TypeDetail::findOrFail($this->typeDetailId)->update([
                    'type_id' => $this->type_id, 'name' => $this->name, 'description' => $this->description, 'is_active' => $this->is_active
                ]);
                session()->flash('success', 'Detail Tipe berhasil diperbarui.');
            } else {
                TypeDetail::create([
                    'type_id' => $this->type_id, 'name' => $this->name, 'description' => $this->description, 'is_active' => $this->is_active
                ]);
                session()->flash('success', 'Detail Tipe berhasil ditambahkan.');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            TypeDetail::findOrFail($this->typeDetailId)->delete();
            session()->flash('success', 'Detail Tipe berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $typeDetails = TypeDetail::query()
            ->with('type')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhereHas('type', fn($tq) => $tq->where('name', 'like', '%'.$this->search.'%')))
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.master.type-detail.admin-master-type-detail-index', ['typeDetails' => $typeDetails])->layout('components.layouts.main.app');
    }
}
