<?php

use App\Models\Police\PoliceStation;
use App\Models\Type\Type;
use App\Models\User;
use App\Models\User\UserType;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bamat:create-all {--password=password : Default password untuk semua akun BAMAT}', function () {
    $defaultPassword = $this->option('password') ?: 'password';
    $hashedPassword = Hash::make($defaultPassword);

    $this->info('Memulai pembuatan/pembaruan akun BAMAT untuk seluruh Polres...');

    // 1. Pastikan UserType BAMAT tersedia
    $allTypeIds = Type::pluck('id')->toArray();
    $bamatUserType = UserType::where('name', 'BAMAT')->first();
    if (!$bamatUserType) {
        $bamatUserType = UserType::create([
            'id' => Str::uuid()->toString(),
            'name' => 'BAMAT',
            'types' => $allTypeIds,
            'level_user' => 1,
            'description' => 'Bintara Administrasi Materiel SBST',
            'is_active' => true,
        ]);
        $this->info('-> UserType BAMAT berhasil dibuat.');
    } else {
        $bamatUserType->update([
            'types' => $allTypeIds,
            'is_active' => true,
        ]);
    }

    // 2. Query seluruh Polres
    $stations = PoliceStation::orderBy('name')->get();
    $this->info("-> Ditemukan {$stations->count()} Polres.");

    $tableRows = [];
    $createdCount = 0;
    $updatedCount = 0;

    foreach ($stations as $index => $station) {
        $slug = Str::slug($station->name);
        $email = "bamat-{$slug}@sbst.test";
        $name = "BAMAT {$station->name}";

        // Cari user yang sudah ada (termasuk soft-deleted)
        $user = User::withTrashed()
            ->where('email', $email)
            ->orWhere(function ($q) use ($station) {
                $q->where('police_station_id', $station->id)
                  ->where('name', 'like', 'BAMAT%');
            })
            ->first();

        $attributes = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'regional_police_id' => $station->regional_police_id,
            'police_station_id' => $station->id,
            'user_type_id' => $bamatUserType->id,
            'level_menu' => 2,
        ];

        if ($user) {
            if ($user->trashed()) {
                $user->restore();
            }
            $user->update($attributes);
            $updatedCount++;
        } else {
            $user = User::create(array_merge($attributes, [
                'id' => Str::uuid()->toString(),
            ]));
            $createdCount++;
        }

        // Sinkronisasi Role Polres
        $user->syncRoles(['Polres']);

        $tableRows[] = [
            $index + 1,
            $station->name,
            $user->name,
            $user->email,
            $defaultPassword,
            'Polres',
        ];
    }

    $this->table(
        ['No', 'Nama Polres', 'Nama Akun', 'Email Login', 'Password', 'Role'],
        $tableRows
    );

    $this->info("SELESAI! Total {$stations->count()} akun BAMAT Polres aktif ({$createdCount} dibuat baru, {$updatedCount} diperbarui).");
    $this->info("Password default: {$defaultPassword}");
})->purpose('Generate atau update akun BAMAT untuk seluruh 39 Polres di Jawa Timur');

