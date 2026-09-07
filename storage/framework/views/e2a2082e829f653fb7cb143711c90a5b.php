<aside
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-gradient-to-b from-blue-900 via-blue-800 to-blue-900 shadow-2xl transition-transform duration-300 ease-in-out"
    :class="sidebarCollapsed ? '-translate-x-full' : 'translate-x-0 w-72'">

    <!-- Sidebar Header (Fixed Top) -->
    <div class="flex h-20 shrink-0 items-center justify-between border-b border-blue-700/50 px-6"
        :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
        <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xlshadow-lg shadow-cyan-500/30">
                <img src="<?php echo e(asset('img/logo.png')); ?>" alt="Logo" width="100">
            </div>
            <div :class="sidebarCollapsed ? 'lg:hidden' : ''">
                <h1 class="text-xl font-bold text-white">ARMASTER</h1>
                <p class="text-xs text-blue-300">Smart Fasmat SBST Terintegrasi</p>
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
        <a  href="<?php echo e(route('dashboard')); ?>"
            class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('dashboard') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasRole(['Admin', 'Polda', 'Warehouse'])): ?>
            <!-- Warehouse Section -->
            <div class="pt-2" x-data="{ open: <?php echo e(request()->routeIs('warehouse.*') ? 'true' : 'false'); ?> }">
                <button @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-300">
                    <span>Warehouse</span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="<?php echo e(route('warehouse.scan')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('warehouse.scan') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('warehouse.scan') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Scan QR SPPM Gudang
                    </a>
                    <a href="<?php echo e(route('warehouse.display')); ?>" target="_blank"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 text-blue-200 hover:bg-blue-700/50 hover:text-white">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
                        Live Monitor Gudang (TV)
                    </a>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasRole(['Admin', 'Polda'])): ?>
            <div class="pt-2" x-data="{ open: <?php echo e(request()->routeIs('menu-polda.*') ? 'true' : 'false'); ?> }">
                <button @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-400">
                    <span>Menu Polda</span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array(Auth::user()->level_menu, [1, 2])): ?>
                    <div x-show="open" x-collapse class="mt-1 space-y-1">
                        <a  href="<?php echo e(route('menu-polda.reception')); ?>"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polda.reception*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                            <span
                                class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polda.reception*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                            Penerimaan Material
                        </a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polda.rack-assignment')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polda.rack-assignment*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polda.rack-assignment*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Masukkan Ke Rak
                    </a>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array(Auth::user()->level_menu, [1, 2])): ?>
                    <div x-show="open" x-collapse class="mt-1 space-y-1">
                        <a  href="<?php echo e(route('menu-polda.material-shipment')); ?>"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polda.material-shipment*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                            <span
                                class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polda.material-shipment*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                            Distribusi Material
                        </a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polda.material-subsidy')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polda.material-subsidy*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polda.material-subsidy*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Subsidi Material
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polda.material-damage')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polda.material-damage*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polda.material-damage*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Material Rusak
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polda.stock')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polda.stock') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polda.stock') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Stock Material
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polda.history')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polda.history') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polda.history') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Riwayat Stock
                    </a>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasRole(['Admin', 'Polres'])): ?>
            <div class="pt-2" x-data="{ open: <?php echo e(request()->routeIs('menu-polres.*') ? 'true' : 'false'); ?> }">
                <button @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-400">
                    <span>Menu Polres</span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polres.material-shipment.receive')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polres.material-shipment.receive*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polres.material-shipment.receive*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Penerimaan Material
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polres.rack-assignment')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polres.rack-assignment*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polres.rack-assignment*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Masukkan Ke Rak
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polres.material-usage')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polres.material-usage', 'menu-polres.material-usage.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polres.material-usage', 'menu-polres.material-usage.*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Material Digunakan
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polres.material-usage-detail')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polres.material-usage-detail') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polres.material-usage-detail') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Material Digunakan Detail
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polres.material-damage')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polres.material-damage*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polres.material-damage*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Material Rusak
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polres.stock')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polres.stock') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polres.stock') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Stock Material
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polres.history')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polres.history*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polres.history*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Riwayat Stock
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polres.material-subsidy')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polres.material-subsidy*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polres.material-subsidy*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Subsidi Material
                    </a>
                </div>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('menu-polres.last-stock')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('menu-polres.last-stock*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('menu-polres.last-stock*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Input Stok Awal
                    </a>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!Auth::user()?->hasRole('Polres') && empty(Auth::user()?->police_station_id)): ?>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="<?php echo e(route('warehouse.display')); ?>" target="_blank"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 text-cyan-300 hover:bg-cyan-600/30 hover:text-white border border-cyan-500/20">
                        <span class="h-2 w-2 rounded-full bg-cyan-400 animate-ping"></span>
                        🖥️ Live Monitor Gudang (TV)
                    </a>
                    <a href="<?php echo e(route('warehouse.scan')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('warehouse.scan') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg shadow-emerald-500/30' : 'text-emerald-300 hover:bg-emerald-600/30 hover:text-white border border-emerald-500/20'); ?>">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        📷 Scan QR Serah Terima
                    </a>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasRole('Admin')): ?>
            <!-- Master Section -->
            <div class="pt-2" x-data="{ open: <?php echo e(request()->routeIs('master.*') ? 'true' : 'false'); ?> }">
                <button @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-400">
                    <span>Master</span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('master.regional-police')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('master.regional-police*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('master.regional-police*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Polda
                    </a>
                    <a  href="<?php echo e(route('master.police-station')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('master.police-station*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('master.police-station*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Polres
                    </a>
                    <a  href="<?php echo e(route('master.type')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('master.type') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('master.type') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Material
                    </a>
                    <a  href="<?php echo e(route('master.type-detail')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('master.type-detail') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('master.type-detail') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Material Detail
                    </a>
                    <a  href="<?php echo e(route('master.rack')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('master.rack') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('master.rack') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Rak
                    </a>
                    <a  href="<?php echo e(route('master.user')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('master.user') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('master.user') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        User
                    </a>
                    <a  href="<?php echo e(route('master.user-type')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('master.user-type') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('master.user-type') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Tipe User
                    </a>
                    <a  href="<?php echo e(route('master.target')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('master.target', 'master.target.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('master.target', 'master.target.*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Target
                    </a>
                </div>
            </div>
            <!-- Stok Section -->
            <div class="pt-2" x-data="{ open: <?php echo e(request()->routeIs('stock.*') ? 'true' : 'false'); ?> }">
                <button @click="open = !open"
                    class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-400">
                    <span>Manajemen Stok</span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a  href="<?php echo e(route('stock.polda')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('stock.polda*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('stock.polda*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Stok Polda
                    </a>
                    <a  href="<?php echo e(route('stock.polres')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('stock.polres*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('stock.polres*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Stok Polres
                    </a>
                    <a  href="<?php echo e(route('stock.history')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('stock.history*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('stock.history*') ? 'bg-white' : 'bg-indigo-400'); ?>"></span>
                        Riwayat Stok
                    </a>
                    <a  href="<?php echo e(route('stock.all')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('stock.all*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('stock.all*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Semua Stok
                    </a>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Laporan Section -->
        <div class="pt-2" x-data="{ open: <?php echo e(request()->routeIs('report.*') ? 'true' : 'false'); ?> }">
            <button @click="open = !open"
                class="flex w-full items-center justify-between rounded-lg px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-400">
                <span>Laporan</span>
                <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-collapse class="mt-1 space-y-1">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasRole(['Admin', 'Polda'])): ?>
                    <a  href="<?php echo e(route('report.reception-regional-police')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('report.reception-regional-police', 'report.reception-regional-police.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('report.reception-regional-police', 'report.reception-regional-police.*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Laporan Penerimaan
                    </a>
                    <a  href="<?php echo e(route('report.delivery')); ?>"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('report.delivery', 'report.delivery.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                        <span
                            class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('report.delivery', 'report.delivery.*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                        Laporan Distribusi Polres
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <a  href="<?php echo e(route('report.reception')); ?>"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('report.reception', 'report.reception.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                    <span
                        class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('report.reception', 'report.reception.*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                    Laporan Penerimaan Polres
                </a>
                <a  href="<?php echo e(route('report.material-usage')); ?>"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('report.material-usage') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                    <span
                        class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('report.material-usage') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                    Laporan Material Digunakan
                </a>
                <a  href="<?php echo e(route('report.material-usage-detail')); ?>"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('report.material-usage-detail') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                    <span
                        class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('report.material-usage-detail') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                    Laporan Material Digunakan Detail
                </a>
                <a  href="<?php echo e(route('report.material-subsidy')); ?>"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('report.material-subsidy*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                    <span
                        class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('report.material-subsidy*') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                    Laporan Material Subsidi
                </a>
                <a  href="<?php echo e(route('report.material-damage')); ?>"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition-all duration-200 <?php echo e(request()->routeIs('report.material-damage') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                    <span
                        class="h-1.5 w-1.5 rounded-full <?php echo e(request()->routeIs('report.material-damage') ? 'bg-white' : 'bg-blue-400'); ?>"></span>
                    Laporan Material Rusak
                </a>
            </div>
        </div>

        <!-- Kotak Pesan -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasRole(['Admin', 'Polda', 'Polres'])): ?>
            <a href="<?php echo e(route('dashboard.kotak-pesan')); ?>"
                class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('*kotak-pesan*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Kotak Pesan
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Peraturan -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasRole(['Admin', 'Polda', 'Polres'])): ?>
            <a href="<?php echo e(route('dashboard.peraturan')); ?>"
                class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 <?php echo e(request()->routeIs('*peraturan*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-blue-200 hover:bg-blue-700/50 hover:text-white'); ?>">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Peraturan
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </nav>

    <!-- User Profile (Fixed Bottom) -->
    
</aside><?php /**PATH /var/www/html/armaster/resources/views/components/layouts/main/sidebar.blade.php ENDPATH**/ ?>