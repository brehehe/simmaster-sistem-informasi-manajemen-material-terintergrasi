<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnevExecutiveExport implements FromCollection, ShouldAutoSize, WithStyles
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $d = $this->data;
        $rows = [];

        $rows[] = ['KEPADA', ':', 'Kasi Fasmat Ditlantas Polda Jatim', ''];
        $rows[] = ['TANGGAL', ':', \Carbon\Carbon::parse($d['date'])->translatedFormat('d F Y'), ''];
        $rows[] = ['WAKTU', ':', 'Pukul '.$d['time_str'], ''];
        $rows[] = ['', '', '', ''];

        // A
        $rows[] = ['A. CAPAIAN TARGET PNBP TAHUN '.$d['year'].' :', '', '', ''];
        $rows[] = ['1. Target PNBP', ':', 'Rp '.number_format($d['target_pnbp'], 0, ',', '.'), ''];
        $rows[] = ['2. Realisasi PNBP', ':', 'Rp '.number_format($d['realized_pnbp'], 0, ',', '.'), ''];
        $rows[] = ['3. Persentase capaian', ':', str_replace('.', ',', (string) $d['pct_pnbp']).'%', ''];
        $rows[] = ['4. Sisa target', ':', 'Rp '.number_format($d['remaining_pnbp'], 0, ',', '.'), '('.str_replace('.', ',', (string) $d['pct_remaining_pnbp']).'%)'];
        $rows[] = ['', '', '', ''];

        // B
        $rows[] = ['B. CAPAIAN PENGGUNAAN MATERIAL (GUNMAT) TAHUN '.$d['year'].' :', '', '', ''];
        $rows[] = ['1. Target Renbut Matreg', ':', number_format($d['target_gunmat'], 0, ',', '.').' unit', ''];
        $rows[] = ['2. Realisasi penggunaan', ':', number_format($d['realized_gunmat'], 0, ',', '.').' unit', ''];
        $rows[] = ['3. Persentase capaian', ':', str_replace('.', ',', (string) $d['pct_gunmat']).'%', ''];
        $rows[] = ['4. Sisa kebutuhan', ':', number_format($d['remaining_gunmat'], 0, ',', '.').' unit', '('.str_replace('.', ',', (string) $d['pct_remaining_gunmat']).'%)'];
        $rows[] = ['', '', '', ''];

        // C
        $rows[] = ['C. KONDISI STOK MATERIAL :', '', '', ''];
        $rows[] = ['1. Stok Polda', ':', number_format($d['stock_polda'], 0, ',', '.').' unit', ''];
        $rows[] = ['2. Stok Polres', ':', number_format($d['stock_polres'], 0, ',', '.').' unit', ''];
        $rows[] = ['3. Material rusak', ':', number_format($d['material_rusak'], 0, ',', '.').' unit', ''];
        $rows[] = ['', '', '', ''];

        // D
        $rows[] = ['D. ANEV PERSEDIAAN WAREHOUSE :', '', '', ''];
        $i = 1;
        foreach ($d['warehouse'] as $whName => $whQty) {
            $rows[] = [$i.'. '.$whName, ':', number_format($whQty, 0, ',', '.').' unit', ''];
            $i++;
        }
        $rows[] = ['', '', '', ''];

        // E
        $rows[] = ['E. ANEV KETERTIBAN INPUT GUNMAT (Belum Input) : Pukul '.$d['time_str'], '', '', ''];
        if (empty($d['missing_satker'])) {
            $rows[] = ['Nihil (Seluruh satker telah tertib mengisi laporan)', '', '', ''];
        } else {
            $j = 1;
            foreach ($d['missing_satker'] as $ms) {
                $rows[] = [$j.'. '.$ms['name'], ':', '(tgl '.implode(', ', $ms['days']).')', ''];
                $j++;
            }
        }
        $rows[] = ['', '', '', ''];

        // F
        $rows[] = ['F. KESIMPULAN ANEV :', '', '', ''];
        $rows[] = ['1. '.$d['kesimpulan_1'], '', '', ''];
        $rows[] = ['2. '.$d['kesimpulan_2'], '', '', ''];

        return collect($rows);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D100')->getFont()->setName('Calibri')->setSize(11);

        // Header
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('C1')->getFont()->setBold(true)->setSize(12);

        // Section headings (A., B., C., D., E., F.)
        $highestRow = $sheet->getHighestRow();
        for ($row = 1; $row <= $highestRow; $row++) {
            $val = (string) $sheet->getCell('A'.$row)->getValue();
            if (preg_match('/^[A-F]\./', $val)) {
                $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle('A'.$row.':D'.$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F4F8');
            }
        }

        return [];
    }
}
