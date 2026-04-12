<div>
    <!-- Page Header -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">Dashboard</h1>
            <p class="mt-2 text-lg text-gray-500">Visualisasi data dan statistik sistem manajemen material terintegrasi.
            </p>
        </div>
        <div class="flex gap-3">
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-white px-5 py-2.5 text-sm font-semibold text-blue-700 shadow-sm hover:bg-blue-50 transition-all hover:shadow-md active:scale-95">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
            <a href="{{ route('login') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-white px-5 py-2.5 text-sm font-semibold text-blue-700 shadow-sm hover:bg-blue-50 transition-all hover:shadow-md active:scale-95">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V5a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Login
            </a>
        </div>
    </div>


    @if($showDataKendaraan)
        <!-- Data Kendaraan Section -->
        <div x-transition
            class="mb-8 rounded-2xl border border-blue-100 bg-white p-6 shadow-xl ring-1 ring-black/5 animate-in fade-in slide-in-from-top-4 duration-300">
            <!-- Jenis Dokumen -->
            <div class="mb-6 p-5 rounded-2xl bg-emerald-50 border border-emerald-100/50 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-base font-bold text-emerald-900 italic uppercase">Jenis Dokumen <span
                            class="text-red-500">*</span> <span class="text-xs font-normal text-emerald-600">(berlaku untuk
                            semua nopol)</span></p>
                </div>
                <div class="flex flex-wrap gap-6 ml-1">
                    @foreach(['STNK', 'TNKB', 'BPKB'] as $doc)
                        <label class="inline-flex items-center cursor-pointer group">
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" value="{{ $doc }}" wire:model.live="selectedDocTypes"
                                    class="peer h-5 w-5 cursor-pointer appearance-none rounded border border-emerald-300 bg-white transition-all checked:bg-blue-600 checked:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                <svg class="pointer-events-none absolute h-3 w-3 text-white opacity-0 peer-checked:opacity-100"
                                    fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span
                                class="ml-3 text-sm font-bold text-blue-900 group-hover:text-blue-700 transition-colors">{{ $doc }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Daftar Kendaraan -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-blue-900 italic uppercase">Daftar Kendaraan</h3>
                        <span
                            class="rounded-full bg-blue-600/10 px-3 py-1 text-xs font-bold text-blue-700 ring-1 ring-blue-600/20">1
                            kendaraan</span>
                    </div>
                    <div class="flex gap-2">
                        <button
                            class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 shadow-sm transition-colors uppercase tracking-wide">Nomor
                            Material</button>
                        <button
                            class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 shadow-sm transition-colors uppercase tracking-wide">Tambah
                            Nopol</button>
                    </div>
                </div>

                <!-- Search Area -->
                <div class="p-6 rounded-2xl border border-gray-200 bg-gray-50/50 shadow-inner">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="relative flex-grow group">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                                <span class="text-gray-400 font-bold text-lg">1</span>
                                <div class="h-5 w-px bg-gray-300"></div>
                            </div>
                            <input type="text" wire:model.lazy="searchNopol" placeholder="Input Nopol (Contoh: L1111AAA)"
                                @keyup.enter="cekKendaraan"
                                class="w-full rounded-xl border-emerald-300 bg-white pl-14 pr-4 py-3.5 text-center font-bold text-xl text-gray-900 placeholder:text-gray-300 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all uppercase tracking-widest shadow-sm">
                        </div>
                        <button wire:click="cekKendaraan" wire:loading.attr="disabled"
                            class="rounded-xl bg-blue-600 px-10 py-3.5 text-base font-bold text-white hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition-all hover:-translate-y-0.5 active:translate-y-0 focus:ring-4 focus:ring-blue-500/20 uppercase tracking-widest">
                            <span wire:loading.remove>Cek</span>
                            <span wire:loading>Checking...</span>
                        </button>
                    </div>

                    @if($vehicleData)
                        <!-- Vehicle Data Display -->
                        <div
                            class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 animate-in fade-in duration-500">
                            <div class="p-1.5 bg-emerald-500 rounded-full">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-emerald-800">DATA DITEMUKAN - <span
                                    class="uppercase tracking-wider underline decoration-sky-300 decoration-2 underline-offset-4">{{ $vehicleData['owner'] }}</span>
                            </p>
                        </div>

                        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                            @php
                                $fields = [
                                    ['label' => 'Nama Pemilik *', 'key' => 'owner'],
                                    ['label' => 'NIK', 'key' => 'nik'],
                                    ['label' => 'No HP', 'key' => 'hp'],
                                    ['label' => 'No Rangka', 'key' => 'chassis'],
                                    ['label' => 'No Mesin', 'key' => 'engine'],
                                    ['label' => 'Merk', 'key' => 'brand'],
                                    ['label' => 'Tipe', 'key' => 'type'],
                                    ['label' => 'Warna', 'key' => 'color'],
                                ];
                            @endphp

                            @foreach($fields as $field)
                                <div class="space-y-1.5">
                                    <label
                                        class="block text-[11px] font-extrabold text-gray-500 uppercase tracking-widest">{{ $field['label'] }}</label>
                                    <div class="relative group">
                                        <input type="text" value="{{ $vehicleData[$field['key']] }}" readonly
                                            class="w-full rounded-xl border-gray-200 bg-gray-50 p-3.5 text-sm font-bold text-gray-900 border transition-colors group-hover:border-blue-200">
                                        <div class="absolute inset-0 bg-transparent cursor-not-allowed"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Serials Banner -->
                        <div
                            class="mt-8 flex flex-col md:flex-row gap-6 p-1 bg-blue-950 rounded-2xl shadow-xl shadow-blue-900/20">
                            <div
                                class="flex-1 flex items-center justify-between p-4 px-6 bg-blue-900/30 rounded-xl border border-blue-800/50">
                                <span class="text-xs font-bold text-blue-300 uppercase tracking-widest">No. Seri BPKB</span>
                                <span
                                    class="text-lg font-black text-white italic tracking-widest">{{ $vehicleData['bpkb_serial'] }}</span>
                            </div>
                            <div
                                class="flex-1 flex items-center justify-between p-4 px-6 bg-blue-900/30 rounded-xl border border-blue-800/50">
                                <span class="text-xs font-bold text-blue-300 uppercase tracking-widest">No. Seri STNK</span>
                                <span
                                    class="text-lg font-black text-white italic tracking-widest">{{ $vehicleData['stnk_serial'] }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Data Pengurus -->
            <div class="mt-10 p-8 rounded-3xl border border-blue-50 bg-blue-50/20 shadow-inner">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-blue-600 rounded-lg shadow-lg shadow-blue-600/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-blue-900 italic uppercase">Data Pengurus</h3>
                    <span class="text-xs font-normal text-blue-500 tracking-normal">(berlaku untuk seluruh kendaraan)</span>
                </div>

                <div class="flex gap-10 mb-8 p-1">
                    @foreach(['WP' => 'WP (Wajib Pajak)', 'Kuasa' => 'Kuasa'] as $val => $label)
                        <label class="inline-flex items-center cursor-pointer group">
                            <div class="relative flex items-center justify-center">
                                <input type="radio" value="{{ $val }}" wire:model.live="pengurusType"
                                    class="peer h-6 w-6 cursor-pointer appearance-none rounded-full border-2 border-blue-200 bg-white transition-all checked:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                                <div
                                    class="absolute h-3 w-3 rounded-full bg-blue-600 opacity-0 peer-checked:opacity-100 transition-opacity">
                                </div>
                            </div>
                            <span
                                class="ml-4 text-sm font-black text-blue-900 uppercase tracking-widest group-hover:text-blue-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="p-5 rounded-2xl bg-amber-50 border border-amber-100 mb-8 flex items-start gap-4">
                    <div class="mt-0.5 p-1.5 bg-amber-200 rounded-full flex-shrink-0">
                        <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-amber-800 italic leading-relaxed">Informasi: Data Pengurus secara
                        otomatis disinkronkan dari hasil pengecekan SAMSAT. Anda dapat memperbarui data ini jika terdapat
                        perubahan pengurusan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @php
                        $pengurusFields = [
                            ['label' => 'Nama WP *', 'wire' => '', 'value' => $vehicleData['owner'] ?? '', 'color' => 'bg-emerald-50 border-emerald-100 text-gray-900 focus:ring-emerald-500'],
                            ['label' => 'NIK WP', 'wire' => '', 'value' => $vehicleData['nik'] ?? '', 'color' => 'bg-emerald-50 border-emerald-100 text-gray-900 focus:ring-emerald-500'],
                            ['label' => 'No HP WP', 'wire' => '', 'placeholder' => '08xxxxxxxxxx', 'color' => 'bg-white border-gray-200'],
                            ['label' => 'Email WP', 'wire' => '', 'placeholder' => 'email@example.com', 'color' => 'bg-white border-gray-200'],
                        ];
                    @endphp

                    @foreach($pengurusFields as $f)
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-extrabold text-gray-500 uppercase tracking-widest">
                                {{ $f['label'] }}
                                @if(str_contains($f['label'], 'HP') || str_contains($f['label'], 'Email'))
                                    <span class="text-gray-400 font-normal leading-normal italic lowercase ml-1">(opsional)</span>
                                @endif
                            </label>
                            <input type="{{ str_contains($f['label'], 'Email') ? 'email' : 'text' }}"
                                value="{{ $f['value'] ?? '' }}" placeholder="{{ $f['placeholder'] ?? '' }}"
                                class="w-full rounded-xl p-4 text-base font-black transition-all focus:ring-4 {{ $f['color'] }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- PNBP Cards -->
        <div
            class="group relative overflow-hidden rounded-3xl bg-blue-700 p-6 shadow-xl transition-all hover:-translate-y-1">
            <div
                class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 transition-transform group-hover:scale-150">
            </div>
            <div class="relative z-10">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-100/80">Target PNBP
                    {{ $activeTargetYear }}
                </p>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-lg font-bold text-blue-200">Rp</span>
                    <h3 class="text-xl font-black text-white">{{ number_format($pnbpStats['target']) }}</h3>
                </div>
            </div>
        </div>

        <div
            class="group relative overflow-hidden rounded-3xl bg-purple-600 p-6 shadow-xl transition-all hover:-translate-y-1">
            <div
                class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 transition-transform group-hover:scale-150">
            </div>
            <div class="relative z-10">
                <p class="text-sm font-bold uppercase tracking-widest text-purple-100/80 italic">Realisasi PNBP
                    {{ $activeTargetYear }}
                </p>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-lg font-bold text-purple-200">Rp</span>
                    <h3 class="text-xl font-black text-white">{{ number_format($pnbpStats['realization']) }}</h3>
                </div>
                <div class="mt-4 flex flex-col gap-2">
                    <div class="h-1.5 w-full rounded-full bg-purple-900/50">
                        <div class="h-full rounded-full bg-white shadow-[0_0_8px_rgba(255,255,255,0.5)]"
                            style="width: {{ min(100, $pnbpStats['percentage']) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-purple-100/80 tracking-widest">AKTUAL</span>
                        <span
                            class="text-lg font-black text-white italic">{{ number_format($pnbpStats['percentage'], 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Renbut Cards -->
        <div
            class="group relative overflow-hidden rounded-3xl bg-cyan-600 p-6 shadow-xl transition-all hover:-translate-y-1">
            <div
                class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 transition-transform group-hover:scale-150">
            </div>
            <div class="relative z-10">
                <p class="text-sm font-bold uppercase tracking-widest text-cyan-100/80 italic">Renbut
                    {{ $activeTargetYear }}
                </p>
                <div class="mt-4 flex items-baseline gap-2">
                    <h3 class="text-xl font-black text-white">{{ number_format($renbutStats['target']) }}</h3>
                    <span class="text-sm font-bold text-cyan-200 italic uppercase">Unit</span>
                </div>
            </div>
        </div>

        <div
            class="group relative overflow-hidden rounded-3xl bg-emerald-600 p-6 shadow-xl transition-all hover:-translate-y-1">
            <div
                class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 transition-transform group-hover:scale-150">
            </div>
            <div class="relative z-10">
                <p class="text-sm font-bold uppercase tracking-widest text-emerald-100/80 italic">Realisasi
                    {{ $activeTargetYear }}
                </p>
                <div class="mt-4 flex items-baseline gap-2">
                    <h3 class="text-xl font-black text-white">{{ number_format($renbutStats['realization']) }}</h3>
                    <span class="text-sm font-bold text-emerald-200 italic uppercase">Unit</span>
                </div>
                <div class="mt-4 flex flex-col gap-2">
                    <div class="h-1.5 w-full rounded-full bg-emerald-900/50">
                        <div class="h-full rounded-full bg-white shadow-[0_0_8px_rgba(255,255,255,0.5)]"
                            style="width: {{ min(100, $renbutStats['percentage']) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span
                            class="text-xs font-extrabold text-emerald-100/80 tracking-widest uppercase">TERPAKAI</span>
                        <span
                            class="text-lg font-black text-white italic">{{ number_format($renbutStats['percentage'], 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="relative overflow-hidden rounded-2xl bg-amber-500 p-5 shadow-lg transition-transform hover:scale-[1.02]">
            <div class="relative z-10">
                <p class="text-sm font-medium text-amber-100 italic">Stock Polda</p>
                <p class="mt-2 text-2xl font-bold text-white">{{ number_format($totalStockPolda) }}</p>
                <div class="mt-2">
                    <span class="text-xs text-amber-100">Total Unit di Polda</span>
                </div>
            </div>
        </div>

        <!-- Stock Polres -->
        <div
            class="relative overflow-hidden rounded-2xl bg-sky-400 p-5 shadow-lg transition-transform hover:scale-[1.02]">
            <div class="relative z-10">
                <p class="text-sm font-medium text-sky-100 italic">Stock Polres</p>
                <p class="mt-2 text-2xl font-bold text-white">{{ number_format($totalStockPolres) }}</p>
                <div class="mt-2">
                    <span class="text-xs text-sky-100">Total Unit di Polres</span>
                </div>
            </div>
        </div>

        <!-- Total Penerimaan -->
        <div
            class="relative overflow-hidden rounded-2xl bg-violet-600 p-5 shadow-lg transition-transform hover:scale-[1.02]">
            <div class="relative z-10">
                <p class="text-sm font-medium text-violet-100 italic">Total Penerimaan</p>
                <p class="mt-2 text-2xl font-bold text-white">{{ number_format($totalReceptions) }}</p>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-3xl bg-rose-600 p-6 shadow-xl transition-all hover:rotate-1">
            <div class="relative z-10 text-right">
                <p class="text-xs font-black uppercase tracking-widest text-rose-100 italic">Penerimaan Hari Ini</p>
                <div class="mt-3 flex items-baseline justify-end gap-2 text-white">
                    <h3 class="text-xl font-black">{{ $receptionsToday }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Target vs Pencapaian Chart -->
        <div class="rounded-3xl border border-gray-100 bg-white p-8 shadow-xl shadow-gray-200/50">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h3 class="text-xl font-black text-gray-900 italic uppercase tracking-tight">Capaian per Lokasi</h3>
                    <p class="text-sm font-bold text-gray-400 mt-1 uppercase tracking-widest">Target vs Realisasi
                        Lapangan</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-xl bg-blue-50 px-4 py-2 border border-blue-100">
                    <div class="h-2 w-2 rounded-full bg-blue-600 animate-pulse"></div>
                    <span id="targetAchievementLocation" class="text-sm font-black text-blue-700">-</span>
                </div>
            </div>
            <div style="height: 350px; position: relative;">
                <canvas id="targetAchievementChart"></canvas>
            </div>
        </div>

        <!-- History Trend Chart -->
        <div class="rounded-3xl border border-gray-100 bg-white p-8 shadow-xl shadow-gray-200/50">
            <div class="mb-8">
                <h3 class="text-xl font-black text-gray-900 italic uppercase tracking-tight">Arus Histori Stock</h3>
                <p class="text-sm font-bold text-gray-400 mt-1 uppercase tracking-widest">Tren Pergerakan 12 Bulan
                    Terakhir</p>
            </div>
            <div style="height: 350px; position: relative;">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <!-- Recent Receptions Table -->
        <div
            class="lg:col-span-2 rounded-3xl border border-gray-100 bg-white shadow-xl shadow-gray-200/50 overflow-hidden">
            <div class="flex items-center justify-between bg-gray-50/50 px-8 py-6 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-1.5 rounded-full bg-blue-600"></div>
                    <div>
                        <h3 class="text-xl font-black text-gray-900 italic uppercase tracking-tight">Penerimaan Terbaru
                        </h3>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-0.5">Daftar Transaksi
                            Masuk Sistem</p>
                    </div>
                </div>
                <a href="/admin/receptions"
                    class="group flex items-center gap-2 text-xs font-black text-blue-600 uppercase tracking-widest hover:text-blue-800 transition-all">
                    Detail Data
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th
                                class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">
                                Kode Reff</th>
                            <th
                                class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">
                                Lokasi Tujuan</th>
                            <th
                                class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">
                                Tanggal Masuk</th>
                            <th
                                class="px-8 py-4 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">
                                Volume</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentReceptions as $reception)
                            <tr class="hover:bg-blue-50/30 transition-all group">
                                <td class="px-8 py-5">
                                    <span
                                        class="font-mono text-sm font-extrabold text-blue-600 group-hover:underline">{{ $reception->code }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black text-gray-800 italic uppercase tracking-tight">{{ $reception->regionalPolice?->name ?? 'N/A' }}</span>
                                        <span
                                            class="text-xs font-bold text-gray-400 mt-0.5">{{ $reception->policeStation?->name ?? 'MAKO POLDA' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-sm font-bold text-gray-500 uppercase">
                                    {{ $reception->date?->locale('id')->format('d M Y') ?? 'N/A' }}
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <span
                                        class="inline-flex items-center rounded-lg bg-blue-100 px-3 py-1 text-xs font-black text-blue-700 italic tracking-widest ring-1 ring-blue-600/10">
                                        {{ $reception->receptionDetails->sum('quantity') }} UNIT
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="px-8 py-20 text-center text-sm font-bold text-gray-400 uppercase italic tracking-widest">
                                    Belum Tersedia Data Penerimaan Terkini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Supplementary Charts Section -->
    <div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-3">
        <!-- Distribution Doughnut -->
        <div class="rounded-3xl border border-gray-100 bg-white p-8 shadow-xl shadow-gray-200/50">
            <h3 class="text-lg font-black text-gray-900 italic uppercase mb-6 tracking-tight">Distribusi Lokasi</h3>
            <div style="height: 250px; position: relative;">
                <canvas id="doughnutChart"></canvas>
            </div>
            <div class="mt-8 space-y-3">
                <div class="flex items-center justify-between p-4 rounded-2xl bg-blue-50 border border-blue-100">
                    <span class="text-xs font-black text-blue-800 uppercase tracking-widest">POLDA</span>
                    <span
                        class="text-lg font-black text-blue-900 italic">{{ number_format($stockDistribution['polda_count']) }}
                        <span class="text-[10px]">UNIT</span></span>
                </div>
                <div class="flex items-center justify-between p-4 rounded-2xl bg-cyan-50 border border-cyan-100">
                    <span class="text-xs font-black text-cyan-800 uppercase tracking-widest">Polres</span>
                    <span
                        class="text-lg font-black text-cyan-900 italic">{{ number_format($stockDistribution['polres_count']) }}
                        <span class="text-[10px]">UNIT</span></span>
                </div>
            </div>
        </div>

        <!-- Stock per Location -->
        <div class="md:col-span-2 rounded-3xl border border-gray-100 bg-white p-8 shadow-xl shadow-gray-200/50">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black text-gray-900 italic uppercase tracking-tight">Peringkat Stok Polres</h3>
                <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">Top 5 Lokasi</span>
            </div>
            <div style="height: 350px; position: relative;">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hexToRgba = (hex, alpha) => {
                const normalized = (hex || '#000000').replace('#', '');
                const bigint = parseInt(normalized, 16);
                const r = (bigint >> 16) & 255;
                const g = (bigint >> 8) & 255;
                const b = bigint & 255;
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            };

            const globalPalette = ['#2563eb', '#f97316', '#10b981', '#eab308', '#8b5cf6', '#ef4444', '#14b8a6', '#0ea5e9'];

            // 1. Target vs Achievement Chart
            const targetAchievementData = @json($targetAchievementChart);
            const targetAchievementCtx = document.getElementById('targetAchievementChart');
            const targetAchievementLocation = document.getElementById('targetAchievementLocation');

            if (targetAchievementCtx && targetAchievementData.locations.length > 0) {
                let currentIdx = 0;
                const labels = targetAchievementData.types;
                const initial = targetAchievementData.locations[0];

                targetAchievementLocation.textContent = initial.label;

                const chart = new Chart(targetAchievementCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Target',
                                data: initial.target,
                                backgroundColor: 'rgba(203, 213, 225, 0.5)',
                                borderColor: '#94a3b8',
                                borderWidth: 0,
                                borderRadius: 8,
                                barPercentage: 0.8,
                                categoryPercentage: 0.8
                            },
                            {
                                label: 'Realisasi',
                                data: initial.actual,
                                backgroundColor: '#2563eb',
                                borderColor: '#2563eb',
                                borderWidth: 0,
                                borderRadius: 8,
                                barPercentage: 0.8,
                                categoryPercentage: 0.8
                            }
                        ]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { display: false },
                            tooltip: { padding: 12, cornerRadius: 12 }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } },
                            y: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
                        }
                    }
                });

                setInterval(() => {
                    currentIdx = (currentIdx + 1) % targetAchievementData.locations.length;
                    const l = targetAchievementData.locations[currentIdx];
                    targetAchievementLocation.textContent = l.label;
                    chart.data.datasets[0].data = l.target;
                    chart.data.datasets[1].data = l.actual;
                    chart.update('active');
                }, 8000);
            }

            // 2. Line Chart - History
            const lineCtx = document.getElementById('lineChart');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: @json($historyStockTrend['labels']),
                        datasets: [{
                            data: @json($historyStockTrend['data']),
                            borderColor: '#2563eb',
                            backgroundColor: (context) => {
                                const ctx = context.chart.ctx;
                                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                                gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
                                gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');
                                return gradient;
                            },
                            borderWidth: 6,
                            fill: true,
                            tension: 0.45,
                            pointRadius: 0,
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: '#2563eb',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { weight: 'bold' } } },
                            x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
                        }
                    }
                });
            }

            // 3. Doughnut Distribution
            const doughnutCtx = document.getElementById('doughnutChart');
            if (doughnutCtx) {
                new Chart(doughnutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Polda', 'Polres'],
                        datasets: [{
                            data: [@json($stockDistribution['polda_count']), @json($stockDistribution['polres_count'])],
                            backgroundColor: ['#2563eb', '#06b6d4'],
                            borderWidth: 8,
                            borderColor: '#fff',
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: { legend: { display: false } }
                    }
                });
            }

            // 4. Bar Chart - Locations
            const barCtx = document.getElementById('barChart');
            if (barCtx) {
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($stockPerLocation->map(fn($i) => $i->policeStation?->name ?? 'N/A')->toArray()),
                        datasets: [{
                            data: @json($stockPerLocation->pluck('total_stock')->toArray()),
                            backgroundColor: ['#1e40af', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd'],
                            borderRadius: 12,
                            barThickness: 45
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { color: '#f1f5f9' }, ticks: { font: { weight: 'bold' } } },
                            x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
                        }
                    }
                });
            }
        });
    </script>
@endpush