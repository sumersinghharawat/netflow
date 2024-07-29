<?php

use App\Http\Controllers\CoreInfController;
use App\Http\Controllers\DemoRegisterController;
use App\Http\Controllers\OcRegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('VerifyToken')->group(function () {
    Route::post('/activate/order', [OcRegisterController::class, 'approveInstantOrder']);
    Route::post('/activate/checkout', [OcRegisterController::class, 'approveInstantCheckout']);
    Route::post('/activate/upgrade/order', [OcRegisterController::class, 'approveInstatntUpgardeOrder']);
    Route::post('/activate/renew/order', [OcRegisterController::class, 'approveInstatntRenewal']);
    Route::post('/activate/commission/order', [OcRegisterController::class, 'ecomcommission']);

});
Route::post('/register/custom-demo', [DemoRegisterController::class, 'registerDemo']);
Route::post('/custom-demo/store-demo', [DemoRegisterController::class, 'storeDemo']);
Route::post('/set-data', [DemoRegisterController::class, 'setData']);
Route::post('/valid_user', [DemoRegisterController::class, 'isUserValid']);
