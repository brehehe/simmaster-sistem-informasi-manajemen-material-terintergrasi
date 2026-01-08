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

                    <select
                        wire:model="type"
                        @disabled($receptionId)
                        class="w-full px-3 py-2 text-sm rounded-lg
                            border border-gray-200
                            bg-white text-gray-700
                            focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                            transition-all duration-200

                            disabled:bg-gray-100
                            disabled:text-gray-500
                            disabled:border-gray-300
                            disabled:cursor-not-allowed"
                    >
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
                    <input type="date" wire:model="date" @disabled($receptionId)
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white focus:bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:border-gray-300 disabled:cursor-not-allowed">
                    @error('date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <input type="text" wire:model="name" @disabled($receptionId) placeholder="Masukkan deskripsi Penerimaan Barang"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white focus:bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:border-gray-300 disabled:cursor-not-allowed">
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
                                <select
                                    id="select-regional-police"
                                    wire:model="regionalPoliceId"
                                    @disabled($receptionId)
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
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Detail Item Barang</h2>
                @if (!$receptionId)
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
                <div class="space-y-4">
                    @foreach ($details as $index => $detail)
                        <div wire:key="detail-{{ $index }}"
                            class="relative bg-gradient-to-br from-white to-gray-50/50 rounded-2xl border border-gray-200/80 shadow-lg shadow-gray-200/50 hover:shadow-xl hover:shadow-gray-300/50 transition-all duration-300 overflow-visible">
                            <!-- Item Number Badge -->
                            <div
                                class="absolute top-0 left-0 bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-4 py-2 rounded-br-2xl font-bold text-sm shadow-lg z-10">
                                Item #{{ $index + 1 }}
                            </div>

                            <div class="p-6 pt-14">
                                <!-- Remove Button -->
                                @if (!$receptionId && count($details) > 1)
                                    <button type="button" wire:click="removeDetail({{ $index }})"
                                        class="absolute top-4 right-4 p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:scale-110 transition-all duration-200"
                                        title="Hapus Item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                @endif

                                <!-- Form Fields Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Material Type Selection -->
                                    <div class="md:col-span-2">
                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                            </svg>
                                            Material <span class="text-red-500">*</span>
                                        </label>
                                        <div wire:ignore wire:key="select-type-{{ rand() }}">
                                            <select id="select-type" @disabled($receptionId) x-data x-ref="input" x-init="
                                                const selectize = $($refs.input).selectize({
                                                    dropdownParent: 'body',
                                                    allowClear: true,
                                                    plugins: {{ $receptionId ? '[]' : "['clear_button']" }},
                                                    onChange: function(e) {
                                                        @this.set('details.{{ $index }}.type_id', e ? e : '');
                                                    }
                                                })[0].selectize;
                                                if ($refs.input.disabled) {
                                                    selectize.disable();
                                                }
                                            "
                                                wire:model="details.{{ $index }}.type_id">
                                                <option value="">-- Pilih Material --</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ $detail['type_id'] == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Type Detail (Conditional) -->
                                    @if ($detail['is_type_detail'])
                                        <div class="md:col-span-2">
                                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                                Detail Material <span class="text-red-500">*</span>
                                            </label>
                                            <div wire:ignore
                                                wire:key="select-type-detail-{{ $index }}-{{ $detail['type_id'] ?? 'empty' }}-{{ rand() }}">
                                                <select id="select-type-detail-{{ $index }}" @disabled($receptionId) x-data
                                                    x-ref="input" x-init="
                                                    const selectize = $($refs.input).selectize({
                                                        dropdownParent: 'body',
                                                        allowClear: true,
                                                        plugins: {{ $receptionId ? '[]' : "['clear_button']" }},
                                                        onChange: function(e) {
                                                            @this.set('details.{{ $index }}.type_detail_id', e ? e : '');
                                                        }
                                                    })[0].selectize;
                                                    if ($refs.input.disabled) {
                                                        selectize.disable();
                                                    }
                                                "
                                                    wire:model="details.{{ $index }}.type_detail_id">
                                                    <option value="">-- Pilih Detail --</option>
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
                                        </div>
                                    @endif

                                    <!-- Serial Number Fields -->
                                    @if ($detail['is_with_serial_number'])
                                        <div wire:key="code-{{ $index }}">
                                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                                Kode Barang
                                            </label>
                                            <input type="text" wire:model="details.{{ $index }}.code"
                                                @disabled($receptionId) placeholder="Kode barang"
                                                class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white focus:bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                                        </div>
                                        <div wire:key="serial-1-{{ $index }}">
                                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                                Serial Number 1
                                            </label>
                                            <input type="text"
                                                wire:model="details.{{ $index }}.number_serial_first"
                                                @disabled($receptionId) placeholder="Serial pertama"
                                                class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white focus:bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                                        </div>
                                        <div wire:key="serial-2-{{ $index }}">
                                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                                Serial Number 2
                                            </label>
                                            <input type="text"
                                                wire:model="details.{{ $index }}.number_serial_second"
                                                @disabled($receptionId) placeholder="Serial kedua"
                                                class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white focus:bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                                        </div>
                                    @endif

                                    <!-- Quantity -->
                                    <div class="{{ $detail['is_with_serial_number'] ? '' : 'md:col-span-2' }}">
                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-600"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z" />
                                            </svg>
                                            Quantity <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" wire:model="details.{{ $index }}.quantity"
                                            @disabled($receptionId) min="0" step="0.01" placeholder="Qty"
                                            class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white focus:bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                                        @error('details.' . $index . '.quantity')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-10 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
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
            {{ $receptionId ? 'Kembali' : 'Batal' }}
        </a>
        @if (!$receptionId)
            <button wire:click="save" type="button"
                class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/30 transition-all duration-200">
                <span wire:loading.remove wire:target="save">Simpan Data</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        @endif
    </div>
</div>
