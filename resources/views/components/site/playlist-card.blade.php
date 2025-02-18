@props(['playlist'])

<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
    <div class="relative">
        <!-- Imagem com overlay gradiente -->
        <img src="{{ $playlist->image_url }}" alt="{{ $playlist->name }}" class="w-full h-48 object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

        <!-- Status badge -->
        @if($playlist->is_active)
            <span class="absolute top-2 right-2 px-2 py-1 text-xs font-semibold bg-green-500 text-white rounded-full">
                {{ __('Active') }}
            </span>
        @endif
    </div>

    <div class="p-4">
        <!-- Nome e Bio -->
        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $playlist->name }}</h3>
        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $playlist->bio }}</p>

        <!-- Social Links -->
        <div class="flex space-x-3 mb-4">
            @if($playlist->instagram_url)
                <a href="{{ $playlist->instagram_url }}" target="_blank" class="text-pink-600 hover:text-pink-700">
                    <i class="fab fa-instagram text-xl"></i>
                </a>
            @endif
            @if($playlist->youtube_url)
                <a href="{{ $playlist->youtube_url }}" target="_blank" class="text-red-600 hover:text-red-700">
                    <i class="fab fa-youtube text-xl"></i>
                </a>
            @endif
            @if($playlist->facebook_url)
                <a href="{{ $playlist->facebook_url }}" target="_blank" class="text-blue-600 hover:text-blue-700">
                    <i class="fab fa-facebook text-xl"></i>
                </a>
            @endif
            @if($playlist->soundcloud_url)
                <a href="{{ $playlist->soundcloud_url }}" target="_blank" class="text-orange-600 hover:text-orange-700">
                    <i class="fab fa-soundcloud text-xl"></i>
                </a>
            @endif
        </div>

        <!-- Tracks Count -->
        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
            <span>
                <i class="fas fa-music mr-1"></i>
                {{ $playlist->tracks->count() }} {{ __('tracks') }}
            </span>
        </div>

        <!-- View Details Button -->
        <a href="{{ route('site.playlists.show', $playlist->slug) }}"
           class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-300">
            {{ __('View Details') }}
        </a>
    </div>
</div>
