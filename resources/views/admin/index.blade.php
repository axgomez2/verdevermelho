@extends('layouts.admin')

@section('title', 'Início')

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['title' => 'Início', 'url' => route('admin.dashboard')]
    ]" />
@endsection

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800">
            Início
        </h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-sm font-medium text-gray-500">Total de discos cadastrados</h3>
                <p class="text-3xl font-bold">{{ $totalVinyls }}</p>
                <p class="text-sm text-gray-600">Total de discos cadastrados no banco de dados</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-sm font-medium text-gray-500">Cadastros Completos</h3>
                <p class="text-3xl font-bold">{{ $completedVinyls }}</p>
                <p class="text-sm text-gray-600">Discos com cadastros completos</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-sm font-medium text-gray-500">Discos sem estoque</h3>
                <p class="text-3xl font-bold">{{ $outOfStockVinyls }}</p>
                <p class="text-sm text-gray-600">Discos sem estoque na base</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-sm font-medium text-gray-500">Total de usuários</h3>
                <p class="text-3xl font-bold">{{ $totalCustomers }}</p>
                <p class="text-sm text-gray-600">Usuários essa semana: 0</p>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-xl font-semibold mb-4">Últimos pedidos</h3>
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Sem pedidos no momento</h4>
                    <p class="text-gray-600">
                        Quando o primeiro pedido chegar, ele vai aparecer aqui para você.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

