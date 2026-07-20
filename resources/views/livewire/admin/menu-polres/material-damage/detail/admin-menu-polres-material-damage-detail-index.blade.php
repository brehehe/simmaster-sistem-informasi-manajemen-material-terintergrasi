<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('menu-polres.material-damage') }}" wire:navigate class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-red-600">
                        {{ $isEditMode ? '📋 Detail BA Material Rusak/Hilang' : '📋 Input BA Material Rusak/Hilang' }}
                    </h1>
                </div>
                <p class="text-gray-500 ml-14">Berita Acara (BA) Kerusakan / Kehilangan Material — stok Polres akan berkurang otomatis</p>
            </div>
            @if($isEditMode)
                <button onclick="printBA()"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-red-700 to-red-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-red-500/25 hover:from-red-800 hover:to-red-700 transition-all text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak BA
                </button>
            @endif
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

    <!-- Header Info Card — Informasi BA -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-red-50 to-orange-50/50">
            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Data Berita Acara
                <span class="text-xs text-red-500 font-normal ml-1">(Kode BA: {{ $code }})</span>
            </h2>
        </div>
        <div class="p-6">
            <!-- Row 1: Tanggal, Status, Polres -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal BA <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="date" @disabled($isEditMode) class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-red-400 focus:ring-2 focus:ring-red-400/20 disabled:bg-gray-100 disabled:text-gray-500">
                    @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Status BA <span class="text-red-500">*</span></label>
                    <select wire:model="status" @disabled($isEditMode) class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-red-400 disabled:bg-gray-100 disabled:text-gray-500">
                        <option value="reported">📋 Dilaporkan</option>
                        <option value="under_review">🔍 Dalam Pemeriksaan</option>
                        <option value="approved">✅ Disetujui</option>
                        <option value="disposed">🗑️ Dimusnahkan</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                @if (Auth::user()->hasRole('Admin'))
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Polres <span class="text-red-500">*</span></label>
                        <div wire:ignore wire:key="select-police-station-{{ rand() }}">
                            <select id="select-police-station" @disabled($isEditMode)
                                x-data="{ toJSON() { return {}; } }" x-init="
                                    setTimeout(() => {
                                        const el = $($el).selectize({
                                            dropdownParent: 'body', allowClear: true,
                                            onChange: function(val) { $wire.set('policeStationId', val || ''); }
                                        })[0].selectize;
                                        el.setValue('{{ $policeStationId }}', true);
                                    }, 10);">
                                <option value="">-- Pilih Polres --</option>
                                @foreach ($policeStations as $ps)
                                    <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('policeStationId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>

            <!-- Row 2: Material Utama + Petugas + Jabatan + Dasar Hukum -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <!-- Material Utama -->
                <div class="border border-red-100 bg-red-50/40 p-4 rounded-xl">
                    <label class="flex items-center gap-2 text-xs font-semibold text-red-900 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                        </svg>
                        Jenis Material <span class="text-red-500">*</span>
                    </label>
                    <div wire:ignore wire:key="select-type-id-{{ rand() }}">
                        <select id="select-type-id" @disabled($isEditMode)
                            x-data="{ toJSON() { return {}; } }" x-init="
                                setTimeout(() => {
                                    const el = $($el).selectize({
                                        dropdownParent: 'body', allowClear: true,
                                        onChange: function(val) { $wire.set('typeId', val || ''); }
                                    })[0].selectize;
                                    el.setValue('{{ $typeId }}', true);
                                }, 10);">
                            <option value="">-- Pilih Material --</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('typeId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Keterangan/Dasar Laporan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Uraian / Dasar Laporan</label>
                    <textarea wire:model="description" rows="3" @disabled($isEditMode)
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-red-400 focus:ring-2 focus:ring-red-400/20 disabled:bg-gray-100 disabled:text-gray-500"
                        placeholder="Contoh: Berdasarkan hasil pengecekan dan verifikasi kondisi fisik material..."></textarea>
                </div>
            </div>

            <!-- Row 3: Petugas yang membuat BA -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Petugas Pembuat BA</label>
                    <input type="text" wire:model="officerName" @disabled($isEditMode)
                        placeholder="Nama lengkap petugas..."
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-red-400 disabled:bg-gray-100 disabled:text-gray-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Jabatan Petugas</label>
                    <input type="text" wire:model="officerRank" @disabled($isEditMode)
                        placeholder="Contoh: Bamin Sarpras, Kaur Fasmat..."
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-red-400 disabled:bg-gray-100 disabled:text-gray-500">
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Table (Daftar Material Rusak/Hilang) -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-amber-50/50 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Daftar Material Rusak / Hilang
                <span class="text-xs text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full border border-orange-200">⚠️ Stok otomatis berkurang saat disimpan</span>
            </h2>
            @if (!$isEditMode)
                <button wire:click="addDetail" type="button"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-semibold py-2 px-4 rounded-xl shadow-lg shadow-green-500/30 transition-all duration-300 transform hover:scale-105 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Tambah Baris
                </button>
            @endif
        </div>

        <div class="p-0">
            <div class="overflow-x-auto overflow-y-visible pb-24">
                <table class="w-full text-sm text-left align-top border-collapse min-w-[1300px]">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
                        <tr>
                            <th class="px-3 py-3 font-semibold w-10 text-center text-xs">No</th>
                            @if($is_type_detail)
                                <th class="px-3 py-3 font-semibold text-xs min-w-[130px]">Detail Material</th>
                            @endif
                            <th class="px-3 py-3 font-semibold text-xs min-w-[130px]">Service</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[130px]">Service Detail</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[140px] bg-blue-50">No Seri A (Awal)</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[140px] bg-blue-50">No Seri B (Akhir)</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[100px]">Status Rusak</th>
                            <th class="px-3 py-3 font-semibold text-xs w-20 text-center bg-red-50">Stok Ada</th>
                            <th class="px-3 py-3 font-semibold text-xs w-20">Jml <span class="text-red-500">*</span></th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[160px]">Keterangan / Alasan <span class="text-red-500">*</span></th>
                            @if (!$isEditMode)
                                <th class="px-3 py-3 font-semibold w-12 text-center text-xs"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($details as $index => $detail)
                            @php
                                $rowTypeDetailId = $detail['type_detail_id'] ?? null;
                                $rowServiceId = $detail['service_id'] ?? null;
                                $filteredServices = collect($this->services)->where(
                                    $rowTypeDetailId ? 'type_detail_id' : 'type_id',
                                    $rowTypeDetailId ? $rowTypeDetailId : $typeId
                                );
                                $selectedSvc = collect($this->services)->firstWhere('id', $rowServiceId);
                                $filteredServiceDetails = data_get($selectedSvc, 'details', []);
                            @endphp
                            <tr wire:key="row-{{ $index }}-{{ $typeId }}" class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-3 py-3 text-center font-medium text-gray-500 align-top text-xs pt-4">{{ $index + 1 }}</td>

                                @if($is_type_detail)
                                    <td class="px-3 py-3 align-top">
                                        <select wire:model.live="details.{{ $index }}.type_detail_id" @disabled($isEditMode)
                                            class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-red-400 disabled:bg-gray-100 disabled:text-gray-500">
                                            <option value="">-- Semua Detail --</option>
                                            @foreach($this->typeDetails as $td)
                                                <option value="{{ data_get($td, 'id') }}">{{ data_get($td, 'name') }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endif

                                <td class="px-3 py-3 align-top">
                                    <select wire:model.live="details.{{ $index }}.service_id" @disabled($isEditMode)
                                        class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-red-400 disabled:bg-gray-100 disabled:text-gray-500">
                                        <option value="">-- Semua --</option>
                                        @foreach($filteredServices as $svc)
                                            <option value="{{ data_get($svc, 'id') }}">{{ data_get($svc, 'name') }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <select wire:model.live="details.{{ $index }}.service_detail_id" @disabled($isEditMode)
                                        class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-red-400 disabled:bg-gray-100 disabled:text-gray-500">
                                        <option value="">-- Semua --</option>
                                        @foreach($filteredServiceDetails as $sdt)
                                            <option value="{{ data_get($sdt, 'id') }}">{{ data_get($sdt, 'name') }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                {{-- No Seri A --}}
                                <td class="px-2 py-3 align-top bg-blue-50/30">
                                    @if($is_with_serial_number)
                                        <div wire:ignore wire:key="stock-key-{{ $index }}-{{ md5(json_encode($stockOptions[$index] ?? [])) }}">
                                            <select x-data="{ toJSON() { return {}; } }" x-init="
                                                const selectize = $($el).selectize({
                                                    dropdownParent: 'body', allowClear: true,
                                                    onChange: function(val) { $wire.set('details.{{ $index }}.selected_stock_key', val); }
                                                })[0].selectize;
                                            " @disabled($isEditMode)
                                                class="w-full text-xs rounded border border-blue-300 disabled:bg-gray-100">
                                                <option value="">-- Pilih S/N --</option>
                                                @foreach($stockOptions[$index] ?? [] as $opt)
                                                    <option value="{{ $opt['key'] }}">{{ $opt['number_serial_first'] ?: $opt['item_code'] }} ({{ $opt['quantity'] }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if(!empty($detail['number_serial_first']))
                                            <div class="text-[10px] text-blue-700 font-mono mt-1">{{ $detail['number_serial_first'] }}</div>
                                        @endif
                                    @else
                                        <input type="text"
                                            wire:model.live="details.{{ $index }}.number_serial_first"
                                            placeholder="No Seri Awal..." @disabled($isEditMode)
                                            class="w-full px-2 py-2 text-xs font-mono rounded border border-blue-300 focus:border-blue-500 bg-white disabled:bg-gray-100">
                                    @endif
                                    @error("details.{$index}.stock_detail_id") <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                </td>

                                {{-- No Seri B --}}
                                <td class="px-2 py-3 align-top bg-blue-50/30">
                                    <input type="text"
                                        wire:model.live="details.{{ $index }}.number_serial_second"
                                        placeholder="No Seri Akhir..." @disabled($isEditMode)
                                        class="w-full px-2 py-2 text-xs font-mono rounded border border-blue-300 focus:border-blue-500 bg-white disabled:bg-gray-100">
                                </td>

                                {{-- Status Rusak/Hilang --}}
                                <td class="px-3 py-3 align-top">
                                    <select wire:model.live="details.{{ $index }}.damage_type" @disabled($isEditMode)
                                        class="w-full px-2 py-2 text-xs rounded border font-semibold {{ ($detail['damage_type'] ?? '') == 'lost' ? 'border-red-400 bg-red-50 text-red-700' : 'border-orange-400 bg-orange-50 text-orange-700' }} disabled:bg-gray-100 disabled:text-gray-500">
                                        <option value="damaged">🔶 Rusak</option>
                                        <option value="lost">🔴 Hilang</option>
                                    </select>
                                </td>

                                <!-- Stok Tersedia -->
                                <td class="px-3 py-3 align-top bg-red-50/30 text-center">
                                    <div class="font-bold {{ $detail['available_quantity'] > 0 ? 'text-red-600' : 'text-gray-400' }} text-sm pt-1">
                                        {{ number_format($detail['available_quantity'], 0, ',', '.') }}
                                    </div>
                                    <div class="text-[10px] text-gray-400">unit</div>
                                </td>

                                <!-- Jumlah Rusak/Hilang -->
                                <td class="px-3 py-3 align-top">
                                    <input type="number" min="1" step="1" max="{{ $detail['available_quantity'] }}"
                                        wire:model.live="details.{{ $index }}.quantity"
                                        placeholder="Qty" @disabled($isEditMode)
                                        class="w-full px-2 py-2 text-xs font-bold text-center rounded border border-red-300 focus:border-red-500 bg-red-50 disabled:bg-gray-100 disabled:text-gray-400 text-red-700">
                                    @error("details.{$index}.quantity")
                                        <p class="text-red-500 text-[10px] mt-1 text-center">{{ $message }}</p>
                                    @enderror
                                </td>

                                <!-- Alasan / Keterangan -->
                                <td class="px-3 py-3 align-top">
                                    <input type="text" wire:model.defer="details.{{ $index }}.reason"
                                        placeholder="Kondisi fisik, penyebab, keterangan..." @disabled($isEditMode)
                                        class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-red-400 disabled:bg-gray-100 disabled:text-gray-500 mb-1">
                                    @error("details.{$index}.reason")
                                        <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p>
                                    @enderror
                                </td>

                                @if (!$isEditMode)
                                    <td class="px-3 py-3 text-center align-top pt-4">
                                        @if (count($details) > 1)
                                            <button type="button" wire:click="removeDetail({{ $index }})"
                                                class="p-1.5 inline-flex items-center justify-center rounded bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            </button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-12 text-center text-gray-400 italic">Klik "Tambah Baris" untuk mulai input material rusak...</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4">
        <div class="text-xs text-gray-400 italic">
            ⚠️ Setelah disimpan, stok material Polres akan berkurang secara otomatis sesuai jumlah yang dilaporkan.
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('menu-polres.material-damage') }}" wire:navigate class="px-8 py-3 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all text-center">Batal</a>
            @if (!$isEditMode)
                <button wire:click="save" type="button"
                    class="px-10 py-3 text-sm font-bold text-white bg-gradient-to-r from-red-600 to-red-700 rounded-xl hover:from-red-700 hover:to-red-800 shadow-xl shadow-red-500/30 transition-all transform hover:scale-105 text-center">
                    <span wire:loading.remove wire:target="save">💾 Simpan BA Material Rusak</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            @endif
        </div>
    </div>

    {{-- ===== PRINT AREA — FORMAT BA MATERIAL RUSAK ===== --}}
    <div id="print-ba-area" style="display:none;">
        <div style="font-family: Arial, sans-serif; padding: 24px; color: #111; max-width: 800px; margin: 0 auto;">
            {{-- Header BA --}}
            <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #b91c1c; padding-bottom: 14px;">
                <h2 style="font-size: 15px; font-weight: bold; letter-spacing: 1px; margin: 0; text-transform: uppercase;">BERITA ACARA</h2>
                <h3 style="font-size: 14px; font-weight: bold; margin: 4px 0 0; color: #b91c1c; text-transform: uppercase;">
                    {{ ($details[0]['damage_type'] ?? 'damaged') == 'lost' ? 'KEHILANGAN' : 'KERUSAKAN' }} MATERIAL FASMAT
                </h3>
                <p style="font-size: 11px; margin: 6px 0 0; color: #555;">Nomor: {{ $code }}</p>
            </div>

            {{-- Pembuka --}}
            <p style="font-size: 11px; line-height: 1.8; margin-bottom: 12px;">
                Pada hari ini, tanggal
                <strong>{{ $date ? \Carbon\Carbon::parse($date)->translatedFormat('d F Y') : '.......' }}</strong>,
                kami yang bertanda tangan di bawah ini:
            </p>
            <table style="width: 100%; font-size: 11px; margin-bottom: 14px;">
                <tr>
                    <td style="width: 160px; padding: 2px 0;">Nama</td>
                    <td style="width: 10px;">:</td>
                    <td><strong>{{ $officerName ?: '........................................' }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 2px 0;">Jabatan</td>
                    <td>:</td>
                    <td><strong>{{ $officerRank ?: '........................................' }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 2px 0;">Satuan</td>
                    <td>:</td>
                    <td><strong>{{ auth()->user()->policeStation?->name ?? '........................................' }}</strong></td>
                </tr>
            </table>

            <p style="font-size: 11px; line-height: 1.8; margin-bottom: 14px;">
                Menyatakan bahwa telah terjadi <strong>{{ ($details[0]['damage_type'] ?? 'damaged') == 'lost' ? 'KEHILANGAN' : 'KERUSAKAN' }}</strong>
                material fasmat dengan rincian sebagai berikut:
            </p>

            {{-- Tabel Daftar Material --}}
            <table style="width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 16px;">
                <thead>
                    <tr style="background: #fee2e2; color: #7f1d1d;">
                        <th style="border: 1px solid #fca5a5; padding: 6px; text-align: center; width: 30px;">No</th>
                        <th style="border: 1px solid #fca5a5; padding: 6px; text-align: left;">Jenis Material</th>
                        <th style="border: 1px solid #fca5a5; padding: 6px; text-align: center;">No Seri A</th>
                        <th style="border: 1px solid #fca5a5; padding: 6px; text-align: center;">No Seri B</th>
                        <th style="border: 1px solid #fca5a5; padding: 6px; text-align: center; width: 60px;">Jml</th>
                        <th style="border: 1px solid #fca5a5; padding: 6px; text-align: center; width: 60px;">Status</th>
                        <th style="border: 1px solid #fca5a5; padding: 6px; text-align: left;">Keterangan / Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $i => $d)
                    <tr style="{{ $i % 2 == 0 ? 'background:#fff' : 'background:#fff7f7' }}">
                        <td style="border: 1px solid #fca5a5; padding: 5px; text-align: center;">{{ $i + 1 }}</td>
                        <td style="border: 1px solid #fca5a5; padding: 5px;">
                            @php $typeName = \App\Models\Type\Type::find($typeId)?->name ?? '-'; @endphp
                            {{ $typeName }}
                            @if(!empty($d['type_detail_id']))
                                / {{ \App\Models\Type\TypeDetail::find($d['type_detail_id'])?->name ?? '' }}
                            @endif
                        </td>
                        <td style="border: 1px solid #fca5a5; padding: 5px; text-align: center; font-family: monospace; color: #1d4ed8;">{{ $d['number_serial_first'] ?: ($d['item_code'] ?: '-') }}</td>
                        <td style="border: 1px solid #fca5a5; padding: 5px; text-align: center; font-family: monospace; color: #1d4ed8;">{{ $d['number_serial_second'] ?: '-' }}</td>
                        <td style="border: 1px solid #fca5a5; padding: 5px; text-align: center; font-weight: bold;">{{ number_format($d['quantity'], 0, ',', '.') }}</td>
                        <td style="border: 1px solid #fca5a5; padding: 5px; text-align: center; color: {{ ($d['damage_type'] ?? '') == 'lost' ? '#b91c1c' : '#c2410c' }}; font-weight: bold;">
                            {{ ($d['damage_type'] ?? '') == 'lost' ? 'HILANG' : 'RUSAK' }}
                        </td>
                        <td style="border: 1px solid #fca5a5; padding: 5px;">{{ $d['reason'] ?: '-' }}</td>
                    </tr>
                    @endforeach
                    <tr style="background: #fee2e2; font-weight: bold;">
                        <td colspan="4" style="border: 1px solid #fca5a5; padding: 5px; text-align: right; color: #7f1d1d;">TOTAL:</td>
                        <td style="border: 1px solid #fca5a5; padding: 5px; text-align: center; color: #7f1d1d;">
                            {{ number_format(array_sum(array_column($details, 'quantity')), 0, ',', '.') }}
                        </td>
                        <td colspan="2" style="border: 1px solid #fca5a5; padding: 5px; color: #7f1d1d;">unit</td>
                    </tr>
                </tbody>
            </table>

            {{-- Uraian --}}
            @if($description)
            <p style="font-size: 11px; margin-bottom: 14px;"><strong>Uraian:</strong> {{ $description }}</p>
            @endif

            {{-- Penutup --}}
            <p style="font-size: 11px; line-height: 1.8; margin-bottom: 24px;">
                Demikian Berita Acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
            </p>

            {{-- TTD --}}
            <div style="display: flex; justify-content: space-between; font-size: 11px; margin-top: 20px;">
                <div style="text-align: center;">
                    <p>Mengetahui,</p>
                    <p style="margin-top: 4px;">Kasat / Kabag</p>
                    <div style="height: 60px;"></div>
                    <p>( ................................. )</p>
                </div>
                <div style="text-align: center;">
                    <p>Dibuat di ............, {{ $date ? \Carbon\Carbon::parse($date)->translatedFormat('d F Y') : '.......' }}</p>
                    <p style="margin-top: 4px;">Yang Membuat BA,</p>
                    <div style="height: 60px;"></div>
                    <p>( <strong>{{ $officerName ?: '..............................' }}</strong> )</p>
                    <p style="font-size: 9px; color: #555;">{{ $officerRank ?: '' }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printBA() {
            const printContents = document.getElementById('print-ba-area').innerHTML;
            const w = window.open('', '_blank');
            w.document.write(`
                <html><head><title>BA Material Rusak</title>
                <style>body{font-family:Arial,sans-serif;} @media print{body{margin:0;}}</style>
                </head><body>${printContents}</body></html>
            `);
            w.document.close();
            w.focus();
            setTimeout(() => { w.print(); w.close(); }, 500);
        }

        document.addEventListener('livewire:navigated', () => {
            $('.selectize-dropdown').remove();
        });
    </script>
</div>
