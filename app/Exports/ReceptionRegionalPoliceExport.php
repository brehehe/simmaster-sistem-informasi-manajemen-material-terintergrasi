<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReceptionRegionalPoliceExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query
            ->orderBy('receptions.date', 'desc')
            ->orderBy('receptions.created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Polda',
            'Kode',
            'Tipe',
            'Material',
            'Material Detail',
            'Nomer Seri',
            'Tanggal',
            'Jumlah',
        ];
    }

    public function map($reception): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $reception->reception?->regionalPolice?->name ?? '-',
            $reception->reception?->code ?? '-',
            ucwords(str_replace('-', ' ', $reception->reception?->type ?? '-')),
            $reception->type?->name ?? '-',
            $reception->typeDetail?->name ?? '-',
            trim(($reception->code ?? '-').' '.($reception->number_serial_first ?? '-').' '.($reception->number_serial_second ?? '-')),
            $reception->reception?->date?->format('d M Y') ?? '-',
            $reception->quantity ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
