<div>
    <!-- Page Header -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 lg:text-3xl">Dashboard</h1>
            <p class="mt-1 text-gray-500">Visualisasi data dan statistik sistem manajemen material.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="toggleDataKendaraan"
                class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-medium text-blue-700 shadow-sm hover:bg-blue-50 transition-colors">
                Data Kendaraan
            </button>
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-medium text-blue-700 shadow-sm hover:bg-blue-50 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>
    </div>


    @if($showDataKendaraan)
        <!-- Data Kendaraan Section -->
        <div x-transition class="mb-8 rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
            <!-- Jenis Dokumen -->
            <div class="mb-6 p-4 rounded-xl bg-green-50/50 border border-green-100">
                <p class="text-sm font-semibold text-green-800 mb-3">Jenis Dokumen <span class="text-red-500">*</span> <span
                        class="text-xs font-normal text-green-600">(berlaku untuk semua nopol)</span></p>
                <div class="flex gap-4">
                    @foreach(['STNK', 'TNKB', 'BPKB'] as $doc)
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="{{ $doc }}" wire:model.live="selectedDocTypes"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm font-bold text-blue-900">{{ $doc }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Daftar Kendaraan -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-blue-900">Daftar Kendaraan</h3>
                        <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-700">1
                            kendaraan</span>
                    </div>
                    <div class="flex gap-2">
                        <button
                            class="rounded-lg bg-blue-600 px-4 py-1.5 text-xs font-bold text-white hover:bg-blue-700">Nomor
                            Material</button>
                        <button
                            class="rounded-lg bg-blue-600 px-4 py-1.5 text-xs font-bold text-white hover:bg-blue-700">Tambah
                            Nopol</button>
                    </div>
                </div>

                <!-- Search Area -->
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/50 mb-4">
                    <div class="flex gap-3">
                        <div class="relative flex-grow">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">1</span>
                            <input type="text" wire:model.lazy="searchNopol" placeholder="L1111AAA"
                                class="w-full rounded-lg border-green-300 bg-white pl-8 pr-4 py-2.5 text-center font-bold text-gray-900 focus:border-green-500 focus:ring-green-500 uppercase">
                        </div>
                        <button wire:click="cekKendaraan" wire:loading.attr="disabled"
                            class="rounded-lg bg-blue-600 px-8 py-2.5 text-sm font-bold text-white hover:bg-blue-700 shadow-sm transition-all focus:ring-4 focus:ring-blue-500/20">
                            Cek
                        </button>
                    </div>

                    @if($vehicleData)
                        <!-- Vehicle Data Display -->
                        <div class="mt-4 p-3 rounded-lg bg-green-50 border border-green-200">
                            <p class="text-xs font-bold text-green-700">Data ditemukan - <span
                                    class="uppercase">{{ $vehicleData['owner'] }}</span></p>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama
                                    Pemilik *</label>
                                <input type="text" value="{{ $vehicleData['owner'] }}" readonly
                                    class="w-full rounded-lg border-gray-200 bg-gray-100 p-2.5 text-sm font-bold text-gray-900">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">NIK</label>
                                <input type="text" value="{{ $vehicleData['nik'] }}" readonly
                                    class="w-full rounded-lg border-gray-200 bg-gray-100 p-2.5 text-sm font-bold text-gray-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">No
                                    HP</label>
                                <input type="text" value="{{ $vehicleData['hp'] }}" readonly
                                    class="w-full rounded-lg border-gray-200 bg-gray-100 p-2.5 text-sm font-bold text-gray-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">No
                                    Rangka</label>
                                <input type="text" value="{{ $vehicleData['chassis'] }}" readonly
                                    class="w-full rounded-lg border-gray-200 bg-gray-100 p-2.5 text-sm font-bold text-gray-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">No
                                    Mesin</label>
                                <input type="text" value="{{ $vehicleData['engine'] }}" readonly
                                    class="w-full rounded-lg border-gray-200 bg-gray-100 p-2.5 text-sm font-bold text-gray-900">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Merk</label>
                                <input type="text" value="{{ $vehicleData['brand'] }}" readonly
                                    class="w-full rounded-lg border-gray-200 bg-gray-100 p-2.5 text-sm font-bold text-gray-900">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tipe</label>
                                <input type="text" value="{{ $vehicleData['type'] }}" readonly
                                    class="w-full rounded-lg border-gray-200 bg-gray-100 p-2.5 text-sm font-bold text-gray-900">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Warna</label>
                                <input type="text" value="{{ $vehicleData['color'] }}" readonly
                                    class="w-full rounded-lg border-gray-200 bg-gray-100 p-2.5 text-sm font-bold text-gray-900">
                            </div>
                        </div>

                        <!-- Serials Banner -->
                        <div class="mt-6 flex flex-wrap gap-4 rounded-lg bg-blue-900 p-4 text-sm font-bold text-white">
                            <div class="flex items-center gap-2">
                                <span class="text-blue-300">No. Seri BPKB :</span>
                                <span>{{ $vehicleData['bpkb_serial'] }}</span>
                            </div>
                            <div class="border-l border-blue-700 h-5 hidden md:block"></div>
                            <div class="flex items-center gap-2">
                                <span class="text-blue-300">No. Seri STNK :</span>
                                <span>{{ $vehicleData['stnk_serial'] }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Data Pengurus -->
            <div class="p-6 rounded-xl border border-gray-100 bg-gray-50/30">
                <p class="text-sm font-bold text-purple-900 mb-4">Data Pengurus <span
                        class="text-xs font-normal text-purple-600 tracking-normal">(berlaku untuk semua kendaraan)</span>
                </p>

                <div class="flex gap-6 mb-6">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" value="WP" wire:model.live="pengurusType"
                            class="border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-bold text-blue-900 uppercase">WP (Wajib Pajak)</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" value="Kuasa" wire:model.live="pengurusType"
                            class="border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-bold text-blue-900 uppercase tracking-wider font-sans">Kuasa</span>
                    </label>
                </div>

                <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-100 mb-6">
                    <p class="text-xs font-medium text-blue-700 italic">Data WP diambil dari hasil cek SAMSAT (bisa diedit
                        jika perlu)</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama WP
                            *</label>
                        <input type="text" value="{{ $vehicleData['owner'] ?? '' }}"
                            class="w-full rounded-lg border-green-100 bg-green-50/50 p-2.5 text-sm font-bold text-gray-900 focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">NIK
                            WP</label>
                        <input type="text" value="{{ $vehicleData['nik'] ?? '' }}"
                            class="w-full rounded-lg border-green-100 bg-green-50/50 p-2.5 text-sm font-bold text-gray-900 focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">No HP WP
                            <span class="text-gray-400 font-normal leading-normal italic">(Opsional)</span></label>
                        <input type="text" placeholder="08xxxxxxxxxx"
                            class="w-full rounded-lg border-gray-200 bg-white p-2.5 text-sm font-bold text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email WP
                            <span class="text-gray-400 font-normal leading-normal italic">(Opsional)</span></label>
                        <input type="email" placeholder="email@example.com"
                            class="w-full rounded-lg border-gray-200 bg-white p-2.5 text-sm font-bold text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($isPolres && $polresDashboardData)
        <!-- ========================================================================= -->
        <!-- DASHBOARD POLRES (KASLAN / KRI MONEV PENGAWASAN) -->
        <!-- ========================================================================= -->
        <div class="mb-8 p-6 rounded-2xl bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-800 text-white shadow-xl">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/30 text-blue-200 text-xs font-semibold mb-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Dashboard Pengawasan & Monev Pimpinan (Kaslan / KRI)
                    </div>
                    <h2 class="text-2xl font-bold text-white">{{ $polresDashboardData['police_station'] }}</h2>
                    <p class="text-blue-200 text-sm mt-1">Sisa stok material PNBP, penggunaan hari ini, dan pengawasan material rusak</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-xl border border-white/10 text-right">
                    <p class="text-xs text-blue-200">Tanggal Hari Ini</p>
                    <p class="text-sm font-bold text-white">{{ now()->locale('id')->isoFormat('D MMMM Y') }}</p>
                </div>
            </div>
        </div>

        <!-- 1. STOCK MATERIAL PNBP PER RAK / DOKUMEN -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="h-6 w-1.5 bg-blue-600 rounded-full"></div>
                    <h3 class="text-lg font-bold text-gray-900">Stock Material (Sisa Stok PNBP)</h3>
                </div>
                <span class="text-xs text-gray-500 italic">* Khusus Material PNBP Polres</span>
            </div>

            <!-- Material PNBP Stock Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @forelse($polresDashboardData['stock_by_material'] as $index => $item)
                    @php
                        $bgGradients = [
                            'bg-gradient-to-br from-blue-700 to-indigo-800',
                            'bg-gradient-to-br from-indigo-700 to-blue-900',
                            'bg-gradient-to-br from-blue-800 to-cyan-800',
                            'bg-gradient-to-br from-blue-900 to-slate-900',
                        ];
                        $bgClass = $bgGradients[$index % count($bgGradients)];
                    @endphp
                    <div class="relative overflow-hidden rounded-2xl {{ $bgClass }} p-6 text-white shadow-lg transition-transform hover:scale-[1.02]">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wider text-blue-200">Sisa Stok</span>
                                <h4 class="text-xl font-extrabold text-white mt-1">{{ $item['type_name'] }}</h4>
                            </div>
                            <div class="bg-white/20 p-3 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-white/20 flex items-baseline justify-between">
                            <span class="text-3xl font-black text-white">{{ number_format($item['total_stock'], 0, ',', '.') }}</span>
                            <span class="text-xs text-blue-200 font-medium">unit</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 p-8 bg-white rounded-2xl border border-gray-100 text-center text-gray-500">
                        Belum ada data stok material PNBP di Polres ini
                    </div>
                @endforelse
            </div>

            <!-- Breakdown Per Rak jika ada -->
            @if(count($polresDashboardData['racks']) > 0)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Penataan Rak Material</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($polresDashboardData['racks'] as $rack)
                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-blue-900">{{ $rack['name'] }}</span>
                                    <span class="text-xs font-bold bg-blue-100 text-blue-700 px-2.5 py-0.5 rounded-full">{{ number_format($rack['total_quantity']) }} unit</span>
                                </div>
                                <div class="space-y-1 mt-2">
                                    @foreach($rack['items'] as $item)
                                        <div class="flex items-center justify-between text-xs text-gray-600">
                                            <span>{{ $item['name'] }}:</span>
                                            <span class="font-semibold text-gray-900">{{ number_format($item['quantity']) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- 2. PENGGUNAAN HARI INI -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="h-6 w-1.5 bg-emerald-600 rounded-full"></div>
                    <h3 class="text-lg font-bold text-gray-900">Penggunaan Hari Ini</h3>
                </div>
                <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                    {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @forelse($polresDashboardData['today_usage'] as $usage)
                    <div class="bg-white rounded-2xl p-5 shadow-md border border-emerald-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase">{{ $usage['type_name'] }}</p>
                            <p class="text-2xl font-extrabold text-emerald-700 mt-1">{{ number_format($usage['quantity_today'], 0, ',', '.') }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">digunakan hari ini</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 p-6 bg-white rounded-2xl border border-gray-100 text-center text-gray-400">
                        Belum ada transaksi penggunaan material hari ini
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 3. MATERIAL RUSAK & RECEIVINGS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Material Rusak Summary -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <h4 class="font-bold text-gray-900">Ringkasan Material Rusak</h4>
                    </div>
                    <span class="text-xs font-bold text-red-600 bg-red-50 px-2.5 py-1 rounded-full">Total: {{ number_format($polresDashboardData['damage_total']) }}</span>
                </div>

                <div class="space-y-3">
                    @forelse($polresDashboardData['damage_by_material'] as $dmg)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-red-50/50 border border-red-100">
                            <span class="text-sm font-semibold text-gray-800">{{ $dmg['type_name'] }}</span>
                            <span class="text-sm font-bold text-red-600">{{ number_format($dmg['quantity']) }} unit</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Tidak ada laporan material rusak di Polres</p>
                    @endforelse
                </div>
            </div>

            <!-- Penerimaan Terbaru dari Polda -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                        <h4 class="font-bold text-gray-900">Penerimaan Material Terbaru dari Polda</h4>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($polresDashboardData['recent_receptions'] as $rec)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50/50 border border-blue-100">
                            <div>
                                <p class="text-xs font-mono text-blue-700 font-bold">{{ $rec->code }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $rec->date ? $rec->date->format('d/m/Y') : '-' }}</p>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-100 text-blue-800">
                                {{ $rec->receptionDetails->sum('quantity') }} unit
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada penerimaan material dari Polda</p>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <!-- ========================================================================= -->
        <!-- KELOMPOK 1: ANEV PNBP & RENCANA KEBUTUHAN (RENBUT) (POLDA / ADMIN) -->
        <!-- ========================================================================= -->
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-6 w-1.5 bg-blue-600 rounded-full"></div>
            <h2 class="text-xl font-bold text-gray-900">Anev PNBP & Rencana Kebutuhan (Renbut)</h2>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <!-- Target PNBP {{ $activeTargetYear }} -->
            <div
                class="relative overflow-hidden rounded-2xl bg-blue-700 p-5 shadow-lg transition-transform hover:scale-[1.02]">
                <div class="relative z-10">
                    <p class="text-sm font-medium text-blue-100 italic">Target PNBP {{ $activeTargetYear }}</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($pnbpStats['target']) }}</p>
                </div>
            </div>

            <!-- Realisasi PNBP {{ $activeTargetYear }} -->
            <div
                class="relative overflow-hidden rounded-2xl bg-purple-500 p-5 shadow-lg transition-transform hover:scale-[1.02]">
                <div class="relative z-10">
                    <p class="text-sm font-medium text-purple-100 italic">Realisasi PNBP {{ $activeTargetYear }}</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($pnbpStats['realization']) }}</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs font-medium text-purple-100">Prosentase</span>
                        <span class="text-xl font-bold text-white">{{ number_format($pnbpStats['percentage'], 0) }} %</span>
                    </div>
                </div>
            </div>

            <!-- Renbut Matreg {{ $activeTargetYear }} -->
            <div
                class="relative overflow-hidden rounded-2xl bg-cyan-500 p-5 shadow-lg transition-transform hover:scale-[1.02]">
                <div class="relative z-10">
                    <p class="text-sm font-medium text-cyan-100 italic">Renbut Matreg {{ $activeTargetYear }}</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($renbutStats['target']) }}</p>
                </div>
            </div>

            <!-- Realisasi Gunmat {{ $activeTargetYear }} -->
            <div
                class="relative overflow-hidden rounded-2xl bg-green-500 p-5 shadow-lg transition-transform hover:scale-[1.02]">
                <div class="relative z-10">
                    <p class="text-sm font-medium text-green-100 italic">Realisasi Gunmat {{ $activeTargetYear }}</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($renbutStats['realization']) }}</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs font-medium text-green-100">Prosentase</span>
                        <span class="text-xl font-bold text-white">{{ number_format($renbutStats['percentage'], 0) }} %</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Target vs Pencapaian Chart -->
        <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Grafik Target dan Pencapaian</h3>
                    <p class="text-sm text-gray-500">Berdasarkan target dan material usage per lokasi</p>
                </div>
                <div class="text-sm text-gray-500">
                    Lokasi: <span id="targetAchievementLocation" class="font-semibold text-blue-600">-</span>
                </div>
            </div>
            <div style="height: 320px; position: relative;">
                <canvas id="targetAchievementChart"></canvas>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- KELOMPOK 2: INVENTORI WAREHOUSE & RAK PENYIMPANAN -->
    <!-- ========================================================================= -->
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-6 w-1.5 bg-emerald-600 rounded-full"></div>
            <h2 class="text-xl font-bold text-gray-900">Inventori Warehouse & Rak Penyimpanan</h2>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <!-- Stock Polda -->
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

            <!-- Penerimaan Hari Ini -->
            <div
                class="relative overflow-hidden rounded-2xl bg-rose-500 p-5 shadow-lg transition-transform hover:scale-[1.02]">
                <div class="relative z-10">
                    <p class="text-sm font-medium text-rose-100 italic">Penerimaan Hari ini</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ $receptionsToday }}</p>
                </div>
            </div>

            <!-- Subsidi Material -->
            <a href="{{ route('menu-polda.material-subsidy') }}"
                class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 p-5 shadow-lg transition-transform hover:scale-[1.02] block">
                <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-white/10"></div>
                <div class="absolute -right-2 bottom-0 h-12 w-12 rounded-full bg-white/10"></div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-orange-100 italic">Subsidi Material</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($totalSubsidies) }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-2 py-0.5 text-xs font-semibold text-white">
                            {{ $totalSubsidiesConfirmed }} Dikonfirmasi
                        </span>
                    </div>
                </div>
            </a>
        </div>

        <!-- TAMPILAN RAK PENYIMPANAN WAREHOUSE POLDA -->
        <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Visualisasi Rak Penyimpanan (Polda Warehouse)</h3>
                    <p class="text-sm text-gray-500">Menampilkan isi material dan sisa stok pada masing-masing rak penyimpanan Polda.</p>
                </div>
                <div class="flex gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        {{ $warehouseRacks->count() }} Total Rak
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        {{ $warehouseRacks->filter(fn($r) => count($r['items']) > 0)->count() }} Terisi
                    </span>
                </div>
            </div>

            <!-- Racks Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($warehouseRacks as $rack)
                    @php $hasStock = count($rack['items']) > 0; @endphp
                    <div class="group relative overflow-hidden rounded-2xl border {{ $hasStock ? 'border-blue-100 bg-gradient-to-b from-white to-blue-50/10' : 'border-dashed border-gray-200 bg-gray-50/30' }} p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                        <!-- Top Accent Bar -->
                        <div class="absolute top-0 inset-x-0 h-1.5 {{ $hasStock ? 'bg-gradient-to-r from-blue-500 to-cyan-500' : 'bg-gray-300' }}"></div>
                        
                        <div class="mb-4 flex items-start justify-between">
                            <div>
                                <h4 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors text-sm truncate max-w-[150px]" title="{{ $rack['name'] }}">{{ $rack['name'] }}</h4>
                                <p class="text-[9px] text-gray-400 mt-0.5 uppercase tracking-wider font-semibold">{{ $rack['description'] ?? 'Rak Penyimpanan' }}</p>
                            </div>
                            <span class="rounded-lg p-1.5 {{ $hasStock ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-400' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </span>
                        </div>

                        <!-- Items List inside the Rack -->
                        <div class="space-y-3 min-h-[110px]">
                            @forelse($rack['items'] as $item)
                                @php
                                    // Make progress percentage relative to a reasonable default cap
                                    $percentage = min(100, ($item['quantity'] / 150) * 100);
                                    
                                    // Different colored progress bars depending on material type
                                    $colorClass = 'bg-blue-500';
                                    if (str_contains(strtolower($item['name']), 'bpkb')) {
                                        $colorClass = 'bg-indigo-600';
                                    } elseif (str_contains(strtolower($item['name']), 'stnk')) {
                                        $colorClass = 'bg-emerald-500';
                                    } elseif (str_contains(strtolower($item['name']), 'tnkb')) {
                                        $colorClass = 'bg-amber-500';
                                    } elseif (str_contains(strtolower($item['name']), 'sim')) {
                                        $colorClass = 'bg-cyan-500';
                                    }
                                @endphp
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-xs font-semibold">
                                        <span class="text-gray-600 truncate max-w-[70%]" title="{{ $item['name'] }}">{{ $item['name'] }}</span>
                                        <span class="text-gray-900 font-bold">{{ number_format($item['quantity']) }} unit</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full rounded-full {{ $colorClass }} transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center h-[110px] text-gray-400">
                                    <svg class="h-8 w-8 stroke-1 mb-1 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <span class="text-[11px] font-medium tracking-wide">Rak Kosong</span>
                                </div>
                            @endforelse
                        </div>

                        <!-- Card Footer -->
                        @if($hasStock)
                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] font-semibold text-gray-500">
                                <span>Total Stok</span>
                                <span class="text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded">{{ number_format($rack['total_quantity']) }} unit</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Hidden Charts & Tables -->
    @if(false)
        <!-- Charts Row -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
            <!-- Line Chart - Larger -->
            <div class="lg:col-span-2 rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Trend History Stock</h3>
                    <p class="text-sm text-gray-500">Total quantity 12 bulan terakhir</p>
                </div>
                <div style="height: 350px; position: relative;">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>

            <!-- Doughnut Chart -->
            <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Distribusi Stock</h3>
                    <p class="text-sm text-gray-500">Polda vs Polres</p>
                </div>
                <div style="height: 250px; position: relative;">
                    <canvas id="doughnutChart"></canvas>
                </div>
                <div class="mt-6 space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                        <div class="flex items-center gap-3">
                            <span class="h-4 w-4 rounded-full bg-blue-600"></span>
                            <span class="text-sm font-medium text-gray-700">Stock Polda</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-900">
                                {{ number_format($stockDistribution['polda_count']) }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $stockDistribution['polda'] }}%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-cyan-50">
                        <div class="flex items-center gap-3">
                            <span class="h-4 w-4 rounded-full bg-cyan-500"></span>
                            <span class="text-sm font-medium text-gray-700">Stock Polres</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-900">
                                {{ number_format($stockDistribution['polres_count']) }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $stockDistribution['polres'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Tables Section -->
        <div class="mb-8">
            <!-- Recent Distributions Table -->
            <div class="rounded-2xl border border-blue-100 bg-white shadow-lg overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Penerimaan Terbaru</h3>
                        <p class="text-sm text-gray-500">5 penerimaan terakhir</p>
                    </div>
                    <a href="#"
                        class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                        Lihat Semua
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Kode</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Lokasi</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Tanggal</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Jumlah Item</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentReceptions as $reception)
                                <tr class="hover:bg-blue-50/50 transition-colors">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="font-mono text-sm font-semibold text-blue-600">{{ $reception->code }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ $reception->regionalPolice?->name ?? 'N/A' }} →
                                        {{ $reception->policeStation?->name ?? 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $reception->date?->locale('id')->format('d M Y') ?? 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                            {{ $reception->receptionDetails->count() }} item
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                        Belum ada data penerimaan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Stock per Location Chart - Full Width -->
        <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg mb-8">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Stok per Lokasi Polres</h3>
                <p class="text-sm text-gray-500">Top 5 lokasi dengan stok terbanyak</p>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="barChart"></canvas>
            </div>
        </div>

        <!-- More Charts Row -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
            <!-- Type Distribution Pie Chart -->
            <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Distribusi Tipe Material</h3>
                    <p class="text-sm text-gray-500">Top 5 tipe material</p>
                </div>
                <div style="height: 250px; position: relative;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>

            <!-- Material Movement Chart -->
            <div class="lg:col-span-2 rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Pergerakan Material</h3>
                    <p class="text-sm text-gray-500">Material Masuk vs Keluar (6 bulan terakhir)</p>
                </div>
                <div style="height: 250px; position: relative;">
                    <canvas id="areaChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Regional Statistics Bar Chart -->
        <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg mb-8">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Statistik Stock per Polda</h3>
                <p class="text-sm text-gray-500">Top 5 Polda dengan stock terbanyak</p>
            </div>
            <div style="height: 280px; position: relative;">
                <canvas id="regionalChart"></canvas>
            </div>
        </div>

        <!-- Data Tables Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
            <!-- LastStock Table -->
            <div class="rounded-2xl border border-blue-100 bg-white shadow-lg overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Last Stock Terbaru</h3>
                    <p class="text-sm text-gray-500">5 entry terakhir</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Lokasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentLastStock as $lastStock)
                                <tr class="hover:bg-blue-50/50">
                                    <td class="px-4 py-3 text-sm font-mono text-blue-600">{{ $lastStock->code }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $lastStock->regionalPolice?->name ?? ($lastStock->policeStation?->name ?? 'N/A') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $lastStock->date?->format('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Material Damage Table -->
            <div class="rounded-2xl border border-blue-100 bg-white shadow-lg overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Material Rusak</h3>
                    <p class="text-sm text-gray-500">5 entry terakhir</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Lokasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($materialDamage as $damage)
                                <tr class="hover:bg-red-50/50">
                                    <td class="px-4 py-3 text-sm font-mono text-red-600">{{ $damage->code }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $damage->regionalPolice?->name ?? ($damage->policeStation?->name ?? 'N/A') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $damage->date?->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Material Usage Table -->
            <div class="rounded-2xl border border-blue-100 bg-white shadow-lg overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Pemakaian Material</h3>
                    <p class="text-sm text-gray-500">5 entry terakhir</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Lokasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($materialUsage as $usage)
                                <tr class="hover:bg-green-50/50">
                                    <td class="px-4 py-3 text-sm font-mono text-green-600">{{ $usage->code }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $usage->regionalPolice?->name ?? ($usage->policeStation?->name ?? 'N/A') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $usage->date?->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    @endif
</div>

@push('scripts')
    <script>
        function initDashboardScripts() {
            const targetAchievementData = @json($targetAchievementChart);
            const targetAchievementPalette = [
                '#2563eb',
                '#f97316',
                '#10b981',
                '#eab308',
                '#8b5cf6',
                '#ef4444',
                '#14b8a6',
                '#0ea5e9',
                '#a855f7',
                '#22c55e',
            ];

            const hexToRgba = (hex, alpha) => {
                const normalized = hex.replace('#', '');
                const bigint = parseInt(normalized, 16);
                const r = (bigint >> 16) & 255;
                const g = (bigint >> 8) & 255;
                const b = bigint & 255;

                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            };

            const targetAchievementCtx = document.getElementById('targetAchievementChart');
            const targetAchievementLocation = document.getElementById('targetAchievementLocation');
            let targetAchievementChart = null;
            let targetAchievementIndex = 0;

            if (targetAchievementCtx && targetAchievementData.locations.length > 0) {
                const initialLocation = targetAchievementData.locations[0];

                targetAchievementLocation.textContent = initialLocation.label;

                const targetColors = targetAchievementData.types.map((_, index) =>
                    targetAchievementPalette[index % targetAchievementPalette.length]
                );
                const achievementColors = targetColors.map((color) => hexToRgba(color, 0.35));
                const targetBackgroundColors = targetColors.map((color) => hexToRgba(color, 0.55));

                targetAchievementChart = new Chart(targetAchievementCtx, {
                    type: 'bar',
                    data: {
                        labels: targetAchievementData.types,
                        datasets: [
                            {
                                label: 'Target',
                                data: initialLocation.target,
                                backgroundColor: targetBackgroundColors,
                                borderColor: targetColors,
                                borderWidth: 1,
                                borderRadius: 6,
                            },
                            {
                                label: 'Pencapaian',
                                data: initialLocation.actual,
                                backgroundColor: achievementColors,
                                borderColor: targetColors,
                                borderWidth: 1,
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                callbacks: {
                                    label: function (context) {
                                        return `${context.dataset.label}: ${context.parsed.y.toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    callback: function (value) {
                                        return value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                setInterval(() => {
                    targetAchievementIndex = (targetAchievementIndex + 1) % targetAchievementData.locations.length;
                    const location = targetAchievementData.locations[targetAchievementIndex];

                    targetAchievementLocation.textContent = location.label;
                    targetAchievementChart.data.datasets[0].data = location.target;
                    targetAchievementChart.data.datasets[1].data = location.actual;
                    targetAchievementChart.update();
                }, 10000);
            }

            // Line Chart - History Stock Trend
            const lineCtx = document.getElementById('lineChart');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: @json($historyStockTrend['labels']),
                        datasets: [{
                            label: 'Quantity',
                            data: @json($historyStockTrend['data']),
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#2563eb',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14
                                },
                                bodyFont: {
                                    size: 13
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Doughnut Chart - Stock Distribution
            const doughnutCtx = document.getElementById('doughnutChart');
            if (doughnutCtx) {
                new Chart(doughnutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Stock Polda', 'Stock Polres'],
                        datasets: [{
                            data: [@json($stockDistribution['polda_count']), @json($stockDistribution['polres_count'])],
                            backgroundColor: ['#2563eb', '#06b6d4'],
                            borderWidth: 0,
                            spacing: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12
                            }
                        }
                    }
                });
            }

            // Bar Chart - Stock per Location
            const barCtx = document.getElementById('barChart');
            if (barCtx) {
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($stockPerLocation->map(fn($item) => $item->policeStation?->name ?? 'N/A')->toArray()),
                        datasets: [{
                            label: 'Total Stock',
                            data: @json($stockPerLocation->pluck('total_stock')->toArray()),
                            backgroundColor: ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
                            borderRadius: 8,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function (context) {
                                        return 'Stock: ' + context.parsed.y.toLocaleString() + ' unit';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    },
                                    callback: function (value) {
                                        return value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Pie Chart - Type Distribution
            const pieCtx = document.getElementById('pieChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: @json($typeDistribution['labels']),
                        datasets: [{
                            data: @json($typeDistribution['data']),
                            backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 12,
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Area Chart - Material Movement
            const areaCtx = document.getElementById('areaChart');
            if (areaCtx) {
                new Chart(areaCtx, {
                    type: 'line',
                    data: {
                        labels: @json($materialMovement['labels']),
                        datasets: [{
                            label: 'Masuk',
                            data: @json($materialMovement['in']),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Keluar',
                            data: @json($materialMovement['out']),
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            fill: true,
                            tension: 0.4
                        }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Bar Chart - Regional Stats
            const regionalCtx = document.getElementById('regionalChart');
            if (regionalCtx) {
                new Chart(regionalCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($regionalStats['labels']),
                        datasets: [{
                            label: 'Total Stock',
                            data: @json($regionalStats['data']),
                            backgroundColor: '#6366f1',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }
        document.addEventListener('DOMContentLoaded', initDashboardScripts);
        document.addEventListener('lived', initDashboardScripts);
    </script>
@endpush