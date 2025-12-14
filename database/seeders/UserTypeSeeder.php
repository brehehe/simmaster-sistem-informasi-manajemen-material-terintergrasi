<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User\UserType;
use App\Models\Type\Type;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Get all type IDs from database
        $allTypeIds = Type::pluck('id')->toArray();

        // Get specific type IDs by name
        $getTypeIds = function (array $names) {
            return Type::whereIn('name', $names)->pluck('id')->toArray();
        };

        // Define user types with their corresponding type IDs
        $datas = [
            'BAMAT' => $allTypeIds, // BAMAT has access to all types
            'BAURTNKB' => $getTypeIds(['TNKB REG', 'TNKB LISTRIK', 'NRKB NOPIL', 'NRKB NOPIL LISTRIK']),
            'BAURBPKB' => $getTypeIds(['E-BPKB', 'BPKB', 'MUTASI']),
            'BAURSTNK' => $getTypeIds(['STNK']),
            'BAURSTCK' => $getTypeIds(['STCK']),
            'BAURSIMCARD' => $getTypeIds(['SIM CARD']),
        ];

        foreach ($datas as $name => $types) {
            UserType::create([
                'name' => $name,
                'types' => $types, // No need for json_encode, model casts it automatically
            ]);
        }
    }
}
