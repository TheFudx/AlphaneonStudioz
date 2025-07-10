@extends('musicplayer.partials.main')
@section('content')
    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto">
     
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
@endsection
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const allPlayButtons = document.querySelectorAll('.album-play-button, .table-play-pause-button');
            let currentAudioInstance = null;
            let currentPlayingButton = null;

            allPlayButtons.forEach(button => {
                button.addEventListener('click', async () => {
                    const audioSrc = button.dataset.audioSrc;
                    const iconElement = button.querySelector('i');

                    // If there's an audio playing and it's *not* the one we just clicked
                    if (currentAudioInstance && currentAudioInstance.src !== audioSrc) {
                        currentAudioInstance.pause();
                        if (currentPlayingButton) {
                            const prevIcon = currentPlayingButton.querySelector('i');
                            prevIcon.classList.remove('fa-pause');
                            prevIcon.classList.add('fa-play');
                        }
                    }

                    // If the clicked button is already playing the current audio, pause it
                    if (currentAudioInstance && currentAudioInstance.src === audioSrc && !
                        currentAudioInstance.paused) {
                        currentAudioInstance.pause();
                        iconElement.classList.remove('fa-pause');
                        iconElement.classList.add('fa-play');
                        currentAudioInstance = null;
                        currentPlayingButton = null;
                        return;
                    }

                    // Create a new Audio instance if none exists or if it's a new track
                    if (!currentAudioInstance || currentAudioInstance.src !== audioSrc) {
                        currentAudioInstance = new Audio(audioSrc);
                        currentPlayingButton = button;

                        currentAudioInstance.onended = () => {
                            if (currentPlayingButton) {
                                const endedIcon = currentPlayingButton.querySelector('i');
                                endedIcon.classList.remove('fa-pause');
                                endedIcon.classList.add('fa-play');
                            }
                            currentAudioInstance = null;
                            currentPlayingButton = null;
                        };

                        currentAudioInstance.onerror = (e) => {
                            console.error('Audio playback error:', e);
                            console.error('Audio source:', audioSrc);
                            alert('Error playing audio. Please check the file or URL.');
                            if (currentPlayingButton) {
                                const errorIcon = currentPlayingButton.querySelector('i');
                                errorIcon.classList.remove('fa-pause');
                                errorIcon.classList.add('fa-play');
                            }
                            currentAudioInstance = null;
                            currentPlayingButton = null;
                        };
                    }

                    // Attempt to play the audio
                    try {
                        await currentAudioInstance.play();
                        iconElement.classList.remove('fa-play');
                        iconElement.classList.add('fa-pause');
                    } catch (error) {
                        console.error('Playback failed:', error);
                        console.error('Audio source that failed:', audioSrc);
                        iconElement.classList.remove('fa-pause');
                        iconElement.classList.add('fa-play');
                        currentAudioInstance = null;
                        currentPlayingButton = null;

                        if (error.name === 'NotSupportedError') {
                            alert(
                                'This audio format might not be supported by your browser, or the file is corrupted.'
                                );
                        } else if (error.name === 'NotAllowedError') {
                            alert(
                                'Playback was prevented by the browser (autoplay policy). Please interact with the page first.'
                                );
                        } else {
                            alert('An unexpected error occurred during playback.');
                        }
                    }
                });
            });
        });
    </script>
@endsection
