@if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" wire:click="closeModal"></div>

        <!-- Modal Content -->
        <div class="relative w-full max-w-xl bg-white shadow-2xl rounded-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-5 py-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">
                        {{ $isEditMode ? 'Edit Polres' : 'Tambah Polres Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <form wire:submit="save" class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
                <!-- Polda Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Polda <span class="text-red-500">*</span>
                    </label>
                    <div wire:ignore wire:key="select-polda-{{ $policeStationId ?? 'new' }}">
                        <select id="select-polda" x-data x-ref="input" x-init="$($refs.input).selectize({
                            dropdownParent: 'body',
                            allowClear: true,
                            plugins: ['clear_button'],
                            onChange: function(e) {
                                @this.set('regional_police_id', e ? e : '');
                            }
                        });"
                            wire:model="regional_police_id">
                            <option value="">-- Pilih Polda --</option>
                            @foreach ($regionalPolices as $polda)
                                <option value="{{ $polda->id }}"
                                    {{ $regional_police_id == $polda->id ? 'selected' : '' }}>{{ $polda->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('regional_police_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="name"
                        class="w-full px-3 py-2 text-sm rounded-lg border {{ $errors->has('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-500/20' }} focus:ring-2 transition-all bg-gray-50 focus:bg-white"
                        placeholder="Nama Polres">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <!-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea wire:model="description" rows="3"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-gray-50 focus:bg-white resize-none"
                        placeholder="Deskripsi Polres (opsional)"></textarea>
                </div> -->

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat
                    </label>
                    <textarea wire:model="address" rows="3"
                        class="w-full px-3 py-2 text-sm rounded-lg border {{ $errors->has('address') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-500/20' }} focus:ring-2 transition-all bg-gray-50 focus:bg-white resize-none"
                        placeholder="Alamat Polres (opsional)"></textarea>
                    @error('address')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-blue-300/20 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                            </div>
                        </label>
                        <span class="text-sm text-gray-600">{{ $is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex gap-3 pt-3 border-t border-gray-100">
                    <button type="button" wire:click="closeModal"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-lg hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
                        <svg wire:loading wire:target="save" class="animate-spin h-4 w-4" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ $isEditMode ? 'Perbarui' : 'Simpan' }}</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
