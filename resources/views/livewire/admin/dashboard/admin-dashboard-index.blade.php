<div>
    <!-- Page Header -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 lg:text-3xl">Dashboard</h1>
            <p class="mt-1 text-gray-500">Visualisasi data dan statistik sistem manajemen material.</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <button wire:click="toggleDataKendaraan"
                class="inline-flex items-center gap-2 rounded-xl border {{ $showDataKendaraan ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'border-blue-200 bg-white text-blue-700 hover:bg-blue-50' }} px-4 py-2.5 text-sm font-semibold shadow-sm transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2" />
                </svg>
                {{ $showDataKendaraan ? 'Tutup Pengecekan' : 'Data Kendaraan' }}
            </button>
            @if(!$showDataKendaraan)
                <button wire:click="$refresh"
                    class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-medium text-blue-700 shadow-sm hover:bg-blue-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            @endif
        </div>
    </div>

    @if($showDataKendaraan)
        <!-- ========================================================================= -->
        <!-- MODUL KHUSUS: PENGECEKAN DATA KENDARAAN (SAMSAT, BAPENDA & ERI) -->
        <!-- ========================================================================= -->
        <div x-transition class="mb-10 rounded-2xl border border-blue-100 bg-white p-6 shadow-xl">
            <!-- Header Modul -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-gray-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Pengecekan Data Kendaraan & Material SBST</h2>
                        <p class="text-xs text-gray-500">Terintegrasi dengan Database BAPENDA Jatim dan ERI Korlantas Polri</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button wire:click="openImportEriModal" type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-xs font-bold shadow-md shadow-emerald-600/20 transition-all">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Import Data ERI
                    </button>
                    @if($vehicleData)
                        <button onclick="window.print()" type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-xs font-bold shadow-md shadow-blue-600/20 transition-all">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Export / Download PDF Hasil Cek
                        </button>
                    @endif
                    <button wire:click="toggleDataKendaraan" type="button"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 px-3.5 py-2 text-xs font-semibold transition-all">
                        Kembali ke Dashboard
                    </button>
                </div>
            </div>

            <!-- Jenis Dokumen -->
            <div class="mb-6 p-4 rounded-xl bg-blue-50/60 border border-blue-100">
                <p class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-2">Jenis Dokumen Yang Dicek <span class="text-red-500">*</span></p>
                <div class="flex gap-6">
                    @foreach(['STNK', 'TNKB', 'BPKB'] as $doc)
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="{{ $doc }}" wire:model.live="selectedDocTypes"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm font-bold text-gray-800">{{ $doc }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Form Pencarian Nopol -->
            <div class="p-6 rounded-2xl border border-gray-200 bg-gray-50/50 mb-8">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Masukkan Nomor Polisi (NOPOL)</label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-grow">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">🚘</span>
                        <input type="text" wire:model.lazy="searchNopol" placeholder="Contoh: L 1111 AAA"
                            class="w-full rounded-xl border-gray-300 bg-white pl-11 pr-4 py-3 font-mono font-black text-lg text-gray-900 uppercase tracking-widest focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-sm">
                    </div>
                    <button wire:click="cekKendaraan" wire:loading.attr="disabled"
                        class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 px-8 py-3 text-sm font-bold text-white shadow-md shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="cekKendaraan">Cek Data Kendaraan</span>
                        <span wire:loading wire:target="cekKendaraan">Memeriksa BAPENDA...</span>
                    </button>
                </div>
            </div>

            @if($vehicleData)
                <!-- Vehicle Data Display & Printable Sheet -->
                <div class="mb-8 rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm" id="printableVehicleCheck">
                    <!-- Status Banner -->
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-4 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-white animate-pulse"></span>
                            <p class="text-sm font-bold uppercase tracking-wide">Data Kendaraan Ditemukan: {{ $vehicleData['nopol'] }} — {{ $vehicleData['owner'] }}</p>
                        </div>
                        <span class="text-xs bg-white/20 px-3 py-1 rounded-full font-mono font-bold">TERVERIFIKASI BAPENDA & ERI</span>
                    </div>

                    <div class="p-6">
                        <!-- Spek Kendaraan Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Pemilik *</label>
                                <input type="text" value="{{ $vehicleData['owner'] }}" readonly class="w-full rounded-lg border-gray-200 bg-gray-50 p-2.5 text-xs font-bold text-gray-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">NIK</label>
                                <input type="text" value="{{ $vehicleData['nik'] }}" readonly class="w-full rounded-lg border-gray-200 bg-gray-50 p-2.5 text-xs font-mono font-bold text-gray-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">No HP</label>
                                <input type="text" value="{{ $vehicleData['hp'] }}" readonly class="w-full rounded-lg border-gray-200 bg-gray-50 p-2.5 text-xs font-bold text-gray-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">No Rangka</label>
                                <input type="text" value="{{ $vehicleData['chassis'] }}" readonly class="w-full rounded-lg border-gray-200 bg-gray-50 p-2.5 text-xs font-mono font-bold text-gray-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">No Mesin</label>
                                <input type="text" value="{{ $vehicleData['engine'] }}" readonly class="w-full rounded-lg border-gray-200 bg-gray-50 p-2.5 text-xs font-mono font-bold text-gray-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Merk</label>
                                <input type="text" value="{{ $vehicleData['brand'] }}" readonly class="w-full rounded-lg border-gray-200 bg-gray-50 p-2.5 text-xs font-bold text-gray-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tipe</label>
                                <input type="text" value="{{ $vehicleData['type'] }}" readonly class="w-full rounded-lg border-gray-200 bg-gray-50 p-2.5 text-xs font-bold text-gray-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Warna</label>
                                <input type="text" value="{{ $vehicleData['color'] }}" readonly class="w-full rounded-lg border-gray-200 bg-gray-50 p-2.5 text-xs font-bold text-gray-900">
                            </div>
                        </div>

                        <!-- Serials Banner -->
                        <div class="mb-6 rounded-xl bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-950 p-5 text-white shadow-md">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-blue-300 mb-3">Identitas Nomor Material SBST Terdaftar</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="p-3 bg-white/10 rounded-lg border border-white/10">
                                    <span class="text-[10px] uppercase font-bold text-blue-200">No. Seri BPKB</span>
                                    <p class="font-mono text-base font-extrabold text-amber-300 mt-0.5">{{ $vehicleData['bpkb_serial'] }}</p>
                                </div>
                                <div class="p-3 bg-white/10 rounded-lg border border-white/10">
                                    <span class="text-[10px] uppercase font-bold text-blue-200">No. Seri STNK</span>
                                    <p class="font-mono text-base font-extrabold text-emerald-300 mt-0.5">{{ $vehicleData['stnk_serial'] }}</p>
                                </div>
                                <div class="p-3 bg-white/10 rounded-lg border border-white/10">
                                    <span class="text-[10px] uppercase font-bold text-blue-200">No. Polisi / TNKB</span>
                                    <p class="font-mono text-base font-extrabold text-white mt-0.5">{{ $vehicleData['tnkb_serial'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Lembar Tanda Tangan Dinas (Exportable) -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest text-center mb-6">Lembar Pengesahan Hasil Pemeriksaan</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-center text-xs">
                                <div>
                                    <p class="font-semibold text-gray-600">Mengetahui,</p>
                                    <p class="font-bold text-gray-900 uppercase">KASUBDIT REGIDENT DITLANTAS POLDA JATIM</p>
                                    <div class="h-20 flex items-center justify-center">
                                        <span class="text-gray-300 italic text-[10px]">[ Tanda Tangan & Cap Dinas ]</span>
                                    </div>
                                    <p class="font-bold text-gray-900 underline uppercase">YANTO MULYANTO P, S.H., S.I.K., M.H., M.Si.</p>
                                    <p class="text-gray-500">AKBP NRP 86052014</p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-600">Petugas Pemeriksa,</p>
                                    <p class="font-bold text-gray-900 uppercase">KASI FASMAT SBST DITLANTAS POLDA JATIM</p>
                                    <div class="h-20 flex items-center justify-center">
                                        <span class="text-gray-300 italic text-[10px]">[ Tanda Tangan & Cap Dinas ]</span>
                                    </div>
                                    <p class="font-bold text-gray-900 underline uppercase">AYIP RIZAL, S.E., M.M.</p>
                                    <p class="text-gray-500">KOMPOL NRP 84091823</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tabel Riwayat Pengecekan Kendaraan -->
            <div class="mt-8 rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 bg-gray-50/50">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Riwayat Pengecekan Kendaraan</h3>
                        <p class="text-xs text-gray-500">Daftar log kendaraan yang telah dicek pada sistem</p>
                    </div>
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800">{{ count($vehicleCheckHistory) }} Riwayat</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">No. Polisi</th>
                                <th class="px-4 py-3">Nama Pemilik</th>
                                <th class="px-4 py-3">Merk / Tipe</th>
                                <th class="px-4 py-3">No. Seri BPKB</th>
                                <th class="px-4 py-3">No. Seri STNK</th>
                                <th class="px-4 py-3">Waktu Cek</th>
                                <th class="px-4 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($vehicleCheckHistory as $index => $history)
                                <tr class="hover:bg-blue-50/40 transition-colors">
                                    <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-mono font-bold text-blue-700">{{ $history['nopol'] }}</td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $history['owner'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $history['brand_type'] }}</td>
                                    <td class="px-4 py-3 font-mono text-gray-700">{{ $history['bpkb'] }}</td>
                                    <td class="px-4 py-3 font-mono text-gray-700">{{ $history['stnk'] }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $history['checked_at'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800">
                                            {{ $history['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-gray-400">Belum ada riwayat pengecekan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Import ERI -->
        @if($showImportEriModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                        <h3 class="text-base font-bold text-gray-900">Import Data ERI Korlantas Polri</h3>
                        <button wire:click="closeImportEriModal" class="text-gray-400 hover:text-gray-600">✕</button>
                    </div>

                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Polisi (NOPOL) <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="eriImportNopol" placeholder="Contoh: L 1234 XY" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 uppercase">
                            @error('eriImportNopol') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Seri BPKB</label>
                            <input type="text" wire:model="eriImportBpkb" placeholder="Contoh: MU998811" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 uppercase">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Seri STNK</label>
                            <input type="text" wire:model="eriImportStnk" placeholder="Contoh: 04992100" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                        <button wire:click="closeImportEriModal" type="button" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                        <button wire:click="processImportEri" type="button" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow">Simpan & Sinkronkan</button>
                    </div>
                </div>
            </div>
        @endif
    @else

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

        <!-- Grafik Fluktuasi Harian Realisasi PNBP & Gunmat (Polda) -->
        <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Grafik Fluktuasi Harian Realisasi PNBP & Gunmat (Tingkat Polda)</h3>
                    <p class="text-sm text-gray-500">Pergerakan harian pertanggal bulan <span class="font-semibold text-blue-600">{{ $dailyPnbpGunmatChart['month_name'] }}</span></p>
                </div>
                <div class="flex items-center gap-2 text-xs flex-wrap">
                    <span class="flex items-center gap-1.5 font-semibold text-purple-700 bg-purple-50 px-3 py-1.5 rounded-lg border border-purple-200 shadow-sm">
                        <span class="h-2.5 w-2.5 rounded-full bg-purple-600"></span> Realisasi PNBP (Rp)
                    </span>
                    <span class="flex items-center gap-1.5 font-semibold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200 shadow-sm">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Realisasi Gunmat (Unit)
                    </span>
                </div>
            </div>
            <div style="height: 320px; position: relative;">
                <canvas id="dailyPnbpGunmatChartCanvas"></canvas>
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

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 mb-6">
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
                    <div class="mt-2">
                        <span class="text-xs text-violet-100">Seluruh Transaksi</span>
                    </div>
                </div>
            </div>

            <!-- Penerimaan Hari Ini -->
            <div
                class="relative overflow-hidden rounded-2xl bg-emerald-600 p-5 shadow-lg transition-transform hover:scale-[1.02]">
                <div class="relative z-10">
                    <p class="text-sm font-medium text-emerald-100 italic">Penerimaan Hari ini</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ $receptionsToday }}</p>
                    <div class="mt-2">
                        <span class="text-xs text-emerald-100">Batch Hari Ini</span>
                    </div>
                </div>
            </div>

            <!-- Material Rusak (NEW) -->
            <a href="{{ route('menu-polda.material-damage') }}"
                class="relative overflow-hidden rounded-2xl bg-rose-600 p-5 shadow-lg transition-transform hover:scale-[1.02] block">
                <div class="relative z-10">
                    <p class="text-sm font-medium text-rose-100 italic">Material Rusak</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($totalMaterialDamage) }}</p>
                    <div class="mt-2">
                        <span class="text-xs text-rose-100">Total Rusak / Afkir</span>
                    </div>
                </div>
            </a>

            <!-- Subsidi Material -->
            <a href="{{ route('menu-polda.material-subsidy') }}"
                class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 p-5 shadow-lg transition-transform hover:scale-[1.02] block">
                <div class="relative z-10">
                    <p class="text-sm font-medium text-orange-100 italic">Subsidi Material</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ number_format($totalSubsidies) }}</p>
                    <div class="mt-2">
                        <span class="text-xs text-white bg-white/20 px-2 py-0.5 rounded-full font-semibold">
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
                    <p class="text-sm text-gray-500">Menampilkan isi material, sisa stok, dan prediksi ketersediaan pada masing-masing rak penyimpanan Polda.</p>
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
                    <div class="group relative overflow-hidden rounded-2xl border {{ $hasStock ? 'border-blue-100 bg-gradient-to-b from-white to-blue-50/10' : 'border-dashed border-gray-200 bg-gray-50/30' }} p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md flex flex-col justify-between">
                        <!-- Top Accent Bar -->
                        <div class="absolute top-0 inset-x-0 h-1.5 {{ $hasStock ? 'bg-gradient-to-r from-blue-500 to-cyan-500' : 'bg-gray-300' }}"></div>
                        
                        <div>
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
                            <div class="space-y-3 min-h-[100px]">
                                @forelse($rack['items'] as $item)
                                    @php
                                        $percentage = min(100, ($item['quantity'] / 150) * 100);
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
                                    <div class="flex flex-col items-center justify-center h-[100px] text-gray-400">
                                        <svg class="h-8 w-8 stroke-1 mb-1 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <span class="text-[11px] font-medium tracking-wide">Rak Kosong</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Card Footer: Total Stok & Prediksi Habis (Page 3) -->
                        <div class="mt-4 pt-3 border-t border-gray-100 space-y-2">
                            @if($hasStock)
                                <div class="flex items-center justify-between text-[11px] font-semibold text-gray-500">
                                    <span>Total Stok</span>
                                    <span class="text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded">{{ number_format($rack['total_quantity']) }} unit</span>
                                </div>
                            @endif
                            <!-- Indikator Prediksi Habis -->
                            <div class="flex items-center justify-between text-[10px] font-semibold {{ $hasStock ? 'text-amber-800 bg-amber-50/80 border-amber-200' : 'text-gray-400 bg-gray-50 border-gray-200' }} px-2 py-1.5 rounded-lg border">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Prediksi Habis:
                                </span>
                                <span class="font-bold {{ $hasStock ? 'text-amber-900' : 'text-gray-400' }}">{{ $rack['prediksi_habis'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
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
    @endif
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

            // Daily PNBP & Gunmat Fluctuation Chart (Polda)
            const dailyChartCtx = document.getElementById('dailyPnbpGunmatChartCanvas');
            if (dailyChartCtx) {
                const dailyData = @json($dailyPnbpGunmatChart);
                new Chart(dailyChartCtx, {
                    type: 'bar',
                    data: {
                        labels: dailyData.labels,
                        datasets: [
                            {
                                type: 'line',
                                label: 'Realisasi PNBP (Rp)',
                                data: dailyData.pnbp,
                                borderColor: '#9333ea',
                                backgroundColor: 'rgba(147, 51, 234, 0.1)',
                                borderWidth: 3,
                                yAxisID: 'yPnbp',
                                tension: 0.35,
                                fill: true,
                                pointRadius: 3,
                                pointBackgroundColor: '#9333ea',
                            },
                            {
                                type: 'bar',
                                label: 'Realisasi Gunmat (Unit)',
                                data: dailyData.gunmat,
                                backgroundColor: 'rgba(16, 185, 129, 0.75)',
                                borderColor: '#10b981',
                                borderWidth: 1,
                                borderRadius: 4,
                                yAxisID: 'yGunmat',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if (context.dataset.yAxisID === 'yPnbp') {
                                            return 'Realisasi PNBP: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                        }
                                        return 'Penggunaan Material: ' + context.parsed.y.toLocaleString('id-ID') + ' unit';
                                    }
                                }
                            }
                        },
                        scales: {
                            yPnbp: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value >= 1000000 ? (value/1000000) + ' Jt' : value.toLocaleString('id-ID'));
                                    }
                                }
                            },
                            yGunmat: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: { drawOnChartArea: false },
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('id-ID') + ' unit';
                                    }
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            const targetAchievementCtx = document.getElementById('targetAchievementChart');
            const targetAchievementLocation = document.getElementById('targetAchievementLocation');
            let targetAchievementChart = null;
            let targetAchievementIndex = 0;

            if (targetAchievementCtx && targetAchievementData.locations && targetAchievementData.locations.length > 0) {
                const initialLocation = targetAchievementData.locations[0];

                if (targetAchievementLocation) {
                    targetAchievementLocation.textContent = initialLocation.label;
                }

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

                    if (targetAchievementLocation) {
                        targetAchievementLocation.textContent = location.label;
                    }
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