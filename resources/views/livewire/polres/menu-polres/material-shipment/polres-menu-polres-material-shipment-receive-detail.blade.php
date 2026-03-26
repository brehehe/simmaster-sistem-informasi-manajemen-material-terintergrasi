<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('menu-polres.material-shipment.receive') }}" wire:navigate
                        class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-purple-600">Detail Pengiriman</h1>
                </div>
                <p class="text-gray-500 ml-14">Review dan konfirmasi penerimaan material dari Polda</p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Shipment Info Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50/50">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Informasi Pengiriman
            </h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kode Pengiriman</label>
                    <p class="text-lg font-bold text-purple-600">{{ $shipment->code }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Pengiriman</label>
                    <p class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($shipment->shipment_date)->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
                    @if ($shipment->status === 'shipped')
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-700 animate-pulse">🔵 Menunggu Konfirmasi</span>
                    @elseif ($shipment->status === 'received')
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-700">🟢 Sudah Diterima</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Dari Polda</label>
                    <p class="text-sm font-semibold text-gray-800">{{ $shipment->senderRegionalPolice->name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tujuan Polres</label>
                    <p class="text-sm font-semibold text-gray-800">{{ $shipment->receiverPoliceStation->name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Dikirim Pada</label>
                    <p class="text-sm font-semibold text-gray-800">{{ $shipment->shipped_at ? \Carbon\Carbon::parse($shipment->shipped_at)->format('d F Y, H:i') : '-' }}</p>
                </div>

                @if ($shipment->notes)
                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Catatan</label>
                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $shipment->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-amber-50/50 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Daftar Rincian Material ({{ $shipment->materialShipmentDetails->count() }} Item)
            </h2>
            <div class="text-sm font-bold text-orange-600 bg-orange-50 px-4 py-2 rounded-xl border border-orange-200">
                Total: {{ number_format($shipment->materialShipmentDetails->sum('quantity'), 0) }} unit
            </div>
        </div>

        <div class="p-0">
            <div class="overflow-x-auto pb-4">
                <table class="w-full text-sm text-left align-top border-collapse min-w-[900px]">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
                        <tr>
                            <th class="px-3 py-3 font-semibold w-12 text-center text-xs">No</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[130px]">Material Utama</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[130px]">Detail Material</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[120px]">Service</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[120px]">Service Detail</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[200px]">Identitas Barang (Kode | S/N)</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[100px]">Catatan</th>
                            <th class="px-3 py-3 font-semibold text-xs w-24 text-center bg-orange-50">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($shipment->materialShipmentDetails as $index => $detail)
                            @php
                                $stockDetail = $detail->stockDetail;
                                $service = $stockDetail?->service;
                                $serviceDetail = $stockDetail?->serviceDetail;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-3 py-3 text-center font-medium text-gray-500 text-xs">{{ $index + 1 }}</td>

                                <td class="px-3 py-3 align-top">
                                    <span class="text-xs font-semibold text-gray-800">{{ $detail->type?->name ?? '-' }}</span>
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <span class="text-xs text-gray-700">{{ $detail->typeDetail?->name ?? '—' }}</span>
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <span class="text-xs text-gray-700">{{ $service?->name ?? '—' }}</span>
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <span class="text-xs text-gray-700">{{ $serviceDetail?->name ?? '—' }}</span>
                                </td>

                                <td class="px-3 py-3 align-top">
                                    @if ($detail->code || $detail->number_serial_first || $detail->number_serial_second)
                                        <span class="text-xs font-mono text-blue-700 bg-blue-50 px-2 py-1 rounded">
                                            {{ implode(' | ', array_filter([$detail->code, $detail->number_serial_first, $detail->number_serial_second])) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <span class="text-xs text-gray-600">{{ $detail->notes ?: '—' }}</span>
                                </td>

                                <td class="px-3 py-3 text-center bg-orange-50/30 align-top">
                                    <span class="text-sm font-bold text-orange-600">{{ number_format($detail->quantity, 0) }}</span>
                                    <span class="text-xs text-gray-400 block">unit</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 mt-4">
        <a href="{{ route('menu-polres.material-shipment.receive') }}" wire:navigate
            class="w-full sm:w-auto px-8 py-3 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all text-center">
            Kembali
        </a>

        @if ($shipment->status === 'shipped')
            <button wire:click="confirmReceipt"
                wire:confirm="Konfirmasi penerimaan material ini? Stock akan ditambahkan ke inventory Polres Anda dan tidak dapat dibatalkan."
                type="button"
                class="w-full sm:w-auto px-10 py-3 text-sm font-bold text-white bg-gradient-to-r from-green-600 to-emerald-500 rounded-xl hover:from-green-700 hover:to-emerald-600 shadow-xl shadow-green-500/30 transition-all transform hover:scale-105 text-center flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                ✅ Konfirmasi Penerimaan
            </button>
        @else
            <div class="w-full sm:w-auto px-6 py-3 text-sm font-semibold text-green-700 bg-green-50 rounded-xl border border-green-200 text-center">
                ✅ Material diterima pada {{ $shipment->received_at ? \Carbon\Carbon::parse($shipment->received_at)->format('d F Y, H:i') : '-' }}
            </div>
        @endif
    </div>
</div>
