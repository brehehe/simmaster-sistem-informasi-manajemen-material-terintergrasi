<!-- Modal Create/Edit -->
@if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" wire:click="closeModal"></div>
        <div class="relative w-full max-w-lg bg-white shadow-2xl rounded-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ $isEditMode ? 'Edit' : 'Tambah' }} Service Detail
                        </h2>
                    </div>
                </div>
            </div>

            <div class="p-6 overflow-y-auto">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Nama Detail <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="name"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-gray-50 focus:bg-white"
                            placeholder="Masukkan nama detail service">
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Harga <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input type="number" wire:model="price"
                                class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-gray-50 focus:bg-white"
                                placeholder="0">
                        </div>
                        @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Deskripsi (Opsional)
                        </label>
                        <textarea wire:model="description" rows="3"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-gray-50 focus:bg-white"
                            placeholder="Masukkan deskripsi..."></textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" wire:model="is_active" id="is_active"
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition-colors">
                        <label for="is_active" class="text-sm font-semibold text-gray-700 select-none cursor-pointer">
                            Status Aktif
                        </label>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-3 sticky bottom-0">
                <button wire:click="closeModal"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button wire:click="save"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-105">
                    {{ $isEditMode ? 'Simpan Perubahan' : 'Tambah Detail' }}
                </button>
            </div>
        </div>
    </div>
@endif
