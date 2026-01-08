<?php

namespace App\Livewire\Admin\Report\Reception;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail;
use App\Models\Police\PoliceStation;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminReportReceptionIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterPolres = '';
    #[Url]
    public $typeId = '';
    #[Url]
    public $typeDetailId = '';
    public $startDate = '';
    public $endDate = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterPolres' => ['except' => ''],
        'typeId' => ['except' => ''],
        'typeDetailId' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterPolres() { $this->resetPage(); }
    public function updatedTypeId() { $this->resetPage(); }
    public function updatedTypeDetailId() { $this->resetPage(); }

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    // Get receptions (details) from MaterialShipmentDetail
    public function getReceptionsProperty()
    {
        $query = MaterialShipmentDetail::query()
            ->select('material_shipment_details.*')
            ->join('material_shipments', 'material_shipment_details.material_shipment_id', '=', 'material_shipments.id')
            ->with([
                'materialShipment',
                'materialShipment.senderRegionalPolice',
                'materialShipment.receiverPoliceStation',
                'materialShipment.receivedByUser',
                'type',
                'typeDetail'
            ])
            ->where('material_shipments.is_active', true)
            ->where('material_shipments.status', 'received');

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('material_shipments.code', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('materialShipment.receiverPoliceStation', function ($polres) {
                        $polres->where('name', 'ilike', '%' . $this->search . '%');
                    })
                    ->orWhereHas('materialShipment.receivedByUser', function ($user) {
                        $user->where('name', 'ilike', '%' . $this->search . '%');
                    })
                    ->orWhereHas('type', function ($t) {
                        $t->where('name', 'ilike', '%' . $this->search . '%');
                    })
                    ->orWhereHas('typeDetail', function ($td) {
                        $td->where('name', 'ilike', '%' . $this->search . '%');
                    })
                    ->orWhere('material_shipment_details.number_serial_first', 'ilike', '%' . $this->search . '%');
            });
        }

        // Polres filter
        if ($this->filterPolres) {
            $query->where('material_shipments.receiver_police_station_id', $this->filterPolres);
        }

        // Type filter
        if ($this->typeId) {
            $query->where('material_shipment_details.type_id', $this->typeId);
        }

        // Type Detail filter
        if ($this->typeDetailId) {
            $query->where('material_shipment_details.type_detail_id', $this->typeDetailId);
        }

        // Date range filter
        if ($this->startDate) {
            $query->whereDate('material_shipments.received_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('material_shipments.received_at', '<=', $this->endDate);
        }

        return $query->orderBy('material_shipments.received_at', 'desc')
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
        // Distinct receptions
        return MaterialShipment::where('is_active', true)
            ->where('status', 'received')
            ->count();
    }

    public function getTotalUnitsProperty()
    {
         return MaterialShipmentDetail::whereHas('materialShipment', function($q) {
             $q->where('is_active', true)
               ->where('status', 'received');
         })->sum('quantity');
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

    public function render()
    {
        $allTypes = Type::orderBy('name')->get();

        $typeDetails = [];
        if ($this->typeId) {
            $typeDetails = TypeDetail::where('type_id', $this->typeId)->orderBy('name')->get();
        } else {
             $typeDetails = TypeDetail::orderBy('name')->get();
        }

        return view('livewire.admin.report.reception.admin-report-reception-index', [
            'receptions' => $this->receptions,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
        ])
            ->layout('components.layouts.main.app');
    }
}
