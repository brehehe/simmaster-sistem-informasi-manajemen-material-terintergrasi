<div>
    <!-- Header -->
    <div class="mb-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Manajemen Target</h1>
                <p class="text-gray-500 mt-1 text-sm">Kelola pagu target tahunan Ditlantas Polda Jatim serta alokasi renbut materiil per polres.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('master.target.capaian', ['selectedYear' => $selectedYear ?: (now()->format('Y'))]) }}" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-purple-500/25 transition-all duration-200 active:scale-95 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Capaian PNBP</span>
                </a>

                <button wire:click="openCreateModal"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-blue-500/25 transition-all duration-200 active:scale-95 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Tambah Target</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session()->has('success'))
        <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-in fade-in duration-200">
            <div class="flex items-center gap-3">
                <div class="p-1.5 bg-emerald-100 rounded-lg text-emerald-600 shrink-0">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm animate-in fade-in duration-200">
            <div class="flex items-center gap-3">
                <div class="p-1.5 bg-rose-100 rounded-lg text-rose-600 shrink-0">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                </div>
                <span class="font-medium text-sm">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <!-- Filter Bar -->
        <div class="p-4 sm:p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50/50 to-blue-50/20">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Left: Per Page & Year Search Filter -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <!-- Per Page -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tampilkan</span>
                        <select wire:model.live="perPage"
                            class="px-3 py-2 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white text-xs font-semibold text-gray-700 shadow-sm">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                    <!-- Filter Tahun (Select Search / Selectize) -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Filter Tahun:</span>
                        <div wire:ignore class="w-48">
                            <select id="select-year-filter" x-data x-init="
                                const selectize = $($el).selectize({
                                    dropdownParent: 'body',
                                    allowClear: true,
                                    placeholder: 'Cari / Pilih Tahun...',
                                    onChange: function(val) {
                                        @this.set('selectedYear', val);
                                    }
                                })[0].selectize;

                                $watch('$wire.selectedYear', function(newVal) {
                                    if (selectize && selectize.getValue() !== newVal) {
                                        selectize.setValue(newVal, true);
                                    }
                                });
                            ">
                                <option value="all">Semua Tahun</option>
                                @foreach ($availableYears as $y)
                                    <option value="{{ $y }}" @selected($selectedYear == (string) $y)>
                                        Tahun {{ $y }} {{ ((string)$y === (string)now()->format('Y')) ? '(Tahun Ini)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if ($selectedYear && $selectedYear !== 'all')
                            <button wire:click="$set('selectedYear', 'all')"
                                class="p-1.5 text-xs text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-100 transition-colors"
                                title="Reset Filter Tahun">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Right: Text Search -->
                <div class="relative w-full lg:w-80">
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Cari nama, deskripsi, tahun..."
                        class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white text-xs font-medium text-gray-700 shadow-sm">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 divide-y divide-gray-100">
                <thead class="bg-gray-50/80 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="py-3.5 px-5 text-center w-14">No</th>
                        <th class="py-3.5 px-5">Nama Target</th>
                        <th class="py-3.5 px-5 text-center w-28">Tahun</th>
                        <th class="py-3.5 px-5">Deskripsi</th>
                        <th class="py-3.5 px-5 text-center w-28">Status</th>
                        <th class="py-3.5 px-5 w-44">Tanggal Dibuat</th>
                        <th class="py-3.5 px-5 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($targets as $index => $target)
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="py-4 px-5 text-center text-xs text-gray-400 font-mono">{{ $targets->firstItem() + $index }}</td>
                            <td class="py-4 px-5">
                                <div class="font-bold text-gray-900">{{ $target->name }}</div>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-black {{ $target->year == now()->format('Y') ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $target->year }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-xs text-gray-500">
                                <div class="max-w-md truncate" title="{{ $target->description }}">
                                    {{ $target->description ?? '-' }}
                                </div>
                            </td>
                            <td class="py-4 px-5 text-center">
                                @if ($target->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-xs text-gray-500">
                                {{ $target->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-4 px-5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Edit Button (Modal) -->
                                    <button wire:click="openEditModal('{{ $target->id }}')"
                                        class="p-2 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition-all hover:scale-105"
                                        title="Edit Target">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>

                                    <!-- Detail Matriks Renbut -->
                                    <a href="{{ route('master.target.detail', ['target_id' => $target->id]) }}" 
                                        class="p-2 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all hover:scale-105"
                                        title="Detail Kuota Matriks Polres">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>

                                    <!-- Capaian PNBP Bulanan -->
                                    <a href="{{ route('master.target.capaian', ['selectedYear' => $target->year]) }}" 
                                        class="p-2 rounded-xl bg-purple-50 text-purple-600 hover:bg-purple-100 transition-all hover:scale-105"
                                        title="Capaian PNBP TA {{ $target->year }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </a>

                                    <!-- Delete Button (Modal) -->
                                    <button wire:click="openDeleteModal('{{ $target->id }}')"
                                        class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition-all hover:scale-105"
                                        title="Hapus Target">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-3 bg-gray-100 rounded-2xl text-gray-400 mb-3">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </div>
                                    <p class="text-gray-600 font-bold">Tidak ada target yang sesuai</p>
                                    <p class="text-gray-400 text-xs mt-1">Coba sesuaikan kata kunci pencarian atau pilih tahun lain.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if ($targets->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $targets->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @include('livewire.admin.master.target.admin-master-target-modal')

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-gray-900/70 backdrop-blur-sm" wire:click="closeDeleteModal"></div>

                <!-- Modal Content -->
                <div class="relative inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl animate-in fade-in zoom-in-95 duration-200">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-100 mb-4">
                            <svg class="h-8 w-8 text-rose-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Target?</h3>
                        <p class="text-gray-500 text-sm">
                            Apakah Anda yakin ingin menghapus target ini beserta seluruh rincian kuotanya? Tindakan ini tidak dapat dibatalkan.
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
