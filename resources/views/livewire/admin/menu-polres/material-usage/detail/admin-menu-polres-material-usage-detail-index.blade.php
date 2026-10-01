<div class="space-y-6">
    <h1 class="text-2xl font-bold">Penggunaan Material Harian Polres</h1>
    <p>Isi seluruh jumlah penggunaan. Masukkan <strong>0</strong> bila tidak digunakan. Nomor seri dicatat berdasarkan batch stok.</p>
    <form wire:submit="save" class="space-y-5">
        @if($errors->any())<div role="alert" class="rounded bg-red-50 p-4 text-red-700">{{ $errors->first() }}</div>@endif
        <div class="flex flex-wrap gap-4">
            <label>Tanggal<input type="date" wire:model="date" required max="{{ now('Asia/Jakarta')->toDateString() }}" class="block rounded border p-2"></label>
            @if(auth()->user()->hasRole('Admin'))
                <label>Polres<select wire:model="policeStationId" required class="block rounded border p-2"><option value="">Pilih Polres</option>@foreach($policeStations as $station)<option value="{{ $station->id }}">{{ $station->name }}</option>@endforeach</select></label>
            @endif
        </div>
        <div class="overflow-x-auto rounded-xl border bg-white">
            <table class="w-full text-sm"><thead class="bg-blue-50"><tr><th class="p-3 text-left">Material / Service</th><th>Satuan</th><th class="p-3">Jumlah penggunaan *</th></tr></thead>
                <tbody>@foreach($rows as $key => $row)
                    <tr wire:key="usage-{{ $key }}" class="border-t"><td class="p-3">{{ $row['label'] }}</td><td class="text-center">{{ $row['unit'] }}</td><td class="p-3"><input aria-label="Jumlah {{ $row['label'] }}" type="number" min="0" step="1" required placeholder="Wajib diisi" wire:model="quantities.{{ $key }}" class="w-40 rounded border p-2">@error('quantities.'.$key)<p class="text-red-600">{{ $message }}</p>@enderror</td></tr>
                @endforeach</tbody>
            </table>
        </div>
        <label class="block">Catatan<textarea wire:model="description" class="mt-1 block w-full rounded border p-2"></textarea></label>
        <button type="submit" wire:loading.attr="disabled" class="rounded-lg bg-blue-600 px-5 py-3 text-white">Simpan penggunaan</button>
        <a href="{{ route('menu-polres.material-usage') }}" class="ml-4">Kembali</a>
    </form>
</div>
