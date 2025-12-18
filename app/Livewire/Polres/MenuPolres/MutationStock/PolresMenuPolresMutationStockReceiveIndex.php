<?php

namespace App\Livewire\Polres\MenuPolres\MutationStock;

use App\Models\Models\MenuPolda\MutationStock\MutationStock;
use Livewire\Component;
use Livewire\WithPagination;

class PolresMenuPolresMutationStockReceiveIndex extends Component
{
    use WithPagination;

    public string $searchCode = '';
    public string $statusFilter = 'received'; // Default: only show sent (pending receive)
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
                if ($user->hasRole('Polda')) {
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
        return $this->redirect(route('menu-polres.mutation-stock.receive.detail', ['id' => $mutation->id]), navigate: true);
    }

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
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
        if ($user->hasRole('Polda')) {
            $query->where('receiver_regional_police_id', $user->regional_police_id);
        } elseif ($user->hasRole('Polres')) {
            $query->where('receiver_police_station_id', $user->police_station_id);
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $mutations = $query->latest('mutation_date')->paginate($this->perPage);

        return view('livewire.polres.menu-polres.mutation-stock.polres-menu-polres-mutation-stock-receive-index', [
            'mutations' => $mutations,
        ])->layout('components.layouts.main.app');
    }
}
