@php
    // Verifica se o vinil tem faixas com URLs do YouTube
    $hasPlayableTracks = $vinyl->tracks->contains(function($track) {
        return !empty($track->youtube_url);
    });

    $isAvailable = $vinyl->vinylSec->quantity > 0 && $vinyl->vinylSec->in_stock;
    $statusText = !$vinyl->vinylSec->in_stock ? 'Fora de Estoque' :
                 ($vinyl->vinylSec->quantity == 0 ? 'Esgotado' : 'Disponível');
    $statusClass = $isAvailable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';

    // Nova validação para promoção
    $isPromotional = $vinyl->vinylSec->is_promotional &&
                    $vinyl->vinylSec->promotional_price &&
                    $vinyl->vinylSec->promotional_price > 0;
@endphp

<div class="bg-white border border-gray-200 rounded-lg shadow hover:shadow-xl transition-all duration-300 group h-full">
    <!-- Card para Desktop (vertical) -->
    <div class="hidden sm:block">
        <div class="relative aspect-square overflow-hidden">
            <img
                src="{{ asset('storage/' . $vinyl->cover_image) }}"
                alt="{{ $vinyl->title }} by {{ $vinyl->artists->pluck('name')->implode(', ') }}"
                class="w-full h-full object-cover object-center group-hover:scale-110 transition-transform duration-300"
                onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'"
            />
            <div class="absolute top-2 left-2 flex flex-col gap-2">
                <span class="{{ $statusClass }} text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ $statusText }}
                </span>
                @if($isPromotional)
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                        Oferta
                    </span>
                @endif
            </div>
            <button
                type="button"
                class="play-button absolute bottom-2 right-2 inline-flex items-center justify-center w-8 h-8 text-white rounded-full focus:ring-4 focus:ring-blue-300 {{ $hasPlayableTracks ? 'bg-blue-700 hover:bg-blue-800' : 'bg-gray-400 cursor-not-allowed' }}"
                data-vinyl-info="{{ json_encode([
                    'tracks' => $vinyl->tracks->map(function($track) {
                        return [
                            'id' => $track->id,
                            'name' => $track->name,
                            'youtube_url' => $track->youtube_url
                        ];
                    }),
                    'artist' => $vinyl->artists->pluck('name')->implode(', '),
                    'coverUrl' => asset('storage/' . $vinyl->cover_image),
                    'vinylTitle' => $vinyl->title
                ]) }}"
                onclick="{{ $hasPlayableTracks ? 'initializeCardPlayer(this)' : '' }}"
                {{ $hasPlayableTracks ? '' : 'disabled' }}
                title="{{ $hasPlayableTracks ? 'Reproduzir' : 'Áudio não disponível' }}"
            >
                <i class="fas fa-play text-xs"></i>
            </button>
        </div>
        <div class="p-4">
            <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}">
                <h5 class="text-base font-semibold tracking-tight text-gray-900 line-clamp-1">
                    {{ $vinyl->artists->pluck('name')->implode(', ') }}
                </h5>
                <p class="text-sm text-gray-500 line-clamp-1">{{ $vinyl->title }}</p>
            </a>
            <div class="flex justify-between items-start mt-2.5">
                <div>
                    <p class="text-xs text-gray-500">{{ $vinyl->recordLabel->name }} • {{ $vinyl->release_year }}</p>
                    @if($isPromotional)
                        <p class="text-xs text-gray-500 line-through">
                            R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                        </p>
                        <p class="text-lg font-bold text-gray-900">
                            R$ {{ number_format($vinyl->vinylSec->promotional_price, 2, ',', '.') }}
                        </p>
                    @else
                        <p class="text-lg font-bold text-gray-900">
                            R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                        </p>
                    @endif
                </div>
                <button
                    type="button"
                    title="{{ $vinyl->inWishlist() ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}"
                    class="wishlist-button text-gray-400 hover:text-red-500 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm p-2"
                    data-product-id="{{ $vinyl->id }}"
                    data-product-type="{{ get_class($vinyl) }}"
                    data-in-wishlist="{{ $vinyl->inWishlist() ? 'true' : 'false' }}"
                >
                    <i class="fas fa-heart {{ $vinyl->inWishlist() ? 'text-red-500' : '' }}"></i>
                </button>
            </div>
            <div class="mt-4">
                <button
                    type="button"
                    class="add-to-cart-button w-full text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 {{ !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}"
                    data-product-id="{{ $vinyl->product->id }}"
                    data-quantity="1"
                    {{ $isAvailable ? '' : 'disabled' }}
                >
                    {{ $isAvailable ? 'Adicionar ao Carrinho' : 'Indisponível' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Card para Mobile (horizontal) -->
    <div class="sm:hidden flex">
        <div class="relative w-1/3">
            <img
                src="{{ asset('storage/' . $vinyl->cover_image) }}"
                alt="{{ $vinyl->title }}"
                class="w-full h-full object-cover object-center"
                onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'"
            />
            <div class="absolute top-1 left-1 flex flex-col gap-1">
                <span class="{{ $statusClass }} text-xs font-medium px-2 py-0.5 rounded-full">
                    {{ $statusText }}
                </span>
                @if($isPromotional)
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded">
                        Oferta
                    </span>
                @endif
            </div>
        </div>
        <div class="w-2/3 p-3 flex flex-col justify-between">
            <div>
                <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}">
                    <h5 class="text-sm font-semibold tracking-tight text-gray-900 line-clamp-1">
                        {{ $vinyl->artists->pluck('name')->implode(', ') }}
                    </h5>
                    <p class="text-xs text-gray-500 line-clamp-1">{{ $vinyl->title }}</p>
                </a>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $vinyl->recordLabel->name }} • {{ $vinyl->release_year }}
                </p>
            </div>
            <div class="flex justify-between items-center mt-2">
                <div>
                    @if($isPromotional)
                        <p class="text-xs text-gray-500 line-through">
                            R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                        </p>
                        <p class="text-base font-bold text-gray-900">
                            R$ {{ number_format($vinyl->vinylSec->promotional_price, 2, ',', '.') }}
                        </p>
                    @else
                        <p class="text-base font-bold text-gray-900">
                            R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                        </p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="play-button inline-flex items-center p-2 text-white rounded-full focus:ring-4 focus:ring-blue-300 {{ $hasPlayableTracks ? 'bg-blue-700 hover:bg-blue-800' : 'bg-gray-400 cursor-not-allowed' }}"
                        data-vinyl-info="{{ json_encode([
                            'tracks' => $vinyl->tracks->map(function($track) {
                                return [
                                    'id' => $track->id,
                                    'name' => $track->name,
                                    'youtube_url' => $track->youtube_url
                                ];
                            }),
                            'artist' => $vinyl->artists->pluck('name')->implode(', '),
                            'coverUrl' => asset('storage/' . $vinyl->cover_image),
                            'vinylTitle' => $vinyl->title
                        ]) }}"
                        onclick="{{ $hasPlayableTracks ? 'initializeCardPlayer(this)' : '' }}"
                        {{ $hasPlayableTracks ? '' : 'disabled' }}
                        title="{{ $hasPlayableTracks ? 'Reproduzir' : 'Áudio não disponível' }}"
                    >
                        <i class="fas fa-play text-xs"></i>
                    </button>
                    <button
                        type="button"
                        title="{{ $vinyl->inWishlist() ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}"
                        class="wishlist-button text-gray-400 hover:text-red-500 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm p-2"
                        data-product-id="{{ $vinyl->id }}"
                        data-product-type="{{ get_class($vinyl) }}"
                        data-in-wishlist="{{ $vinyl->inWishlist() ? 'true' : 'false' }}"
                    >
                        <i class="fas fa-heart {{ $vinyl->inWishlist() ? 'text-red-500' : '' }}"></i>
                    </button>
                    <button
                        type="button"
                        class="add-to-cart-button inline-flex items-center p-2 text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 {{ !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}"
                        data-product-id="{{ $vinyl->product->id }}"
                        data-quantity="1"
                        {{ $isAvailable ? '' : 'disabled' }}
                        title="{{ $isAvailable ? 'Adicionar ao carrinho' : 'Produto indisponível' }}"
                    >
                        <i class="fas fa-shopping-cart"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função específica para inicializar o player no card
    function initializeCardPlayer(buttonElement) {
        try {
            const vinylData = JSON.parse(buttonElement.dataset.vinylInfo);

            // Filtra apenas tracks com URL do YouTube
            const validTracks = vinylData.tracks
                .filter(track => track.youtube_url)
                .map(track => ({
                    id: track.id,
                    name: track.name,
                    artist: vinylData.artist,
                    youtube_url: track.youtube_url,
                    cover_url: vinylData.coverUrl,
                    vinyl_title: vinylData.vinylTitle
                }));

            if (validTracks.length === 0) {
                alert("Não há faixas disponíveis para reprodução");
                return;
            }

            // Inicializa ou reutiliza o player global
            if (!window.audioPlayer) {
                window.audioPlayer = new AudioPlayer();
            }

            // Espera o player estar pronto
            function waitForPlayer() {
                if (window.audioPlayer.isReady) {
                    window.audioPlayer.loadPlaylist(validTracks);
                } else {
                    setTimeout(waitForPlayer, 100);
                }
            }

            waitForPlayer();

        } catch (error) {
            console.error("Erro ao processar dados do vinil:", error);
            alert("Erro ao iniciar a reprodução. Por favor, tente novamente.");
        }
    }

    // Adiciona o evento de click aos botões de play
    document.querySelectorAll('.play-button').forEach(button => {
        button.addEventListener('click', function() {
            initializeCardPlayer(this);
        });
    });
});
</script>
@endpush
