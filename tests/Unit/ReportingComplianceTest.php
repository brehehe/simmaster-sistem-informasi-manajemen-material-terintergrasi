<?php

use App\Livewire\Admin\Report\Anev\ReportingComplianceIndex;
use App\Services\ReportingComplianceService;
use Carbon\CarbonImmutable;

uses(Tests\TestCase::class);

it('classifies submitted reports by calendar days in WIB', function ($timestamp, $expected, $days) {
    config(['app.timezone' => 'UTC']);
    $result = (new ReportingComplianceService)->status('2026-09-10', $timestamp);
    expect($result['color'])->toBe($expected)->and($result['delay'])->toBe($days);
})->with([
    ['2026-09-10 10:00:00', 'green', 0],
    ['2026-09-10 17:00:00', 'yellow', 1],
    ['2026-09-12 10:00:00', 'yellow', 2],
    ['2026-09-13 10:00:00', 'red', 3],
    ['2026-09-16 10:00:00', 'red', 6],
]);

it('does not mark missing reports green', function ($date, $expected, $days) {
    $result = (new ReportingComplianceService)->status($date, null, CarbonImmutable::parse('2026-09-14 10:00', 'Asia/Jakarta'));
    expect($result['reported'])->toBeFalse()->and($result['label'])->toBe('Belum input')
        ->and($result['color'])->toBe($expected)->and($result['delay'])->toBe($days);
})->with([
    ['2026-09-14', 'pending', 0], ['2026-09-13', 'yellow', 1],
    ['2026-09-12', 'yellow', 2], ['2026-09-11', 'red', 3],
]);

it('filters missing reports and keeps the full summary', function () {
    $service = Mockery::mock(ReportingComplianceService::class);
    $service->shouldReceive('calendar')->andReturn(['days' => [], 'rows' => collect()]);
    $service->shouldReceive('rows')->once()->with('2026-09-10', 'all')->andReturn(collect([
        ['id' => 'a', 'name' => 'Polres A', 'color' => 'green', 'reported' => true],
        ['id' => 'b', 'name' => 'Polres B', 'color' => 'red', 'reported' => false],
    ]));
    app()->instance(ReportingComplianceService::class, $service);
    $component = new ReportingComplianceIndex;
    $component->date = '2026-09-10';
    $component->status = 'missing';
    $data = $component->render()->getData();
    expect($data['rows']->pluck('name')->all())->toBe(['Polres B'])
        ->and($data['summary']['total'])->toBe(2)->and($data['summary']['missing'])->toBe(1);
});

it('rejects invalid and future dates without querying reports', function ($date) {
    $service = Mockery::mock(ReportingComplianceService::class);
    $service->shouldReceive('calendar')->andReturn(['days' => [], 'rows' => collect()]);
    $service->shouldNotReceive('rows');
    app()->instance(ReportingComplianceService::class, $service);
    $component = new ReportingComplianceIndex;
    $component->date = $date;
    $data = $component->render()->getData();
    expect($data['dateError'])->not->toBeNull()->and($data['rows'])->toBeEmpty();
})->with(['invalid', '2099-12-31', '2026-02-30']);

it('requires login and the Admin role for the Anev route', function () {
    $route = app('router')->getRoutes()->getByName('report.anev');
    expect($route->gatherMiddleware())->toContain('auth', 'verified');
    expect($route->gatherMiddleware())->toContain(\Spatie\Permission\Middleware\RoleMiddleware::class.':Admin');
    $this->get('/report/anev')->assertRedirect('/login');
});

it('counts non-usage input and chooses the latest submission across sources', function () {
    config(['app.timezone' => 'UTC']);
    $service = new ReportingComplianceService;
    $row = $service->summarize('2026-09-10', [
        'opening' => ['count' => 2, 'submitted_at' => '2026-09-10 09:00:00'],
        'reception' => ['count' => 1, 'submitted_at' => '2026-09-13 09:00:00'],
    ]);
    expect($row['reported'])->toBeTrue()->and($row['report_count'])->toBe(3)
        ->and($row['color'])->toBe('red')->and($row['delay'])->toBe(3);
});

it('does not penalize a missing event-based transaction', function () {
    $row = (new ReportingComplianceService)->summarize('2026-01-01', [], 'opname');
    expect($row['reported'])->toBeFalse()->and($row['color'])->toBe('pending')
        ->and($row['label'])->toBe('Tidak ada input');
});

it('passes the selected input type to the report service', function () {
    $service = Mockery::mock(ReportingComplianceService::class);
    $service->shouldReceive('calendar')->andReturn(['days' => [], 'rows' => collect()]);
    $service->shouldReceive('rows')->once()->with('2026-09-10', 'opening')->andReturn(collect());
    app()->instance(ReportingComplianceService::class, $service);
    $component = new ReportingComplianceIndex;
    $component->date = '2026-09-10';
    $component->source = 'opening';
    expect($component->render()->getData()['sourceError'])->toBeNull();
});

