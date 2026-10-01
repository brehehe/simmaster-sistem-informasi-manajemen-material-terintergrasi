<div>
    <!-- Header: same hierarchy as the other report pages -->
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">Anev Ketertiban Laporan</h1>
                <p class="text-gray-500 mt-1">Monitoring ketertiban input laporan operasional Polda dan Polres</p>
            </div>
            <button type="button" wire:click="exportExcel" wire:loading.attr="disabled"
                @disabled($dateError || $sourceError)
                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
                <span wire:loading wire:target="exportExcel">Menyiapkan Excel...</span>
            </button>
        </div>
        @error('status')<p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
    </div>

    <section class="mb-6 rounded-xl border bg-white p-4">
        <h2 class="text-xl font-bold mb-3">Absensi Penggunaan Material Polres</h2>
        <div class="flex flex-wrap gap-4 mb-4">
            <label>Periode<select wire:model.live="period" class="block rounded border p-2"><option value="daily">Harian</option><option value="weekly">Mingguan</option><option value="monthly">Bulanan</option><option value="custom">Rentang tanggal</option></select></label>
            @if($period === 'daily')<label>Tanggal<input type="date" wire:model.live="date" max="{{ $today }}" class="block rounded border p-2"></label>
            @else<label>Dari<input type="date" wire:model.live="startDate" max="{{ $today }}" class="block rounded border p-2"></label><label>Sampai<input type="date" wire:model.live="endDate" max="{{ $today }}" class="block rounded border p-2"></label>@endif
        </div>
        <p class="text-sm mb-3">✓ Hijau: seluruh material terisi, termasuk nilai 0. ✓ Kuning: sudah input tetapi belum lengkap. ✕ Merah: belum input.</p>
        @if($calendar['error'])<p role="alert" class="text-red-600">{{ $calendar['error'] }}</p>@else
        <div class="overflow-x-auto"><table class="w-full text-sm border-collapse"><thead><tr><th class="border p-2 text-left">Polres</th>@foreach($calendar['days'] as $day)<th class="border p-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($day)->format('d/m') }}</th>@endforeach</tr></thead><tbody>
            @foreach($calendar['rows'] as $row)<tr><td class="border p-2 whitespace-nowrap">{{ $row['name'] }}</td>@foreach($row['cells'] as $day => $cell)<td title="{{ $day }}: {{ $cell['label'] }}" aria-label="{{ $day }}: {{ $cell['label'] }}" class="border p-2 text-center font-bold {{ $cell['complete'] ? 'bg-green-100 text-green-800' : ($cell['reported'] ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $cell['reported'] ? '✓' : '✕' }}</td>@endforeach</tr>@endforeach
        </tbody></table></div>@endif
    </section>
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
        @foreach ([
            ['total', 'Wilayah Aktif', 'from-blue-500 to-indigo-600 shadow-blue-500/30', 'text-blue-100', 'M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H2v-2a3 3 0 015.356-1.857M9 20v-2a4 4 0 018 0v2H9zm7-13a4 4 0 11-8 0 4 4 0 018 0z'],
            ['green', 'Tertib · Hari yang Sama', 'from-green-500 to-emerald-600 shadow-green-500/30', 'text-green-100', 'M5 13l4 4L19 7'],
            ['yellow', 'Selisih 1–2 Hari', 'from-amber-500 to-orange-600 shadow-amber-500/30', 'text-amber-100', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['red', 'Selisih ≥3 Hari', 'from-red-500 to-rose-600 shadow-red-500/30', 'text-red-100', 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'],
            ['missing', 'Belum Input', 'from-gray-500 to-gray-600 shadow-gray-500/30', 'text-gray-100', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ] as [$key, $label, $gradient, $labelColor, $icon])
            <div class="bg-gradient-to-br {{ $gradient }} rounded-2xl p-5 text-white shadow-lg">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="{{ $labelColor }} text-sm">{{ $label }}</p>
                        <p class="text-3xl font-bold mt-1">{{ number_format($summary[$key], 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white/20 rounded-xl p-3 shrink-0">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Table Card with Search & Filter -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label for="anev-date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal laporan</label>
                    <input id="anev-date" type="date" max="{{ $today }}" wire:model.live="date" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    @if ($dateError)<p class="mt-1 text-sm text-red-700" role="alert">{{ $dateError }}</p>@endif
                </div>
                <div>
                    <label for="anev-search" class="block text-sm font-semibold text-gray-700 mb-2">Cari wilayah</label>
                    <input id="anev-search" type="search" wire:model.live.debounce.400ms="search" placeholder="Nama Polres / wilayah" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="anev-source" class="block text-sm font-semibold text-gray-700 mb-2">Jenis input</label>
                    <select id="anev-source" wire:model.live="source" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <option value="all">Semua input operasional</option>
                        @foreach ($sources as $key => $definition)
                            <option value="{{ $key }}">{{ $definition['label'] }}</option>
                        @endforeach
                    </select>
                    @if ($sourceError)<p class="mt-1 text-sm text-red-700" role="alert">{{ $sourceError }}</p>@endif
                </div>
                <div>
                    <label for="anev-status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select id="anev-status" wire:model.live="status" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <option value="">Semua status</option>
                        <option value="missing">Belum input</option>
                        <option value="green">Hijau · Tertib</option>
                        <option value="yellow">Kuning · 1–2 hari</option>
                        <option value="red">Merah · 3 hari atau lebih</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500">
                <p>Ringkasan seluruh wilayah pada tanggal dan jenis input terpilih.</p>
                <span wire:loading role="status" class="text-blue-600">Memuat rekap wilayah...</span>
            </div>
        </div>
        <div class="overflow-x-auto" wire:loading.class="opacity-50">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100"><tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Wilayah</th><th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Selisih hari</th><th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Input terakhir (WIB)</th><th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jumlah input</th><th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Rincian jenis input</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($rows as $row)
                    <tr wire:key="anev-{{ $row['id'] }}" class="hover:bg-blue-50/50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $row['name'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span @class(['inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-lg text-xs font-semibold', 'bg-green-100 text-green-800' => $row['color'] === 'green', 'bg-yellow-100 text-yellow-800' => $row['color'] === 'yellow', 'bg-red-100 text-red-800' => $row['color'] === 'red', 'bg-gray-100 text-gray-700' => $row['color'] === 'pending'])>{{ $row['label'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $row['delay'] }} hari{{ ! $row['reported'] ? ' · belum input' : '' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $row['submitted_at'] ?: '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600"><span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">{{ number_format($row['report_count'], 0, ',', '.') }} input</span></td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @forelse ($row['inputs'] as $key => $input)
                                <div class="whitespace-nowrap py-0.5 text-xs">{{ $sources[$key]['label'] }}: <strong>{{ $input['count'] }}</strong></div>
                            @empty
                                <span class="text-gray-500">Tidak ada input</span>
                            @endforelse
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">Tidak ada data Anev</p>
                                <p class="text-gray-400 text-sm mt-1">{{ $dateError ?: ($sourceError ?: 'Tidak ada wilayah yang sesuai filter.') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-sm text-gray-600">
            <p>Menampilkan <span class="font-semibold">{{ $rows->count() }}</span> dari <span class="font-semibold">{{ $summary['total'] }}</span> wilayah aktif</p>
            <p class="text-xs text-gray-500">Wilayah belum input dapat termasuk kategori kuning/merah.</p>
        </div>
    </div>
    <details class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-xl shadow-gray-200/50 p-4 text-sm text-gray-600">
        <summary class="cursor-pointer font-semibold text-blue-600 focus-visible:outline-2 focus-visible:outline-blue-500">Panduan penilaian Anev</summary>
        <div class="mt-3 space-y-2">
        <p><strong>Hijau:</strong> input pada hari laporan. <strong>Kuning:</strong> selisih 1–2 hari. <strong>Merah:</strong> selisih 3 hari atau lebih.</p>
        <p>Penilaian memakai tanggal laporan dan waktu input terakhir untuk tanggal tersebut (WIB). Wilayah belum input dihitung sampai hari ini; belum input hari ini berstatus menunggu, bukan hijau. Hari dihitung sebagai hari kalender, termasuk hari libur.</p>
        <p>Rekap mencakup dokumen tersimpan yang memiliki rincian, termasuk draft. Warna gabungan mengikuti input paling terlambat, bukan kelengkapan seluruh menu. Jenis transaksi insidental tanpa input ditandai abu-abu saat difilter. Pengiriman dan mutasi dihitung pada wilayah pengirim, bukan sebagai input wilayah penerima. Data master, pesan, serta riwayat stok otomatis tidak dihitung ulang. Semua akun yang sudah masuk dapat melihat rekap wilayah ini.</p>
    </div>
    </details>
</div>
