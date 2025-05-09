<x-admin-layout>
    <div class="px-4 pt-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl">Pedido #{{ $order->reference }}</h1>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200">
                <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>
                Voltar para Pedidos
            </a>
        </div>
        
        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800" role="alert">
                <span class="font-medium">Sucesso!</span> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800" role="alert">
                <span class="font-medium">Erro!</span> {{ session('error') }}
            </div>
        @endif

        @if (session('info'))
            <div class="mb-4 rounded-lg bg-blue-50 p-4 text-sm text-blue-800" role="alert">
                <span class="font-medium">Informação:</span> {{ session('info') }}
            </div>
        @endif
        
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <!-- Coluna principal com detalhes do pedido -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Informações do pedido -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-md">
                    <h2 class="mb-4 text-lg font-medium text-gray-900">Informações do Pedido</h2>
                    
                    <div class="mb-6 grid grid-cols-2 gap-6 md:grid-cols-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Número do Pedido</h3>
                            <p class="text-base font-medium text-gray-900">#{{ $order->reference }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Data</h3>
                            <p class="text-base font-medium text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <p>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @if($order->status == 'paid') bg-green-100 text-green-800
                                    @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'shipped') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'delivered') bg-indigo-100 text-indigo-800
                                    @elseif($order->status == 'canceled') bg-red-100 text-red-800
                                    @elseif($order->status == 'processing') bg-purple-100 text-purple-800
                                    @elseif($order->status == 'refunded') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    @if($order->status == 'paid') Pago
                                    @elseif($order->status == 'pending') Pendente
                                    @elseif($order->status == 'shipped') Enviado
                                    @elseif($order->status == 'delivered') Entregue
                                    @elseif($order->status == 'canceled') Cancelado
                                    @elseif($order->status == 'processing') Em Processamento
                                    @elseif($order->status == 'refunded') Reembolsado
                                    @else {{ ucfirst($order->status) }}
                                    @endif
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Pagamento</h3>
                            <p class="text-base font-medium text-gray-900">
                                @if($order->payment_method == 'credit_card')
                                    Cartão de Crédito
                                @elseif($order->payment_method == 'pix')
                                    PIX
                                @else
                                    {{ ucfirst($order->payment_method) }}
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    @if($order->payment_tid)
                        <div class="mb-4 rounded-lg bg-gray-50 p-4">
                            <h3 class="text-sm font-medium text-gray-700">ID da Transação (TID)</h3>
                            <p class="text-sm text-gray-900">{{ $order->payment_tid }}</p>
                        </div>
                    @endif
                </div>

                <!-- Produtos do pedido -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-md">
                    <h2 class="mb-4 text-lg font-medium text-gray-900">Produtos</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-500">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Produto</th>
                                    <th scope="col" class="px-4 py-3">Preço</th>
                                    <th scope="col" class="px-4 py-3">Qtd</th>
                                    <th scope="col" class="px-4 py-3">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr class="border-b">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                @if($item->product && $item->product->images->count() > 0)
                                                    <img class="mr-3 h-10 w-10 rounded object-cover" src="{{ asset('storage/' . $item->product->images->first()->path) }}" alt="{{ $item->product_name }}">
                                                @else
                                                    <div class="mr-3 flex h-10 w-10 items-center justify-center rounded bg-gray-200">
                                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                                    @if($item->product && $item->product->artist)
                                                        <div class="text-xs text-gray-500">{{ $item->product->artist }}</div>
                                                    @endif
                                                    <div class="text-xs text-gray-500">SKU: {{ $item->product ? $item->product->sku : 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            R$ {{ number_format($item->price, 2, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-4 py-3 font-medium">
                                            R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-4 py-3 text-right font-medium text-gray-900">Subtotal</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-4 py-3 text-right font-medium text-gray-900">Frete</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">R$ {{ number_format($order->shipping_price, 2, ',', '.') }}</td>
                                </tr>
                                @if($order->discount > 0)
                                    <tr class="bg-gray-50">
                                        <td colspan="3" class="px-4 py-3 text-right font-medium text-gray-900">Desconto</td>
                                        <td class="px-4 py-3 font-medium text-green-600">- R$ {{ number_format($order->discount, 2, ',', '.') }}</td>
                                    </tr>
                                @endif
                                <tr class="bg-gray-100">
                                    <td colspan="3" class="px-4 py-3 text-right text-base font-bold text-gray-900">Total</td>
                                    <td class="px-4 py-3 text-base font-bold text-gray-900">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <!-- Histórico do pedido -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-md">
                    <h2 class="mb-4 text-lg font-medium text-gray-900">Histórico do Pedido</h2>
                    
                    <ol class="relative border-l border-gray-200">
                        @foreach($order->statusHistory as $history)
                            <li class="mb-10 ml-6">
                                <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 ring-8 ring-white">
                                    <svg class="h-3 w-3 text-blue-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                </span>
                                <h3 class="mb-1 text-lg font-semibold text-gray-900">
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
                                <time class="mb-2 block text-sm font-normal leading-none text-gray-400">{{ $history->created_at->format('d/m/Y H:i') }}</time>
                                <p class="text-base font-normal text-gray-500">{{ $history->description }}</p>
                                @if($history->is_customer_notified)
                                    <span class="mt-2 inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                        Cliente notificado
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
            
            <!-- Coluna lateral com ações e detalhes complementares -->
            <div class="space-y-6">
                <!-- Ações do pedido -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-md">
                    <h2 class="mb-4 text-lg font-medium text-gray-900">Ações</h2>
                    
                    <!-- Atualizar status -->
                    <div class="mb-6">
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="status" class="mb-2 block text-sm font-medium text-gray-900">Atualizar Status</label>
                                <select id="status" name="status" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendente</option>
                                    <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Pago</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Em Processamento</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Enviado</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Entregue</option>
                                    <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>Cancelado</option>
                                    <option value="refunded" {{ $order->status == 'refunded' ? 'selected' : '' }}>Reembolsado</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="description" class="mb-2 block text-sm font-medium text-gray-900">Descrição</label>
                                <input type="text" id="description" name="description" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500" placeholder="Descrição da atualização" required>
                            </div>
                            
                            <div class="mb-4 flex items-center">
                                <input id="notify_customer" name="notify_customer" type="checkbox" value="1" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500">
                                <label for="notify_customer" class="ml-2 text-sm font-medium text-gray-900">Notificar cliente por e-mail</label>
                            </div>
                            
                            <button type="submit" class="w-full rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300">Atualizar Status</button>
                        </form>
                    </div>
                    
                    <!-- Rastreio -->
                    <div class="mb-6 border-t border-gray-200 pt-6">
                        <h3 class="mb-4 text-base font-medium text-gray-900">Informações de Rastreio</h3>
                        
                        @if($order->tracking_code)
                            <div class="mb-4 rounded-lg bg-gray-50 p-4">
                                <p class="text-sm font-medium text-gray-700">Código de Rastreio</p>
                                <p class="text-sm text-gray-900">{{ $order->tracking_code }}</p>
                                <p class="mt-2 text-sm font-medium text-gray-700">Transportadora</p>
                                <p class="text-sm text-gray-900">{{ $order->shipping_company ?? 'Não especificada' }}</p>
                            </div>
                        @endif
                        
                        <form action="{{ route('admin.orders.update-tracking', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="tracking_code" class="mb-2 block text-sm font-medium text-gray-900">Código de Rastreio</label>
                                <input type="text" id="tracking_code" name="tracking_code" value="{{ $order->tracking_code }}" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500" placeholder="Ex: BR0123456789" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="shipping_company" class="mb-2 block text-sm font-medium text-gray-900">Transportadora</label>
                                <input type="text" id="shipping_company" name="shipping_company" value="{{ $order->shipping_company }}" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500" placeholder="Ex: Correios, Jadlog" required>
                            </div>
                            
                            <div class="mb-4 flex items-center">
                                <input id="notify_customer_tracking" name="notify_customer" type="checkbox" value="1" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500">
                                <label for="notify_customer_tracking" class="ml-2 text-sm font-medium text-gray-900">Notificar cliente por e-mail</label>
                            </div>
                            
                            <button type="submit" class="w-full rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300">Atualizar Rastreio</button>
                        </form>
                    </div>
                    
                    <!-- Etiqueta de envio -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="mb-4 text-base font-medium text-gray-900">Etiqueta de Envio</h3>
                        
                        @if($order->shipping_label_url)
                            <div class="mb-4">
                                <a href="{{ $order->shipping_label_url }}" target="_blank" class="inline-flex w-full items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300">
                                    <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path></svg>
                                    Baixar Etiqueta
                                </a>
                            </div>
                        @else
                            <p class="mb-4 text-sm text-gray-500">Nenhuma etiqueta gerada ainda.</p>
                        @endif
                        
                        @if(!$order->shipping_label_url && in_array($order->status, ['paid', 'processing']))
                            <a href="{{ route('admin.orders.shipping-label', $order->id) }}" class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                                <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                Gerar Etiqueta Melhor Envio
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Informações do cliente -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-md">
                    <h2 class="mb-4 text-lg font-medium text-gray-900">Informações do Cliente</h2>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700">Nome</p>
                        <p class="text-base text-gray-900">{{ $order->user->name }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700">E-mail</p>
                        <p class="text-base text-gray-900">{{ $order->user->email }}</p>
                    </div>
                    
                    @if($order->user->phone)
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700">Telefone</p>
                            <p class="text-base text-gray-900">{{ $order->user->phone }}</p>
                        </div>
                    @endif
                    
                    <a href="{{ route('admin.customers.show', $order->user->id) }}" class="inline-flex items-center text-primary-600 hover:underline">
                        <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
                        Ver Perfil do Cliente
                    </a>
                </div>
                
                <!-- Endereço de entrega -->
                @if($order->shippingAddress)
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-md">
                        <h2 class="mb-4 text-lg font-medium text-gray-900">Endereço de Entrega</h2>
                        
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
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-md">
                    <h2 class="mb-4 text-lg font-medium text-gray-900">Método de Entrega</h2>
                    
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
</x-admin-layout>
