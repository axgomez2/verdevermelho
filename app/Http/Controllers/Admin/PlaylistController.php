<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Models\VinylMaster;
use App\Models\VinylSec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlaylistController extends Controller
{
    public function index()
    {
        $playlists = Playlist::with(['tracks.vinylMaster', 'tracks.trackable'])
            ->latest()
            ->paginate(10);
        return view('admin.playlists.index', compact('playlists'));
    }

    public function create()
    {
        $vinylMasters = VinylMaster::with(['vinylSec', 'artists'])->get();
        return view('admin.playlists.create', compact('vinylMasters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                      => 'required|string|max:255',
            'slug'                      => 'nullable|string|max:255|unique:playlists',
            'bio'                       => 'nullable|string',
            'instagram_url'             => 'nullable|url|max:255',
            'youtube_url'               => 'nullable|url|max:255',
            'facebook_url'              => 'nullable|url|max:255',
            'soundcloud_url'            => 'nullable|url|max:255',
            'image'                     => 'nullable|image|max:2048',
            'is_active'                 => 'boolean',
            'vinyls'                  => 'array|max:10',
            'vinyls.*.vinyl_master_id'  => 'required|exists:vinyl_masters,id',
            'vinyls.*.vinyl_sec_id'     => 'required|exists:vinyl_secs,id'
        ]);

        // Tratamento de upload de imagem
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('playlists', 'public');
            $validated['image'] = $path;
        }

        // Geração do slug se não for fornecido
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $playlist = Playlist::create($validated);

        // Adição dos discos (vinyls) à playlist, se informados
        if (!empty($validated['vinyls'])) {
            foreach ($validated['vinyls'] as $index => $vinylData) {
                $vinylMaster = VinylMaster::find($vinylData['vinyl_master_id']);
                $vinylSec = VinylSec::find($vinylData['vinyl_sec_id']);

                if ($vinylMaster && $vinylSec) {
                    $playlist->tracks()->create([
                        'vinyl_master_id'  => $vinylMaster->id,
                        'trackable_type'   => VinylSec::class,
                        'trackable_id'     => $vinylSec->id,
                        'position'         => $index + 1
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.playlists.index')
            ->with('success', 'Playlist created successfully.');
    }

    public function edit(Playlist $playlist)
    {
        $playlist->load(['tracks.vinylMaster', 'tracks.trackable']);
        $vinylMasters = VinylMaster::with(['vinylSec', 'artists'])->get();

        return view('admin.playlists.edit', compact('playlist', 'vinylMasters'));
    }

    public function update(Request $request, Playlist $playlist)
    {
        $validated = $request->validate([
            'name'                      => 'required|string|max:255',
            'slug'                      => 'nullable|string|max:255|unique:playlists,slug,' . $playlist->id,
            'bio'                       => 'nullable|string',
            'instagram_url'             => 'nullable|url|max:255',
            'youtube_url'               => 'nullable|url|max:255',
            'facebook_url'              => 'nullable|url|max:255',
            'soundcloud_url'            => 'nullable|url|max:255',
            'image'                     => 'nullable|image|max:2048',
            'is_active'                 => 'boolean',
            'vinyls'                  => 'array|max:10',
            'vinyls.*.vinyl_master_id'  => 'required|exists:vinyl_masters,id',
            'vinyls.*.vinyl_sec_id'     => 'required|exists:vinyl_secs,id'
        ]);

        // Tratamento de upload de imagem
        if ($request->hasFile('image')) {
            // Exclui a imagem antiga se existir
            if ($playlist->image) {
                Storage::disk('public')->delete($playlist->image);
            }
            $path = $request->file('image')->store('playlists', 'public');
            $validated['image'] = $path;
        }

        // Geração do slug se não for informado
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $playlist->update($validated);

        // Atualiza os discos (vinyls) vinculados à playlist
        if (isset($validated['vinyls'])) {
            // Remove todas as tracks existentes
            $playlist->tracks()->delete();

            // Adiciona os novos discos
            foreach ($validated['vinyls'] as $index => $vinylData) {
                $vinylMaster = VinylMaster::find($vinylData['vinyl_master_id']);
                $vinylSec = VinylSec::find($vinylData['vinyl_sec_id']);

                if ($vinylMaster && $vinylSec) {
                    $playlist->tracks()->create([
                        'vinyl_master_id'  => $vinylMaster->id,
                        'trackable_type'   => VinylSec::class,
                        'trackable_id'     => $vinylSec->id,
                        'position'         => $index + 1
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.playlists.index')
            ->with('success', 'Playlist updated successfully.');
    }

    public function destroy(Playlist $playlist)
    {
        // Exclui a imagem, se existir
        if ($playlist->image) {
            Storage::disk('public')->delete($playlist->image);
        }

        $playlist->delete();

        return redirect()
            ->route('admin.playlists.index')
            ->with('success', 'Playlist deleted successfully.');
    }

    public function reorderTracks(Request $request, Playlist $playlist)
    {
        $request->validate([
            'tracks'   => 'required|array',
            'tracks.*' => 'exists:playlist_tracks,id'
        ]);

        foreach ($request->tracks as $index => $trackId) {
            $playlist->reorderTrack($trackId, $index + 1);
        }

        return response()->json(['message' => 'Tracks reordered successfully']);
    }

    /**
     * Busca discos (vinyls) filtrando pelo título ou pelo nome do artista.
     * Utiliza o relacionamento definido no modelo VinylMaster (via tabela pivô artist_vinyl_master).
     */
    public function searchVinyls(Request $request)
    {
        $search = $request->get('q');

        if (strlen($search) < 3) {
            return response()->json([]);
        }

        $results = VinylMaster::query()
            ->where('title', 'LIKE', "%{$search}%")
            ->orWhereHas('artists', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            })
            ->with(['vinylSec', 'artists'])
            ->distinct()
            ->get();

        return response()->json($results);
    }
}
