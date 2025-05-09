<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Verde & Vermelho') }} - Painel Administrativo</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Admin Sidebar -->
        <aside id="logo-sidebar" class="fixed left-0 top-0 z-40 h-screen w-64 -translate-x-full border-r border-gray-200 bg-white pt-16 transition-transform lg:translate-x-0" aria-label="Sidebar">
            <div class="h-full overflow-y-auto bg-white px-3 pb-4">
                <div class="mb-4">
                    <a href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('assets/images/logo_embaixada.png') }}" alt="Logo" class="h-12 w-auto">
                    </a>
                </div>
                <ul class="space-y-2 font-medium">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100' : '' }}">
                            <svg class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                                <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                                <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                            </svg>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.vinyls.index') }}" class="flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.vinyls.*') ? 'bg-gray-100' : '' }}">
                            <svg class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <span class="ml-3">Vinyls</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.playlists.index') }}" class="flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.playlists.*') ? 'bg-gray-100' : '' }}">
                            <svg class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                            <span class="ml-3">Playlists</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-100' : '' }}">
                            <svg class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12zM6 10h2v2H6v-2zm0 4h8v2h-8v-2zm10 0h2v2h-2v-2zm-6-4h8v2h-8v-2z"/>
                            </svg>
                            <span class="ml-3">Pedidos</span>
                            <span class="inline-flex items-center justify-center w-3 h-3 p-3 ml-3 text-sm font-medium text-blue-800 bg-blue-100 rounded-full">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.742 11.725 1.485 1.571 5.606-5.858m-10.827.45 3.939 4.019 1.578-1.665" />
                                </svg>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.customers.index') }}" class="flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.customers.*') ? 'bg-gray-100' : '' }}">
                            <svg class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                            </svg>
                            <span class="ml-3">Clientes</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-100' : '' }}">
                            <svg class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 2a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-15a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0V3a1 1 0 0 1 1-1zm0 16a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0v-2a1 1 0 0 1 1-1zm7-7a1 1 0 0 1-1 1h-2a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1zM3 12a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2H4a1 1 0 0 1-1-1z"/>
                            </svg>
                            <span class="ml-3">Configurações</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Admin Navbar -->
        <nav class="fixed top-0 z-50 w-full border-b border-gray-200 bg-white">
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-start">
                        <button type="button" x-data @click="$dispatch('toggle-sidebar')" class="inline-flex items-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 lg:hidden">
                            <span class="sr-only">Abrir menu</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path></svg>
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="ml-2 flex md:mr-24">
                            <span class="self-center whitespace-nowrap text-xl font-semibold sm:text-2xl">{{ config('app.name') }}</span>
                        </a>
                    </div>
                    <div class="flex items-center">
                        <div class="relative ml-3" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="flex rounded-full bg-gray-800 text-sm focus:ring-4 focus:ring-gray-300">
                                <span class="sr-only">Abrir menu do usuário</span>
                                <img class="h-8 w-8 rounded-full" src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" alt="{{ Auth::user()->name }}">
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 z-50 mt-2 w-48 divide-y divide-gray-100 rounded bg-white text-base shadow">
                                <div class="px-4 py-3">
                                    <span class="block text-sm text-gray-900">{{ Auth::user()->name }}</span>
                                    <span class="block truncate text-sm font-medium text-gray-500">{{ Auth::user()->email }}</span>
                                </div>
                                <ul class="py-1">
                                    <li>
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Perfil</a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">Sair</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="lg:ml-64">
            <div class="mt-14 p-4">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800" role="alert">
                        <span class="font-medium">Sucesso!</span> {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800" role="alert">
                        <span class="font-medium">Erro!</span> {{ session('error') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-4 rounded-lg bg-yellow-50 p-4 text-sm text-yellow-800" role="alert">
                        <span class="font-medium">Atenção!</span> {{ session('warning') }}
                    </div>
                @endif

                <!-- Main Content -->
                {{ $slot }}
            </div>
        </div>
    </div>

    <!-- Alpine.js Scripts -->
    <script>
        // Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('toggle-sidebar', function() {
                const sidebar = document.getElementById('logo-sidebar');
                sidebar.classList.toggle('-translate-x-full');
            });
        });
    </script>
</body>
</html>
