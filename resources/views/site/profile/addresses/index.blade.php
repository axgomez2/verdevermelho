<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">Meus Endereços</h1>
                <a href="{{ route('site.profile.addresses.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:ring-primary-300">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    Adicionar Endereço
                </a>
            </div>

            @if(session('success'))
                <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    <span class="font-medium">Sucesso!</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Erro!</span> {{ session('error') }}
                </div>
            @endif

            @if($addresses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($addresses as $address)
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 sm:p-6 relative {{ $address->is_default ? 'ring-2 ring-primary-500' : '' }}">
                            @if($address->is_default)
                                <span class="absolute top-2 right-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    Padrão
                                </span>
                            @endif
                            
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $address->type }}</h3>
                            <p class="text-sm text-gray-700">
                                {{ $address->street }}, {{ $address->number }}
                                @if($address->complement)
                                    - {{ $address->complement }}
                                @endif
                            </p>
                            <p class="text-sm text-gray-700 mb-4">
                                {{ $address->neighborhood }}, {{ $address->city }}/{{ $address->state }} - CEP: {{ substr_replace($address->zip_code, '-', 5, 0) }}
                            </p>
                            
                            <div class="flex space-x-3">
                                <a href="{{ route('site.profile.addresses.edit', $address) }}" class="text-sm font-medium text-primary-600 hover:underline flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    Editar
                                </a>
                                
                                @if(!$address->is_default || $addresses->count() > 1)
                                    <form action="{{ route('site.profile.addresses.destroy', $address) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-600 hover:underline flex items-center" onclick="return confirm('Tem certeza que deseja excluir este endereço?')">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Excluir
                                        </button>
                                    </form>
                                @endif
                                
                                @if(!$address->is_default)
                                    <form action="{{ route('site.profile.addresses.update', $address) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="type" value="{{ $address->type }}">
                                        <input type="hidden" name="zip_code" value="{{ $address->zip_code }}">
                                        <input type="hidden" name="street" value="{{ $address->street }}">
                                        <input type="hidden" name="number" value="{{ $address->number }}">
                                        <input type="hidden" name="complement" value="{{ $address->complement }}">
                                        <input type="hidden" name="neighborhood" value="{{ $address->neighborhood }}">
                                        <input type="hidden" name="city" value="{{ $address->city }}">
                                        <input type="hidden" name="state" value="{{ $address->state }}">
                                        <input type="hidden" name="is_default" value="1">
                                        <button type="submit" class="text-sm font-medium text-blue-600 hover:underline flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Tornar padrão
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 text-center">
                    <p class="text-gray-500 mb-4">Você não possui endereços cadastrados.</p>
                    <a href="{{ route('site.profile.addresses.create') }}" class="inline-flex items-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                        Adicionar Endereço
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
