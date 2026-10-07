<?php

namespace App\Livewire\Polres\MenuPolres\MaterialShipment;

use App\Livewire\Concerns\AuthorizesPolresData;
use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use Livewire\Component;

class PolresMenuPolresMaterialShipmentReceiveDetail extends Component
{
    use AuthorizesPolresData;

    public string $shipmentId;

    public ?MaterialShipment $shipment = null;

    public function boot(): void
    {
        $this->authorizePolresMenu();
    }

    public function mount($id)
    {
        $this->shipmentId = $id;
        $user = auth()->user();

        // Load shipment with details
        $query = MaterialShipment::with([
            'senderRegionalPolice',
            'receiverPoliceStation',
            'materialShipmentDetails.type',
            'materialShipmentDetails.typeDetail',
            'materialShipmentDetails.rack',
            'materialShipmentDetails.stockDetail.service',
            'materialShipmentDetails.stockDetail.serviceDetail',
        ])->where('id', $id);

        if (! $user->hasRole('Admin')) {
            $query->where('receiver_police_station_id', $user->police_station_id);
        }

        $this->shipment = $query->firstOrFail();
        $this->authorizePoliceStation($this->shipment->receiver_police_station_id);
    }

    public function confirmReceipt()
    {
        $this->authorizePoliceStation($this->shipment->receiver_police_station_id);
        if ($this->shipment->status !== 'shipped') {
            session()->flash('error', 'Hanya pengiriman dengan status "Terkirim" yang bisa dikonfirmasi.');

            return;
        }

        try {
            $user = auth()->user();

            // Call model method to mark as received
            // This will:
            // 1. Create/update Stock at Polres
            // 2. Create StockDetail without rack
            // 3. Create history_stocks entries
            $this->shipment->markAsReceived($user);

            session()->flash('success', 'Pengiriman berhasil diterima! Stock telah ditambahkan ke inventory Polres Anda.');

            return $this->redirect(route('menu-polres.material-shipment.receive'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Error: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.polres.menu-polres.material-shipment.polres-menu-polres-material-shipment-receive-detail')
            ->layout('components.layouts.main.app');
    }
}
