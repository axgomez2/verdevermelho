@props(['vinyls'])

<div class="bg-base-300 rounded-xl overflow-hidden"
     x-data="vinylCarousel"
     x-init="startAutoplay">
    <div class="relative">
        <!-- Hot Sales Badge -->
        <div class="absolute top-4 left-4 z-20">
            <span class="bg-primary text-primary-content px-3 py-1 rounded-md text-sm font-medium">
                Hot Sales
            </span>
        </div>

        <!-- Carousel Container -->
        <div class="relative overflow-hidden">
            <div class="flex transition-transform duration-500 ease-out"
                 :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                @foreach($vinyls as $vinyl)
                    <div class="w-full flex-shrink-0">
                        <a href="{{ route('site.vinyl.show', ['artistSlug' => $vinyl->artists->first()->slug, 'titleSlug' => $vinyl->slug]) }}" class="block relative h-[250px] group">
                            <!-- Background Image with Filter -->
                            <div class="absolute inset-0 bg-cover bg-center"
                                 style="background-image: url('{{ $vinyl->cover_image ?? '/placeholder.svg?height=300&width=600' }}');">
                                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm group-hover:bg-black/40 transition-colors duration-300"></div>
                            </div>

                            <!-- Content -->
                            <div class="relative z-10 h-full p-6 flex items-center">
                                <img src="{{ $vinyl->cover_image ?? '/placeholder.svg?height=150&width=150' }}"
                                     alt="{{ $vinyl->title }}"
                                     class="w-32 h-32 object-cover rounded-lg shadow-lg transition-transform duration-300 group-hover:scale-105"/>
                                <div class="ml-6 flex flex-col justify-center text-white">
                                    <h3 class="text-2xl font-bold group-hover:text-primary transition-colors duration-300">{{ $vinyl->artists->pluck('name')->join(', ') }}</h3>
                                    <p class="text-xl mt-1">{{ $vinyl->title }}</p>
                                    <p class="text-sm text-white/80 mt-2">
                                        {{ $vinyl->format ?? '12 inch' }} /
                                        {{ $vinyl->record_label->name ?? 'Unknown Label' }}
                                    </p>
                                    <div class="flex gap-2 mt-3">
                                        @foreach($vinyl->genres as $genre)
                                            <span class="badge badge-sm bg-primary text-primary-content">{{ $genre->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- View Details Button -->
                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span class="btn btn-primary btn-sm">Ver Detalhes</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Navigation Dots -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex justify-center gap-1 z-20">
            @foreach($vinyls as $vinyl)
                <button
                    @click="currentSlide = {{ $loop->index }}"
                    :class="{'bg-primary': currentSlide === {{ $loop->index }}}"
                    class="w-2 h-2 rounded-full bg-white/50 transition-colors">
                </button>
            @endforeach
        </div>

        <!-- Navigation Buttons -->
        <button @click.stop="prev"
                class="absolute left-2 top-1/2 -translate-y-1/2 btn btn-circle btn-sm bg-white/20 hover:bg-white/40 border-none z-20">❮</button>
        <button @click.stop="next"
                class="absolute right-2 top-1/2 -translate-y-1/2 btn btn-circle btn-sm bg-white/20 hover:bg-white/40 border-none z-20">❯</button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('vinylCarousel', () => ({
        currentSlide: 0,
        autoplayInterval: null,
        totalSlides: {{ count($vinyls) }},

        next() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        },

        prev() {
            this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        },

        startAutoplay() {
            this.autoplayInterval = setInterval(() => this.next(), 5000);
        },

        stopAutoplay() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
            }
        }
    }));
});
</script>
@endpush
