<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;

class PoliceStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $poldaJatim = RegionalPolice::where('name', 'Polda Jatim')->first();

        $datas = [
            'Polrestabes Surabaya',
            'Polresta Malang Kota',
            'Polres Malang',
            'Polres Probolinggo',
            'Polres Pasuruan',
            'Polres Lumajang',
            'Polres Bondowoso',
            'Polres Situbondo',
            'Polres Jember',
            'Polresta Banyuwangi',
            'Polres Kediri Kota',
            'Polres Kediri',
            'Polres Blitar Kota',
            'Polres Tulungagung',
            'Polres Nganjuk',
            'Polres Trenggalek',
            'Polres Madiun Kota',
            'Polres Madiun',
            'Polres Ngawi',
            'Polres Magetan',
            'Polres Ponorogo',
            'Polres Pacitan',
            'Polres Bojonegoro',
            'Polres Tuban',
            'Polres Lamongan',
            'Polres Pamekasan',
            'Polres Bangkalan',
            'Polres Sampang',
            'Polres Sumenep',
            'Polres Gresik',
            'Polresta Sidoarjo',
            'Polres Mojokerto',
            'Polres Jombang',
            'Polres Pelabuhan Tanjung Perak',
            'Polres Batu',
            'Polres Probolinggo Kota',
            'Polres Blitar',
            'Polres Pasuruan Kota',
            'Polres Mojokerto Kota',
        ];

        foreach ($datas as $data) {
            PoliceStation::create([
                'name' => $data,
                'regional_police_id' => $poldaJatim?->id,
            ]);
        }
    }
}
