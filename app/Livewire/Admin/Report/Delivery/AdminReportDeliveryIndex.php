<?php

namespace App\Livewire\Admin\Report\Delivery;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminReportDeliveryIndex extends Component
{
    use WithPagination;

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    public $search = '';

    public $perPage = 10;

    public $filterStatus = '';

    public $filterPolda = '';

    public $filterPolres = '';

    public $startDate = '';

    public $endDate = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterPolda' => ['except' => ''],
        'filterPolres' => ['except' => ''],
        'typeId' => ['except' => ''],
        'typeDetailId' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterPolda()
    {
        $this->resetPage();
    }

    public function updatedFilterPolres()
    {
        $this->resetPage();
    }

    public function updatedTypeId()
    {
        $this->resetPage();
    }

    public function updatedTypeDetailId()
    {
        $this->resetPage();
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

    // Get deliveries (details)
    public function getDeliveriesProperty()
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
                'typeDetail',
            ])
            ->where('material_shipments.is_active', true)
            ->where('material_shipments.status', '!=', 'draft');

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('material_shipments.code', 'ilike', '%'.$this->search.'%')
                    ->orWhere('material_shipments.courier_name', 'ilike', '%'.$this->search.'%')
                    ->orWhere('material_shipments.vehicle_number', 'ilike', '%'.$this->search.'%')
                    ->orWhereHas('materialShipment.senderRegionalPolice', function ($polres) {
                        $polres->where('name', 'ilike', '%'.$this->search.'%');
                    })
                    ->orWhereHas('materialShipment.receiverPoliceStation', function ($polres) {
                        $polres->where('name', 'ilike', '%'.$this->search.'%');
                    })
                    ->orWhereHas('type', function ($t) {
                        $t->where('name', 'ilike', '%'.$this->search.'%');
                    })
                    ->orWhereHas('typeDetail', function ($td) {
                        $td->where('name', 'ilike', '%'.$this->search.'%');
                    })
                    ->orWhere('material_shipment_details.number_serial_first', 'ilike', '%'.$this->search.'%');
            });
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where('material_shipments.status', $this->filterStatus);
        }

        if ($this->filterPolda) {
            $query->where('material_shipments.sender_regional_police_id', $this->filterPolda);
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
            $query->whereDate('material_shipments.shipment_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('material_shipments.shipment_date', '<=', $this->endDate);
        }

        return $query->orderBy('material_shipments.shipment_date', 'desc')
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

    public function getRegionalPolicesProperty()
    {
        return RegionalPolice::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // Summary statistics (Adjusted for details view if needed, but keeping aggregate logic for cards)
    public function getTotalShipmentsProperty()
    {
        // Count distinct shipments
        return MaterialShipment::where('is_active', true)
            ->where('status', '!=', 'draft')
            ->count();
    }

    public function getReceivedCountProperty()
    {
        return MaterialShipment::where('is_active', true)
            ->where('status', 'received')
            ->when($this->startDate, fn ($q) => $q->whereDate('shipment_date', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('shipment_date', '<=', $this->endDate))
            ->count();
    }

    public function getInTransitCountProperty()
    {
        return MaterialShipment::where('is_active', true)
            ->where('status', 'shipped')
            ->when($this->startDate, fn ($q) => $q->whereDate('shipment_date', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('shipment_date', '<=', $this->endDate))
            ->count();
    }

    public function getTotalUnitsProperty()
    {
        return MaterialShipmentDetail::whereHas('materialShipment', function ($q) {
            $q->where('is_active', true)
                ->where('status', '!=', 'draft');
        })->sum('quantity');
    }

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    public function exportExcel()
    {
        $filters = [
            'search' => $this->search,
            'filterStatus' => $this->filterStatus,
            'filterPolda' => $this->filterPolda,
            'filterPolres' => $this->filterPolres,
            'typeId' => $this->typeId,
            'typeDetailId' => $this->typeDetailId,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

        $fileName = 'Laporan_Pengiriman_'.now()->format('YmdHis').'.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DeliveryExport($filters),
            $fileName
        );
    }

    private function sanitizeRecursive($data)
    {
        if (is_string($data)) {
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        } elseif (is_array($data)) {
            $ret = [];
            foreach ($data as $i => $d) {
                $ret[$i] = $this->sanitizeRecursive($d);
            }

            return $ret;
        } elseif (is_object($data)) {
            if ($data instanceof \Illuminate\Database\Eloquent\Model) {
                $arr = $data->toArray();
                $cleanArr = $this->sanitizeRecursive($arr);

                return (object) $cleanArr;
            } elseif ($data instanceof \Illuminate\Support\Collection) {
                return $data->map(function ($item) {
                    return $this->sanitizeRecursive($item);
                });
            } else {
                $newData = new \stdClass;
                foreach ($data as $key => $value) {
                    $newData->$key = $this->sanitizeRecursive($value);
                }

                return $newData;
            }
        }

        return $data;
    }

    public function exportPdf()
    {
        $filters = [
            'search' => $this->search,
            'filterStatus' => $this->filterStatus,
            'filterPolda' => $this->filterPolda,
            'filterPolres' => $this->filterPolres,
            'typeId' => $this->typeId,
            'typeDetailId' => $this->typeDetailId,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

        $deliveries = $this->deliveries->items();

        // Deep sanitize
        $jsonObj = json_decode(json_encode($deliveries, JSON_INVALID_UTF8_SUBSTITUTE));
        $deliveries = $this->sanitizeRecursive($jsonObj);

        // Map data for JS
        $dataForPdf = [];
        $statusLabels = [
            'pending' => 'Menunggu',
            'shipped' => 'Dalam Perjalanan',
            'received' => 'Terkirim',
            'rejected' => 'Ditolak',
        ];

        foreach ($deliveries as $index => $delivery) {
            $serialNumber = trim(implode('', array_filter([
                $delivery->code ?? '',
                $delivery->number_serial_first ?? '',
                $delivery->number_serial_second ?? '',
            ]))) ?: '-';

            $dateVal = $delivery->material_shipment->shipment_date ?? null;
            $shipmentDate = '-';
            if ($dateVal) {
                try {
                    $shipmentDate = \Carbon\Carbon::parse($dateVal)->format('d M Y');
                } catch (\Exception $e) {
                    $shipmentDate = $dateVal;
                }
            }

            $status = $delivery->material_shipment->status ?? '';
            // Determine friendly status label
            $statusText = ucfirst($status);
            if ($status === 'received') {
                $statusText = 'Terkirim';
            } elseif ($status === 'shipped') {
                $statusText = 'Dalam Perjalanan';
            } elseif (isset($statusLabels[$status])) {
                $statusText = $statusLabels[$status];
            }

            $dataForPdf[] = [
                $index + 1,
                $delivery->material_shipment->code ?? '-',
                $shipmentDate,
                $delivery->material_shipment->receiver_police_station->name ?? '-',
                $delivery->type->name ?? '-',
                $delivery->type_detail->name ?? '-',
                $serialNumber,
                number_format($delivery->quantity ?? 0, 0, ',', '.'),
                $statusText,
            ];
        }

        $headers = ['No', 'Kode Pengiriman', 'Tanggal Kirim', 'Tujuan', 'Material', 'Detail Material', 'No Seri', 'Qty', 'Status'];
        $fileName = 'Laporan_Pengiriman_'.now()->format('YmdHis').'.pdf';

        $this->dispatch('export-delivery-pdf', [
            'headers' => $headers,
            'data' => $dataForPdf,
            'fileName' => $fileName,
            'filters' => $filters,
        ]);
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

        return view('livewire.admin.report.delivery.admin-report-delivery-index', [
            'deliveries' => $this->deliveries,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
        ])
            ->layout('components.layouts.main.app');
    }
}
