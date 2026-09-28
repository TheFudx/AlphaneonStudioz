<footer id="footer-player"
    class="fixed-footer-player bg-dark-light border-t border-gray-700 px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-6 w-full">
    <!-- Left Section: Thumbnail + Title + Actions (Like, Wishlist, Share) -->
    <div class="flex items-center justify-between sm:justify-start gap-2 sm:gap-4 min-w-0 flex-shrink-0 w-full sm:w-auto">
        <div class="flex items-center gap-3 min-w-0">
            <div id="player-thumb-wrapper"
                class="h-10 w-10 sm:h-12 sm:w-12 rounded-md flex items-center justify-center overflow-hidden bg-gray-700 text-white font-bold text-xl uppercase flex-shrink-0">
                <img id="player-thumbnail" class="w-full h-full object-cover"
                    src="https://placehold.co/60x60/374151/FFFFFF?text=Album">
            </div>
            <div class="truncate max-w-[120px] sm:max-w-[150px] md:max-w-[190px] lg:max-w-[240px]">
                <p id="player-title" class="font-semibold text-xs sm:text-sm truncate">Not Playing</p>
                <p id="player-artist" class="text-gray-400 text-[11px] sm:text-xs truncate">Select a song</p>
                <input type="hidden" id="player-album" value="">
                <input type="hidden" id="player-plays" value="">
            </div>
        </div>

        <!-- Quick Actions: Like, Wishlist, Share -->
        <div class="flex items-center gap-1 sm:gap-1.5 flex-shrink-0 ml-1 sm:ml-2">
            <!-- Like Button (Thumbs Up) -->
            <button id="footer-like-btn" type="button" data-song-id=""
                class="song-like-btn w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 active:scale-90 transition focus:outline-none"
                title="Like">
                <i class="fa-regular fa-thumbs-up text-sm pointer-events-none"></i>
            </button>

            <!-- Wishlist Button (Heart) -->
            <button id="footer-wishlist-btn" type="button" data-song-id=""
                class="song-wishlist w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 active:scale-90 transition focus:outline-none"
                title="Wishlist">
                <i class="fa-regular fa-heart text-sm pointer-events-none"></i>
            </button>

            <!-- Share Button -->
            <button id="footer-share-btn" type="button" data-id="" data-album-name=""
                class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 active:scale-90 transition focus:outline-none"
                title="Share">
                <i class="fa-solid fa-share-nodes text-sm pointer-events-none"></i>
            </button>
        </div>
    </div>

    <!-- Middle Section: Controls + Seekbar -->
    <div class="flex flex-col items-center w-full sm:max-w-xl">
        <!-- Controls Row -->
        <div class="flex items-center justify-center gap-6 mb-1">
            <button id="shuffle-btn" class="text-gray-400 hover:text-white transition" title="Shuffle">
                <i class="fas fa-shuffle text-base sm:text-lg"></i>
            </button>
            <button id="backward-btn" class="text-gray-400 hover:text-white transition" title="Previous">
                <i class="fas fa-backward-step text-base sm:text-lg"></i>
            </button>
            <!-- Add this inside your footer player, near the play button -->
            <div id="track-loader" class="hidden">
                <i class="fas fa-spinner fa-spin text-white text-sm"></i>
            </div>
            <button id="footer-play-pause-btn"
                class="bg-theame text-white h-8 w-8 sm:h-9 sm:w-9 rounded-full flex items-center justify-center shadow-lg transform transition duration-300 hover:scale-105" title="Play/Pause">
                <i class="fas fa-play text-base sm:text-md"></i>
            </button>
            <button id="forward-btn" class="text-gray-400 hover:text-white transition" title="Next">
                <i class="fas fa-forward-step text-base sm:text-lg"></i>
            </button>
            <button id="repeat-btn" class="text-gray-400 hover:text-white transition relative flex items-center justify-center" title="Repeat: Off">
                <i class="fas fa-repeat text-base sm:text-lg"></i>
                <span id="repeat-badge"
                    class="hidden absolute -top-1.5 -right-2 font-bold text-white rounded-full flex items-center justify-center pointer-events-none shadow"
                    style="font-size: 8px; width: 13px; height: 13px; line-height: 1; background-color: var(--color-theme, #FF3A1F);">1</span>
            </button>
        </div>

        <!-- Progress Bar -->
        <div class="flex items-center w-full gap-2">
            <span id="current-time" class="text-[9px] sm:text-[10px] text-gray-400">00:00</span>
            <input type="range" id="music-seek-bar" class="flex-1 accent-theame h-1 rounded-full" min="0"
                max="100" step="0.1" value="0" />
            <span id="total-duration" class="text-[9px] sm:text-[10px] text-gray-400">00:00</span>
        </div>
    </div>

    <!-- Right Section: Volume -->
    <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto justify-center sm:justify-end">
        <i class="fas fa-volume-up text-gray-400 text-sm sm:text-lg"></i>
        <input type="range" id="volume-slider" class="w-24 sm:w-28 accent-theame" min="0" max="1"
            step="0.01" value="1" />

        <!-- Song Details Button -->
        <button id="song-details-btn"
            class="ml-2 h-8 w-8 sm:h-9 sm:w-9 rounded-full flex items-center justify-center
               bg-gray-700 hover:bg-gray-600 text-white transition"
            title="Song Details">
            <i class="fas fa-info text-xs sm:text-sm"></i>
        </button>
    </div>

</footer>
