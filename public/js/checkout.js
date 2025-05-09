document.addEventListener('DOMContentLoaded', function() {
    // Elementos para gerenciamento de endereços
    const addressModal = document.getElementById('address-modal');
    const addressesContainer = document.getElementById('addresses-container');
    const newAddressModal = document.getElementById('new-address-modal');
    const addNewAddressBtn = document.getElementById('add-new-address-btn');
    const saveNewAddressBtn = document.getElementById('save-new-address');
    const newAddressForm = document.getElementById('new-address-form');
    
    // Elementos para gerenciamento de frete
    const shippingModal = document.getElementById('shipping-modal');
    const postalCodeInput = document.getElementById('postal-code');
    const calculateShippingBtn = document.getElementById('calculate-shipping-btn');
    const shippingOptionsContainer = document.getElementById('shipping-options');
    
    // Elementos do formulário de pagamento
    const creditCardRadio = document.getElementById('credit_card');
    const pixRadio = document.getElementById('pix');
    const creditCardForm = document.getElementById('credit-card-form');
    const pixForm = document.getElementById('pix-form');
    
    // Elementos do formulário de cartão de crédito
    const cardName = document.getElementById('cc-name');
    const cardNumber = document.getElementById('cc-number');
    const cardCvv = document.getElementById('cc-cvv');
    const cardExpirationMonth = document.getElementById('cc-expiration-month');
    const cardExpirationYear = document.getElementById('cc-expiration-year');
    const documentNumber = document.getElementById('cc-document-number');
    const installmentsSelect = document.getElementById('cc-installments');
    const checkoutForm = document.getElementById('payment-form');
    
    // Campos ocultos para informações do cartão
    const cardHolderNameHidden = document.getElementById('card_holder_name');
    const cardNumberHidden = document.getElementById('card_number');
    const cardCvvHidden = document.getElementById('card_cvv');
    const cardExpirationMonthHidden = document.getElementById('card_expiration_month');
    const cardExpirationYearHidden = document.getElementById('card_expiration_year');
    const cardToken = document.getElementById('card_token');
    
    // Inicialização
    if (creditCardRadio && pixRadio) {
        togglePaymentMethod();
        
        creditCardRadio.addEventListener('change', togglePaymentMethod);
        pixRadio.addEventListener('change', togglePaymentMethod);
    }
    
    // Função para alternar método de pagamento
    function togglePaymentMethod() {
        if (creditCardRadio.checked) {
            creditCardForm.classList.remove('hidden');
            if (pixForm) pixForm.classList.add('hidden');
        } else if (pixRadio.checked) {
            if (creditCardForm) creditCardForm.classList.add('hidden');
            pixForm.classList.remove('hidden');
        }
    }
    
    // Carregar endereços quando o modal for aberto
    if (addressModal) {
        addressModal.addEventListener('shown.bs.modal', loadAddresses);
    }
    
    // Abrir modal de novo endereço quando o botão for clicado
    if (addNewAddressBtn) {
        addNewAddressBtn.addEventListener('click', function() {
            const addressModalInstance = document.getElementById('address-modal');
            const modal = new Modal(addressModalInstance);
            modal.hide();
            
            setTimeout(function() {
                const newAddressModalInstance = document.getElementById('new-address-modal');
                const newModal = new Modal(newAddressModalInstance);
                newModal.show();
            }, 300);
        });
    }
    
    // Salvar novo endereço
    if (saveNewAddressBtn) {
        saveNewAddressBtn.addEventListener('click', saveNewAddress);
    }
    
    // Calcular frete quando o botão for clicado
    if (calculateShippingBtn) {
        calculateShippingBtn.addEventListener('click', calculateShipping);
    }
    
    // Função para carregar endereços
    function loadAddresses() {
        if (!addressesContainer) return;
        
        fetch('/api/addresses', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderAddresses(data.addresses);
            } else {
                addressesContainer.innerHTML = `
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                        <span class="font-medium">Erro!</span> ${data.message || 'Erro ao carregar endereços.'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erro ao carregar endereços:', error);
            addressesContainer.innerHTML = `
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <span class="font-medium">Erro!</span> Ocorreu um erro ao carregar os endereços.
                </div>
            `;
        });
    }
    
    // Função para renderizar endereços
    function renderAddresses(addresses) {
        if (!addressesContainer) return;
        
        if (addresses.length === 0) {
            addressesContainer.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-gray-500 mb-4">Você não possui endereços cadastrados.</p>
                    <button id="add-new-address-btn-empty" type="button" class="inline-flex items-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                        Adicionar Endereço
                    </button>
                </div>
            `;
            
            document.getElementById('add-new-address-btn-empty').addEventListener('click', function() {
                const addressModalInstance = document.getElementById('address-modal');
                const modal = new Modal(addressModalInstance);
                modal.hide();
                
                setTimeout(function() {
                    const newAddressModalInstance = document.getElementById('new-address-modal');
                    const newModal = new Modal(newAddressModalInstance);
                    newModal.show();
                }, 300);
            });
            
            return;
        }
        
        let html = '<div class="grid gap-4 grid-cols-1 md:grid-cols-2">';
        
        addresses.forEach(address => {
            html += `
                <div class="bg-white rounded-lg border border-gray-200 p-4 relative ${address.is_default ? 'ring-2 ring-primary-500' : ''}">
                    ${address.is_default ? '<span class="absolute top-2 right-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">Padrão</span>' : ''}
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">${address.type}</h3>
                    <p class="text-sm text-gray-700">
                        ${address.street}, ${address.number}
                        ${address.complement ? '- ' + address.complement : ''}
                    </p>
                    <p class="text-sm text-gray-700 mb-4">
                        ${address.neighborhood}, ${address.city}/${address.state} - CEP: ${address.zip_code.replace(/(\d{5})(\d{3})/, '$1-$2')}
                    </p>
                    <div class="flex space-x-3">
                        <button type="button" class="select-address-btn text-sm font-medium text-primary-600 hover:underline flex items-center" data-address-id="${address.id}">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            Selecionar
                        </button>
                        ${!address.is_default ? `
                            <button type="button" class="set-default-address-btn text-sm font-medium text-blue-600 hover:underline flex items-center" data-address-id="${address.id}">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Definir como padrão
                            </button>
                        ` : ''}
                        <button type="button" class="delete-address-btn text-sm font-medium text-red-600 hover:underline flex items-center" data-address-id="${address.id}">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Excluir
                        </button>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        addressesContainer.innerHTML = html;
        
        // Adicionar event listeners
        document.querySelectorAll('.select-address-btn').forEach(button => {
            button.addEventListener('click', function() {
                const addressId = this.getAttribute('data-address-id');
                selectAddress(addressId, addresses);
            });
        });
        
        document.querySelectorAll('.set-default-address-btn').forEach(button => {
            button.addEventListener('click', function() {
                const addressId = this.getAttribute('data-address-id');
                setDefaultAddress(addressId);
            });
        });
        
        document.querySelectorAll('.delete-address-btn').forEach(button => {
            button.addEventListener('click', function() {
                const addressId = this.getAttribute('data-address-id');
                deleteAddress(addressId);
            });
        });
    }
    
    // Função para selecionar um endereço
    function selectAddress(addressId, addresses) {
        const address = addresses.find(addr => addr.id.toString() === addressId.toString());
        if (!address) return;
        
        // Atualizar input hidden
        const shippingAddressIdInput = document.getElementById('shipping_address_id');
        if (shippingAddressIdInput) {
            shippingAddressIdInput.value = addressId;
        }
        
        // Atualizar display de endereço
        const selectedAddressDisplay = document.getElementById('selected-address-display');
        if (selectedAddressDisplay) {
            selectedAddressDisplay.innerHTML = `
                <h3 class="text-base font-medium text-gray-900">${address.type}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    ${address.street}, ${address.number}
                    ${address.complement ? '- ' + address.complement : ''}
                </p>
                <p class="text-sm text-gray-500">
                    ${address.neighborhood}, ${address.city}/${address.state} - CEP: ${address.zip_code.replace(/(\d{5})(\d{3})/, '$1-$2')}
                </p>
            `;
        }
        
        // Atualizar CEP para cálculo de frete automaticamente
        const postalCodeField = document.getElementById('postal-code');
        if (postalCodeField) {
            postalCodeField.value = address.zip_code.replace(/(\d{5})(\d{3})/, '$1-$2');
        }
        
        // Fechar o modal
        const addressModalInstance = document.getElementById('address-modal');
        const modal = new Modal(addressModalInstance);
        modal.hide();
        
        // Mostrar notificação de sucesso
        showNotification('Endereço selecionado com sucesso!', 'success');
    }
    
    // Função para definir endereço como padrão
    function setDefaultAddress(addressId) {
        fetch(`/api/addresses/${addressId}/default`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadAddresses(); // Recarregar a lista de endereços
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Erro ao definir endereço como padrão.', 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao definir endereço como padrão:', error);
            showNotification('Ocorreu um erro ao definir o endereço como padrão.', 'error');
        });
    }
    
    // Função para excluir endereço
    function deleteAddress(addressId) {
        if (!confirm('Tem certeza que deseja excluir este endereço?')) return;
        
        fetch(`/api/addresses/${addressId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadAddresses(); // Recarregar a lista de endereços
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Erro ao excluir endereço.', 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao excluir endereço:', error);
            showNotification('Ocorreu um erro ao excluir o endereço.', 'error');
        });
    }
    
    // Função para salvar novo endereço
    function saveNewAddress() {
        const formData = new FormData(newAddressForm);
        const formObject = {};
        
        formData.forEach((value, key) => {
            if (key === 'is_default') {
                formObject[key] = true;
            } else {
                formObject[key] = value;
            }
        });
        
        fetch('/api/addresses', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formObject),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Limpar formulário
                newAddressForm.reset();
                
                // Fechar modal de novo endereço
                const newAddressModalInstance = document.getElementById('new-address-modal');
                const newModal = new Modal(newAddressModalInstance);
                newModal.hide();
                
                // Atualizar seleção de endereço
                selectAddress(data.address.id, [data.address]);
                
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Erro ao salvar endereço.', 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao salvar endereço:', error);
            showNotification('Ocorreu um erro ao salvar o endereço.', 'error');
        });
    }
    
    // Busca de CEP para novo endereço
    const zipCodeInput = document.getElementById('zip_code');
    const searchCepButton = document.getElementById('search-cep');
    const streetInput = document.getElementById('street');
    const neighborhoodInput = document.getElementById('neighborhood');
    const cityInput = document.getElementById('city');
    const stateInput = document.getElementById('state');
    
    if (zipCodeInput) {
        zipCodeInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            this.value = value;
        });
    }
    
    if (searchCepButton) {
        searchCepButton.addEventListener('click', function() {
            const cep = zipCodeInput.value.replace(/\D/g, '');
            
            if (cep.length !== 8) {
                showNotification('Por favor, digite um CEP válido com 8 dígitos.', 'error');
                return;
            }
            
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        showNotification('CEP não encontrado. Por favor, verifique o número informado.', 'error');
                        return;
                    }
                    
                    streetInput.value = data.logradouro;
                    neighborhoodInput.value = data.bairro;
                    cityInput.value = data.localidade;
                    stateInput.value = data.uf;
                })
                .catch(error => {
                    console.error('Erro ao buscar CEP:', error);
                    showNotification('Ocorreu um erro ao buscar o CEP. Por favor, tente novamente.', 'error');
                });
        });
    }
    
    // Função para calcular frete
    function calculateShipping() {
        const postalCode = postalCodeInput.value.replace(/\D/g, '');
        
        if (postalCode.length !== 8) {
            showNotification('Por favor, digite um CEP válido com 8 dígitos.', 'error');
            return;
        }
        
        // Mostrar loader
        shippingOptionsContainer.innerHTML = `
            <div class="flex justify-center items-center h-32">
                <div role="status">
                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin fill-primary-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                    <span class="sr-only">Carregando...</span>
                </div>
            </div>
        `;
        
        fetch('/api/shipping/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ postal_code: postalCode }),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderShippingOptions(data.shipping_options);
            } else {
                shippingOptionsContainer.innerHTML = `
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                        <span class="font-medium">Erro!</span> ${data.message || 'Erro ao calcular frete.'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erro ao calcular frete:', error);
            shippingOptionsContainer.innerHTML = `
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <span class="font-medium">Erro!</span> Ocorreu um erro ao calcular o frete.
                </div>
            `;
        });
    }
    
    // Função para renderizar opções de frete
    function renderShippingOptions(options) {
        if (!shippingOptionsContainer) return;
        
        if (options.length === 0) {
            shippingOptionsContainer.innerHTML = `
                <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                    <span class="font-medium">Atenção!</span> Não encontramos opções de frete disponíveis para este CEP.
                </div>
            `;
            return;
        }
        
        let html = '<div class="space-y-3">';
        
        options.forEach(option => {
            html += `
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-base font-medium text-gray-900">${option.name}</h3>
                            <p class="text-sm text-gray-500">
                                Prazo de entrega: ${option.delivery_time} dias úteis
                            </p>
                        </div>
                        <div class="flex items-center">
                            <span class="text-lg font-semibold text-gray-900 mr-3">R$ ${option.price.toFixed(2).replace('.', ',')}</span>
                            <button type="button" class="select-shipping-btn text-sm font-medium text-white bg-primary-700 hover:bg-primary-800 rounded-lg px-3 py-1.5" 
                                data-shipping-id="${option.id}" 
                                data-shipping-name="${option.name}" 
                                data-shipping-price="${option.price}">
                                Selecionar
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        shippingOptionsContainer.innerHTML = html;
        
        // Adicionar event listeners
        document.querySelectorAll('.select-shipping-btn').forEach(button => {
            button.addEventListener('click', function() {
                const shippingId = this.getAttribute('data-shipping-id');
                const shippingName = this.getAttribute('data-shipping-name');
                const shippingPrice = this.getAttribute('data-shipping-price');
                selectShippingOption(shippingId, shippingName, shippingPrice);
            });
        });
    }
    
    // Função para selecionar opção de frete
    function selectShippingOption(shippingId, shippingName, shippingPrice) {
        fetch('/api/shipping/select', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                shipping_option: shippingId,
                shipping_name: shippingName,
                shipping_price: shippingPrice
            }),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar a exibição do frete na página
                const shippingDisplay = document.querySelector('.space-y-4 h3.text-base.font-medium');
                if (shippingDisplay) {
                    shippingDisplay.textContent = shippingName;
                }
                
                const shippingPriceDisplay = document.querySelector('.space-y-4 span.text-lg.font-semibold');
                if (shippingPriceDisplay) {
                    shippingPriceDisplay.textContent = `R$ ${parseFloat(shippingPrice).toFixed(2).replace('.', ',')}`;
                }
                
                // Fechar o modal
                const shippingModalInstance = document.getElementById('shipping-modal');
                const modal = new Modal(shippingModalInstance);
                modal.hide();
                
                // Recarregar a página para atualizar os totais
                location.reload();
            } else {
                showNotification(data.message || 'Erro ao selecionar opção de frete.', 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao selecionar opção de frete:', error);
            showNotification('Ocorreu um erro ao selecionar a opção de frete.', 'error');
        });
    }
    
    // Função para mostrar notificações
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed bottom-4 right-4 p-4 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
        notification.innerHTML = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Funções para formatação de cartão de crédito
    
    // Função para formatar o número do cartão de crédito
    function formatCardNumber(value) {
        // Remove todos os caracteres não numéricos
        let cardNumber = value.replace(/\D/g, '');
        // Limita a 16 dígitos
        cardNumber = cardNumber.substring(0, 16);
        // Adiciona espaços a cada 4 dígitos
        cardNumber = cardNumber.replace(/(.{4})/g, '$1 ').trim();
        return cardNumber;
    }
    
    // Função para formatar o CVV
    function formatCvv(value) {
        return value.replace(/\D/g, '').substring(0, 4);
    }
    
    // Função para formatar o CPF
    function formatCpf(value) {
        let cpf = value.replace(/\D/g, '');
        cpf = cpf.substring(0, 11);
        cpf = cpf.replace(/^(\d{3})(\d)/, '$1.$2');
        cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        return cpf;
    }
    
    // Adiciona formatação ao número do cartão durante a digitação
    if (cardNumber) {
        cardNumber.addEventListener('input', function() {
            this.value = formatCardNumber(this.value);
        });
    }
    
    // Adiciona formatação ao CVV durante a digitação
    if (cardCvv) {
        cardCvv.addEventListener('input', function() {
            this.value = formatCvv(this.value);
        });
    }
    
    // Adiciona formatação ao CPF durante a digitação
    if (documentNumber) {
        documentNumber.addEventListener('input', function() {
            this.value = formatCpf(this.value);
        });
    }
    
    // Submit do formulário
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            if (creditCardRadio && creditCardRadio.checked) {
                // Verificação de campos obrigatórios para o cartão de crédito
                if (!cardName.value || !cardNumber.value || !cardCvv.value || 
                    !cardExpirationMonth.value || !cardExpirationYear.value || 
                    !documentNumber.value) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos do cartão de crédito.');
                    return;
                }
                
                // Preenche os campos ocultos para envio
                cardHolderNameHidden.value = cardName.value;
                cardNumberHidden.value = cardNumber.value.replace(/\D/g, '');
                cardCvvHidden.value = cardCvv.value;
                cardExpirationMonthHidden.value = cardExpirationMonth.value;
                cardExpirationYearHidden.value = cardExpirationYear.value;
                
                // Para integração com a Rede Itaú, pode não ser necessário gerar token aqui
                // como era com o PagSeguro, já que isso é tratado no backend
            }
        });
    }
});
