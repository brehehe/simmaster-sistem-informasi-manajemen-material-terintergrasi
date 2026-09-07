<div>
    {{-- ========== HEADER ========== --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📋 Laporan Harian Penggunaan Material</h1>
            <p class="text-gray-500 text-sm mt-0.5">Riwayat & laporan material digunakan per hari, siap anev dan download.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('menu-polres.material-usage.create') }}" wire:navigate
                class="inline-flex items-center gap-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-blue-500/25 hover:from-blue-700 hover:to-indigo-700 transition-all text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Input Material
            </a>
            {{-- Download PDF Button --}}
            <button onclick="printLaporanHarian()"
                class="inline-flex items-center gap-1.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-green-500/25 hover:from-green-700 hover:to-emerald-700 transition-all text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download PDF
            </button>
        </div>
    </div>

    {{-- ========== FLASH MESSAGES ========== --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- ========== FILTER TANGGAL PRESET ========== --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-4">
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Periode:</span>
            <button wire:click="setDatePreset('today')"
                class="text-xs px-3 py-1.5 rounded-full font-semibold transition-all {{ (!$dateFrom && !$dateTo) || ($dateFrom == now()->format('Y-m-d') && $dateTo == now()->format('Y-m-d')) ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Hari Ini
            </button>
            <button wire:click="setDatePreset('yesterday')"
                class="text-xs px-3 py-1.5 rounded-full font-semibold transition-all {{ ($dateFrom == now()->subDay()->format('Y-m-d') && $dateTo == now()->subDay()->format('Y-m-d')) ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Kemarin
            </button>
            <button wire:click="setDatePreset('this_week')"
                class="text-xs px-3 py-1.5 rounded-full font-semibold transition-all {{ ($dateFrom == now()->startOfWeek()->format('Y-m-d') && $dateTo == now()->endOfWeek()->format('Y-m-d')) ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Minggu Ini
            </button>
            <button wire:click="setDatePreset('this_month')"
                class="text-xs px-3 py-1.5 rounded-full font-semibold transition-all {{ ($dateFrom == now()->startOfMonth()->format('Y-m-d') && $dateTo == now()->endOfMonth()->format('Y-m-d')) ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Bulan Ini
            </button>
            <button wire:click="setDatePreset('all')"
                class="text-xs px-3 py-1.5 rounded-full font-semibold transition-all {{ !$dateFrom && !$dateTo ? 'bg-gray-700 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            {{-- Date From --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" wire:model.live="dateFrom"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>
            {{-- Date To --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" wire:model.live="dateTo"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>

            @if(auth()->user()->hasRole('Admin'))
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Polres</label>
                <div wire:ignore>
                    <select id="select-police-station-detail" x-data x-init="
                        const selectize = $($el).selectize({
                            dropdownParent: 'body', allowClear: true, plugins: ['clear_button'],
                            onChange: function(val) { @this.set('policeStationId', val); }
                        })[0].selectize;
                    ">
                        <option value="">Semua Polres</option>
                        @foreach ($policeStations as $station)
                            <option value="{{ $station->id }}" @selected($policeStationId == $station->id)>{{ $station->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Material</label>
                <div wire:ignore wire:key="filter-type-{{ $typeId }}">
                    <select id="select-type-detail-filter" x-data x-init="
                        const selectize = $($el).selectize({
                            dropdownParent: 'body', allowClear: true, plugins: ['clear_button'],
                            onChange: function(val) { @this.set('typeId', val); }
                        })[0].selectize;
                    ">
                        <option value="">Semua Tipe</option>
                        @foreach ($allTypes as $t)
                            <option value="{{ $t->id }}" @selected($typeId == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Penggunaan</label>
                <select wire:model.live="usageType" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    <option value="">Semua Jenis</option>
                    <option value="Material Digunakan">Material Digunakan</option>
                    <option value="Material Pendukung">Material Pendukung</option>
                    <option value="Pembangunan">Pembangunan</option>
                    <option value="Pemeliharaan">Pemeliharaan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ========== SUMMARY CARDS ========== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Total Item Penggunaan</div>
            <div class="text-2xl font-bold">{{ number_format($totalUsageCount, 0, ',', '.') }}</div>
            <div class="text-xs opacity-70 mt-1">{{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d M Y') : 'Semua' }}{{ $dateTo && $dateTo != $dateFrom ? ' – '.\Carbon\Carbon::parse($dateTo)->format('d M Y') : '' }}</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-600 to-green-600 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Total Kuantitas</div>
            <div class="text-2xl font-bold">{{ number_format($totalQty, 0, ',', '.') }}</div>
            <div class="text-xs opacity-70 mt-1">Unit dikeluarkan</div>
        </div>
        <div class="bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Jenis Material</div>
            <div class="text-2xl font-bold">{{ count($typeGroups) }}</div>
            <div class="text-xs opacity-70 mt-1">Kategori aktif</div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl p-4 text-white shadow-lg">
            <div class="text-xs font-medium opacity-80 mb-1">Filter Aktif</div>
            <div class="text-lg font-bold">
                {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : '—' }}
            </div>
            <div class="text-xs opacity-70 mt-1">s.d. {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d/m/Y') : '—' }}</div>
        </div>
    </div>

    {{-- ========== TYPE GROUPS ========== --}}
    @forelse($typeGroups as $group)
        @php
            $type = $group['type'];
            $details = $group['details'];
            $services = $group['services'];
            $hasTypeDetails = $group['hasTypeDetails'];
            $groupTotalQty = $group['groupTotalQty'];
        @endphp

        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6" id="print-group-{{ $type->id }}">
            {{-- Group Header --}}
            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-6 bg-blue-500 rounded-full inline-block"></span>
                    {{ $type->name }}
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 ml-1">
                        {{ $details->total() }} baris
                    </span>
                </h2>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">Total:</span>
                    <span class="text-sm font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                        {{ number_format($groupTotalQty, 0, ',', '.') }} unit
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm min-w-[900px] whitespace-nowrap">
                    <thead>
                        {{-- First header row --}}
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th rowspan="2" class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 w-10">No</th>
                            <th rowspan="2" class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 min-w-[90px]">Tanggal</th>
                            @if($hasTypeDetails)
                                <th rowspan="2" class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase border-r border-gray-200">Detail</th>
                            @endif
                            <th rowspan="2" class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 min-w-[110px] bg-blue-50">No Seri A</th>
                            <th rowspan="2" class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 min-w-[110px] bg-blue-50">No Seri B</th>
                            @foreach($services as $service)
                                @if($service->details->count() > 0)
                                    <th colspan="{{ $service->details->count() }}" class="px-3 py-2 text-center text-xs font-bold text-gray-700 uppercase border-r border-gray-200 bg-gray-100">
                                        {{ $service->name }}
                                    </th>
                                @else
                                    <th rowspan="2" class="px-3 py-2 text-center text-xs font-bold text-gray-700 uppercase border-r border-gray-200 bg-gray-100">
                                        {{ $service->name }}
                                    </th>
                                @endif
                            @endforeach
                            <th rowspan="2" class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 w-20 bg-green-50">Jml</th>
                            <th rowspan="2" class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase border-r border-gray-200">Jenis</th>
                            <th rowspan="2" class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase border-r border-gray-200">Polres</th>
                            <th rowspan="2" class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-24">Aksi</th>
                        </tr>
                        {{-- Second header row: service details --}}
                        <tr class="bg-gray-50 border-b border-gray-200">
                            @foreach($services as $service)
                                @if($service->details->count() > 0)
                                    @foreach($service->details as $sd)
                                        <th class="px-2 py-2 text-center text-xs font-medium text-gray-600 uppercase border-r border-gray-200 min-w-[55px]">
                                            {{ $sd->name }}
                                        </th>
                                    @endforeach
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($details as $index => $detail)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-3 py-2.5 text-center text-xs text-gray-500 border-r border-gray-100">{{ $details->firstItem() + $index }}</td>

                                {{-- Tanggal --}}
                                <td class="px-3 py-2.5 border-r border-gray-100">
                                    <div class="text-xs font-semibold text-gray-800">
                                        {{ $detail->materialUsage?->date ? \Carbon\Carbon::parse($detail->materialUsage->date)->format('d/m/Y') : '-' }}
                                    </div>
                                    <div class="text-[10px] text-gray-400">
                                        {{ $detail->materialUsage?->code ?? '' }}
                                    </div>
                                </td>

                                @if($hasTypeDetails)
                                    <td class="px-3 py-2.5 border-r border-gray-100 text-xs text-gray-700">
                                        {{ $detail->typeDetail->name ?? '-' }}
                                    </td>
                                @endif

                                {{-- No Seri A --}}
                                <td class="px-3 py-2.5 border-r border-gray-100 bg-blue-50/30">
                                    <span class="text-xs font-mono text-blue-700 font-semibold">
                                        {{ $detail->number_serial_first ?: ($detail->item_code ?: '-') }}
                                    </span>
                                </td>

                                {{-- No Seri B --}}
                                <td class="px-3 py-2.5 border-r border-gray-100 bg-blue-50/30">
                                    <span class="text-xs font-mono text-blue-600">
                                        {{ $detail->number_serial_second ?: '-' }}
                                    </span>
                                </td>

                                {{-- Service columns --}}
                                @foreach($services as $service)
                                    @if($service->details->count() > 0)
                                        @foreach($service->details as $sd)
                                            @php
                                                $item = $detail->materialUsageDetailItems
                                                    ->where('service_id', $service->id)
                                                    ->where('service_detail_id', $sd->id)
                                                    ->first();
                                            @endphp
                                            <td class="px-2 py-2.5 text-center border-r border-gray-100">
                                                @if($item && $item->quantity > 0)
                                                    <span class="font-medium text-gray-800 text-xs">{{ number_format($item->quantity, 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-gray-300 text-xs">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    @else
                                        @php
                                            $item = $detail->materialUsageDetailItems
                                                ->where('service_id', $service->id)
                                                ->whereNull('service_detail_id')
                                                ->first();
                                        @endphp
                                        <td class="px-2 py-2.5 text-center border-r border-gray-100">
                                            @if($item && $item->quantity > 0)
                                                <span class="font-medium text-gray-800 text-xs">{{ number_format($item->quantity, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-gray-300 text-xs">—</span>
                                            @endif
                                        </td>
                                    @endif
                                @endforeach

                                {{-- Jumlah --}}
                                <td class="px-3 py-2.5 text-center border-r border-gray-100 bg-green-50/50">
                                    <span class="font-bold text-green-700 text-sm">{{ number_format($detail->quantity, 0, ',', '.') }}</span>
                                </td>

                                {{-- Jenis Penggunaan --}}
                                <td class="px-3 py-2.5 border-r border-gray-100">
                                    @php
                                        $usageColors = [
                                            'Material Digunakan' => 'bg-blue-100 text-blue-700',
                                            'Material Pendukung' => 'bg-purple-100 text-purple-700',
                                            'Pembangunan' => 'bg-orange-100 text-orange-700',
                                            'Pemeliharaan' => 'bg-yellow-100 text-yellow-700',
                                            'Lainnya' => 'bg-gray-100 text-gray-600',
                                        ];
                                        $usageColor = $usageColors[$detail->usage_type] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $usageColor }}">
                                        {{ $detail->usage_type ?? '-' }}
                                    </span>
                                </td>

                                {{-- Polres --}}
                                <td class="px-3 py-2.5 text-xs text-gray-600 border-r border-gray-100">
                                    {{ $detail->materialUsage?->policeStation?->name ?? '-' }}
                                </td>

                                {{-- Aksi --}}
                                <td class="px-3 py-2.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('menu-polres.material-usage.edit', $detail->material_usage_id) }}" wire:navigate
                                            class="p-1.5 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-colors"
                                            title="Edit Penggunaan">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        <button type="button" wire:click="openDeleteModal('{{ $detail->material_usage_id }}')"
                                            class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                            title="Hapus Penggunaan">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    {{-- Footer total per group --}}
                    <tfoot>
                        <tr class="bg-blue-50 border-t-2 border-blue-200">
                            <td colspan="{{ 5 + ($hasTypeDetails ? 1 : 0) + $services->sum(fn($s) => max(1, $s->details->count())) }}"
                                class="px-3 py-2 text-right text-xs font-bold text-blue-700">
                                TOTAL {{ strtoupper($type->name) }}:
                            </td>
                            <td class="px-3 py-2 text-center font-extrabold text-blue-800 text-sm bg-blue-100">
                                {{ number_format($groupTotalQty, 0, ',', '.') }}
                            </td>
                            <td colspan="3" class="px-3 py-2 text-xs text-blue-600">unit</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($details->hasPages())
                @php $pageName = 'page_' . $type->id; @endphp
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs text-gray-500">
                        Menampilkan <strong>{{ $details->firstItem() }}–{{ $details->lastItem() }}</strong> dari <strong>{{ $details->total() }}</strong> data
                    </div>
                    <div class="flex items-center gap-1">
                        @if (!$details->onFirstPage())
                            <button wire:click="previousPage('{{ $pageName }}')" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            </button>
                        @endif
                        @php $cp = $details->currentPage(); $lp = $details->lastPage(); @endphp
                        @for ($p = max(1, $cp-2); $p <= min($lp, $cp+2); $p++)
                            @if ($p == $cp)
                                <span class="px-3 py-1.5 text-xs font-bold text-white bg-blue-600 rounded-lg">{{ $p }}</span>
                            @else
                                <button wire:click="gotoPage({{ $p }}, '{{ $pageName }}')" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">{{ $p }}</button>
                            @endif
                        @endfor
                        @if ($details->hasMorePages())
                            <button wire:click="nextPage('{{ $pageName }}')" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                            </button>
                        @endif
                    </div>
                </div>
            @else
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 text-xs text-gray-500">
                    Menampilkan <strong>{{ $details->count() }}</strong> hasil
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
            <svg class="mx-auto h-14 w-14 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-base font-semibold text-gray-700 mb-1">Tidak ada data penggunaan</h3>
            <p class="text-sm text-gray-400 mb-4">Belum ada material yang digunakan pada periode yang dipilih.</p>
            <a href="{{ route('menu-polres.material-usage.create') }}" wire:navigate
                class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold py-2.5 px-6 rounded-xl hover:bg-blue-700 transition-all text-sm">
                + Input Material Digunakan
            </a>
        </div>
    @endforelse

    {{-- ========== PRINT AREA (hidden, for PDF) ========== --}}
    <div id="print-area" style="display:none;">
        <div style="font-family:Arial,sans-serif; padding:20px; color:#111;">
            <div style="text-align:center; margin-bottom:16px; border-bottom:2px solid #1d4ed8; padding-bottom:12px;">
                <h2 style="font-size:16px; font-weight:bold; margin:0; color:#1d4ed8;">LAPORAN HARIAN PENGGUNAAN MATERIAL</h2>
                <p style="font-size:12px; margin:4px 0 0 0; color:#444;">ARMASTER – Sistem Informasi Manajemen Material</p>
                <p style="font-size:11px; margin:4px 0 0 0; color:#666;">
                    Periode:
                    {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') : 'Semua' }}
                    @if($dateTo && $dateTo != $dateFrom)
                        s.d. {{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}
                    @endif
                    | Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
                </p>
            </div>

            @foreach($typeGroups as $group)
            @php
                $t   = $group['type'];
                $ds  = $group['details'];
                $svs = $group['services'];
                $htd = $group['hasTypeDetails'];
            @endphp
            <h3 style="font-size:13px; font-weight:bold; margin:14px 0 6px 0; background:#eff6ff; padding:6px 10px; border-left:4px solid #2563eb;">{{ $t->name }}</h3>
            <table style="width:100%; border-collapse:collapse; font-size:10px; margin-bottom:8px; white-space:nowrap;">
                <thead>
                    <tr style="background:#dbeafe; color:#1e3a8a;">
                        <th style="border:1px solid #bfdbfe; padding:5px 6px; text-align:left;">No</th>
                        <th style="border:1px solid #bfdbfe; padding:5px 6px;">Tanggal</th>
                        <th style="border:1px solid #bfdbfe; padding:5px 6px;">Kode</th>
                        @if($htd)<th style="border:1px solid #bfdbfe; padding:5px 6px;">Detail</th>@endif
                        <th style="border:1px solid #bfdbfe; padding:5px 6px;">No Seri A</th>
                        <th style="border:1px solid #bfdbfe; padding:5px 6px;">No Seri B</th>
                        @foreach($svs as $sv)
                            @if($sv->details->count() > 0)
                                @foreach($sv->details as $sd)<th style="border:1px solid #bfdbfe; padding:5px 4px; text-align:center;">{{ $sv->name }}/{{ $sd->name }}</th>@endforeach
                            @else
                                <th style="border:1px solid #bfdbfe; padding:5px 4px; text-align:center;">{{ $sv->name }}</th>
                            @endif
                        @endforeach
                        <th style="border:1px solid #bfdbfe; padding:5px 6px; text-align:center;">Jumlah</th>
                        <th style="border:1px solid #bfdbfe; padding:5px 6px;">Jenis</th>
                        <th style="border:1px solid #bfdbfe; padding:5px 6px;">Polres</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ds as $i => $d)
                    <tr style="{{ $i % 2 == 0 ? 'background:#fff' : 'background:#f8faff' }}">
                        <td style="border:1px solid #e2e8f0; padding:4px 6px;">{{ $ds->firstItem() + $i }}</td>
                        <td style="border:1px solid #e2e8f0; padding:4px 6px; text-align:center;">{{ $d->materialUsage?->date ? \Carbon\Carbon::parse($d->materialUsage->date)->format('d/m/Y') : '-' }}</td>
                        <td style="border:1px solid #e2e8f0; padding:4px 6px; font-size:9px; color:#555;">{{ $d->materialUsage?->code ?? '-' }}</td>
                        @if($htd)<td style="border:1px solid #e2e8f0; padding:4px 6px;">{{ $d->typeDetail->name ?? '-' }}</td>@endif
                        <td style="border:1px solid #e2e8f0; padding:4px 6px; font-family:monospace; color:#1d4ed8;">{{ $d->number_serial_first ?: ($d->item_code ?: '-') }}</td>
                        <td style="border:1px solid #e2e8f0; padding:4px 6px; font-family:monospace; color:#2563eb;">{{ $d->number_serial_second ?: '-' }}</td>
                        @foreach($svs as $sv)
                            @if($sv->details->count() > 0)
                                @foreach($sv->details as $sd)
                                    @php $itm = $d->materialUsageDetailItems->where('service_id',$sv->id)->where('service_detail_id',$sd->id)->first(); @endphp
                                    <td style="border:1px solid #e2e8f0; padding:4px 4px; text-align:center;">{{ $itm && $itm->quantity > 0 ? number_format($itm->quantity,0,',','.') : '—' }}</td>
                                @endforeach
                            @else
                                @php $itm = $d->materialUsageDetailItems->where('service_id',$sv->id)->whereNull('service_detail_id')->first(); @endphp
                                <td style="border:1px solid #e2e8f0; padding:4px 4px; text-align:center;">{{ $itm && $itm->quantity > 0 ? number_format($itm->quantity,0,',','.') : '—' }}</td>
                            @endif
                        @endforeach
                        <td style="border:1px solid #e2e8f0; padding:4px 6px; text-align:center; font-weight:bold; color:#065f46;">{{ number_format($d->quantity,0,',','.') }}</td>
                        <td style="border:1px solid #e2e8f0; padding:4px 6px; font-size:9px;">{{ $d->usage_type ?? '-' }}</td>
                        <td style="border:1px solid #e2e8f0; padding:4px 6px; font-size:9px;">{{ $d->materialUsage?->policeStation?->name ?? '-' }}</td>
                    </tr>
                    @endforeach
                    <tr style="background:#dbeafe; font-weight:bold;">
                        <td colspan="{{ 6 + ($htd ? 1 : 0) + $svs->sum(fn($s) => max(1, $s->details->count())) }}" style="border:1px solid #bfdbfe; padding:4px 6px; text-align:right; color:#1e3a8a;">TOTAL {{ strtoupper($t->name) }}:</td>
                        <td style="border:1px solid #bfdbfe; padding:4px 6px; text-align:center; color:#065f46; font-size:12px;">{{ number_format($group['groupTotalQty'],0,',','.') }}</td>
                        <td colspan="2" style="border:1px solid #bfdbfe; padding:4px 6px; color:#1e3a8a;">unit</td>
                    </tr>
                </tbody>
            </table>
            @endforeach

            <div style="margin-top:24px; border-top:1px solid #ccc; padding-top:12px; display:flex; justify-content:space-between; font-size:10px; color:#555;">
                <div>
                    <strong>TOTAL KESELURUHAN:</strong> {{ number_format($totalQty,0,',','.') }} unit dari {{ number_format($totalUsageCount,0,',','.') }} item
                </div>
                <div>
                    Dicetak oleh: {{ auth()->user()->name ?? '-' }} | {{ now()->translatedFormat('d F Y, H:i') }} WIB
                </div>
            </div>
        </div>
    </div>

    {{-- ========== PRINT STYLES + SCRIPT ========== --}}
    @push('scripts')
    <script>
        function printLaporanHarian() {
            const printContents = document.getElementById('print-area').innerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    </script>
    @endpush
    <style>
        @media print {
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area { display: block !important; position: absolute; left: 0; top: 0; width: 100%; }
        }
    </style>
    {{-- ========== DELETE MODAL ========== --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity bg-gray-900/70 backdrop-blur-sm" wire:click="closeModal">
                </div>
                <div
                    class="relative inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                            <svg class="h-8 w-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Data Penggunaan Material</h3>
                        <p class="text-gray-500 text-sm">Apakah Anda yakin ingin menghapus data penggunaan ini? Stok material akan <strong>otomatis dikembalikan</strong> ke stok tersedia.</p>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button type="button" wire:click="closeModal"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200">
                            Batal
                        </button>
                        <button type="button" wire:click="delete"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-red-500 to-red-600 rounded-xl hover:from-red-600 hover:to-red-700 shadow-lg shadow-red-500/30 transition-all duration-200">
                            Ya, Hapus & Kembalikan Stok
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
