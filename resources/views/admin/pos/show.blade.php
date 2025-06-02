@extends('layouts.admin')

@section('title', 'Detalhes da Venda PDV')

@section('content')
<div class="p-4">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">Detalhes da Venda #{{ $sale->invoice_number }}</h1>
    <nav class="flex mb-5" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('admin.pos.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">PDV</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('admin.pos.sales') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Vendas</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $sale->invoice_number }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Informações da venda -->
        <div class="w-full lg:w-2/3">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg mb-6">
                <div class="flex justify-between items-center px-6 py-4 border-b dark:border-gray-700">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z"></path>
                            <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path>
                            <path d="M12 17.5v.5"></path>
                            <path d="M12 6v.5"></path>
                        </svg>
                        <h2 class="text-lg font-medium text-gray-700 dark:text-gray-200">Detalhes da Venda</h2>
                    </div>
                    <a href="{{ route('admin.pos.sales') }}" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:ring-4 focus:ring-gray-100 dark:text-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                        <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5"></path>
                            <path d="M12 19l-7-7 7-7"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Informações da Venda</h3>
                            <div class="space-y-2">
                                <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">Número da Nota:</span> {{ $sale->invoice_number }}</p>
                                <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">Data:</span> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                                <div class="flex items-center mt-1">
                                    <span class="font-medium text-gray-700 dark:text-gray-300 text-sm mr-2">Método de Pagamento:</span>
                                    @switch($sale->payment_method)
                                        @case('money')
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Dinheiro</span>
                                            @break
                                        @case('credit_card')
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">Cartão de Crédito</span>
                                            @break
                                        @case('debit_card')
                                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-purple-900 dark:text-purple-300">Cartão de Débito</span>
                                            @break
                                        @case('pix')
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-yellow-900 dark:text-yellow-300">PIX</span>
                                            @break
                                        @case('transfer')
                                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">Transferência</span>
                                            @break
                                        @default
                                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">{{ $sale->payment_method }}</span>
                                    @endswitch
                                </div>
                                @if($sale->notes)
                                    <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">Observações:</span> {{ $sale->notes }}</p>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Cliente</h3>
                            <div class="space-y-2">
                                @if($sale->customer)
                                    <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">Nome:</span> {{ $sale->customer->name }}</p>
                                    <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">Email:</span> {{ $sale->customer->email }}</p>
                                    @if($sale->customer->phone)
                                        <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">Telefone:</span> {{ $sale->customer->phone }}</p>
                                    @endif
                                    @if($sale->customer->cpf)
                                        <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">CPF:</span> {{ $sale->customer->cpf }}</p>
                                    @endif
                                @elseif($sale->customer_name)
                                    <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">Nome:</span> {{ $sale->customer_name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Cliente não cadastrado</p>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Cliente não identificado</p>
                                @endif
                                
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mt-4 mb-2">Vendedor</h3>
                                <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">Nome:</span> {{ $sale->seller_name }}</p>
                                @if($sale->seller)
                                    <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">Email:</span> {{ $sale->seller->email }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Itens da Venda</h3>
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Disco</th>
                                    <th scope="col" class="px-6 py-3">Artista</th>
                                    <th scope="col" class="px-6 py-3">Preço Unit.</th>
                                    <th scope="col" class="px-6 py-3">Qtd</th>
                                    <th scope="col" class="px-6 py-3">Desconto</th>
                                    <th scope="col" class="px-6 py-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($item->vinylSec && $item->vinylSec->vinyl && $item->vinylSec->vinyl->cover_image)
                                                <img src="{{ asset('storage/' . $item->vinylSec->vinyl->cover_image) }}" 
                                                    alt="{{ $item->vinylSec->vinyl->title }}" 
                                                    class="mr-2 w-10 h-10 object-cover rounded" 
                                                    loading="lazy">
                                            @else
                                                <div class="mr-2 w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                            @endif
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $item->vinylSec->vinyl->title ?? 'Produto não disponível' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($item->vinylSec && $item->vinylSec->vinyl && $item->vinylSec->vinyl->artists)
                                            {{ $item->vinylSec->vinyl->artists->pluck('name')->implode(', ') }}
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4">R$ {{ number_format($item->item_discount, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 font-medium">R$ {{ number_format($item->item_total, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo financeiro -->
        <div class="w-full lg:w-1/3">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg mb-6">
                <div class="flex items-center px-6 py-4 border-b dark:border-gray-700">
                    <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                        <line x1="6" y1="12" x2="6" y2="12"></line>
                        <line x1="10" y1="12" x2="10" y2="12"></line>
                        <line x1="14" y1="12" x2="14" y2="12"></line>
                        <line x1="18" y1="12" x2="18" y2="12"></line>
                    </svg>
                    <h2 class="text-lg font-medium text-gray-700 dark:text-gray-200">Resumo Financeiro</h2>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <span class="text-gray-700 dark:text-gray-300">R$ {{ number_format($sale->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-600 dark:text-gray-400">Desconto:</span>
                        <span class="text-red-600 dark:text-red-400">- R$ {{ number_format($sale->discount, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-600 dark:text-gray-400">Frete:</span>
                        <span class="text-gray-700 dark:text-gray-300">R$ {{ number_format($sale->shipping, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                        <span class="font-semibold text-gray-800 dark:text-white">Total:</span>
                        <span class="font-bold text-lg text-gray-800 dark:text-white">R$ {{ number_format($sale->total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Ações disponíveis -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg mb-6">
                <div class="flex items-center px-6 py-4 border-b dark:border-gray-700">
                    <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    <h2 class="text-lg font-medium text-gray-700 dark:text-gray-200">Ações</h2>
                </div>
                <div class="p-6 space-y-3">
                    <button onclick="window.print()" class="w-full flex justify-center items-center px-4 py-2 text-sm font-medium text-blue-700 bg-white border border-blue-700 rounded-lg hover:bg-blue-50 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-500 dark:hover:bg-gray-700 dark:focus:ring-blue-800">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        Imprimir Comprovante
                    </button>
                    <a href="{{ route('admin.pos.index') }}" class="w-full flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 dark:bg-green-700 dark:hover:bg-green-600 dark:focus:ring-green-800">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        Nova Venda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @media print {
        header, footer, nav[aria-label="Breadcrumb"], nav, button, a {
            display: none !important;
        }
        body {
            padding: 0;
            margin: 0;
            background-color: white;
        }
        .p-4 {
            padding: 0 !important;
        }
        .gap-6 {
            gap: 0 !important;
        }
        .lg\:w-2\/3 {
            width: 100% !important;
            max-width: 100% !important;
        }
        .lg\:w-1\/3 {
            display: none !important;
        }
        .shadow-md, .rounded-lg {
            box-shadow: none !important;
            border-radius: 0 !important;
        }
        .border-b {
            border: none !important;
        }
        .text-2xl {
            font-size: 18pt !important;
        }
        .dark\:bg-gray-800 {
            background-color: white !important;
        }
        .dark\:text-white, .text-gray-700, .text-gray-800 {
            color: black !important;
        }
    }
</style>
@endsection
