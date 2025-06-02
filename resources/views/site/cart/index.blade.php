<x-app-layout>
    <section class="bg-white py-8 antialiased md:py-16">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <h2 class="text-xl font-semibold text-gray-900 sm:text-2xl">Carrinho de Compras</h2>

            <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                <div class="mx-auto w-full lg:w-7/12 flex-none">
                    <div class="space-y-6">
                        <!-- Itens disponíveis -->
                        @foreach($availableItems as $item)
                            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:p-6">

                                <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0 flex-wrap">
                                    <a href="#" class="shrink-0 md:order-1">
                                        @php
                                            // Tentar encontrar o caminho correto da imagem
                                            $coverImage = null;
                                            
                                            // Verificar se temos vinylSec com cover_image
                                            if (isset($item->product) && isset($item->product->productable) && 
                                                isset($item->product->productable->vinylSec) && 
                                                !empty($item->product->productable->vinylSec->cover_image)) {
                                                
                                                $coverImage = asset('storage/' . $item->product->productable->vinylSec->cover_image);
                                            }
                                            // Verificar se temos vinyl com cover_image diretamente
                                            elseif (isset($item->product) && isset($item->product->productable) && 
                                                   !empty($item->product->productable->cover_image)) {
                                                
                                                $coverImage = asset('storage/' . $item->product->productable->cover_image);
                                            }
                                            // Verificar se temos vinylMaster com cover_image
                                            elseif (isset($item->product) && isset($item->product->productable) && 
                                                   isset($item->product->productable->vinylMaster) && 
                                                   !empty($item->product->productable->vinylMaster->cover_image)) {
                                                
                                                $coverImage = asset('storage/' . $item->product->productable->vinylMaster->cover_image);
                                            }
                                            // Caso não encontre nenhuma imagem, usar placeholder
                                            else {
                                                $coverImage = asset('assets/images/placeholder.jpg');
                                            }
                                        @endphp
                                        
                                        <img class="h-20 w-20 object-cover" 
                                            src="{{ $coverImage }}" 
                                            alt="{{ $item->product->productable->title ?? 'Produto' }}">

                                <!-- Formulário oculto para atualização via AJAX -->
                                <form id="update-form-{{ $item->id }}" action="{{ route('site.cart.updateQuantity', ['cartItem' => $item->id]) }}" method="POST" class="hidden">
                                    @csrf
                                    <input type="hidden" name="quantity" value="{{ $item->quantity }}">
                                </form>
                                
                                <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                                    <a href="{{ route('site.vinyl.show', ['artistSlug' => $item->product->productable->artists->first()->slug ?? 'artista', 'titleSlug' => $item->product->productable->slug ?? 'produto']) }}" class="shrink-0 md:order-1">
                                        <div class="relative h-20 w-20 overflow-hidden rounded-md">
                                            <img 
                                                class="h-full w-full object-cover object-center hover:scale-110 transition-transform duration-300" 
                                                src="{{ asset('storage/' . $item->product->productable->cover_image) }}"
                                                alt="{{ $item->product->productable->title }} {{ $item->product->productable->artists ? 'by ' . $item->product->productable->artists->pluck('name')->implode(', ') : '' }}"
                                                onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'"
                                            >
                                            <div class="absolute top-1 left-1">
                                                <span class="{{ ($item->product->productable->vinylSec->quantity > 0 && $item->product->productable->vinylSec->in_stock == 1) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-medium px-1.5 py-0.5 rounded-full">
                                                    {{ $item->product->productable->vinylSec->quantity }} un.
                                                </span>
                                            </div>
                                        </div>

                                    </a>

                                    <label for="counter-input-{{ $item->id }}" class="sr-only">Escolher quantidade:</label>
                                    <div class="flex items-center justify-between md:order-3 md:justify-end">
                                        <div class="flex items-center">
                                            @php
                                                // Obter a quantidade disponível em estoque
                                                $stockQuantity = 0;
                                                if (isset($item->product) && isset($item->product->productable) && 
                                                    isset($item->product->productable->vinylSec) && 
                                                    isset($item->product->productable->vinylSec->quantity)) {
                                                    $stockQuantity = $item->product->productable->vinylSec->quantity;
                                                }
                                            @endphp
                                            <button type="button" data-item-id="{{ $item->id }}" class="decrement-button inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                                <svg class="h-2.5 w-2.5 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>

                                            <input type="text" 
                                                id="counter-input-{{ $item->id }}" 
                                                data-item-id="{{ $item->id }}" 
                                                data-max-quantity="{{ $stockQuantity }}" 
                                                class="quantity-input w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0" 
                                                value="{{ $item->quantity }}" 
                                                min="1" 
                                                max="{{ $stockQuantity }}" 
                                                required 
                                            />
                                            <button type="button" 
                                                data-item-id="{{ $item->id }}" 
                                                data-max-quantity="{{ $stockQuantity }}" 
                                                class="increment-button inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 {{ $item->quantity >= $stockQuantity ? 'opacity-50 cursor-not-allowed' : '' }}">

                                            <input type="text" id="counter-input-{{ $item->id }}" 
                                                   data-item-id="{{ $item->id }}" 
                                                   data-stock="{{ $item->product->productable->vinylSec->quantity }}" 
                                                   class="quantity-input w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0" 
                                                   value="{{ $item->quantity }}" required />
                                            <button type="button" 
                                                    data-item-id="{{ $item->id }}" 
                                                    data-stock="{{ $item->product->productable->vinylSec->quantity }}" 
                                                    class="increment-button inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 {{ $item->quantity >= $item->product->productable->vinylSec->quantity ? 'opacity-50 cursor-not-allowed' : '' }}">

                                                <svg class="h-2.5 w-2.5 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                            @if($stockQuantity > 0)
                                                <span class="ml-2 text-xs text-gray-500">{{ $stockQuantity }} em estoque</span>
                                            @else
                                                <span class="ml-2 text-xs text-red-500">Indisponível</span>
                                            @endif
                                        </div>
                                        <div class="text-end md:order-4 md:w-32">
                                            <p class="text-base font-bold text-gray-900" id="item-total-{{ $item->id }}">R$ {{ number_format($item->product->price * $item->quantity, 2, ',', '.') }}</p>
                                        </div>
                                    </div>

                                    <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
                                        <a href="{{ route('site.vinyl.show', ['artistSlug' => $item->product->productable->artists->first()->slug ?? 'artista', 'titleSlug' => $item->product->productable->slug ?? 'produto']) }}" class="hover:underline">
                                            <h5 class="text-base font-semibold tracking-tight text-gray-900 line-clamp-1">
                                                {{ $item->product->productable->artists->pluck('name')->implode(', ') }}
                                            </h5>
                                            <p class="text-sm text-gray-500 line-clamp-1">{{ $item->product->productable->title }}</p>
                                        </a>
                                        <p class="text-sm {{ $item->product->productable->vinylSec->quantity < 5 ? 'text-orange-600 font-medium' : 'text-gray-500' }}">
                                            Estoque disponível: {{ $item->product->productable->vinylSec->quantity }} unidade(s)
                                        </p>

                                        <div class="flex items-center gap-4">
                                            <form action="{{ route('site.cart.items.destroy', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center text-sm font-medium text-red-600 hover:underline">
                                                    Remover
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <!-- Itens indisponíveis -->
                        @if($unavailableItems->count() > 0)
                            <div class="mt-8">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Itens indisponíveis ou sem estoque</h3>
                                <div class="space-y-6">
                                    @foreach($unavailableItems as $item)
                                        <div class="rounded-lg border border-red-200 bg-red-50 p-4 shadow-sm md:p-6">
                                            <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                                                <a href="{{ $item->product && $item->product->productable ? route('site.vinyl.show', ['artistSlug' => $item->product->productable->artists->first()->slug ?? 'artista', 'titleSlug' => $item->product->productable->slug ?? 'produto']) : '#' }}" class="shrink-0 md:order-1">
                                                    <div class="relative h-20 w-20 overflow-hidden rounded-md">
                                                        <img 
                                                            class="h-full w-full object-cover object-center opacity-50" 
                                                            src="{{ $item->product && $item->product->productable ? asset('storage/' . $item->product->productable->cover_image) : asset('assets/images/placeholder.jpg') }}"
                                                            alt="{{ $item->product && $item->product->productable ? $item->product->productable->title : 'Produto indisponível' }}"
                                                            onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'"
                                                        >
                                                        <div class="absolute top-1 left-1">
                                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-1.5 py-0.5 rounded-full">
                                                                Indisponível
                                                            </span>
                                                        </div>
                                                    </div>
                                                </a>
                                                
                                                <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
                                                    <div>
                                                        @if($item->product && $item->product->productable && $item->product->productable->artists)
                                                            <h5 class="text-base font-semibold tracking-tight text-gray-700 line-clamp-1 opacity-70">
                                                                {{ $item->product->productable->artists->pluck('name')->implode(', ') }}
                                                            </h5>
                                                            <p class="text-sm text-gray-500 line-clamp-1">{{ $item->product->productable->title }}</p>
                                                        @else
                                                            <p class="text-base font-medium text-gray-900">{{ $item->product->productable->title ?? 'Produto indisponível' }}</p>
                                                        @endif
                                                        <p class="text-sm text-red-600 font-medium mt-1">
                                                            @if(!$item->product || !$item->product->productable || !$item->product->productable->vinylSec)
                                                                Produto indisponível
                                                            @elseif($item->product->productable->vinylSec->in_stock == 0)
                                                                Produto indisponível para compra
                                                            @else
                                                                Sem estoque
                                                            @endif
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="flex items-center gap-4">
                                                        <form action="{{ route('site.cart.items.destroy', $item->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex items-center text-sm font-medium text-red-600 hover:underline">
                                                                Remover
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mx-auto mt-6 flex-1 space-y-6 lg:mt-0 lg:w-5/12">
                    <!-- Endereço de Entrega -->                    
                    <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-semibold text-gray-900">Endereço de Entrega</p>
                            <button type="button" onclick="openAddressModal()" class="text-sm font-medium text-primary-600 hover:underline">
                                <i class="fas fa-edit mr-1"></i> {{ is_object($address) ? 'Alterar' : 'Adicionar' }}
                            </button>
                        </div>
                        
                        @if(is_object($address))
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="font-medium text-gray-800">{{ $address->street }}, {{ $address->number }}</p>
                                <p class="text-gray-600">{{ $address->neighborhood }} - {{ $address->city }}/{{ $address->state }}</p>
                                <p class="text-gray-600">CEP: {{ $address->zip_code }}</p>
                                @if($address->complement)
                                    <p class="text-gray-600">{{ $address->complement }}</p>
                                @endif
                            </div>
                        @else
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                                <i class="fas fa-map-marker-alt text-gray-400 text-2xl mb-2"></i>
                                <p class="text-gray-600">Nenhum endereço selecionado</p>
                                <button type="button" onclick="openAddressModal()" class="mt-2 text-sm font-medium text-primary-600 hover:underline">
                                    Adicionar endereço para continuar
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Cálculo de Frete -->
                    <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                        <p class="text-lg font-semibold text-gray-900">Opções de Frete</p>
                        
                        @if(is_object($address) || session('shipping_postal_code'))
                            @php
                                if ($address) {
                                    if (is_object($address)) {
                                        $postalCode = trim($address->zip_code);
                                    } else {
                                        $postalCode = trim($address);
                                    }
                                } else {
                                    $postalCode = session('shipping_postal_code');
                                }
                            @endphp

                            @if(isset($shippingOptions) && count($shippingOptions) > 0)
                                <div class="space-y-3">
                                    @foreach($shippingOptions as $option)
                                        <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                            <input 
                                                type="radio" 
                                                id="shipping_option_{{ $option['id'] }}" 
                                                name="shipping_option" 
                                                value="{{ $option['id'] }}" 
                                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                                                {{ $loop->first ? 'checked' : '' }}
                                            >
                                            <label for="shipping_option_{{ $option['id'] }}" class="ml-3 flex flex-1 justify-between">
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $option['name'] }}</p>
                                                    <p class="text-sm text-gray-500">Entrega em até {{ $option['delivery_time'] }} dias úteis</p>
                                                </div>
                                                <p class="font-medium text-gray-900">R$ {{ number_format($option['price'], 2, ',', '.') }}</p>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                                    <div class="animate-pulse">
                                        <i class="fas fa-truck text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-gray-600">Calculando opções de frete...</p>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                                <i class="fas fa-truck text-gray-400 text-2xl mb-2"></i>
                                <p class="text-gray-600">Adicione um endereço de entrega para ver as opções de frete</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Resumo do Pedido -->                    
                    <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                        <p class="text-lg font-semibold text-gray-900">Resumo do Pedido</p>

                        <div class="space-y-4">
                            <div class="space-y-3">
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-base font-normal text-gray-500">Subtotal</dt>
                                    <dd class="text-base font-medium text-gray-900" id="cart-subtotal">R$ {{ number_format($subtotal, 2, ',', '.') }}</dd>
                                </dl>

                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-base font-normal text-gray-500">Frete</dt>
                                    <dd class="text-base font-medium text-gray-900">
                                        @if(isset($shippingOptions) && count($shippingOptions) > 0)
                                            R$ <span id="shipping-price">{{ number_format($shippingOptions[0]['price'] ?? 0, 2, ',', '.') }}</span>
                                        @else
                                            --
                                        @endif
                                    </dd>
                                </dl>
                            </div>

                            <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-3">
                                <dt class="text-base font-bold text-gray-900">Total</dt>
                                <dd class="text-base font-bold text-gray-900" id="cart-total">R$ {{ number_format($total, 2, ',', '.') }}</dd>
                            </dl>
                        </div>

                        @if(isset($isLoggedIn) && $isLoggedIn)
                            @if(is_object($address) && isset($shippingOptions) && count($shippingOptions) > 0)
                                <div id="checkout-buttons-container" class="space-y-3 pt-2">
                                    <a href="{{ route('site.checkout.index') }}" class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-3 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300">
                                        <i class="fas fa-credit-card mr-2"></i> Finalizar Compra
                                    </a>
                                    
                                    <button 
                                        type="button" 
                                        id="whatsapp-checkout" 
                                        data-whatsapp-number="5511947495050" {{-- Substitua pelo número real da loja --}}
                                        class="flex w-full items-center justify-center rounded-lg bg-green-600 px-5 py-3 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300"
                                    >
                                        <i class="fab fa-whatsapp mr-2"></i> Finalizar compra pelo WhatsApp
                                    </button>
                                </div>
                            @else
                                <div id="checkout-warning" class="bg-yellow-50 p-3 rounded-lg mt-3 border border-yellow-200">
                                    <p class="text-yellow-700 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i> Complete as informações acima para finalizar sua compra</p>
                                </div>
                                <div id="checkout-buttons-container" class="space-y-3 pt-2 hidden">
                                    <a href="{{ route('site.checkout.index') }}" class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-3 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300">
                                        <i class="fas fa-credit-card mr-2"></i> Finalizar Compra
                                    </a>
                                    
                                    <button 
                                        type="button" 
                                        id="whatsapp-checkout" 
                                        data-whatsapp-number="5511999999999"
                                        class="flex w-full items-center justify-center rounded-lg bg-green-600 px-5 py-3 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300"
                                    >
                                        <i class="fab fa-whatsapp mr-2"></i> Finalizar compra pelo WhatsApp
                                    </button>
                                </div>
                            @endif
                        @else
                            <div class="border-t border-gray-200 pt-4 pb-2 mb-2">
                                <div class="text-center mb-4">
                                    <div class="text-lg font-medium text-gray-900 mb-2">Faça login para finalizar sua compra</div>
                                    <p class="text-sm text-gray-600 mb-4">Seus itens serão preservados quando você fizer login</p>
                                    
                                    <button type="button" 
                                        onclick="window.dispatchEvent(new CustomEvent('open-login-modal'))"
                                        class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 mb-3">
                                        <i class="fas fa-user-circle mr-2"></i> Fazer Login
                                    </button>
                                    
                                    <button type="button" 
                                        onclick="window.dispatchEvent(new CustomEvent('open-register-modal'))"
                                        class="flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200">
                                        <i class="fas fa-user-plus mr-2"></i> Criar Nova Conta
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-center gap-2">
                            <span class="text-sm font-normal text-gray-500">ou</span>
                            <a href="{{ route('site.home') }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 underline hover:no-underline">
                                Continuar Comprando
                                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div id="shipping-calculator" class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900">Calculadora de Frete</h3>
                        
                        <x-shipping-calculator :shippingOptions="$shippingOptions ?? []" :postalCode="session('shipping_postal_code')" />
                    </div>
                </div>
            </div>
        </div>
        @include('profile.partials.address-modal')
    </section>

    @push('scripts')
    <script>
        // Função para atualizar o preço total com base na opção de frete selecionada
        function updateTotalPrice() {
            const selectedShipping = document.querySelector('input[name="shipping_option"]:checked');
            if (!selectedShipping) return;
            
            // Mostra indicador de carregamento
            const shippingPriceDisplay = document.getElementById('shipping-price');
            shippingPriceDisplay.innerHTML = '<span class="animate-pulse">atualizando...</span>';
            
            // Atualiza no servidor
            fetch('{{ route("site.cart.updateShipping") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    shipping_option: selectedShipping.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Usa o valor retornado diretamente do servidor em vez de tentar extrair do HTML
                    const shippingPrice = parseFloat(data.shipping);
                    
                    // Formata o preço para exibição
                    const formattedPrice = shippingPrice.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    
                    // Atualiza o preço de frete exibido
                    shippingPriceDisplay.textContent = formattedPrice;
                    
                    // Calcula o novo total
                    const subtotal = {{ $subtotal }};
                    const newTotal = subtotal + shippingPrice;
                    
                    // Atualiza o total exibido
                    const totalElement = document.querySelector('dl:last-child dd');
                    totalElement.textContent = `R$ ${newTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                    
                    // Atualiza os botões de finalização se necessário
                    const checkoutContainer = document.getElementById('checkout-buttons-container');
                    if (checkoutContainer && checkoutContainer.classList.contains('hidden')) {
                        checkoutContainer.classList.remove('hidden');
                        
                        // Remove a mensagem de aviso se existir
                        const warningElement = document.querySelector('.bg-yellow-50');
                        if (warningElement) {
                            warningElement.remove();
                        }
                    }
                } else {
                    // Mostrar erro
                    shippingPriceDisplay.textContent = '--';
                    console.error('Erro ao atualizar frete:', data.error);
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                shippingPriceDisplay.textContent = '--';
            });
        }
        
        // Função para enviar pedido via WhatsApp
        function formatCartForWhatsApp() {
            // Obter informações do cliente
            const clientName = "{{ Auth::user() ? Auth::user()->name : 'Cliente' }}";
            const clientEmail = "{{ Auth::user() ? Auth::user()->email : '' }}";
            
            // Obter informações do endereço
            @if(is_object($address))
            const addressStreet = "{{ $address->street }}";
            const addressNumber = "{{ $address->number }}";
            const addressComplement = "{{ $address->complement ?? '' }}";
            const addressNeighborhood = "{{ $address->neighborhood }}";
            const addressCity = "{{ $address->city }}";
            const addressState = "{{ $address->state }}";
            const addressZipCode = "{{ $address->zip_code }}";
            @else
            const addressInfo = "Sem endereço cadastrado";
            @endif
            
            // Obter informações de frete selecionado
            const shippingSelect = document.getElementById('shipping_option');
            let shippingInfo = "Frete não selecionado";
            if (shippingSelect) {
                const selectedOption = shippingSelect.options[shippingSelect.selectedIndex];
                if (selectedOption) {
                    shippingInfo = selectedOption.text;
                }
            }
            
            // Formatar items do carrinho
            let cartItems = [];
            @foreach($cart->items as $item)
                cartItems.push({
                    title: "{{ $item->product->productable->title }}",
                    quantity: {{ $item->quantity }},
                    price: {{ $item->product->price }},
                    total: {{ $item->product->price * $item->quantity }}
                });
            @endforeach
            
            // Valores do pedido
            const subtotal = {{ $subtotal }};
            const shipping = {{ $shipping }};
            const total = {{ $total }};
            
            // Construir a mensagem
            let message = "*Novo Pedido da Loja Ver&Vermelho*\n\n";
            
            // Informações do cliente
            message += "*Informações do Cliente:*\n";
            message += `Nome: ${clientName}\n`;
            message += `Email: ${clientEmail}\n\n`;
            
            // Endereço
            message += "*Informações de Entrega:*\n";
            @if(is_object($address))
            message += `Endereço: ${addressStreet}, ${addressNumber}\n`;
            if (addressComplement) {
                message += `Complemento: ${addressComplement}\n`;
            }
            message += `Bairro: ${addressNeighborhood}\n`;
            message += `Cidade: ${addressCity}/${addressState}\n`;
            message += `CEP: ${addressZipCode}\n`;
            @else
            message += `${addressInfo}\n`;
            @endif
            message += `\n`;
            
            // Itens do pedido
            message += "*Itens do Pedido:*\n";
            cartItems.forEach((item, index) => {
                message += `${index + 1}. ${item.title} (${item.quantity}x) - R$ ${item.price.toFixed(2).replace('.', ',')} = R$ ${item.total.toFixed(2).replace('.', ',')}\n`;
            });
            message += "\n";
            
            // Resumo do pedido
            message += "*Resumo do Pedido:*\n";
            message += `Subtotal: R$ ${subtotal.toFixed(2).replace('.', ',')}\n`;
            message += `Frete: R$ ${shipping.toFixed(2).replace('.', ',')} (${shippingInfo})\n`;
            message += `*Total: R$ ${total.toFixed(2).replace('.', ',')}*\n\n`;
            
            message += "Gostaria de confirmar este pedido. Como posso pagar?";
            
            return encodeURIComponent(message);
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar opções de frete
            const shippingOptions = document.querySelectorAll('input[name="shipping_option"]');
            shippingOptions.forEach(option => {
                option.addEventListener('change', updateTotalPrice);
            });
            
            // Inicializar o preço total com a primeira opção
            updateTotalPrice();
            
            // Configurar botão de finalização via WhatsApp
            const whatsappButton = document.getElementById('whatsapp-checkout');
            if (whatsappButton) {
                whatsappButton.addEventListener('click', function() {
                    // Obter número de WhatsApp da loja do atributo data
                    const storeWhatsApp = this.getAttribute('data-whatsapp-number') || '5511999999999';
                    
                    // Formatar a mensagem do pedido
                    const message = formatCartForWhatsApp();
                    
                    // Verificar se há itens no carrinho
                    if (!message) {
                        alert('Não há itens no seu carrinho ou ocorreu um erro ao preparar o pedido.');
                        return;
                    }
                    
                    // Primeiro registrar o pedido no banco de dados
                    // Coletar dados para o pedido
                    let items = [];
                    @foreach($cart->items as $item)
                        items.push({
                            id: {{ $item->product->id }},
                            quantity: {{ $item->quantity }},
                            price: {{ $item->product->price }}
                        });
                    @endforeach
                    
                    // Obter dados do endereço e frete
                    @if(is_object($address))
                    const addressId = {{ $address->id }};
                    @else
                    alert('É necessário cadastrar um endereço para finalizar a compra.');
                    return;
                    @endif
                    
                    // Obter informação de frete
                    let shippingServiceName = '';
                    let deliveryTime = 0;
                    const selectedOption = getSelectedShippingOption();
                    
                    if (selectedOption) {
                        shippingServiceName = selectedOption.text;
                        const deliveryMatch = selectedOption.text.match(/(\d+) dias/);
                        if (deliveryMatch) {
                            deliveryTime = parseInt(deliveryMatch[1]);
                        }
                    }
                    
                    // Enviar pedido para registrar no sistema
                    fetch('{{ route("site.checkout.whatsapp.register") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            items: items,
                            shipping_address_id: addressId,
                            shipping_cost: {{ $shipping }},
                            shipping_service_name: shippingServiceName,
                            shipping_delivery_time: deliveryTime,
                            subtotal: {{ $subtotal }},
                            total: {{ $total }}
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Após registrar com sucesso, abrir WhatsApp
                            window.open(`https://api.whatsapp.com/send?phone=${storeWhatsApp}&text=${message}`, '_blank');
                            
                            // Redirecionar para a página de sucesso após um pequeno delay
                            setTimeout(() => {
                                window.location.href = '/pedidos/' + data.order_id;
                            }, 2000);
                        } else {
                            alert('Erro ao finalizar pedido: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Ocorreu um erro ao registrar seu pedido. Tente novamente.');
                    });
                });
            }
            
            const quantityInputs = document.querySelectorAll('.quantity-input');
            const incrementButtons = document.querySelectorAll('.increment-button');
            const decrementButtons = document.querySelectorAll('.decrement-button');

            function updateQuantity(itemId, newQuantity) {
                // Obter o estoque disponível para este item
                const input = document.querySelector(`#counter-input-${itemId}`);
                const availableStock = parseInt(input.dataset.stock);
                
                // Verificar se a quantidade solicitada é maior que o estoque disponível
                if (newQuantity > availableStock) {
                    alert(`Estoque insuficiente. Disponível: ${availableStock} unidade(s)`);
                    input.value = Math.min(newQuantity, availableStock);
                    return;
                }
                
                // Usar o formulário oculto para cada item para enviar a atualização
                const form = document.getElementById(`update-form-${itemId}`);
                const quantityInput = form.querySelector('input[name="quantity"]');
                quantityInput.value = newQuantity;
                
                // Enviar o formulário via fetch
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualizar o total do item
                        const itemTotalElement = document.getElementById(`item-total-${itemId}`);
                        if (itemTotalElement) {
                            // Usar o valor já formatado do backend
                            itemTotalElement.textContent = `R$ ${data.formattedItemTotal}`;
                        }
                        
                        // Atualizar os totais gerais
                        const subtotalElement = document.getElementById('cart-subtotal');
                        const taxElement = document.getElementById('cart-tax');
                        const shippingElement = document.getElementById('cart-shipping');
                        const totalElement = document.getElementById('cart-total');
                        
                        // Usar os valores já formatados do backend
                        if (subtotalElement) subtotalElement.textContent = `R$ ${data.formattedSubtotal}`;
                        if (taxElement) taxElement.textContent = `R$ ${data.formattedTax}`;
                        if (shippingElement) shippingElement.textContent = `R$ ${data.formattedShipping}`;
                        if (totalElement) totalElement.textContent = `R$ ${data.formattedTotal}`;
                        
                        // Atualizar a quantidade no input
                        input.value = data.quantity;
                        
                        // Desabilitar o botão de incremento se necessário
                        const incrementButton = document.querySelector(`.increment-button[data-item-id="${itemId}"]`);
                        if (incrementButton) {
                            if (data.quantity >= data.availableStock) {
                                incrementButton.classList.add('opacity-50', 'cursor-not-allowed');
                            } else {
                                incrementButton.classList.remove('opacity-50', 'cursor-not-allowed');
                            }
                        }
                    } else if (data.error) {
                        alert(data.error);
                        input.value = Math.min(newQuantity, availableStock);
                    } else {
                        alert('Erro ao atualizar quantidade');
                        input.value = Math.min(newQuantity, availableStock);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
            }

            quantityInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const itemId = this.dataset.itemId;

                    const maxQuantity = parseInt(this.dataset.maxQuantity) || 1;
                    let newQuantity = parseInt(this.value);

                    // Validar limites de estoque
                    if (isNaN(newQuantity) || newQuantity < 1) {
                        newQuantity = 1;
                        this.value = 1;
                    } else if (maxQuantity > 0 && newQuantity > maxQuantity) {
                        newQuantity = maxQuantity;
                        this.value = maxQuantity;
                        window.showToast(`Quantidade limitada a ${maxQuantity} unidade(s) em estoque.`, 'warning');
                    }

                    if (newQuantity > 0) {
                        updateQuantity(itemId, newQuantity);

                    const availableStock = parseInt(this.dataset.stock);
                    let newQuantity = parseInt(this.value);
                    
                    // Garantir que a quantidade é pelo menos 1
                    if (newQuantity < 1) {
                        newQuantity = 1;
                        this.value = 1;
                    }
                    
                    // Verificar se excede o estoque disponível
                    if (newQuantity > availableStock) {
                        alert(`Estoque insuficiente. Disponível: ${availableStock} unidade(s)`);
                        newQuantity = availableStock;
                        this.value = availableStock;
                    }
                    
                    updateQuantity(itemId, newQuantity);
                });
            });

            incrementButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const maxQuantity = parseInt(this.dataset.maxQuantity) || 1;
                    const input = document.getElementById(`counter-input-${itemId}`);
                    const currentQuantity = parseInt(input.value);
                    
                    // Verificar se não excede o estoque
                    if (maxQuantity > 0 && currentQuantity >= maxQuantity) {
                        window.showToast(`Quantidade limitada a ${maxQuantity} unidade(s) em estoque.`, 'warning');
                        return; // Não permite aumentar além do estoque
                    }
                    
                    const newQuantity = currentQuantity + 1;

                    const input = document.querySelector(`#counter-input-${itemId}`);
                    const availableStock = parseInt(input.dataset.stock);
                    const currentQty = parseInt(input.value);
                    
                    // Verificar se já atingiu o limite de estoque
                    if (currentQty >= availableStock) {
                        alert(`Estoque insuficiente. Disponível: ${availableStock} unidade(s)`);
                        return;
                    }
                    
                    const newQuantity = currentQty + 1;

                    input.value = newQuantity;
                    updateQuantity(itemId, newQuantity);
                });
            });

            decrementButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const input = document.getElementById(`counter-input-${itemId}`);
                    const currentQuantity = parseInt(input.value);

                    if (currentQuantity > 1) {
                        const newQuantity = currentQuantity - 1;
                        input.value = newQuantity;
                        updateQuantity(itemId, newQuantity);
                    }
                });
            });

            // Add shipping option change handler
            const shippingSelect = document.getElementById('shipping_option');
            if (shippingSelect) {
                shippingSelect.addEventListener('change', function() {
                    fetch('{{ route("site.cart.updateShipping") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            shipping_option: this.value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                });
            }
            // Função para formatar valores monetários
            function formatCurrency(value) {
                return new Intl.NumberFormat('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value);
            }
        });
    </script>
    @endpush
</x-app-layout>

