<?php

use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\PlaylistController as AdminPlaylistController;
use App\Http\Controllers\Pdv\PainelController;
use App\Http\Controllers\Site\AboutController;
use App\Http\Controllers\Site\PlaylistController;
use App\Http\Controllers\Site\DjChartController;
use App\Http\Controllers\Site\RecommendationController;
use App\Http\Controllers\Site\WantlistController;
use App\Http\Controllers\YouTubeController;
use Illuminate\Support\Facades\Route;

// site use
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\SearchController;
use App\Http\Controllers\Site\VinylWebController;
use App\Http\Controllers\Site\VinylDetailsController;
use App\Http\Controllers\Site\WishlistController;
use App\Http\Controllers\Site\CartController;
use App\Http\Controllers\Site\CartItemController;
use App\Http\Controllers\Site\CheckoutController;
use App\Http\Controllers\Site\ChartDjsController;

// admin  use
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VinylController;
use App\Http\Controllers\Admin\VinylImageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CatStyleShopController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DJController;
use App\Http\Controllers\Admin\TrackController;

// open use
use App\Http\Controllers\ProfileController;

// Rotas para o Navbar
Route::prefix('navbar')->group(function () {
    Route::get('/data', [NavbarController::class, 'getNavbarData']);
    Route::get('/cart-preview', [NavbarController::class, 'getCartPreview']);
});

Route::middleware(['auth', 'verified', 'rolemanager:user'])->group(function () {
    Route::get('/favoritos', [WishlistController::class, 'index'])->name('site.wishlist.index');
    Route::post('/favoritos/toggle', [WishlistController::class, 'toggle'])->name('site.wishlist.toggle');



    Route::prefix('carrinho')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('site.cart.index');
        Route::post('/add', [CartController::class, 'addToCart'])->name('site.cart.add');
        Route::post('/update/{itemId}', [CartController::class, 'updateQuantity'])->name('site.cart.update');
        Route::delete('/remove/{itemId}', [CartController::class, 'removeItem'])->name('site.cart.remove');
    });



    // Rotas dos itens do carrinho
    Route::post('/carrinho/items', [CartItemController::class, 'store'])->name('site.cart.items.store');
    Route::put('/carrinho/items/{cartItem}', [CartItemController::class, 'update'])->name('site.cart.items.update');
    Route::delete('/cart/items/{cartItem}', [CartItemController::class, 'destroy'])->name('site.cart.items.destroy');
    Route::post('/cart/update-postal-code', [\App\Http\Controllers\Site\CartController::class, 'updatePostalCode'])
    ->name('site.cart.updatePostalCode');

    //  Rotas de wantlist
    Route::get('/wantlist', [WantlistController::class, 'index'])->name('site.wantlist.index');
    Route::post('/wantlist/toggle', [WantlistController::class, 'toggle'])->name('site.wantlist.toggle');
    //rotas de checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('site.checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('site.checkout.process');
});

