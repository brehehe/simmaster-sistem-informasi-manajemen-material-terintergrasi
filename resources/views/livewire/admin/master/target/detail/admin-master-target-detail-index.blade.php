<div>
    <!-- Header -->
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">
                    {{ $targetId ? 'Detail Target' : 'Buat Target' }}
                </h1>
                <p class="text-gray-500 mt-1">Kelola target berdasarkan polda/polres dan material</p>
            </div>
            <a href="{{ route('master.target') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Target Info -->
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Target</h2>

                <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tahun <span class="text-red-500">*</span>
                        </label>
                        <input type="number" wire:model.live="year" min="2000" max="2100"
                            class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white">
                        @error('year')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Target Matrix -->
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Detail Target</h2>
                    <span class="text-sm text-gray-500">Isikan target sesuai format Excel</span>
                </div>

                @if (count($types) === 0 || count($rows) === 0)
                    <div class="p-8 text-center text-gray-500">
                        Data polda/polres atau material belum tersedia.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                    <th
                                        class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                        No</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                        Ditlantas Polda</th>
                                    @foreach ($types as $type)
                                        <th
                                            class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                            {{ $type['name'] }}</th>
                                    @endforeach
                                    <th
                                        class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                                        Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($rows as $index => $row)
                                    <tr class="hover:bg-blue-50/50 transition-colors">
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            <div class="flex items-center gap-2">
                                                @if ($row['level'] === 'station')
                                                    <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                                                @else
                                                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                                @endif
                                                <span class="{{ $row['level'] === 'station' ? 'pl-1' : '' }}">
                                                    {{ $row['label'] }}
                                                </span>
                                            </div>
                                        </td>
                                        @foreach ($types as $type)
                                            <td class="px-2 py-2">
                                                <input type="number" step="0.01" min="0"
                                                    wire:model="matrix.{{ $row['key'] }}.{{ $type['id'] }}"
                                                    class="w-32 text-right px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white">
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-700">
                                            {{ number_format($this->rowTotal($row['key']), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-50 border-t border-gray-200">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-700" colspan="2">Total</td>
                                    @foreach ($types as $type)
                                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-700">
                                            {{ number_format($this->columnTotal($type['id']), 0, ',', '.') }}
                                        </td>
                                    @endforeach
                                    <td class="px-4 py-3 text-sm text-right font-bold text-blue-700">
                                        {{ number_format($this->grandTotal(), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold py-2.5 px-6 rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M17 16a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h2l2-2h4l2 2h2a2 2 0 012 2v8z" />
                    <path d="M12 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span wire:loading.remove wire:target="save">Simpan Target</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
