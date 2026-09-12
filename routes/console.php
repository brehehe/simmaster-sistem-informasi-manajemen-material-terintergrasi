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

    // Issue 1, 2, 4, 5: STNK Services
    if ($issue === 'all' || $issue === 'stnk') {
        $this->info("\n--- STNK SERVICES ---");
        $stnk = Type::where('name', 'STNK')->first();
        if ($stnk) {
            $svcs = \App\Models\Service\Service::where('type_id', $stnk->id)->with('details')->get();
            foreach ($svcs as $s) {
                $usageCount = \App\Models\MenuPolda\MaterialUsage\MaterialUsageDetailItem::where('service_id', $s->id)->count();
                $this->line("Service: {$s->id} | {$s->name} | active: " . ($s->is_active ? 'Y' : 'N') . " | UsageItems: {$usageCount}");
            }
        }
    }

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

    // Issue 2: Bondowoso
    if ($issue === 'all' || $issue === 'bondowoso') {
        $this->info("\n--- 2. BONDOWOSO TNKB R2 HITAM LISTRIK ---");
        $bondowoso = PoliceStation::where('name', 'ilike', '%bondowoso%')->first();
        $tListrik = Type::where('name', 'ilike', '%TNKB%LISTRIK%')->orWhere('name', 'ilike', '%TCKB%')->get();
        foreach ($tListrik as $tl) {
            $this->line("Type found: {$tl->id} | {$tl->name}");
            if ($bondowoso) {
                $stocks = \App\Models\Stock\Stock::where('police_station_id', $bondowoso->id)->where('type_id', $tl->id)->with('stockDetails')->get();
                foreach ($stocks as $s) {
                    $this->line("  Stock {$s->id} | Qty: {$s->quantity}");
                    foreach ($s->stockDetails as $sd) {
                        $this->line("    StockDetail {$sd->id} | Qty: {$sd->quantity} | serial: {$sd->number_serial_first} - {$sd->number_serial_second}");
                    }
                }
                $ls = \App\Models\LastStock\LastStock::where('police_station_id', $bondowoso->id)->where('type_id', $tl->id)->with('lastStockDetails')->get();
                foreach ($ls as $l) {
                    $this->line("  LastStock {$l->code} | Qty: " . $l->lastStockDetails->sum('quantity'));
                    foreach ($l->lastStockDetails as $ld) {
                        $this->line("    LastStockDetail {$ld->id} | Qty: {$ld->quantity}");
                    }
                }
                $usages = \App\Models\MenuPolda\MaterialUsage\MaterialUsage::where('police_station_id', $bondowoso->id)
                    ->whereHas('materialUsageDetails', fn($q) => $q->where('type_id', $tl->id))
                    ->with(['materialUsageDetails' => fn($q) => $q->where('type_id', $tl->id)])
                    ->get();
                foreach ($usages as $u) {
                    $this->line("  Usage {$u->code} | Date: {$u->date->format('Y-m-d')} | Qty: " . $u->materialUsageDetails->sum('quantity'));
                }
            }
        }
    }

    // Issue 3: Jember
    if ($issue === 'all' || $issue === 'jember') {
        $this->info("\n--- 3. JEMBER BPKB & ALL MATERIALS ---");
        $jember = PoliceStation::where('name', 'ilike', '%jember%')->first();
        if ($jember) {
            $bpkbTypes = Type::where('name', 'ilike', '%BPKB%')->get();
            foreach ($bpkbTypes as $bt) {
                $this->line("BPKB Type: {$bt->id} | {$bt->name}");
                $stocks = \App\Models\Stock\Stock::where('police_station_id', $jember->id)->where('type_id', $bt->id)->with('stockDetails')->get();
                foreach ($stocks as $s) {
                    $this->line("  Stock {$s->id} | Qty: {$s->quantity}");
                    foreach ($s->stockDetails as $sd) {
                        $this->line("    StockDetail {$sd->id} | Qty: {$sd->quantity} | serial: {$sd->number_serial_first} - {$sd->number_serial_second}");
                    }
                }
                $ls = \App\Models\LastStock\LastStock::where('police_station_id', $jember->id)->where('type_id', $bt->id)->with('lastStockDetails')->get();
                foreach ($ls as $l) {
                    $this->line("  LastStock {$l->code} | Qty: " . $l->lastStockDetails->sum('quantity'));
                    foreach ($l->lastStockDetails as $ld) {
                        $this->line("    LastStockDetail {$ld->id} | Qty: {$ld->quantity}");
                    }
                }
                $usages = \App\Models\MenuPolda\MaterialUsage\MaterialUsage::where('police_station_id', $jember->id)
                    ->whereHas('materialUsageDetails', fn($q) => $q->where('type_id', $bt->id))
                    ->with(['materialUsageDetails' => fn($q) => $q->where('type_id', $bt->id)])
                    ->get();
                foreach ($usages as $u) {
                    $this->line("  Usage {$u->code} | Date: {$u->date->format('Y-m-d')} | Qty: " . $u->materialUsageDetails->sum('quantity'));
                }
            }

            // Check negative / 0 stocks for Jember
            $this->line("\nJember all stocks:");
            $allJemberStocks = \App\Models\Stock\Stock::where('police_station_id', $jember->id)->with('type')->get();
            foreach ($allJemberStocks as $js) {
                $this->line("  Type: {$js->type?->name} | Qty: {$js->quantity}");
            }
        }
    }

    // Issue 4: Situbondo
    if ($issue === 'all' || $issue === 'situbondo') {
        $this->info("\n--- 4. SITUBONDO TCKB R2 LISTRIK ---");
        $situbondo = PoliceStation::where('name', 'ilike', '%situbondo%')->first();
        if ($situbondo) {
            $tckbTypes = Type::where('name', 'ilike', '%TCKB%')->orWhere('name', 'ilike', '%listrik%')->get();
            foreach ($tckbTypes as $tt) {
                $this->line("Type: {$tt->id} | {$tt->name}");
                $st = \App\Models\Stock\Stock::where('police_station_id', $situbondo->id)->where('type_id', $tt->id)->first();
                $ls = \App\Models\LastStock\LastStock::where('police_station_id', $situbondo->id)->where('type_id', $tt->id)->with('lastStockDetails')->first();
                $this->line("  Stock Qty: " . ($st ? $st->quantity : 'NONE') . " | LastStock Qty: " . ($ls ? $ls->lastStockDetails->sum('quantity') : 'NONE'));
            }
        }
    }

    // Issue 5: Blitar Kota
    if ($issue === 'all' || $issue === 'blitar') {
        $this->info("\n--- 5. BLITAR KOTA STNK USAGES ---");
        $blitar = PoliceStation::where('name', 'ilike', '%blitar%')->where('name', 'ilike', '%kota%')->first();
        if ($blitar) {
            $usages = \App\Models\MenuPolda\MaterialUsage\MaterialUsage::where('police_station_id', $blitar->id)
                ->with(['materialUsageDetails.materialUsageDetailItems.service'])
                ->get();
            foreach ($usages as $u) {
                $this->line("Usage: {$u->code} | Date: {$u->date->format('Y-m-d')}");
                foreach ($u->materialUsageDetails as $ud) {
                    foreach ($ud->materialUsageDetailItems as $udi) {
                        $this->line("  Item: " . ($udi->service->name ?? 'NO SERVICE') . " | Qty: {$udi->quantity}");
                    }
                }
            }
        }
    }
});

