<?php

use App\Http\Controllers\Admin\CatStyleShopController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlaylistController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TrackController;
use App\Http\Controllers\Admin\VinylController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\VinylImageController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\YouTubeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'rolemanager:admin'])->group(function () {
    //dashboard
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Vinyls
    Route::get('/admin/discos', [VinylController::class, 'index'])->name('admin.vinyls.index');
    Route::get('/admin/discos/adicionar', [VinylController::class, 'create'])->name('admin.vinyls.create');
    Route::post('/admin/discos/salvar', [VinylController::class, 'store'])->name('admin.vinyls.store');
    Route::get('/admin/disco/{id}', [VinylController::class, 'show'])->name('admin.vinyls.show');
    Route::get('/admin/disco/{id}/edit', [VinylController::class, 'edit'])->name('admin.vinyls.edit');
    Route::put('/admin/disco/{id}', [VinylController::class, 'update'])->name('admin.vinyls.update');
    Route::delete('/admin/disco/{id}', [VinylController::class, 'destroy'])->name('admin.vinyls.destroy');

    Route::get('/admin/disco/{id}/completar', [VinylController::class, 'complete'])->name('admin.vinyls.complete');
    Route::post('/admin/disco/{id}/completar', [VinylController::class, 'storeComplete'])->name('admin.vinyl.storeComplete');

    Route::get('/admin/disco/{id}/images', [VinylImageController::class, 'index'])->name('admin.vinyl.images');
    Route::post('/admin/disco/{id}/images', [VinylImageController::class, 'store'])->name('admin.vinyl.images.store');
    Route::delete('/admin/disco/{id}/images/{imageId}', [VinylImageController::class, 'destroy'])->name('admin.vinyl.images.destroy');
    Route::post('/admin/disco/update-field', [VinylController::class, 'updateField'])->name('admin.vinyls.updateField');

    Route::post('/admin/disco/{id}/fetch-discogs-image', [VinylController::class, 'fetchDiscogsImage'])->name('admin.vinyls.fetch-discogs-image');
    Route::post('/admin/disco/{id}/upload-image', [VinylController::class, 'uploadImage'])->name('admin.vinyls.upload-image');
    Route::delete('/admin/disco/{id}/remove-image', [VinylController::class, 'removeImage'])->name('admin.vinyls.remove-image');

    //faixas
    Route::get('/admin/disco/{id}/edit-tracks', [TrackController::class, 'editTracks'])->name('admin.vinyls.edit-tracks');
    Route::put('/admin/disco/{id}/update-tracks', [TrackController::class, 'updateTracks'])->name('admin.vinyls.update-tracks');
    Route::post('/admin/youtube/search', [YouTubeController::class, 'search'])->name('youtube.search');

    // equipments route
    Route::get('/admin/equipamentos', [EquipmentController::class, 'index'])->name('admin.equipments.index');
    Route::get('/admin/equipamentos/adicionar', [EquipmentController::class, 'create'])->name('admin.equipments.create');
    Route::post('/admin/equipamentos', [EquipmentController::class, 'store'])->name('admin.equipments.store');
    Route::get('/admin/equipamentos/{id}/edit', [EquipmentController::class, 'edit'])->name('admin.equipments.edit');
    Route::put('/admin/equipamentos/{id}', [EquipmentController::class, 'update'])->name('admin.equipments.update');
    Route::delete('/admin/equipamentos/{id}', [EquipmentController::class, 'destroy'])->name('admin.equipments.destroy');
    Route::delete('/admin/equipamentos/media/{mediaId}', [EquipmentController::class, 'deleteMedia'])->name('admin.equipments.deleteMedia');
    Route::post('/admin/equipamentos/gerar-descricao', [EquipmentController::class, 'generateDescription'])->name('admin.equipments.generateDescription');

    // Playlist Management
    Route::prefix('admin')->group(function () {
        // Rota de busca de discos (precisa vir antes das rotas com parâmetros)
        Route::get('playlists/search-tracks', [\App\Http\Controllers\Admin\PlaylistController::class, 'searchVinyls'])
            ->name('admin.playlists.search-tracks');
            
        // Rota para edição e atualização de faixas
        Route::get('playlists/{playlist}/edit-tracks', [\App\Http\Controllers\Admin\PlaylistController::class, 'editTracks'])
            ->name('admin.playlists.edit_tracks');
        Route::put('playlists/{playlist}/update-tracks', [\App\Http\Controllers\Admin\PlaylistController::class, 'updateTracks'])
            ->name('admin.playlists.update_tracks');

        // Rotas de CRUD padrão
        Route::resource('playlists', \App\Http\Controllers\Admin\PlaylistController::class)->names([
            'index' => 'admin.playlists.index',
            'create' => 'admin.playlists.create',
            'store' => 'admin.playlists.store',
            'edit' => 'admin.playlists.edit',
            'update' => 'admin.playlists.update',
            'destroy' => 'admin.playlists.destroy',
        ]);
    });

    // cotas de configurações
    Route::prefix('admin')->group(function () {
        Route::get('/configuracoes', [SettingsController::class, 'index'])->name('admin.settings.index');
        // Categorias internas
        Route::get('categorias-estilo', [CatStyleShopController::class, 'index'])->name('admin.cat-style-shop.index');
        Route::get('categorias-estilo/criar', [CatStyleShopController::class, 'create'])->name('admin.cat-style-shop.create');
        Route::post('categorias-estilo', [CatStyleShopController::class, 'store'])->name('admin.cat-style-shop.store');
        Route::get('categorias-estilo/{catStyleShop}/editar', [CatStyleShopController::class, 'edit'])->name('admin.cat-style-shop.edit');
        Route::put('categorias-estilo/{catStyleShop}', [CatStyleShopController::class, 'update'])->name('admin.cat-style-shop.update');
        Route::delete('categorias-estilo/{catStyleShop}', [CatStyleShopController::class, 'destroy'])->name('admin.cat-style-shop.destroy');

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

        // Customer routes
        Route::get('/clientes', [CustomerController::class, 'index'])->name('admin.customers.index');
        Route::get('/cliente/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
        Route::get('/cliente/{customer}/editar', [CustomerController::class, 'edit'])->name('admin.customers.edit');
        Route::put('/cliente/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');

         // Shipping Management
        Route::get('/shipping', [ShippingController::class, 'index'])->name('admin.shipping.index');
        Route::post('/shipping/{order}/generate-label', [ShippingController::class, 'generateLabel'])->name('admin.shipping.generate-label');
        Route::get('/shipping/{order}/print-label', [ShippingController::class, 'printLabel'])->name('admin.shipping.print-label');
        Route::get('/shipping/{order}/track', [ShippingController::class, 'trackShipment'])->name('admin.shipping.track');
        
        // Relatórios
        Route::get('/relatorios', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/relatorios/discos-mais-vistos', [ReportController::class, 'mostViewed'])->name('admin.reports.most-viewed');
        Route::get('/relatorios/disco/{id}', [ReportController::class, 'vinylDetails'])->name('admin.reports.vinyl-details');
        
        // PDV - Ponto de Venda
        Route::get('/pdv', [PosController::class, 'index'])->name('admin.pos.index');
        Route::get('/pdv/vendas', [PosController::class, 'sales'])->name('admin.pos.sales');
        Route::get('/pdv/venda/{id}', [PosController::class, 'show'])->name('admin.pos.show');
        Route::post('/pdv/venda', [PosController::class, 'store'])->name('admin.pos.store');
        Route::post('/pdv/buscar-produtos', [PosController::class, 'search'])->name('admin.pos.search');
        Route::post('/pdv/buscar-clientes', [PosController::class, 'searchCustomers'])->name('admin.pos.search-customers');
    });
});
