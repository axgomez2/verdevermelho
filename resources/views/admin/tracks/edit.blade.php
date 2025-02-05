@extends('layouts.admin')

@section('title', 'Editar Faixas')

@section('content')
<div x-data="trackManager" class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Editar Faixas: {{ $vinyl->title }}</h2>
        <a href="{{ route('admin.vinyls.show', $vinyl->id) }}" class="btn btn-ghost">Voltar</a>
    </div>

    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body p-4">
            <div class="text-sm">
                <span class="font-bold">Artista:</span> {{ $vinyl->artists->pluck('name')->join(', ') }} |
                <span class="font-bold">Ano:</span> {{ $vinyl->release_year }}
            </div>
        </div>
    </div>

    <form action="{{ route('admin.vinyls.update-tracks', $vinyl->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4">
                <div id="tracks-container" class="space-y-4">
                    <template x-for="(track, index) in tracks" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-center bg-base-200 p-2 rounded">
                            <input type="hidden" :name="'tracks['+index+'][id]'" :value="track.id">
                            <input type="text" x-model="track.name" :name="'tracks['+index+'][name]'" class="input input-sm input-bordered col-span-4" placeholder="Nome da Faixa" required>
                            <input type="text" x-model="track.duration" :name="'tracks['+index+'][duration]'" class="input input-sm input-bordered col-span-2" placeholder="Duração">
                            <div class="col-span-6 flex">
                                <input type="url" x-model="track.youtube_url" :name="'tracks['+index+'][youtube_url]'" class="input input-sm input-bordered flex-grow" placeholder="URL do YouTube">
                                <button type="button" class="btn btn-sm btn-square btn-secondary ml-1" @click="searchYouTube(index)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <button type="button" class="btn btn-sm btn-secondary mt-4" @click="addTrack">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Adicionar Faixa
                </button>
            </div>
            <div class="card-actions justify-end p-4">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </div>
    </form>

    <!-- Modal para resultados do YouTube -->
    <div x-show="showModal" class="modal modal-open" x-cloak>
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Selecionar Vídeo do YouTube</h3>
            <div class="space-y-2">
                <template x-for="result in youtubeResults" :key="result.id.videoId">
                    <div class="p-2 hover:bg-base-200 rounded cursor-pointer" @click="selectVideo(result)">
                        <h4 class="font-semibold" x-text="result.snippet.title"></h4>
                        <p class="text-sm text-gray-600" x-text="result.snippet.description"></p>
                    </div>
                </template>
            </div>
            <div class="modal-action">
                <button class="btn btn-sm" @click="closeModal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('trackManager', () => ({
        tracks: @json($vinyl->tracks),
        showModal: false,
        youtubeResults: [],
        activeTrackIndex: null,

        addTrack() {
            this.tracks.push({ name: '', duration: '', youtube_url: '' });
        },

        async searchYouTube(index) {
            this.activeTrackIndex = index;
            const trackName = this.tracks[index].name;
            const artistName = '{{ $vinyl->artists->pluck('name')->join(' ') }}';
            const query = `${artistName} ${trackName}`;

            try {
                const response = await fetch('{{ route('youtube.search') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ query })
                });

                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                if (data.error) throw new Error(data.error);

                this.youtubeResults = data;
                this.showModal = true;
            } catch (error) {
                console.error('Erro ao pesquisar no YouTube:', error);
                alert('Ocorreu um erro ao pesquisar no YouTube. Por favor, tente novamente.');
            }
        },

        selectVideo(video) {
            if (this.activeTrackIndex !== null) {
                this.tracks[this.activeTrackIndex].youtube_url = `https://www.youtube.com/watch?v=${video.id.videoId}`;
            }
            this.closeModal();
        },

        closeModal() {
            this.showModal = false;
            this.youtubeResults = [];
            this.activeTrackIndex = null;
        }
    }));
});
</script>
@endpush

