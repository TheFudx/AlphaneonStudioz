@extends('layouts.main')
@section('title')
    Alphastudioz | Khulps Play
@endsection
@section('styles')
    <style>
        /* Base styles for body and scrollbar hiding */
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
        }
        body::-webkit-scrollbar { /* Chrome, Safari, Opera */
            display: none;
        }

        /* Hide header when in fullscreen mode */
        body.fullscreen-active header {
            display: none;
        }
        body.fullscreen-active footer{
            display: none;
        }
        /* Adjust main page wrapper for fullscreen */
        body.fullscreen-active .main-page-wrapper {
            margin-top: 0 !important;
            height: 100vh !important;
        }
        body.fullscreen-active .shorts-container.fullscreen-swiper-mode {
            height: 100vh;
        }

        /* Hide page loader elements */
        #page-loader {
            display: none;
        }
        #page-loader img {
            display: none;
        }

        /* Main container for the shorts player */
        .main-page-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            margin-top: 70px; /* Space for a hypothetical header */
        }
        .shorts-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 350px; /* Standard width for shorts feed */
            margin: 0 auto;
            background-color: #000;
            overflow-y: scroll;
            height: calc(100vh - 60px); /* Adjust height considering header */
            scroll-snap-type: y mandatory; /* Vertical scroll snapping */
            -ms-overflow-style: none;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }
        .shorts-container::-webkit-scrollbar {
            display: none;
        }

        /* Fullscreen mode for the shorts container (vertical reel) */
        .shorts-container.fullscreen-swiper-mode {
            align-items: center;
            position: fixed;
            top: 0;
            left: 0; /* Align to left edge */
            right: 0; /* Align to left edge */
            transform: none; /* No horizontal translation needed */
            width: 100vw; /* Full viewport width */
            max-width:450px; /* Ensure it takes full width */
            height: 100vh;
            z-index: 999;
            /*margin: 0;  No margin in fullscreen */
            flex-direction: column !important; /* Maintain vertical layout */
            overflow-y: scroll !important; /* Enable vertical scroll */
            overflow-x: hidden !important; /* Disable horizontal scroll */
            scroll-snap-type: y mandatory !important; /* Vertical snap */
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
        .shorts-container.fullscreen-swiper-mode::-webkit-scrollbar {
            display: none;
        }

        /* Video card in fullscreen mode */
        .shorts-container.fullscreen-swiper-mode .video-card {
            width: 100%; /* Take full width of its parent (shorts-container) */
            height: 100vh; /* Each card takes full viewport height */
            scroll-snap-align: start; /* Snap to the top of the card */
            position: relative;
            overflow-y: auto; /* Allow content within card to scroll if needed */
            -webkit-overflow-scrolling: touch;
            flex-shrink: 0; /* Prevent cards from shrinking */
        }

        /* Individual video card styling */
        .video-card {
            width: 100%;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #000;
            scroll-snap-align: start;
            flex-shrink: 0;
            height: 100%;
            position: relative;
        }
        .video-player {
            width: 100%;
            position: relative;
            padding-top: 177.77%; /* 9:16 aspect ratio (16 / 9 * 100%) */
            background-color: black;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        /* Video player in fullscreen mode */
        .shorts-container.fullscreen-swiper-mode .video-player {
            width: 100%;
            height: 100vh;
            max-height: 100vh;
            padding-top: 0;
            flex-shrink: 0;
        }
        .video-player video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain; /* Ensure video fits within player without cropping */
            display: block;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .video-player video.loaded {
            opacity: 1;
        }

        /* Video information overlay */
        .video-info {
            width: 100%;
            padding: 0 20px 20px;
            box-sizing: border-box;
            color: white;
            text-align: left;
            position: absolute;
            bottom: 0;
            left: 0;
            z-index: 5;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0) 100%);
            padding-top: 80px;
            font-size: 3.5vw;
        }
        .video-info h3 {
            margin: 0;
            font-size: 1.2em;
            color: white;
            margin-bottom: 5px;
        }
        .video-info p {
            margin: 0;
            font-size: 13px;
            color: #bbb;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .video-info .profile-img {
            height: 32px;
            width: 32px;
            border-radius: 50%;
            margin-right: 8px;
            vertical-align: middle;
        }

        /* Responsive adjustments for video info font size */
        @media (max-width: 320px) {
            .video-info {
                font-size: 4.5vw;
            }
        }
        @media (min-width: 600px) {
            .shorts-container {
                max-width: 325px;
            }
            .video-info {
                font-size: 16px;
            }
        }

        /* Video info positioning in fullscreen mode */
        .shorts-container.fullscreen-swiper-mode .video-card .video-info {
            padding-left: 20px;
            padding-right: 20px;
            width: 100%;
            bottom: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0) 100%);
        }

        /* Loading spinner styling */
        /* .loading-spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #fff;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            position: absolute;
            z-index: 10;
        } */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Mute button styling */
        .mute-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 6;
        }
        .mute-button svg {
            fill: white;
            width: 20px;
            height: 20px;
        }

        /* Video controls styling */
        .video-controls {
            position: absolute;
            bottom: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 7;
        }

        /* Mute button and video controls positioning in fullscreen mode */
        .shorts-container.fullscreen-swiper-mode .mute-button {
            top: 30px;
            right: 50px !important; /* Consistent padding from right edge */
        }
        .shorts-container.fullscreen-swiper-mode .video-controls {
            bottom: 70px;
            right: 50px !important; /* Consistent padding from right edge */
        }

        /* Individual control button styling */
        .control-button {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .control-button:hover {
            background: rgba(0, 0, 0, 0.8);
        }
        .control-button svg {
            fill: white;
            width: 24px;
            height: 24px;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 480px) {
            .shorts-container {
                max-width: 100vw !important;
                height: 85% !important;
                padding: 0;
            }
            .video-info {
                font-size: 4vw;
                padding: 0px 16px 20px;
            }
            .mute-button,
            .control-button {
                width: 36px;
                height: 36px;
            }
            .mute-button svg,
            .control-button svg {
                width: 18px;
                height: 18px;
            }
            .shorts-container.fullscreen-swiper-mode  {
                left: 0;
                right: 0;
                max-width: 100vw !important;
                height: 100% !important;
            }
            .shorts-container.fullscreen-swiper-mode .video-info {
                padding-left: 20px !important;
                padding-right: 16px !important;
                padding-bottom: 90px !important;
                width: 100% !important;
            }
             .shorts-container.fullscreen-swiper-mode .video-info{
                font-size: 13px !important ;
             }
            .shorts-container.fullscreen-swiper-mode .mute-button,
            .shorts-container.fullscreen-swiper-mode .video-controls {
                right: 6px !important;
                bottom:  119px !important;
            }
        }

        @media (min-width: 481px) and (max-width: 991px) {
            .video-info {
                font-size: 1rem;
                padding: 80px 24px 24px;
            }
            .shorts-container.fullscreen-swiper-mode .video-info {
                padding-left: 24px;
                padding-right: 24px;
                width: 100%;
            }
            .shorts-container.fullscreen-swiper-mode .mute-button,
            .shorts-container.fullscreen-swiper-mode .video-controls {
                right: 24px;
            }
        }

        @media (min-width: 992px) {
            .shorts-container {
                max-width: 375px;
            }
            .video-info {
                font-size: 16px;
                padding-left: 24px;
                padding-right: 24px;
            }
            .shorts-container.fullscreen-swiper-mode .video-info {
                padding-left: 20px;
                padding-right: 20px;
                width: 100%;
                bottom: 0;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0) 100%);
            }
            /* Adjust controls for larger screens in fullscreen to be relative to the centered video */
            .shorts-container.fullscreen-swiper-mode .mute-button,
            .shorts-container.fullscreen-swiper-mode .video-controls {
                /* This calculation centers the controls relative to the max-width of the shorts-container */
                right: calc(50vw - 187.5px + 20px);
            }
        }
    </style>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const shortsContainer = document.querySelector('.shorts-container');
            const videoCards = document.querySelectorAll('.video-card');
            const body = document.body;
            let currentPlayingVideo = null;
            let globalMutedState = true;
            /**
             * Updates the play/pause icon based on video state.
             * @param {HTMLVideoElement} videoElement The video element.
             * @param {SVGElement} playBtn The play icon SVG.
             * @param {SVGElement} pauseBtn The pause icon SVG.
             */
            const updatePlayPauseIcon = (videoElement, playBtn, pauseBtn) => {
                if (videoElement.paused) {
                    playBtn.style.display = 'block';
                    pauseBtn.style.display = 'none';
                } else {
                    playBtn.style.display = 'none';
                    pauseBtn.style.display = 'block';
                }
            };

            /**
             * Updates the mute/unmute icon based on video state.
             * @param {HTMLVideoElement} videoElement The video element.
             * @param {HTMLElement} muteBtn The mute button container.
             * @param {SVGElement} volumeOnIcon The volume on icon SVG.
             * @param {SVGElement} volumeOffIcon The volume off icon SVG.
             */
            // const updateMuteIcon = (videoElement, muteBtn, volumeOnIcon, volumeOffIcon) => {
            //     if (videoElement.muted) {
            //         volumeOnIcon.style.display = 'none';
            //         volumeOffIcon.style.display = 'block';
            //         muteBtn.dataset.muted = 'true';
            //     } else {
            //         volumeOnIcon.style.display = 'block';
            //         volumeOffIcon.style.display = 'none';
            //         muteBtn.dataset.muted = 'false';
            //     }
            // };
            const updateMuteIcon = (muteBtn, isMuted) => {
                const volumeOnIcon = muteBtn.querySelector('.volume-on');
                const volumeOffIcon = muteBtn.querySelector('.volume-off');

                if (isMuted) {
                    volumeOnIcon.style.display = 'none';
                    volumeOffIcon.style.display = 'block';
                    muteBtn.dataset.muted = 'true';
                } else {
                    volumeOnIcon.style.display = 'block';
                    volumeOffIcon.style.display = 'none';
                    muteBtn.dataset.muted = 'false';
                }
            };
            /**
             * Plays a video and handles UI updates. Pauses any previously playing video.
             * @param {HTMLVideoElement} videoElement The video element to play.
             */
            const playVideo = (videoElement) => {
                // If there's a video currently playing and it's not the one we want to play, pause it.
                if (currentPlayingVideo && currentPlayingVideo !== videoElement) {
                    pauseVideo(currentPlayingVideo);
                }

                const card = videoElement.closest('.video-card');
                if (!card) return;

                // const spinner = card.querySelector('.loading-spinner');
                const playIcon = card.querySelector('.play-icon');
                const pauseIcon = card.querySelector('.pause-icon');

                //spinner.style.display = 'block'; // Show spinner while loading/buffering
                videoElement.classList.remove('loaded'); // Remove loaded class until it's actually playing

                // Attempt to play the video
                videoElement.play().then(() => {
                    // spinner.style.display = 'none';
                    videoElement.classList.add('loaded');
                    updatePlayPauseIcon(videoElement, playIcon, pauseIcon);
                    currentPlayingVideo = videoElement; // Set the new current playing video
                }).catch(e => {
                    console.error("Video play failed:", e);
                    // spinner.style.display = 'none'; // Hide spinner on error or autoplay block
                    // Ensure play icon is visible if autoplay was blocked
                    updatePlayPauseIcon(videoElement, playIcon, pauseIcon);
                });
            };

            /**
             * Pauses a video and handles UI updates.
             * @param {HTMLVideoElement} videoElement The video element to pause.
             */
            const pauseVideo = (videoElement) => {
                if (videoElement) {
                    videoElement.pause();
                    //videoElement.currentTime = 0; // Reset video to start for next play
                    const card = videoElement.closest('.video-card');
                    if (card) {
                        // const spinner = card.querySelector('.loading-spinner');
                        const playIcon = card.querySelector('.play-icon');
                        const pauseIcon = card.querySelector('.pause-icon');
                        //spinner.style.display = 'block'; // Show spinner briefly or until next play
                        //videoElement.classList.remove('loaded');
                        updatePlayPauseIcon(videoElement, playIcon, pauseIcon);
                    }
                }
            };

            // Intersection Observer for vertical scrolling
            const observerOptions = {
                root: shortsContainer,
                rootMargin: '0px',
                threshold: 0.8 // Video is considered "in view" when 80% visible
            };

            const videoObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const video = entry.target.querySelector('video');
                    if (!video) return;

                    // Play/pause based on intersection regardless of fullscreen mode
                    if (entry.isIntersecting) {
                        playVideo(video);
                    } else {
                        pauseVideo(video);
                    }
                });
            }, observerOptions);

            // Initialize all video cards
            videoCards.forEach((card) => {
                const video = card.querySelector('video');
                // const spinner = card.querySelector('.loading-spinner');
                const playPauseButton = card.querySelector('.play-pause-button');
                const playIcon = playPauseButton.querySelector('.play-icon');
                const pauseIcon = playPauseButton.querySelector('.pause-icon');
                const fullscreenButton = card.querySelector('.fullscreen-button');
                const fullscreenIcon = fullscreenButton.querySelector('.fullscreen-icon');
                const exitFullscreenIcon = fullscreenButton.querySelector('.exit-fullscreen-icon');
                const muteButton = card.querySelector('.mute-button');
                const volumeOnIcon = muteButton.querySelector('.volume-on');
                const volumeOffIcon = muteButton.querySelector('.volume-off');

                // Observe each video card for intersection
                videoObserver.observe(card);

                // Event listeners for video elements to manage spinner and icons
                video.addEventListener('canplaythrough', () => {
                    if (video === currentPlayingVideo) {
                        // spinner.style.display = 'none';
                        video.classList.add('loaded');
                        updatePlayPauseIcon(video, playIcon, pauseIcon);
                    }
                });

                video.addEventListener('waiting', () => {
                    // spinner.style.display = 'block';
                    video.classList.remove('loaded');
                });

                video.addEventListener('playing', () => {
                    // spinner.style.display = 'none';
                    video.classList.add('loaded');
                    updatePlayPauseIcon(video, playIcon, pauseIcon);
                });

                video.addEventListener('pause', () => {
                    updatePlayPauseIcon(video, playIcon, pauseIcon);
                });

                video.addEventListener('error', (e) => {
                    console.error('Video loading error:', e);
                    // spinner.style.display = 'none';
                });

                // Play/Pause button click handler
                playPauseButton.addEventListener('click', () => {
                    if (video.paused) {
                        playVideo(video);
                    } else {
                        pauseVideo(video);
                    }
                });

                // Mute/Unmute button click handler
                // muteButton.addEventListener('click', () => {
                //     video.muted = !video.muted;
                //     updateMuteIcon(video, muteButton, volumeOnIcon, volumeOffIcon);
                //     // If unmuting and paused, try to play
                //     if (!video.muted && video.paused) {
                //         playVideo(video);
                //     }
                // });

                muteButton.addEventListener('click', () => {
                    // Toggle the global mute state
                    globalMutedState = !globalMutedState;

                    // Apply the new global mute state to ALL videos and their respective mute buttons
                    document.querySelectorAll('video').forEach(vid => {
                        vid.muted = globalMutedState;
                        const btn = vid.closest('.video-card').querySelector('.mute-button');
                        updateMuteIcon(btn, globalMutedState);
                    });

                    // If unmuting and the current playing video is paused, try to play it
                    if (!globalMutedState && currentPlayingVideo && currentPlayingVideo.paused) {
                        playVideo(currentPlayingVideo);
                    }
                });

                // Fullscreen button click handler
                fullscreenButton.addEventListener('click', () => {
                    if (shortsContainer.classList.contains('fullscreen-swiper-mode')) {
                        // Exit simulated fullscreen
                        shortsContainer.classList.remove('fullscreen-swiper-mode');
                        body.classList.remove('fullscreen-active');
                        fullscreenIcon.style.display = 'block';
                        exitFullscreenIcon.style.display = 'none';

                        // The Intersection Observer will handle playing the correct video
                        // after exiting fullscreen and the scroll position settles.
                    } else {
                        // Enter simulated fullscreen
                        shortsContainer.classList.add('fullscreen-swiper-mode');
                        body.classList.add('fullscreen-active');
                        fullscreenIcon.style.display = 'none';
                        exitFullscreenIcon.style.display = 'block';

                        // Ensure the video is muted for autoplay to work reliably in most browsers
                        video.muted = true;
                        updateMuteIcon(video, muteButton, volumeOnIcon, volumeOffIcon);

                        // Play the video that just entered fullscreen
                        playVideo(video);
                    }
                });

                // Handle video ending for looping (already in loop attribute, but good for explicit control)
                video.addEventListener('ended', () => {
                    video.currentTime = 0;
                    video.play().catch(e => console.error("Loop play failed:", e));
                });
            });

            // No explicit scroll event listener needed for play/pause in fullscreen
            // because the Intersection Observer handles it for both normal and fullscreen modes.
        });
    </script>
