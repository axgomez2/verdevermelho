<?php

use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Pdv\PainelController;
use App\Http\Controllers\Site\AboutController;
use App\Http\Controllers\Site\PlaylistController;
use App\Http\Controllers\Site\RecommendationController;
use App\Http\Controllers\Site\WantlistController;
use App\Http\Controllers\Site\AddressController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\SearchController;
use App\Http\Controllers\Site\VinylWebController;
use App\Http\Controllers\Site\VinylDetailsController;
use App\Http\Controllers\Site\WishlistController;
use App\Http\Controllers\Site\ChartDjsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Site\NavbarController;
use App\Http\Controllers\Auth\GoogleController;
use Laravel\Socialite\Facades\Socialite;

// Rotas para o Navbar


// Rotas para Wishlist
Route::middleware('auth')->group(function () {
    Route::post('/wishlist/toggle-favorite', [WishlistController::class, 'toggleFavorite'])->name('wishlist.toggle-favorite');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
});

Route::get('/discos/promocoes', [VinylWebController::class, 'promotions'])->name('site.vinyls.promotions');

require __DIR__.'/admin.php';
require __DIR__.'/notifications.php';

Route::middleware(['auth', 'verified', 'rolemanager:resale'])->group(function () {
    Route::prefix('pdv')->group(function () {
        Route::get('/', [PainelController::class, 'index'])->name('pdv.dashboard');
    });
});

// Site Routes
Route::get('/', [HomeController::class, 'index'])->name('site.home');
Route::get('/discos', [VinylWebController::class, 'index'])->name('site.vinyls.index');
Route::get('/busca', [SearchController::class, 'index'])->name('site.search');
Route::get('/djcharts', [ChartDjsController::class, 'index'])->name('site.djcharts.index');
Route::get('/djcharts/{dj:slug}', [ChartDjsController::class, 'show'])->name('site.djcharts.show');
Route::get('/equipamentos', [EquipmentController::class, 'index'])->name('site.equipments.index');
Route::get('/equipamentos/{slug}', [EquipmentController::class, 'show'])->name('site.equipments.show');
Route::get('/sobre-a-loja', [AboutController::class, 'index'])->name('site.about');

// Newsletter
Route::post('/newsletter/cadastro', [\App\Http\Controllers\Site\NewsletterController::class, 'store'])->name('site.newsletter.store');

Route::get('/discos/categoria/{slug}', [VinylWebController::class, 'byCategory'])->name('vinyls.byCategory');

// Playlist Routes
Route::get('/playlists', [PlaylistController::class, 'index'])->name('site.playlists.index');
Route::get('/playlists/{slug}', [PlaylistController::class, 'show'])->name('site.playlists.show');
Route::get('/playlists/{slug}/tracks', [PlaylistController::class, 'getPlaylistTracks'])->name('site.playlists.tracks');

// Rota genérica para detalhes de vinil - deve vir por último para não conflitar com outras rotas
Route::get('/{artistSlug}/{titleSlug}', [VinylDetailsController::class, 'show'])->name('site.vinyl.show');

Route::post('/address/store', [AddressController::class, 'store'])->name('address.store');

// Rotas de autenticação com Google
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Rota temporária para teste do Google Login
Route::get('/test-google', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/test-google/callback', function () {
    try {
        $user = Socialite::driver('google')->user();
        dd($user); // Isso mostrará os dados do usuário
    } catch (Exception $e) {
        dd($e->getMessage()); // Isso mostrará qualquer erro que ocorra
    }
});

Route::get('/privacy-policy', function () {
    return view('site.privacy-policy');
})->name('privacy.policy');

Route::get('/terms-of-service', function () {
    return view('site.terms-of-service');
})->name('terms.service');

require __DIR__.'/auth.php';
require __DIR__.'/users.php';
require __DIR__.'/cart.php';
require __DIR__.'/checkout.php';

