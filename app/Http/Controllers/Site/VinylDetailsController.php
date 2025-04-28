<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\VinylMaster;
use App\Models\VinylView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VinylDetailsController extends Controller
{
    public function show($artistSlug, $titleSlug)
    {
        $vinyl = VinylMaster::whereHas('artists', function ($query) use ($artistSlug) {
                $query->where('slug', $artistSlug);
            })
            ->where('slug', $titleSlug)
            ->with(['artists', 'recordLabel', 'vinylSec', 'genres', 'tracks'])
            ->firstOrFail();

        $vinyl->tracks->transform(function ($track) use ($vinyl) {
            $track->artist = $vinyl->artists->pluck('name')->implode(', ');
            $track->cover_url = $vinyl->cover_image_url;
            return $track;
        });
        
        // Buscar discos relacionados (por gênero ou artista)
        $relatedVinyls = collect([]);
        
        if ($vinyl->genres && $vinyl->genres->count() > 0) {
            // Pegamos os IDs dos gêneros deste disco
            $genreIds = $vinyl->genres->pluck('id')->toArray();
            
            // Buscamos outros discos com os mesmos gêneros
            $byGenre = VinylMaster::whereHas('genres', function($query) use ($genreIds) {
                    $query->whereIn('genres.id', $genreIds);
                })
                ->where('id', '!=', $vinyl->id) // Excluir o disco atual
                ->with(['artists', 'vinylSec'])
                ->take(4) // Limitamos a 4 discos relacionados
                ->get();
                
            $relatedVinyls = $relatedVinyls->merge($byGenre);
        }
        
        // Se ainda não tivermos 4 discos relacionados, buscamos por artista
        if ($relatedVinyls->count() < 4 && $vinyl->artists && $vinyl->artists->count() > 0) {
            $artistIds = $vinyl->artists->pluck('id')->toArray();
            
            $neededCount = 4 - $relatedVinyls->count();
            
            $byArtist = VinylMaster::whereHas('artists', function($query) use ($artistIds) {
                    $query->whereIn('artists.id', $artistIds);
                })
                ->where('id', '!=', $vinyl->id) // Excluir o disco atual
                ->whereNotIn('id', $relatedVinyls->pluck('id')->toArray()) // Excluir os já adicionados
                ->with(['artists', 'vinylSec'])
                ->take($neededCount)
                ->get();
                
            $relatedVinyls = $relatedVinyls->merge($byArtist);
        }
        
        // Garantir que temos no máximo 4 discos
        $relatedVinyls = $relatedVinyls->take(4);
        
        // Registrar a visualização do disco
        VinylView::recordView(
            $vinyl,
            Auth::user(),
            request()->ip()
        );

        return view('site.vinyls.details', compact('vinyl', 'relatedVinyls'));
    }
}


