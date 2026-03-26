<?php

namespace Database\Seeders;

use App\Models\Police\RegionalPolice;
use App\Models\Target\Target;
use App\Models\Target\TargetDetail;
use App\Models\Type\Type;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class TargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = (int) now()->format('Y');

        $target = Target::create([
            'name' => "Target Ditlantas {$year}",
            'year' => $year,
            'description' => 'Dummy target untuk keperluan pengujian',
            'is_active' => true,
        ]);

        $types = Type::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $regionalPolice = RegionalPolice::query()
            ->with(['policeStations' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        foreach ($regionalPolice as $regional) {
            $this->seedRow($target, $regional->name, $regional->id, null, $types);

            foreach ($regional->policeStations as $station) {
                $this->seedRow($target, $station->name, $station->regional_police_id, $station->id, $types);
            }
        }
    }

    private function seedRow(Target $target, string $label, ?string $regionalPoliceId, ?string $policeStationId, Collection $types): void
    {
        foreach ($types as $type) {
            TargetDetail::create([
                'name' => $label,
                'target_id' => $target->id,
                'regional_police_id' => $regionalPoliceId,
                'police_station_id' => $policeStationId,
                'type_id' => $type->id,
                'type_detail_id' => null,
                'quantity' => random_int(10000, 5000000),
                'description' => $target->description,
                'is_active' => true,
            ]);
        }
    }
}
