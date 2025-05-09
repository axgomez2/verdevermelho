<x-app-layout>
    <div class="bg-white py-8 antialiased md:py-10">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl mb-6">Finalizar Compra</h1>

            @if(session('error'))
                <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Erro!</span> {{ session('error') }}
                </div>
            @endif

    <div class="md:gap-6 lg:flex lg:items-start">
        <!-- Resumo do Carrinho -->
        <div class="mx-auto w-full lg:w-5/12 flex-none mb-8 lg:mb-0 lg:order-2">
            <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Seu Carrinho</h2>
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-primary-100 text-xs font-medium text-primary-800">{{ $cart->items->count() }}</span>
                </div>
                
                <div class="border-b border-gray-200 pb-4">
                    <ul class="divide-y divide-gray-200">
                        @foreach($cart->items as $item)
                        <li class="py-3 flex justify-between items-center">
                            <div class="flex-1">
                                @php
                                    $title = $item->product->productable->title ?? $item->product->name ?? 'Produto';
                                @endphp
                                <h3 class="text-sm font-medium text-gray-900">{{ $title }}</h3>
                                <p class="text-xs text-gray-500">Quantidade: {{ $item->quantity }}</p>
                            </div>
                            <span class="text-sm font-medium text-gray-900">R$ {{ number_format($item->quantity * $item->product->price, 2, ',', '.') }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="space-y-2">
                    <dl class="flex items-center justify-between gap-4">
                        <dt class="text-base font-normal text-gray-500">Subtotal</dt>
                        <dd class="text-base font-medium text-gray-900">R$ {{ number_format($subtotal, 2, ',', '.') }}</dd>
                    </dl>
                    
                    <dl class="flex items-center justify-between gap-4">
                        <dt class="text-base font-normal text-gray-500">Frete</dt>
                        <dd class="text-base font-medium text-gray-900">R$ {{ number_format($shippingCost, 2, ',', '.') }}</dd>
                    </dl>
                    
                    <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-4">
                        <dt class="text-lg font-bold text-gray-900">Total</dt>
                        <dd class="text-lg font-bold text-gray-900">R$ {{ number_format($total, 2, ',', '.') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Formulário de Checkout -->
        <div class="mx-auto w-full lg:w-7/12 flex-none lg:order-1">
            <form id="payment-form" action="{{ route('site.checkout.process') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Endereço de Entrega -->
                <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900">Endereço de Entrega</h2>
                        <button type="button" data-modal-target="address-modal" data-modal-toggle="address-modal" class="text-sm font-medium text-primary-600 hover:underline inline-flex items-center">
                            <svg class="mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Alterar Endereço
                        </button>
                    </div>
                    
                    @if($addresses->count() > 0)
                        <div id="address-selection-container" class="mb-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                @php
                                    $defaultAddress = $addresses->where('is_default', true)->first() ?? $addresses->first();
                                @endphp
                                <input type="hidden" name="shipping_address_id" id="shipping_address_id" value="{{ $defaultAddress->id }}">
                                
                                <div id="selected-address-display">
                                    <h3 class="text-base font-medium text-gray-900">{{ $defaultAddress->type }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $defaultAddress->street }}, {{ $defaultAddress->number }}
                                        @if($defaultAddress->complement)
                                            - {{ $defaultAddress->complement }}
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $defaultAddress->neighborhood }}, {{ $defaultAddress->city }}/{{ $defaultAddress->state }} - CEP: {{ substr_replace($defaultAddress->zip_code, '-', 5, 0) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                            <span class="font-medium">Atenção!</span> Você não possui endereços cadastrados. 
                            <button type="button" data-modal-target="new-address-modal" data-modal-toggle="new-address-modal" class="font-medium underline hover:text-yellow-900">Cadastre um endereço</button> para continuar.
                        </div>
                    @endif
                </div>

                <!-- Seção de Frete -->
                <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900">Informações de Frete</h2>
                        <button type="button" data-modal-target="shipping-modal" data-modal-toggle="shipping-modal" class="text-sm font-medium text-primary-600 hover:underline inline-flex items-center">
                            <svg class="mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Alterar Frete
                        </button>
                    </div>
                    
                    @if(session('shipping_postal_code') && session('selected_shipping_option'))
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-base font-medium text-gray-900">{{ session('selected_shipping_name') ?? 'Frete selecionado' }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        CEP: {{ substr_replace(session('shipping_postal_code'), '-', 5, 0) }}
                                    </p>
                                    @if(isset($shippingOptions))
                                        @foreach($shippingOptions as $option)
                                            @if($option['id'] == session('selected_shipping_option'))
                                                <p class="text-sm text-gray-500">
                                                    Prazo de entrega: {{ $option['delivery_time'] }} dias úteis
                                                </p>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                <div>
                                    <span class="text-lg font-semibold text-gray-900">R$ {{ number_format(session('selected_shipping_price'), 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                            <span class="font-medium">Atenção!</span> Você ainda não selecionou uma opção de frete. 
                            <button type="button" data-modal-target="shipping-modal" data-modal-toggle="shipping-modal" class="font-medium underline hover:text-yellow-900">Calcule o frete</button> antes de continuar.
                        </div>
                    @endif
                </div>

                <!-- Método de Pagamento -->
                <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                    <h2 class="text-xl font-semibold text-gray-900">Método de Pagamento</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input id="credit_card" name="payment_method" type="radio" value="credit_card" checked required class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 focus:ring-2">
                            <label for="credit_card" class="ms-2 text-sm font-medium text-gray-900 flex items-center">
                                <svg class="w-6 h-6 text-gray-800 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 9h2m3 0h5M1 5h18M1 1h18v12H1z"/></svg>
                                Cartão de Crédito
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="pix" name="payment_method" type="radio" value="pix" required class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 focus:ring-2">
                            <label for="pix" class="ms-2 text-sm font-medium text-gray-900 flex items-center">
                                <svg class="w-6 h-6 text-gray-800 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5v10M3 5a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm0 10a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm9-10v.4A3.6 3.6 0 0 1 8.4 9H6.61A3.6 3.6 0 0 0 3 12.605M14.458 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/></svg>
                                PIX
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Cartão de Crédito -->
                <div id="credit-card-form" class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6 mt-4">
                    <h2 class="text-xl font-semibold text-gray-900">Detalhes do Cartão</h2>
                    <input type="hidden" name="card_token" id="card_token" value="">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <label for="cc-name" class="block mb-2 text-sm font-medium text-gray-900">Nome no Cartão</label>
                            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" id="cc-name" placeholder="Nome como impresso no cartão" required>
                            <small class="text-xs text-gray-500 mt-1 block">Nome completo como mostrado no cartão</small>
                        </div>
                        <div class="col-span-1">
                            <label for="cc-number" class="block mb-2 text-sm font-medium text-gray-900">Número do Cartão</label>
                            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" id="cc-number" placeholder="0000 0000 0000 0000" required>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="cc-expiration-month" class="block mb-2 text-sm font-medium text-gray-900">Mês de Expiração</label>
                            <select class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" id="cc-expiration-month" required>
                                <option value="">Mês</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="cc-expiration-year" class="block mb-2 text-sm font-medium text-gray-900">Ano de Expiração</label>
                            <select class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" id="cc-expiration-year" required>
                                <option value="">Ano</option>
                                @for($i = date('Y'); $i <= date('Y') + 15; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="cc-cvv" class="block mb-2 text-sm font-medium text-gray-900">CVV</label>
                            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" id="cc-cvv" placeholder="000" required>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="cc-installments" class="block mb-2 text-sm font-medium text-gray-900">Parcelas</label>
                            <select class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" id="cc-installments" name="installments" required>
                                <option value="1">1x de R$ {{ number_format($total, 2, ',', '.') }} (sem juros)</option>
                                @if($total >= 100)
                                <option value="2">2x de R$ {{ number_format($total / 2, 2, ',', '.') }} (sem juros)</option>
                                <option value="3">3x de R$ {{ number_format($total / 3, 2, ',', '.') }} (sem juros)</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label for="cc-document-number" class="block mb-2 text-sm font-medium text-gray-900">CPF do Titular</label>
                            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" id="cc-document-number" name="document_number" placeholder="000.000.000-00" required>
                        </div>
                    </div>
                </div>
                
                <!-- PIX -->
                <div id="pix-form" class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6 mt-4 hidden">
                    <h2 class="text-xl font-semibold text-gray-900">Pagamento com PIX</h2>
                    <p class="text-sm text-gray-600">Ao finalizar o pedido, você receberá um QR Code para realizar o pagamento via PIX.</p>
                    <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 p-4 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium">O pagamento por PIX é processado imediatamente. Após a confirmação, seu pedido será enviado para separação.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Campos ocultos para armazenar informações do cartão, se necessário para a integração -->
                <input type="hidden" name="card_holder_name" id="card_holder_name">
                <input type="hidden" name="card_number" id="card_number">
                <input type="hidden" name="card_cvv" id="card_cvv">
                <input type="hidden" name="card_expiration_month" id="card_expiration_month">
                <input type="hidden" name="card_expiration_year" id="card_expiration_year">

                <div class="mt-6">
                    <button type="submit" class="w-full inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        Finalizar Pedido Seguro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Incluir modais -->
@include('site.checkout.address-modal')

@push('scripts')
<!-- Inicialização do Flowbite (para modais) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>

<!-- Script de gerenciamento do checkout -->
<script src="{{ asset('js/checkout.js') }}"></script>


@endpush
</x-app-layout>
