<div x-data="{
    showScannerModal: false,
    scanner: null,
    cameraError: null,
    openScanner() {
        this.showScannerModal = true;
        this.cameraError = null;
    },
    closeScanner() {
        this.stopCamera();
        this.showScannerModal = false;
    },
    startCamera() {
        this.cameraError = null;
        if (!window.Html5Qrcode) {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js';
            script.onload = () => { this.runCamera(); };
            document.head.appendChild(script);
        } else {
            this.runCamera();
        }
    },
    runCamera() {
        this.$nextTick(() => {
            try {
                const html5QrCode = new Html5Qrcode('polres-qr-reader');
                this.scanner = html5QrCode;
                html5QrCode.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 220, height: 220 } },
                    (decodedText) => {
                        $wire.set('searchCode', decodedText);
                        $wire.call('searchByCode');
                        this.closeScanner();
                    },
                    () => {}
                ).catch(err => {
                    this.cameraError = 'Izin kamera ditolak atau kamera tidak tersedia.';
                });
            } catch (e) {
                this.cameraError = 'Kamera tidak dapat diinisialisasi.';
            }
        });
    },
    stopCamera() {
        if (this.scanner) {
            this.scanner.stop().catch(() => {});
            this.scanner = null;
        }
    }
}">
    <!-- Header -->
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">Penerimaan Material</h1>
                <p class="text-gray-500 mt-1">Terima dan pantau pengiriman material SPPM yang dikirim dari Polda</p>
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

    <!-- Search Card -->
    <div
        class="bg-gradient-to-br from-blue-50 to-blue-50/50 rounded-2xl shadow-xl shadow-blue-200/50 border border-blue-100 overflow-hidden mb-6 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-3 rounded-xl bg-blue-500 text-white shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Cari Pengiriman</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Manual Input Nomor SPPM -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Input Nomor SPPM Manual</label>
                <div class="flex gap-2">
                    <input wire:model="searchCode" type="text"
                        placeholder="Contoh: SPPM/SHP-20260721-001..."
                        class="flex-1 px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200"
                        wire:keydown.enter="searchByCode">
                    <button wire:click="searchByCode" type="button"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Barcode Scanner -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Atau Scan Barcode/QR Code</label>
                <button @click="openScanner()" type="button"
                    class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Verifikasi QR Code / Barcode SPPM
                </button>
            </div>
        </div>
    </div>

    <!-- Received Shipments Table -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800">Pengiriman Diterima</h3>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Tampilkan</span>
                <select wire:model.live="perPage"
                    class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                </select>
                <span class="text-sm text-gray-600">data</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-4 w-12 text-center">No</th>
                        <th class="px-6 py-4">Kode SPPM</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4 text-center">Rendi (I, II, III, IV)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4">Polres</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($materialShipments as $index => $shipment)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-sm text-gray-500 text-center">
                                {{ $materialShipments->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold font-mono text-blue-600">{{ $shipment->code }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                                {{ $shipment->shipment_date ? \Carbon\Carbon::parse($shipment->shipment_date)->format('d M Y') : ($shipment->shipped_at ? \Carbon\Carbon::parse($shipment->shipped_at)->format('d M Y') : '-') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                                {{ $shipment->shipped_at ? \Carbon\Carbon::parse($shipment->shipped_at)->format('H:i') : ($shipment->created_at ? \Carbon\Carbon::parse($shipment->created_at)->format('H:i') : '-') }} WIB
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @php
                                    $stage = $shipment->rendi_stage ?? ($shipment->status === 'received' ? 'Rendi IV' : 'Rendi II');
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $stage }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if ($shipment->status === 'shipped' || $shipment->status === 'sent')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                        Dikirim
                                    </span>
                                @elseif ($shipment->status === 'received')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                        Diterima
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                        {{ ucfirst($shipment->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-semibold whitespace-nowrap">
                                {{ $shipment->receiverPoliceStation->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <a  href="{{ route('menu-polres.material-shipment.receive.detail', ['id' => $shipment->id]) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                Belum ada data pengiriman material diterima
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $materialShipments->links() }}
        </div>
    </div>

    <!-- SCANNER MODAL FOR POLRES RECEIVE -->
    <div x-show="showScannerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-gray-100 overflow-hidden" @click.away="closeScanner()">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-white">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    🔍 Verifikasi & Scan QR Code SPPM
                </h3>
                <button @click="closeScanner()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <div class="p-6 overflow-y-auto flex-1 space-y-4">
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 p-4 rounded-2xl">
                    <label class="block text-xs font-bold text-blue-900 mb-2">Input Nomor SPPM Manual:</label>
                    <div class="flex items-center gap-2">
                        <input type="text" x-model="$wire.searchCode" placeholder="Contoh: SPPM/SHP-20260721-001"
                            @keydown.enter="$wire.searchByCode(); closeScanner();"
                            class="w-full px-3.5 py-2.5 text-xs font-mono font-bold rounded-xl border border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white">
                        <button type="button" @click="$wire.searchByCode(); closeScanner();"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition-all shrink-0">
                            Cari SPPM
                        </button>
                    </div>
                </div>

                <div class="text-center pt-2">
                    <button type="button" @click="startCamera()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all inline-flex items-center gap-1.5">
                        📷 Buka Kamera Webcam Scanner
                    </button>
                </div>

                <div id="polres-qr-reader" class="w-full max-w-xs mx-auto rounded-xl overflow-hidden bg-slate-900 border border-slate-800" style="max-height: 200px;"></div>

                <template x-if="cameraError">
                    <div class="p-3 bg-amber-500/10 border border-amber-500/30 rounded-xl text-amber-600 text-xs text-center">
                        ⚠️ <span x-text="cameraError"></span>
                    </div>
                </template>
            </div>

            <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex justify-end shrink-0">
                <button @click="closeScanner()" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-bold text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
