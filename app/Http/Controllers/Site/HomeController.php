<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Deejay;
use App\Models\VinylMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
{
    // Contagem de VinylMasters
    $totalVinyls = VinylMaster::count();

    // Contagem de VinylMasters com produto associado
    $vinylsWithProduct = VinylMaster::whereHas('product')->count();

    // Amostra de VinylMasters para debug
    $sampleVinyls = VinylMaster::take(5)->get();

    // Produtos relacionados aos VinylMasters de amostra
    $relatedProducts = Product::whereIn('productable_id', $sampleVinyls->pluck('id'))
        ->where('productable_type', VinylMaster::class)
        ->get();

    // Vinis mais recentes para exibição
    $latestVinyls = VinylMaster::with(['artists', 'recordLabel', 'vinylSec', 'product'])
        ->whereHas('vinylSec')
        ->whereHas('product')
        ->latest()
        ->take(30)
        ->get();

    // DJs ativos
    $featuredDjs = Deejay::where('is_active', true)->withCount('recommendations')->take(3)->get();

    // Passamos todas essas informações para a view
    return view('site.index', compact(
        'latestVinyls',
        'totalVinyls',
        'vinylsWithProduct',
        'sampleVinyls',
        'relatedProducts',
        'featuredDjs'
    ));
}
}
