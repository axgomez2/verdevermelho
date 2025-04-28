@extends('layouts.admin')

@section('content')
<div class="p-4">
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">Discos Mais Vistos</h1>
        <a href="{{ route('admin.reports.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
            Voltar
        </a>
    </div>

    <!-- Filtros -->
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
        <form action="{{ route('admin.reports.most-viewed') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="period" class="block mb-2 text-sm font-medium text-gray-900">Período</label>
                <select id="period" name="period" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>Todos os tempos</option>
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hoje</option>
                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Esta semana</option>
                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Este mês</option>
                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Este ano</option>
                </select>
            </div>
            
            <div>
                <label for="limit" class="block mb-2 text-sm font-medium text-gray-900">Mostrar</label>
                <select id="limit" name="limit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="20" {{ request('limit') == 20 ? 'selected' : '' }}>20 por página</option>
                    <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50 por página</option>
                    <option value="100" {{ request('limit') == 100 ? 'selected' : '' }}>100 por página</option>
                </select>
            </div>
            
            <div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de discos -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Disco</th>
                        <th scope="col" class="px-6 py-3">Artista</th>
                        <th scope="col" class="px-6 py-3">Visualizações</th>
                        <th scope="col" class="px-6 py-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vinyls as $vinyl)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900 flex items-center">
                            @if($vinyl->cover_image)
                            <div class="flex-shrink-0 h-10 w-10 mr-3">
                                <img class="h-10 w-10 rounded-sm object-cover" src="{{ Storage::url($vinyl->cover_image) }}" alt="{{ $vinyl->title }}">
                            </div>
                            @endif
                            <span>{{ $vinyl->title }}</span>
                        </td>
                        <td class="px-6 py-4">
                            {{ $vinyl->artists->pluck('name')->implode(', ') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-lg font-semibold">{{ number_format($vinyl->view_count, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-3">
                                <a href="{{ route('admin.reports.vinyl-details', $vinyl->id) }}" class="font-medium text-blue-600 hover:underline">
                                    Detalhes
                                </a>
                                <a href="{{ route('admin.vinyls.edit', $vinyl->id) }}" class="font-medium text-green-600 hover:underline">
                                    Editar Disco
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="bg-white border-b">
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            Nenhum disco encontrado para o período selecionado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        <div class="px-6 py-4">
            {{ $vinyls->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
