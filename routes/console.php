<?php

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Type\Type;
use App\Models\User;
use App\Models\User\UserType;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bamat:create-all {--password=password : Default password untuk semua akun}', function () {
    $defaultPassword = $this->option('password') ?: 'password';
    $hashedPassword = Hash::make($defaultPassword);

    $this->info('================================================================');
    $this->info('  MEMULAI SINKRONISASI AKUN BAMAT & DOMAIN @ARMASTER.NET');
    $this->info('================================================================');

    // 0. Update semua email lama @sbst.test menjadi @armaster.net
    $oldTestUsers = User::withTrashed()->where('email', 'ilike', '%@sbst.test')->get();
    foreach ($oldTestUsers as $oldUser) {
        $newEmail = str_replace('@sbst.test', '@armaster.net', $oldUser->email);
        $oldUser->update(['email' => $newEmail]);
    }
    $this->info("-> Berhasil migrasi {$oldTestUsers->count()} email dari @sbst.test ke @armaster.net.");

    // 1. Pastikan User Types Polda & Polres tersedia
    $allTypeIds = Type::pluck('id')->toArray();
    $getTypeIds = fn(array $names) => Type::whereIn('name', $names)->pluck('id')->toArray();

    $userTypeDefinitions = [
        'BAMAT' => ['types' => $allTypeIds, 'level' => 1, 'desc' => 'Bintara Administrasi Materiel SBST'],
        'SAMSAT POLDA' => ['types' => $getTypeIds(['STNK', 'TNKB REG', 'TNKB R2 PUTIH', 'TNKB R4 PUTIH', 'MUTASI']), 'level' => 2, 'desc' => 'Pelayanan Samsat Ditlantas Polda Jatim'],
        'SIE FASMAT' => ['types' => $allTypeIds, 'level' => 2, 'desc' => 'Seksi Fasilitas Materiel SBST Ditlantas'],
        'SIE STNK' => ['types' => $getTypeIds(['STNK', 'STCK']), 'level' => 2, 'desc' => 'Seksi STNK Ditlantas Polda Jatim'],
        'SIE BPKB' => ['types' => $getTypeIds(['E-BPKB', 'BPKB', 'MUTASI']), 'level' => 2, 'desc' => 'Seksi BPKB Ditlantas Polda Jatim'],
        'SIE SIM' => ['types' => $getTypeIds(['SIM CARD']), 'level' => 2, 'desc' => 'Seksi SIM Ditlantas Polda Jatim'],
        'SIE TNKB' => ['types' => $getTypeIds(['TNKB REG', 'TNKB LISTRIK', 'NRKB NOPIL', 'NRKB NOPIL LISTRIK']), 'level' => 2, 'desc' => 'Seksi TNKB Ditlantas Polda Jatim'],
    ];

    $userTypeModels = [];
    foreach ($userTypeDefinitions as $utName => $utData) {
        $ut = UserType::where('name', $utName)->first();
        if ($ut) {
            $ut->update([
                'types' => $utData['types'],
                'level_user' => $utData['level'],
                'description' => $utData['desc'],
                'is_active' => true,
            ]);
        } else {
            $ut = UserType::create([
                'id' => Str::uuid()->toString(),
                'name' => $utName,
                'types' => $utData['types'],
                'level_user' => $utData['level'],
                'description' => $utData['desc'],
                'is_active' => true,
            ]);
        }
        $userTypeModels[$utName] = $ut;
    }

    $polda = RegionalPolice::where('name', 'ilike', '%Polda%')->first() ?? RegionalPolice::first();

    // Helper upsert user
    $upsertAccount = function (string $email, array $attributes, array $roles = []) use ($hashedPassword) {
        $user = User::withTrashed()->where('email', $email)->first();
        if ($user) {
            if ($user->trashed()) {
                $user->restore();
            }
            $user->update(array_merge($attributes, ['password' => $hashedPassword]));
        } else {
            $user = User::create(array_merge($attributes, [
                'id' => Str::uuid()->toString(),
                'email' => $email,
                'password' => $hashedPassword,
            ]));
        }
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }
        return $user;
    };

    // 2. Buat Akun Admin & Polda Induk
    if ($polda) {
        $upsertAccount('admin@armaster.net', [
            'name' => 'Admin ARMASTER',
            'level_menu' => 1,
        ], ['Admin']);

        $upsertAccount('polda-jatim@armaster.net', [
            'name' => 'Polda Jatim',
            'regional_police_id' => $polda->id,
            'police_station_id' => null,
            'user_type_id' => null,
            'level_menu' => 1,
        ], ['Polda']);

        $upsertAccount('bamat-polda-jatim@armaster.net', [
            'name' => 'BAMAT Polda Jatim (Gudang)',
            'regional_police_id' => $polda->id,
            'police_station_id' => null,
            'user_type_id' => $userTypeModels['BAMAT']->id,
            'level_menu' => 2,
        ], ['Polda']);
    }

    // 3. Buat 4 Akun BAMAT SAMSAT POLDA
    $samsatPoldaList = [
        [
            'name' => 'BAMAT SAMSAT Surabaya Timur',
            'email' => 'bamat-samsat-timur@armaster.net',
            'keterangan' => 'Samsat Manyar',
        ],
        [
            'name' => 'BAMAT SAMSAT Surabaya Barat',
            'email' => 'bamat-samsat-barat@armaster.net',
            'keterangan' => 'Samsat Tandes',
        ],
        [
            'name' => 'BAMAT SAMSAT Surabaya Selatan',
            'email' => 'bamat-samsat-selatan@armaster.net',
            'keterangan' => 'Samsat Ketintang',
        ],
        [
            'name' => 'BAMAT SAMSAT Surabaya Utara',
            'email' => 'bamat-samsat-utara@armaster.net',
            'keterangan' => 'Samsat Kedung Cowek',
        ],
    ];

    $samsatTableRows = [];
    foreach ($samsatPoldaList as $idx => $samsat) {
        $user = $upsertAccount($samsat['email'], [
            'name' => $samsat['name'],
            'regional_police_id' => $polda?->id,
            'police_station_id' => null,
            'user_type_id' => $userTypeModels['SAMSAT POLDA']->id,
            'level_menu' => 2,
        ], ['Polda']);

        $samsatTableRows[] = [
            $idx + 1,
            $user->name,
            $user->email,
            $defaultPassword,
            'Polda',
            $samsat['keterangan'],
        ];
    }

    // Juga pastikan akun samsat polda umum
    $upsertAccount('samsat-polda-jatim@armaster.net', [
        'name' => 'SAMSAT Polda Jatim',
        'regional_police_id' => $polda?->id,
        'police_station_id' => null,
        'user_type_id' => $userTypeModels['SAMSAT POLDA']->id,
        'level_menu' => 2,
    ], ['Polda']);

    // 4. Buat 3 Akun BAMAT Pelayanan Materiil (SIM, STNK, BPKB) + Fasmat & TNKB
    $bamatPoldaList = [
        [
            'name' => 'BAMAT SIM Polda Jatim',
            'email' => 'bamat-sim@armaster.net',
            'user_type' => 'SIE SIM',
        ],
        [
            'name' => 'BAMAT STNK Polda Jatim',
            'email' => 'bamat-stnk@armaster.net',
            'user_type' => 'SIE STNK',
        ],
        [
            'name' => 'BAMAT BPKB Polda Jatim',
            'email' => 'bamat-bpkb@armaster.net',
            'user_type' => 'SIE BPKB',
        ],
        [
            'name' => 'BAMAT FASMAT Polda Jatim',
            'email' => 'bamat-fasmat@armaster.net',
            'user_type' => 'SIE FASMAT',
        ],
        [
            'name' => 'BAMAT TNKB Polda Jatim',
            'email' => 'bamat-tnkb@armaster.net',
            'user_type' => 'SIE TNKB',
        ],
    ];

    $bamatPoldaTableRows = [];
    foreach ($bamatPoldaList as $idx => $item) {
        $user = $upsertAccount($item['email'], [
            'name' => $item['name'],
            'regional_police_id' => $polda?->id,
            'police_station_id' => null,
            'user_type_id' => $userTypeModels[$item['user_type']]->id,
            'level_menu' => 2,
        ], ['Polda']);

        $bamatPoldaTableRows[] = [
            $idx + 1,
            $user->name,
            $user->email,
            $defaultPassword,
            'Polda',
            $item['user_type'],
        ];
    }

    // 5. Buat Akun BAMAT Seluruh Polres se-Jawa Timur (39 Polres)
    $stations = PoliceStation::orderBy('name')->get();
    $polresTableRows = [];

    foreach ($stations as $index => $station) {
        $slug = Str::slug($station->name);
        $bamatEmail = "bamat-{$slug}@armaster.net";
        $bamatName = "BAMAT {$station->name}";

        $bamatUser = $upsertAccount($bamatEmail, [
            'name' => $bamatName,
            'regional_police_id' => $station->regional_police_id,
            'police_station_id' => $station->id,
            'user_type_id' => $userTypeModels['BAMAT']->id,
            'level_menu' => 2,
        ], ['Polres']);

        // Akun Induk Polres
        $stationEmail = "{$slug}@armaster.net";
        $upsertAccount($stationEmail, [
            'name' => $station->name,
            'regional_police_id' => $station->regional_police_id,
            'police_station_id' => $station->id,
            'user_type_id' => null,
            'level_menu' => 1,
        ], ['Polres']);

        $polresTableRows[] = [
            $index + 1,
            $station->name,
            $bamatUser->name,
            $bamatUser->email,
            $defaultPassword,
            'Polres',
        ];
    }

    $this->info("\n--- 1. DAFTAR AKUN 4 BAMAT SAMSAT POLDA ---");
    $this->table(
        ['No', 'Nama Akun', 'Email Login', 'Password', 'Role', 'Keterangan'],
        $samsatTableRows
    );

    $this->info("\n--- 2. DAFTAR AKUN 3 BAMAT PELAYANAN POLDA (SIM, STNK, BPKB) ---");
    $this->table(
        ['No', 'Nama Akun', 'Email Login', 'Password', 'Role', 'User Type'],
        $bamatPoldaTableRows
    );

    $this->info("\n--- 3. DAFTAR AKUN BAMAT POLRES JATIM ({$stations->count()} POLRES) ---");
    $this->table(
        ['No', 'Nama Polres', 'Nama Akun', 'Email Login', 'Password', 'Role'],
        $polresTableRows
    );

    $this->info("\n================================================================");
    $this->info("SELESAI! Seluruh akun berhasil dimigrasikan ke domain @armaster.net.");
    $this->info("Password default untuk semua akun di atas: {$defaultPassword}");
    $this->info("================================================================");
})->purpose('Generate atau update akun BAMAT Polres, SAMSAT Polda, dan Pelayanan Polda dengan domain @armaster.net');

