<div>
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-blue-700">📦 Stock Material Polres</h1>
            <p class="text-gray-500 text-sm mt-0.5">Detail stok material — material utama & pendukung — beserta posisi rak & nomor seri</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Total Jenis Material</div>
            <div class="text-2xl font-bold">{{ $stocks->total() }}</div>
            <div class="text-xs opacity-70 mt-1">Kelompok material</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-600 to-green-600 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Total Stok Keseluruhan</div>
            <div class="text-2xl font-bold">{{ number_format($totalStock, 0, ',', '.') }}</div>
            <div class="text-xs opacity-70 mt-1">Unit tersedia</div>
        </div>
        <div class="bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Material Bernomor Seri</div>
            <div class="text-2xl font-bold">{{ number_format($serializedCount, 0, ',', '.') }}</div>
            <div class="text-xs opacity-70 mt-1">Item tercatat</div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Total Baris Detail</div>
            <div class="text-2xl font-bold">{{ number_format($totalDetailRows, 0, ',', '.') }}</div>
            <div class="text-xs opacity-70 mt-1">Baris stock detail</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-{{ auth()->user()->hasRole('Admin') ? '4' : '3' }} gap-4">
            @if(auth()->user()->hasRole('Admin'))
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Filter Polres</label>
                    <div wire:ignore>
                        <select id="select-police-station" x-data x-init="
                            const selectize = $($el).selectize({
                                dropdownParent: 'body', allowClear: true, plugins: ['clear_button'],
                                onChange: function(val) { @this.set('policeStationId', val); }
                            })[0].selectize;
                        " placeholder="Semua Polres">
                            <option value="">Semua Polres</option>
                            @foreach ($policeStations as $station)
                                <option value="{{ $station->id }}" @selected($policeStationId == $station->id)>{{ $station->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Filter Jenis Material</label>
                <div wire:ignore>
                    <select id="select-type" x-data x-init="
                        const selectize = $($el).selectize({
                            dropdownParent: 'body', allowClear: true, plugins: ['clear_button'],
                            onChange: function(val) { @this.set('typeId', val); }
                        })[0].selectize;
                    " placeholder="Semua Material">
                        <option value="">Semua Material</option>
                        @foreach ($allTypes as $t)
                            <option value="{{ $t->id }}" @selected($typeId == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Filter Detail Material</label>
                <div wire:ignore wire:key="select-type-detail-wrapper-{{ $typeId }}">
                    <select id="select-type-detail-{{ $typeId }}" x-data x-init="
                        const selectize = $($el).selectize({
                            dropdownParent: 'body', allowClear: true, plugins: ['clear_button'],
                            onChange: function(val) { @this.set('typeDetailId', val); }
                        })[0].selectize;
                    " placeholder="Semua Detail">
                        <option value="">Semua Detail</option>
                        @foreach ($typeDetails as $td)
                            <option value="{{ $td->id }}" @selected($typeDetailId == $td->id)>{{ $td->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pencarian</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari material, no seri, rak..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>
        </div>

        <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100">
            <span class="text-xs text-gray-500">Tampilkan:</span>
            <select wire:model.live="perPage" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm bg-gray-50">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-xs text-gray-500">data per halaman</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm min-w-[900px]">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-700 to-blue-600 text-white">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase w-10">No</th>
                        @if(auth()->user()->hasRole('Admin'))
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Polres</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase min-w-[130px]">Jenis Material</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase min-w-[110px]">Detail Material</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase min-w-[120px] bg-blue-800/30">No Seri A</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase min-w-[120px] bg-blue-800/30">No Seri B</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase min-w-[110px]">Posisi Rak</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase w-28 bg-green-700/30">Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stocks as $groupIndex => $stock)
                        @php
                            $details = $stock['details'];
                            $rowspan = $details->count();
                        @endphp
                        @foreach ($details as $detailIndex => $detail)
                            <tr class="hover:bg-blue-50/40 transition-colors duration-150">
                                {{-- Group cell (only on first row) --}}
                                @if ($detailIndex == 0)
                                    <td class="px-4 py-3 text-center text-xs text-gray-500 align-top border-r border-gray-200 font-medium" rowspan="{{ $rowspan }}">
                                        {{ $stocks->firstItem() + $groupIndex }}
                                    </td>
                                    @if(auth()->user()->hasRole('Admin'))
                                        <td class="px-4 py-3 text-xs text-gray-700 align-top border-r border-gray-200" rowspan="{{ $rowspan }}">
                                            <span class="font-semibold">{{ $stock['policeStation']->name ?? '-' }}</span>
                                        </td>
                                    @endif
                                    <td class="px-4 py-3 align-top border-r border-gray-200" rowspan="{{ $rowspan }}">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">
                                            {{ $stock['type']->name ?? '-' }}
                                        </span>
                                        <div class="text-[10px] text-gray-400 mt-1">
                                            Total: <span class="font-bold text-blue-600">{{ number_format($stock['total_quantity'], 0, ',', '.') }}</span> unit
                                        </div>
                                    </td>
                                @endif

                                {{-- Detail Material --}}
                                <td class="px-4 py-3 text-xs text-gray-700">
                                    {{ $detail->typeDetail->name ?? '-' }}
                                </td>

                                {{-- No Seri A --}}
                                <td class="px-4 py-3 text-xs bg-blue-50/30">
                                    @if($detail->number_serial_first || $detail->code)
                                        <span class="font-mono text-blue-700 font-semibold">
                                            {{ $detail->number_serial_first ?: $detail->code }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- No Seri B --}}
                                <td class="px-4 py-3 text-xs bg-blue-50/30">
                                    @if($detail->number_serial_second)
                                        <span class="font-mono text-blue-600">{{ $detail->number_serial_second }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                {{-- Posisi Rak --}}
                                <td class="px-4 py-3 text-xs text-gray-700">
                                    @if($detail->rack?->name)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-orange-700 bg-orange-50 border border-orange-200 px-2 py-0.5 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4zM3 9a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                                            {{ $detail->rack->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Stok per baris --}}
                                <td class="px-4 py-3 text-center bg-green-50/40">
                                    <span class="text-sm font-bold {{ $detail->total_quantity > 0 ? 'text-green-700' : 'text-red-400' }}">
                                        {{ number_format($detail->total_quantity, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <svg class="mx-auto h-14 w-14 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-gray-500 font-medium">Tidak ada data stock</p>
                                <p class="text-gray-400 text-xs mt-1">Belum ada stock material di level Polres</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if ($stocks->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-gray-500">
                    Menampilkan <strong>{{ $stocks->firstItem() }}–{{ $stocks->lastItem() }}</strong> dari <strong>{{ $stocks->total() }}</strong> kelompok
                </div>
                <div class="flex items-center gap-1">
                    @if (!$stocks->onFirstPage())
                        <button wire:click="previousPage" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </button>
                    @endif
                    @php $cp = $stocks->currentPage(); $lp = $stocks->lastPage(); @endphp
                    @for ($p = max(1, $cp-2); $p <= min($lp, $cp+2); $p++)
                        @if ($p == $cp)
                            <span class="px-3 py-1.5 text-xs font-bold text-white bg-blue-600 rounded-lg">{{ $p }}</span>
                        @else
                            <button wire:click="gotoPage({{ $p }})" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">{{ $p }}</button>
                        @endif
                    @endfor
                    @if ($stocks->hasMorePages())
                        <button wire:click="nextPage" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                        </button>
                    @endif
                </div>
            </div>
        @else
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 text-xs text-gray-500">
                Menampilkan <strong>{{ $stocks->count() }}</strong> hasil
            </div>
        @endif
    </div>
</div>
