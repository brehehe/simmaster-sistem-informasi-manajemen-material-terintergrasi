<?php

use App\Services\SerialRangeService;

it('counts inclusive serials and retains dotted formatting', function () {
    $service = new SerialRangeService;
    expect($service->validate('12.479.501', '12.482.000', 2500, '12.470.000', '12.500.000'))->toBe([12479501, 12482000]);
    expect($service->format(12482001, '12.479.501'))->toBe('12.482.001');
    expect($service->format(11, 'A000001'))->toBe('A000011');
});

it('rejects off by one and foreign batch serials', function ($start, $end, $qty) {
    (new SerialRangeService)->validate($start, $end, $qty, 'A000001', 'A001000');
})->with([['A000001', 'A001000', 999], ['A000001', 'B001000', 1000], ['A000999', 'A001001', 3], ['A000004', 'A000002', 3]])->throws(InvalidArgumentException::class);
