<div>
    <!-- Header -->
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">Pengiriman Material</h1>
                <p class="text-gray-500 mt-1">Kelola pengiriman material dari Polda ke Polres</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('menu-polda.material-shipment.create') }}" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-105 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Tambah Data
                </a>
            </div>
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

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <!-- Search & Filter -->
        <div class="p-4 border-b border-gray-100">
            <div class="bg-white rounded-xl mb-6">
                <div class="grid grid-cols-1 md:grid-cols-{{ auth()->user()->hasRole('Admin') ? '4' : '3' }} gap-4">
                    @if(auth()->user()->hasRole('Admin'))
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pengirim</label>
                            <div wire:ignore>
                                <select id="select-regional-police" x-data x-init="
                                    const selectize = $($el).selectize({
                                        dropdownParent: 'body',
                                        allowClear: true,
                                        plugins: ['clear_button'],
                                        onChange: function(val) {
                                            @this.set('poldaFilter', val);
                                        }
                                    })[0].selectize;
                                "
                                placeholder="Semua Polda">
                                    <option value="">Semua Polda</option>
                                    @foreach ($regionalPolices as $regional)
                                        <option value="{{ $regional->id }}" @selected($poldaFilter == $regional->id)>{{ $regional->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Penerima</label>
                        <div wire:ignore>
                            <select id="select-regional-police" x-data x-init="
                                const selectize = $($el).selectize({
                                    dropdownParent: 'body',
                                    allowClear: true,
                                    plugins: ['clear_button'],
                                    onChange: function(val) {
                                        @this.set('polresFilter', val);
                                    }
                                })[0].selectize;
                            "
                            placeholder="Semua Polres">
                                <option value="">Semua Polres</option>
                                @foreach ($policeStations as $policeStation)
                                    <option value="{{ $policeStation->id }}" @selected($polresFilter == $policeStation->id)>{{ $policeStation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Tampilkan</span>
                    <select wire:model.live="perPage"
                        class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-600">data</span>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto">
                    <!-- Status Filter -->
                    <select wire:model.live="statusFilter"
                        class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="shipped">Terkirim</option>
                        <option value="received">Diterima</option>
                    </select>

                    <!-- Polres Filter -->
                    @if (count($policeStations) > 0)
                        <select wire:model.live="polresFilter"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                            <option value="">Semua Polres</option>
                            @foreach ($policeStations as $ps)
                                <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                            @endforeach
                        </select>
                    @endif

                    <!-- Date Range -->
                    <div class="flex items-center gap-2">
                        <input wire:model.live.debounce.300ms="startDate" type="date"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                        <span class="text-gray-400">-</span>
                        <input wire:model.live.debounce.300ms="endDate" type="date"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                    </div>

                    <!-- Search -->
                    <div class="relative w-full sm:w-80">
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Cari kode pengiriman..."
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
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kode
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Dari Polda</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tujuan
                            Polres</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Items
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($shipments as $index => $shipment)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $shipments->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-blue-600">{{ $shipment->code }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($shipment->shipment_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $shipment->senderRegionalPolice->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $shipment->receiverPoliceStation->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $shipment->material_shipment_details_count ?? $shipment->materialShipmentDetails->count() }}
                                        Items</span>
                                    <span class="text-xs text-gray-500">Total:
                                        {{ number_format($shipment->material_shipment_details_sum_quantity ?? ($shipment->relationLoaded('materialShipmentDetails') ? $shipment->materialShipmentDetails->sum('quantity') : 0), 0) }}
                                        unit</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($shipment->status === 'draft')
                                    <span
                                        class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                        🟡 Draft
                                    </span>
                                @elseif($shipment->status === 'shipped')
                                    <span
                                        class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                        🔵 Terkirim
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                        🟢 Diterima
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5" x-data="{ showPrintMenu: false }">
                                    <!-- Print Dropdown (2 Output SPPM) -->
                                    <div class="relative">
                                        <button @click="showPrintMenu = !showPrintMenu" @click.away="showPrintMenu = false"
                                            class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors flex items-center gap-1"
                                            title="Pilihan Cetak SPPM">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                                            </svg>
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                        <div x-show="showPrintMenu" x-cloak
                                            class="absolute right-0 mt-1 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-50 text-left text-xs">
                                            <a href="{{ route('menu-polda.material-shipment.print', ['id' => $shipment->id, 'mode' => 'ttd_ka']) }}"
                                                target="_blank" class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 font-medium">
                                                📄 SPPM TTD KA (Kosong / Pengajuan)
                                            </a>
                                            <a href="{{ route('menu-polda.material-shipment.print', ['id' => $shipment->id, 'mode' => 'qr']) }}"
                                                target="_blank" class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 font-medium border-t border-gray-100">
                                                📱 SPPM Versi Barcode / QR Code
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Scan QR Code Button (Petugas Warehouse) -->
                                    <button wire:click="openScanQrModal('{{ $shipment->id }}')"
                                        class="p-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors"
                                        title="Scan QR Code Warehouse">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h0.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </button>

                                    <!-- View Data Pengambilan (Picking Sheet) -->
                                    <button wire:click="openPickingDetailModal('{{ $shipment->id }}')"
                                        class="p-2 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors"
                                        title="View Data Pengambilan Material (Warehouse)">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>

                                    @if ($shipment->status === 'draft')
                                        <a href="{{ route('menu-polda.material-shipment.edit', ['id' => $shipment->id]) }}"
                                            class="p-2 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-colors"
                                            title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        <button wire:click="openDeleteModal('{{ $shipment->id }}')"
                                            class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                            title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-4"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">Tidak ada pengiriman</p>
                                <p class="text-gray-400 text-sm mt-1">Buat pengiriman baru dengan klik tombol "Buat
                                    Pengiriman"</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($shipments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $shipments->links() }}
            </div>
        @endif
    </div>

    <!-- Delete Modal -->
@if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity bg-gray-900/70 backdrop-blur-sm" wire:click="closeModal">
                </div>
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
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Pengiriman</h3>
                        <p class="text-gray-500">Apakah Anda yakin ingin menghapus pengiriman ini? Tindakan ini tidak
                            dapat dibatalkan.</p>
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
    @if ($showDetailModal && $selectedShipment)
        <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/80 backdrop-blur-md" wire:click="closeDetailModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full border border-gray-100">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-white leading-6" id="modal-title">
                                    Detail Pengiriman: {{ $selectedShipment->code }}
                                </h3>
                                <p class="mt-2 text-blue-100 text-sm">
                                    {{ \Carbon\Carbon::parse($selectedShipment->shipment_date)->format('d F Y') }}
                                </p>
                            </div>
                            <button wire:click="closeDetailModal" class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-xl">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="bg-white px-8 py-8">
                        <!-- Header Info Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-100">
                                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block mb-1">Pengirim</span>
                                <p class="text-gray-900 font-bold">{{ $selectedShipment->senderRegionalPolice->name ?? '-' }}</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-purple-50 border border-purple-100">
                                <span class="text-xs font-bold text-purple-600 uppercase tracking-wider block mb-1">Penerima</span>
                                <p class="text-gray-900 font-bold">{{ $selectedShipment->receiverPoliceStation->name ?? '-' }}</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-100">
                                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block mb-1">Status</span>
                                @if($selectedShipment->status === 'draft')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">🟡 Draft</span>
                                @elseif($selectedShipment->status === 'shipped')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-bold bg-blue-100 text-blue-700">🔵 Terkirim</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-bold bg-green-100 text-green-700">🟢 Diterima</span>
                                @endif
                            </div>
                        </div>

                        @if($selectedShipment->notes)
                            <div class="mb-8 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Catatan Pengiriman:</span>
                                <p class="text-gray-700 italic">"{{ $selectedShipment->notes }}"</p>
                            </div>
                        @endif

                        <!-- Items Table -->
                        <div class="overflow-hidden rounded-2xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 font-bold">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs text-gray-500 uppercase">Material</th>
                                        <th class="px-6 py-4 text-left text-xs text-gray-500 uppercase">Service Detail</th>
                                        <th class="px-6 py-4 text-left text-xs text-gray-500 uppercase">Identitas (S/N)</th>
                                        <th class="px-6 py-4 text-center text-xs text-gray-500 uppercase">Qty</th>
                                        <th class="px-6 py-4 text-left text-xs text-gray-500 uppercase">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($selectedShipment->materialShipmentDetails as $item)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-gray-900">{{ $item->type->name ?? '-' }}</span>
                                                    <span class="text-xs text-gray-500">{{ $item->typeDetail->name ?? '-' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col">
                                                    <span class="text-sm text-gray-700">{{ $item->stockDetail->service->name ?? '-' }}</span>
                                                    <span class="text-xs text-gray-500 italic">{{ $item->stockDetail->serviceDetail->name ?? '-' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-mono text-blue-600 font-bold">{{ $item->code ?: '-' }}</span>
                                                    <span class="text-[10px] text-gray-500">{{ $item->number_serial_first ?: '-' }} / {{ $item->number_serial_second ?: '-' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center justify-center min-w-[40px] px-2.5 py-1 rounded-lg text-sm font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                                    {{ number_format($item->quantity, 0) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-sm text-gray-600">{{ $item->notes ?: '-' }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-end">
                        <button wire:click="closeDetailModal" class="px-8 py-3 bg-white border-2 border-gray-200 text-gray-700 font-bold rounded-2xl hover:bg-gray-100 transition-all shadow-sm">
                            Tutup Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- SCAN QR CODE MODAL — SERAH TERIMA WAREHOUSE (DENGAN TTD & FOTO) --}}
    @if($showScanQrModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            x-data="{
                scanner: null, isScanning: false, showCameraBox: false, cameraError: null,
                sigCanvas: null, sigCtx: null, isDrawing: false, sigEmpty: true,
                photoStream: null, showPhotoCamera: false, photoPreview: null,
                initSignature() {
                    this.$nextTick(() => {
                        const c = this.$refs.sigPad;
                        if (!c) return;
                        this.sigCanvas = c;
                        this.sigCtx = c.getContext('2d');
                        this.sigCtx.strokeStyle = '#1e293b';
                        this.sigCtx.lineWidth = 2;
                        this.sigCtx.lineCap = 'round';
                        this.sigCtx.lineJoin = 'round';
                    });
                },
                startDraw(e) { this.isDrawing=true; this.sigEmpty=false; const p=this.getPos(e); this.sigCtx.beginPath(); this.sigCtx.moveTo(p.x,p.y); },
                draw(e) { if(!this.isDrawing) return; e.preventDefault(); const p=this.getPos(e); this.sigCtx.lineTo(p.x,p.y); this.sigCtx.stroke(); },
                stopDraw() { if(!this.isDrawing) return; this.isDrawing=false; if(!this.sigEmpty) $wire.set('pickerSignature',this.sigCanvas.toDataURL('image/png')); },
                clearSig() { if(this.sigCtx) this.sigCtx.clearRect(0,0,this.sigCanvas.width,this.sigCanvas.height); this.sigEmpty=true; $wire.set('pickerSignature',''); },
                getPos(e) { const r=this.sigCanvas.getBoundingClientRect(); const sx=this.sigCanvas.width/r.width,sy=this.sigCanvas.height/r.height; const cx=e.touches?e.touches[0].clientX:e.clientX,cy=e.touches?e.touches[0].clientY:e.clientY; return {x:(cx-r.left)*sx,y:(cy-r.top)*sy}; },
                startCamera() {
                    this.cameraError=null; this.isScanning=true; this.showCameraBox=true;
                    if (!window.Html5Qrcode) { const s=document.createElement('script'); s.src='https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js'; s.onload=()=>this.runCameraStream(); document.head.appendChild(s); } else { this.runCameraStream(); }
                },
                runCameraStream() {
                    this.$nextTick(() => {
                        try {
                            const qr = new Html5Qrcode('admin-qr-reader'); this.scanner=qr;
                            qr.start({facingMode:'environment'},{fps:10,qrbox:{width:220,height:220}},
                                (text)=>{ $wire.set('scanInputCode',text); $wire.call('processScanQr'); this.stopCamera(); }, ()=>{}
                            ).catch(()=>{ this.isScanning=false; this.cameraError='Izin kamera ditolak.'; });
                        } catch(e) { this.isScanning=false; this.cameraError='Kamera tidak dapat diinisialisasi.'; }
                    });
                },
                stopCamera() { this.showCameraBox=false; if(this.scanner&&this.isScanning){ this.scanner.stop().then(()=>this.isScanning=false).catch(()=>this.isScanning=false); } },
                async openPhotoCamera() {
                    try { this.photoStream=await navigator.mediaDevices.getUserMedia({video:{facingMode:'environment'}}); this.$refs.photoVid.srcObject=this.photoStream; this.showPhotoCamera=true; }
                    catch(e) { alert('Izin kamera ditolak.'); }
                },
                closePhotoCamera() { if(this.photoStream){this.photoStream.getTracks().forEach(t=>t.stop());this.photoStream=null;} this.showPhotoCamera=false; },
                capturePhoto() {
                    const cv=document.createElement('canvas'); cv.width=this.$refs.photoVid.videoWidth; cv.height=this.$refs.photoVid.videoHeight;
                    cv.getContext('2d').drawImage(this.$refs.photoVid,0,0);
                    cv.toBlob(async(blob)=>{ const f=new File([blob],'foto-'+Date.now()+'.jpg',{type:'image/jpeg'}); const dt=new DataTransfer(); dt.items.add(f); this.$refs.photoInput.files=dt.files; this.$refs.photoInput.dispatchEvent(new Event('change')); this.photoPreview=cv.toDataURL('image/jpeg',0.8); this.closePhotoCamera(); },'image/jpeg',0.85);
                },
                handlePhotoChange(e) { const f=e.target.files[0]; if(f){const r=new FileReader();r.onload=ev=>this.photoPreview=ev.target.result;r.readAsDataURL(f);} }
            }"
            x-init="$nextTick(() => { if ($wire.scannedShipment) initSignature(); })">
            <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[95vh] flex flex-col shadow-2xl border border-gray-100 overflow-hidden">
                {{-- Sticky Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
                    <h3 class="text-base font-bold flex items-center gap-2">
                        📷 Verifikasi & Serah Terima Material — QR SPPM
                    </h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('warehouse.scan') }}" target="_blank"
                            class="text-xs bg-white/20 hover:bg-white/30 text-white font-semibold px-3 py-1.5 rounded-lg transition-all">
                            🖥️ Buka Halaman Warehouse
                        </a>
                        <button wire:click="closeScanQrModal" @click="stopCamera(); closePhotoCamera()" class="text-white/80 hover:text-white">✕</button>
                    </div>
                </div>

                {{-- Scrollable Modal Body --}}
                <div class="p-6 overflow-y-auto flex-1 space-y-4">
                    {{-- Live Camera QR Scanner --}}
                    <div x-show="showCameraBox" x-cloak class="bg-slate-950 p-3 rounded-2xl border-2 border-emerald-400/40 text-center">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-emerald-400 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>KAMERA SCANNER AKTIF
                            </span>
                            <button @click="stopCamera()" class="px-2 py-0.5 bg-red-500/20 hover:bg-red-500/30 text-red-400 text-[10px] font-bold rounded-lg border border-red-500/30">
                                🛑 Sembunyikan
                            </button>
                        </div>
                        <div id="admin-qr-reader" class="w-full max-w-xs mx-auto rounded-xl overflow-hidden bg-slate-900 border border-slate-800" style="max-height: 200px;"></div>
                        <template x-if="cameraError">
                            <div class="mt-2 p-2 bg-amber-500/10 border border-amber-500/30 rounded-xl text-amber-300 text-[11px]">
                                ⚠️ <span x-text="cameraError"></span>
                            </div>
                        </template>
                    </div>

                    {{-- Code Input Section --}}
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 p-4 rounded-2xl">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-emerald-900">Scan / Input Kode SPPM:</label>
                            <button type="button" @click="showCameraBox ? stopCamera() : startCamera()"
                                class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 underline flex items-center gap-1">
                                <span x-text="showCameraBox ? '🙈 Sembunyikan Kamera' : '📷 Buka Kamera Scanner'"></span>
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="text" wire:model="scanInputCode" placeholder="Masukkan / scan kode (Contoh: SHP-20260720-001)"
                                wire:keydown.enter="processScanQr"
                                class="w-full px-3.5 py-2.5 text-xs font-mono font-bold rounded-xl border border-emerald-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 bg-white">
                            <button type="button" wire:click="processScanQr"
                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all shrink-0">
                                Verifikasi
                            </button>
                        </div>
                    </div>

                    @if($scannedShipment)
                        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 text-xs">
                            <div class="flex items-center justify-between border-b border-gray-200 pb-2 mb-3">
                                <div>
                                    <span class="font-mono text-sm font-bold text-blue-600">{{ $scannedShipment->code }}</span>
                                    <div class="text-gray-500 text-[10px]">Tujuan: <strong>{{ $scannedShipment->receiverPoliceStation?->name ?? 'Polres' }}</strong></div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full font-bold text-[10px] {{ $scannedShipment->status === 'shipped' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ strtoupper($scannedShipment->status) }}
                                </span>
                            </div>

                            {{-- Tabel Rincian dengan Nomor Seri --}}
                            <h4 class="font-bold text-gray-700 mb-2 uppercase tracking-wider text-[10px]">Rincian Barang, Nomor Seri & Lokasi Rak:</h4>
                            <div class="overflow-x-auto rounded-xl border border-gray-200 mb-3">
                                <table class="w-full text-[10px]">
                                    <thead class="bg-gray-100 text-gray-600 font-semibold">
                                        <tr>
                                            <th class="p-2">Material</th>
                                            <th class="p-2 font-mono bg-blue-50">Nomor Seri / Kode</th>
                                            <th class="p-2 text-center bg-emerald-50">Rak</th>
                                            <th class="p-2 text-center">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($scannedShipment->materialShipmentDetails as $d)
                                            <tr class="bg-white">
                                                <td class="p-2 font-bold text-gray-800">{{ $d->type?->name ?? '-' }} <span class="text-gray-400 font-normal">({{ $d->typeDetail?->name ?? '-' }})</span></td>
                                                <td class="p-2 font-mono text-blue-600 bg-blue-50/30">{{ implode(' | ', array_filter([$d->code, $d->number_serial_first, $d->number_serial_second])) ?: '—' }}</td>
                                                <td class="p-2 text-center bg-emerald-50/30"><span class="font-bold text-emerald-700">📍 {{ $d->stockDetail?->rack?->name ?? 'Gudang' }}</span></td>
                                                <td class="p-2 text-center font-bold text-blue-600">{{ number_format($d->quantity, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($scannedShipment->status === 'draft')
                                {{-- Identitas Pengambil + TTD + Foto --}}
                                <div class="border border-purple-100 rounded-xl p-3 bg-purple-50/30 mb-3" x-init="initSignature()">
                                    <h4 class="font-bold text-purple-800 text-[10px] uppercase tracking-wider mb-2">👤 Identitas Pengambil</h4>
                                    <div class="grid grid-cols-3 gap-2 mb-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="pickerName" placeholder="Nama lengkap" class="w-full px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-purple-400">
                                            @error('pickerName') <p class="text-red-500 text-[9px] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-gray-600 mb-1">Pangkat <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="pickerRank" placeholder="Pangkat" class="w-full px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-purple-400">
                                            @error('pickerRank') <p class="text-red-500 text-[9px] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-gray-600 mb-1">Jabatan <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="pickerPosition" placeholder="Jabatan" class="w-full px-2 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-purple-400">
                                            @error('pickerPosition') <p class="text-red-500 text-[9px] mt-0.5">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    {{-- TTD Canvas --}}
                                    <div class="mb-2">
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-[10px] font-semibold text-gray-600">✍️ TTD Digital <span class="text-red-500">*</span></label>
                                            <button type="button" @click="clearSig()" class="text-[9px] text-red-500 bg-red-50 px-2 py-0.5 rounded font-semibold">🗑️ Hapus</button>
                                        </div>
                                        <div class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 relative overflow-hidden" style="touch-action:none;">
                                            <canvas x-ref="sigPad" width="560" height="110" class="w-full cursor-crosshair block"
                                                @mousedown="startDraw($event)" @mousemove="draw($event)" @mouseup="stopDraw()" @mouseleave="stopDraw()"
                                                @touchstart.prevent="startDraw($event)" @touchmove.prevent="draw($event)" @touchend="stopDraw()"></canvas>
                                            <div x-show="sigEmpty" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                <p class="text-gray-300 text-xs">Tanda tangan di sini...</p>
                                            </div>
                                        </div>
                                        @error('pickerSignature') <p class="text-red-500 text-[9px] mt-0.5">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- Foto Dokumentasi --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-[10px] font-semibold text-gray-600">📸 Foto Dokumentasi (opsional)</label>
                                            <button type="button" @click="showPhotoCamera ? closePhotoCamera() : openPhotoCamera()"
                                                class="text-[9px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">
                                                <span x-text="showPhotoCamera ? '🙈 Tutup' : '📷 Kamera'"></span>
                                            </button>
                                        </div>
                                        <div x-show="showPhotoCamera" class="rounded-xl overflow-hidden bg-black mb-2">
                                            <video x-ref="photoVid" autoplay playsinline class="w-full max-h-28 object-cover"></video>
                                            <div class="flex justify-center p-1.5 bg-black/50">
                                                <button type="button" @click="capturePhoto()" class="w-8 h-8 rounded-full bg-white border-2 border-gray-300 text-sm flex items-center justify-center">📸</button>
                                            </div>
                                        </div>
                                        <input x-ref="photoInput" type="file" wire:model="pickerPhoto" accept="image/*" @change="handlePhotoChange($event)"
                                            class="w-full text-[10px] text-gray-600 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-amber-100 file:text-amber-700">
                                        <div x-show="photoPreview" class="mt-2 rounded-lg overflow-hidden border border-gray-200">
                                            <img :src="photoPreview" alt="Preview" class="w-full max-h-24 object-cover">
                                        </div>
                                        @error('pickerPhoto') <p class="text-red-500 text-[9px] mt-0.5">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <button wire:click="confirmWarehousePicking" type="button"
                                    wire:loading.attr="disabled" wire:loading.class="opacity-50"
                                    class="w-full py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/25 transition-all text-xs flex items-center justify-center gap-2">
                                    <span wire:loading.remove wire:target="confirmWarehousePicking">✅ Konfirmasi Serah Terima Material</span>
                                    <span wire:loading wire:target="confirmWarehousePicking">⏳ Menyimpan...</span>
                                </button>
                            @else
                                <div class="text-center py-2 text-xs text-green-700 font-bold bg-green-50 rounded-xl border border-green-200">
                                    ✅ SPPM sudah diproses (status: {{ strtoupper($scannedShipment->status) }})
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Sticky Modal Footer --}}
                <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex justify-end shrink-0">
                    <button wire:click="closeScanQrModal" @click="stopCamera(); closePhotoCamera()" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-bold text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- VIEW DATA PENGAMBILAN MODAL (PICKING LOG SHEET) --}}
    @if($showPickingDetailModal && $selectedShipment)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-gray-100 overflow-hidden">
                {{-- Sticky Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-white">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            📋 Data Pengambilan Material (Warehouse Picking Sheet)
                        </h3>
                        <p class="text-xs text-gray-500">Rincian lokasi rak & barang yang diambil petugas warehouse untuk SPPM: <strong class="font-mono text-blue-600">{{ $selectedShipment->code }}</strong></p>
                    </div>
                    <button wire:click="closePickingDetailModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                {{-- Scrollable Modal Body --}}
                <div class="p-6 overflow-y-auto flex-1 space-y-4">
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-2xl text-xs border border-gray-200">
                        <div>
                            <span class="text-gray-400 block">Tujuan Polres:</span>
                            <strong class="text-gray-800 text-sm">{{ $selectedShipment->receiverPoliceStation?->name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Tanggal Pengiriman:</span>
                            <strong class="text-gray-800 text-sm">{{ \Carbon\Carbon::parse($selectedShipment->shipment_date)->format('d F Y') }}</strong>
                        </div>
                    </div>

                    {{-- Identitas Pengambil (jika sudah diisi) --}}
                    @if($selectedShipment->picker_name)
                        <div class="bg-purple-50 border border-purple-100 rounded-2xl p-4 text-xs">
                            <h4 class="font-bold text-purple-800 uppercase tracking-wider text-[10px] mb-3">👤 Identitas Pengambil Material</h4>
                            <div class="grid grid-cols-3 gap-4 mb-3">
                                <div><span class="text-gray-400 block">Nama</span><strong class="text-gray-800">{{ $selectedShipment->picker_name }}</strong></div>
                                <div><span class="text-gray-400 block">Pangkat</span><strong class="text-gray-800">{{ $selectedShipment->picker_rank }}</strong></div>
                                <div><span class="text-gray-400 block">Jabatan</span><strong class="text-gray-800">{{ $selectedShipment->picker_position }}</strong></div>
                            </div>
                            @if($selectedShipment->picked_at)
                                <div class="text-[10px] text-gray-400 mb-2">⏰ Serah terima: {{ \Carbon\Carbon::parse($selectedShipment->picked_at)->format('d F Y, H:i') }} WIB</div>
                            @endif
                            @if($selectedShipment->picker_signature)
                                <div class="mb-2"><span class="text-gray-400 block mb-1 text-[10px]">✍️ TTD Digital:</span>
                                    <div class="bg-white rounded-xl border border-gray-200 p-2 inline-block">
                                        <img src="{{ $selectedShipment->picker_signature }}" alt="TTD" class="max-h-16 max-w-full">
                                    </div>
                                </div>
                            @endif
                            @if($selectedShipment->picker_photo)
                                <div><span class="text-gray-400 block mb-1 text-[10px]">📸 Foto Dokumentasi:</span>
                                    <img src="{{ Storage::url($selectedShipment->picker_photo) }}" alt="Foto" class="max-h-32 rounded-xl border border-gray-200 object-cover">
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-xs text-gray-400 bg-gray-50 rounded-xl p-3 border border-gray-100 text-center">Identitas pengambil belum diisi (belum diproses serah terima)</div>
                    @endif

                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Daftar Barang, Nomor Seri & Lokasi Rak:</h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-2xl">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-100 text-gray-700 font-semibold">
                                <tr>
                                    <th class="p-3 text-center w-10">No</th>
                                    <th class="p-3">Nama Material</th>
                                    <th class="p-3">Detail</th>
                                    <th class="p-3 font-mono bg-blue-50">Nomor Seri / Kode</th>
                                    <th class="p-3 text-center bg-emerald-50">Lokasi Rak</th>
                                    <th class="p-3 text-center">Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($selectedShipment->materialShipmentDetails as $idx => $det)
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-3 text-center text-gray-400">{{ $idx + 1 }}</td>
                                        <td class="p-3 font-bold text-gray-800">{{ $det->type?->name ?? '-' }}</td>
                                        <td class="p-3 text-gray-600">{{ $det->typeDetail?->name ?? '-' }}</td>
                                        <td class="p-3 font-mono text-blue-600 bg-blue-50/30">{{ implode(' | ', array_filter([$det->code, $det->number_serial_first, $det->number_serial_second])) ?: '—' }}</td>
                                        <td class="p-3 text-center bg-emerald-50/50"><span class="font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded border border-emerald-200 font-mono">📍 {{ $det->stockDetail?->rack?->name ?? 'Gudang Utama' }}</span></td>
                                        <td class="p-3 text-center font-bold text-blue-600 font-mono">{{ number_format($det->quantity, 0, ',', '.') }} unit</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Sticky Modal Footer --}}
                <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex justify-end shrink-0">
                    <button wire:click="closePickingDetailModal" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-bold text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>


