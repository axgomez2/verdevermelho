@extends('layouts.admin')

@section('content')
<div class="p-4">
    <div class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">Lista de Inscritos na Newsletter</h1>
        <a href="{{ route('admin.newsletter.compose') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Enviar E-mail
        </a>
    </div>

    <!-- Filtros -->
    <div class="mb-4 p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <form action="{{ route('admin.newsletter.index') }}" method="GET" class="flex flex-wrap gap-3">
            <div class="flex-grow min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar por e-mail</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="min-w-[150px]">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            
            <div class="self-end">
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Filtrar
                </button>
                
                <a href="{{ route('admin.newsletter.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 ml-2">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabela de inscritos -->
    <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        E-mail
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Data de Inscrição
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($subscribers as $subscriber)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $subscriber->email }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($subscriber->is_active)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Ativo
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inativo
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $subscriber->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <div class="flex justify-center space-x-2">
                            <form action="{{ route('admin.newsletter.toggle', $subscriber) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $subscriber->is_active ? 'Desativar' : 'Ativar' }}
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.newsletter.destroy', $subscriber) }}" method="POST" 
                                onsubmit="return confirm('Tem certeza que deseja excluir esta inscrição?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 ml-2">
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                        Nenhum inscrito encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Paginação -->
    <div class="mt-4">
        {{ $subscribers->links() }}
    </div>
</div>
@endsection
