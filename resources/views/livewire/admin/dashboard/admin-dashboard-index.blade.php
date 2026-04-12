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

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
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
                    <span class="text-xl font-bold text-white">{{ number_format($renbutStats['percentage'], 0) }}
                        %</span>
                </div>
            </div>
        </div>

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
    </div>

    <!-- Target vs Pencapaian Chart -->
    <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg mb-8">
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
                    <t body class="divide-y divide-gray-100">
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
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
        });
    </script>
@endpush