@if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" wire:click="closeModal"></div>

        <!-- Modal Content -->
        <div class="relative w-full max-w-lg bg-white shadow-2xl rounded-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white">
                            {{ $isEditMode ? 'Edit Target Ditlantas' : 'Tambah Target Baru' }}
                        </h3>
                        <p class="text-xs text-blue-100 mt-0.5">
                            {{ $isEditMode ? 'Perbarui informasi pagu target atau tahun anggaran' : 'Buat data target pagu tahunan baru' }}
                        </p>
                    </div>
                    <button wire:loading.attr="disabled" wire:click="closeModal" class="text-white/80 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <form wire:submit="save" class="p-6 space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Nama Target <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="name"
                        class="w-full px-3.5 py-2.5 text-sm rounded-xl border {{ $errors->has('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-500/20' }} focus:ring-2 transition-all bg-gray-50 focus:bg-white"
                        placeholder="Contoh: Target Ditlantas 2026">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Tahun Anggaran <span class="text-red-500">*</span>
                    </label>
                    <input type="number" wire:model="year" min="2000" max="2100"
                        class="w-full px-3.5 py-2.5 text-sm rounded-xl border {{ $errors->has('year') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-500/20' }} focus:ring-2 transition-all bg-gray-50 focus:bg-white"
                        placeholder="2026">
                    @error('year')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Deskripsi
                    </label>
                    <textarea wire:model="description" rows="3"
                        class="w-full px-3.5 py-2.5 text-sm rounded-xl border {{ $errors->has('description') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-500/20' }} focus:ring-2 transition-all bg-gray-50 focus:bg-white resize-none"
                        placeholder="Deskripsi target atau keterangan pelengkap (opsional)"></textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                    <div>
                        <span class="text-sm font-semibold text-gray-900 block">Status Aktif</span>
                        <span class="text-xs text-gray-500">Target yang aktif akan menjadi rujukan utama sistem</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Footer Buttons -->
                <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="button" wire:loading.attr="disabled" wire:click="closeModal"
                        class="px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl hover:from-blue-700 hover:to-cyan-600 shadow-md shadow-blue-500/20 transition-all active:scale-95">
                        <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span>{{ $isEditMode ? 'Simpan Perubahan' : 'Tambah Target' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
