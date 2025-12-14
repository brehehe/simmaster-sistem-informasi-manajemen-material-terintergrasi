<?php

namespace Database\Seeders\Type;

use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Illuminate\Database\Seeder;

class TypeDetailSeeder extends Seeder
{
    public function run(): void
    {
        $datas = [
            'TNKB REG' => [
                'R2 PUTIH',
                'R2 MERAH',
                'R2 TCKB',
                'R4 PUTIH',
                'R4 MERAH',
                'R4 KUNING',
                'R4 TCKB',
            ],
            'TNKB LISTRIK' => [
                'R2 HITAM',
                'R2 MERAH',
                'R2 PUTIH',
                'R4 HITAM',
                'R4 MERAH',
                'R4 KUNING',
                'R4 PUTIH',
            ],
        ];

        foreach($datas as $typeName => $details) {
            $type = Type::where('name', $typeName)->first();

            if ($type) {
                foreach ($details as $detail) {
                    TypeDetail::create([
                        'type_id' => $type->id,
                        'name' => $detail,
                    ]);
                }
            }
        }
    }
}
