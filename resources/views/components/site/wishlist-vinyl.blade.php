<div class="wishlist-item-card rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:p-6">
    @php
        $isAvailable = ($vinyl->vinylSec && $vinyl->vinylSec->quantity > 0 && $vinyl->vinylSec->in_stock == 1);
        $inWishlist = auth()->check() && $vinyl->inWishlist();
        $inWantlist = auth()->check() && $vinyl->inWantlist();
    @endphp
    <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
        <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}" class="shrink-0 md:order-1">
            <img
                src="{{ $vinyl->cover_image }}"
                alt="{{ $vinyl->title }} by {{ $vinyl->artists->pluck('name')->implode(', ') }}"
                class="sm:w-36 h-36 flex-shrink-0 object-cover object-center"
                onerror="this.src='https://placehold.co/400x400?text=Sem+imagem'"
            />
        </a>
        
        <div class="text-end md:order-4 md:w-32">
            @if($isAvailable)
                @if($vinyl->vinylSec->is_promotional == 1)
                    <p class="text-sm text-gray-500 line-through">R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</p>
                    <p class="text-lg font-bold text-red-600">R$ {{ number_format($vinyl->vinylSec->promotional_price, 2, ',', '.') }}</p>
                @else
                    <p class="text-lg font-bold">R$ {{ number_format($vinyl->vinylSec->price, 2, ',', '.') }}</p>
                @endif
                
                <p class="text-xs text-gray-500 mt-1">Em estoque: {{ $vinyl->vinylSec->quantity }}</p>
            @else
                <p class="text-lg font-bold text-red-500">Indisponível</p>
            @endif
        </div>

        <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
            <div>
                <h2 class="text-lg font-semibold line-clamp-1">
                    {{ $vinyl->artists->pluck('name')->implode(', ') }}
                </h2>
                <p class="text-sm text-gray-600">{{ $vinyl->title }}</p>
                <div class="mt-1">
                    <p class="text-sm text-gray-600">{{ $vinyl->recordLabel->name }} • {{ $vinyl->release_year }}</p>
                    <p class="text-sm text-gray-600">Faixas: {{ $vinyl->tracks->count() }}</p>
                </div>
            </div> 

            <div class="flex items-center gap-2 flex-wrap">
                @if($isAvailable)
                <button type="button" 
                    class="add-to-cart-btn inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                    data-product-id="{{ $vinyl->product ? $vinyl->product->id : $vinyl->id }}"
                    data-in-cart="false"
                >
                    <svg class="me-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="add-to-cart-text">Adicionar ao Carrinho</span>
                </button>
                @else
                <button type="button" 
                    class="wantlist-button inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm bg-sky-50 text-sky-700 hover:bg-sky-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 {{ $inWantlist ? 'bg-sky-100' : '' }}"
                    data-product-id="{{ $vinyl->id }}"
                    data-product-type="{{ get_class($vinyl) }}"
                    data-in-wantlist="{{ json_encode($inWantlist) }}"
                    {{ !auth()->check() ? 'onclick="showLoginToast()"' : '' }}
                >
                    <i class="fas fa-bell me-1.5 {{ $inWantlist ? 'text-sky-700' : '' }}"></i>
                    <span class="wantlist-text">{{ $inWantlist ? 'Você será notificado quando disponível' : 'Notifique-me quando disponível' }}</span>
                </button>
                @endif
                
                <button type="button" 
                    class="inline-flex items-center justify-center rounded-md bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 shadow-sm hover:bg-red-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 remove-from-wishlist"
                    data-product-id="{{ $vinyl->id }}"
                    data-product-type="{{ get_class($vinyl) }}"
                    {{ !auth()->check() ? 'onclick="showLoginToast()"' : '' }}
                >
                    <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6,18 17.94,6 M18,18 6.06,6" />
                    </svg>
                    Remover
                </button>
                
                <button type="button" 
                    class="inline-flex items-center justify-center rounded-md bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600 save-for-later"
                    data-product-id="{{ $vinyl->id }}"
                    data-vinyl-id="{{ $vinyl->id }}"
                    data-product-type="App\Models\VinylMaster"
                >
                    <svg class="me-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    Salvar para depois
                </button>
            </div>
        </div>
    </div>
</div>
