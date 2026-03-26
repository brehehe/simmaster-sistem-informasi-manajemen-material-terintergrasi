<?php

namespace App\Exports;

use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class MaterialUsageDetailExport implements FromCollection, WithHeadings, WithTitle
{
    protected $filters;

    protected $user;

    public function __construct($filters = [], $user = null)
    {
        $this->filters = $filters;
        $this->user = $user;
    }

    public function collection()
    {
        $query = MaterialUsageDetail::query()
            ->whereHas('materialUsage', function ($q) {
                if ($this->user && $this->user->hasRole('Admin')) {
                    if (! empty($this->filters['policeStationId'])) {
                        $q->where('police_station_id', $this->filters['policeStationId']);
                    }
                    if (! empty($this->filters['regionalPoliceId'])) {
                        $q->where('regional_police_id', $this->filters['regionalPoliceId']);
                    }
                } else {
                    if ($this->user && $this->user->policeStation) {
                        $q->where('police_station_id', $this->user->policeStation->id);
                    }
                    if ($this->user && $this->user->regionalPolice) {
                        $q->where('regional_police_id', $this->user->regionalPolice->id);
                    }
                }
            })
            ->when(! empty($this->filters['typeId']), function ($q) {
                $q->where('type_id', $this->filters['typeId']);
            })
            ->when(! empty($this->filters['typeDetailId']), function ($q) {
                $q->where('type_detail_id', $this->filters['typeDetailId']);
            })
            ->with([
                'typeDetail',
                'materialUsageDetailItems.service',
                'materialUsageDetailItems.serviceDetail',
                'materialUsage.policeStation',
                'materialUsage.regionalPolice',
                'type',
            ])
            ->get();

        // Filter by user permissions for Types
        if ($this->user && $this->user->userType && ! empty($this->user->userType->types)) {
            $query = $query->whereIn('type_id', $this->user->userType->types);
        }

        $data = new Collection;
        $index = 0;

        foreach ($query as $detail) {
            $index++;

            $serialNumber = trim(implode('', array_filter([
                $detail->code,
                $detail->number_serial_first,
                $detail->number_serial_second,
            ]))) ?: '-';

            // Add main detail row
            $data->push([
                $index,
                $detail->type?->name ?? '-',
                $detail->typeDetail?->name ?? '-',
                $serialNumber,
                $detail->materialUsage?->policeStation?->name ?? $detail->materialUsage?->regionalPolice?->name ?? '-',
                '', // Service
                '', // Service Detail
                $detail->quantity ?? 0,
            ]);

            // Add service detail items
            if ($detail->materialUsageDetailItems) {
                foreach ($detail->materialUsageDetailItems as $item) {
                    $data->push([
                        '',
                        '',
                        '',
                        '',
                        '',
                        $item->service?->name ?? '-',
                        $item->serviceDetail?->name ?? '-',
                        $item->quantity ?? 0,
                    ]);
                }
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Jenis Material',
            'Detail Material',
            'Nomer Seri',
            'Lokasi',
            'Service',
            'Service Detail',
            'Kuantitas',
        ];
    }

    public function title(): string
    {
        return 'Detail Penggunaan Material';
    }
}
