<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Models\VinylMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlaylistController extends Controller
{
    public function index()
    {
        $playlists = Playlist::with('tracks.vinylMaster')
            ->latest()
            ->paginate(12);
        return view('admin.playlists.index', compact('playlists'));
    }

    public function create()
    {
        return view('admin.playlists.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:playlists',
            'bio' => 'nullable|string',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'soundcloud_url' => 'nullable|url|max:255',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'vinyls' => 'array|max:10',
            'vinyls.*' => 'required|exists:vinyl_masters,id'
        ], [
            'name.required' => 'O nome da playlist é obrigatório',
            'name.max' => 'O nome não pode ter mais que 255 caracteres',
            'slug.unique' => 'Este slug já está em uso',
            'image.image' => 'O arquivo deve ser uma imagem',
            'image.max' => 'A imagem não pode ter mais que 2MB',
            'vinyls.max' => 'Uma playlist não pode ter mais que 10 discos'
        ]);

        if ($request->hasFile('image')) {
            // Processar a imagem para garantir que seja quadrada
            $image = \Intervention\Image\ImageManager::gd()->read($request->file('image'));
            
            // Determinar o tamanho do quadrado (usar o menor lado)
            $width = $image->width();
            $height = $image->height();
            $size = min($width, $height);
            
            // Recortar para um quadrado
            $image = $image->cover($size, $size);
            
            // Gerar um nome de arquivo único
            $filename = 'playlist_' . time() . '_' . uniqid() . '.jpg';
            $path = 'playlists/' . $filename;
            
            // Salvar a imagem no storage com qualidade 80%
            \Storage::disk('public')->put($path, $image->toJpeg(80));
            
            $validated['image'] = $path;
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $playlist = Playlist::create($validated);

        if (!empty($validated['vinyls'])) {
            foreach ($validated['vinyls'] as $index => $vinylId) {
                $vinyl = VinylMaster::find($vinylId);
                $playlist->addTrack($vinyl, $vinyl);
            }
        }

        return redirect()
            ->route('admin.playlists.index')
            ->with('success', 'Playlist criada com sucesso!');
    }

    public function edit(Playlist $playlist)
    {
        $playlist->load('tracks.vinylMaster');
        return view('admin.playlists.edit', compact('playlist'));
    }

    public function update(Request $request, Playlist $playlist)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:playlists,slug,' . $playlist->id,
            'bio' => 'nullable|string',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'soundcloud_url' => 'nullable|url|max:255',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'vinyls' => 'array|max:10',
            'vinyls.*' => 'required|exists:vinyl_masters,id'
        ], [
            'name.required' => 'O nome da playlist é obrigatório',
            'name.max' => 'O nome não pode ter mais que 255 caracteres',
            'slug.unique' => 'Este slug já está em uso',
            'image.image' => 'O arquivo deve ser uma imagem',
            'image.max' => 'A imagem não pode ter mais que 2MB',
            'vinyls.max' => 'Uma playlist não pode ter mais que 10 discos'
        ]);

        if ($request->hasFile('image')) {
            // Excluir a imagem anterior se existir
            if ($playlist->image) {
                Storage::disk('public')->delete($playlist->image);
            }
            
            // Processar a imagem para garantir que seja quadrada
            $image = \Intervention\Image\ImageManager::gd()->read($request->file('image'));
            
            // Determinar o tamanho do quadrado (usar o menor lado)
            $width = $image->width();
            $height = $image->height();
            $size = min($width, $height);
            
            // Recortar para um quadrado
            $image = $image->cover($size, $size);
            
            // Gerar um nome de arquivo único
            $filename = 'playlist_' . time() . '_' . uniqid() . '.jpg';
            $path = 'playlists/' . $filename;
            
            // Salvar a imagem no storage com qualidade 80%
            \Storage::disk('public')->put($path, $image->toJpeg(80));
            
            $validated['image'] = $path;
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $playlist->update($validated);

        if (isset($validated['vinyls'])) {
            // Remove todas as faixas atuais
            $playlist->tracks()->delete();
            
            // Adiciona as novas faixas
            foreach ($validated['vinyls'] as $index => $vinylId) {
                $vinyl = VinylMaster::find($vinylId);
                $playlist->addTrack($vinyl, $vinyl);
            }
        }

        return redirect()
            ->route('admin.playlists.index')
            ->with('success', 'Playlist atualizada com sucesso!');
    }

    public function destroy(Playlist $playlist)
    {
        if ($playlist->image) {
            Storage::disk('public')->delete($playlist->image);
        }

        $playlist->delete();

        return redirect()
            ->route('admin.playlists.index')
            ->with('success', 'Playlist excluída com sucesso!');
    }

    public function searchVinyls(Request $request)
    {
        try {
            $query = $request->get('query');
            
            $vinyls = VinylMaster::with(['artists', 'genres'])
                ->where(function($q) use ($query) {
                    $q->whereHas('artists', function($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    })
                    ->orWhere('title', 'like', "%{$query}%");
                })
                ->limit(10)
                ->get()
                ->map(function($vinyl) {
                    return [
                        'id' => $vinyl->id,
                        'titulo' => $vinyl->title,
                        'artista' => $vinyl->artists->pluck('name')->join(', '),
                        'capa' => $vinyl->cover_image ? asset('storage/' . $vinyl->cover_image) : asset('images/default-playlist.svg'),
                        'generos' => $vinyl->genres->pluck('name')->toArray()
                    ];
                });

            return response()->json($vinyls);
        } catch (\Exception $e) {
            \Log::error('Erro na busca de discos: ' . $e->getMessage());
            return response()->json(['erro' => 'Ocorreu um erro ao buscar os discos.'], 500);
        }
    }
    
    /**
     * Mostrar formulário para editar faixas da playlist
     */
    public function editTracks(Playlist $playlist)
    {
        $playlist->load('tracks.vinylMaster.artists');
        return view('admin.playlists.edit_tracks', compact('playlist'));
    }
    
    /**
     * Atualizar faixas da playlist
     */
    public function updateTracks(Request $request, Playlist $playlist)
    {
        $validated = $request->validate([
            'vinyls' => 'array|max:10',
            'vinyls.*.vinyl_master_id' => 'required|exists:vinyl_masters,id',
        ], [
            'vinyls.max' => 'Uma playlist não pode ter mais que 10 discos'
        ]);
        
        // Remove todas as faixas atuais
        $playlist->tracks()->delete();
        
        // Adiciona as novas faixas se foram enviadas
        if (isset($validated['vinyls'])) {
            foreach ($validated['vinyls'] as $index => $vinylData) {
                $vinylMasterId = $vinylData['vinyl_master_id'];
                $vinyl = VinylMaster::find($vinylMasterId);
                if ($vinyl) {
                    $playlist->addTrack($vinyl, $vinyl);
                }
            }
        }

        return redirect()
            ->route('admin.playlists.edit', $playlist)
            ->with('success', 'Faixas da playlist atualizadas com sucesso!');
    }
}
