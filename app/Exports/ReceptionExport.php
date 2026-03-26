<?php

namespace App\Exports;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReceptionExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
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

        // Apply filters
        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('material_shipments.code', 'ilike', '%'.$search.'%');
            });
        }

        if (! empty($this->filters['filterPolres'])) {
            $query->where('material_shipments.receiver_police_station_id', $this->filters['filterPolres']);
        }

        if (! empty($this->filters['typeId'])) {
            $query->where('material_shipment_details.type_id', $this->filters['typeId']);
        }

        if (! empty($this->filters['typeDetailId'])) {
            $query->where('material_shipment_details.type_detail_id', $this->filters['typeDetailId']);
        }

        if (! empty($this->filters['startDate'])) {
            $query->whereDate('material_shipments.received_at', '>=', $this->filters['startDate']);
        }

        if (! empty($this->filters['endDate'])) {
            $query->whereDate('material_shipments.received_at', '<=', $this->filters['endDate']);
        }

        return $query->orderBy('material_shipments.received_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Penerimaan',
            'Polres Penerima',
            'Jenis',
            'Material',
            'Detail Material',
            'Nomer Seri',
            'Tanggal Diterima',
            'Diterima Oleh',
            'Kuantitas',
        ];
    }

    public function map($reception): array
    {
        static $index = 0;
        $index++;

        $serialNumber = trim(implode('', array_filter([
            $reception->code,
            $reception->number_serial_first,
            $reception->number_serial_second,
        ]))) ?: '-';

        return [
            $index,
            $reception->materialShipment?->code ?? '-',
            $reception->materialShipment?->receiverPoliceStation?->name ?? '-',
            ucwords(str_replace('-', ' ', $reception->materialShipment?->type ?? '-')),
            $reception->type?->name ?? '-',
            $reception->typeDetail?->name ?? '-',
            $serialNumber,
            $reception->materialShipment?->received_at ? (is_string($reception->materialShipment->received_at) ? \Carbon\Carbon::parse($reception->materialShipment->received_at)->format('d M Y H:i') : $reception->materialShipment->received_at->format('d M Y H:i')) : '-',
            $reception->materialShipment?->receivedByUser?->name ?? '-',
            $reception->quantity ?? 0,
        ];
    }

    public function title(): string
    {
        return 'Laporan Penerimaan';
    }
}
