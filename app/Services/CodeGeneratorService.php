<?php

namespace App\Services;

use App\Models\MenuPolda\RackAssignment\RackAssignment;
use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\MenuPolda\MutationStock\MutationStock;
use App\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy;
use App\Models\StockOpname\StockOpname;
use App\Models\Reception\Reception;
use Illuminate\Database\Eloquent\Model;

class CodeGeneratorService
{
    /**
     * Generate unique prefix-date-number sequence for any model.
     *
     * @param class-string<Model> $modelClass
     * @param string $prefix
     * @param int $padding
     * @return string
     */
    public static function generate(string $modelClass, string $prefix, int $padding = 4): string
    {
        $date = now()->format('Ymd');
        $fullPrefix = "{$prefix}-{$date}-";

        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = method_exists($modelClass, 'withTrashed') ? $modelClass::withTrashed() : $modelClass::query();

        $lastRecord = $query
            ->where('code', 'like', $fullPrefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastRecord && preg_match('/-(\d+)$/', (string)$lastRecord->code, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        $code = $fullPrefix . str_pad($nextNumber, $padding, '0', STR_PAD_LEFT);

        // Safeguard collision loop to guarantee absolute uniqueness
        while ($query->where('code', $code)->exists()) {
            $nextNumber++;
            $code = $fullPrefix . str_pad($nextNumber, $padding, '0', STR_PAD_LEFT);
        }

        return $code;
    }
}
