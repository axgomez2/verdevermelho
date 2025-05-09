<x-admin-layout>
    <div class="px-4 pt-6">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl">Gerenciamento de Pedidos</h1>
        </div>
        
        <!-- Cards de estatísticas -->
        <div class="mb-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="flex items-center rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-normal text-gray-500">Total de Pedidos</h2>
                    <p class="text-xl font-semibold text-gray-900">{{ $statistics['total'] }}</p>
                </div>
            </div>
            
            <div class="flex items-center rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-normal text-gray-500">Pendentes</h2>
                    <p class="text-xl font-semibold text-gray-900">{{ $statistics['pending'] }}</p>
                </div>
            </div>
            
            <div class="flex items-center rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-normal text-gray-500">Pagos/Processando</h2>
                    <p class="text-xl font-semibold text-gray-900">{{ $statistics['paid'] + $statistics['processing'] }}</p>
                </div>
            </div>
            
            <div class="flex items-center rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-5h2v5a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0017 7h-3V4a1 1 0 00-1-1H3z"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-normal text-gray-500">Enviados</h2>
                    <p class="text-xl font-semibold text-gray-900">{{ $statistics['shipped'] }}</p>
                </div>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-md">
            <h2 class="mb-4 text-lg font-medium text-gray-900">Filtros</h2>
            
            <form action="{{ route('admin.orders.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label for="status" class="mb-2 block text-sm font-medium text-gray-900">Status</label>
                        <select id="status" name="status" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500">
                            <option value="all" {{ !$status || $status == 'all' ? 'selected' : '' }}>Todos os Status</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Pago</option>
                            <option value="processing" {{ $status == 'processing' ? 'selected' : '' }}>Em Processamento</option>
                            <option value="shipped" {{ $status == 'shipped' ? 'selected' : '' }}>Enviado</option>
                            <option value="delivered" {{ $status == 'delivered' ? 'selected' : '' }}>Entregue</option>
                            <option value="canceled" {{ $status == 'canceled' ? 'selected' : '' }}>Cancelado</option>
                            <option value="refunded" {{ $status == 'refunded' ? 'selected' : '' }}>Reembolsado</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="search" class="mb-2 block text-sm font-medium text-gray-900">Busca</label>
                        <input type="text" id="search" name="search" value="{{ $search ?? '' }}" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500" placeholder="Pedido, nome ou email">
                    </div>
                    
                    <div>
                        <label for="date_from" class="mb-2 block text-sm font-medium text-gray-900">Data Inicial</label>
                        <input type="date" id="date_from" name="date_from" value="{{ $dateFrom ?? '' }}" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label for="date_to" class="mb-2 block text-sm font-medium text-gray-900">Data Final</label>
                        <input type="date" id="date_to" name="date_to" value="{{ $dateTo ?? '' }}" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.orders.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200">Limpar</a>
                    <button type="submit" class="rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300">Filtrar</button>
                </div>
            </form>
        </div>
        
        <!-- Lista de Pedidos -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-md">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">Pedido</th>
                            <th scope="col" class="px-4 py-3">Cliente</th>
                            <th scope="col" class="px-4 py-3">Data</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Total</th>
                            <th scope="col" class="px-4 py-3">Pagamento</th>
                            <th scope="col" class="px-4 py-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    #{{ $order->reference }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $order->user->name }}</div>
                                    <div class="text-xs">{{ $order->user->email }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
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
                                </td>
                                <td class="px-4 py-3 font-medium">
                                    R$ {{ number_format($order->total, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($order->payment_method == 'credit_card')
                                        Cartão de Crédito
                                    @elseif($order->payment_method == 'pix')
                                        PIX
                                    @else
                                        {{ ucfirst($order->payment_method) }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="font-medium text-primary-600 hover:underline">Ver</a>
                                        
                                        @if($order->status == 'paid' || $order->status == 'processing')
                                            <a href="{{ route('admin.orders.shipping-label', $order->id) }}" class="font-medium text-blue-600 hover:underline">Gerar Etiqueta</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        
                        @if($orders->isEmpty())
                            <tr class="border-b">
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    Nenhum pedido encontrado com os filtros selecionados.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <div class="px-4 py-3">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
