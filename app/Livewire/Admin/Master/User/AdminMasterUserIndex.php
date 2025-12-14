<?php

namespace App\Livewire\Admin\Master\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Police\RegionalPolice;
use App\Models\Police\PoliceStation;
use App\Models\User\UserType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminMasterUserIndex extends Component
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
    public ?string $userId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?string $regional_police_id = null;
    public ?string $police_station_id = null;
    public ?string $user_type_id = null;
    public ?string $role = null;

    // Dropdown Data
    public $regionalPolices = [];
    public $policeStations = [];
    public $userTypes = [];
    public $roles = [];

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
        $this->regionalPolices = RegionalPolice::orderBy('name')->get();
        $this->userTypes = UserType::orderBy('name')->get();
        $this->roles = \App\Models\Spatie\Role::orderBy('name')->get();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedRegionalPoliceId($value)
    {
        $this->policeStations = $value
            ? PoliceStation::where('regional_police_id', $value)->orderBy('name')->get()
            : [];
        $this->police_station_id = null;
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
        $this->userId = $id;

        $user = User::with('roles')->findOrFail($id);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->regional_police_id = $user->regional_police_id;
        $this->police_station_id = $user->police_station_id;
        $this->user_type_id = $user->user_type_id;
        $this->role = $user->roles->first()?->name;

        // Load police stations based on selected regional police
        if ($this->regional_police_id) {
            $this->policeStations = PoliceStation::where('regional_police_id', $this->regional_police_id)
                ->orderBy('name')
                ->get();
        }

        $this->showModal = true;
    }

    public function openDeleteModal($id)
    {
        $this->userId = $id;
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
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->regional_police_id = null;
        $this->police_station_id = null;
        $this->user_type_id = null;
        $this->role = null;
        $this->policeStations = [];
        $this->resetValidation();
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'regional_police_id' => 'nullable|exists:regional_police,id',
            'police_station_id' => 'nullable|exists:police_stations,id',
            'user_type_id' => 'nullable|exists:user_types,id',
            'role' => 'nullable|string|exists:roles,name',
        ];

        if (!$this->isEditMode) {
            $rules['password'] = 'required|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|min:8|confirmed';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEditMode) {
                $user = User::findOrFail($this->userId);
                $user->name = $this->name;
                $user->email = $this->email;
                $user->regional_police_id = $this->regional_police_id;
                $user->police_station_id = $this->police_station_id;
                $user->user_type_id = $this->user_type_id;

                if ($this->password) {
                    $user->password = Hash::make($this->password);
                }

                $user->save();

                // Update role
                $user->syncRoles($this->role ? [$this->role] : []);

                session()->flash('success', 'User berhasil diperbarui.');
            } else {
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'regional_police_id' => $this->regional_police_id,
                    'police_station_id' => $this->police_station_id,
                    'user_type_id' => $this->user_type_id,
                ]);

                // Assign role
                if ($this->role) {
                    $user->assignRole($this->role);
                }

                session()->flash('success', 'User berhasil ditambahkan.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $user = User::findOrFail($this->userId);
            $user->delete();

            session()->flash('success', 'User berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $users = User::query()
            ->with(['roles'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.master.user.admin-master-user-index', [
            'users' => $users,
        ])->layout('components.layouts.main.app');
    }
}
