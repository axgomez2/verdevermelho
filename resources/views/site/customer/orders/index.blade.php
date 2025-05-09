<x-app-layout>
    <div class="bg-white py-8 antialiased md:py-10">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">Meus Pedidos</h1>
                <a href="{{ route('site.shop.index') }}" class="text-sm font-medium text-primary-600 hover:underline flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>
                    Continuar Comprando
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

            @if($orders->isEmpty())
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Você ainda não realizou nenhum pedido</h2>
                    <p class="text-gray-500 mb-6">Explore nossa coleção de discos de vinil e faça seu primeiro pedido!</p>
                    <a href="{{ route('site.shop.index') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300">
                        Explorar Loja
                        <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3">Pedido</th>
                                <th scope="col" class="px-4 py-3">Data</th>
                                <th scope="col" class="px-4 py-3">Status</th>
                                <th scope="col" class="px-4 py-3">Total</th>
                                <th scope="col" class="px-4 py-3">Pagamento</th>
                                <th scope="col" class="px-4 py-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-4 py-4 font-medium text-gray-900">
                                        #{{ $order->reference }}
                                    </td>
                                    <td class="px-4 py-4">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-4">
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
                                    </td>
                                    <td class="px-4 py-4 font-medium">
                                        R$ {{ number_format($order->total, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($order->payment_method == 'credit_card')
                                            Cartão de Crédito
                                        @elseif($order->payment_method == 'pix')
                                            PIX
                                        @else
                                            {{ ucfirst($order->payment_method) }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('site.customer.orders.show', $order->id) }}" class="font-medium text-primary-600 hover:underline">Detalhes</a>
                                            
                                            @if($order->status == 'pending')
                                                <a href="{{ route('site.customer.orders.payment', $order->id) }}" class="font-medium text-blue-600 hover:underline">Pagar</a>
                                                <a href="{{ route('site.customer.orders.cancel', $order->id) }}" class="font-medium text-red-600 hover:underline" onclick="return confirm('Tem certeza que deseja cancelar este pedido?')">Cancelar</a>
                                            @endif
                                            
                                            @if($order->status == 'shipped')
                                                <a href="{{ route('site.customer.orders.confirm-receipt', $order->id) }}" class="font-medium text-green-600 hover:underline" onclick="return confirm('Confirmar que você recebeu este pedido?')">Confirmar Recebimento</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
