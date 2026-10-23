<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resume Anev Kasi Fasmat - {{ $data['date'] }}</title>
    <style>
        @page {
            margin: 20mm 20mm 20mm 25mm;
            size: A4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #111827;
            margin: 0;
            padding: 0;
        }
        .kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .kop h2 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop h3 {
            margin: 2px 0 0 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop p {
            margin: 2px 0 0 0;
            font-size: 9pt;
            color: #4b5563;
        }
        .recipient {
            margin-bottom: 18px;
            font-size: 11pt;
        }
        .recipient table {
            width: 100%;
            border-collapse: collapse;
        }
        .recipient td {
            padding: 2px 0;
            vertical-align: top;
        }
        .section-title {
            font-weight: bold;
            margin-top: 14px;
            margin-bottom: 4px;
            font-size: 11pt;
            text-transform: uppercase;
        }
        .item-list {
            margin-left: 20px;
            margin-bottom: 6px;
        }
        .item-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .item-list td {
            padding: 1.5px 0;
            vertical-align: top;
        }
        .item-num {
            width: 24px;
        }
        .item-label {
            width: 230px;
        }
        .item-colon {
            width: 15px;
            text-align: center;
        }
        .item-val {
            font-weight: normal;
        }
        .bold {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            float: right;
            text-align: center;
            width: 250px;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="kop">
        <h2>Kepolisian Negara Republik Indonesia</h2>
        <h3>Daerah Jawa Timur &middot; Direktorat Lalu Lintas</h3>
        <p>Jl. Ahmad Yani No. 116, Gayungan, Surabaya, Jawa Timur 60235</p>
    </div>

    <div class="recipient">
        <table>
            <tr>
                <td style="width: 80px;" class="bold">Kepada</td>
                <td style="width: 15px;">:</td>
                <td class="bold">Kasi Fasmat Ditlantas Polda Jatim</td>
            </tr>
            <tr>
                <td class="bold">Tanggal</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($data['date'])->locale('id')->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td class="bold">Waktu</td>
                <td>:</td>
                <td>Pukul {{ $data['time_str'] }}</td>
            </tr>
        </table>
    </div>

    {{-- A. CAPAIAN TARGET PNBP --}}
    <div class="section-title">A. CAPAIAN TARGET PNBP TAHUN {{ $data['year'] }} :</div>
    <div class="item-list">
        <table>
            <tr>
                <td class="item-num">1.</td>
                <td class="item-label">Target PNBP</td>
                <td class="item-colon">:</td>
                <td class="item-val">Rp{{ number_format($data['target_pnbp'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="item-num">2.</td>
                <td class="item-label">Realisasi PNBP</td>
                <td class="item-colon">:</td>
                <td class="item-val">Rp{{ number_format($data['realized_pnbp'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="item-num">3.</td>
                <td class="item-label">Persentase capaian</td>
                <td class="item-colon">:</td>
                <td class="item-val bold">{{ str_replace('.', ',', (string) $data['pct_pnbp']) }}%</td>
            </tr>
            <tr>
                <td class="item-num">4.</td>
                <td class="item-label">Sisa target</td>
                <td class="item-colon">:</td>
                <td class="item-val">Rp{{ number_format($data['remaining_pnbp'], 0, ',', '.') }} ({{ str_replace('.', ',', (string) $data['pct_remaining_pnbp']) }}%)</td>
            </tr>
        </table>
    </div>

    {{-- B. CAPAIAN PENGGUNAAN MATERIAL --}}
    <div class="section-title">B. CAPAIAN PENGGUNAAN MATERIAL (GUNMAT) TAHUN {{ $data['year'] }} :</div>
    <div class="item-list">
        <table>
            <tr>
                <td class="item-num">1.</td>
                <td class="item-label">Target Renbut Matreg</td>
                <td class="item-colon">:</td>
                <td class="item-val">{{ number_format($data['target_gunmat'], 0, ',', '.') }} unit</td>
            </tr>
            <tr>
                <td class="item-num">2.</td>
                <td class="item-label">Realisasi penggunaan</td>
                <td class="item-colon">:</td>
                <td class="item-val">{{ number_format($data['realized_gunmat'], 0, ',', '.') }} unit</td>
            </tr>
            <tr>
                <td class="item-num">3.</td>
                <td class="item-label">Persentase capaian</td>
                <td class="item-colon">:</td>
                <td class="item-val bold">{{ str_replace('.', ',', (string) $data['pct_gunmat']) }}%</td>
            </tr>
            <tr>
                <td class="item-num">4.</td>
                <td class="item-label">Sisa kebutuhan</td>
                <td class="item-colon">:</td>
                <td class="item-val">{{ number_format($data['remaining_gunmat'], 0, ',', '.') }} unit ({{ str_replace('.', ',', (string) $data['pct_remaining_gunmat']) }}%)</td>
            </tr>
        </table>
    </div>

    {{-- C. KONDISI STOK MATERIAL --}}
    <div class="section-title">C. KONDISI STOK MATERIAL :</div>
    <div class="item-list">
        <table>
            <tr>
                <td class="item-num">1.</td>
                <td class="item-label">Stok Polda</td>
                <td class="item-colon">:</td>
                <td class="item-val">{{ number_format($data['stock_polda'], 0, ',', '.') }} unit</td>
            </tr>
            <tr>
                <td class="item-num">2.</td>
                <td class="item-label">Stok Polres</td>
                <td class="item-colon">:</td>
                <td class="item-val">{{ number_format($data['stock_polres'], 0, ',', '.') }} unit</td>
            </tr>
            <tr>
                <td class="item-num">3.</td>
                <td class="item-label">Material rusak</td>
                <td class="item-colon">:</td>
                <td class="item-val">{{ number_format($data['material_rusak'], 0, ',', '.') }} unit</td>
            </tr>
        </table>
    </div>

    {{-- D. ANEV PERSEDIAAN WAREHOUSE --}}
    <div class="section-title">D. ANEV PERSEDIAAN WAREHOUSE :</div>
    <div class="item-list">
        <table>
            @php $idx = 1; @endphp
            @foreach($data['warehouse'] as $whName => $whQty)
                <tr>
                    <td class="item-num">{{ $idx++ }}.</td>
                    <td class="item-label">{{ $whName }}</td>
                    <td class="item-colon">:</td>
                    <td class="item-val">{{ number_format($whQty, 0, ',', '.') }} unit</td>
                </tr>
            @endforeach
        </table>
    </div>

    {{-- E. ANEV KETERTIBAN INPUT GUNMAT --}}
    <div class="section-title">E. ANEV KETERTIBAN INPUT GUNMAT (Belum Input) : Pukul {{ $data['time_str'] }}</div>
    <div class="item-list">
        @if(empty($data['missing_satker']))
            <p style="margin: 0; color: #15803d; font-weight: bold;">Nihil (Seluruh satker telah tertib mengisi laporan)</p>
        @else
            <table>
                @foreach($data['missing_satker'] as $msIdx => $ms)
                    <tr>
                        <td class="item-num">{{ $loop->iteration }}.</td>
                        <td class="item-label" style="width: 250px;">{{ $ms['name'] }}</td>
                        <td class="item-colon">:</td>
                        <td class="item-val">(tgl {{ implode(', ', $ms['days']) }})</td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>

    {{-- F. KESIMPULAN ANEV --}}
    <div class="section-title">F. KESIMPULAN ANEV :</div>
    <div class="item-list">
        <table>
            <tr>
                <td class="item-num">1.</td>
                <td class="item-val">{{ $data['kesimpulan_1'] }}</td>
            </tr>
            <tr>
                <td class="item-num">2.</td>
                <td class="item-val">{{ $data['kesimpulan_2'] }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p style="margin-bottom: 50px;">Petugas Piket Anev,</p>
        <p class="bold" style="text-decoration: underline;">URMIN FASMAT</p>
    </div>
    <div class="clear"></div>
</body>
</html>
