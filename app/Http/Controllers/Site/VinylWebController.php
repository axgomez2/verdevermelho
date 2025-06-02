<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\CatStyleShop;
use App\Models\VinylMaster;
use App\Models\Style;
use App\Models\VinylSec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VinylWebController extends Controller
{
    public function index(Request $request)
    {
        $query = VinylMaster::with(['artists', 'recordLabel', 'vinylSec', 'styles', 'product', 'tracks', 'catStyleShops']);

        // Apply search independently
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('artists', function ($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        // Apply other filters
        $query = $this->applyFilters($query, $request);

        // Apply sorting
        $query = $this->applySorting($query, $request);

        $vinyls = $query->paginate(20)->appends($request->all());

        // Transform the vinyls to include track information
        $vinyls->getCollection()->transform(function ($vinyl) {
            $vinyl->tracks->transform(function ($track) use ($vinyl) {
                $track->artist = $vinyl->artists->pluck('name')->implode(', ');
                $track->cover_url = $vinyl->cover_image_url;
                return $track;
            });
            return $vinyl;
        });

        // Obter categorias de música para filtro
        $categories = CatStyleShop::orderBy('nome')->get();
        
        // Obter gravadoras para filtro
        $recordLabels = DB::table('record_labels')
            ->whereIn('id', function($query) {
                $query->select('record_label_id')
                    ->from('vinyl_masters')
                    ->whereNotNull('record_label_id')
                    ->distinct();
            })
            ->orderBy('name')
            ->get();

        // Obter anos disponíveis para filtro
        $releaseYears = VinylMaster::select('release_year')
            ->whereNotNull('release_year')
            ->distinct()
            ->orderBy('release_year', 'desc')
            ->pluck('release_year');

        // Obter estilos para filtro
        $styles = Style::all();

        // Get min and max prices
        $priceRange = VinylSec::selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

        return view('site.vinyls.index', compact(
            'vinyls', 
            'styles', 
            'priceRange', 
            'categories', 
            'recordLabels', 
            'releaseYears'
        ));
    }

    private function applyFilters($query, $request)
    {
        // Filtro de disponibilidade (opcional)
        if ($request->filled('available') && $request->available == 1) {
            $query->whereHas('vinylSec', function ($q) {
                $q->where('in_stock', 1)->where('quantity', '>', 0);
            });
        }

        // Filtro de estilo (style)
        if ($request->filled('style')) {
            $query->whereHas('styles', function ($q) use ($request) {
                $q->where('id', $request->style);
            });
        }

        // Filtro de categoria (catstyleshop)
        if ($request->filled('category')) {
            $query->whereHas('catStyleShops', function ($q) use ($request) {
                $q->where('cat_style_shop_id', $request->category);
            });
        }

        // Filtro de gravadora (record_label)
        if ($request->filled('record_label')) {
            $query->where('record_label_id', $request->record_label);
        }

        // Filtro de ano de lançamento
        if ($request->filled('release_year')) {
            $query->where('release_year', $request->release_year);
        }

        // Filtro de preço
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereHas('vinylSec', function ($q) use ($request) {
                $q->whereBetween('price', [$request->min_price, $request->max_price]);
            });
        }

        return $query;
    }

    private function applySorting($query, $request)
    {
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        switch ($sortBy) {
            case 'price':
                $query->join('vinyl_secs', 'vinyl_masters.id', '=', 'vinyl_secs.vinyl_master_id')
                      ->orderBy('vinyl_secs.price', $sortOrder);
                break;
            case 'release_year':
                $query->orderBy('release_year', $sortOrder);
                break;
            case 'title':
                $query->orderBy('title', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
                break;
        }

        return $query;
    }

    public function byCategory(Request $request, $slug)
{
    // Busca a categoria pelo slug
    $category = CatStyleShop::where('slug', $slug)->firstOrFail();

    // Query base
    $query = VinylMaster::whereHas('catStyleShops', function($q) use ($slug) {
        $q->where('slug', $slug);
    })->with(['vinylSec', 'artists', 'recordLabel', 'catStyleShops']);

        // Filtro de disponibilidade (opcional, controlado pelo frontend)
        if ($request->filled('available') && $request->available == 1) {
            $query->whereHas('vinylSec', function ($q) {
                $q->where('in_stock', 1)->where('quantity', '>', 0);
            });
        }

        // Aplicar ordenação
        switch ($request->get('sort')) {
        case 'price_asc':
            $query->join('vinyl_secs', 'vinyl_masters.id', '=', 'vinyl_secs.vinyl_master_id')
                  ->orderBy('vinyl_secs.price', 'asc')
                  ->select('vinyl_masters.*');
            break;
        case 'price_desc':
            $query->join('vinyl_secs', 'vinyl_masters.id', '=', 'vinyl_secs.vinyl_master_id')
                  ->orderBy('vinyl_secs.price', 'desc')
                  ->select('vinyl_masters.*');
            break;
        case 'artist_asc':
            $query->join('artist_vinyl_master', 'vinyl_masters.id', '=', 'artist_vinyl_master.vinyl_master_id')
                  ->join('artists', 'artists.id', '=', 'artist_vinyl_master.artist_id')
                  ->orderBy('artists.name', 'asc')
                  ->select('vinyl_masters.*')
                  ->distinct();
            break;
        case 'artist_desc':
            $query->join('artist_vinyl_master', 'vinyl_masters.id', '=', 'artist_vinyl_master.vinyl_master_id')
                  ->join('artists', 'artists.id', '=', 'artist_vinyl_master.artist_id')
                  ->orderBy('artists.name', 'desc')
                  ->select('vinyl_masters.*')
                  ->distinct();
            break;
        case 'name_asc':
            $query->orderBy('title', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('title', 'desc');
            break;
        default:
            $query->latest();
            break;
    }

    // Executar a query com paginação
    $vinyls = $query->paginate(20)->withQueryString();

    return view('site.vinyls.category', compact('vinyls', 'category'));
}


}
