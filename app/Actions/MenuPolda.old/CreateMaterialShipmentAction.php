<?php

namespace App\Actions\MenuPolda;

use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail;
use App\Models\User;
use App\Notifications\SppmCreatedNotification;
use Illuminate\Support\Facades\DB;

class CreateMaterialShipmentAction
{
    /**
     * Execute SPPM creation or update.
     */
    public function execute(
        array $headerData,
        array $details,
        bool $sendImmediately = false,
        ?string $shipmentId = null
    ): MaterialShipment {
        return DB::transaction(function () use ($headerData, $details, $sendImmediately, $shipmentId) {
            $isEditMode = !empty($shipmentId);

            if ($isEditMode) {
                $shipment = MaterialShipment::findOrFail($shipmentId);
                $shipment->update($headerData);
                $shipment->materialShipmentDetails()->delete();
            } else {
                $shipment = MaterialShipment::create($headerData);
            }

            foreach ($details as $item) {
                if (($item['quantity'] ?? 0) <= 0) continue;

                $parts = explode(' | ', (string)($item['selected_stock_key'] ?? ''));
                $code = $parts[0] ?? null;
                $sn1  = $parts[1] ?? null;
                $sn2  = $parts[2] ?? null;

                MaterialShipmentDetail::create([
                    'material_shipment_id' => $shipment->id,
                    'stock_detail_id'      => $item['stock_detail_id'] ?: null,
                    'type_id'              => $item['type_id'],
                    'type_detail_id'       => $item['type_detail_id'] ?: null,
                    'code'                 => ($code && $code !== '-') ? $code : null,
                    'number_serial_first'  => ($sn1 && $sn1 !== '-') ? $sn1 : null,
                    'number_serial_second' => ($sn2 && $sn2 !== '-') ? $sn2 : null,
                    'quantity'             => (float)$item['quantity'],
                    'notes'                => $item['notes'] ?? null,
                    'is_active'            => true,
                ]);
            }

            if ($sendImmediately && $shipment->status === 'draft') {
                $shipment->markAsShipped();

                // Send notification to Polres users
                if ($shipment->receiver_police_station_id) {
                    $polresUsers = User::where('police_station_id', $shipment->receiver_police_station_id)
                        ->where('is_active', true)
                        ->get();

                    foreach ($polresUsers as $polresUser) {
                        $polresUser->notify(new SppmCreatedNotification($shipment));
                    }
                }
            }

            return $shipment;
        });
    }

    /**
     * Static helper for clean invocation.
     */
    public static function run(
        array $headerData,
        array $details,
        bool $sendImmediately = false,
        ?string $shipmentId = null
    ): MaterialShipment {
        return (new self())->execute($headerData, $details, $sendImmediately, $shipmentId);
    }
}
