<?php

namespace App\Actions\MenuPolda;

use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;

class CreateMaterialDamageAction
{
    public function execute(
        array $headerData,
        array $details,
        array $mappedStockIds,
        string $typeId,
        ?string $materialDamageId = null
    ): MaterialDamage {
        return DB::transaction(function () use ($headerData, $details, $mappedStockIds, $typeId, $materialDamageId) {
            $stockService = new StockService();
            $isEditMode = !empty($materialDamageId);

            if ($isEditMode) {
                $materialDamage = MaterialDamage::with('materialDamageDetails')->findOrFail($materialDamageId);

                // Revert previous stock deduction before re-processing
                $stockService->deleteMaterialDamage($materialDamage);

                $materialDamage->update($headerData);
                $materialDamage->materialDamageDetails()->delete();
            } else {
                if (empty($headerData['code']) || MaterialDamage::withTrashed()->where('code', $headerData['code'])->exists()) {
                    $headerData['code'] = MaterialDamage::generateCode();
                }
                $materialDamage = MaterialDamage::create($headerData);
            }

            foreach ($details as $index => $detail) {
                $materialDamage->materialDamageDetails()->create([
                    'stock_detail_id'      => $mappedStockIds[$index] ?? null,
                    'type_id'              => $typeId,
                    'type_detail_id'       => !empty($detail['type_detail_id']) ? $detail['type_detail_id'] : null,
                    'rack_id'              => null,
                    'item_code'            => !empty($detail['item_code']) ? $detail['item_code'] : null,
                    'number_serial_first'  => !empty($detail['number_serial_first']) ? $detail['number_serial_first'] : null,
                    'number_serial_second' => !empty($detail['number_serial_second']) ? $detail['number_serial_second'] : null,
                    'quantity'             => (float)$detail['quantity'],
                    'damage_type'          => $detail['damage_type'] ?? 'damaged',
                    'reason'               => $detail['reason'] ?? '',
                    'description'          => $detail['description'] ?? '',
                    'is_active'            => true,
                ]);
            }

            $stockService = new StockService();
            $stockService->processMaterialDamage($materialDamage);

            return $materialDamage;
        });
    }

    public static function run(
        array $headerData,
        array $details,
        array $mappedStockIds,
        string $typeId,
        ?string $materialDamageId = null
    ): MaterialDamage {
        return (new self())->execute($headerData, $details, $mappedStockIds, $typeId, $materialDamageId);
    }
}
