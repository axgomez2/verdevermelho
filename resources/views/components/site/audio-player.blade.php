<div id="audio-player" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg p-4 z-50 hidden">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center space-x-4">
                <img id="album-cover" src="/path/to/default/cover.jpg" alt="Album cover" class="w-16 h-16 object-cover rounded-md">
                <div>
                    <h3 id="track-title" class="text-lg font-semibold"></h3>
                    <p id="track-artist" class="text-sm text-gray-600"></p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button id="prev-btn" class="focus:outline-none">
                    <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button id="play-pause-btn" class="focus:outline-none">
                    <svg id="play-icon" class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg id="pause-icon" class="w-8 h-8 text-gray-800 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </button>
                <button id="next-btn" class="focus:outline-none">
                    <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
            <div class="flex items-center space-x-4">
                <span id="current-time" class="text-sm text-gray-600">0:00</span>
                <span class="text-sm text-gray-600">/</span>
                <span id="duration" class="text-sm text-gray-600">0:00</span>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-800 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15.536a5 5 0 001.414 1.414m0 0l-2.828 2.828-1.414-1.414 2.828-2.828a9 9 0 0112.728 0l-1.414 1.414a7 7 0 00-9.9 0z"></path>
                    </svg>
                    <input type="range" id="volume-control" min="0" max="100" value="100" class="w-20">
                </div>
            </div>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-4 dark:bg-gray-700 relative" id="progress-container">
            <div id="progress-bar" class="bg-blue-600 h-full rounded-full relative" style="width: 0%">
                <div id="progress-handle" class="absolute right-0 top-1/2 transform -translate-y-1/2 w-4 h-4 bg-blue-600 rounded-full shadow-md cursor-pointer"></div>
            </div>
            <div id="progress-preview" class="absolute top-0 h-full bg-blue-300 opacity-50 pointer-events-none hidden"></div>
        </div>
    </div>
    <div id="youtube-player"></div>
</div>

