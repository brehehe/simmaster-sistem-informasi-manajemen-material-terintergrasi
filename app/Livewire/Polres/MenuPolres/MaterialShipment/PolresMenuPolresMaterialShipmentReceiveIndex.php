<?php

namespace App\Livewire\Polres\MenuPolres\MaterialShipment;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use Livewire\Component;
use Livewire\WithPagination;

class PolresMenuPolresMaterialShipmentReceiveIndex extends Component
{
    use WithPagination;

    public string $searchCode = '';
    public string $statusFilter = 'received'; // Default: only show shipped (pending receive)
    public int $perPage = 10;

    public function searchByCode()
    {
        if (empty($this->searchCode)) {
            session()->flash('error', 'Silakan masukkan kode pengiriman.');
            return;
        }

        $user = auth()->user();

        $shipment = MaterialShipment::where('code', $this->searchCode)
            ->where('receiver_police_station_id', $user->police_station_id)
            ->first();

        if (!$shipment) {
            session()->flash('error', 'Pengiriman dengan kode "' . $this->searchCode . '" tidak ditemukan atau bukan untuk Polres Anda.');
            return;
        }

        // Redirect to detail page
        return redirect()->route('menu-polres.material-shipment.receive.detail', ['id' => $shipment->id]);
    }

    public function render()
    {
        $user = auth()->user();

        $query = MaterialShipment::with(['senderRegionalPolice', 'receiverPoliceStation', 'materialShipmentDetails'])
            ->where('receiver_police_station_id', $user->police_station_id)
            ->where('is_active', true);

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $shipments = $query->latest('shipment_date')->paginate($this->perPage);

        return view('livewire.polres.menu-polres.material-shipment.polres-menu-polres-material-shipment-receive-index', [
            'shipments' => $shipments,
        ])->layout('components.layouts.main.app');
    }
}
