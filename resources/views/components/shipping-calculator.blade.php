@props(['shippingOptions', 'postalCode' => null])

<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm" x-data="{
    loading: false,
    postalCode: '{{ $postalCode ?? session('shipping_postal_code') }}',
    error: '',
    options: {{ json_encode($shippingOptions) }},
    selectedOption: '{{ session("selected_shipping_option") }}',
    formatPostalCode(input) {
        const cleaned = input.replace(/\D/g, '');
        if (cleaned.length > 5) {
            return cleaned.substring(0, 5) + '-' + cleaned.substring(5, 8);
        }
        return cleaned;
    },
    async calculateShipping() {
        if (this.postalCode.length < 8) {
            this.error = 'CEP inválido. O CEP deve ter 8 dígitos.';
            return;
        }
        
        const cleanedPostalCode = this.postalCode.replace(/\D/g, '');
        this.loading = true;
        this.error = '';
        
        try {
            // Usar a rota nomeada para garantir a URL correta
            const response = await fetch(`{{ route('site.cart.getShippingOptions', '') }}/${cleanedPostalCode}`);
            const data = await response.json();
            
            if (data.success) {
                this.options = data.options;
                this.error = '';
            } else {
                this.error = data.error;
                this.options = [];
            }
        } catch (error) {
            this.error = 'Erro ao calcular frete. Tente novamente.';
            console.error('Erro:', error);
        } finally {
            this.loading = false;
        }
    },
    async selectShippingOption(optionId) {
        this.loading = true;
        
        try {
            const response = await fetch('{{ route("site.cart.updateShipping") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    shipping_option: optionId
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.selectedOption = optionId;
                window.location.reload();
            } else {
                this.error = data.error || 'Erro ao selecionar opção de frete';
            }
        } catch (error) {
            this.error = 'Erro ao selecionar opção de frete';
            console.error('Erro:', error);
        } finally {
            this.loading = false;
        }
    }
}">
    <h3 class="mb-4 text-lg font-medium text-gray-900">Calcular Frete</h3>

    <!-- CEP Input Form -->
    <div class="space-y-4">
        <div class="space-y-2">
            <label for="postal_code" class="text-sm font-medium text-gray-700">CEP</label>
            <div class="flex space-x-2">
                <input type="text"
                       id="postal_code"
                       x-model="postalCode"
                       x-on:input="postalCode = formatPostalCode($event.target.value)"
                       x-on:keyup.enter="calculateShipping"
                       class="flex-grow rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                       placeholder="00000-000"
                       required />
                <button type="button"
                        x-on:click="calculateShipping"
                        x-bind:disabled="loading"
                        class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50">
                    <span x-show="!loading">Calcular</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Calculando...
                    </span>
                </button>
            </div>
            <p class="text-xs text-gray-500">Digite o CEP sem traço</p>
        </div>
    </div>

    <!-- Error Messages -->
    <div class="mt-4 rounded-md bg-red-50 p-4" x-show="error" x-cloak>
        <p class="text-sm text-red-700" x-text="error"></p>
    </div>

    @if(session('shipping_error'))
        <div class="mt-4 rounded-md bg-red-50 p-4">
            <p class="text-sm text-red-700">{{ session('shipping_error') }}</p>
        </div>
    @endif

    <!-- Shipping Options -->
    <div class="mt-4 space-y-3" x-show="options.length > 0" x-cloak>
        <h4 class="text-sm font-medium text-gray-900">Opções de Envio:</h4>
        <template x-for="option in options" :key="option.id">
            <div class="flex items-center justify-between rounded-md border border-gray-200 p-3 hover:bg-gray-50 transition-colors duration-150" 
                 :class="{ 'bg-blue-50 border-blue-200': selectedOption == option.id }">
                <div class="flex items-center space-x-3">
                    <input type="radio"
                           name="shipping_option"
                           :id="'shipping_' + option.id"
                           :value="option.id"
                           :checked="selectedOption == option.id"
                           @change="selectShippingOption(option.id)"
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500" />
                    <label :for="'shipping_' + option.id" class="flex flex-col">
                        <span class="text-sm font-medium text-gray-900" x-text="option.name"></span>
                        <span class="text-xs text-gray-500">
                            Entrega em <span x-text="option.delivery_time"></span> dias úteis
                        </span>
                    </label>
                </div>
                <span class="text-sm font-medium text-gray-900" x-text="'R$ ' + new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(option.price)"></span>
            </div>
        </template>
    </div>
    
    <!-- Fallback for non-Alpine browsers and initial page load -->
    @if(isset($shippingOptions) && count($shippingOptions) > 0)
        <div class="mt-4 space-y-3" x-show="options.length == 0">
            <h4 class="text-sm font-medium text-gray-900">Opções de Envio:</h4>
            @foreach($shippingOptions as $option)
                <div class="flex items-center justify-between rounded-md border border-gray-200 p-3 hover:bg-gray-50 transition-colors duration-150 {{ session('selected_shipping_option') == $option['id'] ? 'bg-blue-50 border-blue-200' : '' }}">
                    <div class="flex items-center space-x-3">
                        <input type="radio"
                               name="shipping_option"
                               id="shipping_static_{{ $option['id'] }}"
                               value="{{ $option['id'] }}"
                               {{ session('selected_shipping_option') == $option['id'] ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500" />
                        <label for="shipping_static_{{ $option['id'] }}" class="flex flex-col">
                            <span class="text-sm font-medium text-gray-900">{{ $option['name'] }}</span>
                            <span class="text-xs text-gray-500">
                                Entrega em {{ $option['delivery_time'] }} dias úteis
                            </span>
                        </label>
                    </div>
                    <span class="text-sm font-medium text-gray-900">
                        R$ {{ number_format($option['price'], 2, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CEP mask
        const postalCodeInput = document.getElementById('postal_code');
        if (postalCodeInput) {
            postalCodeInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 8) value = value.substr(0, 8);
                if (value.length > 5) {
                    value = value.substr(0, 5) + '-' + value.substr(5);
                }
                e.target.value = value;
            });
        }

        // Shipping option selection
        const shippingOptions = document.querySelectorAll('input[name="shipping_option"]');
        shippingOptions.forEach(option => {
            option.addEventListener('change', function() {
                fetch('{{ route("site.cart.updateShipping") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        shipping_option: this.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.error || 'Erro ao atualizar o frete');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro ao atualizar o frete');
                });
            });
        });
    });
</script>
@endpush
