<div x-data="{ showSppmModal: false }">
    <!-- Header -->
    <div class="mb-6 print:hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('menu-polres.material-shipment.receive') }}" 
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
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3 print:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3 print:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Shipment Info Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6 print:hidden">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50/50 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Informasi Pengiriman
            </h2>
            <div>
                <button @click="showSppmModal = true" type="button"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm shadow-lg shadow-purple-500/30 transition-all transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    DOWNLOAD SPPM
                </button>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4 items-center">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kode Pengiriman / SPPM</label>
                    <p class="text-lg font-bold text-purple-600 font-mono">{{ $shipment->code }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Pengiriman</label>
                    <p class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($shipment->shipment_date)->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
                    @if ($shipment->status === 'shipped' || $shipment->status === 'sent')
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-700 animate-pulse">🔵 Menunggu Konfirmasi</span>
                    @elseif ($shipment->status === 'received')
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-700">🟢 Sudah Diterima</span>
                    @else
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-700">{{ ucfirst($shipment->status) }}</span>
                    @endif
                </div>
                <div>
                    <button @click="showSppmModal = true" type="button"
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm shadow-lg shadow-purple-500/30 transition-all transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        PREVIEW SPPM
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gray-100">
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
                    <p class="text-sm font-semibold text-gray-800">{{ $shipment->shipped_at ? \Carbon\Carbon::parse($shipment->shipped_at)->format('d F Y, H:i') : '-' }} WIB</p>
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

    <!-- Items Table (Daftar Rincian Material - 8 Columns) -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6 print:hidden">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-amber-50/50 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Daftar Rincian Material ({{ $shipment->materialShipmentDetails->count() }} Item)
            </h2>
            <div class="text-sm font-bold text-orange-600 bg-orange-50 px-4 py-2 rounded-xl border border-orange-200">
                Total: {{ number_format($shipment->materialShipmentDetails->sum('quantity'), 0, ',', '.') }} unit
            </div>
        </div>

        <div class="p-0">
            <div class="overflow-x-auto pb-4">
                <table class="w-full text-sm text-left align-top border-collapse min-w-[900px]">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 font-semibold w-12 text-center text-xs">No</th>
                            <th class="px-4 py-3 font-semibold text-xs min-w-[130px]">Material Utama</th>
                            <th class="px-4 py-3 font-semibold text-xs min-w-[130px]">Detail Material</th>
                            <th class="px-4 py-3 font-semibold text-xs min-w-[120px]">Service</th>
                            <th class="px-4 py-3 font-semibold text-xs min-w-[120px]">Service Detail</th>
                            <th class="px-4 py-3 font-semibold text-xs min-w-[200px]">Identitas Barang (Kode | S/N)</th>
                            <th class="px-4 py-3 font-semibold text-xs min-w-[100px]">Catatan</th>
                            <th class="px-4 py-3 font-semibold text-xs w-28 text-center bg-orange-50">Jumlah</th>
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
                                <td class="px-4 py-3 text-center font-medium text-gray-500 text-xs">{{ $index + 1 }}</td>

                                <td class="px-4 py-3 align-top">
                                    <span class="text-xs font-semibold text-gray-800">{{ $detail->type?->name ?? '-' }}</span>
                                </td>

                                <td class="px-4 py-3 align-top">
                                    <span class="text-xs text-gray-700">{{ $detail->typeDetail?->name ?? '—' }}</span>
                                </td>

                                <td class="px-4 py-3 align-top">
                                    <span class="text-xs text-gray-700">{{ $service?->name ?? '—' }}</span>
                                </td>

                                <td class="px-4 py-3 align-top">
                                    <span class="text-xs text-gray-700">{{ $serviceDetail?->name ?? '—' }}</span>
                                </td>

                                <td class="px-4 py-3 align-top">
                                    @if ($detail->code || $detail->number_serial_first || $detail->number_serial_second)
                                        <span class="text-xs font-mono text-blue-700 bg-blue-50 px-2 py-1 rounded">
                                            {{ implode(' | ', array_filter([$detail->code, $detail->number_serial_first, $detail->number_serial_second])) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 align-top">
                                    <span class="text-xs text-gray-600">{{ $detail->notes ?: '—' }}</span>
                                </td>

                                <td class="px-4 py-3 text-center bg-orange-50/30 align-top">
                                    <span class="text-sm font-bold text-orange-600">{{ number_format($detail->quantity, 0, ',', '.') }}</span>
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
    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 mt-4 print:hidden">
        <a href="{{ route('menu-polres.material-shipment.receive') }}" 
            class="w-full sm:w-auto px-8 py-3 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all text-center">
            Kembali
        </a>

        <!-- Download / Preview SPPM Button -->
        <button @click="showSppmModal = true" type="button"
            class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-purple-700 bg-purple-50 border border-purple-200 rounded-xl hover:bg-purple-100 transition-all text-center flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Download / Preview SPPM
        </button>

        @if ($shipment->status === 'shipped' || $shipment->status === 'sent')
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

    <!-- Modal Preview & Download SPPM -->
    <div x-show="showSppmModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">

        <div class="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100 my-8">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-purple-800 to-indigo-900 text-white flex items-center justify-between print:hidden">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Dokumen SPPM (Surat Perintah Pengeluaran Material)</h3>
                        <p class="text-xs text-purple-200">Pratinjau resmi dokumen pengiriman material SBST</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="window.print()" type="button"
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg flex items-center gap-1.5 shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak / Download PDF
                    </button>
                    <button @click="showSppmModal = false" type="button"
                        class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- SPPM Document Content (Printable) -->
            <div class="p-8 max-h-[80vh] overflow-y-auto" id="printableSppm">
                <!-- Kop Surat -->
                <div class="border-b-2 border-gray-900 pb-4 mb-6 text-center">
                    <h2 class="text-sm font-bold tracking-wider uppercase text-gray-800">KEPOLISIAN NEGARA REPUBLIK INDONESIA</h2>
                    <h3 class="text-xs font-bold tracking-wider uppercase text-gray-700">{{ $shipment->senderRegionalPolice->name }}</h3>
                    <p class="text-[10px] text-gray-500 italic">Jalan Raya Utama No. 1, SBST Terintegrasi</p>
                    <div class="mt-4 pt-2 border-t border-gray-300">
                        <h1 class="text-lg font-black text-gray-900 underline uppercase tracking-wide">SURAT PERINTAH PENGELUARAN MATERIAL (SPPM)</h1>
                        <p class="text-xs font-mono font-bold text-purple-700 mt-0.5">Nomor: {{ $shipment->code }}</p>
                    </div>
                </div>

                <!-- Detail Header SPPM -->
                <div class="grid grid-cols-2 gap-4 text-xs mb-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <div>
                        <table class="w-full">
                            <tr>
                                <td class="py-1 font-semibold text-gray-500 w-28">Dari (Pengirim)</td>
                                <td class="py-1 font-bold text-gray-900">: {{ $shipment->senderRegionalPolice->name }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 font-semibold text-gray-500">Tujuan (Penerima)</td>
                                <td class="py-1 font-bold text-gray-900">: {{ $shipment->receiverPoliceStation->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table class="w-full">
                            <tr>
                                <td class="py-1 font-semibold text-gray-500 w-28">Tanggal SPPM</td>
                                <td class="py-1 font-bold text-gray-900">: {{ \Carbon\Carbon::parse($shipment->shipment_date)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1 font-semibold text-gray-500">Status</td>
                                <td class="py-1 font-bold text-green-700">: {{ strtoupper($shipment->status) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tabel Rincian Material SPPM -->
                <div class="mb-6">
                    <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-2">Rincian Material Yang Dikeluarkan:</h4>
                    <table class="w-full text-xs text-left border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-300">
                                <th class="p-2 border border-gray-300 text-center w-10 font-bold">NO</th>
                                <th class="p-2 border border-gray-300 font-bold">MATERIAL UTAMA</th>
                                <th class="p-2 border border-gray-300 font-bold">DETAIL MATERIAL</th>
                                <th class="p-2 border border-gray-300 font-bold">SERVICE / ITEM PENDUKUNG</th>
                                <th class="p-2 border border-gray-300 font-bold">NOMOR SERI / KODE</th>
                                <th class="p-2 border border-gray-300 text-right font-bold w-24">JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shipment->materialShipmentDetails as $idx => $item)
                                <tr class="border-b border-gray-200">
                                    <td class="p-2 border border-gray-300 text-center font-medium">{{ $idx + 1 }}</td>
                                    <td class="p-2 border border-gray-300 font-bold text-gray-900">{{ $item->type?->name ?? '-' }}</td>
                                    <td class="p-2 border border-gray-300 text-gray-700">{{ $item->typeDetail?->name ?? '-' }}</td>
                                    <td class="p-2 border border-gray-300 text-gray-700">{{ $item->stockDetail?->service?->name ?? '-' }}</td>
                                    <td class="p-2 border border-gray-300 font-mono text-xs">{{ implode(' | ', array_filter([$item->code, $item->number_serial_first, $item->number_serial_second])) ?: '-' }}</td>
                                    <td class="p-2 border border-gray-300 text-right font-bold text-gray-900">{{ number_format($item->quantity, 0, ',', '.') }} unit</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-bold">
                                <td colspan="5" class="p-2 border border-gray-300 text-right uppercase">Total Material:</td>
                                <td class="p-2 border border-gray-300 text-right text-purple-800">{{ number_format($shipment->materialShipmentDetails->sum('quantity'), 0, ',', '.') }} unit</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Tanda Tangan Section -->
                <div class="grid grid-cols-2 gap-8 text-xs text-center mt-12 pt-4">
                    <div>
                        <p class="font-medium text-gray-600">Penerima Material,</p>
                        <p class="font-bold text-gray-800 mt-1 mb-16">{{ $shipment->receiverPoliceStation->name }}</p>
                        <p class="font-bold underline text-gray-900">{{ $shipment->receivedByUser?->name ?? '(............................................)' }}</p>
                        <p class="text-[10px] text-gray-500">Petugas Logistik Polres</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-600">Pengirim Material,</p>
                        <p class="font-bold text-gray-800 mt-1 mb-16">{{ $shipment->senderRegionalPolice->name }}</p>
                        <p class="font-bold underline text-gray-900">(............................................)</p>
                        <p class="text-[10px] text-gray-500">Kasi Fasmat Polda</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
