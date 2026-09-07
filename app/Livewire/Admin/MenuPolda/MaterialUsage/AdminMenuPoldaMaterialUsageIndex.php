<?php

namespace App\Livewire\Admin\MenuPolda\MaterialUsage;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPoldaMaterialUsageIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $perPage = 10;
    public bool $showDeleteModal = false;
    public ?string $materialUsageId = null;

    public function render()
    {
        $query = MaterialUsage::with(['materialUsageDetails', 'regionalPolice'])
            ->where('is_active', true);

        // Role-based filtering
        $user = auth()->user();
        if ($user->hasRole('Polda')) {
            $query->where('regional_police_id', $user->regional_police_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Date filtering
        if ($this->startDate) {
            $query->whereDate('date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        $materialUsages = $query->latest('date')->paginate($this->perPage);

        return view('livewire.admin.menu-polda.material-usage.admin-menu-polda-material-usage-index', [
            'materialUsages' => $materialUsages
        ])->layout('components.layouts.main.app');
    }

    public function openDeleteModal($id)
    {
        $this->materialUsageId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->materialUsageId = null;
    }

    public function delete(StockService $stockService)
    {
        if ($this->materialUsageId) {
            try {
                DB::beginTransaction();

                $materialUsage = MaterialUsage::with('materialUsageDetails')->find($this->materialUsageId);
                if ($materialUsage) {
                    // Restore stock & delete history
                    $stockService->deleteMaterialUsage($materialUsage);

                    foreach ($materialUsage->materialUsageDetails as $detail) {
                        $detail->materialUsageDetailItems()->delete();
                    }
                    $materialUsage->materialUsageDetails()->delete();
                    $materialUsage->delete();

                    DB::commit();
                    session()->flash('success', 'Data material usage berhasil dihapus.');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }
        $this->closeModal();
    }
}
