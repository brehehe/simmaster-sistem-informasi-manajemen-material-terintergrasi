<table>
    <!-- Kop Surat -->
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 11pt; text-align: left;">KEPOLISIAN NEGARA REPUBLIK INDONESIA</td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 11pt; text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DAERAH JAWA TIMUR</td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 11pt; text-align: left; text-decoration: underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DIREKTORAT LALU LINTAS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    
    <!-- Spacers -->
    <tr><td colspan="5"></td></tr>
    
    <!-- Title -->
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 13pt; text-align: center; text-decoration: underline;">BERITA ACARA PENGUJIAN PENERIMAAN/PEMERIKSAAN MATERIEL</td>
    </tr>
    <tr>
        <td colspan="5" style="font-size: 11pt; text-align: center;">Nomor : {{ $reception->bappm_number ?? 'BAPPM /       /V/2026/Ditlantas' }}</td>
    </tr>
    
    <tr><td colspan="5"></td></tr>
    
    <!-- Narrative -->
    <tr>
        <td colspan="5" style="text-align: justify; font-size: 11pt;">Pada hari ini <strong>{{ \Carbon\Carbon::parse($reception->date)->translatedFormat('l') }}</strong> tanggal <strong>{{ \Carbon\Carbon::parse($reception->date)->translatedFormat('d F Y') }}</strong>, kami yang bertanda tangan di bawah ini masing-masing:</td>
    </tr>
    
    <tr><td colspan="5"></td></tr>
    
    <!-- Tim Komisi Table -->
    <tr style="background-color: #F3F4F6;">
        <th style="border: 1px solid #000000; font-weight: bold; text-align: center; font-size: 11pt;">NO</th>
        <th style="border: 1px solid #000000; font-weight: bold; text-align: left; font-size: 11pt;" colspan="2">NAMA</th>
        <th style="border: 1px solid #000000; font-weight: bold; text-align: left; font-size: 11pt;">PANGKAT / NRP</th>
        <th style="border: 1px solid #000000; font-weight: bold; text-align: left; font-size: 11pt;">JABATAN</th>
    </tr>
    <tr>
        <td style="border: 1px solid #000000; text-align: center; font-size: 11pt;">1</td>
        <td style="border: 1px solid #000000; font-weight: bold; font-size: 11pt;" colspan="2">{{ $reception->commission_member_1_name ?? '-' }}</td>
        <td style="border: 1px solid #000000; font-size: 11pt;">{{ $reception->commission_member_1_rank ?? '-' }} / NRP {{ $reception->commission_member_1_nip ?? '-' }}</td>
        <td style="border: 1px solid #000000; font-size: 11pt;">{{ $reception->commission_member_1_position ?? '-' }}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000000; text-align: center; font-size: 11pt;">2</td>
        <td style="border: 1px solid #000000; font-weight: bold; font-size: 11pt;" colspan="2">{{ $reception->commission_member_2_name ?? '-' }}</td>
        <td style="border: 1px solid #000000; font-size: 11pt;">{{ $reception->commission_member_2_rank ?? '-' }} / NRP {{ $reception->commission_member_2_nip ?? '-' }}</td>
        <td style="border: 1px solid #000000; font-size: 11pt;">{{ $reception->commission_member_2_position ?? '-' }}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000000; text-align: center; font-size: 11pt;">3</td>
        <td style="border: 1px solid #000000; font-weight: bold; font-size: 11pt;" colspan="2">{{ $reception->commission_member_3_name ?? '-' }}</td>
        <td style="border: 1px solid #000000; font-size: 11pt;">{{ $reception->commission_member_3_rank ?? '-' }} / NRP {{ $reception->commission_member_3_nip ?? '-' }}</td>
        <td style="border: 1px solid #000000; font-size: 11pt;">{{ $reception->commission_member_3_position ?? '-' }}</td>
    </tr>
    
    <tr><td colspan="5"></td></tr>
    
    <!-- Commission Scope -->
    <tr>
        <td colspan="5" style="text-align: justify; font-size: 11pt;">Telah ditunjuk sebagai Tim Komisi Penguji Penerimaan Materiel berdasarkan Surat Keputusan Direktur Lalu Lintas Polda Jawa Timur, untuk memeriksa mutu serta jumlah materiel yang dimasukkan di gudang tersebut di atas berdasarkan Surat Perintah Pengeluaran Materiel Korlantas Polri Nomor : <strong>{{ $reception->code }}</strong> tanggal <strong>{{ $reception->sppm_date ? \Carbon\Carbon::parse($reception->sppm_date)->translatedFormat('d F Y') : '-' }}</strong> oleh Korlantas Polri.</td>
    </tr>
    
    <tr><td colspan="5"></td></tr>
    
    <tr>
        <td colspan="5" style="font-size: 11pt;">Hasil pengujian materiel adalah sebagai berikut :</td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold; text-decoration: underline; font-size: 11pt;">Kesatu : materiel terdapat baik</td>
    </tr>
    
    <tr><td colspan="5"></td></tr>
    
    <!-- Data Table -->
    <tr style="background-color: #F3F4F6;">
        <th style="border: 1px solid #000000; font-weight: bold; text-align: center; font-size: 11pt;">No</th>
        <th style="border: 1px solid #000000; font-weight: bold; text-align: left; font-size: 11pt;">Nama dan Kode Materiel</th>
        <th style="border: 1px solid #000000; font-weight: bold; text-align: right; font-size: 11pt;">Banyaknya</th>
        <th style="border: 1px solid #000000; font-weight: bold; text-align: left; font-size: 11pt;">Terbilang</th>
        <th style="border: 1px solid #000000; font-weight: bold; text-align: center; font-size: 11pt;">Satuan</th>
    </tr>
    
    @php $no = 1; @endphp
    @forelse($receptionDetails as $group)
        <tr>
            <td style="border: 1px solid #000000; text-align: center; font-size: 11pt; vertical-align: top;">{{ $no++ }}</td>
            <td style="border: 1px solid #000000; font-weight: bold; font-size: 11pt;">{{ $group['name'] }}</td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold; font-size: 11pt;">{{ number_format($group['total_quantity'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; font-size: 10pt; text-transform: capitalize;">{{ strtolower($reception->terbilang($group['total_quantity'])) }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-size: 11pt;">Lbr/Set/Pasang</td>
        </tr>
        @foreach($group['sub_items'] as $sub)
            <tr>
                <td style="border: 1px solid #000000; text-align: center; font-size: 11pt;"></td>
                <td style="border: 1px solid #000000; font-style: italic; font-size: 11pt; padding-left: 15px;">- {{ $sub['name'] }}</td>
                <td style="border: 1px solid #000000; text-align: right; font-style: italic; font-size: 11pt;">{{ number_format($sub['quantity'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000000; font-style: italic; font-size: 10pt; text-transform: capitalize;">{{ strtolower($reception->terbilang($sub['quantity'])) }}</td>
                <td style="border: 1px solid #000000; text-align: center; font-style: italic; font-size: 11pt;">Lbr/Set</td>
            </tr>
        @endforeach
    @empty
        <tr>
            <td colspan="5" style="border: 1px solid #000000; text-align: center; font-size: 11pt;">Tidak ada detail penerimaan.</td>
        </tr>
    @endforelse
    
    <tr><td colspan="5"></td></tr>
    
    <tr>
        <td colspan="5" style="font-weight: bold; text-decoration: underline; font-size: 11pt;">Kedua : materiel terdapat tidak baik : NIHIL</td>
    </tr>
    
    <tr><td colspan="5"></td></tr>
    
    <tr>
        <td colspan="5" style="text-align: justify; font-size: 11pt;">Berita Acara ini dibuat dengan sebenarnya dalam rangkap 2 (dua) dan bila pernyataan dalam Berita Acara ini ternyata tidak benar, Tim Komisi akan mempertanggungjawabkan serta bersedia menerima segala tindakan yang diambil.</td>
    </tr>
    
    <tr><td colspan="5"></td></tr>
    <tr><td colspan="5"></td></tr>
    
    <!-- Signatures Section -->
    <!-- Row 1: Titles -->
    <tr>
        <td colspan="2" style="font-size: 11pt; text-align: left; vertical-align: top;">
            Bukti Pemasukan Nomor : __________________<br>
            Materiel terdapat baik, telah kami terima dan dibukukan dalam pertanggungjawaban.
        </td>
        <td></td>
        <td colspan="2" style="font-weight: bold; font-size: 11pt; text-align: center; vertical-align: top;">
            TIM KOMISI
        </td>
    </tr>
    
    <!-- Row 2: Dates and Member 1 -->
    <tr>
        <td colspan="2" style="font-size: 11pt; text-align: left; vertical-align: top;">
            Surabaya, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ \Carbon\Carbon::parse($reception->date)->translatedFormat('F Y') }}<br>
            <strong>KASI FASMAT SBST DITLANTAS POLDA JATIM</strong>
        </td>
        <td></td>
        <td colspan="2" style="font-size: 11pt; text-align: left; vertical-align: top;">
            1. &nbsp; <u><strong>{{ $reception->commission_member_1_name ?? '-' }}</strong></u><br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $reception->commission_member_1_rank ?? '-' }} NRP {{ $reception->commission_member_1_nip ?? '-' }}
        </td>
    </tr>
    
    <!-- Row 3: Blank/Spacing / Member 2 -->
    <tr>
        <td colspan="2"></td>
        <td></td>
        <td colspan="2" style="font-size: 11pt; text-align: left; vertical-align: top;">
            2. &nbsp; <u><strong>{{ $reception->commission_member_2_name ?? '-' }}</strong></u><br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $reception->commission_member_2_rank ?? '-' }} NRP {{ $reception->commission_member_2_nip ?? '-' }}
        </td>
    </tr>
    
    <!-- Row 4: Names & Signature and Member 3 -->
    <tr>
        <td colspan="2" style="font-size: 11pt; text-align: left; vertical-align: bottom;">
            <u><strong>{{ $reception->kasi_fasmat_name ?? 'AYIP RIZAL, S.E., M.M.' }}</strong></u><br>
            {{ $reception->kasi_fasmat_rank ?? 'KOMPOL' }} NRP {{ $reception->kasi_fasmat_nip ?? '84091823' }}
        </td>
        <td></td>
        <td colspan="2" style="font-size: 11pt; text-align: left; vertical-align: top;">
            3. &nbsp; <u><strong>{{ $reception->commission_member_3_name ?? '-' }}</strong></u><br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $reception->commission_member_3_rank ?? '-' }} NRP {{ $reception->commission_member_3_nip ?? '-' }}
        </td>
    </tr>
    
    <tr><td colspan="5"></td></tr>
    <tr><td colspan="5"></td></tr>
    
    <!-- Row 5: Ordonatur Header -->
    <tr>
        <td colspan="5" style="font-size: 11pt; text-align: center;">
            Surabaya, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ \Carbon\Carbon::parse($reception->date)->translatedFormat('F Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 11pt; text-align: center;">
            MENGETAHUI<br>
            ORDONATUR<br>
            DIREKTUR LALU LINTAS POLDA JAWA TIMUR
        </td>
    </tr>
    
    <!-- Row 6: Ordonatur Spacing -->
    <tr><td colspan="5"></td></tr>
    <tr><td colspan="5"></td></tr>
    <tr><td colspan="5"></td></tr>
    
    <!-- Row 7: Ordonatur Name -->
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 11pt; text-align: center; text-decoration: underline;">
            {{ $reception->ordonatur_name ?? 'IWAN SAKTIADI, S.I.K., M.M., M.Si' }}
        </td>
    </tr>
    <tr>
        <td colspan="5" style="font-size: 11pt; text-align: center;">
            {{ $reception->ordonatur_rank ?? 'BRIGADIR JENDERAL POLISI' }}
        </td>
    </tr>
</table>
