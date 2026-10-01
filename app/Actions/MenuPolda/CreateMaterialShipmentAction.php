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
            \Illuminate\Support\Facades\Gate::authorize('create', MaterialShipment::class);
            $user = auth()->user();
            if (! $user->hasRole('Admin') && $user->regional_police_id !== $headerData['sender_regional_police_id']) {
                abort(403);
            }
            if (! \App\Models\Police\PoliceStation::whereKey($headerData['receiver_police_station_id'])->where('regional_police_id', $headerData['sender_regional_police_id'])->exists()) {
                throw new \RuntimeException('Polres tujuan tidak sesuai Polda.');
            }
            if (MaterialShipment::withTrashed()->where('code', $headerData['code'])->when($shipmentId, fn ($q) => $q->where('id', '!=', $shipmentId))->exists()) {
                throw new \RuntimeException('Nomor SPPM sudah digunakan.');
            }
            $isEditMode = ! empty($shipmentId);

            if ($isEditMode) {
                $shipment = MaterialShipment::whereKey($shipmentId)->lockForUpdate()->firstOrFail();
                \Illuminate\Support\Facades\Gate::authorize('update', $shipment);
                $shipment->update($headerData);
                $shipment->materialShipmentDetails()->delete();
            } else {
                if (empty($headerData['code']) || MaterialShipment::withTrashed()->where('code', $headerData['code'])->exists()) {
                    $headerData['code'] = MaterialShipment::generateCode($headerData['sender_regional_police_id'] ?? null);
                }
                $shipment = MaterialShipment::create($headerData);
            }

            if (! $details) {
                throw new \RuntimeException('Minimal satu material harus diisi.');
            }
            $totals = [];
            $intervals = [];
            foreach ($details as $item) {
                if (($item['quantity'] ?? 0) <= 0) {
                    throw new \RuntimeException('Jumlah kirim harus lebih dari 0.');
                }

                $stockDetail = \App\Models\Stock\StockDetail::whereKey($item['stock_detail_id'])->lockForUpdate()->firstOrFail();
                if ($stockDetail->regional_police_id !== $headerData['sender_regional_police_id'] || $stockDetail->police_station_id || ! $stockDetail->is_active || $item['quantity'] > $stockDetail->quantity || (int) $item['quantity'] != $item['quantity']) {
                    throw new \RuntimeException('Batch atau kuantitas material tidak valid.');
                }
                $totals[$stockDetail->id] = ($totals[$stockDetail->id] ?? 0) + $item['quantity'];
                if ($totals[$stockDetail->id] > $stockDetail->quantity) {
                    throw new \RuntimeException('Total pengiriman melebihi sisa batch.');
                }
                $resolvedCode = $stockDetail->code;
                $sn1 = $item['number_serial_first'] ?? null;
                $sn2 = $item['number_serial_second'] ?? null;
                if ($stockDetail->number_serial_first || $stockDetail->number_serial_second) {
                    [$first, $last] = app(\App\Services\SerialRangeService::class)->validate((string) $sn1, (string) $sn2, (int) $item['quantity'], $stockDetail->number_serial_first, $stockDetail->number_serial_second);
                    foreach ($intervals[$stockDetail->id] ?? [] as [$a, $b]) {
                        if ($first <= $b && $last >= $a) {
                            throw new \RuntimeException('Rentang seri antarbaris bertumpang tindih.');
                        }
                    }
                    $intervals[$stockDetail->id][] = [$first, $last];
                } elseif ($sn1 || $sn2) {
                    throw new \RuntimeException('Batch ini tidak memiliki nomor seri.');
                }

                MaterialShipmentDetail::create([
                    'material_shipment_id' => $shipment->id,
                    'stock_detail_id' => $item['stock_detail_id'] ?: null,
                    'type_id' => $stockDetail->type_id,
                    'type_detail_id' => $stockDetail->type_detail_id,
                    'code' => $resolvedCode,
                    'number_serial_first' => ($sn1 && $sn1 !== '-') ? $sn1 : null,
                    'number_serial_second' => ($sn2 && $sn2 !== '-') ? $sn2 : null,
                    'quantity' => (float) $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                    'is_active' => true,
                ]);
            }

            if ($sendImmediately && $shipment->status === 'draft') {
                $shipment->markAsShipped();

            }

            if (! $isEditMode) {
                // Drafts appear in the recipient inbox before warehouse pickup.
                if ($shipment->receiver_police_station_id) {
                    $polresUsers = User::where('police_station_id', $shipment->receiver_police_station_id)
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
        return (new self)->execute($headerData, $details, $sendImmediately, $shipmentId);
    }
}
