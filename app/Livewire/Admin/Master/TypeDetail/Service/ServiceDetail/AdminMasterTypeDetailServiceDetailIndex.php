<?php

namespace App\Livewire\Admin\Master\TypeDetail\Service\ServiceDetail;

use App\Models\Service\Service;
use App\Models\Service\ServiceDetail;
use App\Models\Type\TypeDetail;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMasterTypeDetailServiceDetailIndex extends Component
{
    use WithPagination;

    public string $type_detail_id;
    public string $service_id;
    public string $search = '';
    public int $perPage = 5;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditMode = false;

    public ?string $serviceDetailId = null;
    public string $name = '';
    public string $price = '';
    public ?string $description = null;
    public bool $is_active = true;

    protected $queryString = ['search' => ['except' => ''], 'perPage' => ['except' => 10]];

    public function mount($type_detail_id, $service_id)
    {
        $this->type_detail_id = $type_detail_id;
        $this->service_id = $service_id;
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
        $this->serviceDetailId = $id;
        $serviceDetail = ServiceDetail::findOrFail($id);
        $this->name = $serviceDetail->name;
        $this->price = (float) $serviceDetail->price;
        $this->description = $serviceDetail->description;
        $this->is_active = $serviceDetail->is_active;
        $this->showModal = true;
    }

    public function openDeleteModal($id)
    {
        $this->serviceDetailId = $id;
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
        $this->serviceDetailId = null;
        $this->name = '';
        $this->price = '';
        $this->description = null;
        $this->is_active = true;
        $this->resetValidation();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'price.required' => 'Harga wajib diisi.',
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isEditMode) {
                $serviceDetail = ServiceDetail::findOrFail($this->serviceDetailId);
                $serviceDetail->update([
                    'name' => $this->name,
                    'price' => $this->price,
                    'description' => $this->description,
                    'is_active' => $this->is_active
                ]);
                session()->flash('success', 'Service Detail berhasil diperbarui.');
            } else {
                ServiceDetail::create([
                    'service_id' => $this->service_id,
                    'name' => $this->name,
                    'price' => $this->price,
                    'description' => $this->description,
                    'is_active' => $this->is_active
                ]);
                session()->flash('success', 'Service Detail berhasil ditambahkan.');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            ServiceDetail::findOrFail($this->serviceDetailId)->delete();
            session()->flash('success', 'Service Detail berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $typeDetail = TypeDetail::with('type')->findOrFail($this->type_detail_id);
        $service = Service::findOrFail($this->service_id);
        $serviceDetails = ServiceDetail::query()
            ->where('service_id', $this->service_id)
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%'))
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.master.type-detail.service.service-detail.admin-master-type-detail-service-detail-index', [
            'typeDetail' => $typeDetail,
            'service' => $service,
            'serviceDetails' => $serviceDetails
        ])->layout('components.layouts.main.app');
    }
}
