<?php

namespace App\Actions\MenuPolda;

use App\Models\MenuPolda\RackAssignment\RackAssignment;
use App\Models\MenuPolda\RackAssignment\RackAssignmentDetail;
use App\Models\Stock\StockDetail;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;

class CreateRackAssignmentAction
{
    /**
     * Execute rack assignment creation or update with stock adjustment.
     */
    public function execute(
        array $headerData,
        array $details,
        ?string $typeId = null,
        ?string $rackAssignmentId = null
    ): RackAssignment {
        return DB::transaction(function () use ($headerData, $details, $typeId, $rackAssignmentId) {
            $isEditMode = !empty($rackAssignmentId);

            if ($isEditMode) {
                $rackAssignment = RackAssignment::findOrFail($rackAssignmentId);
                $rackAssignment->update($headerData);
                $rackAssignment->rackAssignmentDetails()->delete();
            } else {
                $rackAssignment = RackAssignment::create($headerData);
            }

            foreach ($details as $detail) {
                if (empty($detail['stock_detail_id'])) {
                    continue;
                }

                $stockDetail = StockDetail::findOrFail($detail['stock_detail_id']);

                if (isset($detail['available_quantity']) && $detail['quantity'] > $detail['available_quantity']) {
                    throw new \Exception('Quantity melebihi stok tersedia.');
                }

                $rackAssignment->rackAssignmentDetails()->create([
                    'stock_detail_id' => $stockDetail->id,
                    'type_id' => $detail['type_id'] ?? $typeId,
                    'type_detail_id' => !empty($detail['type_detail_id']) ? $detail['type_detail_id'] : null,
                    'from_rack_id' => !empty($detail['from_rack_id']) ? $detail['from_rack_id'] : null,
                    'to_rack_id' => !empty($detail['to_rack_id']) ? $detail['to_rack_id'] : null,
                    'item_code' => $detail['item_code'] ?? '',
                    'number_serial_first' => $detail['number_serial_first'] ?? '',
                    'number_serial_second' => $detail['number_serial_second'] ?? '',
                    'quantity' => (float)$detail['quantity'],
                    'description' => $detail['notes'] ?? ($detail['description'] ?? ''),
                    'is_active' => true,
                ]);
            }

            $stockService = app(StockService::class);
            $stockService->processRackAssignment($rackAssignment);

            return $rackAssignment;
        });
    }

    /**
     * Static helper for clean invocation.
     */
    public static function run(
        array $headerData,
        array $details,
        ?string $typeId = null,
        ?string $rackAssignmentId = null
    ): RackAssignment {
        return (new self())->execute($headerData, $details, $typeId, $rackAssignmentId);
    }
}
