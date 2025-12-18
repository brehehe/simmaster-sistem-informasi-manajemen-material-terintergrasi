<?php

namespace App\Livewire\Admin\Report\Reception;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Police\PoliceStation;
use Livewire\Component;
use Livewire\WithPagination;

class AdminReportReceptionIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterPolres = '';
    public$startDate = '';
    public $endDate = '';

public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }   

    // Get receptions from MaterialShipment table (received status only)
    public function getReceptionsProperty()
    {
        $query = MaterialShipment::with([
            'senderRegionalPolice',
            'receiverPoliceStation',
            'materialShipmentDetails.typeDetail',
            'receivedByUser'
        ])
            ->where('is_active', true)
            ->where('status', 'received'); // Only received shipments

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('receiverPoliceStation', function ($polres) {
                        $polres->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('receivedByUser', function ($user) {
                        $user->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Polres filter
        if ($this->filterPolres) {
            $query->where('receiver_police_station_id', $this->filterPolres);
        }

        // Date range filter
        if ($this->startDate) {
            $query->whereDate('received_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('received_at', '<=', $this->endDate);
        }

        return $query->latest('received_at')
            ->paginate($this->perPage);
    }

    public function getPoliceStationsProperty()
    {
        return PoliceStation::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // Summary statistics
    public function getTotalReceptionsProperty()
    {
        return $this->receptions->total();
    }

    public function getTotalUnitsProperty()
    {
        $receptionIds = $this->receptions->pluck('id');

        return \DB::table('material_shipment_details')
            ->whereIn('material_shipment_id', $receptionIds)
            ->sum('quantity');
    }

    public function getTodayReceptionsProperty()
    {
        return MaterialShipment::where('is_active', true)
            ->where('status', 'received')
            ->whereDate('received_at', today())
            ->count();
    }

    public function getThisMonthReceptionsProperty()
    {
        return MaterialShipment::where('is_active', true)
            ->where('status', 'received')
            ->whereMonth('received_at', now()->month)
            ->whereYear('received_at', now()->year)
            ->count();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterPolres()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.report.reception.admin-report-reception-index', [
            'receptions' => $this->receptions,
        ])
            ->layout('components.layouts.main.app');
    }
}
