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
                                <input type="checkbox" name="is_default" id="is_default" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
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
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeAddressModal();

                const addressList = document.querySelector('.address-list');
                const noAddressMessage = document.querySelector('.text-yellow-600');

                if (noAddressMessage) {
                    noAddressMessage.remove();
                }

                if (!addressList) {
                    const container = document.querySelector('section > div');
                    const countMessage = document.createElement('p');
                    countMessage.className = 'mb-4';
                    countMessage.textContent = 'Voce adicionou um novo endereço:';
                    container.insertBefore(countMessage, container.firstChild);

                    const newAddressList = document.createElement('div');
                    newAddressList.className = 'address-list space-y-4';
                    container.insertBefore(newAddressList, countMessage.nextSibling);
                } else {
                    const countMessage = addressList.previousElementSibling;
                    const count = parseInt(countMessage.textContent.match(/\d+/)[0]) + 1;
                    countMessage.textContent = `You have ${count} address(es) registered:`;
                }

                const newAddress = document.createElement('div');
                newAddress.className = 'p-4 border rounded';
                newAddress.innerHTML = `
                    <p><strong>${data.address.type}</strong></p>
                    <p>${data.address.street}, ${data.address.number}</p>
                    ${data.address.complement ? `<p>${data.address.complement}</p>` : ''}
                    <p>${data.address.neighborhood}</p>
                    <p>${data.address.city} - ${data.address.state}</p>
                    <p>${data.address.zip_code}</p>
                    ${data.address.is_default ? `<p class="mt-2 text-green-600">Default Address</p>` : ''}
                `;

                document.querySelector('.address-list').appendChild(newAddress);

                form.reset();
            } else {
                console.error('Failed to save address:', data.message);
                alert('Failed to save address: ' + data.message);
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
