<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">Anev Ketertiban Laporan</h1>
                <p class="text-gray-500 mt-1">Monitoring ketertiban input laporan operasional Polda dan Polres jajaran Polda Jatim</p>
            </div>
            <div class="flex flex-wrap items-center gap-2" x-data="{ copied: false }">
                <textarea x-ref="waRawText" class="sr-only" aria-hidden="true">{{ $waText }}</textarea>

                {{--<button type="button"
                    @click="navigator.clipboard.writeText($refs.waRawText.value); copied = true; setTimeout(() => copied = false, 2500)"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-emerald-500/20 transition-all duration-200 transform hover:scale-105 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>
                    <span x-show="!copied">Salin Format WA</span>
                    <span x-show="copied" x-cloak class="font-bold">✓ Tersalin!</span>
                </button>--}}

                <button type="button" wire:click="exportExecutivePdf" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-red-600 to-rose-500 hover:from-red-700 hover:to-rose-600 text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-red-500/20 transition-all duration-200 transform hover:scale-105 text-sm disabled:opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" />
                    </svg>
                    <span wire:loading.remove wire:target="exportExecutivePdf">Export PDF Resume</span>
                    <span wire:loading wire:target="exportExecutivePdf">Menyiapkan PDF...</span>
                </button>

                <button type="button" wire:click="exportExecutiveExcel" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-emerald-500 hover:from-green-700 hover:to-emerald-600 text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-green-500/20 transition-all duration-200 transform hover:scale-105 text-sm disabled:opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    <span wire:loading.remove wire:target="exportExecutiveExcel">Export Excel Resume</span>
                    <span wire:loading wire:target="exportExecutiveExcel">Menyiapkan Excel...</span>
                </button>

                <button type="button" wire:click="exportExcel" wire:loading.attr="disabled"
                    @disabled($dateError || $sourceError)
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-blue-500/20 transition-all duration-200 transform hover:scale-105 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V3zm2 3v10h10V6H5z" clip-rule="evenodd" />
                    </svg>
                    <span wire:loading.remove wire:target="exportExcel">Export Tabel Rekap</span>
                    <span wire:loading wire:target="exportExcel">Mengunduh...</span>
                </button>
            </div>
        </div>
        @error('status')<p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
    </div>

    <!-- Executive Brief Resume (Format Kasi Fasmat Ditlantas Polda Jatim) -->
    <div class="mb-6 bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-blue-100 overflow-hidden" x-data="{ expanded: true }">
        <div class="p-5 bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold tracking-wider text-blue-300 uppercase">Laporan Eksekutif Harian</span>
                    <h2 class="text-lg font-bold text-white tracking-wide">Kepada : Kasi Fasmat Ditlantas Polda Jatim</h2>
                    <p class="text-xs text-blue-200 mt-0.5">Resume Analisa & Evaluasi (ANEV) Kepatuhan & Persediaan Material Tahun {{ $executiveData['year'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-800/60 text-blue-200 border border-blue-700">
                    Posisi: {{ $executiveData['time_str'] }}
                </span>
                <button type="button" @click="expanded = !expanded" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-200" :class="{'rotate-180': !expanded}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="expanded" x-collapse class="p-6 space-y-6">
            <!-- Grid Sections A, B, C, D -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <!-- A. Capaian Target PNBP -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50/50 rounded-xl p-4 border border-blue-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-blue-700 tracking-wider">A. TARGET PNBP {{ $executiveData['year'] }}</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-blue-200/60 text-blue-800">{{ str_replace('.', ',', (string)$executiveData['pct_pnbp']) }}%</span>
                        </div>
                        <div class="space-y-1.5 text-xs text-gray-700">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Target:</span>
                                <span class="font-semibold">Rp{{ number_format($executiveData['target_pnbp'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Realisasi:</span>
                                <span class="font-bold text-blue-900">Rp{{ number_format($executiveData['realized_pnbp'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between pt-1 border-t border-blue-200/60 text-amber-900">
                                <span class="text-gray-500">Sisa Target:</span>
                                <span class="font-medium">Rp{{ number_format($executiveData['remaining_pnbp'], 0, ',', '.') }} ({{ str_replace('.', ',', (string)$executiveData['pct_remaining_pnbp']) }}%)</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full bg-blue-200 rounded-full h-1.5 mt-3 overflow-hidden">
                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $executiveData['pct_pnbp']) }}%"></div>
                    </div>
                </div>

                <!-- B. Capaian Penggunaan Material (Gunmat) -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50/50 rounded-xl p-4 border border-emerald-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-emerald-700 tracking-wider">B. PENGGUNAAN MATERIAL (GUNMAT)</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-200/60 text-emerald-800">{{ str_replace('.', ',', (string)$executiveData['pct_gunmat']) }}%</span>
                        </div>
                        <div class="space-y-1.5 text-xs text-gray-700">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Target Renbut:</span>
                                <span class="font-semibold">{{ number_format($executiveData['target_gunmat'], 0, ',', '.') }} unit</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Realisasi Pakai:</span>
                                <span class="font-bold text-emerald-900">{{ number_format($executiveData['realized_gunmat'], 0, ',', '.') }} unit</span>
                            </div>
                            <div class="flex justify-between pt-1 border-t border-emerald-200/60 text-teal-900">
                                <span class="text-gray-500">Sisa Kebutuhan:</span>
                                <span class="font-medium">{{ number_format($executiveData['remaining_gunmat'], 0, ',', '.') }} unit ({{ str_replace('.', ',', (string)$executiveData['pct_remaining_gunmat']) }}%)</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full bg-emerald-200 rounded-full h-1.5 mt-3 overflow-hidden">
                        <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ min(100, $executiveData['pct_gunmat']) }}%"></div>
                    </div>
                </div>

                <!-- C. Kondisi Stok Material -->
                <div class="bg-gradient-to-br from-purple-50 to-violet-50/50 rounded-xl p-4 border border-purple-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-purple-700 tracking-wider">C. KONDISI STOK MATERIAL</span>
                            <span class="text-xs font-semibold text-purple-600">Total Terdaftar</span>
                        </div>
                        <div class="space-y-1.5 text-xs text-gray-700">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Stok Polda:</span>
                                <span class="font-bold text-purple-900">{{ number_format($executiveData['stock_polda'], 0, ',', '.') }} unit</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Stok Polres:</span>
                                <span class="font-bold text-indigo-900">{{ number_format($executiveData['stock_polres'], 0, ',', '.') }} unit</span>
                            </div>
                            <div class="flex justify-between pt-1 border-t border-purple-200/60">
                                <span class="text-gray-500">Material Rusak:</span>
                                <span class="font-bold text-rose-600">{{ number_format($executiveData['material_rusak'], 0, ',', '.') }} unit</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-[11px] text-purple-700 bg-purple-100/60 px-2 py-1 rounded text-center">
                        Total Fisik: {{ number_format($executiveData['stock_polda'] + $executiveData['stock_polres'], 0, ',', '.') }} unit
                    </div>
                </div>

                <!-- D. Anev Persediaan Warehouse -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50/50 rounded-xl p-4 border border-amber-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-amber-800 tracking-wider">D. GUDANG WAREHOUSE POLDA</span>
                            <span class="text-xs font-semibold text-amber-700">5 Komoditas</span>
                        </div>
                        <div class="space-y-1 text-xs text-gray-700">
                            @foreach ($executiveData['warehouse'] as $whKey => $whVal)
                                <div class="flex justify-between py-0.5">
                                    <span class="text-gray-500 font-medium">{{ $whKey }}:</span>
                                    <span class="font-semibold text-gray-900">{{ number_format($whVal, 0, ',', '.') }} unit</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-2 text-[11px] text-amber-800 bg-amber-100/60 px-2 py-1 rounded text-center">
                        Total Gudang Polda: {{ number_format(array_sum($executiveData['warehouse']), 0, ',', '.') }} unit
                    </div>
                </div>
            </div>

            <!-- Sections E & F: Ketertiban Input & Kesimpulan -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- E. Anev Ketertiban Input Gunmat -->
                <div class="bg-rose-50/60 rounded-xl p-5 border border-rose-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="flex h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                            <h3 class="text-sm font-bold text-rose-900 uppercase">E. KETERTIBAN INPUT GUNMAT (BELUM INPUT)</h3>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded bg-rose-200 text-rose-800">Pukul {{ $executiveData['time_str'] }}</span>
                    </div>

                    @if (empty($executiveData['missing_satker']))
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-xs font-semibold flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Nihil: Seluruh satker jajaran telah tertib menginput laporan operasional.
                        </div>
                    @else
                        <p class="text-xs text-rose-800 mb-2">Terdapat <strong>{{ $executiveData['satker_count'] }} satker</strong> yang belum menyelesaikan input laporan:</p>
                        <div class="max-h-48 overflow-y-auto space-y-1.5 pr-1">
                            @foreach ($executiveData['missing_satker'] as $ms)
                                <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-rose-100 text-xs">
                                    <div class="flex items-center gap-2 font-medium text-gray-800">
                                        <span class="text-rose-600 font-bold">{{ $loop->iteration }}.</span>
                                        <span>{{ $ms['name'] }}</span>
                                    </div>
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-rose-100 text-rose-700">
                                        tgl: {{ implode(', ', $ms['days']) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- F. Kesimpulan Anev -->
                <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="flex h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                            <h3 class="text-sm font-bold text-slate-900 uppercase">F. KESIMPULAN & REKOMENDASI ANEV</h3>
                        </div>
                        <ol class="space-y-2 text-xs text-slate-700">
                            <li class="flex items-start gap-2 bg-white p-3 rounded-lg border border-slate-200">
                                <span class="font-bold text-blue-600">1.</span>
                                <span>{{ $executiveData['kesimpulan_1'] }}</span>
                            </li>
                            <li class="flex items-start gap-2 bg-white p-3 rounded-lg border border-slate-200">
                                <span class="font-bold text-blue-600">2.</span>
                                <span>{{ $executiveData['kesimpulan_2'] }}</span>
                            </li>
                        </ol>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-[11px] text-slate-500">
                        <span>Ditlantas Polda Jatim · Subdit Regident · Seksi Fasmat</span>
                        <span>Otomatis dikompilasi oleh SiMMaster</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Absensi Penggunaan Material Polres (Redesigned per other report menus) -->
    <section class="mb-6 bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <!-- Header & Filter Bar -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50/80 to-white">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center p-2 rounded-xl bg-purple-100 text-purple-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Absensi Penggunaan Material Polres</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Matriks pemantauan input berkala material harian pada masing-masing Satpas & Satker jajaran</p>
                        </div>
                    </div>
                </div>
                <!-- Legend Badges -->
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-200 font-semibold shadow-xs">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                        ✓ Lengkap (Seluruh input terisi)
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-yellow-50 text-yellow-800 border border-yellow-200 font-semibold shadow-xs">
                        <span class="h-2 w-2 rounded-full bg-yellow-500"></span>
                        ✓ Terisi Sebagian
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 font-semibold shadow-xs">
                        <span class="h-2 w-2 rounded-full bg-red-500"></span>
                        ✕ Belum Input
                    </span>
                </div>
            </div>

            <!-- Modern Filter Controls -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-gray-100">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Periode Kalender</label>
                    <select wire:model.live="period" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white">
                        <option value="daily">Harian (5 Hari Terakhir)</option>
                        <option value="weekly">Mingguan (Minggu Berjalan)</option>
                        <option value="monthly">Bulanan (Bulan Berjalan)</option>
                        <option value="custom">Rentang Tanggal Khusus</option>
                    </select>
                </div>

                @if($period === 'daily')
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Acuan</label>
                        <input type="date" wire:model.live="date" max="{{ $today }}"
                            class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white">
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                        <input type="date" wire:model.live="startDate" max="{{ $today }}"
                            class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                        <input type="date" wire:model.live="endDate" max="{{ $today }}"
                            class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white">
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Cari Satker / Polres</label>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari nama wilayah..."
                        class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white">
                </div>
            </div>
        </div>

        <!-- Matrix Calendar Table -->
        @if($calendar['error'])
            <div class="p-6 text-center">
                <p role="alert" class="text-sm font-semibold text-rose-600 bg-rose-50 p-4 rounded-xl border border-rose-200">{{ $calendar['error'] }}</p>
            </div>
        @else
            <div class="overflow-x-auto" wire:loading.class="opacity-60">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-slate-100 border-b border-gray-200 text-gray-700">
                            <th class="sticky left-0 z-20 bg-slate-100/95 backdrop-blur-sm px-4 py-3 text-left font-bold text-xs uppercase tracking-wider border-r border-gray-200 min-w-[200px] shadow-xs">
                                Satuan Kerja / Polres
                            </th>
                            @foreach($calendar['days'] as $day)
                                @php
                                    $carbonDay = \Carbon\Carbon::parse($day);
                                    $isSunday = $carbonDay->isSunday();
                                @endphp
                                <th class="px-3 py-2.5 text-center whitespace-nowrap font-bold text-xs border-r border-gray-100 last:border-r-0 {{ $isSunday ? 'text-rose-600 bg-rose-50/50' : 'text-gray-700' }}">
                                    <div>{{ $carbonDay->translatedFormat('D') }}</div>
                                    <div class="text-[11px] font-normal text-gray-500">{{ $carbonDay->format('d/m') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($calendar['rows'] as $row)
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="sticky left-0 z-10 bg-white px-4 py-2.5 whitespace-nowrap text-xs font-semibold text-gray-900 border-r border-gray-200 shadow-xs">
                                    {{ $row['name'] }}
                                </td>
                                @foreach($row['cells'] as $day => $cell)
                                    <td class="px-2 py-2 text-center border-r border-gray-100 last:border-r-0">
                                        <div title="{{ $day }}: {{ $cell['label'] }}"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold transition-transform hover:scale-110 shadow-xs {{ $cell['complete'] ? 'bg-emerald-500 text-white shadow-emerald-500/20' : ($cell['reported'] ? 'bg-amber-400 text-slate-900 shadow-amber-400/20' : 'bg-rose-100 text-rose-700 border border-rose-200') }}">
                                            {{ $cell['reported'] ? '✓' : '✕' }}
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($calendar['days']) + 1 }}" class="p-8 text-center text-gray-500 text-sm">
                                    Tidak ada data satker yang sesuai dengan pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 bg-gray-50/60 text-xs text-gray-500 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <span>Total <strong>{{ $calendar['rows']->count() }} satker</strong> dipantau pada rentang waktu ini.</span>
                <span class="text-gray-400">Status diperbarui otomatis berdasarkan input transaksi harian.</span>
            </div>
        @endif
    </section>

    <!-- Operational Summary KPI Cards -->
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
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Wilayah</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Selisih hari</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Input terakhir (WIB)</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jumlah input</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Rincian jenis input</th>
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