@endsection
@section('main-section')
    <div class="main-page-wrapper">
        <div class="shorts-container">
            <!-- Example video data. Replace with your actual Laravel Blade loop if needed. -->
           @foreach($videos as $video) 
            <div class="video-card">
                <div class="video-player">
                    {{-- <div class="loading-spinner"></div> --}}
                    <!-- Replace src with actual video URLs from your backend -->
                    <video playsinline loop preload="auto" muted autoplay poster="{{$video->landscape_url}}">
                        <source src="{{$video->video_320_url}}" type="video/mp4" size="360">
                        <!-- Add more source tags for different resolutions if available -->
                        <source src="{{$video->video_480_url}}" type="video/mp4" size="480">
                        <source src="{{$video->video_720_url}}" type="video/mp4" size="720">
                        <source src="{{$video->video_1080_url}}" type="video/mp4" size="1080">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <div class="video-info">
                    <p>
                        @if(!$video->user->profile_picture == null)
                            <img src="{{ asset('storage/profile_pictures/' . $video->user->profile_picture) }}" class="profile-img img-fluid" alt="Profile Picture"> 
                        @else
                            <img src="{{ url('/') }}/asset/images/profile.png" class="profile-img img-fluid" alt="Profile Picture"> 
                        @endif
                        @ {{$video->user->name}}
                    </p>
                    <p><b>{{ $video->name }}</b></p>
                    <p>{!! $video->description !!}</p>
                </div>
                <div class="mute-button" data-muted="true">
                    <svg class="volume-off" viewBox="0 0 24 24">
                        <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .9-.2 1.75-.56 2.54l1.46 1.46C20.6 14.99 21 13.54 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27l9 9V19c0 1.38 1.12 2.5 2.5 2.5S17 20.38 17 19v-2.73l4.73 4.73L22 20.73 14.27 13 5 3.73 4.27 3zM14.5 5.9v-.63c-2.89.86-5 3.54-5 6.71v1.73l-2.06-2.06c-.3-.79-.44-1.65-.44-2.54 0-2.84 1.66-5.26 4.03-6.4z" />
                    </svg>
                    <svg class="volume-on" style="display:none;" viewBox="0 0 24 24">
                        <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z" />
                    </svg>
                </div>
                <div class="video-controls">
                    <div class="control-button play-pause-button">
                        <svg class="play-icon" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                        <svg class="pause-icon" style="display:none;" viewBox="0 0 24 24">
                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                        </svg>
                    </div>
                    <div class="control-button fullscreen-button">
                        <svg class="fullscreen-icon" viewBox="0 0 24 24">
                            <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z" />
                        </svg>
                        <svg class="exit-fullscreen-icon" style="display:none;" viewBox="0 0 24 24">
                            <path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H7v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z" />
                        </svg>
                    </div>
                </div>
            </div>
           
             {{-- @empty
                <p style="padding: 20px; text-align: center; color: white;">No shorts videos available right now.</p> --}}
            @endforeach
        </div>
    </div>
@endsection