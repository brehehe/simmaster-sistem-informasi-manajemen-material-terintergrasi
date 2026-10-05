<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('menu-polda.material-shipment') }}" class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-blue-600">
                    {{ $shipmentId ? 'Edit SPPM' : 'Buat SPPM' }}
                </h1>
            </div>
            <p class="mt-1 text-sm text-gray-500 ml-11">
                Surat Perintah Pengeluaran Materiel — Draft dapat dicetak untuk dibawa ke warehouse dan diproses pengiriman ke Polres.
            </p>
        </div>
        <a href="{{ route('menu-polda.material-shipment') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 transition-colors">
            <span aria-hidden="true">←</span> Kembali ke Daftar SPPM
        </a>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
        <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm flex items-start gap-3">
            <svg class="h-5 w-5 text-red-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="font-bold">Formulir belum dapat disimpan:</p>
                <p class="mt-0.5 text-xs text-red-600">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    <!-- Card 1: Informasi Utama SPPM -->
    <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl shadow-gray-200/50">
        <div class="border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50/50 px-6 py-4">
            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Informasi Utama Pengiriman
            </h2>
        </div>
        <div class="grid gap-6 p-6 md:grid-cols-2">
            <!-- Nomor SPPM -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="sppm-code" class="block text-sm font-semibold text-gray-700">
                        Nomor SPPM <span class="text-red-500">*</span>
                    </label>
                    <button type="button" wire:click="resetCodeToDefault" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors" title="Kembalikan ke format nomor default">
                        Format Default
                    </button>
                </div>
                <input 
                    type="text" 
                    id="sppm-code" 
                    wire:model="code" 
                    placeholder="Contoh: SPPM/1/X/LOG.3.6.7./2026" 
                    class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 font-mono text-sm font-semibold text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                >
                <p class="mt-1 text-xs text-gray-400">Nomor SPPM otomatis terisi default dan dapat diubah bebas sesuai kebutuhan.</p>
                @error('code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Tanggal Pengiriman -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Tanggal Pengiriman <span class="text-red-500">*</span>
                </label>
                <input type="date" wire:model.live="shipment_date" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                @error('shipment_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Dropdown Polda Pengirim (Admin Only) -->
            @if(auth()->user()->hasRole('Admin'))
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Polda Pengirim <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="regional_police_id" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <option value="">Pilih Polda</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                    @error('regional_police_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            @endif

            <!-- Dropdown Polres Tujuan -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Polres Tujuan <span class="text-red-500">*</span>
                </label>
                <select wire:model="receiver_police_station_id" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    <option value="">Pilih Polres Tujuan</option>
                    @foreach($stations as $station)
                        <option value="{{ $station->id }}">{{ $station->name }}</option>
                    @endforeach
                </select>
                @error('receiver_police_station_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <!-- Card 2: Rincian Material & Batch -->
    <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl shadow-gray-200/50">
        <div class="border-b border-gray-100 px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Daftar Material Pengiriman
                </h2>
                <p class="mt-1 text-sm text-gray-500">Pilih batch stok material utama atau pendukung yang akan dikirim</p>
            </div>
            <button type="button" wire:click="addDetail" class="inline-flex items-center gap-2 rounded-xl bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100 border border-blue-200 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Material
            </button>
        </div>

        <div class="border-b border-blue-100 bg-blue-50/60 px-6 py-3 text-xs text-blue-800">
            Jumlah nomor seri = akhir − awal + 1. Sisa rentang nomor seri pada batch stok otomatis disesuaikan saat SPPM disimpan atau dipindai warehouse.
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="w-12 px-4 py-3.5 text-center">No</th>
                        <th class="px-4 py-3.5">Material & Batch Stok <span class="text-red-500">*</span></th>
                        <th class="w-24 px-4 py-3.5 text-center">Satuan</th>
                        <th class="w-32 px-4 py-3.5 text-center">Jumlah <span class="text-red-500">*</span></th>
                        <th class="w-36 px-4 py-3.5">Seri Awal</th>
                        <th class="w-36 px-4 py-3.5">Seri Akhir</th>
                        <th class="w-16 px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($details as $index => $detail)
                        @php($stock = $stocks->firstWhere('id', $detail['stock_detail_id']))
                        <tr wire:key="shipment-row-{{ $index }}" class="transition-colors hover:bg-gray-50/70">
                            <td class="px-4 py-3.5 text-center text-xs text-gray-400 font-semibold">{{ $index + 1 }}</td>
                            <td class="px-4 py-3.5">
                                <select aria-label="Batch material {{ $index + 1 }}" wire:model.live="details.{{ $index }}.stock_detail_id" class="w-full min-w-[280px] rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                    <option value="">Pilih material dan batch...</option>
                                    @foreach($stocks as $batch)
                                        <option value="{{ $batch->id }}">
                                            {{ $batch->type?->name }} {{ $batch->typeDetail?->name }} {{ $batch->service?->name }} {{ $batch->serviceDetail?->name }} | {{ $batch->code ?? 'Batch' }} {{ $batch->number_serial_first ? "({$batch->number_serial_first}–{$batch->number_serial_second})" : '' }} | Sisa: {{ (int)$batch->quantity }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('details.'.$index.'.stock_detail_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs font-medium text-gray-500">
                                <span class="inline-block rounded-md bg-gray-100 px-2 py-1 font-semibold text-gray-600">{{ $stock?->type?->unit ?? 'Unit' }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <input aria-label="Jumlah kirim" type="number" min="1" step="1" wire:model.live.blur="details.{{ $index }}.quantity" class="w-24 rounded-lg border border-gray-200 px-2.5 py-1.5 text-center text-sm font-semibold tabular-nums focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                @error('details.'.$index.'.quantity')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="px-4 py-3.5">
                                <input aria-label="Seri awal" wire:model.live.blur="details.{{ $index }}.number_serial_first" class="w-full rounded-lg border border-gray-200 px-2.5 py-1.5 text-sm font-mono focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:bg-gray-100 disabled:text-gray-400" @disabled(!$stock?->number_serial_first) placeholder="{{ $stock?->number_serial_first ? 'Seri awal' : 'Non-seri' }}">
                                @error('details.'.$index.'.number_serial_first')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="px-4 py-3.5">
                                <input aria-label="Seri akhir" wire:model="details.{{ $index }}.number_serial_second" class="w-full rounded-lg border border-gray-200 px-2.5 py-1.5 text-sm font-mono focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:bg-gray-100 disabled:text-gray-400" @disabled(!$stock?->number_serial_first) placeholder="{{ $stock?->number_serial_first ? 'Seri akhir' : 'Non-seri' }}">
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <button type="button" wire:click="removeDetail({{ $index }})" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Hapus baris">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-gray-500">
                                Belum ada material yang ditambahkan. Klik tombol "Tambah Material" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Card 3: Catatan Tambahan -->
    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <label for="sppm-notes" class="mb-2 block text-sm font-semibold text-gray-700">
            Catatan Tambahan <span class="font-normal text-gray-400">(opsional)</span>
        </label>
        <textarea id="sppm-notes" wire:model="notes" rows="3" placeholder="Tambahkan catatan atau keterangan pengiriman jika diperlukan..." class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"></textarea>
    </section>

    <!-- Sticky Bottom Actions Bar -->
    <div class="sticky bottom-0 z-10 flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-lg sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-gray-500">
            <span>Total baris material: <strong class="text-blue-600 font-bold">{{ count($details) }}</strong> item</span>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('menu-polda.material-shipment') }}" class="rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-200 transition-colors">
                Batal
            </a>
            <button type="button" wire:click="save(false)" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-xl border border-blue-600 bg-white px-5 py-2.5 text-sm font-semibold text-blue-600 hover:bg-blue-50 transition-colors shadow-sm disabled:opacity-50">
                <span wire:loading.remove wire:target="save(false)">💾 Simpan Draft untuk Polres</span>
                <span wire:loading wire:target="save(false)">Menyimpan Draft...</span>
            </button>
            <button type="button" wire:click="save(true)" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/20 hover:from-blue-700 hover:to-cyan-600 transition-all disabled:opacity-50">
                <span wire:loading.remove wire:target="save(true)">🚀 Simpan dan Kirim</span>
                <span wire:loading wire:target="save(true)">Memproses Kirim...</span>
            </button>
        </div>
    </div>
</div>
