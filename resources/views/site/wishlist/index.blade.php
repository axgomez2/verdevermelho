<x-app-layout>





    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold mb-6">Minha Lista de Desejos</h1>
            @if($wishlistItems->count() > 0)
                <div class="font-[sans-serif] p-4 mx-auto max-w-[1400px]">
                    <h2 class="font-jersey text-xl sm:text-3xl text-gray-800 mt-3 mb-6">Meus discos em wishlist:</h2>
                    <ul class="space-y-6">
                        @foreach($wishlistItems as $item)
                            <li>
                                @if($item instanceof App\Models\VinylMaster)
                                    @include('components.site.wishlist-vinyl', ['vinyl' => $item])
                                @elseif($item instanceof App\Models\Equipment)
                                    @include('components.site.wishlist-equipment', ['equipment' => $item])
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-xl text-gray-600">Sua lista de desejos está vazia.</p>
            @endif
        </div>
    </div>
</div>



</x-app-layout>
