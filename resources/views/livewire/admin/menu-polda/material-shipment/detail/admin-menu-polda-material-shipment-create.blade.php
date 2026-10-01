<div class="space-y-6">
    <h1 class="text-2xl font-bold">{{ $shipmentId ? 'Edit' : 'Buat' }} SPPM</h1>
    <p>Draft dapat dibuka dan dicetak Polres untuk dibawa ke warehouse. Warehouse dapat memindai draft maupun SPPM terkirim yang belum diambil. Penerimaan Polres dilakukan setelah pemindaian warehouse.</p>
    @if($errors->any())<div role="alert" class="rounded bg-red-50 p-4 text-red-700">{{ $errors->first() }}</div>@endif
    <div class="grid gap-4 rounded-xl bg-white p-5 md:grid-cols-2">
        <label>Nomor SPPM<div class="flex items-center gap-1"><span>SPPM/</span><input aria-label="Nomor SPPM" wire:model="number" inputmode="numeric" class="w-28 rounded border p-2" placeholder="Nomor"><span>/VII/LOG.3.6.7./{{ substr($shipment_date, 0, 4) }}</span></div></label>
        <label>Tanggal<input type="date" wire:model.live="shipment_date" class="block rounded border p-2"></label>
        @if(auth()->user()->hasRole('Admin'))<label>Polda<select wire:model.live="regional_police_id" class="block w-full rounded border p-2"><option value="">Pilih Polda</option>@foreach($regions as $region)<option value="{{ $region->id }}">{{ $region->name }}</option>@endforeach</select></label>@endif
        <label>Polres tujuan<select wire:model="receiver_police_station_id" class="block w-full rounded border p-2"><option value="">Pilih Polres</option>@foreach($stations as $station)<option value="{{ $station->id }}">{{ $station->name }}</option>@endforeach</select></label>
    </div>
    <div class="overflow-x-auto rounded-xl border bg-white"><table class="w-full text-sm"><thead class="bg-blue-50"><tr><th class="p-3">Material utama / pendukung dan batch</th><th>Satuan</th><th>Jumlah</th><th>Seri awal</th><th>Seri akhir</th><th></th></tr></thead><tbody>
        @foreach($details as $index => $detail)
            @php($stock = $stocks->firstWhere('id', $detail['stock_detail_id']))
            <tr wire:key="shipment-row-{{ $index }}" class="border-t">
                <td class="p-3"><select aria-label="Batch material {{ $index + 1 }}" wire:model.live="details.{{ $index }}.stock_detail_id" class="w-80 rounded border p-2"><option value="">Pilih material dan batch</option>@foreach($stocks as $batch)<option value="{{ $batch->id }}">{{ $batch->type?->name }} {{ $batch->typeDetail?->name }} {{ $batch->service?->name }} {{ $batch->serviceDetail?->name }} | {{ $batch->code }} {{ $batch->number_serial_first }}–{{ $batch->number_serial_second }} | Sisa {{ (int)$batch->quantity }}</option>@endforeach</select></td>
                <td class="p-2">{{ $stock?->type?->unit ?? 'Unit' }}</td>
                <td class="p-2"><input aria-label="Jumlah kirim" type="number" min="1" step="1" wire:model.live.blur="details.{{ $index }}.quantity" class="w-24 rounded border p-2"></td>
                <td class="p-2"><input aria-label="Seri awal" wire:model.live.blur="details.{{ $index }}.number_serial_first" class="w-40 rounded border p-2" @disabled(!$stock?->number_serial_first)></td>
                <td class="p-2"><input aria-label="Seri akhir" wire:model="details.{{ $index }}.number_serial_second" class="w-40 rounded border p-2" @disabled(!$stock?->number_serial_first)></td>
                <td class="p-2"><button wire:click="removeDetail({{ $index }})" class="text-red-600">Hapus</button></td>
            </tr>
        @endforeach
    </tbody></table></div>
    <button wire:click="addDetail" class="rounded bg-green-600 px-4 py-2 text-white">Tambah material / pendukung</button>
    <p class="text-sm text-gray-600">Jumlah nomor seri = akhir − awal + 1. Sisa rentang batch diperbarui saat pengiriman atau pemindaian warehouse.</p>
    <label class="block">Catatan<textarea wire:model="notes" class="block w-full rounded border p-2"></textarea></label>
    <div class="flex gap-4"><button wire:click="save(false)" wire:loading.attr="disabled" class="rounded bg-blue-600 px-5 py-3 text-white">Simpan Draft untuk Polres</button><button wire:click="save(true)" wire:loading.attr="disabled" class="rounded bg-green-600 px-5 py-3 text-white">Simpan dan Kirim</button><a href="{{ route('menu-polda.material-shipment') }}" class="p-3">Kembali</a></div>
</div>
