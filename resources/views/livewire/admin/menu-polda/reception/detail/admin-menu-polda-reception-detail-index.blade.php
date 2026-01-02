<div>
    <!-- Header -->
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">
                    {{ $isEditMode ? 'Edit' : 'Tambah' }} Penerimaan Barang
                </h1>
                <p class="text-gray-500 mt-1">{{ $isEditMode ? 'Perbarui' : 'Buat' }} data Penerimaan Barang</p>
            </div>
            <a href="{{ route('menu-polda.reception') }}" wire:navigate
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <!-- Code (Read-only) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="code" readonly
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tipe <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="type"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white focus:bg-white">
                        <option value="penerimaan">Penerimaan</option>
                        <option value="stock-awal">Stock Awal</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="date"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white focus:bg-white">
                    @error('date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <input type="text" wire:model="name" placeholder="Masukkan deskripsi Penerimaan Barang"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white focus:bg-white">
                </div>

            </div>
            <div class="grid grid-cols-1 gap-6">
                <!-- Regional Police -->
                @if (auth()->user()->hasRole('Admin'))
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Polda <span class="text-red-500">*</span>
                        </label>
                        @if ($canSelectRegionalPolice)
                            <div wire:ignore wire:key="select-regional-police-{{ rand() }}">
                                <select id="select-regional-police" x-data x-ref="input" x-init="$($refs.input).selectize({
                                    dropdownParent: 'body',
                                    allowClear: true,
                                    plugins: ['clear_button'],
                                    onChange: function(e) {
                                        @this.set('regionalPoliceId', e ? e : '');
                                    }
                                });"
                                    wire:model="regionalPoliceId">
                                    <option value="">-- Pilih Polda --</option>
                                    @foreach ($regionalPolices as $rp)
                                        <option value="{{ $rp->id }}"
                                            {{ $regionalPoliceId == $rp->id ? 'selected' : '' }}>{{ $rp->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            @php
                                $selectedPolda = $regionalPolices->firstWhere('id', $regionalPoliceId);
                            @endphp
                            <input type="text" value="{{ $selectedPolda?->name ?? '-' }}" readonly
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed">
                        @endif
                        @error('regionalPoliceId')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- Description -->
                <!-- <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea wire:model="description" rows="3" placeholder="Masukkan deskripsi (opsional)"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white focus:bg-white"></textarea>
                </div> -->

                <!-- Is Active -->
                <!-- <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="is_active" id="is_active"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_active" class="text-sm font-semibold text-gray-700">
                        Aktif
                    </label>
                </div> -->
            </div>
        </div>
    </div>

    <!-- Detail Items Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Detail Item Barang</h2>
                <button wire:click="addDetail" type="button"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-semibold py-2 px-4 rounded-xl shadow-lg shadow-green-500/30 transition-all duration-300 transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Tambah Item
                </button>
            </div>

            @if (count($details) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full" style="min-width: 1400px;">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 50px;">No</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 200px;">Material</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 200px;">Material Detail</th>
                                {{-- <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 180px;">Rack</th> --}}
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 150px;">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 150px;">Serial 1</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 150px;">Serial 2</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 120px;">Qty</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($details as $index => $detail)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div wire:ignore wire:key="select-type-{{ rand() }}">
                                            <select id="select-type" x-data x-ref="input" x-init="$($refs.input).selectize({
                                                dropdownParent: 'body',
                                                allowClear: true,
                                                plugins: ['clear_button'],
                                                onChange: function(e) {
                                                    @this.set('details.{{ $index }}.type_id', e ? e : '');
                                                }
                                            });"
                                                wire:model="details.{{ $index }}.type_id">
                                                <option value="">-- Pilih Polres --</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ $detail['type_id'] == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div wire:ignore
                                            wire:key="select-type-detail-{{ $index }}-{{ $detail['type_id'] ?? 'empty' }}-{{ rand() }}">
                                            <select id="select-type-detail-{{ $index }}" x-data x-ref="input"
                                                x-init="$($refs.input).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    plugins: ['clear_button'],
                                                    onChange: function(e) {
                                                        @this.set('details.{{ $index }}.type_detail_id', e ? e : '');
                                                    }
                                                });"
                                                wire:model="details.{{ $index }}.type_detail_id">
                                                <option value="">-- Pilih --</option>
                                                @if (!empty($detail['type_id']))
                                                    @foreach (\App\Models\Type\TypeDetail::where('type_id', $detail['type_id'])->where('is_active', true)->orderBy('name')->get() as $td)
                                                        <option value="{{ $td->id }}"
                                                            {{ $detail['type_detail_id'] == $td->id ? 'selected' : '' }}>
                                                            {{ $td->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </td>
                                    {{-- <td class="px-4 py-3">
                                        <div wire:ignore
                                            wire:key="select-rack-{{ $index }}-{{ $regionalPoliceId }}-{{ $policeStationId }}-{{ rand() }}">
                                            <select id="select-rack-{{ $index }}" x-data x-ref="input"
                                                x-init="$($refs.input).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    plugins: ['clear_button'],
                                                    onChange: function(e) {
                                                        @this.set('details.{{ $index }}.rack_id', e ? e : '');
                                                    }
                                                });"
                                                wire:model="details.{{ $index }}.rack_id">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($racks as $rack)
                                                    <option value="{{ $rack->id }}"
                                                        {{ $detail['rack_id'] == $rack->id ? 'selected' : '' }}>
                                                        {{ $rack->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td> --}}
                                    <td class="px-4 py-3">
                                        <input type="text" wire:model="details.{{ $index }}.code"
                                            placeholder="Kode barang"
                                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white focus:bg-white">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text"
                                            wire:model="details.{{ $index }}.number_serial_first"
                                            placeholder="Serial pertama"
                                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white focus:bg-white">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text"
                                            wire:model="details.{{ $index }}.number_serial_second"
                                            placeholder="Serial kedua"
                                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white focus:bg-white">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" wire:model="details.{{ $index }}.quantity"
                                            min="0" step="0.01" placeholder="Qty"
                                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white focus:bg-white">
                                        @error('details.' . $index . '.quantity')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="removeDetail({{ $index }})" type="button"
                                            class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                            title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-10 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mx-auto mb-4"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-gray-500 text-lg font-medium">Belum ada item detail</p>
                    <p class="text-gray-400 text-sm mt-1">Klik "Tambah Item" untuk menambahkan detail barang</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex items-center justify-end gap-3">
        <a href="{{ route('menu-polda.reception') }}" wire:navigate
            class="px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200">
            Batal
        </a>
        <button wire:click="save" type="button"
            class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/30 transition-all duration-200">
            <span wire:loading.remove wire:target="save">Simpan Data</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>
    </div>
</div>
