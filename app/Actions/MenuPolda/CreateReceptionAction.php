<?php

namespace App\Actions\MenuPolda;

use App\Models\Reception\Reception;
use App\Models\Reception\ReceptionDetail;
use App\Models\Reception\ReceptionDetailItem;
use App\Models\Stock\HistoryStockDetail;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;

class CreateReceptionAction
{
    /**
     * Execute reception creation / update with detail items and stock synchronization.
     */
    public function execute(
        array $headerData,
        array $details,
        array $supportingMaterials = [],
        ?string $receptionId = null
    ): Reception {
        return DB::transaction(function () use ($headerData, $details, $supportingMaterials, $receptionId) {
            $isEditMode = !empty($receptionId);
            $stockService = new StockService();

            if ($isEditMode) {
                $reception = Reception::findOrFail($receptionId);
                $reception->update($headerData);

                // Revert previous stock before re-processing
                $stockService->deleteReceptionStock($reception);
                $reception->receptionDetails()->delete();
            } else {
                $reception = Reception::create($headerData);
            }

            // Create parent ReceptionDetail
            $totalQuantity = collect($details)->sum('quantity') + collect($supportingMaterials)->sum('quantity');
            $receptionDetail = ReceptionDetail::create([
                'reception_id' => $reception->id,
                'type_id' => $reception->type_id ?: null,
                'type_detail_id' => null,
                'code' => null,
                'number_serial_first' => null,
                'number_serial_second' => null,
                'quantity' => $totalQuantity,
                'description' => $reception->description ?? '',
                'is_active' => true,
            ]);

            // Create main items
            foreach ($details as $payload) {
                if (($payload['quantity'] ?? 0) > 0) {
                    $this->createDetailItemAndHistory($reception, $receptionDetail, $payload);
                }
            }

            // Create supporting items
            foreach ($supportingMaterials as $payload) {
                if (($payload['quantity'] ?? 0) > 0) {
                    $this->createDetailItemAndHistory($reception, $receptionDetail, $payload);
                }
            }

            // Process stock updates and history
            $reception->load('receptionDetails.receptionDetailItems');
            $stockService->processReception($reception);

            return $reception;
        });
    }

    /**
     * Static helper for clean one-line invocation.
     */
    public static function run(
        array $headerData,
        array $details,
        array $supportingMaterials = [],
        ?string $receptionId = null
    ): Reception {
        return (new self())->execute($headerData, $details, $supportingMaterials, $receptionId);
    }

    protected function createDetailItemAndHistory(
        Reception $reception,
        ReceptionDetail $receptionDetail,
        array $payload
    ): void {
        $quantity = (float)($payload['quantity'] ?? 0);
        if ($quantity <= 0) return;

        $typeId = !empty($payload['type_id']) ? $payload['type_id'] : $reception->type_id;
        $typeDetailId = !empty($payload['type_detail_id']) ? $payload['type_detail_id'] : null;
        $serviceId = (!empty($payload['service_id']) && $payload['service_id'] !== 'MAIN_MATERIAL') ? $payload['service_id'] : null;
        $serviceDetailId = !empty($payload['service_detail_id']) ? $payload['service_detail_id'] : null;
        $code = $payload['code'] ?? null;
        $sn1 = $payload['number_serial_first'] ?? null;
        $sn2 = $payload['number_serial_second'] ?? null;

        $detailItem = ReceptionDetailItem::create([
            'reception_id' => $reception->id,
            'reception_detail_id' => $receptionDetail->id,
            'service_id' => $serviceId,
            'service_detail_id' => $serviceDetailId,
            'type_id' => $typeId ?: null,
            'type_detail_id' => $typeDetailId,
            'item_code' => $code,
            'number_serial_first' => $sn1,
            'number_serial_second' => $sn2,
            'quantity' => $quantity,
            'description' => $receptionDetail->description ?? '',
            'is_active' => true,
        ]);

        $serialText = trim(implode(' ', array_filter([$code, $sn1, $sn2])));

        HistoryStockDetail::create([
            'code' => $reception->code . '-' . uniqid(),
            'reception_detail_item_id' => $detailItem->id,
            'type_id' => $typeId ?: null,
            'type_detail_id' => $typeDetailId,
            'service_id' => $serviceId,
            'service_detail_id' => $serviceDetailId,
            'regional_police_id' => $reception->regional_police_id,
            'police_station_id' => $reception->police_station_id,
            'date' => $reception->date,
            'serial_number' => $serialText ?: null,
            'status_type' => 'in',
            'quantity' => $quantity,
            'description' => $receptionDetail->description ?? ('Penerimaan material: ' . $reception->name),
            'is_active' => true,
        ]);
    }
}
