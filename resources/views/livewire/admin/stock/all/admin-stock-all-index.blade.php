<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">Stock Semua</h1>
                <p class="text-gray-500 mt-1">Daftar stock material di level Semua</p>
            </div>
        </div>
    </div>

    <!-- Filters (Outside Table) -->
    <!-- Filters (Outside Table) -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-6 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(auth()->user()->hasRole('Admin'))
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Polda</label>
                    <div wire:ignore>
                        <select id="select-police-station" x-data x-init="
                            const selectize = $($el).selectize({
                                dropdownParent: 'body',
                                allowClear: true,
                                plugins: ['clear_button'],
                                onChange: function(val) {
                                    @this.set('regionalPoliceId', val);
                                }
                            })[0].selectize;
                        "
                        placeholder="Semua Polda">
                            <option value="">Semua Polda</option>
                            @foreach ($regionalPolices as $police)
                                <option value="{{ $police->id }}" @selected($regionalPoliceId == $police->id)>{{ $police->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Polres</label>
                    <div wire:ignore>
                        <select id="select-police-station" x-data x-init="
                            const selectize = $($el).selectize({
                                dropdownParent: 'body',
                                allowClear: true,
                                plugins: ['clear_button'],
                                onChange: function(val) {
                                    @this.set('policeStationId', val);
                                }
                            })[0].selectize;
                        "
                        placeholder="Semua Polres">
                            <option value="">Semua Polres</option>
                            @foreach ($policeStations as $policeStation)
                                <option value="{{ $policeStation->id }}" @selected($policeStationId == $policeStation->id)>{{ $policeStation->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Material</label>
                <div wire:ignore>
                    <select id="select-type" x-data x-init="
                        const selectize = $($el).selectize({
                            dropdownParent: 'body',
                            allowClear: true,
                            plugins: ['clear_button'],
                            onChange: function(val) {
                                @this.set('typeId', val);
                            }
                        })[0].selectize;
                    "
                    placeholder="Semua Tipe">
                        <option value="">Semua Material</option>
                        @foreach ($allTypes as $t)
                            <option value="{{ $t->id }}" @selected($typeId == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Material Detail</label>
                <div wire:ignore wire:key="select-type-detail-wrapper-{{ $typeId }}">
                    <select id="select-type-detail-{{ $typeId }}" x-data x-init="
                        const selectize = $($el).selectize({
                            dropdownParent: 'body',
                            allowClear: true,
                            plugins: ['clear_button'],
                            onChange: function(val) {
                                @this.set('typeDetailId', val);
                            }
                        })[0].selectize;
                    "
                    placeholder="Semua Material Detail">
                        <option value="">Semua Material Detail</option>
                        @foreach ($typeDetails as $td)
                            <option value="{{ $td->id }}" @selected($typeDetailId == $td->id)>{{ $td->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <!-- Search & Filter Header -->
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-center justify-between gap-4">
                <!-- Per Page Select (Left) -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Tampilkan</span>
                    <select wire:model.live="perPage"
                        class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-600">data</span>
                </div>

                <!-- Search Input (Right) -->
                <div class="relative w-80">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari type/detail/rak..."
                        class="w-full pl-4 pr-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        @if(auth()->user()->hasRole('Admin'))
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Polda
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Polres
                        </th>
                        @endif
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jenis
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Sisa
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No Seri
                            / Type Detail</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Quantity</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Letak
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stocks as $groupIndex => $stock)
                        @php
                            $details = $stock['details'];
                            $rowspan = $details->count();
                        @endphp
                        @foreach ($details as $detailIndex => $detail)
                            <tr class="hover:bg-blue-50/50 transition-colors duration-150">
                                @if ($detailIndex == 0)
                                    <td class="px-6 py-4 text-sm text-gray-600 align-top border-r border-gray-200"
                                        rowspan="{{ $rowspan }}">
                                        {{ $stocks->firstItem() + $groupIndex }}
                                    </td>
                                    @if(auth()->user()->hasRole('Admin'))
                                    <td class="px-6 py-4 text-sm text-gray-600 align-top border-r border-gray-200"
                                        rowspan="{{ $rowspan }}">
                                        {{ $stock['regionalPolice']->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 align-top border-r border-gray-200"
                                        rowspan="{{ $rowspan }}">
                                        {{ $stock['policeStation']->name ?? '-' }}
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 align-top border-r border-gray-200"
                                        rowspan="{{ $rowspan }}">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            {{ $stock['type']->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center align-top border-r border-gray-200"
                                        rowspan="{{ $rowspan }}">
                                        <span class="text-xl font-bold text-blue-600">
                                            {{ number_format($stock['total_quantity'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if ($detail->number_serial_first && $detail->number_serial_second)
                                        {{ $detail->code ? Str::ucfirst($detail->code) : '' }}
                                        {{ $detail->number_serial_first }} - {{ $detail->number_serial_second }}
                                    @else
                                        {{ $detail->typeDetail->name ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ number_format($detail->total_quantity, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $detail->rack->name ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">Tidak ada data stock</p>
                                    <p class="text-gray-400 text-sm mt-1">Belum ada stock material di level Polda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if ($stocks->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Info Text -->
                    <div class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">{{ $stocks->firstItem() }}</span>
                        sampai <span class="font-semibold">{{ $stocks->lastItem() }}</span>
                        dari <span class="font-semibold">{{ $stocks->total() }}</span> hasil
                    </div>

                    <!-- Pagination Numbers -->
                    <div class="flex items-center gap-1">
                        {{-- Previous Button --}}
                        @if ($stocks->onFirstPage())
                            <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        @else
                            <button wire:click="previousPage"
                                class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                            $currentPage = $stocks->currentPage();
                            $lastPage = $stocks->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp

                        @if ($start > 1)
                            <button wire:click="gotoPage(1)"
                                class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                1
                            </button>
                            @if ($start > 2)
                                <span class="px-2 py-2 text-sm text-gray-400">...</span>
                            @endif
                        @endif

                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $currentPage)
                                <span class="px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPage({{ $page }})"
                                    class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                    {{ $page }}
                                </button>
                            @endif
                        @endfor

                        @if ($end < $lastPage)
                            @if ($end < $lastPage - 1)
                                <span class="px-2 py-2 text-sm text-gray-400">...</span>
                            @endif
                            <button wire:click="gotoPage({{ $lastPage }})"
                                class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                {{ $lastPage }}
                            </button>
                        @endif

                        {{-- Next Button --}}
                        @if ($stocks->hasMorePages())
                            <button wire:click="nextPage"
                                class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        @else
                            <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                <div class="text-sm text-gray-600">
                    Menampilkan <span class="font-semibold">{{ $stocks->count() }}</span> hasil
                </div>
            </div>
        @endif
    </div>
</div>
