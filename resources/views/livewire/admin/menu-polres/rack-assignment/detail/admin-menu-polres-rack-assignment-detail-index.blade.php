<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('menu-polres.rack-assignment') }}"  class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-blue-600">
                        {{ $isEditMode ? 'Detail' : 'Penugasan Rak Baru' }}
                    </h1>
                </div>
                <p class="text-gray-500 ml-14">Pindahkan material ke/dari rak di Polres</p>
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

    <!-- Header Info Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50/50">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Informasi Penugasan Rak
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kode <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.defer="code" readonly class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="date" @disabled($isEditMode) class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:bg-gray-100 disabled:text-gray-500">
                    @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih SPPM (Penerimaan Material)</label>
                    <select wire:model.live="materialShipmentId" @disabled($isEditMode)
                        class="w-full px-3 py-2 text-sm rounded-lg border border-purple-200 bg-purple-50/40 text-purple-900 font-semibold focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 disabled:bg-gray-100 disabled:text-gray-500">
                        <option value="">-- Pilih Kode SPPM Terbaru / Unassigned --</option>
                        @foreach ($availableSppms as $sppm)
                            <option value="{{ $sppm->id }}">{{ $sppm->code }} (Tgl: {{ \Carbon\Carbon::parse($sppm->received_at ?? $sppm->shipment_date)->format('d/m/Y') }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-purple-600 mt-1">Otomatis muat rincian isi dari SPPM ke rak.</p>
                </div>

                @if (Auth::user()->hasRole('Admin'))
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Polres <span class="text-red-500">*</span></label>
                        <div wire:ignore wire:key="select-police-station-{{ rand() }}">
                            <select id="select-police-station" wire:model="policeStationId" @disabled($isEditMode)
                                x-data x-init="
                                    setTimeout(() => {
                                        const el = $($el).selectize({
                                            dropdownParent: 'body',
                                            allowClear: true,
                                            onChange: function(val) { @this.set('policeStationId', val || ''); }
                                        })[0].selectize;
                                        el.setValue(@this.get('policeStationId'), true);
                                    }, 10);
                                ">
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Material Utama -->
                <div class="border border-blue-100 bg-blue-50/50 p-4 rounded-xl">
                    <label class="flex items-center gap-2 text-sm font-semibold text-blue-900 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                        </svg>
                        Material Utama <span class="text-red-500">*</span>
                    </label>
                    <div wire:ignore wire:key="select-type-id-{{ rand() }}">
                        <select id="select-type-id" wire:model="typeId" @disabled($isEditMode)
                            x-data x-init="
                                setTimeout(() => {
                                    const el = $($el).selectize({
                                        dropdownParent: 'body',
                                        allowClear: true,
                                        onChange: function(val) { @this.set('typeId', val || ''); }
                                    })[0].selectize;
                                    el.setValue(@this.get('typeId'), true);
                                }, 10);
                            ">
                            <option value="">-- Pilih Material --</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('typeId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea wire:model="description" rows="2" @disabled($isEditMode) class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:bg-gray-100 disabled:text-gray-500" placeholder="Catatan penugasan rak..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-amber-50/50 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Detail Penugasan Rak
            </h2>
            @if (!$isEditMode)
                <button wire:click="addDetail" type="button"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-green-500/30 transition-all duration-300 transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Tambah Item
                </button>
            @endif
        </div>

        <div class="p-0">
            <div class="overflow-x-auto overflow-y-visible pb-24">
                <table class="w-full text-sm text-left align-top border-collapse min-w-[1200px]">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
                        <tr>
                            <th class="px-3 py-3 font-semibold w-12 text-center text-xs">No</th>
                            @if($is_type_detail)
                                <th class="px-3 py-3 font-semibold text-xs min-w-[140px]">Detail Material</th>
                            @endif
                            <th class="px-3 py-3 font-semibold text-xs min-w-[140px]">Service</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[140px]">Service Detail</th>
                            @if($is_with_serial_number)
                                <th class="px-3 py-3 font-semibold text-xs min-w-[240px]">Identitas Barang (Kode | S/N)</th>
                            @endif
                            <th class="px-3 py-3 font-semibold text-xs min-w-[140px] bg-yellow-50">Rak Asal</th>
                            <th class="px-3 py-3 font-semibold text-xs min-w-[140px] bg-green-50">Rak Tujuan</th>
                            <th class="px-3 py-3 font-semibold text-xs w-28 text-center bg-blue-50">Tersedia</th>
                            <th class="px-3 py-3 font-semibold text-xs w-24">Jumlah <span class="text-red-500">*</span></th>
                            @if (!$isEditMode)
                                <th class="px-3 py-3 font-semibold w-16 text-center text-xs">Aksi</th>
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
                                $fromRackName = $detail['from_rack_id']
                                    ? ($racks->firstWhere('id', $detail['from_rack_id'])?->name ?? '—')
                                    : 'Tanpa Rak';
                            @endphp
                            <tr wire:key="row-{{ $index }}-{{ $typeId }}" class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-3 py-3 text-center font-medium text-gray-500 align-top text-xs pt-5">{{ $index + 1 }}</td>

                                @if($is_type_detail)
                                    <td class="px-3 py-3 align-top">
                                        <select wire:model.live="details.{{ $index }}.type_detail_id" @disabled($isEditMode)
                                            class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                            <option value="">-- Semua Detail --</option>
                                            @foreach($this->typeDetails as $td)
                                                <option value="{{ data_get($td, 'id') }}">{{ data_get($td, 'name') }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endif

                                <td class="px-3 py-3 align-top">
                                    <select wire:model.live="details.{{ $index }}.service_id" @disabled($isEditMode)
                                        class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                        <option value="">-- Semua Service --</option>
                                        @foreach($filteredServices as $svc)
                                            <option value="{{ data_get($svc, 'id') }}">{{ data_get($svc, 'name') }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="px-3 py-3 align-top">
                                    <select wire:model.live="details.{{ $index }}.service_detail_id" @disabled($isEditMode)
                                        class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                        <option value="">-- Semua --</option>
                                        @foreach($filteredServiceDetails as $sdt)
                                            <option value="{{ data_get($sdt, 'id') }}">{{ data_get($sdt, 'name') }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                @if($is_with_serial_number)
                                    <td class="px-2 py-3 align-top">
                                        <div wire:ignore wire:key="stock-key-{{ $index }}-{{ md5(json_encode($stockOptions[$index] ?? [])) }}">
                                            <select x-data x-init="
                                                const selectize = $($el).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    onChange: function(val) {
                                                        @this.set('details.{{ $index }}.selected_stock_key', val);
                                                    }
                                                })[0].selectize;
                                            " wire:model="details.{{ $index }}.selected_stock_key" @disabled($isEditMode)
                                                class="w-full text-xs rounded border border-gray-300 disabled:bg-gray-100">
                                                <option value="">-- Pilih Barang --</option>
                                                @foreach($stockOptions[$index] ?? [] as $opt)
                                                    <option value="{{ $opt['key'] }}">{{ $opt['key'] }} (Stok: {{ $opt['quantity'] }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error("details.{$index}.stock_detail_id") <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                    </td>
                                @endif

                                <!-- From Rack (readonly, auto-filled) -->
                                <td class="px-3 py-3 align-top bg-yellow-50/30">
                                    <div class="text-xs font-semibold {{ $detail['from_rack_id'] ? 'text-yellow-700 bg-yellow-100' : 'text-gray-400 bg-gray-100' }} px-2 py-2 rounded text-center">
                                        📦 {{ $fromRackName }}
                                    </div>
                                </td>

                                <!-- To Rack (user select) -->
                                <td class="px-3 py-3 align-top bg-green-50/30">
                                    <select wire:model.live="details.{{ $index }}.to_rack_id" @disabled($isEditMode)
                                        class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-green-500 disabled:bg-gray-100 disabled:text-gray-500">
                                        <option value="">— Tanpa Rak —</option>
                                        @foreach($racks as $rack)
                                            <option value="{{ $rack->id }}">{{ $rack->name }}</option>
                                        @endforeach
                                    </select>
                                    @error("details.{$index}.to_rack_id") <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                </td>

                                <!-- Tersedia -->
                                <td class="px-3 py-3 align-top bg-blue-50/30 text-center">
                                    <div class="font-semibold {{ $detail['available_quantity'] > 0 ? 'text-blue-600' : 'text-gray-400' }} pt-2 text-xs">
                                        {{ number_format($detail['available_quantity'], 0, ',', '.') }}
                                    </div>
                                </td>

                                <!-- Quantity -->
                                <td class="px-3 py-3 align-top">
                                    <input type="number" min="1" step="1" max="{{ $detail['available_quantity'] }}"
                                        wire:model.live="details.{{ $index }}.quantity"
                                        placeholder="Qty" @disabled($isEditMode)
                                        class="w-full px-2 py-2 text-xs font-bold text-center rounded border border-blue-300 focus:border-blue-500 bg-blue-50 disabled:bg-gray-100 disabled:text-gray-400 text-blue-700">
                                    @error("details.{$index}.quantity")
                                        <p class="text-red-500 text-[10px] mt-1 text-center">{{ $message }}</p>
                                    @enderror
                                </td>

                                @if (!$isEditMode)
                                    <td class="px-3 py-3 text-center align-top pt-4">
                                        @if (count($details) > 1)
                                            <button type="button" wire:click="removeDetail({{ $index }})"
                                                class="p-1.5 inline-flex items-center justify-center rounded bg-red-50 text-red-500 hover:bg-red-100 transition-colors" title="Hapus Item">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            </button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-gray-400 italic">Klik "Tambah Item" untuk mulai memilih barang...</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 mt-4">
        <a href="{{ route('menu-polres.rack-assignment') }}"  class="w-full sm:w-auto px-8 py-3 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all text-center">Batal</a>
        @if (!$isEditMode)
            <button wire:click="save" type="button"
                class="w-full sm:w-auto px-10 py-3 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl hover:from-blue-700 hover:to-indigo-800 shadow-xl shadow-blue-500/30 transition-all transform hover:scale-105 text-center">
                <span wire:loading.remove wire:target="save">💾 Simpan Penugasan</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        @endif
    </div>

    <script>
        document.addEventListener('lived', () => {
            $('.selectize-dropdown').remove();
        });
    </script>
</div>
