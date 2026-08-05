<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-2xl bg-blue-50 border border-blue-100 text-blue-700">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Scan QR SPPM — Serah Terima Gudang</h1>
                    <p class="text-xs text-gray-500 mt-0.5">Verifikasi dan dokumentasi pengambilan materiil dari gudang berbasis QR Code SPPM</p>
                </div>
            </div>
            <a href="{{ route('warehouse.display') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold text-xs shadow-sm transition-all">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Live Monitor Gudang (TV)
            </a>
        </div>
    </div>

    {{-- Progress Steps --}}
    <div class="flex items-center justify-center gap-0 mb-8">
        <div class="flex items-center gap-2 px-4 py-2 rounded-l-xl {{ $step === 'scan' ? 'bg-blue-700 text-white' : 'bg-blue-50 text-blue-800' }} font-semibold text-xs transition-colors">
            <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[11px] font-bold">1</span>
            Verifikasi SPPM
        </div>
        <div class="w-8 h-0.5 {{ in_array($step, ['form','success']) ? 'bg-blue-600' : 'bg-gray-200' }} transition-colors"></div>
        <div class="flex items-center gap-2 px-4 py-2 {{ in_array($step, ['form','success']) ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-400' }} font-semibold text-xs transition-colors">
            <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[11px] font-bold">2</span>
            Form TTD & Dokumentasi
        </div>
        <div class="w-8 h-0.5 {{ $step === 'success' ? 'bg-blue-600' : 'bg-gray-200' }} transition-colors"></div>
        <div class="flex items-center gap-2 px-4 py-2 rounded-r-xl {{ $step === 'success' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-400' }} font-semibold text-xs transition-colors">
            <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[11px] font-bold">✓</span>
            Selesai
        </div>
    </div>

    {{-- ========================== STEP 1: SCAN QR ========================== --}}
    @if($step === 'scan')
    <div class="max-w-6xl mx-auto" x-data="{
        scanner: null, isScanning: false, showCamera: false, cameraError: null,
        startCamera() {
            this.cameraError = null;
            this.isScanning = true;
            this.showCamera = true;
            if (!window.Html5Qrcode) {
                const s = document.createElement('script');
                s.src = 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js';
                s.onload = () => this.runCamera();
                document.head.appendChild(s);
            } else { this.runCamera(); }
        },
        runCamera() {
            this.$nextTick(() => {
                try {
                    const qr = new Html5Qrcode('ws-qr-reader');
                    this.scanner = qr;
                    qr.start({ facingMode: 'environment' }, { fps: 10, qrbox: { width: 240, height: 240 } },
                        (text) => {
                            $wire.set('scanInputCode', text);
                            $wire.call('processScanQr');
                            this.stopCamera();
                        }, () => {}
                    ).catch(() => { this.isScanning = false; this.cameraError = 'Izin kamera ditolak.'; });
                } catch(e) { this.isScanning = false; this.cameraError = 'Kamera tidak dapat diinisialisasi.'; }
            });
        },
        stopCamera() {
            this.showCamera = false;
            if (this.scanner && this.isScanning) {
                this.scanner.stop().then(() => this.isScanning = false).catch(() => this.isScanning = false);
            }
        }
    }">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Card Header -->
            <div class="p-6 bg-gradient-to-r from-slate-900 to-blue-900 text-white">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Verifikasi Kode / QR Code SPPM
                </h2>
                <p class="text-blue-200 text-xs mt-1">Scan QR dari dokumen SPPM atau ketik nomor SPPM secara manual</p>
            </div>

            <div class="p-6 space-y-5">
                {{-- Camera Viewfinder --}}
                <div x-show="showCamera" x-cloak class="bg-slate-950 p-3 rounded-2xl border border-blue-400/40">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-blue-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span>Kamera Active
                        </span>
                        <button @click="stopCamera()" class="px-2 py-0.5 bg-red-500/20 hover:bg-red-500/30 text-red-400 text-[10px] font-bold rounded-lg border border-red-500/30">
                            Sembunyikan Kamera
                        </button>
                    </div>
                    <div id="ws-qr-reader" class="w-full max-w-sm mx-auto rounded-xl overflow-hidden" style="min-height: 240px;"></div>
                    <template x-if="cameraError">
                        <div class="mt-2 p-2 bg-amber-500/10 border border-amber-500/30 rounded-xl text-amber-300 text-[11px]">
                            <span x-text="cameraError"></span>
                        </div>
                    </template>
                </div>

                {{-- Input Area --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Nomor SPPM</label>
                        <button type="button" @click="showCamera ? stopCamera() : startCamera()"
                            class="text-xs font-semibold text-blue-700 hover:text-blue-900 underline flex items-center gap-1">
                            <span x-text="showCamera ? 'Sembunyikan Kamera' : 'Buka Kamera Scanner'"></span>
                        </button>
                    </div>

                    <div class="flex gap-3">
                        <input type="text" wire:model="scanInputCode"
                            wire:keydown.enter="processScanQr"
                            placeholder="Contoh: SHP-JATIM-20260805-001"
                            class="flex-1 px-4 py-3 text-sm font-mono font-bold rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-gray-50 focus:bg-white">
                        <button wire:click="processScanQr"
                            class="px-6 py-3 bg-blue-800 hover:bg-blue-900 text-white font-bold text-xs rounded-xl shadow-sm transition-all whitespace-nowrap">
                            Cari SPPM
                        </button>
                    </div>

                    @error('scanInputCode')
                        <div class="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700">
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Info --}}
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs text-slate-700">
                    <p class="font-bold mb-1 uppercase tracking-wider text-[11px] text-blue-900">Petunjuk Operasional:</p>
                    <ul class="space-y-1 list-disc list-inside text-gray-600">
                        <li>Minta QR Code SPPM dari Admin Polres atau cetakan dokumen resmi.</li>
                        <li>Scan QR menggunakan kamera browser atau masukkan nomor SPPM secara manual.</li>
                        <li>Pastikan status SPPM berstatus <strong>Draft</strong> untuk diproses serah terima.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ========================== STEP 2: FORM TTD & FOTO ========================== --}}
    @if($step === 'form' && $scannedShipment)
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Info SPPM --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold">📋 SPPM: {{ $scannedShipment->code }}</h2>
                    <p class="text-blue-100 text-sm mt-0.5">
                        {{ $scannedShipment->senderRegionalPolice?->name }} →
                        {{ $scannedShipment->receiverPoliceStation?->name }}
                    </p>
                </div>
                <button wire:click="resetScan" class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-lg transition-all">
                    ← Scan Ulang
                </button>
            </div>

            {{-- Daftar Material --}}
            <div class="p-5">
                <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">Rincian Barang & Lokasi Rak:</h3>
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 text-gray-600 font-semibold">
                            <tr>
                                <th class="p-3">Material</th>
                                <th class="p-3">Detail</th>
                                <th class="p-3 font-mono">Nomor Seri / Kode</th>
                                <th class="p-3 text-center bg-emerald-50">Lokasi Rak</th>
                                <th class="p-3 text-center">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($scannedShipment->materialShipmentDetails as $d)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3 font-bold text-gray-800">{{ $d->type?->name ?? '-' }}</td>
                                    <td class="p-3 text-gray-600">{{ $d->typeDetail?->name ?? '-' }}</td>
                                    <td class="p-3 font-mono text-blue-600">
                                        {{ implode(' | ', array_filter([$d->code, $d->number_serial_first, $d->number_serial_second])) ?: '—' }}
                                    </td>
                                    <td class="p-3 text-center bg-emerald-50/40">
                                        <span class="font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded border border-emerald-200">
                                            📍 {{ $d->stockDetail?->rack?->name ?? 'Gudang Utama' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center font-bold text-blue-700">{{ number_format($d->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Form Identitas --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    👤 Identitas Pengambil Material
                </h2>
                <p class="text-gray-500 text-sm mt-0.5">Data petugas yang mengambil material dari warehouse</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="pickerName" placeholder="Nama lengkap pengambil"
                        class="w-full px-3 py-2.5 text-sm rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                    @error('pickerName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pangkat <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="pickerRank" placeholder="Contoh: BRIGADIR, BRIPKA"
                        class="w-full px-3 py-2.5 text-sm rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                    @error('pickerRank') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="pickerPosition" placeholder="Contoh: Bamat Polres ..."
                        class="w-full px-3 py-2.5 text-sm rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all">
                    @error('pickerPosition') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- TTD Digital + Foto Dokumentasi --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- TTD Digital --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden"
                x-data="{
                    canvas: null, ctx: null, isDrawing: false, isEmpty: true,
                    init() {
                        this.$nextTick(() => {
                            this.canvas = this.$refs.sigCanvas;
                            this.ctx = this.canvas.getContext('2d');
                            this.ctx.strokeStyle = '#1e293b';
                            this.ctx.lineWidth = 2;
                            this.ctx.lineCap = 'round';
                            this.ctx.lineJoin = 'round';
                        });
                    },
                    startDraw(e) {
                        this.isDrawing = true;
                        this.isEmpty = false;
                        const pos = this.getPos(e);
                        this.ctx.beginPath();
                        this.ctx.moveTo(pos.x, pos.y);
                    },
                    draw(e) {
                        if (!this.isDrawing) return;
                        e.preventDefault();
                        const pos = this.getPos(e);
                        this.ctx.lineTo(pos.x, pos.y);
                        this.ctx.stroke();
                    },
                    stopDraw() {
                        this.isDrawing = false;
                        if (!this.isEmpty) {
                            const dataUrl = this.canvas.toDataURL('image/png');
                            $wire.set('pickerSignature', dataUrl);
                        }
                    },
                    clearCanvas() {
                        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                        this.isEmpty = true;
                        $wire.set('pickerSignature', '');
                    },
                    getPos(e) {
                        const rect = this.canvas.getBoundingClientRect();
                        const scaleX = this.canvas.width / rect.width;
                        const scaleY = this.canvas.height / rect.height;
                        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                        return {
                            x: (clientX - rect.left) * scaleX,
                            y: (clientY - rect.top) * scaleY
                        };
                    }
                }" x-init="init()">
                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-blue-50 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">✍️ Tanda Tangan Digital</h2>
                    <button type="button" @click="clearCanvas()" class="text-xs font-semibold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-all">
                        🗑️ Hapus TTD
                    </button>
                </div>
                <div class="p-5">
                    <div class="border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-gray-50 relative"
                        style="touch-action: none;">
                        <canvas x-ref="sigCanvas"
                            width="480" height="200"
                            class="w-full cursor-crosshair"
                            style="display: block;"
                            @mousedown="startDraw($event)"
                            @mousemove="draw($event)"
                            @mouseup="stopDraw()"
                            @mouseleave="stopDraw()"
                            @touchstart.prevent="startDraw($event)"
                            @touchmove.prevent="draw($event)"
                            @touchend="stopDraw()">
                        </canvas>
                        <div x-show="isEmpty" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <p class="text-gray-300 text-sm font-medium select-none">Tanda tangan di sini...</p>
                        </div>
                    </div>
                    @error('pickerSignature')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-2">Gunakan mouse atau sentuhan layar untuk menandatangani</p>
                </div>
            </div>

            {{-- Foto Dokumentasi --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden"
                x-data="{
                    stream: null, isCamera: false, photoPreview: null,
                    async startCamera() {
                        try {
                            this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                            this.$refs.videoEl.srcObject = this.stream;
                            this.isCamera = true;
                        } catch(e) { alert('Izin kamera ditolak.'); }
                    },
                    stopCamera() {
                        if (this.stream) { this.stream.getTracks().forEach(t => t.stop()); this.stream = null; }
                        this.isCamera = false;
                    },
                    capturePhoto() {
                        const canvas = document.createElement('canvas');
                        canvas.width = this.$refs.videoEl.videoWidth;
                        canvas.height = this.$refs.videoEl.videoHeight;
                        canvas.getContext('2d').drawImage(this.$refs.videoEl, 0, 0);
                        canvas.toBlob(async (blob) => {
                            const file = new File([blob], 'foto-dokumentasi-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                            const dt = new DataTransfer();
                            dt.items.add(file);
                            this.$refs.fileInput.files = dt.files;
                            this.$refs.fileInput.dispatchEvent(new Event('change'));
                            this.photoPreview = canvas.toDataURL('image/jpeg', 0.8);
                            this.stopCamera();
                        }, 'image/jpeg', 0.85);
                    },
                    handleFileChange(e) {
                        const f = e.target.files[0];
                        if (f) {
                            const reader = new FileReader();
                            reader.onload = ev => this.photoPreview = ev.target.result;
                            reader.readAsDataURL(f);
                        }
                    }
                }">
                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">📸 Foto Dokumentasi</h2>
                    <div class="flex gap-2">
                        <button type="button" @click="isCamera ? stopCamera() : startCamera()"
                            class="text-xs font-semibold text-amber-700 hover:text-amber-900 bg-amber-100 hover:bg-amber-200 px-3 py-1.5 rounded-lg transition-all">
                            <span x-text="isCamera ? '🙈 Tutup' : '📷 Kamera'"></span>
                        </button>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    {{-- Camera View --}}
                    <div x-show="isCamera" class="rounded-xl overflow-hidden bg-black">
                        <video x-ref="videoEl" autoplay playsinline class="w-full max-h-48 object-cover"></video>
                        <div class="flex justify-center p-2 bg-black/50">
                            <button type="button" @click="capturePhoto()"
                                class="w-12 h-12 rounded-full bg-white border-4 border-gray-300 hover:bg-gray-100 transition-all shadow-lg flex items-center justify-center text-xl">
                                📸
                            </button>
                        </div>
                    </div>

                    {{-- Upload --}}
                    <div x-show="!isCamera">
                        <input x-ref="fileInput" type="file" wire:model="pickerPhoto" accept="image/*"
                            @change="handleFileChange($event)"
                            class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 transition-all">
                        <p class="text-xs text-gray-400 mt-1">Upload foto JPG/PNG (maks 5MB) atau gunakan kamera</p>
                    </div>

                    {{-- Preview --}}
                    <div x-show="photoPreview" class="rounded-xl overflow-hidden border border-gray-200">
                        <img :src="photoPreview" alt="Preview foto dokumentasi" class="w-full max-h-48 object-cover">
                        <div class="px-3 py-2 bg-green-50 border-t border-green-100">
                            <p class="text-xs text-green-700 font-semibold">✅ Foto siap diupload</p>
                        </div>
                    </div>

                    @error('pickerPhoto') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pb-6">
            <button wire:click="resetScan" type="button"
                class="w-full sm:w-auto px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all text-sm">
                ← Scan Ulang
            </button>
            <button wire:click="submitPicking" type="button"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-wait"
                class="w-full sm:w-auto px-10 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl shadow-xl shadow-emerald-500/25 transition-all transform hover:scale-105 text-sm flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="submitPicking">✅ Submit Serah Terima</span>
                <span wire:loading wire:target="submitPicking">⏳ Menyimpan...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- ========================== STEP 3: SUCCESS ========================== --}}
    @if($step === 'success')
    <div class="max-w-2xl mx-auto text-center py-12 bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 mb-1">Serah Terima Berhasil Disimpan!</h2>
        <p class="text-xs text-gray-500 mb-2">Data pengambilan fisik materiil, TTD digital, dan foto dokumentasi telah tercatat di sistem.</p>
        @if($scannedShipment)
            <div class="inline-block bg-blue-50 border border-blue-200 text-blue-800 font-mono font-bold text-sm px-4 py-1.5 rounded-lg mb-6">
                Kode SPPM: {{ $scannedShipment->code }}
            </div>
        @endif

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @if($scannedShipment)
                <a href="{{ route('menu-polda.material-shipment.print', $scannedShipment->id) }}" target="_blank"
                    class="w-full sm:w-auto px-6 py-3 bg-blue-800 hover:bg-blue-900 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Bukti Serah Terima (PDF)
                </a>
            @endif
            <button wire:click="resetScan" type="button"
                class="w-full sm:w-auto px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs rounded-xl transition-all">
                Scan SPPM Berikutnya
            </button>
        </div>
    </div>
    @endif
</div>
