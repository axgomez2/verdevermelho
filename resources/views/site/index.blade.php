<x-app-layout>

    <div class="hidden md:block">
        <div class="relative min-h-[75vh] flex items-center bg-gray-900">
            <div class="absolute inset-0">
                <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-900/90 to-transparent z-10"></div>
                <img src="https://therecordhub.com/cdn/shop/articles/realistic-scene-with-vinyl-records-neighborhood-yard-sale_optimized_100_3500x.jpg?v=1719231981"
                     class="w-full h-full object-cover opacity-60" alt="Background">
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-4 relative z-20">
                <div class="grid lg:grid-cols-12 gap-6"> <!-- Alterado para 12 colunas -->
                    <!-- Hero Content -->
                    <div class="lg:col-span-5">
                        <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                            Embaixada<br>
                            <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-blue-200">Dance Music</span>
                        </h1>
                        <p class="text-xl text-gray-300 mb-8 leading-relaxed max-w-xl">
                            Agora também online, o mais completo catálogo do Brasil
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="#destaques" class="group px-8 py-4 bg-blue-600 text-white font-medium rounded-full hover:bg-blue-700 transition-all flex items-center">
                                Ver discos
                            </a>
                            <a href="#playlists" class="group px-8 py-4 bg-white/10 text-white font-medium rounded-full hover:bg-white/20 transition-all flex items-center backdrop-blur-sm">
                                Equipamentos
                            </a>
                        </div>
                    </div>
                    <!-- Acompanhe também -->
                    <div class="lg:col-span-7"> <!-- Corrigido para 7 colunas -->
                        <div class="relative">
                            <div class="px-4">
                                <p class="text-xl text-gray-300 mb-8 leading-relaxed max-w-xl">
                                    Acompanhe também:
                                </p>
                                <div class="grid md:grid-cols-2 gap-8">
                                    <a href="https://www.instagram.com/marcosfreitasdj" target="_blank" rel="noopener noreferrer"
                                       class="group relative aspect-[21/15] rounded-2xl overflow-hidden">
                                        <img src="{{ asset('assets/images/marcosfreitas.jpg') }}"
                                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                             alt="DJ Marcos Freitas">
                                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent">
                                            <div class="absolute bottom-0 left-0 right-0 p-8">
                                                <div class="flex items-end justify-between">
                                                    <div>
                                                        <div class="flex items-center gap-2 text-blue-400 font-medium mb-3">
                                                            {{-- <i class="fas fa-calendar-alt"></i>
                                                            <span>15 de Março • 22h</span> --}}
                                                        </div>
                                                        <h3 class="text-2xl font-bold text-white mb-2">DJ Marcos Freitas</h3>
                                                        {{-- <p class="text-gray-300 flex items-center">
                                                            <i class="fas fa-map-marker-alt mr-2"></i>
                                                            House Music Night • São Paulo
                                                        </p> --}}
                                                    </div>
                                                    <div class="flex items-center gap-3 text-white">
                                                        <span class="text-sm font-medium opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0 transition-all duration-300">
                                                            Seguir
                                                        </span>
                                                        <div class="bg-blue-500/20 p-3 rounded-full backdrop-blur-sm group-hover:bg-blue-500/40 transition-colors">
                                                            <i class="fab fa-instagram text-xl"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>

                                    <a href="https://www.instagram.com/energyfestsp/" target="_blank" rel="noopener noreferrer"
                                       class="group relative aspect-[21/15] rounded-2xl overflow-hidden">
                                        <img src="{{ asset('assets/images/energyfest.jpg') }}"
                                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                             alt="Energy Fest">
                                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent">
                                            <div class="absolute bottom-0 left-0 right-0 p-8">
                                                <div class="flex items-end justify-between">
                                                    <div>
                                                        {{-- <div class="flex items-center gap-2 text-blue-400 font-medium mb-3">
                                                            <i class="fas fa-calendar-alt"></i>
                                                            <span>22 de Março • 20h</span>
                                                        </div> --}}
                                                        <h3 class="text-2xl font-bold text-white mb-2">Energy Fest</h3>
                                                        {{-- <p class="text-gray-300 flex items-center">
                                                            <i class="fas fa-map-marker-alt mr-2"></i>
                                                            Festival de Música Eletrônica • Rio de Janeiro
                                                        </p> --}}
                                                    </div>
                                                    <div class="flex items-center gap-3 text-white">
                                                        <span class="text-sm font-medium opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0 transition-all duration-300">
                                                            Seguir
                                                        </span>
                                                        <div class="bg-blue-500/20 p-3 rounded-full backdrop-blur-sm group-hover:bg-blue-500/40 transition-colors">
                                                            <i class="fab fa-instagram text-xl"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white">


        <div class="pt-8 pb-8">
            <div class=" max-w-7xl mx-auto ">
                <div class="">
                    <x-vinyl-carousel :vinyls="$slideVinyls" />
                </div>

            </div>
        </div>

    </div>
