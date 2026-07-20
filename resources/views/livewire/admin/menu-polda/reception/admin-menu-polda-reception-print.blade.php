<div>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: left;
            margin-bottom: 15px;
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
            margin: 20px 0;
        }
        .sppm-title h2 {
            margin: 0;
            font-size: 13px;
            text-decoration: underline;
            font-weight: bold;
        }
        .sppm-title p {
            margin: 2px 0 0 0;
            font-size: 11px;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
        }
        table.data-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f9fafb;
        }
        
        /* Utility for printing */
        @media print {
            .no-print { display: none; }
        }
    </style>

    @if(!($isPdf ?? false))
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Print Dokumen BAPPM</button>
    </div>
    @endif

    <!-- Kop Surat -->
    <div class="header-kop">
        <p>KEPOLISIAN NEGARA REPUBLIK INDONESIA</p>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DAERAH JAWA TIMUR</p>
        <p class="underline-text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DIREKTORAT LALU LINTAS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
    </div>

    <!-- Title -->
    <div class="sppm-title">
        <h2>BERITA ACARA PENGUJIAN PENERIMAAN/PEMERIKSAAN MATERIEL</h2>
        <p>Nomor : {{ $reception->bappm_number ?? 'BAPPM /       /V/2026/Ditlantas' }}</p>
    </div>

    <!-- Narrative -->
    <p style="text-indent: 30px; text-align: justify; margin-top: 10px; margin-bottom: 10px;">
        Pada hari ini <span style="font-weight: bold;">{{ \Carbon\Carbon::parse($reception->date)->translatedFormat('l') }}</span> tanggal <span style="font-weight: bold;">{{ \Carbon\Carbon::parse($reception->date)->translatedFormat('d F Y') }}</span>, kami yang bertanda tangan di bawah ini masing-masing:
    </p>

    <!-- Tim Komisi Table -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
        <thead>
            <tr style="border-bottom: 1px solid #000; border-top: 1px solid #000; background-color: #f9fafb;">
                <th style="width: 30px; text-align: center; padding: 5px; border: 1px solid #000;">NO</th>
                <th style="text-align: left; padding: 5px; border: 1px solid #000;">NAMA</th>
                <th style="width: 180px; text-align: left; padding: 5px; border: 1px solid #000;">PANGKAT / NRP</th>
                <th style="text-align: left; padding: 5px; border: 1px solid #000;">JABATAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 5px; text-align: center; border: 1px solid #000; vertical-align: top;">1.</td>
                <td style="padding: 5px; border: 1px solid #000; vertical-align: top; font-weight: bold;">{{ $reception->commission_member_1_name ?? '-' }}</td>
                <td style="padding: 5px; border: 1px solid #000; vertical-align: top;">{{ $reception->commission_member_1_rank ?? '-' }} / NRP {{ $reception->commission_member_1_nip ?? '-' }}</td>
                <td style="padding: 5px; border: 1px solid #000; vertical-align: top;">{{ $reception->commission_member_1_position ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 5px; text-align: center; border: 1px solid #000; vertical-align: top;">2.</td>
                <td style="padding: 5px; border: 1px solid #000; vertical-align: top; font-weight: bold;">{{ $reception->commission_member_2_name ?? '-' }}</td>
                <td style="padding: 5px; border: 1px solid #000; vertical-align: top;">{{ $reception->commission_member_2_rank ?? '-' }} / NRP {{ $reception->commission_member_2_nip ?? '-' }}</td>
                <td style="padding: 5px; border: 1px solid #000; vertical-align: top;">{{ $reception->commission_member_2_position ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 5px; text-align: center; border: 1px solid #000; vertical-align: top;">3.</td>
                <td style="padding: 5px; border: 1px solid #000; vertical-align: top; font-weight: bold;">{{ $reception->commission_member_3_name ?? '-' }}</td>
                <td style="padding: 5px; border: 1px solid #000; vertical-align: top;">{{ $reception->commission_member_3_rank ?? '-' }} / NRP {{ $reception->commission_member_3_nip ?? '-' }}</td>
                <td style="padding: 5px; border: 1px solid #000; vertical-align: top;">{{ $reception->commission_member_3_position ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <p style="text-indent: 30px; text-align: justify; margin-bottom: 15px;">
        Telah ditunjuk sebagai Tim Komisi Penguji Penerimaan Materiel berdasarkan Surat Keputusan Direktur Lalu Lintas Polda Jawa Timur, untuk memeriksa mutu serta jumlah materiel yang dimasukkan di gudang tersebut di atas berdasarkan Surat Perintah Pengeluaran Materiel Korlantas Polri Nomor : <span style="font-weight: bold;">{{ $reception->code }}</span> tanggal <span style="font-weight: bold;">{{ $reception->sppm_date ? \Carbon\Carbon::parse($reception->sppm_date)->translatedFormat('d F Y') : '-' }}</span> oleh Korlantas Polri.
    </p>

    <p style="margin-bottom: 5px;">Hasil pengujian materiel adalah sebagai berikut :</p>
    <p style="margin-left: 15px; font-weight: bold; text-decoration: underline; margin-bottom: 10px;">Kesatu : materiel terdapat baik</p>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama dan Kode Materiel</th>
                <th style="width: 120px;">Banyaknya</th>
                <th>Terbilang</th>
                <th style="width: 80px;">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($receptionDetails as $group)
                <tr>
                    <td style="text-align: center; vertical-align: top;">{{ $no++ }}</td>
                    <td style="font-weight: bold;">{{ $group['name'] }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($group['total_quantity'], 0, ',', '.') }}</td>
                    <td style="text-transform: capitalize; font-size: 10px;">{{ strtolower($reception->terbilang($group['total_quantity'])) }}</td>
                    <td style="text-align: center;">Lbr/Set/Pasang</td>
                </tr>
                @foreach($group['sub_items'] as $sub)
                    <tr>
                        <td></td>
                        <td style="padding-left: 20px; font-style: italic;">- {{ $sub['name'] }}</td>
                        <td style="text-align: right; font-style: italic;">{{ number_format($sub['quantity'], 0, ',', '.') }}</td>
                        <td style="text-transform: capitalize; font-size: 10px; font-style: italic;">{{ strtolower($reception->terbilang($sub['quantity'])) }}</td>
                        <td style="text-align: center; font-style: italic;">Lbr/Set</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; height: 80px;">Tidak ada detail penerimaan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p style="margin-left: 15px; font-weight: bold; text-decoration: underline; margin-top: 10px; margin-bottom: 10px;">Kedua : materiel terdapat tidak baik : NIHIL</p>

    <p style="text-align: justify; margin-bottom: 25px;">
        Berita Acara ini dibuat dengan sebenarnya dalam rangkap 2 (dua) dan bila pernyataan dalam Berita Acara ini ternyata tidak benar, Tim Komisi akan mempertanggungjawabkan serta bersedia menerima segala tindakan yang diambil.
    </p>

    <!-- Signatures Section -->
    <div style="margin-top: 30px; page-break-inside: avoid;">
        <table style="width: 100%; border: none;">
            <tr>
                <!-- Kasi Fasmat (Kiri) -->
                <td style="width: 45%; vertical-align: top; text-align: left; padding-right: 30px;">
                    <p style="margin-bottom: 0;">Bukti Pemasukan Nomor : __________________</p>
                    <p style="margin-top: 2px;">Materiel terdapat baik, telah kami terima dan dibukukan dalam pertanggungjawaban.</p>
                    <p style="margin-top: 15px; margin-bottom: 55px;">
                        Surabaya, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ \Carbon\Carbon::parse($reception->date)->translatedFormat('F Y') }}<br>
                        <span style="font-weight: bold;">KASI FASMAT SBST DITLANTAS POLDA JATIM</span>
                    </p>
                    <p style="font-weight: bold; text-decoration: underline; margin-bottom: 0;">{{ $reception->kasi_fasmat_name ?? 'AYIP RIZAL, S.E., M.M.' }}</p>
                    <p style="margin-top: 2px;">{{ $reception->kasi_fasmat_rank ?? 'KOMPOL' }} NRP {{ $reception->kasi_fasmat_nip ?? '84091823' }}</p>
                </td>
                
                <!-- Spacer -->
                <td style="width: 10%;"></td>
                
                <!-- Tim Komisi (Kanan) -->
                <td style="width: 45%; vertical-align: top; text-align: left;">
                    <p style="text-align: center; font-weight: bold; margin-bottom: 25px;">TIM KOMISI</p>
                    
                    <div style="margin-bottom: 15px;">
                        <span style="display: inline-block; width: 20px;">1.</span>
                        <div style="display: inline-block; vertical-align: top;">
                            <span style="font-weight: bold; text-decoration: underline;">{{ $reception->commission_member_1_name ?? '-' }}</span><br>
                            {{ $reception->commission_member_1_rank ?? '-' }} NRP {{ $reception->commission_member_1_nip ?? '-' }}
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <span style="display: inline-block; width: 20px;">2.</span>
                        <div style="display: inline-block; vertical-align: top;">
                            <span style="font-weight: bold; text-decoration: underline;">{{ $reception->commission_member_2_name ?? '-' }}</span><br>
                            {{ $reception->commission_member_2_rank ?? '-' }} NRP {{ $reception->commission_member_2_nip ?? '-' }}
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <span style="display: inline-block; width: 20px;">3.</span>
                        <div style="display: inline-block; vertical-align: top;">
                            <span style="font-weight: bold; text-decoration: underline;">{{ $reception->commission_member_3_name ?? '-' }}</span><br>
                            {{ $reception->commission_member_3_rank ?? '-' }} NRP {{ $reception->commission_member_3_nip ?? '-' }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Ordonatur (Tengah Bawah) -->
        <div style="margin-top: 30px; text-align: center;">
            <p style="margin-bottom: 5px;">Surabaya, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ \Carbon\Carbon::parse($reception->date)->translatedFormat('F Y') }}</p>
            <p style="font-weight: bold; margin-bottom: 55px;">
                MENGETAHUI<br>
                ORDONATUR<br>
                DIREKTUR LALU LINTAS POLDA JAWA TIMUR
            </p>
            <p style="font-weight: bold; text-decoration: underline; margin-bottom: 0;">{{ $reception->ordonatur_name ?? 'IWAN SAKTIADI, S.I.K., M.M., M.Si' }}</p>
            <p style="margin-top: 2px;">{{ $reception->ordonatur_rank ?? 'BRIGADIR JENDERAL POLISI' }}</p>
        </div>
    </div>
</div>
