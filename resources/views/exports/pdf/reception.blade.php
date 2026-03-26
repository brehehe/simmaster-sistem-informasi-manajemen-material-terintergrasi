<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penerimaan Material</title>
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
    <h1>Laporan Penerimaan Material</h1>

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
                <th width="15%">Polres</th>
                <th width="10%">Material</th>
                <th width="12%">Detail</th>
                <th width="22%">Nomer Seri</th>
                <th width="13%">Tanggal</th>
                <th width="15%">Kuantitas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receptions as $index => $reception)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $reception->material_shipment?->code ?? '-' }}</td>
                    <td>{{ $reception->material_shipment?->receiver_police_station?->name ?? '-' }}</td>
                    <td>{{ $reception->type?->name ?? '-' }}</td>
                    <td>{{ $reception->type_detail?->name ?? '-' }}</td>
                    <td class="serial-number">{{ trim(implode('', array_filter([$reception?->code, $reception?->number_serial_first, $reception?->number_serial_second]))) ?: '-' }}</td>
                    <td>{{ $reception->material_shipment?->received_at ? (is_string($reception->material_shipment->received_at) ? \Carbon\Carbon::parse($reception->material_shipment->received_at)->format('d M Y H:i') : $reception->material_shipment->received_at->format('d M Y H:i')) : '-' }}</td>
                    <td class="text-right">{{ number_format($reception?->quantity ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
