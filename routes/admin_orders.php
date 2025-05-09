<?php

use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Orders Routes
|--------------------------------------------------------------------------
|
| Rotas para o painel administrativo de gerenciamento de pedidos
|
*/

Route::middleware(['auth', 'rolemanager:admin'])->group(function () {
    // Gerenciamento de pedidos
    Route::get('/admin/pedidos', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/admin/pedidos/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/admin/pedidos/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
    Route::post('/admin/pedidos/{id}/rastreio', [OrderController::class, 'updateTracking'])->name('admin.orders.update-tracking');
    Route::get('/admin/pedidos/{id}/etiqueta', [OrderController::class, 'generateShippingLabel'])->name('admin.orders.shipping-label');
});
