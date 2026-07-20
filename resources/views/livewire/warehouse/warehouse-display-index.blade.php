<div wire:poll.10s x-data="{ darkMode: $persist(true).as('warehouse_tv_darkmode') }" :class="darkMode ? 'bg-slate-950 text-slate-100' : 'bg-slate-100 text-slate-900'" class="min-h-screen p-4 md:p-6 transition-colors duration-300 font-sans selection:bg-cyan-500 selection:text-white" id="warehouse-tv-container">

    {{-- Top Header / Banner Monitor --}}
    <div :class="darkMode ? 'bg-slate-900/90 border-slate-800' : 'bg-white border-slate-200 shadow-xl'" class="mb-6 border rounded-3xl p-4 md:p-5 backdrop-blur-md flex flex-col md:flex-row items-center justify-between gap-4 transition-colors">
        <div class="flex items-center gap-4">
            {{-- Kembali ke Dashboard Button --}}
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2 px-3.5 py-2 rounded-2xl text-xs font-bold transition-all border shadow-sm"
                :class="darkMode ? 'bg-slate-800 hover:bg-slate-700 text-slate-200 border-slate-700' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-300'">
                <svg class="w-4 h-4 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Dashboard
            </a>

            <div class="h-8 w-px bg-slate-700/50 hidden md:block"></div>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-cyan-500/30 text-xl animate-pulse">
                    🖥️
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl md:text-xl font-black tracking-tight flex items-center gap-2" :class="darkMode ? 'text-white' : 'text-slate-900'">
                            MONITOR LIVE WAREHOUSE
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 uppercase tracking-widest">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                            LIVE (10s)
                        </span>
                    </div>
                    <p class="text-xs mt-0.5" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">Layar Monitor TV Gudang — Rak, Update Stok, & Antrean Picking</p>
                </div>
            </div>
        </div>

        {{-- Right Controls: Location Filter + Theme Switcher + Live Clock + Fullscreen --}}
        <div class="flex items-center gap-3 flex-wrap">
            @if(auth()->user()->hasRole('Admin') && count($policeStations) > 0)
                <select wire:model.live="selectedPoliceStationId" :class="darkMode ? 'bg-slate-800 border-slate-700 text-slate-200' : 'bg-slate-50 border-slate-300 text-slate-800'" class="border text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-cyan-500 outline-none">
                    <option value="">Semua Lokasi / Polda</option>
                    @foreach($policeStations as $ps)
                        <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                    @endforeach
                </select>
            @endif

            {{-- Dark / Light Mode Toggle --}}
            <button @click="darkMode = !darkMode" :class="darkMode ? 'bg-slate-800 hover:bg-slate-700 text-amber-400 border-slate-700' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-300'" class="border px-3.5 py-2 rounded-2xl text-xs font-bold flex items-center gap-1.5 transition-all">
                <span x-show="darkMode" class="flex items-center gap-1">☀️ Mode Terang</span>
                <span x-show="!darkMode" class="flex items-center gap-1">🌙 Mode Gelap</span>
            </button>

            {{-- Live Clock --}}
            <div :class="darkMode ? 'bg-slate-800/80 border-slate-700/80' : 'bg-slate-100 border-slate-300'" class="border px-3.5 py-1.5 rounded-2xl text-center">
                <div class="text-[10px] uppercase font-bold tracking-wider" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">Waktu Sistem</div>
                <div class="text-sm font-black text-cyan-500 font-mono" id="live-clock">--:--:-- WIB</div>
            </div>

            {{-- Fullscreen Toggle --}}
            <button onclick="toggleFullScreen()" class="bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold px-3.5 py-2 rounded-2xl text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                Fullscreen
            </button>
        </div>
    </div>

    {{-- Live Summary Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div :class="darkMode ? 'bg-slate-900/80 border-slate-800' : 'bg-white border-slate-200 shadow-md'" class="border rounded-2xl p-4 flex items-center justify-between transition-colors">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">Total Stok Tersedia</div>
                <div class="text-3xl font-black mt-1 font-mono" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ number_format($totalStockQty, 0, ',', '.') }}</div>
                <div class="text-[10px] text-emerald-500 font-semibold mt-1">✓ Terakumulasi di Gudang</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-500 flex items-center justify-center text-2xl font-bold">
                📦
            </div>
        </div>

        <div :class="darkMode ? 'bg-slate-900/80 border-slate-800' : 'bg-white border-slate-200 shadow-md'" class="border rounded-2xl p-4 flex items-center justify-between transition-colors">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">Rak Aktif Terisi</div>
                <div class="text-3xl font-black text-cyan-500 mt-1 font-mono">{{ $activeRacksCount }} / {{ $racks->count() }}</div>
                <div class="text-[10px] text-cyan-600 font-semibold mt-1">Lokasi penyimpanan terdata</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-500 flex items-center justify-center text-2xl font-bold">
                🏢
            </div>
        </div>

        <div :class="darkMode ? 'bg-slate-900/80 border-slate-800' : 'bg-white border-slate-200 shadow-md'" class="border rounded-2xl p-4 flex items-center justify-between transition-colors">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">Antrean Distribusi</div>
                <div class="text-3xl font-black text-amber-500 mt-1 font-mono">{{ $pendingQueueCount }}</div>
                <div class="text-[10px] text-amber-600 font-semibold mt-1">⏳ Perlu picking / pengiriman</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-500 flex items-center justify-center text-2xl font-bold">
                🚚
            </div>
        </div>

        <div :class="darkMode ? 'bg-slate-900/80 border-slate-800' : 'bg-white border-slate-200 shadow-md'" class="border rounded-2xl p-4 flex items-center justify-between transition-colors">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">Pergerakan Hari Ini</div>
                <div class="text-3xl font-black text-emerald-500 mt-1 font-mono">{{ $recentMovements->count() }}</div>
                <div class="text-[10px] text-emerald-600 font-semibold mt-1">Transaksi stok tercatat</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 flex items-center justify-center text-2xl font-bold">
                ⚡
            </div>
        </div>
    </div>

    {{-- Main Grid: Left = KFC Queue Board, Right = Live Racks Board --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- LEFT COLUMN (5 Cols): ANTREAN DISTRIBUSI & PICKING (KFC ORDER DISPLAY BOARD) --}}
        <div class="lg:col-span-5 flex flex-col gap-6">
            <div :class="darkMode ? 'bg-slate-900/90 border-slate-800' : 'bg-white border-slate-200 shadow-xl'" class="border rounded-3xl p-5 flex-1 transition-colors">
                <div class="flex items-center justify-between border-b pb-4 mb-4" :class="darkMode ? 'border-slate-800' : 'border-slate-200'">
                    <h2 class="text-base font-black flex items-center gap-2" :class="darkMode ? 'text-white' : 'text-slate-900'">
                        <span class="w-3 h-3 rounded-full bg-amber-400 animate-ping"></span>
                        ⏳ ANTREAN DISTRIBUSI & PICKING
                    </h2>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-amber-500/20 text-amber-500 border border-amber-500/30">
                        {{ $pendingShipments->count() }} PENGIRIMAN
                    </span>
                </div>

                <div class="flex flex-col gap-3 max-h-[620px] overflow-y-auto pr-1">
                    @forelse($pendingShipments as $shipment)
                        <div :class="darkMode ? 'bg-slate-800/80 border-slate-700/80' : 'bg-slate-50 border-slate-200'" class="border-l-4 {{ $shipment->status === 'sent' || $shipment->status === 'shipped' ? 'border-amber-500' : 'border-cyan-500' }} border-y border-r rounded-2xl p-4 shadow-sm transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono font-black text-cyan-500 text-sm tracking-wider">
                                    {{ $shipment->code }}
                                </span>
                                @if($shipment->status === 'sent' || $shipment->status === 'shipped')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-600 border border-amber-500/30 uppercase">
                                        🚚 DALAM PENGIRIMAN
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-cyan-500/20 text-cyan-600 border border-cyan-500/30 uppercase">
                                        📦 SIAP PICKING / AMBIL
                                    </span>
                                @endif
                            </div>

                            <div class="text-xs font-extrabold mb-2" :class="darkMode ? 'text-white' : 'text-slate-900'">
                                Tujuan: <span class="text-cyan-500 font-bold">{{ $shipment->receiverPoliceStation?->name ?? 'Polres Tujuan' }}</span>
                            </div>

                            {{-- Item Breakdown --}}
                            <div :class="darkMode ? 'bg-slate-900/90 border-slate-800' : 'bg-white border-slate-200'" class="rounded-xl p-2.5 border text-xs">
                                <div class="text-[10px] font-bold uppercase tracking-wider mb-1" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">Rincian Barang yang Diambil:</div>
                                @foreach($shipment->materialShipmentDetails as $item)
                                    <div class="flex items-center justify-between py-1 border-b last:border-0" :class="darkMode ? 'border-slate-800 text-slate-200' : 'border-slate-100 text-slate-800'">
                                        <div class="flex items-center gap-1.5 font-medium">
                                            <span class="text-cyan-500">▪</span>
                                            {{ $item->type->name ?? '-' }}
                                            @if($item->typeDetail)
                                                <span :class="darkMode ? 'text-slate-400' : 'text-slate-500'">({{ $item->typeDetail->name }})</span>
                                            @endif
                                        </div>
                                        <span class="font-black text-emerald-500 font-mono">{{ number_format($item->quantity, 0, ',', '.') }} unit</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-2 text-[10px] flex items-center justify-between" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">
                                <span>Tanggal: {{ $shipment->created_at ? $shipment->created_at->format('d/m/Y H:i') : '-' }}</span>
                                <span class="font-mono">{{ $shipment->created_at ? $shipment->created_at->diffForHumans() : '' }}</span>
                            </div>
                        </div>
                    @empty
                        <div :class="darkMode ? 'bg-slate-800/40 border-slate-800' : 'bg-slate-50 border-slate-200'" class="border rounded-2xl p-10 text-center">
                            <div class="text-4xl mb-2">✅</div>
                            <div class="font-bold text-sm" :class="darkMode ? 'text-slate-400' : 'text-slate-600'">Tidak ada antrean picking saat ini</div>
                            <div class="text-xs text-slate-500 mt-1">Semua pengiriman material telah diproses</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN (7 Cols): MONITOR DATA RAK & LOKASI STOK --}}
        <div class="lg:col-span-7 flex flex-col gap-6">
            <div :class="darkMode ? 'bg-slate-900/90 border-slate-800' : 'bg-white border-slate-200 shadow-xl'" class="border rounded-3xl p-5 flex-1 transition-colors">
                <div class="flex items-center justify-between border-b pb-4 mb-4" :class="darkMode ? 'border-slate-800' : 'border-slate-200'">
                    <h2 class="text-base font-black flex items-center gap-2" :class="darkMode ? 'text-white' : 'text-slate-900'">
                        <span class="w-3 h-3 rounded-full bg-cyan-400"></span>
                        🏢 MONITOR DATA RAK & POSISI MATERIAL
                    </h2>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-500 border border-cyan-500/30">
                        {{ $racks->count() }} RAK TERDAFTAR
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[620px] overflow-y-auto pr-1">
                    @forelse($racks as $rack)
                        <div :class="darkMode ? 'bg-slate-800/80 border-slate-700/80' : 'bg-slate-50 border-slate-200 shadow-sm'" class="border rounded-2xl p-4 hover:border-cyan-500/50 transition-all">
                            <div class="flex items-center justify-between border-b pb-2.5 mb-3" :class="darkMode ? 'border-slate-700/60' : 'border-slate-200'">
                                <div>
                                    <h3 class="font-black text-base flex items-center gap-2" :class="darkMode ? 'text-white' : 'text-slate-900'">
                                        <span class="px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-500 border border-cyan-500/30 font-mono text-xs">{{ $rack['code'] }}</span>
                                        {{ $rack['name'] }}
                                    </h3>
                                    <div class="text-[10px] mt-0.5" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">{{ $rack['location'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-black text-emerald-500 font-mono">{{ number_format($rack['total_quantity'], 0, ',', '.') }}</div>
                                    <div class="text-[10px]" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">unit tersimpan</div>
                                </div>
                            </div>

                            {{-- Stored Material Details --}}
                            @if(count($rack['items']) > 0)
                                <div class="flex flex-col gap-1.5">
                                    @foreach($rack['items']->take(4) as $sd)
                                        <div :class="darkMode ? 'bg-slate-900/70 border-slate-800' : 'bg-white border-slate-200'" class="px-2.5 py-1.5 rounded-xl border flex items-center justify-between text-xs">
                                            <div>
                                                <span class="font-bold" :class="darkMode ? 'text-slate-200' : 'text-slate-800'">{{ $sd->type?->name ?? '-' }}</span>
                                                @if($sd->typeDetail)
                                                    <span class="text-[10px]" :class="darkMode ? 'text-slate-400' : 'text-slate-500'"> ({{ $sd->typeDetail->name }})</span>
                                                @endif
                                                @if($sd->number_serial_first || $sd->code)
                                                    <div class="text-[10px] font-mono text-cyan-500">{{ $sd->number_serial_first ?: $sd->code }}</div>
                                                @endif
                                            </div>
                                            <span class="font-bold text-cyan-500 font-mono text-xs bg-cyan-500/10 px-2 py-0.5 rounded border border-cyan-500/20">
                                                {{ number_format($sd->quantity, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                    @if(count($rack['items']) > 4)
                                        <div class="text-[10px] text-slate-500 text-center font-semibold pt-1">
                                            + {{ count($rack['items']) - 4 }} item lainnya di rak ini
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="py-4 text-center text-slate-500 text-xs italic">
                                    Rak kosong (Belum ada material diletakkan)
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-2 py-12 text-center text-slate-500">
                            Belum ada data rak terdaftar di lokasi ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Bar: Realtime Pergerakan Stok --}}
    <div :class="darkMode ? 'bg-slate-900/90 border-slate-800' : 'bg-white border-slate-200 shadow-xl'" class="mt-6 border rounded-3xl p-5 transition-colors">
        <div class="flex items-center justify-between mb-3 border-b pb-3" :class="darkMode ? 'border-slate-800' : 'border-slate-200'">
            <h3 class="text-sm font-black flex items-center gap-2 uppercase tracking-wider" :class="darkMode ? 'text-white' : 'text-slate-900'">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                ⚡ LOG PERGERAKAN STOK HARI INI
            </h3>
            <span class="text-xs" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">Total Hari Ini: <strong class="text-emerald-500">{{ $recentMovements->count() }}</strong> transaksi</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            @forelse($recentMovements as $mv)
                <div :class="darkMode ? 'bg-slate-800/80 border-slate-700/60' : 'bg-slate-50 border-slate-200'" class="border rounded-xl p-3 text-xs">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-mono text-[10px]" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">{{ $mv->created_at ? $mv->created_at->format('H:i') : '-' }}</span>
                        <span class="font-bold px-1.5 py-0.5 rounded text-[10px] {{ $mv->status_type === 'out' ? 'bg-red-500/20 text-red-500 border border-red-500/30' : 'bg-emerald-500/20 text-emerald-500 border border-emerald-500/30' }}">
                            {{ $mv->status_type === 'out' ? 'OUT' : 'IN' }}
                        </span>
                    </div>
                    <div class="font-bold truncate" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ $mv->type?->name ?? '-' }}</div>
                    <div class="text-[10px] truncate" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">{{ $mv->description ?? '-' }}</div>
                    <div class="font-mono font-black text-right mt-1.5 text-xs {{ $mv->status_type === 'out' ? 'text-red-500' : 'text-emerald-500' }}">
                        {{ $mv->status_type === 'out' ? '-' : '+' }}{{ number_format($mv->quantity, 0, ',', '.') }}
                    </div>
                </div>
            @empty
                <div class="col-span-6 py-4 text-center text-slate-500 text-xs italic">
                    Belum ada pergerakan stok tercatat hari ini.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Digital Clock Script & Fullscreen Handler --}}
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockEl = document.getElementById('live-clock');
            if (clockEl) {
                clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }

        setInterval(updateClock, 1000);
        updateClock();

        function toggleFullScreen() {
            const elem = document.documentElement;
            if (!document.fullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    </script>
</div>
