<?php

namespace App\Services;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\Police\PoliceStation;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ReportingComplianceService
{
    public const TIMEZONE = 'Asia/Jakarta';

    public function status(string $date, ?string $submittedAt, ?CarbonImmutable $now = null): array
    {
        $day = CarbonImmutable::parse($date, self::TIMEZONE)->startOfDay();
        $submitted = $submittedAt ? CarbonImmutable::parse($submittedAt, config('app.timezone'))->setTimezone(self::TIMEZONE) : null;
        $reference = ($submitted ?? $now ?? CarbonImmutable::now(self::TIMEZONE))->setTimezone(self::TIMEZONE)->startOfDay();
        $delay = max(0, (int) $day->diffInDays($reference, false));
        $color = $delay >= 3 ? 'red' : ($delay >= 1 ? 'yellow' : ($submitted ? 'green' : 'pending'));

        return [
            'color' => $color,
            'delay' => $delay,
            'submitted_at' => $submitted?->format('d/m/Y H:i').'',
            'label' => $submitted ? ($delay === 0 ? 'Tertib' : 'Terlambat '.$delay.' hari') : 'Belum input',
            'reported' => $submitted !== null,
        ];
    }

    public static function sources(): array
    {
        return [
            'usage' => ['label' => 'Pemakaian', 'model' => MaterialUsage::class, 'date' => 'date', 'relation' => 'materialUsageDetails'],
            'opening' => ['label' => 'Stok awal', 'model' => \App\Models\LastStock\LastStock::class, 'date' => 'date', 'relation' => 'lastStockDetails'],
            'reception' => ['label' => 'Penerimaan', 'model' => \App\Models\Reception\Reception::class, 'date' => 'date', 'relation' => 'receptionDetails'],
            'damage' => ['label' => 'Material rusak', 'model' => \App\Models\MenuPolda\MaterialDamage\MaterialDamage::class, 'date' => 'date', 'relation' => 'materialDamageDetails'],
            'subsidy' => ['label' => 'Subsidi', 'model' => \App\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy::class, 'date' => 'subsidy_date', 'relation' => 'materialSubsidyDetails'],
            'mutation' => ['label' => 'Mutasi keluar', 'model' => \App\Models\MenuPolda\MutationStock\MutationStock::class, 'date' => 'mutation_date', 'relation' => 'mutationStockDetails', 'polres' => 'sender_police_station_id', 'polda' => 'sender_regional_police_id'],
            'shipment' => ['label' => 'Pengiriman', 'model' => \App\Models\MenuPolda\MaterialShipment\MaterialShipment::class, 'date' => 'shipment_date', 'relation' => 'materialShipmentDetails', 'polres' => null, 'polda' => 'sender_regional_police_id'],
            'opname' => ['label' => 'Stok opname', 'model' => \App\Models\StockOpname\StockOpname::class, 'date' => 'opname_date', 'relation' => 'stockOpnameDetails'],
            'rack' => ['label' => 'Penempatan rak', 'model' => \App\Models\MenuPolda\RackAssignment\RackAssignment::class, 'date' => 'date', 'relation' => 'rackAssignmentDetails'],
        ];
    }

    public function summarize(string $date, array $inputs, string $source = 'all'): array
    {
        $latest = null;
        $count = 0;
        foreach ($inputs as $input) {
            $count += $input['count'];
            if ($input['submitted_at'] && ($latest === null || $input['submitted_at'] > $latest)) {
                $latest = $input['submitted_at'];
            }
        }
        $result = $this->status($date, $latest);
        // Event-based documents do not have a daily reporting obligation.
        if (! $latest && ! in_array($source, ['all', 'usage'], true)) {
            $result['color'] = 'pending';
            $result['label'] = 'Tidak ada input';
        }

        return array_merge($result, ['report_count' => $count, 'inputs' => $inputs]);
    }

    public function rows(string $date, string $source = 'all'): Collection
    {
        $sources = self::sources();
        if ($source !== 'all') {
            if (! isset($sources[$source])) {
                throw new \InvalidArgumentException('Jenis input tidak valid.');
            }
            $sources = [$source => $sources[$source]];
        }
        $reports = [];
        foreach ($sources as $key => $definition) {
            $polres = array_key_exists('polres', $definition) ? $definition['polres'] : 'police_station_id';
            $polda = $definition['polda'] ?? 'regional_police_id';
            $query = $definition['model']::query()
                ->where($definition['date'], $date)->where('is_active', true)
                ->whereHas($definition['relation'], function ($query) use ($key) {
                    if ($key === 'usage') {
                        $query->where('is_active', true);
                    }
                })
                ->selectRaw(($polres ? $polres : 'NULL').' as station_id, '.$polda.' as region_id, MAX(created_at) as submitted_at, COUNT(*) as report_count')
                ->groupBy($polda);
            if ($polres) {
                $query->groupBy($polres);
            }
            foreach ($query->get() as $report) {
                $owner = $report->station_id ? 'polres:'.$report->station_id : 'polda:'.$report->region_id;
                $reports[$owner][$key] = ['count' => (int) $report->report_count, 'submitted_at' => $report->submitted_at];
            }
        }
        $units = \App\Models\Police\RegionalPolice::where('is_active', true)->orderBy('name')->get(['id', 'name'])
            ->map(fn ($unit) => ['id' => 'polda:'.$unit->id, 'name' => $unit->name]);
        $units = $units->concat(PoliceStation::where('is_active', true)->orderBy('name')->get(['id', 'name'])
            ->map(fn ($unit) => ['id' => 'polres:'.$unit->id, 'name' => $unit->name]));

        return $units->map(function ($unit) use ($reports, $date, $source) {
            return array_merge($unit, $this->summarize($date, $reports[$unit['id']] ?? [], $source));
        });
    }
}
