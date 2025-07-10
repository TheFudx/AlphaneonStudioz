@extends('musicplayer.partials.main')
@section('content')
    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <div class="flex-1 overflow-y-auto p-6 space-y-8 bg-dark">
            <!-- Breadcrumbs / Navigation -->
            <nav class="text-sm text-gray-400">
                <a href="#" class="hover:text-white">Popular Albums</a> &gt;
                <span class="text-theme">All Albums</span>
            </nav>
            <!-- Popular Albums Section -->
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
                            <!-- Play Button Overlay -->
                            <div
                                class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <button
                                    class="bg-theame text-white shadow-lg transition duration-300 transform scale-100 hover:scale-110 album-play-button"
                                    data-audio-src="{{ $m->music_file }}"
                                    data-title="{{ $m->title }}"
                                    data-artist="{{ $m->artists->name }}"
                                    data-thumbnail="{{ $m->thumbnail_url }}"">
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
            <!-- Popular Tracks Section -->
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
                                            data-audio-src="{{ $m->music_file }}">
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
@endsection
@section('script')
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
@endsection
