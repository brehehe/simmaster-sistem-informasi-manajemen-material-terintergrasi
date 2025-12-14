<?php

namespace App\Livewire\Admin\Master\Rack;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rack\Rack;
use Illuminate\Support\Facades\Auth;
use App\Models\Police\RegionalPolice;
use App\Models\Police\PoliceStation;

class AdminMasterRackIndex extends Component
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
    public ?string $rackId = null;
    public string $name = '';
    public ?string $description = null;
    public bool $is_active = true;
    public ?string $regionalPoliceId = null;
    public ?string $policeStationId = null;

    // Dropdown Data
    public $regionalPolices = [];
    public $policeStations = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount() {
        $this->loadDropdownData();
    }

    public function loadDropdownData() {
        $this->regionalPolices = RegionalPolice::orderBy('name')->get();
    }

    public function updatedRegionalPoliceId($value)
    {
        $this->policeStations = $value
            ? PoliceStation::where('regional_police_id', $value)->orderBy('name')->get()
            : [];
        $this->policeStationId = null;
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
        $this->rackId = $id;

        $rack = Rack::findOrFail($id);
        $this->name = $rack->name;
        $this->description = $rack->description;
        $this->is_active = $rack->is_active;
        $this->regionalPoliceId = $rack->regional_police_id;
        $this->updatedRegionalPoliceId($rack->regional_police_id);
        $this->policeStationId = $rack->police_station_id;

        $this->showModal = true;
    }

    public function openDeleteModal($id)
    {
        $this->rackId = $id;
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
        $this->rackId = null;
        $this->name = '';
        $this->description = null;
        $this->is_active = true;
        $this->regionalPoliceId = null;
        $this->policeStationId = null;
        $this->policeStations = [];
        $this->resetValidation();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'regionalPoliceId' => 'nullable|exists:regional_police,id',
            'policeStationId' => 'nullable|exists:police_stations,id',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'regionalPoliceId.exists' => 'Regional Police tidak ditemukan.',
            'policeStationId.exists' => 'Police Station tidak ditemukan.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEditMode) {
                dd($this->policeStationId);
                $rack = Rack::findOrFail($this->rackId);
                $rack->name = $this->name;
                $rack->description = $this->description;
                $rack->is_active = $this->is_active;
                $rack->regional_police_id = $this->regionalPoliceId;
                $rack->police_station_id = $this->policeStationId;
                $rack->save();

                session()->flash('success', 'Rak berhasil diperbarui.');
            } else {
                Rack::create([
                    'name' => $this->name,
                    'description' => $this->description,
                    'is_active' => $this->is_active,
                    'regional_police_id'=>$this->regionalPoliceId,
                    'police_station_id'=>$this->policeStationId,
                ]);

                session()->flash('success', 'Rak berhasil ditambahkan.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $rack = Rack::findOrFail($this->rackId);
            $rack->delete();

            session()->flash('success', 'Rak berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $racks = Rack::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'asc');

            $user = Auth::user();

            if ($user->regional_police_id != null) {
                $racks->where('regional_police_id', $user->regional_police_id);
            }

            if ($user->police_station_id != null) {
                $racks->where('police_station_id', $user->police_station_id);
            }

        return view('livewire.admin.master.rack.admin-master-rack-index', [
            'racks' => $racks->paginate($this->perPage),
        ])->layout('components.layouts.main.app');
    }
}
