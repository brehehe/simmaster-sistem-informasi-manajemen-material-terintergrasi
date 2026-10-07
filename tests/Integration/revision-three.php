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
    foreach (['types', 'type_details', 'services', 'service_details', 'regional_police', 'police_stations', 'stocks', 'stock_details', 'material_usages', 'material_usage_details', 'material_usage_detail_items', 'material_shipments', 'material_shipment_details', 'history_stocks', 'receptions', 'last_stocks', 'last_stock_details'] as $table) {
        DB::statement('CREATE TEMP TABLE '.$table.' (LIKE public.'.$table.' INCLUDING ALL) ON COMMIT DROP');
    }
    Livewire\Livewire::test(App\Livewire\Auth\Login\AuthLoginIndex::class)
        ->set('email', [])
        ->set('password', [])
        ->set('remember', [])
        ->call('login')
        ->assertHasErrors(['email', 'password', 'remember']);
    revisionCheck(true, 'invalid login payload is handled as validation instead of a server error');
    $region = RegionalPolice::create(['name' => 'Test Region', 'is_active' => true]);
    $station = PoliceStation::create(['name' => 'Test Station', 'regional_police_id' => $region->id, 'is_active' => true]);
    $type = Type::create(['name' => 'Test Material', 'price' => 10, 'unit' => 'Lembar', 'is_active' => true]);
    $svc1 = Service::create(['name' => 'First Service', 'type_id' => $type->id, 'price' => 100, 'is_active' => true]);
    $svc2 = Service::create(['name' => 'Second Service', 'type_id' => $type->id, 'price' => 200, 'is_active' => true]);
    $stock = Stock::create(['type_id' => $type->id, 'police_station_id' => $station->id, 'quantity' => 10, 'is_active' => true]);
    $batch = StockDetail::create(['stock_id' => $stock->id, 'type_id' => $type->id, 'police_station_id' => $station->id, 'quantity' => 10, 'number_serial_first' => '001', 'number_serial_second' => '010', 'is_active' => true]);
    $user = revisionUser('Polres', $station->id);
    Auth::setUser($user);
    $initialStockForm = new App\Livewire\Admin\MenuPolres\LastStock\Detail\AdminMenuPolresLastStockDetailIndex;
    $initialStockForm->mount();
    revisionCheck($initialStockForm->policeStationId === $station->id && collect($initialStockForm->types)->contains('id', $type->id), 'initial stock input is available for the signed-in Polres');

    $otherStationForStock = PoliceStation::create(['name' => 'Other Stock Station', 'regional_police_id' => $region->id, 'is_active' => true]);
    $foreignInitialStock = App\Models\LastStock\LastStock::create([
        'code' => 'LS-FOREIGN',
        'date' => '2026-01-01',
        'type_id' => $type->id,
        'police_station_id' => $otherStationForStock->id,
        'regional_police_id' => $region->id,
        'is_active' => true,
    ]);
    try {
        (new App\Livewire\Admin\MenuPolres\LastStock\Detail\AdminMenuPolresLastStockDetailIndex)->mount($foreignInitialStock->id);
        throw new RuntimeException('Foreign initial stock edit accepted');
    } catch (Symfony\Component\HttpKernel\Exception\HttpException $e) {
        revisionCheck($e->getStatusCode() === 403, 'Polres cannot edit another station initial stock');
    }
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
    $legacySameDate = MaterialUsage::create([
        'code' => 'MU-LEGACY-SAME-DATE',
        'date' => '2026-01-01',
        'police_station_id' => $station->id,
        'is_active' => true,
    ]);
    $legacySameDateDetail = $legacySameDate->materialUsageDetails()->create([
        'type_id' => $type->id,
        'quantity' => 0,
        'usage_type' => 'Material Digunakan',
        'is_active' => true,
    ]);
    $legacySameDateDetail->materialUsageDetailItems()->create([
        'material_usage_id' => $legacySameDate->id,
        'type_id' => $type->id,
        'service_id' => $svc1->id,
        'quantity' => 0,
        'usage_type' => 'Material Digunakan',
        'is_active' => true,
    ]);
    $debtUsage = $service->save($user, $station->id, '2026-02-01', [$keys[0] => 5, $keys[1] => 5]);
    revisionCheck((int) StockDetail::where('police_station_id', $station->id)->where('type_id', $type->id)->sum('quantity') === -3, 'usage above available stock is saved as a negative balance');
    app(App\Services\StockService::class)->deleteMaterialUsage($debtUsage);
    foreach ($debtUsage->materialUsageDetails as $debtDetail) {
        $debtDetail->materialUsageDetailItems()->delete();
    }
    $debtUsage->materialUsageDetails()->delete();
    $debtUsage->delete();
    Livewire\Livewire::test(App\Livewire\Admin\MenuPolres\MaterialUsage\Detail\AdminMenuPolresMaterialUsageDetailIndex::class, ['id' => $usage->id])
        ->set('quantities.'.$keys[0], 1)
        ->set('quantities.'.$keys[1], 1)
        ->call('save')
        ->assertHasNoErrors();
    revisionCheck((int) $batch->fresh()->quantity === 8, 'editing restores original stock exactly once');
    revisionCheck($legacySameDate->fresh()->is_active, 'existing usage remains editable when another legacy report has the same date');
    $trend = (new App\Services\DashboardStatsService)->getDailyPnbpGunmatTrend(1, 2026);
    revisionCheck($trend['pnbp'][0] == 300 && $trend['gunmat'][0] == 2, 'PNBP uses service tariffs and Gunmat uses actual quantities');
    $calendar = (new App\Services\ReportingComplianceService)->calendar('2026-01-01', '2026-01-02');
    $calendarStation = collect($calendar['rows'])->firstWhere('id', $station->id);
    revisionCheck($calendarStation['cells']['2026-01-01']['complete'] && ! $calendarStation['cells']['2026-01-02']['reported'], 'calendar distinguishes complete and missing days');
    // Historical deficits may be edited, reduced, or increased and remain auditable.
    $batch->update(['quantity' => -5]);
    $stock->update(['quantity' => -5]);
    $service->save($user, $station->id, '2026-01-01', [$keys[0] => 0, $keys[1] => 1], '', $usage->id);
    revisionCheck((int) StockDetail::where('police_station_id', $station->id)->where('type_id', $type->id)->sum('quantity') === -4, 'reducing usage repairs a negative balance by the exact delta');
    $service->save($user, $station->id, '2026-01-01', [$keys[0] => 0, $keys[1] => 1], '', $usage->id);
    revisionCheck((int) StockDetail::where('police_station_id', $station->id)->where('type_id', $type->id)->sum('quantity') === -4, 'unchanged negative-stock report does not deduct twice');
    $service->save($user, $station->id, '2026-01-01', [$keys[0] => 0, $keys[1] => 2], '', $usage->id);
    revisionCheck((int) StockDetail::where('police_station_id', $station->id)->where('type_id', $type->id)->sum('quantity') === -5, 'increased usage remains saved as a larger negative balance');

    $svc1->update(['is_active' => false]);
    $legacyCatalog = $service->catalog(null, false, $usage->fresh('materialUsageDetails.materialUsageDetailItems'));
    revisionCheck(isset($legacyCatalog[$keys[0]]), 'previous transaction retains inactive material row for editing');
    $service->save($user, $station->id, '2026-01-01', [$keys[0] => 0, $keys[1] => 1], '', $usage->id);
    revisionCheck((int) StockDetail::where('police_station_id', $station->id)->where('type_id', $type->id)->sum('quantity') === -4, 'previous-date transaction with changed catalog remains editable');
    $svc1->update(['is_active' => true]);

    StockDetail::where('police_station_id', $station->id)->where('type_id', $type->id)->update(['quantity' => 0]);
    Stock::where('police_station_id', $station->id)->where('type_id', $type->id)->update(['quantity' => 0]);
    $batch->update(['quantity' => 8]);
    $stock->update(['quantity' => 8]);

    $other = revisionUser('Polres', (string) Str::uuid());
    try {
        $service->save($other, $station->id, '2026-01-01', [$keys[0] => 0, $keys[1] => 0]);
        throw new RuntimeException('Foreign station accepted');
    } catch (Symfony\Component\HttpKernel\Exception\HttpException $e) {
        revisionCheck($e->getStatusCode() === 403, 'cross-station usage blocked');
    }

    $admin = revisionUser('Admin');
    Auth::setUser($admin);
    $admin->setRelation('userType', (new App\Models\User\UserType)->forceFill(['types' => [(string) Str::uuid()]]));
    $usageIndex = (new App\Livewire\Admin\MenuPolres\MaterialUsage\AdminMenuPolresMaterialUsageIndex)->render();
    revisionCheck($usageIndex->getData()['materialUsages']->total() > 0, 'admin usage list is not restricted by an assigned material type');
    $source = Stock::create(['type_id' => $type->id, 'regional_police_id' => $region->id, 'quantity' => 100, 'is_active' => true]);
    $sourceBatch = StockDetail::create(['stock_id' => $source->id, 'type_id' => $type->id, 'regional_police_id' => $region->id, 'quantity' => 100, 'number_serial_first' => 'A000001', 'number_serial_second' => 'A000100', 'is_active' => true]);
    $header = ['code' => 'SPPM/1/VII/LOG.3.6.7./2026', 'shipment_date' => '2026-10-01', 'sender_regional_police_id' => $region->id, 'receiver_police_station_id' => $station->id, 'status' => 'draft', 'is_active' => true];
    $details = [['stock_detail_id' => $sourceBatch->id, 'quantity' => 10, 'number_serial_first' => 'A000001', 'number_serial_second' => 'A000010']];
    $shipment = CreateMaterialShipmentAction::run($header, $details);
    revisionCheck((int) $sourceBatch->fresh()->quantity === 100 && $user->can('view', $shipment), 'draft visible to recipient before stock deduction');
    Auth::setUser($user);
    $receiveDetail = new App\Livewire\Polres\MenuPolres\MaterialShipment\PolresMenuPolresMaterialShipmentReceiveDetail;
    $receiveDetail->mount($shipment->id);
    revisionCheck($receiveDetail->shipment instanceof App\Models\MenuPolda\MaterialShipment\MaterialShipment, 'Polres can open incoming material detail');
    Auth::setUser($admin);
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
    $drawImage = imagecreatetruecolor(200, 60);
    imagefill($drawImage, 0, 0, imagecolorallocate($drawImage, 255, 255, 255));
    imageline($drawImage, 10, 20, 180, 40, imagecolorallocate($drawImage, 20, 35, 60));
    ob_start();
    imagepng($drawImage);
    $drawBytes = ob_get_clean();
    imagedestroy($drawImage);
    $drawn = 'data:image/png;base64,'.base64_encode($drawBytes);
    $officers->kasiSignatureMode = 'draw';
    $officers->kasiSignatureDrawn = $drawn;
    $officers->directorSignatureMode = 'upload';
    $uploadPath = tempnam(sys_get_temp_dir(), 'armaster-signature-');
    file_put_contents($uploadPath, $drawBytes);
    $officers->directorSignatureUpload = new \Illuminate\Http\UploadedFile($uploadPath, 'signature.png', 'image/png', null, true);
    $officers->saveOfficials('officials');
    revisionCheck($reception->fresh()->kasi_signature === $drawn && $reception->fresh()->director_signature === $drawn, 'drawn and uploaded signatures save together');
    unlink($uploadPath);
    $officers->saveOfficials('commission');
    revisionCheck($reception->fresh()->director_signature === $drawn, 'saving commission preserves officer signatures');
    $officers->kasiSignatureDrawn = 'data:image/png;base64,invalid';
    try {
        $officers->saveOfficials('officials');
        throw new RuntimeException('Invalid signature accepted');
    } catch (ValidationException) {
        revisionCheck($reception->fresh()->kasi_signature === $drawn, 'invalid drawing leaves saved signature intact');
    }
    $reception->update(['kasi_signature' => $signature, 'director_signature' => $signature]);
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.menu-polda.reception.admin-menu-polda-reception-print', ['reception' => $reception->fresh(), 'receptionDetails' => [], 'isPdf' => true])->output();
    file_put_contents('/tmp/armaster-revision-reception.pdf', $pdf);
    revisionCheck(str_starts_with($pdf, '%PDF'), 'reception PDF embeds uploaded signatures');
    \Livewire\Livewire::test(App\Livewire\Admin\MenuPolda\Reception\Detail\AdminMenuPoldaReceptionDetailIndex::class)
        ->assertSee('Gambar langsung')->assertSee('Unggah gambar')->assertSee('Tanda Tangan Pejabat');
    revisionCheck(true, 'reception form renders both signature methods');
    $usageForm = \Livewire\Livewire::test(App\Livewire\Admin\MenuPolres\MaterialUsage\Detail\AdminMenuPolresMaterialUsageDetailIndex::class)
        ->assertSee('Rincian Penggunaan Material')->assertSee('Sudah diisi')->assertSee('First Service')
        ->assertSee('Seluruh kolom otomatis berisi 0');
    revisionCheck(collect($usageForm->get('quantities'))->every(fn ($quantity) => $quantity === 0), 'daily usage form initializes all quantities with zero');
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
