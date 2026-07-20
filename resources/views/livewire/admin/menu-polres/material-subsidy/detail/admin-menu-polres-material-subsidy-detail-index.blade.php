<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('menu-polres.material-subsidy') }}" wire:navigate class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $subsidyId ? 'Edit Draft Subsidi Silang' : 'Input Material Subsidi Silang (Polres)' }}
            </h1>
        </div>
        <p class="text-gray-500 text-sm ml-12">Pencatatan penyerahan material subsidi silang antar unit / instansi dari stok Polres</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2 border-b border-gray-100 pb-3">
            <span class="w-2 h-5 bg-blue-600 rounded-full inline-block"></span>
            Informasi Transaksi Subsidi
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Transaksi <span class="text-red-500">*</span></label>
                <input type="date" wire:model="subsidyDate" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                @error('subsidyDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Penerima / Tujuan Subsidi <span class="text-red-500">*</span></label>
                <input type="text" wire:model="recipientName" placeholder="Contoh: Polres Sampang / Polsek Sepulu..." class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                @error('recipientName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Polres Pengeluar</label>
                <input type="text" value="{{ auth()->user()->policeStation?->name ?? 'Polres' }}" disabled class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 bg-gray-50 text-gray-500 font-semibold cursor-not-allowed">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Keterangan Penerima / Dasar Penyerahan</label>
                <textarea wire:model="recipientDescription" rows="2" placeholder="Contoh: Permohonan subsidi material dari Polres X No. ST/123..." class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan Tambahan (Opsional)</label>
                <textarea wire:model="notes" rows="2" placeholder="Catatan internal transaksi..." class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"></textarea>
            </div>
        </div>
    </div>

    <!-- Items Card -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Daftar Material Disubsidikan
            </h2>
            <button type="button" wire:click="addItem" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-xl shadow transition-all text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Tambah Material
            </button>
        </div>

        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse min-w-[700px]">
                    <thead class="bg-gray-50 text-gray-600 font-semibold text-xs border-b border-gray-200">
                        <tr>
                            <th class="p-3 w-10 text-center">No</th>
                            <th class="p-3 min-w-[180px]">Jenis Material <span class="text-red-500">*</span></th>
                            <th class="p-3 min-w-[180px]">Detail Material</th>
                            <th class="p-3 text-center w-28 bg-blue-50">Stok Ada</th>
                            <th class="p-3 text-center w-28">Jumlah <span class="text-red-500">*</span></th>
                            <th class="p-3 min-w-[150px]">Catatan</th>
                            <th class="p-3 text-center w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($items as $index => $item)
                            @php
                                $typeDetails = $this->getTypeDetailsForItem($index);
                                $availableStock = $this->getAvailableStockForItem($index);
                            @endphp
                            <tr wire:key="item-row-{{ $index }}" class="hover:bg-gray-50/50">
                                <td class="p-3 text-center text-xs text-gray-400 pt-4">{{ $index + 1 }}</td>
                                <td class="p-3">
                                    <select wire:model.live="items.{{ $index }}.type_id" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 focus:border-blue-500">
                                        <option value="">-- Pilih Jenis Material --</option>
                                        @foreach($types as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                    @error("items.{$index}.type_id") <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                </td>
                                <td class="p-3">
                                    <select wire:model.live="items.{{ $index }}.type_detail_id" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 focus:border-blue-500">
                                        <option value="">-- Semua Detail --</option>
                                        @foreach($typeDetails as $td)
                                            <option value="{{ $td['id'] }}">{{ $td['name'] }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-3 text-center bg-blue-50/40">
                                    <span class="font-bold text-xs {{ $availableStock > 0 ? 'text-blue-700' : 'text-gray-400' }}">
                                        {{ number_format($availableStock, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <input type="number" min="1" max="{{ max(1, $availableStock) }}" wire:model.live="items.{{ $index }}.quantity" class="w-full px-3 py-2 text-xs font-bold text-center rounded-lg border border-blue-300 bg-blue-50 text-blue-700">
                                    @error("items.{$index}.quantity") <p class="text-red-500 text-[10px] mt-1 text-center">{{ $message }}</p> @enderror
                                </td>
                                <td class="p-3">
                                    <input type="text" wire:model.defer="items.{{ $index }}.notes" placeholder="Catatan item..." class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200">
                                </td>
                                <td class="p-3 text-center pt-4">
                                    @if(count($items) > 1)
                                        <button type="button" wire:click="removeItem({{ $index }})" class="p-1 text-red-500 hover:bg-red-50 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('menu-polres.material-subsidy') }}" wire:navigate class="px-6 py-2.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl">Batal</a>
        <button type="button" wire:click="save" class="px-8 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-xl shadow-lg shadow-blue-500/25 transition-all">
            💾 Simpan Draft Subsidi Silang
        </button>
    </div>
</div>
