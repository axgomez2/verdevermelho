@extends('layouts.admin')

@section('title', 'Complete Vinyl Record')

@section('content')
<div x-data="vinylComplete" class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6">
                Completar cadastro de: {{ $vinylMaster->artists->pluck('name')->join(', ') }} - {{ $vinylMaster->title }}
            </h2>
            <form action="{{ route('admin.vinyl.storeComplete', $vinylMaster->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="alert alert-error mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div>
                        <h3 class="font-bold">Campos obrigatórios</h3>
                        <div class="text-xs">Complete todos esses campos, é importante:</div>
                    </div>
                    <div>
                        <a href="{{ $vinylMaster->discogs_url }}" target='_blank' class="btn btn-secondary">Link do disco no Discogs</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="label" for="weight_id">
                            <span class="label-text">Peso:</span>
                        </label>
                        <select id="weight_id" name="weight_id" class="select select-bordered w-full" required>
                            <option value="">Selecionar peso:</option>
                            @foreach($weights as $weight)
                                <option value="{{ $weight->id }}" {{ $weight->id == 1 ? 'selected' : '' }}>
                                    {{ $weight->name }} ({{ $weight->value }} {{ $weight->unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label" for="dimension_id">
                            <span class="label-text">Dimensões:</span>
                        </label>
                        <select id="dimension_id" name="dimension_id" class="select select-bordered w-full" required>
                            <option value="">Selecionar dimensão</option>
                            @foreach($dimensions as $dimension)
                                <option value="{{ $dimension->id }}" {{ $dimension->id == 3 ? 'selected' : '' }}>
                                    {{ $dimension->name }} ({{ $dimension->height }}x{{ $dimension->width }}x{{ $dimension->depth }} {{ $dimension->unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label" for="quantity">
                            <span class="label-text">Estoque:</span>
                        </label>
                        <input type="number" id="quantity" name="quantity" min="0" class="input input-bordered w-full" value="1" required>
                    </div>

                    <div>
                        <label class="label" for="price">
                            <span class="label-text">Preço de Venda:</span>
                        </label>
                        <input type="number" id="price" name="price" step="0.01" min="0" class="input input-bordered w-full" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label for="cat_style_shop_id" class="label">
                            <span class="label-text">Categoria de Estilo da Loja</span>
                        </label>
                        <select name="cat_style_shop_id" id="cat_style_shop_id" class="select select-bordered w-full">
                            <option value="">Selecione uma categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('cat_style_shop_id', $vinylMaster->vinylSec->cat_style_shop_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('cat_style_shop_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text">Produto novo?</span>
                        </label>
                        <div class="flex gap-4">
                            <label class="label cursor-pointer">
                                <input type="radio" name="is_new" value="1" class="radio">
                                <span class="label-text ml-2">Novo</span>
                            </label>
                            <label class="label cursor-pointer">
                                <input type="radio" name="is_new" value="0" class="radio" checked>
                                <span class="label-text ml-2">Usado</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text">Em promoção?</span>
                        </label>
                        <div class="flex gap-4">
                            <label class="label cursor-pointer">
                                <input type="radio" name="is_promotional" value="1" class="radio">
                                <span class="label-text ml-2">SIM</span>
                            </label>
                            <label class="label cursor-pointer">
                                <input type="radio" name="is_promotional" value="0" class="radio" checked>
                                <span class="label-text ml-2">NÃO</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text">Em Estoque</span>
                        </label>
                        <div class="flex gap-4">
                            <label class="label cursor-pointer">
                                <input type="radio" name="in_stock" value="1" class="radio" checked>
                                <span class="label-text ml-2">Com estoque</span>
                            </label>
                            <label class="label cursor-pointer">
                                <input type="radio" name="in_stock" value="0" class="radio">
                                <span class="label-text ml-2">Sem estoque</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="alert alert-success mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div>
                        <h3 class="font-bold">Campos opcionais</h3>
                        <div class="text-xs">Campos auxiliares no cadastro, mas não são obrigatórios:</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="label" for="cover_status">
                            <span class="label-text">Estado da capa:</span>
                        </label>
                        <select id="cover_status" name="cover_status" class="select select-bordered w-full">
                            <option value="">Selecione estado da capa</option>
                            @foreach(['mint', 'near_mint', 'very_good', 'good', 'fair', 'poor'] as $status)
                                <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label" for="midia_status">
                            <span class="label-text">Estado da mídia:</span>
                        </label>
                        <select id="midia_status" name="midia_status" class="select select-bordered w-full">
                            <option value="">Selecionar estado da midia</option>
                            @foreach(['mint', 'near_mint', 'very_good', 'good', 'fair', 'poor'] as $status)
                                <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label" for="catalog_number">
                            <span class="label-text">Numero de catálogo:</span>
                        </label>
                        <input type="text" id="catalog_number" name="catalog_number" class="input input-bordered w-full">
                    </div>

                    <div>
                        <label class="label" for="barcode">
                            <span class="label-text">Barcode:</span>
                        </label>
                        <input type="text" id="barcode" name="barcode" class="input input-bordered w-full">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="label" for="format">
                            <span class="label-text">Formato:</span>
                        </label>
                        <input type="text" id="format" name="format" class="input input-bordered w-full">
                    </div>

                    <div>
                        <label class="label" for="num_discs">
                            <span class="label-text">Numero de discos:</span>
                        </label>
                        <input type="number" id="num_discs" name="num_discs" min="1" value="1" class="input input-bordered w-full" required>
                    </div>

                    <div>
                        <label class="label" for="speed">
                            <span class="label-text">Velocidade:</span>
                        </label>
                        <input type="text" id="speed" name="speed" class="input input-bordered w-full">
                    </div>

                    <div>
                        <label class="label" for="edition">
                            <span class="label-text">Edição (se aplicável):</span>
                        </label>
                        <input type="text" id="edition" name="edition" class="input input-bordered w-full">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="label" for="buy_price">
                            <span class="label-text">Preço de compra:</span>
                        </label>
                        <input type="number" id="buy_price" name="buy_price" step="0.01" min="0" class="input input-bordered w-full">
                    </div>

                    <div>
                        <label class="label" for="promotional_price">
                            <span class="label-text">Preço promocional:</span>
                        </label>
                        <input type="number" id="promotional_price" name="promotional_price" step="0.01" min="0" class="input input-bordered w-full">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="label" for="notes">
                        <span class="label-text">Notas e descrição:</span>
                    </label>
                    <textarea id="notes" name="notes" rows="3" class="textarea textarea-bordered w-full"></textarea>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Faixas: importante</h3>
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Faixa</th>
                                    <th>Duração</th>
                                    <th>YouTube URL</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tracks as $track)
                                    <tr>
                                        <td>{{ $track->name }}</td>
                                        <td>{{ $track->duration }}</td>
                                        <td>
                                            <input type="text" name="track_youtube_urls[{{ $track->id }}]" placeholder="YouTube URL (optional)" class="input input-bordered w-full youtube-url-input">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-secondary search-youtube" @click="searchYouTube('{{ $track->name }}', $event.target.closest('tr').querySelector('.youtube-url-input'))">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                                </svg>
                                                Buscar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-control">
                    <button type="submit" class="btn btn-primary">Completar cadastro</button>
                </div>
            </form>
        </div>
    </div>

    <!-- YouTube search results modal -->
    <div x-show="showModal" class="modal modal-open" x-cloak>
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Select YouTube Video</h3>
            <div id="youtube-results-list" class="space-y-4">
                <template x-for="result in youtubeResults" :key="result.id.videoId">
                    <div class="card bg-base-100 shadow-sm hover:shadow-md cursor-pointer" @click="selectVideo(result)">
                        <div class="card-body">
                            <h2 class="card-title" x-text="result.snippet.title"></h2>
                            <p x-text="result.snippet.description"></p>
                        </div>
                    </div>
                </template>
            </div>
            <div class="modal-action">
                <button class="btn" @div>
            <div class="modal-action">
                <button class="btn" @click="closeModal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('vinylComplete', () => ({
        showModal: false,
        youtubeResults: [],
        activeInputField: null,

        async searchYouTube(trackName, inputField) {
            this.activeInputField = inputField;
            const artistName = '{{ $vinylMaster->artists->pluck('name')->join(' ') }}';
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

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }

                this.youtubeResults = data;
                this.showModal = true;
            } catch (error) {
                console.error('Erro ao pesquisar no YouTube:', error);
                alert('Ocorreu um erro ao pesquisar no YouTube. Por favor, tente novamente.');
            }
        },

        selectVideo(video) {
            if (this.activeInputField) {
                this.activeInputField.value = `https://www.youtube.com/watch?v=${video.id.videoId}`;
            }
            this.closeModal();
        },

        closeModal() {
            this.showModal = false;
            this.youtubeResults = [];
            this.activeInputField = null;
        }
    }));
});
</script>
@endpush

