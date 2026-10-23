<?php

use App\Services\AnevExecutiveService;
use App\Services\DailyMaterialUsageService;

uses(Tests\TestCase::class);

it('formats executive resume text matching official Kasi Fasmat memo structure', function () {
    $service = new AnevExecutiveService;

    $mockData = [
        'year' => 2026,
        'time_str' => '21.00 WIB',
        'target_pnbp' => 2210588370000,
        'realized_pnbp' => 901265283002,
        'pct_pnbp' => 40.8,
        'remaining_pnbp' => 1309323086998,
        'pct_remaining_pnbp' => 59.2,
        'target_gunmat' => 19337001,
        'realized_gunmat' => 8019478,
        'pct_gunmat' => 41.5,
        'remaining_gunmat' => 11317523,
        'pct_remaining_gunmat' => 58.5,
        'stock_polda' => 6494500,
        'stock_polres' => 9628712,
        'material_rusak' => 3,
        'warehouse' => [
            'SIM' => 2851000,
            'STNK' => 1514500,
            'STCK' => 1200000,
            'E-BPKB' => 434000,
            'BPKB' => 495000,
        ],
        'missing_satker' => [
            ['name' => 'Polres Jember', 'label' => 'Polres Jember (tgl 16)', 'days' => [16], 'count' => 1],
            ['name' => 'Polres Madiun', 'label' => 'Polres Madiun (tgl 16)', 'days' => [16], 'count' => 1],
            ['name' => 'Sie SIM Polda Jatim', 'label' => 'Sie SIM Polda Jatim (tgl 12, 14, 15, 16)', 'days' => [12, 14, 15, 16], 'count' => 4],
        ],
        'satker_count' => 3,
        'kesimpulan_1' => 'Realisasi PNBP mencapai 40,8% dan Realisasi gunmat mencapai 41,5%.',
        'kesimpulan_2' => 'Terdapat 3 satker yang masih belum melengkapi input Gunmat. Perlu peningkatan ketertiban penginputan, terutama Sie SIM Polda Jatim dikarenakan 4 hari berturut.',
    ];

    $formatted = $service->generateFormattedText($mockData);

    expect($formatted)->toContain('Kepada : Kasi Fasmat Ditlantas Polda Jatim')
        ->and($formatted)->toContain('A. CAPAIAN TARGET PNBP TAHUN 2026 :')
        ->and($formatted)->toContain('1. Target PNBP : Rp2.210.588.370.000')
        ->and($formatted)->toContain('2. Realisasi PNBP : Rp901.265.283.002')
        ->and($formatted)->toContain('3. Persentase capaian : 40,8%')
        ->and($formatted)->toContain('4. Sisa target : Rp1.309.323.086.998 (59,2%)')
        ->and($formatted)->toContain('B. CAPAIAN PENGGUNAAN MATERIAL (GUNMAT) TAHUN 2026 :')
        ->and($formatted)->toContain('1. Target Renbut Matreg : 19.337.001 unit')
        ->and($formatted)->toContain('2. Realisasi penggunaan : 8.019.478 unit')
        ->and($formatted)->toContain('3. Persentase capaian : 41,5%')
        ->and($formatted)->toContain('4. Sisa kebutuhan : 11.317.523 unit (58,5%)')
        ->and($formatted)->toContain('C. KONDISI STOK MATERIAL :')
        ->and($formatted)->toContain('1. Stok Polda : 6.494.500 unit')
        ->and($formatted)->toContain('2. Stok Polres : 9.628.712 unit')
        ->and($formatted)->toContain('3. Material rusak : 3 unit')
        ->and($formatted)->toContain('D. ANEV PERSEDIAAN WAREHOUSE :')
        ->and($formatted)->toContain('1. SIM : 2.851.000 unit')
        ->and($formatted)->toContain('2. STNK : 1.514.500 unit')
        ->and($formatted)->toContain('3. STCK : 1.200.000 unit')
        ->and($formatted)->toContain('4. E-BPKB : 434.000 unit')
        ->and($formatted)->toContain('5. BPKB : 495.000 unit')
        ->and($formatted)->toContain('E. ANEV KETERTIBAN INPUT GUNMAT (Belum Input) : Pukul 21.00 WIB')
        ->and($formatted)->toContain('1. Polres Jember (tgl 16)')
        ->and($formatted)->toContain('F. KESIMPULAN ANEV :')
        ->and($formatted)->toContain('1. Realisasi PNBP mencapai 40,8% dan Realisasi gunmat mencapai 41,5%.')
        ->and($formatted)->toContain('2. Terdapat 3 satker yang masih belum melengkapi input Gunmat.');
});

it('sorts SIM services so all Baru come first followed by Perpanjangan in official class rank', function () {
    $rawServices = collect([
        (object) ['id' => 1, 'name' => 'Penerbitan SIM C Perpanjangan'],
        (object) ['id' => 2, 'name' => 'Penerbitan SIM A Baru'],
        (object) ['id' => 3, 'name' => 'Penerbitan SIM B I Baru'],
        (object) ['id' => 4, 'name' => 'Penerbitan SIM A Perpanjangan'],
        (object) ['id' => 5, 'name' => 'Penerbitan SIM C Baru'],
        (object) ['id' => 6, 'name' => 'Penerbitan SIM D Baru'],
        (object) ['id' => 7, 'name' => 'Penerbitan SIM B I Perpanjangan'],
    ]);

    $sorted = DailyMaterialUsageService::sortServices($rawServices);
    $names = $sorted->pluck('name')->all();

    expect($names)->toBe([
        'Penerbitan SIM A Baru',
        'Penerbitan SIM B I Baru',
        'Penerbitan SIM C Baru',
        'Penerbitan SIM D Baru',
        'Penerbitan SIM A Perpanjangan',
        'Penerbitan SIM B I Perpanjangan',
        'Penerbitan SIM C Perpanjangan',
    ]);
});
