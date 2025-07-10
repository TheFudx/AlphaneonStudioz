<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alphanon Studio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            --color-theme: #FF3A1F; /* Define theme color for easier use */
        }

        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: var(--color-dark);
            /* Apply the dark background */
            display: flex; /* Make body a flex container */
            flex-direction: column; /* Stack children vertically */
            min-height: 100vh; /* Ensure body takes full viewport height */
        }

        .bg-dark {
            background-color: var(--color-dark);
        }

        .bg-dark-light {
            background-color: var(--color-dark-light);
        }

        .hover\:bg-gray-700:hover {
            /* Custom hover for nav items */
            background-color: var(--color-gray-700-custom);
        }

        .text-gray-400 {
            /* Custom text color for certain elements */
            color: var(--color-gray-400-custom);
        }

        .text-theme { /* Added for the theme color usage */
            color: var(--color-theme);
        }

        /* Custom scrollbar for better aesthetics */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--color-dark-light);
            /* Darker gray for the track */
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #4A5568;
            /* Even darker gray for the thumb */
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #606f80;
        }

        .aside_logo {
            height: 62px;
            width: 175px;
        }

        .bg-theame {
            background-color: var(--color-theme); /* Use theme variable */
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

        /* Specific styles for the album play button */
        .album-play-button {
            width: 60px;
            height: 60px;
            font-size: 1.8rem;
        }

        .table-play-pause-button {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }

        .table-image {
            height: 35px;
            width: 35px;
            display: inline;
            border-radius: 4px; /* Slightly rounded corners for the image */
        }

        /* Styles for the fixed footer player */
        .fixed-footer-player {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: var(--color-dark-light); /* Darker background for player */
            border-top: 1px solid #374151; /* Subtle top border */
            z-index: 1000; /* Ensure it stays on top */
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem; /* Space between sections */
        }

        /* Padding for the main content to avoid being hidden by the footer */
        main {
            padding-bottom: 96px; /* Adjust this value based on your footer's height */
                                  /* Footer height (approx 64px) + some extra for breathing room */
        }

        /* Seek bar styling */
        #music-seek-bar {
            width: 100%;
            height: 4px;
            background-color: #4A5568;
            border-radius: 2px;
            -webkit-appearance: none; /* Remove default styling */
            appearance: none;
            cursor: pointer;
        }

        #music-seek-bar::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 12px;
            height: 12px;
            background-color: var(--color-theme);
            border-radius: 50%;
            cursor: pointer;
            margin-top: -4px; /* Center thumb vertically */
        }

        #music-seek-bar::-moz-range-thumb {
            width: 12px;
            height: 12px;
            background-color: var(--color-theme);
            border-radius: 50%;
            cursor: pointer;
        }

        /* Volume slider styling */
        #volume-slider {
            width: 100px; /* Fixed width for volume slider */
            height: 4px;
            background-color: #4A5568;
            border-radius: 2px;
            -webkit-appearance: none;
            appearance: none;
            cursor: pointer;
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
        }

        #volume-slider::-moz-range-thumb {
            width: 12px;
            height: 12px;
            background-color: var(--color-theme);
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
    @yield('style')
</head>

