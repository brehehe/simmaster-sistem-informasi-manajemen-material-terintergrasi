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
            $stockService = app(StockService::class);
            $isEditMode = !empty($materialUsageId);

            if ($isEditMode) {
                $materialUsage = MaterialUsage::with('materialUsageDetails')->findOrFail($materialUsageId);

                // Revert previous stock deduction before re-processing
                $stockService->deleteMaterialUsage($materialUsage);

                $materialUsage->update($headerData);

                foreach ($materialUsage->materialUsageDetails as $oldDetail) {
                    $oldDetail->materialUsageDetailItems()->delete();
                }
                $materialUsage->materialUsageDetails()->delete();
            } else {
                if (empty($headerData['code']) || MaterialUsage::withTrashed()->where('code', $headerData['code'])->exists()) {
                    $headerData['code'] = MaterialUsage::generateCode();
                }
                $materialUsage = MaterialUsage::create($headerData);
            }

            $validDetailsCount = 0;

            foreach ($details as $detail) {
                if (empty($detail['stock_detail_id'])) {
                    continue;
                }

                $stockDetail = StockDetail::findOrFail($detail['stock_detail_id']);
                $qty = (float)($detail['quantity'] ?? 0);

                if ($qty <= 0) {
                    throw new \Exception('Jumlah material yang digunakan harus lebih dari 0.');
                }

                if ($qty > $stockDetail->quantity) {
                    $itemLabel = $stockDetail->code ? $stockDetail->code . ' ' : '';
                    $itemLabel .= $stockDetail->number_serial_first ?: ($stockDetail->type?->name ?? 'Material');
                    throw new \Exception("Jumlah ({$qty}) melebihi stok yang tersedia ({$stockDetail->quantity}) untuk {$itemLabel}.");
                }

                $currentTypeId = $detail['type_id'] ?? $typeId;

                $usageDetail = $materialUsage->materialUsageDetails()->create([
                    'stock_detail_id' => $stockDetail->id,
                    'type_id' => $currentTypeId,
                    'type_detail_id' => !empty($detail['type_detail_id']) ? $detail['type_detail_id'] : null,
                    'rack_id' => $stockDetail->rack_id,
                    'item_code' => $detail['item_code'] ?? ($stockDetail->code ?? ''),
                    'number_serial_first' => $detail['number_serial_first'] ?? ($stockDetail->number_serial_first ?? ''),
                    'number_serial_second' => $detail['number_serial_second'] ?? ($stockDetail->number_serial_second ?? ''),
                    'quantity' => $qty,
                    'usage_type' => $detail['usage_type'] ?? 'Material Digunakan',
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
                    'item_code' => $detail['item_code'] ?? ($stockDetail->code ?? ''),
                    'number_serial_first' => $detail['number_serial_first'] ?? ($stockDetail->number_serial_first ?? ''),
                    'number_serial_second' => $detail['number_serial_second'] ?? ($stockDetail->number_serial_second ?? ''),
                    'quantity' => $qty,
                    'usage_type' => $detail['usage_type'] ?? 'Material Digunakan',
                    'description' => $detail['description'] ?? '',
                    'is_active' => true,
                ]);

                $validDetailsCount++;
            }

            if ($validDetailsCount === 0) {
                throw new \Exception('Tidak ada detail item material yang valid untuk disimpan.');
            }

            // Sync stock reduction and stock history
            $materialUsage->load('materialUsageDetails');
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
