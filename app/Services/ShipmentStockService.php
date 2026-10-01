<?php

namespace App\Services;

use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Stock\StockDetail;
use RuntimeException;

class ShipmentStockService
{
    // Caller holds the shipment lock and a database transaction.
    public function deduct(MaterialShipment $shipment): void
    {
        if ($shipment->stock_deducted_at) {
            return;
        }
        $serials = app(SerialRangeService::class);
        $details = $shipment->materialShipmentDetails()->get()->sort(function ($a, $b) use ($serials) {
            $batchOrder = strcmp($a->stock_detail_id, $b->stock_detail_id);
            if ($batchOrder || ! $a->number_serial_first || ! $b->number_serial_first) {
                return $batchOrder;
            }

            // Consume higher intervals first so lower intervals remain in the original batch.
            return $serials->parse($b->number_serial_first)['number'] <=> $serials->parse($a->number_serial_first)['number'];
        });
        foreach ($details as $detail) {
            $stock = StockDetail::whereKey($detail->stock_detail_id)->lockForUpdate()->firstOrFail();
            if ($stock->regional_police_id !== $shipment->sender_regional_police_id || $stock->police_station_id || ! $stock->is_active || $stock->type_id !== $detail->type_id || $detail->quantity <= 0 || $detail->quantity != (int) $detail->quantity || $stock->quantity < $detail->quantity) {
                throw new RuntimeException('Batch tidak sesuai atau sisa stok tidak mencukupi.');
            }
            $aggregate = $stock->stock()->lockForUpdate()->firstOrFail();
            if ($aggregate->quantity < $detail->quantity) {
                throw new RuntimeException('Stok material tidak mencukupi.');
            }
            if ($stock->number_serial_first || $stock->number_serial_second) {
                [$first, $last] = $serials->validate((string) $detail->number_serial_first, (string) $detail->number_serial_second, (int) $detail->quantity, $stock->number_serial_first, $stock->number_serial_second);
                $batchFirst = $serials->parse($stock->number_serial_first)['number'];
                $batchLast = $serials->parse($stock->number_serial_second)['number'];
                if ($batchLast - $batchFirst + 1 !== (int) $stock->quantity) {
                    throw new RuntimeException('Kuantitas dan rentang batch tidak cocok. Koreksi batch sebelum pengiriman.');
                }
                if ($first > $batchFirst && $last < $batchLast) {
                    $tail = $stock->replicate();
                    $tail->number_serial_first = $serials->format($last + 1, $stock->number_serial_first);
                    $tail->quantity = $batchLast - $last;
                    $tail->save();
                    $stock->number_serial_second = $serials->format($first - 1, $stock->number_serial_second);
                    $stock->quantity = $first - $batchFirst;
                } elseif ($first > $batchFirst) {
                    $stock->number_serial_second = $serials->format($first - 1, $stock->number_serial_second);
                    $stock->quantity -= $detail->quantity;
                } elseif ($last < $batchLast) {
                    $stock->number_serial_first = $serials->format($last + 1, $stock->number_serial_first);
                    $stock->quantity -= $detail->quantity;
                } else {
                    $stock->quantity = 0;
                    $stock->number_serial_first = null;
                    $stock->number_serial_second = null;
                }
            } else {
                if ($detail->number_serial_first || $detail->number_serial_second) {
                    throw new RuntimeException('Batch ini tidak memiliki rentang nomor seri.');
                }
                $stock->quantity -= $detail->quantity;
            }
            $stock->save();
            $aggregate->decrement('quantity', $detail->quantity);
            \App\Models\Stock\HistoryStock::create([
                'code' => \App\Models\Stock\HistoryStock::generateCode(), 'material_shipment_id' => $shipment->id,
                'type_id' => $detail->type_id, 'type_detail_id' => $detail->type_detail_id,
                'service_id' => $stock->service_id, 'service_detail_id' => $stock->service_detail_id,
                'regional_police_id' => $shipment->sender_regional_police_id, 'police_station_id' => null,
                'date' => $shipment->shipment_date, 'status_type' => 'out', 'quantity' => -$detail->quantity,
                'description' => 'Pengiriman SPPM '.$shipment->code, 'is_active' => true,
            ]);
        }
        $shipment->stock_deducted_at = now();
        $shipment->save();
    }
}
