<!-- Create/Edit Item Modal -->
@if ($showItemModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 transition-opacity bg-gray-900/70 backdrop-blur-sm" wire:click="closeItemModal"></div>

            <!-- Modal Content -->
            <div class="relative inline-block w-full max-w-lg p-6 my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl animate-in fade-in zoom-in-95 duration-200">
                <!-- Header -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl {{ $isItemEditMode ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                            @if ($isItemEditMode)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 whitespace-overwarp">
                                {{ $isItemEditMode ? 'Edit Item Penerimaan PNBP' : 'Tambah Item Penerimaan PNBP' }}
                            </h3>
                            <p class="text-xs text-gray-500 whitespace-overwarp">
                                Sheet: <span class="font-bold text-blue-600">{{ $selectedMonth }}</span> (TA {{ $selectedYear }})
                            </p>
                        </div>
                    </div>
                    <button wire:click="closeItemModal" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="saveItem" class="mt-4 space-y-4">
                    <!-- Row: Tingkatan/Level & No Urut -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 whitespace-overwarp">
                                Tingkatan / Level <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model="itemLevel"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white">
                                <option value="1">Level 1 - Kategori Utama (Header)</option>
                                <option value="2">Level 2 - Sub-Kategori Materiil</option>
                                <option value="3">Level 3 - Rincian Khusus (Deep)</option>
                            </select>
                            @error('itemLevel') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 whitespace-overwarp">
                                Nomor / Kode (No)
                            </label>
                            <input type="text" wire:model="itemNo"
                                placeholder="Contoh: 1, a., 1)"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white">
                            @error('itemNo') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Jenis Penerimaan (Nama) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 whitespace-overwarp">
                            Jenis Penerimaan PNBP (Nama) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" wire:model="itemName"
                            placeholder="Contoh: Penerbitan SIM A, Penerbitan STNK..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white">
                        @error('itemName') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tarif (Rp) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 whitespace-overwarp">
                            Tarif PNBP (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-gray-400">Rp</span>
                            <input type="number" step="any" min="0" wire:model.live.debounce.250ms="itemTarif"
                                placeholder="0"
                                class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white">
                        </div>
                        @error('itemTarif') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Row: Target Volume & Realisasi Volume -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 whitespace-overwarp">
                                Target Volume (Unit) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="any" min="0" wire:model.live.debounce.250ms="itemVolumeTarget"
                                placeholder="0"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white">
                            @error('itemVolumeTarget') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 whitespace-overwarp">
                                Realisasi Volume (Unit) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="any" min="0" wire:model.live.debounce.250ms="itemVolumeRealization"
                                placeholder="0"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white">
                            @error('itemVolumeRealization') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Live Calculation Preview Card -->
                    @php
                        $calcTargetRp = (float)$itemVolumeTarget * (float)$itemTarif;
                        $calcRealRp   = (float)$itemVolumeRealization * (float)$itemTarif;
                        $calcPct      = $calcTargetRp > 0 ? round(($calcRealRp / $calcTargetRp) * 100, 2) : 0;
                    @endphp
                    <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200/80 space-y-2">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 flex items-center justify-between whitespace-overwarp">
                            <span>Kalkulasi Otomatis (Format Excel)</span>
                            <span class="text-blue-600 font-extrabold">Formula Aktif</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div class="bg-white p-2 rounded-lg border border-gray-200">
                                <span class="text-[10px] text-gray-500 block">Target (Rp)</span>
                                <span class="font-bold text-gray-900 truncate block">Rp {{ number_format($calcTargetRp) }}</span>
                            </div>
                            <div class="bg-white p-2 rounded-lg border border-gray-200">
                                <span class="text-[10px] text-gray-500 block">Realisasi (Rp)</span>
                                <span class="font-bold text-emerald-600 truncate block">Rp {{ number_format($calcRealRp) }}</span>
                            </div>
                            <div class="bg-white p-2 rounded-lg border border-gray-200">
                                <span class="text-[10px] text-gray-500 block">% Capaian</span>
                                <span class="font-black text-cyan-600 block">{{ number_format($calcPct, 2) }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Apply to all months option (only on create) -->
                    @if (! $isItemEditMode)
                        <div class="pt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="applyToAllMonths"
                                    class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="text-xs text-gray-700 font-medium whitespace-overwarp">
                                    Terapkan item ini ke seluruh 13 sheet (Januari s/d Desember & Rekap)
                                </span>
                            </label>
                        </div>
                    @endif

                    <!-- Modal Actions -->
                    <div class="mt-6 pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                        <button type="button" wire:click="closeItemModal"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white rounded-xl shadow-md transition-all active:scale-95 {{ $isItemEditMode ? 'bg-amber-600 hover:bg-amber-700 shadow-amber-500/20' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/20' }}">
                            <svg wire:loading wire:target="saveItem" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span>{{ $isItemEditMode ? 'Simpan Perubahan' : 'Tambah Item' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Delete Item Confirmation Modal -->
@if ($showDeleteItemModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/70 backdrop-blur-sm" wire:click="closeDeleteItemModal"></div>
            <div class="relative inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl animate-in fade-in zoom-in-95 duration-200">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-rose-100 mb-4">
                        <svg class="h-7 w-7 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2 whitespace-overwarp">Hapus Item Materiil?</h3>
                    <p class="text-gray-500 text-xs whitespace-overwarp leading-relaxed">
                        Apakah Anda yakin ingin menghapus item <strong class="text-gray-900">"{{ $itemName }}"</strong> dari Sheet {{ $selectedMonth }} TA {{ $selectedYear }}? Seluruh kalkulasi grup akan dihitung ulang secara otomatis.
                    </p>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" wire:loading.attr="disabled" wire:click="closeDeleteItemModal"
                        class="flex-1 px-4 py-2.5 text-xs font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:loading.attr="disabled" wire:click="deleteItem"
                        class="flex-1 px-4 py-2.5 text-xs font-semibold text-white bg-gradient-to-r from-rose-600 to-red-600 rounded-xl hover:from-rose-700 hover:to-red-700 shadow-md shadow-rose-500/20 transition-all active:scale-95">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
