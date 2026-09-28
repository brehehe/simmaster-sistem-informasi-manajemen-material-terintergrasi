<?php

namespace App\Exports;

use App\Services\ReportingComplianceService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportingComplianceExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithCustomValueBinder, WithHeadings, WithStrictNullComparison, WithStyles
{
    public function __construct(private Collection $rows, private string $date, private string $source) {}

    public function headings(): array
    {
        return ['No', 'Tanggal laporan', 'Jenis input', 'Wilayah', 'Status', 'Selisih hari', 'Input terakhir (WIB)', 'Jumlah input', 'Rincian jenis input'];
    }

    public function collection(): Collection
    {
        $sources = ReportingComplianceService::sources();

        return $this->rows->values()->map(function ($row, $index) use ($sources) {
            $details = collect($row['inputs'])->map(fn ($input, $key) => $sources[$key]['label'].': '.$input['count'])->implode('; ');

            return [$index + 1, $this->date, $this->source === 'all' ? 'Semua input operasional' : $sources[$this->source]['label'], $row['name'], $row['label'], $row['delay'], $row['submitted_at'] ?: '—', $row['report_count'], $details ?: 'Tidak ada input'];
        });
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        return [1 => ['font' => ['bold' => true]]];
    }
}