<hr class="border-t border-gray-200">

    <!-- Events & Featured Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">



            <!-- Latest Releases -->
            <div class="mb-20">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Últimos Discos Adicionados:</h2>
                        <p class="text-gray-500">As novidades mais recentes para sua coleção</p>
                    </div>
                    <a href="#" class="group inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                        Ver todos os discos
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($latestVinyls as $vinyl)
                        @include('components.site.vinyl-card', ['vinyl' => $vinyl])
                    @endforeach
                </div>
            </div>

            <!-- Featured Playlists -->
            <div id="playlists">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Dj Charts</h2>
                        <p class="text-gray-500">Recomendações dos especialistas:</p>
                    </div>
                    <a href="{{ route('site.playlists.index') }}"
                       class="group inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                        Ver todas
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    @forelse($featuredPlaylists as $playlist)
                        <div class="group bg-gray-50 rounded-2xl overflow-hidden hover:bg-gray-100 transition-all duration-300">
                            <a href="{{ route('site.playlists.show', $playlist->slug) }}">
                                <div class="relative aspect-video">
                                    @if($playlist->image)
                                        <img src="{{ asset('storage/' . $playlist->image) }}"
                                             alt="{{ $playlist->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                            <i class="fas fa-headphones text-4xl text-white"></i>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                        <div class="transform scale-75 group-hover:scale-100 transition-transform duration-300">
                                            <div class="bg-white/20 p-5 rounded-full backdrop-blur-sm">
                                                <i class="fas fa-play text-3xl text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-xl font-bold text-gray-900">{{ $playlist->name }}</h3>
                                    <span class="bg-blue-100 text-blue-600 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $playlist->tracks->count() }} faixas
                                    </span>
                                </div>
                                <p class="text-gray-600 mb-4 line-clamp-2">{{ $playlist->bio }}</p>
                                <a href="{{ route('site.playlists.show', $playlist->slug) }}"
                                   class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                                    Ouvir agora
                                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 py-12 text-center">
                            <p class="text-lg text-gray-500">Nenhuma playlist disponível no momento.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Discos Aleatórios por Categoria -->
            @foreach($categoriesWithRandomVinyls as $categoryData)
            <div class="{{ $loop->first ? 'mb-20 mt-20' : 'mb-20' }}">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $categoryData['category']->nome }}</h2>
                    </div>

                    <a href="{{ route('vinyls.byCategory', $categoryData['category']->slug) }}" 
                       class="group inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                        Ver mais discos
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($categoryData['vinyls'] as $vinyl)
                        @include('components.site.vinyl-card', ['vinyl' => $vinyl])
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

     <!-- Newsletter Banner -->
     <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('/assets/images/pattern.png')] opacity-10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 relative">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-white font-medium flex items-center">
                    <span class="bg-white/20 p-1.5 rounded-full mr-3">
                        <i class="fas fa-gift text-white"></i>
                    </span>
                    Receba ofertas exclusivas em primeira mão
                </p>
                <div class="relative flex-shrink-0">
                    <form id="newsletter-form" action="{{ route('site.newsletter.store') }}" method="POST" class="flex gap-2">
                        @csrf
                        <div class="relative">
                            <input type="email" name="email" id="newsletter-email"
                                class="w-64 pl-10 pr-4 py-1.5 rounded-full text-sm border-0 focus:ring-2 focus:ring-white/20"
                                placeholder="Seu e-mail" required>
                            <i class="fas fa-envelope absolute left-3.5 top-2 text-gray-400"></i>
                        </div>
                        <button type="submit" id="newsletter-submit"
                                class="px-6 py-1.5 bg-white text-blue-600 text-sm font-medium rounded-full hover:bg-blue-50 transition-colors">
                            cadastre-se
                        </button>
                    </form>
                    <div id="newsletter-success" class="hidden absolute mt-2 left-0 right-0 py-1.5 px-3 bg-green-600/90 text-white text-sm font-medium rounded-md">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Email cadastrado com sucesso!</span>
                        </div>
                    </div>
                    <div id="newsletter-error" class="hidden absolute mt-2 left-0 right-0 py-1.5 px-3 bg-red-600/90 text-white text-sm font-medium rounded-md">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span id="newsletter-error-message">Ocorreu um erro ao processar sua solicitação.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const newsletterForm = document.getElementById('newsletter-form');
        const successMessage = document.getElementById('newsletter-success');
        const errorMessage = document.getElementById('newsletter-error');
        const errorMessageText = document.getElementById('newsletter-error-message');
        
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const emailInput = document.getElementById('newsletter-email');
                const submitButton = document.getElementById('newsletter-submit');
                const formData = new FormData(newsletterForm);
                
                // Esconder mensagens de feedback antes de enviar
                successMessage.classList.add('hidden');
                errorMessage.classList.add('hidden');
                
                // Desativar o botão durante o envio
                submitButton.disabled = true;
                submitButton.innerHTML = 'Enviando...';
                
                fetch(newsletterForm.action, {
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
                        // Mensagem de sucesso
                        successMessage.classList.remove('hidden');
                        emailInput.value = ''; // Limpar o campo de email
                        
                        // Esconder a mensagem de sucesso após 5 segundos
                        setTimeout(() => {
                            successMessage.classList.add('hidden');
                        }, 5000);
                    } else {
                        // Mensagem de erro
                        errorMessageText.textContent = data.message;
                        errorMessage.classList.remove('hidden');
                        
                        // Esconder a mensagem de erro após 8 segundos
                        setTimeout(() => {
                            errorMessage.classList.add('hidden');
                        }, 8000);
                    }
                })
                .catch(error => {
                    // Erro no processamento
                    errorMessageText.textContent = 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente.';
                    errorMessage.classList.remove('hidden');
                })
                .finally(() => {
                    // Reativar o botão
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'cadastre-se';
                });
            });
        }
    });
    </script>

</x-app-layout>
