<div>
    <!-- Header -->
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-cyan-600">
                    Laporan Pengiriman Material
                </h1>
                <p class="text-gray-500 mt-1">Monitoring pengiriman material ke satuan kerja</p>
            </div>
            <div class="flex gap-2">
                <button onclick="exportToExcel('deliveryReportTable', 'laporan-pengiriman')"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-emerald-500 hover:from-green-700 hover:to-emerald-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-green-500/30 transition-all duration-300 transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Export Excel
                </button>
                <button
                    onclick="exportToPDF({ tableId: 'deliveryReportTable', title: 'Laporan Pengiriman Material', filename: 'laporan-pengiriman', summaryCards: [{ label: 'Total', value: '{{ $this->totalShipments }}' }, { label: 'Terkirim', value: '{{ $this->receivedCount }}' }, { label: 'Dalam Perjalanan', value: '{{ $this->inTransitCount }}' }] })"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-red-600 to-rose-500 hover:from-red-700 hover:to-rose-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-red-500/30 transition-all duration-300 transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                            clip-rule="evenodd" />
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div
            class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl p-5 text-white shadow-lg shadow-cyan-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-cyan-100 text-sm">Total Pengiriman</p>
                    <p class="text-3xl font-bold mt-1">{{ $this->totalShipments }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                </div>
            </div>
        </div>
        <div
            class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-5 text-white shadow-lg shadow-green-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Terkirim</p>
                    <p class="text-3xl font-bold mt-1">{{ $this->receivedCount }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>
        <div
            class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-5 text-white shadow-lg shadow-amber-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm">Dalam Perjalanan</p>
                    <p class="text-3xl font-bold mt-1">{{ $this->inTransitCount }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>
        <div
            class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-2xl p-5 text-white shadow-lg shadow-gray-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-100 text-sm">Total Unit Dikirim</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($this->totalUnits) }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>


    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <!-- Search & Filter Header -->
        <div class="p-4 border-b border-gray-100">
            <!-- Filter Card -->
            <div class="bg-white rounded-xl mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if(Auth::user()->hasRole('Admin'))
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Dari (Polda)</label>
                            <div wire:ignore>
                                <select id="select-polres" x-data x-init="
                                    const selectize = $($el).selectize({
                                        dropdownParent: 'body',
                                        allowClear: true,
                                        plugins: ['clear_button'],
                                        onChange: function(val) {
                                            @this.set('filterPolda', val);
                                        }
                                    })[0].selectize;
                                "
                                placeholder="Semua Polda">
                                    <option value="">Semua Polda</option>
                                    @foreach ($this->regionalPolices as $regionalPolice)
                                        <option value="{{ $regionalPolice->id }}" @selected($filterPolda == $regionalPolice->id)>{{ $regionalPolice->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Tujuan (Polres)</label>
                        <div wire:ignore>
                            <select id="select-polres" x-data x-init="
                                const selectize = $($el).selectize({
                                    dropdownParent: 'body',
                                    allowClear: true,
                                    plugins: ['clear_button'],
                                    onChange: function(val) {
                                        @this.set('filterPolres', val);
                                    }
                                })[0].selectize;
                            "
                            placeholder="Semua Polres">
                                <option value="">Semua Polres</option>
                                @foreach ($this->policeStations as $polres)
                                    <option value="{{ $polres->id }}" @selected($filterPolres == $polres->id)>{{ $polres->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Status</label>
                        <select wire:model.live="filterStatus"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            <option value="">Semua Status</option>
                            @foreach ($this->statuses as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
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
                            placeholder="Cari kode, tujuan atau kurir..."
                            class="w-full pl-4 pr-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full" id="deliveryReportTable">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Kode
                            Pengiriman</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tanggal
                            Kirim</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tujuan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Material</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Detail Material</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            No Seri</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Qty</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($deliveries as $index => $detail)
                        <tr class="hover:bg-cyan-50/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $deliveries->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-cyan-100 text-cyan-700">
                                    {{ $detail->materialShipment->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $detail->materialShipment->shipment_date ? Carbon\Carbon::parse($detail->materialShipment->shipment_date)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="font-medium text-gray-900">
                                        {{ $detail->materialShipment->receiverPoliceStation->name ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $detail->type->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $detail->typeDetail->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $detail->number_serial_first }}
                                @if($detail->number_serial_last)
                                - {{ $detail->number_serial_last }}
                                @elseif($detail->number_serial_second)
                                - {{ $detail->number_serial_second }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-100 text-purple-700">
                                    {{ number_format($detail->quantity) }} unit
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($detail->materialShipment->status === 'received')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        Terkirim
                                    </span>
                                @elseif($detail->materialShipment->status === 'shipped')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        Dalam Perjalanan
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        {{ ucfirst($detail->materialShipment->status) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-10 text-center">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">Tidak ada data pengiriman</p>
                                    <p class="text-gray-400 text-sm mt-1">Data pengiriman tidak ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $deliveries->links() }}
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/report-export.js') }}"></script>
@endpush
