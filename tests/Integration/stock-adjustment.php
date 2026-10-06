<?php

// Run: php tests/Integration/stock-adjustment.php
// PostgreSQL temporary tables shadow inventory tables; real stock data is never written.
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Spatie\Role;
use App\Models\Stock\HistoryStock;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use App\Models\StockOpname\StockOpname;
use App\Models\User;
use App\Services\StockAdjustmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

function checkAdjustment(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
    echo 'PASS '.$message.PHP_EOL;
}
function adjustmentUser(string $role, string $owner): User
{
    $user = (new User)->forceFill(['id' => (string) Str::uuid(), 'police_station_id' => $role === 'Polres' ? $owner : null, 'regional_police_id' => $role === 'Polda' ? $owner : null]);
    $user->setRelation('roles', collect([(new Role)->forceFill(['name' => $role, 'guard_name' => 'web'])]));

    return $user;
}
DB::beginTransaction();
try {
    foreach (['stocks', 'stock_details', 'stock_opnames', 'stock_opname_details', 'history_stocks', 'types', 'services', 'service_details', 'type_details'] as $table) {
        DB::statement('CREATE TEMP TABLE '.$table.' (LIKE public.'.$table.' INCLUDING ALL) ON COMMIT DROP');
    }
    $service = new StockAdjustmentService;
    foreach (['polres', 'polda'] as $scope) {
        $owner = (string) Str::uuid();
        $user = adjustmentUser(ucfirst($scope), $owner);
        $attributes = ['type_id' => (string) Str::uuid(), 'police_station_id' => $scope === 'polres' ? $owner : null, 'regional_police_id' => $scope === 'polda' ? $owner : null, 'quantity' => 100, 'is_active' => true];
        $stock = Stock::create($attributes);
        $detail = StockDetail::create($attributes + ['stock_id' => $stock->id]);
        $token = (string) Str::uuid();
        $record = $service->apply($user, $scope, $owner, $detail->id, '100', '0', 'Koreksi saldo hasil pemeriksaan', $token);
        checkAdjustment((float) $detail->fresh()->quantity === 0.0 && (float) $stock->fresh()->quantity === 0.0, $scope.' adjustment to zero updates detail and total');
        $repeat = $service->apply($user, $scope, $owner, $detail->id, '100', '0', 'Koreksi saldo hasil pemeriksaan', $token);
        checkAdjustment($repeat->id === $record->id && StockOpname::whereKey($token)->count() === 1 && HistoryStock::where('police_station_id', $attributes['police_station_id'])->where('regional_police_id', $attributes['regional_police_id'])->count() === 1, $scope.' duplicate request creates one adjustment and history');
        $service->apply($user, $scope, $owner, $detail->id, '0', '-5', 'Koreksi saldo negatif', (string) Str::uuid());
        checkAdjustment((float) $detail->fresh()->quantity === -5.0 && (float) $stock->fresh()->quantity === -5.0, $scope.' negative balance supported');
        $service->apply($user, $scope, $owner, $detail->id, '-5', '20', 'Koreksi penambahan saldo', (string) Str::uuid());
        checkAdjustment((float) $stock->fresh()->quantity === 20.0, $scope.' positive adjustment uses delta');
        try {
            $service->apply($user, $scope, $owner, $detail->id, '-5', '99', 'Saldo stale harus ditolak', (string) Str::uuid());
            throw new RuntimeException('Stale stock accepted');
        } catch (ValidationException) {
            checkAdjustment((float) $stock->fresh()->quantity === 20.0, $scope.' stale form does not overwrite stock');
        }
        try {
            $service->apply($user, $scope, (string) Str::uuid(), $detail->id, '20', '99', 'Wilayah lain harus ditolak', (string) Str::uuid());
            throw new RuntimeException('Foreign owner accepted');
        } catch (HttpException $e) {
            checkAdjustment($e->getStatusCode() === 403, $scope.' cannot adjust another owner');
        }
        try {
            $service->apply($user, $scope, $owner, $detail->id, '20', '20', 'Tidak ada perubahan saldo', (string) Str::uuid());
            throw new RuntimeException('No-op accepted');
        } catch (ValidationException) {
            checkAdjustment((float) $stock->fresh()->quantity === 20.0, $scope.' unchanged balance rejected');
        }
        Illuminate\Support\Facades\Auth::setUser($user);
        $component = new App\Livewire\Admin\StockAdjustment\StockAdjustmentIndex;
        $component->mount($scope);
        $html = $component->render()->with(get_object_vars($component))->with('errors', new Illuminate\Support\ViewErrorBag)->render();
        checkAdjustment(str_contains($html, 'Simpan Penyesuaian') && str_contains($html, 'ADJ-'), $scope.' form and audit history render');
        $audit = $record->stockOpnameDetails()->first();
        checkAdjustment((float) $audit->system_quantity === 100.0 && (float) $audit->physical_quantity === 0.0 && $record->checked_by === $user->id, $scope.' audit captures before, after, actor');
    }
    $admin = adjustmentUser('Admin', '');
    $owner = (string) Str::uuid();
    foreach (StockAdjustmentService::MATERIAL_CORRECTIONS as $oldName => $newName) {
        $oldType = App\Models\Type\Type::create(['name' => $oldName, 'is_active' => true]);
        $newType = App\Models\Type\Type::create(['name' => $newName, 'is_active' => true]);
        App\Models\Service\Service::create(['name' => 'Layanan TNKB', 'type_id' => $newType->id, 'price' => 0, 'is_active' => true]);
        $catalog = app(App\Services\DailyMaterialUsageService::class)->catalog([$oldType->id]);
        checkAdjustment(count($catalog) === 1 && reset($catalog)['type_id'] === $newType->id, 'legacy operator access includes the canonical TNKB material');
        $stock = Stock::create(['type_id' => $oldType->id, 'police_station_id' => $owner, 'quantity' => 12, 'is_active' => true]);
        $batch = StockDetail::create(['stock_id' => $stock->id, 'type_id' => $oldType->id, 'police_station_id' => $owner, 'quantity' => 12, 'is_active' => true]);
        $token = (string) Str::uuid();
        try {
            $service->correctMaterial(adjustmentUser('Polres', $owner), 'polres', $owner, $batch->id, '12', 'Koreksi jenis material', $token);
            throw new RuntimeException('Non-admin correction accepted');
        } catch (HttpException $e) {
            checkAdjustment($e->getStatusCode() === 403, 'material correction restricted to admin');
        }
        $record = $service->correctMaterial($admin, 'polres', $owner, $batch->id, '12', 'Koreksi jenis material', $token);
        $service->correctMaterial($admin, 'polres', $owner, $batch->id, '12', 'Koreksi jenis material', $token);
        checkAdjustment((float) $batch->fresh()->quantity === 0.0 && (float) $stock->fresh()->quantity === 0.0, $oldName.' source cleared');
        checkAdjustment((float) StockDetail::where('type_id', $newType->id)->sum('quantity') === 12.0 && (float) Stock::where('type_id', $newType->id)->sum('quantity') === 12.0, $newName.' credited exactly once');
        checkAdjustment($record->stockOpnameDetails()->count() === 2, 'transfer records source and destination audit');
    }
    Illuminate\Support\Facades\Auth::setUser($admin);
    $component = new App\Livewire\Admin\StockAdjustment\StockAdjustmentIndex;
    $component->mount('polres');
    $component->owner = $owner;
    $component->correctMaterial = true;
    $html = $component->render()->with(get_object_vars($component))->with('errors', new Illuminate\Support\ViewErrorBag)->render();
    checkAdjustment(str_contains($html, 'seluruh saldo dipindahkan') && str_contains($html, 'wire:model.live="correctMaterial"'), 'admin transfer form renders using the existing adjustment layout');
    checkAdjustment(StockOpname::count() === 8, 'only successful adjustments persisted in temporary tables');
} finally {
    DB::rollBack();
}
