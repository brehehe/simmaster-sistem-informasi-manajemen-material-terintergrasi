<?php

namespace App\Actions\MenuPolda;

use App\Models\MenuPolda\MutationStock\MutationStock;
use App\Models\MenuPolda\MutationStock\MutationStockDetail;
use App\Models\Stock\StockDetail;
use Illuminate\Support\Facades\DB;

class CreateMutationStockAction
{
    /**
     * Execute mutation stock creation or update.
     */
    public function execute(
        array $headerData,
        array $details,
        bool $send = false,
        ?string $mutationId = null
    ): MutationStock {
        return DB::transaction(function () use ($headerData, $details, $send, $mutationId) {
            $isEditMode = !empty($mutationId);

            if ($isEditMode) {
                $mutation = MutationStock::findOrFail($mutationId);

                if ($mutation->status !== 'draft') {
                    throw new \Exception('Hanya mutasi dengan status draft yang bisa diedit');
                }

                $mutation->update($headerData);
                $mutation->mutationStockDetails()->delete();
            } else {
                $mutation = MutationStock::create(array_merge($headerData, [
                    'status' => 'draft',
                    'is_active' => true,
                ]));
            }

            foreach ($details as $detail) {
                if (empty($detail['stock_detail_id'])) {
                    continue;
                }

                $stockDetail = StockDetail::with(['type', 'typeDetail'])->findOrFail($detail['stock_detail_id']);

                if ($detail['quantity'] > $stockDetail->quantity) {
                    $code = $stockDetail->code ?: 'Item';
                    throw new \Exception("Quantity untuk {$code} melebihi stock tersedia");
                }

                MutationStockDetail::create([
                    'mutation_stock_id' => $mutation->id,
                    'stock_detail_id' => $detail['stock_detail_id'],
                    'type_id' => $stockDetail->type_id,
                    'type_detail_id' => $stockDetail->type_detail_id,
                    'code' => $stockDetail->code ?? '',
                    'number_serial_first' => $stockDetail->number_serial_first ?? '',
                    'number_serial_second' => $stockDetail->number_serial_second ?? '',
                    'quantity' => (float)$detail['quantity'],
                    'notes' => $detail['notes'] ?? '',
                    'is_active' => true,
                ]);
            }

            if ($send) {
                $mutation->markAsSent();
            }

            return $mutation;
        });
    }

    /**
     * Static helper for clean invocation.
     */
    public static function run(
        array $headerData,
        array $details,
        bool $send = false,
        ?string $mutationId = null
    ): MutationStock {
        return (new self())->execute($headerData, $details, $send, $mutationId);
    }
}
