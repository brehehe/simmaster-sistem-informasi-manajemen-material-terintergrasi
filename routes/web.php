<?php

use App\Livewire\Admin\Dashboard\AdminDashboardIndex;
use App\Livewire\Admin\Master\PoliceStation\AdminMasterPoliceStationIndex;
use App\Livewire\Admin\Master\RegionalPolice\AdminMasterRegionalPoliceIndex;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::group(['middleware' => ['auth', 'verified'], 'namespace' => 'App\\Livewire\\Admin'], function () {
    Route::get('dashboard', 'Dashboard\\AdminDashboardIndex')
        ->name('dashboard');

    Route::group(['namespace' => 'Master'], function () {
        Route::get('master/regional-police', 'RegionalPolice\\AdminMasterRegionalPoliceIndex')
            ->name('master.regional-police');

        Route::get('master/police-station', 'PoliceStation\\AdminMasterPoliceStationIndex')
            ->name('master.police-station');

        Route::get('master/type', 'Type\\AdminMasterTypeIndex')
            ->name('master.type');

        Route::get('master/type-detail', 'TypeDetail\\AdminMasterTypeDetailIndex')
            ->name('master.type-detail');

        Route::get('master/user', 'User\\AdminMasterUserIndex')
            ->name('master.user');

        Route::get('master/user-type', 'UserType\\AdminMasterUserTypeIndex')
            ->name('master.user-type');

        Route::get('master/rack', 'Rack\\AdminMasterRackIndex')
            ->name('master.rack');
    });

    Route::group(['namespace' => 'Report'], function () {
        Route::get('report/delivery', 'Delivery\\AdminReportDeliveryIndex')
            ->name('report.delivery');

        Route::get('report/delivery/{id}/detail', 'Delivery\\Detail\\AdminReportDeliveryDetailIndex')
            ->name('report.delivery.detail');

        Route::get('report/reception', 'Reception\\AdminReportReceptionIndex')
            ->name('report.reception');

        Route::get('report/reception/{id}/detail', 'Reception\\Detail\\AdminReportReceptionDetailIndex')
            ->name('report.reception.detail');

        Route::get('report/stock-opname', 'StockOpname\\AdminReportStockOpnameIndex')
            ->name('report.stock-opname');

        Route::get('report/stock-opname/{id}/detail', 'StockOpname\\Detail\\AdminReportStockOpnameDetailIndex')
            ->name('report.stock-opname.detail');

        Route::get('report/stock', 'Stock\\AdminReportStockIndex')
            ->name('report.stock');

        Route::get('report/stock-in', 'StockIn\\AdminReportStockInIndex')
            ->name('report.stock-in');

        Route::get('report/stock-out', 'StockOut\\AdminReportStockOutIndex')
            ->name('report.stock-out');
    });

    Route::group(['namespace' => 'MenuPolda'], function () {
        Route::get('menu-polda/reception', 'Reception\\AdminMenuPoldaReceptionIndex')
            ->name('menu-polda.reception');

        Route::get('menu-polda/reception/edit/{id}', 'Reception\\Detail\\AdminMenuPoldaReceptionDetailIndex')
            ->name('menu-polda.reception.edit');

        Route::get('menu-polda/reception/create', 'Reception\\Detail\\AdminMenuPoldaReceptionDetailIndex')
            ->name('menu-polda.reception.create');

        Route::get('menu-polda/last-stock', 'LastStock\\AdminMenuPoldaLastStockIndex')
            ->name('menu-polda.last-stock');

        Route::get('menu-polda/last-stock/edit/{id}', 'LastStock\\Detail\\AdminMenuPoldaLastStockDetailIndex')
            ->name('menu-polda.last-stock.edit');

        Route::get('menu-polda/last-stock/create', 'LastStock\\Detail\\AdminMenuPoldaLastStockDetailIndex')
            ->name('menu-polda.last-stock.create');

        Route::get('menu-polda/history', 'History\\AdminMenuPoldaHistoryIndex')
            ->name('menu-polda.history');

        Route::get('menu-polda/stock', 'Stock\\AdminMenuPoldaStockIndex')
            ->name('menu-polda.stock');

        // Rack Assignment
        Route::get('menu-polda/rack-assignment', 'RackAssignment\\AdminMenuPoldaRackAssignmentIndex')
            ->name('menu-polda.rack-assignment');
        Route::get('menu-polda/rack-assignment/create', 'RackAssignment\\Detail\\AdminMenuPoldaRackAssignmentDetailIndex')
            ->name('menu-polda.rack-assignment.create');
        Route::get('menu-polda/rack-assignment/edit/{id}', 'RackAssignment\\Detail\\AdminMenuPoldaRackAssignmentDetailIndex')
            ->name('menu-polda.rack-assignment.edit');

        // Material Usage
        Route::get('menu-polda/material-usage', 'MaterialUsage\\AdminMenuPoldaMaterialUsageIndex')
            ->name('menu-polda.material-usage');
        Route::get('menu-polda/material-usage/create', 'MaterialUsage\\Detail\\AdminMenuPoldaMaterialUsageDetailIndex')
            ->name('menu-polda.material-usage.create');
        Route::get('menu-polda/material-usage/edit/{id}', 'MaterialUsage\\Detail\\AdminMenuPoldaMaterialUsageDetailIndex')
            ->name('menu-polda.material-usage.edit');

        // Material Damage
        Route::get('menu-polda/material-damage', 'MaterialDamage\\AdminMenuPoldaMaterialDamageIndex')
            ->name('menu-polda.material-damage');
        Route::get('menu-polda/material-damage/create', 'MaterialDamage\\Detail\\AdminMenuPoldaMaterialDamageDetailIndex')
            ->name('menu-polda.material-damage.create');
        Route::get('menu-polda/material-damage/edit/{id}', 'MaterialDamage\\Detail\\AdminMenuPoldaMaterialDamageDetailIndex')
            ->name('menu-polda.material-damage.edit');

        // Material Shipment
        Route::get('menu-polda/material-shipment', 'MaterialShipment\\AdminMenuPoldaMaterialShipmentIndex')
            ->name('menu-polda.material-shipment');
        Route::get('menu-polda/material-shipment/create', 'MaterialShipment\\Detail\\AdminMenuPoldaMaterialShipmentCreate')
            ->name('menu-polda.material-shipment.create');
        Route::get('menu-polda/material-shipment/edit/{id}', 'MaterialShipment\\Detail\\AdminMenuPoldaMaterialShipmentCreate')
            ->name('menu-polda.material-shipment.edit');
    });

    Route::group(['namespace' => 'Stock'], function () {
        Route::get('stock/polda', 'Polda\\AdminStockPoldaIndex')
            ->name('stock.polda');

        Route::get('stock/polres', 'Polres\\AdminStockPolresIndex')
            ->name('stock.polres');

        Route::get('stock/history', 'History\\AdminStockHistoryIndex')
            ->name('stock.history');
    });
});

// Polres Routes (Outside Admin namespace)
Route::group(['middleware' => ['auth', 'verified'], 'namespace' => 'App\\Livewire\\Polres\\MenuPolres'], function () {
    // Material Shipment - Receive
    Route::get('menu-polres/material-shipment/receive', 'MaterialShipment\\PolresMenuPolresMaterialShipmentReceiveIndex')
        ->name('menu-polres.material-shipment.receive');
    Route::get('menu-polres/material-shipment/receive/{id}', 'MaterialShipment\\PolresMenuPolresMaterialShipmentReceiveDetail')
        ->name('menu-polres.material-shipment.receive.detail');
});

Route::group(['namespace' => 'App\\Livewire\\Auth\\Login'], function () {
    Route::get('login', 'AuthLoginIndex')
        ->name('login');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::redirect('', 'login');
