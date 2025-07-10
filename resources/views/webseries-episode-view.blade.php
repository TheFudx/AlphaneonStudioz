@extends('layouts.main')
@section('title')
    Alphastudioz | Webseries Show
@endsection
@section('main-section')
    @php
        $user_subscription = app('logged-in-user')->subscription;
    @endphp
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const video = document.getElementById("transcodedplayer");
            const source =
                'https://alphastudioz.in/admin_panel/public/storage/videos/{{ $video->transcoded_video_path }}/master.m3u8';
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
                hls.on(Hls.Events.MANIFEST_PARSED, function(event, data) {
                    const availableQualities = hls.levels.map(level => level.height);
                    defaultOptions.quality = {
                        default: availableQualities[0],
                        options: availableQualities,
                        forced: true,
                        onChange: (e) => updateQuality(e),
                    };
                    const player = new Plyr(video, defaultOptions);
                    player.on('loadedmetadata', () => {
                        const totalSegments = Math.ceil(player.duration / thumbnailInterval);
                        addThumbnailPreview(player, totalSegments);
                    });
                    // Attach custom controls
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
                        `https://alphastudioz.in/admin_panel/public/storage/videos/{{ $video->transcoded_video_path }}/thumbnails/480p_thumbnail_${String(segmentIndex).padStart(3, '0')}.jpg`;
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
                // Create Skip Intro button
                //const skipIntroBtn = document.createElement('button');
                //skipIntroBtn.classList.add('plyr__skip-intro-btn');
                //skipIntroBtn.innerHTML = 'Skip Intro';
                //skipIntroBtn.style.display = 'none'; // Initially hidden
                //controlsContainer.appendChild(skipIntroBtn);
                // Create countdown element
                const countdown = document.createElement('p');
                countdown.classList.add('plyr__countdown-text');
                countdown.id = 'countdown';
                countdown.innerHTML = 'Next episode will play in';
                countdown.style.display = 'none';
                controlsContainer.appendChild(countdown);
                // Create Previous Episode button
                if (typeof previousEpisodeUrl !== 'undefined') {
                    const prevEpisodeBtn = document.createElement('button');
                    prevEpisodeBtn.classList.add('plyr__prev-episode-btn');
                    prevEpisodeBtn.innerHTML = 'Previous Episode';
                    controlsContainer.appendChild(prevEpisodeBtn);
                    prevEpisodeBtn.addEventListener('click', () => playEpisode(previousEpisodeUrl));
                }
                // Create Next Episode button
                if (typeof nextEpisodeUrl !== 'undefined') {
                    const nextEpisodeBtn = document.createElement('button');
                    nextEpisodeBtn.classList.add('plyr__next-episode-btn');
                    nextEpisodeBtn.innerHTML = 'Next Episode';
                    controlsContainer.appendChild(nextEpisodeBtn);
                    nextEpisodeBtn.addEventListener('click', () => playEpisode(nextEpisodeUrl));
                    // Countdown to next episode
                    player.on('timeupdate', () => {
                        const timeRemaining = player.duration - player.currentTime;
                        if (timeRemaining <= 80) {
                            nextEpisodeBtn.style.display = 'block';
                            countdown.style.display = 'block';
                            countdown.innerText =
                                `Next episode will play in ${Math.floor(timeRemaining)} seconds`;
                            if (timeRemaining <= 0) {
                                playEpisode(nextEpisodeUrl);
                            }
                        } else {
                            countdown.style.display = 'none';
                        }
                    });
                }
                // Show Skip Intro button within a specific time range
                const video = player.media;
                const skipToTime = 390; // Time to skip to (e.g., 6 minutes 30 seconds)
                const buttonVisibleAfter = 330; // Show the button after 5 minutes 30 seconds
                video.addEventListener('timeupdate', () => {
                    if (video.currentTime >= buttonVisibleAfter && video.currentTime < skipToTime) {
                        skipIntroBtn.style.display = 'block';
                    } else {
                        skipIntroBtn.style.display = 'none';
                    }
                });
                // Skip Intro button click event
                skipIntroBtn.addEventListener('click', () => {
                    video.currentTime = skipToTime;
                    player.play();
                    skipIntroBtn.style.display = 'none';
                });
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

        .plyr__next-episode-btn {
            background-color: rgba(0, 0, 0, 0.9);
            color: #fff;
            border: none;
            padding: 8px 16px;
            font-size: 14px !important;
            margin-left: 10px;
            border-radius: 4px;
            cursor: pointer;
            z-index: 99999;
            transition: background-color 0.3s;
        }

        /* .nav-pills button{
                  color: #FFFFFF;
      border: 1px solid #FF3A1F !important;
      background-color:
            } */
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
        /* new Added */
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
        @media(max-width:540px){
            #innerBanners{
                margin-top: 45px !important
            }
            .cast-image{
                /* height: 80px !important; width: 80px !important; */
            }
            .cast-name-container{
                height: 50px;
                /* Reduced height for smaller screens */
            }
            .cast-name-container p{
                font-size: 12px !important;
            }
            .nav-pills .nav-link  {
                font-size: 12px !important;
            }
            .ep-number{
                font-size: 12px !important;
            }
        }
    </style>
    <section id="innerBanners" class="inner-banner">
        <div class="inner-banner-section">
            <div class="inner-banner-holder {{ $user_subscription == 'Yes' ? 'd-none' : 'd-block' }}">
                <img src="https://alphastudioz.in/admin_panel/public/images/series/{{ $video->landscape }}"
                    class="inner-landscape" width="100%" alt="">
                <img src="https://alphastudioz.in/admin_panel/public/images/series/{{ $video->thumbnail }}"
                    class="inner-thumbnail" width="100%" alt="">
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
                                        @if (app('logged-in-user')->subscription == 'No')
                                            <a href="{{ route('subscribe') }}">
                                                <button type="button" class="btn-common "><i
                                                        class="icon-play"></i>Subscribe To Watch</button>
                                            </a>
                                        @else
                                            <a href="#">
                                                <button type="button" class="btn-common watch-now"><i
                                                        class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch Now</button>
                                            </a>
                                        @endif
                                        @include('watchlist-button', [
                                            'id' => $video->id,
                                            'type' => 'episode',
                                            'watchlist' => $watchlist,
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if ($video->episode_upload_type == 'youtube')
                <iframe
                    src="{{ $video->episode_320 }}{{ app('logged-in-user')->subscription == 'Yes' ? '&autoplay=1' : '' }}&rel=0&showinfo=0&modestbranding=1"
                    height="600px" width="100%" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen id="player"
                    style="display: {{ app('logged-in-user')->subscription == 'Yes' ? 'block' : 'none' }};"></iframe>
            @elseif($video->episode_upload_type == 'bn_stream_url')
                <div style="position:relative;padding-top:56.25%; display: {{ app('logged-in-user')->subscription == 'Yes' ? 'block' : 'none' }};"
                    id="player" oncontextmenu="return false;"><iframe
                        src="{{ $video->Episods_320 }}?autoplay=true&loop=false&muted=false&preload=true&responsive=true"
                        loading="lazy" style="border:0;position:absolute;top:0;height:100%;width:100%;"
                        allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;"
                        allowfullscreen="true"></iframe></div>
            @elseif($video->episode_upload_type == 'server_episodes')
                @if (app('logged-in-user')->subscription == 'Yes')
                    <video controls autoplay playsinline poster="{{ $video->landscape_url }}" preload="auto"
                        id="webseriesplayer"
                        style="display: {{ app('logged-in-user')->subscription == 'Yes' ? 'block' : 'none' }};"
                        class="w-100">
                        <source
                            src="{{ asset('https://alphastudioz.in/admin_panel/public/images/video/' . $video->episode_320) }}"
                            type="video/mp4" size="360">
                        <source
                            src="{{ asset('https://alphastudioz.in/admin_panel/public/images/video/' . $video->episode_480) }}"
                            type="video/mp4" size="480">
                        <source
                            src="{{ asset('https://alphastudioz.in/admin_panel/public/images/video/' . $video->episode_720) }}"
                            type="video/mp4" size="720">
                        <source
                            src="{{ asset('https://alphastudioz.in/admin_panel/public/images/video/' . $video->episode_1080) }}"
                            type="video/mp4" size="1080">
                        Your browser does not support the video tag.
                    </video>
                @endif
            @else
                @if (app('logged-in-user')->subscription == 'Yes')
                    <video id="transcodedplayer" controls playsinline class="w-100" poster="{{ $video->landscape_url }}"
                        style="display: {{ app('logged-in-user')->subscription == 'Yes' ? 'block' : 'none' }};">
                        <source src="" type="video/m3u8">
                        <source src="" type="video/m3u8">
                        <source src="" type="video/m3u8">
                        <source src="" type="video/m3u8">
                    </video>
                @endif
                @if ($previousEpisode)
                    @php
                        $previousEpisodeId = App\Helpers\VideoHelper::encryptID($previousEpisode->id);
                    @endphp
                    <script>
                        var previousEpisodeUrl =
                            '{{ route('webseries.episodes.view', [ 'id' => $previousEpisodeId]) }}';
                    </script>
                @endif
                @if ($nextEpisode)
                    @php
                        $nextEpisodeId = App\Helpers\VideoHelper::encryptID($nextEpisode->id);
                    @endphp
                    <script>
                        var nextEpisodeUrl =
                            '{{ route('webseries.episodes.view', ['id' => $nextEpisodeId]) }}';
                    </script>
                @endif
            @endif
    </section>
    <section id="webseries-details" style="display: {{ $user_subscription == 'Yes' ? 'block' : 'none' }};">
        <div class="webseries-details-container">
            <div class="webseries-data">
                <div class="row">
                    <div class="col-md-8">
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
                </div>
            </div>
        </div>
    </section>
    {{-- <section id="section-home-newdes" class="podcast">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid">
                    <span class="l-head">Star Cast</span>
                    <div id="starCastCarousel" class="owl-carousel owl-theme mt-3" data-skip-carousel="true">
                        @foreach ($casts as $c)
                            <div class="item text-center">
                                <img src="{{ $c->image_url }}" style="height: 100px; width: 100px; border-radius: 50%; object-fit: cover;" class="img-fluid d-block mx-auto cast-image"
                                    alt="cast_image">
                                <div class="cast-name-container mt-2">
                                    <p class="mt-1 mb-0 text-white">{{ $c->name }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
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
                                    ->where('season_id', $data->id)
                                    ->where('status', 1)
                                    ->get();
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
                                                            <a href="{{ route('webseries.episodes.view', [
                                                                                'id' => App\Helpers\VideoHelper::encryptID($e->id)
                                                                            ]) }}">
                                                                <img src="https://alphastudioz.in/admin_panel/public/images/series/{{ $e->landscape }}"
                                                                alt="">
                                                            </a>
                                                            <p style="color: white" class="ep-number">{{ $data->season }}
                                                                | Episode {{ $e->episode_no }} </p>
                                                        </div>
                                                        <div class="alpha-card-large {{ $e->trailer_url ? 'movie-card' : '' }}"
                                                            id="movie-card">
                                                            <img src="https://alphastudioz.in/admin_panel/public/images/series/{{ $e->landscape }}"
                                                                class="thumbnail" id="thumbnail" alt="">
                                                            <video class="video-holder" loop muted id="video-holder">
                                                                <source src="{{ $e->trailer_url }}" type="video/mp4">
                                                            </video>
                                                            <div class="content-holder pt-5">
                                                                {{-- <img src="{{url('/')}}/asset/images/tere-sang.png" class="title mt-5" alt=""> --}}
                                                                <div class="content">
                                                                    <div class="button-holder row">
                                                                        <div class="col-9">
                                                                            <a
                                                                                href="{{ route('webseries.episodes.view', [
                                                                                'id' => App\Helpers\VideoHelper::encryptID($e->id)
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
                            {{-- @dd($series,$watchlist) --}}
                            @foreach ($allSeries as $item)
                                <div class="item">
                                    <div class="cards-container">
                                        <div class="alpha-card skeleton">
                                            <div class="image-wrapper">
                                                <a
                                                    href="{{ route('webseries.view', ['id' => App\Helpers\VideoHelper::encryptID($item->id)]) }}"><img
                                                        src="https://alphastudioz.in/admin_panel/public/images/series/{{ $item->landscape_image }}"
                                                        alt=""></a>
                                            </div>
                                            <div class="alpha-card-large {{ $item->trailer_url ? 'movie-card' : '' }}"
                                                id="movie-card">
                                                <img src="https://alphastudioz.in/admin_panel/public/images/series/{{ $item->landscape_image }}"
                                                    class="thumbnail" id="thumbnail" alt="">
                                                <video class="video-holder" loop muted id="video-holder">
                                                    <source src="{{ $item->trailer_url }}" type="video/mp4">
                                                </video>
                                                <div class="content-holder">
                                                    {{-- <img src="{{url('/')}}/asset/images/tere-sang.png" class="title mt-5" alt=""> --}}
                                                    <div class="content">
                                                        <div class="button-holder row">
                                                            <div class="col-9">
                                                                <a
                                                                    href="{{ route('webseries.view', ['id' => App\Helpers\VideoHelper::encryptID($item->id)]) }}">
                                                                    <button class="btn-watchnow" type="button">
                                                                        <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                        Watch Now</button></a>
                                                            </div>
                                                            <div class="col-3">
                                                                @include('watchlist-button', [
                                                                    'id' => $item->id,
                                                                    'type' => 'series',
                                                                    'watchlist' => $watchlist,
                                                                ])
                                                            </div>
                                                        </div>
                                                        <ul class="animate__animated animate__fadeInUp mt-3">
                                                            <li>{{ \Carbon\Carbon::parse($item->release_date)->format('Y') }}
                                                            </li>
                                                            <li>&#x2022;</li>
                                                            <li>{{ $item->season }}</li>
                                                            <li>&#x2022;</li>
                                                            <li>Total Episodes - {{ $item->total_episode }}</li>
                                                        </ul>
                                                        <p class="mt-1 mb2 animate__animated animate__fadeInUp">
                                                            {{ Str::words(strip_tags($item->description), 15, '...') }}
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
@endsection
