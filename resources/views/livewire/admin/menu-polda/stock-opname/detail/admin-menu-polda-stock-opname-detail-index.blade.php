<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">Detail Stock Opname</h1>
                <p class="text-gray-500 mt-1">{{ $opname->code }}</p>
            </div>
            <div>
                <a href="{{ route('menu-polda.stock-opname.create') }}"
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

    <!-- Header Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <!-- Left Card -->
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Umum</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Kode:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $opname->code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Tanggal:</span>
                    <span class="text-sm text-gray-900">{{ $opname->opname_date->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Status:</span>
                    @if ($opname->status === 'draft')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                            Draft
                        </span>
                    @elseif($opname->status === 'completed')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                            Completed
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            Approved
                        </span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Owner:</span>
                    @if ($opname->regional_police_id)
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-700">
                                POLDA
                            </span>
                            <span class="text-sm text-gray-900">{{ $opname->regionalPolice->name }}</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">
                                POLRES
                            </span>
                            <span class="text-sm text-gray-900">{{ $opname->policeStation->name }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Card -->
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Approval Info</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Diperiksa Oleh:</span>
                    <span class="text-sm text-gray-900">{{ $opname->checkedByUser->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Disetujui Oleh:</span>
                    <span class="text-sm text-gray-900">{{ $opname->approvedByUser->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Tanggal Approval:</span>
                    <span
                        class="text-sm text-gray-900">{{ $opname->approved_at ? $opname->approved_at->format('d M Y H:i') : '-' }}</span>
                </div>
            </div>

            @if ($opname->notes)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <span class="text-sm font-semibold text-gray-700">Catatan:</span>
                    <p class="text-sm text-gray-600 mt-1">{{ $opname->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Summary Card -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl border border-blue-200 p-6 mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-blue-900">Total Difference</h3>
                <p class="text-3xl font-bold text-blue-700 mt-1">
                    @php
                        $totalDiff = $opname->stockOpnameDetails->sum('difference');
                    @endphp
                    {{ $totalDiff > 0 ? '+' : '' }}{{ number_format($totalDiff, 0, ',', '.') }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-blue-800">Total Items</p>
                <p class="text-2xl font-bold text-blue-900">{{ $opname->stockOpnameDetails->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Stock Opname Details Table -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-4">
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-900">Detail Stock Opname</h2>
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
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">System Qty</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Physical Qty</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Difference</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($opname->stockOpnameDetails as $index => $detail)
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $detail->type->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $detail->typeDetail->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $detail->rack->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if ($detail->code)
                                    {{ Str::ucfirst($detail->code) }}
                                    {{ $detail->number_serial_first }} - {{ $detail->number_serial_second }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="text-sm font-semibold text-gray-900">{{ number_format($detail->system_quantity, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="text-sm font-semibold text-gray-900">{{ number_format($detail->physical_quantity, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $diff = $detail->difference;
                                @endphp
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold
                                    @if ($diff > 0) bg-green-100 text-green-700
                                    @elseif($diff < 0) bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $detail->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-3">
        @if ($opname->status === 'draft')
            <a href="{{ route('menu-polda.stock-opname.edit', $opname->id) }}"
                class="px-6 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold rounded-lg transition-colors">
                Edit Stock Opname
            </a>
            <button wire:click="markAsCompleted"
                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg shadow-green-500/30 transition-all duration-200">
                Mark as Completed
            </button>
        @elseif($opname->status === 'completed')
            <button wire:click="openApproveModal"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg shadow-blue-500/30 transition-all duration-200">
                Approve & Adjust Stock
            </button>
        @endif
    </div>

    <!-- Approve Confirmation Modal -->
    @if ($showApproveModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Konfirmasi Approval</h3>
                <p class="text-sm text-gray-600 text-center mb-6">
                    Approve stock opname akan menyesuaikan stock sesuai dengan physical quantity.
                    Tindakan ini tidak dapat dibatalkan. Lanjutkan?
                </p>
                <div class="flex gap-3">
                    <button wire:click="closeModal"
                        class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                        Batal
                    </button>
                    <button wire:click="approve"
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                        Approve
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
