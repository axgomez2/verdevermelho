<x-app-layout>

    <div x-data="{ email: '' }" class="relative font-sans ">
        <div class="absolute inset-0 bg-black opacity-50 z-10"></div>
        <img src="https://therecordhub.com/cdn/shop/articles/realistic-scene-with-vinyl-records-neighborhood-yard-sale_optimized_100_3500x.jpg?v=1719231981" alt="Banner Image" class="absolute inset-0 w-full h-full object-cover object-bottom" />
        <div class="min-h-[400px] relative z-20 h-full max-w-4xl mx-auto flex flex-col justify-center items-center text-center px-6 py-12">
          <div class="max-w-3xl mx-auto text-center">
            <h3 class="text-white md:text-5xl text-4xl font-bold">A embaixada dance music</h3>
            <p class="text-gray-300 text-sm mt-6">Agora é digital, cadastre-se e receba nossas atualizações e ofertas</p>
            <form @submit.prevent="subscribeUser" class="form-control">
            <div class="max-w-lg mx-auto bg-gray-100 flex p-1 rounded-full text-left mt-12 border focus-within:border-gray-700">

              <input type='email' placeholder='Digite seu email' class="w-full outline-none bg-transparent text-sm text-gray-800 px-4 py-3" />
              <button type='button'
                class="bg-gray-800 hover:bg-gray-700 transition-all text-white tracking-wide text-sm rounded-full px-6 py-3">Enviar</button>

            </div>
            </form>
            <p class="mt-4 text-sm text-gray-300">
                Ao se cadastrar, você concorda com nossos <a href="#" class="underline hover:text-white">termos de serviço</a>.
            </p>
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



