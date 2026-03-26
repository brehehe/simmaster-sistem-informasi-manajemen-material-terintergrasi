<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penerimaan Barang</title>
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
    <h1>Laporan Penerimaan Barang</h1>

    @if($filters)
        <div class="filter-info">
            @if(!empty($filters['search']))
                Pencarian: {{ $filters['search'] }} |
            @endif
            @if(!empty($filters['regionalPolice']))
                Polda: {{ $filters['regionalPolice'] }} |
            @endif
            @if(!empty($filters['type']))
                Tipe: {{ ucwords(str_replace('-', ' ', $filters['type'])) }} |
            @endif
            @if(!empty($filters['material']))
                Material: {{ $filters['material'] }} |
            @endif
            @if(!empty($filters['materialDetail']))
                Material Detail: {{ $filters['materialDetail'] }} |
            @endif
            @if(!empty($filters['startDate']) || !empty($filters['endDate']))
                Periode: {{ $filters['startDate'] ?? '...' }} s/d {{ $filters['endDate'] ?? '...' }}
            @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">Polda</th>
                <th width="8%">Kode</th>
                <th width="8%">Jenis</th>
                <th width="12%">Material</th>
                <th width="11%">Detail Material</th>
                <th width="25%">Nomer Seri</th>
                <th width="11%">Tanggal</th>
                <th width="10%">Kuantitas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $reception)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $reception->reception?->regionalPolice?->name ?? '-' }}</td>
                    <td>{{ $reception->reception?->code ?? '-' }}</td>
                    <td>{{ ucwords(str_replace('-', ' ', $reception->reception?->type ?? '-')) }}</td>
                    <td>{{ $reception->type?->name ?? '-' }}</td>
                    <td>{{ $reception->typeDetail?->name ?? '-' }}</td>
                    <td class="serial-number">{{ trim(implode('', array_filter([$reception?->code, $reception?->number_serial_first, $reception?->number_serial_second]))) ?: '-' }}</td>
                    <td>{{ $reception?->date?->format('d M Y') ?? '-' }}</td>
                    <td class="text-right">{{ number_format($reception?->quantity ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on: {{ now()->format('d M Y H:i:s') }}
    </div>
</body>
</html>
