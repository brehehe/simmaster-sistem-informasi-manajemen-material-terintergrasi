<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="fixed top-5 right-5 z-[9999] flex items-center gap-3 rounded-xl bg-emerald-500 px-5 py-4 text-white shadow-2xl">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed top-5 right-5 z-[9999] flex items-center gap-3 rounded-xl bg-red-500 px-5 py-4 text-white shadow-2xl">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('menu-polda.material-subsidy') }}"
            class="p-2 rounded-xl text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-blue-900">
                {{ $subsidyId ? 'Edit Subsidi Material' : 'Tambah Subsidi Material' }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">Data akan disimpan sebagai draft. Konfirmasi di halaman daftar untuk memproses stok.</p>
        </div>
    </div>

    <!-- Form -->
    <form wire:submit="save">
        <div class="space-y-5">
            <!-- Header Card -->
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
                <h2 class="text-base font-bold text-gray-800 mb-5">Informasi Subsidi</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Tanggal Subsidi <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model="subsidyDate"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('subsidyDate') border-red-400 @enderror">
                        @error('subsidyDate')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Polda (Admin only) -->
                    @if(Auth::user()->hasRole('Admin'))
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Polda</label>
                            <select wire:model="regionalPoliceId"
                                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                <option value="">-- Pilih Polda --</option>
                                @foreach($regionalPolices as $rp)
                                    <option value="{{ $rp->id }}">{{ $rp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Nama Penerima -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nama Penerima <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="recipientName"
                            placeholder="Contoh: Satgas XYZ, Instansi ABC"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('recipientName') border-red-400 @enderror">
                        @error('recipientName')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keterangan Penerima -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Keterangan Penerima</label>
                        <input type="text" wire:model="recipientDescription"
                            placeholder="Alamat, jabatan, atau informasi tambahan"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <!-- Catatan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan</label>
                        <textarea wire:model="notes" rows="2"
                            placeholder="Catatan tambahan (opsional)"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 resize-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Items Card -->
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-bold text-gray-800">Item Material</h2>
                    <button type="button" wire:click="addItem"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-600 hover:bg-blue-100 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Item
                    </button>
                </div>

                @error('items')
                    <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600">
                        {{ $message }}
                    </div>
                @enderror

                <div class="space-y-4">
                    @foreach($items as $index => $item)
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                            <div class="flex items-start justify-between mb-3">
                                <span class="text-xs font-bold text-gray-400 uppercase">Item #{{ $index + 1 }}</span>
                                @if(count($items) > 1)
                                    <button type="button" wire:click="removeItem({{ $index }})"
                                        class="text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Type -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">
                                        Jenis Material <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="items.{{ $index }}.type_id"
                                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none @error('items.'.$index.'.type_id') border-red-400 @enderror">
                                        <option value="">-- Pilih Material --</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('items.'.$index.'.type_id')
                                        <p class="mt-0.5 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Type Detail -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Detail Material</label>
                                    <select wire:model="items.{{ $index }}.type_detail_id"
                                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                                        <option value="">-- Semua Detail --</option>
                                        @foreach($typeDetails[$index] ?? [] as $td)
                                            <option value="{{ $td['id'] }}">{{ $td['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">
                                        Jumlah <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" wire:model="items.{{ $index }}.quantity"
                                        min="1"
                                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none @error('items.'.$index.'.quantity') border-red-400 @enderror">
                                    @error('items.'.$index.'.quantity')
                                        <p class="mt-0.5 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('menu-polda.material-subsidy') }}"
                    class="rounded-xl border border-gray-200 px-6 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" wire:loading.attr="disabled"
                    class="rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 transition-all">
                    <span wire:loading.remove wire:target="save">
                        <svg class="inline h-4 w-4 mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan sebagai Draft
                    </span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </form>
</div>
