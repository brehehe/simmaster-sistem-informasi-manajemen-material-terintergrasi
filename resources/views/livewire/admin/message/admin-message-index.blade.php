<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-600">Kotak Pesan & Notifikasi</h1>
                <p class="text-gray-500 mt-1">Pusat komunikasi, notifikasi material rusak, dan subsidi silang antar jajaran</p>
            </div>
            <button wire:click="openCreateModal"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Kirim Pesan / Notifikasi
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Pesan</p>
                    <p class="text-3xl font-bold mt-1">{{ $totalMessagesCount }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl p-5 text-white shadow-lg shadow-red-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Belum Dibaca</p>
                    <p class="text-3xl font-bold mt-1">{{ $unreadCount }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-5 text-white shadow-lg shadow-amber-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm">Notifikasi Mat. Rusak</p>
                    <p class="text-3xl font-bold mt-1">{{ $damageNotifCount }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white shadow-lg shadow-emerald-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm">Subsidi Silang</p>
                    <p class="text-3xl font-bold mt-1">{{ $subsidyNotifCount }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <!-- Tabs & Filters -->
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Inbox / Sent Tabs -->
                <div class="flex bg-gray-200/70 p-1 rounded-xl w-fit">
                    <button wire:click="setTab('inbox')"
                        class="px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-200 flex items-center gap-2 {{ $activeTab === 'inbox' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        Pesan Masuk
                        @if($unreadCount > 0)
                            <span class="px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full">{{ $unreadCount }}</span>
                        @endif
                    </button>
                    <button wire:click="setTab('sent')"
                        class="px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-200 flex items-center gap-2 {{ $activeTab === 'sent' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Pesan Terkirim
                    </button>
                </div>

                <!-- Category & Read Status Filters -->
                <div class="flex flex-wrap items-center gap-3">
                    <select wire:model.live="filterCategory" class="px-3 py-2 text-sm rounded-lg border border-gray-200 bg-white focus:border-blue-500">
                        <option value="">Semua Kategori</option>
                        <option value="material_damage">Notifikasi Material Rusak</option>
                        <option value="cross_subsidy">Subsidi Silang</option>
                        <option value="general_info">Informasi Umum</option>
                    </select>

                    <select wire:model.live="filterReadStatus" class="px-3 py-2 text-sm rounded-lg border border-gray-200 bg-white focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="unread">Belum Dibaca</option>
                        <option value="read">Sudah Dibaca</option>
                    </select>

                    <div class="relative">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari subjek, isi..."
                            class="pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200 bg-white focus:border-blue-500 w-60">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message List Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-4 w-12 text-center">No</th>
                        <th class="px-6 py-4 w-28">Kategori</th>
                        <th class="px-6 py-4">Pengirim / Penerima</th>
                        <th class="px-6 py-4">Subjek & Isi Pesan</th>
                        <th class="px-6 py-4 w-32">Tanggal</th>
                        <th class="px-6 py-4 w-28 text-center">Status</th>
                        <th class="px-6 py-4 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($messages as $index => $msg)
                        <tr class="hover:bg-gray-50/80 transition-colors {{ !$msg->is_read && $activeTab === 'inbox' ? 'bg-blue-50/40 font-semibold' : '' }}">
                            <td class="px-6 py-4 text-sm text-gray-500 text-center">
                                {{ $messages->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($msg->category === 'material_damage')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        Material Rusak
                                    </span>
                                @elseif($msg->category === 'cross_subsidy')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Subsidi Silang
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        Info Umum
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($activeTab === 'inbox')
                                    <div class="font-bold text-gray-900">{{ $msg->senderPoliceStation?->name ?? ($msg->senderRegionalPolice?->name ?? $msg->sender?->name) }}</div>
                                    <div class="text-xs text-gray-500">Dari: {{ $msg->sender?->name }}</div>
                                @else
                                    <div class="font-bold text-gray-900">
                                        @if($msg->receiver_type === 'all')
                                            Semua Jajaran
                                        @elseif($msg->receiver_type === 'polres')
                                            {{ $msg->receiverPoliceStation?->name ?? 'Polres' }}
                                        @else
                                            {{ $msg->receiverRegionalPolice?->name ?? 'Polda' }}
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">Tujuan: {{ ucfirst($msg->receiver_type) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="font-bold text-gray-900 truncate max-w-md">{{ $msg->subject }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-md mt-0.5">{{ Str::limit($msg->message, 80) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $msg->created_at ? $msg->created_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($msg->is_read)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Sudah Dibaca
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 animate-pulse">
                                        Belum Dibaca
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="viewMessage('{{ $msg->id }}')"
                                        class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                                        title="Buka Pesan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    @if($activeTab === 'inbox')
                                        <button wire:click="replyMessage('{{ $msg->id }}')"
                                            class="p-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors"
                                            title="Balas Pesan">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">Tidak ada pesan</p>
                                <p class="text-gray-400 text-sm mt-1">Kotak pesan masih kosong</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Menampilkan <span class="font-semibold">{{ $messages->firstItem() }}</span> sampai <span class="font-semibold">{{ $messages->lastItem() }}</span> dari <span class="font-semibold">{{ $messages->total() }}</span> hasil
                </div>
                <div>{{ $messages->links() }}</div>
            </div>
        </div>
    </div>

    <!-- Create Message Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/70 backdrop-blur-sm" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="relative inline-block w-full max-w-2xl my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl overflow-hidden">
                    <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-gradient-to-r from-blue-600 to-cyan-600 text-white">
                        <h3 class="text-xl font-bold">Kirim Pesan / Notifikasi</h3>
                        <button wire:click="closeModal" class="text-white/80 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="sendMessage" class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori Pesan <span class="text-red-500">*</span></label>
                                <select wire:model="category" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 focus:border-blue-500">
                                    <option value="general_info">Informasi Umum / Pengumuman</option>
                                    <option value="material_damage">Notifikasi Material Rusak</option>
                                    <option value="cross_subsidy">Permohonan/Notifikasi Subsidi Silang</option>
                                </select>
                                @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tujuan Penerima <span class="text-red-500">*</span></label>
                                <select wire:model.live="receiver_type" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 focus:border-blue-500">
                                    <option value="all">Semua Jajaran (Broadcast)</option>
                                    <option value="polda">Polda</option>
                                    <option value="polres">Polres</option>
                                </select>
                            </div>
                        </div>

                        @if($receiver_type === 'polda')
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Polda Tujuan</label>
                                <select wire:model="receiver_regional_police_id" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 focus:border-blue-500">
                                    <option value="">Semua Polda</option>
                                    @foreach($regionalPolices as $rp)
                                        <option value="{{ $rp->id }}">{{ $rp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($receiver_type === 'polres')
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Polres Tujuan</label>
                                <select wire:model="receiver_police_station_id" class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 focus:border-blue-500">
                                    <option value="">Semua Polres</option>
                                    @foreach($policeStations as $ps)
                                        <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Subjek Pesan <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="subject" placeholder="Judul / Subjek Pesan"
                                class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 focus:border-blue-500">
                            @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Isi Pesan <span class="text-red-500">*</span></label>
                            <textarea wire:model="message_text" rows="5" placeholder="Tuliskan rincian notifikasi atau informasi..."
                                class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 focus:border-blue-500"></textarea>
                            @error('message_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Lampiran File (Opsional)</label>
                            <input type="file" wire:model="attachment" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('attachment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" wire:click="closeModal" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/30 transition-all">
                                <span wire:loading.remove wire:target="sendMessage">Kirim Pesan</span>
                                <span wire:loading wire:target="sendMessage">Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Detail Message Modal -->
    @if ($showDetailModal && $selectedMessage)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/70 backdrop-blur-sm" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="relative inline-block w-full max-w-2xl my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-mono text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md">{{ $selectedMessage->code }}</span>
                            <h3 class="text-xl font-bold text-gray-900 mt-2">{{ $selectedMessage->subject }}</h3>
                        </div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl text-sm">
                            <div>
                                <span class="text-xs text-gray-500 block">Pengirim:</span>
                                <span class="font-bold text-gray-900">{{ $selectedMessage->senderPoliceStation?->name ?? ($selectedMessage->senderRegionalPolice?->name ?? $selectedMessage->sender?->name) }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 block">Waktu:</span>
                                <span class="font-semibold text-gray-700">{{ $selectedMessage->created_at ? $selectedMessage->created_at->format('d M Y H:i') : '-' }}</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs text-gray-500 block mb-1">Isi Pesan:</span>
                            <div class="p-4 bg-gray-50/50 rounded-xl text-sm text-gray-800 whitespace-pre-wrap leading-relaxed border border-gray-100">
                                {{ $selectedMessage->message }}
                            </div>
                        </div>

                        @if($selectedMessage->attachment_path)
                            <div class="pt-2">
                                <span class="text-xs text-gray-500 block mb-1">Lampiran File:</span>
                                <a href="{{ Storage::url($selectedMessage->attachment_path) }}" target="_blank"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-sm font-semibold transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                    </svg>
                                    Unduh Lampiran
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="p-6 border-t border-gray-100 flex items-center justify-between">
                        <button wire:click="closeModal" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                            Tutup
                        </button>
                        <button wire:click="replyMessage('{{ $selectedMessage->id }}')" class="px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Balas Pesan Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
