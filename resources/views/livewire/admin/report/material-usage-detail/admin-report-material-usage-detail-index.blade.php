<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detail Penggunaan Material</h1>
        <p class="text-gray-600">Daftar detail penggunaan material dikelompokkan berdasarkan tipe material.</p>
    </div>

    {{-- Filters --}}
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Penggunaan</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($this->totalItems, 0, ',', '.') }}</p>
                    <p class="text-xs text-blue-200 mt-1">Transaksi</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-cyan-500 to-teal-600 rounded-2xl p-5 text-white shadow-lg shadow-cyan-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-cyan-100 text-sm">Total Unit</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($this->totalQuantity, 0, ',', '.') }}</p>
                    <p class="text-xs text-cyan-200 mt-1">Item</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @if(auth()->user()->hasRole('Admin'))
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Polda</label>
                <div wire:ignore>
                        <select id="select-regional-police" x-data x-init="
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Polres</label>
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
                            @foreach ($policeStations as $station)
                                <option value="{{ $station->id }}" @selected($policeStationId == $station->id)>{{ $station->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Tipe</label>
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
                        <option value="">Semua Tipe</option>
                        @foreach ($allTypes as $t)
                            <option value="{{ $t->id }}" @selected($typeId == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Tipe Detail</label>
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
                    placeholder="Semua Detail">
                        <option value="">Semua Detail</option>
                        @foreach ($typeDetails as $td)
                            <option value="{{ $td->id }}" @selected($typeDetailId == $td->id)>{{ $td->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @forelse($typeGroups as $group)
        @php
            $type = $group['type'];
            $details = $group['details'];
            $services = $group['services'];
            $hasTypeDetails = $group['hasTypeDetails'];
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="p-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                    {{ $type->name }}
                </h2>
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800">
                    {{ $details->total() }} Item
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        {{-- First Header Row: Services --}}
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200" style="width: 50px;">No</th>

                            @if($hasTypeDetails)
                                <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">Tipe Detail</th>
                            @endif

                            @if($type->is_with_serial_number)
                                <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">No Seri</th>
                            @endif

                            @foreach($services as $service)
                                @if($service->details->count() > 0)
                                    <th colspan="{{ $service->details->count() }}" class="px-4 py-2 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-100">
                                        {{ $service->name }}
                                    </th>
                                @else
                                    <th rowspan="2" class="px-4 py-2 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-100">
                                        {{ $service->name }}
                                    </th>
                                @endif
                            @endforeach

                            <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah</th>
                            <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200" style="width: 50px;">Polda</th>
                             <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200" style="width: 50px;">Polres</th>
                        </tr>

                        {{-- Second Header Row: Service Details --}}
                        <tr class="bg-gray-50 border-b border-gray-200">
                            @foreach($services as $service)
                                @if($service->details->count() > 0)
                                    @foreach($service->details as $serviceDetail)
                                        <th class="px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase tracking-wider border-r border-gray-200 min-w-[60px]">
                                            {{ $serviceDetail->name }}
                                        </th>
                                    @endforeach
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($details as $index => $detail)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-gray-100">
                                    {{ $details->firstItem() + $index }}
                                </td>


                                @if($hasTypeDetails)
                                    <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $detail->typeDetail->name ?? '-' }}
                                        </div>
                                    </td>
                                @endif

                                @if($type->is_with_serial_number)
                                    <td class="px-4 py-3 whitespace-nowrap border-r border-gray-100">
                                        @if($detail->item_code)
                                            <span class="text-xs text-blue-600 font-medium mb-1">{{ $detail->item_code }}</span>
                                        @endif
                                        @if($detail->number_serial_first)
                                            <span class="text-xs text-gray-600 font-mono">{{ $detail->number_serial_first }}</span>
                                        @endif
                                        @if($detail->number_serial_second)
                                            <span class="text-xs text-gray-500 font-mono">{{ $detail->number_serial_second }}</span>
                                        @endif
                                        @if(!$detail->item_code && !$detail->number_serial_first && !$detail->number_serial_second)
                                            <span class="text-gray-300">0</span>
                                        @endif
                                    </td>
                                @endif

                                {{-- Iterate through Services and their Details to create cells --}}
                                @foreach($services as $service)
                                    @if($service->details->count() > 0)
                                        @foreach($service->details as $serviceDetail)
                                            @php
                                                // Find the item for this specific service and service detail
                                                $item = $detail->materialUsageDetailItems
                                                    ->where('service_id', $service->id)
                                                    ->where('service_detail_id', $serviceDetail->id)
                                                    ->first();
                                            @endphp
                                            <td class="px-2 py-3 text-center border-r border-gray-100">
                                                @if($item && $item->quantity > 0)
                                                    <span class="font-medium text-gray-800 text-sm">
                                                        {{ number_format($item->quantity, 0,',','.') }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-300">0</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    @else
                                        @php
                                            // Find the item for this specific service (no detail)
                                            $item = $detail->materialUsageDetailItems
                                                ->where('service_id', $service->id)
                                                ->whereNull('service_detail_id')
                                                ->first();
                                        @endphp
                                        <td class="px-2 py-3 text-center border-r border-gray-100">
                                            @if($item && $item->quantity > 0)
                                                <span class="font-medium text-gray-800 text-sm">
                                                    {{ number_format($item->quantity, 0,',','.') }}
                                                </span>
                                            @else
                                                <span class="text-gray-300">0</span>
                                            @endif
                                        </td>
                                    @endif
                                @endforeach

                                <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900">
                                    {{ number_format($detail->quantity, 0,',','.') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-gray-100">
                                    {{ $detail?->materialUsage?->regionalPolice?->name }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-gray-100">
                                    {{ $detail?->materialUsage?->policeStation?->name }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($details->hasPages())
                @php
                    $pageName = 'page_' . $type->id;
                @endphp
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-600">
                            Menampilkan <span class="font-semibold">{{ $details->firstItem() }}</span>
                            sampai <span class="font-semibold">{{ $details->lastItem() }}</span>
                            dari <span class="font-semibold">{{ $details->total() }}</span> hasil
                        </div>
                        <div class="flex items-center gap-1">
                            @if ($details->onFirstPage())
                                <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @else
                                <button wire:click="previousPage('{{ $pageName }}')"
                                    class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif
                            @php
                                $currentPage = $details->currentPage();
                                $lastPage = $details->lastPage();
                                $start = max(1, $currentPage - 2);
                                $end = min($lastPage, $currentPage + 2);
                            @endphp
                            @if ($start > 1)
                                <button wire:click="gotoPage(1, '{{ $pageName }}')"
                                    class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">1</button>
                                @if ($start > 2)
                                    <span class="px-2 py-2 text-sm text-gray-400">...</span>
                                @endif
                            @endif
                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $currentPage)
                                    <span
                                        class="px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg">{{ $page }}</span>
                                @else
                                    <button wire:click="gotoPage({{ $page }}, '{{ $pageName }}')"
                                        class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">{{ $page }}</button>
                                @endif
                            @endfor
                            @if ($end < $lastPage)
                                @if ($end < $lastPage - 1)
                                    <span class="px-2 py-2 text-sm text-gray-400">...</span>
                                @endif
                                <button wire:click="gotoPage({{ $lastPage }}, '{{ $pageName }}')"
                                    class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">{{ $lastPage }}</button>
                            @endif
                            @if ($details->hasMorePages())
                                <button wire:click="nextPage('{{ $pageName }}')"
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
                        Menampilkan <span class="font-semibold">{{ $details->count() }}</span> hasil
                    </div>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
            <p class="mt-1 text-sm text-gray-500">Belum ada detail penggunaan material yang tercatat.</p>
        </div>
    @endforelse
</div>
