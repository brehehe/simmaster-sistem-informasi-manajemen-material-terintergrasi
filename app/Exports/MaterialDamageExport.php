<?php

namespace App\Exports;

use App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class MaterialDamageExport implements FromQuery, WithHeadings, WithMapping, WithTitle
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
        $query = MaterialDamageDetail::query()
            ->join('material_damages', 'material_damage_details.material_damage_id', '=', 'material_damages.id')
            ->select('material_damage_details.*')
            ->with([
                'materialDamage.regionalPolice',
                'materialDamage.policeStation',
                'type',
                'typeDetail',
            ])
            ->where('material_damage_details.is_active', true);

        // Role filtering
        if ($this->user && $this->user->hasRole('Polda')) {
            $query->where('material_damages.regional_police_id', $this->user->regional_police_id)
                ->whereNull('material_damages.police_station_id');
        }

        if ($this->user && $this->user->hasRole('Polres')) {
            $query->where('material_damages.police_station_id', $this->user->police_station_id);
        }

        // Apply filters
        if (! empty($this->filters['regionalPoliceId'])) {
            $query->where('material_damages.regional_police_id', $this->filters['regionalPoliceId']);
        }

        if (! empty($this->filters['policeStationId'])) {
            $query->where('material_damages.police_station_id', $this->filters['policeStationId']);
        }

        if (! empty($this->filters['typeId'])) {
            $query->where('material_damage_details.type_id', $this->filters['typeId']);
        }

        if (! empty($this->filters['typeDetailId'])) {
            $query->where('material_damage_details.type_detail_id', $this->filters['typeDetailId']);
        }

        if (! empty($this->filters['filterStatus'])) {
            $query->where('material_damages.status', $this->filters['filterStatus']);
        }

        if (! empty($this->filters['startDate'])) {
            $query->whereDate('material_damages.date', '>=', $this->filters['startDate']);
        }

        if (! empty($this->filters['endDate'])) {
            $query->whereDate('material_damages.date', '<=', $this->filters['endDate']);
        }

        return $query->latest('material_damages.date');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Kerusakan',
            'Polda',
            'Polres',
            'Jenis',
            'Material',
            'Detail Material',
            'Nomer Seri',
            'Tanggal',
            'Kuantitas',
            'Status',
            'Keterangan',
        ];
    }

    public function map($damage): array
    {
        static $index = 0;
        $index++;

        $serialNumber = trim(implode('', array_filter([
            $damage->code,
            $damage->number_serial_first,
            $damage->number_serial_second,
        ]))) ?: '-';

        $statusLabels = [
            'reported' => 'Dilaporkan',
            'under_review' => 'Dalam Pemeriksaan',
            'approved' => 'Disetujui',
            'disposed' => 'Dimusnahkan',
        ];

        $status = $statusLabels[$damage->materialDamage?->status] ?? '-';

        return [
            $index,
            $damage->materialDamage?->code ?? '-',
            $damage->materialDamage?->regionalPolice?->name ?? '-',
            $damage->materialDamage?->policeStation?->name ?? '-',
            ucwords(str_replace('-', ' ', $damage->materialDamage?->type ?? '-')),
            $damage->type?->name ?? '-',
            $damage->typeDetail?->name ?? '-',
            $serialNumber,
            $damage->materialDamage?->date ? (is_string($damage->materialDamage->date) ? \Carbon\Carbon::parse($damage->materialDamage->date)->format('d M Y') : $damage->materialDamage->date->format('d M Y')) : '-',
            $damage->quantity ?? 0,
            $status,
            $damage->materialDamage?->description ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Laporan Kerusakan Material';
    }
}
