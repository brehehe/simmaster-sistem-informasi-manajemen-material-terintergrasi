<?php

namespace App\Livewire\Polres\MenuPolres\MaterialShipment;

use App\Livewire\Concerns\AuthorizesPolresData;
use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Police\PoliceStation;
use Livewire\Component;
use Livewire\WithPagination;

class PolresMenuPolresMaterialShipmentReceiveIndex extends Component
{
    use AuthorizesPolresData;
    use WithPagination;

    public string $searchCode = '';

    public string $statusFilter = '';

    public string $policeStationId = '';

    public int $perPage = 10;

    public function boot(): void
    {
        $this->authorizePolresMenu();
    }

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

        $query = MaterialShipment::where('code', 'ilike', '%'.trim($this->searchCode).'%');
        if ($user->hasRole('Admin')) {
            if ($this->policeStationId) {
                $query->where('receiver_police_station_id', $this->policeStationId);
            }
        } else {
            $query->where('receiver_police_station_id', $user->police_station_id);
        }
        $shipment = $query->first();

        if (! $shipment) {
            session()->flash('error', 'Pengiriman dengan nomor SPPM "'.$this->searchCode.'" tidak ditemukan.');

            return;
        }

        return $this->redirect(route('menu-polres.material-shipment.receive.detail', ['id' => $shipment->id]), navigate: true);
    }

    public function render()
    {
        $user = auth()->user();

        $query = MaterialShipment::query()
            ->with(['senderRegionalPolice', 'receiverPoliceStation', 'materialShipmentDetails'])
            ->where('is_active', true);

        if ($user->hasRole('Admin')) {
            if ($this->policeStationId) {
                $query->where('receiver_police_station_id', $this->policeStationId);
            }
        } else {
            $query->where('receiver_police_station_id', $user->police_station_id);
        }

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
            'policeStations' => $user->hasRole('Admin')
                ? PoliceStation::where('is_active', true)->orderBy('name')->get()
                : collect(),
        ])->layout('components.layouts.main.app');
    }
}
