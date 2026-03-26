<?php

namespace App\Exports;

use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class MaterialUsageExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $filters;

    protected $user;

    public function __construct($filters = [], $user = null)
    {
        $this->filters = $filters;
        $this->user = $user;
    }

    public function query()
    {
        $query = MaterialUsageDetail::query()
            ->join('material_usages', 'material_usage_details.material_usage_id', '=', 'material_usages.id')
            ->select('material_usage_details.*')
            ->with([
                'materialUsage.regionalPolice',
                'materialUsage.policeStation',
                'type',
                'typeDetail',
            ])
            ->where('material_usage_details.is_active', true);

        // Role-based filtering
        if ($this->user && $this->user->hasRole('Polda')) {
            $query->where('material_usages.regional_police_id', $this->user->regional_police_id)
                ->whereNull('material_usages.police_station_id');
        }

        if ($this->user && $this->user->hasRole('Polres')) {
            $query->where('material_usages.police_station_id', $this->user->police_station_id);
        }

        // Apply filters
        if (! empty($this->filters['regionalPoliceId'])) {
            $query->where('material_usages.regional_police_id', $this->filters['regionalPoliceId']);
        }

        if (! empty($this->filters['policeStationId'])) {
            $query->where('material_usages.police_station_id', $this->filters['policeStationId']);
        }

        if (! empty($this->filters['typeId'])) {
            $query->where('material_usage_details.type_id', $this->filters['typeId']);
        }

        if (! empty($this->filters['typeDetailId'])) {
            $query->where('material_usage_details.type_detail_id', $this->filters['typeDetailId']);
        }

        if (! empty($this->filters['startDate'])) {
            $query->whereDate('material_usages.date', '>=', $this->filters['startDate']);
        }

        if (! empty($this->filters['endDate'])) {
            $query->whereDate('material_usages.date', '<=', $this->filters['endDate']);
        }

        return $query->latest('material_usages.date');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Penggunaan',
            'Polda',
            'Polres',
            'Jenis',
            'Material',
            'Detail Material',
            'Nomer Seri',
            'Tanggal',
            'Kuantitas',
            'Keterangan',
        ];
    }

    public function map($usage): array
    {
        static $index = 0;
        $index++;

        $serialNumber = trim(implode('', array_filter([
            $usage->code,
            $usage->number_serial_first,
            $usage->number_serial_second,
        ]))) ?: '-';

        return [
            $index,
            $usage->materialUsage?->code ?? '-',
            $usage->materialUsage?->regionalPolice?->name ?? '-',
            $usage->materialUsage?->policeStation?->name ?? '-',
            ucwords(str_replace('-', ' ', $usage->materialUsage?->type ?? '-')),
            $usage->type?->name ?? '-',
            $usage->typeDetail?->name ?? '-',
            $serialNumber,
            $usage->materialUsage?->date ? (is_string($usage->materialUsage->date) ? \Carbon\Carbon::parse($usage->materialUsage->date)->format('d M Y') : $usage->materialUsage->date->format('d M Y')) : '-',
            $usage->quantity ?? 0,
            $usage->materialUsage?->description ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Laporan Penggunaan Material';
    }
}
