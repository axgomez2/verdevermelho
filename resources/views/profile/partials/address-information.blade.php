<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Endereços Cadastrados') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Gerencie seus endereços de entrega e cobrança") }}
        </p>
    </header>

    <div class="mt-6">

        @if($user->addresses->isNotEmpty())
            @foreach($user->addresses as $address)
            <div class="p-4 border rounded">
                <p><strong>{{ $address->type }}</strong></p>
                <p>{{ $address->street }}, {{ $address->number }}</p>
                @if($address->complement)
                    <p>{{ $address->complement }}</p>
                @endif
                <p>{{ $address->neighborhood }}</p>
                <p>{{ $address->city }} - {{ $address->state }}</p>
                <p>{{ $address->zip_code }}</p>
                @if($address->is_default)
                    <p class="mt-2 text-green-600">{{ __('Default Address') }}</p>
                @endif
            </div>
            @endforeach
        @else
            <p>Nenhum endereço cadastrado.</p>
        @endif




        <button type="button" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="openAddressModal()">
            {{ __('Adicionar novo endereço') }}
        </button>
    </div>
</section>
