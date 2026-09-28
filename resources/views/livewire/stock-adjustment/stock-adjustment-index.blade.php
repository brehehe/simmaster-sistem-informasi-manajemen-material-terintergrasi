<div>
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">Penyesuaian Stok {{ ucfirst($scope) }}</h1>
                <p class="text-gray-500 mt-1">Buat penyesuaian saldo material dan catat alasan perubahan</p>
            </div>
            <a href="{{ route('menu-'.$scope.'.stock') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors duration-200">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                Kembali
            </a>
        </div>
    </div>
    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3" role="status">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    <form wire:submit="save" class="mb-6">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
            <div class="p-6 space-y-5">
                <h2 class="text-xl font-bold text-gray-900">Informasi Utama</h2>
        <p class="text-sm text-blue-700 bg-blue-50 border border-blue-100 rounded-xl p-4">Masukkan <strong>saldo akhir yang benar</strong>, bukan jumlah tambahan/pengurangan. Nilai 0 dan minus diperbolehkan. Penyesuaian langsung memperbarui stok setelah disimpan.</p>
        @if (auth()->user()->hasRole('Admin'))
            <div>
                <label for="adjustment-owner" class="block text-sm font-semibold text-gray-700 mb-2">{{ ucfirst($scope) }}</label>
                <select id="adjustment-owner" wire:model.live="owner" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white">
                    <option value="">Pilih wilayah</option>
                    @foreach ($owners as $unit)<option value="{{ $unit->id }}">{{ $unit->name }}</option>@endforeach
                </select>
            </div>
        @endif
        <div>
            <label for="adjustment-stock" class="block text-sm font-semibold text-gray-700 mb-2">Material / Batch <span class="text-red-500">*</span></label>
            <select id="adjustment-stock" wire:model.live="stockId" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white">
                <option value="">Pilih stok yang akan disesuaikan</option>
                @foreach ($stocks as $stock)
                    <option value="{{ $stock->id }}">{{ $stock->type?->name }} {{ $stock->typeDetail?->name }} {{ $stock->service?->name }} {{ $stock->serviceDetail?->name }} | {{ $stock->code ?: 'Tanpa kode' }} {{ $stock->number_serial_first }} {{ $stock->number_serial_second }} | {{ $stock->rack?->name ?: 'Tanpa rak' }} | Saldo: {{ $stock->quantity }}</option>
                @endforeach
            </select>
            @error('stockId')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            @if ($stocks->isEmpty())<p class="mt-2 text-sm text-gray-500">{{ $owner ? 'Belum ada stok aktif di wilayah ini. Tambahkan material melalui stok awal atau penerimaan terlebih dahulu.' : 'Pilih wilayah untuk memuat stok.' }}</p>@endif
        </div>
                    </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-6">
            <div class="p-6 space-y-5">
                <h2 class="text-xl font-bold text-gray-900">Detail Penyesuaian</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div><p class="text-sm font-semibold text-gray-700 mb-2">Saldo sebelum</p><p class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm font-semibold text-gray-600">{{ $expected !== '' ? $expected : '—' }}</p></div>
            <div>
                <label for="adjustment-quantity" class="block text-sm font-semibold text-gray-700 mb-2">Saldo akhir <span class="text-red-500">*</span></label>
                <input id="adjustment-quantity" type="number" step="0.01" wire:model.live.debounce.400ms="quantity" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white" required>
                @error('quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div><p class="text-sm font-semibold text-gray-700 mb-2">Selisih penyesuaian</p><p class="bg-blue-50 border border-blue-200 text-blue-700 rounded-lg px-3 py-2 text-sm font-semibold">{{ is_numeric($quantity) && is_numeric($expected) ? number_format((float) $quantity - (float) $expected, 2, ',', '.') : '—' }}</p></div>
        </div>
        <div>
            <label for="adjustment-reason" class="block text-sm font-semibold text-gray-700 mb-2">Alasan penyesuaian <span class="text-red-500">*</span></label>
            <textarea id="adjustment-reason" wire:model="reason" required minlength="5" maxlength="1000" rows="3" placeholder="Jelaskan alasan koreksi dan referensi dokumen pemeriksaan" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white"></textarea>
            @error('reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
                    </div>
        </div>
        <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
            <a href="{{ route('menu-'.$scope.'.stock') }}" class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200">Batal</a>
            <button type="submit" wire:loading.attr="disabled" @disabled($stockId === '') class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white text-sm font-semibold py-3 px-6 rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span wire:loading.remove wire:target="save">Simpan Penyesuaian</span><span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </form>
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">Riwayat Penyesuaian</h2>
            <p class="text-sm text-gray-500 mt-1">20 penyesuaian terakhir pada wilayah yang dipilih</p>
        </div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 text-gray-600 text-left"><tr><th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Tanggal / Kode</th><th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Material</th><th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Sebelum → Sesudah</th><th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Alasan</th><th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Petugas</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($history as $entry)
                    @foreach ($entry->stockOpnameDetails as $detail)
                        <tr class="hover:bg-blue-50/50 transition-colors duration-150"><td class="px-6 py-4">{{ $entry->opname_date->format('d/m/Y') }}<p class="text-xs text-gray-500">{{ $entry->code }}</p></td><td class="px-6 py-4 text-gray-600">{{ $detail->type?->name }}<p class="text-xs text-gray-500">{{ $detail->code }} {{ $detail->number_serial_first }}</p></td><td class="px-6 py-4 whitespace-nowrap font-semibold text-blue-700">{{ $detail->system_quantity }} → {{ $detail->physical_quantity }}</td><td class="px-6 py-4 text-gray-600">{{ $detail->notes }}</td><td class="px-6 py-4 text-gray-600">{{ $entry->checkedByUser?->name ?? '—' }}</td></tr>
                    @endforeach
                @empty
                    <tr><td colspan="5" class="p-10 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <p class="text-gray-500 text-lg font-medium">Belum ada penyesuaian stok</p>
                            <p class="text-gray-400 text-sm mt-1">Riwayat akan muncul setelah penyesuaian disimpan.</p>
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table></div>
    </div>
</div>
