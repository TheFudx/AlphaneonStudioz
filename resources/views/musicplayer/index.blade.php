<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alphanon Studio</title>
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="{{ $meta_description ?? config('app.project_full_name') . ' is an entertainment platform where we are offering a wide range of content. ' . config('app.project_full_name') . ' provides you the best video streaming experience.' }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ URL::to('/asset/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ URL::to('/asset/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ URL::to('/asset/favicon_io/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ URL::to('/asset/favicon_io/site.webmanifest') }}">
    <link rel="manifest" href="/manifest.json">

    
    <!-- Facebook Open Graph Tags -->
    <meta property="og:title" content="{{ $meta_title ?? config('app.project_name') }}" />
    <meta property="og:description"
        content="{{ $meta_description ?? config('app.project_full_name') . ' provides you the best video streaming experience.' }}" />
    <meta property="og:image" content="{{ $meta_image ?? url('/') . '/asset/images/logo.png' }}" />
    <meta property="og:url" content="{{ $meta_url ?? config('app.website_url') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="{{ config('app.project_name') }}" />
    <meta property="og:locale" content="en_US" />
    <link rel="canonical" href="{{ url()->current() }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.4/howler.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"> --}}
    </script>
    <style>
        .d-none {
            display: none !important;
        }
        :root {
            --color-dark: #1A1A1A;
            /* A very dark charcoal */
            --color-dark-light: #2C2C2C;
            /* A slightly lighter charcoal */
            --color-gray-700-custom: #374151;
            /* Custom shade for hover */
            --color-gray-400-custom: #9CA3AF;
            /* Custom shade for text */
            --color-theme: #FF3A1F;
            /* Define the theme color for consistency */
        }

        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: var(--color-dark);
        }

        .bg-dark {
            background-color: var(--color-dark);
        }

        .bg-dark-light {
            background-color: var(--color-dark-light);
        }

        .hover\:bg-gray-700:hover {
            background-color: var(--color-gray-700-custom);
        }

        .text-gray-400 {
            color: var(--color-gray-400-custom);
        }

        /* ================================
       GLOBAL SCROLLBAR (fallback)
       ================================ */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--color-dark-light);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #4A5568;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #606f80;
        }

        .sidebar-link {
            position: relative;
            display: block;
            color: #fff;
            text-decoration: none;
        }

        .sidebar-link:hover {
            background: #374151;
        }

        .sidebar-link a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: var(--color-theme);
            border-radius: 0 4px 4px 0;
        }

        /* ================================
       LEFT SIDEBAR SCROLLBAR
       ================================ */
        .sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--color-theme);
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #ff5c40;
            /* brighter theme */
        }

        /* ================================
       MAIN CONTENT SCROLLBAR
       ================================ */
        #main-content::-webkit-scrollbar {
            width: 10px;
        }

        #main-content::-webkit-scrollbar-track {
            background: var(--color-dark);
        }

        #main-content::-webkit-scrollbar-thumb {
            background: #ff5c40;
            /* teal green */
        }

        #main-content::-webkit-scrollbar-thumb:hover {
            background: #ff5c40;
        }

        /* ================================
        RIGHT SIDEBAR (ALBUM SONGS)
        ================================ */
        #album-songs::-webkit-scrollbar,
        #songs-list::-webkit-scrollbar {
            width: 6px;
        }

        #album-songs::-webkit-scrollbar-thumb,
        #songs-list::-webkit-scrollbar-thumb {
            background: #ff5c40;
            border-radius: 999px;
        }

        #album-songs::-webkit-scrollbar-thumb:hover,
        #songs-list::-webkit-scrollbar-thumb:hover {
            background: #ff7860;
        }

        /* Firefox support */
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: var(--color-theme) var(--color-dark-light);
        }

        #sidebar nav::-webkit-scrollbar {
            width: 4px;
        }

        #sidebar nav::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidebar nav::-webkit-scrollbar-thumb {
            background-color: #ff5c40;
            border-radius: 999px;
        }

        #main-content {
            scrollbar-width: thin;
            scrollbar-color: #ff5c40 var(--color-dark);
        }

        #album-songs,
        #songs-list {
            scrollbar-width: thin;
            scrollbar-color: #ff5c40 var(--color-dark-light);
        }

        /* ================================
       Your existing custom styles
       ================================ */
        .aside_logo {
            height: 68px;
        }

        .bg-theame {
            background-color: var(--color-theme);
            font-weight: 600;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .bg-theame:hover {
            font-weight: 700;
        }

        .bg-play-button {
            background-color: var(--color-theme)
        }

        .album-play-button {
            width: 60px;
            height: 60px;
            font-size: 1.8rem;
        }

        .table-play-pause-button {
            width: 32px;
            height: 32px;
        }

        .table-image {
            height: 35px;
            width: 35px;
            display: inline;
        }

        .fixed-footer-player {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: var(--color-dark-light);
            border-top: 1px solid #374151;
            z-index: 1000;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        main {
            padding-bottom: 96px;
        }

        input[type="range"] {
            width: 100%;
        }

        /* Seek bar styling */
        #music-seek-bar {
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, var(--color-theme) 0%, var(--color-theme) 0%, #4A5568 0%, #4A5568 100%);
            border-radius: 2px;
            -webkit-appearance: none;
            appearance: none;
            cursor: pointer;
            transition: background 0.2s ease-in-out;
        }

        #music-seek-bar::-webkit-slider-runnable-track {
            width: 100%;
            height: 4px;
            background: transparent;
            border-radius: 2px;
        }

        #music-seek-bar::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 12px;
            height: 12px;
            background-color: var(--color-theme);
            border-radius: 50%;
            cursor: pointer;
            margin-top: -4px;
            box-shadow: 0 0 0 2px rgba(255, 58, 31, 0.4);
            transition: background-color 0.2s ease-in-out;
        }

        #music-seek-bar::-moz-range-thumb {
            width: 12px;
            height: 12px;
            background-color: var(--color-theme);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 0 0 2px rgba(255, 58, 31, 0.4);
            transition: background-color 0.2s ease-in-out;
        }

        /* Volume slider styling */
        #volume-slider {
            width: 100px;
            height: 4px;
            background: linear-gradient(to right, var(--color-theme) 0%, var(--color-theme) 0%, #4A5568 0%, #4A5568 100%);
            border-radius: 2px;
            -webkit-appearance: none;
            appearance: none;
            cursor: pointer;
            transition: background 0.2s ease-in-out;
        }

        #volume-slider::-webkit-slider-runnable-track {
            width: 100%;
            height: 4px;
            background: transparent;
            border-radius: 2px;
        }

        #volume-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 12px;
            height: 12px;
            background-color: var(--color-theme);
            border-radius: 50%;
            cursor: pointer;
            margin-top: -4px;
            box-shadow: 0 0 0 2px rgba(255, 58, 31, 0.4);
            transition: background-color 0.2s ease-in-out;
        }

        #volume-slider::-moz-range-thumb {
            width: 12px;
            height: 12px;
            background-color: var(--color-theme);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 0 0 2px rgba(255, 58, 31, 0.4);
            transition: background-color 0.2s ease-in-out;
        }

        .play-track {
            position: relative;
            overflow: hidden;
        }

        .play-track img {
            transition: 0.3s ease;
        }

        .play-track.playing img {
            opacity: 0.6;
            /* little fade */
            filter: brightness(1.1);
        }

        /* Overlay */
        .play-track::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.25);
            opacity: 0;
            transition: 0.3s ease;
            pointer-events: none;
        }

        .play-track.playing::after {
            opacity: 1;
        }

        .play-song.playing {
            background-color: rgba(255, 92, 64, 0.14) !important;
            border-color: rgba(255, 92, 64, 0.5) !important;
            box-shadow: 0 0 15px rgba(255, 92, 64, 0.15);
        }

        .play-song.playing h4,
        .play-song.playing .song-title-text {
            color: #ff5c40 !important;
            font-weight: 700;
        }

        /* Fade image when playing */
        .play-song.playing img {
            opacity: 0.75;
        }

        /* Make overlay always visible when playing */
        .play-song.playing .thumbnail-overlay {
            opacity: 1 !important;
        }

        /* Pause icon visible */
        .play-song.playing .thumbnail-overlay i {
            content: "\f04c";
            /* pause icon */
        }

        /* Toast Notification */
        .custom-toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(50px);
            background: #fff;
            color: #ef4444;
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 14px;
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 99999;
        }

        .custom-toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* Dark overlay for mobile when sidebar is open */
        #sidebar.open::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        @media (max-width: 768px) {
            #album-songs {
                display: none;
                /* Hide sidebar on small screens */
            }

            #mobile-songs-list {
                display: block;
                width: 100%;
                margin-top: 1rem;
                background: var(--color-dark-light);
                border-radius: 0.5rem;
                padding: 1rem;
            }
        }

        @media (min-width: 769px) {
            #mobile-songs-list {
                display: none;
                /* Hide mobile list on bigger screens */
            }
        }

        .music-loader {
            display: flex;
            align-items: flex-end;
            height: 30px;
        }

        .music-loader .wave {
            width: 4px;
            height: 10px;
            background: linear-gradient(to top, #ff5c40, #ff9966);
            margin: 0 2px;
            border-radius: 50px;
            animation: music 0.6s infinite ease-in-out alternate;
        }

        .music-loader .wave:nth-child(1) {
            animation-delay: -0.2s;
        }

        .music-loader .wave:nth-child(2) {
            animation-delay: -0.4s;
        }

        .music-loader .wave:nth-child(3) {
            animation-delay: -0.6s;
        }

        .music-loader .wave:nth-child(4) {
            animation-delay: -0.8s;
        }

        .music-loader .wave:nth-child(5) {
            animation-delay: -1s;
        }

        @keyframes music {
            0% {
                height: 6px;
            }

            100% {
                height: 30px;
            }
        }

        .song-active {
            background-color: #374151 !important;
            /* gray-700 */
            border-left: 3px solid #8b5cf6;
            /* theme color */
        }

        /* Album cards */
        .album-card {
            border: 1px solid transparent;
        }

        .album-card.active {
            border-color: #FF3A1F;
        }

        /* ===== Ultra-thin horizontal scrollbar ===== */
        .album-scroll {
            scrollbar-width: thin;
            /* Firefox (minimum allowed) */
            scrollbar-color: #FF3A1F transparent;
        }

        /* Chrome, Edge, Safari */
        .album-scroll::-webkit-scrollbar {
            height: 1px;
            /* ⚠️ minimum usable */
        }

        .album-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .album-scroll::-webkit-scrollbar-thumb {
            background-color: #FF3A1F;
            border-radius: 10px;
        }

        .album-scroll::-webkit-scrollbar {
            height: 0;
        }

        .album-scroll:hover::-webkit-scrollbar {
            height: 1px;
        }

        .animate-slideUp {
            animation: slideUp 0.3s ease-out;
        }
    </style>
</head>

<body class="bg-dark text-white flex min-h-screen" style="height:90vh">
    <div id="page-loader" class="hidden fixed inset-0 flex items-center justify-center bg-black/30 z-50">
        <div class="music-loader flex space-x-1">
            <div class="wave"></div>
            <div class="wave"></div>
            <div class="wave"></div>
            <div class="wave"></div>
            <div class="wave"></div>
        </div>
    </div>
    @include('musicplayer.partials.sidebar')
    <main class="flex-1 flex flex-col overflow-hidden">
        @include('musicplayer.partials.header')
        <div class="flex flex-1 overflow-hidden">
            <!-- Left: Main Content (bigger) -->
            <div id="main-content" style="height:73vh" class="flex-1 min-w-0 overflow-y-auto p-3 space-y-4 bg-dark">
                @yield('content')
            </div>
            <!-- Share Modal -->
            <div id="AlbumShareModal"
                class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center transition-opacity duration-300 p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 relative animate-scaleIn">
                    <!-- Close Button -->
                    <button class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl">
                        &times;
                    </button>
                    <h3 class="text-xl font-semibold mb-5 text-center">Share</h3>
                    <!-- Social Icons Row -->
                    <div class="flex justify-around mb-5">
                        <a id="shareWhatsapp" href="#" target="_blank"
                            class="flex flex-col items-center gap-1 hover:opacity-80">
                            <div class="bg-gray-100 p-3 rounded-full">
                                <i class="fa-brands fa-whatsapp text-2xl" style="color:#25D366;"></i>
                            </div>
                            <span class="text-sm text-gray-700">WhatsApp</span>
                        </a>
                        <a id="shareFacebook" href="#" target="_blank"
                            class="flex flex-col items-center gap-1 hover:opacity-80">
                            <div class="bg-gray-100 p-3 rounded-full">
                                <i class="fa-brands fa-facebook text-2xl" style="color:#1877F2;"></i>
                            </div>
                            <span class="text-sm text-gray-700">Facebook</span>
                        </a>
                        <a id="shareInstagram" href="#" target="_blank"
                            class="flex flex-col items-center gap-1 hover:opacity-80">
                            <div class="bg-gray-100 p-3 rounded-full">
                                <i class="fa-brands fa-instagram text-2xl"
                                    style="background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);
                            -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                            </div>
                            <span class="text-sm text-gray-700">Instagram</span>
                        </a>
                    </div>
                    <!-- Copy Link Box -->
                    <div class="flex items-center border rounded-xl overflow-hidden">
                        <input type="text" id="shareLink" class="flex-1 p-3 text-gray-700 text-sm focus:outline-none"
                            readonly>
                        <button onclick="copyShareLink()"
                            class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-3 font-medium">
                            Copy
                        </button>
                    </div>
                </div>
            </div>
            <!-- Right: Songs (smaller) -->
            <div id="album-songs" style="height:73vh" class="w-64 lg:w-72 flex-shrink-0 bg-dark-light rounded-xl border border-gray-800 shadow-xl ml-2 flex flex-col overflow-hidden">
                <!-- Static Header (Non-scrolling) -->
                <div class="flex items-center justify-between px-3 py-2.5 border-b border-gray-800 flex-shrink-0 bg-dark-light">
                    <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                        <i class="fas fa-compact-disc text-theme"></i>
                        <span>Songs</span>
                    </h3>
                </div>
                <!-- Scrollable Songs List -->
                <div id="songs-list" class="flex-1 min-h-0 overflow-y-auto p-2.5 sm:p-3 space-y-1.5 text-gray-300">
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-music text-3xl mb-2 text-gray-600 block animate-pulse"></i>
                        <p class="text-xs">Select an album to view songs.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div id="songDetailsModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50">
        <div class="bg-gray-900 w-[96%] sm:w-[90%] max-w-2xl lg:max-w-3xl rounded-3xl p-8 sm:p-10 relative text-center">
            <!-- Close Button -->
            <button id="closeSongModal" class="absolute top-3 right-3 text-gray-400 hover:text-white text-xl"> &times;
            </button>
            <!-- Album Image -->
            <img id="modal-thumbnail" src="https://placehold.co/300x300/374151/FFFFFF?text=Album"
                class="w-48 h-48 mx-auto rounded-lg object-cover mb-4">
            <!-- Song Info -->
            <h2 id="modal-title" class="text-lg font-semibold truncate">Song Title</h2>
            <p id="modal-artist" class="text-gray-400 text-sm truncate">Artist Name</p>
            <!-- Optional Extra Info -->
            <div class="flex justify-center gap-2 mt-4 flex-wrap">
                <p id="modal-album" class="px-3 py-1 rounded-full bg-gray-800 text-xs text-gray-300 mt-2">Album: —</p>
                <p id="modal-plays" class="px-3 py-1 rounded-full bg-gray-800 text-xs text-gray-300 mt-2">Plays: —</p>
                <p id="modal-duration" class="px-3 py-1 rounded-full bg-gray-800 text-xs text-gray-300 mt-2">Duration:
                    —</p>
            </div>
        </div>
    </div>
    @include('musicplayer.partials.footer')
    <script>
        window.slugify = function(text) {
            if (!text) return "";
            return text.toString()
                .toLowerCase()
                .trim()
                .replace(/&/g, "and")
                .replace(/[^\w\s-]/g, "")
                .replace(/[\s_-]+/g, "-")
                .replace(/^-+|-+$/g, "");
        };
        var slugify = window.slugify;

        window.onerror = function(message, source, lineno, colno, error) {
            let errText = `JS Error: ${message} at line ${lineno}`;
            if (typeof showToast === 'function') {
                showToast(`❌ ${errText}`);
            } else {
                console.error(errText);
            }
            return false;
        };
        const detailsBtn = document.getElementById('song-details-btn');
        const modal = document.getElementById('songDetailsModal');
        const closeBtn = document.getElementById('closeSongModal');
        detailsBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const title = document.getElementById('player-title')?.innerText || '';
            const artist = document.getElementById('player-artist')?.innerText || '';
            const album = document.getElementById('player-album')?.value || '—';
            const plays = document.getElementById('player-plays')?.value || '0';
            const thumbEl = document.getElementById('player-thumbnail');
            const duration = document.getElementById('total-duration')?.innerText || '00:00';
            document.getElementById('modal-title').innerText = title;
            document.getElementById('modal-artist').innerText = 'Artist: ' + artist;
            document.getElementById('modal-album').innerText = 'Album: ' + album;
            document.getElementById('modal-plays').innerText = 'Plays: ' + plays;
            // Duration formatting
            document.getElementById('modal-duration').innerText = 'Duration: ' + duration;
            const modalThumb = document.getElementById('modal-thumbnail');
            const thumbSrc = thumbEl?.src || '';
            if (!thumbSrc || thumbSrc.includes('placehold.co')) {
                modalThumb.style.display = 'none';
                let avatar = document.getElementById('modal-letter-avatar');
                if (!avatar) {
                    avatar = document.createElement('div');
                    avatar.id = 'modal-letter-avatar';
                    avatar.className =
                        'w-48 h-48 mx-auto rounded-lg flex items-center justify-center bg-gray-700 text-white text-6xl font-bold mb-4 uppercase';
                    modalThumb.parentNode.insertBefore(avatar, modalThumb);
                }
                avatar.innerText = title ? title.charAt(0) : '?';
                avatar.style.display = 'flex';
            } else {
                modalThumb.src = thumbSrc;
                modalThumb.style.display = 'block';
                const avatar = document.getElementById('modal-letter-avatar');
                if (avatar) avatar.style.display = 'none';
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
        closeBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    </script>
    {{-- sidebar toggle --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const openBtn = document.getElementById("open-sidebar");
            const closeBtn = document.getElementById("close-sidebar");
            const pageLoader = document.getElementById("page-loader");

            function closeSidebar() {
                sidebar.classList.add("-translate-x-full");
                sidebar.classList.remove("translate-x-0", "open");
            }
            openBtn.addEventListener("click", () => {
                sidebar.classList.remove("-translate-x-full");
                sidebar.classList.add("translate-x-0", "open");
            });
            closeBtn.addEventListener("click", closeSidebar);
            // 🔥 Show loader when clicking any link inside sidebar
            sidebar.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", () => {
                    pageLoader.classList.remove("hidden");
                    closeSidebar();
                });
            });
            // 🔥 Hide loader ONLY when page finishes loading
            window.addEventListener("load", () => {
                pageLoader.classList.add("hidden");
            });
        });
    </script>
    {{-- Main logic for play pause  --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let currentHowl = null;
            let currentTrackIndex = -1;
            let currentPlayingTrackId = null;
            let currentPlayingTrackSrc = null;
            let currentPlayingButton = null;
            let currentPlayingAlbumBtn = null;
            let playlist = [];
            const footerPlayPauseButton = document.getElementById("footer-play-pause-btn");
            const footerPlayPauseIcon = footerPlayPauseButton.querySelector("i");
            const playerTitle = document.getElementById("player-title");
            const playerArtist = document.getElementById("player-artist");
            const playerAlbum = document.getElementById("player-album");
            const playerPlays = document.getElementById("player-plays");
            const playerThumbnail = document.getElementById("player-thumbnail");
            const musicSeekBar = document.getElementById("music-seek-bar");
            const currentTimeSpan = document.getElementById("current-time");
            const totalDurationSpan = document.getElementById("total-duration");
            const volumeSlider = document.getElementById("volume-slider");
            const backwardBtn = document.getElementById("backward-btn");
            const forwardBtn = document.getElementById("forward-btn");
            const preloadCache = {}; // { index: Howl }

            function preloadTrack(index) {
                if (!playlist[index] || preloadCache[index]) return;
                preloadCache[index] = new Howl({
                    src: [playlist[index].src],
                    html5: true,
                    preload: true, // just load, don't play
                    volume: 0
                });
            }

            // ─── Show / Hide loader ──────────────────────────────────────
            function showTrackLoader() {
                document.getElementById("track-loader").classList.remove("hidden");
                footerPlayPauseButton.classList.add("hidden");
            }

            function hideTrackLoader() {
                document.getElementById("track-loader").classList.add("hidden");
                footerPlayPauseButton.classList.remove("hidden");
            }

            function formatTime(ms) {
                let sec = Math.floor(ms / 1000);
                let min = Math.floor(sec / 60);
                sec = sec % 60;
                return `${String(min).padStart(2, "0")}:${String(sec).padStart(2, "0")}`;
            }

            function updateBarFill(el, value) {
                const themeColor = getComputedStyle(document.documentElement).getPropertyValue("--color-theme")
                    .trim();
                el.style.background = `linear-gradient(to right, ${themeColor} ${value}%, #4A5568 ${value}%)`;
            }

            function resetAllPlayButtons() {
                // Reset all .play-track buttons
                document.querySelectorAll(".play-track").forEach(btn => {
                    btn.classList.remove("playing");
                    const icon = btn.querySelector("i");
                    if (icon) {
                        icon.classList.remove("fa-pause");
                        icon.classList.add("fa-play");
                    }
                    const overlay = btn.querySelector(".thumbnail-overlay");
                    if (overlay) overlay.style.opacity = "";
                });

                // Reset all .song-play buttons
                document.querySelectorAll(".song-play").forEach(btn => {
                    const icon = btn.querySelector("i");
                    if (icon) {
                        icon.classList.remove("fa-pause");
                        icon.classList.add("fa-play");
                    }
                });

                // Reset all song-row highlights
                document.querySelectorAll(".song-row").forEach(row => {
                    row.classList.remove("song-active");
                });

                currentPlayingButton = null;
            }

            // ─── MASTER SYNC — call this whenever play state changes ─────
            function syncAllButtons(trackId, isPlaying) {
                // 1️⃣ Footer play/pause - ALWAYS keep in sync
                if (footerPlayPauseIcon) {
                    footerPlayPauseIcon.classList.toggle("fa-play", !isPlaying);
                    footerPlayPauseIcon.classList.toggle("fa-pause", isPlaying);
                }

                // 2️⃣ Individual song buttons (.play-track, .table-play-pause-button)
                document.querySelectorAll(".play-track, .table-play-pause-button").forEach(btn => {
                    const btnTrackId = btn.dataset.trackId || btn.dataset.songId;
                    const isMatch = Boolean(trackId && btnTrackId && String(btnTrackId) === String(trackId));
                    btn.classList.toggle("playing", isMatch && isPlaying);
                    const icon = btn.querySelector("i");
                    if (icon) {
                        icon.classList.toggle("fa-play", !(isMatch && isPlaying));
                        icon.classList.toggle("fa-pause", isMatch && isPlaying);
                    }
                    // thumbnail overlay
                    const overlay = btn.querySelector(".thumbnail-overlay");
                    if (overlay) overlay.style.opacity = (isMatch && isPlaying) ? "1" : "";
                });

                // 3️⃣ Song-row play buttons (.song-play)
                document.querySelectorAll(".song-play").forEach(btn => {
                    const btnTrackId = btn.dataset.songId || btn.dataset.trackId;
                    const isMatch = Boolean(trackId && btnTrackId && String(btnTrackId) === String(trackId));
                    const icon = btn.querySelector("i");
                    if (icon) {
                        icon.classList.toggle("fa-play", !(isMatch && isPlaying));
                        icon.classList.toggle("fa-pause", isMatch && isPlaying);
                    }
                });

                // 4️⃣ Album-level play button (.album-play)
                const albumContainer = document.getElementById("album-page-container");
                const currentAlbumId = albumContainer?.dataset?.albumId;
                document.querySelectorAll(".album-play").forEach(btn => {
                    const btnAlbumId = $(btn).data("album-id");
                    const isMatch = (currentPlayingAlbumBtn === btn) || 
                        Boolean(currentAlbumId && btnAlbumId && String(btnAlbumId) === String(currentAlbumId) && trackId);
                    if (isMatch) {
                        const icon = $(btn).find("i");
                        icon.toggleClass("fa-play", !isPlaying);
                        icon.toggleClass("fa-pause", isPlaying);
                    }
                });

                // 5️⃣ Playlist play-all button (.playlist-play, .wishlist-songs-play, .liked-songs-play)
                if (currentPlayingAlbumBtn) {
                    const isPlaylistBtn = currentPlayingAlbumBtn.classList.contains("playlist-play") ||
                        currentPlayingAlbumBtn.classList.contains("wishlist-songs-play") ||
                        currentPlayingAlbumBtn.classList.contains("liked-songs-play");
                    if (isPlaylistBtn) {
                        const icon = $(currentPlayingAlbumBtn).find("i");
                        icon.toggleClass("fa-play", !isPlaying);
                        icon.toggleClass("fa-pause", isPlaying);
                    }
                }

                // 6️⃣ Active row highlight
                document.querySelectorAll(".song-row").forEach(row => {
                    const rowSongId = row.dataset.songId || row.dataset.trackId;
                    const isMatch = Boolean(trackId && rowSongId && String(rowSongId) === String(trackId));
                    row.classList.toggle("song-active", isMatch && isPlaying);
                });
            }

            function resetPlayer() {
                if (currentHowl) {
                    currentHowl.stop();
                    if (currentHowl._sounds) {
                        currentHowl._sounds.forEach(s => {
                            if (s._node && typeof s._node.pause === 'function') {
                                s._node.pause();
                                s._node.currentTime = 0;
                            }
                        });
                    }
                    currentHowl.unload();
                }
                currentHowl = null;
                currentTrackIndex = -1;
                currentPlayingTrackId = null;
                currentPlayingTrackSrc = null;
                currentPlayingButton = null;
                currentPlayingAlbumBtn = null;
                resetAllPlayButtons();
                if (currentPlayingAlbumBtn) {
                    $(currentPlayingAlbumBtn).find("i").removeClass("fa-pause").addClass("fa-play");
                }
                footerPlayPauseIcon.classList.remove("fa-pause");
                footerPlayPauseIcon.classList.add("fa-play");
                playerTitle.textContent = "Not Playing";
                playerArtist.textContent = "Select a song";
                playerThumbnail.src = "https://placehold.co/60x60/374151/FFFFFF?text=Album";
                currentTimeSpan.textContent = "00:00";
                totalDurationSpan.textContent = "00:00";
                musicSeekBar.value = 0;
                updateBarFill(musicSeekBar, 0);
            }

            function getActiveTrackElements() {
                const isMobile = window.innerWidth <= 768;
                if (isMobile) {
                    const mobilePlaying = document.querySelectorAll("#mobile-songs li.play-track, #mobile-songs .play-song");
                    if (mobilePlaying.length > 0) return Array.from(mobilePlaying);
                    const mobileCards = document.querySelectorAll("#songs-card-body .table-play-pause-button, #songs-card-body .play-track");
                    if (mobileCards.length > 0) return Array.from(mobileCards);
                } else {
                    const sidebarPlaying = document.querySelectorAll("#songs-list li.play-track, #songs-list .play-song");
                    if (sidebarPlaying.length > 0) return Array.from(sidebarPlaying);
                    const tableButtons = document.querySelectorAll("#songs-table-body .table-play-pause-button");
                    if (tableButtons.length > 0) return Array.from(tableButtons);
                    const tableRows = document.querySelectorAll("#songs-table-body tr.song-row");
                    if (tableRows.length > 0) return Array.from(tableRows);
                }
                const fallbackPlaying = document.querySelectorAll("#songs-list .play-track, #mobile-songs .play-track, #songs-table-body .table-play-pause-button, .table-play-pause-button");
                if (fallbackPlaying.length > 0) return Array.from(fallbackPlaying);
                return Array.from(document.querySelectorAll("#songs-table-body tr.song-row, #songs-card-body > div"));
            }

            window.buildPlaylist = function() {
                 const newButtons = getActiveTrackElements();
                 const currentButtons = playlist.map(t => t.button);

                 let isSame = newButtons.length === currentButtons.length;
                 if (isSame) {
                     for (let i = 0; i < newButtons.length; i++) {
                         if (newButtons[i] !== currentButtons[i]) {
                             isSame = false;
                             break;
                         }
                     }
                 }
                 if (isSame) return;

                // ✅ Save currently playing track before rebuilding
                const playingSrc = (currentHowl && currentTrackIndex >= 0) ?
                    playlist[currentTrackIndex]?.src :
                    currentPlayingTrackSrc;

                playlist = [];

                Object.values(preloadCache).forEach(h => h.unload());
                Object.keys(preloadCache).forEach(k => delete preloadCache[k]);

                newButtons.forEach((btn, index) => {
                    playlist.push({
                        id: btn.dataset.trackId || btn.dataset.songId || btn.getAttribute("data-track-id") || $(btn).data("track-id"),
                        src: btn.dataset.audioSrc || $(btn).data("audio-src"),
                        title: btn.dataset.title,
                        artist: btn.dataset.artist,
                        thumbnail: btn.dataset.thumbnail,
                        duration: parseInt(btn.dataset.duration),
                        album: btn.dataset.album,
                        plays: btn.dataset.plays,
                        button: btn,
                    });
                    btn.dataset.index = index;
                    btn.onclick = onSongClick;
                });

                // ✅ Re-sync playing state after rebuild
                if ((playingSrc || currentPlayingTrackId) && currentHowl) {
                    const newIndex = playlist.findIndex(t => 
                        (playingSrc && t.src === playingSrc) || 
                        (currentPlayingTrackId && String(t.id) === String(currentPlayingTrackId))
                    );
                    if (newIndex !== -1) {
                        currentTrackIndex = newIndex;
                    }
                    syncAllButtons(currentPlayingTrackId, currentHowl.playing());
                }

                console.log("Playlist rebuilt:", playlist.length, "songs");
            };

            function onSongClick(e) {
                if (e.target.closest && e.target.closest('.song-wishlist, .song-like-btn, .SongShareBtn, .share-song, .add-to-playlist')) {
                    return;
                }
                if (e.target.closest && e.target.closest('button.table-play-pause-button, button.song-play, button.play-track') && e.currentTarget !== e.target.closest('button')) {
                    return;
                }
                e.preventDefault();
                e.stopPropagation();

                const clickedIndex = parseInt(e.currentTarget.dataset.index);
                const clickedTrackId = e.currentTarget.dataset.trackId || e.currentTarget.dataset.songId || $(e.currentTarget).data('track-id') || $(e.currentTarget).data('song-id');
                const clickedSrc = e.currentTarget.dataset.audioSrc || $(e.currentTarget).data('audio-src');

                const isCurrentPlayingSong = (currentHowl && (
                    (currentPlayingTrackId && clickedTrackId && String(currentPlayingTrackId) === String(clickedTrackId)) ||
                    (currentPlayingTrackSrc && clickedSrc && currentPlayingTrackSrc === clickedSrc) ||
                    (!isNaN(clickedIndex) && clickedIndex === currentTrackIndex)
                ));

                if (isCurrentPlayingSong) {
                    if (currentHowl.playing()) {
                        currentHowl.pause();
                        if (currentHowl._sounds) {
                            currentHowl._sounds.forEach(s => {
                                if (s._node && typeof s._node.pause === 'function') s._node.pause();
                            });
                        }
                        updateIcons(false);
                    } else {
                        currentHowl.play();
                        updateIcons(true);
                    }
                    return;
                }

                buildPlaylist();
                const newIdx = !isNaN(clickedIndex) ? clickedIndex : playlist.findIndex(t => (clickedTrackId && String(t.id) === String(clickedTrackId)) || (clickedSrc && t.src === clickedSrc));
                playTrack(newIdx !== -1 ? newIdx : 0);
            }

            function slugify(text) {
                if (!text) return "";
                return text.toString()
                    .toLowerCase()
                    .trim()
                    .replace(/&/g, "and")
                    .replace(/[^\w\s-]/g, "")
                    .replace(/[\s_-]+/g, "-")
                    .replace(/^-+|-+$/g, "");
            }

            function updatePageSongUrl(track) {
                if (!track) return;
                const currentPath = window.location.pathname;

                // 1. Album page: /album/{album-name}/{song-name}
                const albumContainer = document.getElementById("album-page-container");
                const isAlbumPage = !!albumContainer || currentPath.startsWith("/album");
                if (isAlbumPage) {
                    let albumSlug = albumContainer?.dataset.albumSlug;
                    if (!albumSlug) {
                        if (track.album) {
                            albumSlug = slugify(track.album);
                        } else {
                            const parts = currentPath.split("/").filter(Boolean);
                            if (parts.length >= 2 && parts[0] === "album") {
                                albumSlug = parts[1];
                            }
                        }
                    }
                    const songSlug = slugify(track.title);
                    if (albumSlug && songSlug) {
                        const newUrl = `/album/${albumSlug}/${songSlug}`;
                        if (currentPath !== newUrl) {
                            window.history.pushState({ path: newUrl }, "", newUrl);
                            if (track.title) {
                                document.title = `${track.title} - ${track.album || 'Album'}`;
                            }
                        }
                    }
                    return;
                }

                // 2. Popular songs page: /popular-songs/{song-name}
                const popularContainer = document.getElementById("popular-songs-container");
                const isPopularPage = !!popularContainer || currentPath.startsWith("/popular-songs");
                if (isPopularPage) {
                    const songSlug = slugify(track.title);
                    if (songSlug) {
                        const newUrl = `/popular-songs/${songSlug}`;
                        if (currentPath !== newUrl) {
                            window.history.pushState({ path: newUrl }, "", newUrl);
                            if (track.title) {
                                document.title = `${track.title} - Popular Songs`;
                            }
                        }
                    }
                    return;
                }
            }

            function updateAlbumSongUrl(track) {
                updatePageSongUrl(track);
            }

            let hasAutoPlayedAlbumSong = false;
            function autoPlayActiveAlbumSong() {
                const albumContainer = document.getElementById("album-page-container");
                if (!albumContainer) return;

                const albumSlug = albumContainer.dataset.albumSlug;
                const activeSongSlug = albumContainer.dataset.activeSongSlug;
                const activeSongId = albumContainer.dataset.activeSongId;

                // Normalize /album/{id} to /album/{albumSlug}
                const path = window.location.pathname;
                if (path.match(/^\/album\/\d+\/?$/) && albumSlug) {
                    window.history.replaceState({ path: `/album/${albumSlug}` }, "", `/album/${albumSlug}`);
                }

                if ((activeSongSlug || activeSongId) && !hasAutoPlayedAlbumSong) {
                    hasAutoPlayedAlbumSong = true;
                    setTimeout(() => {
                        let targetBtn = null;
                        if (activeSongId) {
                            targetBtn = document.querySelector(`.play-track[data-track-id="${activeSongId}"]`);
                        }
                        if (!targetBtn && activeSongSlug) {
                            const allPlayButtons = document.querySelectorAll(".play-track");
                            for (let btn of allPlayButtons) {
                                if (slugify(btn.dataset.title) === activeSongSlug) {
                                    targetBtn = btn;
                                    break;
                                }
                            }
                        }
                        if (targetBtn) {
                            const isSingle = albumContainer.dataset.isSingle === "1";
                            const songId = targetBtn.dataset.trackId || targetBtn.dataset.songId || activeSongId;
                            if (isSingle && songId) {
                                $.get(`{{ url('/songs') }}/${songId}/single/all`, (data) => {
                                    if (data && data.html) {
                                        $("#songs-list").html(data.html);
                                        $("#mobile-songs").html(data.html);
                                    }
                                    buildPlaylist();
                                    const idx = playlist.findIndex(t => String(t.id) === String(songId));
                                    playTrack(idx !== -1 ? idx : 0);
                                });
                                return;
                            }
                            const idx = parseInt(targetBtn.dataset.index);
                            if (!isNaN(idx)) {
                                playTrack(idx);
                            } else {
                                targetBtn.click();
                            }
                        }
                    }, 350);
                }
            }

            let hasAutoPlayedPopularSong = false;
            function autoPlayActivePopularSong() {
                const popularContainer = document.getElementById("popular-songs-container");
                if (!popularContainer) return;

                const activeSongSlug = popularContainer.dataset.activeSongSlug;
                const activeSongId = popularContainer.dataset.activeSongId;

                if ((activeSongSlug || activeSongId) && !hasAutoPlayedPopularSong) {
                    hasAutoPlayedPopularSong = true;
                    setTimeout(() => {
                        let targetBtn = null;
                        if (activeSongId) {
                            targetBtn = document.querySelector(`.play-track[data-track-id="${activeSongId}"]`);
                        }
                        if (!targetBtn && activeSongSlug) {
                            const allPlayButtons = document.querySelectorAll(".play-track");
                            for (let btn of allPlayButtons) {
                                if (slugify(btn.dataset.title) === activeSongSlug) {
                                    targetBtn = btn;
                                    break;
                                }
                            }
                        }
                        if (targetBtn) {
                            const isSingle = popularContainer.dataset.isSingle === "1";
                            const songId = targetBtn.dataset.trackId || targetBtn.dataset.songId || activeSongId;
                            if (isSingle && songId) {
                                $.get(`{{ url('/songs') }}/${songId}/single/all`, (data) => {
                                    if (data && data.html) {
                                        $("#songs-list").html(data.html);
                                        $("#mobile-songs").html(data.html);
                                    }
                                    buildPlaylist();
                                    const idx = playlist.findIndex(t => String(t.id) === String(songId));
                                    playTrack(idx !== -1 ? idx : 0);
                                });
                                return;
                            }
                            const idx = parseInt(targetBtn.dataset.index);
                            if (!isNaN(idx)) {
                                playTrack(idx);
                            } else {
                                targetBtn.click();
                            }
                        }
                    }, 350);
                }
            }

            // ─── Remove isTransitioning completely, use generation counter ───
            let playGeneration = 0; // increments each time a new track loads

            function playTrack(index) {
                const track = playlist[index];
                if (!track) return;

                // Same track toggle — instant, no guard needed
                const isSameTrack = (
                    (index === currentTrackIndex) ||
                    (currentPlayingTrackId && track.id && String(currentPlayingTrackId) === String(track.id)) ||
                    (currentPlayingTrackSrc && track.src && currentPlayingTrackSrc === track.src)
                );

                if (isSameTrack && currentHowl) {
                    if (currentHowl.playing()) {
                        currentHowl.pause();
                        if (currentHowl._sounds) {
                            currentHowl._sounds.forEach(s => {
                                if (s._node && typeof s._node.pause === 'function') s._node.pause();
                            });
                        }
                        updateIcons(false);
                    } else {
                        currentHowl.play();
                        updateAlbumSongUrl(track);
                        updateIcons(true);
                    }
                    return;
                }

                // ✅ Increment generation — all previous Howl callbacks become stale
                const myGeneration = ++playGeneration;

                // ✅ Kill current howl cleanly
                if (currentHowl) {
                    currentHowl.off(); // remove all listeners first
                    currentHowl.stop();
                    if (currentHowl._sounds) {
                        currentHowl._sounds.forEach(s => {
                            if (s._node && typeof s._node.pause === 'function') {
                                s._node.pause();
                                s._node.currentTime = 0;
                            }
                        });
                    }
                    currentHowl.unload();
                    currentHowl = null;
                }

                currentTrackIndex = index;
                currentPlayingTrackId = track.id || track.button?.dataset?.trackId || $(track.button).data('track-id');
                currentPlayingTrackSrc = track.src;
                if (currentPlayingTrackId) {
                    localStorage.setItem("playingTrackId", currentPlayingTrackId);
                }
                hasRepeatedOnce = false;
                resetAllPlayButtons();
                updateAlbumSongUrl(track);
                currentPlayingButton = track.button;
                currentPlayingButton?.classList.add("playing");

                const icon = currentPlayingButton?.querySelector(".thumbnail-overlay i");
                if (icon) {
                    icon.classList.remove("fa-play");
                    icon.classList.add("fa-pause");
                }

                const overlay = currentPlayingButton?.querySelector(".thumbnail-overlay");
                if (overlay) overlay.style.opacity = "1";

                showTrackLoader();
                updateFooter(track, false);

                const cachedHowl = preloadCache[index];
                delete preloadCache[index];

                currentHowl = cachedHowl || new Howl({
                    src: [track.src],
                    html5: true,
                    volume: parseFloat(volumeSlider.value),
                });

                currentHowl.volume(parseFloat(volumeSlider.value));

                currentHowl.once("load", () => {
                    if (myGeneration !== playGeneration) return; // ✅ stale, ignore
                    totalDurationSpan.textContent = formatTime(currentHowl.duration() * 1000);
                });

                currentHowl.on("play", () => {
                    if (myGeneration !== playGeneration) return;
                    hideTrackLoader();
                    updateIcons(true);
                    requestAnimationFrame(updateSeek);
                    preloadTrack(index + 1);
                    preloadTrack(index - 1);

                    // ✅ Save playing state to localStorage
                    localStorage.setItem("playingTrack", JSON.stringify({
                        src: track.src,
                        title: track.title,
                        artist: track.artist,
                        album: track.album,
                        plays: track.plays,
                        thumbnail: track.thumbnail,
                        duration: track.duration,
                        trackId: currentPlayingTrackId,
                    }));
                });

                currentHowl.on("pause", () => {
                    if (myGeneration !== playGeneration) return;
                    updateIcons(false);
                });

                currentHowl.on("stop", () => {
                    if (myGeneration !== playGeneration) return;
                    updateIcons(false);
                });

                currentHowl.on("end", () => {
                    if (myGeneration !== playGeneration) return;
                    console.log("Song ended. Index:", index, "Playlist length:", playlist.length, "Repeat mode:", repeatMode);

                    if (repeatMode === "continuous") {
                        // Repeat song continuously
                        currentHowl.seek(0);
                        currentHowl.play();
                        updateIcons(true);
                        const track_id = track.button?.dataset.trackId;
                        if (track_id) {
                            fetch(`{{ url('/music') }}/${track_id}/play`, {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                        }
                    } else if (repeatMode === "once") {
                        // Repeat song one time only
                        if (!hasRepeatedOnce) {
                            hasRepeatedOnce = true;
                            currentHowl.seek(0);
                            currentHowl.play();
                            updateIcons(true);
                            const track_id = track.button?.dataset.trackId;
                            if (track_id) {
                                fetch(`{{ url('/music') }}/${track_id}/play`, {
                                    method: "POST",
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });
                            }
                        } else {
                            // Already repeated 1 time: reset repeat mode to off and continue to next song
                            hasRepeatedOnce = false;
                            setRepeatMode("off");
                            if (isShuffle) {
                                const nextIndex = getNextTrackIndex();
                                if (nextIndex !== -1) playTrack(nextIndex);
                            } else if (currentTrackIndex < playlist.length - 1) {
                                playTrack(currentTrackIndex + 1);
                            } else {
                                updateIcons(false);
                                if (footerPlayPauseIcon) {
                                    footerPlayPauseIcon.classList.remove("fa-pause");
                                    footerPlayPauseIcon.classList.add("fa-play");
                                }
                                currentHowl.seek(0);
                                musicSeekBar.value = 0;
                                updateBarFill(musicSeekBar, 0);
                                currentTimeSpan.textContent = "00:00";
                            }
                        }
                    } else {
                        // repeatMode === 'off'
                        if (isShuffle) {
                            const nextIndex = getNextTrackIndex();
                            if (nextIndex !== -1) {
                                playTrack(nextIndex);
                            }
                        } else if (currentTrackIndex < playlist.length - 1) {
                            playTrack(currentTrackIndex + 1);
                        } else {
                            updateIcons(false);
                            if (footerPlayPauseIcon) {
                                footerPlayPauseIcon.classList.remove("fa-pause");
                                footerPlayPauseIcon.classList.add("fa-play");
                            }
                            currentHowl.seek(0);
                            musicSeekBar.value = 0;
                            updateBarFill(musicSeekBar, 0);
                            currentTimeSpan.textContent = "00:00";
                        }
                    }
                });

                currentHowl.on("loaderror", () => {
                    if (myGeneration !== playGeneration) return; // ✅ stale, ignore
                    hideTrackLoader();
                    resetPlayer();
                });

                currentHowl.play();

                const track_id = track.button?.dataset.trackId;
                if (track_id) {
                    fetch(`{{ url('/music') }}/${track_id}/play`, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                }
            }

            function updateSeek() {
                if (!currentHowl) return;

                if (!isDraggingSeekBar) {
                    const seek = currentHowl.seek();
                    if (typeof seek === "number" && currentHowl.duration()) {
                        const pct = (seek / currentHowl.duration()) * 100;
                        musicSeekBar.value = pct;
                        updateBarFill(musicSeekBar, pct);
                        currentTimeSpan.textContent = formatTime(seek * 1000);
                    }
                }

                // ✅ Keep loop alive as long as a song is loaded
                if (currentHowl) {
                    requestAnimationFrame(updateSeek);
                }
            }

            function updateFooter(track, isPlaying) {
                const wrapper = document.getElementById("player-thumb-wrapper");
                if (track.thumbnail && track.thumbnail !== "") {
                    // SHOW image
                    playerThumbnail.src = track.thumbnail;
                    playerThumbnail.style.display = "block";
                    // CLEAN letter avatar but KEEP the image
                    wrapper.style.background = "transparent";
                    wrapper.innerHTML = ""; // clear letter
                    wrapper.appendChild(playerThumbnail); // re-attach image if needed
                } else {
                    // HIDE image
                    playerThumbnail.style.display = "none";
                    // SHOW letter avatar
                    const letter = track.title?.charAt(0).toUpperCase() || "?";
                    wrapper.innerHTML = letter;
                    wrapper.style.background = "linear-gradient(to right, #4f46e5, #9333ea)";
                    wrapper.style.color = "#fff";
                    wrapper.style.fontWeight = "bold";
                    wrapper.style.fontSize = "20px";
                    wrapper.style.display = "flex";
                    wrapper.style.alignItems = "center";
                    wrapper.style.justifyContent = "center";
                }
                console.log(track);
                playerTitle.textContent = capitalizeFirst(track.title);
                playerArtist.textContent = track.artist;
                playerAlbum.value = track.album ? capitalizeFirst(track.album) : "—";
                playerPlays.value = track.plays ? track.plays : "0";
                totalDurationSpan.textContent = formatTime(track.duration);
                updateIcons(isPlaying);

                // Sync footer Like, Wishlist, and Share buttons
                const trackId = track.id || track.button?.dataset.trackId || $(track.button).data('track-id');
                if (trackId) {
                    $('#footer-like-btn').attr('data-song-id', trackId).data('song-id', trackId);
                    $('#footer-wishlist-btn').attr('data-song-id', trackId).data('song-id', trackId);
                    $('#footer-share-btn')
                        .attr('data-id', trackId).data('id', trackId)
                        .attr('data-title', track.title || '').data('title', track.title || '')
                        .attr('data-album-name', track.album || '').data('album-name', track.album || '')
                        .attr('data-album-id', track.button?.dataset.albumId || '').data('album-id', track.button?.dataset.albumId || '');

                    // Fast DOM sync
                    const existingLike = document.querySelector(`.song-like-btn[data-song-id="${trackId}"]:not(#footer-like-btn) i.fa-solid`);
                    const existingWishlist = document.querySelector(`.song-wishlist[data-song-id="${trackId}"]:not(#footer-wishlist-btn) i.fa-solid`);

                    if (existingLike) {
                        $('#footer-like-btn').find('i').attr('class', 'fa-solid text-theme fa-thumbs-up text-sm pointer-events-none');
                    } else {
                        $('#footer-like-btn').find('i').attr('class', 'fa-regular fa-thumbs-up text-sm pointer-events-none text-gray-400');
                    }

                    if (existingWishlist) {
                        $('#footer-wishlist-btn').find('i').attr('class', 'fa-solid text-red-500 fa-heart text-sm pointer-events-none');
                    } else {
                        $('#footer-wishlist-btn').find('i').attr('class', 'fa-regular fa-heart text-sm pointer-events-none text-gray-400');
                    }

                    // Query server for status
                    $.get(`{{ url('/song') }}/${trackId}/status`, function(res) {
                        if (res && res.is_liked !== undefined) {
                            if (res.is_liked) {
                                $('#footer-like-btn').find('i').attr('class', 'fa-solid text-theme fa-thumbs-up text-sm pointer-events-none');
                            } else {
                                $('#footer-like-btn').find('i').attr('class', 'fa-regular fa-thumbs-up text-sm pointer-events-none text-gray-400');
                            }
                            if (res.is_wishlisted) {
                                $('#footer-wishlist-btn').find('i').attr('class', 'fa-solid text-red-500 fa-heart text-sm pointer-events-none');
                            } else {
                                $('#footer-wishlist-btn').find('i').attr('class', 'fa-regular fa-heart text-sm pointer-events-none text-gray-400');
                            }
                        }
                    });
                }
            }

            function capitalizeFirst(text) {
                return text ? text.charAt(0).toUpperCase() + text.slice(1) : "";
            }

            function updateIcons(isPlaying) {
                const track = (currentTrackIndex >= 0 && playlist[currentTrackIndex]) ? playlist[currentTrackIndex] : null;
                const trackId = track?.id || track?.button?.dataset?.trackId || currentPlayingTrackId;
                syncAllButtons(trackId, isPlaying);
            }

            function updateVolumeIcon(vol) {
                const icon = document.querySelector(".fa-volume-up, .fa-volume-low, .fa-volume-xmark");
                if (!icon) return;

                icon.className = "fas text-gray-400 text-sm sm:text-lg"; // reset

                if (vol === 0) {
                    icon.classList.add("fa-volume-xmark"); // muted
                } else if (vol < 0.5) {
                    icon.classList.add("fa-volume-low"); // low
                } else {
                    icon.classList.add("fa-volume-up"); // high
                }
            }
            musicSeekBar.addEventListener("input", () => {
                updateBarFill(musicSeekBar, musicSeekBar.value);
                if (currentHowl && currentHowl.duration()) {
                    // ✅ Always preview time while dragging
                    const previewTime = (musicSeekBar.value / 100) * currentHowl.duration();
                    currentTimeSpan.textContent = formatTime(previewTime * 1000);
                }
            });
            // ✅ Keep change listener only for keyboard/click on bar (not drag)
            musicSeekBar.addEventListener("change", () => {
                if (!isDraggingSeekBar) {
                    seekToCurrentBarValue();
                }
            });
            let isDraggingSeekBar = false;

            musicSeekBar.addEventListener("mousedown", () => isDraggingSeekBar = true);
            musicSeekBar.addEventListener("touchstart", () => isDraggingSeekBar = true, {
                passive: true
            });

            musicSeekBar.addEventListener("mouseup", () => {
                isDraggingSeekBar = false;
                seekToCurrentBarValue(); // ✅ directly seek, no dispatchEvent
            });

            musicSeekBar.addEventListener("touchend", () => {
                isDraggingSeekBar = false;
                seekToCurrentBarValue();
            });
            // ✅ Single seek function — no duplicate logic
            function seekToCurrentBarValue() {
                if (!currentHowl || currentHowl.state() !== "loaded") return;

                const newTime = (musicSeekBar.value / 100) * currentHowl.duration();

                currentHowl.seek(newTime);
                currentTimeSpan.textContent = formatTime(newTime * 1000);
                updateBarFill(musicSeekBar, musicSeekBar.value);

                // ✅ Ensure playing after seek
                if (!currentHowl.playing()) {
                    currentHowl.play();
                    updateIcons(true);
                }
            }
            // ✅ ADD these instead — covers mouse + touch + keyboard
            volumeSlider.addEventListener("input", () => {
                const vol = parseFloat(volumeSlider.value);
                if (currentHowl) currentHowl.volume(vol);
                updateBarFill(volumeSlider, vol * 100);
                updateVolumeIcon(vol);
            });

            volumeSlider.addEventListener("change", () => {
                const vol = parseFloat(volumeSlider.value);
                if (currentHowl) currentHowl.volume(vol);
                updateBarFill(volumeSlider, vol * 100);
            });
            footerPlayPauseButton.addEventListener("click", () => {
                if (!currentHowl) return alert("Please select a song first.");
                if (currentHowl.playing()) {
                    currentHowl.pause();
                    if (currentHowl._sounds) {
                        currentHowl._sounds.forEach(s => {
                            if (s._node && typeof s._node.pause === 'function') {
                                s._node.pause();
                            }
                        });
                    }
                    updateIcons(false);
                } else {
                    currentHowl.play();
                    updateIcons(true);
                }
            });
            backwardBtn.addEventListener("click", () => {
                if (!currentHowl) return;

                const currentSeek = currentHowl.seek();

                if (currentSeek > 3) {
                    currentHowl.seek(Math.max(0, currentSeek - 10));
                } else {
                    if (currentTrackIndex > 0) playTrack(currentTrackIndex - 1);
                    else currentHowl.seek(0);
                }
            });

            forwardBtn.addEventListener("click", () => {
                if (!currentHowl) return;
                const nextIndex = getNextTrackIndex();
                if (nextIndex !== -1) playTrack(nextIndex);
            });

            // ─── Shuffle & Repeat State ─────────────────────────────
            let repeatMode = 'off'; // 'off' | 'continuous' | 'once'
            let hasRepeatedOnce = false;
            let isRepeat = false; // backward compatibility
            let isShuffle = false;
            const repeatBtn = document.getElementById("repeat-btn");
            const repeatBadge = document.getElementById("repeat-badge");
            const shuffleBtn = document.getElementById("shuffle-btn");

            function setShuffleState(active) {
                isShuffle = active;
                if (shuffleBtn) {
                    shuffleBtn.classList.toggle("text-gray-400", !isShuffle);
                    shuffleBtn.classList.toggle("text-white", isShuffle);
                    shuffleBtn.style.color = isShuffle ?
                        getComputedStyle(document.documentElement).getPropertyValue("--color-theme").trim() :
                        "";
                }
            }

            function setRepeatMode(mode) {
                repeatMode = mode;
                hasRepeatedOnce = false;
                isRepeat = (repeatMode === 'continuous' || repeatMode === 'once');
                const themeColor = getComputedStyle(document.documentElement).getPropertyValue("--color-theme").trim() || "#FF3A1F";

                if (!repeatBtn) return;

                if (repeatMode === 'off') {
                    repeatBtn.classList.add("text-gray-400");
                    repeatBtn.classList.remove("text-white");
                    repeatBtn.style.color = "";
                    repeatBtn.title = "Repeat: Off";
                    if (repeatBadge) repeatBadge.classList.add("hidden");
                } else if (repeatMode === 'continuous') {
                    repeatBtn.classList.remove("text-gray-400");
                    repeatBtn.classList.add("text-white");
                    repeatBtn.style.color = themeColor;
                    repeatBtn.title = "Repeat: Song Continuously";
                    if (repeatBadge) repeatBadge.classList.add("hidden");
                } else if (repeatMode === 'once') {
                    repeatBtn.classList.remove("text-gray-400");
                    repeatBtn.classList.add("text-white");
                    repeatBtn.style.color = themeColor;
                    repeatBtn.title = "Repeat: Once (1 Time Only)";
                    if (repeatBadge) repeatBadge.classList.remove("hidden");
                }
            }

            shuffleBtn?.addEventListener("click", () => {
                setShuffleState(!isShuffle);
            });

            repeatBtn?.addEventListener("click", () => {
                if (repeatMode === 'off') {
                    setRepeatMode('continuous');
                } else if (repeatMode === 'continuous') {
                    setRepeatMode('once');
                } else {
                    setRepeatMode('off');
                }
            });

            repeatBtn?.addEventListener("dblclick", (e) => {
                e.preventDefault();
                setRepeatMode('once');
            });

            function getNextTrackIndex() {
                if (!playlist || playlist.length === 0) return -1;
                if (isShuffle) {
                    if (playlist.length === 1) return 0;
                    let randomIndex;
                    let attempts = 0;
                    do {
                        randomIndex = Math.floor(Math.random() * playlist.length);
                        attempts++;
                    } while (randomIndex === currentTrackIndex && attempts < 20);
                    return randomIndex;
                }
                return currentTrackIndex < playlist.length - 1 ? currentTrackIndex + 1 : 0;
            }
            // $(document).on("click", ".album-play-card", function(e) {
            //     // ❌ If clicked on play / wishlist / share button → do nothing
            //     if ($(e.target).closest(".album-play, .album-wishlist, .ShareBtn").length) {
            //         return;
            //     }
            //     const albumId = $(this).data("album-id");
            //     // Trigger the play button inside this card
            //     $(this).find(".album-play").first().trigger("click");
            // });
            $(document).on("click", ".album-play-card", function(e) {
                // ❌ If clicked on play / wishlist / like / share button → do nothing
                if ($(e.target).closest(".album-play, .album-wishlist, .album-like-btn, .album-all-wishlist, .ShareBtn").length) {
                    return;
                }
                const albumSlug = $(this).data("album-slug");
                const albumId = $(this).data("album-id");
                const target = albumSlug || albumId;
                if (target) {
                    window.location.href = `/album/${target}`;
                }
            });
            $(document).on("click", ".album-play", function(e) {
                e.preventDefault();
                e.stopPropagation(); // prevent album card click
                const albumId = $(this).data("album-id");
                const clickedBtn = this;
                const clickedIcon = $(this).find("i");
                const albumContainer = document.getElementById("album-page-container");
                const currentAlbumId = albumContainer?.dataset?.albumId;

                // ✅ If clicking same album while playing → pause
                const isSameAlbumPlaying = (currentPlayingAlbumBtn === clickedBtn) ||
                    Boolean(currentAlbumId && albumId && String(currentAlbumId) === String(albumId) && currentHowl);

                if (isSameAlbumPlaying && currentHowl) {
                    if (currentHowl.playing()) {
                        currentHowl.pause();
                        if (currentHowl._sounds) {
                            currentHowl._sounds.forEach(s => {
                                if (s._node && typeof s._node.pause === 'function') s._node.pause();
                            });
                        }
                        clickedIcon.removeClass("fa-pause").addClass("fa-play");
                        updateIcons(false);
                    } else {
                        currentHowl.play();
                        clickedIcon.removeClass("fa-play").addClass("fa-pause");
                        updateIcons(true);
                    }
                    return;
                }
                // 🔄 Stop previous album completely
                if (currentHowl) {
                    currentHowl.stop();
                    if (currentHowl._sounds) {
                        currentHowl._sounds.forEach(s => {
                            if (s._node && typeof s._node.pause === 'function') {
                                s._node.pause();
                                s._node.currentTime = 0;
                            }
                        });
                    }
                    currentHowl.unload();
                    currentHowl = null;
                }
                currentTrackIndex = -1;
                currentPlayingButton = null;
                resetAllPlayButtons();
                // Reset old album button
                if (currentPlayingAlbumBtn && currentPlayingAlbumBtn !== clickedBtn) {
                    $(currentPlayingAlbumBtn)
                        .find("i")
                        .removeClass("fa-pause")
                        .addClass("fa-play");
                }
                currentPlayingAlbumBtn = clickedBtn;
                // Show pause icon immediately
                clickedIcon.removeClass("fa-play").addClass("fa-pause");
                // ✅ Detect mobile
                const isMobile = window.innerWidth <= 768;
                if (isMobile) {
                    $("#mobile-songs").html("<p class='text-gray-400'>Loading songs...</p>");
                } else {
                    $("#songs-list").html("<p class='text-gray-400'>Loading songs...</p>");
                }
                const isSingle = $(this).data("is-single") == "1";
                const songId = $(this).data("song-id");
                const songsUrl = (isSingle && songId)
                    ? `{{ url('/songs') }}/${songId}/single/all`
                    : `{{ url('/album') }}/${albumId}/songs`;
                $.get(songsUrl, (data) => {
                    const htmlContent = data.html;
                    if (isMobile) {
                        const $target = $("#mobile-songs");
                        if ($target.length) {
                            $("html, body").animate({
                                scrollTop: $target.offset().top
                            }, 300);
                        }
                        $("#mobile-songs").html(htmlContent);
                    } else {
                        $("#songs-list").html(htmlContent);
                    }
                    buildPlaylist();
                    // autoplay first song
                    setTimeout(() => {
                        const selector = isMobile ? "#mobile-songs .play-track" : "#songs-list .play-track";
                        const firstNewTrack = document.querySelector(selector);
                        if (firstNewTrack) {
                            const index = parseInt(firstNewTrack.dataset.index);
                            if (!isNaN(index)) {
                                playTrack(index);
                                return;
                            }
                        }
                        playTrack(0);
                    }, 150);
                });
            });
            $(document).on("click", ".song-row", function(e) {
                // ❌ If clicked on play / wishlist / like / share button → do nothing
                if ($(e.target).closest(".song-play, .play-track, .table-play-pause-button, .song-wishlist, .song-like-btn, .SongShareBtn").length) {
                    return;
                }
                const albumId = $(this).data("album-id");
                const songId = $(this).data("song-id") || $(this).data("track-id");
                const type = $(this).data("type"); // album or playlist
                const playlist_name = $(this).data("playlist-name") || "";
                const audioSrc = $(this).data("audio-src");

                // Same track toggle — instant play/pause toggle
                const isSameTrack = (currentHowl && (
                    (currentPlayingTrackId && songId && String(currentPlayingTrackId) === String(songId)) ||
                    (currentPlayingTrackSrc && audioSrc && currentPlayingTrackSrc === audioSrc) ||
                    (playlist[currentTrackIndex] && (
                        (playlist[currentTrackIndex].id && songId && String(playlist[currentTrackIndex].id) === String(songId)) ||
                        (playlist[currentTrackIndex].src === audioSrc)
                    ))
                ));

                if (isSameTrack) {
                    if (currentHowl.playing()) {
                        currentHowl.pause();
                        if (currentHowl._sounds) {
                            currentHowl._sounds.forEach(s => {
                                if (s._node && typeof s._node.pause === 'function') s._node.pause();
                            });
                        }
                        updateIcons(false);
                    } else {
                        currentHowl.play();
                        updateIcons(true);
                    }
                    return;
                }

                // If clicked song exists in current playlist, play directly!
                const playlistIdx = playlist.findIndex(t => (songId && String(t.id) === String(songId)) || (audioSrc && t.src === audioSrc));
                if (playlistIdx !== -1) {
                    playTrack(playlistIdx);
                    return;
                }

                // ✅ Stop previous song & reset player when switching album
                if (currentHowl) {
                    currentHowl.stop();
                    if (currentHowl._sounds) {
                        currentHowl._sounds.forEach(s => {
                            if (s._node && typeof s._node.pause === 'function') {
                                s._node.pause();
                                s._node.currentTime = 0;
                            }
                        });
                    }
                    currentHowl.unload();
                    currentHowl = null;
                }
                currentTrackIndex = -1; // reset index
                currentPlayingButton = null; // reset previous button
                resetAllPlayButtons(); // reset all buttons visually
                if (currentPlayingAlbumBtn && currentPlayingAlbumBtn !== this) {
                    // Reset old album button to play
                    $(currentPlayingAlbumBtn).find("i").removeClass("fa-pause").addClass("fa-play");
                }
                // currentPlayingAlbumBtn = this; 
                // set new album button
                // ✅ Detect mobile view
                const isMobile = window.innerWidth <= 768;
                if (isMobile) {
                    $("#mobile-songs").html("<p class='text-gray-400'>Loading songs...</p>");
                } else {
                    $("#songs-list").html("<p class='text-gray-400'>Loading songs...</p>");
                }
                $.get(`{{ url('/songs') }}/${songId}/${type}/${playlist_name}`, (data) => {
                    const htmlContent = data.html; // use the HTML part
                    if (isMobile) {
                        const $target = $("#mobile-songs");
                        if ($target.length) {
                            $("html, body").animate({
                                scrollTop: $target.offset().top
                            }, 300);
                        }
                        $("#mobile-songs").html(htmlContent);
                    } else {
                        $("#songs-list").html(htmlContent);
                    }
                    buildPlaylist(); // rebuild playlist
                    setTimeout(() => {
                        const target = document.querySelector(
                            `.play-track[data-track-id="${songId}"]`);
                        target?.click();
                    }, 100);
                });
            });
            $(document).on("click", ".song-play, .play-track.table-play-pause-button, button.play-track", function(e) {
                e.preventDefault();
                e.stopPropagation();
                const albumId = $(this).data("album-id");
                const songId = $(this).data("song-id") || $(this).data("track-id");
                const type = $(this).data("type") || "album";
                const playlist_name = $(this).data("playlist-name") || "";
                const audioSrc = $(this).data("audio-src");

                // Same track toggle — instant play/pause toggle
                const isSameTrack = (currentHowl && (
                    (currentPlayingTrackId && songId && String(currentPlayingTrackId) === String(songId)) ||
                    (currentPlayingTrackSrc && audioSrc && currentPlayingTrackSrc === audioSrc) ||
                    (playlist[currentTrackIndex] && (
                        (playlist[currentTrackIndex].id && songId && String(playlist[currentTrackIndex].id) === String(songId)) ||
                        (playlist[currentTrackIndex].src === audioSrc)
                    ))
                ));

                if (isSameTrack) {
                    if (currentHowl.playing()) {
                        currentHowl.pause();
                        if (currentHowl._sounds) {
                            currentHowl._sounds.forEach(s => {
                                if (s._node && typeof s._node.pause === 'function') s._node.pause();
                            });
                        }
                        updateIcons(false);
                    } else {
                        currentHowl.play();
                        updateIcons(true);
                    }
                    return;
                }

                // If clicked song exists in current playlist, play directly!
                const playlistIdx = playlist.findIndex(t => (songId && String(t.id) === String(songId)) || (audioSrc && t.src === audioSrc));
                if (playlistIdx !== -1) {
                    playTrack(playlistIdx);
                    return;
                }

                // ✅ Stop previous song & reset player when switching album
                if (currentHowl) {
                    currentHowl.stop();
                    if (currentHowl._sounds) {
                        currentHowl._sounds.forEach(s => {
                            if (s._node && typeof s._node.pause === 'function') {
                                s._node.pause();
                                s._node.currentTime = 0;
                            }
                        });
                    }
                    currentHowl.unload();
                    currentHowl = null;
                }
                currentTrackIndex = -1; // reset index
                currentPlayingButton = null; // reset previous button
                resetAllPlayButtons(); // reset all buttons visually
                if (currentPlayingAlbumBtn && currentPlayingAlbumBtn !== this) {
                    // Reset old album button to play
                    $(currentPlayingAlbumBtn).find("i").removeClass("fa-pause").addClass("fa-play");
                }
                // currentPlayingAlbumBtn = this; 
                // set new album button
                // ✅ Detect mobile view
                const isMobile = window.innerWidth <= 768;
                if (isMobile) {
                    $("#mobile-songs").html("<p class='text-gray-400'>Loading songs...</p>");
                } else {
                    $("#songs-list").html("<p class='text-gray-400'>Loading songs...</p>");
                }
                $.get(`{{ url('/songs') }}/${songId}/${type}/${playlist_name}`, (data) => {
                    const htmlContent = data.html; // use the HTML part
                    if (isMobile) {
                        const $target = $("#mobile-songs");
                        if ($target.length) {
                            $("html, body").animate({
                                scrollTop: $target.offset().top
                            }, 300);
                        }
                        $("#mobile-songs").html(htmlContent);
                    } else {
                        $("#songs-list").html(htmlContent);
                    }
                    buildPlaylist(); // rebuild playlist
                    setTimeout(() => {
                        const target = document.querySelector(
                            `.play-track[data-track-id="${songId}"]`);
                        target?.click();
                    }, 100);
                });
            });
            $(document).on("click", ".playlist-play", function(e) {
                e.preventDefault();
                e.stopPropagation();
                const playlistName = $(this).data("playlist");
                const clickedBtn = this;
                const clickedIcon = $(this).find("i");
                // If clicking same playlist while playing → pause
                if (currentPlayingAlbumBtn === clickedBtn && currentHowl) {
                    if (currentHowl.playing()) {
                        currentHowl.pause();
                        if (currentHowl._sounds) {
                            currentHowl._sounds.forEach(s => {
                                if (s._node && typeof s._node.pause === 'function') s._node.pause();
                            });
                        }
                        clickedIcon.removeClass("fa-pause").addClass("fa-play");
                    } else {
                        currentHowl.play();
                        clickedIcon.removeClass("fa-play").addClass("fa-pause");
                    }
                    updateIcons(currentHowl.playing());
                    return;
                }
                // Stop previous playlist
                if (currentHowl) {
                    currentHowl.stop();
                    if (currentHowl._sounds) {
                        currentHowl._sounds.forEach(s => {
                            if (s._node && typeof s._node.pause === 'function') {
                                s._node.pause();
                                s._node.currentTime = 0;
                            }
                        });
                    }
                    currentHowl.unload();
                    currentHowl = null;
                }
                currentTrackIndex = -1;
                currentPlayingButton = null;
                resetAllPlayButtons();
                if (currentPlayingAlbumBtn && currentPlayingAlbumBtn !== clickedBtn) {
                    $(currentPlayingAlbumBtn)
                        .find("i")
                        .removeClass("fa-pause")
                        .addClass("fa-play");
                }
                currentPlayingAlbumBtn = clickedBtn;
                clickedIcon.removeClass("fa-play").addClass("fa-pause");
                const isMobile = window.innerWidth <= 768;
                if (isMobile) {
                    $("#mobile-songs").html("<p class='text-gray-400'>Loading songs...</p>");
                } else {
                    $("#songs-list").html("<p class='text-gray-400'>Loading songs...</p>");
                }
                // Call playlist API
                const userId = $(this).data("user-id");
                const songsUrl = userId ? `{{ url('/playlist') }}/${encodeURIComponent(playlistName)}/songs/${userId}` : `{{ url('/playlist') }}/${encodeURIComponent(playlistName)}/songs`;
                $.get(songsUrl, function(data) {
                    const htmlContent = data.html;
                    if (isMobile) {
                        const $target = $("#mobile-songs");
                        if ($target.length) {
                            $("html, body").animate({
                                scrollTop: $target.offset().top
                            }, 300);
                        }
                        $("#mobile-songs").html(htmlContent);
                    } else {
                        $("#songs-list").html(htmlContent);
                    }
                    buildPlaylist();
                    // autoplay first song
                    setTimeout(() => {
                        const selector = isMobile ? "#mobile-songs .play-track" : "#songs-list .play-track";
                        const firstNewTrack = document.querySelector(selector);
                        if (firstNewTrack) {
                            const index = parseInt(firstNewTrack.dataset.index);
                            if (!isNaN(index)) {
                                playTrack(index);
                                return;
                            }
                        }
                        playTrack(0);
                    }, 150);
                }).fail(function(xhr, status, error) {
                    alert("❌ Failed to load playlist songs!\nURL: /playlist/" + playlistName + "/songs\nError: " + error + "\nStatus: " + status);
                });
            });
            $(document).on("click", ".wishlist-songs-play", function(e) {
                e.preventDefault();
                e.stopPropagation();
                const playlistName = $(this).data("playlist");
                const clickedBtn = this;
                const clickedIcon = $(this).find("i");
                // If clicking same playlist while playing → pause
                if (currentPlayingAlbumBtn === clickedBtn && currentHowl) {
                    if (currentHowl.playing()) {
                        currentHowl.pause();
                        if (currentHowl._sounds) {
                            currentHowl._sounds.forEach(s => {
                                if (s._node && typeof s._node.pause === 'function') s._node.pause();
                            });
                        }
                        clickedIcon.removeClass("fa-pause").addClass("fa-play");
                    } else {
                        currentHowl.play();
                        clickedIcon.removeClass("fa-play").addClass("fa-pause");
                    }
                    updateIcons(currentHowl.playing());
                    return;
                }
                // Stop previous playlist
                if (currentHowl) {
                    currentHowl.stop();
                    if (currentHowl._sounds) {
                        currentHowl._sounds.forEach(s => {
                            if (s._node && typeof s._node.pause === 'function') {
                                s._node.pause();
                                s._node.currentTime = 0;
                            }
                        });
                    }
                    currentHowl.unload();
                    currentHowl = null;
                }
                currentTrackIndex = -1;
                currentPlayingButton = null;
                resetAllPlayButtons();
                if (currentPlayingAlbumBtn && currentPlayingAlbumBtn !== clickedBtn) {
                    $(currentPlayingAlbumBtn)
                        .find("i")
                        .removeClass("fa-pause")
                        .addClass("fa-play");
                }
                currentPlayingAlbumBtn = clickedBtn;
                clickedIcon.removeClass("fa-play").addClass("fa-pause");
                const isMobile = window.innerWidth <= 768;
                if (isMobile) {
                    $("#mobile-songs").html("<p class='text-gray-400'>Loading songs...</p>");
                } else {
                    $("#songs-list").html("<p class='text-gray-400'>Loading songs...</p>");
                }
                // Call playlist API
                const targetUserId = $(this).data("user-id");
                const wishlistApiUrl = targetUserId ? `{{ url('/wishlist/all-songs') }}/${targetUserId}` : `{{ url('/wishlist/all-songs') }}`;
                $.get(wishlistApiUrl, function(data) {
                    const htmlContent = data.html;
                    if (isMobile) {
                        const $target = $("#mobile-songs");
                        if ($target.length) {
                            $("html, body").animate({
                                scrollTop: $target.offset().top
                            }, 300);
                        }
                        $("#mobile-songs").html(htmlContent);
                    } else {
                        $("#songs-list").html(htmlContent);
                    }
                    buildPlaylist();
                    // autoplay first song
                    setTimeout(() => {
                        const selector = isMobile ? "#mobile-songs .play-track" : "#songs-list .play-track";
                        const firstNewTrack = document.querySelector(selector);
                        if (firstNewTrack) {
                            const index = parseInt(firstNewTrack.dataset.index);
                            if (!isNaN(index)) {
                                playTrack(index);
                                return;
                            }
                        }
                        playTrack(0);
                    }, 150);
                }).fail(function(xhr, status, error) {
                    alert("❌ Failed to load wishlist songs!\nURL: /wishlist/all-songs\nError: " + error + "\nStatus: " + status + "\nResponse: " + xhr.responseText);
                });
            });

            $(document).on("click", ".wishlist-songs-shuffle", function(e) {
                e.preventDefault();
                e.stopPropagation();
                setShuffleState(true);

                const isMobile = window.innerWidth <= 768;
                const targetUserId = $(this).data("user-id") || $(".wishlist-songs-play").data("user-id") || "";
                const shuffleUrl = targetUserId ? `{{ url('/wishlist/shuffle') }}/${targetUserId}` : `{{ url('/wishlist/shuffle') }}`;

                if (isMobile) {
                    $("#mobile-songs").html("<p class='text-gray-400'>Shuffling songs...</p>");
                } else {
                    $("#songs-list").html("<p class='text-gray-400'>Shuffling songs...</p>");
                }

                $.get(shuffleUrl, function(res) {
                    if (res.status) {
                        $("#songs-table-body").html(res.desktop);
                        $("#songs-card-body").html(res.mobile);
                        if (res.html) {
                            if (isMobile) {
                                $("#mobile-songs").html(res.html);
                            } else {
                                $("#songs-list").html(res.html);
                            }
                        }
                        buildPlaylist();
                        setTimeout(() => {
                            const selector = isMobile ? "#mobile-songs .play-track" : "#songs-list .play-track";
                            const firstNewTrack = document.querySelector(selector);
                            if (firstNewTrack) {
                                const index = parseInt(firstNewTrack.dataset.index);
                                if (!isNaN(index)) {
                                    playTrack(index);
                                    return;
                                }
                            }
                            if (playlist.length > 0) {
                                playTrack(0);
                            }
                        }, 150);
                    }
                }).fail(function() {
                    buildPlaylist();
                    if (playlist.length > 0) {
                        const randomIndex = Math.floor(Math.random() * playlist.length);
                        playTrack(randomIndex);
                    } else {
                        $(".wishlist-songs-play").trigger("click");
                    }
                });
            });

            $(document).on("click", ".liked-songs-play", function(e) {
                e.preventDefault();
                e.stopPropagation();
                const clickedBtn = this;
                const clickedIcon = $(this).find("i");
                // If clicking same button while playing → pause/play
                if (currentPlayingAlbumBtn === clickedBtn && currentHowl) {
                    if (currentHowl.playing()) {
                        currentHowl.pause();
                        if (currentHowl._sounds) {
                            currentHowl._sounds.forEach(s => {
                                if (s._node && typeof s._node.pause === 'function') s._node.pause();
                            });
                        }
                        clickedIcon.removeClass("fa-pause").addClass("fa-play");
                    } else {
                        currentHowl.play();
                        clickedIcon.removeClass("fa-play").addClass("fa-pause");
                    }
                    updateIcons(currentHowl.playing());
                    return;
                }
                if (currentHowl) {
                    currentHowl.stop();
                    if (currentHowl._sounds) {
                        currentHowl._sounds.forEach(s => {
                            if (s._node && typeof s._node.pause === 'function') {
                                s._node.pause();
                                s._node.currentTime = 0;
                            }
                        });
                    }
                    currentHowl.unload();
                    currentHowl = null;
                }
                currentTrackIndex = -1;
                currentPlayingButton = null;
                resetAllPlayButtons();
                if (currentPlayingAlbumBtn && currentPlayingAlbumBtn !== clickedBtn) {
                    $(currentPlayingAlbumBtn).find("i").removeClass("fa-pause").addClass("fa-play");
                }
                currentPlayingAlbumBtn = clickedBtn;
                clickedIcon.removeClass("fa-play").addClass("fa-pause");

                const isMobile = window.innerWidth <= 768;
                if (isMobile) {
                    $("#mobile-songs").html("<p class='text-gray-400'>Loading songs...</p>");
                } else {
                    $("#songs-list").html("<p class='text-gray-400'>Loading songs...</p>");
                }

                const targetUserId = $(this).data("user-id");
                const likedApiUrl = targetUserId ? `{{ url('/liked/all-songs') }}/${targetUserId}` : `{{ url('/liked/all-songs') }}`;
                $.get(likedApiUrl, function(data) {
                    const htmlContent = data.html;
                    if (isMobile) {
                        const $target = $("#mobile-songs");
                        if ($target.length) {
                            $("html, body").animate({ scrollTop: $target.offset().top }, 300);
                        }
                        $("#mobile-songs").html(htmlContent);
                    } else {
                        $("#songs-list").html(htmlContent);
                    }
                    buildPlaylist();
                    setTimeout(() => {
                        const selector = isMobile ? "#mobile-songs .play-track" : "#songs-list .play-track";
                        const firstNewTrack = document.querySelector(selector);
                        if (firstNewTrack) {
                            const index = parseInt(firstNewTrack.dataset.index);
                            if (!isNaN(index)) {
                                playTrack(index);
                                return;
                            }
                        }
                        playTrack(0);
                    }, 150);
                }).fail(function() {
                    buildPlaylist();
                    if (playlist.length > 0) playTrack(0);
                });
            });

            $(document).on("click", ".liked-songs-shuffle", function(e) {
                e.preventDefault();
                e.stopPropagation();
                setShuffleState(true);

                const isMobile = window.innerWidth <= 768;
                const targetUserId = $(this).data("user-id") || $(".liked-songs-play").data("user-id") || "";
                const shuffleUrl = targetUserId ? `{{ url('/liked/shuffle') }}/${targetUserId}` : `{{ url('/liked/shuffle') }}`;

                if (isMobile) {
                    $("#mobile-songs").html("<p class='text-gray-400'>Shuffling songs...</p>");
                } else {
                    $("#songs-list").html("<p class='text-gray-400'>Shuffling songs...</p>");
                }

                $.get(shuffleUrl, function(res) {
                    if (res.status) {
                        $("#songs-table-body").html(res.desktop);
                        $("#songs-card-body").html(res.mobile);
                        if (res.html) {
                            if (isMobile) {
                                $("#mobile-songs").html(res.html);
                            } else {
                                $("#songs-list").html(res.html);
                            }
                        }
                        buildPlaylist();
                        setTimeout(() => {
                            const selector = isMobile ? "#mobile-songs .play-track" : "#songs-list .play-track";
                            const firstNewTrack = document.querySelector(selector);
                            if (firstNewTrack) {
                                const index = parseInt(firstNewTrack.dataset.index);
                                if (!isNaN(index)) {
                                    playTrack(index);
                                    return;
                                }
                            }
                            if (playlist.length > 0) {
                                playTrack(0);
                            }
                        }, 150);
                    }
                }).fail(function() {
                    buildPlaylist();
                    if (playlist.length > 0) {
                        const randomIndex = Math.floor(Math.random() * playlist.length);
                        playTrack(randomIndex);
                    }
                });
            });

            buildPlaylist();
            autoPlayActiveAlbumSong();
            autoPlayActivePopularSong();
            updateBarFill(volumeSlider, volumeSlider.value * 100);
            updateVolumeIcon(parseFloat(volumeSlider.value));

            setInterval(() => {
                if (currentHowl && currentHowl.playing()) {
                    localStorage.setItem("playingSeek", currentHowl.seek());
                }
            }, 1000);

            const observer = new MutationObserver(() => {
                // ✅ Don't rebuild playlist while a song is actively playing
                // if (currentHowl && currentHowl.playing()) return;
                buildPlaylist();
                autoPlayActiveAlbumSong();
                autoPlayActivePopularSong();
            });
            observer.observe(document.getElementById("main-content"), {
                childList: true,
                subtree: true
            });

            const savedTrack = localStorage.getItem("playingTrack");
            const savedSeek = parseFloat(localStorage.getItem("playingSeek") || "0");

            if (savedTrack) {
                try {
                    const track = JSON.parse(savedTrack);
                    updateFooter(track, false);
                    const existingIndex = playlist.findIndex(t => t.src === track.src);
                    if (existingIndex !== -1) {
                        playTrack(existingIndex);
                        setTimeout(() => {
                            if (currentHowl && savedSeek > 0) {
                                currentHowl.seek(savedSeek);
                            }
                        }, 500);
                    } else {
                        const tempTrack = {
                            ...track,
                            button: null
                        };
                        playlist = [tempTrack];
                        currentTrackIndex = 0;
                        currentPlayingTrackId = track.id || track.songId || null;
                        currentPlayingTrackSrc = track.src || null;
                        currentHowl = new Howl({
                            src: [track.src],
                            html5: true,
                            volume: parseFloat(volumeSlider.value),
                        });
                        currentHowl.once("load", () => {
                            totalDurationSpan.textContent = formatTime(currentHowl.duration() * 1000);
                            if (savedSeek > 0) currentHowl.seek(savedSeek);
                        });
                        currentHowl.on("play", () => {
                            hideTrackLoader();
                            updateIcons(true);
                            requestAnimationFrame(updateSeek);
                        });
                        currentHowl.on("pause", () => {
                            updateIcons(false);
                        });
                        currentHowl.on("stop", () => {
                            updateIcons(false);
                        });
                        currentHowl.on("end", () => {
                            localStorage.removeItem("playingTrack");
                            localStorage.removeItem("playingSeek");
                            updateIcons(false);
                        });
                        currentHowl.play();
                        showTrackLoader();
                        updateFooter(track, false);
                    }
                } catch (e) {
                    localStorage.removeItem("playingTrack");
                    localStorage.removeItem("playingSeek");
                }
            }
            $("#searchInput").keyup(function() {
                let search = $(this).val();
                if (search.length < 1) {
                    $("#searchResults").hide();
                    return;
                }
                $.ajax({
                    url: "{{ route('search.music') }}",
                    type: "GET",
                    data: {
                        search: search
                    },
                    success: function(data) {
                        let resultsBox = $("#searchResults");
                        resultsBox.empty();
                        if (data.length === 0) {
                            resultsBox.append(
                                '<p class="p-2 text-gray-400">No results found</p>');
                        } else {
                            data.forEach(song => {
                                resultsBox.append(`
                                <div class="flex items-center justify-between p-2 border-b border-gray-700 hover:bg-gray-700 cursor-pointer">
    
                                    <div class="flex items-center gap-3">
    
                                        <div class="relative w-10 h-10 flex-shrink-0">
                                            ${song.thumbnail_url
                                                        ? `<img src="${song.thumbnail_url}" class="w-10 h-10 rounded object-cover">`
                                                        : `<div class="w-10 h-10 rounded bg-gradient-to-r from-indigo-500 to-purple-500
                                                                                                                                                                                                            flex items-start justify-start pt-1 pl-1 
                                                                                                                                                                                                            uppercase font-bold text-white text-xs leading-none">
                                                                                                                                                                                                                                                        ${song.title.substring(0, 1)}
                                                                                                                                                                                                                                                </div>`
                                                    }
    
                                            <button class="song-play play-track table-play-pause-button absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                                                data-track-id="${song.id}" data-audio-src="${song.music_file}"
                                                 data-album="${song.album_name}" data-plays ="${song.plays}"
                                                data-title="${song.title}" data-artist="${song.artist_id}"
                                                data-thumbnail="${song.thumbnail_url}" data-duration="${song.duration}" 
                                                data-index="0" data-album-id="${song.album_id}" data-song-id="${song.id}"
                                            >
                                                <i class="fas fa-play text-white text-xs"></i>
                                            </button>
                                        </div>
    
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-gray-200 text-sm">${song.title}</span>
                                            <span class="text-gray-400 text-xs">${song.artist_id}</span>
                                        </div>
                                    </div>
    
                                 
                                </div>
                            `);
                            });
                        }
                        resultsBox.show();
                        resultsBox.find(".play-track").off("click").on("click", function(e) {
                            e.preventDefault();
                            const btn = $(this);
                            const tempTrack = {
                                src: btn.data("audio-src"),
                                title: btn.data("title"),
                                artist: btn.data("artist"),
                                album: btn.data("album"),
                                plays: btn.data("plays"),
                                thumbnail: btn.data("thumbnail"),
                                duration: parseInt(btn.data("duration")),
                                button: this
                            };
                            // RESET PLAYER before switching
                            if (currentHowl) {
                                currentHowl.stop();
                                if (currentHowl._sounds) {
                                    currentHowl._sounds.forEach(s => {
                                        if (s._node && typeof s._node.pause === 'function') {
                                            s._node.pause();
                                            s._node.currentTime = 0;
                                        }
                                    });
                                }
                                currentHowl.unload(); // ⬅ unload to fully destroy sound
                                currentHowl = null;
                            }
                            // Set playlist only to this track
                            playlist = [tempTrack];
                            currentTrackIndex = 0;
                            playTrack(0); // start playing immediately
                            const songId = btn.data("song-id");
                            // Remove previous active row
                            $(".song-row").removeClass("song-active");
                            // Find matching row in Popular Songs
                            const row = $(`.song-row[data-song-id="${songId}"]`);
                            if (row.length) {
                                row.addClass("song-active");
                                // Smooth scroll to that row
                                row[0].scrollIntoView({
                                    behavior: "smooth",
                                    block: "center"
                                });
                            }
                            resultsBox.hide();
                        });
                    }
                });
            });
            // Close dropdown when clicking outside
            $(document).click(function(e) {
                if (!$(e.target).closest("#searchInput, #searchResults").length) {
                    $("#searchResults").hide();
                }
            });
        });
    </script>
    {{-- AJAX navigation  --}}
    <script>
        function updateActiveSidebar(targetUrl) {
            $("a[data-link]").removeClass("active");
            try {
                let targetPath = new URL(targetUrl, window.location.origin).pathname.replace(/\/+$/, "");
                $("a[data-link]").each(function() {
                    let href = $(this).attr("href");
                    if (href) {
                        let linkPath = new URL(href, window.location.origin).pathname.replace(/\/+$/, "");
                        if (linkPath === targetPath || (linkPath && targetPath.startsWith(linkPath))) {
                            $(this).addClass("active");
                        }
                    }
                });
            } catch (e) {
                console.error("Could not update sidebar active link:", e);
            }
        }

        function loadPageContent(url, pushState = false) {
            $("#page-loader").removeClass("hidden");
            $.get(url, function(response) {
                let htmlToParse = null;
                let meta = null;

                if (typeof response === "string") {
                    try {
                        let parsed = JSON.parse(response);
                        if (parsed && parsed.html) {
                            response = parsed;
                        }
                    } catch (e) {}
                }

                if (typeof response === "object" && response && response.html) {
                    htmlToParse = response.html;
                    meta = response.meta;
                } else if (typeof response === "string") {
                    htmlToParse = response;
                }

                if (htmlToParse) {
                    let $temp = $("<div>").append($.parseHTML(htmlToParse));
                    let $extracted = $temp.find("#main-content");
                    let contentHtml = $extracted.length ? $extracted.html() : htmlToParse;

                    $("#main-content").html(contentHtml);

                    if (meta) {
                        if (meta.title) document.title = meta.title;
                        if (meta.description) {
                            $("meta[name='description']").attr("content", meta.description);
                            $("meta[property='og:description']").attr("content", meta.description);
                        }
                        if (meta.title) $("meta[property='og:title']").attr("content", meta.title);
                        if (meta.url) $("meta[property='og:url']").attr("content", meta.url);
                        if (meta.image) $("meta[property='og:image']").attr("content", meta.image);
                    }

                    if (pushState) {
                        window.history.pushState({ path: url }, "", url);
                    }
                    window.scrollTo(0, 0);

                    if (typeof buildPlaylist === "function") {
                        buildPlaylist();
                    }

                    updateActiveSidebar(url);
                } else {
                    window.location.href = url;
                }
                $("#page-loader").addClass("hidden");
            }).fail(function() {
                $("#page-loader").addClass("hidden");
                window.location.href = url;
            });
        }

        $(document).on("click", "a[data-link]", function(e) {
            let url = $(this).attr("href");
            const isFullPage = $(this).data('fullpage'); // check attribute
            if (isFullPage) {
                // ⬅ This will bypass AJAX and do normal navigation
                window.location.href = url;
                return true;
            }
            e.preventDefault();
            console.log("Navigating to:", url);
            loadPageContent(url, true);
        });

        // Back / forward navigation
        window.addEventListener("popstate", function(e) {
            let url = window.location.href;
            console.log("Popstate navigating to:", url);
            loadPageContent(url, false);
        });
    </script>
    {{-- Profile dropdown  --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const profileDropdownBtn = document.getElementById('profileDropdownBtn');
            const profileDropdown = document.getElementById('profileDropdown');
            // Function to toggle the visibility of the dropdown
            function toggleDropdown() {
                profileDropdown.classList.toggle('hidden');
            }
            // Click listener for the dropdown button
            profileDropdownBtn.addEventListener('click', (event) => {
                event.stopPropagation(); // Prevents the document click listener from firing immediately
                toggleDropdown();
            });
            // Click listener for the entire document to close the dropdown
            document.addEventListener('click', (event) => {
                // Check if the click is outside the dropdown and the button
                const isClickInside = profileDropdown.contains(event.target) || profileDropdownBtn.contains(
                    event.target);
                if (!isClickInside && !profileDropdown.classList.contains('hidden')) {
                    toggleDropdown();
                }
            });
            // Handle the logout link click. In a real application, this would submit a form or call an API.
            const logoutBtn = document.getElementById('logoutBtn');
            logoutBtn.addEventListener('click', (event) => {
                event.preventDefault(); // Stop the link from navigating
                alert('You have been logged out!');
                // In a real app, you would add logic here to clear the session and redirect to a login page.
            });
        });
    </script>
    {{-- Share Modal  --}}
    <script>
        $(document).ready(function() {
            const shareModal = $("#AlbumShareModal");
            const shareLinkInput = $("#shareLink");
            // ✅ Event Delegation: will work even after #main-content reload
            $(document).on("click", ".ShareBtn", function() {
                const btn = $(this);
                let albumSlug = btn.data("album-slug") || btn.attr("data-album-slug");

                if (!albumSlug) {
                    const albumName = btn.data("album-name") || btn.attr("data-album-name") ||
                                      btn.closest(".album-card, [data-album-name]").data("album-name") ||
                                      btn.closest("div.p-3, div.relative, div").find("h3").first().text().trim() ||
                                      document.getElementById("album-page-container")?.dataset.albumSlug;
                    if (albumName) {
                        albumSlug = slugify(albumName);
                    }
                }

                if (!albumSlug) {
                    albumSlug = btn.data("album-id") || btn.attr("data-album-id") || "";
                }

                const shareUrl = "{{ config('app.website_url') }}album/" + albumSlug;
                // ✅ Set dynamic share link
                shareLinkInput.val(shareUrl);
                $("#shareWhatsapp").attr("href", "https://wa.me/?text=" + encodeURIComponent(shareUrl));
                $("#shareFacebook").attr("href", "https://www.facebook.com/sharer/sharer.php?u=" +
                    encodeURIComponent(shareUrl));
                $("#shareInstagram").attr("href", "https://www.instagram.com/?url=" + encodeURIComponent(
                    shareUrl));
                // Show modal
                shareModal.removeClass("hidden");
            });
            function buildSongShareUrl(songId, songTitle, albumName, albumId) {
                let albumSlug = "";
                const albumContainer = document.getElementById("album-page-container");
                if (albumContainer && albumContainer.dataset.albumSlug) {
                    albumSlug = albumContainer.dataset.albumSlug;
                } else if (albumName) {
                    albumSlug = slugify(albumName);
                } else if (albumId) {
                    albumSlug = slugify(albumId);
                }

                let songSlug = "";
                if (songTitle && songTitle !== "Not Playing") {
                    songSlug = slugify(songTitle);
                } else if (songId) {
                    const titleEl = document.querySelector(`.play-track[data-track-id="${songId}"]`);
                    if (titleEl && titleEl.dataset.title) {
                        songSlug = slugify(titleEl.dataset.title);
                    } else {
                        songSlug = songId;
                    }
                }

                const currentPath = window.location.pathname;
                if ((currentPath.startsWith("/popular-songs") || !albumSlug) && songSlug) {
                    return "{{ config('app.website_url') }}popular-songs/" + songSlug;
                }

                if (albumSlug && songSlug) {
                    return "{{ config('app.website_url') }}album/" + albumSlug + "/" + songSlug;
                }

                if (albumSlug) {
                    return "{{ config('app.website_url') }}album/" + albumSlug;
                }

                return "{{ config('app.website_url') }}song/" + songId;
            }

            $(document).on("click", ".share-song", function() {
                const id = $(this).data("id") || $(this).attr("data-id");
                const title = $(this).data("title") || $(this).attr("data-title") || "";
                const album_name = $(this).data("album-name") || $(this).attr("data-album-name") || "";
                const album_id = $(this).data("album-id") || $(this).attr("data-album-id") || "";
                const shareUrl = buildSongShareUrl(id, title, album_name, album_id);
                // ✅ Set dynamic share link
                shareLinkInput.val(shareUrl);
                $("#shareWhatsapp").attr("href", "https://wa.me/?text=" + encodeURIComponent(shareUrl));
                $("#shareFacebook").attr("href", "https://www.facebook.com/sharer/sharer.php?u=" +
                    encodeURIComponent(shareUrl));
                $("#shareInstagram").attr("href", "https://www.instagram.com/?url=" + encodeURIComponent(
                    shareUrl));
                // Show modal
                shareModal.removeClass("hidden");
            });
            $(document).on("click", ".wishlist-share-btn", function(e) {
                e.preventDefault();
                e.stopPropagation();
                const userId = $(this).data("user-id") || "{{ auth()->check() ? \App\Helpers\VideoHelper::encryptID(auth()->id()) : '' }}";
                const shareUrl = "{{ config('app.website_url') }}songs-wishlist/" + userId;
                shareLinkInput.val(shareUrl);
                $("#shareWhatsapp").attr("href", "https://wa.me/?text=" + encodeURIComponent(shareUrl));
                $("#shareFacebook").attr("href", "https://www.facebook.com/sharer/sharer.php?u=" +
                    encodeURIComponent(shareUrl));
                $("#shareInstagram").attr("href", "https://www.instagram.com/?url=" + encodeURIComponent(
                    shareUrl));
                shareModal.removeClass("hidden");
            });
            $(document).on("click", ".liked-songs-share-btn", function(e) {
                e.preventDefault();
                e.stopPropagation();
                const userId = $(this).data("user-id") || "{{ auth()->check() ? \App\Helpers\VideoHelper::encryptID(auth()->id()) : '' }}";
                const shareUrl = "{{ config('app.website_url') }}liked-songs/" + userId;
                shareLinkInput.val(shareUrl);
                $("#shareWhatsapp").attr("href", "https://wa.me/?text=" + encodeURIComponent(shareUrl));
                $("#shareFacebook").attr("href", "https://www.facebook.com/sharer/sharer.php?u=" +
                    encodeURIComponent(shareUrl));
                $("#shareInstagram").attr("href", "https://www.instagram.com/?url=" + encodeURIComponent(
                    shareUrl));
                shareModal.removeClass("hidden");
            });
            $(document).on("click", ".playlist-share-btn", function(e) {
                e.preventDefault();
                e.stopPropagation();
                const playlistName = $(this).data("playlist");
                const playlistSlug = $(this).data("playlist-slug") || (playlistName ? playlistName.toString().toLowerCase().trim().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '').replace(/\-\-+/g, '-') : '');
                const userId = $(this).data("user-id") || "{{ auth()->check() ? \App\Helpers\VideoHelper::encryptID(auth()->id()) : '' }}";
                const shareUrl = "{{ config('app.website_url') }}playlist/" + playlistSlug + "/" + userId;
                shareLinkInput.val(shareUrl);
                $("#shareWhatsapp").attr("href", "https://wa.me/?text=" + encodeURIComponent(shareUrl));
                $("#shareFacebook").attr("href", "https://www.facebook.com/sharer/sharer.php?u=" +
                    encodeURIComponent(shareUrl));
                $("#shareInstagram").attr("href", "https://www.instagram.com/?url=" + encodeURIComponent(
                    shareUrl));
                shareModal.removeClass("hidden");
            });
            $(document).on("click", "#footer-share-btn", function(e) {
                e.preventDefault();
                e.stopPropagation();
                const songId = $(this).attr("data-id") || $(this).data("id");
                if (!songId) {
                    showToast("⚠️ Select a song first to share.");
                    return;
                }
                const songTitle = $(this).attr("data-title") || $(this).data("title") || $("#player-title").text();
                const albumName = $(this).attr("data-album-name") || $(this).data("album-name") || $("#player-album").val();
                const albumId = $(this).attr("data-album-id") || $(this).data("album-id") || "";
                const shareUrl = buildSongShareUrl(songId, songTitle, albumName, albumId);
                shareLinkInput.val(shareUrl);
                $("#shareWhatsapp").attr("href", "https://wa.me/?text=" + encodeURIComponent(shareUrl));
                $("#shareFacebook").attr("href", "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(shareUrl));
                $("#shareInstagram").attr("href", "https://www.instagram.com/?url=" + encodeURIComponent(shareUrl));
                shareModal.removeClass("hidden");
            });
            $(document).on("click", ".SongShareBtn", function(e) {
                e.stopPropagation(); // 🚨 prevent row click
                $(".song-dropdown").not($(this).siblings(".song-dropdown")).addClass("hidden");
                $(this).siblings(".song-dropdown").toggleClass("hidden");
            });
            $(document).on("click", ".song-dropdown button", function(e) {
                e.stopPropagation(); // 🚨 prevent row play
            });
            $(document).on("click", function() {
                $(".song-dropdown").addClass("hidden");
            });
            // Close modal when clicking the close button
            shareModal.on("click", "button", function() {
                shareModal.addClass("hidden");
            });
            // Close modal when clicking outside of modal content
            shareModal.on("click", function(e) {
                if (e.target === this) {
                    shareModal.addClass("hidden");
                }
            });
            // Copy share link function
            window.copyShareLink = function() {
                shareLinkInput.select();
                document.execCommand("copy");
                alert("Link copied to clipboard!");
            };
        });
    </script>
    {{-- Wishlist AJAX  --}}
    <script>
        $(document).on("click", ".album-wishlist", function(e) {
            e.preventDefault();
            let button = $(this);
            let albumId = button.data("album-id");
            $.ajax({
                url: "{{ url('/album') }}/" + albumId + "/wishlist",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function(data) {
                    if (data.status === "added") {
                        // Change heart icon to solid (filled)
                        button.find("i")
                            .removeClass("fa-regular fa-heart")
                            .addClass("fa-solid fa-heart text-red-500");
                        showToast("✅ Album added to wishlist!");
                    } else if (data.status === "removed") {
                        // Change heart icon back to outline
                        button.find("i")
                            .removeClass("fa-solid fa-heart text-red-500")
                            .addClass("fa-regular fa-heart");
                        showToast("❌ Album removed from wishlist!");
                    }
                },
                error: function() {
                    alert("Failed to update wishlist.");
                }
            });
        });
        $(document).on("click", ".song-wishlist", function(e) {
            e.preventDefault();
            e.stopPropagation();
            let button = $(this);
            let songId = button.data("song-id") || button.attr("data-song-id");
            if (!songId) {
                showToast("⚠️ Select a song first.");
                return;
            }
            $.ajax({
                url: "{{ url('/song') }}/" + songId + "/wishlist",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function(data) {
                    if (data.status === "added") {
                        // Change heart icon to solid (filled) for all instances
                        $(`.song-wishlist[data-song-id="${songId}"]`).find("i")
                            .removeClass("fa-regular fa-heart")
                            .addClass("fa-solid fa-heart text-red-500");
                        showToast("✅ Song added to wishlist!");
                    } else if (data.status === "removed") {
                        // Change heart icon back to outline for all instances
                        $(`.song-wishlist[data-song-id="${songId}"]`).find("i")
                            .removeClass("fa-solid fa-heart text-red-500")
                            .addClass("fa-regular fa-heart");
                        showToast("❌ Song removed from wishlist!");
                    }
                },
                error: function() {
                    alert("Failed to update wishlist.");
                }
            });
        });

        // ⭐ Song Like / Unlike Handler (Thumbs Up)
        $(document).on("click", ".song-like-btn", function(e) {
            e.preventDefault();
            e.stopPropagation();
            let button = $(this);
            let songId = button.data("song-id") || button.attr("data-song-id");
            if (!songId) {
                showToast("⚠️ Select a song first.");
                return;
            }
            $.ajax({
                url: "{{ url('/song') }}/" + songId + "/like",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function(data) {
                    if (data.status === "liked") {
                        $(`.song-like-btn[data-song-id="${songId}"]`).find("i")
                            .removeClass("fa-regular fa-thumbs-up")
                            .addClass("fa-solid fa-thumbs-up text-theme");
                        showToast("👍 Song liked!");
                    } else if (data.status === "unliked") {
                        $(`.song-like-btn[data-song-id="${songId}"]`).find("i")
                            .removeClass("fa-solid fa-thumbs-up text-theme")
                            .addClass("fa-regular fa-thumbs-up");
                        showToast("Song unliked.");
                        if ($("#liked-songs-container").length) {
                            $(`tr[data-song-id="${songId}"]`).fadeOut(300, function() {
                                $(this).remove();
                                if ($("#songs-table-body tr").length === 0) {
                                    $(".songs-liked-actions, .songs-liked-section").hide();
                                    $("#liked-song-empty-div").removeClass("hidden d-none").show();
                                }
                            });
                            $(`.mobile-song-card[data-song-id="${songId}"]`).fadeOut(300, function() {
                                $(this).remove();
                                if ($("#songs-card-body .mobile-song-card").length === 0) {
                                    $(".songs-liked-actions, .songs-liked-section").hide();
                                    $("#liked-song-empty-div").removeClass("hidden d-none").show();
                                }
                            });
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        showToast("⚠️ Please login to like songs.");
                    } else {
                        showToast("Failed to update like status.");
                    }
                }
            });
        });

        // ⭐ Album Like / Unlike Handler (Thumbs Up)
        $(document).on("click", ".album-like-btn", function(e) {
            e.preventDefault();
            e.stopPropagation();
            let button = $(this);
            let albumId = button.data("album-id");
            $.ajax({
                url: "{{ url('/album') }}/" + albumId + "/like",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function(data) {
                    if (data.status === "liked") {
                        $(`.album-like-btn[data-album-id="${albumId}"]`).find("i")
                            .removeClass("fa-regular fa-thumbs-up")
                            .addClass("fa-solid fa-thumbs-up text-theme");
                        showToast("👍 Album liked!");
                    } else if (data.status === "unliked") {
                        $(`.album-like-btn[data-album-id="${albumId}"]`).find("i")
                            .removeClass("fa-solid fa-thumbs-up text-theme")
                            .addClass("fa-regular fa-thumbs-up");
                        showToast("Album unliked.");
                        if ($("#liked-albums-container").length) {
                            $(`.album-card[data-album-id="${albumId}"]`).fadeOut(300, function() {
                                $(this).remove();
                                if ($(".album-card").length === 0) {
                                    $(".albums-liked-actions").remove();
                                    $("#album-div").removeClass("hidden d-none").show();
                                    $("#mobile-songs-list").remove();
                                }
                            });
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        showToast("⚠️ Please login to like albums.");
                    } else {
                        showToast("Failed to update like status.");
                    }
                }
            });
        });
        let selectedSongId = null;
        $(document).on("click", ".add-to-playlist", function(e) {
            e.preventDefault();
            e.stopPropagation();
            let container = $(this).closest(".relative");
            container.find(".playlist-dropdown").toggleClass("hidden");
        });

        function loadPlaylists() {
            $.get("{{ route('playlist.list') }}", function(data) {
                let html = "";
                data.forEach(function(item) {
                    html += `
            <div class="playlist-item cursor-pointer p-2 text-white hover:bg-gray-700"
                 data-name="${item.playlist_name}">
                 ${item.playlist_name}
            </div>
            `;
                });
                $("#playlistList").html(html);
            });
        }
        $(document).on("click", ".playlist-item", function() {
            let playlist = $(this).data("playlist");
            let songId = $(this).data("song-id");
            let albumId = $(this).data("album-id");
            $.ajax({
                url: "{{ route('playlist.add.song') }}",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    playlist_name: playlist,
                    song_id: songId,
                    album_id: albumId
                },
                success: function(res) {
                    if (res.status === "added") {
                        showToast("🎵 Song added to playlist");
                    }
                    if (res.status === "exists") {
                        showToast("⚠️ Already in playlist");
                    }
                    $(".playlist-dropdown").addClass("hidden");
                    $(".song-dropdown").addClass("hidden");
                }
            });
        });
        $(".create-playlist").click(function() {
            let name = prompt("Playlist name");
            let songId = $(this).data("song-id");
            let albumId = $(this).data("album-id");
            if (!name) return;
            $.ajax({
                url: "{{ route('playlist.create') }}",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    song_id: songId,
                    album_id: albumId,
                    playlist_name: name
                },
                success: function(res) {
                    showToast("✅ Playlist created and song added");
                    $(".playlist-dropdown").addClass("hidden");
                    $(".song-dropdown").addClass("hidden");
                    $.get('/playlist/list', function(data) {
                        $("#sidebar-playlists").html(data);
                    });

                }
            });
        });
        $(document).on("keyup", "#playlistSearch", function(e) {
            e.stopPropagation();

            let value = $(this).val().toLowerCase();

            $(".playlist-toggle").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
        $(document).on("click", ".playlist-dropdown", function(e) {
            e.stopPropagation();
        });
        $(document).click(function() {
            $(".song-dropdown").addClass("hidden");
            $(".playlist-dropdown").addClass("hidden");
        })
        //         $(document).on("mouseenter",".add-to-playlist",function(){
        //     $(this).siblings(".playlist-dropdown").removeClass("hidden");
        // });
        // $(document).on("mouseleave",".relative",function(){
        //     $(this).find(".playlist-dropdown").addClass("hidden");
        // });
        function showToast(message) {
            let toast = document.createElement("div");
            toast.className = "custom-toast";
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add("show");
            }, 100);
            setTimeout(() => {
                toast.classList.remove("show");
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        }
    </script>
    {{-- Album Wishlist page AJAX remove  --}}
    <script>
        $(document).on("click", ".album-all-wishlist", function(e) {
            e.preventDefault();
            let button = $(this);
            let albumId = button.data("album-id");
            $.ajax({
                url: "{{ url('/album') }}/" + albumId + "/wishlist",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function(data) {
                    if (data.status === "removed") {
                        // Animate and remove card from DOM
                        let card = button.closest(".album-card");
                        card.fadeOut(300, function() {
                            $(this).remove();
                            if ($(".album-card").length === 0) {
                                $(".clear-all-album-wishlist-btn").remove();
                                $("#album-div").removeClass('d-none hidden').show();
                                $("#mobile-songs-list").remove();
                                $(".mobile-songs-list").addClass('d-none hidden');
                            }
                        });
                        showToast("❌ Album removed from wishlist!");
                    }
                },
                error: function() {
                    alert("Failed to update wishlist.");
                }
            });
        });

        $(document).on("click", ".clear-all-album-wishlist-btn", function(e) {
            e.preventDefault();
            if (!confirm("Are you sure you want to remove all albums from your wishlist?")) {
                return;
            }

            $.post("/album-wishlist/clear", {
                _token: "{{ csrf_token() }}"
            }, function(res) {
                if (res.status) {
                    $(".clear-all-album-wishlist-btn").remove();
                    $(".album-card").remove();
                    $("#mobile-songs-list").remove();
                    $(".mobile-songs-list").addClass('d-none hidden');
                    $("#album-div").removeClass("d-none hidden").show();
                    showToast("✅ " + (res.message || "All albums cleared from wishlist successfully!"));
                } else {
                    alert(res.message || "Failed to clear albums from wishlist.");
                }
            }).fail(function() {
                alert("An error occurred while clearing wishlist albums.");
            });
        });
    </script>
    {{-- Song Wishlist page AJAX remove  --}}
    <script>
        $(document).on("click", ".song-all-wishlist", function(e) {
            e.preventDefault();
            let button = $(this);
            let songId = button.data("song-id");
            $.ajax({
                url: "{{ url('/song') }}/" + songId +
                    "/wishlist", // <-- You must have this route in web.php
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content")
                },
                success: function(data) {
                    if (data.status === "removed") {
                        // Find the <tr> of this song and fade it out, then remove
                        let row = button.closest("tr");
                        row.fadeOut(300, function() {
                            $(this).remove();
                            // If no rows left, show "empty state" message
                            if ($("tbody tr").length === 0) {
                                $(".songs-wishlist-actions").remove();
                                $(".songs-wishlist-section").remove();
                                $("#song-empty-div").removeClass('d-none hidden').show();
                            }
                        });
                        showToast("❌ Song removed from wishlist!");
                    }
                },
                error: function() {
                    alert("Failed to update wishlist.");
                }
            });
        });

        $(document).on("click", ".clear-all-song-wishlist-btn", function(e) {
            e.preventDefault();
            if (!confirm("Are you sure you want to remove all songs from your wishlist?")) {
                return;
            }

            $.post("/song-wishlist/clear", {
                _token: "{{ csrf_token() }}"
            }, function(res) {
                if (res.status) {
                    $(".songs-wishlist-actions").remove();
                    $(".songs-wishlist-section").remove();
                    $("#mobile-songs-list").remove();
                    $("#song-empty-div").removeClass("d-none hidden").show();
                    showToast("✅ " + (res.message || "All songs cleared from wishlist successfully!"));
                } else {
                    alert(res.message || "Failed to clear songs from wishlist.");
                }
            }).fail(function() {
                alert("An error occurred while clearing wishlist songs.");
            });
        });

        $(document).on("click", ".clear-all-liked-songs-btn", function(e) {
            e.preventDefault();
            if (!confirm("Are you sure you want to remove all songs from your liked list?")) {
                return;
            }

            $.post("/liked-songs/clear", {
                _token: "{{ csrf_token() }}"
            }, function(res) {
                if (res.status) {
                    $(".songs-liked-actions").remove();
                    $(".songs-liked-section").remove();
                    $("#liked-song-empty-div").removeClass("d-none hidden").show();
                    showToast("👍 " + (res.message || "All liked songs cleared successfully!"));
                } else {
                    alert(res.message || "Failed to clear liked songs.");
                }
            }).fail(function() {
                alert("An error occurred while clearing liked songs.");
            });
        });

        $(document).on("click", ".clear-all-liked-album-btn", function(e) {
            e.preventDefault();
            if (!confirm("Are you sure you want to remove all albums from your liked list?")) {
                return;
            }

            $.post("/liked-albums/clear", {
                _token: "{{ csrf_token() }}"
            }, function(res) {
                if (res.status) {
                    $(".albums-liked-actions").remove();
                    $(".album-card").remove();
                    $("#mobile-songs-list").remove();
                    $("#album-div").removeClass("d-none hidden").show();
                    showToast("👍 " + (res.message || "All liked albums cleared successfully!"));
                } else {
                    alert(res.message || "Failed to clear liked albums.");
                }
            }).fail(function() {
                alert("An error occurred while clearing liked albums.");
            });
        });
    </script>
    {{-- album clik and list of songs  --}}
    <script>
        let hasMoreSongs = true;
        let page = 1;
        let loading = false;
        hasMoreSongs = true;
        let activeAlbumId = null; // track album
        $(document).on('click', '.album-card', function() {
            activeAlbumId = $(this).data('album-id'); // ⭐ set album
            page = 1; // ⭐ reset pagination
            loading = false;
            $("#load-more-indicator").removeClass("hidden");
            $('.album-card').removeClass('ring-2 ring-theme');
            // $(this).addClass('ring-2 ring-theme');
            // Remove active from all
            $('.album-card').removeClass('active');
            // Add active to clicked album
            $(this).addClass('active');
            // $('#songs-table-body').html('<tr><td colspan="5" class="text-center py-4">Loading...</td></tr>');
            // $('#songs-card-body').html('<div class="text-center py-4">Loading...</div>');
            $.get(`/album-click/${activeAlbumId}/songs?page=1`, function(res) {
                checkScrollable();
                $('#songs-table-body').html(res.desktop);
                $('#songs-card-body').html(res.mobile);
            });
        });
    </script>
    {{-- load more songs --}}
    <script>
        const tableContainer = document.querySelector("#main-content");
        tableContainer.addEventListener("scroll", function() {
            if (!hasMoreSongs) return; // ⭐ NO MORE DATA
            const albumContainer = document.getElementById("album-page-container");
            if (albumContainer && albumContainer.dataset.isSingle === "1") return;
            const popularContainer = document.getElementById("popular-songs-container");
            if (popularContainer && popularContainer.dataset.isSingle === "1") return;
            const {
                scrollTop,
                scrollHeight,
                clientHeight
            } = tableContainer;
            // ⭐ Only trigger if scrolling is possible
            if (scrollHeight <= clientHeight) return;
            if (!loading && scrollTop + clientHeight >= scrollHeight - 10) {
                loading = true;
                const indicator = document.getElementById("load-more-indicator");
                if (indicator) indicator.classList.remove("hidden");
                setTimeout(loadMoreSongs, 800);
            }
        });

        function loadMoreSongs() {
            page++;
            let url = activeAlbumId ?
                `/album-click/${activeAlbumId}/songs?page=${page}` :
                `${window.location.pathname}?page=${page}`;
            $.get(url, function(res) {
                checkScrollable();
                if (!res.desktop && !res.mobile) {
                    stopLoading();
                    return;
                }
                $("#songs-table-body").append(res.desktop);
                $("#songs-card-body").append(res.mobile);
                hasMoreSongs = res.hasMore; // ⭐ update flag
                if (!hasMoreSongs) {
                    stopLoading();
                    return;
                }
                $("#load-more-indicator").addClass("hidden");
                loading = false;
            });
        }

        function checkScrollable() {
            const indicator = document.getElementById("load-more-indicator");
            if (!indicator) return;
            if (tableContainer.scrollHeight <= tableContainer.clientHeight || !hasMoreSongs) {
                indicator.classList.add("hidden");
            }
        }

        function stopLoading() {
            loading = true; // ❗ prevents future calls
            $("#load-more-indicator").addClass("hidden");
        }
        $('.view-all-songs').on('click', function() {
            activeAlbumId = null;
            page = 1;
            hasMoreSongs = true;
            $.get(`${window.location.pathname}?page=1`, function(res) {
                $('#songs-table-body').html(res.desktop);
                $('#songs-card-body').html(res.mobile);
            });
        });
    </script>
    <script>
        // Toggle dropdown
        $("#playlist-menu-btn").click(function(e) {
            e.stopPropagation();
            $("#playlist-menu").toggle();
        });
        // Close dropdown if clicking outside
        $(document).click(function() {
            $("#playlist-menu").hide();
        });
        // Open edit modal
        $("#edit-details-btn").click(function() {
            $("#playlist-menu").hide();
            $("#editPlaylistModal").removeClass("hidden").addClass("flex");
        });
        // Close modal
        $("#close-edit-modal").click(function() {
            $("#editPlaylistModal").removeClass("flex").addClass("hidden");
        });
        $("#save-playlist-details").click(function() {
            let name = $("#edit-playlist-name").val();
            let old_name = $("#playlist-title").text().trim();
            let imageFile = $("#edit-playlist-image")[0].files[0];
            let formData = new FormData();
            formData.append("name", name);
            formData.append("old_name", old_name);
            formData.append("_token", "{{ csrf_token() }}");
            // only append if user selected new image
            if (imageFile) {
                formData.append("image", imageFile);
            }
            $.ajax({
                url: "/playlist/update-details",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status) {
                        // location.reload();
                        window.location.href = "/playlist/" + encodeURIComponent(name);
                    }
                }
            });
        });
        $("#edit-playlist-image").on("change", function() {
            let file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $("#current-playlist-image").attr("src", e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
        $(document).on("click", ".delete-playlist", function() {
            let old_name = $("#playlist-title").text().trim();
            if (!confirm("Delete this playlist?")) return;
            $.post("/playlist/delete", {
                name: old_name,
                _token: "{{ csrf_token() }}"
            }, function() {
                window.location.href = "/musics-index";
            });
        });
        $(document).on("click", ".clear-all-playlist-btn", function(e) {
            e.preventDefault();
            let playlistName = $(this).data("playlist") || $("#playlist-title").text().trim();
            if (!confirm("Are you sure you want to remove all songs from \"" + playlistName + "\"?")) {
                return;
            }
            $("#playlist-menu").hide();

            $.post("/playlist/clear", {
                playlist: playlistName,
                _token: "{{ csrf_token() }}"
            }, function(res) {
                if (res.status) {
                    $(".desktop-songs-table").remove();
                    $(".mobile-songs-card-body").remove();
                    $("#mobile-songs-list").remove();
                    $(".clear-all-playlist-btn").remove();
                    $("#playlist-song-count").text("0 songs");
                    $("#playlist-empty-div").removeClass("d-none hidden").show();
                    showToast("✅ " + (res.message || "All songs cleared from playlist successfully!"));
                } else {
                    alert(res.message || "Failed to clear songs from playlist.");
                }
            }).fail(function(xhr) {
                alert("An error occurred while clearing the playlist.");
            });
        });
        $(".share-playlist").click(function() {
            let url = window.location.href;
            navigator.clipboard.writeText(url);
            alert("Playlist link copied!");
        });

        $(document).on("click", ".playlist-toggle-add", function() {
            let btn = $(this);
            let songId = btn.data("song-id");
            let albumId = btn.data("album-id");
            let playlist = btn.data("playlist");

            $.post("/playlist/add", {
                song_id: songId,
                playlist: playlist,
                album_id: albumId,
                _token: "{{ csrf_token() }}"
            }, function(res) {
                if (res.status) {
                    // Replace Add button with Remove button
                    let parentDiv = btn.closest('.playlist-item');
                    let playlistName = btn.data("playlist");

                    parentDiv.html(`
                <span class="text-sm text-white truncate">${playlistName}</span>
                <div class="flex gap-2">
                    <button class="playlist-toggle-remove bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded"
                        data-playlist="${playlistName}" 
                        data-song-id="${songId}" 
                        data-album-id="${albumId}">
                        <i class="fas fa-minus mr-1"></i> Remove
                    </button>
                </div>
            `);

                    // Show success message (optional)
                    showToast('Added to playlist successfully!');
                }
            });
        });

        // Handle Add/Remove Toggle from Song Dropdown Menu
        $(document).on("click", ".playlist-toggle", function(e) {
            e.preventDefault();
            e.stopPropagation();
            let btn = $(this);
            let playlist = btn.data("playlist");
            let songId = btn.data("song-id");
            let albumId = btn.data("album-id");
            let exists = btn.data("exists");

            if (exists == 1) {
                // Remove from playlist
                $.post("/playlist/remove", {
                    song_id: songId,
                    playlist: playlist,
                    album_id: albumId,
                    _token: "{{ csrf_token() }}"
                }, function(res) {
                    if (res.status) {
                        btn.data("exists", 0);
                        btn.html(`➕ Add to ${playlist}`);
                        showToast(`❌ Song removed from ${playlist}!`);

                        // If we are currently on the playlist view page for this specific playlist
                        // we should dynamically remove the song from the view.
                        let songRow = btn.closest("tr.song-row");
                        if (songRow.length > 0 && songRow.data("type") === "playlist" && songRow.data("playlist-name") === playlist) {
                            songRow.fadeOut(300, function() {
                                $(this).remove();
                                if ($("#songs-table-body tr.song-row").length === 0) {
                                    $(".desktop-songs-table").remove();
                                    $(".mobile-songs-card-body").remove();
                                    $("#playlist-empty-div").removeClass("d-none hidden").show();
                                }
                            });
                        }

                        let songCard = btn.closest("div.play-track");
                        if (songCard.length > 0 && songCard.data("type") === "playlist" && songCard.data("playlist-name") === playlist) {
                            songCard.fadeOut(300, function() {
                                $(this).remove();
                                if ($("#songs-card-body div.play-track").length === 0) {
                                    $(".desktop-songs-table").remove();
                                    $(".mobile-songs-card-body").remove();
                                    $("#playlist-empty-div").removeClass("d-none hidden").show();
                                }
                            });
                        }
                    }
                });
            } else {
                // Add to playlist
                $.post("/playlist/add", {
                    song_id: songId,
                    playlist: playlist,
                    album_id: albumId,
                    _token: "{{ csrf_token() }}"
                }, function(res) {
                    if (res.status) {
                        btn.data("exists", 1);
                        btn.html(`✔ Remove from ${playlist}`);
                        showToast(`🎵 Song added to ${playlist}!`);
                    } else {
                        showToast(`⚠️ ${res.message || 'Error adding song'}`);
                    }
                });
            }
        });

        // Handle Remove from Playlist
        $(document).on("click", ".playlist-toggle-remove", function() {
            let btn = $(this);
            let songId = btn.data("song-id");
            let albumId = btn.data("album-id");
            let playlist = btn.data("playlist");

            $.post("/playlist/remove", {
                song_id: songId,
                playlist: playlist,
                album_id: albumId,
                _token: "{{ csrf_token() }}"
            }, function(res) {
                if (res.status) {
                    // Replace Remove button with Add button
                    let parentDiv = btn.closest('.playlist-item');
                    let playlistName = btn.data("playlist");

                    parentDiv.html(`
                <span class="text-sm text-white truncate">${playlistName}</span>
                <div class="flex gap-2">
                    <button class="playlist-toggle-add bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded"
                        data-playlist="${playlistName}" 
                        data-song-id="${songId}" 
                        data-album-id="${albumId}">
                        <i class="fas fa-plus mr-1"></i> Add
                    </button>
                </div>
            `);

                    // Show success message (optional)
                    showToast('Removed from playlist successfully!');
                }
            });
        });

        // Toast notification function (if you want to show messages)
        function showToast(message) {
            // You can implement your own toast notification here
            // Example using a simple alert or custom toast
            console.log(message);
            // Or use a library like toastr
            // toastr.success(message);
        }

        $("#create-playlist-sidebar").click(function() {
            $("#createPlaylistModal").removeClass("hidden").addClass("flex");
        });
        $(document).on("click", "#create-playlist-sidebar", function() {

            let name = prompt("Enter playlist name:");

            if (!name) return;

            $.post("/playlist/create", {
                playlist_name: name,
                _token: "{{ csrf_token() }}"
            }, function(res) {

                if (res.status) {
                    location.reload();
                }

            });

        });
        // $("#edit-playlist").click(function() {
        //     $("#playlist-title").hide();
        //     $("#playlist-name-input").removeClass("hidden").focus();
        // });
        // $("#playlist-name-input").keypress(function(e) {
        //     if (e.which == 13) {
        //         let newName = $(this).val();
        //       
        //  let old_name = $("#playlist-title").text().trim();
        //         $.post("/playlist/update-name", {
        //             old_name: oldName,
        //             new_name: newName,
        //             _token: "{{ csrf_token() }}"
        //         }, function(res) {
        //             if (res.status) {
        //                 // redirect to new playlist url
        //                 window.location.href = "/playlist/" + encodeURIComponent(newName);
        //             }
        //         });
        //     }
        // });
        $(document).on("click", ".playlist-shuffle", function() {
            let playlist = $(this).data("playlist");
            let userId = $(this).data("user-id");
            let btn = document.getElementById("shuffle-btn");
            if (btn) btn.click();
            let shuffleUrl = userId ? "/playlist/shuffle/" + encodeURIComponent(playlist) + "/" + userId : "/playlist/shuffle/" + encodeURIComponent(playlist);
            $.get(shuffleUrl, function(res) {
                if (res.status) {
                    // Desktop table
                    $("#songs-table-body").html(res.desktop);
                    // Mobile cards
                    $("#songs-card-body").html(res.mobile);
                    if (window.buildPlaylist) window.buildPlaylist();
                }
            });
        });
        $(document).on("click", ".album-shuffle", function() {
            let album_id = $(this).data("album-id");
            let btn = document.getElementById("shuffle-btn");
            if (btn) btn.click();
            $.get("/album/shuffle/" + album_id, function(res) {
                if (res.status) {
                    // Desktop table
                    $("#songs-table-body").html(res.desktop);
                    // Mobile cards
                    $("#songs-card-body").html(res.mobile);
                    if (window.buildPlaylist) window.buildPlaylist();
                }
            });
        });
        $("#playlist-image").change(function() {
            let file = this.files[0];
            let old_name = $("#playlist-title").text().trim();
            let formData = new FormData();
            formData.append("image", file);
            formData.append("playlist", old_name);
            formData.append("_token", "{{ csrf_token() }}");
            $.ajax({
                url: "/playlist/update-image",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status) {
                        location.reload();
                    }
                }
            });
        });
    </script>
</body>

</html>
