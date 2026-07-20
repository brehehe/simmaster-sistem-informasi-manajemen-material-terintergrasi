<div>
    <!-- Header -->
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">
                    {{ $isEditMode ? 'Edit' : 'Tambah' }} Stock Awal
                </h1>
                <p class="text-gray-500 mt-1">{{ $isEditMode ? 'Perbarui' : 'Buat' }} data Stock Awal</p>
            </div>
            <a href="{{ route('menu-polres.last-stock') }}" 
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

    <!-- Flash Messages -->
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

    <!-- Main Form Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Utama</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <!-- Code (Read-only) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="code" readonly
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed">
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="date" @disabled($lastStockId)
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white focus:bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                    @error('date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Material Master Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Material <span class="text-red-500">*</span>
                    </label>
                    <div wire:ignore wire:key="select-type-master-{{ rand() }}">
                        <select id="select-type-master" @disabled($lastStockId) x-data x-ref="input" x-init="
                            const selectize = $($refs.input).selectize({
                                dropdownParent: 'body',
                                allowClear: true,
                                plugins: {{ $lastStockId ? '[]' : "['clear_button']" }},
                                onChange: function(e) {
                                    @this.set('typeId', e ? e : '');
                                }
                            })[0].selectize;
                            if ($refs.input.disabled) { selectize.disable(); }
                        "
                            wire:model="typeId">
                            <option value="">-- Pilih Material --</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}"
                                    {{ $typeId == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('typeId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Police Station (Optional for Admin, auto for Polres) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Polres
                    </label>
                    @if ($canSelectPoliceStation)
                        <div wire:ignore wire:key="select-police-station-{{ rand() }}">
                            <select id="select-police-station" @disabled($lastStockId) x-data x-ref="input" x-init="
                                const selectize = $($refs.input).selectize({
                                    dropdownParent: 'body',
                                    allowClear: true,
                                    plugins: {{ $lastStockId ? '[]' : "['clear_button']" }},
                                    onChange: function(e) {
                                        @this.set('policeStationId', e ? e : '');
                                    }
                                })[0].selectize;
                                if ($refs.input.disabled) { selectize.disable(); }
                            "
                                wire:model="policeStationId">
                                <option value="">-- Pilih Polres --</option>
                                @foreach ($policeStations as $ps)
                                    <option value="{{ $ps->id }}"
                                        {{ $policeStationId == $ps->id ? 'selected' : '' }}>{{ $ps->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        @php
                            $selectedPolres = $policeStations->firstWhere('id', $policeStationId);
                        @endphp
                        <input type="text" value="{{ $selectedPolres?->name ?? '-' }}" readonly
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed">
                    @endif
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea wire:model="description" @disabled($lastStockId) rows="1" placeholder="Masukkan deskripsi (opsional)"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white focus:bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Items Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-visible mb-20">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Perincian Item</h2>
                @if (!$lastStockId && $typeId)
                    <button wire:click="addDetail" type="button"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-semibold py-2 px-4 rounded-xl shadow-lg shadow-green-500/30 transition-all duration-300 transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Tambah Baris
                    </button>
                @endif
            </div>

            @if ($typeId)
                <div class="overflow-x-auto overflow-y-visible min-h-[400px]">
                    <table class="w-full text-sm text-left border-separate border-spacing-y-2">
                        <thead class="text-xs uppercase text-gray-400 font-bold">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3 min-w-[200px]">Detail Material</th>
                                <th class="px-4 py-3 min-w-[200px]">Servis</th>
                                <th class="px-4 py-3 min-w-[200px]">Detail Servis</th>
                                <th class="px-4 py-3 min-w-[200px]">Rak</th>
                                @if ($is_with_serial_number)
                                    <th class="px-4 py-3 min-w-[150px]">Kode</th>
                                    <th class="px-4 py-3 min-w-[150px]">SN 1</th>
                                    <th class="px-4 py-3 min-w-[150px]">SN 2</th>
                                @endif
                                <th class="px-4 py-3 min-w-[120px]">Qty <span class="text-red-500">*</span></th>
                                @if (!$lastStockId)
                                    <th class="px-4 py-3 w-[50px]">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="overflow-visible">
                            @foreach ($details as $index => $detail)
                                <tr wire:key="row-{{ $index }}" class="bg-gray-50/50 hover:bg-gray-100/50 transition-colors duration-200">
                                    <td class="px-4 py-3 font-bold text-gray-500 rounded-l-xl">{{ $index + 1 }}</td>
                                    
                                    <!-- Detail Material -->
                                    <td class="px-4 py-3 overflow-visible">
                                        <div wire:ignore wire:key="td-{{ $index }}-{{ $typeId }}-{{ rand() }}">
                                            <select @disabled($lastStockId) class="selectize-td" x-data x-ref="input" x-init="
                                                $($refs.input).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    onChange: function(e) {
                                                        @this.set('details.{{ $index }}.type_detail_id', e ? e : '');
                                                    }
                                                });
                                            " wire:model="details.{{ $index }}.type_detail_id">
                                                <option value="">-- Semua --</option>
                                                @foreach ($typeDetails as $td)
                                                    <option value="{{ $td->id }}">{{ $td->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>

                                    <!-- Service -->
                                    <td class="px-4 py-3 overflow-visible">
                                        <div wire:ignore wire:key="svc-{{ $index }}-{{ $typeId }}-{{ rand() }}">
                                            <select @disabled($lastStockId) class="selectize-svc" x-data x-ref="input" x-init="
                                                $($refs.input).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    onChange: function(e) {
                                                        @this.set('details.{{ $index }}.service_id', e ? e : '');
                                                    }
                                                });
                                            " wire:model="details.{{ $index }}.service_id">
                                                <option value="">-- Semua --</option>
                                                @foreach ($services as $svc)
                                                    <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>

                                    <!-- Service Detail -->
                                    <td class="px-4 py-3 overflow-visible">
                                        <div wire:ignore wire:key="svcd-{{ $index }}-{{ $detail['service_id'] ?? 'none' }}-{{ rand() }}">
                                            <select @disabled($lastStockId) class="selectize-svcd" x-data x-ref="input" x-init="
                                                $($refs.input).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    onChange: function(e) {
                                                        @this.set('details.{{ $index }}.service_detail_id', e ? e : '');
                                                    }
                                                });
                                            " wire:model="details.{{ $index }}.service_detail_id">
                                                <option value="">-- Semua --</option>
                                                @if (!empty($detail['service_id']))
                                                    @foreach (\App\Models\Service\ServiceDetail::where('service_id', $detail['service_id'])->where('is_active', true)->orderBy('name')->get() as $sd)
                                                        <option value="{{ $sd->id }}">{{ $sd->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </td>

                                    <!-- Rack -->
                                    <td class="px-4 py-3 overflow-visible">
                                        <div wire:ignore wire:key="rack-{{ $index }}-{{ $policeStationId ?? 'none' }}-{{ rand() }}">
                                            <select @disabled($lastStockId) class="selectize-rack" x-data x-ref="input" x-init="
                                                $($refs.input).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    onChange: function(e) {
                                                        @this.set('details.{{ $index }}.rack_id', e ? e : '');
                                                    }
                                                });
                                            " wire:model="details.{{ $index }}.rack_id">
                                                <option value="">-- Tanpa Rak --</option>
                                                @foreach ($racks as $rack)
                                                    <option value="{{ $rack->id }}">{{ $rack->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>

                                    @if ($is_with_serial_number)
                                        <td class="px-4 py-3">
                                            <input type="text" wire:model.blur="details.{{ $index }}.code" @disabled($lastStockId)
                                                class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white" placeholder="Kode">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" wire:model.blur="details.{{ $index }}.number_serial_first" @disabled($lastStockId)
                                                class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white" placeholder="SN1">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" wire:model.blur="details.{{ $index }}.number_serial_second" @disabled($lastStockId)
                                                class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white" placeholder="SN2">
                                        </td>
                                    @endif

                                    <td class="px-4 py-3">
                                        <input type="number" wire:model.blur="details.{{ $index }}.quantity" @disabled($lastStockId) step="0.01"
                                            class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white" placeholder="0">
                                    </td>

                                    @if (!$lastStockId)
                                        <td class="px-4 py-3 rounded-r-xl">
                                            <button type="button" wire:click="removeDetail({{ $index }})"
                                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-gray-500 text-lg font-medium">Pilih Master Material terlebih dahulu</p>
                    <p class="text-gray-400 text-sm mt-1">Gunakan dropdown Material di bagian Informasi Utama</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-md border-t border-gray-100 p-4 z-20 shadow-[0_-10px_20px_rgba(0,0,0,0.05)]">
        <div class="max-w-7xl mx-auto flex items-center justify-end gap-3 px-4">
            <a href="{{ route('menu-polres.last-stock') }}" 
                class="px-6 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                Batal
            </a>
            @if (!$lastStockId)
                <button wire:click="save" type="button"
                    class="px-8 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/30 transition-all duration-200">
                    <span wire:loading.remove wire:target="save">Simpan Data Stock Awal</span>
                    <span wire:loading wire:target="save">Memproses...</span>
                </button>
            @endif
        </div>
    </div>
</div>
