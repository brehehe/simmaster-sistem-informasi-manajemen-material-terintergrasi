<?php

namespace App\Actions\MenuPolda;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetailItem;
use App\Models\Stock\StockDetail;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;

class CreateMaterialUsageAction
{
    /**
     * Execute material usage creation or update with stock adjustment.
     */
    public function execute(
        array $headerData,
        array $details,
        ?string $typeId = null,
        ?string $materialUsageId = null
    ): MaterialUsage {
        return DB::transaction(function () use ($headerData, $details, $typeId, $materialUsageId) {
            $isEditMode = !empty($materialUsageId);

            if ($isEditMode) {
                $materialUsage = MaterialUsage::findOrFail($materialUsageId);
                $materialUsage->update($headerData);

                foreach ($materialUsage->materialUsageDetails as $oldDetail) {
                    $oldDetail->materialUsageDetailItems()->delete();
                }
                $materialUsage->materialUsageDetails()->delete();
            } else {
                $materialUsage = MaterialUsage::create($headerData);
            }

            foreach ($details as $detail) {
                if (empty($detail['stock_detail_id'])) {
                    continue;
                }

                $stockDetail = StockDetail::findOrFail($detail['stock_detail_id']);

                if (isset($detail['available_quantity']) && $detail['quantity'] > $detail['available_quantity']) {
                    throw new \Exception('Jumlah melebihi stok tersedia.');
                }

                $currentTypeId = $detail['type_id'] ?? $typeId;

                $usageDetail = $materialUsage->materialUsageDetails()->create([
                    'stock_detail_id' => $stockDetail->id,
                    'type_id' => $currentTypeId,
                    'type_detail_id' => !empty($detail['type_detail_id']) ? $detail['type_detail_id'] : null,
                    'rack_id' => $stockDetail->rack_id,
                    'item_code' => $detail['item_code'] ?? '',
                    'number_serial_first' => $detail['number_serial_first'] ?? '',
                    'number_serial_second' => $detail['number_serial_second'] ?? '',
                    'quantity' => (float)$detail['quantity'],
                    'usage_type' => $detail['usage_type'] ?? 'usage',
                    'description' => $detail['description'] ?? '',
                    'is_active' => true,
                ]);

                // Create flattened item
                MaterialUsageDetailItem::create([
                    'material_usage_id' => $materialUsage->id,
                    'material_usage_detail_id' => $usageDetail->id,
                    'stock_detail_id' => $stockDetail->id,
                    'service_id' => !empty($detail['service_id']) ? $detail['service_id'] : null,
                    'service_detail_id' => !empty($detail['service_detail_id']) ? $detail['service_detail_id'] : null,
                    'type_id' => $currentTypeId,
                    'type_detail_id' => !empty($detail['type_detail_id']) ? $detail['type_detail_id'] : null,
                    'rack_id' => $stockDetail->rack_id,
                    'item_code' => $detail['item_code'] ?? '',
                    'number_serial_first' => $detail['number_serial_first'] ?? '',
                    'number_serial_second' => $detail['number_serial_second'] ?? '',
                    'quantity' => (float)$detail['quantity'],
                    'usage_type' => $detail['usage_type'] ?? 'usage',
                    'description' => $detail['description'] ?? '',
                    'is_active' => true,
                ]);
            }

            // Sync stock reduction and stock history
            $stockService = app(StockService::class);
            $stockService->processMaterialUsage($materialUsage);

            return $materialUsage;
        });
    }

    /**
     * Static helper for clean invocation.
     */
    public static function run(
        array $headerData,
        array $details,
        ?string $typeId = null,
        ?string $materialUsageId = null
    ): MaterialUsage {
        return (new self())->execute($headerData, $details, $typeId, $materialUsageId);
    }
}
