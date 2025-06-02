<x-app-layout>

    <div class="mb-6" x-data="{filterModal: false}">
        <!-- Botão para abrir o modal de filtros -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Todos os discos</h2>
            
            <button type="button" 
                @click="filterModal = true"
                class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex items-center">
                <i class="fas fa-filter mr-2"></i> Filtrar por propriedades
                <svg class="w-2.5 h-2.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
            </button>
        </div>
        
        <!-- Backdrop com opacidade -->
        <div x-show="filterModal" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-50"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-50"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black z-40">
        </div>
        
        <!-- Modal de filtros -->
        <div x-show="filterModal" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90"
            @click.away="filterModal = false"
            class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full justify-center items-center flex"
            tabindex="-1" aria-hidden="true">
            
            <!-- Modal de filtros - Conteúdo -->
            <div class="relative w-full max-w-2xl max-h-full">
                <!-- Modal de filtros - Conteúdo interno -->
                <div class="relative bg-white rounded-lg shadow">
                    <!-- Modal de filtros - Cabeçalho -->
                    <div class="flex items-center justify-between p-4 border-b rounded-t">
                        <h3 class="text-xl font-semibold text-gray-900">
                            Filtrar por propriedades
                        </h3>
                        <button type="button" @click="filterModal = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fechar modal</span>
                        </button>
                    </div>
                    
                    <!-- Modal de filtros - Corpo -->
                    <form action="{{ route('site.vinyls.index') }}" method="GET" class="p-6 space-y-6">
                        <div class="space-y-4">
                            <!-- Filtro 1: Categoria -->
                            <div class="grid grid-cols-2 gap-4 items-center border-b border-gray-200 pb-4">
                                <label class="block text-sm font-medium text-gray-900">Categoria</label>
                                <select name="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">Todas as categorias</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Filtro 2: Gravadora -->
                            <div class="grid grid-cols-2 gap-4 items-center border-b border-gray-200 pb-4">
                                <label class="block text-sm font-medium text-gray-900">Gravadora</label>
                                <select name="record_label" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">Todas as gravadoras</option>
                                    @foreach($recordLabels as $label)
                                        <option value="{{ $label->id }}" {{ request('record_label') == $label->id ? 'selected' : '' }}>{{ $label->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Filtro 3: Ano de Lançamento -->
                            <div class="grid grid-cols-2 gap-4 items-center border-b border-gray-200 pb-4">
                                <label class="block text-sm font-medium text-gray-900">Ano de Lançamento</label>
                                <select name="release_year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">Todos os anos</option>
                                    @foreach($releaseYears as $year)
                                        <option value="{{ $year }}" {{ request('release_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Filtro 4: Faixa de Preço -->
                            <div class="grid grid-cols-2 gap-4 items-center border-b border-gray-200 pb-4">
                                <label class="block text-sm font-medium text-gray-900">Faixa de Preço</label>
                                <div class="w-full">
                                    <div class="flex justify-between mb-2">
                                        <span id="min_price_display" class="text-sm text-gray-700">R$ {{ number_format(request('min_price', $priceRange->min_price), 2, ',', '.') }}</span>
                                        <span id="max_price_display" class="text-sm text-gray-700">R$ {{ number_format(request('max_price', $priceRange->max_price), 2, ',', '.') }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <input type="range" name="min_price" id="min_price" 
                                            min="{{ $priceRange->min_price }}" 
                                            max="{{ $priceRange->max_price }}" 
                                            step="1" 
                                            value="{{ request('min_price', $priceRange->min_price) }}" 
                                            class="w-full h-2 bg-blue-100 rounded-lg appearance-none cursor-pointer">
                                    </div>
                                    <div>
                                        <input type="range" name="max_price" id="max_price" 
                                            min="{{ $priceRange->min_price }}" 
                                            max="{{ $priceRange->max_price }}" 
                                            step="1" 
                                            value="{{ request('max_price', $priceRange->max_price) }}" 
                                            class="w-full h-2 bg-blue-100 rounded-lg appearance-none cursor-pointer">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ordenar por e Direção -->
                            <div class="grid grid-cols-2 gap-4 items-center pb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-2">Ordenar por</label>
                                    <select name="sort_by" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Data de adição</option>
                                        <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Preço</option>
                                        <option value="release_year" {{ request('sort_by') == 'release_year' ? 'selected' : '' }}>Ano de lançamento</option>
                                        <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Título</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 mb-2">Ordem</label>
                                    <select name="sort_order" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Crescente</option>
                                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Decrescente</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botões de ação -->
                        <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                            <button type="button" @click="filterModal = false" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">
                                Cancelar
                            </button>
                            <div class="flex space-x-2">
                                <a href="{{ route('site.vinyls.index') }}" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-gray-100">
                                    Limpar tudo
                                </a>
                                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    Aplicar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Indicadores de filtros ativos (tags) -->
    @if(request()->hasAny(['category', 'record_label', 'release_year', 'min_price', 'max_price']) && (request('min_price') != $priceRange->min_price || request('max_price') != $priceRange->max_price))
    <div class="flex flex-wrap gap-2 mb-4">
        <span class="text-sm text-gray-700">Filtros ativos:</span>
        
        @if(request('category'))
            @php $categoryName = $categories->where('id', request('category'))->first()->nome ?? ''; @endphp
            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1.5 rounded-full flex items-center hover:bg-blue-200">
                Categoria: {{ $categoryName }}
                <svg class="w-2 h-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </a>
        @endif
        
        @if(request('record_label'))
            @php $labelName = $recordLabels->where('id', request('record_label'))->first()->name ?? ''; @endphp
            <a href="{{ request()->fullUrlWithQuery(['record_label' => null]) }}" class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1.5 rounded-full flex items-center hover:bg-blue-200">
                Gravadora: {{ $labelName }}
                <svg class="w-2 h-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </a>
        @endif
        
        @if(request('release_year'))
            <a href="{{ request()->fullUrlWithQuery(['release_year' => null]) }}" class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1.5 rounded-full flex items-center hover:bg-blue-200">
                Ano: {{ request('release_year') }}
                <svg class="w-2 h-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </a>
        @endif
        
        @if(request('min_price') != $priceRange->min_price || request('max_price') != $priceRange->max_price)
            <a href="{{ request()->fullUrlWithQuery(['min_price' => $priceRange->min_price, 'max_price' => $priceRange->max_price]) }}" class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1.5 rounded-full flex items-center hover:bg-blue-200">
                Preço: R${{ number_format(request('min_price', $priceRange->min_price), 2, ',', '.') }} - R${{ number_format(request('max_price', $priceRange->max_price), 2, ',', '.') }}
                <svg class="w-2 h-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </a>
        @endif
        
        <a href="{{ route('site.vinyls.index') }}" class="bg-gray-100 text-gray-800 text-xs font-medium px-3 py-1.5 rounded-full flex items-center hover:bg-gray-200">
            Limpar todos
            <svg class="w-2 h-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </a>
    </div>
    @endif

    <div class="container mx-auto p-4">
        <h2 class="font-jersey text-xl sm:text-3xl  text-gray-800 mt-3  ">Todos os discos</h2>
        <div class="divider mb-3"></div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">

            @foreach($vinyls as $vinyl)
                <x-site.vinyl-card :vinyl="$vinyl" />
            @endforeach
            
            <!-- Paginação -->
            <div class="mt-8">
                {{ $vinyls->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const minPriceInput = document.getElementById('min_price');
            const maxPriceInput = document.getElementById('max_price');
            const minPriceDisplay = document.getElementById('min_price_display');
            const maxPriceDisplay = document.getElementById('max_price_display');

            // Função para atualizar o display de preço
            function updatePriceDisplay(input, display) {
                if (display) {
                    display.textContent = 'R$ ' + parseFloat(input.value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }

            // Garantir que o valor mínimo não ultrapasse o máximo
            function ensureValidRange() {
                const minValue = parseFloat(minPriceInput.value);
                const maxValue = parseFloat(maxPriceInput.value);
                
                if (minValue > maxValue) {
                    minPriceInput.value = maxValue;
                    updatePriceDisplay(minPriceInput, minPriceDisplay);
                }
            }

            // Adicionar eventos para atualizar os displays
            if (minPriceInput && minPriceDisplay) {
                minPriceInput.addEventListener('input', function() {
                    updatePriceDisplay(minPriceInput, minPriceDisplay);
                    ensureValidRange();
                });
            }

            if (maxPriceInput && maxPriceDisplay) {
                maxPriceInput.addEventListener('input', function() {
                    updatePriceDisplay(maxPriceInput, maxPriceDisplay);
                    ensureValidRange();
                });
            }

            // Inicializar os displays com os valores atuais
            if (minPriceInput && minPriceDisplay) {
                updatePriceDisplay(minPriceInput, minPriceDisplay);
            }
            if (maxPriceInput && maxPriceDisplay) {
                updatePriceDisplay(maxPriceInput, maxPriceDisplay);
            }
        });
    </script>

</x-app-layout>
