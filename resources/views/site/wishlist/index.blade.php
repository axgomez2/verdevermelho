<x-app-layout>
    <x-breadcrumb :items="[
                ['label' => 'Meus Favoritos']
            ]" />
    <div class="py-6 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="wishlist-container" class="mt-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Meus Favoritos</h1>
                    
                    @if($wishlistItems->count() > 0)
                    <button id="add-all-to-cart" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Adicionar Todos ao Carrinho
                    </button>
                    @endif
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <p class="text-gray-600">Seus discos favoritos ficam salvos aqui para você encontrá-los facilmente mais tarde. 
                    Você pode adicionar ou remover itens da sua lista de favoritos a qualquer momento.</p>
                </div>

                @if($wishlistItems->count() > 0)
                <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
      <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-4xl">
        <div class="space-y-6">
                        @foreach($wishlistItems as $vinyl)
                            @include('components.site.wishlist-vinyl', ['vinyl' => $vinyl])
                        @endforeach
                    </div></div>
                </div>

                    <div class="mt-8">
                        {{ $wishlistItems->links() }}
                    </div>
                @else
                    <div class="bg-white p-8 rounded-lg shadow-sm text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-700 mb-2">Sua lista de favoritos está vazia</h2>
                        <p class="text-gray-500 mb-6">Adicione discos e equipamentos aos seus favoritos para encontrá-los facilmente depois.</p>
                        <div class="flex justify-center space-x-4">
                            <a href="{{ route('site.vinyls.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                                Explorar Discos
                            </a>
                            <a href="{{ route('site.equipments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                </svg>
                                Ver Equipamentos
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @push('scripts')
    @vite(['resources/js/wishlist-cart.js', 'resources/js/wishlist-item.js'])
    @endpush
</x-app-layout>
