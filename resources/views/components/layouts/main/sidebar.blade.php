<aside
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-gradient-to-b from-blue-900 via-blue-800 to-blue-900 shadow-2xl transition-transform duration-300 ease-in-out"
    :class="sidebarCollapsed ? '-translate-x-full' : 'translate-x-0 w-72'">

    <!-- Sidebar Header (Fixed Top) -->
    <div class="flex h-20 shrink-0 items-center justify-between border-b border-blue-700/50 px-6"
        :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3" >
            <div class="flex h-12 w-12 items-center justify-center rounded-xlshadow-lg shadow-cyan-500/30">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" width="100">
            </div>
            <div :class="sidebarCollapsed ? 'lg:hidden' : ''">
                <h1 class="text-xl font-bold text-white">SIMMASTER</h1>
                <p class="text-xs text-blue-300">Sistem Informasi Manajemen Material SBST Terintergrasi</p>
            </div>
        </a>
        <button @click="sidebarCollapsed = true"
            class="rounded-lg p-2 text-blue-300 hover:bg-blue-700/50 hover:text-white lg:hidden">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation (Scrollable) -->
    <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}"
            >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>

        @if (Auth::user()->hasRole(['Admin', 'Polda']))
            <div class="pt-2" x-data="{ open: {{ request()->routeIs('menu-polda.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-400">
                    <span>Menu Polda</span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                @if(in_array(Auth::user()->level_menu,[1,2]))
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polda.reception') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polda.reception*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polda.reception*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Penerimaan Material
                    </a>
                </div>
                @endif
                <!-- <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polda.rack-assignment') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polda.rack-assignment*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polda.rack-assignment*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Masukkan Ke Rak
                    </a>
                </div> -->
                <!-- <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polda.material-usage') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polda.material-usage*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polda.material-usage*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Material Digunakan
                    </a>
                </div> -->
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polda.material-damage') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polda.material-damage*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polda.material-damage*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Material Rusak
                    </a>
                </div>
                @if(in_array(Auth::user()->level_menu,[1,2]))
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polda.material-shipment') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polda.material-shipment*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polda.material-shipment*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Distribusi Material
                    </a>
                </div>
                @endif
                <!-- <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polda.stock-opname') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polda.stock-opname*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polda.stock-opname*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Stock Opname
                    </a>
                </div> -->
                <!-- <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polda.mutation-stock') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polda.mutation-stock') || request()->routeIs('menu-polda.mutation-stock.create') || request()->routeIs('menu-polda.mutation-stock.edit') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polda.mutation-stock') || request()->routeIs('menu-polda.mutation-stock.create') || request()->routeIs('menu-polda.mutation-stock.edit') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Mutasi Stock
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polda.mutation-stock.receive') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polda.mutation-stock.receive*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polda.mutation-stock.receive*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Terima Mutasi Stock
                    </a>
                </div> -->
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polda.stock') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polda.stock') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polda.stock') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Stock Material
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polda.history') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polda.history') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polda.history') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Riwayat Stock
                    </a>
                </div>
                <!-- <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polda.last-stock') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polda.last-stock', 'menu-polda.last-stock.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polda.last-stock', 'menu-polda.last-stock.*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Tambah Stock Akhir
                    </a>
                </div> -->
            </div>
        @endif
        @if (Auth::user()->hasRole(['Admin', 'Polres']))
            <div class="pt-2" x-data="{ open: {{ request()->routeIs('menu-polres.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-400">
                    <span>Menu Polres</span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polres.material-shipment.receive') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polres.material-shipment.receive*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polres.material-shipment.receive*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Penerimaan Material
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polres.rack-assignment') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polres.rack-assignment*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polres.rack-assignment*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Masukkan Ke Rak
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polres.material-usage') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polres.material-usage','menu-polres.material-usage.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polres.material-usage','menu-polres.material-usage.*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Material Digunakan
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polres.material-usage-detail') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polres.material-usage-detail') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polres.material-usage-detail') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Material Digunakan Detail
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polres.material-damage') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polres.material-damage*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polres.material-damage*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Material Rusak
                    </a>
                </div>
                <!-- <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polres.stock-opname') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polres.stock-opname*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polres.stock-opname*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Stock Opname
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polres.mutation-stock') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polres.mutation-stock', 'menu-polres.mutation-stock.create', 'menu-polres.mutation-stock.edit') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polres.mutation-stock', 'menu-polres.mutation-stock.edit', 'menu-polres.mutation-stock.create') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Mutasi Stock
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polres.mutation-stock.receive') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polres.mutation-stock.receive', 'menu-polres.mutation-stock.receive.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polres.mutation-stock.receive', 'menu-polres.mutation-stock.receive.*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Terima Mutasi Stock
                    </a>
                </div> -->
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polres.stock') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polres.stock') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polres.stock') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Stock Material
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polres.history') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polres.history*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polres.history*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Riwayat Stock
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('menu-polres.last-stock') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('menu-polres.last-stock*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('menu-polres.last-stock*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Input Stok Awal
                    </a>
                </div>
            </div>
        @endif
        @if (Auth::user()->hasRole('Admin'))
            <!-- Master Section -->
            <div class="pt-2" x-data="{ open: {{ request()->routeIs('master.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-400">
                    <span>Master</span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('master.regional-police') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('master.regional-police*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('master.regional-police*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Polda
                    </a>
                    <a href="{{ route('master.police-station') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('master.police-station*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('master.police-station*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Polres
                    </a>
                    <a href="{{ route('master.type') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('master.type') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('master.type') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Material
                    </a>
                    <a href="{{ route('master.type-detail') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('master.type-detail') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('master.type-detail') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Material Detail
                    </a>
                    <a href="{{ route('master.rack') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('master.rack') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('master.rack') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Rak
                    </a>
                    <a href="{{ route('master.user') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('master.user') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('master.user') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        User
                    </a>
                    <a href="{{ route('master.user-type') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('master.user-type') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('master.user-type') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Tipe User
                    </a>
                    <a href="{{ route('master.target') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('master.target','master.target.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('master.target','master.target.*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Target  
                    </a>
                </div>
            </div>
            <!-- Stok Section -->
            <div class="pt-2" x-data="{ open: {{ request()->routeIs('stock.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-400">
                    <span>Manajemen Stok</span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('stock.polda') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('stock.polda*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('stock.polda*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Stok Polda
                    </a>
                    <a href="{{ route('stock.polres') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('stock.polres*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('stock.polres*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Stok Polres
                    </a>
                    <a href="{{ route('stock.all') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('stock.all*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('stock.all*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                        Semua Stok
                    </a>
                    <a href="{{ route('stock.history') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('stock.history*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('stock.history*') ? 'bg-white' : 'bg-indigo-400' }}"></span>
                        Riwayat Stok
                    </a>
                </div>
            </div>
        @endif

        <!-- Laporan Section -->
        <div class="pt-2" x-data="{ open: {{ request()->routeIs('report.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-400">
                <span>Laporan</span>
                <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-collapse class="mt-1 space-y-1">
                @if(Auth::user()->hasRole(['Admin','Polda']))
                <a href="{{ route('report.reception-regional-police') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('report.reception-regional-police', 'report.reception-regional-police.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                    <span
                        class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('report.reception-regional-police', 'report.reception-regional-police.*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                    Laporan Penerimaan
                </a>
                <a href="{{ route('report.delivery') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('report.delivery', 'report.delivery.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                    <span
                        class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('report.delivery', 'report.delivery.*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                    Laporan Distribusi Polres
                </a>
                @endif
                <a href="{{ route('report.reception') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('report.reception', 'report.reception.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                    <span
                        class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('report.reception', 'report.reception.*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                    Laporan Penerimaan Polres
                </a>
                <!-- <a href="{{ route('report.stock-opname') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('report.stock-opname', 'report.stock-opname.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                    <span
                        class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('report.stock-opname', 'report.stock-opname.*') ? 'bg-white' : 'bg-blue-400' }}"></span>
                    Laporan Stock Opname
                </a> -->
                <!-- <a href="{{ route('report.stock') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('report.stock') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                    <span
                        class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('report.stock') ? 'bg-white' : 'bg-blue-400' }}"></span>
                    Laporan Stok Material
                </a> -->
                <!-- <a href="{{ route('report.stock-in') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('report.stock-in') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                    <span
                        class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('report.stock-in') ? 'bg-white' : 'bg-blue-400' }}"></span>
                    Laporan Stok Masuk
                </a>
                <a href="{{ route('report.stock-out') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('report.stock-out') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                    <span
                        class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('report.stock-out') ? 'bg-white' : 'bg-blue-400' }}"></span>
                    Laporan Stok Keluar
                </a> -->
                <a href="{{ route('report.material-usage') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('report.material-usage') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                    <span
                        class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('report.material-usage') ? 'bg-white' : 'bg-blue-400' }}"></span>
                    Laporan Material Digunakan
                </a>
                <a href="{{ route('report.material-usage-detail') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('report.material-usage-detail') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                    <span
                        class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('report.material-usage-detail') ? 'bg-white' : 'bg-blue-400' }}"></span>
                    Laporan Material Digunakan Detail
                </a>
                <a href="{{ route('report.material-damage') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('report.material-damage') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                    <span
                        class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('report.material-damage') ? 'bg-white' : 'bg-blue-400' }}"></span>
                    Laporan Material Rusak
                </a>
                <!-- <a href="{{ route('report.mutation') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 {{ request()->routeIs('report.mutation') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white' }}">
                    <span
                        class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('report.mutation') ? 'bg-white' : 'bg-blue-400' }}"></span>
                    Laporan Mutasi Stock
                </a> -->
            </div>
        </div>
    </nav>

    <!-- User Profile (Fixed Bottom) -->
    {{-- <div class="shrink-0 border-t border-blue-700/50 bg-blue-900 p-4">
        <div class="flex items-center gap-3 rounded-xl bg-blue-800/50 p-3" x-data="{ open: false }">
            <div
                class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 text-sm font-bold text-white">
                {{ auth()->user()->initials() }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-blue-300">{{ auth()->user()->email }}</p>
            </div>
            <div class="relative">
                <button @click="open = !open"
                    class="rounded-lg p-1.5 text-blue-300 hover:bg-blue-700/50 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute bottom-full right-0 mb-2 w-48 rounded-xl bg-white py-2 shadow-xl" x-cloak>
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil Saya
                    </a>
                    <a href="#"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan
                    </a>
                    <hr class="my-2">
                    @livewire('auth.logout.auth-logout-index')
                </div>
            </div>
        </div>
    </div> --}}
</aside>
