<?php

// All writes use transaction-scoped PostgreSQL temporary tables, never operational tables.
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Actions\MenuPolda\CreateMaterialShipmentAction;
use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Service\Service;
use App\Models\Spatie\Role;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use App\Models\Type\Type;
use App\Models\User;
use App\Services\DailyMaterialUsageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

function revisionCheck(bool $ok, string $label): void
{
    if (! $ok) {
        throw new RuntimeException($label);
    } echo 'PASS '.$label.PHP_EOL;
}
function revisionUser(string $role, ?string $station = null, ?string $region = null): User
{
    $user = (new User)->forceFill(['id' => (string) Str::uuid(), 'police_station_id' => $station, 'regional_police_id' => $region]);
    $user->setRelation('roles', collect([(new Role)->forceFill(['name' => $role, 'guard_name' => 'web'])]));
    $user->setRelation('userType', null);

    return $user;
}
DB::beginTransaction();
try {
    foreach (['types', 'type_details', 'services', 'service_details', 'regional_police', 'police_stations', 'stocks', 'stock_details', 'material_usages', 'material_usage_details', 'material_usage_detail_items', 'material_shipments', 'material_shipment_details', 'history_stocks', 'receptions'] as $table) {
        DB::statement('CREATE TEMP TABLE '.$table.' (LIKE public.'.$table.' INCLUDING ALL) ON COMMIT DROP');
    }
    $region = RegionalPolice::create(['name' => 'Test Region', 'is_active' => true]);
    $station = PoliceStation::create(['name' => 'Test Station', 'regional_police_id' => $region->id, 'is_active' => true]);
    $type = Type::create(['name' => 'Test Material', 'price' => 10, 'unit' => 'Lembar', 'is_active' => true]);
    $svc1 = Service::create(['name' => 'First Service', 'type_id' => $type->id, 'price' => 100, 'is_active' => true]);
    $svc2 = Service::create(['name' => 'Second Service', 'type_id' => $type->id, 'price' => 200, 'is_active' => true]);
    $stock = Stock::create(['type_id' => $type->id, 'police_station_id' => $station->id, 'quantity' => 10, 'is_active' => true]);
    $batch = StockDetail::create(['stock_id' => $stock->id, 'type_id' => $type->id, 'police_station_id' => $station->id, 'quantity' => 10, 'number_serial_first' => '001', 'number_serial_second' => '010', 'is_active' => true]);
    $user = revisionUser('Polres', $station->id);
    Auth::setUser($user);
    $service = new DailyMaterialUsageService;
    $catalog = $service->catalog();
    $keys = array_keys($catalog);
    revisionCheck(count($catalog) === 2, 'daily form enumerates all services');
    try {
        $service->save($user, $station->id, '2026-01-01', [$keys[0] => 0, $keys[1] => '']);
        throw new RuntimeException('Blank accepted');
    } catch (ValidationException) {
        revisionCheck(MaterialUsage::count() === 0, 'blank quantities rejected without writes');
    }
    $usage = $service->save($user, $station->id, '2026-01-01', [$keys[0] => 3, $keys[1] => 0]);
    revisionCheck((int) $batch->fresh()->quantity === 7 && $usage->materialUsageDetails()->count() === 2, 'positive and explicit zero saved with correct stock');
    revisionCheck($batch->fresh()->number_serial_first === '001', 'usage retains the random-use batch range');
    try {
        $service->save($user, $station->id, '2026-01-01', [$keys[0] => 1, $keys[1] => 0]);
        throw new RuntimeException('Duplicate accepted');
    } catch (ValidationException) {
        revisionCheck((int) $batch->fresh()->quantity === 7, 'duplicate date and coverage cannot deduct twice');
    }
    try {
        $service->save($user, $station->id, '2026-01-02', [$keys[0] => 5, $keys[1] => 5]);
        throw new RuntimeException('Overspend accepted');
    } catch (ValidationException) {
        revisionCheck((int) $batch->fresh()->quantity === 7 && MaterialUsage::count() === 1, 'shared-batch overspend rolls back all rows');
    }
    $service->save($user, $station->id, '2026-01-01', [$keys[0] => 1, $keys[1] => 1], '', $usage->id);
    revisionCheck((int) $batch->fresh()->quantity === 8, 'editing restores original stock exactly once');
    $trend = (new App\Services\DashboardStatsService)->getDailyPnbpGunmatTrend(1, 2026);
    revisionCheck($trend['pnbp'][0] == 300 && $trend['gunmat'][0] == 2, 'PNBP uses service tariffs and Gunmat uses actual quantities');
    $calendar = (new App\Services\ReportingComplianceService)->calendar('2026-01-01', '2026-01-02');
    revisionCheck($calendar['rows'][0]['cells']['2026-01-01']['complete'] && ! $calendar['rows'][0]['cells']['2026-01-02']['reported'], 'calendar distinguishes complete and missing days');
    $other = revisionUser('Polres', (string) Str::uuid());
    try {
        $service->save($other, $station->id, '2026-01-01', [$keys[0] => 0, $keys[1] => 0]);
        throw new RuntimeException('Foreign station accepted');
    } catch (Symfony\Component\HttpKernel\Exception\HttpException $e) {
        revisionCheck($e->getStatusCode() === 403, 'cross-station usage blocked');
    }

    $admin = revisionUser('Admin');
    Auth::setUser($admin);
    $source = Stock::create(['type_id' => $type->id, 'regional_police_id' => $region->id, 'quantity' => 100, 'is_active' => true]);
    $sourceBatch = StockDetail::create(['stock_id' => $source->id, 'type_id' => $type->id, 'regional_police_id' => $region->id, 'quantity' => 100, 'number_serial_first' => 'A000001', 'number_serial_second' => 'A000100', 'is_active' => true]);
    $header = ['code' => 'SPPM/1/VII/LOG.3.6.7./2026', 'shipment_date' => '2026-10-01', 'sender_regional_police_id' => $region->id, 'receiver_police_station_id' => $station->id, 'status' => 'draft', 'is_active' => true];
    $details = [['stock_detail_id' => $sourceBatch->id, 'quantity' => 10, 'number_serial_first' => 'A000001', 'number_serial_second' => 'A000010']];
    $shipment = CreateMaterialShipmentAction::run($header, $details);
    revisionCheck((int) $sourceBatch->fresh()->quantity === 100 && $user->can('view', $shipment), 'draft visible to recipient before stock deduction');
    $shipment->markAsShipped();
    revisionCheck((int) $sourceBatch->fresh()->quantity === 90 && $sourceBatch->fresh()->number_serial_first === 'A000011', 'dispatch deducts quantity and advances remaining serial range');
    try {
        $shipment->markAsReceived($user);
        throw new RuntimeException('Unscanned receive accepted');
    } catch (RuntimeException $e) {
        revisionCheck(str_contains($e->getMessage(), 'warehouse'), 'receipt requires warehouse scan');
    }
    $image = imagecreatetruecolor(2, 2);
    ob_start();
    imagepng($image);
    $signature = 'data:image/png;base64,'.base64_encode(ob_get_clean());
    $picking = ['picker_name' => 'Test', 'picker_rank' => 'Test', 'picker_position' => 'Test', 'picker_signature' => $signature];
    $shipment->recordPicking($admin, $picking);
    revisionCheck((int) $sourceBatch->fresh()->quantity === 90 && $shipment->picked_at !== null, 'already shipped SPPM remains scannable without duplicate deduction');
    $shipment->markAsReceived($user);
    revisionCheck((int) $sourceBatch->fresh()->quantity === 90 && (int) $stock->fresh()->quantity === 18, 'receipt credits Polres once without second sender deduction');
    try {
        $shipment->markAsReceived($user);
        throw new RuntimeException('Double receipt accepted');
    } catch (RuntimeException $e) {
        revisionCheck((int) $stock->fresh()->quantity === 18, 'repeated receipt does not credit twice');
    }
    $header['code'] = 'SPPM/2/VII/LOG.3.6.7./2026';
    $details[0]['number_serial_first'] = 'A000050';
    $details[0]['number_serial_second'] = 'A000059';
    $middle = CreateMaterialShipmentAction::run($header, $details);
    $middle->recordPicking($admin, $picking);
    revisionCheck((int) $source->fresh()->quantity === 80 && StockDetail::where('regional_police_id', $region->id)->sum('quantity') == 80, 'warehouse scan of draft splits middle interval and preserves total');
    revisionCheck(StockDetail::where('number_serial_first', 'A000060')->where('number_serial_second', 'A000100')->exists(), 'right-hand serial remainder retained');
    try {
        CreateMaterialShipmentAction::run($header, $details);
        throw new RuntimeException('Duplicate number accepted');
    } catch (RuntimeException $e) {
        revisionCheck(str_contains($e->getMessage(), 'sudah digunakan'), 'duplicate SPPM numbers rejected');
    }
    $print = new App\Livewire\Admin\MenuPolda\MaterialShipment\AdminMenuPoldaMaterialShipmentPrint;
    $print->mount($shipment->id);
    $html = $print->render()->with(get_object_vars($print))->with('errors', new Illuminate\Support\ViewErrorBag)->render();
    revisionCheck(str_contains($html, 'A000010') && str_contains($html, 'Lembar') && str_contains($html, 'data:image/svg+xml;base64,'), 'SPPM renders sent serials, metadata unit and local barcode');
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.menu-polda.material-shipment.admin-menu-polda-material-shipment-print', ['shipment' => $shipment->fresh()->load('materialShipmentDetails.type'), 'shipmentDetails' => $shipment->materialShipmentDetails, 'formatter' => $print, 'mode' => 'qr', 'isPdf' => true])->output();
    file_put_contents('/tmp/armaster-revision-sppm.pdf', $pdf);
    revisionCheck(str_starts_with($pdf, '%PDF'), 'SPPM PDF generated with embedded barcode');
    $print->mode = 'ttd_ka';
    $ka = $print->render()->with(get_object_vars($print))->render();
    revisionCheck(! str_contains($ka, 'data:image/svg+xml;base64,'), 'KA submission leaves approval signature empty');
    $reception = App\Models\Reception\Reception::create(['code' => 'TEST-RECEPTION', 'name' => 'Test Reception', 'date' => '2026-10-01', 'type' => 'penerimaan', 'regional_police_id' => $region->id, 'is_active' => true]);
    $officers = new App\Livewire\Admin\MenuPolda\Reception\Detail\AdminMenuPoldaReceptionDetailIndex;
    $officers->receptionId = $reception->id;
    $officers->regionalPoliceId = $region->id;
    $officers->ordonatur_name = 'Direktur Uji';
    $officers->ordonatur_rank = 'Kombes';
    $officers->ordonatur_nrp = '12345678';
    $officers->saveOfficials('officials');
    revisionCheck($reception->fresh()->ordonatur_nrp === '12345678' && (int) $source->fresh()->quantity === 80, 'saving officials stores NRP without changing inventory');
    $reception->update(['kasi_signature' => $signature, 'director_signature' => $signature]);
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.menu-polda.reception.admin-menu-polda-reception-print', ['reception' => $reception->fresh(), 'receptionDetails' => [], 'isPdf' => true])->output();
    file_put_contents('/tmp/armaster-revision-reception.pdf', $pdf);
    revisionCheck(str_starts_with($pdf, '%PDF'), 'reception PDF embeds uploaded signatures');
    echo "All revision integration checks passed; temporary writes rolled back.\n";
} catch (Throwable $e) {
    DB::rollBack();
    fwrite(STDERR, $e->getMessage()."\n".$e->getTraceAsString()."\n");
    exit(1);
} finally {
    if (DB::transactionLevel() > 0) {
        DB::rollBack();
    }
}