<div class="font-[sans-serif] p-4 mx-auto max-w-[1400px]">
    <h2 class="font-jersey text-xl sm:text-3xl text-gray-800 mt-3">Últimos discos adicionados</h2>
    <div class="divider mb-3"></div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
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
<div class="flex justify-center items-center min-h-screen">
    <div class="max-w-[720px] mx-auto">
        <div class="block mb-4 mx-auto border-b border-slate-300 pb-2 max-w-[360px]">
            <a
                target="_blank"
                href="https://www.material-tailwind.com/docs/html/card"
                class="block w-full px-4 py-2 text-center text-slate-700 transition-all"
            >
                More components on <b>Material Tailwind</b>.
            </a>
        </div>

        <!-- Centering wrapper -->
        <div class="relative flex w-full max-w-[26rem] flex-col rounded-xl bg-white bg-clip-border text-gray-700 shadow-lg">
            <div
                class="relative mx-4 mt-4 overflow-hidden text-white shadow-lg rounded-xl bg-blue-gray-500 bg-clip-border shadow-blue-gray-500/40">
                <img
                    src="https://images.unsplash.com/photo-1499696010180-025ef6e1a8f9?ixlib=rb-4.0.3&amp;ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&amp;auto=format&amp;fit=crop&amp;w=1470&amp;q=80"
                    alt="ui/ux review check" />
                <div
                    class="absolute inset-0 w-full h-full to-bg-black-10 bg-gradient-to-tr from-transparent via-transparent to-black/60">
                </div>
                <button
                    class="!absolute top-4 right-4 h-8 max-h-[32px] w-8 max-w-[32px] select-none rounded-full text-center align-middle font-sans text-xs font-medium uppercase text-red-500 transition-all hover:bg-red-500/10 active:bg-red-500/30 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                    type="button">
                    <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-6 h-6">
                            <path
                                d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z">
                            </path>
                        </svg>
                    </span>
                </button>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                    <h5 class="block font-sans text-xl antialiased font-medium leading-snug tracking-normal text-blue-gray-900">
                        Wooden House, Florida
                    </h5>
                    <p
                        class="flex items-center gap-1.5 font-sans text-base font-normal leading-relaxed text-blue-gray-900 antialiased">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="-mt-0.5 h-5 w-5 text-yellow-700">
                            <path fill-rule="evenodd"
                                d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                clip-rule="evenodd"></path>
                        </svg>
                        5.0
                    </p>
                </div>
                <p class="block font-sans text-base antialiased font-light leading-relaxed text-gray-700">
                    Enter a freshly updated and thoughtfully furnished peaceful home surrounded by ancient trees, stone
                    walls, and open meadows.
                </p>
                <div class="inline-flex flex-wrap items-center gap-3 mt-8 group">
                    <span
                        class="cursor-pointer rounded-full border border-gray-900/5 bg-gray-900/5 p-3 text-gray-900 transition-colors hover:border-gray-900/10 hover:bg-gray-900/10 hover:!opacity-100 group-hover:opacity-70">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-5 h-5">
                            <path d="M12 7.5a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"></path>
                            <path fill-rule="evenodd"
                                d="M1.5 4.875C1.5 3.839 2.34 3 3.375 3h17.25c1.035 0 1.875.84 1.875 1.875v9.75c0 1.036-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 011.5 14.625v-9.75zM8.25 9.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM18.75 9a.75.75 0 00-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 00.75-.75V9.75a.75.75 0 00-.75-.75h-.008zM4.5 9.75A.75.75 0 015.25 9h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75V9.75z"
                                clip-rule="evenodd"></path>
                            <path
                                d="M2.25 18a.75.75 0 000 1.5c5.4 0 10.63.722 15.6 2.075 1.19.324 2.4-.558 2.4-1.82V18.75a.75.75 0 00-.75-.75H2.25z">
                            </path>
                        </svg>
                    </span>
                    <span
                        class="cursor-pointer rounded-full border border-gray-900/5 bg-gray-900/5 p-3 text-gray-900 transition-colors hover:border-gray-900/10 hover:bg-gray-900/10 hover:!opacity-100 group-hover:opacity-70">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-5 h-5">
                            <path fill-rule="evenodd"
                                d="M1.371 8.143c5.858-5.857 15.356-5.857 21.213 0a.75.75 0 010 1.061l-.53.53a.75.75 0 01-1.06 0c-4.98-4.979-13.053-4.979-18.032 0a.75.75 0 01-1.06 0l-.53-.53a.75.75 0 010-1.06zm3.182 3.182c4.1-4.1 10.749-4.1 14.85 0a.75.75 0 010 1.061l-.53.53a.75.75 0 01-1.062 0 8.25 8.25 0 00-11.667 0 .75.75 0 01-1.06 0l-.53-.53a.75.75 0 010-1.06zm3.204 3.182a6 6 0 018.486 0 .75.75 0 010 1.061l-.53.53a.75.75 0 01-1.061 0 3.75 3.75 0 00-5.304 0 .75.75 0 01-1.06 0l-.53-.53a.75.75 0 010-1.06zm3.182 3.182a1.5 1.5 0 012.122 0 .75.75 0 010 1.061l-.53.53a.75.75 0 01-1.061 0l-.53-.53a.75.75 0 010-1.06z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </span>
                    <span
                        class="cursor-pointer rounded-full border border-gray-900/5 bg-gray-900/5 p-3 text-gray-900 transition-colors hover:border-gray-900/10 hover:bg-gray-900/10 hover:!opacity-100 group-hover:opacity-70">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-5 h-5">
                            <path
                                d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z">
                            </path>
                            <path
                                d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z">
                            </path>
                        </svg>
                    </span>
                    <span
                        class="cursor-pointer rounded-full border border-gray-900/5 bg-gray-900/5 p-3 text-gray-900 transition-colors hover:border-gray-900/10 hover:bg-gray-900/10 hover:!opacity-100 group-hover:opacity-70">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-5 h-5">
                            <path d="M19.5 6h-15v9h15V6z"></path>
                            <path fill-rule="evenodd"
                                d="M3.375 3C2.339 3 1.5 3.84 1.5 4.875v11.25C1.5 17.16 2.34 18 3.375 18H9.75v1.5H6A.75.75 0 006 21h12a.75.75 0 000-1.5h-3.75V18h6.375c1.035 0 1.875-.84 1.875-1.875V4.875C22.5 3.839 21.66 3 20.625 3H3.375zm0 13.5h17.25a.375.375 0 00.375-.375V4.875a.375.375 0 00-.375-.375H3.375A.375.375 0 003 4.875v11.25c0 .207.168.375.375.375z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </span>
                    <span
                        class="cursor-pointer rounded-full border border-gray-900/5 bg-gray-900/5 p-3 text-gray-900 transition-colors hover:border-gray-900/10 hover:bg-gray-900/10 hover:!opacity-100 group-hover:opacity-70">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-5 h-5">
                            <path fill-rule="evenodd"
                                d="M12.963 2.286a.75.75 0 00-1.071-.136 9.742 9.742 0 00-3.539 6.177A7.547 7.547 0 016.648 6.61a.75.75 0 00-1.152-.082A9 9 0 1015.68 4.534a7.46 7.46 0 01-2.717-2.248zM15.75 14.25a3.75 3.75 0 11-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 011.925-3.545 3.75 3.75 0 013.255 3.717z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </span>
                    <span
                        class="cursor-pointer rounded-full border border-gray-900/5 bg-gray-900/5 p-3 text-gray-900 transition-colors hover:border-gray-900/10 hover:bg-gray-900/10 hover:!opacity-100 group-hover:opacity-70">
                        +20
                    </span>
                </div>
            </div>
            <div class="p-6 pt-3">
                <button
                    class="block w-full select-none rounded-lg bg-gray-900 py-3.5 px-7 text-center align-middle font-sans text-sm font-bold uppercase text-white shadow-md shadow-gray-900/10 transition-all hover:shadow-lg hover:shadow-gray-900/20 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                    type="button">
                    Reserve
                </button>
            </div>
        </div>
    </div>
</div>

</x-app-layout>
