<div>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: left;
            margin-bottom: 20px;
        }
        .header-kop p {
            margin: 0;
            line-height: 1.2;
        }
        .header-kop .underline-text {
            border-bottom: 1px solid #000;
            display: inline-block;
            padding-bottom: 2px;
            margin-top: 2px;
        }
        .sppm-title {
            text-align: center;
            margin: 30px 0;
        }
        .sppm-title h2 {
            margin: 0;
            font-size: 14px;
            text-decoration: underline;
        }
        .sppm-title p {
            margin: 2px 0 0 0;
            font-size: 12px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .info-table td:nth-child(1) { width: 150px; }
        .info-table td:nth-child(2) { width: 10px; }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        table.data-table th {
            text-align: center;
            font-weight: normal;
        }
        
        .signatures {
            width: 100%;
            margin-top: 50px;
        }
        .signatures table {
            width: 100%;
            border-collapse: collapse;
        }
        .signatures td {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        .signatures .sign-space {
            height: 70px;
        }
        .signatures .name-title {
            text-decoration: underline;
            font-weight: bold;
        }
        
        .receiver-info {
            margin-top: 30px;
        }
        .receiver-info table td {
            padding: 2px 5px;
        }
        .receiver-info td:nth-child(1) { width: 100px; }
        .receiver-info td:nth-child(2) { width: 10px; }
        
        @media print {
            .no-print { display: none; }
        }
    </style>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: #fff; border: none; border-radius: 5px; cursor: pointer;">Print Dokumen</button>
    </div>

    <!-- Kop Surat & QR Code -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
        <div class="header-kop" style="margin-bottom: 0;">
            <p>KEPOLISIAN NEGARA REPUBLIK INDONESIA</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DAERAH JAWA TIMUR</p>
            <p class="underline-text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DIREKTORAT LALU LINTAS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
        </div>
    </div>

    <!-- Title -->
    <div class="sppm-title">
        <h2>SURAT PERINTAH PENGELUARAN MATERIEL</h2>
        <p>Nomor : SPPM/{{ $shipment->code }}/LOG.3.6.7./{{ date('Y') }}</p>
    </div>

    <!-- Info -->
    <table class="info-table">
        <tr>
            <td>Surabaya tanggal</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($shipment->shipment_date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Diserahkan kepada</td>
            <td>:</td>
            <td>{{ strtoupper($shipment?->receiverPoliceStation?->name ?? '-') }}</td>
        </tr>
        <tr>
            <td>Berdasarkan</td>
            <td>:</td>
            <td>Pengiriman Material Kode {{ $shipment->code }}</td>
        </tr>
    </table>

    <p style="margin-bottom: 10px;">Materiel sebagai berikut :</p>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 40px;">No</th>
                <th rowspan="2">Nama dan Kode<br>Materiel</th>
                <th rowspan="2" style="width: 60px;">Satuan</th>
                <th colspan="2">Banyaknya</th>
                <th colspan="2">Harga ( Rp )</th>
            </tr>
            <tr>
                <th style="width: 80px;">Angka</th>
                <th>Huruf</th>
                <th style="width: 80px;">Satuan</th>
                <th style="width: 100px;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($shipmentDetails as $group)
                <tr>
                    <td style="text-align: center; vertical-align: top;">{{ $no++ }}</td>
                    <td>{{ $group['name'] }}</td>
                    
                    @if(count($group['sub_items']) == 0 || $group['total_quantity'] > 0)
                        <td style="text-align: center;">@if($group['total_quantity'] > 0) Lbr/Set @endif</td>
                        <td style="text-align: right;">@if($group['total_quantity'] > 0) {{ number_format($group['total_quantity'], 0, ',', '.') }} @endif</td>
                        <td style="text-transform: capitalize;">@if($group['total_quantity'] > 0) {{ strtolower($this->terbilang($group['total_quantity'])) }} @endif</td>
                        <td style="text-align: right;">@if($group['total_quantity'] > 0) {{ number_format($group['base_price'], 0, ',', '.') }} @endif</td>
                        <td style="text-align: right;">@if($group['total_quantity'] > 0) {{ number_format($group['base_price'] * $group['total_quantity'], 0, ',', '.') }} @endif</td>
                    @else
                        <td></td><td></td><td></td><td></td><td></td>
                    @endif
                </tr>
                
                @php
                    $letters = range('a', 'z');
                    $subNo = 0;
                @endphp
                @foreach($group['sub_items'] as $sub)
                    <tr>
                        <td></td>
                        <td style="padding-left: 15px;">{{ $letters[$subNo++] ?? '-' }}. {{ $sub['name'] }}</td>
                        <td style="text-align: center;">Lbr/Set</td>
                        <td style="text-align: right;">{{ number_format($sub['quantity'], 0, ',', '.') }}</td>
                        <td style="text-transform: capitalize;">{{ strtolower($this->terbilang($sub['quantity'])) }}</td>
                        <td style="text-align: right;">{{ number_format($sub['price'], 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($sub['price'] * $sub['quantity'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; height: 100px;">Tidak ada rincian pengiriman.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signatures -->
    <div class="signatures">
        <table>
            <tr>
                <td></td>
                <td>
                    <div style="margin-bottom: 5px;">Surabaya, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ \Carbon\Carbon::parse($shipment->shipment_date)->translatedFormat('F Y') }}</div>
                    @if(($mode ?? 'qr') === 'ttd_ka')
                        <div style="font-weight: bold; margin-bottom: 2px;">a.n. DIREKTUR LALU LINTAS POLDA JATIM</div>
                        <div style="font-weight: bold; margin-bottom: 5px;">KASUBDIT REGIDENT / KASI PASMAT</div>
                        <div class="sign-space" style="height: 75px;"></div>
                        <div class="name-title">...................................................</div>
                        <div style="font-size: 10px; margin-top: 2px;">PANGKAT / NRP. ...........................</div>
                    @else
                        <div class="sign-space" style="height: 60px;"></div>
                        <div class="name-title">...................................................</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Pihak Penerima -->
    <div class="receiver-info">
        <p>YANG MENERIMA :</p>
        <table style="width: auto;">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>__________________________</td>
            </tr>
            <tr>
                <td>Pangkat / Nrp</td>
                <td>:</td>
                <td>__________________________</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>__________________________</td>
            </tr>
            <tr>
                <td>Tanda Tangan</td>
                <td>:</td>
                <td>__________________________</td>
            </tr>
        </table>
    </div>

    @if(($mode ?? 'qr') !== 'ttd_ka')
        <div style="margin-top:10px; text-align: right;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ $shipment->code }}" 
                    alt="QR Code" 
                    style="width: 90px; height: 90px; border: 1px solid #ddd; padding: 2px; background: #fff;">
            <p style="font-size: 9px; margin: 3px 0 0 0; font-family: monospace; font-weight: bold; color: #1e3a8a;">SPPM QR: {{ $shipment->code }}</p>
        </div>  
    @else
        <div style="margin-top: 15px; font-size: 9px; color: #666; font-style: italic; border-top: 1px dashed #ccc; padding-top: 5px;">
            * Dokumen SPPM ini khusus untuk pengajuan TTD Kasubdit / Kasi Pasmat.
        </div>
    @endif
</div>
