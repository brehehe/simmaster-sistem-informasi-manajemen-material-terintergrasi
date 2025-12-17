<?php

namespace App\Livewire\Admin\MenuPolda\MutationStock\Receive;

use App\Models\Models\MenuPolda\MutationStock\MutationStock;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPoldaMutationStockReceiveIndex extends Component
{
    use WithPagination;

    public string $searchCode = '';
    public string $statusFilter = 'received'; // Default: only show received
    public int $perPage = 10;

    public function searchByCode()
    {
        if (empty($this->searchCode)) {
            session()->flash('error', 'Silakan masukkan kode mutasi.');
            return;
        }

        $user = auth()->user();

        // Flexible search: receiver can be either Polda or Polres
        $mutation = MutationStock::where('code', $this->searchCode)
            ->where(function ($q) use ($user) {
                if ($user->hasRole('Polda') || $user->hasRole('Admin')) {
                    $q->where('receiver_regional_police_id', $user->regional_police_id);
                } elseif ($user->hasRole('Polres')) {
                    $q->where('receiver_police_station_id', $user->police_station_id);
                }
            })
            ->first();

        if (!$mutation) {
            session()->flash('error', 'Mutasi dengan kode "' . $this->searchCode . '" tidak ditemukan atau bukan untuk lokasi Anda.');
            return;
        }

        // Redirect to detail page
        return redirect()->route('menu-polda.mutation-stock.receive.detail', ['id' => $mutation->id]);
    }

    public function render()
    {
        $user = auth()->user();

        $query = MutationStock::with([
            'senderRegionalPolice',
            'senderPoliceStation',
            'receiverRegionalPolice',
            'receiverPoliceStation',
            'mutationStockDetails'
        ])->where('is_active', true);

        // Filter by receiver location
        if ($user->hasRole('Polda') || $user->hasRole('Admin')) {
            $query->where('receiver_regional_police_id', $user->regional_police_id);
        } elseif ($user->hasRole('Polres')) {
            $query->where('receiver_police_station_id', $user->police_station_id);
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $mutations = $query->latest('mutation_date')->paginate($this->perPage);

        return view('livewire.admin.menu-polda.mutation-stock.receive.admin-menu-polda-mutation-stock-receive-index', [
            'mutations' => $mutations,
        ])->layout('components.layouts.main.app');
    }
}
