<!-- Modal para seleção de produtos -->
<div x-data="productSelector()" x-init="initialize()" x-cloak>
    <!-- Botão para abrir o modal -->
    <button type="button" 
            @click="openModal" 
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Adicionar Produtos ao Email
    </button>

    <!-- Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Selecionar Produtos para o Email
                            </h3>
                            
                            <!-- Busca de produtos -->
                            <div class="mb-4">
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <input type="text" 
                                            x-model="searchTerm" 
                                            @keydown.enter.prevent="searchProducts"
                                            placeholder="Buscar por título, artista ou gênero" 
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <button type="button" 
                                            @click="searchProducts"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Buscar
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Resultados da busca -->
                            <div class="border rounded-md p-3 max-h-96 overflow-y-auto">
                                <!-- Mensagem de carregamento -->
                                <div x-show="searchPerformed && !searchResults.length" class="py-4 text-center">
                                    <p x-show="searchTerm && searchPerformed" class="text-gray-500">Buscando produtos...</p>
                                    <p x-show="!searchTerm" class="text-gray-500">Digite algo para buscar produtos</p>
                                </div>
                                
                                <!-- Grid de resultados -->
                                <div x-show="searchResults.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <template x-for="product in searchResults" :key="product.id">
                                        <div class="border rounded-md p-3 flex items-start space-x-3" :class="{'bg-blue-50': isSelected(product.id)}">
                                            <div class="flex-shrink-0 w-16 h-16 overflow-hidden rounded-md">
                                                <img :src="product.image" :alt="product.title" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate" x-text="product.title"></p>
                                                <p class="text-sm text-gray-500 truncate" x-text="product.artist"></p>
                                                <p class="text-sm font-bold text-gray-900" x-text="product.price"></p>
                                            </div>
                                            <div>
                                                <button type="button" 
                                                        @click="toggleProductSelection(product)"
                                                        :class="isSelected(product.id) ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                                                        class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <svg x-show="!isSelected(product.id)" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    <svg x-show="isSelected(product.id)" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Mensagem quando não há resultados -->
                            <div class="text-center py-6" x-show="searchPerformed && searchResults.length === 0">
                                <p class="text-gray-500">Nenhum produto encontrado com os termos buscados.</p>
                            </div>
                            
                            <!-- Produtos selecionados -->
                            <div class="mt-6" x-show="selectedProducts.length > 0">
                                <h4 class="text-md font-medium text-gray-900 mb-2">Produtos Selecionados (<span x-text="selectedProducts.length"></span>)</h4>
                                <div class="border rounded-md p-3 max-h-40 overflow-y-auto">
                                    <ul class="divide-y divide-gray-200">
                                        <template x-for="product in selectedProducts" :key="product.id">
                                            <li class="py-2 flex justify-between items-center">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex-shrink-0 w-10 h-10 overflow-hidden rounded-md">
                                                        <img :src="product.image" :alt="product.title" class="w-full h-full object-cover">
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="product.title"></p>
                                                        <p class="text-sm text-gray-500 truncate" x-text="product.artist"></p>
                                                    </div>
                                                </div>
                                                <button type="button" 
                                                        @click="removeProduct(product.id)"
                                                        class="inline-flex items-center p-1 border border-gray-300 rounded-md text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" 
                            @click="insertProductsToEditor"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                            :disabled="selectedProducts.length === 0">
                        Inserir no Email
                    </button>
                    <button type="button" 
                            @click="closeModal" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function productSelector() {
        return {
            showModal: false,
            searchTerm: '',
            searchPerformed: false,
            searchResults: [],
            selectedProducts: [],
            
            initialize() {
                // Inicializar os produtos já selecionados anteriormente (se existir)
                const existingProducts = document.getElementById('selectedProducts').value;
                if (existingProducts) {
                    try {
                        this.selectedProducts = JSON.parse(existingProducts);
                    } catch (e) {
                        console.error('Erro ao carregar produtos selecionados:', e);
                    }
                }
            },
            
            openModal() {
                this.showModal = true;
            },
            
            closeModal() {
                this.showModal = false;
            },
            
            searchProducts() {
                if (!this.searchTerm.trim()) return;
                
                this.searchResults = [];
                this.searchPerformed = true;
                
                // Fazer requisição AJAX para buscar produtos
                fetch(`/admin/relatorios/mailing/buscar-produtos?q=${encodeURIComponent(this.searchTerm)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Erro na resposta: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (Array.isArray(data)) {
                            this.searchResults = data;
                        } else if (data && data.error) {
                            console.error('Erro retornado pela API:', data.error);
                            this.searchResults = [];
                        } else {
                            console.error('Formato de resposta inválido');
                            this.searchResults = [];
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar produtos:', error);
                        this.searchResults = [];
                    });
            },
            
            isSelected(productId) {
                return this.selectedProducts.some(p => p.id === productId);
            },
            
            toggleProductSelection(product) {
                if (this.isSelected(product.id)) {
                    this.removeProduct(product.id);
                } else {
                    this.selectedProducts.push(product);
                    // Atualizar o campo hidden com a lista de produtos selecionados
                    document.getElementById('selectedProducts').value = JSON.stringify(this.selectedProducts);
                }
            },
            
            removeProduct(productId) {
                this.selectedProducts = this.selectedProducts.filter(p => p.id !== productId);
                // Atualizar o campo hidden com a lista de produtos selecionados
                document.getElementById('selectedProducts').value = JSON.stringify(this.selectedProducts);
            },
            
            insertProductsToEditor() {
                if (!this.selectedProducts.length) return;
                
                // Gerar HTML para os produtos selecionados
                let productsHtml = 
                `<div style="margin: 20px 0;">
                    <h2 style="font-size: 20px; margin-bottom: 15px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px;">Produtos em Destaque</h2>
                    <div style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: center;">`;
                
                this.selectedProducts.forEach(product => {
                    productsHtml += `
                        <div style="width: 180px; border: 1px solid #eee; border-radius: 8px; overflow: hidden; margin-bottom: 15px;">
                            <a href="${product.url}" style="text-decoration: none; color: inherit;">
                                <img src="${product.image}" alt="${product.title}" style="width: 100%; height: 180px; object-fit: cover;">
                                <div style="padding: 10px;">
                                    <h3 style="font-size: 14px; font-weight: bold; margin: 0 0 5px 0; color: #333;">${product.title}</h3>
                                    <p style="font-size: 12px; color: #777; margin: 0 0 5px 0;">${product.artist}</p>
                                    <p style="font-size: 16px; font-weight: bold; color: #e51717; margin: 0;">${product.price}</p>
                                </div>
                            </a>
                        </div>`;
                });
                
                productsHtml += `
                    </div>
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="{{ url('/discos') }}" style="display: inline-block; background-color: #e51717; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px;">Ver Todos os Produtos</a>
                    </div>
                </div>`;
                
                // Inserir HTML no editor TinyMCE
                if (typeof tinymce !== 'undefined') {
                    tinymce.activeEditor.execCommand('mceInsertContent', false, productsHtml);
                } else {
                    // Fallback para textarea simples
                    const textarea = document.getElementById('content');
                    const cursorPos = textarea.selectionStart;
                    const textBefore = textarea.value.substring(0, cursorPos);
                    const textAfter = textarea.value.substring(cursorPos);
                    textarea.value = textBefore + productsHtml + textAfter;
                }
                
                this.closeModal();
            }
        }
    }
</script>
@endpush