Artisan::command('fix:issues', function () {
    $this->info("================================================================");
    $this->info("  MEMULAI PERBAIKAN 8 KENDALA POLRES & SAMSAT");
    $this->info("================================================================");

    DB::transaction(function () {
        // 1. Normalisasi Service STNK (Issue 1, 2, 4, 5, 6)
        $this->info("\n1. Normalisasi Layanan STNK...");
        $targetService = \App\Models\Service\Service::find('01a01567-4ae2-7395-b9ac-d64ad5f8b022');
        if ($targetService) {
            $targetService->update([
                'name' => 'Penerbitan STNK Roda 2 atau 3 Perpanjangan',
                'is_active' => true,
            ]);
            $this->info("   -> Updated service 01a01567-4ae2-7395-b9ac-d64ad5f8b022 to 'Penerbitan STNK Roda 2 atau 3 Perpanjangan'");
        }

        $duplicateService = \App\Models\Service\Service::find('01a0957f-2cf3-72e1-a0c4-b090ea47d0df');
        if ($duplicateService) {
            $migrated = \App\Models\MenuPolda\MaterialUsage\MaterialUsageDetailItem::where('service_id', $duplicateService->id)
                ->update(['service_id' => $targetService->id]);
            $this->info("   -> Migrated {$migrated} usage items from duplicate service to main service");
            
            \App\Models\Stock\StockDetail::where('service_id', $duplicateService->id)
                ->update(['service_id' => $targetService->id]);
            \App\Models\LastStock\LastStockDetail::where('service_id', $duplicateService->id)
                ->update(['service_id' => $targetService->id]);
            
            $duplicateService->forceDelete();
            $this->info("   -> Removed duplicate service 01a0957f-2cf3-72e1-a0c4-b090ea47d0df");
        }

        // 2. Realokasi Penggunaan STNK Blitar Kota (Issue 5)
        $this->info("\n2. Realokasi Penggunaan STNK Blitar Kota...");
        $blitarKota = PoliceStation::where('name', 'ilike', '%blitar%')->where('name', 'ilike', '%kota%')->first();
        if ($blitarKota && $targetService) {
            $usageCodes = ['MU-20260911-0046', 'MU-20260911-0047', 'MU-20260912-0004'];
            $perubahanSvc = \App\Models\Service\Service::find('01a01567-c0b0-704c-b1d5-2e356a367948'); // Penerbitan STNK Roda 2 atau 3 Perubahan
            
            $blitarItems = \App\Models\MenuPolda\MaterialUsage\MaterialUsageDetailItem::with('materialUsage')->whereHas('materialUsage', function ($q) use ($blitarKota, $usageCodes) {
                $q->where('police_station_id', $blitarKota->id)->whereIn('code', $usageCodes);
            })->where('service_id', $perubahanSvc?->id)->get();

            foreach ($blitarItems as $bi) {
                $bi->update(['service_id' => $targetService->id]);
                $this->info("   -> Reallocated Blitar item (Qty: {$bi->quantity}) under {$bi->materialUsage->code} to {$targetService->name}");
            }
        }

        // 3. Update UserType SAMSAT POLDA (Issue 6)
        $this->info("\n3. Update UserType SAMSAT POLDA...");
        $samsatTypeNames = ['STNK', 'TNKB REG', 'TNKB R2 PUTIH', 'TNKB R4 PUTIH', 'MUTASI'];
        $samsatTypeIds = Type::whereIn('name', $samsatTypeNames)->pluck('id')->toArray();
        $samsatUserType = UserType::where('name', 'SAMSAT POLDA')->first();
        if ($samsatUserType) {
            $samsatUserType->update([
                'types' => $samsatTypeIds,
                'level_user' => 2,
                'description' => 'Pelayanan Samsat Ditlantas Polda Jatim',
                'is_active' => true,
            ]);
            $this->info("   -> SAMSAT POLDA types updated to: " . implode(', ', $samsatTypeNames));
        }

        // 4. Bangkalan STNK Pengurangan 225 (Issue 7)
        $this->info("\n4. Bangkalan STNK Pengurangan 225...");
        $lsd = \App\Models\LastStock\LastStockDetail::find('01a09604-fb66-73cf-a49b-c8117e6a6462');
        if ($lsd) {
            $lsd->update(['quantity' => 573.00]);
            $this->info("   -> Bangkalan LastStockDetail 01a09604-fb66-73cf-a49b-c8117e6a6462 set to 573.00");
        }
        $sd = \App\Models\Stock\StockDetail::find('01a09604-fb85-7311-95c6-80e84077f564');
        if ($sd) {
            $sd->update(['quantity' => 573.00]);
            $this->info("   -> Bangkalan StockDetail 01a09604-fb85-7311-95c6-80e84077f564 set to 573.00");
        }
        $sd2 = \App\Models\Stock\StockDetail::find('01a09604-fba1-73cd-8124-ba3296f37de7');
        if ($sd2) {
            $sd2->update(['quantity' => 177.00]);
            $this->info("   -> Bangkalan StockDetail 01a09604-fba1-73cd-8124-ba3296f37de7 set to 177.00 (1000 - 823 usages)");
        }
        $bangkalanStock = \App\Models\Stock\Stock::find('01a07c27-8bb7-72ed-bea7-f156673da686');
        if ($bangkalanStock) {
            $bangkalanStock->update(['quantity' => 1750.00]);
            $this->info("   -> Bangkalan Stock 01a07c27-8bb7-72ed-bea7-f156673da686 set to 1750.00");
        }
        $hs = \App\Models\Stock\HistoryStock::find('01a09604-fb96-706b-8b04-380d82b802b6');
        if ($hs) {
            $hs->update(['quantity' => 573.00]);
            $this->info("   -> Bangkalan HistoryStock 01a09604-fb96-706b-8b04-380d82b802b6 set to 573.00");
        }
        // Relink Bangkalan usages to active stock details
        \App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail::where('stock_detail_id', '01a07c93-9c81-7012-ac94-cfc1e2289bed')
            ->update(['stock_detail_id' => '01a09604-fb85-7311-95c6-80e84077f564']);
        \App\Models\MenuPolda\MaterialUsage\MaterialUsageDetailItem::where('stock_detail_id', '01a07c93-9c81-7012-ac94-cfc1e2289bed')
            ->update(['stock_detail_id' => '01a09604-fb85-7311-95c6-80e84077f564']);

        \App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail::where('stock_detail_id', '01a07c93-9c9d-7175-8f7a-70cc877269c5')
            ->update(['stock_detail_id' => '01a09604-fba1-73cd-8124-ba3296f37de7']);
        \App\Models\MenuPolda\MaterialUsage\MaterialUsageDetailItem::where('stock_detail_id', '01a07c93-9c9d-7175-8f7a-70cc877269c5')
            ->update(['stock_detail_id' => '01a09604-fba1-73cd-8124-ba3296f37de7']);
        $this->info("   -> Relinked Bangkalan material usages to active stock details");

        // 5. Mojokerto Kab Stock Synchronization (Issue 8)
        $this->info("\n5. Mojokerto Kab Stock Synchronization...");
        $mojokerto = PoliceStation::where('name', 'ilike', '%mojokerto%')->where('name', 'not ilike', '%kota%')->first();
        if ($mojokerto) {
            $tMutasi = Type::where('name', 'MUTASI')->first();
            $tTnkbR2 = Type::where('name', 'TNKB R2 PUTIH')->first();
            if ($tMutasi) {
                \App\Models\Stock\Stock::where('police_station_id', $mojokerto->id)->where('type_id', $tMutasi->id)
                    ->update(['quantity' => 3048.00]);
                $this->info("   -> Mojokerto Kab MUTASI Stock set to 3048.00");
            }
            if ($tTnkbR2) {
                \App\Models\Stock\Stock::where('police_station_id', $mojokerto->id)->where('type_id', $tTnkbR2->id)
                    ->update(['quantity' => 67707.00]);
                $this->info("   -> Mojokerto Kab TNKB R2 PUTIH Stock set to 67707.00");
            }
        }

        // 6. Bondowoso TNKB R2 HITAM LISTRIK (Issue 2)
        $this->info("\n6. Bondowoso TNKB R2 Hitam Listrik Stock Awal...");
        $bondoLsd = \App\Models\LastStock\LastStockDetail::find('01a08008-d0bb-7346-8ccf-11eacf787bf0');
        if ($bondoLsd) {
            $bondoLsd->update(['quantity' => 400.00]);
            $this->info("   -> Bondowoso LastStockDetail set to 400.00");
        }
        $bondoHs = \App\Models\Stock\HistoryStock::find('01a08008-d0f0-7287-a2cc-24db6595e365');
        if ($bondoHs) {
            $bondoHs->update(['quantity' => 400.00]);
            $this->info("   -> Bondowoso HistoryStock set to 400.00");
        }
        $bondoStock = \App\Models\Stock\Stock::find('01a08008-d0cf-72c4-b4ee-12311e250a92');
        if ($bondoStock) {
            $bondoStock->update(['quantity' => 398.00]);
            $this->info("   -> Bondowoso Stock set to 398.00 (400 - 2 usages)");
        }

        // 7. Jember BPKB Restoration (Issue 3)
        $this->info("\n7. Jember BPKB Restoration...");
        $jember = PoliceStation::where('name', 'ilike', '%jember%')->first();
        $tBpkb = Type::where('name', 'BPKB')->first();
        if ($jember && $tBpkb) {
            // Restore LastStock
            $jemberLs = \App\Models\LastStock\LastStock::withTrashed()->find('01a07c08-7662-7265-98ca-3050e2a2562d');
            if ($jemberLs) {
                $jemberLs->restore();
                $jemberLs->update(['is_active' => true]);
                $this->info("   -> Restored Jember LastStock LS-20260907-0025");
            }
            $jemberLsd = \App\Models\LastStock\LastStockDetail::withTrashed()->find('01a07c08-7670-701e-8816-4f192ba6fc44');
            if ($jemberLsd) {
                $jemberLsd->restore();
                $jemberLsd->update(['quantity' => 2582.00, 'is_active' => true]);
                $this->info("   -> Restored Jember LastStockDetail (qty: 2582.00)");
            }
            $jemberHs = \App\Models\Stock\HistoryStock::withTrashed()->find('01a07c08-769e-72f6-8896-619d32b9acf7');
            if ($jemberHs) {
                $jemberHs->restore();
                $jemberHs->update(['quantity' => 2582.00, 'is_active' => true]);
                $this->info("   -> Restored Jember HistoryStock (qty: 2582.00)");
            }
            $jemberSd = \App\Models\Stock\StockDetail::withTrashed()->find('01a07c08-768f-7095-8160-1a1c00fa3238');
            if ($jemberSd) {
                $jemberSd->restore();
                $jemberSd->update(['quantity' => 1748.00, 'is_active' => true]);
                $this->info("   -> Restored Jember StockDetail (qty: 1748.00 = 2582 - 834 usages)");
            }
            $jemberStock = \App\Models\Stock\Stock::where('police_station_id', $jember->id)->where('type_id', $tBpkb->id)->first();
            if ($jemberStock) {
                $jemberStock->update(['quantity' => 1748.00]);
                $this->info("   -> Updated Jember Stock to 1748.00");
            }

            // Relink Jember BPKB usages to the restored active stock detail
            $activeJemberSdId = '01a07c08-768f-7095-8160-1a1c00fa3238';
            \App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail::whereHas('materialUsage', fn($q) => $q->where('police_station_id', $jember->id))
                ->where('type_id', $tBpkb->id)
                ->update(['stock_detail_id' => $activeJemberSdId]);
            \App\Models\MenuPolda\MaterialUsage\MaterialUsageDetailItem::whereHas('materialUsage', fn($q) => $q->where('police_station_id', $jember->id))
                ->where('type_id', $tBpkb->id)
                ->update(['stock_detail_id' => $activeJemberSdId]);
            $this->info("   -> Relinked all Jember BPKB usages to active StockDetail {$activeJemberSdId}");
        }

        // 8. Situbondo TCKB R2 Listrik (Issue 4)
        $this->info("\n8. Situbondo TCKB R2 Listrik verification...");
        $situbondo = PoliceStation::where('name', 'ilike', '%situbondo%')->first();
        $tTckbListrik = Type::where('name', 'TCKB R2 LISTRIK')->first();
        if ($situbondo && $tTckbListrik) {
            $sitStock = \App\Models\Stock\Stock::where('police_station_id', $situbondo->id)->where('type_id', $tTckbListrik->id)->first();
            if (!$sitStock) {
                $sitStock = \App\Models\Stock\Stock::create([
                    'type_id' => $tTckbListrik->id,
                    'police_station_id' => $situbondo->id,
                    'quantity' => 0,
                    'is_active' => true,
                ]);
            } else {
                $sitStock->update(['is_active' => true]);
            }
            $sitSd = \App\Models\Stock\StockDetail::where('police_station_id', $situbondo->id)->where('type_id', $tTckbListrik->id)->first();
            if ($sitSd) {
                $sitSd->update(['is_active' => true]);
            }
            $this->info("   -> Situbondo TCKB R2 Listrik stock record confirmed active (qty: 0)");
        }
    });

    $this->info("\n================================================================");
    $this->info("  SEMUA 8 KENDALA BERHASIL DIPERBAIKI!");
    $this->info("================================================================");
})->purpose('Eksekusi perbaikan komprehensif untuk 8 kendala Polres & Samsat');


