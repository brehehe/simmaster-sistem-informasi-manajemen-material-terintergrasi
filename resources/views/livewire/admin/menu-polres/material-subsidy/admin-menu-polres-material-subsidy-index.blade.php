<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">🔄 Material Subsidi Silang (Polres)</h1>
            <p class="text-gray-500 text-sm mt-0.5">Kelola penyerahan / pengeluaran material subsidi silang antar unit atau Polres</p>
        </div>
        <div>
            <a href="{{ route('menu-polres.material-subsidy.create') }}" wire:navigate
                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-blue-500/25 transition-all text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Tambah Subsidi Silang
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Total Transaksi</div>
            <div class="text-2xl font-bold">{{ number_format($totalSubsidies, 0, ',', '.') }}</div>
            <div class="text-xs opacity-70 mt-1">Subsidi silang</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-600 to-teal-600 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Total Unit Disubsidikan</div>
            <div class="text-2xl font-bold">{{ number_format($totalItems, 0, ',', '.') }}</div>
            <div class="text-xs opacity-70 mt-1">Material keluar</div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Status Draft</div>
            <div class="text-2xl font-bold">{{ $subsidies->filter(fn($s) => $s->status === 'draft')->count() }}</div>
            <div class="text-xs opacity-70 mt-1">Belum dikonfirmasi</div>
        </div>
        <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Terkonfirmasi</div>
            <div class="text-2xl font-bold">{{ $subsidies->filter(fn($s) => $s->status === 'confirmed')->count() }}</div>
            <div class="text-xs opacity-70 mt-1">Stok berkurang</div>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pencarian</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kode, penerima, catatan..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft (Belum Konfirmasi)</option>
                    <option value="confirmed">Confirmed (Terkonfirmasi)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dari Tanggal</label>
                <input type="date" wire:model.live="startDate" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sampai Tanggal</label>
                <input type="date" wire:model.live="endDate" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200">
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold text-xs uppercase">
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Kode Transaksi</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Penerima / Tujuan</th>
                        <th class="px-4 py-3 text-center">Jumlah Item</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center min-w-[150px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subsidies as $index => $subsidy)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $subsidies->firstItem() + $index }}</td>
                            <td class="px-4 py-3 font-mono font-semibold text-blue-700 text-xs">
                                {{ $subsidy->code }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-700">
                                {{ $subsidy->subsidy_date ? $subsidy->subsidy_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-800 text-xs">{{ $subsidy->recipient_name }}</div>
                                @if($subsidy->recipient_description)
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ Str::limit($subsidy->recipient_description, 40) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold text-gray-800 text-xs">
                                    {{ $subsidy->materialSubsidyDetails->sum('quantity') }} unit
                                </span>
                                <div class="text-[10px] text-gray-400">({{ $subsidy->materialSubsidyDetails->count() }} jenis)</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($subsidy->status === 'confirmed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                                        ✅ Terkonfirmasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                        📝 Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- Detail Modal --}}
                                    <button wire:click="viewDetail('{{ $subsidy->id }}')" class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>

                                    @if($subsidy->status === 'draft')
                                        {{-- Edit --}}
                                        <a href="{{ route('menu-polres.material-subsidy.edit', $subsidy->id) }}" wire:navigate class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        {{-- Confirm Button --}}
                                        <button wire:click="openConfirmModal('{{ $subsidy->id }}')" class="p-1.5 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors" title="Konfirmasi & Kurangi Stok">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>

                                        {{-- Delete --}}
                                        <button wire:click="openDeleteModal('{{ $subsidy->id }}')" class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400 italic">
                                Belum ada data subsidi silang. Klik "Tambah Subsidi Silang" untuk membuat transaksi baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($subsidies->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-gray-500">
                    Menampilkan <strong>{{ $subsidies->firstItem() }}–{{ $subsidies->lastItem() }}</strong> dari <strong>{{ $subsidies->total() }}</strong> hasil
                </div>
                <div>{{ $subsidies->links() }}</div>
            </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedSubsidy)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-gray-100">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        📋 Detail Subsidi Silang — <span class="font-mono text-blue-600">{{ $selectedSubsidy->code }}</span>
                    </h3>
                    <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4 text-xs">
                    <div>
                        <span class="text-gray-400 block">Penerima / Tujuan:</span>
                        <strong class="text-gray-800 text-sm">{{ $selectedSubsidy->recipient_name }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Tanggal Transaksi:</span>
                        <strong class="text-gray-800 text-sm">{{ $selectedSubsidy->subsidy_date->format('d F Y') }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Status:</span>
                        <span class="font-bold {{ $selectedSubsidy->status === 'confirmed' ? 'text-green-600' : 'text-amber-600' }}">
                            {{ strtoupper($selectedSubsidy->status) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Polres Pengeluar:</span>
                        <strong class="text-gray-800 text-sm">{{ $selectedSubsidy->policeStation?->name ?? 'Polres' }}</strong>
                    </div>
                </div>

                @if($selectedSubsidy->recipient_description)
                    <div class="mb-4 bg-gray-50 p-3 rounded-lg text-xs text-gray-600 border border-gray-200">
                        <strong>Keterangan Penerima:</strong> {{ $selectedSubsidy->recipient_description }}
                    </div>
                @endif

                <h4 class="text-xs font-bold uppercase text-gray-500 mb-2">Daftar Material Disubsidikan:</h4>
                <div class="overflow-x-auto mb-4 border border-gray-200 rounded-lg">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="p-2.5">No</th>
                                <th class="p-2.5">Jenis Material</th>
                                <th class="p-2.5">Detail</th>
                                <th class="p-2.5 text-center">Jumlah</th>
                                <th class="p-2.5">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($selectedSubsidy->materialSubsidyDetails as $i => $item)
                                <tr>
                                    <td class="p-2.5 text-center text-gray-400">{{ $i + 1 }}</td>
                                    <td class="p-2.5 font-semibold text-gray-800">{{ $item->type->name ?? '-' }}</td>
                                    <td class="p-2.5 text-gray-600">{{ $item->typeDetail->name ?? '-' }}</td>
                                    <td class="p-2.5 text-center font-bold text-blue-600">{{ number_format($item->quantity, 0, ',', '.') }} unit</td>
                                    <td class="p-2.5 text-gray-500">{{ $item->notes ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100">
                    <button wire:click="closeDetailModal" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-xs">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Confirm Modal --}}
    @if($showConfirmModal && $selectedSubsidy)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 text-center">
                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-4 text-xl">✅</div>
                <h3 class="text-base font-bold text-gray-800 mb-2">Konfirmasi Subsidi Silang?</h3>
                <p class="text-xs text-gray-500 mb-6">
                    Setelah dikonfirmasi, stok material Polres akan berkurang secara otomatis sebanyak
                    <strong>{{ $selectedSubsidy->materialSubsidyDetails->sum('quantity') }} unit</strong> dan transaksi ini tidak dapat diubah lagi.
                </p>

                <div class="flex items-center justify-center gap-3">
                    <button wire:click="closeConfirmModal" class="px-5 py-2.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl">Batal</button>
                    <button wire:click="confirmSubsidy" class="px-6 py-2.5 text-xs font-bold text-white bg-green-600 hover:bg-green-700 rounded-xl shadow-lg shadow-green-500/25">Ya, Konfirmasi Sekarang</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 text-xl">🗑️</div>
                <h3 class="text-base font-bold text-gray-800 mb-2">Hapus Draft Subsidi Silang?</h3>
                <p class="text-xs text-gray-500 mb-6">Data draft subsidi ini akan dihapus permanen.</p>

                <div class="flex items-center justify-center gap-3">
                    <button wire:click="closeDeleteModal" class="px-5 py-2.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl">Batal</button>
                    <button wire:click="deleteSubsidy" class="px-6 py-2.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-lg shadow-red-500/25">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
