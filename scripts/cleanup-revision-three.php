<?php

// php scripts/cleanup-revision-three.php [--apply]
// Explicitly identified test fixtures only. Export each affected row before hiding/deleting it.
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$roots = [
    'types' => ['MATERIAL AUDIT TEST', 'Test'],
    'type_details' => ['Test'],
    'services' => ['Test', 'Test 123', 'Tes'],
    'service_details' => ['Test', 'Test Service '],
    'regional_police' => ['POLDA TEST AUDIT'],
    'police_stations' => ['POLRES TEST AUDIT'],
];
$columns = collect(DB::select("SELECT table_name,column_name FROM information_schema.columns WHERE table_schema='public'"))->groupBy('table_name')->map(fn ($cols) => $cols->pluck('column_name')->all());
$plan = [];
foreach ($roots as $table => $names) {
    $plan[$table] = DB::table($table)->whereIn('name', $names)->pluck('id')->all();
}
// Rows positively labelled as tests, already reversed and archived by the earlier audit.
$plan['material_usages'] = DB::table('material_usages')->where('description', 'Test Usage')->whereNotNull('deleted_at')->pluck('id')->all();
$plan['history_stocks'] = DB::table('history_stocks')->where('description', 'Stock from last stock: Stok Awal TNKB Test')->whereNotNull('deleted_at')->pluck('id')->all();
$references = [
    'type_id' => 'types', 'type_detail_id' => 'type_details', 'service_id' => 'services', 'service_detail_id' => 'service_details',
    'regional_police_id' => 'regional_police', 'sender_regional_police_id' => 'regional_police', 'receiver_regional_police_id' => 'regional_police',
    'police_station_id' => 'police_stations', 'sender_police_station_id' => 'police_stations', 'receiver_police_station_id' => 'police_stations',
    'stock_id' => 'stocks', 'stock_detail_id' => 'stock_details', 'rack_id' => 'racks', 'last_stock_id' => 'last_stocks', 'last_stock_detail_id' => 'last_stock_details',
    'material_usage_id' => 'material_usages', 'material_usage_detail_id' => 'material_usage_details',
    'material_damage_id' => 'material_damages', 'material_shipment_id' => 'material_shipments', 'material_subsidy_id' => 'material_subsidies',
    'mutation_stock_id' => 'mutation_stocks', 'rack_assignment_id' => 'rack_assignments', 'stock_opname_id' => 'stock_opnames',
    'reception_id' => 'receptions', 'reception_detail_id' => 'reception_details', 'reception_detail_item_id' => 'reception_detail_items',
    'history_stock_id' => 'history_stocks',
];
do {
    $changed = false;
    foreach ($columns as $table => $fields) {
        if (! in_array('id', $fields) || in_array($table, ['users', 'migrations', 'jobs', 'failed_jobs', 'sessions'])) {
            continue;
        }
        foreach ($references as $field => $parent) {
            if (! in_array($field, $fields) || empty($plan[$parent])) {
                continue;
            }
            $ids = DB::table($table)->whereIn($field, $plan[$parent])->pluck('id')->all();
            $new = array_diff($ids, $plan[$table] ?? []);
            if ($new) {
                $plan[$table] = array_values(array_unique(array_merge($plan[$table] ?? [], $new)));
                $changed = true;
            }
        }
    }
} while ($changed);
// Guard against an accidentally shared test owner/master: do not touch real material transactions.
$auditType = DB::table('types')->where('name', 'MATERIAL AUDIT TEST')->value('id');
foreach (['stocks', 'stock_details', 'last_stock_details', 'material_usage_details', 'material_damage_details', 'material_subsidy_details', 'mutation_stock_details', 'rack_assignment_details', 'stock_opname_details'] as $table) {
    if (! empty($plan[$table]) && DB::table($table)->whereIn('id', $plan[$table])->whereNotNull('type_id')->where('type_id', '!=', $auditType)->exists()) {
        // Other explicit test masters may appear only as archived rows; refuse active mixed documents.
        if (in_array('deleted_at', $columns[$table]) && ! DB::table($table)->whereIn('id', $plan[$table])->whereNull('deleted_at')->whereNotNull('type_id')->where('type_id', '!=', $auditType)->exists()) {
            continue;
        }
        throw new RuntimeException('Mixed operational/test records in '.$table.'; manual review required.');
    }
}
foreach ($plan as $table => $ids) {
    if ($ids) {
        echo $table.': '.count($ids).PHP_EOL;
    }
}
if (! in_array('--apply', $argv, true)) {
    echo "Preview only. Use --apply after database backup.\n";
    exit;
}
$path = storage_path('app/private/backups/revision-three-cleanup-'.date('Ymd-His').'.json');
if (! is_dir(dirname($path))) {
    mkdir(dirname($path), 0700, true);
}
DB::transaction(function () use ($plan, $columns, $path) {
    $archive = [];
    foreach ($plan as $table => $ids) {
        if ($ids) {
            $archive[$table] = DB::table($table)->whereIn('id', $ids)->lockForUpdate()->get()->all();
        }
    }
    if (file_put_contents($path, json_encode($archive, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)) === false) {
        throw new RuntimeException('Cannot write cleanup archive.');
    }
    chmod($path, 0600);
    foreach ($plan as $table => $ids) {
        if (! $ids) {
            continue;
        }
        if (in_array('deleted_at', $columns[$table])) {
            $data = ['deleted_at' => now()];
            if (in_array('is_active', $columns[$table])) {
                $data['is_active'] = false;
            }
            DB::table($table)->whereIn('id', $ids)->update($data);
        } else {
            DB::table($table)->whereIn('id', $ids)->delete();
        }
    }
});
echo 'Archived and cleaned '.array_sum(array_map('count',$plan)).' identified test rows. Archive: '.$path.PHP_EOL;
