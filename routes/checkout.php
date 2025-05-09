<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\CheckoutController;
use App\Http\Controllers\Site\PaymentController;
use App\Http\Controllers\Site\PaymentNotificationController;

Route::middleware(['auth'])->prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('site.checkout.index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('site.checkout.process');
});

// Rotas de Pagamento
Route::middleware(['auth'])->prefix('pagamentos')->group(function () {
    Route::get('/boleto/{order}', [PaymentController::class, 'showBoletoPage'])->name('site.payments.boleto');
    Route::get('/pix/{order}', [PaymentController::class, 'showPixPage'])->name('site.payments.pix');
    Route::get('/check-pix-status/{order}/{pixId}', [PaymentController::class, 'checkPixStatus'])->name('site.payments.check-pix-status');
});

// Rota de notificação do PagSeguro (não requer autenticação)
Route::post('/pagamentos/notificacao', [PaymentNotificationController::class, 'handlePagSeguroNotification']);
