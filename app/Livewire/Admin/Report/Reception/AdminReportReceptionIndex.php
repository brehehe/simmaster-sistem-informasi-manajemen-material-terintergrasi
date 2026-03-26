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

    public function updatedSearch()
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
                'typeDetail',
            ])
            ->where('material_shipments.is_active', true)
            ->where('material_shipments.status', 'received');

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('material_shipments.code', 'ilike', '%'.$this->search.'%')
                    ->orWhereHas('materialShipment.receiverPoliceStation', function ($polres) {
                        $polres->where('name', 'ilike', '%'.$this->search.'%');
                    })
                    ->orWhereHas('materialShipment.receivedByUser', function ($user) {
                        $user->where('name', 'ilike', '%'.$this->search.'%');
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
        return MaterialShipmentDetail::whereHas('materialShipment', function ($q) {
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

    public function exportExcel()
    {
        $filters = [
            'search' => $this->search,
            'filterPolres' => $this->filterPolres,
            'typeId' => $this->typeId,
            'typeDetailId' => $this->typeDetailId,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

        $fileName = 'Laporan_Penerimaan_'.now()->format('YmdHis').'.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReceptionExport($filters),
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
                // Convert model to array first to process attributes/relations
                // We use json_decode(json_encode) here to get stdClass but ensuring we strip bad chars first if possible
                // But better to convert to array, sanitize, then cast to object
                $arr = $data->toArray();
                $cleanArr = $this->sanitizeRecursive($arr);

                return (object) $cleanArr;
            } elseif ($data instanceof \Illuminate\Support\Collection) {
                return $data->map(function ($item) {
                    return $this->sanitizeRecursive($item);
                });
            } else {
                // StdClass or other objects
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
            'filterPolres' => $this->filterPolres,
            'typeId' => $this->typeId,
            'typeDetailId' => $this->typeDetailId,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

        $receptions = $this->receptions->items();

        // Deep sanitize first
        $jsonObj = json_decode(json_encode($receptions, JSON_INVALID_UTF8_SUBSTITUTE));
        $receptions = $this->sanitizeRecursive($jsonObj);

        // Prepare flat data for jsPDF
        // This moves formatting logic from Blade to PHP
        $dataForPdf = [];
        foreach ($receptions as $index => $reception) {
            $serialNumber = trim(implode('', array_filter([
                $reception->code ?? '',
                $reception->number_serial_first ?? '',
                $reception->number_serial_second ?? '',
            ]))) ?: '-';

            $receivedAt = '-';
            if (! empty($reception->material_shipment->received_at)) {
                // Determine if string or object (sanitization might have made it string or stdClass if deeply nested?)
                // sanitizeRecursive forces strings to be strings.
                // Original logic handled string vs object.
                // After json_encode, dates are usually strings (ISO 8601).
                $dateVal = $reception->material_shipment->received_at;
                try {
                    $receivedAt = \Carbon\Carbon::parse($dateVal)->format('d M Y H:i');
                } catch (\Exception $e) {
                    $receivedAt = $dateVal;
                }
            }

            $dataForPdf[] = [
                $index + 1,
                $reception->material_shipment->code ?? '-',
                $reception->material_shipment->receiver_police_station->name ?? '-',
                $reception->type->name ?? '-',
                $reception->type_detail->name ?? '-',
                $serialNumber,
                $receivedAt,
                number_format($reception->quantity ?? 0, 0, ',', '.'),
            ];
        }

        $headers = ['No', 'Kode Pengiriman', 'Penerima', 'Tipe', 'Detail Tipe', 'Nomer Seri', 'Tanggal Diterima', 'Qty'];
        $fileName = 'Laporan_Penerimaan_'.now()->format('YmdHis').'.pdf';

        $this->dispatch('export-reception-pdf', [
            'headers' => $headers,
            'data' => $dataForPdf,
            'fileName' => $fileName,
            'filters' => $filters, // Optional, if we want to print filters in PDF
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

        return view('livewire.admin.report.reception.admin-report-reception-index', [
            'receptions' => $this->receptions,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
        ])
            ->layout('components.layouts.main.app');
    }
}
