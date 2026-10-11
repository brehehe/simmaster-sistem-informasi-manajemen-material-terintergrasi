<div class="space-y-6 pb-12">
    <style>
        .whitespace-overwarp,
        .whitespace-overflow-wrap {
            white-space: normal !important;
            overflow-wrap: break-word !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
        }
    </style>

    <!-- Top Header: Title & Navigation -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1 whitespace-overwarp">
                <a href="{{ route('master.target') }}" class="hover:text-blue-600 transition-colors">Master Target</a>
                <span>/</span>
                <span class="text-blue-600 font-medium">Capaian Realisasi PNBP {{ $selectedYear }}</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight whitespace-overwarp">
                Capaian Target Realisasi PNBP TA {{ $selectedYear }}
            </h1>
            <p class="text-gray-500 text-sm mt-1 whitespace-overwarp">
                Rekapitulasi dan pemantauan per bulan capaian realisasi penerimaan negara bukan pajak Ditlantas Polda Jatim Tahun Anggaran {{ $selectedYear }}.
            </p>
        </div>

        <!-- Header Actions: Sync & Back -->
        <div class="flex items-center gap-2.5 shrink-0">
            <!-- Sync Button -->
            <button wire:click="syncNow" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 whitespace-nowrap">
                <svg wire:loading.remove wire:target="syncNow" class="h-4 w-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <svg wire:loading wire:target="syncNow" class="h-4 w-4 text-blue-600 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span>Sinkronisasi Data</span>
            </button>

            <!-- Back to Master Target -->
            <a href="{{ route('master.target') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all active:scale-95 whitespace-nowrap">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Master Target</span>
            </a>
        </div>
    </div>

    <!-- Dedicated Toolbar: Filter Tahun & CRUD Controls -->
    <div class="bg-white rounded-2xl border border-gray-200/80 p-3 sm:px-4 sm:py-3 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <!-- Left: Year Filter with Select Search -->
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider shrink-0">Filter Tahun:</span>
            </div>
            <div wire:ignore class="w-52 sm:w-60">
                <select id="select-year-capaian" x-data x-init="
                    const selectize = $($el).selectize({
                        dropdownParent: 'body',
                        allowClear: false,
                        placeholder: 'Cari / Pilih Tahun...',
                        onChange: function(val) {
                            if (val) {
                                @this.set('selectedYear', parseInt(val));
                            }
                        }
                    })[0].selectize;

                    $watch('$wire.selectedYear', function(newVal) {
                        if (selectize && selectize.getValue() != newVal) {
                            selectize.setValue(newVal, true);
                        }
                    });
                ">
                    @foreach ($availableYears as $yearOpt)
                        <option value="{{ $yearOpt }}" @selected($selectedYear == $yearOpt)>
                            Tahun {{ $yearOpt }} {{ ($yearOpt == now()->format('Y')) ? '(Tahun Ini)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Right: Target CRUD Controls for Selected Year -->
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-semibold text-gray-500 mr-1 hidden lg:inline">Aksi Target TA {{ $selectedYear }}:</span>

            <!-- Tambah Target -->
            <button wire:click="openCreateModal"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100 text-xs font-semibold transition-all active:scale-95 shadow-sm"
                title="Tambah Target Baru">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>Tambah Target</span>
            </button>

            <!-- Edit Target -->
            <button wire:click="openEditModal"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 hover:bg-amber-100 text-xs font-semibold transition-all active:scale-95 shadow-sm"
                title="Edit Target TA {{ $selectedYear }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                <span>Edit</span>
            </button>

            <!-- Hapus Target -->
            <button wire:click="openDeleteModal"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 text-xs font-semibold transition-all active:scale-95 shadow-sm"
                title="Hapus Target TA {{ $selectedYear }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>Hapus</span>
            </button>
        </div>
    </div>

    <!-- Notifications -->
    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-in fade-in duration-200 whitespace-overwarp">
            <div class="flex items-center gap-3">
                <div class="p-1.5 bg-emerald-100 rounded-lg text-emerald-600 shrink-0">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <span class="font-medium text-sm whitespace-overwarp">{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm animate-in fade-in duration-200 whitespace-overwarp">
            <div class="flex items-center gap-3">
                <div class="p-1.5 bg-rose-100 rounded-lg text-rose-600 shrink-0">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                </div>
                <span class="font-medium text-sm whitespace-overwarp">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Missing Excel Notice Banner -->
    @if (! $hasExcelFile)
        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex flex-col sm:flex-row sm:items-start gap-4 shadow-sm whitespace-overwarp">
            <div class="p-2 bg-amber-100 rounded-xl text-amber-700 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="space-y-1">
                <h4 class="text-base font-bold whitespace-overwarp">File Excel Capaian Belum Diunggah untuk Tahun Anggaran {{ $selectedYear }}</h4>
                <p class="text-xs text-amber-800 leading-relaxed whitespace-overwarp">
                    File buku capaian <code>CAPAIAN TARGET PNBP {{ $selectedYear }}.xlsx</code> belum tersedia di folder <code>public/</code>.
                    Sistem menampilkan data kuota target dari database ARMASTER (Total DB: <strong>{{ number_format($syncInfo['db_total_quantity'] ?? 0) }} Unit</strong> pada {{ $syncInfo['db_details_count'] ?? 0 }} baris rincian Polres).
                </p>
                <p class="text-xs text-amber-700 whitespace-overwarp">
                    Untuk menyambungkan data capaian {{ $selectedYear }}, unggah file Excel capaian dengan nama <code>CAPAIAN TARGET PNBP {{ $selectedYear }}.xlsx</code> ke folder <code>public/</code>.
                </p>
            </div>
        </div>
    @endif

    <!-- High-Impact Stat Cards (Refined & Proportional Font Size) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Target PNBP Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 p-4 sm:p-5 shadow-lg border border-blue-600/30 text-white min-h-[135px] flex flex-col justify-between">
            <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-200 whitespace-overwarp">Target PNBP {{ $selectedYear }}</p>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white/15 text-blue-100 shrink-0 whitespace-nowrap">TA {{ $selectedYear }}</span>
                </div>
                <div class="mt-2 flex items-baseline gap-1.5 flex-wrap">
                    <span class="text-xs font-semibold text-blue-200/90 shrink-0">Rp</span>
                    <span class="text-lg sm:text-xl font-bold tracking-tight text-white tabular-nums">
                        {{ number_format($syncInfo['excel_target_rp'] ?? 0) }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-blue-200/80 whitespace-overwarp">Total Pagu Target Ditlantas</p>
            </div>
        </div>

        <!-- Realisasi PNBP Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800 p-4 sm:p-5 shadow-lg border border-purple-500/30 text-white min-h-[135px] flex flex-col justify-between">
            <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs font-bold uppercase tracking-wider text-purple-200 whitespace-overwarp">Realisasi PNBP {{ $selectedYear }}</p>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-400/20 text-emerald-200 border border-emerald-400/30 shrink-0 whitespace-nowrap">
                        {{ $hasExcelFile ? 'Terealisasi' : 'Belum Ada' }}
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-1.5 flex-wrap">
                    <span class="text-xs font-semibold text-purple-200/90 shrink-0">Rp</span>
                    <span class="text-lg sm:text-xl font-bold tracking-tight text-white tabular-nums">
                        {{ number_format($syncInfo['excel_real_rp'] ?? 0) }}
                    </span>
                </div>
                <div class="mt-2.5 flex items-center gap-2.5">
                    <div class="flex-1 bg-black/25 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-400 to-cyan-300 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $syncInfo['excel_percentage'] ?? 0) }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-cyan-200 shrink-0 whitespace-nowrap tabular-nums">
                        {{ number_format($syncInfo['excel_percentage'] ?? 0, 2) }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Target Matreg Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-600 via-teal-700 to-blue-800 p-4 sm:p-5 shadow-lg border border-cyan-500/30 text-white min-h-[135px] flex flex-col justify-between">
            <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-200 whitespace-overwarp">Target Volume Matreg</p>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white/15 text-cyan-100 shrink-0 whitespace-nowrap">{{ $selectedYear }}</span>
                </div>
                <div class="mt-2 flex items-baseline gap-1.5 flex-wrap">
                    <span class="text-lg sm:text-xl font-bold tracking-tight text-white tabular-nums">
                        {{ number_format(($syncInfo['excel_target_qty'] > 0 ? $syncInfo['excel_target_qty'] : ($syncInfo['db_total_quantity'] ?? 0))) }}
                    </span>
                    <span class="text-xs font-medium text-cyan-200 shrink-0">Unit</span>
                </div>
                <p class="mt-1 text-xs text-cyan-200/80 whitespace-overwarp">Total Renbut Materiel se-Jawa Timur</p>
            </div>
        </div>

        <!-- Realisasi Gunmat Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 p-4 sm:p-5 shadow-lg border border-emerald-500/30 text-white min-h-[135px] flex flex-col justify-between">
            <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-200 whitespace-overwarp">Realisasi Volume Gunmat</p>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-900/60 text-emerald-200 shrink-0 whitespace-nowrap">{{ $selectedYear }}</span>
                </div>
                <div class="mt-2 flex items-baseline gap-1.5 flex-wrap">
                    <span class="text-lg sm:text-xl font-bold tracking-tight text-white tabular-nums">
                        {{ number_format($syncInfo['excel_real_qty'] ?? 0) }}
                    </span>
                    <span class="text-xs font-medium text-emerald-200 shrink-0">Unit</span>
                </div>
                <div class="mt-2.5 flex items-center justify-between text-xs text-emerald-100">
                    <span class="opacity-80">Capaian Volume:</span>
                    <span class="font-bold text-xs text-white shrink-0 whitespace-nowrap tabular-nums">
                        {{ ($syncInfo['excel_target_qty'] ?? 0) > 0 ? number_format((($syncInfo['excel_real_qty'] ?? 0) / $syncInfo['excel_target_qty']) * 100, 2) : 0 }}%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Per Sheet Bulanan (Horizontal Scrollable Table) -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-gradient-to-r from-gray-50 to-blue-50/30">
            <div>
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2 whitespace-overwarp">
                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Ringkasan Capaian Seluruh Sheet (Januari – Desember & Rekap TA {{ $selectedYear }})
                </h2>
                <p class="text-xs text-gray-500 mt-0.5 whitespace-overwarp">Klik salah satu bulan pada tombol navigasi di bawah untuk melihat rincian per jenis materiil.</p>
            </div>
            {{--<div class="flex items-center gap-2 text-xs text-gray-500 font-medium whitespace-overwarp">
                <span>Sumber: <code class="bg-gray-100 px-2 py-0.5 rounded text-blue-600 whitespace-overwarp">{{ $syncInfo['excel_file'] ?? "Belum ada file Excel TA {$selectedYear}" }}</code></span>
            </div>--}}
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-fixed min-w-[980px] text-left text-sm text-gray-600 divide-y divide-gray-100">
                <colgroup>
                    <col class="w-12">
                    <col class="w-44">
                    <col class="w-32">
                    <col class="w-44">
                    <col class="w-32">
                    <col class="w-44">
                    <col class="w-36">
                    <col class="w-32">
                    <col class="w-20">
                </colgroup>
                <thead class="bg-gray-50/80 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="py-3 px-3 text-center whitespace-overwarp">No</th>
                        <th class="py-3 px-3 whitespace-overwarp">Bulan / Sheet</th>
                        <th class="py-3 px-3 text-right whitespace-overwarp">Target (Vol)</th>
                        <th class="py-3 px-3 text-right whitespace-overwarp">Target (Rp)</th>
                        <th class="py-3 px-3 text-right whitespace-overwarp">Realisasi (Vol)</th>
                        <th class="py-3 px-3 text-right whitespace-overwarp">Realisasi (Rp)</th>
                        <th class="py-3 px-3 text-center whitespace-overwarp">% Capaian</th>
                        <th class="py-3 px-3 text-center whitespace-overwarp">Status</th>
                        <th class="py-3 px-3 text-center whitespace-overwarp">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($monthlySummaries as $row)
                        @php
                            $isSelected = $selectedMonth === $row['sheet_name'];
                            $isRekap = $row['sheet_name'] === 'REKAP';
                        @endphp
                        <tr class="transition-colors hover:bg-blue-50/50 {{ $isSelected ? 'bg-blue-50/80 font-medium' : ($isRekap ? 'bg-indigo-50/40 font-semibold' : '') }}">
                            <td class="py-3 px-3 text-center text-xs text-gray-400 whitespace-overwarp">{{ $row['index'] }}</td>
                            <td class="py-3 px-3 flex flex-wrap items-center gap-1.5 whitespace-overwarp">
                                <span class="font-bold {{ $isRekap ? 'text-indigo-700' : 'text-gray-900' }} whitespace-overwarp">{{ $row['label'] }}</span>
                                @if($isRekap)
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700 whitespace-overwarp">Total Akumulasi</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right whitespace-overwarp">{{ number_format($row['volume_target']) }}</td>
                            <td class="py-3 px-3 text-right font-medium text-gray-900 whitespace-overwarp">Rp {{ number_format($row['target_rp']) }}</td>
                            <td class="py-3 px-3 text-right whitespace-overwarp">{{ number_format($row['volume_realization']) }}</td>
                            <td class="py-3 px-3 text-right font-bold {{ $row['realization_rp'] > 0 ? 'text-emerald-600' : 'text-gray-400' }} whitespace-overwarp">
                                Rp {{ number_format($row['realization_rp']) }}
                            </td>
                            <td class="py-3 px-3 text-center whitespace-overwarp">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5 overflow-hidden shrink-0">
                                        <div class="h-1.5 rounded-full {{ $row['percentage'] > 0 ? 'bg-emerald-500' : 'bg-gray-300' }}"
                                            style="width: {{ min(100, $row['percentage']) }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold {{ $row['percentage'] > 0 ? 'text-emerald-700' : 'text-gray-400' }} whitespace-overwarp">
                                        {{ number_format($row['percentage'], 2) }}%
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center whitespace-overwarp">
                                @if($row['volume_realization'] > 0 || $row['realization_rp'] > 0)
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 whitespace-overwarp">
                                        Terealisasi
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-500 whitespace-overwarp">
                                        Belum Ada Data
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center whitespace-overwarp">
                                <button wire:click="setMonth('{{ $row['sheet_name'] }}')"
                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all whitespace-overwarp {{ $isSelected ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    {{ $isSelected ? 'Aktif' : 'Buka' }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sheet Navigation Tabs & Search Filter -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 whitespace-overwarp">
                    <span>Rincian Jenis Penerimaan Materiil:</span>
                    <span class="text-blue-600 underline uppercase font-black whitespace-overwarp">Sheet {{ $selectedMonth }} (TA {{ $selectedYear }})</span>
                </h3>
                <p class="text-xs text-gray-500 whitespace-overwarp">Pilih sheet di bawah untuk menampilkan 12 kategori materiil dan sub-kategorinya.</p>
            </div>

            <!-- Action: Tambah Item & Search Filter -->
            <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                <!-- Tambah Item Materiil Button -->
                <button wire:click="openCreateItemModal"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md shadow-blue-500/20 transition-all active:scale-95 whitespace-nowrap shrink-0"
                    title="Tambah Item Materiil PNBP Baru">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Tambah Item</span>
                </button>

                <!-- Search Filter -->
                <div class="relative w-full sm:w-64">
                    <input wire:model.live.debounce.250ms="search" type="text"
                        placeholder="Cari materiil (contoh: SIM A, STNK...)"
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-sm bg-white whitespace-overwarp">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Month Navigation Pills -->
        <div class="flex flex-wrap gap-1.5 p-1.5 bg-gray-100/80 rounded-2xl border border-gray-200">
            @foreach(\App\Services\PnbpTargetService::SHEETS as $sheetName)
                @php
                    $isActive = $selectedMonth === $sheetName;
                    $isRekapTab = $sheetName === 'REKAP';
                @endphp
                <button wire:click="setMonth('{{ $sheetName }}')"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors duration-150 whitespace-overwarp {{ $isActive ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : ($isRekapTab ? 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' : 'text-gray-600 hover:bg-white/80 hover:text-gray-900') }}">
                    {{ $sheetName }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Detailed Categorized Items Table (With Item CRUD Support) -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed min-w-[1060px] text-left text-sm text-gray-700 divide-y divide-gray-100">
                <colgroup>
                    <col class="w-16">
                    <col class="w-auto">
                    <col class="w-28">
                    <col class="w-28">
                    <col class="w-36">
                    <col class="w-28">
                    <col class="w-36">
                    <col class="w-24">
                    <col class="w-24">
                </colgroup>
                <thead class="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                    <tr>
                        <th class="py-3 px-3 text-center whitespace-overwarp">No</th>
                        <th class="py-3 px-3 whitespace-overwarp">Jenis Penerimaan PNBP</th>
                        <th class="py-3 px-3 text-right whitespace-overwarp">Tarif (Rp)</th>
                        <th class="py-3 px-3 text-right whitespace-overwarp">Target (Vol)</th>
                        <th class="py-3 px-3 text-right whitespace-overwarp">Target (Rp)</th>
                        <th class="py-3 px-3 text-right whitespace-overwarp">Realisasi (Vol)</th>
                        <th class="py-3 px-3 text-right whitespace-overwarp">Realisasi (Rp)</th>
                        <th class="py-3 px-3 text-center whitespace-overwarp">% Capaian</th>
                        <th class="py-3 px-3 text-center whitespace-overwarp">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($filteredItems as $item)
                        @php
                            $isHeader = ($item['level'] ?? 2) === 1;
                            $isTotal  = ($item['level'] ?? 2) === 0;
                            $isSub    = ($item['level'] ?? 2) === 2;
                            $isDeep   = ($item['level'] ?? 2) === 3;
                            $itemId   = $item['id'] ?? null;
                        @endphp
                        <tr class="{{ $isTotal ? 'bg-gradient-to-r from-blue-700 to-indigo-800 text-white font-extrabold text-base' : ($isHeader ? 'bg-blue-50/60 font-bold text-gray-900' : 'hover:bg-gray-50/80') }}">
                            <td class="py-3 px-3 text-center {{ $isTotal ? 'text-white' : ($isHeader ? 'text-blue-700 font-extrabold' : 'text-gray-400 text-xs') }} whitespace-overwarp">
                                {{ $item['no'] }}
                            </td>
                            <td class="py-3 px-3 whitespace-overwarp leading-relaxed">
                                <div class="flex items-start">
                                    @if($isDeep)
                                        <span class="inline-block w-8 shrink-0"></span>
                                        <span class="text-xs text-gray-400 mr-2 shrink-0 mt-0.5">&bull;</span>
                                    @elseif($isSub)
                                        <span class="inline-block w-4 shrink-0"></span>
                                    @endif
                                    <span class="{{ $isTotal ? 'text-white text-base' : ($isHeader ? 'text-blue-900' : 'text-gray-800') }} whitespace-overwarp">
                                        {{ $item['name'] }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-right text-xs {{ $isTotal ? 'text-white' : 'text-gray-500' }} whitespace-overwarp">
                                @if(($item['tarif'] ?? 0) > 0)
                                    Rp {{ number_format($item['tarif']) }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right {{ $isTotal ? 'text-white' : '' }} whitespace-overwarp">
                                {{ number_format($item['volume_target'] ?? 0) }}
                            </td>
                            <td class="py-3 px-3 text-right font-semibold {{ $isTotal ? 'text-white' : 'text-gray-900' }} whitespace-overwarp">
                                Rp {{ number_format($item['target_rp'] ?? 0) }}
                            </td>
                            <td class="py-3 px-3 text-right {{ $isTotal ? 'text-white' : '' }} whitespace-overwarp">
                                {{ number_format($item['volume_realization'] ?? 0) }}
                            </td>
                            <td class="py-3 px-3 text-right font-bold {{ $isTotal ? 'text-cyan-200 text-base' : (($item['realization_rp'] ?? 0) > 0 ? 'text-emerald-600' : 'text-gray-400') }} whitespace-overwarp">
                                Rp {{ number_format($item['realization_rp'] ?? 0) }}
                            </td>
                            <td class="py-3 px-3 text-center whitespace-overwarp">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-extrabold {{ $isTotal ? 'bg-white/20 text-white' : (($item['percentage'] ?? 0) > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-400') }} whitespace-overwarp">
                                    {{ number_format($item['percentage'] ?? 0, 2) }}%
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                @if ($isTotal)
                                    <span class="text-white/40 text-xs font-bold">-</span>
                                @elseif ($itemId)
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="openEditItemModal('{{ $itemId }}')"
                                            class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-105 transition-all shadow-sm"
                                            title="Edit Item {{ $item['name'] }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                        <button wire:click="openDeleteItemModal('{{ $itemId }}')"
                                            class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:scale-105 transition-all shadow-sm"
                                            title="Hapus Item {{ $item['name'] }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-300 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-500 text-sm whitespace-overwarp">
                                @if(!$hasExcelFile)
                                    Belum ada rincian materiil untuk Tahun Anggaran {{ $selectedYear }}. Klik "Tambah Item" untuk menambahkan.
                                @elseif($search)
                                    Tidak ada materiil yang sesuai dengan pencarian "{{ $search }}".
                                @else
                                    Tidak ada rincian data pada sheet ini.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create/Edit Modal for Target -->
    @include('livewire.admin.master.target.admin-master-target-modal')

    <!-- Create/Edit/Delete Modals for Categorized Item -->
    @include('livewire.admin.master.target.capaian.admin-master-target-capaian-item-modal')

    <!-- Delete Confirmation Modal for Target -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/70 backdrop-blur-sm" wire:click="closeDeleteModal"></div>
                <div class="relative inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl animate-in fade-in zoom-in-95 duration-200">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-100 mb-4">
                            <svg class="h-8 w-8 text-rose-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2 whitespace-overwarp">Hapus Target TA {{ $selectedYear }}?</h3>
                        <p class="text-gray-500 text-sm whitespace-overwarp">
                            Apakah Anda yakin ingin menghapus data target tahun {{ $selectedYear }} beserta seluruh rincian kuotanya? Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="button" wire:loading.attr="disabled" wire:click="closeDeleteModal"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                            Batal
                        </button>
                        <button type="button" wire:loading.attr="disabled" wire:click="delete"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-rose-600 to-red-600 rounded-xl hover:from-rose-700 hover:to-red-700 shadow-md shadow-rose-500/20 transition-all active:scale-95">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
