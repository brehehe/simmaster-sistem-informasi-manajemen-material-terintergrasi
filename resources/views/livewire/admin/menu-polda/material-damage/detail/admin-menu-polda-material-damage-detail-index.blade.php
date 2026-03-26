<div>
    <!-- Header -->
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">
                    {{ $isEditMode ? 'Lihat' : 'Tambah' }} Material Rusak
                </h1>
                <p class="text-gray-500 mt-1">Kelola material rusak dengan tracking pengurangan stock (Batch)</p>
            </div>
            <a href="{{ route('menu-polda.material-damage') }}" wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

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

    <!-- Header Info Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Utama</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="code" readonly
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="date" @disabled($materialDamageId)
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:bg-gray-100 disabled:text-gray-500">
                    @error('date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                @if (auth()->user()->hasRole('Admin'))
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Polda <span class="text-red-500">*</span>
                        </label>
                        @if ($canSelectRegionalPolice ?? true)
                            <div wire:ignore wire:key="select-regional-police-{{ rand() }}">
                                <select
                                    id="select-regional-police"
                                    wire:model="regionalPoliceId"
                                    @disabled($materialDamageId)
                                    x-data
                                    x-ref="input"
                                    x-init="
                                        const selectize = $($refs.input).selectize({
                                            dropdownParent: 'body',
                                            allowClear: true,
                                            onChange: function(e) {
                                                @this.set('regionalPoliceId', e ?? '');
                                            }
                                        })[0].selectize;

                                        if ($refs.input.disabled) {
                                            selectize.disable();
                                        }
                                    "
                                >
                                    <option value="">-- Pilih Polda --</option>
                                    @foreach ($regionalPolices as $rp)
                                        <option value="{{ $rp->id }}">{{ $rp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            @php
                                $selectedPolda = collect($regionalPolices)->firstWhere('id', $regionalPoliceId);
                            @endphp
                            <input type="text" value="{{ $selectedPolda?->name ?? '-' }}" readonly
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed">
                        @endif
                        @error('regionalPoliceId')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
                
                <div class="border border-blue-100 bg-blue-50/50 p-4 rounded-xl">
                    <label class="flex items-center gap-2 text-sm font-semibold text-blue-900 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                        </svg>
                        Material Utama <span class="text-red-500">*</span>
                    </label>
                    <div wire:ignore wire:key="select-type-global-{{ rand() }}">
                        <select id="select-type-global" @disabled($materialDamageId) x-data x-ref="input" x-init="
                            const selectize = $($refs.input).selectize({
                                dropdownParent: 'body',
                                allowClear: true,
                                plugins: {{ $materialDamageId ? '[]' : "['clear_button']" }},
                                onChange: function(e) {
                                    @this.set('typeId', e ? e : '');
                                }
                            })[0].selectize;
                            if ($refs.input.disabled) {
                                selectize.disable();
                            }
                        " wire:model="typeId">
                            <option value="">-- Pilih Material --</option>
                            @foreach ($types as $typeOpt)
                                <option value="{{ $typeOpt->id }}"
                                    {{ $typeId == $typeOpt->id ? 'selected' : '' }}>
                                    {{ $typeOpt->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('typeId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Laporan (Opsional)</label>
                <textarea wire:model="description" rows="2" placeholder="Catatan laporan..." @disabled($materialDamageId)
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 disabled:bg-gray-100 disabled:text-gray-500"></textarea>
            </div>
        </div>
    </div>

    <!-- Detail Items Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Detail Material Rusak</h2>
                @if (!$materialDamageId)
                    <button wire:click="addDetail" type="button"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-green-500/30 transition-all duration-300 transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Tambah Item
                    </button>
                @endif
            </div>

            @if (count($details) > 0)
                <div class="overflow-x-auto overflow-y-visible pb-12">
                    <table class="w-full text-sm text-left align-top border-collapse min-w-[1200px]">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
                            <tr>
                                <th class="px-3 py-3 font-semibold w-12 text-center text-xs">No</th>
                                @if($is_type_detail)
                                    <th class="px-3 py-3 font-semibold text-xs min-w-[150px]">Detail Material</th>
                                @endif
                                <th class="px-3 py-3 font-semibold text-xs min-w-[150px]">Service</th>
                                <th class="px-3 py-3 font-semibold text-xs min-w-[150px]">Service Detail</th>
                                @if($is_with_serial_number)
                                    <th class="px-3 py-3 font-semibold text-xs min-w-[250px]">Identitas Barang (Kode | Serial Number)</th>
                                @endif
                                <th class="px-3 py-3 font-semibold text-xs w-32">Kondisi / Alasan</th>
                                <th class="px-3 py-3 font-semibold text-xs w-28 text-center bg-blue-50">Tersedia</th>
                                <th class="px-3 py-3 font-semibold text-xs w-24">Rusak <span class="text-red-500">*</span></th>
                                @if (!$materialDamageId)
                                    <th class="px-3 py-3 font-semibold w-16 text-center text-xs">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($details as $index => $detail)
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
                                <tr wire:key="detail-{{ $index }}" class="hover:bg-gray-50/20 transition-colors">
                                    <td class="px-3 py-3 text-center font-medium text-gray-500 align-top text-xs pt-5">
                                        {{ $index + 1 }}
                                    </td>

                                    @if($is_type_detail)
                                        <td class="px-3 py-3 align-top">
                                            <select wire:model.live="details.{{ $index }}.type_detail_id" @disabled($materialDamageId)
                                                class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-purple-500 disabled:bg-gray-100 disabled:text-gray-500">
                                                <option value="">-- Semua Detail --</option>
                                                @foreach($this->typeDetails as $td)
                                                    <option value="{{ data_get($td, 'id') }}">{{ data_get($td, 'name') }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endif
                                    
                                    <td class="px-3 py-3 align-top">
                                        <select wire:model.live="details.{{ $index }}.service_id" @disabled($materialDamageId)
                                            class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-purple-500 disabled:bg-gray-100 disabled:text-gray-500">
                                            <option value="">-- Semua Service --</option>
                                            @foreach($filteredServices as $svc)
                                                <option value="{{ data_get($svc, 'id') }}">{{ data_get($svc, 'name') }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td class="px-3 py-3 align-top">
                                        <select wire:model.live="details.{{ $index }}.service_detail_id" @disabled($materialDamageId)
                                            class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-purple-500 disabled:bg-gray-100 disabled:text-gray-500">
                                            <option value="">-- Semua --</option>
                                            @foreach($filteredServiceDetails as $sdt)
                                                <option value="{{ data_get($sdt, 'id') }}">{{ data_get($sdt, 'name') }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    @if($is_with_serial_number)
                                        <td class="px-3 py-3 align-top">
                                            <div wire:ignore wire:key="select-stock-key-{{ $index }}-{{ md5(json_encode($detail['available_stocks'] ?? [])) }}">
                                                <select x-data x-init="
                                                    const selectize = $($el).selectize({
                                                        dropdownParent: 'body',
                                                        allowClear: true,
                                                        onChange: function(val) {
                                                            @this.set('details.{{ $index }}.selected_stock_key', val);
                                                        }
                                                    })[0].selectize;
                                                " wire:model="details.{{ $index }}.selected_stock_key" @disabled($materialDamageId)
                                                    class="w-full text-xs rounded border border-gray-300 focus:border-purple-500 disabled:bg-gray-100 disabled:text-gray-500">
                                                    <option value="">-- Pilih Barang --</option>
                                                    @foreach($detail['available_stocks'] ?? [] as $stockOpt)
                                                        <option value="{{ $stockOpt['value'] }}">{{ $stockOpt['label'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                    @endif

                                    <td class="px-3 py-3 align-top w-48">
                                        <div class="space-y-2">
                                            <select wire:model.defer="details.{{ $index }}.damage_type" @disabled($materialDamageId)
                                                class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-purple-500 disabled:bg-gray-100 disabled:text-gray-500">
                                                <option value="damaged">Rusak Fisik</option>
                                                <option value="lost">Hilang / Susut</option>
                                            </select>
                                            <input type="text" wire:model.defer="details.{{ $index }}.reason" placeholder="Penjelasan keluhan" @disabled($materialDamageId) class="w-full px-2 py-2 text-xs rounded border border-gray-300 focus:border-purple-500 disabled:bg-gray-100 disabled:text-gray-400">
                                        </div>
                                    </td>

                                    <td class="px-3 py-3 align-top bg-blue-50/30">
                                        <div class="h-full flex flex-col justify-center items-center font-semibold {{ $detail['available_quantity'] > 0 ? 'text-blue-600' : 'text-gray-400' }} pt-2">
                                            {{ number_format($detail['available_quantity'], 0, ',', '.') }}
                                        </div>
                                    </td>

                                    <td class="px-3 py-3 align-top">
                                        <input type="number" min="0" step="1" max="{{ $detail['available_quantity'] }}" wire:model.live="details.{{ $index }}.quantity" placeholder="Qty" @disabled($materialDamageId) class="w-full px-2 py-2 text-xs font-bold text-center rounded border border-red-300 focus:border-red-500 bg-red-50 disabled:bg-gray-100 disabled:text-gray-400 text-red-700">
                                        @error("details.{$index}.quantity")
                                            <p class="text-red-500 text-[10px] mt-1 text-center font-medium leading-tight">{{ $message }}</p>
                                        @enderror
                                    </td>

                                    @if (!$materialDamageId)
                                        <td class="px-3 py-3 text-center align-top pt-4">
                                            @if (count($details) > 1)
                                                <button type="button" wire:click="removeDetail({{ $index }})"
                                                    class="p-1.5 inline-flex items-center justify-center rounded bg-red-50 text-red-500 hover:bg-red-100 transition-colors" title="Hapus Item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-10 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-gray-500 text-lg font-medium">Belum ada item detail</p>
                    <p class="text-gray-400 text-sm mt-1">Klik "Tambah Item" untuk mengurangi stok yang rusak</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex items-center justify-end gap-3">
        <a href="{{ route('menu-polda.material-damage') }}" wire:navigate
            class="px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200">
            {{ $materialDamageId ? 'Kembali' : 'Batal' }}
        </a>
        @if (!$materialDamageId)
            <button wire:click="save" type="button"
                class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/30 transition-all duration-200">
                <span wire:loading.remove wire:target="save">Simpan Data</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        @endif
    </div>
</div>
