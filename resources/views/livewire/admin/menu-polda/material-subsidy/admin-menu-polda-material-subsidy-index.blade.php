<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="fixed top-5 right-5 z-[9999] flex items-center gap-3 rounded-xl bg-emerald-500 px-5 py-4 text-white shadow-2xl">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed top-5 right-5 z-[9999] flex items-center gap-3 rounded-xl bg-red-500 px-5 py-4 text-white shadow-2xl">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-blue-900">Subsidi Material</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data subsidi material Polda</p>
        </div>
        @if(in_array(Auth::user()->level_menu, [1, 2]))
            <a href="{{ route('menu-polda.material-subsidy.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition-all hover:from-blue-700 hover:to-blue-800 hover:shadow-blue-500/40">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Data
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="mb-5 rounded-2xl bg-white p-5 shadow-sm border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @if(Auth::user()->hasRole('Admin'))
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Filter Polda</label>
                    <select wire:model.live="poldaFilter" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">Semua Polda</option>
                        @foreach($regionalPolices as $rp)
                            <option value="{{ $rp->id }}">{{ $rp->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Filter Status</label>
                <select wire:model.live="statusFilter" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="confirmed">Dikonfirmasi</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" wire:model.live="startDate" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" wire:model.live="endDate" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
        </div>
        <div class="mt-3 flex items-center gap-3">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span>Tampilkan</span>
                <select wire:model.live="perPage" class="rounded-lg border border-gray-200 px-2 py-1 text-sm focus:border-blue-500 focus:outline-none">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span>data</span>
            </div>
            <div class="flex-1">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari kode, penerima, catatan..."
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NO</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">KODE</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">POLDA</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">PENERIMA</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">TANGGAL</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">JUMLAH ITEM</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">STATUS</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($subsidies as $index => $subsidy)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ ($subsidies->currentPage() - 1) * $subsidies->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-mono text-sm font-semibold text-gray-800">{{ $subsidy->code }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $subsidy->regionalPolice?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $subsidy->recipient_name }}</div>
                                @if($subsidy->recipient_description)
                                    <div class="text-xs text-gray-400 truncate max-w-xs">{{ $subsidy->recipient_description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $subsidy->subsidy_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-bold text-gray-800">
                                {{ $subsidy->materialSubsidyDetails->count() }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($subsidy->status === 'confirmed')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Dikonfirmasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- View Detail -->
                                    <button wire:click="viewDetail('{{ $subsidy->id }}')"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    @if($subsidy->status === 'draft' && in_array(Auth::user()->level_menu, [1, 2]))
                                        <!-- Edit -->
                                        <a href="{{ route('menu-polda.material-subsidy.edit', $subsidy->id) }}"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <!-- Confirm -->
                                        <button wire:click="openConfirmModal('{{ $subsidy->id }}')"
                                            class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Konfirmasi Subsidi">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <!-- Delete -->
                                        <button wire:click="openDeleteModal('{{ $subsidy->id }}')"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="h-16 w-16 rounded-2xl bg-gray-100 flex items-center justify-center">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Tidak ada data subsidi material</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subsidies->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $subsidies->links() }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-gray-100 text-sm text-gray-500">
                Menampilkan {{ $subsidies->count() }} hasil
            </div>
        @endif
    </div>

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedSubsidy)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Detail Subsidi Material</h3>
                        <p class="text-sm text-gray-500 font-mono">{{ $selectedSubsidy->code }}</p>
                    </div>
                    <button wire:click="closeDetailModal" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-5">
                    <!-- Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase">Polda</p>
                            <p class="text-sm font-medium text-gray-800">{{ $selectedSubsidy->regionalPolice?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase">Tanggal</p>
                            <p class="text-sm font-medium text-gray-800">{{ $selectedSubsidy->subsidy_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase">Penerima</p>
                            <p class="text-sm font-medium text-gray-800">{{ $selectedSubsidy->recipient_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase">Status</p>
                            @if($selectedSubsidy->status === 'confirmed')
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    Dikonfirmasi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                    Draft
                                </span>
                            @endif
                        </div>
                        @if($selectedSubsidy->recipient_description)
                            <div class="col-span-2">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Keterangan Penerima</p>
                                <p class="text-sm text-gray-700">{{ $selectedSubsidy->recipient_description }}</p>
                            </div>
                        @endif
                        @if($selectedSubsidy->notes)
                            <div class="col-span-2">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Catatan</p>
                                <p class="text-sm text-gray-700">{{ $selectedSubsidy->notes }}</p>
                            </div>
                        @endif
                        @if($selectedSubsidy->confirmed_at)
                            <div class="col-span-2">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Dikonfirmasi Pada</p>
                                <p class="text-sm text-gray-700">{{ $selectedSubsidy->confirmed_at->format('d M Y, H:i') }} oleh {{ $selectedSubsidy->confirmedByUser?->name ?? '-' }}</p>
                            </div>
                        @endif
                    </div>
                    <!-- Items -->
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase mb-2">Item Material</p>
                        <div class="rounded-xl border border-gray-100 overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Material</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Detail</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($selectedSubsidy->materialSubsidyDetails as $detail)
                                        <tr>
                                            <td class="px-4 py-2 font-medium text-gray-800">{{ $detail->type?->name ?? '-' }}</td>
                                            <td class="px-4 py-2 text-gray-500">{{ $detail->typeDetail?->name ?? '-' }}</td>
                                            <td class="px-4 py-2 text-right font-bold text-gray-800">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Confirm Modal -->
    @if($showConfirmModal && $selectedSubsidy)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-12 w-12 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Konfirmasi Subsidi</h3>
                            <p class="text-sm text-gray-500">{{ $selectedSubsidy->code }}</p>
                        </div>
                    </div>
                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 mb-5">
                        <p class="text-sm text-amber-800 font-medium">⚠️ Perhatian!</p>
                        <p class="text-sm text-amber-700 mt-1">
                            Mengkonfirmasi subsidi ini akan <strong>langsung mengurangi stok Polda</strong> sebanyak total kuantitas item di bawah. Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                    <div class="space-y-2 mb-5">
                        @foreach($selectedSubsidy->materialSubsidyDetails as $detail)
                            <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                                <span class="text-sm text-gray-700">{{ $detail->type?->name ?? '-' }}
                                    @if($detail->typeDetail) — {{ $detail->typeDetail->name }} @endif
                                </span>
                                <span class="text-sm font-bold text-gray-900">{{ number_format($detail->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex gap-3">
                        <button wire:click="closeConfirmModal"
                            class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button wire:click="confirmSubsidy" wire:loading.attr="disabled"
                            class="flex-1 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-emerald-600 hover:to-emerald-700 disabled:opacity-50">
                            <span wire:loading.remove wire:target="confirmSubsidy">Ya, Konfirmasi</span>
                            <span wire:loading wire:target="confirmSubsidy">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="w-full max-w-sm rounded-2xl bg-white shadow-2xl">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Hapus Subsidi</h3>
                            <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-5">Apakah Anda yakin ingin menghapus data subsidi material ini?</p>
                    <div class="flex gap-3">
                        <button wire:click="closeDeleteModal"
                            class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button wire:click="delete" wire:loading.attr="disabled"
                            class="flex-1 rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-red-600 hover:to-red-700 disabled:opacity-50">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