// admin
Route::middleware(['auth', 'verified', 'rolemanager:admin'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Playlist Management
        Route::resource('playlists', AdminPlaylistController::class)->names([
            'index' => 'admin.playlists.index',
            'create' => 'admin.playlists.create',
            'store' => 'admin.playlists.store',
            'edit' => 'admin.playlists.edit',
            'update' => 'admin.playlists.update',
            'destroy' => 'admin.playlists.destroy',
        ]);
        Route::post('playlists/{playlist}/reorder', [AdminPlaylistController::class, 'reorderTracks'])
            ->name('admin.playlists.reorder');
            Route::get('vinyls/search', [\App\Http\Controllers\Admin\PlaylistController::class, 'searchVinyls'])
    ->name('admin.vinyls.search');

        // vinyls route
        Route::get('/discos', [VinylController::class, 'index'])->name('admin.vinyls.index');
        Route::get('/discos/search', [VinylController::class, 'search'])->name('admin.vinyls.search');
        Route::get('/disco/{id}/completar', [VinylController::class, 'complete'])->name('admin.vinyls.complete');
        Route::post('/disco/{id}/completar', [VinylController::class, 'storeComplete'])->name('admin.vinyl.storeComplete');
        Route::delete('/disco/{id}', [VinylController::class, 'destroy'])->name('admin.vinyls.destroy');

        Route::get('/disco/{id}/images', [VinylImageController::class, 'index'])->name('admin.vinyl.images');
        Route::post('/disco/{id}/images', [VinylImageController::class, 'store'])->name('admin.vinyl.images.store');
        Route::delete('/disco/{id}/images/{imageId}', [VinylImageController::class, 'destroy'])->name('admin.vinyl.images.destroy');
        Route::post('/vinyls/update-field', [VinylController::class, 'updateField'])->name('admin.vinyls.updateField');

        Route::post('/vinyls/{id}/fetch-discogs-image', [VinylController::class, 'fetchDiscogsImage'])->name('admin.vinyls.fetch-discogs-image');
        Route::post('/vinyls/{id}/upload-image', [VinylController::class, 'uploadImage'])->name('admin.vinyls.upload-image');
        Route::delete('/vinyls/{id}/remove-image', [VinylController::class, 'removeImage'])->name('admin.vinyls.remove-image');

        //faixas
        Route::get('/disco/{id}/edit-tracks', [TrackController::class, 'editTracks'])->name('admin.vinyls.edit-tracks');
        Route::put('/disco/{id}/update-tracks', [TrackController::class, 'updateTracks'])->name('admin.vinyls.update-tracks');
        Route::post('/youtube/search', [YouTubeController::class, 'search'])->name('youtube.search');

        // equipments route
        Route::get('/equipamentos', [EquipmentController::class, 'index'])->name('admin.equipments.index');
        Route::get('/equipamentos/adicionar', [EquipmentController::class, 'create'])->name('admin.equipments.create');
        Route::post('/equipamentos', [EquipmentController::class, 'store'])->name('admin.equipments.store');
        Route::get('/equipamentos/{id}/edit', [EquipmentController::class, 'edit'])->name('admin.equipments.edit');
        Route::put('/equipamentos/{id}', [EquipmentController::class, 'update'])->name('admin.equipments.update');
        Route::delete('/equipamentos/{id}', [EquipmentController::class, 'destroy'])->name('admin.equipments.destroy');

        Route::get('/discos/adicionar', [VinylController::class, 'create'])->name('admin.vinyls.create');
        Route::post('/discos/salvar', [VinylController::class, 'store'])->name('admin.vinyls.store');
        Route::get('/disco/{id}', [VinylController::class, 'show'])->name('admin.vinyls.show');
        Route::get('/disco/{id}/edit', [VinylController::class, 'edit'])->name('admin.vinyls.edit');
        Route::put('/disco/{id}', [VinylController::class, 'update'])->name('admin.vinyls.update');

        // settings routes
        Route::get('/configuracoes', [SettingsController::class, 'index'])->name('admin.settings.index');

        // Categorias internas
        Route::get('categorias-estilo', [CatStyleShopController::class, 'index'])->name('admin.cat-style-shop.index');
        Route::get('categorias-estilo/criar', [CatStyleShopController::class, 'create'])->name('admin.cat-style-shop.create');
        Route::post('categorias-estilo', [CatStyleShopController::class, 'store'])->name('admin.cat-style-shop.store');
        Route::get('categorias-estilo/{catStyleShop}/editar', [CatStyleShopController::class, 'edit'])->name('admin.cat-style-shop.edit');
        Route::put('categorias-estilo/{catStyleShop}', [CatStyleShopController::class, 'update'])->name('admin.cat-style-shop.update');
        Route::delete('categorias-estilo/{catStyleShop}', [CatStyleShopController::class, 'destroy'])->name('admin.cat-style-shop.destroy');

        // djs
        Route::get('djs', [DJController::class, 'index'])->name('admin.djs.index');
        Route::get('djs/adicionar', [DJController::class, 'create'])->name('admin.djs.create');
        Route::post('djs', [DJController::class, 'store'])->name('admin.djs.store');
        Route::get('djs/{dj}', [DJController::class, 'show'])->name('admin.djs.show');
        Route::get('djs/{dj}/edit', [DJController::class, 'edit'])->name('admin.djs.edit');
        Route::put('djs/{dj}', [DJController::class, 'update'])->name('admin.djs.update');
        Route::delete('djs/{dj}', [DJController::class, 'destroy'])->name('admin.djs.destroy');
        Route::post('djs/{dj}/recommendations', [DJController::class, 'updateRecommendations'])->name('admin.djs.update-recommendations');
        Route::put('djs/{dj}/toggle-active', [DJController::class, 'toggleActive'])->name('admin.djs.toggle-active');
        Route::get('djs/{dj}/manage-vinyls', [DJController::class, 'manageVinyls'])->name('admin.djs.manage-vinyls');
        Route::post('djs/{dj}/update-recommendations', [DJController::class, 'updateRecommendations'])->name('admin.djs.update-recommendations');
        Route::get('djs/{dj}/search-vinyls', [DJController::class, 'searchVinyls'])->name('admin.djs.search-vinyls');

        // Weight routes
        Route::post('settings/weights', [SettingsController::class, 'storeWeight'])->name('admin.settings.storeWeight');
        Route::put('settings/weights/{weight}', [SettingsController::class, 'updateWeight'])->name('admin.settings.updateWeight');
        Route::delete('settings/weights/{weight}', [SettingsController::class, 'deleteWeight'])->name('admin.settings.deleteWeight');

        // Dimension routes
        Route::post('/settings/dimensions', [SettingsController::class, 'storeDimension'])->name('admin.settings.storeDimension');
        Route::put('/settings/dimensions/{dimension}', [SettingsController::class, 'updateDimension'])->name('admin.settings.updateDimension');
        Route::delete('/settings/dimensions/{dimension}', [SettingsController::class, 'deleteDimension'])->name('admin.settings.deleteDimension');

        // Brand routes
        Route::post('/settings/brands', [SettingsController::class, 'storeBrand'])->name('admin.settings.storeBrand');
        Route::put('/settings/brands/{brand}', [SettingsController::class, 'updateBrand'])->name('admin.settings.updateBrand');
        Route::delete('/settings/brands/{brand}', [SettingsController::class, 'deleteBrand'])->name('admin.settings.deleteBrand');

        // Equipment Category routes
        Route::post('/settings/equipment-categories', [SettingsController::class, 'storeEquipmentCategory'])->name('admin.settings.storeEquipmentCategory');
        Route::put('/settings/equipment-categories/{equipmentCategory}', [SettingsController::class, 'updateEquipmentCategory'])->name('admin.settings.updateEquipmentCategory');
        Route::delete('/settings/equipment-categories/{equipmentCategory}', [SettingsController::class, 'deleteEquipmentCategory'])->name('admin.settings.deleteEquipmentCategory');
    });

    // Customer routes
    Route::get('/clientes', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/cliente/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/cliente/{customer}/editar', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/cliente/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/meu-perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/meu-perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/meu-perfil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/address', [ProfileController::class, 'storeAddress'])->name('address.store');
});

