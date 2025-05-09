<?php

use App\Http\Controllers\Site\RedeItauNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas para Webhooks de Pagamento
|--------------------------------------------------------------------------
|
| Estas rotas são utilizadas para receber notificações dos gateways de pagamento
|
*/

// Webhooks da Rede Itaú
Route::prefix('webhook')->group(function () {
    Route::post('/rede-itau', [RedeItauNotificationController::class, 'handleNotification'])
        ->name('webhook.rede-itau');
});
