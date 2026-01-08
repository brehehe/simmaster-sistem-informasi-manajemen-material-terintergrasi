<div>
    <!-- Header -->
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">
                    {{ $isEditMode ? 'Edit' : 'Tambah' }} Material Usage
                </h1>
                <p class="text-gray-500 mt-1">Kelola penggunaan material dengan tracking pengurangan stock (Batch)</p>
            </div>
            <a href="{{ route('menu-polres.material-usage') }}" wire:navigate
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

            <div class="grid grid-cols-1 md:grid-cols-{{ auth()->user()->hasRole('Admin') ? 3 : 2 }} gap-6 mb-4">
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
                    <input type="date" wire:model="date" {{ $isEditMode ? 'disabled' : '' }}
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 {{ $isEditMode ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                    @error('date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if (auth()->user()->hasRole('Admin'))
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Polres <span class="text-red-500">*</span>
                        </label>
                        <div wire:ignore wire:key="select-police-station-{{ rand() }}">
                            <select
                                id="select-police-station"
                                wire:model="policeStationId"
                                {{ $isEditMode ? 'disabled' : '' }}
                                x-data
                                x-ref="input"
                                x-init="
                                    const selectize = $($refs.input).selectize({
                                        dropdownParent: 'body',
                                        allowClear: true,
                                        onChange: function(e) {
                                            @this.set('policeStationId', e ?? '');
                                        }
                                    })[0].selectize;

                                    if ($refs.input.disabled) {
                                        selectize.disable();
                                    }
                                "
                            >
                                <option value="">-- Pilih Polres --</option>
                                @foreach ($policeStations as $ps)
                                    <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('policeStationId')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi (Opsional)</label>
                <textarea wire:model="description" rows="2" placeholder="Catatan tambahan..." {{ $isEditMode ? 'disabled' : '' }}
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 {{ $isEditMode ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}"></textarea>
            </div>
        </div>
    </div>

    <!-- Detail Items Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Detail Item Barang</h2>
                @if(!$isEditMode)
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
                            class="relative bg-gradient-to-br from-white to-gray-50/50 rounded-2xl border border-gray-200/80 shadow-lg shadow-gray-200/50 hover:shadow-xl hover:shadow-gray-300/50 transition-all duration-300 overflow-hidden">
                            <!-- Item Number Badge -->
                            <div
                                class="absolute top-0 left-0 bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-4 py-2 rounded-br-2xl font-bold text-sm shadow-lg">
                                Item #{{ $index + 1 }}
                            </div>

                            <div class="p-6 pt-14">
                                <!-- Remove Button -->
                                @if (!$isEditMode && count($details) > 1)
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
                                    <!-- Stock Selection -->
                                    <div class="md:col-span-2">
                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                                            </svg>
                                            Pilih Stock <span class="text-red-500">*</span>
                                        </label>
                                        <div wire:ignore wire:key="select-stock-detail-{{ rand() }}">
                                            <select
                                                id="select-stock-detail"
                                                wire:model="details.{{ $index }}.stock_detail_id"
                                                {{ $isEditMode ? 'disabled' : '' }}
                                                x-data
                                                x-ref="input"
                                                x-init="
                                                    const selectize = $($refs.input).selectize({
                                                        dropdownParent: 'body',
                                                        allowClear: true,
                                                        onChange: function(e) {
                                                            @this.set('details.{{ $index }}.stock_detail_id', e ?? '');
                                                        }
                                                    })[0].selectize;

                                                    if ($refs.input.disabled) {
                                                        selectize.disable();
                                                    }
                                                "
                                            >
                                                <option value="">-- Pilih Stock Detail --</option>
                                                @foreach ($stockDetails as $sd)
                                                    <option value="{{ $sd->id }}">{{ $sd->type->name ?? '' }} -
                                                    {{ $sd->typeDetail->name ?? '' }}
                                                    {{ $sd->rack->name ?? 'Tanpa Rak' }} @if($sd->code && $sd->number_serial_first && $sd->number_serial_second) {{ $sd->code }} {{$sd->number_serial_first}} - {{$sd->number_serial_second}} @endif
                                                    (Tersedia: {{ $sd->quantity }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error("details.{$index}.stock_detail_id")
                                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Quantity -->
                                    <div class="md:col-span-2">
                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-600"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z" />
                                            </svg>
                                            Quantity <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" wire:model="details.{{ $index }}.quantity"
                                            max="{{ $details[$index]['available_quantity'] ?? 0 }}" min="1" {{ $isEditMode ? 'disabled' : '' }}
                                            class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 {{ $isEditMode ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                        <div class="flex items-center justify-between mt-1">
                                            <small class="text-xs text-gray-500">Max:
                                                {{ $details[$index]['available_quantity'] ?? 0 }}</small>
                                            @if (!empty($details[$index]['quantity']) && $details[$index]['quantity'] > 0)
                                                <small class="text-xs font-semibold text-blue-600">
                                                    Sisa:
                                                    {{ max(0, ($details[$index]['available_quantity'] ?? 0) - ($details[$index]['quantity'] ?? 0)) }}
                                                </small>
                                            @endif
                                        </div>
                                        @error("details.{$index}.quantity")
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Stock Info Panel -->
                                @if (!empty($details[$index]['stock_detail_id']))
                                    <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200/50 rounded-xl">
                                        <div class="flex items-start gap-3">
                                            <div class="p-2 bg-blue-100 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-sm font-bold text-gray-900 mb-2">Informasi Stock</h4>
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                                                    <div>
                                                        <span class="text-gray-500">Kode:</span>
                                                        <p class="font-semibold text-gray-900">
                                                            {{ $details[$index]['item_code'] }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Serial 1:</span>
                                                        <p class="font-semibold text-gray-900">
                                                            {{ $details[$index]['number_serial_first'] ?: '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Serial 2:</span>
                                                        <p class="font-semibold text-gray-900">
                                                            {{ $details[$index]['number_serial_second'] ?: '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Tersedia:</span>
                                                        <p class="font-bold text-blue-600">
                                                            {{ number_format($details[$index]['available_quantity'], 0) }}
                                                            unit</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Service Breakdown -->
                                @if ($detail['type_id'] && $this->hasServices($detail['type_id']))
                                    <div class="mt-6 p-4 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200/50 rounded-xl">
                                        <div class="flex items-start gap-3">
                                            <div class="p-2 bg-purple-100 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-sm font-bold text-gray-900 mb-3">Breakdown Service/Service Detail</h4>
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-3">
                                                    @foreach ($this->getServicesForType($detail['type_id']) as $service)
                                                        @if ($service->details_count > 0)
                                                            {{-- Service with Details --}}
                                                            <div class="bg-white p-3 rounded-lg border border-purple-200">
                                                                <h5 class="text-sm font-semibold text-gray-800 mb-2">{{ $service->name }}</h5>
                                                                @foreach ($service->details as $serviceDetail)
                                                                    <div class="mb-2 last:mb-0">
                                                                        <label class="block text-xs text-gray-600 mb-1">{{ $serviceDetail->name }}</label>
                                                                        <input type="number" min="0" step="0.01"
                                                                            wire:model="details.{{ $index }}.service_items.{{ $service->id }}.{{ $serviceDetail->id }}.quantity"
                                                                            placeholder="Qty" {{ $isEditMode ? 'disabled' : '' }}
                                                                            class="w-full px-2 py-1 text-sm rounded border border-gray-300 focus:border-purple-500 focus:ring-1 focus:ring-purple-500/20 {{ $isEditMode ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            {{-- Service without Details --}}
                                                            <div class="bg-white p-3 rounded-lg border border-purple-200">
                                                                <label class="block text-sm font-semibold text-gray-800 mb-1">{{ $service->name }}</label>
                                                                <input type="number" min="0" step="0.01"
                                                                    wire:model="details.{{ $index }}.service_items.{{ $service->id }}.quantity"
                                                                    placeholder="Qty" {{ $isEditMode ? 'disabled' : '' }}
                                                                    class="w-full px-2 py-1 text-sm rounded border border-gray-300 focus:border-purple-500 focus:ring-1 focus:ring-purple-500/20 {{ $isEditMode ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
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
        <a href="{{ route('menu-polres.material-usage') }}" wire:navigate
            class="px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200">
            Batal
        </a>
                @if(!$isEditMode)

        <button wire:click="save" type="button" {{ $isEditMode ? 'disabled' : '' }}
            class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/30 transition-all duration-200 {{ $isEditMode ? 'opacity-50 cursor-not-allowed' : '' }}">
            <span wire:loading.remove wire:target="save">Simpan Data</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>
        @endif
    </div>
</div>
