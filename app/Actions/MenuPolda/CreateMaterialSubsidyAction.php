<?php

namespace App\Actions\MenuPolda;

use App\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy;
use App\Models\MenuPolda\MaterialSubsidy\MaterialSubsidyDetail;
use Illuminate\Support\Facades\DB;

class CreateMaterialSubsidyAction
{
    /**
     * Execute material subsidy creation or update.
     */
    public function execute(
        array $headerData,
        array $items,
        ?string $subsidyId = null
    ): MaterialSubsidy {
        return DB::transaction(function () use ($headerData, $items, $subsidyId) {
            $isEditMode = !empty($subsidyId);

            if ($isEditMode) {
                $subsidy = MaterialSubsidy::findOrFail($subsidyId);
                $subsidy->update($headerData);
                $subsidy->materialSubsidyDetails()->delete();
            } else {
                $subsidy = MaterialSubsidy::create(array_merge($headerData, [
                    'status' => 'draft',
                    'is_active' => true,
                ]));
            }

            foreach ($items as $item) {
                if (empty($item['type_id']) || ($item['quantity'] ?? 0) <= 0) {
                    continue;
                }

                $subsidy->materialSubsidyDetails()->create([
                    'type_id' => $item['type_id'],
                    'type_detail_id' => !empty($item['type_detail_id']) ? $item['type_detail_id'] : null,
                    'stock_detail_id' => !empty($item['stock_detail_id']) ? $item['stock_detail_id'] : null,
                    'quantity' => (float)$item['quantity'],
                    'notes' => $item['notes'] ?? '',
                ]);
            }

            return $subsidy;
        });
    }

    /**
     * Static helper for clean invocation.
     */
    public static function run(
        array $headerData,
        array $items,
        ?string $subsidyId = null
    ): MaterialSubsidy {
        return (new self())->execute($headerData, $items, $subsidyId);
    }
}
