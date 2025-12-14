<?php

namespace Database\Seeders\Type;

use App\Models\Type\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $datas = [
            'SIM CARD',
            'STNK',
            'STCK',
            'E-BPKB',
            'BPKB',
            'MUTASI',
            'TNKB REG',
            'TNKB LISTRIK',
            'NRKB NOPIL',
            'NRKB NOPIL LISTRIK',
        ];

        foreach($datas as $data) {
            Type::create([
                'name' => $data,
            ]);
        }
    }
}
