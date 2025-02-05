@extends('layouts.admin')

@section('title', 'Create New Vinyl')

@section('content')
<div x-data="vinylManager" class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-xl">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6">Pesquisar novo disco:</h2>

            <form action="{{ route('admin.vinyls.create') }}" method="GET" @submit="startSearch">
                <div class="flex items-center space-x-2">
                    <input type="text" name="query" value="{{ $query }}" class="input input-bordered flex-grow" placeholder="Encontre o disco pelo artista, título ou código do disco">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                        Pesquisar
                    </button>
                </div>
            </form>
            <div x-show="loading" class="mt-4">
                <progress class="progress w-full"></progress>
            </div>

            <div id="searchResults">
                @if($selectedRelease)
                    <div class="mt-8 lg:max-w-4xl xl:max-w-6xl mx-auto">
                        <h3 class="text-xl font-semibold mb-4">Você selecionou o disco: {{ $selectedRelease['title'] }}</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1">
                                @if(isset($selectedRelease['images']) && count($selectedRelease['images']) > 0)
                                    <img src="{{ $selectedRelease['images'][0]['uri'] }}" alt="{{ $selectedRelease['title'] }}" class="w-full rounded-lg shadow-lg">
                                @else
                                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center rounded-lg">
                                        <span class="text-gray-500">Sem imagem</span>
                                    </div>
                                @endif
                            </div>
                            <div class="md:col-span-2">
                                <div class="space-y-4">
                                    <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                                        <div class="sm:col-span-1">
                                            <dt class="text-sm font-medium text-gray-500">Artista</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ implode(', ', array_column($selectedRelease['artists'], 'name')) }}</dd>
                                        </div>
                                        <div class="sm:col-span-1">
                                            <dt class="text-sm font-medium text-gray-500">Título</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $selectedRelease['title'] }}</dd>
                                        </div>
                                        <div class="sm:col-span-1">
                                            <dt class="text-sm font-medium text-gray-500">Ano</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $selectedRelease['year'] ?? 'Desconhecido' }}</dd>
                                        </div>
                                        <div class="sm:col-span-1">
                                            <dt class="text-sm font-medium text-gray-500">Gênero</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ implode(', ', $selectedRelease['genres'] ?? ['Não especificado']) }}</dd>
                                        </div>
                                        <div class="sm:col-span-1">
                                            <dt class="text-sm font-medium text-gray-500">Estilos</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ implode(', ', $selectedRelease['styles'] ?? ['Não especificado']) }}</dd>
                                        </div>
                                        <div class="sm:col-span-1">
                                            <dt class="text-sm font-medium text-gray-500">País</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $selectedRelease['country'] ?? 'Desconhecido' }}</dd>
                                        </div>
                                        @if(isset($selectedRelease['labels']))
                                            <div class="sm:col-span-1">
                                                <dt class="text-sm font-medium text-gray-500">Gravadora</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ implode(', ', array_column($selectedRelease['labels'], 'name')) }}</dd>
                                            </div>
                                        @endif
                                    </dl>

                                    <div>
                                        <h4 class="text-lg font-semibold mb-2">Informações de Mercado (Discogs)</h4>
                                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                                            @if(isset($selectedRelease['community']['have']))
                                                <div class="sm:col-span-1">
                                                    <dt class="text-sm font-medium text-gray-500">Quantidade em coleções</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">{{ $selectedRelease['community']['have'] }}</dd>
                                                </div>
                                            @endif
                                            @if(isset($selectedRelease['num_for_sale']))
                                                <div class="sm:col-span-1">
                                                    <dt class="text-sm font-medium text-gray-500">Quantidade à venda</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">{{ $selectedRelease['num_for_sale'] }}</dd>
                                                </div>
                                            @endif
                                            @if(isset($selectedRelease['lowest_price']))
                                                <div class="sm:col-span-1">
                                                    <dt class="text-sm font-medium text-gray-500">Preço mais baixo</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">{{ $selectedRelease['lowest_price'] }} {{ $selectedRelease['price_currency'] ?? 'USD' }}</dd>
                                                </div>
                                            @endif
                                        </dl>
                                    </div>

                                    @if(isset($selectedRelease['tracklist']))
                                        <div>
                                            <h4 class="text-lg font-semibold mb-2">Tracklist</h4>
                                            <ol class="list-decimal list-inside space-y-1">
                                                @foreach($selectedRelease['tracklist'] as $track)
                                                    <li class="text-sm">
                                                        <span class="font-medium">{{ $track['title'] }}</span>
                                                        @if(isset($track['duration']))
                                                            <span class="text-gray-500 ml-2">({{ $track['duration'] }})</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    @endif

                                    @if(isset($selectedRelease['notes']))
                                        <div>
                                            <h4 class="text-lg font-semibold mb-2">Notas</h4>
                                            <p class="text-sm text-gray-700">{{ $selectedRelease['notes'] }}</p>
                                        </div>
                                    @endif

                                    <div>
                                        <a href="{{ $selectedRelease['uri'] }}" target='_blank' class="btn btn-secondary">Link do disco no Discogs</a>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <button @click="saveVinyl({{ $selectedRelease['id'] }})" class="btn btn-primary" :disabled="saveLoading">
                                        <span x-show="saveLoading" class="loading loading-spinner loading-sm mr-2"></span>
                                        <span x-text="saveLoading ? 'Salvando...' : 'Salvar disco'"></span>
                                    </button>
                                    <a href="{{ route('admin.vinyls.create') }}" class="btn btn-ghost ml-2">Voltar para busca</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(count($searchResults) > 0)
                    <div class="mt-8" x-data="{ formatFilter: '', countryFilter: '', yearFilter: '' }">
                        <h3 class="text-xl font-semibold mb-4">Resultados da Busca</h3>

                        <div class="flex flex-wrap gap-4 mb-4">
                            <select x-model="formatFilter" class="select select-bordered w-full max-w-xs">
                                <option value="">Todos os Formatos</option>
                                @foreach(collect($searchResults)->pluck('format')->flatten()->unique() as $format)
                                    <option value="{{ $format }}">{{ $format }}</option>
                                @endforeach
                            </select>

                            <select x-model="countryFilter" class="select select-bordered w-full max-w-xs">
                                <option value="">Todos os Países</option>
                                @foreach(collect($searchResults)->pluck('country')->unique() as $country)
                                    <option value="{{ $country }}">{{ $country }}</option>
                                @endforeach
                            </select>

                            <select x-model="yearFilter" class="select select-bordered w-full max-w-xs">
                                <option value="">Todos os Anos</option>
                                @foreach(collect($searchResults)->pluck('year')->filter()->unique() as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                                <option value="Desconhecido">Ano desconhecido</option>
                            </select>
                        </div>

                        <div class="space-y-4">
                            @foreach($searchResults as $result)
                                <div class="card bg-base-100 shadow-sm"
                                     x-show="
                                        (!formatFilter || '{{ $result['format'][0] ?? '' }}'.includes(formatFilter)) &&
                                        (!countryFilter || '{{ $result['country'] }}' === countryFilter) &&
                                        (!yearFilter || '{{ $result['year'] ?? 'Desconhecido' }}' === yearFilter)
                                     ">
                                    <div class="card-body">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $result['thumb'] ?? '/placeholder-image.jpg' }}" alt="{{ $result['title'] }}" class="w-16 h-16 object-cover rounded">
                                            </div>
                                            <div class="flex-grow">
                                                <h4 class="text-lg font-semibold">{{ $result['title'] }}</h4>
                                                <div class="text-sm text-gray-500">
                                                    <span>{{ $result['year'] ?? 'Ano desconhecido' }}</span>
                                                    @if(isset($result['format']))
                                                        <span class="mx-2">|</span>
                                                        <span>{{ $result['format'][0] ?? 'Format unknown' }}</span>
                                                    @endif
                                                    @if(isset($result['country']))
                                                        <span class="mx-2">|</span>
                                                        <span>{{ $result['country'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.vinyls.create', ['release_id' => $result['id'], 'query' => $query]) }}" class="btn btn-primary btn-sm">
                                                    Selecionar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($query)
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mt-4" role="alert">
                        Nenhum resultado encontrado para "{{ $query }}".
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div x-show="showModal" class="modal modal-open" x-cloak>
        <div class="modal-box relative z-50">
            <h3 class="font-bold text-lg" x-text="modalStatus === 'exists' ? 'Disco já cadastrado' : (modalStatus === 'success' ? 'Disco salvo com sucesso!' : 'Erro')"></h3>
            <p class="py-4" x-text="modalMessage"></p>
            <div class="modal-action">
                <template x-if="modalStatus === 'success'">
                    <div>
                        <a :href="completeVinylUrl" class="btn btn-primary">Completar cadastro</a>
                        <button @click="closeModalAndRedirect" class="btn btn-secondary">Voltar para lista de discos</button>
                        <button @click="closeModal" class="btn btn-ghost">Fechar</button>
                    </div>
                </template>
                <template x-if="modalStatus === 'exists'">
                    <div>
                        <button @click="closeModalAndRedirect" class="btn btn-secondary">Voltar para lista de discos</button>
                        <button @click="closeModal" class="btn btn-ghost">Fechar</button>
                    </div>
                </template>
                <template x-if="modalStatus === 'error'">
                    <button @click="closeModal" class="btn btn-error">Fechar</button>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('vinylManager', () => ({
        loading: false,
        saveLoading: false,
        showModal: false,
        savedVinylId: null,
        modalMessage: '',
        modalStatus: '',

        startSearch() {
            this.loading = true;
        },

        get completeVinylUrl() {
            return this.savedVinylId
                ? '{{ route('admin.vinyls.complete', ['id' => ':id']) }}'.replace(':id', this.savedVinylId)
                : '#';
        },

        async saveVinyl(releaseId) {
            this.saveLoading = true;
            try {
                const response = await fetch('{{ route('admin.vinyls.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ release_id: releaseId })
                });

                const data = await response.json();
                console.log('Resposta do servidor:', data); // Log para depuração

                this.savedVinylId = data.vinyl_id;
                this.modalMessage = data.message;
                this.modalStatus = data.status;
                this.showModal = true;
            } catch (error) {
                console.error('Erro detalhado:', error);
                this.modalMessage = 'Ocorreu um erro ao processar o disco. Por favor, tente novamente.';
                this.modalStatus = 'error';
                this.showModal = true;
            } finally {
                this.saveLoading = false;
            }
        },

        closeModal() {
            this.showModal = false;
        },

        closeModalAndRedirect() {
            this.showModal = false;
            window.location.href = '{{ route('admin.vinyls.index') }}';
        }
    }));
});
</script>
@endpush

