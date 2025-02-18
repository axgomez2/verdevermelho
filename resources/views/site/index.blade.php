<x-app-layout>

    <div class="container mx-auto p-4  ">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 ">
            <!-- Newsletter Section -->
            <div class="relative h-[500px] rounded-xl overflow-hidden row-span-3">
                <div class="absolute inset-0 bg-cover bg-center"
                     style="background-image: url('https://therecordhub.com/cdn/shop/articles/realistic-scene-with-vinyl-records-neighborhood-yard-sale_optimized_100_3500x.jpg?v=1719231981')">
                    <div class="absolute inset-0 bg-black/50"></div>
                </div>
                <div class="relative h-full flex flex-col justify-center items-center p-8 text-white">
                    <h2 class="text-4xl font-bold mb-4 text-center">A embaixada dance music</h2>
                    <p class="text-lg mb-8 text-center max-w-md">
                        Fique por dentro das últimas novidades, promoções e lançamentos exclusivos da nossa loja.
                    </p>
                    <form action="#" method="POST" class="w-full max-w-md">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-4">
                            <input
                                type="email"
                                name="email"
                                placeholder="Seu melhor e-mail"
                                class="input input-bordered w-full text-black"
                                required
                            />
                            <button type="submit" class="btn btn-primary whitespace-nowrap">
                                Inscrever-se
                            </button>
                        </div>
                    </form>

                </div>

            </div>

            <!-- Right Side Content -->
            <div class="flex flex-col gap-6">
                <!-- Vinyl Carousel Component -->
                <x-vinyl-carousel :vinyls="$latestVinyls" />

                <!-- Location Images -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 h-[174px]">
                    <!-- Location 1 -->
                    <div class="relative rounded-xl overflow-hidden">
                        <img
                            src="/placeholder.svg?height=250&width=400"
                            alt="Location 1"
                            class="w-full h-full object-cover"
                        />
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                            <h3 class="text-2xl font-bold text-white">Localização 1</h3>
                        </div>
                    </div>
                    <!-- Location 2 -->
                    <div class="relative rounded-xl overflow-hidden">
                        <img
                            src="/placeholder.svg?height=250&width=400"
                            alt="Location 2"
                            class="w-full h-full object-cover"
                        />
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                            <h3 class="text-2xl font-bold text-white">Localização 2</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <script>
    function subscribeUser() {
        if (this.email) {
            // Here you would typically send the email to your server
            alert('Inscrito com o email: ' + this.email);
            this.email = ''; // Clear the input after submission
        } else {
            alert('Por favor, insira um email válido.');
        }
    }
    </script>



<div class="font-[sans-serif] p-4 mx-8 max-w-[1400px]">
    <h2 class="font-jersey text-xl sm:text-3xl text-gray-800 mt-4">Últimos discos adicionados</h2>
    <div class="divider mb-3"></div>
    <div class="container mb-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
        @foreach($latestVinyls as $vinyl)
            @include('components.site.vinyl-card', ['vinyl' => $vinyl])
        @endforeach
    </div>
</div>



<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <h2 class="text-2xl font-bold p-4 bg-blue-500 text-white">DJ Charts</h2>
    <div class="p-4">
        <p class="mb-4">Confira as recomendações dos nossos DJs em destaque!</p>
        @foreach($featuredDjs as $dj)
            <div class="mb-2">
                <span class="font-semibold">{{ $dj->name }}</span>: {{ $dj->recommendations->count() }} recomendações
            </div>
        @endforeach
        <a href="{{ route('site.djcharts.index') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Ver todos os DJ Charts</a>
    </div>
</div>


<!-- component -->
<!-- component -->


        <!-- Centering wrapper -->

    </div>
</div>

</x-app-layout>
