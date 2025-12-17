<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('menu-polda.mutation-stock') }}" wire:navigate
                        class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-blue-600">
                        {{ $isEditMode ? 'Edit Mutasi Stock' : 'Buat Mutasi Stock' }}
                    </h1>
                </div>
                <p class="text-gray-500 ml-14">
                    {{ $isEditMode ? 'Perbarui data mutasi stock' : 'Buat mutasi stock baru antar lokasi' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

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
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50/50">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                Informasi Mutasi
            </h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kode Mutasi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                clip-rule="evenodd" />
                        </svg>
                        Kode Mutasi
                    </label>
                    <input type="text" wire:model="code" readonly
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 font-medium cursor-not-allowed">
                </div>

                <!-- Tanggal Mutasi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd" />
                        </svg>
                        Tanggal Mutasi
                    </label>
                    <input type="date" wire:model="mutation_date"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                    @error('mutation_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SENDER SECTION -->
                @if (Auth::user()->hasRole('Admin'))
                    <div class="md:col-span-2 p-4 rounded-xl bg-orange-50 border border-orange-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M8.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414L11 7.586V3a1 1 0 10-2 0v4.586l-.293-.293z" />
                                <path
                                    d="M3 5a2 2 0 012-2h1a1 1 0 010 2H5v7h2l1 2h4l1-2h2V5h-1a1 1 0 110-2h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z" />
                            </svg>
                            Pengirim / Dari
                        </h3>

                        <!-- Sender Type Toggle -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Lokasi Pengirim</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="sender_type" value="polda"
                                        class="w-4 h-4 text-blue-600">
                                    <span class="text-sm font-medium">Polda</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="sender_type" value="polres"
                                        class="w-4 h-4 text-blue-600">
                                    <span class="text-sm font-medium">Polres</span>
                                </label>
                            </div>
                        </div>

                        <!-- Sender Polda -->
                        @if ($sender_type === 'polda')
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Polda Pengirim</label>
                                <select wire:model.live="sender_regional_police_id"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                    <option value="">Pilih Polda</option>
                                    @foreach ($regionalPolices as $rp)
                                        <option value="{{ $rp->id }}">{{ $rp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <!-- Sender Polres -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Polres Pengirim</label>
                                <select wire:model.live="sender_police_station_id"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                    <option value="">Pilih Polres</option>
                                    @foreach ($policeStations as $ps)
                                        <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- RECEIVER SECTION -->
                <div class="md:col-span-2 p-4 rounded-xl bg-green-50 border border-green-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                            <path
                                d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                        </svg>
                        Penerima / Ke
                    </h3>

                    <!-- Receiver Type Toggle -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Lokasi Penerima</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="receiver_type" value="polda"
                                    class="w-4 h-4 text-blue-600">
                                <span class="text-sm font-medium">Polda</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="receiver_type" value="polres"
                                    class="w-4 h-4 text-blue-600">
                                <span class="text-sm font-medium">Polres</span>
                            </label>
                        </div>
                    </div>

                    <!-- Receiver Polda -->
                    @if ($receiver_type === 'polda')
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Polda Penerima</label>
                            <select wire:model="receiver_regional_police_id"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                <option value="">Pilih Polda</option>
                                @foreach ($regionalPolices as $rp)
                                    <option value="{{ $rp->id }}">{{ $rp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <!-- Receiver Polres -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Polres Penerima</label>
                            <select wire:model="receiver_police_station_id"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                <option value="">Pilih Polres</option>
                                @foreach ($policeStations as $ps)
                                    <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <!-- Catatan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z"
                                clip-rule="evenodd" />
                        </svg>
                        Catatan (Opsional)
                    </label>
                    <textarea wire:model="notes" rows="3"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200"
                        placeholder="Tambahkan catatan mutasi..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Section -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-amber-50/50">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Detail Stock
                </h2>
                <button wire:click="addDetail" type="button"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-semibold py-2 px-4 rounded-xl shadow-lg shadow-green-500/30 transition-all duration-300 transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Tambah Item
                </button>
            </div>
        </div>

        <div class="p-6">
            @if (count($details) > 0)
                <div class="space-y-4">
                    @foreach ($details as $index => $detail)
                        <div
                            class="relative p-6 rounded-2xl bg-gradient-to-br from-white to-gray-50/50 border-2 border-gray-100 shadow-md hover:shadow-lg transition-all duration-300">
                            <!-- Item Number Badge -->
                            <div
                                class="absolute -top-3 -left-3 w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white font-bold flex items-center justify-center shadow-lg">
                                {{ $index + 1 }}
                            </div>

                            <!-- Delete Button -->
                            <button wire:click="removeDetail({{ $index }})" type="button"
                                class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-gradient-to-br from-red-500 to-pink-500 text-white flex items-center justify-center shadow-lg hover:scale-110 transition-transform duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <!-- Stock Selection -->
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                                        </svg>
                                        Pilih Stock
                                    </label>
                                    <select wire:model.live="details.{{ $index }}.stock_detail_id"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                        <option value="">-- Pilih Stock --</option>
                                        @foreach ($stockDetails as $sd)
                                            <option value="{{ $sd->id }}">
                                                {{ $sd->code }} - {{ $sd->type->name ?? '' }} /
                                                {{ $sd->typeDetail->name ?? '' }}
                                                @if ($sd->rack)
                                                    [Rack: {{ $sd->rack->name }}]
                                                @endif
                                                (Qty: {{ $sd->quantity }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("details.{$index}.stock_detail_id")
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- display readonly fields and quantity input similar to material shipment -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-2">Tipe Material</label>
                                    <input type="text"
                                        value="{{ $detail['type_id'] ? \App\Models\Type\Type::find($detail['type_id'])->name ?? '-' : '-' }}"
                                        readonly
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-600 text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-2">Detail Tipe</label>
                                    <input type="text"
                                        value="{{ $detail['type_detail_id'] ? \App\Models\Type\TypeDetail::find($detail['type_detail_id'])->name ?? '-' : '-' }}"
                                        readonly
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-600 text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-2">Kode</label>
                                    <input type="text" value="{{ $detail['code'] ?: '-' }}" readonly
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-600 text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-2">Serial Pertama</label>
                                    <input type="text" value="{{ $detail['number_serial_first'] ?: '-' }}"
                                        readonly
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-600 text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-2">Serial Kedua</label>
                                    <input type="text" value="{{ $detail['number_serial_second'] ?: '-' }}"
                                        readonly
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-600 text-sm">
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Quantity (Max: {{ $detail['available_quantity'] }})
                                    </label>
                                    <input type="number" wire:model.live="details.{{ $index }}.quantity"
                                        min="1" max="{{ $detail['available_quantity'] }}"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                    @error("details.{$index}.quantity")
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror

                                    @if ($detail['stock_detail_id'])
                                        <div class="mt-2 p-2 rounded-lg bg-blue-50 border border-blue-100">
                                            <p class="text-xs text-blue-700">
                                                Sisa Stock: <span
                                                    class="font-bold">{{ $detail['available_quantity'] - ($detail['quantity'] ?? 0) }}</span>
                                                unit
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-600 mb-2">Catatan Item</label>
                                    <input type="text" wire:model="details.{{ $index }}.notes"
                                        placeholder="Catatan tambahan untuk item ini..."
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mx-auto mb-4"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="text-gray-500 text-lg font-medium">Belum ada item</p>
                    <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah Item" untuk menambahkan stock</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
        <a href="{{ route('menu-polda.mutation-stock') }}" wire:navigate
            class="w-full sm:w-auto px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200 text-center">
            Batal
        </a>

        @if (!$isEditMode || ($mutation && $mutation->status === 'draft'))
            <button wire:click="save(false)" type="button"
                class="w-full sm:w-auto px-6 py-3 text-sm font-semibold text-gray-700 bg-gradient-to-r from-gray-200 to-gray-300 rounded-xl hover:from-gray-300 hover:to-gray-400 shadow-lg transition-all duration-200 text-center">
                💾 Simpan Draft
            </button>
            <button wire:click="save(true)" type="button"
                class="w-full sm:w-auto px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/30 transition-all duration-200 transform hover:scale-105 text-center">
                🚀 Kirim Sekarang
            </button>
        @endif
    </div>
</div>
