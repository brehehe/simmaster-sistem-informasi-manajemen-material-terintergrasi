<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penggunaan Material</title>
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
    <h1>Laporan Penggunaan Material</h1>

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
                <th width="10%">Kode</th>
                <th width="12%">Lokasi</th>
                <th width="10%">Material</th>
                <th width="12%">Detail</th>
                <th width="22%">Nomer Seri</th>
                <th width="13%">Tanggal</th>
                <th width="8%">Kuantitas</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usages as $index => $usage)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $usage->material_usage?->code ?? '-' }}</td>
                    <td>{{ $usage->material_usage?->police_station?->name ?? $usage->material_usage?->regional_police?->name ?? '-' }}</td>
                    <td>{{ $usage->type?->name ?? '-' }}</td>
                    <td>{{ $usage->type_detail?->name ?? '-' }}</td>
                    <td class="serial-number">{{ trim(implode('', array_filter([$usage?->code, $usage?->number_serial_first, $usage?->number_serial_second]))) ?: '-' }}</td>
                    <td>{{ $usage->material_usage?->date ? (is_string($usage->material_usage->date) ? \Carbon\Carbon::parse($usage->material_usage->date)->format('d M Y') : $usage->material_usage->date->format('d M Y')) : '-' }}</td>
                    <td class="text-right">{{ number_format($usage?->quantity ?? 0, 0, ',', '.') }}</td>
                    <td>{{ Str::limit($usage->material_usage?->description ?? '-', 50) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
