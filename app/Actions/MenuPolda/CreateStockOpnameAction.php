<?php

namespace App\Actions\MenuPolda;

use App\Models\StockOpname\StockOpname;
use App\Models\StockOpname\StockOpnameDetail;
use Illuminate\Support\Facades\DB;

class CreateStockOpnameAction
{
    /**
     * Execute stock opname creation or update with calculated differences.
     */
    public function execute(
        array $headerData,
        array $details,
        ?string $opnameId = null
    ): StockOpname {
        return DB::transaction(function () use ($headerData, $details, $opnameId) {
            $isEditMode = !empty($opnameId);

            if ($isEditMode) {
                $opname = StockOpname::findOrFail($opnameId);
                $opname->update($headerData);
                $opname->stockOpnameDetails()->delete();
            } else {
                if (empty($headerData['code']) || StockOpname::withTrashed()->where('code', $headerData['code'])->exists()) {
                    $isPolda = !empty($headerData['regional_police_id']) && empty($headerData['police_station_id']);
                    $headerData['code'] = StockOpname::generateCode($isPolda);
                }
                $opname = StockOpname::create(array_merge($headerData, [
                    'status' => $headerData['status'] ?? 'draft',
                    'is_active' => true,
                ]));
            }

            foreach ($details as $detail) {
                if (empty($detail['stock_detail_id'])) {
                    continue;
                }

                $systemQty = (float)($detail['system_quantity'] ?? 0);
                $physicalQty = (float)($detail['physical_quantity'] ?? 0);
                $difference = (float)($detail['difference'] ?? ($physicalQty - $systemQty));

                StockOpnameDetail::create([
                    'stock_opname_id' => $opname->id,
                    'stock_detail_id' => $detail['stock_detail_id'],
                    'type_id' => $detail['type_id'],
                    'type_detail_id' => !empty($detail['type_detail_id']) ? $detail['type_detail_id'] : null,
                    'rack_id' => !empty($detail['rack_id']) ? $detail['rack_id'] : null,
                    'code' => !empty($detail['code']) ? $detail['code'] : '-',
                    'number_serial_first' => $detail['number_serial_first'] ?? null,
                    'number_serial_second' => $detail['number_serial_second'] ?? null,
                    'system_quantity' => $systemQty,
                    'physical_quantity' => $physicalQty,
                    'difference' => $difference,
                    'notes' => $detail['notes'] ?? '',
                    'is_active' => true,
                ]);
            }

            return $opname;
        });
    }

    /**
     * Static helper for clean invocation.
     */
    public static function run(
        array $headerData,
        array $details,
        ?string $opnameId = null
    ): StockOpname {
        return (new self())->execute($headerData, $details, $opnameId);
    }
}
