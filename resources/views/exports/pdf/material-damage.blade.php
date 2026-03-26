<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kerusakan Material</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .filter-info {
            text-align: center;
            font-size: 10px;
            margin-bottom: 20px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #f3f4f6;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }
        .serial-number {
            font-size: 9px;
            line-height: 1.3;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Laporan Kerusakan Material</h1>

    @if($filters)
        <div class="filter-info">
            @if(!empty($filters['startDate']) || !empty($filters['endDate']))
                Periode: {{ $filters['startDate'] ?? '-' }} s/d {{ $filters['endDate'] ?? '-' }}
            @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="9%">Kode</th>
                <th width="11%">Lokasi</th>
                <th width="9%">Material</th>
                <th width="11%">Detail</th>
                <th width="20%">Nomer Seri</th>
                <th width="10%">Tanggal</th>
                <th width="7%">Qty</th>
                <th width="10%">Status</th>
                <th width="10%">Ket</th>
            </tr>
        </thead>
        <tbody>
            @foreach($damages as $index => $damage)
                @php
                    $statusLabels = [
                        'reported' => 'Dilaporkan',
                        'under_review' => 'Pemeriksaan',
                        'approved' => 'Disetujui',
                        'disposed' => 'Dimusnahkan',
                    ];
                    $status = $statusLabels[$damage->material_damage?->status] ?? '-';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $damage->material_damage?->code ?? '-' }}</td>
                    <td>{{ $damage->material_damage?->police_station?->name ?? $damage->material_damage?->regional_police?->name ?? '-' }}</td>
                    <td>{{ $damage->type?->name ?? '-' }}</td>
                    <td>{{ $damage->type_detail?->name ?? '-' }}</td>
                    <td class="serial-number">{{ trim(implode('', array_filter([$damage?->code, $damage?->number_serial_first, $damage?->number_serial_second]))) ?: '-' }}</td>
                    <td>{{ $damage->material_damage?->date ? (is_string($damage->material_damage->date) ? \Carbon\Carbon::parse($damage->material_damage->date)->format('d M Y') : $damage->material_damage->date->format('d M Y')) : '-' }}</td>
                    <td class="text-right">{{ number_format($damage?->quantity ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $status }}</td>
                    <td>{{ Str::limit($damage->material_damage?->description ?? '-', 30) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
