<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'SIMMASTER' }} - Sistem Informasi Manajemen Material SBST Terintegrasi</title>

        <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Selectize CSS -->
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css">

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] {
                display: none !important;
            }

            /* Custom Selectize Styling */
            .selectize-control {
                width: 100%;
            }

            .selectize-control.single .selectize-input {
                width: 100%;
                padding: 0.5rem 0.75rem;
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
                background-color: #ffffff !important;
                font-size: 0.875rem;
                box-shadow: none;
                min-height: 38px;
                height: auto;
                line-height: 1.25rem;
                display: flex;
                align-items: center;
            }

            /* Ensure white background for all selectize states */
            .selectize-control.single .selectize-input,
            .selectize-control.single .selectize-input.not-full,
            .selectize-control.single .selectize-input.dropdown-active,
            .selectize-control.single .selectize-input.input-active {
                background-color: #ffffff !important;
                background: #ffffff !important;
                background-image: none !important;
                box-shadow: none !important;
            }

            .selectize-control.single .selectize-input>* {
                line-height: 1.25rem;
            }

            .selectize-control.single .selectize-input.focus {
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
                background-color: #fff !important;
            }

            .selectize-control.single .selectize-input input {
                font-size: 0.875rem;
            }

            /* Clear button alignment */
            .selectize-input>.clear-button,
            .selectize-input>a.clear {
                position: absolute;
                right: 2.5rem;
                top: 50%;
                transform: translateY(-50%);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin: 0;
                padding: 2px;
                line-height: 1;
            }

            .selectize-dropdown {
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                margin-top: 4px;
                z-index: 99999 !important;
                max-height: 250px !important;
                overflow-y: auto !important;
                background: white;
            }

            .selectize-dropdown .option {
                padding: 8px 12px;
                font-size: 0.875rem;
            }

            .selectize-dropdown .option.active {
                background-color: #3b82f6;
                color: white;
            }

            .selectize-dropdown .option:hover {
                background-color: #eff6ff;
            }

            .selectize-dropdown .option.active:hover {
                background-color: #2563eb;
            }

            /* Change selected item in dropdown from green to blue */
            .selectize-dropdown .selected {
                background-color: #3b82f6 !important;
                color: white !important;
            }

            .selectize-input.disabled {
                opacity: 0.6;
                background-color: #f3f4f6;
            }
        </style>
    </head>

    <body class="min-h-screen bg-gradient-to-br from-blue-50 via-sky-50 to-cyan-50 font-sans antialiased"
        x-data="{
            sidebarCollapsed: window.innerWidth < 1024 ? true : (localStorage.getItem('sidebarCollapsed') === 'true')
        }"
        x-init="$watch('sidebarCollapsed', value => localStorage.setItem('sidebarCollapsed', value))"
        @resize.window="if (window.innerWidth < 1024) sidebarCollapsed = true">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="!sidebarCollapsed && window.innerWidth < 1024" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden"
            @click="sidebarCollapsed = true" x-cloak>
        </div>

        <!-- Sidebar -->
        @include('components.layouts.main.sidebar')

        <!-- Top Header (Fixed outside main content) -->
        @include('components.layouts.main.header')

        <!-- Main Content Area -->
        <div class="transition-all duration-300 ease-in-out mt-16" :class="sidebarCollapsed ? '' : 'lg:pl-72'">
            <!-- Main Content -->
            <main class="min-h-[calc(100vh-8rem)] p-4 lg:p-8">
                {{ $slot }}
            </main>
        </div>

        <!-- jQuery (required for Selectize) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <!-- Selectize JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"></script>

        @stack('scripts')

        <!-- Export Libraries CDN -->
        <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
        <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    </body>

</html>
