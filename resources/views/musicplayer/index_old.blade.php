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
        #album-songs::-webkit-scrollbar {
            width: 8px;
        }

        #album-songs::-webkit-scrollbar-thumb {
            background: #3b82f6;
            /* blue */
        }

        #album-songs::-webkit-scrollbar-thumb:hover {
            background: #60a5fa;
        }

        /* Firefox support */
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: var(--color-theme) var(--color-dark-light);
        }

        #main-content {
            scrollbar-width: thin;
            scrollbar-color: #ff5c40 var(--color-dark);
        }

        #album-songs {
            scrollbar-width: thin;
            scrollbar-color: #3b82f6 var(--color-dark-light);
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

        .play-song.playing {
            background-color: #ef4444;
            /* red-500 */
            color: #fff;
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
            <div id="main-content" style="height:80vh" class="flex-[4] overflow-y-auto p-6 space-y-8 bg-dark">
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
            <div id="album-songs" style="height:80vh" class="flex-[1] bg-dark-light rounded-lg p-4 overflow-y-auto">
                <h3 class="text-lg font-semibold mb-3">Songs</h3>
                <div id="songs-list" class="space-y-2 text-gray-300">
                    <p>Select an album to view songs.</p>
                </div>
            </div>
        </div>
    </main>
    @include('musicplayer.partials.footer')
    {{-- Sidebar open close  --}}
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
            let currentPlayingButton = null;
            let currentPlayingAlbumBtn = null;
            let playlist = [];

            const footerPlayPauseButton = document.getElementById("footer-play-pause-btn");
            const footerPlayPauseIcon = footerPlayPauseButton.querySelector("i");
            const playerTitle = document.getElementById("player-title");
            const playerArtist = document.getElementById("player-artist");
            const playerThumbnail = document.getElementById("player-thumbnail");
            const musicSeekBar = document.getElementById("music-seek-bar");
            const currentTimeSpan = document.getElementById("current-time");
            const totalDurationSpan = document.getElementById("total-duration");
            const volumeSlider = document.getElementById("volume-slider");
            const backwardBtn = document.getElementById("backward-btn");
            const forwardBtn = document.getElementById("forward-btn");

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
                document.querySelectorAll(".play-track")
                    .forEach(btn => {
                        btn.classList.remove("playing");
                        const icon = btn.querySelector("i");
                        if (icon) {
                            icon.classList.remove("fa-pause");
                            icon.classList.add("fa-play");
                        }
                    });

                document.querySelectorAll(".album-play i")
                    .forEach(icon => {
                        icon.classList.remove("fa-pause");
                        icon.classList.add("fa-play");
                    });
            }

            function setAllMatchingButtonsToPause(trackId, isPlaying) {
                // First clear everything
                document.querySelectorAll(".play-track").forEach(btn => {
                    btn.classList.remove("playing");
                    const icon = btn.querySelector("i");
                    if (icon) {
                        icon.classList.remove("fa-pause");
                        icon.classList.add("fa-play");
                    }
                });

                // Then set only matching buttons
                document.querySelectorAll(`.play-track[data-track-id="${trackId}"]`)
                    .forEach(btn => {
                        btn.classList.toggle("playing", isPlaying);
                        const icon = btn.querySelector("i");
                        if (icon) {
                            icon.classList.toggle("fa-play", !isPlaying);
                            icon.classList.toggle("fa-pause", isPlaying);
                        }
                    });
            }


            function resetPlayer() {
                if (currentHowl) currentHowl.stop();
                currentHowl = null;
                currentTrackIndex = -1;
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

            window.buildPlaylist = function() { // ✅ attach to window
                playlist = [];
                document.querySelectorAll(".play-track")
                    .forEach((btn, index) => {
                        playlist.push({
                            src: btn.dataset.audioSrc,
                            title: btn.dataset.title,
                            artist: btn.dataset.artist,
                            thumbnail: btn.dataset.thumbnail,
                            duration: parseInt(btn.dataset.duration),
                            button: btn
                        });
                        btn.dataset.index = index;
                        btn.onclick = onSongClick;
                    });
            };


            function onSongClick(e) {
                e.preventDefault();
                buildPlaylist();
                playTrack(parseInt(e.currentTarget.dataset.index));
            }

            function playTrack(index) {
                const track = playlist[index];
                if (!track) return;

                // If same track, just toggle play/pause
                if (index === currentTrackIndex && currentHowl) {
                    if (currentHowl.playing()) currentHowl.pause();
                    else currentHowl.play();
                    updateIcons(currentHowl.playing());
                    return;
                }

                if (currentHowl) {
                    currentHowl.stop();
                    currentHowl.unload();
                }

                currentTrackIndex = index;
                resetAllPlayButtons();

                currentPlayingButton = track.button;
                currentPlayingButton.classList.add("playing");
                currentPlayingButton.querySelector("i")?.classList.replace("fa-play", "fa-pause");

                // ✅ Update footer immediately
                updateFooter(track, true);

                currentHowl = new Howl({
                    src: [track.src],
                    html5: true,
                    volume: parseFloat(volumeSlider.value),
                    onplay: () => {
                        requestAnimationFrame(updateSeek); // Keep updating seek bar
                    },
                    onend: () => {
                        if (index < playlist.length - 1) playTrack(index + 1);
                        else playTrack(0);
                    },
                    onloaderror: resetPlayer
                });

                currentHowl.play();
                let track_id = playlist[currentTrackIndex].button.dataset.trackId;
                if (playlist[currentTrackIndex] !== currentTrackIndex) {
                    const incrementUrl = "{{ url('/music') }}/" + track_id + "/play";
                    // Increment play count when starting a new track
                    fetch(incrementUrl, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                }
            }


            function updateSeek() {
                if (!currentHowl || !currentHowl.playing()) return;

                // ✅ Only update seek bar if user is NOT dragging
                if (!isDraggingSeekBar) {
                    const seek = currentHowl.seek();
                    musicSeekBar.value = (seek / currentHowl.duration()) * 100;
                    updateBarFill(musicSeekBar, musicSeekBar.value);
                    currentTimeSpan.textContent = formatTime(seek * 1000);
                }

                requestAnimationFrame(updateSeek);
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

                playerTitle.textContent = track.title;
                playerArtist.textContent = track.artist;
                totalDurationSpan.textContent = formatTime(track.duration);

                updateIcons(isPlaying);
            }


            function updateIcons(isPlaying) {
                footerPlayPauseIcon.classList.toggle("fa-play", !isPlaying);
                footerPlayPauseIcon.classList.toggle("fa-pause", isPlaying);

                if (playlist[currentTrackIndex]) {
                    const trackId = playlist[currentTrackIndex].button.dataset.trackId;
                    setAllMatchingButtonsToPause(trackId, isPlaying);
                }
            }
            musicSeekBar.addEventListener("input", () => {
                updateBarFill(musicSeekBar, musicSeekBar.value);

                if (currentHowl && currentHowl.state() === "loaded") {
                    // Preview time (even if paused)
                    const previewTime = (musicSeekBar.value / 100) * currentHowl.duration();
                    currentTimeSpan.textContent = formatTime(previewTime * 1000);
                }
            });
            musicSeekBar.addEventListener("change", () => {
                if (currentHowl && currentHowl.state() === "loaded") {
                    const newTime = (musicSeekBar.value / 100) * currentHowl.duration();
                    currentHowl.seek(newTime);

                    // ✅ If song was playing, keep playing from new position
                    if (!currentHowl.playing()) {
                        currentHowl.play();
                        updateIcons(true);
                    }
                }
            });
            let isDraggingSeekBar = false;

            // When user starts dragging
            musicSeekBar.addEventListener("mousedown", () => isDraggingSeekBar = true);
            musicSeekBar.addEventListener("touchstart", () => isDraggingSeekBar = true);

            // When user releases
            musicSeekBar.addEventListener("mouseup", () => {
                isDraggingSeekBar = false;
                musicSeekBar.dispatchEvent(new Event("change")); // Trigger seek logic
            });
            musicSeekBar.addEventListener("touchend", () => {
                isDraggingSeekBar = false;
                musicSeekBar.dispatchEvent(new Event("change"));
            });
            volumeSlider.addEventListener("input", () => {
                if (currentHowl) currentHowl.volume(parseFloat(volumeSlider.value));
                updateBarFill(volumeSlider, volumeSlider.value * 100);
            });

            footerPlayPauseButton.addEventListener("click", () => {
                if (!currentHowl) return alert("Please select a song first.");
                currentHowl.playing() ? currentHowl.pause() : currentHowl.play();
                updateIcons(currentHowl.playing());
            });

            backwardBtn.addEventListener("click", () => {
                if (!currentHowl) return;
                const newTime = currentHowl.seek() - 10;
                if (newTime > 0) currentHowl.seek(newTime);
                else if (currentTrackIndex > 0) playTrack(currentTrackIndex - 1);
                else currentHowl.seek(0);
            });

            forwardBtn.addEventListener("click", () => {
                if (!currentHowl) return;
                if (currentTrackIndex < playlist.length - 1) playTrack(currentTrackIndex + 1);
                else playTrack(0);
            });
            $(document).on("click", ".album-play", function(e) {
                e.preventDefault();
                const albumId = $(this).data("album-id");

                // ✅ Stop previous song & reset player when switching album
                if (currentHowl) {
                    currentHowl.stop();
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

                currentPlayingAlbumBtn = this; // set new album button

                // ✅ Detect mobile view
                const isMobile = window.innerWidth <= 768;

                if (isMobile) {
                    $("#mobile-songs").html("<p class='text-gray-400'>Loading songs...</p>");
                } else {
                    $("#songs-list").html("<p class='text-gray-400'>Loading songs...</p>");
                }

                $.get(`{{ url('/album') }}/${albumId}/songs`, (data) => {
                    const htmlContent = data.html; // use the HTML part

                    if (isMobile) {
                        const $target = $("#mobile-songs");

                        if ($target.length) {
                            $("html, body").animate({
                                scrollTop: $target.offset().top
                            }, 300);
                        }

                    } else {
                        $("#songs-list").html(htmlContent);
                    }

                    buildPlaylist(); // rebuild playlist
                    document.querySelector(".play-song")?.click(); // autoplay first song
                });
            });


            $(document).on("click", ".song-play", function(e) {
                e.preventDefault();
                const albumId = $(this).data("album-id");
                const songId = $(this).data("song-id");

                // ✅ Stop previous song & reset player when switching album
                if (currentHowl) {
                    currentHowl.stop();
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

                currentPlayingAlbumBtn = this; // set new album button

                // ✅ Detect mobile view
                const isMobile = window.innerWidth <= 768;

                if (isMobile) {
                    $("#mobile-songs").html("<p class='text-gray-400'>Loading songs...</p>");
                } else {
                    $("#songs-list").html("<p class='text-gray-400'>Loading songs...</p>");
                }

                $.get(`{{ url('/songs') }}/${songId}`, (data) => {
                    const htmlContent = data.html; // use the HTML part

                    if (isMobile) {

                        const $target = $("#mobile-songs");

                        if ($target.length) {
                            $("html, body").animate({
                                scrollTop: $target.offset().top
                            }, 300);
                        }
                    } else {
                        $("#songs-list").html(htmlContent);
                    }

                    buildPlaylist(); // rebuild playlist
                    document.querySelector(".play-song")?.click(); // autoplay first song
                });
            });



            buildPlaylist();
            updateBarFill(volumeSlider, volumeSlider.value * 100);

            const observer = new MutationObserver(() => buildPlaylist());
            observer.observe(document.getElementById("main-content"), {
                childList: true,
                subtree: true
            });


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
                                thumbnail: btn.data("thumbnail"),
                                duration: parseInt(btn.data("duration")),
                                button: this
                            };

                            // RESET PLAYER before switching
                            if (currentHowl) {
                                currentHowl.unload(); // ⬅ unload to fully destroy sound
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
            loadPageContent(url, true);
        });

        // Back / forward navigation
        window.addEventListener("popstate", function(e) {
            let url = window.location.href;
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
                const albumId = $(this).data("album-id");
                // const shareUrl = window.location.origin + "/album/" + albumId;
                const shareUrl = "{{ config('app.website_url') }}UAT/public/album/" + albumId + "/songs-list";

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

            let button = $(this);
            let songId = button.data("song-id");

            $.ajax({
                url: "{{ url('/song') }}/" + songId + "/wishlist",
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
                        showToast("✅ Song added to wishlist!");
                    } else if (data.status === "removed") {
                        // Change heart icon back to outline
                        button.find("i")
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
                                $("#album-div").removeClass('d-none');
                                $(".mobile-songs-list").addClass('d-none');

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
                                $(".songs-wishlist-section").remove();
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
    </script>

    <script>
        let page = 1;
        let loading = false;
        const tableContainer = document.querySelector("#main-content");

        tableContainer.addEventListener("scroll", function() {
            const {
                scrollTop,
                scrollHeight,
                clientHeight
            } = tableContainer;
            if (!loading && scrollTop + clientHeight >= scrollHeight - 10) {
                // Add a delay of 1 second (1000ms) before loading
                loading = true;
                const indicator = document.getElementById("load-more-indicator");
                if (indicator) {
                    indicator.classList.remove("hidden");

                    setTimeout(() => {
                        loadMoreSongs();
                    }, 1000); // change 1000 to however many ms you want
                }
            }
        });

        function loadMoreSongs() {
            page++;
            const indicator = document.getElementById("load-more-indicator");

            $.get(`${window.location.pathname}?page=${page}`, function(res) {

                if (!res.desktop && !res.mobile) {
                    indicator.innerText = "";
                    return;
                }

                // Desktop table rows
                $("#songs-table-body").append(res.desktop);

                // Mobile cards
                $("#songs-card-body").append(res.mobile);

                indicator.classList.add("hidden");
                loading = false;
            });
        }
    </script>

</body>

</html>
