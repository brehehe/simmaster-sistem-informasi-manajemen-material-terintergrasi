<?php

use App\Livewire\Admin\MenuPolda\MaterialUsage\Detail\AdminMenuPoldaMaterialUsageDetailIndex;

it('keeps an explicitly entered zero in service usage totals', function () {
    $component = new AdminMenuPoldaMaterialUsageDetailIndex;
    $component->details = [['quantity' => 12, 'service_items' => ['bbn1' => ['quantity' => '0']]]];
    $component->recalculateDetailQuantity(0);
    expect($component->details[0]['quantity'])->toBe(0);
});

it('sums nested service usage including zeros', function () {
    $component = new AdminMenuPoldaMaterialUsageDetailIndex;
    $component->details = [['quantity' => '', 'service_items' => ['bbn1' => [
        'r4' => ['quantity' => '0'], 'r2' => ['quantity' => '7'],
    ]]]];
    $component->recalculateDetailQuantity(0);
    expect($component->details[0]['quantity'])->toBe(7.0);
});