Artisan::command('fix:check {issue?}', function ($issue = 'all') {
    $this->info("Running fix:check for issue: {$issue}");

    // Issue 7: Bangkalan STNK
    if ($issue === 'all' || $issue === 'bangkalan') {
        $this->info("\n--- 7. BANGKALAN STNK ---");
        $bangkalan = PoliceStation::where('name', 'ilike', '%bangkalan%')->first();
        $stnk = Type::where('name', 'STNK')->first();
        if ($bangkalan && $stnk) {
            $ls = \App\Models\LastStock\LastStock::where('police_station_id', $bangkalan->id)->where('type_id', $stnk->id)->with('lastStockDetails')->get();
            foreach ($ls as $l) {
                $this->line("LastStock {$l->code} | date: {$l->date->format('Y-m-d')} | Qty: " . $l->lastStockDetails->sum('quantity'));
                foreach ($l->lastStockDetails as $d) {
                    $this->line("  Detail id: {$d->id} | Qty: {$d->quantity} | serial: {$d->number_serial_first} - {$d->number_serial_second} | code: {$d->code}");
                }
            }
            $stocks = \App\Models\Stock\Stock::where('police_station_id', $bangkalan->id)->where('type_id', $stnk->id)->with('stockDetails')->get();
            foreach ($stocks as $s) {
                $this->line("Stock id: {$s->id} | Qty: {$s->quantity}");
                foreach ($s->stockDetails as $sd) {
                    $this->line("  StockDetail id: {$sd->id} | Qty: {$sd->quantity} | serial: {$sd->number_serial_first} - {$sd->number_serial_second}");
                }
            }
            $usages = \App\Models\MenuPolda\MaterialUsage\MaterialUsage::where('police_station_id', $bangkalan->id)
                ->whereHas('materialUsageDetails', fn($q) => $q->where('type_id', $stnk->id))
                ->with(['materialUsageDetails' => fn($q) => $q->where('type_id', $stnk->id)])
                ->get();
            $this->line("Total Usages: " . $usages->count() . " | Sum qty: " . $usages->flatMap->materialUsageDetails->sum('quantity'));
        }
    }

    // Issue 8: Mojokerto Kab
    if ($issue === 'all' || $issue === 'mojokerto') {
        $this->info("\n--- 8. MOJOKERTO KAB (TNKB R2 PUTIH & MUTASI) ---");
        $mojokerto = PoliceStation::where('name', 'ilike', '%mojokerto%')->where('name', 'not ilike', '%kota%')->first();
        $this->line("Police Station: " . ($mojokerto->name ?? 'NULL') . " (ID: " . ($mojokerto->id ?? 'NULL') . ")");
        $types = Type::whereIn('name', ['TNKB R2 PUTIH', 'MUTASI'])->get();
        foreach ($types as $t) {
            $stocks = \App\Models\Stock\Stock::where('police_station_id', $mojokerto->id)->where('type_id', $t->id)->with('stockDetails')->get();
            foreach ($stocks as $s) {
                $this->line("Type {$t->name} Stock id: {$s->id} | Qty: {$s->quantity}");
                foreach ($s->stockDetails as $sd) {
                    $this->line("  StockDetail id: {$sd->id} | Qty: {$sd->quantity} | rack: {$sd->rack_id}");
                }
            }
        }
    }

    // Issue 6: Samsat Surabaya Barat
    if ($issue === 'all' || $issue === 'sby_barat') {
        $this->info("\n--- 6. SAMSAT SBY BARAT ---");
        $user = User::where('email', 'bamat-samsat-barat@armaster.net')->with(['userType', 'roles'])->first();
        $this->line("User: " . ($user->name ?? 'NULL') . " | Role: " . $user->roles->pluck('name')->implode(', '));
        $this->line("UserType: " . ($user->userType->name ?? 'NULL') . " | Types count: " . count($user->userType->types ?? []));
        $userTypes = Type::whereIn('id', $user->userType->types ?? [])->pluck('name')->toArray();
        $this->line("UserType types: " . implode(', ', $userTypes));
    }
});

