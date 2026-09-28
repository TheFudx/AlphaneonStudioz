<!-- Sidebar -->
<aside id="sidebar"
    class="sidebar fixed md:static top-0 left-0 w-64 bg-dark-light flex flex-col md:flex-shrink-0 
           transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-50"
    style="height: calc(100vh - 80px);">
    <!-- 80px = your music player height, adjust if needed -->

    <!-- Logo Section - Fixed, won't scroll -->
    <div class="px-6 pt-6 pb-4 flex justify-between items-center flex-shrink-0">
        <a href="{{ url('/') }}" class="flex items-center space-x-2 text-white">
            <img src="{{ url('/') }}/asset/images/logo.png"
                alt="Alphanon Studio Logo" class="aside_logo">
        </a>

        <!-- Close Button (Mobile Only) -->
        <button id="close-sidebar" class="md:hidden text-gray-400 hover:text-white text-xl">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Scrollable Nav Section -->
    <nav class="flex-1 overflow-y-auto px-6 pb-6 space-y-4"
         style="scrollbar-width: thin; scrollbar-color: #ff5c40 transparent;">

        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">Discover</h3>
        <ul class="space-y-2">
            <li class="sidebar-link">
                <a href="{{ route('popular.albums') }}" data-link
                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200 
                    text-theme font-semibold {{ request()->is('popular-albums') ? 'active' : '' }}">
                    <i class="fas fa-compact-disc"></i>
                    <span>Popular Albums</span>
                </a>
            </li>
            <li class="sidebar-link">
                <a href="{{ route('popular.songs') }}" data-link data-fullpage="1"
                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200 
                    text-theme font-semibold {{ request()->is('popular-songs') ? 'active' : '' }}">
                    <i class="fas fa-music"></i>
                    <span>Popular Songs</span>
                </a>
            </li>
        </ul>

        <h3 class="text-xs font-semibold text-gray-500 uppercase mt-4 mb-2">Your Music</h3>
        <ul class="space-y-2">
            <li class="sidebar-link">
                <a href="{{ route('albums.liked') }}" data-link
                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200 
                    text-theme font-semibold {{ request()->is('liked-albums*') ? 'active' : '' }}">
                    <i class="fas fa-thumbs-up"></i>
                    <span>Liked Albums</span>
                </a>
            </li>
            <li class="sidebar-link">
                <a href="{{ route('songs.liked') }}" data-link
                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200 
                    text-theme font-semibold {{ request()->is('liked-songs*') ? 'active' : '' }}">
                    <i class="fas fa-thumbs-up"></i>
                    <span>Liked Songs</span>
                </a>
            </li>
            <li class="sidebar-link">
                <a href="{{ route('album.wishlist') }}" data-link
                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200 
                    text-theme font-semibold {{ request()->is('album-wishlist*') || request()->is('album.wishlist') ? 'active' : '' }}">
                    <i class="fas fa-heart"></i>
                    <span>Album Wishlist</span>
                </a>
            </li>
            <li class="sidebar-link">
                <a href="{{ route('songs.wishlist') }}" data-link
                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200 
                    text-theme font-semibold {{ request()->is('songs-wishlist*') || request()->is('songs.wishlist') ? 'active' : '' }}">
                    <i class="fas fa-heart"></i>
                    <span>Song Wishlist</span>
                </a>
            </li>
        </ul>

        @php
            $playlists = DB::table('music_playlist')
                ->where('user_id', auth()->id())
                ->select('playlist_name')
                ->distinct()
                ->get();
        @endphp

        <h3 class="text-xs font-semibold text-gray-500 uppercase mt-4 mb-2">Your Playlists</h3>

        <li class="sidebar-link list-none">
            <button id="create-playlist-sidebar"
                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 w-full text-left text-theme font-semibold">
                <i class="fas fa-plus"></i>
                <span>New Playlist</span>
            </button>
        </li>

        <ul id="sidebar-playlists" class="space-y-2">
            @if ($playlists->count() > 0)
                @include('musicplayer.partials.sidebar-playlists')
            @endif
        </ul>

    </nav>
</aside>

<!-- Mobile Toggle Button -->
<button id="open-sidebar" class="md:hidden fixed top-4 left-4 z-50 bg-dark-light p-2 rounded-lg shadow-lg text-white">
    <i class="fas fa-bars"></i>
</button>