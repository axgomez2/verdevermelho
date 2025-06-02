@extends('layouts.admin')

@section('title', 'PDV - Ponto de Venda')

@section('styles')
<style>
    .product-item {
        transition: all 0.2s;
    }
    .product-item:hover {
        transform: translateY(-3px);
    }
    .cart-item {
        transition: background 0.3s;
    }
</style>
@endsection

@section('content')
<div class="p-4">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white mb-2">PDV - Ponto de Venda</h1>
    <nav class="flex mb-5" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">PDV</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Mensagens de alerta -->
    @if(session('success'))
    <div id="alert-success" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
        </svg>
        <span class="sr-only">Sucesso</span>
        <div class="ml-3 text-sm font-medium">{{ session('success') }}</div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-success" aria-label="Close">
            <span class="sr-only">Fechar</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div id="alert-error" class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.5 11.5-3-3m0 0L7 5.5m3.5 3.5L7 12.5m3.5-3.5 3.5-3.5"/>
        </svg>
        <span class="sr-only">Erro</span>
        <div class="ml-3 text-sm font-medium">{{ session('error') }}</div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-error" aria-label="Close">
            <span class="sr-only">Fechar</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Coluna da esquerda - Pesquisa e produtos -->
                <div class="w-full lg:w-7/12">
                    <!-- Barra de pesquisa e resultados -->
                    <div class="mb-6" id="search-component">
                        <div class="flex">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    id="product-search" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                    placeholder="Buscar produto por título, artista ou código de barras">
                            </div>
                            <button 
                                id="search-button" 
                                class="flex items-center justify-center p-2.5 ml-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                </svg>
                                <span class="ml-1">Buscar</span>
                            </button>
                        </div>
                    
                        <!-- Resultados da pesquisa -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Resultados da busca</h3>
                            
                            <div id="search-results">
                                <!-- Estado inicial - vazio -->
                                <div id="empty-state" class="w-full text-center py-6 text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto mb-2 w-10 h-10 text-gray-400 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                    <p>Pesquise por um produto para exibir os resultados.</p>
                                </div>
                                
                                <!-- Estado de carregamento - inicialmente oculto -->
                                <div id="loading-state" class="w-full text-center py-6 hidden">
                                    <div class="flex justify-center">
                                        <div class="animate-spin h-10 w-10 border-4 border-blue-500 dark:border-blue-700 rounded-full border-t-transparent"></div>
                                    </div>
                                    <p class="mt-2 text-gray-500 dark:text-gray-400">Buscando produtos...</p>
                                </div>
                                
                                <!-- Estado de erro - inicialmente oculto -->
                                <div id="error-state" class="w-full text-center py-6 text-red-500 hidden">
                                    <svg class="mx-auto mb-2 w-10 h-10" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                    </svg>
                                    <p id="error-message">Erro na busca.</p>
                                </div>
                                
                                <!-- Estado de sucesso - inicialmente oculto -->
                                <div id="success-state" class="hidden">
                                    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                                        <span id="product-count">0</span> produto(s) encontrado(s).
                                    </p>
                                    <div id="products-container" class="flex flex-wrap -mx-2">
                                        <!-- Os produtos serão inseridos aqui via JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vendas recentes -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Últimas 5 vendas</h3>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-4">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Nota</th>
                                        <th scope="col" class="px-4 py-3">Data</th>
                                        <th scope="col" class="px-4 py-3">Cliente</th>
                                        <th scope="col" class="px-4 py-3">Valor</th>
                                        <th scope="col" class="px-4 py-3">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSales as $sale)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $sale->invoice_number }}</td>
                                        <td class="px-4 py-2">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-2">{{ $sale->customer ? $sale->customer->name : $sale->customer_name ?? 'Cliente não identificado' }}</td>
                                        <td class="px-4 py-2 font-medium">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('admin.pos.show', $sale->id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                                <svg class="w-4 h-4 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                Ver
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="5" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Nenhuma venda recente.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <a href="{{ route('admin.pos.sales') }}" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                            Ver todas as vendas
                        </a>
                    </div>
                </div>

                <!-- Coluna da direita - Carrinho de compras -->
                <div class="w-full lg:w-5/12">
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                        <div class="bg-gray-800 dark:bg-gray-700 text-white px-4 py-3">
                            <h3 class="text-lg font-medium">Carrinho de Compras</h3>
                        </div>
                        <div class="p-4">
                            <!-- Dados do cliente -->
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="text-base font-medium text-gray-700 dark:text-gray-300">Cliente</h4>
                                    <button type="button" id="search-customer-btn" class="flex items-center text-xs px-3 py-1.5 text-blue-700 border border-blue-700 rounded-lg hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                                        <svg class="w-3.5 h-3.5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        Buscar cliente
                                    </button>
                                </div>
                                <input type="hidden" id="customer-id">
                                <input type="text" id="customer-name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Nome do cliente (opcional)">
                            </div>

                            <!-- Itens do carrinho -->
                            <div class="mb-4">
                                <h4 class="text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Itens</h4>
                                <div id="cart-items" class="overflow-y-auto mb-3 border border-gray-200 dark:border-gray-700 rounded-lg" style="max-height: 300px;">
                                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                                        Nenhum item adicionado ao carrinho.
                                    </div>
                                </div>

                                <!-- Resumo do pedido -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                                        <span id="subtotal" class="font-medium text-gray-900 dark:text-white">R$ 0,00</span>
                                    </div>
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="flex items-center">
                                            <span class="text-gray-600 dark:text-gray-400 mr-2">Desconto:</span>
                                            <div class="flex w-28">
                                                <input type="number" id="discount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="0" min="0" step="0.01">
                                                <span class="inline-flex items-center px-2 text-sm text-gray-900 bg-gray-200 border border-l-0 border-gray-300 rounded-r-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">R$</span>
                                            </div>
                                        </div>
                                        <span id="discount-value" class="text-gray-900 dark:text-white">R$ 0,00</span>
                                    </div>
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="flex items-center">
                                            <div class="flex-1 p-4 overflow-auto bg-white border rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700" x-data="pdvSearch()">
    <div class="mb-4">
        <div class="flex items-center space-x-2">
            <div class="flex-1">
                <input id="productSearch" x-ref="searchInput" type="search" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" 
                    placeholder="Buscar discos por título, artista ou código de barras..."
                    @keyup.enter="buscar()">
            </div>
            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                @click="buscar()">
                <i class="fas fa-search mr-2"></i> Buscar
            </button>
        </div>
    </div>
    
    <div id="searchResults" x-ref="resultContainer" class="mt-4">
        <!-- Status da busca -->
        <template x-if="status === 'empty'">
            <div class="w-full text-center py-10 text-gray-500 dark:text-gray-400">
                <p>Digite um termo para buscar discos.</p>
            </div>
        </template>
        
        <template x-if="status === 'loading'">
            <div class="w-full text-center py-6">
                <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-blue-600 border-r-transparent dark:border-blue-500" role="status">
                    <span class="sr-only">Carregando...</span>
                </div>
            </div>
        </template>
        
        <template x-if="status === 'error'">
            <div class="w-full text-center py-6 text-red-600 dark:text-red-500">
                <p x-text="'Erro: ' + errorMessage"></p>
            </div>
        </template>
        
        <!-- Resultados -->
        <template x-if="status === 'results'">
            <div>
                <div x-show="produtos.length === 0" class="w-full text-center py-6 text-gray-500 dark:text-gray-400">
                    <p>Nenhum disco encontrado. Tente outro termo de busca.</p>
                </div>
                
                <div x-show="produtos.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="produto in produtos" :key="produto.id">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden product-item">
                            <div class="flex items-center p-3 border-b dark:border-gray-700">
                                <img :src="produto.image" class="w-16 h-16 object-cover rounded mr-3" alt="Capa do disco">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white" x-text="produto.title"></h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="produto.artist"></p>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-sm font-bold text-blue-600 dark:text-blue-500" x-text="'R$ ' + parseFloat(produto.price).toFixed(2).replace('.', ',')"></span>
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300" x-text="'Estoque: ' + produto.stock"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700">
                                <button type="button" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                                    @click="addToCart(produto)">
                                    <i class="fas fa-cart-plus mr-1"></i> Adicionar ao Carrinho
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>                                    <div class="flex justify-between items-center mb-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                                        <span class="font-semibold text-gray-900 dark:text-white">Total:</span>
                                        <span id="total" class="font-bold text-lg text-gray-900 dark:text-white">R$ 0,00</span>
                                    </div>
                                </div>
                            </div>
{{-- Comentário: placeholder removido aqui --}}

                            <!-- Método de pagamento -->
                            <div class="mb-4">
                                <h4 class="text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Método de Pagamento</h4>
                                <select id="payment-method" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="money">Dinheiro</option>
                                    <option value="credit_card">Cartão de Crédito</option>
                                    <option value="debit_card">Cartão de Débito</option>
                                    <option value="pix">PIX</option>
                                    <option value="transfer">Transferência</option>
                                </select>
                            </div>

                            <!-- Observações -->
                            <div class="mb-4">
                                <h4 class="text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Observações</h4>
                                <textarea id="notes" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" rows="2" placeholder="Observações adicionais sobre a venda"></textarea>
                            </div>

                            <!-- Botões de ação -->
                            <div class="space-y-3">
                                <button id="confirm-sale" class="w-full flex justify-center items-center text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    Finalizar Venda
                                </button>
                                <button id="clear-cart" class="w-full flex justify-center items-center text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Limpar Carrinho
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de busca de clientes -->
<div id="customer-search-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Buscar Cliente</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="customer-search-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Fechar</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <div class="relative mb-4">
                    <div class="flex">
                        <input type="text" id="customer-search-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg rounded-r-none focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Nome, e-mail ou CPF">
                        <button type="button" id="search-customer-button" class="flex items-center px-4 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="customer-search-results" class="overflow-y-auto max-h-60 divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Resultados da pesquisa de clientes -->
                </div>
            </div>
            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button type="button" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" data-modal-hide="customer-search-modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Template para item de produto -->
<template id="product-template">
    <div class="w-full sm:w-1/2 md:w-1/2 lg:w-1/3 p-2">
        <div class="bg-white rounded-lg shadow-md overflow-hidden product-item dark:bg-gray-800" data-id="" data-price="" data-stock="">
            <img src="" class="w-full h-40 object-cover product-image" alt="Capa do disco">
            <div class="p-3">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate product-title"></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate product-artist"></p>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-sm font-bold text-gray-900 dark:text-white product-price"></span>
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 product-stock"></span>
                </div>
                <button class="w-full mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 add-to-cart-btn flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="8" y1="11" x2="16" y2="11"></line>
                    </svg>
                    Adicionar
                </button>
            </div>
        </div>
    </div>
</template>

<!-- Template para item do carrinho -->
<template id="cart-item-template">
    <div class="border-b border-gray-200 dark:border-gray-700 p-3 cart-item" data-id="" data-price="">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-2">
            <div class="flex-1">
                <div class="font-medium text-gray-900 dark:text-white item-title"></div>
                <div class="text-xs text-gray-500 dark:text-gray-400 item-artist"></div>
                <div class="text-sm text-gray-600 dark:text-gray-300 item-price"></div>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex">
                    <button class="decrease-qty px-2 py-1 text-sm font-medium text-gray-900 bg-gray-100 border border-gray-300 rounded-l-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-gray-700" type="button">-</button>
                    <input type="number" class="item-quantity bg-gray-50 border-y border-gray-300 text-gray-900 text-center text-sm focus:ring-blue-500 focus:border-blue-500 block w-12 py-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="1" min="1">
                    <button class="increase-qty px-2 py-1 text-sm font-medium text-gray-900 bg-gray-100 border border-gray-300 rounded-r-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-gray-700" type="button">+</button>
                </div>
                <button class="remove-item p-1.5 text-sm text-red-600 bg-red-50 rounded-lg hover:text-white hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 dark:text-red-500 dark:bg-gray-800 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="flex justify-between items-center mt-2">
            <span class="text-xs text-gray-500 dark:text-gray-400">Estoque: <span class="item-stock"></span></span>
            <span class="font-semibold text-gray-900 dark:text-white item-total"></span>
        </div>
    </div>
</template>
@endsection

<!-- Toast Messages -->
<div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>

@section('scripts')
<script>
    /**
     * Sistema PDV - JavaScript Principal
     * 
     * Este script gerencia todas as funcionalidades do PDV, incluindo:
     * - Busca de produtos
     * - Adição de itens ao carrinho
     * - Cálculo de valores
     * - Finalização de vendas
     * - Etc.
     */
    
    // Função para atualizar os totais do carrinho
    function atualizarTotaisCarrinho() {
        // Contar itens no carrinho
        const cartItems = document.getElementById('cart-items');
        const cartItemCount = cartItems ? cartItems.querySelectorAll('.cart-item').length : 0;
        
        // Atualizar contador de itens
        const itemCount = document.getElementById('item-count');
        if (itemCount) itemCount.textContent = cartItemCount;
        
        // Calcular subtotal
        let subtotalValue = 0;
        let cartItemsArray = cartItems ? Array.from(cartItems.querySelectorAll('.cart-item')) : [];
        
        cartItemsArray.forEach(item => {
            const priceEl = item.querySelector('.item-price');
            const quantityEl = item.querySelector('.item-quantity');
            
            if (priceEl && quantityEl) {
                const price = parseFloat(priceEl.getAttribute('data-price') || 0);
                const quantity = parseInt(quantityEl.value || 1);
                subtotalValue += price * quantity;
            }
        });
        
        // Atualizar subtotal na interface
        const subtotalEl = document.getElementById('subtotal');
        if (subtotalEl) {
            subtotalEl.textContent = `R$ ${subtotalValue.toFixed(2).replace('.', ',')}`;
            subtotalEl.setAttribute('data-value', subtotalValue);
        }
        
        // Calcular total (subtotal - desconto + frete)
        const discountEl = document.getElementById('discount-value');
        const shippingEl = document.getElementById('shipping-value');
        
        const discountValue = discountEl ? parseFloat(discountEl.value || 0) : 0;
        const shippingValue = shippingEl ? parseFloat(shippingEl.value || 0) : 0;
        
        const totalValue = subtotalValue - discountValue + shippingValue;
        
        // Atualizar total na interface
        const totalEl = document.getElementById('total');
        if (totalEl) {
            totalEl.textContent = `R$ ${totalValue.toFixed(2).replace('.', ',')}`;
            totalEl.setAttribute('data-value', totalValue);
        }
    }
<script>
$(document).ready(function() {
    console.log('jQuery inicializado no PDV');
    
    // Função para formatar valor em moeda brasileira
    function formatarMoeda(valor) {
        return 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',');
    }
    
    // Função para buscar produtos
    function buscarProdutos() {
        const searchTerm = $('#product-search').val().trim();
        
        // Verificar se o termo está vazio
        if (searchTerm === '') {
            $('#empty-state').show();
            $('#loading-state, #error-state, #success-state').hide();
            $('#error-message').text('Digite um termo para buscar');
            return;
        }
        
        // Mostrar estado de carregamento
        $('#empty-state, #error-state, #success-state').hide();
        $('#loading-state').show();
        
        // Realizar busca AJAX
        $.ajax({
            url: '{{ route("admin.pos.search") }}',
            type: 'POST',
            data: JSON.stringify({ term: searchTerm }),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Produtos encontrados:', response.products);
                
                // Ocultar todos os estados
                $('#empty-state, #loading-state, #error-state').hide();
                
                // Verificar se encontrou produtos
                if (response.products && response.products.length > 0) {
                    // Atualizar contagem
                    $('#product-count').text(response.products.length);
                    
                    // Limpar e preencher container de produtos
                    const productsContainer = $('#products-container');
                    productsContainer.empty();
                    
                    // Adicionar cada produto encontrado
                    $.each(response.products, function(index, produto) {
                        const produtoHtml = `
                            <div class="w-full sm:w-1/2 md:w-1/2 lg:w-1/3 p-2">
                                <div class="bg-white rounded-lg shadow-md overflow-hidden product-item dark:bg-gray-800" data-id="${produto.id}" data-price="${produto.price}" data-stock="${produto.stock}">
                                    <div class="relative">
                                        <img src="${produto.image}" class="w-full h-40 object-cover" alt="${produto.title}">
                                        <button class="absolute top-2 right-2 p-1.5 bg-white bg-opacity-80 rounded-full dark:bg-gray-800 dark:bg-opacity-80 ${produto.stock > 0 ? 'toggle-wishlist' : 'toggle-wantlist'}" data-product-id="${produto.id}">
                                            ${produto.stock > 0 ? 
                                                '<i class="fas fa-heart text-gray-400 hover:text-red-500"></i>' : 
                                                '<i class="fas fa-flag text-gray-400 hover:text-blue-500"></i>'}
                                        </button>
                                    </div>
                                    <div class="p-3">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">${produto.title}</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${produto.artist}</p>
                                        <div class="flex justify-between items-center mt-2">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">${formatarMoeda(produto.price)}</span>
                                            <span class="text-xs px-2 py-1 rounded-full ${produto.stock > 5 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'}">
                                                ${produto.stock} em estoque
                                            </span>
                                        </div>
                                        <button 
                                            class="add-to-cart w-full mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center justify-center ${produto.stock <= 0 ? 'opacity-50 cursor-not-allowed' : ''}" 
                                            ${produto.stock <= 0 ? 'disabled' : ''}>
                                            <svg class="w-3.5 h-3.5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="9" cy="21" r="1"></circle>
                                                <circle cx="20" cy="21" r="1"></circle>
                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                            </svg>
                                            Adicionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        productsContainer.append(produtoHtml);
                    });
                    
                    // Mostrar resultados
                    $('#success-state').show();
                } else {
                    // Mostrar estado de erro - nenhum produto encontrado
                    $('#error-state').show();
                    $('#error-message').text('Nenhum produto encontrado para "' + searchTerm + '"');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro na busca AJAX:', error);
                $('#empty-state, #loading-state, #success-state').hide();
                $('#error-state').show();
                $('#error-message').text('Erro ao buscar produtos: ' + error);
            }
        });
    }
    
    // Função para gerenciar o carrinho no localStorage
    const cartStorage = {
        getItems: function() {
            const items = localStorage.getItem('pdv_cart_items');
            return items ? JSON.parse(items) : [];
        },
        addItem: function(item) {
            const items = this.getItems();
            // Verificar se o item já existe no carrinho
            const existingItemIndex = items.findIndex(i => i.id === item.id);
            
            if (existingItemIndex >= 0) {
                // Item já existe, incrementar quantidade
                items[existingItemIndex].quantity += item.quantity;
            } else {
                // Novo item
                items.push(item);
            }
            
            localStorage.setItem('pdv_cart_items', JSON.stringify(items));
            return items;
        },
        updateItem: function(itemId, quantity) {
            const items = this.getItems();
            const index = items.findIndex(i => i.id === itemId);
            
            if (index >= 0) {
                items[index].quantity = quantity;
                localStorage.setItem('pdv_cart_items', JSON.stringify(items));
            }
            
            return items;
        },
        removeItem: function(itemId) {
            let items = this.getItems();
            items = items.filter(i => i.id !== itemId);
            localStorage.setItem('pdv_cart_items', JSON.stringify(items));
            return items;
        },
        clearItems: function() {
            localStorage.removeItem('pdv_cart_items');
            return [];
        }
    };
    
    // Função para adicionar produto ao carrinho
    function adicionarAoCarrinho(produto) {
        // Verificar estoque
        if (produto.stock <= 0) {
            alert('Produto sem estoque disponível');
            return;
        }
        
        // Buscar template e container do carrinho
        const cartItems = $('#cart-items');
        const cartItemTemplate = document.getElementById('cart-item-template');
        
        if (cartItems.length && cartItemTemplate) {
            // Clonar o template
            const newItem = $(cartItemTemplate.content.cloneNode(true));
            
            // Preencher dados do produto
            newItem.find('.product-title').text(produto.title);
            newItem.find('.product-artist').text(produto.artist);
            newItem.find('.product-price').text(formatarMoeda(produto.price));
            newItem.find('.product-image').attr('src', produto.image);
            newItem.find('.product-id').val(produto.id);
            newItem.find('.product-quantity').val(1);
            newItem.find('.cart-item').attr('data-price', produto.price);
            newItem.find('.cart-item').attr('data-id', produto.id);
            
            // Adicionar ao carrinho
            cartItems.prepend(newItem);
            
            // Salvar no localStorage
            cartStorage.addItem({
                id: produto.id,
                title: produto.title,
                artist: produto.artist,
                price: produto.price,
                image: produto.image,
                quantity: 1
            });
            
            // Atualizar totais
            atualizarTotaisCarrinho();
            
            // Feedback visual
            alert(`Produto "${produto.title}" adicionado ao carrinho!`);
        }
    }
    
    // Atualizar totais do carrinho
    function atualizarTotaisCarrinho() {
        let subtotalValue = 0;
        let itemCount = 0;
        
        // Calcular subtotal e contagem
        $('.cart-item').each(function() {
            const price = parseFloat($(this).attr('data-price') || 0);
            const qty = parseInt($(this).find('.product-quantity').val() || 1);
            subtotalValue += price * qty;
            itemCount += qty;
        });
        
        // Atualizar interface
        $('#item-count').text(itemCount);
        $('#subtotal').text(formatarMoeda(subtotalValue));
        
        // Calcular outros valores
        const descontoValue = parseFloat($('#discount-value').attr('data-value') || 0);
        const freteValue = parseFloat($('#shipping-value').attr('data-value') || 0);
        const totalValue = subtotalValue - descontoValue + freteValue;
        
        // Atualizar totais
        $('#discount-value').text(formatarMoeda(descontoValue));
        $('#shipping-value').text(formatarMoeda(freteValue));
        $('#total').text(formatarMoeda(totalValue));
    }
    
    // Event Listeners
    
    // Buscar produtos ao clicar no botão
    $('#search-button').on('click', buscarProdutos);
    
    // Buscar produtos ao pressionar Enter no campo de busca
    $('#product-search').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            buscarProdutos();
        }
    });
    
    // Adicionar ao carrinho quando clicar no botão Adicionar
    $(document).on('click', '.add-to-cart', function() {
        const productItem = $(this).closest('.product-item');
        const id = productItem.attr('data-id');
        const title = productItem.find('h3').text();
        const artist = productItem.find('p').text();
        const price = parseFloat(productItem.attr('data-price'));
        const stock = parseInt(productItem.attr('data-stock'));
        const image = productItem.find('img').attr('src');
        
        const produto = {
            id: id,
            title: title,
            artist: artist,
            price: price,
            stock: stock,
            image: image
        };
        
        adicionarAoCarrinho(produto);
    });
    
    // Delegação de eventos para o carrinho
    $('#cart-items').on('click', '.increase-qty', function() {
        const cartItem = $(this).closest('.cart-item');
        const quantityInput = cartItem.find('.product-quantity');
        let quantity = parseInt(quantityInput.val()) || 1;
        quantityInput.val(quantity + 1);
        
        // Atualizar localStorage
        const itemId = cartItem.attr('data-id');
        cartStorage.updateItem(itemId, quantity + 1);
        
        atualizarTotaisCarrinho();
    });
    
    $('#cart-items').on('click', '.decrease-qty', function() {
        const cartItem = $(this).closest('.cart-item');
        const quantityInput = cartItem.find('.product-quantity');
        let quantity = parseInt(quantityInput.val()) || 2;
        if (quantity > 1) {
            quantityInput.val(quantity - 1);
            
            // Atualizar localStorage
            const itemId = cartItem.attr('data-id');
            cartStorage.updateItem(itemId, quantity - 1);
            
            atualizarTotaisCarrinho();
        }
    });
    
    $('#cart-items').on('click', '.remove-item', function() {
        if (confirm('Deseja remover este item do carrinho?')) {
            const cartItem = $(this).closest('.cart-item');
            const itemId = cartItem.attr('data-id');
            
            // Remover do DOM
            cartItem.remove();
            
            // Remover do localStorage
            cartStorage.removeItem(itemId);
            
            atualizarTotaisCarrinho();
        }
    });
    
    $('#cart-items').on('change', '.product-quantity', function() {
        // Garantir valor mínimo de 1
        if (parseInt($(this).val()) < 1) {
            $(this).val(1);
        }
        
        const cartItem = $(this).closest('.cart-item');
        const itemId = cartItem.attr('data-id');
        const quantity = parseInt($(this).val());
        
        // Atualizar localStorage
        cartStorage.updateItem(itemId, quantity);
        
        atualizarTotaisCarrinho();
    });
    
    // Botão para limpar carrinho
    $('#clear-cart').on('click', function() {
        if (confirm('Tem certeza que deseja limpar o carrinho?')) {
            // Limpar DOM
            $('#cart-items').empty();
            
            // Limpar localStorage
            cartStorage.clearItems();
            
            atualizarTotaisCarrinho();
        }
    });
    
    // Gerenciamento de Wishlist (produtos disponíveis) e Wantlist (produtos indisponíveis)
    $(document).on('click', '.toggle-wishlist', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        
        // Verificar se o usuário está logado
        @if(auth()->check())
            $.ajax({
                url: '/wishlist/toggle-favorite',
                type: 'POST',
                data: {
                    product_id: productId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.added) {
                        $(`.toggle-wishlist[data-product-id="${productId}"] i`).removeClass('text-gray-400').addClass('text-red-500');
                    } else {
                        $(`.toggle-wishlist[data-product-id="${productId}"] i`).removeClass('text-red-500').addClass('text-gray-400');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao atualizar wishlist:', error);
                    alert('Erro ao adicionar produto aos favoritos. Tente novamente.');
                }
            });
        @else
            // Usuário não logado - mostrar alerta
            if (confirm('Você precisa estar logado para adicionar produtos aos favoritos. Deseja fazer login agora?')) {
                window.location.href = '{{ route("login") }}';
            }
        @endif
    });
    
    $(document).on('click', '.toggle-wantlist', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        
        // Verificar se o usuário está logado
        @if(auth()->check())
            $.ajax({
                url: '/wantlist/toggle-favorite',
                type: 'POST',
                data: {
                    product_id: productId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.added) {
                        $(`.toggle-wantlist[data-product-id="${productId}"] i`).removeClass('text-gray-400').addClass('text-blue-500');
                    } else {
                        $(`.toggle-wantlist[data-product-id="${productId}"] i`).removeClass('text-blue-500').addClass('text-gray-400');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao atualizar wantlist:', error);
                    alert('Erro ao adicionar produto à lista de espera. Tente novamente.');
                }
            });
        @else
            // Usuário não logado - mostrar alerta
            if (confirm('Você precisa estar logado para adicionar produtos à lista de espera. Deseja fazer login agora?')) {
                window.location.href = '{{ route("login") }}';
            }
        @endif
    });
});
</script>
<!-- Template para itens do carrinho -->
<template id="cart-item-template">
    <div class="flex justify-between items-center py-2 border-b dark:border-gray-700 cart-item" data-price="0">
        <div class="flex items-start">
            <img class="w-10 h-10 rounded object-cover mr-2 product-image" src="" alt="Produto">
            <div class="flex-1">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white product-title">Nome do Produto</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 product-artist">Artista</p>
                <div class="flex items-center mt-1">
                    <button type="button" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 decrease-qty">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" min="1" value="1" class="mx-1 w-12 text-xs text-center border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white product-quantity">
                    <button type="button" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 increase-qty">
                        <i class="fas fa-plus"></i>
                    </button>
                    <input type="hidden" class="product-id" value="">
                </div>
            </div>
            <div class="text-right ml-2">
                <span class="text-sm font-semibold text-gray-900 dark:text-white product-price">R$ 0,00</span>
                <button type="button" class="block text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-xs mt-1 remove-item">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
    </div>
</template>

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM totalmente carregado e analisado');
        
        // Carregar itens do localStorage
        carregarCarrinhoDoLocalStorage();
        
        // Debug para verificar se Flowbite está disponível
        console.log('Flowbite disponível?', typeof initFlowbite === 'function' || typeof window.Flowbite !== 'undefined');
        
    });
    
    // Função para carregar itens do localStorage ao iniciar a página
    function carregarCarrinhoDoLocalStorage() {
        const cartItems = cartStorage.getItems();
        const cartContainer = $('#cart-items');
        const cartItemTemplate = document.getElementById('cart-item-template');
        
        if (cartItems.length > 0 && cartContainer.length && cartItemTemplate) {
            // Limpar o carrinho atual
            cartContainer.empty();
            
            // Adicionar cada item do localStorage
            cartItems.forEach(item => {
                // Clonar o template
                const newItem = $(cartItemTemplate.content.cloneNode(true));
                
                // Preencher dados do produto
                newItem.find('.product-title').text(item.title);
                newItem.find('.product-artist').text(item.artist);
                newItem.find('.product-price').text(formatarMoeda(item.price));
                newItem.find('.product-image').attr('src', item.image);
                newItem.find('.product-id').val(item.id);
                newItem.find('.product-quantity').val(item.quantity);
                newItem.find('.cart-item').attr('data-price', item.price);
                newItem.find('.cart-item').attr('data-id', item.id);
                
                // Adicionar ao carrinho
                cartContainer.append(newItem);
            });
            
            // Atualizar totais
            atualizarTotaisCarrinho();
            
            console.log(`Carregados ${cartItems.length} itens do localStorage para o carrinho`);
        }
        
        // Inicializar componentes do Flowbite
        if (typeof initFlowbite === 'function') {
            console.log('Inicializando Flowbite via initFlowbite()');
            initFlowbite();
        } else if (typeof window.Flowbite !== 'undefined') {
            console.log('Inicializando Flowbite via window.Flowbite');
            // Inicialização alternativa para Flowbite
            const modals = document.querySelectorAll('[data-modal-toggle]');
            console.log('Modais encontrados:', modals.length);
            modals.forEach(modal => {
                const targetId = modal.getAttribute('data-modal-toggle');
                if (targetId) {
                    const targetEl = document.getElementById(targetId);
                    if (targetEl) {
                        new window.Flowbite.Modal(targetEl);
                    }
                }
            });
        } else {
            console.warn('Flowbite não encontrado! Os componentes podem não funcionar corretamente.');
        }
        // Elementos do DOM - com verificação de existência
        const productSearch = document.getElementById('product-search');
        const searchButton = document.getElementById('search-button');
        const searchResults = document.getElementById('search-results');
        const cartItems = document.getElementById('cart-items');
        
        // Verificar se os elementos essenciais foram encontrados
        if (!productSearch) console.error('Elemento #product-search não encontrado!');
        if (!searchButton) console.error('Elemento #search-button não encontrado!');
        if (!searchResults) console.error('Elemento #search-results não encontrado!');
        if (!cartItems) console.error('Elemento #cart-items não encontrado!');
        
        console.log('Elementos principais:', {
            'Campo de busca': productSearch ? 'Encontrado' : 'NÃO ENCONTRADO',
            'Botão de busca': searchButton ? 'Encontrado' : 'NÃO ENCONTRADO',
            'Container de resultados': searchResults ? 'Encontrado' : 'NÃO ENCONTRADO',
            'Container do carrinho': cartItems ? 'Encontrado' : 'NÃO ENCONTRADO'
        });
        
        // Inicializar eventos de busca - removidos, pois agora são gerenciados pelo Alpine.js
        // A busca agora é controlada pelo Alpine.js com x-data="discSearch()"
        console.log('Usando Alpine.js para gerenciar eventos de busca');
        const subtotalEl = document.getElementById('subtotal');
        const discountEl = document.getElementById('discount');
        const discountValueEl = document.getElementById('discount-value');
        const shippingEl = document.getElementById('shipping');
        const shippingValueEl = document.getElementById('shipping-value');
        const totalEl = document.getElementById('total');
        const confirmSaleBtn = document.getElementById('confirm-sale');
        const clearCartBtn = document.getElementById('clear-cart');
        const paymentMethodEl = document.getElementById('payment-method');
        const notesEl = document.getElementById('notes');
        const customerIdEl = document.getElementById('customer-id');
        const customerNameEl = document.getElementById('customer-name');
        const searchCustomerBtn = document.getElementById('search-customer-btn');
        // Referência ao modal do Flowbite (inicializado automaticamente)
        const customerSearchInput = document.getElementById('customer-search-input');
        const searchCustomerButton = document.getElementById('search-customer-button');
        const customerSearchResults = document.getElementById('customer-search-results');
        
        // Templates
        const productTemplate = document.getElementById('product-template').content;
        const cartItemTemplate = document.getElementById('cart-item-template').content;
        
        // Estado do carrinho
        let cart = [];
        let subtotal = 0;
        let discount = 0;
        let shipping = 0;
        let total = 0;
        
        // Formato para valores monetários
        const formatCurrency = (value) => {
            return 'R$ ' + parseFloat(value).toFixed(2).replace('.', ',');
        };
        
        // Função Alpine.js para gerenciar a busca de discos
        function discSearch() {
            return {
                searchTerm: '',
                produtos: [],
                status: 'empty', // empty, loading, error, success
                errorMessage: '',
                
                // Buscar discos no backend
                buscarDiscos() {
                    if (!this.searchTerm.trim()) {
                        this.status = 'empty';
                        return;
                    }
                    
                    this.status = 'loading';
                    
                    // Preparar FormData com o termo de busca e token CSRF
                    const formData = new FormData();
                    formData.append('term', this.searchTerm.trim());
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    // Fazer requisição fetch para a rota de busca
                    fetch('{{ route("admin.pos.search") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Resposta recebida:', response.status);
                        if (!response.ok) {
                            throw new Error(`Erro na requisição: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Dados recebidos:', data);
                        this.produtos = data.products || [];
                        this.status = 'success';
                    })
                    .catch(error => {
                        console.error('Erro na busca:', error);
                        this.errorMessage = error.message || 'Erro ao buscar discos';
                        this.status = 'error';
                    });
                },
                
                // Formatar moeda
                formatarMoeda(valor) {
                    return 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',');
                },
                
                // Adicionar produto ao carrinho
                adicionarAoCarrinho(produto) {
                    console.log('Adicionando ao carrinho:', produto);
                    
                    // Verificar se o produto existe e tem estoque
                    if (!produto || produto.stock <= 0) {
                        alert('Produto indisponível ou sem estoque!');
                        return;
                    }
                    
                    // Aqui você pode implementar a lógica para adicionar ao carrinho
                    // Por exemplo, chamar uma função existente ou atualizar diretamente a lista de itens
                    
                    // Se houver uma função global adicionarProdutoAoCarrinho, chamá-la
                    if (typeof window.adicionarProdutoAoCarrinho === 'function') {
                        window.adicionarProdutoAoCarrinho(produto.id, produto.title, produto.price, 1);
                    } else {
                        // Implementação fallback se a função não existir
                        const cartItems = document.getElementById('cart-items');
                        if (cartItems) {
                            // Adicionar item ao DOM do carrinho
                            const itemHtml = `
                                <div class="flex justify-between items-center py-2 border-b dark:border-gray-700">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">${produto.title}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">${produto.artist}</p>
                                        <div class="flex items-center mt-1">
                                            <button type="button" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <span class="mx-2 text-gray-900 dark:text-white">1</span>
                                            <button type="button" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">${this.formatarMoeda(produto.price)}</span>
                                        <button type="button" class="block text-red-600 dark:text-red-500 hover:text-red-800 dark:hover:text-red-400 mt-1">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                            
                            // Inserir no início da lista
                            cartItems.insertAdjacentHTML('afterbegin', itemHtml);
                            
                            // Atualizar contagem e total
                            this.atualizarTotaisCarrinho();
                        }
                    }
                    
                    // Feedback visual
                    alert(`"${produto.title}" adicionado ao carrinho!`);
                },
                
                // Atualizar totais do carrinho
                atualizarTotaisCarrinho() {
                    // Implementação simplificada - em produção isso seria mais complexo
                    const cartItems = document.querySelectorAll('#cart-items > div');
                    const itemCount = document.getElementById('item-count');
                    const subtotal = document.getElementById('subtotal');
                    const total = document.getElementById('total');
                    
                    if (itemCount) itemCount.textContent = cartItems.length;
                    
                    // Você pode implementar o cálculo real do subtotal e total aqui
                    // Exemplo simplificado:
                    if (subtotal) subtotal.textContent = 'R$ 0,00'; // placeholder
                    if (total) total.textContent = 'R$ 0,00'; // placeholder
                }
            };
        }
            
            // Código alternativo com fetch (caso necessário futuramente)
            /*
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ term: termo })
            })
            .then(response => {
                console.log('Resposta recebida, status:', response.status);
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Dados recebidos:', data);
                processarResultadosBusca(data);
            })
            .catch(error => {
                console.error('Erro na busca:', error);
                searchResults.innerHTML = `
                    <div class="w-full text-center py-6 text-red-600 dark:text-red-500">
                        <p>Erro ao buscar discos: ${error.message}</p>
                        <p class="mt-2 text-sm">Verifique o console para mais detalhes.</p>
                    </div>
                `;
            });
            */
        }
        
        // Função para processar e exibir os resultados
        function processarResultadosBusca(data) {
            if (!searchResults) {
                console.error('Container de resultados não disponível para exibir resultados!');
                return;
            }
            
            // Limpar resultados anteriores
            searchResults.innerHTML = '';
            
            // Verificar se há produtos
            if (!data.products || data.products.length === 0) {
                searchResults.innerHTML = '<div class="w-full text-center py-6 text-gray-500 dark:text-gray-400"><p>Nenhum disco encontrado. Tente outro termo de busca.</p></div>';
                return;
            }
            
            // Criar grid para os resultados
            const grid = document.createElement('div');
            grid.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4';
            searchResults.appendChild(grid);
            
            // Processar cada produto
            data.products.forEach(produto => {
                try {
                    console.log('Processando produto:', produto.id, produto.title);
                    
                    // Criar card do produto diretamente com HTML
                    const cardHtml = `
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden product-item" data-id="${produto.id}" data-price="${produto.price}" data-stock="${produto.stock}">
                            <img src="${produto.image || '{{ asset("assets/images/placeholder.jpg") }}'}" class="w-full h-40 object-cover" alt="${produto.title}">
                            <div class="p-4">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">${produto.title}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${produto.artist}</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">${formatCurrency(produto.price)}</span>
                                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Estoque: ${produto.stock}</span>
                                </div>
                                <button class="add-to-cart-btn w-full mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                    <svg class="w-3.5 h-3.5 inline-block mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="9" cy="21" r="1"></circle>
                                        <circle cx="20" cy="21" r="1"></circle>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                    </svg>
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    `;
                    
                    // Criar elemento para o card
                    const card = document.createElement('div');
                    card.innerHTML = cardHtml;
                    grid.appendChild(card);
                    
                    // Adicionar evento de clique no botão de adicionar
                    card.querySelector('.add-to-cart-btn').addEventListener('click', function() {
                        console.log('Botão adicionar clicado para produto:', produto.id);
                        addToCart(produto);
                    });
                    
                } catch (err) {
                    console.error('Erro ao processar produto:', err, produto);
                }
            });
        }
        };
        
        // Adicionar ao carrinho
        const addToCart = (product) => {
            console.log('Função addToCart chamada com produto:', product);
            
            // Verificar se o produto já está no carrinho
            const existingItem = cart.find(item => item.id === product.id);
            
            if (existingItem) {
                // Verificar estoque
                if (existingItem.quantity < product.stock) {
                    existingItem.quantity++;
                    console.log('Quantidade incrementada para:', existingItem.quantity);
                    updateCartUI();
                    // Feedback visual
                    showToast(`Quantidade de ${product.title} atualizada no carrinho`, 'success');
                } else {
                    showToast(`Quantidade máxima atingida para ${product.title}`, 'warning');
                }
            } else {
                // Adicionar novo item
                const newItem = {
                    id: product.id,
                    title: product.title,
                    artist: product.artist,
                    price: product.price,
                    quantity: 1,
                    stock: product.stock,
                    image: product.image
                };
                
                cart.push(newItem);
                console.log('Novo item adicionado ao carrinho:', newItem);
                updateCartUI();
                
                // Feedback visual
                showToast(`${product.title} adicionado ao carrinho`, 'success');
            }
        };
        
        // Atualizar UI do carrinho
        const updateCartUI = () => {
            console.log('Atualizando UI do carrinho');
            cartItems.innerHTML = '';
            
            if (cart.length === 0) {
                cartItems.innerHTML = '<div class="p-4 text-center text-gray-500 dark:text-gray-400">Nenhum item adicionado ao carrinho.</div>';
                subtotal = 0;
            } else {
                subtotal = 0;
                
                cart.forEach(item => {
                    try {
                        console.log('Renderizando item do carrinho:', item);
                        const clone = document.importNode(cartItemTemplate, true);
                        const itemTotal = item.price * item.quantity;
                        subtotal += itemTotal;
                        
                        // Configurar dados do item
                        clone.querySelector('.cart-item').dataset.id = item.id;
                        clone.querySelector('.cart-item').dataset.price = item.price;
                        clone.querySelector('.item-title').textContent = item.title;
                        clone.querySelector('.item-artist').textContent = item.artist;
                        clone.querySelector('.item-price').textContent = formatCurrency(item.price);
                        clone.querySelector('.item-quantity').value = item.quantity;
                        clone.querySelector('.item-stock').textContent = item.stock;
                        clone.querySelector('.item-total').textContent = formatCurrency(itemTotal);
                        
                        // Eventos dos botões
                        clone.querySelector('.decrease-qty').addEventListener('click', function() {
                            if (item.quantity > 1) {
                                item.quantity--;
                                updateCartUI();
                                showToast(`Quantidade de ${item.title} atualizada`, 'info');
                            }
                        });
                        
                        clone.querySelector('.increase-qty').addEventListener('click', function() {
                            if (item.quantity < item.stock) {
                                item.quantity++;
                                updateCartUI();
                                showToast(`Quantidade de ${item.title} atualizada`, 'info');
                            } else {
                                showToast(`Quantidade máxima atingida para ${item.title}`, 'warning');
                            }
                        });
                        
                        clone.querySelector('.item-quantity').addEventListener('change', function(e) {
                            const newQty = parseInt(e.target.value);
                            if (newQty >= 1 && newQty <= item.stock) {
                                item.quantity = newQty;
                                updateCartUI();
                                showToast(`Quantidade de ${item.title} atualizada`, 'info');
                            } else {
                                e.target.value = item.quantity;
                                showToast(`Quantidade inválida. Deve ser entre 1 e ${item.stock}`, 'error');
                            }
                        });
                        
                        clone.querySelector('.remove-item').addEventListener('click', function() {
                            cart = cart.filter(i => i.id !== item.id);
                            updateCartUI();
                            showToast(`${item.title} removido do carrinho`, 'info');
                        });
                        
                        cartItems.appendChild(clone);
                    } catch (error) {
                        console.error('Erro ao renderizar item do carrinho:', error);
                    }
                });
            }
            
            // Atualizar resumo
            discount = parseFloat(discountEl.value) || 0;
            shipping = parseFloat(shippingEl.value) || 0;
            total = subtotal - discount + shipping;
            
            subtotalEl.textContent = formatCurrency(subtotal);
            discountValueEl.textContent = formatCurrency(discount);
            shippingValueEl.textContent = formatCurrency(shipping);
            totalEl.textContent = formatCurrency(total);
        };
        
        // Buscar clientes
        const searchCustomers = () => {
            const term = customerSearchInput.value.trim();
            if (!term) return;
            
            customerSearchResults.innerHTML = '<div class="text-center my-3"><div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-blue-600 border-r-transparent dark:border-blue-500" role="status"><span class="sr-only">Carregando...</span></div></div>';
            
            fetch('{{ route('admin.pos.search-customers') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ term })
            })
            .then(response => response.json())
            .then(data => {
                customerSearchResults.innerHTML = '';
                
                if (data.customers.length === 0) {
                    customerSearchResults.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 py-3">Nenhum cliente encontrado.</div>';
                    return;
                }
                
                data.customers.forEach(customer => {
                    const item = document.createElement('button');
                    item.className = 'w-full text-left p-3 hover:bg-gray-100 dark:hover:bg-gray-700';
                    item.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">${customer.name}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">${customer.email}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">${customer.cpf || ''}</span>
                            </div>
                        </div>
                    `;
                    
                    item.addEventListener('click', function() {
                        customerIdEl.value = customer.id;
                        customerNameEl.value = customer.name;
                        // Fechar o modal do Flowbite
                        const modalElement = document.getElementById('customer-search-modal');
                        if (typeof window.Flowbite !== 'undefined' && modalElement._modal) {
                            // Flowbite v2+
                            modalElement._modal.hide();
                        } else {
                            // Fallback para outros métodos
                            modalElement.classList.add('hidden');
                            modalElement.setAttribute('aria-hidden', 'true');
                            document.body.classList.remove('overflow-hidden');
                        }
                    });
                    
                    customerSearchResults.appendChild(item);
                });
            })
            .catch(error => {
                console.error('Erro ao buscar clientes:', error);
                customerSearchResults.innerHTML = '<div class="text-center text-red-600 dark:text-red-500 py-3">Erro ao buscar clientes. Tente novamente.</div>';
            });
        };
        
        // Finalizar venda
        const processSale = () => {
            if (cart.length === 0) {
                alert('Adicione pelo menos um produto ao carrinho.');
                return;
            }
            
            const saleData = {
                items: cart.map(item => ({
                    id: item.id,
                    quantity: item.quantity
                })),
                user_id: customerIdEl.value || null,
                customer_name: customerNameEl.value || null,
                payment_method: paymentMethodEl.value,
                discount: discount,
                shipping: shipping,
                notes: notesEl.value
            };
            
            confirmSaleBtn.disabled = true;
            confirmSaleBtn.innerHTML = '<div class="inline-block h-4 w-4 mr-2 animate-spin rounded-full border-2 border-solid border-white border-r-transparent" role="status"></div> Processando...';
            
            fetch('{{ route('admin.pos.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(saleData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Venda realizada com sucesso! Nota: ${data.invoice_number}`);
                    // Limpar carrinho
                    cart = [];
                    updateCartUI();
                    // Limpar formulário
                    customerIdEl.value = '';
                    customerNameEl.value = '';
                    discountEl.value = 0;
                    shippingEl.value = 0;
                    notesEl.value = '';
                    paymentMethodEl.value = 'money';
                    // Redirecionar para detalhes da venda
                    window.location.href = '{{ route('admin.pos.index') }}';
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro ao processar venda:', error);
                alert('Erro ao processar a venda. Tente novamente.');
            })
            .finally(() => {
                confirmSaleBtn.disabled = false;
                confirmSaleBtn.innerHTML = '<svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> Finalizar Venda';
            });
        };
        
        // Toast de notificações
        const showToast = (message, type = 'info') => {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            let iconSvg = '';
            let bgClass = '';
            
            switch(type) {
                case 'success':
                    bgClass = 'bg-green-500 dark:bg-green-600';
                    iconSvg = '<svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                    break;
                case 'error':
                    bgClass = 'bg-red-500 dark:bg-red-600';
                    iconSvg = '<svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
                    break;
                case 'warning':
                    bgClass = 'bg-yellow-500 dark:bg-yellow-600';
                    iconSvg = '<svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>';
                    break;
                default: // info
                    bgClass = 'bg-blue-500 dark:bg-blue-600';
                    iconSvg = '<svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>';
            }
            
            toast.className = `flex items-center w-full max-w-xs p-4 mb-3 ${bgClass} rounded-lg shadow text-white`;
            toast.innerHTML = `
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                    ${iconSvg}
                </div>
                <div class="ml-3 text-sm font-normal">${message}</div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 text-white hover:text-gray-200 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex items-center justify-center h-8 w-8" onclick="this.parentElement.remove()">
                    <span class="sr-only">Fechar</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            `;
            
            toastContainer.appendChild(toast);
            
            // Auto-remove após 3 segundos
            setTimeout(() => {
                if (toast && toast.parentNode) {
                    toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3000);
        };
        
        // Configurar eventos com verificação de existência
        console.log('Configurando eventos de busca...');
        
        if (searchButton) {
            console.log('Adicionando evento de clique ao botão de busca');
            searchButton.addEventListener('click', function(e) {
                console.log('Botão de busca clicado');
                e.preventDefault();
                buscarDiscos();
            });
        } else {
            console.error('Não foi possível adicionar evento ao botão de busca (elemento não encontrado)');
        }
        
        if (productSearch) {
            console.log('Adicionando evento de tecla ao campo de busca');
            productSearch.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    console.log('Tecla Enter pressionada no campo de busca');
                    e.preventDefault();
                    buscarDiscos();
                }
            });
        } else {
            console.error('Não foi possível adicionar evento ao campo de busca (elemento não encontrado)');
        }
        
        clearCartBtn.addEventListener('click', function() {
            if (confirm('Tem certeza que deseja limpar o carrinho?')) {
                cart = [];
                updateCartUI();
            }
        });
        
        discountEl.addEventListener('change', updateCartUI);
        shippingEl.addEventListener('change', updateCartUI);
        
        confirmSaleBtn.addEventListener('click', processSale);
        
        // Eventos de busca de cliente
        searchCustomerBtn.addEventListener('click', function() {
            customerSearchInput.value = '';
            customerSearchResults.innerHTML = '';
            // Mostrar o modal do Flowbite
            const $targetEl = document.getElementById('customer-search-modal');
            if (typeof window.Flowbite !== 'undefined') {
                // Flowbite v2+
                const modal = new window.Flowbite.Modal($targetEl);
                modal.show();
            } else {
                // Fallback para outros métodos
                $targetEl.classList.remove('hidden');
                $targetEl.setAttribute('aria-hidden', 'false');
                document.body.classList.add('overflow-hidden');
            }
        });
        
        searchCustomerButton.addEventListener('click', searchCustomers);
        customerSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchCustomers();
        });
        
        // Inicializar UI
        updateCartUI();
    });
</script>
@endsection
