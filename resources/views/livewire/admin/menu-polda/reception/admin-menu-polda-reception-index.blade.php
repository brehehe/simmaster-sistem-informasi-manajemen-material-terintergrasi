<div>
    <!-- Header -->
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">
                    Penerimaan Barang
                </h1>
                <p class="text-gray-500 mt-1">Kelola data penerimaan barang</p>
            </div>
            <a href="{{ route('menu-polda.reception.create') }}" wire:navigate
                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Tambah Data
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Table Card with Search & Filter -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <!-- Search & Filter Header -->
        <div class="p-4 border-b border-gray-100">
            <!-- Filter Card -->
            <div class="bg-white rounded-xl mb-6">
                <div class="grid grid-cols-1 md:grid-cols-{{ auth()->user()->hasRole('Admin') ? '4' : '3' }} gap-4">
                    @if(auth()->user()->hasRole('Admin'))
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Polda</label>
                            <div wire:ignore>
                                <select id="select-regional-police" x-data x-init="
                                    const selectize = $($el).selectize({
                                        dropdownParent: 'body',
                                        allowClear: true,
                                        plugins: ['clear_button'],
                                        onChange: function(val) {
                                            @this.set('regionalPoliceId', val);
                                        }
                                    })[0].selectize;
                                "
                                placeholder="Semua Polda">
                                    <option value="">Semua Polda</option>
                                    @foreach ($regionalPolices as $regional)
                                        <option value="{{ $regional->id }}" @selected($regionalPoliceId == $regional->id)>{{ $regional->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Tipe Transaksi</label>
                        <select wire:model.live="type"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            <option value="">Semua Tipe</option>
                            <option value="penerimaan">Penerimaan</option>
                            <option value="stock-awal">Stock Awal</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Material</label>
                        <div wire:ignore>
                            <select id="select-type" x-data x-init="
                                const selectize = $($el).selectize({
                                    dropdownParent: 'body',
                                    allowClear: true,
                                    plugins: ['clear_button'],
                                    onChange: function(val) {
                                        @this.set('typeId', val);
                                    }
                                })[0].selectize;
                            "
                            placeholder="Semua Material">
                                <option value="">Semua Material</option>
                                @foreach ($allTypes as $t)
                                    <option value="{{ $t->id }}" @selected($typeId == $t->id)>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Material Detail</label>
                        <div wire:ignore wire:key="select-type-detail-wrapper-{{ $typeId }}">
                            <select id="select-type-detail-{{ $typeId }}" x-data x-init="
                                const selectize = $($el).selectize({
                                    dropdownParent: 'body',
                                    allowClear: true,
                                    plugins: ['clear_button'],
                                    onChange: function(val) {
                                        @this.set('typeDetailId', val);
                                    }
                                })[0].selectize;
                            "
                            placeholder="Semua Material Detail">
                                <option value="">Semua Material Detail</option>
                                @foreach ($typeDetails as $td)
                                    <option value="{{ $td->id }}" @selected($typeDetailId == $td->id)>{{ $td->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Search and Date Filter Layout -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mt-4">
                <!-- Per Page Select (Left) -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Tampilkan</span>
                    <select wire:model.live="perPage"
                        class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-600">data</span>
                </div>

                <!-- Search & Date Filter (Right) -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto">
                    <!-- Date Range Filter -->
                    <div class="flex items-center gap-2">
                        <input wire:model.live.debounce.300ms="startDate" type="date" placeholder="Dari Tanggal"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                        <span class="text-gray-400">-</span>
                        <input wire:model.live.debounce.300ms="endDate" type="date" placeholder="Sampai Tanggal"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full sm:w-80">
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Cari kode, nama, deskripsi..."
                            class="w-full pl-4 pr-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Polda</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nomor SPPM</th>
                         <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Material</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Jumlah Total</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($receptions as $index => $reception)
                        <tr class="hover:bg-blue-50/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $receptions->firstItem() + $index }}
                            </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $reception?->regionalPolice?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $reception->code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ Str::title(Str::replace('-',' ',$reception->type)) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $reception->typeMaterial?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $reception?->date->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center font-bold">
                                {{ number_format($reception->receptionDetails->sum('quantity'),0,',','.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="viewDetail('{{ $reception->id }}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <a href="{{ route('menu-polda.reception.print', $reception->id) }}" target="_blank" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Print BAPPM">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <button wire:click="exportBappmPdf('{{ $reception->id }}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Download PDF BAPPM">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A1 1 0 0112 2.586L15.414 6A1 1 0 0116 6.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <button wire:click="exportBappmExcel('{{ $reception->id }}')" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Download Excel BAPPM">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v2a1 1 0 102 0v-2zm3-3a1 1 0 00-2 0v5a1 1 0 102 0V9zm3 4a1 1 0 10-2 0v1a1 1 0 102 0v-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <button wire:click="openDeleteModal('{{ $reception->id }}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-10 text-center">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">Tidak ada data penerimaan barang</p>
                                    <p class="text-gray-400 text-sm mt-1">Silakan tambah data baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if ($receptions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Info Text -->
                    <div class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">{{ $receptions->firstItem() }}</span>
                        sampai <span class="font-semibold">{{ $receptions->lastItem() }}</span>
                        dari <span class="font-semibold">{{ $receptions->total() }}</span> hasil
                    </div>

                    <!-- Pagination Numbers -->
                    <div class="flex items-center gap-1">
                        {{-- Previous Button --}}
                        @if ($receptions->onFirstPage())
                            <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        @else
                            <button wire:click="previousPage"
                                class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                            $currentPage = $receptions->currentPage();
                            $lastPage = $receptions->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp

                        @if ($start > 1)
                            <button wire:click="gotoPage(1)"
                                class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                1
                            </button>
                            @if ($start > 2)
                                <span class="px-2 py-2 text-sm text-gray-400">...</span>
                            @endif
                        @endif

                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $currentPage)
                                <span class="px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPage({{ $page }})"
                                    class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                    {{ $page }}
                                </button>
                            @endif
                        @endfor

                        @if ($end < $lastPage)
                            @if ($end < $lastPage - 1)
                                <span class="px-2 py-2 text-sm text-gray-400">...</span>
                            @endif
                            <button wire:click="gotoPage({{ $lastPage }})"
                                class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                {{ $lastPage }}
                            </button>
                        @endif

                        {{-- Next Button --}}
                        @if ($receptions->hasMorePages())
                            <button wire:click="nextPage"
                                class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        @else
                            <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                <div class="text-sm text-gray-600">
                    Menampilkan <span class="font-semibold">{{ $receptions->count() }}</span> hasil
                </div>
            </div>
        @endif
    </div>

    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-gray-900/70 backdrop-blur-sm" wire:click="closeModal">
                </div>

                <!-- Modal Content -->
                <div
                    class="relative inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                            <svg class="h-8 w-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Data Penerimaan Barang</h3>
                        <p class="text-gray-500">Apakah Anda yakin ingin menghapus data ini? Semua detail item akan
                            ikut terhapus. Tindakan ini tidak dapat dibatalkan.</p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button wire:click="closeModal"
                            class="flex-1 px-4 py-3 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200">
                            Batal
                        </button>
                        <button wire:click="delete"
                            class="flex-1 px-4 py-3 text-sm font-semibold text-white bg-gradient-to-r from-red-500 to-red-600 rounded-xl hover:from-red-600 hover:to-red-700 shadow-lg shadow-red-500/30 transition-all duration-200">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Detail Modal -->
    @if ($showDetailModal && $selectedReception)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-gray-900/70 backdrop-blur-sm" wire:click="closeModal">
                </div>

                <!-- Modal Content -->
                <div class="relative inline-block w-full max-w-5xl my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl">
                    
                    <div class="flex items-center justify-between p-6 border-b border-gray-100">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Detail Transaksi: {{ $selectedReception->code }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ Str::title(Str::replace('-',' ',$selectedReception->type)) }} • {{ $selectedReception->date->format('d M Y') }}</p>
                        </div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="w-full text-sm text-left align-top">
                                <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold text-center w-12">No</th>
                                        <th class="px-4 py-3 font-semibold">Detail Material</th>
                                        <th class="px-4 py-3 font-semibold">Service</th>
                                        <th class="px-4 py-3 font-semibold">Service Detail</th>
                                        <th class="px-4 py-3 font-semibold">Kode Barang</th>
                                        <th class="px-4 py-3 font-semibold">SN 1</th>
                                        <th class="px-4 py-3 font-semibold">SN 2</th>
                                        <th class="px-4 py-3 font-semibold text-center w-24">Qty</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($selectedReceptionDetails as $index => $item)
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="px-4 py-3 text-center text-gray-500">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $item->typeDetail->name ?? '-' }}</td>
                                            <td class="px-4 py-3 text-gray-600">{{ $item->service->name ?? '-' }}</td>
                                            <td class="px-4 py-3 text-gray-600">{{ $item->serviceDetail->name ?? '-' }}</td>
                                            <td class="px-4 py-3 text-gray-600 font-mono">{{ $item->item_code ?? '-' }}</td>
                                            <td class="px-4 py-3 text-gray-600 font-mono">{{ $item->number_serial_first ?? '-' }}</td>
                                            <td class="px-4 py-3 text-gray-600 font-mono">{{ $item->number_serial_second ?? '-' }}</td>
                                            <td class="px-4 py-3 text-center font-bold text-gray-900 bg-gray-50/30">{{ number_format($item->quantity,0,',','.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 italic">Tidak ada rincian item.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-100 flex justify-end gap-3">
                        <button wire:click="exportBappmExcel('{{ $selectedReception->id }}')"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-lg shadow-emerald-500/30 transition-all duration-200 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v2a1 1 0 102 0v-2zm3-3a1 1 0 00-2 0v5a1 1 0 102 0V9zm3 4a1 1 0 10-2 0v1a1 1 0 102 0v-1z" clip-rule="evenodd" />
                            </svg>
                            Export Excel BAPPM
                        </button>
                        <button wire:click="exportBappmPdf('{{ $selectedReception->id }}')"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-lg shadow-red-500/30 transition-all duration-200 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A1 1 0 0112 2.586L15.414 6A1 1 0 0116 6.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                            Export PDF BAPPM
                        </button>
                        <a href="{{ route('menu-polda.reception.print', $selectedReception->id) }}" target="_blank"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all duration-200">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                                </svg>
                                Print BAPPM
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