Route::middleware(['auth', 'verified', 'rolemanager:resale'])->group(function () {
    Route::prefix('pdv')->group(function () {
        Route::get('/', [PainelController::class, 'index'])->name('pdv.dashboard');
    });
});

// Site Routes
Route::get('/', [HomeController::class, 'index'])->name('site.home');
Route::get('/discos', [VinylWebController::class, 'index'])->name('site.vinyls.index');
Route::get('/{artistSlug}/{titleSlug}', [VinylDetailsController::class, 'show'])->name('site.vinyl.show');
Route::get('/busca', [SearchController::class, 'index'])->name('site.search');
Route::get('/djcharts', [ChartDjsController::class, 'index'])->name('site.djcharts.index');
Route::get('/djcharts/{dj:slug}', [ChartDjsController::class, 'show'])->name('site.djcharts.show');
Route::get('/equipamentos', [EquipmentController::class, 'index'])->name('site.equipments.index');
Route::get('/equipamentos/{slug}', [EquipmentController::class, 'show'])->name('site.equipments.show');
Route::get('/sobre-a-loja', [AboutController::class, 'index'])->name('site.about');



Route::get('/discos/categoria/{slug}', [VinylWebController::class, 'byCategory'])->name('vinyls.byCategory');


// Playlist Routes
Route::get('/playlists', [PlaylistController::class, 'index'])->name('site.playlists.index');
Route::get('/playlists/{slug}', [PlaylistController::class, 'show'])->name('site.playlists.show');
Route::get('/playlists/{slug}/tracks', [PlaylistController::class, 'getPlaylistTracks'])->name('site.playlists.tracks');

Route::post('/address/store', [AddressController::class, 'store'])->name('address.store');

require __DIR__.'/auth.php';
