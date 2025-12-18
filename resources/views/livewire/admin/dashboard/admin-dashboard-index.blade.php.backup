<div>
    <!-- Page Header -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 lg:text-3xl">Dashboard</h1>
            <p class="mt-1 text-gray-500">Visualisasi data dan statistik sistem manajemen material.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-medium text-blue-700 shadow-sm hover:bg-blue-50 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Total Penerimaan -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-6 shadow-xl shadow-blue-500/20 transition-transform hover:scale-[1.02]">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-100">Total Penerimaan</p>
                        <p class="mt-2 text-4xl font-bold text-white">{{ number_format($totalReceptions) }}</p>
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
                        {{ abs($percentageChange) }}%
                    </span>
                    <span class="text-sm text-blue-200">dari bulan lalu</span>
                </div>
            </div>
        </div>

        <!-- Stock Polda -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 shadow-xl shadow-indigo-500/20 transition-transform hover:scale-[1.02]">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-indigo-100">Stock Polda</p>
                        <p class="mt-2 text-4xl font-bold text-white">{{ number_format($totalStockPolda) }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/20 p-3">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-indigo-200">Total unit di Polda</span>
                </div>
            </div>
        </div>

        <!-- Stock Polres -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-500 to-cyan-600 p-6 shadow-xl shadow-cyan-500/20 transition-transform hover:scale-[1.02]">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-cyan-100">Stock Polres</p>
                        <p class="mt-2 text-4xl font-bold text-white">{{ number_format($totalStockPolres) }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/20 p-3">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-cyan-200">Total unit di Polres</span>
                </div>
            </div>
        </div>

        <!-- Penerimaan Hari Ini -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 shadow-xl shadow-emerald-500/20 transition-transform hover:scale-[1.02]">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-emerald-100">Penerimaan Hari Ini</p>
                        <p class="mt-2 text-4xl font-bold text-white">{{ $receptionsToday }}</p>
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
                    <span class="text-sm text-emerald-100">Material terdaftar</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
        <!-- Line Chart - Larger -->
        <div class="lg:col-span-2 rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Trend History Stock</h3>
                <p class="text-sm text-gray-500">Total quantity 12 bulan terakhir</p>
            </div>
            <div x-data="{
                init() {
                    new Chart(this.$refs.lineChart, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($historyStockTrend['labels']) !!},
                            datasets: [{
                                label: 'Quantity',
                                data: {!! json_encode($historyStockTrend['data']) !!},
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#2563eb',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true, position: 'top' },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: { size: 14 },
                                    bodyFont: { size: 13 }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f1f5f9' },
                                    ticks: { font: { size: 12 } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 12 } }
                                }
                            }
                        }
                    });
                }
            }">
                <canvas x-ref="lineChart" style="height: 350px;"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart -->
        <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Distribusi Stock</h3>
                <p class="text-sm text-gray-500">Polda vs Polres</p>
            </div>
            <div x-data="{
                init() {
                    new Chart(this.$refs.doughnutChart, {
                        type: 'doughnut',
                        data: {
                            labels: ['Stock Polda', 'Stock Polres'],
                            datasets: [{
                                data: [{{ $stockDistribution['polda_count'] }}, {{ $stockDistribution['polres_count'] }}],
                                backgroundColor: ['#2563eb', '#06b6d4'],
                                borderWidth: 0,
                                spacing: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: { padding: 15, font: { size: 12 } }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12
                                }
                            }
                        }
                    });
                }
            }">
                <canvas x-ref="doughnutChart" style="height: 250px;"></canvas>
            </div>
            <div class="mt-6 space-y-3">
                <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                    <div class="flex items-center gap-3">
                        <span class="h-4 w-4 rounded-full bg-blue-600"></span>
                        <span class="text-sm font-medium text-gray-700">Stock Polda</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-gray-900">
                            {{ number_format($stockDistribution['polda_count']) }}</div>
                        <div class="text-xs text-gray-500">{{ $stockDistribution['polda'] }}%</div>
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-cyan-50">
                    <div class="flex items-center gap-3">
                        <span class="h-4 w-4 rounded-full bg-cyan-500"></span>
                        <span class="text-sm font-medium text-gray-700">Stock Polres</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-gray-900">
                            {{ number_format($stockDistribution['polres_count']) }}</div>
                        <div class="text-xs text-gray-500">{{ $stockDistribution['polres'] }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Tables Section -->
    <div class="mb-8">
        <!-- Recent Distributions Table -->
        <div class="rounded-2xl border border-blue-100 bg-white shadow-lg overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Penerimaan Terbaru</h3>
                    <p class="text-sm text-gray-500">5 penerimaan terakhir</p>
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
                                Kode</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Lokasi</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Tanggal</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Jumlah Item</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentReceptions as $reception)
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        class="font-mono text-sm font-semibold text-blue-600">{{ $reception->code }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ $reception->regionalPolice?->name ?? 'N/A' }} →
                                    {{ $reception->policeStation?->name ?? 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ $reception->date?->locale('id')->format('d M Y') ?? 'N/A' }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        {{ $reception->receptionDetails->count() }} item
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                    Belum ada data penerimaan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Stock per Location Chart - Full Width -->
    <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg mb-8">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Stok per Lokasi Polres</h3>
            <p class="text-sm text-gray-500">Top 5 lokasi dengan stok terbanyak</p>
        </div>
        <div x-data="{
            init() {
                new Chart(this.$refs.barChart, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($stockPerLocation->map(fn($item) => $item->policeStation?->name ?? 'N/A')->toArray()) !!},
                        datasets: [{
                            label: 'Total Stock',
                            data: {!! json_encode($stockPerLocation->pluck('total_stock')->toArray()) !!},
                            backgroundColor: ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
                            borderRadius: 8,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 14 },
                                bodyFont: { size: 13 },
                                callbacks: {
                                    label: function(context) {
                                        return 'Stock: ' + context.parsed.y.toLocaleString() + ' unit';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    font: { size: 12 },
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 12 } }
                            }
                        }
                    }
                });
            }
        }">
            <canvas x-ref="barChart" style="height: 300px;"></canvas>
        </div>
    </div>

    <!-- StockOpname Statistics Cards -->
    {{-- <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Opname</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stockOpnameStats['total'] }}</p>
                </div>
                <div class="rounded-xl bg-gray-100 p-3">
                    <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-6 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-700">Draft</p>
                    <p class="mt-2 text-3xl font-bold text-yellow-900">{{ $stockOpnameStats['draft'] }}</p>
                </div>
                <div class="rounded-xl bg-yellow-200 p-3">
                    <svg class="h-6 w-6 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-700">Completed</p>
                    <p class="mt-2 text-3xl font-bold text-blue-900">{{ $stockOpnameStats['completed'] }}</p>
                </div>
                <div class="rounded-xl bg-blue-200 p-3">
                    <svg class="h-6 w-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-green-200 bg-green-50 p-6 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-700">Approved</p>
                    <p class="mt-2 text-3xl font-bold text-green-900">{{ $stockOpnameStats['approved'] }}</p>
                </div>
                <div class="rounded-xl bg-green-200 p-3">
                    <svg class="h-6 w-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- More Charts Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
        <!-- Type Distribution Pie Chart -->
        <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Distribusi Tipe Material</h3>
                <p class="text-sm text-gray-500">Top 5 tipe material</p>
            </div>
            <div x-data="{
                init() {
                    new Chart(this.$refs.pieChart, {
                        type: 'pie',
                        data: {
                            labels: {!! json_encode($typeDistribution['labels']) !!},
                            datasets: [{
                                data: {!! json_encode($typeDistribution['data']) !!},
                                backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { padding: 12, font: { size: 11 } }
                                }
                            }
                        }
                    });
                }
            }">
                <canvas x-ref="pieChart" style="height: 250px;"></canvas>
            </div>
        </div>

        <!-- Material Movement Chart -->
        <div class="lg:col-span-2 rounded-2xl border border-blue-100 bg-white p-6 shadow-lg">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Pergerakan Material</h3>
                <p class="text-sm text-gray-500">Material Masuk vs Keluar (6 bulan terakhir)</p>
            </div>
            <div x-data="{
                init() {
                    new Chart(this.$refs.areaChart, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($materialMovement['labels']) !!},
                            datasets: [{
                                    label: 'Masuk',
                                    data: {!! json_encode($materialMovement['in']) !!},
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: 'Keluar',
                                    data: {!! json_encode($materialMovement['out']) !!},
                                    borderColor: '#ef4444',
                                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true, position: 'top' }
                            },
                            scales: {
                                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            }">
                <canvas x-ref="areaChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Regional Statistics Bar Chart -->
    <div class="rounded-2xl border border-blue-100 bg-white p-6 shadow-lg mb-8">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Statistik Stock per Polda</h3>
            <p class="text-sm text-gray-500">Top 5 Polda dengan stock terbanyak</p>
        </div>
        <div x-data="{
            init() {
                new Chart(this.$refs.regionalChart, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($regionalStats['labels']) !!},
                        datasets: [{
                            label: 'Total Stock',
                            data: {!! json_encode($regionalStats['data']) !!},
                            backgroundColor: '#6366f1',
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
            <canvas x-ref="regionalChart" style="height: 280px;"></canvas>
        </div>
    </div>

    <!-- Data Tables Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
        <!-- LastStock Table -->
        <div class="rounded-2xl border border-blue-100 bg-white shadow-lg overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Last Stock Terbaru</h3>
                <p class="text-sm text-gray-500">5 entry terakhir</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Lokasi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentLastStock as $lastStock)
                            <tr class="hover:bg-blue-50/50">
                                <td class="px-4 py-3 text-sm font-mono text-blue-600">{{ $lastStock->code }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $lastStock->regionalPolice?->name ?? ($lastStock->policeStation?->name ?? 'N/A') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $lastStock->date?->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Material Damage Table -->
        <div class="rounded-2xl border border-blue-100 bg-white shadow-lg overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Material Rusak</h3>
                <p class="text-sm text-gray-500">5 entry terakhir</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Lokasi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($materialDamage as $damage)
                            <tr class="hover:bg-red-50/50">
                                <td class="px-4 py-3 text-sm font-mono text-red-600">{{ $damage->code }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $damage->regionalPolice?->name ?? ($damage->policeStation?->name ?? 'N/A') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $damage->date?->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Material Usage Table -->
        <div class="rounded-2xl border border-blue-100 bg-white shadow-lg overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Pemakaian Material</h3>
                <p class="text-sm text-gray-500">5 entry terakhir</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Lokasi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($materialUsage as $usage)
                            <tr class="hover:bg-green-50/50">
                                <td class="px-4 py-3 text-sm font-mono text-green-600">{{ $usage->code }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $usage->regionalPolice?->name ?? ($usage->policeStation?->name ?? 'N/A') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $usage->date?->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
