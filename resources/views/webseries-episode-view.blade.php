@extends('layouts.main')
@section('title')
    {{ $meta_title ?? config('app.project_name') . '| Webseries Show' }}
@endsection
@section('main-section')
    @php
        $user = app('logged-in-user');
    @endphp
    <link rel="stylesheet" href="{{ URL::to('/asset/css/plyr.css') }}" media="print" onload="this.media='all'">
    <script src="{{ url('/') }}/asset/js/plyr.js?v={{ time() }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        const resumeTime = {{ $lastWatchTime ?? 0 }};

        function saveWatchTime(seconds) {

            fetch("{{ route('save.watch.time') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        user_id: "{{ auth()->id() }}",
                        video_id: "{{ $video->id }}",
                        episode_id: "{{ $video->id }}",
                        type_id: "{{ $video->type_id }}",
                        series_id: "{{ $video->series_id }}",
                        category_id: "{{ $video->category_id }}",
                        watch_time: seconds,
                        type: "episode"
                    })
                })
                .then(res => res.json())
                .then(data => {
                    console.log("Watch time saved:", seconds);
                })
                .catch(err => console.error("Error saving watch time:", err));
        }
        document.addEventListener('DOMContentLoaded', () => {
            const video = document.getElementById("transcodedplayer");
            const source =
                config('app.website_url') . 'admin_panel/public/storage/videos/{{ $video->transcoded_video_path }}/master.m3u8';
            const defaultOptions = {
                controls: [
                    'play-large',
                    'play',
                    'progress',
                    'current-time',
                    'duration',
                    'mute',
                    'volume',
                    'captions',
                    'settings',
                    'pip',
                    'airplay',
                    'fullscreen',
                ]
            };
            const thumbnailInterval = 4; // Interval between segments (matches your ffmpeg hls_time)
            if (Hls.isSupported()) {
                const hls = new Hls();
                hls.loadSource(source);
                // hls.on(Hls.Events.MANIFEST_PARSED, function(event, data) {
                //     const availableQualities = hls.levels.map(level => level.height);
                //     defaultOptions.quality = {
                //         default: availableQualities[0],
                //         options: availableQualities,
                //         forced: true,
                //         onChange: (e) => updateQuality(e),
                //     };
                //     const player = new Plyr(video, defaultOptions);
                //     player.on('loadedmetadata', () => {
                //         const totalSegments = Math.ceil(player.duration / thumbnailInterval);
                //         addThumbnailPreview(player, totalSegments);
                //     });
                //     // Attach custom controls
                //     addCustomControls(player);
                // });
                hls.on(Hls.Events.MANIFEST_PARSED, function() {

                    const availableQualities = hls.levels.map((l) => l.height);
                    availableQualities.unshift(0);

                    defaultOptions.quality = {
                        default: 0,
                        options: availableQualities,
                        forced: true,
                        onChange: (newQuality) => updateQuality(newQuality),
                    };

                    const player = new Plyr(video, defaultOptions);

                    let lastSentTime = 0;

                    player.on('timeupdate', () => {

                        const currentSec = Math.floor(player.currentTime);

                        // Send every 5 seconds
                        if (currentSec !== lastSentTime) {
                            lastSentTime = currentSec;
                            saveWatchTime(currentSec); // send every second
                        }
                    });
                    player.on('loadedmetadata', () => {
                        if (resumeTime > 5) { // ensure it’s valid
                            player.currentTime = resumeTime;
                            console.log("Resumed at:", resumeTime);
                        }
                        const totalSegments = Math.ceil(player.duration / thumbnailInterval);
                        addThumbnailPreview(player, totalSegments);
                    });
                    addCustomControls(player);
                });
                hls.attachMedia(video);
                window.hls = hls;
            }

            function updateQuality(newQuality) {
                window.hls.levels.forEach((level, levelIndex) => {
                    if (level.height === newQuality) {
                        window.hls.currentLevel = levelIndex;
                    }
                });
            }
            // Add dynamic thumbnail preview functionality
            function addThumbnailPreview(player, totalSegments) {
                const controlsContainer = player.elements.container;
                const thumbnailPreview = document.createElement('img');
                thumbnailPreview.className = 'plyr__control thumbnail-preview';
                thumbnailPreview.id = 'thumbnailPreview';
                thumbnailPreview.src = `{{ $video->landscape_url }}`; // Initial placeholder
                controlsContainer.appendChild(thumbnailPreview);
                const progressBar = player.elements.progress;
                progressBar.addEventListener('mousemove', (e) => {
                    const rect = progressBar.getBoundingClientRect();
                    const offsetX = e.clientX - rect.left;
                    const segmentIndex = Math.floor((offsetX / progressBar.offsetWidth) * totalSegments);
                    const thumbnailUrl =
                        `{{ config('app.website_url') }}admin_panel/public/storage/videos/{{ $video->transcoded_video_path }}/thumbnails/480p_thumbnail_${String(segmentIndex).padStart(3, '0')}.jpg`;
                    thumbnailPreview.src = thumbnailUrl;
                    const isFullscreen = !!document.fullscreenElement;
                    thumbnailPreview.style.left = `${e.pageX - (isFullscreen ? 70 : 70)}px`;
                    thumbnailPreview.style.display = 'block';
                });
                progressBar.addEventListener('mouseleave', () => {
                    thumbnailPreview.style.display = 'none';
                });
            }

            function addCustomControls(player) {
                const controlsContainer = player.elements.controls;
                const playerContainer = player.elements.container;

                // Countdown overlay
                const countdownOverlay = document.createElement('div');
                countdownOverlay.id = 'countdownOverlay';
                countdownOverlay.classList.add('plyr__countdown-overlay');
                countdownOverlay.innerHTML = `
                    <span class="up-next-label">Up Next</span>
                    <span class="countdown-timer" id="countdown-timer-text"></span>
                `;
                playerContainer.appendChild(countdownOverlay);

                // Previous Episode
                if (typeof previousEpisodeUrl !== 'undefined') {
                    const prevEpisodeBtn = document.createElement('button');
                    prevEpisodeBtn.classList.add('plyr__prev-episode-btn');
                    prevEpisodeBtn.innerHTML = 'Previous Episode';
                    controlsContainer.appendChild(prevEpisodeBtn);
                    prevEpisodeBtn.onclick = () => playEpisode(previousEpisodeUrl);
                }

                // Next Episode
                if (typeof nextEpisodeUrl !== 'undefined' && nextEpisodeUrl !== null) {
                    const nextEpisodeBtn = document.createElement('button');
                    nextEpisodeBtn.classList.add('plyr__next-episode-btn');
                    nextEpisodeBtn.innerHTML = 'Next Episode';
                    controlsContainer.appendChild(nextEpisodeBtn);
                    nextEpisodeBtn.onclick = () => playEpisode(nextEpisodeUrl);

                    const timerText = document.getElementById('countdown-timer-text');

                    player.on('timeupdate', () => {
                        const timeRemaining = Math.floor(player.duration - player.currentTime);

                        if (timeRemaining <= 60 && timeRemaining > 0) {
                            countdownOverlay.style.display = 'block';
                            timerText.textContent = `Next video in ${timeRemaining}s`;
                        } else {
                            countdownOverlay.style.display = 'none';
                        }
                    });

                    player.on('ended', () => {
                        playEpisode(nextEpisodeUrl);
                    });
                }

                // =========================
                // ✅ CENTER SKIP CONTROLS
                // =========================

                const centerControls = document.createElement('div');
                centerControls.classList.add('plyr__center-controls');

                // LEFT (-10)
                const leftSkip = document.createElement('div');
                leftSkip.classList.add('plyr__center-skip');
                leftSkip.innerHTML = `
            <i class="fa-solid fa-angle-double-left"></i>
            <span>10</span>
            `;

                // RIGHT (+10)
                const rightSkip = document.createElement('div');
                rightSkip.classList.add('plyr__center-skip');
                rightSkip.innerHTML = `
            <i class="fa-solid fa-angle-double-right"></i>
            <span>10</span>
            `;

                centerControls.appendChild(leftSkip);
                centerControls.appendChild(rightSkip);
                playerContainer.appendChild(centerControls);

                // Click
                leftSkip.onclick = () => {
                    player.currentTime = Math.max(player.currentTime - 10, 0);
                };

                rightSkip.onclick = () => {
                    player.currentTime = Math.min(player.currentTime + 10, player.duration);
                };

                // =========================
                // ✅ DOUBLE CLICK SUPPORT
                // =========================

                playerContainer.addEventListener('dblclick', (e) => {
                    const rect = playerContainer.getBoundingClientRect();
                    const clickX = e.clientX - rect.left;

                    if (clickX < rect.width / 2) {
                        player.currentTime = Math.max(player.currentTime - 10, 0);
                        showSkip('left');
                    } else {
                        player.currentTime = Math.min(player.currentTime + 10, player.duration);
                        showSkip('right');
                    }
                });

                function showSkip(side) {
                    centerControls.style.opacity = 1;

                    if (side === 'left') {
                        leftSkip.style.transform = "scale(1.2)";
                        setTimeout(() => leftSkip.style.transform = "scale(1)", 200);
                    } else {
                        rightSkip.style.transform = "scale(1.2)";
                        setTimeout(() => rightSkip.style.transform = "scale(1)", 200);
                    }

                    setTimeout(() => {
                        centerControls.style.opacity = 0;
                    }, 800);
                }
            }

            function playEpisode(url) {
                window.location.href = url;
            }
        });
    </script>
    <style>
        /* Custom styling for the thumbnail preview */
        .thumbnail-preview {
            position: absolute;
            width: 150px;
            height: 100px;
            display: none;
            border-radius: 4px;
            border: 2px solid #FFFFFF;
            pointer-events: none;
            z-index: 99999;
            bottom: 100px;
            padding: 0px !important;
        }

        /* Prev / Next Episode Buttons — Base */
        .plyr__prev-episode-btn,
        .plyr__next-episode-btn {
            background-color: rgba(0, 0, 0, 0.85);
            color: #fff;
            border: none;
            padding: 6px 12px;
            font-size: 12px !important;
            margin-left: 6px;
            border-radius: 4px;
            cursor: pointer;
            z-index: 9999;
            transition: background-color 0.3s;
            white-space: nowrap;
        }

        .plyr__prev-episode-btn:hover,
        .plyr__next-episode-btn:hover {
            background-color: rgba(255, 58, 31, 0.9);
        }

        /* Countdown Overlay ON the Video */
        .plyr__countdown-overlay {
            display: none;
            position: absolute;
            bottom: 70px;
            right: 16px;
            background: rgba(0, 0, 0, 0.78);
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            line-height: 1.5;
            z-index: 9999;
            text-align: center;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            pointer-events: none;
            min-width: 160px;
        }

        .plyr__countdown-overlay .up-next-label {
            font-size: 10px;
            opacity: 0.75;
            display: block;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plyr__countdown-overlay .countdown-timer {
            font-size: 14px;
            font-weight: 700;
            display: block;
        }

        /* Tablet (max 940px) */
        @media (max-width: 940px) {

            .plyr__prev-episode-btn,
            .plyr__next-episode-btn {
                padding: 5px 9px;
                font-size: 10px !important;
                margin-left: 4px;
                position: static !important;
                top: unset !important;
            }

            .plyr__countdown-overlay {
                bottom: 60px;
                right: 10px;
                padding: 8px 12px;
                min-width: 130px;
            }

            .plyr__countdown-overlay .up-next-label {
                font-size: 9px;
            }

            .plyr__countdown-overlay .countdown-timer {
                font-size: 12px;
            }
        }

        /* Mobile (max 540px) */
        @media (max-width: 540px) {
            #innerBanners {
                margin-top: 45px !important;
            }

            .plyr__prev-episode-btn,
            .plyr__next-episode-btn {
                padding: 4px 7px;
                font-size: 9px !important;
                margin-left: 3px;
                border-radius: 3px;
            }

            .plyr__countdown-overlay {
                bottom: 52px;
                right: 8px;
                padding: 6px 10px;
                min-width: 110px;
                border-radius: 6px;
            }

            .plyr__countdown-overlay .up-next-label {
                font-size: 8px;
            }

            .plyr__countdown-overlay .countdown-timer {
                font-size: 11px;
            }

            .cast-name-container {
                height: 50px;
            }

            .cast-name-container p {
                font-size: 12px !important;
            }

            .nav-pills .nav-link {
                font-size: 12px !important;
            }

            .ep-number {
                font-size: 12px !important;
            }
        }

        /* CENTER CONTROLS */
        .plyr__center-controls {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            gap: 700px;
            /* distance from play button */
            opacity: 0;
            transition: 0.3s;
            pointer-events: none;
            z-index: 9999;
        }

        /* Show on hover */
        .plyr:hover .plyr__center-controls {
            opacity: 1;
        }

        /* Skip buttons */
        .plyr__center-skip {
            width: 40px;
            height: 40px;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            /* icon top, text bottom */
            justify-content: center;
            align-items: center;
            cursor: pointer;
            color: #fff;
            font-size: 10px;
            backdrop-filter: blur(6px);
            pointer-events: auto;
        }

        /* Icon */
        .plyr__center-skip i {
            font-size: 12px;
            margin-bottom: 2px;
        }

        /* Text */
        .plyr__center-skip span {
            font-size: 10px;
            font-weight: 600;
        }

        .nav-pills .nav-link {
            border-radius: 5px !important;
            border: 1px solid #FF3A1F !important;
            color: #ff3a1f;
        }

        .nav-pills .nav-link.active {
            color: #FFFFFF;
            background-color: #FF3A1F !important;
            border-radius: 5px !important;
        }

        /* Cast */
        .cast-name-container {
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 15px;
        }

        .cast-name-container p {
            line-height: 1.2;
        }

        #starCastCarousel.owl-carousel .owl-item {
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .owl-carousel .owl-nav button.owl-prev,
        .owl-carousel .owl-nav button.owl-next {
            font-size: 3rem;
            top: 30%;
        }

        /* Share Button */
        .btn-share {
            background-color: #FF4B2B;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        /* Share Modal Background */
        .share-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(3px);
        }

        /* Share Modal Content Box */
        .share-modal-content {
            background: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.3s ease;
            position: relative;
        }

        /* Share Modal Close Button */
        .share-modal .close {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: #333;
        }

        /* Social Icons Row */
        .share-icons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .share-icons a {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            font-size: 14px;
            color: #333;
        }

        .share-icons i {
            font-size: 24px;
            background: #f1f1f1;
            padding: 12px;
            border-radius: 50%;
            margin-bottom: 5px;
            transition: background 0.2s ease;
        }

        .share-icons i:hover {
            background: #ddd;
        }

        /* Copy Link Box */
        .copy-link-box {
            display: flex;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .copy-link-box input {
            flex: 1;
            padding: 10px;
            border: none;
            outline: none;
            font-size: 14px;
        }

        .copy-link-box button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-weight: bold;
        }

        .copy-link-box button:hover {
            background: #0056b3;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Toast Notification */
        .custom-toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(50px);
            background: #333;
            color: white;
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


        /*new added*/
        .btn-details-watchlist {
            background-color: #FF4B2B;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        .btn-details-watchlist:hover {
            background-color: #FF4B2B;
        }

        .btn-details-watchlist .details-watchlist-tooltip {
            display: none;
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.75);
            color: #fff;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            white-space: nowrap;
            z-index: 10;
        }

        .btn-details-watchlist:hover .details-watchlist-tooltip {
            display: block;
        }

        .btn-details-watchlist-anchor {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #FF4B2B;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            position: relative;
        }

        .btn-details-watchlist-anchor:hover {
            background-color: #FF4B2B;
            color: white;
        }

        .btn-details-watchlist-anchor .details-watchlist-tooltip {
            display: none;
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.75);
            color: #fff;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            white-space: nowrap;
            z-index: 10;
        }

        .btn-details-watchlist-anchor:hover .details-watchlist-tooltip {
            display: block;
        }

        .details-watchlist-form {
            margin-bottom: 0;
            display: inline-flex;
        }
    </style>
    <section id="innerBanners" class="inner-banner">
        <div class="inner-banner-section">
            <div class="inner-banner-holder {{ $user && $user->subscription == 'Yes' ? 'd-none' : 'd-block' }}">
                <img src="{{ config('app.website_url') }}admin_panel/public/images/series/{{ $video->landscape }}"
                    class="inner-landscape" width="100%" alt="{{ $video->landscape }}" loading="lazy">
                <img src="{{ config('app.website_url') }}admin_panel/public/images/series/{{ $video->thumbnail }}"
                    class="inner-thumbnail" width="100%" alt="{{ $video->thumbnail }}" loading="lazy">
                <div class="overlay position-absolute">
                    <div class="movie-short-description ">
                        <div class="row">
                            <div class="col-9">
                                <div class="short-data position-absolute bottom-0 pb-5 col-sm-8 col-10">
                                    <h5>{{ $video->name }}</h5>
                                    <p class="mb-1 ">{{ \Carbon\Carbon::parse($video->release_date)->format('Y') }} |
                                        @if ($video->episode_duration)
                                            {{ MillisecondsToTime($video->episode_duration) }}
                                        @else
                                            -
                                        @endif | {{ $video->language_name }}
                                    </p>
                                    <div class="line"></div>
                                    <ul class="mb-1 mt-2">
                                        @foreach ($catListData as $item)
                                            <li>&#x2022; {{ $item->name }}</li>
                                        @endforeach
                                    </ul>
                                    {{-- <p>Director : <span class="bold-600">Sohail Khan</span> </p> --}}
                                    <div class="d-flex position-relative">
                                        @if ($user && $user->subscription == 'No')
                                            <a href="#">
                                                <button type="button" class="btn-watchnow"
                                                    onclick="openSubscriptionModal()"><i
                                                        class="fa-solid fa-play"></i>&nbsp;&nbsp;Subscribe To Watch</button>
                                            </a>
                                        @elseif(!$user)
                                            <a href="#" onclick="openSubscriptionModal()">
                                                <button type="button" class="btn-watchnow"><i
                                                        class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch Now</button>
                                            </a>
                                        @else
                                            <a href="#">
                                                <button type="button" class="btn-watchnow watch-now"><i
                                                        class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch Now</button>
                                            </a>
                                        @endif
                                        @include('watchlist-button', [
                                            'id' => $video->id,
                                            'type_id' => $video->type_id,
                                            'category_id' => $video->category_id,
                                            'type' => 'episode',
                                            'watchlist' => $watchlist,
                                        ])
                                        <a href="#">
                                            <button type="button" class="btn-watchlist ms-2 text-white"
                                                onclick="openShareModal()">
                                                <i class="fa fa-share"></i>
                                                <div class="toast-message-box-rm">Share</div>
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if ($video->episode_upload_type == 'youtube')
                <iframe
                    src="{{ $video->episode_320 }}{{ $user && $user->subscription == 'Yes' ? '&autoplay=1' : '' }}&rel=0&showinfo=0&modestbranding=1"
                    height="600px" width="100%" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen id="player"
                    style="display: {{ $user && $user->subscription == 'Yes' ? 'block' : 'none' }};"></iframe>
            @elseif($video->episode_upload_type == 'bn_stream_url')
                <div style="position:relative;padding-top:56.25%; display: {{ $user && $user->subscription == 'Yes' ? 'block' : 'none' }};"
                    id="player" oncontextmenu="return false;"><iframe
                        src="{{ $video->episode_320 }}?autoplay=true&loop=false&muted=false&preload=true&responsive=true"
                        loading="lazy" style="border:0;position:absolute;top:0;height:100%;width:100%;"
                        allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;"
                        allowfullscreen="true"></iframe></div>
            @elseif($video->episode_upload_type == 'server_episodes')
                @if ($user && $user->subscription == 'Yes')
                    <video controls autoplay playsinline poster="{{ $video->landscape_url }}" preload="auto"
                        id="webseriesplayer"
                        style="display: {{ $user && $user->subscription == 'Yes' ? 'block' : 'none' }};" class="w-100">
                        <source
                            src="{{ asset(config('app.website_url') . 'admin_panel/public/images/video/' . $video->episode_320) }}"
                            type="video/mp4" size="360">
                        <source
                            src="{{ asset(config('app.website_url') . 'admin_panel/public/images/video/' . $video->episode_480) }}"
                            type="video/mp4" size="480">
                        <source
                            src="{{ asset(config('app.website_url') . 'admin_panel/public/images/video/' . $video->episode_720) }}"
                            type="video/mp4" size="720">
                        <source
                            src="{{ asset(config('app.website_url') . 'admin_panel/public/images/video/' . $video->episode_1080) }}"
                            type="video/mp4" size="1080">
                        Your browser does not support the video tag.
                    </video>
                @endif
            @else
                @if ($user && $user->subscription == 'Yes')
                    <video id="transcodedplayer" controls playsinline class="w-100" poster="{{ $video->landscape_url }}"
                        style="display: {{ $user && $user->subscription == 'Yes' ? 'block' : 'none' }};">
                        <source src="" type="video/m3u8">
                        <source src="" type="video/m3u8">
                        <source src="" type="video/m3u8">
                        <source src="" type="video/m3u8">
                    </video>
                @endif

                    @if ($previousEpisode)
                        <script>
                            var previousEpisodeUrl = '{{ $previousEpisodeUrl }}';
                        </script>
                    @else
                        <script>
                            var previousEpisodeUrl = null;
                        </script>
                    @endif

                    @if ($nextEpisode)
                        <script>
                            var nextEpisodeUrl = '{{ $nextEpisodeUrl }}';
                        </script>
                    @else
                        <script>
                            var nextEpisodeUrl = null;
                        </script>
                    @endif
                @endif
    </section>
    <section id="webseries-details" style="display: {{ $user && $user->subscription == 'Yes' ? 'block' : 'none' }};">
        <div class="webseries-details-container">
            <div class="webseries-data">
                <div class="row">
                    <div class="col-md-7">
                        <h2>{{ $video->name }}</h2>
                        <ul class="animate__animated animate__fadeInUp mt-3 ">
                            <li class="d-inline">{{ \Carbon\Carbon::parse($video->release_date)->format('Y') }}</li>
                            <li class="d-inline">&#x2022;</li>
                            <li class="d-inline">
                                @if ($video->episode_duration)
                                    {{ MillisecondsToTime($video->episode_duration) }}
                                @else
                                    -
                                @endif
                            </li>
                            <li class="d-inline">&#x2022;</li>
                            <li class="d-inline"> {{ $video->language_name }}</li>
                        </ul>
                        <div class="line"></div>
                        <ul class="mb-1 mt-2">
                            @foreach ($catListData as $item)
                                <li class="d-inline">&#x2022; {{ $item->name }}</li>
                            @endforeach
                        </ul>
                        <div class="description mt-4" id="description">
                            {!! $video->description !!}
                        </div>
                        <span class="show-more-btn" id="toggleDescription">Show More</span>
                    </div>
                    <div class="col-md-5 float-right text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">

                            <!-- Share Button -->
                            <button class="btn-share" onclick="openShareModal()">
                                <i class="fa fa-share"></i>&nbsp; Share
                            </button>
                            @php
                                $type = 'episode';
                            @endphp
                            <!-- Watchlist Button (inlined, unique classes) -->
                            @if ($watchlist->contains('episode_id', $video->id))
                                <form action="{{ route('watchlist.toggle') }}" class="details-watchlist-form"
                                    method="POST">
                                    @csrf
                                    @if ($type == 'series')
                                        <input type="hidden" name="series_id" value="{{ $video->series_id }}">
                                    @elseif($type == 'episode')
                                        <input type="hidden" name="series_id" value="{{ $video->series_id }}">
                                        <input type="hidden" name="episode_id" value="{{ $video->id }}">
                                        <input type="hidden" name="video_id" value="{{ $video->id }}">
                                    @else
                                        <input type="hidden" name="video_id" value="{{ $video->id }}">
                                    @endif
                                    <input type="hidden" name="type_id" value="{{ $video->type_id }}">
                                    <input type="hidden" name="category_id" value="{{ $video->category_id }}">
                                    <input type="hidden" name="type" value="{{ $type }}">
                                    <button type="submit" class="btn-details-watchlist">
                                        <svg width="24" height="13" viewBox="0 0 24 13" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect width="2.00845" height="9.87727" rx="1.00423"
                                                transform="matrix(0.851668 -0.524082 0.556629 0.830761 0 4.08691)"
                                                fill="white" />
                                            <rect width="2.19463" height="21.0917" rx="1.09731"
                                                transform="matrix(0.553502 0.832848 -0.853569 0.52098 22.7852 0)"
                                                fill="white" />
                                        </svg>
                                        Watchlist
                                        <div class="details-watchlist-tooltip">Remove from Watch List</div>
                                    </button>
                                </form>
                            @elseif (!$user || $user->subscription !== 'Yes')
                                <a href="#" class="btn-details-watchlist-anchor" onclick="openSubscriptionModal()">
                                    <svg width="18" height="20" viewBox="0 0 18 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect x="8.45654" y="0.34668" width="1.61563" height="18.8704" rx="0.807817"
                                            fill="white" />
                                        <rect x="17.7461" y="8.88379" width="1.79718" height="16.9642" rx="0.89859"
                                            transform="rotate(90 17.7461 8.88379)" fill="white" />
                                    </svg>
                                    Watchlist
                                    <div class="details-watchlist-tooltip">Add to Watch List</div>
                                </a>
                            @else
                                <form action="{{ route('watchlist.toggle') }}" method="POST"
                                    class="details-watchlist-form">
                                    @csrf
                                    @if ($type == 'series')
                                        <input type="hidden" name="series_id" value="{{ $video->series_id }}">
                                    @elseif($type == 'episode')
                                        <input type="hidden" name="series_id" value="{{ $video->series_id }}">
                                        <input type="hidden" name="episode_id" value="{{ $video->id }}">
                                        <input type="hidden" name="video_id" value="{{ $video->id }}">
                                    @else
                                        <input type="hidden" name="video_id" value="{{ $video->id }}">
                                    @endif
                                    <input type="hidden" name="type_id" value="{{ $video->type_id }}">
                                    <input type="hidden" name="category_id" value="{{ $video->category_id }}">
                                    <input type="hidden" name="type" value="{{ $type }}">
                                    <button type="submit" class="btn-details-watchlist">
                                        <svg width="18" height="20" viewBox="0 0 18 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect x="8.45654" y="0.34668" width="1.61563" height="18.8704" rx="0.807817"
                                                fill="white" />
                                            <rect x="17.7461" y="8.88379" width="1.79718" height="16.9642" rx="0.89859"
                                                transform="rotate(90 17.7461 8.88379)" fill="white" />
                                        </svg>
                                        Watchlist
                                        <div class="details-watchlist-tooltip">Add to Watch List</div>
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="section-home-newdes" class="podcast ">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid">
                    <ul class="nav nav-pills mb-3 mt-3" id="pills-tab" role="tablist">

                        @foreach ($seriesdata as $index => $item)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link @if ($index == 0) active @endif"
                                    id="pills-{{ $item->id }}-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-{{ $item->id }}" type="button" role="tab"
                                    aria-controls="pills-{{ $item->id }}"
                                    aria-selected="true">{{ $item->season }}</button>
                            </li>&nbsp;&nbsp;
                        @endforeach
                    </ul>
                    <span class="l-head">Episodes </span>
                    <div class="tab-content mt-4" id="pills-tabContent">
                        @foreach ($seriesdata as $index => $data)
                            @php
                                $episodesdata = DB::table('episodes')
                                    ->join('language', 'language.id', '=', 'episodes.language_id')
                                    ->join('series', 'series.id', '=', 'episodes.series_id')

                                    ->where('season_id', $data->id)
                                    ->where('episodes.status', 1)
                                    ->orderBy('episode_no', 'DESC')

                                    ->get([
                                        'episodes.id as id',
                                        'series_id',
                                        'type_id',
                                        'category_id',
                                        'language_id',
                                        'episode_duration',
                                        'thumbnail',
                                        'landscape',
                                        'release_date',
                                        'episodes.description as description',
                                        'episodes.status',
                                        'episodes.created_at',
                                        'episode_no',
                                        'episodes.slug',
                                        'series.season_no as season_no',
                                        'series.slug as series_slug',
                                        'series.trailer_url',
                                    ]);

                            @endphp
                            <div class="tab-pane fade @if ($index == 0) show active @endif"
                                id="pills-{{ $data->id }}" role="tabpanel"
                                aria-labelledby="pills-{{ $data->id }}-tab">
                                <div class="slider-container">
                                    <div class="latest-release-slider owl-carousel owl-theme">
                                        @foreach ($episodesdata as $e)
                                            <div class="item">
                                                <div class="cards-container">
                                                    <div class="alpha-card skeleton">
                                                        <div class="image-wrapper">
                                                            <a
                                                                href="{{ route('webseries.episode.view.by.slug', [
                                                                    'slug' => $e->series_slug,
                                                                    'season' => $e->season_no,
                                                                    'episode' => $e->episode_no,
                                                                ]) }}">
                                                                <img src="{{ config('app.website_url') }}admin_panel/public/images/series/{{ $e->landscape }}"
                                                                    alt="{{ $e->landscape }}" loading="lazy">
                                                            </a>
                                                            <p style="color: white" class="ep-number">{{ $data->season }}
                                                                | Episode {{ $e->episode_no }} </p>
                                                        </div>
                                                        <div class="alpha-card-large {{ $e->trailer_url ? 'movie-card' : '' }}"
                                                            id="movie-card">
                                                            <img src="{{ config('app.website_url') }}admin_panel/public/images/series/{{ $e->landscape }}"
                                                                class="thumbnail" id="thumbnail"
                                                                alt="{{ $e->landscape }}" loading="lazy">
                                                            <video class="video-holder" loop muted id="video-holder">
                                                                <source
                                                                    src="{{ config('app.website_url') }}admin_panel/public/images/video/{{ $e->trailer_url }}"
                                                                    type="video/mp4">
                                                            </video>
                                                            <div class="content-holder pt-5">
                                                                <div class="content">
                                                                    <div class="button-holder row">
                                                                        <div class="col-9">
                                                                            <a
                                                                                href="{{ route('webseries.episode.view.by.slug', [
                                                                                    'slug' => $e->series_slug,
                                                                                    'season' => $e->season_no,
                                                                                    'episode' => $e->episode_no,
                                                                                ]) }}">
                                                                                <button class="btn-watchnow"
                                                                                    type="button">
                                                                                    <i
                                                                                        class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch
                                                                                    Now</button></a>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            @include('watchlist-button', [
                                                                                'id' => $e->id,
                                                                                'type_id' => $e->type_id,
                                                                                'category_id' => $e->category_id,
                                                                                'type' => 'episode',
                                                                                'watchlist' => $watchlist,
                                                                            ])
                                                                        </div>
                                                                    </div>
                                                                    <ul class="animate__animated animate__fadeInUp mt-3">
                                                                        <li>{{ \Carbon\Carbon::parse($e->release_date)->format('Y') }}
                                                                        </li>
                                                                        <li>&#x2022;</li>
                                                                        <li>
                                                                            @if ($e->episode_duration)
                                                                                {{ MillisecondsToTime($e->episode_duration) }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </li>
                                                                        <li>&#x2022;</li>
                                                                        <li>Episode - {{ $e->episode_no }}</li>
                                                                    </ul>
                                                                    <p
                                                                        class="mt-3 mb-4 animate__animated animate__fadeInUp">
                                                                        {{ Str::words(strip_tags($e->description), 15, '...') }}
                                                                    </p>
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
                        @endforeach
                    </div>
                </div>
    </section>
    <hr>
    <section id="section-home-newdes" class="webseries">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid">
                    <div class="homepage-heading d-flex justify-content-between align-items-center pe-3">
                        <span class="l-head">More Like this - Web Series </span>
                    </div>
                    <div class="slider-container">
                        <div class="latest-release-slider owl-carousel owl-theme" id="webseriesslider">

                            @foreach ($allSeries as $item)
                                <div class="item">
                                    <div class="cards-container">
                                        <div class="alpha-card skeleton">
                                            <div class="image-wrapper">

                                                <a
                                                    href="{{ route('webseries.episode.view.by.slug', [
                                                        'slug' => $item->series->slug,
                                                        'season' => $item->series->season_no,
                                                        'episode' => $item->episode_no,
                                                    ]) }}"><img
                                                        src="{{ config('app.website_url') }}admin_panel/public/images/series/{{ $item->series->landscape_image }}"
                                                        alt="{{ $item->series->landscape_image }}" loading="lazy"></a>
                                            </div>
                                            <div class="alpha-card-large {{ $item->trailer_url ? 'movie-card' : '' }}"
                                                id="movie-card">
                                                <img src="{{ config('app.website_url') }}admin_panel/public/images/series/{{ $item->series->landscape_image }}"
                                                    class="thumbnail" id="thumbnail"
                                                    alt="{{ $item->series->landscape_image }}" loading="lazy">
                                                <video class="video-holder" loop muted id="video-holder">
                                                    <source src="{{ $item->series->trailer_url }}" type="video/mp4">
                                                </video>
                                                <div class="content-holder">
                                                    <div class="content">
                                                        <div class="button-holder row">
                                                            <div class="col-9">
                                                                <a
                                                                    href="{{ route('webseries.episode.view.by.slug', [
                                                                        'slug' => $item->series->slug,
                                                                        'season' => $item->series->season_no,
                                                                        'episode' => $item->episode_no,
                                                                    ]) }}">
                                                                    <button class="btn-watchnow" type="button">
                                                                        <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                        Watch Now</button></a>
                                                            </div>
                                                            <div class="col-3">
                                                                @include('watchlist-button', [
                                                                    'id' => $item->id,
                                                                    'type_id' => $item->type_id,
                                                                    'category_id' => $item->category_id,
                                                                    'type' => 'episode',
                                                                    'watchlist' => $watchlist,
                                                                ])
                                                            </div>
                                                        </div>
                                                        <ul class="animate__animated animate__fadeInUp mt-3">
                                                            <li>{{ \Carbon\Carbon::parse($item->release_date)->format('Y') }}
                                                            </li>
                                                            <li>&#x2022;</li>
                                                            <li>{{ $item->series->season }}</li>
                                                            <li>&#x2022;</li>
                                                            <li>Total Episodes - {{ $item->series->total_episode }}</li>
                                                        </ul>
                                                        <p class="mt-1 mb2 animate__animated animate__fadeInUp">
                                                            {{ Str::words(strip_tags($item->series->description), 15, '...') }}
                                                        </p>
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
        </div>
    </section>
    <!-- Share Modal -->
    <div id="shareModal" class="share-modal">
        <div class="share-modal-content">
            <span class="close" onclick="closeShareModal()">&times;</span>
            <h3 class="text-lg font-semibold mb-4">Share</h3>

            <!-- Social Icons -->
            <div class="share-icons">
                <!-- WhatsApp -->
                <a href="https://wa.me/?text={{ urlencode(route('webseries.episode.view.by.slug', ['slug' => $video->series->slug,'season' => $video->series->season_no, 'episode' => $video->episode_no])) }}"
                    target="_blank">
                    <i class="fa-brands fa-whatsapp" style="color:#25D366;"></i>
                    <span>WhatsApp</span>
                </a>

                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('webseries.episode.view.by.slug', ['slug' => $video->series->slug, 'season' => $video->series->season_no, 'episode' => $video->episode_no])) }}"
                    target="_blank">
                    <i class="fa-brands fa-facebook" style="color:#1877F2;"></i>
                    <span>Facebook</span>
                </a>
                <!-- Instagram -->
                <a href="https://www.instagram.com/?url={{ urlencode(route('webseries.episode.view.by.slug', ['slug' => $video->series->slug,'season' => $video->series->season_no,'episode' => $video->episode_no])) }}"
                    target="_blank">
                    <i class="fa-brands fa-instagram"
                        style="background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);
                           -webkit-background-clip: text;
                           -webkit-text-fill-color: transparent;"></i>
                    <span>Instagram</span>
                </a>
                <!-- X (Twitter) -->
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('webseries.episode.view.by.slug', ['slug' => $video->series->slug,'season' => $video->series->season_no,'episode' => $video->episode_no])) }}"
                    target="_blank">
                    <i class="fa-brands fa-x-twitter" style="color:#000;"></i>
                    <span>X</span>
                </a>


            </div>

            <!-- Copy Link Section -->
            <div class="copy-link-box">
                <input type="text" id="shareLink"
                    value="{{ route('webseries.episode.view.by.slug', ['slug' => $video->series->slug,'season' => $video->series->season_no,'episode' => $video->episode_no]) }}"
                    readonly>
                <button onclick="copyShareLink()">Copy</button>
            </div>
        </div>
    </div>
    <script>
        function openShareModal() {
            document.getElementById("shareModal").style.display = "block";
        }

        function closeShareModal() {
            document.getElementById("shareModal").style.display = "none";
        }

        function copyShareLink() {
            let input = document.getElementById("shareLink");

            // Select the text
            input.select();
            input.setSelectionRange(0, 99999); // for mobile devices

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(input.value)
                    .then(() => showToast("Link copied!"))
                    .catch(() => fallbackCopy(input));
            } else {
                fallbackCopy(input);
            }
        }

        function fallbackCopy(input) {
            try {
                document.execCommand('copy');
                showToast("Link copied!");
            } catch (err) {
                showToast("Could not copy link. Please copy manually.");
            }
        }

        // Show toast notification
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

        // Close modal when clicking outside
        window.onclick = function(event) {
            let modal = document.getElementById("shareModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
@endsection
