<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">Laporan Material Subsidi</h1>
                <p class="text-gray-500 mt-1">Laporan rekapitulasi data subsidi material SBST</p>
            </div>
        </div>
    </div>

    <!-- Filters Outside Table -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-6 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @if(Auth::user()->hasRole('Admin'))
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Polda</label>
                    <select wire:model.live="regionalPoliceId" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500">
                        <option value="">Semua Polda</option>
                        @foreach ($regionalPolices as $police)
                            <option value="{{ $police->id }}">{{ $police->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Material</label>
                <select wire:model.live="typeId" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500">
                    <option value="">Semua Material</option>
                    @foreach ($allTypes as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Material Detail</label>
                <select wire:model.live="typeDetailId" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500">
                    <option value="">Semua Material Detail</option>
                    @foreach ($typeDetails as $td)
                        <option value="{{ $td->id }}">{{ $td->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select wire:model.live="filterStatus" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="confirmed">Confirmed</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-100">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" wire:model.live="startDate" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="date" wire:model.live="endDate" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <!-- Search Header -->
        <div class="p-4 border-b border-gray-100 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Tampilkan</span>
                <select wire:model.live="perPage" class="px-3 py-2 rounded-lg border border-gray-200 text-sm">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="text-sm text-gray-600">data</span>
            </div>

            <div class="relative w-80">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kode/penerima..."
                    class="w-full pl-4 pr-4 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <th class="px-4 py-4 w-12 text-center">No</th>
                        <th class="px-4 py-4">Kode Transaksi</th>
                        <th class="px-4 py-4">Polda</th>
                        <th class="px-4 py-4">Penerima</th>
                        <th class="px-4 py-4">Tanggal</th>
                        <th class="px-4 py-4">Material</th>
                        <th class="px-4 py-4">Material Detail</th>
                        <th class="px-4 py-4 text-right">Quantity</th>
                        <th class="px-4 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($subsidyDetails as $index => $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-sm text-gray-500 text-center">
                                {{ $subsidyDetails->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-mono text-blue-600">
                                {{ $item->materialSubsidy->code ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->materialSubsidy->regionalPolice->name ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->materialSubsidy->recipient_name ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->materialSubsidy->subsidy_date ? $item->materialSubsidy->subsidy_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->type->name ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $item->typeDetail->name ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900">
                                {{ number_format($item->quantity, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                @if($item->materialSubsidy->status === 'confirmed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Confirmed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Draft
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-10 text-center text-gray-500">
                                Tidak ada data laporan subsidi material
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $subsidyDetails->links() }}
        </div>
    </div>
</div>
