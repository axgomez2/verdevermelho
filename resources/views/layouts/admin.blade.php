<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="corporate">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased">
    <div class="drawer lg:drawer-open" x-data="{ sidebarOpen: false }">
        <input id="my-drawer" type="checkbox" class="drawer-toggle" x-model="sidebarOpen" />
        <div class="drawer-content flex flex-col">
            <!-- Navbar -->
            <div class="navbar bg-base-100 shadow-md">
                <div class="flex-none lg:hidden">
                    <label for="my-drawer" class="btn btn-square btn-ghost">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </label>
                </div>
                <div class="flex-1">
                    <a class="btn btn-ghost normal-case text-xl">{{ config('app.name', 'Laravel') }}</a>
                </div>
                <div class="flex-none">
                    <div class="dropdown dropdown-end" x-data="{ open: false }">
                        <label tabindex="0" class="btn btn-ghost btn-circle avatar" @click="open = !open">
                            <div class="w-10 rounded-full">
                                <img src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" alt="{{ Auth::user()->name }}" />
                            </div>
                        </label>
                        <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52" x-show="open" @click.away="open = false">
                            <li><a href="{{ route('profile.edit') }}">Perfil</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left">Sair</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="container mx-auto px-4 py-8">
                @yield('breadcrumb')
                @yield('content')
            </main>

            <!-- Footer -->

        </div>

        <!-- Sidebar -->
        <div class="drawer-side">
            <label for="my-drawer" class="drawer-overlay"></label>
            <ul class="menu p-4 w-52 h-full bg-base-200 text-base-content">
                <li class="mb-4">
                    <a href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('assets/images/logo_embaixada.png') }}" alt="logo" class="h-12 w-auto">
                    </a>
                </li>
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Inicio</a></li>
                <li><a href="{{ route('admin.vinyls.index') }}" class="{{ request()->routeIs('admin.vinyls.*') ? 'active' : '' }}">Discos</a></li>
                <li><a href="#" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">Equipamentos</a></li>
                <li><a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">Configurações</a></li>
                <li><a href="#" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">Pedidos</a></li>
                <li><a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">Clientes</a></li>
                <li><a href="{{ route('admin.cat-style-shop.index') }}" class="{{ request()->routeIs('admin.cat-style-shop.*') ? 'active' : '' }}">Categorias Internas</a></li>
                <li><a href="{{ route('admin.djs.index') }}" class="{{ request()->routeIs('admin.djs.*') ? 'active' : '' }}">DJs e Recomendações</a></li>
            </ul>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

