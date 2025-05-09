<?php

use App\Http\Controllers\Site\CustomerOrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Area Routes
|--------------------------------------------------------------------------
|
| Rotas para área do cliente relacionadas a pedidos, endereços e preferências
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Rotas para gerenciamento de pedidos do cliente
    Route::prefix('minha-conta')->name('site.customer.')->group(function () {
        // Pedidos
        Route::get('/pedidos', [CustomerOrderController::class, 'index'])->name('orders.index');
        Route::get('/pedidos/{id}', [CustomerOrderController::class, 'show'])->name('orders.show');
        Route::get('/pedidos/{id}/pagamento', [CustomerOrderController::class, 'paymentLink'])->name('orders.payment');
        Route::get('/pedidos/{id}/cancelar', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/pedidos/{id}/confirmar-recebimento', [CustomerOrderController::class, 'confirmReceipt'])->name('orders.confirm-receipt');
    });
});
