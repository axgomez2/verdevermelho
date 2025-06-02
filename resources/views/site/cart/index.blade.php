<x-app-layout>
    <section class="bg-white py-8 antialiased md:py-16">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <h2 class="text-xl font-semibold text-gray-900 sm:text-2xl">Carrinho de Compras</h2>

            <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-4xl">
                    <div class="space-y-6">
                        <!-- Itens disponíveis -->
                        @foreach($availableItems as $item)
                            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:p-6">
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
                                            <button type="button" data-item-id="{{ $item->id }}" class="decrement-button inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                                <svg class="h-2.5 w-2.5 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>
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

                <div class="mx-auto mt-6 max-w-4xl flex-1 space-y-6 lg:mt-0 lg:w-full">
                    <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                        <p class="text-xl font-semibold text-gray-900">Resumo do Pedido</p>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-base font-normal text-gray-500">Subtotal</dt>
                                    <dd class="text-base font-medium text-gray-900" id="cart-subtotal">R$ {{ number_format($subtotal, 2, ',', '.') }}</dd>
                                </dl>

                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-base font-normal text-gray-500">Frete</dt>
                                    <dd class="text-base font-medium text-gray-900">
                                        @if($address || session('shipping_postal_code'))
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
                                                <select name="shipping_option" id="shipping_option" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                    @foreach($shippingOptions as $option)
                                                        <option value="{{ $option['id'] }}">
                                                            {{ $option['name'] }} - {{ $option['delivery_time'] }} dias - R$ {{ number_format($option['price'], 2, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <p class="text-sm text-gray-500">Calculando frete...</p>
                                            @endif

                                            <p class="mt-2 text-sm text-gray-600">
                                                Entrega para:
                                                @if(is_object($address))
                                                    {{ $address->street }}, {{ $address->number }} - {{ $address->city }}/{{ $address->state }}
                                                @else
                                                    CEP: {{ $postalCode }}
                                                @endif
                                            </p>
                                        @else
                                            <button type="button" onclick="openAddressModal()" class="text-blue-600 hover:underline">
                                                Adicionar endereço para calcular frete
                                            </button>
                                        @endif
                                    </dd>
                                </dl>

                                <!-- Campo de impostos removido conforme solicitado -->
                            </div>

                            <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2">
                                <dt class="text-base font-bold text-gray-900">Total</dt>
                                <dd class="text-base font-bold text-gray-900" id="cart-total">R$ {{ number_format($total, 2, ',', '.') }}</dd>
                            </dl>
                        </div>

                        <a href="{{ route('site.checkout.index') }}" class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300">
                            Finalizar Compra
                        </a>

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
        document.addEventListener('DOMContentLoaded', function() {
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
                    const input = document.querySelector(`#counter-input-${itemId}`);
                    const newQuantity = Math.max(1, parseInt(input.value) - 1);
                    input.value = newQuantity;
                    updateQuantity(itemId, newQuantity);
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

