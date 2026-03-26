<?php

namespace App\Exports;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DeliveryExport implements FromQuery, WithHeadings, WithMapping, WithTitle
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
            ->where('material_shipments.status', '!=', 'draft');

        // Apply filters
        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('material_shipments.code', 'ilike', '%'.$search.'%')
                    ->orWhere('material_shipments.courier_name', 'ilike', '%'.$search.'%')
                    ->orWhere('material_shipments.vehicle_number', 'ilike', '%'.$search.'%');
            });
        }

        if (! empty($this->filters['filterStatus'])) {
            $query->where('material_shipments.status', $this->filters['filterStatus']);
        }

        if (! empty($this->filters['filterPolda'])) {
            $query->where('material_shipments.sender_regional_police_id', $this->filters['filterPolda']);
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
            $query->whereDate('material_shipments.shipment_date', '>=', $this->filters['startDate']);
        }

        if (! empty($this->filters['endDate'])) {
            $query->whereDate('material_shipments.shipment_date', '<=', $this->filters['endDate']);
        }

        return $query->orderBy('material_shipments.shipment_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Pengiriman',
            'Polda Pengirim',
            'Polres Penerima',
            'Jenis',
            'Material',
            'Detail Material',
            'Nomer Seri',
            'Tanggal Kirim',
            'Kuantitas',
            'Status',
            'Kurir',
            'No. Kendaraan',
        ];
    }

    public function map($delivery): array
    {
        static $index = 0;
        $index++;

        $serialNumber = trim(implode('', array_filter([
            $delivery->code,
            $delivery->number_serial_first,
            $delivery->number_serial_second,
        ]))) ?: '-';

        return [
            $index,
            $delivery->materialShipment?->code ?? '-',
            $delivery->materialShipment?->senderRegionalPolice?->name ?? '-',
            $delivery->materialShipment?->receiverPoliceStation?->name ?? '-',
            ucwords(str_replace('-', ' ', $delivery->materialShipment?->type ?? '-')),
            $delivery->type?->name ?? '-',
            $delivery->typeDetail?->name ?? '-',
            $serialNumber,
            $delivery->materialShipment?->shipment_date ? (is_string($delivery->materialShipment->shipment_date) ? \Carbon\Carbon::parse($delivery->materialShipment->shipment_date)->format('d M Y') : $delivery->materialShipment->shipment_date->format('d M Y')) : '-',
            $delivery->quantity ?? 0,
            $delivery->materialShipment?->status == 'shipped' ? 'Dalam Perjalanan' : 'Terkirim',
            $delivery->materialShipment?->courier_name ?? '-',
            $delivery->materialShipment?->vehicle_number ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Laporan Pengiriman';
    }
}
