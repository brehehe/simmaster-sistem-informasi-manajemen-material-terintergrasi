<?php

namespace App\Livewire\Polres\MenuPolres\MaterialShipment;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use Livewire\Component;
use Livewire\WithPagination;

class PolresMenuPolresMaterialShipmentReceiveIndex extends Component
{
    use WithPagination;

    public string $searchCode = '';
    public string $statusFilter = '';
    public int $perPage = 10;

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    public function searchByCode()
    {
        if (empty($this->searchCode)) {
            session()->flash('error', 'Silakan masukkan nomor SPPM.');
            return;
        }

        $user = auth()->user();

        $shipment = MaterialShipment::where('code', 'ilike', '%'.trim($this->searchCode).'%')
            ->where('receiver_police_station_id', $user->police_station_id)
            ->first();

        if (!$shipment) {
            session()->flash('error', 'Pengiriman dengan nomor SPPM "' . $this->searchCode . '" tidak ditemukan.');
            return;
        }

        return $this->redirect(route('menu-polres.material-shipment.receive.detail', ['id' => $shipment->id]), navigate: true);
    }

    public function render()
    {
        $user = auth()->user();

        $query = MaterialShipment::query()
            ->with(['senderRegionalPolice', 'receiverPoliceStation', 'materialShipmentDetails'])
            ->where('receiver_police_station_id', $user->police_station_id)
            ->where('is_active', true);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->searchCode) {
            $query->where(function ($q) {
                $q->where('code', 'ilike', '%'.trim($this->searchCode).'%')
                    ->orWhere('notes', 'ilike', '%'.trim($this->searchCode).'%');
            });
        }

        $materialShipments = $query->orderBy('shipment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.polres.menu-polres.material-shipment.polres-menu-polres-material-shipment-receive-index', [
            'materialShipments' => $materialShipments,
        ])->layout('components.layouts.main.app');
    }
}
