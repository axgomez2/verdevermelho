<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\VinylMaster;
use App\Models\Playlist;
use App\Models\CatStyleShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $latestVinyls = VinylMaster::with(['artists', 'vinylSec', 'catStyleShops'])
            ->latest()
            ->take(value: 20)
            ->get();

        $slideVinyls = VinylMaster::with(['artists', 'vinylSec', 'catStyleShops'])
        ->latest()
        ->take(value: 10)
        ->get();

        // Obter as 3 playlists mais recentes que estão ativas
        $featuredPlaylists = Playlist::with(['tracks.vinylMaster', 'tracks.trackable'])
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();
            
        // Buscar categorias que tenham discos (independente de disponibilidade)
        $categoriesWithVinyls = CatStyleShop::select('cat_style_shop.id', 'cat_style_shop.nome', 'cat_style_shop.slug')
            ->join('cat_style_shop_vinyl_master', 'cat_style_shop.id', '=', 'cat_style_shop_vinyl_master.cat_style_shop_id')
            ->join('vinyl_masters', 'cat_style_shop_vinyl_master.vinyl_master_id', '=', 'vinyl_masters.id')
            ->join('vinyl_secs', 'vinyl_masters.id', '=', 'vinyl_secs.vinyl_master_id')
            ->groupBy('cat_style_shop.id', 'cat_style_shop.nome', 'cat_style_shop.slug')
            ->having(DB::raw('COUNT(DISTINCT vinyl_masters.id)'), '>=', 2) // Reduzido de 5 para 2
            ->orderBy('cat_style_shop.nome')
            ->limit(15) // Aumentado para exibir até 15 categorias
            ->get();
        
        // Armazenar os discos aleatórios para cada categoria
        $categoriesWithRandomVinyls = [];
        
        foreach ($categoriesWithVinyls as $category) {
            $randomVinyls = VinylMaster::with(['artists', 'vinylSec', 'catStyleShops'])
                ->whereHas('vinylSec', function ($query) {
                    $query->where('in_stock', true);
                })
                ->whereHas('catStyleShops', function ($query) use ($category) {
                    $query->where('cat_style_shop_id', $category->id);
                })
                ->inRandomOrder()
                ->take(4)
                ->get();
                
            if ($randomVinyls->count() > 0) {
                $categoriesWithRandomVinyls[] = [
                    'category' => $category,
                    'vinyls' => $randomVinyls
                ];
            }
        }
            
        return view('site.index', compact(
            'latestVinyls', 
            'slideVinyls', 
            'featuredPlaylists',
            'categoriesWithRandomVinyls'
        ));
    }

    public function about()
    {
        return view('site.about');
    }
}
