<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Surat Perintah Pengeluaran Materiel' }}</title>
</head>
<body onload="window.print()">
    {{ $slot }}
</body>
</html>