<body class="bg-dark text-white">
    <aside class="w-64 bg-dark-light p-6 flex flex-col md:flex-shrink-0 md:block hidden min-h-screen overflow-y-auto">
        <div class="mb-8">
            <a href="#" class="flex items-center space-x-2 text-white">
                <img src="{{ url('/') }}/asset/images/logo.png" alt="Alphanon Studio Logo" class="aside_logo">
            </a>
        </div>

        <nav class="space-y-4 mb-8">
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">Discover</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="#"
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200 text-theme font-semibold">
                            <i class="fas fa-compact-disc"></i>
                            <span>Popular Albums</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="bg-dark-light p-4 flex items-center justify-between shadow-md z-10">
            <div class="relative flex-1 max-w-lg mx-auto md:mx-0">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search"
                    class="w-full bg-dark rounded-full py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-theme transition-all duration-200">
            </div>

            <div class="flex items-center space-x-4 ml-auto">
                <a href="#" class="text-gray-400 hover:text-white transition duration-200 relative">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
                </a>

                <a href="#" class="flex items-center space-x-2">
                    @php
                        // Dummy user data for demonstration, replace with actual app('logged-in-user') logic
                        $loggedInUser = (object)['name' => 'Demo User', 'email' => 'demo@example.com', 'profile_picture' => null];
                        // If you have a real user, uncomment this:
                        // $loggedInUser = app('logged-in-user');
                    @endphp
                    @if ($loggedInUser->profile_picture)
                        <img src="{{ asset('storage/profile_pictures/' . $loggedInUser->profile_picture) }}"
                            alt="User Avatar" class="h-9 w-9 rounded-full border-2 border-theme">
                    @else
                        <img src="https://placehold.co/36x36/6B46C1/FFFFFF?text={{ $loggedInUser->email ? strtoupper(substr($loggedInUser->email, 0, 1)) : strtoupper(substr($loggedInUser->name, 0, 1)) }}"
                            alt="User Avatar" class="h-9 w-9 rounded-full border-2 border-theme">
                    @endif
                    <span class="font-semibold text-sm hidden sm:block">{{ $loggedInUser->name }}</span>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition duration-200 hidden md:block">
                    <i class="fas fa-chevron-down text-sm"></i>
                </a>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 space-y-8 bg-dark">
            <nav class="text-sm text-gray-400">
                <a href="#" class="hover:text-white">Popular Albums</a> &gt;
                <span class="text-theme">All Albums</span>
            </nav>
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Popular Albums</h2>
                    <a href="#" class="text-theme hover:underline text-sm">View All &gt;</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-6">
                   

                    @foreach ($musics as $m)
                        <div class="bg-dark-light rounded-lg shadow-lg overflow-hidden relative group">
                            <img src="{{ $m->thumbnail_url }}" alt="{{ $m->title }} Album Cover"
                                class="w-full h-55 object-cover rounded-t-lg">
                            <div
                                class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <button
                                    class="bg-theame text-white shadow-lg transition duration-300 transform scale-100 hover:scale-110 album-play-button"
                                    data-audio-src="{{ $m->music_file }}"
                                    data-title="{{ $m->title }}"
                                    data-artist="{{ $m->artists->name }}"
                                    data-thumbnail="{{ $m->thumbnail_url }}"
                                >
                                    <i class="fas fa-play text-lg"></i>
                                </button>
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold truncate">{{ $m->title }}</h3>
                                <p class="text-gray-400 text-sm">{{ $m->artists->name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Popular Tracks</h2>
                    <a href="#" class="text-theme hover:underline text-sm">View All &gt;</a>
                </div>
                <div class="bg-dark-light rounded-lg shadow-lg p-4">
                    <table class="w-full text-left table-auto">
                        <thead>
                            <tr class="text-gray-400 uppercase text-xs">
                                <th class="py-2 px-4">#</th>
                                <th></th>
                                <th class="py-2 px-4">Title</th>
                                <th class="py-2 px-4">Artist</th>
                                <th class="py-2 px-4 hidden md:table-cell">Album</th>
                                <th class="py-2 px-4 text-right hidden lg:table-cell">Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($musics as $m)
                                <tr class="border-b border-gray-700 hover:bg-gray-700 transition duration-200">
                                    <td class="py-3 px-4 flex items-center space-x-3">
                                        <span class="text-gray-400">{{ $loop->iteration }}</span>
                                    </td>
                                    <td>
                                        <button class="bg-theame text-white table-play-pause-button"
                                            data-audio-src="{{ $m->music_file }}"
                                            data-title="{{ $m->title }}"
                                            data-artist="{{ $m->artists->name }}"
                                            data-thumbnail="{{ $m->thumbnail_url }}"
                                        >
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </td>
                                    <td class="py-3 px-4 font-semibold"> <span><img src="{{ $m->thumbnail_url }}"
                                                class="table-image" alt="Thumb"></span> &nbsp;&nbsp;{{ $m->title }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-400">{{ $m->artists->name }}</td>
                                    <td class="py-3 px-4 text-gray-400 hidden md:table-cell">{{ $m->album_name }}</td>
                                    <td class="py-3 px-4 text-right text-gray-400 hidden lg:table-cell">
                                        {{ MillisecondsToTimeForMusic($m->duration) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

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

    <script>
        // Global variables for the audio player state
        let currentAudioInstance = null;
        let currentPlayingButton = null; // Button that initiated playback (album or table)
        let footerPlayPauseButton = document.getElementById('footer-play-pause-btn'); // The main play/pause button in the footer
        let footerPlayPauseIcon = footerPlayPauseButton.querySelector('i');
        let playerTitle = document.getElementById('player-title');
        let playerArtist = document.getElementById('player-artist');
        let playerThumbnail = document.getElementById('player-thumbnail');
        let musicSeekBar = document.getElementById('music-seek-bar');
        let currentTimeSpan = document.getElementById('current-time');
        let totalDurationSpan = document.getElementById('total-duration');
        let volumeSlider = document.getElementById('volume-slider');

        document.addEventListener('DOMContentLoaded', () => {
            const allPlayButtons = document.querySelectorAll('.album-play-button, .table-play-pause-button');

            // Function to update the footer player UI
            function updateFooterPlayerUI(musicData, isPlaying) {
                playerTitle.textContent = musicData.title;
                playerArtist.textContent = musicData.artist;
                playerThumbnail.src = musicData.thumbnail;

                if (isPlaying) {
                    footerPlayPauseIcon.classList.remove('fa-play');
                    footerPlayPauseIcon.classList.add('fa-pause');
                } else {
                    footerPlayPauseIcon.classList.remove('fa-pause');
                    footerPlayPauseIcon.classList.add('fa-play');
                }
            }

            // Function to reset all play buttons to 'play' icon
            function resetAllPlayButtons() {
                allPlayButtons.forEach(btn => {
                    const icon = btn.querySelector('i');
                    icon.classList.remove('fa-pause');
                    icon.classList.add('fa-play');
                });
            }

            // Unified play/pause handler
            async function handlePlayback(button, audioSrc, musicData) {
                const iconElement = button.querySelector('i');

                // If a song is currently playing AND it's a different song
                if (currentAudioInstance && currentAudioInstance.src !== audioSrc) {
                    currentAudioInstance.pause();
                    resetAllPlayButtons(); // Reset all other buttons
                    currentAudioInstance = null; // Clear previous instance
                    currentPlayingButton = null;
                }

                // If the same song is clicked again (toggle play/pause)
                if (currentAudioInstance && currentAudioInstance.src === audioSrc) {
                    if (currentAudioInstance.paused) {
                        try {
                            await currentAudioInstance.play();
                            iconElement.classList.remove('fa-play');
                            iconElement.classList.add('fa-pause');
                            updateFooterPlayerUI(musicData, true);
                        } catch (error) {
                            console.error('Playback failed on resume:', error);
                            alert('Error resuming audio.');
                        }
                    } else {
                        currentAudioInstance.pause();
                        iconElement.classList.remove('fa-pause');
                        iconElement.classList.add('fa-play');
                        updateFooterPlayerUI(musicData, false);
                    }
                    return; // Exit after handling toggle
                }

                // If no song is playing or it's a new song
                currentAudioInstance = new Audio(audioSrc);
                currentPlayingButton = button; // Store the button that initiated playback

                // Set initial volume from slider
                currentAudioInstance.volume = volumeSlider.value;

                // Event Listeners for the new audio instance
                currentAudioInstance.onended = () => {
                    resetAllPlayButtons();
                    footerPlayPauseIcon.classList.remove('fa-pause');
                    footerPlayPauseIcon.classList.add('fa-play');
                    playerTitle.textContent = 'Not Playing';
                    playerArtist.textContent = 'Select a song';
                    playerThumbnail.src = 'https://placehold.co/60x60/374151/FFFFFF?text=Album'; // Reset thumbnail
                    currentTimeSpan.textContent = '00:00';
                    totalDurationSpan.textContent = '00:00';
                    musicSeekBar.value = 0;
                    currentAudioInstance = null;
                    currentPlayingButton = null;
                };

                currentAudioInstance.onerror = (e) => {
                    console.error('Audio playback error:', e);
                    alert('Error playing audio. Please check the file or URL.');
                    resetAllPlayButtons(); // Reset all buttons on error
                    footerPlayPauseIcon.classList.remove('fa-pause');
                    footerPlayPauseIcon.classList.add('fa-play');
                    currentAudioInstance = null;
                    currentPlayingButton = null;
                };

                currentAudioInstance.ontimeupdate = () => {
                    if (currentAudioInstance.duration) {
                        const progress = (currentAudioInstance.currentTime / currentAudioInstance.duration) * 100;
                        musicSeekBar.value = progress;
                        currentTimeSpan.textContent = formatTime(currentAudioInstance.currentTime);
                    }
                };

                currentAudioInstance.onloadedmetadata = () => {
                    totalDurationSpan.textContent = formatTime(currentAudioInstance.duration);
                    musicSeekBar.max = 100; // Max will always be 100% for progress
                };

                // Attempt to play
                try {
                    await currentAudioInstance.play();
                    resetAllPlayButtons(); // Reset all others before setting current one
                    iconElement.classList.remove('fa-play');
                    iconElement.classList.add('fa-pause');
                    updateFooterPlayerUI(musicData, true);
                } catch (error) {
                    console.error('Playback failed:', error);
                    alert('An error occurred while trying to play the audio. Please try again.');
                    iconElement.classList.remove('fa-pause');
                    iconElement.classList.add('fa-play'); // Revert icon on failure
                    currentAudioInstance = null;
                    currentPlayingButton = null;
                }
            }

            // Event listeners for album and table buttons
            allPlayButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const audioSrc = button.dataset.audioSrc;
                    const musicData = {
                        title: button.dataset.title,
                        artist: button.dataset.artist,
                        thumbnail: button.dataset.thumbnail
                    };
                    handlePlayback(button, audioSrc, musicData);
                });
            });

            // Event listener for the footer play/pause button
            footerPlayPauseButton.addEventListener('click', async () => {
                if (currentAudioInstance) {
                    const musicData = {
                        title: playerTitle.textContent,
                        artist: playerArtist.textContent,
                        thumbnail: playerThumbnail.src
                    };
                    handlePlayback(currentPlayingButton, currentAudioInstance.src, musicData);
                } else {
                    alert('Please select a song from the list to play.');
                }
            });

            // Seek bar functionality
            let isSeeking = false;
            musicSeekBar.addEventListener('mousedown', () => {
                isSeeking = true;
            });
            musicSeekBar.addEventListener('mouseup', () => {
                isSeeking = false;
                if (currentAudioInstance) {
                    const seekTime = (currentAudioInstance.duration / 100) * musicSeekBar.value;
                    currentAudioInstance.currentTime = seekTime;
                }
            });
            musicSeekBar.addEventListener('input', () => {
                if (currentAudioInstance && isSeeking) {
                    currentTimeSpan.textContent = formatTime((currentAudioInstance.duration / 100) * musicSeekBar.value);
                }
            });

            // Volume slider functionality
            volumeSlider.addEventListener('input', () => {
                if (currentAudioInstance) {
                    currentAudioInstance.volume = volumeSlider.value;
                }
            });

            // Helper function to format time (e.g., 123 seconds -> 02:03)
            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = Math.floor(seconds % 60);
                return `${minutes < 10 ? '0' : ''}${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
            }
        });
    </script>
</body>

</html>