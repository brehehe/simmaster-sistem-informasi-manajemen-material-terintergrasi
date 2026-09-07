<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e($title ?? 'Login'); ?> - Sistem Informasi Manajemen Material SBST Terintegrasi</title>
        <link rel="icon" type="image/png" href="<?php echo e(asset('img/logo.png')); ?>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

        <!-- Styles / Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    </head>

    <body class="font-sans antialiased">
        <?php echo e($slot); ?>

    </body>

</html>
<?php /**PATH /var/www/html/armaster/resources/views/components/layouts/auth.blade.php ENDPATH**/ ?>