<div class="space-y-6" x-data="{ values: $wire.entangle('quantities'), get filled() { return Object.values(this.values).filter(v => v !== '' && v !== null && v !== undefined).length; } }">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><h1 class="text-3xl font-bold text-blue-600">{{ $materialUsageId ? 'Edit' : 'Tambah' }} Penggunaan Material</h1><p class="mt-1 text-sm text-gray-500">Laporan penggunaan material harian Polres</p></div>
        <a href="{{ route('menu-polres.material-usage') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200"><span aria-hidden="true">←</span> Kembali</a>
    </div>
    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-600 to-cyan-500 p-5 text-white"><p class="text-sm text-blue-100">Material / layanan</p><p class="mt-1 text-3xl font-bold">{{ count($rows) }}</p></div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Sudah diisi</p><p class="mt-1 text-3xl font-bold text-green-600" x-text="filled"></p></div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Belum diisi</p><p class="mt-1 text-3xl font-bold text-amber-600" x-text="{{ count($rows) }} - filled"></p></div>
    </div>
    <form wire:submit="save" class="space-y-6">
        @if($errors->any())<div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"><strong>Data belum dapat disimpan.</strong> {{ $errors->first() }}</div>@endif
        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl shadow-gray-200/50">
            <div class="border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50/50 px-6 py-4"><h2 class="text-lg font-bold text-gray-900">Informasi Laporan</h2></div>
            <div class="grid gap-6 p-6 sm:grid-cols-2">
                <div><label for="usage-date" class="mb-2 block text-sm font-semibold text-gray-700">Tanggal penggunaan <span class="text-red-500">*</span></label><input id="usage-date" type="date" wire:model="date" required max="{{ now('Asia/Jakarta')->toDateString() }}" class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">@error('date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                @if(auth()->user()->hasRole('Admin'))
                    <div><label for="usage-station" class="mb-2 block text-sm font-semibold text-gray-700">Polres <span class="text-red-500">*</span></label><select id="usage-station" wire:model="policeStationId" required class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"><option value="">Pilih Polres</option>@foreach($policeStations as $station)<option value="{{ $station->id }}">{{ $station->name }}</option>@endforeach</select>@error('policeStationId')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                @else
                    <div><p class="mb-2 text-sm font-semibold text-gray-700">Polres</p><p class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ auth()->user()->policeStation?->name ?? 'Polres Anda' }}</p></div>
                @endif
            </div>
        </section>
        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl shadow-gray-200/50">
            <div class="border-b border-gray-100 px-6 py-5"><h2 class="text-lg font-bold text-gray-900">Rincian Penggunaan Material</h2><p class="mt-1 text-sm text-gray-500">Seluruh material dan layanan tersedia di bawah. Isi jumlah aktual atau <strong class="text-blue-600">0</strong> jika tidak digunakan.</p></div>
            <div class="border-b border-blue-100 bg-blue-50/60 px-6 py-3 text-xs text-blue-800">Seluruh kolom otomatis berisi 0. Ubah hanya layanan yang digunakan. Jika stok belum tersedia, penggunaan tetap tersimpan sebagai saldo minus.</div>
            <div class="overflow-x-auto"><table class="w-full min-w-[560px] text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500"><tr><th class="w-14 px-5 py-3">No</th><th class="px-5 py-3">Material / layanan</th><th class="w-24 px-4 py-3 text-center">Satuan</th><th class="w-48 px-5 py-3">Jumlah penggunaan <span class="text-red-500">*</span></th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                @php($lastType = null)
                @foreach($rows as $key => $row)
                    @if($lastType !== $row['type_id'])
                        <tr class="bg-blue-50/70"><th colspan="4" class="px-5 py-3 font-semibold text-blue-900">{{ explode(' / ', $row['label'])[0] }}</th></tr>
                        @php($lastType = $row['type_id'])
                    @endif
                    <tr wire:key="usage-{{ $key }}" class="transition-colors hover:bg-gray-50/70">
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $loop->iteration }}</td><td class="px-5 py-3 font-medium text-gray-700"><label for="quantity-{{ $key }}">{{ $row['label'] }}</label></td><td class="px-4 py-3 text-center text-xs text-gray-500">{{ $row['unit'] }}</td>
                        <td class="px-5 py-3"><input id="quantity-{{ $key }}" type="number" inputmode="numeric" min="0" max="999999999" step="1" required x-model="values['{{ $key }}']" class="w-full rounded-lg border px-3 py-2 text-right text-sm tabular-nums focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 {{ $errors->has('quantities.'.$key) ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white' }}">@error('quantities.'.$key)<p role="alert" class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
        </section>
        <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm"><label for="usage-note" class="mb-2 block text-sm font-semibold text-gray-700">Catatan <span class="font-normal text-gray-400">(opsional)</span></label><textarea id="usage-note" wire:model="description" rows="3" maxlength="1000" placeholder="Tambahkan keterangan laporan jika diperlukan…" class="block w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"></textarea></section>
        <div class="sticky bottom-0 z-10 flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-lg sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-500"><span class="font-semibold text-blue-600" x-text="filled"></span> dari {{ count($rows) }} kolom terisi</p>
            <div class="flex items-center justify-end gap-3"><a href="{{ route('menu-polres.material-usage') }}" class="rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-200">Batal</a><button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/20 disabled:opacity-50"><span wire:loading.remove wire:target="save">Simpan Penggunaan</span><span wire:loading wire:target="save">Menyimpan…</span></button></div>
        </div>
    </form>
</div>
