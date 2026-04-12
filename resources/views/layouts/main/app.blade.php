<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARMASTER</title>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 antialiased">
    <div class="min-h-screen">
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            @yield('content')
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>