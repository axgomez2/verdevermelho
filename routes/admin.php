<?php

use App\Http\Controllers\Admin\PlaylistController;
use App\Http\Controllers\Admin\VinylController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Playlists
    Route::resource('playlists', PlaylistController::class);
    Route::post('playlists/{playlist}/reorder', [PlaylistController::class, 'reorderTracks'])->name('playlists.reorder');

    // Vinyl Search API
    Route::get('/admin/playlists/search-vinyls', [PlaylistController::class, 'searchVinyls'])->name('admin.playlists.search-vinyls');
});
