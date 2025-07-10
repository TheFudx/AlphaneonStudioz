 <footer class="fixed-footer-player">
        <div class="flex items-center space-x-4 min-w-0 flex-shrink-0">
            <img id="player-thumbnail" src="https://placehold.co/60x60/374151/FFFFFF?text=Album" alt="Current Album Cover" class="h-16 w-16 rounded-md object-cover">
            <div class="truncate">
                <p id="player-title" class="font-semibold text-lg truncate">Not Playing</p>
                <p id="player-artist" class="text-gray-400 text-sm truncate">Select a song</p>
            </div>
        </div>

        <div class="flex flex-col items-center flex-grow max-w-2xl px-4">
            <div class="flex items-center space-x-6 mb-2">
                <button class="text-gray-400 hover:text-white"><i class="fas fa-shuffle text-lg"></i></button>
                <button class="text-gray-400 hover:text-white"><i class="fas fa-backward-step text-xl"></i></button>
                <button id="footer-play-pause-btn" class="bg-theame text-white h-12 w-12 rounded-full flex items-center justify-center shadow-lg transform transition duration-300 hover:scale-105">
                    <i class="fas fa-play text-xl"></i>
                </button>
                <button class="text-gray-400 hover:text-white"><i class="fas fa-forward-step text-xl"></i></button>
                <button class="text-gray-400 hover:text-white"><i class="fas fa-repeat text-lg"></i></button>
            </div>
            <div class="flex items-center w-full space-x-2">
                <span id="current-time" class="text-xs text-gray-400">00:00</span>
                <input type="range" id="music-seek-bar" value="0" min="0" max="100">
                <span id="total-duration" class="text-xs text-gray-400">00:00</span>
            </div>
        </div>

        <div class="flex items-center space-x-3 flex-shrink-0">
            <i class="fas fa-volume-up text-gray-400 text-lg"></i>
            <input type="range" id="volume-slider" min="0" max="1" step="0.01" value="1">
        </div>
    </footer>