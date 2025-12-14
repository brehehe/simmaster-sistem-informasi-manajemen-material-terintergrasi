<div>
    <!-- Header -->
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">
                    {{ $isEditMode ? 'Edit' : 'Tambah' }} Rack Assignment
                </h1>
                <p class="text-gray-500 mt-1">Pindahkan material ke/antar rak (Batch)</p>
            </div>
            <a href="{{ route('menu-polda.rack-assignment') }}" wire:navigate
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
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
                    <input type="date" wire:model="date"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    @error('date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if (auth()->user()->hasRole('Admin'))
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Polda <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="regionalPoliceId"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            <option value="">-- Pilih Polda --</option>
                            @foreach ($regionalPolices as $rp)
                                <option value="{{ $rp->id }}">{{ $rp->name }}</option>
                            @endforeach
                        </select>
                        @error('regionalPoliceId')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi (Opsional)</label>
                <textarea wire:model="description" rows="2" placeholder="Catatan tambahan..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"></textarea>
            </div>
        </div>
    </div>

    <!-- Detail Items Table -->
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
                    <table class="w-full" style="min-width: 1600px;">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 50px;">No</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 250px;">Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 150px;">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 130px;">Serial 1</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 130px;">Serial 2</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 150px;">From Rack</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 180px;">To Rack</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 100px;">Qty</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase"
                                    style="min-width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($details as $index => $detail)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <select wire:model.live="details.{{ $index }}.stock_detail_id"
                                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                            <option value="">-- Pilih Stock --</option>
                                            @foreach ($stockDetails as $stock)
                                                <option value="{{ $stock->id }}">
                                                    {{ $stock->type->name ?? '' }} - {{ $stock->typeDetail->name ?? '' }} {{ $stock->rack->name ?? 'Tanpa Rak' }} {{ $stock->code }}
                                                    ({{ $stock->quantity }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("details.{$index}.stock_detail_id")
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" wire:model="details.{{ $index }}.item_code"
                                            readonly
                                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text"
                                            wire:model="details.{{ $index }}.number_serial_first" readonly
                                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text"
                                            wire:model="details.{{ $index }}.number_serial_second" readonly
                                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text"
                                            value="{{ $detail['from_rack_id'] ? $racks->firstWhere('id', $detail['from_rack_id'])->name ?? 'Tanpa Rak' : 'Tanpa Rak' }}"
                                            readonly
                                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-600">
                                    </td>
                                    <td class="px-4 py-3">
                                        <select wire:model="details.{{ $index }}.to_rack_id"
                                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                            <option value="">-- Tanpa Rak --</option>
                                            @foreach ($racks as $rack)
                                                <option value="{{ $rack->id }}">{{ $rack->name }}</option>
                                            @endforeach
                                        </select>
                                        @error("details.{$index}.to_rack_id")
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" wire:model="details.{{ $index }}.quantity"
                                            max="{{ $details[$index]['available_quantity'] ?? 0 }}"
                                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                        <small class="text-xs text-gray-500">Max:
                                            {{ $details[$index]['available_quantity'] ?? 0 }}</small>
                                        @error("details.{$index}.quantity")
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
        <a href="{{ route('menu-polda.rack-assignment') }}" wire:navigate
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
