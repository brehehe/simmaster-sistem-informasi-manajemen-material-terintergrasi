<div>
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-red-600">Laporan Material Rusak</h1>
                <p class="text-gray-500 mt-1">Monitoring material yang mengalami kerusakan</p>
            </div>
            <div class="flex gap-2">
                <button wire:click="exportExcel"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-emerald-500 hover:from-green-700 hover:to-emerald-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-green-500/30 transition-all duration-300 transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    Export Excel
                </button>
                <button wire:click="exportPdf"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-red-600 to-rose-500 hover:from-red-700 hover:to-rose-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-red-500/30 transition-all duration-300 transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" /></svg>
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl p-5 text-white shadow-lg shadow-red-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Total Kerusakan</p>
                    <p class="text-3xl font-bold mt-1">{{ $this->totalDamages }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl p-5 text-white shadow-lg shadow-orange-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Total Unit Rusak</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($this->totalUnits) }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl p-5 text-white shadow-lg shadow-yellow-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Hari Ini</p>
                    <p class="text-3xl font-bold mt-1">{{ $this->todayDamages }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
             <!-- Filter Card -->
            <div class="bg-white rounded-xl mb-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @if(Auth::user()->hasRole('Admin'))
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Polda</label>
                            <div wire:ignore>
                                <select id="select-polda-damage" x-data x-init="
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
                                    @foreach ($this->regionalPolices as $polda)
                                        <option value="{{ $polda->id }}" @selected($regionalPoliceId == $polda->id)>{{ $polda->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Polres</label>
                            <div wire:ignore>
                                <select id="select-polres-damage" x-data x-init="
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
                                    @foreach ($this->policeStations as $polres)
                                        <option value="{{ $polres->id }}" @selected($policeStationId == $polres->id)>{{ $polres->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Material</label>
                        <div wire:ignore>
                            <select id="select-type-damage" x-data x-init="
                                const selectize = $($el).selectize({
                                    dropdownParent: 'body',
                                    allowClear: true,
                                    plugins: ['clear_button'],
                                    onChange: function(val) {
                                        @this.set('typeId', val);
                                    }
                                })[0].selectize;
                            "
                            placeholder="Semua Material">
                                <option value="">Semua Material</option>
                                @foreach ($this->allTypes as $t)
                                    <option value="{{ $t->id }}" @selected($typeId == $t->id)>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Detail</label>
                        <div wire:ignore wire:key="select-type-detail-wrapper-{{ $typeId }}">
                            <select id="select-type-detail-damage-{{ $typeId }}" x-data x-init="
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

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Status</label>
                        <div wire:ignore>
                            <select id="select-status-damage" x-data x-init="
                                const selectize = $($el).selectize({
                                    dropdownParent: 'body',
                                    allowClear: true,
                                    plugins: ['clear_button'],
                                    onChange: function(val) {
                                        @this.set('filterStatus', val);
                                    }
                                })[0].selectize;
                            "
                            placeholder="Semua Status">
                                <option value="">Semua Status</option>
                                @foreach ($this->statuses as $key => $label)
                                    <option value="{{ $key }}" @selected($filterStatus == $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mt-4">
                 <!-- Per Page Select (Left) -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Tampilkan</span>
                    <select wire:model.live="perPage" class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                        <option value="5">5</option><option value="10">10</option><option value="25">25</option><option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-600">data</span>
                </div>

                 <!-- Search & Date Filter (Right) -->
                 <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto">
                     <div class="flex items-center gap-2">
                        <input wire:model.live.debounce.300ms="startDate" type="date" placeholder="Dari Tanggal"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                        <span class="text-gray-400">-</span>
                        <input wire:model.live.debounce.300ms="endDate" type="date" placeholder="Sampai Tanggal"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                    </div>

                    <div class="relative w-full md:w-80">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kode atau lokasi..."
                            class="w-full pl-4 pr-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                    </div>
                 </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="materialDamageTable">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Material</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Detail Material</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($damages as $index => $detail)
                        <tr class="hover:bg-red-50/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $damages->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-700">{{ $detail->materialDamage->code }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $detail->materialDamage->date ? \Carbon\Carbon::parse($detail->materialDamage->date)->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4"><div class="font-medium text-gray-900">{{ $detail->materialDamage->regionalPolice->name ?? $detail->materialDamage->policeStation->name ?? '-' }}</div></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $detail->type->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $detail->typeDetail->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-orange-100 text-orange-700">{{ number_format($detail->quantity) }} unit</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($detail->materialDamage->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ $this->statuses[$detail->materialDamage->status] }}</span>
                                @elseif($detail->materialDamage->status === 'disposed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $this->statuses[$detail->materialDamage->status] }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ $this->statuses[$detail->materialDamage->status] ?? $detail->materialDamage->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $detail->materialDamage->description ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="p-10 text-center"><div class="flex flex-col items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg><p class="text-gray-500 text-lg font-medium">Tidak ada data material rusak</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">{{ $damages->links() }}</div>
    </div>
</div>

@push('scripts')
    {{-- <script src="{{ asset('js/report-export.js') }}"></script> --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('export-damage-pdf', (event) => {
                const { headers, data, fileName } = event[0];
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({ orientation: 'landscape' });

                doc.setFontSize(16);
                doc.text('Laporan Material Rusak', 14, 15);
                doc.setFontSize(10);
                doc.text(`Dicetak pada: ${new Date().toLocaleString('id-ID')}`, 14, 22);

                doc.autoTable({
                    head: [headers],
                    body: data,
                    startY: 30,
                    theme: 'grid',
                    styles: { fontSize: 8, cellPadding: 2 },
                    headStyles: { fillColor: [220, 38, 38] }, // Red-600 match
                });

                doc.save(fileName);
            });
        });
    </script>
@endpush
