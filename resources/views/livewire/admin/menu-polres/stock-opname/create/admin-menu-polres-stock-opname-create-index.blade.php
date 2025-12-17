<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">Buat Stock Opname</h1>
                <p class="text-gray-500 mt-1">Buat stock opname baru untuk Polres</p>
            </div>
            <div>
                <a href="{{ route('menu-polres.stock-opname') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 rounded-lg">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-lg">
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <form wire:submit.prevent="save">
        <!-- Form Section -->
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-6 mb-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Stock Opname</h2>

            <!-- Opname Date -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Opname <span
                        class="text-red-500">*</span></label>
                <input type="date" wire:model="opname_date"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                @error('opname_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                <textarea wire:model="notes" rows="3"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                    placeholder="Catatan tambahan (opsional)"></textarea>
            </div>

            <!-- Load Stock Button -->
            <div>
                <button type="button" wire:click="loadStock"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg shadow-green-500/30 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Load Stock
                </button>
                <p class="text-xs text-gray-500 mt-2">Klik untuk memuat semua stock dari Polres Anda</p>
            </div>
        </div>

        <!-- Stock Details Table -->
        @if ($stocksLoaded && !empty($stockDetails))
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-4">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900">Detail Stock Opname</h2>
                    <p class="text-sm text-gray-600 mt-1">Total: <span
                            class="font-semibold">{{ count($stockDetails) }}</span> item</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Detail</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Rak</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">No. Seri</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">System Qty
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Physical
                                    Qty <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Difference
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($stockDetails as $index => $detail)
                                <tr class="hover:bg-blue-50/50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $detail['type_name'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $detail['type_detail_name'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $detail['rack_name'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        @if ($detail['code'])
                                            {{ Str::ucfirst($detail['code']) }}
                                            {{ $detail['number_serial_first'] }} -
                                            {{ $detail['number_serial_second'] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="text-sm font-semibold text-gray-900">{{ number_format($detail['system_quantity'], 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01"
                                            wire:model.lazy="stockDetails.{{ $index }}.physical_quantity"
                                            class="w-24 px-2 py-1 text-center text-sm rounded border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                        @error("stockDetails.{$index}.physical_quantity")
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $diff = $detail['difference'];
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold
                                            @if ($diff > 0) bg-green-100 text-green-700
                                            @elseif($diff < 0) bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" wire:model="stockDetails.{{ $index }}.notes"
                                            class="w-full px-2 py-1 text-sm rounded border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                            placeholder="Catatan (opsional)">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('menu-polres.stock-opname') }}"
                    class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg shadow-blue-500/30 transition-all duration-200">
                    Simpan Stock Opname
                </button>
            </div>
        @endif
    </form>
</div>
