<div class="h-screen flex overflow-hidden">
    <!-- Left Side - Branding & Illustration -->
    <div
        class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-20 w-72 h-72 bg-cyan-400 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-blue-400 rounded-full blur-3xl"></div>
            <div
                class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-indigo-500 rounded-full blur-3xl">
            </div>
        </div>

        <!-- Grid Pattern -->
        <div class="absolute inset-0 opacity-5"
            style="background-image: url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;);">
        </div>

        <!-- Content -->
        <div class="relative z-10 flex flex-col justify-center items-center w-full h-full p-8">
            <!-- Logo -->
            <div class="mb-6">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-24">
            </div>

            <!-- Title -->
            <h1 class="text-3xl font-bold text-white mb-3 text-center">SIMMASTER</h1>
            <p class="text-base text-blue-200 mb-6 text-center max-w-md">Sistem Informasi Manajemen Material
                Terintergrasi</p>

            <!-- Illustration - Traffic/Distribution (Parallax Effect) -->
            <div class="relative w-full max-w-sm overflow-hidden mb-6">
                <style>
                    @keyframes scrollBackground {
                        0% {
                            transform: translateX(100%);
                        }

                        100% {
                            transform: translateX(-100%);
                        }
                    }

                    @keyframes roadLines {
                        0% {
                            stroke-dashoffset: 0;
                        }

                        100% {
                            stroke-dashoffset: -40;
                        }
                    }

                    @keyframes wheelSpin {
                        0% {
                            transform: rotate(0deg);
                        }

                        100% {
                            transform: rotate(360deg);
                        }
                    }

                    @keyframes truckBounce {

                        0%,
                        100% {
                            transform: translateY(0);
                        }

                        50% {
                            transform: translateY(-2px);
                        }
                    }

                    .scroll-bg {
                        animation: scrollBackground 8s linear infinite;
                    }

                    .road-lines {
                        animation: roadLines 0.5s linear infinite;
                    }

                    .wheel-spin {
                        animation: wheelSpin 0.3s linear infinite;
                        transform-origin: center;
                    }

                    .truck-bounce {
                        animation: truckBounce 0.3s ease-in-out infinite;
                    }
                </style>
                <svg class="w-full h-auto" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Road (Static) -->
                    <path d="M0 200 L400 200" stroke="#1e40af" stroke-width="60" />
                    <!-- Road Lines (Moving) -->
                    <path d="M0 200 L400 200" stroke="#60a5fa" stroke-width="4" stroke-dasharray="20 20"
                        class="road-lines" />

                    <!-- Moving Background Elements -->
                    <!-- Group 1: Building POLDA + Tree -->
                    <g class="scroll-bg" style="animation-delay: 0s;">
                        <!-- Tree next to building -->
                        <rect x="290" y="160" width="8" height="40" fill="#854d0e" />
                        <circle cx="294" cy="145" r="18" fill="#166534" />
                        <!-- Building POLDA -->
                        <rect x="320" y="100" width="50" height="100" fill="#1e3a5f" />
                        <rect x="330" y="110" width="12" height="15" fill="#7dd3fc" />
                        <rect x="348" y="110" width="12" height="15" fill="#7dd3fc" />
                        <rect x="330" y="135" width="12" height="15" fill="#7dd3fc" />
                        <rect x="348" y="135" width="12" height="15" fill="#7dd3fc" />
                        <rect x="330" y="160" width="12" height="15" fill="#7dd3fc" />
                        <rect x="348" y="160" width="12" height="15" fill="#7dd3fc" />
                        <rect x="338" y="180" width="15" height="20" fill="#0ea5e9" />
                        <text x="345" y="95" font-size="8" fill="#7dd3fc" text-anchor="middle">POLDA</text>
                    </g>

                    <!-- Group 2: Building POLRES + Tree -->
                    <g class="scroll-bg" style="animation-delay: -4s;">
                        <!-- Tree next to building -->
                        <rect x="280" y="165" width="6" height="35" fill="#854d0e" />
                        <circle cx="283" cy="150" r="15" fill="#15803d" />
                        <!-- Building POLRES -->
                        <rect x="310" y="120" width="40" height="80" fill="#1e3a5f" />
                        <rect x="318" y="130" width="10" height="12" fill="#7dd3fc" />
                        <rect x="332" y="130" width="10" height="12" fill="#7dd3fc" />
                        <rect x="318" y="150" width="10" height="12" fill="#7dd3fc" />
                        <rect x="332" y="150" width="10" height="12" fill="#7dd3fc" />
                        <rect x="325" y="180" width="12" height="20" fill="#0ea5e9" />
                        <text x="330" y="115" font-size="7" fill="#7dd3fc" text-anchor="middle">POLRES</text>
                        <!-- Another tree after building -->
                        <rect x="360" y="160" width="8" height="40" fill="#854d0e" />
                        <circle cx="364" cy="145" r="18" fill="#166534" />
                    </g>

                    <!-- Group 3: Trees only -->
                    <g class="scroll-bg" style="animation-delay: -2s;">
                        <rect x="300" y="165" width="6" height="35" fill="#854d0e" />
                        <circle cx="303" cy="150" r="15" fill="#15803d" />
                        <rect x="330" y="160" width="8" height="40" fill="#854d0e" />
                        <circle cx="334" cy="145" r="20" fill="#166534" />
                        <rect x="360" y="165" width="6" height="35" fill="#854d0e" />
                        <circle cx="363" cy="152" r="13" fill="#14532d" />
                    </g>

                    <!-- Group 4: Small building + Trees -->
                    <g class="scroll-bg" style="animation-delay: -6s;">
                        <!-- Tree -->
                        <rect x="295" y="165" width="6" height="35" fill="#854d0e" />
                        <circle cx="298" cy="150" r="15" fill="#15803d" />
                        <!-- Small Building -->
                        <rect x="320" y="140" width="35" height="60" fill="#1e3a5f" />
                        <rect x="328" y="150" width="8" height="10" fill="#7dd3fc" />
                        <rect x="340" y="150" width="8" height="10" fill="#7dd3fc" />
                        <rect x="328" y="165" width="8" height="10" fill="#7dd3fc" />
                        <rect x="340" y="165" width="8" height="10" fill="#7dd3fc" />
                        <rect x="333" y="182" width="10" height="18" fill="#0ea5e9" />
                        <!-- Tree -->
                        <rect x="365" y="160" width="8" height="40" fill="#854d0e" />
                        <circle cx="369" cy="145" r="18" fill="#166534" />
                    </g>

                    <!-- TRUCK (Static in center with bounce) -->
                    <g class="truck-bounce">
                        <!-- Truck Body -->
                        <rect x="120" y="150" width="100" height="50" rx="4" fill="#0ea5e9" />
                        <!-- Truck Cabin -->
                        <rect x="220" y="160" width="40" height="40" rx="4" fill="#0284c7" />
                        <!-- Truck Window -->
                        <rect x="230" y="168" width="20" height="15" rx="2" fill="#7dd3fc" />
                        <!-- Headlight -->
                        <rect x="258" y="185" width="4" height="8" rx="1" fill="#fbbf24" />
                        <!-- Taillight -->
                        <rect x="118" y="185" width="4" height="8" rx="1" fill="#ef4444" />

                        <!-- Packages on truck -->
                        <rect x="130" y="130" width="25" height="20" rx="2" fill="#fbbf24" />
                        <rect x="160" y="125" width="25" height="25" rx="2" fill="#f97316" />
                        <rect x="190" y="130" width="25" height="20" rx="2" fill="#22c55e" />

                        <!-- Wheels -->
                        <g>
                            <!-- Back Wheel 1 -->
                            <circle cx="145" cy="200" r="15" fill="#1e3a5f" />
                            <circle cx="145" cy="200" r="8" fill="#475569" />
                            <line x1="145" y1="192" x2="145" y2="208" stroke="#64748b"
                                stroke-width="3" class="wheel-spin" style="transform-origin: 145px 200px;" />
                            <line x1="137" y1="200" x2="153" y2="200" stroke="#64748b"
                                stroke-width="3" class="wheel-spin" style="transform-origin: 145px 200px;" />

                            <!-- Back Wheel 2 -->
                            <circle cx="195" cy="200" r="15" fill="#1e3a5f" />
                            <circle cx="195" cy="200" r="8" fill="#475569" />
                            <line x1="195" y1="192" x2="195" y2="208" stroke="#64748b"
                                stroke-width="3" class="wheel-spin" style="transform-origin: 195px 200px;" />
                            <line x1="187" y1="200" x2="203" y2="200" stroke="#64748b"
                                stroke-width="3" class="wheel-spin" style="transform-origin: 195px 200px;" />

                            <!-- Front Wheel -->
                            <circle cx="240" cy="200" r="15" fill="#1e3a5f" />
                            <circle cx="240" cy="200" r="8" fill="#475569" />
                            <line x1="240" y1="192" x2="240" y2="208" stroke="#64748b"
                                stroke-width="3" class="wheel-spin" style="transform-origin: 240px 200px;" />
                            <line x1="232" y1="200" x2="248" y2="200" stroke="#64748b"
                                stroke-width="3" class="wheel-spin" style="transform-origin: 240px 200px;" />
                        </g>
                    </g>
                </svg>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-6 w-full max-w-sm">
                <div class="text-center">
                    <div class="text-2xl font-bold text-white">34</div>
                    <div class="text-xs text-blue-300">Polda</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-white">500+</div>
                    <div class="text-xs text-blue-300">Polres</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-white">24/7</div>
                    <div class="text-xs text-blue-300">Monitoring</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div
        class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-8 lg:p-10 bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen lg:max-h-screen overflow-y-auto">
        <div class="w-full max-w-md">
            <!-- Mobile Logo -->
            <div class="lg:hidden flex flex-col items-center mb-6">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-400 to-blue-500 shadow-xl shadow-cyan-500/30 mb-4">
                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-blue-900">SIMMASTER</h1>
                <p class="text-sm text-blue-600">Sistem Informasi Manajemen Material Terintergrasi</p>
            </div>
            <div class="hidden lg:flex justify-center mb-3">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-20">
            </div>
            <!-- Welcome Text -->
            <div class="mb-5">
                <h2 class="text-2xl font-bold text-gray-900 mb-2 flex items-center justify-center gap-2">
                    Selamat Datang! <span class="text-2xl">👋</span>
                </h2>
                <p class="text-sm text-gray-600 text-center">Silakan masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <!-- Login Form -->
            <form wire:submit="login" class="space-y-4">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input wire:model="email" type="email" id="email" placeholder="nama@email.com"
                            class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 @error('email') border-red-500 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input wire:model="password" :type="show ? 'text' : 'password'" id="password"
                            placeholder="••••••••"
                            class="w-full pl-12 pr-12 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 @error('password') border-red-500 @enderror">
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="remember" type="checkbox"
                            class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0">
                        <span class="text-sm text-gray-600">Ingat saya</span>
                    </label>
                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">
                        Lupa password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3.5 px-6 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2"
                    wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-not-allowed">
                    <span wire:loading.remove>
                        Masuk
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        {{-- <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg> --}}
                        Tunggu Sebentar
                    </span>
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-5 text-center">
                <p class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} Sistem Informasi Manajemen Material Terintergrasi
                </p>
                {{-- <p class="text-xs text-gray-400 mt-1">
                    Kepolisian Republik Indonesia
                </p> --}}
            </div>
        </div>
    </div>
</div>
