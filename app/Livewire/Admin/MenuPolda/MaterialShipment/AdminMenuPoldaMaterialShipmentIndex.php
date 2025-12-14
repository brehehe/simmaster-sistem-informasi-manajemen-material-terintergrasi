<?php

namespace App\Livewire\Admin\MenuPolda\MaterialShipment;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Police\PoliceStation;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMenuPoldaMaterialShipmentIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public string $statusFilter = '';
    public string $polresFilter = '';
    public int $perPage = 10;
    public bool $showDeleteModal = false;
    public ?string $shipmentId = null;

    public function render()
    {
        $user = auth()->user();

        $query = MaterialShipment::with(['senderRegionalPolice', 'receiverPoliceStation', 'materialShipmentDetails'])
            ->where('is_active', true);

        // Role-based: Polda only sees their own shipments
        if ($user->hasRole('Polda')) {
            $query->where('sender_regional_police_id', $user->regional_police_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Polres filter
        if ($this->polresFilter) {
            $query->where('receiver_police_station_id', $this->polresFilter);
        }

        // Date filtering
        if ($this->startDate) {
            $query->whereDate('shipment_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('shipment_date', '<=', $this->endDate);
        }

        $shipments = $query->latest('shipment_date')->paginate($this->perPage);

        // Get police stations for filter
        $policeStations = [];
        if ($user->hasRole('Polda')) {
            $policeStations = PoliceStation::where('regional_police_id', $user->regional_police_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('livewire.admin.menu-polda.material-shipment.admin-menu-polda-material-shipment-index', [
            'shipments' => $shipments,
            'policeStations' => $policeStations,
        ])->layout('components.layouts.main.app');
    }

    public function openDeleteModal($id)
    {
        $this->shipmentId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->shipmentId = null;
    }

    public function delete()
    {
        if ($this->shipmentId) {
            $shipment = MaterialShipment::find($this->shipmentId);
            if ($shipment && $shipment->status === 'draft') {
                $shipment->delete();
                session()->flash('success', 'Pengiriman berhasil dihapus.');
            } else {
                session()->flash('error', 'Hanya pengiriman dengan status draft yang bisa dihapus.');
            }
        }
        $this->closeModal();
    }
}
