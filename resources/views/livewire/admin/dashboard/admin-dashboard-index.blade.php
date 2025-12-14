<div>
    <!-- Page Header -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 lg:text-3xl">Dashboard</h1>
            <p class="mt-1 text-gray-500">Selamat datang kembali! Berikut ringkasan sistem hari ini.</p>
        </div>
        <div class="flex gap-2">
            <button
                class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-medium text-blue-700 shadow-sm hover:bg-blue-50 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
            <button
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-500/30 hover:from-blue-700 hover:to-cyan-700 transition-all">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Permintaan
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Total Distribusi -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-6 shadow-xl shadow-blue-500/20 transition-transform hover:scale-[1.02]">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-100">Total Distribusi</p>
                        <p class="mt-2 text-4xl font-bold text-white">1,234</p>
                    </div>
                    <div class="rounded-2xl bg-white/20 p-3">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-green-400/20 px-2 py-0.5 text-xs font-medium text-green-100">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        12%
                    </span>
                    <span class="text-sm text-blue-200">dari bulan lalu</span>
                </div>
            </div>
        </div>

        <!-- Dalam Pengiriman -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-500 to-cyan-600 p-6 shadow-xl shadow-cyan-500/20 transition-transform hover:scale-[1.02]">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-cyan-100">Dalam Pengiriman</p>
                        <p class="mt-2 text-4xl font-bold text-white">48</p>
                    </div>
                    <div class="rounded-2xl bg-white/20 p-3">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-green-400"></span>
                    </span>
                    <span class="text-sm text-cyan-100">Sedang dalam perjalanan</span>
                </div>
            </div>
        </div>

        <!-- Stok Polda -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 shadow-xl shadow-indigo-500/20 transition-transform hover:scale-[1.02]">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-indigo-100">Stok Polda</p>
                        <p class="mt-2 text-4xl font-bold text-white">8,567</p>
                    </div>
                    <div class="rounded-2xl bg-white/20 p-3">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-indigo-200">Unit tersedia di gudang</span>
                </div>
            </div>
        </div>

        <!-- Diterima Hari Ini -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 shadow-xl shadow-emerald-500/20 transition-transform hover:scale-[1.02]">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-emerald-100">Diterima Hari Ini</p>
                        <p class="mt-2 text-4xl font-bold text-white">23</p>
                    </div>
                    <div class="rounded-2xl bg-white/20 p-3">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm text-emerald-100">Semua dikonfirmasi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
        <!-- Line Chart -->
        <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Distribusi Bulanan</h3>
                    <p class="text-sm text-gray-500">Statistik pengiriman tahun 2024</p>
                </div>
                <span
                    class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    +24.5%
                </span>
            </div>
            <div x-data="{
                init() {
                    new Chart(this.$refs.lineChart, {
                        type: 'line',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                            datasets: [{
                                label: 'Distribusi',
                                data: [65, 78, 90, 81, 95, 110, 125, 140, 130, 145, 160, 175],
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#2563eb',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            }">
                <canvas x-ref="lineChart" style="height: 280px;"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart -->
        <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Status Distribusi</h3>
                    <p class="text-sm text-gray-500">Persentase status hari ini</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="flex-1" x-data="{
                    init() {
                        new Chart(this.$refs.doughnutChart, {
                            type: 'doughnut',
                            data: {
                                labels: ['Diterima', 'Dalam Perjalanan', 'Diproses', 'Pending'],
                                datasets: [{
                                    data: [45, 25, 20, 10],
                                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#6b7280'],
                                    borderWidth: 0,
                                    spacing: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%',
                                plugins: { legend: { display: false } }
                            }
                        });
                    }
                }">
                    <canvas x-ref="doughnutChart" style="height: 200px; width: 150px;"></canvas>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                        <span class="text-sm text-gray-600">Diterima</span>
                        <span class="ml-auto font-semibold text-gray-900">45%</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="h-3 w-3 rounded-full bg-amber-500"></span>
                        <span class="text-sm text-gray-600">Dalam Perjalanan</span>
                        <span class="ml-auto font-semibold text-gray-900">25%</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="h-3 w-3 rounded-full bg-blue-500"></span>
                        <span class="text-sm text-gray-600">Diproses</span>
                        <span class="ml-auto font-semibold text-gray-900">20%</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="h-3 w-3 rounded-full bg-gray-500"></span>
                        <span class="text-sm text-gray-600">Pending</span>
                        <span class="ml-auto font-semibold text-gray-900">10%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
        <!-- Recent Distributions Table -->
        <div class="lg:col-span-2 rounded-2xl border border-blue-100 bg-white shadow-lg overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Distribusi Terbaru</h3>
                    <p class="text-sm text-gray-500">5 pengiriman terakhir</p>
                </div>
                <a href="#"
                    class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                    Lihat Semua
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                No. Resi</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Rute</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Tanggal</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="font-mono text-sm font-semibold text-blue-600">SBST-2024-001234</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">Polda Jatim → Polres Surabaya
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">08 Des 2024</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"></span>
                                    Dalam Perjalanan
                                </span>
                            </td>
                        </tr>
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="font-mono text-sm font-semibold text-blue-600">SBST-2024-001233</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">Polda Jatim → Polres Malang
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">08 Des 2024</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Diterima
                                </span>
                            </td>
                        </tr>
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="font-mono text-sm font-semibold text-blue-600">SBST-2024-001232</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">Polda Jatim → Polres Sidoarjo
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">07 Des 2024</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                    Diproses
                                </span>
                            </td>
                        </tr>
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="font-mono text-sm font-semibold text-blue-600">SBST-2024-001231</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">Polda Jatim → Polres Gresik
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">07 Des 2024</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Diterima
                                </span>
                            </td>
                        </tr>
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="font-mono text-sm font-semibold text-blue-600">SBST-2024-001230</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">Polda Jatim → Polres Kediri
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">06 Des 2024</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Diterima
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                <div class="grid grid-cols-2 gap-3">
                    <button
                        class="group flex flex-col items-center gap-3 rounded-xl border-2 border-blue-100 bg-gradient-to-b from-blue-50 to-white p-4 transition-all hover:border-blue-300 hover:shadow-lg">
                        <div
                            class="rounded-xl bg-blue-100 p-3 text-blue-600 transition-transform group-hover:scale-110">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Buat Permintaan</span>
                    </button>
                    <button
                        class="group flex flex-col items-center gap-3 rounded-xl border-2 border-cyan-100 bg-gradient-to-b from-cyan-50 to-white p-4 transition-all hover:border-cyan-300 hover:shadow-lg">
                        <div
                            class="rounded-xl bg-cyan-100 p-3 text-cyan-600 transition-transform group-hover:scale-110">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Scan QR</span>
                    </button>
                    <button
                        class="group flex flex-col items-center gap-3 rounded-xl border-2 border-indigo-100 bg-gradient-to-b from-indigo-50 to-white p-4 transition-all hover:border-indigo-300 hover:shadow-lg">
                        <div
                            class="rounded-xl bg-indigo-100 p-3 text-indigo-600 transition-transform group-hover:scale-110">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Laporan</span>
                    </button>
                    <button
                        class="group flex flex-col items-center gap-3 rounded-xl border-2 border-emerald-100 bg-gradient-to-b from-emerald-50 to-white p-4 transition-all hover:border-emerald-300 hover:shadow-lg">
                        <div
                            class="rounded-xl bg-emerald-100 p-3 text-emerald-600 transition-transform group-hover:scale-110">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Konfirmasi</span>
                    </button>
                </div>
            </div>

            <!-- Bar Chart -->
            <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Stok per Polres</h3>
                    <p class="text-sm text-gray-500">Top 5 lokasi</p>
                </div>
                <div x-data="{
                    init() {
                        new Chart(this.$refs.barChart, {
                            type: 'bar',
                            data: {
                                labels: ['Surabaya', 'Malang', 'Sidoarjo', 'Gresik', 'Kediri'],
                                datasets: [{
                                    data: [1200, 950, 800, 650, 500],
                                    backgroundColor: ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
                                    borderRadius: 8
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                                    x: { grid: { display: false } }
                                }
                            }
                        });
                    }
                }">
                    <canvas x-ref="barChart" style="height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Flow -->
    <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Alur Proses Distribusi</h3>
            <p class="text-sm text-gray-500">Langkah-langkah dalam sistem SBST</p>
        </div>
        <div class="overflow-x-auto">
            <div class="flex items-center justify-between gap-4" style="min-width: 700px;">
                <!-- Step 1 -->
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/30">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white ring-2 ring-white">
                            1</div>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gray-900">Buat Permintaan</p>
                    <span class="mt-1 rounded-full bg-blue-100 px-3 py-0.5 text-xs font-medium text-blue-700">Admin
                        Polda</span>
                </div>

                <div class="h-1 flex-1 rounded-full bg-gradient-to-r from-blue-400 to-cyan-400"></div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 to-cyan-600 text-white shadow-lg shadow-cyan-500/30">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-cyan-600 text-xs font-bold text-white ring-2 ring-white">
                            2</div>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gray-900">Cek Stok</p>
                    <span
                        class="mt-1 rounded-full bg-cyan-100 px-3 py-0.5 text-xs font-medium text-cyan-700">Sistem</span>
                </div>

                <div class="h-1 flex-1 rounded-full bg-gradient-to-r from-cyan-400 to-indigo-400"></div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-500/30">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white ring-2 ring-white">
                            3</div>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gray-900">Generate QR</p>
                    <span
                        class="mt-1 rounded-full bg-indigo-100 px-3 py-0.5 text-xs font-medium text-indigo-700">Sistem</span>
                </div>

                <div class="h-1 flex-1 rounded-full bg-gradient-to-r from-indigo-400 to-amber-400"></div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-500/30">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-amber-600 text-xs font-bold text-white ring-2 ring-white">
                            4</div>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gray-900">Scan & Kirim</p>
                    <span class="mt-1 rounded-full bg-amber-100 px-3 py-0.5 text-xs font-medium text-amber-700">Admin
                        Polda</span>
                </div>

                <div class="h-1 flex-1 rounded-full bg-gradient-to-r from-amber-400 to-emerald-400"></div>

                <!-- Step 5 -->
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/30">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold text-white ring-2 ring-white">
                            5</div>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gray-900">Konfirmasi</p>
                    <span
                        class="mt-1 rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-medium text-emerald-700">Admin
                        Polres</span>
                </div>
            </div>
        </div>
    </div>
</div>
