<?php

namespace App\Livewire\Admin\Report\Delivery;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Police\PoliceStation;
use Livewire\Component;
use Livewire\WithPagination;

class AdminReportDeliveryIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterStatus = '';
    public $filterPolres = '';
    public $startDate = '';
    public $endDate = '';

    // Get deliveries from MaterialShipment table
    public function getDeliveriesProperty()
    {
        $query = MaterialShipment::with([
            'senderRegionalPolice',
            'receiverPoliceStation',
            'materialShipmentDetails.typeDetail',
            'receivedByUser'
        ])
            ->where('is_active', true)
            ->where('status', '!=', 'draft'); // Only show shipped/received shipments

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('courier_name', 'like', '%' . $this->search . '%')
                    ->orWhere('vehicle_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('receiverPoliceStation', function ($polres) {
                        $polres->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Polres filter
        if ($this->filterPolres) {
            $query->where('receiver_police_station_id', $this->filterPolres);
        }

        // Date range filter
        if ($this->startDate) {
            $query->whereDate('shipment_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('shipment_date', '<=', $this->endDate);
        }

        return $query->latest('shipment_date')
            ->paginate($this->perPage);
    }

    public function getStatusesProperty()
    {
        return [
            'shipped' => 'Dalam Perjalanan',
            'received' => 'Terkirim',
        ];
    }

    public function getPoliceStationsProperty()
    {
        return PoliceStation::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // Summary statistics
    public function getTotalShipmentsProperty()
    {
        return $this->deliveries->total();
    }

    public function getReceivedCountProperty()
    {
        return MaterialShipment::where('is_active', true)
            ->where('status', 'received')
            ->when($this->startDate, fn($q) => $q->whereDate('shipment_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('shipment_date', '<=', $this->endDate))
            ->count();
    }

    public function getInTransitCountProperty()
    {
        return MaterialShipment::where('is_active', true)
            ->where('status', 'shipped')
            ->when($this->startDate, fn($q) => $q->whereDate('shipment_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('shipment_date', '<=', $this->endDate))
            ->count();
    }

    public function getTotalUnitsProperty()
    {
        $shipmentIds = $this->deliveries->pluck('id');

        return \DB::table('material_shipment_details')
            ->whereIn('material_shipment_id', $shipmentIds)
            ->sum('quantity');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterPolres()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.report.delivery.admin-report-delivery-index', [
            'deliveries' => $this->deliveries,
        ])
            ->layout('components.layouts.main.app');
    }
}
