<?php

use App\Http\Controllers\Site\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/notificacoes', [NotificationController::class, 'index'])->name('site.notifications.index');
    Route::get('/notificacoes/{id}/marcar-como-lida', [NotificationController::class, 'markAsRead'])->name('site.notifications.mark-as-read');
    Route::get('/notificacoes/marcar-todas-como-lidas', [NotificationController::class, 'markAllAsRead'])->name('site.notifications.mark-all-read');
    Route::get('/notificacoes/verificar', [NotificationController::class, 'check'])->name('site.notifications.check');
});
