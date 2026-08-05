<div wire:poll.30s="loadNotifications" x-data="{ open: false }" class="relative">
    {{-- Bell Button --}}
    <button @click="open = !open; if(open) { $wire.loadNotifications(); }"
        class="relative rounded-xl p-2.5 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute right-1.5 top-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-black text-white animate-pulse shadow-lg shadow-red-500/40">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open" @click.away="open = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="absolute right-0 mt-2 w-96 rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 z-50 overflow-hidden"
        x-cloak>

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="font-bold text-white text-sm">Notifikasi</h3>
                @if($unreadCount > 0)
                    <span class="bg-white/20 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                        {{ $unreadCount }} belum dibaca
                    </span>
                @endif
            </div>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead"
                    class="text-xs text-white/80 hover:text-white font-medium transition-colors">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        {{-- Notification List --}}
        <div class="max-h-96 overflow-y-auto divide-y divide-gray-50">
            @forelse($notifications as $notif)
                <a href="{{ $notif['url'] }}"
                    wire:click="markAsRead('{{ $notif['id'] }}')"
                    @click="open = false"
                    class="flex gap-3 px-4 py-3.5 hover:bg-blue-50/60 transition-colors group {{ !$notif['read'] ? 'bg-blue-50/30' : '' }}">

                    {{-- Icon --}}
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                        {{ !$notif['read'] ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                        📦
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 leading-tight line-clamp-1">
                            {{ $notif['data']['shipment_code'] ?? 'Notifikasi Baru' }}
                        </p>
                        <p class="text-xs text-gray-600 mt-0.5 line-clamp-2 leading-snug">
                            {{ $notif['data']['message'] ?? '' }}
                        </p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="text-[10px] text-gray-400">{{ $notif['time_ago'] }}</span>
                            @if(!$notif['read'])
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-4 py-10 text-center">
                    <div class="text-3xl mb-2">🔔</div>
                    <p class="text-sm font-medium text-gray-500">Tidak ada notifikasi</p>
                    <p class="text-xs text-gray-400 mt-1">Notifikasi SPPM baru akan muncul di sini</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if(count($notifications) > 0)
            <div class="border-t border-gray-100 px-4 py-2.5 bg-gray-50/50">
                <a href="{{ route('menu-polres.material-shipment.receive') }}"
                    @click="open = false"
                    class="text-xs text-blue-600 hover:text-blue-800 font-semibold flex items-center justify-center gap-1 transition-colors">
                    Lihat semua penerimaan material
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>
</div>
