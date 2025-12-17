<?php

namespace App\Livewire\Admin\MenuPolda\MutationStock\Receive;

use App\Models\Models\MenuPolda\MutationStock\MutationStock;
use Livewire\Component;

class AdminMenuPoldaMutationStockReceiveDetail extends Component
{
    public string $mutationId;
    public ?MutationStock $mutation = null;

    public function mount($id)
    {
        $this->mutationId = $id;
        $user = auth()->user();

        // Load mutation with details
        $query = MutationStock::with([
            'senderRegionalPolice',
            'senderPoliceStation',
            'receiverRegionalPolice',
            'receiverPoliceStation',
            'mutationStockDetails.type',
            'mutationStockDetails.typeDetail',
        ])->where('id', $id);

        // Verify user is the receiver
        if ($user->hasRole('Polda') || $user->hasRole('Admin')) {
            $query->where('receiver_regional_police_id', $user->regional_police_id);
        } elseif ($user->hasRole('Polres')) {
            $query->where('receiver_police_station_id', $user->police_station_id);
        }

        $this->mutation = $query->firstOrFail();
    }

    public function confirmReceipt()
    {
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

            return redirect()->route('menu-polda.mutation-stock.receive');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.menu-polda.mutation-stock.receive.admin-menu-polda-mutation-stock-receive-detail')
            ->layout('components.layouts.main.app');
    }
}
