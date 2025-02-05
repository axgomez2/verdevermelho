


<nav x-data="{ open: false }" class= "text-white    ">
    <!-- Top navbar - Currency and Search -->
    <div class=" bg-gray-900 border-b border-gray-800">
        <div class="container mx-auto">
            <div class="flex items-center justify-between h-12 px-4">
                <!-- Search Bar -->


                <div class="flex-1 max-w-2xl mx-4">
                    <div class="relative">
                        <div>
                            <!-- Ícone de lupa -->
                            <button class="btn btn-ghost btn-circle text-white hover:text-gray-300"
                                onclick="search.showModal()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>

                        </div>
                    </div>
                </div>

                <!-- Right side icons -->
                <div class="flex items-center space-x-5">
                    <!-- dropdown -->
                    @auth
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                class="flex items-center space-x-1 text-white hover:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ Auth::user()->name }}</span>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                class="absolute left-0 mt-2 w-48 bg-gray-900 rounded-md shadow-lg z-50">
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-white hover:bg-gray-700">Editar Perfil</a>
                                <a href="{{ route('site.wishlist.index') }}"
                                    class="block px-4 py-2 text-sm text-white hover:bg-gray-700">Lista de Desejos</a>
                                    <a href="{{ route('site.wantlist.index') }}"
                                    class="block px-4 py-2 text-sm text-white hover:bg-gray-700">Wantlist</a>
                                <a href="#" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">Meus Pedidos</a>
                                <hr class="border-b-0 my-4" />
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700">
                                        sair da conta
                                    </button>
                                </form>
                            </div>
                        </div>

                        <a href="{{ route('site.wishlist.index') }}" class="text-white hover:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </a>
                        <a href="{{ route('site.cart.index') }}" class="text-white hover:text-gray-300 relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span
                                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">0</span>
                        </a>
                    @else
                        <div class="dropdown dropdown-end">
                            <label tabindex="0" class="btn btn-ghost rounded-btn">
                                <div class="flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="hidden md:inline">Área do Cliente</span>
                                </div>
                            </label>
                            <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-indigo-600 rounded-box w-52 mt-4">
                                <li>
                                    <a href="{{route('login')}}" class="hover:bg-indigo-950 transition-colors duration-200"
                                        >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                        Login
                                    </a>
                                </li>
                                <li>
                                    <a href="{{route('register')}}" class="hover:bg-base-200 transition-colors duration-200"
                                       >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                        Cadastre-se
                                    </a>
                                </li>
                            </ul>


                        </div>

                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Main navbar -->
    <div class="container mx-auto  bg-white shadow-md">
        <div class="flex items-center justify-between h-24 px-4">
            <!-- Mobile menu button -->
            <div x-data="{ open: false }" class="sm:hidden">
                <button @click="open = !open" class="text-slate-600 hover:text-slate-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Mobile menu -->
                <div x-show="open" @click.away="open = false"
                    class="absolute left-0 w-full bg-gray-600 shadow-lg z-50">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <a href="{{ route('site.home') }}"
                            class="block px-3 py-2 text-white hover:bg-gray-700 rounded-md">Inicio</a>
                        <a href="{{ route('site.vinyls.index') }}"
                            class="block px-3 py-2 text-white hover:bg-gray-700 rounded-md">Discos</a>
                        <a href="{{ route('site.equipments.index') }}"
                            class="block px-3 py-2 text-white hover:bg-gray-700 rounded-md">Equipamentos</a>
                        <a href="{{ route('site.about') }}"
                            class="block px-3 py-2 text-white hover:bg-gray-700 rounded-md">Sobre a loja</a>
                        <a href="{{ route('register') }}"
                            class="block px-3 py-2 text-white hover:bg-gray-700 rounded-md">Ofertas</a>
                    </div>
                </div>
            </div>
            <!-- Logo -->
            <div class="flex-1 flex justify-center">
                <a href="/" class="text-white text-2xl font-bold">
                    <img src="{{ asset('assets/images/logo_embaixada.png') }}" alt="logo"
                        class='h-14 sm:h-20 md:h-20 lg:h-16 mt-2 mb-2 ' /></a>
            </div>
        </div>
    </div>

    <!-- Bottom navbar - Desktop only -->
    <div class="hidden sm:block border-t bg-black border-gray-800">
        <div class="container mx-auto">
            <div class="flex items-center justify-center h-12 px-4">
                <div class="flex items-center space-x-8 overflow-x-auto">
                    <a href="{{ route('site.home') }}"
                        class="text-white hover:text-gray-300 text-sm whitespace-nowrap">Inicio</a>
                    <a href="{{ route('site.vinyls.index') }}"
                        class="text-white hover:text-gray-300 text-sm whitespace-nowrap">Discos</a>
                    <a href="{{ route('site.equipments.index') }}"
                        class="text-white hover:text-gray-300 text-sm whitespace-nowrap">Equipamentos</a>
                    <a href="{{ route('site.about') }}"
                        class="text-white hover:text-gray-300 text-sm whitespace-nowrap">Sobre a loja</a>

                    <a href="#" class="text-red-500 hover:text-red-400 text-sm whitespace-nowrap">Ofertas</a>
                </div>
            </div>
        </div>
    </div>
</nav>






{{-- modal search --}}
<dialog id="search" class="modal">
    <div
        class="fixed inset-0 p-4 flex flex-wrap justify-center items-center w-full h-full z-[1000] before:fixed before:inset-0 before:w-full before:h-full before:bg-[rgba(0,0,0,0.5)] overflow-auto font-[sans-serif]">
        <div class="modal-box">
            <div class="w-full max-w-lg bg-white shadow-lg rounded-md p-8 relative">

                <div class="my-8 text-center">
                    <h4 class="text-2xl text-gray-800 font-bold">pesquisa</h4>
                    <p class="text-sm text-gray-500 mt-2">Procure por artista ou titulo</p>
                    <form action="{{ route('search') }}" method="GET" class="flex">
                        <input type="text" name="q"
                            placeholder="digiteo que  busca e aperte o enter"
                            class="px-4 py-2.5 mt-6 bg-[#f0f1f2] text-gray-800 w-full text-sm focus:bg-transparent outline-blue-600 rounded-md" />
                    </form>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>voltar</button>
        </form>
</dialog>
