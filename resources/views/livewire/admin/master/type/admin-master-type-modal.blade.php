@if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" wire:click="closeModal"></div>
        <div class="relative w-full max-w-xl bg-white shadow-2xl rounded-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-5 py-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">{{ $isEditMode ? 'Edit Tipe' : 'Tambah Tipe Baru' }}</h3>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white"><svg
                            xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
            </div>
            <form wire:submit="save" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span
                            class="text-red-500">*</span></label>
                    <input type="text" wire:model="name"
                        class="w-full px-3 py-2 text-sm rounded-lg border {{ $errors->has('name') ? 'border-red-300' : 'border-gray-200' }} focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-gray-50"
                        placeholder="Nama tipe">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea wire:model="description" rows="3"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-gray-50 resize-none"
                        placeholder="Deskripsi (opsional)"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                            </div>
                        </label>
                        <span class="text-sm text-gray-600">{{ $is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                    </div>
                </div>
                <div class="flex gap-3 pt-3 border-t border-gray-100">
                    <button type="button" wire:click="closeModal"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-lg shadow-lg shadow-blue-500/30">{{ $isEditMode ? 'Perbarui' : 'Simpan' }}</button>
                </div>
            </form>
        </div>
    </div>
@endif
