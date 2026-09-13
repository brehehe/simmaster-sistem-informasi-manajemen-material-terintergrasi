<div class="max-w-7xl mx-auto space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-blue-600">Pengaturan</span>
                <span>/</span>
                <span class="text-gray-700 capitalize">{{ $tab === 'security' ? 'Keamanan & Password' : 'Profil Akun' }}</span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 flex items-center gap-3">
                <span class="p-2 rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
                Pengaturan & Profil Akun
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Kelola informasi akun, data satuan kerja jajaran, dan amankan kata sandi Anda.
            </p>
        </div>

        <!-- Quick Info Satker Badge -->
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center gap-2">
                <span class="flex h-2.5 w-2.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-semibold text-gray-700">
                    {{ $user->roles->pluck('name')->first() ?? 'Pengguna' }}
                    @if($user->policeStation)
                        - {{ $user->policeStation->name }}
                    @elseif($user->regionalPolice)
                        - {{ $user->regionalPolice->name }}
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left Sidebar: Profile Card & Tab Nav -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Profile Identity Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/80 text-center relative overflow-hidden">
                <!-- Background Decorative Pattern -->
                <div class="absolute top-0 left-0 right-0 h-24 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500"></div>

                <!-- Avatar Circle -->
                <div class="relative pt-6 mb-4">
                    <div class="mx-auto h-24 w-24 rounded-full bg-white p-1.5 shadow-xl">
                        <div class="h-full w-full rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white text-2xl font-bold tracking-wider shadow-inner">
                            {{ $user->initials() }}
                        </div>
                    </div>
                </div>

                <!-- Name & Email -->
                <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-xs text-gray-500 truncate mt-0.5">{{ $user->email }}</p>

                <!-- Badges -->
                <div class="flex flex-wrap items-center justify-center gap-2 mt-4">
                    @foreach($user->roles as $role)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $role->name }}
                        </span>
                    @endforeach
                    @if($user->userType)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-cyan-50 text-cyan-700 border border-cyan-200">
                            {{ $user->userType->name }}
                        </span>
                    @endif
                </div>

                <div class="mt-6 pt-5 border-t border-gray-100 text-left space-y-3 text-xs">
                    <!-- Satuan Kerja / Lokasi -->
                    <div class="flex items-start justify-between">
                        <span class="text-gray-500">Satuan Kerja:</span>
                        <span class="font-medium text-gray-800 text-right max-w-[180px]">
                            @if($user->policeStation)
                                {{ $user->policeStation->name }}
                            @elseif($user->regionalPolice)
                                {{ $user->regionalPolice->name }}
                            @else
                                Polda Jatim
                            @endif
                        </span>
                    </div>

                    <!-- Level User -->
                    @if($user->userType && $user->userType->level_user)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Tingkat / Level:</span>
                        <span class="font-medium text-gray-800 uppercase">{{ $user->userType->level_user }}</span>
                    </div>
                    @endif

                    <!-- Wewenang Material -->
                    @if(!empty($managedTypes))
                    <div class="pt-2 border-t border-dashed border-gray-100">
                        <span class="text-gray-500 block mb-1.5">Wewenang Material:</span>
                        <div class="flex flex-wrap gap-1">
                            @foreach($managedTypes as $tName)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-[11px] font-medium">
                                    {{ $tName }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tab Navigation Menu -->
            <div class="bg-white rounded-2xl p-2 shadow-sm border border-gray-200/80 space-y-1">
                <button type="button"
                    wire:click="setTab('profile')"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all {{ $tab === 'profile' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 {{ $tab === 'profile' ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Informasi Profil</span>
                    </div>
                    <svg class="h-4 w-4 {{ $tab === 'profile' ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <button type="button"
                    wire:click="setTab('security')"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all {{ $tab === 'security' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 {{ $tab === 'security' ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Keamanan & Password</span>
                    </div>
                    <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $tab === 'security' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700' }}">
                        Krusial
                    </span>
                </button>
            </div>
        </div>

        <!-- Right Content: Tab Panels -->
        <div class="lg:col-span-8">
            <!-- TAB 1: INFORMASI PROFIL -->
            @if($tab === 'profile')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 lg:p-8 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Perbarui Informasi Profil
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Perbarui nama tampilan atau alamat email login akun Anda.
                    </p>
                </div>

                <!-- Flash Message -->
                @if (session()->has('profile_status'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 text-sm">
                        <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('profile_status') }}</span>
                    </div>
                @endif

                <form wire:submit.prevent="updateProfileInformation" class="space-y-5">
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nama Lengkap / Nama Akun <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input id="name" type="text" wire:model="name" required
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-sm transition-all shadow-sm"
                                placeholder="Masukkan nama akun">
                        </div>
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Login -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Alamat Email Login <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input id="email" type="email" wire:model="email" required
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-sm transition-all shadow-sm"
                                placeholder="email@armaster.net">
                        </div>
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Institutional Info Box -->
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 text-xs text-gray-600 space-y-1">
                        <div class="font-semibold text-gray-800 flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Catatan Pengaturan Akun Jajaran:
                        </div>
                        <p>Penetapan Satuan Kerja (Polda/Polres/Samsat), User Type, dan wewenang tipe material dikontrol secara terpusat oleh Super Admin. Jika ada pergantian wewenang, silakan hubungi tim IT / Ditlantas Polda Jatim.</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end pt-3">
                        <button type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium py-2.5 px-6 rounded-xl shadow-md shadow-blue-500/20 transition-all text-sm disabled:opacity-50">
                            <svg wire:loading wire:target="updateProfileInformation" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="updateProfileInformation">Simpan Perubahan Profil</span>
                            <span wire:loading wire:target="updateProfileInformation">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <!-- TAB 2: KEAMANAN & GANTI PASSWORD -->
            @if($tab === 'security')
            <div class="space-y-6">
                <!-- Security Advisory Banner -->
                <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-orange-600 rounded-2xl p-6 text-white shadow-lg shadow-amber-500/20 relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 opacity-15">
                        <svg class="h-40 w-40 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div class="relative z-10 flex items-start gap-4">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl shrink-0">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base lg:text-lg font-bold">Penting: Amankan Akun Anda Sendiri</h3>
                            <p class="text-xs lg:text-sm text-amber-100 mt-1 leading-relaxed">
                                Karena daftar akun dan password awal dibagikan secara umum pada jajaran, <strong>setiap satuan kerja sangat dianjurkan untuk segera mengganti password bawaan</strong> dengan password baru yang rahasia. Setelah diubah, pihak lain tidak akan bisa masuk atau melihat (<em>"saling inceng"</em>) data material dan laporan satuan kerja Anda.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Password Change Form Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 lg:p-8 space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Ganti Kata Sandi Akun
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Gunakan kombinasi minimal 8 karakter dengan huruf dan angka untuk keamanan optimal.
                        </p>
                    </div>

                    <!-- Flash Success Message -->
                    @if (session()->has('password_status'))
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 text-sm">
                            <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <p class="font-semibold">Berhasil!</p>
                                <p class="text-xs text-emerald-700 mt-0.5">{{ session('password_status') }}</p>
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="updatePassword" class="space-y-5">
                        <!-- Current Password Field with Eye Toggle -->
                        <div x-data="{ show: false }">
                            <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Kata Sandi Saat Ini <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input id="current_password"
                                    :type="show ? 'text' : 'password'"
                                    wire:model="current_password"
                                    required
                                    autocomplete="current-password"
                                    class="w-full pl-10 pr-12 py-2.5 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-sm transition-all shadow-sm"
                                    placeholder="Masukkan kata sandi lama / default saat ini">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password Field with Eye Toggle -->
                        <div x-data="{ show: false }">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Kata Sandi Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <input id="password"
                                    :type="show ? 'text' : 'password'"
                                    wire:model="password"
                                    required
                                    autocomplete="new-password"
                                    class="w-full pl-10 pr-12 py-2.5 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-sm transition-all shadow-sm"
                                    placeholder="Masukkan kata sandi baru (min. 8 karakter)">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password Field with Eye Toggle -->
                        <div x-data="{ show: false }">
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <input id="password_confirmation"
                                    :type="show ? 'text' : 'password'"
                                    wire:model="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    class="w-full pl-10 pr-12 py-2.5 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-sm transition-all shadow-sm"
                                    placeholder="Ulangi kata sandi baru Anda">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Security Tips Checklist -->
                        <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-100 text-xs text-blue-800 space-y-1.5">
                            <p class="font-semibold flex items-center gap-1.5 text-blue-900">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Petunjuk Keamanan Kata Sandi:
                            </p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700 pl-1">
                                <li>Panjang minimal adalah 8 karakter.</li>
                                <li>Disarankan mengombinasikan huruf besar, huruf kecil, dan angka.</li>
                                <li>Kata sandi Anda dienkripsi secara aman (hashing satu arah) dan tidak dapat dilihat oleh pihak manapun di database.</li>
                            </ul>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end pt-3">
                            <button type="submit"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-lg shadow-blue-500/25 transition-all text-sm disabled:opacity-50">
                                <svg wire:loading wire:target="updatePassword" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="updatePassword">Perbarui Kata Sandi Akun</span>
                                <span wire:loading wire:target="updatePassword">Memproses...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
