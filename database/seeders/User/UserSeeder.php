<?php

namespace Database\Seeders\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Police\RegionalPolice;
use App\Models\Police\PoliceStation;
use App\Models\User\UserType;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password'); // hash sekali saja

        // Ambil semua user type sekali saja
        $userTypes = UserType::select('id', 'name','level_user')->get();

        // Eager load policeStations, biar tidak query ulang per Polda
        $regionalPolices = RegionalPolice::select('id', 'name')
            ->with(['policeStations:id,regional_police_id,name'])
            ->get();

        foreach ($regionalPolices as $regionalPolice) {

            $email = Str::slug($regionalPolice->name) . '@sbst.test';

            $user = User::create([
                'level_menu'        => 1,
                'name'              => $regionalPolice->name,
                'email'             => $email,
                'password'          => $password,
                'regional_police_id'=> $regionalPolice->id,
            ]);

            // pakai assignRole tidak apa-apa, ini cuma 1x per polda
            $user->assignRole('Polda');

            foreach ($userTypes as $userType) {

                    $email = Str::slug($userType->name . '-' . $regionalPolice->name) . '@sbst.test';

                    $user = User::create([
                        'level_menu'        => $userType->level_user === 1 ? 2 : 3,
                        'name'             => $userType->name . ' ' . $regionalPolice->name,
                        'email'            => $email,
                        'password'         => $password,
                        'regional_police_id'=> $regionalPolice->id,
                        'user_type_id'     => $userType->id,
                    ]);

                    $user->assignRole('Polda');
                }

            // pakai relasi yang sudah di-with()
            foreach ($regionalPolice->policeStations as $policeStation) {

                $email = Str::slug($policeStation->name) . '@sbst.test';

                $user = User::create([
                    'level_menu'        => 1,
                    'name'             => $policeStation->name,
                    'email'            => $email,
                    'password'         => $password,
                    'regional_police_id'=> $regionalPolice->id,
                    'police_station_id'=> $policeStation->id,
                ]);

                $user->assignRole('Polres');

                foreach ($userTypes as $userType) {

                    $email = Str::slug($userType->name . '-' . $policeStation->name) . '@sbst.test';

                    $user = User::create([
                        'level_menu'        => $userType->level_user === 1 ? 2 : 3,
                        'name'             => $userType->name . ' ' . $policeStation->name,
                        'email'            => $email,
                        'password'         => $password,
                        'regional_police_id'=> $regionalPolice->id,
                        'police_station_id'=> $policeStation->id,
                        'user_type_id'     => $userType->id,
                    ]);

                    $user->assignRole('Polres');
                }
            }
        }
    }
}
