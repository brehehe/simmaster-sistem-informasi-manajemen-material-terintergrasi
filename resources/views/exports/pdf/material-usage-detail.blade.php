<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Detail Penggunaan Material</title>
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
            margin-bottom: 20px;
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
        .type-header {
            background-color: #e5e7eb;
            font-weight: bold;
            padding: 8px;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .service-row {
            background-color: #f9fafb;
            font-style: italic;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Laporan Detail Penggunaan Material</h1>

    @if($filters)
        <div class="filter-info">
            Filter diterapkan
        </div>
    @endif

    @foreach($typeGroups as $group)
        <div class="type-header">{{ $group->type->name }}</div>

        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="15%">Detail Material</th>
                    <th width="20%">Nomer Seri</th>
                    <th width="15%">Lokasi</th>
                    <th width="15%">Service</th>
                    <th width="17%">Service Detail</th>
                    <th width="15%">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($group->details as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->type_detail?->name ?? '-' }}</td>
                        <td class="serial-number">{{ trim(implode('', array_filter([$detail?->code, $detail?->number_serial_first, $detail?->number_serial_second]))) ?: '-' }}</td>
                        <td>{{ $detail->material_usage?->police_station?->name ?? $detail->material_usage?->regional_police?->name ?? '-' }}</td>
                        <td>-</td>
                        <td>-</td>
                        <td class="text-right">{{ number_format($detail?->quantity ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @if($detail->material_usage_detail_items && count($detail->material_usage_detail_items) > 0)
                        @foreach($detail->material_usage_detail_items as $item)
                            <tr class="service-row">
                                <td></td>
                                <td colspan="3"></td>
                                <td>{{ $item->service?->name ?? '-' }}</td>
                                <td>{{ $item->service_detail?->name ?? '-' }}</td>
                                <td class="text-right">{{ number_format($item?->quantity ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
