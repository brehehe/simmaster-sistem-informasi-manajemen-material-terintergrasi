<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">Koreksi No. Seri & Detail (Polda)</h1>
                <p class="text-gray-500 mt-1">Perbaiki nomor seri, varian detail material, atau penempatan rak pada stok aktif Polda</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('menu-polda.stock') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Lihat Stock Material
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-6 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(auth()->user()->hasRole('Admin'))
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Polda</label>
                    <select wire:model.live="regionalPoliceId"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all text-sm bg-gray-50 focus:bg-white">
                        <option value="">Semua Polda</option>
                        @foreach ($regionalPolices as $police)
                            <option value="{{ $police->id }}">{{ $police->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Jenis Material</label>
                <select wire:model.live="typeId"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all text-sm bg-gray-50 focus:bg-white">
                    <option value="">Semua Jenis Material</option>
                    @foreach ($allTypes as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Detail Material</label>
                <select wire:model.live="typeDetailId"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all text-sm bg-gray-50 focus:bg-white">
                    <option value="">Semua Detail Material</option>
                    @foreach ($typeDetails as $td)
                        <option value="{{ $td->id }}">{{ $td->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <!-- Search & Per Page Header -->
        <div class="p-4 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Per Page -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Tampilkan</span>
                    <select wire:model.live="perPage"
                        class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-gray-50 focus:bg-white text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-600">data</span>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-80">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari seri, kode, rak, jenis..."
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-gray-50 focus:bg-white text-sm">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 text-left">
                        <th class="px-5 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        @if(auth()->user()->hasRole('Admin'))
                            <th class="px-5 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Polda</th>
                        @endif
                        <th class="px-5 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Jenis Material</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Detail Material</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Kode Seri</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">No Seri Awal</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">No Seri Akhir</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Sisa Stok</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Letak Rak</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stockDetails as $index => $detail)
                        <tr class="hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $stockDetails->firstItem() + $index }}
                            </td>
                            @if(auth()->user()->hasRole('Admin'))
                                <td class="px-5 py-4 text-sm text-gray-700 font-medium">
                                    {{ $detail->regionalPolice->name ?? '-' }}
                                </td>
                            @endif
                            <td class="px-5 py-4 text-sm">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                    {{ $detail->type->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-900 font-medium">
                                {{ $detail->typeDetail->name ?? 'PNBP / Tanpa Detail' }}
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-gray-800">
                                {{ $detail->code ?: '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm font-mono text-gray-700">
                                {{ $detail->number_serial_first ?: '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm font-mono text-gray-700">
                                {{ $detail->number_serial_second ?: '-' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-sm font-bold text-blue-600">
                                    {{ number_format($detail->quantity, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $detail->rack->name ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <button wire:click="edit('{{ $detail->id }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all text-xs font-semibold shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Edit Seri
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('Admin') ? 10 : 9 }}" class="p-10 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">Tidak ada data stok yang ditemukan</p>
                                    <p class="text-gray-400 text-sm mt-1">Coba sesuaikan filter atau kata kunci pencarian</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($stockDetails->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $stockDetails->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Edit Dialog -->
    @if ($isOpenModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" 
                 wire:click="closeModal" aria-hidden="true"></div>

            <!-- Modal Panel -->
            <div class="relative z-10 w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100 my-8 transform transition-all text-left">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-cyan-600 px-6 py-4 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur-md flex items-center justify-center text-white shadow-inner">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white tracking-wide">Edit No. Seri & Detail Material</h3>
                            <p class="text-xs text-blue-100/80">Koreksi rincian seri, varian detail, dan penempatan stok Polda</p>
                        </div>
                    </div>
                    <button wire:click="closeModal" 
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-white/80 hover:text-white hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form wire:submit.prevent="save">
                    <div class="p-6 space-y-5">
                        <!-- Material Info Banner -->
                        <div class="p-3.5 bg-gradient-to-r from-blue-50/90 via-indigo-50/40 to-blue-50/50 rounded-xl border border-blue-100/80 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/25">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider block">Jenis Material</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $selectedTypeName }}</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-blue-200/70 rounded-full text-xs font-semibold text-blue-700 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Stok Polda
                            </span>
                        </div>

                        <!-- Varian / Detail Material Selection -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Varian / Detail Material
                            </label>
                            <select wire:model="selectedTypeDetailId"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm bg-gray-50/70 focus:bg-white transition-all text-gray-800">
                                <option value="">-- PNBP / Tanpa Detail Material --</option>
                                @foreach ($availableTypeDetails as $atd)
                                    <option value="{{ $atd->id }}">{{ $atd->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Pilih varian detail jika material memiliki pembagian kategori (misal: TNKB R2/R4, dsb).
                            </p>
                            @error('selectedTypeDetailId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Card Group: Kode Seri & Rentang Nomor Seri & Stok -->
                        <div class="p-4 bg-gray-50/70 rounded-xl border border-gray-200/80 space-y-3.5">
                            <div class="flex items-center justify-between pb-2 border-b border-gray-200/60">
                                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                    </svg>
                                    Nomor Seri & Jumlah Stok
                                </span>
                                <span class="text-[11px] text-gray-400">Kosongkan jika material tidak berseri</span>
                            </div>

                            <!-- Row 1: Kode Awalan & Jumlah Stok -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                                        Kode / Awalan Seri
                                    </label>
                                    <input wire:model="code" type="text" placeholder="Misal: W / SB (opsional)"
                                        class="w-full px-3.5 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm bg-white uppercase font-semibold text-gray-800 tracking-wider">
                                    @error('code') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                                        Jumlah Stok Fisik <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="quantity" type="number" min="0" step="1"
                                        class="w-full px-3.5 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm bg-white font-bold text-blue-600">
                                    @error('quantity') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Row 2: Seri Awal & Seri Akhir -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                                        Nomor Seri Awal
                                    </label>
                                    <input wire:model="number_serial_first" type="text" placeholder="Contoh: 09057001"
                                        class="w-full px-3.5 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm bg-white font-mono text-gray-800">
                                    @error('number_serial_first') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                                        Nomor Seri Akhir
                                    </label>
                                    <input wire:model="number_serial_second" type="text" placeholder="Contoh: 09057500"
                                        class="w-full px-3.5 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm bg-white font-mono text-gray-800">
                                    @error('number_serial_second') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Penempatan Rak & Keterangan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Penempatan Rak
                                </label>
                                <select wire:model="rack_id"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm bg-gray-50/70 focus:bg-white text-gray-800">
                                    <option value="">-- Tanpa Rak / Belum Masuk --</option>
                                    @foreach ($availableRacks as $rack)
                                        <option value="{{ $rack->id }}">{{ $rack->name }}</option>
                                    @endforeach
                                </select>
                                @error('rack_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Catatan / Alasan Koreksi
                                </label>
                                <textarea wire:model="description" rows="2" placeholder="Catatan perbaikan (opsional)..."
                                    class="w-full px-3.5 py-2 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm bg-gray-50/70 focus:bg-white text-gray-800 resize-none"></textarea>
                                @error('description') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-gray-50/90 border-t border-gray-100 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-800 bg-white border border-gray-200 hover:bg-gray-100 rounded-xl transition-all shadow-sm">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 rounded-xl shadow-lg shadow-blue-500/25 transition-all disabled:opacity-50">
                            <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Perubahan
                            </span>
                            <span wire:loading wire:target="save" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
