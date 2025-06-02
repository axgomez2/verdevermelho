<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\CartController;
use App\Http\Controllers\Site\CartItemController;
use App\Http\Controllers\Site\ShippingController;
use App\Http\Controllers\Site\WhatsAppCheckoutController;

Route::prefix('carrinho')->group(function () {
    // Cart routes
    Route::get('/', [CartController::class, 'index'])->name('site.cart.index');
    Route::post('/items', [CartItemController::class, 'store'])->name('site.cart.add');
    Route::delete('/items/{cartItem}', [CartItemController::class, 'destroy'])->name('site.cart.items.destroy');
    Route::post('/items/{cartItem}', [CartItemController::class, 'update'])->name('site.cart.update');
    Route::post('/update/{cartItem}', [CartController::class, 'updateQuantity'])->name('site.cart.updateQuantity');
    Route::post('/check-stock', [CartItemController::class, 'checkStock'])->name('site.cart.checkStock');
    Route::get('/check-items', [CartItemController::class, 'checkCartItems'])->name('site.cart.checkItems');

    // Shipping routes
    Route::post('/postal-code', [CartController::class, 'updatePostalCode'])->name('site.cart.updatePostalCode');
    Route::post('/shipping/update', [CartController::class, 'updateShipping'])->name('site.cart.updateShipping');
    Route::get('/shipping/options/{postalCode}', [ShippingController::class, 'getShippingOptions'])->name('site.cart.getShippingOptions');
    
    // WhatsApp checkout route
    Route::post('/checkout/whatsapp/register', [WhatsAppCheckoutController::class, 'registerOrder'])->name('site.checkout.whatsapp.register');
});
