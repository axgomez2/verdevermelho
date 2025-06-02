<div id="addressModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="addressForm" action="{{ route('address.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        {{ __('Adicionar novo endereço') }}
                    </h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Tipo de endereço:') }}</label>
                            <select id="type" name="type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="Casa">{{ __('casa - principal)') }}</option>
                                <option value="Trabalho">{{ __('comercial') }}</option>
                                <option value="Entrega">{{ __('entrega somente') }}</option>
                                <option value="Cobrança">{{ __('cobrança') }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="zip_code" class="block text-sm font-medium text-gray-700">{{ __('cep') }}</label>
                            <input type="text" name="zip_code" id="zip_code" required maxlength="8" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="street" class="block text-sm font-medium text-gray-700">{{ __('Logradouro') }}</label>
                            <input type="text" name="street" id="street" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="number" class="block text-sm font-medium text-gray-700">{{ __('Numero') }}</label>
                            <input type="text" name="number" id="number" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="complement" class="block text-sm font-medium text-gray-700">{{ __('Complemento') }}</label>
                            <input type="text" name="complement" id="complement" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="neighborhood" class="block text-sm font-medium text-gray-700">{{ __('Bairro') }}</label>
                            <input type="text" name="neighborhood" id="neighborhood" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">{{ __('Cidade') }}</label>
                            <input type="text" name="city" id="city" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">{{ __('UF') }}</label>
                            <input type="text" name="state" id="state" required maxlength="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="is_default" class="inline-flex items-center">
                                <input type="checkbox" name="is_default" id="is_default" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">{{ __('Esse é meu endereço principal') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Salvar Endereço') }}
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeAddressModal()">
                        {{ __('Cancelar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddressModal() {
    document.getElementById('addressModal').classList.remove('hidden');
}

function closeAddressModal() {
    document.getElementById('addressModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addressForm');
    
    // Adicionar loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    
    function setLoading(isLoading) {
        if (isLoading) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Salvando...';
        } else {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    }
    
    // Remover mensagens de erro anteriores
    function clearErrors() {
        const errorElements = form.querySelectorAll('.text-red-500');
        errorElements.forEach(element => element.remove());
        
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.classList.remove('border-red-500', 'ring-red-500');
            input.classList.add('border-gray-300', 'ring-indigo-500');
        });
    }
    
    // Mostrar mensagens de erro
    function showErrors(errors) {
        for (const field in errors) {
            const input = document.getElementById(field);
            if (input) {
                input.classList.remove('border-gray-300', 'ring-indigo-500');
                input.classList.add('border-red-500', 'ring-red-500');
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-red-500 text-sm mt-1';
                errorDiv.textContent = errors[field][0];
                
                input.parentNode.appendChild(errorDiv);
            }
        }
    }
    
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        clearErrors();
        setLoading(true);

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 422) {
                    return response.json().then(data => {
                        throw new Error('ValidationError:' + JSON.stringify(data.errors));
                    });
                }
                throw new Error('Erro na requisição: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            setLoading(false);
            if (data.success) {
                // Exibir mensagem de sucesso em toast ou notificação
                const successToast = document.createElement('div');
                successToast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                successToast.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>${data.message || 'Endereço adicionado com sucesso!'}</span>
                    </div>
                `;
                document.body.appendChild(successToast);
                
                // Remover a notificação após 3 segundos
                setTimeout(() => {
                    successToast.remove();
                }, 3000);
                
                closeAddressModal();

                // Coletar dados do endereço da resposta
                const address = data.address;
                
                // Atualizar a interface para mostrar o novo endereço
                const addressList = document.querySelector('.address-list');
                const noAddressMessage = document.querySelector('.text-yellow-600');

                if (noAddressMessage) {
                    noAddressMessage.remove();
                }

                // Verificar se já existe uma lista de endereços
                if (!addressList) {
                    // Criar uma nova lista se não existir
                    const container = document.querySelector('section > div');
                    if (container) {
                        const countMessage = document.createElement('p');
                        countMessage.className = 'mb-4';
                        countMessage.textContent = 'Você adicionou um novo endereço:';
                        container.insertBefore(countMessage, container.firstChild);

                        const newAddressList = document.createElement('div');
                        newAddressList.className = 'address-list space-y-4';
                        container.insertBefore(newAddressList, countMessage.nextSibling);
                    }
                } else {
                    // Atualizar a contagem de endereços se a lista já existir
                    const countMessage = addressList.previousElementSibling;
                    if (countMessage) {
                        // Extrair o número com segurança
                        let count = 1;
                        const matches = countMessage.textContent.match(/\d+/);
                        if (matches && matches.length > 0) {
                            count = parseInt(matches[0]) + 1;
                        }
                        countMessage.textContent = `Você tem ${count} endereço(s) cadastrado(s):`;
                    }
                }

                // Criar o elemento de exibição do endereço
                const newAddress = document.createElement('div');
                newAddress.className = 'p-4 border rounded';
                
                // Verificar o objeto de endereço retornado
                if (address) {
                    // Usar dados do endereço retornado pela API
                    newAddress.innerHTML = `
                        <p><strong>${address.type || 'Endereço'}</strong></p>
                        <p>${address.street || ''}, ${address.number || ''}</p>
                        ${address.complement ? `<p>${address.complement}</p>` : ''}
                        <p>${address.neighborhood || ''}</p>
                        <p>${address.city || ''} - ${address.state || ''}</p>
                        <p>${address.zip_code || ''}</p>
                        ${address.is_default ? `<p class="mt-2 text-green-600">Endereço Principal</p>` : ''}
                    `;
                } else if (data && data.address) {
                    // Verificar data.address (formato alternativo)
                    newAddress.innerHTML = `
                        <p><strong>${data.address.type || 'Endereço'}</strong></p>
                        <p>${data.address.street || ''}, ${data.address.number || ''}</p>
                        ${data.address.complement ? `<p>${data.address.complement}</p>` : ''}
                        <p>${data.address.neighborhood || ''}</p>
                        <p>${data.address.city || ''} - ${data.address.state || ''}</p>
                        <p>${data.address.zip_code || ''}</p>
                        ${data.address.is_default ? `<p class="mt-2 text-green-600">Endereço Principal</p>` : ''}
                    `;
                } else if (data && data.message) {
                    // Se tivermos apenas uma mensagem de sucesso, mas sem objeto de endereço
                    newAddress.innerHTML = `<p>Endereço adicionado com sucesso!</p>`;
                } else {
                    // Se não tiver endereço na resposta, usar os dados do formulário
                    const formType = document.getElementById('type').value || 'Endereço';
                    const formStreet = document.getElementById('street').value || '';
                    const formNumber = document.getElementById('number').value || '';
                    const formComplement = document.getElementById('complement').value || '';
                    const formNeighborhood = document.getElementById('neighborhood').value || '';
                    const formCity = document.getElementById('city').value || '';
                    const formState = document.getElementById('state').value || '';
                    const formZipCode = document.getElementById('zip_code').value || '';
                    const formIsDefault = document.getElementById('is_default').checked;
                    
                    newAddress.innerHTML = `
                        <p><strong>${formType}</strong></p>
                        <p>${formStreet}, ${formNumber}</p>
                        ${formComplement ? `<p>${formComplement}</p>` : ''}
                        <p>${formNeighborhood}</p>
                        <p>${formCity} - ${formState}</p>
                        <p>${formZipCode}</p>
                        ${formIsDefault ? `<p class="mt-2 text-green-600">Endereço Principal</p>` : ''}
                    `;
                }

                // Adicionar o novo endereço à lista
                const addressListUpdated = document.querySelector('.address-list');
                if (addressListUpdated) {
                    addressListUpdated.appendChild(newAddress);
                }

                // Limpar formulário
                form.reset();
                
                // Processar cálculo de frete com o novo CEP
                const zipCode = address ? address.zip_code : document.getElementById('zip_code').value;
                if (zipCode) {
                    const cleanedZipCode = zipCode.replace(/\D/g, '');
                    
                    // Mostrar indicador de carregamento do frete
                    const loadingIndicator = document.createElement('div');
                    loadingIndicator.className = 'fixed bottom-4 right-4 bg-blue-500 text-white px-4 py-2 rounded shadow-lg z-50';
                    loadingIndicator.innerHTML = `
                        <div class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Calculando frete...</span>
                        </div>
                    `;
                    document.body.appendChild(loadingIndicator);
                    
                    // Fazer a requisição para calcular o frete
                    fetch(`/carrinho/shipping/options/${cleanedZipCode}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Frete calculado com sucesso:', data);
                        loadingIndicator.remove();
                        
                        // Recarregar a página para mostrar as opções de frete
                        window.location.href = '/carrinho';
                    })
                    .catch(error => {
                        console.error('Erro ao calcular frete:', error);
                        loadingIndicator.remove();
                        
                        // Notificar o usuário sobre o erro, mas ainda recarregar a página
                        const errorToast = document.createElement('div');
                        errorToast.className = 'fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50';
                        errorToast.innerHTML = `
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Erro ao calcular frete. Recarregando página...</span>
                            </div>
                        `;
                        document.body.appendChild(errorToast);
                        
                        setTimeout(() => {
                            window.location.href = '/carrinho';
                        }, 2000);
                    });
                }
            } else {
                // Exibir mensagem de erro mais amigável
                setLoading(false);
                console.error('Erro ao salvar endereço:', data.message);
                
                const errorToast = document.createElement('div');
                errorToast.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50';
                errorToast.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>${data.message || 'Erro ao salvar endereço. Tente novamente.'}</span>
                    </div>
                `;
                document.body.appendChild(errorToast);
                
                // Remover a notificação após 5 segundos
                setTimeout(() => {
                    errorToast.remove();
                }, 5000);
                
                // Se houver erros de validação, exibir nos campos
                if (data.errors) {
                    showErrors(data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred: ' + error.message);
        });
    });
});

document.getElementById('zip_code').addEventListener('blur', function() {
    let cep = this.value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('street').value = data.logradouro;
                    document.getElementById('neighborhood').value = data.bairro;
                    document.getElementById('city').value = data.localidade;
                    document.getElementById('state').value = data.uf;
                }
            })
            .catch(error => console.error('Error:', error));
    }
});
</script>

<script>

</script>
