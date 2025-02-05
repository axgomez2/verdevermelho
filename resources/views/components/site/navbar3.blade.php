<!-- component -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Responsive Store Layout with Full-Screen Cart Drawer on Mobile</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

  <!-- Hidden Checkbox to Toggle Cart Drawer -->
  <input type="checkbox" id="cartToggle" class="hidden">

  <!-- Top Navbar -->
  <header class="w-full bg-white shadow-md p-4 fixed top-0 left-0 z-20">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <div class="hidden md:flex w-full justify-between items-center">
        <div class="text-lg font-bold">Logo</div>
        <nav class="flex space-x-4">
          <a href="#" class="text-gray-700 hover:text-gray-900">Home</a>
          <a href="#" class="text-gray-700 hover:text-gray-900">Shop</a>
          <a href="#" class="text-gray-700 hover:text-gray-900">About</a>
        </nav>
        <div class="flex space-x-4 items-center">
          <a href="#" class="text-gray-700 hover:text-gray-900">Account</a>
          <label for="cartToggle" class="cursor-pointer text-gray-700 hover:text-gray-900">
            <!-- Cart Icon to Open Drawer -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-2 8h14l-2-8m-5 0V5m0 10a1 1 0 101 1m-5-5a1 1 0 101 1" />
            </svg>
          </label>
        </div>
      </div>

      <!-- Mobile View: Menu Button and Logo -->
      <div class="md:hidden flex items-center justify-between w-full">
        <button class="text-gray-700 hover:text-gray-900">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
          </svg>
        </button>
        <div class="text-lg font-bold">Logo</div>
      </div>
    </div>
  </header>

  <!-- Main Content (for spacing) -->


  <!-- Bottom Mobile Navbar -->
  <footer class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t shadow-md">
    <div class="flex justify-around items-center p-2">
      <a href="#" class="text-gray-700 hover:text-gray-900 flex flex-col items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M3 6h18M3 18h18" />
        </svg>
        <span class="text-xs">Menu</span>
      </a>
      <a href="#" class="text-gray-700 hover:text-gray-900 flex flex-col items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5v14" />
        </svg>
        <span class="text-xs">Shop</span>
      </a>
      <a href="#" class="text-gray-700 hover:text-gray-900 flex flex-col items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span class="text-xs">Account</span>
      </a>
      <label for="cartToggle" class="cursor-pointer text-gray-700 hover:text-gray-900 flex flex-col items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18m-7 6h7" />
        </svg>
        <span class="text-xs">Basket</span>
      </label>
    </div>
  </footer>

  <!-- Cart Drawer and Overlay -->
  <div id="cartOverlay" class="fixed inset-0 z-30 bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out"></div>

  <div id="cartDrawer" class="fixed top-0 right-0 h-full w-80 md:w-96 bg-white shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-40 p-4 md:max-w-xs">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-bold">Your Cart</h2>
      <label for="cartToggle" class="cursor-pointer text-gray-500 hover:text-gray-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </label>
    </div>
    <p class="text-gray-700">Your cart is empty.</p>
  </div>

  <!-- Tailwind CSS-based conditional classes using peer selector -->
  <style>
    /* Show Cart Drawer and Overlay When Checkbox is Checked */
    #cartToggle:checked ~ #cartDrawer {
      transform: translateX(0);
    }
    #cartToggle:checked ~ #cartOverlay {
      opacity: 1;
      pointer-events: auto;
    }
    /* Make Drawer Fullscreen on Small Screens */
    @media (max-width: 768px) {
      #cartDrawer {
        width: 100vw;
      }
    }
  </style>

</body>
</html>
