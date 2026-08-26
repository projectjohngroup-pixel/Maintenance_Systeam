<?php

/*
|--------------------------------------------------------------------------
| API ROUTES (REACT NATIVE)
|--------------------------------------------------------------------------
|
| Semua endpoint berprefiks /api.
| Autentikasi memakai Bearer Token (tabel api_tokens).
| React Native TIDAK boleh mengakses database secara langsung.
|
*/

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiDashboardController;
use App\Http\Controllers\Api\ApiNotificationController;
use App\Http\Controllers\Api\ApiWorkOrderController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIK
|--------------------------------------------------------------------------
*/

Route::post('/login', [ApiAuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('api.login');

/*
|--------------------------------------------------------------------------
| TERAUTENTIKASI (BEARER TOKEN)
|--------------------------------------------------------------------------
*/

Route::middleware([
    App\Http\Middleware\CheckApiToken::class,
])->group(function () {

    Route::post('/logout', [ApiAuthController::class, 'logout'])
        ->name('api.logout');

    Route::get('/dashboard', [ApiDashboardController::class, 'index'])
        ->name('api.dashboard');

    Route::get('/work-orders', [ApiWorkOrderController::class, 'index'])
        ->name('api.work-orders.index');

    Route::get('/work-orders/{id}', [ApiWorkOrderController::class, 'show'])
        ->whereNumber('id')
        ->name('api.work-orders.show');

    Route::patch(
        '/work-orders/{id}/status',
        [ApiWorkOrderController::class, 'updateStatus']
    )
        ->whereNumber('id')
        ->name('api.work-orders.status');

    Route::get('/notifications', [ApiNotificationController::class, 'index'])
        ->name('api.notifications.index');

});
