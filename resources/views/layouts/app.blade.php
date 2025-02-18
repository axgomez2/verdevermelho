<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="@yield('meta_description', 'A LOJA REFERENCIA EM DISCOS DE DE DANCE MUSIC.')">
    <meta name="keywords" content="@yield('meta_keywords', 'vinyl, records, music, albums, turntable, audiophile')">
    <meta name="author" content="Your Company Name">
    <meta name="robots" content="index, follow">

    <title>@yield('title', 'EMBAIXADA DANCE MUSIC - 30 ANOS DE MERCADO')</title>

    <!-- Fonts -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Facebook Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'A LOJA REFERENCIA EM DISCOS DE DE DANCE MUSIC.')">
    <meta property="og:description" content="@yield('og_description', 'Discover and purchase high-quality vinyl records from our extensive collection.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@yourtwitterhandle">
    <meta name="twitter:title" content="@yield('twitter_title', 'A LOJA REFERENCIA EM DISCOS DE DE DANCE MUSIC.')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Discover and purchase high-quality vinyl records from our extensive collection.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/twitter-image.jpg'))">



    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">


    {{-- font awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom CSS -->
    @stack('styles')

    <!-- Google Analytics -->
    <!-- Replace UA-XXXXX-Y with your actual Google Analytics tracking code -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-XXXXX-Y"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-XXXXX-Y');
    </script>

</head>


<body>
    <div class="bg-slate-200">
        @include('components.site.nav4')
    </div>
    <!-- Page Content -->
    <main class=" p-4 bg-gray-200">
        <div >
            {{ $slot }}
        </div>
      </main>

    </div>
    @include('components.site.audio-player')

    @include('site.footer')



    <script src="https://www.youtube.com/iframe_api"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="{{ asset('assets/js/audio-player.js') }}" defer></script>
    {{-- <script src="{{ asset('assets/js/vinyl-card.js') }}" defer></script>
    <script src="{{ asset('assets/js/track-list.js') }}" defer></script>
    <script src="{{ asset('assets/js/vinyl-player.js') }}" defer></script> --}}
    <script src="{{ asset('assets/js/favorites.js') }}"></script>
    <script src="{{ asset('assets/js/cart.js') }}"></script>
    <script src="{{ asset('assets/js/wantlist.js') }}"></script>
    <script src="{{ asset('assets/js/toast.js') }}" type="module"></script>

    <script src="https://unpkg.com/flowbite@1.6.6/dist/flowbite.js"></script>



    @stack('scripts')



</body>

</html>
