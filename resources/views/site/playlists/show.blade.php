<x-app-layout>
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section with Cover Image -->
            <div class="relative rounded-lg overflow-hidden mb-8 h-64 md:h-96">
                <img src="{{ $playlist->image_url }}" alt="{{ $playlist->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $playlist->name }}</h1>
                    <p class="text-lg text-gray-200 line-clamp-2">{{ $playlist->bio }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Social Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4">{{ __('Connect') }}</h2>

                        <!-- Social Links -->
                        <div class="space-y-4">
                            @if($playlist->instagram_url)
                            <a href="{{ $playlist->instagram_url }}" target="_blank"
                               class="flex items-center space-x-3 text-gray-700 hover:text-pink-600 transition-colors">
                                <i class="fab fa-instagram text-2xl"></i>
                                <span>Instagram</span>
                            </a>
                            @endif

                            @if($playlist->youtube_url)
                            <a href="{{ $playlist->youtube_url }}" target="_blank"
                               class="flex items-center space-x-3 text-gray-700 hover:text-red-600 transition-colors">
                                <i class="fab fa-youtube text-2xl"></i>
                                <span>YouTube</span>
                            </a>
                            @endif

                            @if($playlist->facebook_url)
                            <a href="{{ $playlist->facebook_url }}" target="_blank"
                               class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition-colors">
                                <i class="fab fa-facebook text-2xl"></i>
                                <span>Facebook</span>
                            </a>
                            @endif

                            @if($playlist->soundcloud_url)
                            <a href="{{ $playlist->soundcloud_url }}" target="_blank"
                               class="flex items-center space-x-3 text-gray-700 hover:text-orange-600 transition-colors">
                                <i class="fab fa-soundcloud text-2xl"></i>
                                <span>SoundCloud</span>
                            </a>
                            @endif
                        </div>

                        <!-- Bio Section -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('About') }}</h3>
                            <p class="text-gray-600">{{ $playlist->bio }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Tracks List -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-6">{{ __('Tracks') }}</h2>

                        <div class="space-y-4" x-data="playlistPlayer()">
                            @foreach($playlist->tracks as $track)
                            <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors"
                                 :class="{'bg-gray-50': currentTrack && currentTrack.id === {{ $track->id }}}">
                                <!-- Play Button -->
                                <button @click="togglePlay({{ json_encode([
                                    'id' => $track->id,
                                    'title' => $track->vinylMaster->title,
                                    'artist' => $track->vinylMaster->artists->pluck('name')->implode(', '),
                                    'preview_url' => $track->trackable->preview_url ?? null
                                ]) }})"
                                        class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                                    <i class="fas" :class="currentTrack && currentTrack.id === {{ $track->id }} && isPlaying ? 'fa-pause' : 'fa-play'"></i>
                                </button>

                                <!-- Track Info -->
                                <div class="flex-grow">
                                    <h3 class="font-medium text-gray-900">{{ $track->vinylMaster->title }}</h3>
                                    <p class="text-sm text-gray-500">{{ $track->vinylMaster->artists->pluck('name')->implode(', ') }}</p>
                                </div>

                                <!-- Duration -->
                                @if($track->trackable->duration)
                                <span class="text-sm text-gray-500">
                                    {{ gmdate("i:s", $track->trackable->duration) }}
                                </span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function playlistPlayer() {
            return {
                audio: new Audio(),
                currentTrack: null,
                isPlaying: false,

                togglePlay(track) {
                    if (!track.preview_url) {
                        alert('{{ __("No preview available for this track") }}');
                        return;
                    }

                    if (this.currentTrack && this.currentTrack.id === track.id) {
                        if (this.isPlaying) {
                            this.audio.pause();
                            this.isPlaying = false;
                        } else {
                            this.audio.play();
                            this.isPlaying = true;
                        }
                    } else {
                        if (this.audio) {
                            this.audio.pause();
                        }
                        this.currentTrack = track;
                        this.audio.src = track.preview_url;
                        this.audio.play();
                        this.isPlaying = true;
                    }

                    this.audio.onended = () => {
                        this.isPlaying = false;
                    };
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