it('rejects unknown input types without querying', function () {
    $service = Mockery::mock(ReportingComplianceService::class);
    $service->shouldReceive('calendar')->andReturn(['days' => [], 'rows' => collect()]);
    $service->shouldNotReceive('rows');
    app()->instance(ReportingComplianceService::class, $service);
    $component = new ReportingComplianceIndex;
    $component->date = '2026-09-10';
    $component->source = 'unknown';
    expect($component->render()->getData()['sourceError'])->not->toBeNull();
});

function anevUser(string $role): \App\Models\User
{
    $user = new \App\Models\User;
    $user->id = 'anev-test-user';
    $user->setRelation('roles', collect([new \Spatie\Permission\Models\Role(['name' => $role, 'guard_name' => 'web'])]));

    return $user;
}

it('denies non-admin page access and export', function ($role) {
    $this->actingAs(anevUser($role));
    $this->get('/report/anev')->assertForbidden();
    $component = new ReportingComplianceIndex;
    expect(fn () => $component->boot())->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    expect(fn () => $component->exportExcel())->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
})->with(['Polda', 'Polres', 'Warehouse']);

it('exports only rows matching the selected filters', function () {
    $this->actingAs(anevUser('Admin'));
    \Maatwebsite\Excel\Facades\Excel::fake();
    $base = ['color' => 'red', 'reported' => false, 'label' => 'Belum input', 'delay' => 3, 'submitted_at' => '', 'report_count' => 0, 'inputs' => []];
    $service = Mockery::mock(ReportingComplianceService::class);
    $service->shouldReceive('calendar')->andReturn(['days' => [], 'rows' => collect()]);
    $service->shouldReceive('rows')->once()->with('2026-09-10', 'usage')->andReturn(collect([
        array_merge($base, ['name' => 'Polres A']),
        array_merge($base, ['name' => 'Polres B']),
        array_merge($base, ['name' => 'Polres A tertib', 'color' => 'green', 'reported' => true]),
    ]));
    app()->instance(ReportingComplianceService::class, $service);
    $component = new ReportingComplianceIndex;
    $component->boot();
    $component->date = '2026-09-10';
    $component->source = 'usage';
    $component->status = 'missing';
    $component->search = 'Polres A';
    $component->exportExcel();
    \Maatwebsite\Excel\Facades\Excel::assertDownloaded('anev-ketertiban-laporan-2026-09-10.xlsx', function ($export) {
        expect($export->collection()->all())->toBe([[1, '2026-09-10', 'Pemakaian', 'Polres A', 'Belum input', 3, '—', 0, 'Tidak ada input']]);

        return true;
    });
});

it('rejects invalid export filters before querying', function ($field, $value) {
    $this->actingAs(anevUser('Admin'));
    $service = Mockery::mock(ReportingComplianceService::class);
    $service->shouldReceive('calendar')->andReturn(['days' => [], 'rows' => collect()]);
    $service->shouldNotReceive('rows');
    app()->instance(ReportingComplianceService::class, $service);
    $component = new ReportingComplianceIndex;
    $component->date = '2026-09-10';
    $component->{$field} = $value;
    expect(fn () => $component->exportExcel())->toThrow(\Illuminate\Validation\ValidationException::class);
})->with([['date', 'invalid'], ['date', '2099-12-31'], ['source', 'unknown'], ['status', 'unknown']]);

it('writes a readable Excel workbook preserving zero counts and literal names', function () {
    config(['excel.temporary_files.local_path' => sys_get_temp_dir()]);
    $export = new \App\Exports\ReportingComplianceExport(collect([
        ['name' => '=1+1', 'label' => 'Tertib', 'delay' => 0, 'submitted_at' => '10/09/2026 08:00', 'report_count' => 0, 'inputs' => ['usage' => ['count' => 0]]],
    ]), '2026-09-10', 'all');
    $path = tempnam(sys_get_temp_dir(), 'anev-test-');
    try {
        file_put_contents($path, \Maatwebsite\Excel\Facades\Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX));
        \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder);
        $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet = $book->getActiveSheet();
        expect($sheet->getCell('D2')->getValue())->toBe('=1+1')
            ->and($sheet->getCell('D2')->getDataType())->toBe('s')
            ->and($sheet->getCell('F2')->getValue())->toBe(0)
            ->and($sheet->getCell('H2')->getValue())->toBe(0)
            ->and($sheet->getCell('I2')->getValue())->toBe('Pemakaian: 0');
        $book->disconnectWorksheets();
    } finally {
        unlink($path);
    }
});
