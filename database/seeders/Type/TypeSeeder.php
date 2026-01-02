<?php

namespace Database\Seeders\Type;

use App\Models\Type\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $datas = [
            'SIM CARD' => true,
            'STNK' => true,
            'STCK' => true,
            'E-BPKB' => true,
            'BPKB' => true,
            'MUTASI' => false,
            'TNKB REG' => false,
            'TNKB LISTRIK' => false,
            'NRKB NOPIL' => false,
            'NRKB NOPIL LISTRIK' => false,
        ];

        foreach($datas as $name => $is_with_serial_number) {
            Type::create([
                'name' => $name,
                'is_with_serial_number' => $is_with_serial_number,
            ]);
        }
    }
}
