<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">Adicionar Endereço</h1>
                    <a href="{{ route('site.profile.addresses.index') }}" class="text-sm font-medium text-primary-600 hover:underline flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Voltar para endereços
                    </a>
                </div>
            </div>

            @if(session('error'))
                <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Erro!</span> {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 sm:p-6">
                <form action="{{ route('site.profile.addresses.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                        <div class="sm:col-span-2">
                            <label for="type" class="block mb-2 text-sm font-medium text-gray-900">Tipo de Endereço</label>
                            <select id="type" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                <option value="Residencial" {{ old('type') == 'Residencial' ? 'selected' : '' }}>Residencial</option>
                                <option value="Comercial" {{ old('type') == 'Comercial' ? 'selected' : '' }}>Comercial</option>
                                <option value="Outro" {{ old('type') == 'Outro' ? 'selected' : '' }}>Outro</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-1">
                            <label for="zip_code" class="block mb-2 text-sm font-medium text-gray-900">CEP</label>
                            <input type="text" name="zip_code" id="zip_code" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="00000-000" value="{{ old('zip_code') }}" maxlength="9" required>
                            @error('zip_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-1">
                            <button type="button" id="search-cep" class="h-10 mt-8 px-4 py-2 text-sm font-medium text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:ring-primary-300">
                                Buscar CEP
                            </button>
                        </div>
                        
                        <div class="sm:col-span-2">
                            <label for="street" class="block mb-2 text-sm font-medium text-gray-900">Rua</label>
                            <input type="text" name="street" id="street" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="Nome da rua" value="{{ old('street') }}" required>
                            @error('street')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-1">
                            <label for="number" class="block mb-2 text-sm font-medium text-gray-900">Número</label>
                            <input type="text" name="number" id="number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="Número" value="{{ old('number') }}" required>
                            @error('number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-1">
                            <label for="complement" class="block mb-2 text-sm font-medium text-gray-900">Complemento</label>
                            <input type="text" name="complement" id="complement" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="Apto, bloco, etc." value="{{ old('complement') }}">
                            @error('complement')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-2">
                            <label for="neighborhood" class="block mb-2 text-sm font-medium text-gray-900">Bairro</label>
                            <input type="text" name="neighborhood" id="neighborhood" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="Bairro" value="{{ old('neighborhood') }}" required>
                            @error('neighborhood')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-1">
                            <label for="city" class="block mb-2 text-sm font-medium text-gray-900">Cidade</label>
                            <input type="text" name="city" id="city" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="Cidade" value="{{ old('city') }}" required>
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-1">
                            <label for="state" class="block mb-2 text-sm font-medium text-gray-900">Estado</label>
                            <select id="state" name="state" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                                <option value="">Selecione</option>
                                <option value="AC" {{ old('state') == 'AC' ? 'selected' : '' }}>Acre</option>
                                <option value="AL" {{ old('state') == 'AL' ? 'selected' : '' }}>Alagoas</option>
                                <option value="AP" {{ old('state') == 'AP' ? 'selected' : '' }}>Amapá</option>
                                <option value="AM" {{ old('state') == 'AM' ? 'selected' : '' }}>Amazonas</option>
                                <option value="BA" {{ old('state') == 'BA' ? 'selected' : '' }}>Bahia</option>
                                <option value="CE" {{ old('state') == 'CE' ? 'selected' : '' }}>Ceará</option>
                                <option value="DF" {{ old('state') == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                <option value="ES" {{ old('state') == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                <option value="GO" {{ old('state') == 'GO' ? 'selected' : '' }}>Goiás</option>
                                <option value="MA" {{ old('state') == 'MA' ? 'selected' : '' }}>Maranhão</option>
                                <option value="MT" {{ old('state') == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                <option value="MS" {{ old('state') == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                <option value="MG" {{ old('state') == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                <option value="PA" {{ old('state') == 'PA' ? 'selected' : '' }}>Pará</option>
                                <option value="PB" {{ old('state') == 'PB' ? 'selected' : '' }}>Paraíba</option>
                                <option value="PR" {{ old('state') == 'PR' ? 'selected' : '' }}>Paraná</option>
                                <option value="PE" {{ old('state') == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                <option value="PI" {{ old('state') == 'PI' ? 'selected' : '' }}>Piauí</option>
                                <option value="RJ" {{ old('state') == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                <option value="RN" {{ old('state') == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                <option value="RS" {{ old('state') == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                <option value="RO" {{ old('state') == 'RO' ? 'selected' : '' }}>Rondônia</option>
                                <option value="RR" {{ old('state') == 'RR' ? 'selected' : '' }}>Roraima</option>
                                <option value="SC" {{ old('state') == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                <option value="SP" {{ old('state') == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                <option value="SE" {{ old('state') == 'SE' ? 'selected' : '' }}>Sergipe</option>
                                <option value="TO" {{ old('state') == 'TO' ? 'selected' : '' }}>Tocantins</option>
                            </select>
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-2">
                            <div class="flex items-center">
                                <input id="is_default" name="is_default" type="checkbox" value="1" {{ old('is_default') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                <label for="is_default" class="ml-2 text-sm font-medium text-gray-900">Definir como endereço padrão</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="mt-6 inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200 hover:bg-primary-800">
                        Salvar Endereço
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const zipCodeInput = document.getElementById('zip_code');
            const searchCepButton = document.getElementById('search-cep');
            const streetInput = document.getElementById('street');
            const neighborhoodInput = document.getElementById('neighborhood');
            const cityInput = document.getElementById('city');
            const stateInput = document.getElementById('state');
            
            // Aplicar máscara ao CEP
            zipCodeInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 5) {
                    value = value.substring(0, 5) + '-' + value.substring(5, 8);
                }
                this.value = value;
            });
            
            // Buscar endereço pelo CEP
            searchCepButton.addEventListener('click', function() {
                const cep = zipCodeInput.value.replace(/\D/g, '');
                
                if (cep.length !== 8) {
                    alert('Por favor, digite um CEP válido com 8 dígitos.');
                    return;
                }
                
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.erro) {
                            alert('CEP não encontrado. Por favor, verifique o número informado.');
                            return;
                        }
                        
                        streetInput.value = data.logradouro;
                        neighborhoodInput.value = data.bairro;
                        cityInput.value = data.localidade;
                        stateInput.value = data.uf;
                    })
                    .catch(error => {
                        console.error('Erro ao buscar CEP:', error);
                        alert('Ocorreu um erro ao buscar o CEP. Por favor, tente novamente.');
                    });
            });
        });
    </script>
    @endpush
</x-app-layout>
