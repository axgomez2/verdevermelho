<x-app-layout>
    <div class="bg-white py-8 antialiased md:py-10">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">Detalhes do Pedido #{{ $order->reference }}</h1>
                <a href="{{ route('site.customer.orders.index') }}" class="text-sm font-medium text-primary-600 hover:underline flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>
                    Voltar para Meus Pedidos
                </a>
            </div>

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                    <span class="font-medium">Sucesso!</span> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <span class="font-medium">Erro!</span> {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Coluna principal com detalhes do pedido -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Status do pedido -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Status do Pedido</h2>
                        
                        <div class="relative">
                            <div class="flex mb-2">
                                <div class="flex items-center relative z-10 w-full">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full 
                                        @if(in_array($order->status, ['pending', 'paid', 'processing', 'shipped', 'delivered'])) 
                                            bg-blue-600 
                                        @else 
                                            bg-gray-300 
                                        @endif text-white shrink-0">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-gray-900">Aguardando Pagamento</h3>
                                        @if($order->status == 'pending')
                                            <p class="text-xs text-gray-500">Aguardando seu pagamento</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 h-0.5 mt-5"></div>
                            </div>
                            
                            <div class="flex mb-2">
                                <div class="flex items-center relative z-10 w-full">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full 
                                        @if(in_array($order->status, ['paid', 'processing', 'shipped', 'delivered'])) 
                                            bg-blue-600 
                                        @else 
                                            bg-gray-300 
                                        @endif text-white shrink-0">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-gray-900">Pagamento Confirmado</h3>
                                        @if($order->status == 'paid')
                                            <p class="text-xs text-gray-500">Seu pagamento foi aprovado</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 h-0.5 mt-5"></div>
                            </div>
                            
                            <div class="flex mb-2">
                                <div class="flex items-center relative z-10 w-full">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full 
                                        @if(in_array($order->status, ['processing', 'shipped', 'delivered'])) 
                                            bg-blue-600 
                                        @else 
                                            bg-gray-300 
                                        @endif text-white shrink-0">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-5h2v5a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0017 7h-3V4a1 1 0 00-1-1H3z"></path></svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-gray-900">Em Processamento</h3>
                                        @if($order->status == 'processing')
                                            <p class="text-xs text-gray-500">Seu pedido está sendo preparado</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 h-0.5 mt-5"></div>
                            </div>
                            
                            <div class="flex mb-2">
                                <div class="flex items-center relative z-10 w-full">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full 
                                        @if(in_array($order->status, ['shipped', 'delivered'])) 
                                            bg-blue-600 
                                        @else 
                                            bg-gray-300 
                                        @endif text-white shrink-0">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z"></path></svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-gray-900">Enviado</h3>
                                        @if($order->status == 'shipped')
                                            <p class="text-xs text-gray-500">Seu pedido está a caminho</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 h-0.5 mt-5"></div>
                            </div>
                            
                            <div class="flex">
                                <div class="flex items-center relative z-10 w-full">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full 
                                        @if($order->status == 'delivered') 
                                            bg-blue-600 
                                        @else 
                                            bg-gray-300 
                                        @endif text-white shrink-0">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-gray-900">Entregue</h3>
                                        @if($order->status == 'delivered')
                                            <p class="text-xs text-gray-500">Pedido entregue com sucesso</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($order->tracking_code)
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">Código de Rastreamento</h3>
                                        <p class="text-sm text-gray-500">{{ $order->tracking_code }}</p>
                                    </div>
                                    <a href="https://linkcorreios.com.br/?id={{ $order->tracking_code }}" target="_blank" class="px-3 py-2 text-xs font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300">Rastrear</a>
                                </div>
                            </div>
                        @endif

                        <!-- Ações disponíveis para o pedido -->
                        <div class="mt-6 flex flex-wrap gap-2">
                            @if($order->status == 'pending')
                                <a href="{{ route('site.customer.orders.payment', $order->id) }}" class="px-4 py-2 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300">
                                    Pagar Pedido
                                </a>
                                <a href="{{ route('site.customer.orders.cancel', $order->id) }}" onclick="return confirm('Tem certeza que deseja cancelar este pedido?')" class="px-4 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200">
                                    Cancelar Pedido
                                </a>
                            @endif
                            
                            @if($order->status == 'shipped')
                                <a href="{{ route('site.customer.orders.confirm-receipt', $order->id) }}" onclick="return confirm('Confirmar que você recebeu este pedido?')" class="px-4 py-2 text-sm font-medium text-center text-white bg-green-700 rounded-lg hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300">
                                    Confirmar Recebimento
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Produtos do pedido -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Produtos</h2>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Produto</th>
                                        <th scope="col" class="px-4 py-3">Preço</th>
                                        <th scope="col" class="px-4 py-3">Qtd</th>
                                        <th scope="col" class="px-4 py-3">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr class="bg-white border-b">
                                            <td class="px-4 py-4">
                                                <div class="flex items-center">
                                                    @if($item->product && $item->product->images->count() > 0)
                                                        <img class="w-10 h-10 rounded object-cover mr-3" src="{{ asset('storage/' . $item->product->images->first()->path) }}" alt="{{ $item->product_name }}">
                                                    @else
                                                        <div class="w-10 h-10 rounded bg-gray-200 mr-3 flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                                        @if($item->product && $item->product->artist)
                                                            <div class="text-xs text-gray-500">{{ $item->product->artist }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                R$ {{ number_format($item->price, 2, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-4">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-4 py-4 font-medium">
                                                R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Histórico do pedido -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Histórico do Pedido</h2>
                        
                        <ol class="relative border-l border-gray-200">
                            @foreach($order->statusHistory as $history)
                                <li class="mb-10 ml-6">
                                    <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white">
                                        <svg class="w-3 h-3 text-blue-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                    </span>
                                    <h3 class="mb-1 text-sm font-semibold text-gray-900">
                                        @if($history->status == 'paid') Pagamento Aprovado
                                        @elseif($history->status == 'pending') Pedido Pendente
                                        @elseif($history->status == 'shipped') Pedido Enviado
                                        @elseif($history->status == 'delivered') Pedido Entregue
                                        @elseif($history->status == 'canceled') Pedido Cancelado
                                        @elseif($history->status == 'processing') Em Processamento
                                        @elseif($history->status == 'refunded') Reembolsado
                                        @else {{ ucfirst($history->status) }}
                                        @endif
                                    </h3>
                                    <time class="block mb-2 text-xs font-normal leading-none text-gray-400">{{ $history->created_at->format('d/m/Y H:i') }}</time>
                                    <p class="text-sm font-normal text-gray-500">{{ $history->comment }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
                
                <!-- Coluna lateral com resumo do pedido -->
                <div class="space-y-6">
                    <!-- Resumo do pedido -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumo do Pedido</h2>
                        
                        <div class="mb-4">
                            <div class="text-sm text-gray-500 mb-1">Número do Pedido</div>
                            <div class="font-medium">#{{ $order->reference }}</div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="text-sm text-gray-500 mb-1">Data</div>
                            <div class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="text-sm text-gray-500 mb-1">Status</div>
                            <div class="font-medium">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full 
                                    @if($order->status == 'paid') bg-green-100 text-green-800
                                    @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'shipped') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'delivered') bg-indigo-100 text-indigo-800
                                    @elseif($order->status == 'canceled') bg-red-100 text-red-800
                                    @elseif($order->status == 'processing') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    @if($order->status == 'paid') Pago
                                    @elseif($order->status == 'pending') Pendente
                                    @elseif($order->status == 'shipped') Enviado
                                    @elseif($order->status == 'delivered') Entregue
                                    @elseif($order->status == 'canceled') Cancelado
                                    @elseif($order->status == 'processing') Em Processamento
                                    @else {{ ucfirst($order->status) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="text-sm text-gray-500 mb-1">Forma de Pagamento</div>
                            <div class="font-medium">
                                @if($order->payment_method == 'credit_card')
                                    Cartão de Crédito
                                @elseif($order->payment_method == 'pix')
                                    PIX
                                @else
                                    {{ ucfirst($order->payment_method) }}
                                @endif
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 mt-4 pt-4">
                            <div class="flex justify-between mb-2">
                                <div class="text-sm text-gray-500">Subtotal</div>
                                <div class="font-medium">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</div>
                            </div>
                            
                            <div class="flex justify-between mb-2">
                                <div class="text-sm text-gray-500">Frete</div>
                                <div class="font-medium">R$ {{ number_format($order->shipping_price, 2, ',', '.') }}</div>
                            </div>
                            
                            @if($order->discount > 0)
                                <div class="flex justify-between mb-2">
                                    <div class="text-sm text-gray-500">Desconto</div>
                                    <div class="font-medium text-green-600">- R$ {{ number_format($order->discount, 2, ',', '.') }}</div>
                                </div>
                            @endif
                            
                            <div class="flex justify-between pt-4 border-t border-gray-200 mt-2">
                                <div class="text-base font-semibold">Total</div>
                                <div class="text-base font-semibold">R$ {{ number_format($order->total, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Endereço de entrega -->
                    @if($order->shippingAddress)
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Endereço de Entrega</h2>
                            
                            <div class="text-sm text-gray-700">
                                <p class="font-medium">{{ $order->shippingAddress->type }}</p>
                                <p>{{ $order->shippingAddress->street }}, {{ $order->shippingAddress->number }}</p>
                                @if($order->shippingAddress->complement)
                                    <p>{{ $order->shippingAddress->complement }}</p>
                                @endif
                                <p>{{ $order->shippingAddress->neighborhood }}</p>
                                <p>{{ $order->shippingAddress->city }}/{{ $order->shippingAddress->state }}</p>
                                <p>CEP: {{ substr_replace($order->shippingAddress->zip_code, '-', 5, 0) }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Método de envio -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Método de Entrega</h2>
                        
                        <div class="text-sm text-gray-700">
                            <p class="font-medium">{{ $order->shipping_method }}</p>
                            @if($order->shipping_days)
                                <p class="mt-1">Prazo estimado: {{ $order->shipping_days }} dias úteis</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
