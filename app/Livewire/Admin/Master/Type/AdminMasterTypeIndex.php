<?php

namespace App\Livewire\Admin\Master\Type;

use App\Models\Type\Type;
use App\Models\User\UserType;
use Livewire\Component;
use Livewire\WithPagination;

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

    public string $unit = 'Unit';

    public float $price = 0;

    public ?string $description = null;

    public bool $is_active = true;

    public bool $is_with_serial_number = false;

    protected $queryString = ['search' => ['except' => ''], 'perPage' => ['except' => 10]];

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
        $this->unit = $type->unit ?? 'Unit';
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
        $this->unit = 'Unit';
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
            'unit' => 'required|string|max:30',
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
                $type->update(['unit' => $this->unit, 'name' => $this->name, 'description' => $this->description, 'price' => $this->price, 'is_active' => $this->is_active, 'is_with_serial_number' => $this->is_with_serial_number]);
                session()->flash('success', 'Tipe berhasil diperbarui.');
            } else {
                $type = Type::create(['unit' => $this->unit, 'name' => $this->name, 'description' => $this->description, 'price' => $this->price, 'is_active' => $this->is_active, 'is_with_serial_number' => $this->is_with_serial_number]);

                // Otomatis sinkronkan tipe baru ke user types umum (BAMAT dan SIE FASMAT)
                foreach (['BAMAT', 'SIE FASMAT'] as $utName) {
                    $ut = UserType::where('name', $utName)->first();
                    if ($ut) {
                        $current = $ut->types ?? [];
                        if (!in_array($type->id, $current)) {
                            $current[] = $type->id;
                            $ut->update(['types' => $current]);
                        }
                    }
                }

                // Sinkronkan ke seksi / baur terkait berdasarkan kategori nama
                $upperName = strtoupper($type->name);
                $targetUserTypes = [];
                if (str_contains($upperName, 'TNKB') || str_contains($upperName, 'NRKB') || str_contains($upperName, 'TCKB')) {
                    $targetUserTypes = array_merge($targetUserTypes, ['BAURTNKB', 'SIE TNKB']);
                }
                if (str_contains($upperName, 'BPKB') || str_contains($upperName, 'MUTASI')) {
                    $targetUserTypes = array_merge($targetUserTypes, ['BAURBPKB', 'SIE BPKB']);
                }
                if (str_contains($upperName, 'STNK')) {
                    $targetUserTypes = array_merge($targetUserTypes, ['BAURSTNK', 'SIE STNK']);
                }
                if (str_contains($upperName, 'SIM')) {
                    $targetUserTypes = array_merge($targetUserTypes, ['BAURSIMCARD', 'SIE SIM']);
                }
                if (str_contains($upperName, 'STCK')) {
                    $targetUserTypes = array_merge($targetUserTypes, ['BAURSTCK']);
                }
                foreach (array_unique($targetUserTypes) as $utName) {
                    $ut = UserType::where('name', $utName)->first();
                    if ($ut) {
                        $current = $ut->types ?? [];
                        if (!in_array($type->id, $current)) {
                            $current[] = $type->id;
                            $ut->update(['types' => $current]);
                        }
                    }
                }

                session()->flash('success', 'Tipe berhasil ditambahkan dan disinkronkan ke hak akses pengguna.');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $type = Type::findOrFail($this->typeId);
            UserType::all()->each(function ($ut) use ($type) {
                if ($ut->types && in_array($type->id, $ut->types)) {
                    $ut->update(['types' => array_values(array_diff($ut->types, [$type->id]))]);
                }
            });
            $type->delete();
            session()->flash('success', 'Tipe berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function render()
    {
        $types = Type::query()
            ->withCount(['typeDetails', 'services'])
            ->when($this->search, fn ($q) => $q->where('name', 'ilike', '%'.$this->search.'%')->orWhere('description', 'ilike', '%'.$this->search.'%'))
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.master.type.admin-master-type-index', ['types' => $types])->layout('components.layouts.main.app');
    }
}
