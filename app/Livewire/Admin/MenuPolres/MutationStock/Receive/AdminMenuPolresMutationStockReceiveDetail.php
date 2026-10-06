<?php

namespace App\Livewire\Admin\MenuPolres\MutationStock\Receive;

use App\Livewire\Concerns\AuthorizesPolresData;
use App\Models\MenuPolda\MutationStock\MutationStock;
use Livewire\Component;

class AdminMenuPolresMutationStockReceiveDetail extends Component
{
    use AuthorizesPolresData;

    public string $mutationId;

    public ?MutationStock $mutation = null;

    public function boot(): void
    {
        $this->authorizePolresMenu();
    }

    public function mount($id)
    {
        $this->mutationId = $id;
        $user = auth()->user();

        // Load mutation with details
        $query = MutationStock::with([
            'senderRegionalPolice',
            'senderPoliceStation',
            'receiverPoliceStation',
            'mutationStockDetails.type',
            'mutationStockDetails.typeDetail',
        ])->where('id', $id);

        if (! $user->hasRole('Admin')) {
            $query->where('receiver_police_station_id', $user->police_station_id);
        }

        $this->mutation = $query->firstOrFail();
        $this->authorizePoliceStation($this->mutation->receiver_police_station_id);
    }

    public function confirmReceipt()
    {
        $this->authorizePoliceStation($this->mutation->receiver_police_station_id);
        if ($this->mutation->status !== 'sent') {
            session()->flash('error', 'Hanya mutasi dengan status "Terkirim" yang bisa dikonfirmasi.');

            return;
        }

        try {
            $user = auth()->user();

            // Call model method to mark as received
            // This will:
            // 1. Deduct stock from sender
            // 2. Create/update Stock at receiver
            // 3. Create StockDetail without rack
            // 4. Create history_stocks entries
            $this->mutation->markAsReceived($user);

            session()->flash('success', 'Mutasi stock berhasil diterima! Stock telah ditambahkan ke inventory Anda.');

            return $this->redirect(route('menu-polres.mutation-stock.receive'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Error: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.menu-polres.mutation-stock.receive.admin-menu-polres-mutation-stock-receive-detail')
            ->layout('components.layouts.main.app');
    }
}
