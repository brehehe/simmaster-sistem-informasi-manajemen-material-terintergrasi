<?php

namespace Database\Seeders;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Type\Type;
use App\Models\User;
use App\Models\User\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BamatUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = 'password';
        $hashedPassword = Hash::make($defaultPassword);

        $this->command->info('Memulai sinkronisasi akun BAMAT & migrasi domain @armaster.net...');

        // 0. Update semua email lama @sbst.test menjadi @armaster.net
        $oldTestUsers = User::withTrashed()->where('email', 'like', '%@sbst.test')->get();
        foreach ($oldTestUsers as $oldUser) {
            $newEmail = str_replace('@sbst.test', '@armaster.net', $oldUser->email);
            $oldUser->update(['email' => $newEmail]);
        }
        $this->command->info("-> Berhasil migrasi {$oldTestUsers->count()} email dari @sbst.test ke @armaster.net.");

        // 1. Pastikan User Types Polda & Polres tersedia
        $allTypeIds = Type::pluck('id')->toArray();
        $getTypeIds = fn(array $names) => Type::whereIn('name', $names)->pluck('id')->toArray();

        $userTypeDefinitions = [
            'BAMAT' => ['types' => $allTypeIds, 'level' => 1, 'desc' => 'Bintara Administrasi Materiel SBST'],
            'SAMSAT POLDA' => ['types' => $getTypeIds(['STNK', 'TNKB REG', 'TNKB R2 PUTIH', 'TNKB R4 PUTIH', 'BPKB', 'E-BPKB', 'STCK']), 'level' => 2, 'desc' => 'Pelayanan Samsat Ditlantas Polda Jatim'],
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

        $polda = RegionalPolice::where('name', 'like', '%Polda%')->first() ?? RegionalPolice::first();

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

        $this->command->info("\n--- 1. DAFTAR AKUN 4 BAMAT SAMSAT POLDA ---");
        $this->command->table(
            ['No', 'Nama Akun', 'Email Login', 'Password', 'Role', 'Keterangan'],
            $samsatTableRows
        );

        $this->command->info("\n--- 2. DAFTAR AKUN 3 BAMAT PELAYANAN POLDA (SIM, STNK, BPKB) ---");
        $this->command->table(
            ['No', 'Nama Akun', 'Email Login', 'Password', 'Role', 'User Type'],
            $bamatPoldaTableRows
        );

        $this->command->info("\n--- 3. DAFTAR AKUN BAMAT POLRES JATIM ({$stations->count()} POLRES) ---");
        $this->command->table(
            ['No', 'Nama Polres', 'Nama Akun', 'Email Login', 'Password', 'Role'],
            $polresTableRows
        );

        $this->command->info("\n================================================================");
        $this->command->info("SELESAI! Seluruh akun berhasil dimigrasikan ke domain @armaster.net.");
        $this->command->info("Password default untuk semua akun di atas: {$defaultPassword}");
        $this->command->info("================================================================");
    }
}
