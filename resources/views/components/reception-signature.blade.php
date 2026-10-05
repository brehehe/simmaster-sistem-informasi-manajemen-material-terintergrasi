@props(['person', 'title', 'saved' => null, 'upload' => null])
@php
    $mode = $person.'SignatureMode';
    $drawn = $person.'SignatureDrawn';
    $file = $person.'SignatureUpload';
@endphp
<section class="rounded-xl border border-gray-200 bg-gray-50/50 p-4 sm:p-5" x-data="{ mode: $wire.entangle('{{ $mode }}') }">
    <h3 class="font-bold text-gray-900">{{ $title }}</h3>
    <p class="mt-1 text-xs text-gray-500">Pilih cara menambahkan tanda tangan pada dokumen.</p>
    <div class="my-4 grid grid-cols-2 gap-2 rounded-xl bg-gray-100 p-1">
        <label class="cursor-pointer rounded-lg px-3 py-2 text-center text-sm font-semibold" :class="mode === 'draw' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-600'">
            <input type="radio" value="draw" x-model="mode" name="{{ $mode }}" class="mr-1 accent-blue-600"> Gambar langsung
        </label>
        <label class="cursor-pointer rounded-lg px-3 py-2 text-center text-sm font-semibold" :class="mode === 'upload' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-600'">
            <input type="radio" value="upload" x-model="mode" name="{{ $mode }}" class="mr-1 accent-blue-600"> Unggah gambar
        </label>
    </div>
    <div x-show="mode === 'draw'" x-cloak>
        <div wire:ignore x-data="armasterSignature($wire, '{{ $drawn }}')">
            <div class="relative overflow-hidden rounded-xl border-2 border-dashed border-blue-200 bg-white">
                <canvas x-ref="pad" width="1000" height="300" style="width:100%;aspect-ratio:10/3;touch-action:none;display:block" aria-label="Area gambar {{ $title }}"
                    @pointerdown.prevent="start($event)" @pointermove.prevent="move($event)" @pointerup="finish()" @pointercancel="finish()" @lostpointercapture="finish()"></canvas>
                <span x-show="!dirty && !drawing" class="pointer-events-none absolute inset-0 flex items-center justify-center text-sm text-gray-400">Gambar tanda tangan di sini</span>
            </div>
            <div class="mt-2 flex items-center justify-between gap-3">
                <p class="text-xs text-gray-500">Gunakan mouse, pena, atau sentuhan jari.</p>
                <button type="button" @click="clear()" class="rounded-lg px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">Gambar ulang</button>
            </div>
        </div>
    </div>
    <div x-show="mode === 'upload'">
        <label class="block rounded-xl border-2 border-dashed border-blue-200 bg-white p-4">
            <span class="mb-2 block text-sm font-medium text-gray-700">Pilih gambar tanda tangan</span>
            <input type="file" wire:model="{{ $file }}" accept="image/png,image/jpeg" aria-label="Unggah {{ $title }}" class="block w-full text-xs text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:font-semibold file:text-blue-700">
            <span class="mt-2 block text-xs text-gray-400">PNG atau JPG, maksimal 2 MB. Gunakan gambar yang jelas.</span>
        </label>
        <p wire:loading wire:target="{{ $file }}" role="status" class="mt-2 text-xs text-blue-600">Mengunggah gambar…</p>
        @if($upload && !$errors->has($file) && in_array($upload->getMimeType(), ['image/png', 'image/jpeg']))
            <div class="mt-3 rounded-xl border border-blue-100 bg-white p-3"><p class="mb-2 text-xs font-semibold text-blue-600">Pratinjau unggahan baru</p><img src="{{ $upload->temporaryUrl() }}" alt="Pratinjau {{ $title }}" class="h-24 w-full object-contain"></div>
        @endif
    </div>
    @error($file)<p role="alert" class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
    @error($drawn)<p role="alert" class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
    @error($mode)<p role="alert" class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
    @if($saved)
        <div class="mt-4 border-t border-gray-200 pt-3"><p class="mb-2 text-xs font-semibold text-green-700">Tanda tangan tersimpan</p><img src="{{ $saved }}" alt="{{ $title }} tersimpan" class="h-20 w-full rounded-lg bg-white object-contain"></div>
    @endif
</section>
