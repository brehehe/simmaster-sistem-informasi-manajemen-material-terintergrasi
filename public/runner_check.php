<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->boot();

header('Content-Type: application/json');

use App\Models\Police\PoliceStation;
use App\Models\Type\Type;
use App\Models\User;
use App\Models\User\UserType;
use App\Models\Stock\Stock;
use App\Models\Stock\StockDetail;
use App\Models\LastStock\LastStock;
use App\Models\LastStock\LastStockDetail;
use App\Models\Service\Service;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetailItem;

$result = [];

// 1. Check Bangkalan STNK
$bangkalan = PoliceStation::where('name', 'ilike', '%bangkalan%')->first();
$stnk = Type::where('name', 'STNK')->first();
$bangkalanData = [];
if ($bangkalan && $stnk) {
    $ls = LastStock::where('police_station_id', $bangkalan->id)->where('type_id', $stnk->id)->with('lastStockDetails')->get();
    $stocks = Stock::where('police_station_id', $bangkalan->id)->where('type_id', $stnk->id)->with('stockDetails')->get();
    $bangkalanData = [
        'lastStocks' => $ls->toArray(),
        'stocks' => $stocks->toArray(),
    ];
}
$result['bangkalan'] = $bangkalanData;

// 2. Check Mojokerto Kab
$mojokerto = PoliceStation::where('name', 'ilike', '%mojokerto%')->where('name', 'not ilike', '%kota%')->first();
$mojoData = [];
if ($mojokerto) {
    $types = Type::whereIn('name', ['TNKB R2 PUTIH', 'MUTASI'])->get();
    foreach ($types as $t) {
        $st = Stock::where('police_station_id', $mojokerto->id)->where('type_id', $t->id)->with('stockDetails')->get();
        $sdSum = StockDetail::where('police_station_id', $mojokerto->id)->where('type_id', $t->id)->where('is_active', true)->sum('quantity');
        $mojoData[$t->name] = [
            'stock_table_sum' => $st->sum('quantity'),
            'stock_detail_sum' => $sdSum,
            'stocks' => $st->toArray(),
        ];
    }
}
$result['mojokerto'] = $mojoData;

// 3. Check Samsat SBY Barat
$userBarat = User::where('email', 'bamat-samsat-barat@armaster.net')->with('userType')->first();
$result['samsat_barat'] = [
    'user' => $userBarat ? $userBarat->toArray() : null,
    'types_names' => $userBarat && $userBarat->userType ? Type::whereIn('id', $userBarat->userType->types ?? [])->pluck('name')->toArray() : [],
];

// 4. Check STNK services
$stnkServices = Service::where('type_id', $stnk->id)->get(['id', 'name', 'price', 'is_active']);
$result['stnk_services'] = $stnkServices->toArray();

echo json_encode($result, JSON_PRETTY_PRINT);
