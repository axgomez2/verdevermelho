<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\CatStyleShop;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Verifica se a coluna uuid existe na tabela users
        // Se existir na estrutura do modelo mas não na tabela, remove a coluna do fillable
        try {
            if (DB::getSchemaBuilder()->hasTable('users')) {
                $columns = DB::getSchemaBuilder()->getColumnListing('users');
                
                // Se a coluna uuid não existir na tabela
                if (!in_array('uuid', $columns)) {
                    // Modifica o modelo User para não tentar usar uuid
                    User::creating(function ($user) {
                        unset($user->uuid);
                    });
                }
            }
        } catch (\Exception $e) {
            // Ignora erros e continua a inicialização
        }
        
        // Compartilha categorias, carrinho e contagem do carrinho para a view do navbar
        View::composer('components.site.nav4', function ($view) {
            $categories = CatStyleShop::orderBy('nome')->get();

            $cart = null;
            $cartCount = 0;
            $wishlistCount = 0;

            if (auth()->check()) {
                // Busca ou cria o carrinho para o usuário autenticado e carrega os itens com produto
                $cart = Cart::firstOrNew(['user_id' => auth()->id()]);
                $cart->load('items.product');
                $cartCount = $cart->items->sum('quantity');

                // Conta os itens nos favoritos
                $wishlistCount = Wishlist::where('user_id', auth()->id())->count();
            }

            $view->with([
                'categories' => $categories,
                'cart' => $cart,
                'cartCount' => $cartCount,
                'wishlistCount' => $wishlistCount,
            ]);
        });
    }
}

