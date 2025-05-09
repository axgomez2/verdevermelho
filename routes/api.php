<?php

use App\Http\Controllers\Site\AddressController;
use App\Http\Controllers\Site\ShippingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API para endereços
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/addresses', [AddressController::class, 'apiIndex']);
    Route::post('/addresses', [AddressController::class, 'apiStore']);
    Route::put('/addresses/{address}/default', [AddressController::class, 'apiSetDefault']);
    Route::delete('/addresses/{address}', [AddressController::class, 'apiDestroy']);
});

// API para cálculo de frete
Route::prefix('shipping')->group(function () {
    Route::post('/calculate', [ShippingController::class, 'apiCalculate']);
    Route::post('/select', [ShippingController::class, 'apiSelectOption']);
});
