

  <!-- Drawer for Mobile Menu -->
  <div class="drawer ">
    <input id="my-drawer" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
      <!-- Top Navbar -->
      <header class="navbar bg-slate-100 shadow-md fixed top-0 left-0 z-50">
        <div class="navbar-start">
          <div class="lg:hidden">
            <label for="my-drawer" class="btn btn-square btn-ghost">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </label>
          </div>
          <a href="/" class=' '>
            <img src="{{ asset('assets/images/logo_embaixada.png') }}"
                        alt="Logo" class="h-14 sm:h-20 md:h-20 lg:h-16 mt-2 mb-2">
          </a>
        </div>

        <div class="navbar-center hidden lg:flex ">
            <ul class="menu menu-horizontal px-1">
              <li>
                <a href="{{ route('site.home') }}" class="text-slate-800 hover:bg-slate-800 hover:text-yellow-400 rounded-md text-MD font-semibold py-3 px-4">INICIO</a>
            </li>
              <li>
                <details>
                  <summary   class="text-slate-800 hover:bg-slate-800 hover:text-yellow-400 rounded-md text-MD font-semibold py-3 px-4">DISCOS</summary>
                  <ul class="p-2 shadow-xl bg-slate-200 rounded-lg w-[600px] z-100">
                    <div  class="dropdown-content menu p-4  ">
                        <div class="grid grid-cols-3 gap-6">
                          <div>
                            <h3 class="font-bold text-lg mb-3 text-slate-800">Gêneros</h3>
                            <ul class="space-y-2">
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['category' => 'rock']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Rock</a>
                              </li>
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['category' => 'jazz']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Jazz</a>
                              </li>
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['category' => 'classical']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Clássica</a>
                              </li>
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['category' => 'electronic']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Eletrônica</a>
                              </li>
                            </ul>
                          </div>
                          <div>
                            <h3 class="font-bold text-lg mb-3 text-slate-800">Destaques</h3>
                            <ul class="space-y-2">
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['featured' => 'new']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Lançamentos</a>
                              </li>
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['featured' => 'bestsellers']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Mais Vendidos</a>
                              </li>
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['featured' => 'rare']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Raridades</a>
                              </li>
                            </ul>
                          </div>
                        </div>
                        <div class="mt-6 pt-4 border-t border-gray-100">
                          <a href="{{ route('site.vinyls.index') }}" class="inline-block bg-slate-800 text-yellow-400 py-2 px-4 rounded hover:bg-yellow-400 hover:text-slate-800 transition duration-300">Ver todos os discos</a>
                        </div>
                      </div>
                  </ul>
                </details>
              </li>

              <li>
                <details>
                  <summary   class="text-slate-800 hover:bg-slate-800 hover:text-yellow-400 rounded-md text-md font-semibold py-3 px-4">EQUIPAMENTOS</summary>
                  <ul class="p-2 shadow-xl bg-slate-200 rounded-lg w-[600px] z-100">
                    <div  class="dropdown-content menu p-4  ">
                        <div class="grid grid-cols-3 gap-6">
                          <div>
                            <h3 class="font-bold text-lg mb-3 text-slate-800">Gêneros</h3>
                            <ul class="space-y-2">
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['category' => 'rock']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Rock</a>
                              </li>
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['category' => 'jazz']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Jazz</a>
                              </li>
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['category' => 'classical']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Clássica</a>
                              </li>
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['category' => 'electronic']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Eletrônica</a>
                              </li>
                            </ul>
                          </div>
                          <div>
                            <h3 class="font-bold text-lg mb-3 text-slate-800">Destaques</h3>
                            <ul class="space-y-2">
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['featured' => 'new']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Lançamentos</a>
                              </li>
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['featured' => 'bestsellers']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Mais Vendidos</a>
                              </li>
                              <li class="hover:bg-transparent">
                                <a href="{{ route('site.vinyls.index', ['featured' => 'rare']) }}" class="block text-slate-700 hover:text-yellow-600 p-0">Raridades</a>
                              </li>
                            </ul>
                          </div>
                        </div>
                        <div class="mt-6 pt-4 border-t border-gray-100">
                          <a href="{{ route('site.vinyls.index') }}" class="inline-block bg-slate-800 text-yellow-400 py-2 px-4 rounded hover:bg-yellow-400 hover:text-slate-800 transition duration-300">Ver todos os discos</a>
                        </div>
                      </div>
                  </ul>
                </details>
              </li>
              <li>
                <a href="{{ route('site.home') }}" class="text-slate-800 hover:bg-slate-800 hover:text-yellow-400 rounded-md text-MD font-semibold py-3 px-4">OFERTAS</a>
            </li>
            <li>
                <a href="{{ route('site.home') }}" class="text-slate-800 hover:bg-slate-800 hover:text-yellow-400 rounded-md text-MD font-semibold py-3 px-4">CONTATO</a>
            </li>
            </ul>
          </div>

        <div class="navbar-end">
          <div class="hidden lg:block">
            <div class="dropdown dropdown-end">
              <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                <div class="w-10 rounded-full">
                  <img src="/path/to/user/avatar.jpg" alt="User Avatar" />
                </div>
              </label>
              <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                <li><a>Profile</a></li>
                <li><a>Settings</a></li>
                <li><a>Logout</a></li>
              </ul>
            </div>
          </div>
          <label for="cart-drawer" class="btn btn-ghost btn-circle">
            <div class="indicator">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
              <span class="badge badge-sm indicator-item">0</span>
            </div>
          </label>
        </div>
      </header>



      <!-- Bottom Mobile Navbar -->
<footer class="btm-nav bg-slate-800 sm:hidden fixed bottom-0 left-0 right-0 z-50">
    <a href="#" class="text-primary">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
      <span class="btm-nav-label">Home</span>
    </a>
    <a href="#" class="text-primary">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
      <span class="btm-nav-label">Shop</span>
    </a>
    <a href="#" class="text-primary">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
      <span class="btm-nav-label">Account</span>
    </a>
    <label for="cart-drawer" class="text-primary">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
      <span class="btm-nav-label">Basket</span>
    </label>
  </footer>
    </div>
    <div class="drawer-side z-50">
      <label for="my-drawer" class="drawer-overlay"></label>
      <ul class="menu p-4 w-80 min-h-full bg-base-200 text-base-content">
        <!-- Sidebar content here -->
        <li><a href="#">Home</a></li>
        <li><a href="#">Shop</a></li>
        <li><a href="#">About</a></li>
        <li><a href="#">Account</a></li>
      </ul>
    </div>
  </div>

  <!-- Cart Drawer -->
  <div class="drawer drawer-end z-50">
    <input id="cart-drawer" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
      <!-- Page content here -->
    </div>
    <div class="drawer-side">
      <label for="cart-drawer" class="drawer-overlay"></label>
      <div class="menu p-4 w-80 min-h-full bg-base-200 text-base-content">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold">Your Cart</h2>
          <label for="cart-drawer" class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </label>
        </div>
        <p class="text-base-content">Your cart is empty.</p>
      </div>
    </div>
  </div>
