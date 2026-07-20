<header
    class="fixed top-0 left-0 right-0 z-40 flex h-16 items-center justify-between border-b border-blue-100 bg-white/80 px-4 backdrop-blur-xl transition-all duration-300 ease-in-out lg:px-8"
    :class="sidebarCollapsed ? 'lg:left-0' : 'lg:left-72'">
    <!-- Left Side: Toggle, Mobile Menu & Search -->
    <div class="flex items-center gap-3">
        <!-- Desktop Sidebar Toggle Button -->
        <button @click="sidebarCollapsed = !sidebarCollapsed"
            class="hidden lg:flex rounded-xl p-2 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors"
            title="Toggle Sidebar">
            <svg x-show="!sidebarCollapsed" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            <svg x-show="sidebarCollapsed" x-cloak class="h-6 w-6" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
        </button>

        <!-- Mobile Menu Button -->
        <button @click="sidebarCollapsed = false" class="rounded-xl p-2 text-blue-600 hover:bg-blue-50 lg:hidden">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Search Bar -->
        <div class="hidden lg:block w-80">
            {{-- <div class="relative">
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" placeholder="Cari no resi, polda, polres..."
                    class="w-full rounded-xl border border-blue-100 bg-blue-50/50 py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div> --}}
        </div>
    </div>

    <!-- Right Side -->
    <div class="flex items-center gap-3">
        <!-- Notification -->
        {{-- <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="relative rounded-xl p-2.5 text-gray-500 hover:bg-blue-50 hover:text-blue-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span
                    class="absolute right-1.5 top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">3</span>
            </button>
            <!-- Notification Dropdown -->
            <div x-show="open" @click.away="open = false" x-transition
                class="absolute right-0 mt-2 w-80 rounded-2xl bg-white py-2 shadow-xl ring-1 ring-black/5" x-cloak>
                <div class="border-b border-gray-100 px-4 py-3">
                    <h3 class="font-semibold text-gray-900">Notifikasi</h3>
                </div>
                <div class="max-h-80 overflow-y-auto">
                    <a href="#" class="flex gap-3 px-4 py-3 hover:bg-blue-50">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">Paket SBST-001234 terkirim</p>
                            <p class="text-xs text-gray-500">5 menit yang lalu</p>
                        </div>
                    </a>
                    <a href="#" class="flex gap-3 px-4 py-3 hover:bg-blue-50">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">Konfirmasi penerimaan berhasil</p>
                            <p class="text-xs text-gray-500">30 menit yang lalu</p>
                        </div>
                    </a>
                </div>
            </div>
        </div> --}}

        <!-- Date -->
        <div class="hidden items-center gap-2 rounded-xl bg-blue-50 px-4 py-2 text-sm text-blue-700 lg:flex">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ now()->translatedFormat('l, d F Y') }}
        </div>

        <!-- User Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="flex items-center gap-2 rounded-xl p-1.5 hover:bg-blue-50 transition-colors">
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 text-sm font-bold text-white shadow-lg shadow-blue-500/30">
                    {{ auth()->user()->initials() }}
                </div>
                <div class="hidden lg:block text-left">
                    <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->roles->pluck('name')->first() ?? 'User' }}</p>
                </div>
                <svg class="hidden lg:block h-4 w-4 text-gray-400 transition-transform"
                    :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- User Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-56 rounded-2xl bg-white py-2 shadow-xl ring-1 ring-black/5" x-cloak>

                <!-- User Info -->
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>

                <!-- Menu Items -->
                <div class="py-1">
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                        >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil Saya
                    </a>
                    <a href="#"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan
                    </a>
                </div>

                <!-- Logout -->
                <div class="border-t border-gray-100 pt-1">
                    @livewire('auth.logout.auth-logout-index')
                </div>
            </div>
        </div>
    </div>
</header>
