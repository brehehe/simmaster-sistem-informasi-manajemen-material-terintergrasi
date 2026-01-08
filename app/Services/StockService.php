<?php

namespace App\Services;

use App\Models\LastStock\LastStock;
use App\Models\LastStock\LastStockDetail;
use App\Models\Reception\Reception;
use App\Models\Reception\ReceptionDetail;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StockService
{
    /**
     * Process last stock and update stocks with history tracking
     */
    public function processLastStock(LastStock $lastStock): void
    {
        foreach ($lastStock->lastStockDetails as $lastStockDetail) {
            // Find or create stock record
            $stock = $this->updateOrCreateStock(
                $lastStockDetail->type_id,
                $lastStockDetail->type_detail_id,
                $lastStock->regional_police_id,
                $lastStock->police_station_id,
                $lastStockDetail->quantity
            );

            // Create stock detail record
            $this->createStockDetail($stock, $lastStockDetail);

            // Create history stock record
            $this->createHistoryStock($lastStock, $lastStockDetail, $stock);
        }
    }

    /**
     * Find or create stock and update quantity
     */
    protected function updateOrCreateStock(
        ?string $typeId,
        ?string $typeDetailId,
        ?string $regionalPoliceId,
        ?string $policeStationId,
        float $quantity
    ): Stock {
        $stock = Stock::where('type_id', $typeId)
            ->where('type_detail_id', $typeDetailId)
            ->where('regional_police_id', $regionalPoliceId)
            ->where('police_station_id', $policeStationId)
            ->first();

        if ($stock) {
            // Update existing stock - add quantity
            $stock->quantity += $quantity;
            $stock->save();
        } else {
            // Create new stock
            $stock = Stock::create([
                'type_id' => $typeId,
                'type_detail_id' => $typeDetailId,
                'regional_police_id' => $regionalPoliceId,
                'police_station_id' => $policeStationId,
                'quantity' => $quantity,
                'is_active' => true,
            ]);
        }

        return $stock;
    }

    /**
     * Create stock detail record
     */
    protected function createStockDetail(Stock $stock, LastStockDetail $lastStockDetail): StockDetail
    {
        return StockDetail::create([
            'stock_id' => $stock->id,
            'type_id' => $lastStockDetail->type_id,
            'type_detail_id' => $lastStockDetail->type_detail_id,
            'regional_police_id' => $stock->regional_police_id,
            'police_station_id' => $stock->police_station_id,
            'rack_id' => $lastStockDetail->rack_id,
            'code' => $lastStockDetail->code,
            'number_serial_first' => $lastStockDetail->number_serial_first,
            'number_serial_second' => $lastStockDetail->number_serial_second,
            'quantity' => $lastStockDetail->quantity,
            'description' => $lastStockDetail->description,
            'is_active' => true,
        ]);
    }

    /**
     * Create history stock record
     */
    protected function createHistoryStock(
        LastStock $lastStock,
        LastStockDetail $lastStockDetail,
        Stock $stock
    ): HistoryStock {
        return HistoryStock::create([
            'code' => HistoryStock::generateCode(),
            'last_stock_id' => $lastStock->id,
            'last_stock_detail_id' => $lastStockDetail->id,
            'type_id' => $lastStockDetail->type_id,
            'type_detail_id' => $lastStockDetail->type_detail_id,
            'regional_police_id' => $lastStock->regional_police_id,
            'serial_number' => $lastStockDetail?->code ? Str::ucfirst($lastStockDetail->code) . ' ' . $lastStockDetail->number_serial_first . ' ' . $lastStockDetail->number_serial_second : null,
            'police_station_id' => $lastStock->police_station_id,
            'rack_id' => $lastStockDetail->rack_id,
            'date' => $lastStock->date,
            'type' => 'last',
            'quantity' => $lastStockDetail->quantity,
            'description' => $lastStockDetail->description ?? 'Stock from last stock: ' . $lastStock->name,
            'is_active' => true,
        ]);
    }

    /**
     * Process reception and update stocks with history tracking
     */
    public function processReception(Reception $reception): void
    {
        foreach ($reception->receptionDetails as $receptionDetail) {
            // Find or create stock record
            $stock = $this->updateOrCreateStock(
                $receptionDetail->type_id,
                $receptionDetail->type_detail_id,
                $reception->regional_police_id,
                $reception->police_station_id,
                $receptionDetail->quantity
            );

            // Create stock detail record from reception
            $this->createStockDetailFromReception($stock, $receptionDetail);

            // Create history stock record for reception
            $this->createHistoryStockFromReception($reception, $receptionDetail, $stock);
        }
    }

    /**
     * Create stock detail record from reception detail
     */
    protected function createStockDetailFromReception(Stock $stock, ReceptionDetail $receptionDetail): StockDetail
    {
        return StockDetail::create([
            'stock_id' => $stock->id,
            'type_id' => $receptionDetail->type_id,
            'type_detail_id' => $receptionDetail->type_detail_id,
            'regional_police_id' => $stock->regional_police_id,
            'police_station_id' => $stock->police_station_id,
            'rack_id' => $receptionDetail->rack_id,
            'code' => $receptionDetail->code,
            'number_serial_first' => $receptionDetail->number_serial_first,
            'number_serial_second' => $receptionDetail->number_serial_second,
            'quantity' => $receptionDetail->quantity,
            'description' => $receptionDetail->description,
            'is_active' => true,
        ]);
    }

    /**
     * Create history stock record from reception
     */
    protected function createHistoryStockFromReception(
        Reception $reception,
        ReceptionDetail $receptionDetail,
        Stock $stock
    ): HistoryStock {
        return HistoryStock::create([
            'code' => HistoryStock::generateCode(),
            'reception_id' => $reception->id,
            'reception_detail_id' => $receptionDetail->id,
            'type_id' => $receptionDetail->type_id,
            'type_detail_id' => $receptionDetail->type_detail_id,
            'regional_police_id' => $reception->regional_police_id,
            'serial_number' => $receptionDetail?->code ? Str::ucfirst($receptionDetail->code) . ' ' . $receptionDetail->number_serial_first . ' ' . $receptionDetail->number_serial_second : null,
            'police_station_id' => $reception->police_station_id,
            'rack_id' => $receptionDetail->rack_id,
            'date' => $reception->date,
            'type' => 'in',
            'quantity' => $receptionDetail->quantity,
            'description' => $receptionDetail->description ?? 'Stock from reception: ' . $reception->name,
            'is_active' => true,
        ]);
    }

    /**
     * Process stock reduction (for future use)
     */
    public function reduceStock(
        ?string $typeId,
        ?string $typeDetailId,
        ?string $regionalPoliceId,
        ?string $policeStationId,
        float $quantity,
        string $description = null
    ): Stock {
        $stock = Stock::where('type_id', $typeId)
            ->where('type_detail_id', $typeDetailId)
            ->where('regional_police_id', $regionalPoliceId)
            ->where('police_station_id', $policeStationId)
            ->firstOrFail();

        if ($stock->quantity < $quantity) {
            throw new \Exception('Insufficient stock. Available: ' . $stock->quantity . ', Required: ' . $quantity);
        }

        $stock->quantity -= $quantity;
        $stock->save();

        // Create history record for reduction
        HistoryStock::create([
            'code' => HistoryStock::generateCode(),
            'type_id' => $typeId,
            'type_detail_id' => $typeDetailId,
            'regional_police_id' => $regionalPoliceId,
            'police_station_id' => $policeStationId,
            'serial_number' => $stock?->code ? Str::ucfirst($stock->code) . ' ' . $stock->number_serial_first . ' ' . $stock->number_serial_second : null,
            'date' => now(),
            'type' => 'out',
            'quantity' => -$quantity,
            'description' => $description ?? 'Stock reduction',
            'is_active' => true,
        ]);

        return $stock;
    }

    /**
     * Process rack assignment - move stock to different rack (BATCH)
     * Supports stock splitting: if quantity < available, splits into 2 stock_details
     * Auto-consolidates: merges stock_details with same type/serial/rack
     */
    public function processRackAssignment($rackAssignment): void
    {
        // Eager load details with relationships
        $rackAssignment->load(['rackAssignmentDetails.fromRack', 'rackAssignmentDetails.toRack']);

        foreach ($rackAssignment->rackAssignmentDetails as $detail) {
            $stockDetail = StockDetail::find($detail->stock_detail_id);

            if ($stockDetail) {
                // Check if we're moving partial quantity (stock split)
                if ($detail->quantity < $stockDetail->quantity) {
                    // SPLIT: Create new stock_detail for remaining quantity (stays at old rack)
                    $remainingQty = $stockDetail->quantity - $detail->quantity;

                    StockDetail::create([
                        'type_id' => $stockDetail->type_id,
                        'type_detail_id' => $stockDetail->type_detail_id,
                        'regional_police_id' => $stockDetail->regional_police_id,
                        'police_station_id' => $stockDetail->police_station_id,
                        'rack_id' => $stockDetail->rack_id, // Stays at old rack
                        'code' => $stockDetail->code,
                        'number_serial_first' => $stockDetail->number_serial_first,
                        'number_serial_second' => $stockDetail->number_serial_second,
                        'quantity' => $remainingQty,
                        'is_active' => true,
                    ]);

                    // Update original: set quantity to moved amount and move to new rack
                    $stockDetail->quantity = $detail->quantity;
                    $stockDetail->rack_id = $detail->to_rack_id;
                    $stockDetail->save();
                } else {
                    // FULL MOVE: Just update the rack_id
                    $stockDetail->rack_id = $detail->to_rack_id;
                    $stockDetail->save();
                }

                // AUTO-CONSOLIDATE: Check if there's another stock_detail with same attributes
                $duplicates = StockDetail::where('id', '!=', $stockDetail->id)
                    ->where('type_id', $stockDetail->type_id)
                    ->where('regional_police_id', $stockDetail->regional_police_id)
                    ->where('code', $stockDetail->code)
                    ->where('number_serial_first', $stockDetail->number_serial_first)
                    ->where('number_serial_second', $stockDetail->number_serial_second)
                    ->where('is_active', true);

                // Check type_detail_id (nullable)
                if ($stockDetail->type_detail_id === null) {
                    $duplicates->whereNull('type_detail_id');
                } else {
                    $duplicates->where('type_detail_id', $stockDetail->type_detail_id);
                }

                // Check police_station_id (nullable)
                if ($stockDetail->police_station_id === null) {
                    $duplicates->whereNull('police_station_id');
                } else {
                    $duplicates->where('police_station_id', $stockDetail->police_station_id);
                }

                // Check rack_id (nullable) - most important for rack assignment!
                if ($stockDetail->rack_id === null) {
                    $duplicates->whereNull('rack_id');
                } else {
                    $duplicates->where('rack_id', $stockDetail->rack_id);
                }

                $duplicates = $duplicates->get();

                if ($duplicates->count() > 0) {
                    // MERGE: Sum all quantities into the first record
                    $totalQuantity = $stockDetail->quantity + $duplicates->sum('quantity');
                    $stockDetail->quantity = $totalQuantity;
                    $stockDetail->save();

                    // Delete duplicates (soft delete if using SoftDeletes, hard delete otherwise)
                    foreach ($duplicates as $dup) {
                        $dup->delete();
                    }
                }

                // Create history record
                $fromRackName = $detail->from_rack_id ? ($detail->fromRack->name ?? 'Unknown') : 'Tanpa Rak';
                $toRackName = $detail->to_rack_id ? ($detail->toRack->name ?? 'Unknown') : 'Tanpa Rak';

                HistoryStock::create([
                    'code' => HistoryStock::generateCode(),
                    'rack_assignment_id' => $rackAssignment->id,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'regional_police_id' => $rackAssignment->regional_police_id,
                    'serial_number' => $detail->item_code . ' ' . $detail->number_serial_first . ' ' . $detail->number_serial_second,
                    'police_station_id' => $rackAssignment->police_station_id,
                    'rack_id' => $detail->from_rack_id, // Source Rack
                    'date' => $rackAssignment->date,
                    'type' => 'rack_move',
                    'status_type' => 'out', // Out from source
                    'quantity' => $detail->quantity,
                    'description' => 'Rack move OUT: ' . $fromRackName . ' → ' . $toRackName . ' (' . $detail->quantity . ' units)',
                    'is_active' => true,
                ]);

                HistoryStock::create([
                    'code' => HistoryStock::generateCode(),
                    'rack_assignment_id' => $rackAssignment->id,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'regional_police_id' => $rackAssignment->regional_police_id,
                    'serial_number' => $detail->item_code . ' ' . $detail->number_serial_first . ' ' . $detail->number_serial_second,
                    'police_station_id' => $rackAssignment->police_station_id,
                    'rack_id' => $detail->to_rack_id, // Destination Rack
                    'date' => $rackAssignment->date,
                    'type' => 'rack_move',
                    'status_type' => 'in', // In to destination
                    'quantity' => $detail->quantity,
                    'description' => 'Rack move IN: ' . $fromRackName . ' → ' . $toRackName . ' (' . $detail->quantity . ' units)',
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Process material usage - reduce stock quantity (BATCH)
     */
    public function processMaterialUsage($materialUsage): void
    {
        foreach ($materialUsage->materialUsageDetails as $detail) {
            // Reduce stock detail quantity
            $stockDetail = StockDetail::find($detail->stock_detail_id);
            if ($stockDetail) {
                $stockDetail->quantity -= $detail->quantity;
                $stockDetail->save();

                // Also reduce from main stock table
                $stock = Stock::where('type_id', $detail->type_id)
                    ->where('type_detail_id', $detail->type_detail_id)
                    ->where('regional_police_id', $materialUsage->regional_police_id)
                    ->first();

                if ($stock) {
                    $stock->quantity -= $detail->quantity;
                    $stock->save();
                }

                // Create history record
                HistoryStock::create([
                    'code' => HistoryStock::generateCode(),
                    'material_usage_id' => $materialUsage->id,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'regional_police_id' => $materialUsage->regional_police_id,
                    'serial_number' => $detail->item_code . ' ' . $detail->number_serial_first . ' ' . $detail->number_serial_second,
                    'police_station_id' => $materialUsage->police_station_id,
                    'rack_id' => $detail->rack_id,
                    'date' => $materialUsage->date,
                    'type' => 'usage',
                    'status_type' => 'out',
                    'quantity' => $detail->quantity,
                    'description' => 'Material ' . $detail->usage_type . ' - ' . ($detail->description ?? ''),
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Process material damage/lost - reduce stock quantity (BATCH)
     */
    public function processMaterialDamage($materialDamage): void
    {
        foreach ($materialDamage->materialDamageDetails as $detail) {
            // Reduce stock detail quantity
            $stockDetail = StockDetail::find($detail->stock_detail_id);
            if ($stockDetail) {
                $stockDetail->quantity -= $detail->quantity;
                $stockDetail->save();

                // Also reduce from main stock table
                $stock = Stock::where('type_id', $detail->type_id)
                    ->where('type_detail_id', $detail->type_detail_id)
                    ->where('regional_police_id', $materialDamage->regional_police_id)
                    ->first();

                if ($stock) {
                    $stock->quantity -= $detail->quantity;
                    $stock->save();
                }

                // Create history record
                $damageTypeLabel = $detail->damage_type === 'damaged' ? 'Rusak' : 'Hilang';
                HistoryStock::create([
                    'code' => HistoryStock::generateCode(),
                    'material_damage_id' => $materialDamage->id,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                    'regional_police_id' => $materialDamage->regional_police_id,
                    'serial_number' => $detail->item_code . ' ' . $detail->number_serial_first . ' ' . $detail->number_serial_second,
                    'police_station_id' => $materialDamage->police_station_id,
                    'rack_id' => $detail->rack_id,
                    'date' => $materialDamage->date,
                    'status_type'=>'out',
                    'type' => 'damage',
                    'quantity' => $detail->quantity,
                    'description' => 'Material ' . $damageTypeLabel . ': ' . $detail->reason,
                    'is_active' => true,
                ]);
            }
        }
    }
    /**
     * Delete reception stock and history
     */
    public function deleteReceptionStock(Reception $reception): void
    {
        // 1. Delete stock history associated with this reception
        HistoryStock::where('reception_id', $reception->id)->delete();

        // 2. Reduce stock quantity and delete/update stock details
        foreach ($reception->receptionDetails as $detail) {
            // Find stock detail to remove
            // Eloquent where matches null correctly as IS NULL
            $stockDetail = StockDetail::where('type_id', $detail->type_id)
                ->where('type_detail_id', $detail->type_detail_id)
                ->where('regional_police_id', $reception->regional_police_id)
                ->where('police_station_id', $reception->police_station_id)
                ->where('rack_id', $detail->rack_id)
                ->where('code', $detail->code)
                ->where('number_serial_first', $detail->number_serial_first)
                ->where('number_serial_second', $detail->number_serial_second)
                ->first();

            if ($stockDetail) {
                if ($stockDetail->quantity <= $detail->quantity) {
                    $stockDetail->delete();
                } else {
                    $stockDetail->quantity -= $detail->quantity;
                    $stockDetail->save();
                }
            }

            // 3. Reduce aggregated stock
            $stock = Stock::where('type_id', $detail->type_id)
                ->where('type_detail_id', $detail->type_detail_id)
                ->where('regional_police_id', $reception->regional_police_id)
                ->where('police_station_id', $reception->police_station_id)
                ->first();

            if ($stock) {
                $stock->quantity -= $detail->quantity;
                if ($stock->quantity < 0) {
                    $stock->quantity = 0;
                }
                $stock->save();
            }
        }
    }
}
