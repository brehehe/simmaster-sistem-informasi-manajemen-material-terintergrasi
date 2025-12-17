<?php

namespace App\Livewire\Admin\MenuPolres\MutationStock\Receive;

use App\Models\Models\MenuPolda\MutationStock\MutationStock;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPolresMutationStockReceiveIndex extends Component
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

        // Search mutation where current police station is the receiver
        $mutation = MutationStock::where('code', $this->searchCode)
            ->where('receiver_police_station_id', $user->police_station_id)
            ->first();

        if (!$mutation) {
            session()->flash('error', 'Mutasi dengan kode "' . $this->searchCode . '" tidak ditemukan atau bukan untuk Polres Anda.');
            return;
        }

        // Redirect to detail page
        return redirect()->route('menu-polres.mutation-stock.receive.detail', ['id' => $mutation->id]);
    }

    public function render()
    {
        $user = auth()->user();

        $query = MutationStock::with([
            'senderRegionalPolice',
            'senderPoliceStation',
            'receiverPoliceStation',
            'mutationStockDetails'
        ])->where('is_active', true);

        // Filter by receiver (current police station)
        $query->where('receiver_police_station_id', $user->police_station_id);

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $mutations = $query->latest('mutation_date')->paginate($this->perPage);

        return view('livewire.admin.menu-polres.mutation-stock.receive.admin-menu-polres-mutation-stock-receive-index', [
            'mutations' => $mutations,
        ])->layout('components.layouts.main.app');
    }
}
