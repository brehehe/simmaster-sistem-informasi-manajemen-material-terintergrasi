<?php

namespace Database\Seeders;

use App\Models\Police\PoliceStation;
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

        $this->command->info('Memulai pembuatan/pembaruan akun BAMAT untuk seluruh Polres...');

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
            $this->command->info('-> UserType BAMAT berhasil dibuat.');
        } else {
            $bamatUserType->update([
                'types' => $allTypeIds,
                'is_active' => true,
            ]);
        }

        // 2. Query seluruh Polres
        $stations = PoliceStation::orderBy('name')->get();
        $this->command->info("-> Ditemukan {$stations->count()} Polres.");

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

        $this->command->table(
            ['No', 'Nama Polres', 'Nama Akun', 'Email Login', 'Password', 'Role'],
            $tableRows
        );

        $this->command->info("SELESAI! Total {$stations->count()} akun BAMAT Polres aktif ({$createdCount} dibuat baru, {$updatedCount} diperbarui).");
    }
}
