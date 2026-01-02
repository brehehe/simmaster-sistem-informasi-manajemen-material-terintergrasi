<div>
    <!-- Header -->
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">Pengiriman Material</h1>
                <p class="text-gray-500 mt-1">Kelola pengiriman material dari Polda ke Polres</p>
            </div>
            <a href="{{ route('menu-polda.material-shipment.create') }}" wire:navigate
                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Tambah Data
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
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

    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <!-- Search & Filter -->
        <div class="p-4 border-b border-gray-100">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Tampilkan</span>
                    <select wire:model.live="perPage"
                        class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-600">data</span>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto">
                    <!-- Status Filter -->
                    <select wire:model.live="statusFilter"
                        class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="shipped">Terkirim</option>
                        <option value="received">Diterima</option>
                    </select>

                    <!-- Polres Filter -->
                    @if (count($policeStations) > 0)
                        <select wire:model.live="polresFilter"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                            <option value="">Semua Polres</option>
                            @foreach ($policeStations as $ps)
                                <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                            @endforeach
                        </select>
                    @endif

                    <!-- Date Range -->
                    <div class="flex items-center gap-2">
                        <input wire:model.live.debounce.300ms="startDate" type="date"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                        <span class="text-gray-400">-</span>
                        <input wire:model.live.debounce.300ms="endDate" type="date"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                    </div>

                    <!-- Search -->
                    <div class="relative w-full sm:w-80">
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Cari kode pengiriman..."
                            class="w-full pl-4 pr-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kode
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tujuan
                            Polres</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Items
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($shipments as $index => $shipment)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $shipments->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-blue-600">{{ $shipment->code }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($shipment->shipment_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $shipment->receiverPoliceStation->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $shipment->materialShipmentDetails->count() }}
                                        Items</span>
                                    <span class="text-xs text-gray-500">Total:
                                        {{ number_format($shipment->materialShipmentDetails->sum('quantity'), 0) }}
                                        unit</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($shipment->status === 'draft')
                                    <span
                                        class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                        🟡 Draft
                                    </span>
                                @elseif($shipment->status === 'shipped')
                                    <span
                                        class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                        🔵 Terkirim
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                        🟢 Diterima
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Print PDF Button (for all status) -->
                                    <button
                                        onclick='printShippingDocument(@json($shipment))'
                                        class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                                        title="Cetak PDF">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    @if ($shipment->status === 'draft')
                                        <a href="{{ route('menu-polda.material-shipment.edit', ['id' => $shipment->id]) }}"
                                            wire:navigate
                                            class="p-2 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-colors"
                                            title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        <button wire:click="openDeleteModal('{{ $shipment->id }}')"
                                            class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                            title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @else
                                        <a href="{{ route('menu-polda.material-shipment.edit', $shipment->id) }}"
                                            wire:navigate
                                            class="p-2 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 transition-colors"
                                            title="View">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd"
                                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-4"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">Tidak ada pengiriman</p>
                                <p class="text-gray-400 text-sm mt-1">Buat pengiriman baru dengan klik tombol "Buat
                                    Pengiriman"</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($shipments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $shipments->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function printShippingDocument(shipment) {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF();

                // Helper function to format date
                const formatDate = (dateString) => {
                    const date = new Date(dateString);
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                    return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
                };

                // Generate QRCode dengan settingan optimal untuk scanning
                const qrDiv = document.createElement('div');
                const qr = new QRCode(qrDiv, {
                    text: shipment.code,
                    width: 400,
                    height: 400,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.M  // Medium level untuk balance antara data dan error correction
                });

                // Get canvas setelah QR generated
                const canvas = qrDiv.querySelector('canvas');
                const qrImg = canvas.toDataURL('image/png');

                // PDF Layout
                const pageWidth = doc.internal.pageSize.getWidth();
                let yPos = 20;

                // === HEADER ===
                doc.setFontSize(16);
                doc.setFont('helvetica', 'bold');
                doc.text('SURAT PENGIRIMAN MATERIAL', pageWidth / 2, yPos, {
                    align: 'center'
                });

                yPos += 7;
                doc.setFontSize(10);
                doc.setFont('helvetica', 'normal');
                doc.text('SISTEM INFORMASI MANAJEMEN MATERIAL SBST', pageWidth / 2, yPos, {
                    align: 'center'
                });

                yPos += 10;
                doc.setDrawColor(59, 130, 246);
                doc.setLineWidth(0.5);
                doc.line(20, yPos, pageWidth - 20, yPos);

                // === SHIPPING INFO (LEFT) & QR CODE (RIGHT) SIDE BY SIDE ===
                yPos += 10;

                // Info pengiriman di kiri
                const infoX = 20;
                let infoY = yPos;

                doc.setFont('helvetica', 'bold');
                doc.setFontSize(11);
                doc.text('INFORMASI PENGIRIMAN', infoX, infoY);

                infoY += 2;
                doc.setDrawColor(59, 130, 246);
                doc.line(infoX, infoY, infoX + 60, infoY);

                infoY += 6;
                doc.setFontSize(9);
                doc.setFont('helvetica', 'normal');

                const infoData = [
                    ['Tanggal', ': ' + formatDate(shipment.shipment_date)],
                    ['Pengirim', ': ' + (shipment.sender_regional_police?.name || '-')],
                    ['Penerima', ': ' + (shipment.receiver_police_station?.name || '-')],
                    ['Status', ': ' + (shipment.status === 'draft' ? 'Draft' : shipment.status === 'shipped' ?
                        'Terkirim' : 'Diterima')]
                ];

                infoData.forEach(([label, value]) => {
                    doc.setFont('helvetica', 'bold');
                    doc.text(label, infoX, infoY);
                    doc.setFont('helvetica', 'normal');
                    doc.text(value, infoX + 25, infoY);
                    infoY += 5;
                });

                // QRCode di kanan
                const qrSize = 40; // Increased from 35 to 40 for better scanning
                const qrX = pageWidth - qrSize - 25;
                doc.addImage(qrImg, 'PNG', qrX, yPos, qrSize, qrSize);

                // Kode dibawah QR
                doc.setFontSize(8);
                doc.setFont('helvetica', 'bold');
                doc.text(shipment.code, qrX + (qrSize / 2), yPos + qrSize + 4, {
                    align: 'center'
                });

                // === ITEMS TABLE ===
                yPos = Math.max(yPos + qrSize + 10, infoY + 5); // Adjust yPos to be below both QR and info
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(11);
                doc.text('DAFTAR MATERIAL', 20, yPos);

                yPos += 2;
                doc.setDrawColor(59, 130, 246);
                doc.line(20, yPos, 70, yPos);

                yPos += 3;

                // Prepare table data
                const tableData = [];
                shipment.material_shipment_details.forEach((item, index) => {
                    tableData.push([
                        (index + 1).toString(),
                        item.type?.name || '-',
                        item.type_detail?.name || '-',
                        item.code + ' ' + item.number_serial_first + ' - ' + item.number_serial_second || '-',
                        item.quantity.toString() + ' unit'
                    ]);
                });

                doc.autoTable({
                    startY: yPos,
                    head: [
                        ['No', 'Material', 'Detail Material', 'Kode Serial', 'Jumlah']
                    ],
                    body: tableData,
                    theme: 'grid',
                    headStyles: {
                        fillColor: [59, 130, 246],
                        textColor: 255,
                        fontSize: 9,
                        fontStyle: 'bold',
                        halign: 'center'
                    },
                    bodyStyles: {
                        fontSize: 8,
                        textColor: 50
                    },
                    columnStyles: {
                        0: {
                            halign: 'center',
                            cellWidth: 10
                        },
                        1: {
                            cellWidth: 40
                        },
                        2: {
                            cellWidth: 45
                        },
                        3: {
                            cellWidth: 40
                        },
                        4: {
                            halign: 'center',
                            cellWidth: 25
                        }
                    },
                    margin: {
                        left: 20,
                        right: 20
                    }
                });

                // === SUMMARY ===
                yPos = doc.lastAutoTable.finalY + 8;
                doc.setFontSize(9);
                doc.setFont('helvetica', 'bold');
                doc.text('Total Item: ' + shipment.material_shipment_details.length + ' jenis material', 20, yPos);
                yPos += 4;
                const totalQty = shipment.material_shipment_details.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
                doc.text('Total Quantity: ' + totalQty.toLocaleString('id-ID') + ' unit', 20, yPos);

                // === SIGNATURE SECTION ===
                yPos += 15;

                // Check if there's enough space, otherwise add new page
                if (yPos > 250) {
                    doc.addPage();
                    yPos = 20;
                }

                doc.setFont('helvetica', 'bold');
                doc.setFontSize(9);

                // Pengirim
                doc.text('Pengirim,', 30, yPos);
                // Penerima
                doc.text('Penerima,', pageWidth - 60, yPos);

                yPos += 20;

                // Signature lines
                doc.line(25, yPos, 75, yPos);
                doc.line(pageWidth - 65, yPos, pageWidth - 15, yPos);

                yPos += 5;
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(8);
                doc.text('Nama & Tanda Tangan', 30, yPos);
                doc.text('Nama & Tanda Tangan', pageWidth - 60, yPos);

                // === FOOTER ===
                const pageCount = doc.internal.getNumberOfPages();
                for (let i = 1; i <= pageCount; i++) {
                    doc.setPage(i);
                    doc.setFontSize(8);
                    doc.setTextColor(150);
                    doc.setFont('helvetica', 'italic');
                    doc.text(
                        'Dicetak pada: ' + new Date().toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }),
                        pageWidth / 2,
                        doc.internal.pageSize.getHeight() - 10, {
                            align: 'center'
                        }
                    );
                }

                // Save PDF
                doc.save(`Surat_Pengiriman_${shipment.code}.pdf`);
            }
        </script>
    @endpush

    <!-- Delete Modal -->
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
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Pengiriman</h3>
                        <p class="text-gray-500">Apakah Anda yakin ingin menghapus pengiriman ini? Tindakan ini tidak
                            dapat dibatalkan.</p>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button wire:click="closeModal"
                            class="flex-1 px-4 py-3 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200">
                            Batal
                        </button>
                        <button wire:click="delete"
                            class="flex-1 px-4 py-3 text-sm font-semibold text-white bg-gradient-to-r from-red-500 to-red-600 rounded-xl hover:from-red-600 hover:to-red-700 shadow-lg shadow-red-500/30 transition-all duration-200">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
