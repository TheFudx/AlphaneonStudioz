@extends('layouts.main')
@section('title')
    Alphastudioz | Khulps Play
@endsection
@section('styles')
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
   <style>
    #page-loader{
        display: none;
    }
    #page-loader img{
        display: none
    }
   </style>
@endsection
@section('scripts')
  <script>
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function() {
                const videoId = this.dataset.videoId;
                fetch(`/khlup/${videoId}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.liked) {
                            this.textContent = 'Liked';
                        } else {
                            this.textContent = 'Like';
                        }
                        this.nextElementSibling.textContent = `Likes: ${data.likesCount}`;
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
        document.addEventListener('DOMContentLoaded', () => {
            const sliderContainer = document.querySelector('.slider-conttainer');
            // Fullscreen functionality
            document.addEventListener('click', (e) => {
                const fullscreenBtn = e.target.closest('.fullscreen-btn');
                const exitFullscreenBtn = e.target.closest('.exitfullscreen');
                const targetContainer = e.target.closest('.slider-conttainer'); // Target slider container
                if (fullscreenBtn) {
                    // Enter fullscreen for the specific .slider-conttainer
                    if (!document.fullscreenElement) {
                        if (targetContainer.requestFullscreen) {
                            targetContainer.requestFullscreen();
                        } else if (targetContainer.webkitRequestFullscreen) { // Safari
                            targetContainer.webkitRequestFullscreen();
                        } else if (targetContainer.msRequestFullscreen) { // IE/Edge
                            targetContainer.msRequestFullscreen();
                        }
                    }
                } else if (exitFullscreenBtn) {
                    // Exit fullscreen
                    if (document.fullscreenElement) {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        } else if (document.webkitExitFullscreen) { // Safari
                            document.webkitExitFullscreen();
                        } else if (document.msExitFullscreen) { // IE/Edge
                            document.msExitFullscreen();
                        }
                    }
                }
            });
            // Listen for fullscreen change and dynamically update button states
            document.addEventListener('fullscreenchange', () => {
                const fullscreenElements = document.querySelectorAll('.fullscreen-btn, .exitfullscreen');
                fullscreenElements.forEach((btn) => {
                    if (document.fullscreenElement) {
                        btn.classList.add('exitfullscreen');
                        btn.classList.remove('fullscreen-btn');
                    } else {
                        btn.classList.add('fullscreen-btn');
                        btn.classList.remove('exitfullscreen');
                    }
                });
            });
            // Initialize Swiper
            const swiper = new Swiper('.swiper-container', {
                direction: 'vertical',
                slidesPerView: 1,
                spaceBetween: 0,
                mousewheel: true,
                on: {
                    slideChangeTransitionEnd: () => {
                        // Pause all videos and reset play buttons
                        document.querySelectorAll('.shorts-player').forEach(video => video.pause());
                        document.querySelectorAll('.play-btn, .pause-btn').forEach(btn => {
                            btn.classList.remove('pause-btn');
                            btn.classList.add('play-btn');
                        });
                        // Play the current video
                        const currentSlide = document.querySelector('.swiper-slide-active video');
                        if (currentSlide) {
                            currentSlide.currentTime = 0; // Reset playback to the beginning
                            currentSlide.play();
                            // Update the play button for the current video
                            const playButton = document.querySelector('.swiper-slide-active .play-btn');
                            if (playButton) {
                                playButton.classList.remove('play-btn');
                                playButton.classList.add('pause-btn');
                            }
                        }
                    }
                }
            });
            // Auto-play the first video
            const firstSlide = document.querySelector('.swiper-slide-active');
            if (firstSlide) {
                const firstVideo = firstSlide.querySelector('video');
                // Case 1: HLS (transcoded video)
                if (firstVideo && firstVideo.tagName === 'VIDEO' && firstVideo.readyState < 3) {
                    firstVideo.addEventListener('canplay', () => {
                        firstVideo.play().then(() => {
                            const playBtn = firstSlide.querySelector('.play-btn, .pause-btn');
                            if (playBtn) {
                                playBtn.classList.remove('play-btn');
                                playBtn.classList.add('pause-btn');
                            }
                        }).catch(err => console.warn("Autoplay failed:", err));
                    });
                } else if (firstVideo) {
                    // Case 2: MP4 or already ready
                    firstVideo.play().then(() => {
                        const playBtn = firstSlide.querySelector('.play-btn, .pause-btn');
                        if (playBtn) {
                            playBtn.classList.remove('play-btn');
                            playBtn.classList.add('pause-btn');
                        }
                    }).catch(err => console.warn("Autoplay failed:", err));
                }
            }
            document.addEventListener('click', function(e) {
                const btn = e.target;
                if (btn.classList.contains('play-btn') || btn.classList.contains('pause-btn')) {
                    const video = btn.closest('.swiper-slide').querySelector('.shorts-player');
                    if (!video) return;
                    if (video.paused) {
                        video.play();
                        btn.classList.remove('play-btn');
                        btn.classList.add('pause-btn');
                    } else {
                        video.pause();
                        btn.classList.remove('pause-btn');
                        btn.classList.add('play-btn');
                    }
                }
            });
            // Volume control functionality
            document.querySelectorAll('.volume-control').forEach(control => {
                const button = control.querySelector('.unmute-btn');
                const slider = control.querySelector('.volume-slider');
                const video = control.closest('.swiper-slide').querySelector('.shorts-player');
                // Mute/Unmute functionality
                button.addEventListener('click', (e) => {
                    if (video.muted) {
                        video.muted = false;
                        e.target.classList.remove('mute-btn');
                        e.target.classList.add('unmute-btn');
                    } else {
                        video.muted = true;
                        e.target.classList.remove('unmute-btn');
                        e.target.classList.add('mute-btn');
                    }
                });
                // Adjust volume using the slider
                slider.addEventListener('input', (e) => {
                    video.volume = e.target.value;
                    if (video.volume === 0) {
                        video.muted = true;
                        button.classList.remove('unmute-btn');
                        button.classList.add('mute-btn');
                    } else {
                        video.muted = false;
                        button.classList.remove('mute-btn');
                        button.classList.add('unmute-btn');
                    }
                });
            });
        });
    </script>
@endsection
@section('main-section')


    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <section id="innerBanners" class="mt-3">
        <div class="inner-banner-section">
            <div class="inner-banner-holder">
                <div class="slider-conttainer pt-5">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            @dd($videos)
                            @foreach ($videos as $video)
                                @php
                                    // $landscap_url = asset('storage/userKhlups/' . $video->landscape_url);
                                    
                                    $landscap_url = $video->landscape_url;
                                    $video_320_url = $video->video_320_url;
                                    $video_480_url = $video->video_480_url;
                                    $video_720_url = $video->video_720_url;
                                    $video_1080_url = $video->video_1080_url;

                                    // $landscap_url = asset('storage/userKhlups/' . $video->landscape_url);
                                    // $video_320_url = asset('storage/userKhlups/' . $video->video_320_url);
                                    // $video_480_url = asset('storage/userKhlups/' . $video->video_480_url);
                                    // $video_720_url = asset('storage/userKhlups/' . $video->video_720_url);
                                    // $video_1080_url =asset('storage/userKhlups/' . $video->video_1080_url);
                                @endphp
                                <div class="swiper-slide">
                                    <div class="video-container">
                                        @if ($video->video_upload_type === 'transcoded')
                                            <video id="shorts-player-{{ $loop->index }}" class="shorts-player" loop
                                                playsinline preload="auto" poster="{{ $landscap_url }}">
                                                <!-- Fallback message for non-HLS compatible browsers -->
                                                <p>Your browser does not support HLS playback. Please update your browser.
                                                </p>
                                            </video>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', () => {
                                                    const videoElement = document.getElementById("shorts-player-{{ $loop->index }}");
                                                    const source =
                                                        `https://alphastudioz.in/admin_panel/public/storage/kluphs/{{ $video->transcoded_video_path }}/master.m3u8`;
                                                    if (Hls.isSupported()) {
                                                        const hls = new Hls();
                                                        hls.loadSource(source);
                                                        hls.attachMedia(videoElement);
                                                    } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
                                                        videoElement.src = source;
                                                    }
                                                });
                                            </script>
                                        @else
                                            <video id="shorts-player-{{ $loop->index }}" class="shorts-player" playsinline
                                                preload="auto" loop poster="{{ $landscap_url }}">
                                                <source src="{{ $video_320_url }}" type="video/mp4" size="360">
                                                <source src="{{ $video_480_url }}" type="video/mp4" size="480">
                                                <source src="{{ $video_720_url }}" type="video/mp4" size="720">
                                                <source src="{{ $video_1080_url }}" type="video/mp4" size="1080">
                                                Your browser does not support the video tag.
                                            </video>
                                        @endif
                                        <div class="video-small-screen-player video-large-screen-player">
                                            <div class="action-buttons">
                                                <div class="play-puase-button d-flex">
                                                    {{-- <button class="play-btn" type="button"></button> --}}
                                                    <button class="play-btn{{ $loop->first ? ' play-btn' : '' }}"
                                                        type="button"></button>
                                                    <div class="volume-control ms-3">
                                                        <button class="unmute-btn" type="button"></button>
                                                        <input type="range" class="volume-slider" min="0"
                                                            max="1" step="0.1" value="1" />
                                                    </div>
                                                    <button class="fullscreen-btn" type="button"></button>
                                                </div>
                                                <div class="socialite-buttons">
                                                    {{-- <button class="like-btn mt-3" data-video-id="{{ $video->id }}" type="button">Like</button>
                                                    <button class="dislike-btn mt-3" type="button"></button>
                                                    <button class="share-btn mt-3" type="button"></button>
                                                    <button class="comments-btn mt-3" type="button"></button> --}}
                                                </div>
                                                <div class="video-details">
                                                    <div class="profile">
                                                        <div class="d-flex">
                                                            <div class="">
                                                                @if (!$video->user->profile_picture == null)
                                                                    <img src="{{ asset('storage/profile_pictures/' . $video->user->profile_picture) }}"
                                                                        class="profile-img img-fluid" alt="Profile Picture">
                                                                @else
                                                                    <img src="{{ url('/') }}/asset/images/profile.png"
                                                                        class="profile-img img-fluid" alt="">
                                                                @endif
                                                            </div>
                                                            <div class="ms-2 d-flex align-items-center">
                                                                <h4>@ {{ $video->user->name }}</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="video-title-desc mt-3">
                                                        <span>{{ $video->name }}</span>
                                                        {!! $video->description !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
    </section>
  
@endsection
