<?php

namespace App\Livewire\Admin\Master\PoliceStation;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;

class AdminMasterPoliceStationIndex extends Component
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
    public ?string $policeStationId = null;
    public ?string $regional_police_id = null;
    public string $name = '';
    public ?string $address = null;
    public ?string $description = null;
    public bool $is_active = true;

    // Dropdown Data
    public $regionalPolices = [];

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
        $this->regionalPolices = RegionalPolice::where('is_active', true)->orderBy('name')->get();
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
        $this->policeStationId = $id;

        $policeStation = PoliceStation::findOrFail($id);
        $this->regional_police_id = $policeStation->regional_police_id;
        $this->name = $policeStation->name;
        $this->address = $policeStation->address;
        $this->description = $policeStation->description;
        $this->is_active = $policeStation->is_active;

        $this->showModal = true;
    }

    public function openDeleteModal($id)
    {
        $this->policeStationId = $id;
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
        $this->policeStationId = null;
        $this->regional_police_id = null;
        $this->name = '';
        $this->address = null;
        $this->description = null;
        $this->is_active = true;
        $this->resetValidation();
    }

    protected function rules()
    {
        return [
            'regional_police_id' => 'required|exists:regional_police,id',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'regional_police_id.required' => 'Polda wajib dipilih.',
            'regional_police_id.exists' => 'Polda tidak valid.',
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEditMode) {
                $policeStation = PoliceStation::findOrFail($this->policeStationId);
                $policeStation->regional_police_id = $this->regional_police_id;
                $policeStation->name = $this->name;
                $policeStation->address = $this->address;
                $policeStation->description = $this->description;
                $policeStation->is_active = $this->is_active;
                $policeStation->save();

                session()->flash('success', 'Polres berhasil diperbarui.');
            } else {
                PoliceStation::create([
                    'regional_police_id' => $this->regional_police_id,
                    'name' => $this->name,
                    'address' => $this->address,
                    'description' => $this->description,
                    'is_active' => $this->is_active,
                ]);

                session()->flash('success', 'Polres berhasil ditambahkan.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $policeStation = PoliceStation::findOrFail($this->policeStationId);
            $policeStation->delete();

            session()->flash('success', 'Polres berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $policeStations = PoliceStation::query()
            ->with('regionalPolice')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('regionalPolice', function ($rq) {
                          $rq->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.master.police-station.admin-master-police-station-index', [
            'policeStations' => $policeStations,
        ])->layout('components.layouts.main.app');
    }
}
