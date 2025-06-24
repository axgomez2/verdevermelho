<x-app-layout>
    <div class="font-sans p-4 bg-gray-100" x-data="{
        isFavorite: {{ $vinyl->inWishlist() ? 'true' : 'false' }},
        isInCart: {{ $vinyl->inCart() ? 'true' : 'false' }},
        quantity: 1,
        maxQuantity: {{ $vinyl->vinylSec ? $vinyl->vinylSec->quantity : 0 }},
        isInStock: {{ $vinyl->vinylSec && $vinyl->vinylSec->quantity > 0 && $vinyl->vinylSec->in_stock == 1 ? 'true' : 'false' }},
        currentImage: 0,
        images: [
            '{{ asset('storage/' . $vinyl->cover_image) }}'
            @if($vinyl->media && $vinyl->media->count() > 0)
                @foreach($vinyl->media as $media)
                    , '{{ asset('storage/' . $media->path) }}'
                @endforeach
            @endif
            @if($vinyl->images && is_array($vinyl->images))
                @foreach($vinyl->images as $image)
                    , '{{ $image }}'
                @endforeach
            @endif
        ],
        toggleFavorite() {
            @if(auth()->check())
                this.isFavorite = !this.isFavorite;
                fetch('/api/toggle-favorite', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        id: {{ $vinyl->id }},
                        type: 'App\\Models\\VinylMaster'
                    })
                });
            @else
                // Redirecionar para login ou exibir alerta
                alert('É necessário estar logado para adicionar aos favoritos.');
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 1000);
            @endif
        },
        addToCart() {
            @if(auth()->check())
                if (this.quantity > 0 && this.quantity <= this.maxQuantity) {
                    this.isInCart = true;
                    // Mostrar indicador de loading
                    const addButton = document.querySelector('.btn-add-to-cart');
                    if (addButton) {
                        addButton.classList.add('loading');
                        addButton.disabled = true;
                    }

                    // Enviar para a API
                    fetch('{{ route("site.cart.add") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({
                            product_id: {{ $vinyl->product->id ?? 0 }},
                            quantity: this.quantity
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Atualizar contador do carrinho no header se existir
                            const cartCounter = document.getElementById('cart-counter');
                            if (cartCounter) {
                                cartCounter.textContent = data.count;
                                cartCounter.classList.remove('hidden');
                            }
                            // Notificação de sucesso
                            alert('Item adicionado ao carrinho com sucesso!');
                        } else {
                            // Notificação de erro
                            alert('Erro ao adicionar item ao carrinho: ' + (data.message || 'Tente novamente mais tarde.'));
                            this.isInCart = false;
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao adicionar ao carrinho:', error);
                        alert('Erro ao adicionar ao carrinho. Por favor, tente novamente.');
                        this.isInCart = false;
                    })
                    .finally(() => {
                        // Remover indicador de loading
                        if (addButton) {
                            addButton.classList.remove('loading');
                            addButton.disabled = false;
                        }
                    });
                }
            @else
                // Redirecionar para login ou exibir alerta
                alert('É necessário estar logado para adicionar itens ao carrinho.');
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 1000);
            @endif
        },
        buyNow() {
            this.addToCart();
            // Redirecionar para checkout
            setTimeout(() => {
                window.location.href = '{{ route("site.checkout.index") }}';
            }, 300);
        },
        nextImage() {
            this.currentImage = (this.currentImage + 1) % this.images.length;
        },
        prevImage() {
            this.currentImage = (this.currentImage - 1 + this.images.length) % this.images.length;
        },
        setImage(index) {
            this.currentImage = index;
        }
    }"
    x-init="
        // Criar o objeto global para o player de áudio
        window.audioPlayer = {
            loadTrack: function(track) {
                // Verificar se o usuário está logado (opcional, dependendo da regra de negócio)
                @if(!auth()->check())
                    // Mostrar alerta sobre necessidade de login (opcional)
                    // alert('Faça login para ouvir as faixas!');
                @endif

                // Mostrar o player se tiver URL do YouTube
                if (!track.youtube_url) {
                    alert('Esta faixa não possui audiõvel disponível.');
                    return;
                }

                // Mostrar o player
                const player = document.getElementById('audio-player');
                if (player) {
                    player.classList.remove('hidden');

                    // Atualizar informações da faixa
                    const trackTitle = document.getElementById('track-title');
                    const trackTitleMobile = document.getElementById('track-title-mobile');
                    const trackArtist = document.getElementById('track-artist');
                    const trackArtistMobile = document.getElementById('track-artist-mobile');
                    const albumCover = document.getElementById('album-cover');
                    const albumCoverMobile = document.getElementById('album-cover-mobile');

                    if (trackTitle) trackTitle.textContent = track.name;
                    if (trackTitleMobile) trackTitleMobile.textContent = track.name;
                    if (trackArtist) trackArtist.textContent = track.artist;
                    if (trackArtistMobile) trackArtistMobile.textContent = track.artist;
                    if (albumCover) {
                        albumCover.src = track.cover_url;
                        albumCover.alt = track.vinyl_title;
                    }
                    if (albumCoverMobile) {
                        albumCoverMobile.src = track.cover_url;
                        albumCoverMobile.alt = track.vinyl_title;
                    }

                    // Disparar evento para informar o player que deve carregar essa faixa
                    const event = new CustomEvent('load-track', { detail: track });
                    document.dispatchEvent(event);

                    // Iniciar a reprodução automicamente
                    setTimeout(() => {
                        const playPauseBtn = document.getElementById('play-pause-btn');
                        if (playPauseBtn && playPauseBtn.classList.contains('playing')) {
                            // Já está tocando, não faz nada
                        } else if (playPauseBtn) {
                            playPauseBtn.click(); // Inicia a reprodução
                        }
                    }, 1000);
                }
            }
        };

        document.addEventListener('alpine:init', () => {
            Alpine.data('audioPlayer', audioPlayerData);
        });
    "
    >
        <div class="lg:max-w-6xl max-w-xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="grid items-start grid-cols-1 lg:grid-cols-2 gap-8 max-lg:gap-12 max-sm:gap-8">
                <div class="w-full sticky top-0">
                    <div class="flex flex-col gap-4">
                        <div class="bg-white shadow p-4 mt-6 relative">
                            <img :src="images[currentImage]" alt="{{ $vinyl->title }}"
                                class="w-full aspect-square object-cover object-center" />
                            <button @click="toggleFavorite"
                                    class="absolute top-6 right-6 p-2 bg-white rounded-full shadow-md hover:bg-gray-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="{ 'text-red-500 fill-current': isFavorite, 'text-gray-400': !isFavorite }" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                            <button @click="prevImage" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button @click="nextImage" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                        <div class="bg-white p-2 w-full max-w-full overflow-auto">
                            <div class="flex justify-between flex-row gap-4 shrink-0">
                                <template x-for="(image, index) in images" :key="index">
                                    <img :src="image" :alt="`Product ${index + 1}`"
                                        class="w-16 h-16 aspect-square object-cover object-top cursor-pointer shadow-md"
                                        :class="{ 'border-b-2 border-black': currentImage === index, 'border-b-2 border-transparent': currentImage !== index }"
                                        @click="setImage(index)" />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full">
                    <div class="mt-8">
                        <h3 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $vinyl->artists->pluck('name')->implode(', ') }}</h3>
                        <h3 class="text-xl sm:text-2xl text-gray-700 mt-2">{{ $vinyl->title }}</h3>
                        <div class="mt-4 space-y-2">
                            <p class="text-base text-gray-600"><span class="font-semibold">Label:</span> {{ $vinyl->recordLabel->name }}</p>
                            <p class="text-base text-gray-600"><span class="font-semibold">Ano:</span> {{ $vinyl->release_year }}</p>
                            <p class="text-base text-gray-600"><span class="font-semibold">País:</span> {{ $vinyl->country }}</p>
                        </div>

                        <div class="mt-6">
                            <h2 class="text-xl font-semibold mb-2">Gênero:</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach($vinyl->genres as $genre)
                                    <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">{{ $genre->name }}</span>
                                @endforeach
                                @foreach($vinyl->styles as $style)
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">{{ $style->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex items-center flex-wrap gap-2 mt-8">
                            @if($vinyl->vinylSec && $vinyl->vinylSec->is_promotional == 1 && $vinyl->vinylSec->promotional_price && $vinyl->vinylSec->promotional_price > 0)
                            <p class="text-gray-500 text-base"><strike>R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</strike></p>
                            <h4 class="text-stone-800 text-2xl sm:text-3xl font-bold">R$ {{ number_format($vinyl->vinylSec->promotional_price, 2, ',', '.') }}</h4>
                            <div class="flex py-1 px-2 bg-amber-600 font-semibold ml-4">
                                <span class="text-white text-sm">oferta</span>
                            </div>
                            @elseif($vinyl->vinylSec)
                            <h4 class="text-stone-800 text-2xl sm:text-3xl font-bold">R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</h4>
                            @else
                            <h4 class="text-stone-800 text-2xl sm:text-3xl font-bold">Preço indisponível</h4>
                            @endif
                        </div>

                        <div class="mt-8 mr-4 flex flex-wrap gap-4">
                            <div class="w-full flex justify-between items-center mb-4" x-show="isInStock">
                                <span class="text-green-600 text-sm font-medium">Em estoque: <span x-text="maxQuantity"></span> unidades disponíveis</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-gray-700">Quantidade:</span>
                                    <div class="flex border border-gray-300 rounded overflow-hidden">
                                        <button type="button" @click="quantity > 1 ? quantity-- : null"
                                            class="px-2 py-1 bg-gray-100 hover:bg-gray-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <input type="number" x-model.number="quantity" min="1" :max="maxQuantity"
                                            class="w-12 text-center border-0 focus:ring-0"
                                            @change="quantity < 1 ? quantity = 1 : (quantity > maxQuantity ? quantity = maxQuantity : null)">
                                        <button type="button" @click="quantity < maxQuantity ? quantity++ : null"
                                            class="px-2 py-1 bg-gray-100 hover:bg-gray-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full py-2" x-show="!isInStock">
                                <button
                                    id="add-to-wantlist-button"
                                    type="button"
                                    class="w-full text-sky-700 bg-white border border-sky-300 focus:outline-none hover:bg-sky-50 focus:ring-4 focus:ring-sky-200 font-medium rounded-lg text-sm px-5 py-2.5 {{ auth()->check() && $vinyl->inWantlist() ? 'bg-sky-50' : '' }}"
                                    data-product-id="{{ $vinyl->id }}"
                                    data-product-type="{{ get_class($vinyl) }}"
                                    data-in-wantlist="{{ json_encode(auth()->check() && $vinyl->inWantlist()) }}"
                                    {{ !auth()->check() ? 'onclick="showLoginToast()"' : '' }}
                                >
                                    <i class="fas fa-bell mr-2"></i>
                                    <span class="wantlist-text">
                                        {{ auth()->check() && $vinyl->inWantlist() ? 'Você será notificado quando disponível' : 'Notifique-me quando disponível' }}
                                    </span>
                                </button>
                            </div>

                            <button type="button"
                                class="btn btn-outline btn-primary flex-grow btn-add-to-cart"
                                @click="addToCart()"
                                :disabled="!isInStock || isInCart"
                                :class="{'opacity-50 cursor-not-allowed': !isInStock || isInCart}"
                            >
                                <span x-text="isInCart ? 'Adicionado ao carrinho' : 'Adicionar ao carrinho'"></span>
                            </button>

                            <button type="button"
                                class="btn btn-primary flex-grow"
                                @click="buyNow()"
                                :disabled="!isInStock"
                                :class="{'opacity-50 cursor-not-allowed': !isInStock}"
                            >
                                Comprar agora
                            </button>
                        </div>
                        @if($vinyl->description)
                            <div class="mt-8">
                                <h2 class="text-2xl font-bold mb-4">Descrição</h2>
                                <p class="text-gray-700">{{ $vinyl->description }}</p>
                            </div>
                        @endif



                        <hr class="my-8 border-gray-300" />

                        <h2 class="text-2xl font-bold mb-4">LISTA DE FAIXAS:</h2>
                        <div class="overflow-x-auto">
                            <table class="table w-full">
                                <thead>
                                    <tr>
                                        <th class="text-left">#</th>
                                        <th class="text-left">Faixa</th>
                                        <th class="text-left">Duração</th>
                                        <th class="text-left">##</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vinyl->tracks as $track)
                                        <tr class="hover:bg-gray-100">
                                            <td>{{ $track->position }}</td>
                                            <td>{{ $track->name }}</td>
                                            <td>{{ $track->duration ?? 'N/A' }}</td>
                                            <td>


                                                @if($track->youtube_url)
                                                <button
                                                class="btn btn-sm btn-circle btn-ghost track-play-button play-track-btn"
                                                title="Reproduzir faixa"
                                                data-track-id="{{ $track->id }}"
                                                data-track-name="{{ $track->name }}"
                                                data-track-artist="{{ $vinyl->artists->pluck('name')->implode(', ') }}"
                                                data-track-url="{{ $track->youtube_url }}"
                                                data-track-cover="{{ asset('storage/' . $vinyl->cover_image) }}"
                                                data-track-vinyl="{{ $vinyl->title }}"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-500 bg-gray-100 rounded">Áudio indisponível</span>
                                    @endif



                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>



                        <hr class="my-8 border-gray-300" />


                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Recomendações -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Recomendações do mesmo artista -->
        @php
            // Buscar discos do mesmo artista
            $sameArtistVinyls = \App\Models\VinylMaster::whereHas('artists', function($query) use ($vinyl) {
                $query->whereIn('artists.id', $vinyl->artists->pluck('id'));
            })
            ->where('id', '!=', $vinyl->id)
            ->with(['artists', 'vinylSec', 'product'])
            ->inRandomOrder()
            ->take(4)
            ->get();

            // Buscar discos da mesma categoria
            $categoryIds = $vinyl->catStyleShops->pluck('id')->toArray();
            $sameCategoryVinyls = [];

            if (!empty($categoryIds)) {
                $sameCategoryVinyls = \App\Models\VinylMaster::whereHas('catStyleShops', function($query) use ($categoryIds) {
                    $query->whereIn('cat_style_shop_id', $categoryIds);
                })
                ->where('id', '!=', $vinyl->id)
                ->whereNotIn('id', $sameArtistVinyls->pluck('id')->toArray())
                ->with(['artists', 'vinylSec', 'product'])
                ->inRandomOrder()
                ->take(4)
                ->get();
            }
        @endphp

                @if($sameArtistVinyls->count() > 0)
        <div class="mb-10">
            <h2 class="text-2xl font-bold mb-6">Mais de {{ $vinyl->artists->pluck('name')->implode(', ') }}</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($sameArtistVinyls as $relatedVinyl)
                <div class="bg-white rounded-lg shadow-md overflow-hidden h-full flex flex-col">
                    <a href="{{ route('site.vinyl.show', ['artistSlug' => $relatedVinyl->artists->first()->slug ?? 'artista', 'titleSlug' => $relatedVinyl->slug]) }}" class="block overflow-hidden">
                        <div class="relative aspect-square">
                            <img src="{{ asset('storage/' . $relatedVinyl->cover_image) }}"
                                alt="{{ $relatedVinyl->title }}"
                                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">

                            @if(!$relatedVinyl->vinylSec || $relatedVinyl->vinylSec->quantity <= 0 || $relatedVinyl->vinylSec->in_stock != 1)
                            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">Esgotado</span>
                            @endif

                            @if($relatedVinyl->vinylSec && $relatedVinyl->vinylSec->is_promotional == 1)
                            <span class="absolute top-2 left-2 bg-amber-500 text-white text-xs font-bold px-2 py-1 rounded">Promoção</span>
                            @endif
                        </div>
                    </a>

                    <div class="p-4 flex-grow flex flex-col">
                        <h3 class="font-semibold text-lg mb-1 line-clamp-1">
                            <a href="{{ route('site.vinyl.show', ['artistSlug' => $relatedVinyl->artists->first()->slug ?? 'artista', 'titleSlug' => $relatedVinyl->slug]) }}" class="hover:text-blue-600 transition-colors">
                                {{ $relatedVinyl->artists->pluck('name')->implode(', ') }}
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-2 line-clamp-1">{{ $relatedVinyl->title }}</p>

                        <div class="mt-auto">
                            @if($relatedVinyl->vinylSec && $relatedVinyl->vinylSec->price > 0)
                                @if($relatedVinyl->vinylSec->is_promotional == 1 && $relatedVinyl->vinylSec->promotional_price && $relatedVinyl->vinylSec->promotional_price > 0)
                                <p class="text-gray-500 line-through text-sm">R$ {{ number_format($relatedVinyl->vinylSec->price, 2, ',', '.') }}</p>
                                <p class="text-lg font-bold">R$ {{ number_format($relatedVinyl->vinylSec->promotional_price, 2, ',', '.') }}</p>
                                @else
                                <p class="text-lg font-bold">R$ {{ number_format($relatedVinyl->vinylSec->price, 2, ',', '.') }}</p>
                                @endif
                            @else
                                <p class="text-gray-500">Preço indisponível</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

                @if($sameCategoryVinyls && $sameCategoryVinyls->count() > 0)
        <div class="mb-10">
            <h2 class="text-2xl font-bold mb-6">Você pode gostar também</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($sameCategoryVinyls as $relatedVinyl)
                <div class="bg-white rounded-lg shadow-md overflow-hidden h-full flex flex-col">
                    <a href="{{ route('site.vinyl.show', ['artistSlug' => $relatedVinyl->artists->first()->slug ?? 'artista', 'titleSlug' => $relatedVinyl->slug]) }}" class="block overflow-hidden">
                        <div class="relative aspect-square">
                            <img src="{{ asset('storage/' . $relatedVinyl->cover_image) }}"
                                alt="{{ $relatedVinyl->title }}"
                                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">

                            @if(!$relatedVinyl->vinylSec || $relatedVinyl->vinylSec->quantity <= 0 || $relatedVinyl->vinylSec->in_stock != 1)
                            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">Esgotado</span>
                            @endif

                            @if($relatedVinyl->vinylSec && $relatedVinyl->vinylSec->is_promotional == 1)
                            <span class="absolute top-2 left-2 bg-amber-500 text-white text-xs font-bold px-2 py-1 rounded">Promoção</span>
                            @endif
                        </div>
                    </a>

                    <div class="p-4 flex-grow flex flex-col">
                        <h3 class="font-semibold text-lg mb-1 line-clamp-1">
                            <a href="{{ route('site.vinyl.show', ['artistSlug' => $relatedVinyl->artists->first()->slug ?? 'artista', 'titleSlug' => $relatedVinyl->slug]) }}" class="hover:text-blue-600 transition-colors">
                                {{ $relatedVinyl->artists->pluck('name')->implode(', ') }}
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-2 line-clamp-1">{{ $relatedVinyl->title }}</p>

                        <div class="mt-auto">
                            @if($relatedVinyl->vinylSec && $relatedVinyl->vinylSec->price > 0)
                                @if($relatedVinyl->vinylSec->is_promotional == 1 && $relatedVinyl->vinylSec->promotional_price && $relatedVinyl->vinylSec->promotional_price > 0)
                                <p class="text-gray-500 line-through text-sm">R$ {{ number_format($relatedVinyl->vinylSec->price, 2, ',', '.') }}</p>
                                <p class="text-lg font-bold">R$ {{ number_format($relatedVinyl->vinylSec->promotional_price, 2, ',', '.') }}</p>
                                @else
                                <p class="text-lg font-bold">R$ {{ number_format($relatedVinyl->vinylSec->price, 2, ',', '.') }}</p>
                                @endif
                            @else
                                <p class="text-gray-500">Preço indisponível</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Adicionar handler para o botão de wantlist
            const wantlistButton = document.getElementById('add-to-wantlist-button');
            if (wantlistButton) {
                wantlistButton.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const productType = this.dataset.productType;
                    const isInWantlist = this.dataset.inWantlist === 'true';

                    // Se o usuário não estiver logado, o atributo onclick já lidará com isso
                    if (!this.getAttribute('onclick')) {
                        toggleWantlistItem(productId, productType, isInWantlist, this);
                    }
                });
            }

            // Função para alternar item na wantlist
            function toggleWantlistItem(productId, productType, isInWantlist, button) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('/wantlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        product_type: productType
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualizar o atributo data-in-wantlist
                        button.dataset.inWantlist = data.in_wantlist.toString();

                        // Atualizar classes de estilo
                        if (data.in_wantlist) {
                            button.classList.add('bg-sky-50');
                        } else {
                            button.classList.remove('bg-sky-50');
                        }

                        // Atualizar o texto
                        const textSpan = button.querySelector('.wantlist-text');
                        if (textSpan) {
                            textSpan.textContent = data.in_wantlist
                                ? 'Você será notificado quando disponível'
                                : 'Notifique-me quando disponível';
                        }

                        // Mostrar mensagem
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro ao alternar item na wantlist:', error);
                    alert('Erro ao processar sua solicitação. Tente novamente.');
                });
            }

            // Função para mostrar mensagem de login necessário
            window.showLoginToast = function() {
                alert('É necessário estar logado para adicionar à lista de notificações.');
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 1000);
            };
            // Encontrar todos os botões de play
            const playButtons = document.querySelectorAll('.play-track-btn');

            // Adicionar evento de clique a cada botão
            playButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Obter dados da faixa
                    const trackData = {
                        id: this.dataset.trackId,
                        name: this.dataset.trackName,
                        artist: this.dataset.trackArtist,
                        youtube_url: this.dataset.trackUrl,
                        cover_url: this.dataset.trackCover,
                        vinyl_title: this.dataset.trackVinyl
                    };

                    // Tentar diferentes métodos para iniciar o player
                    try {
                        // Tentar o objeto audioPlayer global
                        if (window.audioPlayer && typeof window.audioPlayer.loadTrack === 'function') {
                            window.audioPlayer.loadTrack(trackData);
                        }
                        // Tentar com o classe AudioPlayer
                        else if (window.AudioPlayer) {
                            // Se a instância ainda não existe, criar uma
                            if (!window.audioPlayer) {
                                window.audioPlayer = new AudioPlayer();
                            }
                            setTimeout(() => {
                                window.audioPlayer.loadTrack(trackData);
                            }, 500);
                        }
                        // Método de fallback - mostrar o player e disparar evento
                        else {
                            console.log('Usando método de fallback para o player');
                            const player = document.getElementById('audio-player');
                            if (player) {
                                player.classList.remove('hidden');
                                // Disparar evento customizado para o player
                                const event = new CustomEvent('load-track', {
                                    detail: trackData
                                });
                                document.dispatchEvent(event);
                            }
                        }
                    } catch (error) {
                        console.error('Erro ao iniciar o player:', error);
                        alert('Não foi possível reproduzir esta faixa. Tente novamente mais tarde.');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>