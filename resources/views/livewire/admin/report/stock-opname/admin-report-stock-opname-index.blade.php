<div>
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-emerald-600">Laporan Stock Opname</h1>
                <p class="text-gray-500 mt-1">Monitoring pemeriksaan fisik stock persediaan</p>
            </div>
            <div class="flex gap-2">
                <button onclick="exportToExcel('opnameTable', 'laporan-stock-opname')" class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-emerald-500 hover:from-green-700 hover:to-emerald-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-green-500/30 transition-all duration-300 transform hover:scale-105"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>Export Excel</button>
                <button onclick="exportToPDF({ tableId: 'opnameTable', title: 'Laporan Stock Opname', filename: 'laporan-stock-opname', summaryCards: [{ label: 'Total', value: '{{ $this->totalOpnames }}' }] })" class="inline-flex items-center gap-2 bg-gradient-to-r from-red-600 to-rose-500 hover:from-red-700 hover:to-rose-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-red-500/30 transition-all duration-300 transform hover:scale-105"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" /></svg>Export PDF</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl p-5 text-white shadow-lg shadow-emerald-500/30"><div class="flex items-center justify-between"><div><p class="text-emerald-100 text-sm">Total Opname</p><p class="text-3xl font-bold mt-1">{{ $this->totalOpnames }}</p></div><div class="bg-white/20 rounded-xl p-3"><svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg></div></div></div>
        <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/30"><div class="flex items-center justify-between"><div><p class="text-blue-100 text-sm">Selesai</p><p class="text-3xl font-bold mt-1">{{ $this->completedCount }}</p></div><div class="bg-white/20 rounded-xl p-3"><svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div></div></div>
        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-5 text-white shadow-lg shadow-purple-500/30"><div class="flex items-center justify-between"><div><p class="text-purple-100 text-sm">Total Item Diperiksa</p><p class="text-3xl font-bold mt-1">{{ $opnames->sum(fn($o) => $o->stockOpnameDetails->count()) }}</p></div><div class="bg-white/20 rounded-xl p-3"><svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div></div></div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2"><span class="text-sm text-gray-600">Tampilkan</span><select wire:model.live="perPage" class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm"><option value="5">5</option><option value="10">10</option><option value="25">25</option><option value="50">50</option></select><span class="text-sm text-gray-600">data</span></div>
                    <select wire:model.live="filterStatus" class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm"><option value="">Semua Status</option>@foreach ($this->statuses as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select>
                </div>
                <div class="relative w-full md:w-80"><input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kode opname..." class="w-full pl-4 pr-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm"></div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="opnameTable">
                <thead><tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200"><th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th><th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kode</th><th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th><th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Lokasi</th><th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Total Item</th><th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th><th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Catatan</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($opnames as $index => $opname)
                        <tr class="hover:bg-emerald-50/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $opnames->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap"><span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700">{{ $opname->code }}</span></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $opname->opname_date ? \Carbon\Carbon::parse($opname->opname_date)->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4"><div class="font-medium text-gray-900">{{ $opname->regionalPolice->name ?? $opname->policeStation->name ?? '-' }}</div></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center"><span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700">{{ $opname->stockOpnameDetails->count() }} item</span></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($opname->status === 'completed')<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ $this->statuses[$opname->status] }}</span>
                                @elseif($opname->status === 'approved')<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ $this->statuses[$opname->status] }}</span>
                                @else<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $this->statuses[$opname->status] ?? $opname->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $opname->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-10 text-center"><div class="flex flex-col items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg><p class="text-gray-500 text-lg font-medium">Tidak ada data stock opname</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">{{ $opnames->links() }}</div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/report-export.js') }}"></script>
@endpush
