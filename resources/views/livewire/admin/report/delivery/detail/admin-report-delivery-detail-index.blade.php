<div>
    <!-- Header with Back Button -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.report.delivery.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-cyan-600">
                        Detail Pengiriman
                    </h1>
                    <p class="text-gray-500 mt-1">{{ $this->delivery['no_surat_jalan'] }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z"
                            clip-rule="evenodd" />
                    </svg>
                    Cetak Surat Jalan
                </button>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Informasi Pengiriman -->
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                </svg>
                Informasi Pengiriman
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">No Surat Jalan</span>
                    <span class="font-semibold text-gray-800">{{ $this->delivery['no_surat_jalan'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal Kirim</span>
                    <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($this->delivery['tanggal_kirim'])->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal Terima</span>
                    <span class="font-semibold text-gray-800">
                        @if($this->delivery['tanggal_terima'])
                            {{ \Carbon\Carbon::parse($this->delivery['tanggal_terima'])->format('d M Y') }}
                        @else
                            <span class="text-amber-500">Belum diterima</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    @if($this->delivery['status'] === 'Terkirim')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            {{ $this->delivery['status'] }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                            {{ $this->delivery['status'] }}
                        </span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Kurir</span>
                    <span class="font-semibold text-gray-800">{{ $this->delivery['kurir'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Kendaraan</span>
                    <span class="font-semibold text-gray-800">{{ $this->delivery['kendaraan'] }} ({{ $this->delivery['jenis_kendaraan'] }})</span>
                </div>
            </div>
        </div>

        <!-- Informasi Tujuan -->
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
                Informasi Tujuan
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Tujuan</span>
                    <span class="font-semibold text-gray-800">{{ $this->delivery['tujuan'] }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Alamat</span>
                    <p class="font-semibold text-gray-800 mt-1">{{ $this->delivery['alamat'] }}</p>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Penerima</span>
                    <span class="font-semibold text-gray-800">{{ $this->delivery['penerima'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Telepon</span>
                    <span class="font-semibold text-gray-800">{{ $this->delivery['telepon_penerima'] }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Catatan</span>
                    <p class="font-medium text-gray-700 mt-1 bg-gray-50 rounded-lg p-3 text-sm">{{ $this->delivery['catatan'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Barang -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                    <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
                Detail Barang Dikirim
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kode Barang</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Jumlah Kirim</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Jumlah Terima</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Kondisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($this->deliveryItems as $index => $item)
                        <tr class="hover:bg-cyan-50/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700">
                                    {{ $item['kode_barang'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                {{ $item['nama_barang'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-cyan-100 text-cyan-700">
                                    {{ number_format($item['jumlah_kirim']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-green-100 text-green-700">
                                    {{ number_format($item['jumlah_terima']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $item['satuan'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($item['kondisi'] === 'Baik')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        {{ $item['kondisi'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        {{ $item['kondisi'] }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 border-t border-gray-200">
                        <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-700">Total</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-cyan-100 text-cyan-700">
                                {{ number_format($this->deliveryItems->sum('jumlah_kirim')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-green-100 text-green-700">
                                {{ number_format($this->deliveryItems->sum('jumlah_terima')) }}
                            </span>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
