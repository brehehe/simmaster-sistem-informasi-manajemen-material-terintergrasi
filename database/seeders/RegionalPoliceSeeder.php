<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Police\RegionalPolice;

class RegionalPoliceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = ['Polda Jatim'];

        foreach($datas as $data) {
            RegionalPolice::create([
                'name' => $data,
            ]);
        }
    }
}
