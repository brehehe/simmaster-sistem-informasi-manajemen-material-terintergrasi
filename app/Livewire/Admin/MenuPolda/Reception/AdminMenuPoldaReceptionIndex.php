<?php

namespace App\Livewire\Admin\MenuPolda\Reception;

use App\Models\Police\RegionalPolice;
use App\Models\Reception\Reception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPoldaReceptionIndex extends Component
{
    use WithPagination;

    // Search & Filter
    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $perPage = 10;

    // Delete Modal
    public bool $showDeleteModal = false;
    public ?string $receptionId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
        'perPage' => ['except' => 10],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function openDeleteModal($id)
    {
        $this->receptionId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->receptionId = null;
    }

    public function delete()
    {
        try {
            $reception = Reception::findOrFail($this->receptionId);

            // Delete all details first
            $reception->receptionDetails()->delete();

            // Then delete the main record
            $reception->delete();

            session()->flash('success', 'Data penerimaan barang berhasil dihapus.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();

        $receptions = Reception::query()
            ->with(['regionalPolice', 'policeStation'])
            ->withCount('receptionDetails')
            // Role-based filtering
            ->when($user->hasRole('Polda'), function ($query) use ($user) {
                $query->where('regional_police_id', $user->regional_police_id);
            })
            // Search
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            // Date filter
            ->when($this->startDate, function ($query) {
                $query->whereDate('date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('date', '<=', $this->endDate);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.menu-polda.reception.admin-menu-polda-reception-index', [
            'receptions' => $receptions,
        ])->layout('components.layouts.main.app');
    }
}
