<div>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: center;
            width: 340px;
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
            font-size: 11px;
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
            page-break-inside: avoid;
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

    @if(!($isPdf ?? false))
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button wire:click="exportPdf" wire:loading.attr="disabled" style="padding: 10px 20px;">Unduh PDF</button>
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: #fff; border: none; border-radius: 5px; cursor: pointer;">Print Dokumen</button>
    </div>

    @endif
    <!-- Kop Surat & QR Code -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
        <div class="header-kop" style="margin-bottom: 0;">
            <p>KEPOLISIAN NEGARA REPUBLIK INDONESIA</p>
            <p>DAERAH JAWA TIMUR</p>
            <p class="underline-text">DIREKTORAT LALU LINTAS</p>
        </div>
    </div>

    <!-- Title -->
    <div class="sppm-title">
        <h2>SURAT PERINTAH PENGELUARAN MATERIEL</h2>
        <p>Nomor : {{ $shipment->code }}</p>
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
                <th rowspan="2" style="width: 4%;">No</th>
                <th rowspan="2" style="width: 28%;">Nama dan Kode<br>Materiel</th>
                <th rowspan="2" style="width: 8%;">Satuan</th>
                <th colspan="2">Banyaknya</th>
                <th colspan="2">Harga ( Rp )</th><th rowspan="2" style="width: 8%;">Ket</th>
            </tr>
            <tr>
                <th style="width: 10%;">Angka</th>
                <th style="width: 18%;">Huruf</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 14%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shipmentDetails as $item)
                @php
                    $price = $item->stockDetail?->serviceDetail?->price ?? $item->stockDetail?->service?->price ?? $item->typeDetail?->price ?? $item->type?->price ?? 0;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->type?->name }} {{ $item->typeDetail?->name }}<br>{{ $item->stockDetail?->service?->name }} {{ $item->stockDetail?->serviceDetail?->name }}
                        @if($item->code)<br>Kode: {{ $item->code }}@endif
                        @if($item->number_serial_first)<br>Nomor seri: {{ $item->number_serial_first }} – {{ $item->number_serial_second }}@endif
                    </td>
                    <td>{{ $item->type?->unit ?? 'Unit' }}</td>
                    <td>{{ number_format($item->quantity, 0, ',', '.') }}</td>
                    <td>{{ $formatter->terbilang($item->quantity) }}</td>
                    <td>{{ number_format($price, 0, ',', '.') }}</td>
                    <td>{{ number_format($price * $item->quantity, 0, ',', '.') }}</td><td>{{ $item->notes }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Tidak ada rincian pengiriman.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signatures -->
    <div class="signatures">
        <table>
            <tr>
                <td></td>
                <td>
                    <div style="margin-bottom: 5px;">Surabaya, &nbsp;&nbsp;&nbsp;{{ \Carbon\Carbon::parse($shipment->shipment_date)->translatedFormat('F Y') }}</div>
                    @if(($mode ?? 'qr') === 'ttd_ka')
                        <div style="font-weight: bold; margin-bottom: 2px;">a.n. DIREKTUR LALU LINTAS POLDA JATIM</div>
                        <div style="font-weight: bold; margin-bottom: 5px;">KASUBDIT REGIDENT / KASI PASMAT</div>
                        <div class="sign-space" style="height: 75px;"></div>
                        <div class="name-title">...................................................</div>
                        <div style="font-size: 10px; margin-top: 2px;">PANGKAT / NRP. ...........................</div>
                    @else
                        <div>a.n. DIREKTUR LALU LINTAS POLDA JATIM</div>
                        <div>KASUBDIT REGIDENT</div>
                        <div style="margin: 8px auto;"><img src="{{ app(\App\Services\DocumentQrService::class)->dataUri($shipment->code) }}" alt="Barcode SPPM" width="90" height="90"></div>
                        <div style="font-size: 9px;">{{ $shipment->code }}</div>
                        <div class="name-title">...................................................</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Pihak Penerima / Pengambil Gudang -->
    <div class="receiver-info" style="margin-top: 15px;">
        <p style="font-weight: bold; margin-bottom: 5px;">YANG MENERIMA / SERAH TERIMA GUDANG :</p>
        <table style="width: auto; border: none;">
            <tr>
                <td style="border: none; padding: 2px 10px 2px 0; font-weight: bold;">Nama</td>
                <td style="border: none; padding: 2px 5px;">:</td>
                <td style="border: none; padding: 2px 0;">{{ $shipment->picker_name ?: ($shipment->received_by ?: '__________________________') }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 10px 2px 0; font-weight: bold;">Pangkat / NRP</td>
                <td style="border: none; padding: 2px 5px;">:</td>
                <td style="border: none; padding: 2px 0;">{{ $shipment->picker_rank ?: '__________________________' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 10px 2px 0; font-weight: bold;">Jabatan</td>
                <td style="border: none; padding: 2px 5px;">:</td>
                <td style="border: none; padding: 2px 0;">{{ $shipment->picker_position ?: '__________________________' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 10px 2px 0; font-weight: bold; vertical-align: top;">Tanda Tangan</td>
                <td style="border: none; padding: 2px 5px; vertical-align: top;">:</td>
                <td style="border: none; padding: 2px 0;">
                    @if($shipment->picker_signature)
                        <img src="{{ $shipment->picker_signature }}" style="max-height: 55px; display: block; margin-top: 2px;">
                    @else
                        __________________________
                    @endif
                </td>
            </tr>
        </table>
    </div>

    @if($shipment->picker_photo)
        <div style="margin-top: 20px; page-break-inside: avoid; border-top: 1px dashed #ccc; pt-3;">
            <p style="font-size: 10px; font-weight: bold; margin-bottom: 5px;">LAMPIRAN FOTO DOKUMENTASI SERAH TERIMA GUDANG:</p>
            <img src="{{ \Illuminate\Support\Facades\Storage::url($shipment->picker_photo) }}" style="max-height: 140px; border: 1px solid #ccc; border-radius: 4px; padding: 3px;">
        </div>
    @endif

</div>
