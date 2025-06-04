@props(['vinyl'])

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
                <span class="{{ ($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0 && $vinyl->vinylSec->in_stock == 1) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ ($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0 && $vinyl->vinylSec->in_stock == 1) ? 'Disponível' : 'Indisponível' }}
                </span>
                @if($vinyl->vinylSec && $vinyl->vinylSec->is_promotional && $vinyl->vinylSec->promotional_price && $vinyl->vinylSec->promotional_price > 0)
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                        Oferta
                    </span>
                @endif
            </div>
            <button
                type="button"
                class="play-button absolute bottom-2 right-2 inline-flex items-center justify-center w-8 h-8 text-white rounded-full focus:ring-4 focus:ring-blue-300 {{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'bg-blue-700 hover:bg-blue-800' : 'bg-gray-400 cursor-not-allowed' }}"
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
                {{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? '' : 'disabled' }}
                title="{{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'Reproduzir' : 'Áudio não disponível' }}"
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
                    @if($vinyl->vinylSec && $vinyl->vinylSec->is_promotional && $vinyl->vinylSec->promotional_price && $vinyl->vinylSec->promotional_price > 0)
                        <p class="text-xs text-gray-500 line-through">
                            R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                        </p>
                        <p class="text-lg font-bold text-gray-900">
                            R$ {{ number_format($vinyl->vinylSec->promotional_price, 2, ',', '.') }}
                        </p>
                    @elseif($vinyl->vinylSec)
                        <p class="text-lg font-bold text-gray-900">
                            R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                        </p>
                    @else
                        <p class="text-lg font-bold text-gray-900">Preço indisponível</p>
                    @endif
                </div>
                @php
                    $isAvailable = ($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0 && $vinyl->vinylSec->in_stock == 1);
                    $inWishlist = auth()->check() && $vinyl->inWishlist();
                    $inWantlist = auth()->check() && $vinyl->inWantlist();
                @endphp
                
                @if($isAvailable)
                <button
                    type="button"
                    title="{{ ($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0) ? (auth()->check() && $vinyl->inWishlist() ? 'Remover dos favoritos' : 'Adicionar aos favoritos') : (auth()->check() && $vinyl->inWantlist() ? 'Remover da wantlist' : 'Adicionar à wantlist') }}"
                    class="wishlist-button text-gray-400 hover:text-red-500 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm p-2"
                    data-product-id="{{ $vinyl->id }}"
                    data-product-type="{{ get_class($vinyl) }}"
                    data-is-available="{{ json_encode($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0) }}"
                    data-in-wishlist="{{ json_encode(auth()->check() && ($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0 ? $vinyl->inWishlist() : $vinyl->inWantlist())) }}"
                >
                    @if($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0)
                        <i class="fas fa-heart {{ auth()->check() && $vinyl->inWishlist() ? 'text-red-500' : '' }}"></i>
                    @else
                        <i class="fas fa-flag {{ auth()->check() && $vinyl->inWantlist() ? 'text-red-500' : '' }}"></i>
                    @endif
                </button>
                @else
                <button
                    type="button"
                    title="{{ $inWantlist ? 'Remover da lista de notificações' : 'Adicionar à lista de notificações' }}"
                    class="wantlist-button text-gray-400 hover:text-sky-500 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm p-2"
                    data-product-id="{{ $vinyl->id }}"
                    data-product-type="{{ get_class($vinyl) }}"
                    data-is-available="false"
                    data-in-wantlist="{{ json_encode($inWantlist) }}"
                    {{ !auth()->check() ? 'onclick="showLoginToast()"' : '' }}
                >
                    <i class="fas fa-bell {{ $inWantlist ? 'text-sky-500' : '' }}"></i>
                </button>
                @endif
            </div>
            <div class="mt-4">
                @if($vinyl->vinylSec->quantity > 0 && $vinyl->vinylSec->in_stock == 1)
                <button
                    type="button"
                    class="add-to-cart-button w-full text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5"
                    data-product-id="{{ $vinyl->product ? $vinyl->product->id : $vinyl->id }}"
                    data-quantity="1"
                >
                    <span class="add-to-cart-text">Adicionar ao Carrinho</span>
                </button>
                @else
                <button
                    type="button"
                    class="add-to-wantlist-button w-full text-sky-700 bg-white border border-sky-300 focus:outline-none hover:bg-sky-50 focus:ring-4 focus:ring-sky-200 font-medium rounded-lg text-sm px-5 py-2.5 {{ auth()->check() && $vinyl->inWantlist() ? 'bg-sky-50' : '' }}"
                    data-product-id="{{ $vinyl->id }}"
                    data-product-type="{{ get_class($vinyl) }}"
                    data-in-wantlist="{{ json_encode(auth()->check() && $vinyl->inWantlist()) }}"
                    {{ !auth()->check() ? 'onclick="showLoginToast()"' : '' }}
                >
                    <i class="fas fa-bell mr-2"></i>
                    <span class="wantlist-text">{{ auth()->check() && $vinyl->inWantlist() ? 'Você será notificado quando disponível' : 'Notifique-me quando disponível' }}</span>
                </button>
                @endif
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
                <span class="{{ ($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0 && $vinyl->vinylSec->in_stock == 1) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-medium px-2 py-0.5 rounded-full">
                    {{ ($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0 && $vinyl->vinylSec->in_stock == 1) ? 'Disponível' : 'Indisponível' }}
                </span>
                @if($vinyl->vinylSec && $vinyl->vinylSec->is_promotional && $vinyl->vinylSec->promotional_price && $vinyl->vinylSec->promotional_price > 0)
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
                    @if($vinyl->vinylSec && $vinyl->vinylSec->is_promotional && $vinyl->vinylSec->promotional_price && $vinyl->vinylSec->promotional_price > 0)
                        <p class="text-xs text-gray-500 line-through">
                            R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                        </p>
                        <p class="text-base font-bold text-gray-900">
                            R$ {{ number_format($vinyl->vinylSec->promotional_price, 2, ',', '.') }}
                        </p>
                    @elseif($vinyl->vinylSec)
                        <p class="text-base font-bold text-gray-900">
                            R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}
                        </p>
                    @else
                        <p class="text-base font-bold text-gray-900">Preço indisponível</p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="play-button inline-flex items-center p-2 text-white rounded-full focus:ring-4 focus:ring-blue-300 {{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'bg-blue-700 hover:bg-blue-800' : 'bg-gray-400 cursor-not-allowed' }}"
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
                        onclick="{{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'initializeCardPlayer(this)' : '' }}"
                        {{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? '' : 'disabled' }}
                        title="{{ $vinyl->tracks->contains(function($track) { return !empty($track->youtube_url); }) ? 'Reproduzir' : 'Áudio não disponível' }}"
                    >
                        <i class="fas fa-play text-xs"></i>
                    </button>
                    @php
                        $isAvailable = ($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0 && $vinyl->vinylSec->in_stock == 1);
                        $inWishlist = auth()->check() && $vinyl->inWishlist();
                        $inWantlist = auth()->check() && $vinyl->inWantlist();
                    @endphp
                    
                    @if($isAvailable)
                    <button
                        type="button"
                        title="{{ $inWishlist ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}"
                        class="wishlist-button text-gray-400 hover:text-red-500 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm p-2"
                        data-product-id="{{ $vinyl->id }}"
                        data-product-type="{{ get_class($vinyl) }}"
                        data-is-available="true"
                        data-in-wishlist="{{ json_encode($inWishlist) }}"
                    >
                        <i class="fas fa-heart {{ $inWishlist ? 'text-red-500' : '' }}"></i>
                    </button>
                    @else
                    <button
                        type="button"
                        title="{{ $inWantlist ? 'Remover da lista de notificações' : 'Adicionar à lista de notificações' }}"
                        class="wantlist-button text-gray-400 hover:text-sky-500 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm p-2"
                        data-product-id="{{ $vinyl->id }}"
                        data-product-type="{{ get_class($vinyl) }}"
                        data-is-available="false"
                        data-in-wantlist="{{ json_encode($inWantlist) }}"
                        {{ !auth()->check() ? 'onclick="showLoginToast()"' : '' }}
                    >
                        <i class="fas fa-bell {{ $inWantlist ? 'text-sky-500' : '' }}"></i>
                    </button>
                    @endif
                    @if($vinyl->vinylSec->quantity > 0 && $vinyl->vinylSec->in_stock == 1)
                    <button
                        type="button"
                        class="add-to-cart-button inline-flex items-center p-2 text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200"
                        data-product-id="{{ $vinyl->product ? $vinyl->product->id : $vinyl->id }}"
                        data-quantity="1"
                        title="Adicionar ao carrinho"
                    >
                        <i class="fas fa-shopping-cart"></i>
                        <span class="add-to-cart-text sr-only">Adicionar ao Carrinho</span>
                    </button>
                    @else
                    <button
                        type="button"
                        class="wantlist-button add-to-wantlist-button inline-flex items-center p-2 text-sky-700 bg-white border border-sky-300 rounded-lg hover:bg-sky-50 focus:ring-4 focus:ring-sky-200 {{ auth()->check() && $vinyl->inWantlist() ? 'bg-sky-50' : '' }}"
                        data-product-id="{{ $vinyl->id }}"
                        data-product-type="{{ get_class($vinyl) }}"
                        data-in-wantlist="{{ json_encode(auth()->check() && $vinyl->inWantlist()) }}"
                        data-auth="{{ json_encode(auth()->check()) }}"
                        title="{{ auth()->check() && $vinyl->inWantlist() ? 'Você já será notificado' : 'Notificar quando disponível' }}"
                    >
                        <i class="fas fa-bell"></i>
                        <span class="wantlist-text sr-only">Notificar quando disponível</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para mostrar alerta para erros de reprodução - Usa a função global showToast
    function showFlowbiteAlert(message, type = 'error') {
        // Usa a função global showToast se disponível
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
            return;
        }
        
        // Fallback caso a função global não esteja disponível
        console.warn('Função showToast não encontrada, usando alerta básico');
        alert(message);
    }

    // Adiciona eventos aos botões de play
    document.querySelectorAll('.play-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (this.disabled) return;

            try {
                const vinylData = JSON.parse(this.dataset.vinylInfo);
                if (!vinylData?.tracks?.length) {
                    throw new Error('Dados do vinyl inválidos');
                }

                // Formata as tracks para o player
                const tracks = vinylData.tracks
                    .filter(track => track.youtube_url)
                    .map(track => ({
                        id: track.id,
                        name: track.name,
                        artist: vinylData.artist,
                        youtube_url: track.youtube_url,
                        cover_url: vinylData.coverUrl
                    }));

                if (!tracks.length) {
                    throw new Error('Nenhuma faixa disponível para reprodução');
                }

                // Carrega a playlist no player
                if (window.audioPlayer) {
                    window.audioPlayer.loadPlaylist(tracks);
                }
            } catch (error) {
                console.error('Erro:', error);
                if (typeof window.showToast === 'function') {
                    window.showToast(error.message, 'error');
                } else {
                    console.warn('Função showToast não encontrada, usando alert básico');
                    alert(error.message);
                }
            }
        });
    });
});
</script>
@endpush
