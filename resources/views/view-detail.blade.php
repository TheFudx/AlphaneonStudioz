@extends('layouts.main')
@section('title')
    Alphastudioz | Details
@endsection
<style>
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
@section('main-section')

    @include('video-upload-type-common', ['video' => $video])

    <section id="innerBanners" class="inner-banner">
        <div class="inner-banner-section">
            <div class="inner-banner-holder {{ app('logged-in-user')->subscription == 'Yes' ? 'd-none' : 'd-block' }}">
                <img src="https://alphastudioz.in/admin_panel/public/images/video/{{ $video->landscape }}"
                    class="inner-landscape" width="100%" alt="">
                <img src="https://alphastudioz.in/admin_panel/public/images/video/{{ $video->thumbnail }}"
                    class="inner-thumbnail" width="100%" alt="">
                <div class="overlay position-absolute">
                    <div class="movie-short-description ">
                        <div class="row">
                            <div class="col-9">
                                <div class="short-data position-absolute bottom-0 pb-2 col-sm-8 col-10">
                                    <h5>{{ $video->name }}</h5>
                                    <ul class="animate__animated animate__fadeInUp mt-3">
                                        <li>{{ \Carbon\Carbon::parse($video->release_date)->format('Y') }}</li>
                                        <li>&#x2022;</li>
                                        <li>
                                            @if ($video->video_duration)
                                                {{ MillisecondsToTime($video->video_duration) }}
                                            @else
                                                -
                                            @endif
                                        </li>
                                        <li>&#x2022;</li>
                                        <li> {{ $video->language_name }}</li>
                                    </ul>
                                    <div class="line"></div>
                                    <ul class="mb-1 mt-2">
                                        @foreach ($catListData as $item)
                                            <li>&#x2022; {{ $item->name }}</li>
                                        @endforeach
                                    </ul>
                                    <p class="mb-2 mt-2">{{ Str::words(strip_tags($video->description), 20, '...') }}</p>
                                    <div class="d-flex position-relative">
                                        @if (app('logged-in-user')->subscription == 'No')
                                            <a href="{{ route('subscribe') }}">
                                                <button type="button" class="btn-watchnow "> <i
                                                        class="fa-solid fa-play"></i>&nbsp;&nbsp; Subscribe To
                                                    Watch</button>
                                            </a>
                                        @else
                                            <a href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($video->id)]) }}">
                                                <button class="btn-common watch-now" type="button">
                                                    <i class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch Now
                                                </button>
                                            </a>
                                        @endif
                                        @include('watchlist-button', [
                                            'id' => $video->id,
                                            'type' => 'video',
                                            'watchlist' => $watchlist,
                                        ])
                                        {{-- @if (app('logged-in-user')->subscription == 'Yes') --}}
                                        {{-- @if ($user_downloads)
                                            <a href="{{ route('downloded.videos') }}" target="_blank">
                                                <button type="button" class="btn-watchlist ms-2 text-white">
                                                    <i class="fa fa-check text-white"></i>&nbsp;&nbsp; Downloaded
                                                    <div class="toast-message-box-rm">Already Downloaded</div>
                                                </button>
                                            </a>
                                        @else
                                            <button type="submit"
                                                class="btn-watchlist ms-2 download-offline-button text-white"
                                                data-video-url="{{ route('video.proxy', ['externalUrl' => $video->video_320_url]) }}"
                                                data-video-id={{ $video->id }} data-video-name="{{ $video->name }}"
                                                data-video-thumbnail-url="{{ $video->landscape_url }}">
                                                <i class="fa fa-download text-white"></i>&nbsp;&nbsp; Download
                                                <div class="toast-message-box-rm">Download</div>
                                            </button>
                                        @endif
                                        <p class="download-status-{{ $video->id }} text-white"></p> --}}
                                        {{-- @endif --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if ($video->video_upload_type == 'youtube')
                <iframe
                    src="{{ $video->video_320 }}{{ app('logged-in-user')->subscription == 'Yes' ? '&autoplay=1' : '' }}&rel=0&showinfo=0&modestbranding=1"
                    height="600px" width="100%" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen id="player"
                    style="display: {{ app('logged-in-user')->subscription == 'Yes' ? 'block' : 'none' }};"></iframe>
            @elseif($video->video_upload_type == 'bn_stream_url')
                <div style="position:relative;padding-top:56.25%; display: {{ app('logged-in-user')->subscription == 'Yes' ? 'block' : 'none' }};"
                    id="player"><iframe
                        src="{{ $video->video_320 }}?autoplay=true&loop=false&muted=false&preload=true&responsive=true"
                        loading="lazy" style="border:0;position:absolute;top:0;height:100%;width:100%;"
                        allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;"
                        allowfullscreen="true"></iframe></div>
            @elseif($video->video_upload_type == 'server_video')
                @if (app('logged-in-user')->subscription == 'Yes')
                    <video controls autoplay playsinline poster="{{ $video->landscape_url }}" preload="auto" id="player"
                        style="display: {{ app('logged-in-user')->subscription == 'Yes' ? 'block' : 'none' }};"
                        class="w-100">
                        <source
                            src="{{ asset('https://alphastudioz.in/admin_panel/public/images/video/' . $video->video_320) }}"
                            type="video/mp4" size="360">
                        <source
                            src="{{ asset('https://alphastudioz.in/admin_panel/public/images/video/' . $video->video_480) }}"
                            type="video/mp4" size="480">
                        <source
                            src="{{ asset('https://alphastudioz.in/admin_panel/public/images/video/' . $video->video_720) }}"
                            type="video/mp4" size="720">
                        <source
                            src="{{ asset('https://alphastudioz.in/admin_panel/public/images/video/' . $video->video_1080) }}"
                            type="video/mp4" size="1080">
                        Your browser does not support the video tag.
                    </video>
                @endif
            @else
                @if ($video->video_size === 1)
                    @if (app('logged-in-user')->subscription == 'Yes')
                        <video id="player" autoplay controls playsinline class="w-100"
                            poster="{{ $video->landscape_url }}" preload="auto"
                            style="display: {{ app('logged-in-user')->subscription == 'Yes' ? 'block' : 'none' }};">
                            <source
                                src="{{ asset('https://alphastudioz.in/admin_panel/public/images/video/' . $video->transcoded_video_path . '.mp4') }}"
                                type="video/mp4">
                            <source
                                src="{{ asset('https://alphastudioz.in/admin_panel/public/images/video/' . $video->transcoded_video_path . '.mp4') }}"
                                type="video/mp4">
                            <source
                                src="{{ asset('https://alphastudioz.in/admin_panel/public/images/video/' . $video->transcoded_video_path . '.mp4') }}"
                                type="video/mp4">
                            <source
                                src="{{ asset('https://alphastudioz.in/admin_panel/public/images/video/' . $video->transcoded_video_path . '.mp4') }}"
                                type="video/mp4">
                        </video>
                    @endif
                @else
                    @if (app('logged-in-user')->subscription == 'Yes')
                        <video id="transcodedplayer" autoplay controls playsinline class="w-100"
                            poster="{{ $video->landscape_url }}" preload="auto"
                            style="display: {{ app('logged-in-user')->subscription == 'Yes' ? 'block' : 'none' }};">
                            <source src="" type="video/mp4">
                            <source src="" type="video/mp4">
                            <source src="" type="video/mp4">
                            <source src="" type="video/mp4">
                        </video>
                    @endif
                @endif
            @endif
    </section>
    <section id="webseries-details" style="display: {{ app('logged-in-user')->subscription == 'Yes' ? 'block' : 'none' }};">
        <div class="webseries-details-container">
            <div class="webseries-data">
                <div class="row">
                    <div class="col-md-8">
                        <h2>{{ $video->name }}</h2>
                        <ul class="animate__animated animate__fadeInUp mt-3 ">
                            <li class="d-inline">{{ \Carbon\Carbon::parse($video->release_date)->format('Y') }}</li>
                            <li class="d-inline">&#x2022;</li>
                            <li class="d-inline">
                                @if ($video->video_duration)
                                    {{ MillisecondsToTime($video->video_duration) }}
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
                        @if($video->type_id === config('constant.TYPES.TRAILER'))
                        @if ($video->released == 'Yes')
                            <a href="{{ $video->released_url }}">
                                <button type="button" class="btn-common">
                                    <i class="icon-bl-play"></i>
                                    @if ($video->trailer_for == 'Movies')
                                        Watch Full Movie
                                    @elseif($video->trailer_for == 'Music')
                                        Listen Full Song
                                    @elseif($video->trailer_for == 'Series')
                                        Watch All Episodes
                                    @elseif($video->trailer_for == 'Short Film')
                                        Watch Full Short Film
                                    @elseif($video->trailer_for == 'Podcast')
                                        Watch Full Podcast
                                    @endif

                                </button>
                            </a>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- star cast  --}}
    <section id="section-home-newdes" class="podcast">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid">
                    <span class="l-head">Star Cast</span>
                    {{-- Owl Carousel Container --}}
                    <div id="starCastCarousel" class="owl-carousel owl-theme mt-3" data-skip-carousel="true">
                        @foreach ($casts as $c)
                            <div class="item text-center">
                                <img src="{{ $c->image_url }}"
                                    style="height: 100px; width: 100px; border-radius: 50%; object-fit: cover;"
                                    class="img-fluid d-block mx-auto cast-image" alt="cast_image">
                                {{-- Container for text to ensure fixed height --}}
                                <div class="cast-name-container mt-2">
                                    <p class="mt-1 mb-0 text-white">{{ $c->name }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- More like this  --}}
    @php
        if ($video->type_id === config('constant.TYPES.SHORTFILMS')) {
            $morelikethisData = $shortfilmsData;
            $subtitle = 'Short Films';
        } elseif ($video->type_id === config('constant.TYPES.PODCAST')) {
            $morelikethisData = $poadcastData;
            $subtitle = 'Podcasts';
        } elseif ($video->type_id === config('constant.TYPES.TRAILER')) {
            $morelikethisData = $trailerData;
            $subtitle = 'Trailers';
        } elseif ($video->type_id === config('constant.TYPES.MUSIC')) {
            $morelikethisData = $musicData;
            $subtitle = 'Musics';
        } else {
            $morelikethisData = $movieData;
            $subtitle = 'Movies';
        }
    @endphp
    <br>
    @include('section-wise-common', [
        'class' => 'movies',
        'title' => 'More Like This',
        'sub_title' => $subtitle,
        'viewall_route' => route('movies.list'),
        'id' => 'webseriesslider',
        'data' => $morelikethisData,
        'view_route' => 'view.details',
    ]);
    <script>
        // Get all buttons with the class 'download-offline-button'
        // Get all buttons with the class 'download-offline-button'
        document.addEventListener('DOMContentLoaded', () => {
            const downloadButtons = document.querySelectorAll('.download-offline-button');

            downloadButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    console.log('event.currentTarget.dataset-->', event.currentTarget.dataset);
                    const videoUrl = event.currentTarget.dataset.videoUrl;
                    const videoId = event.currentTarget.dataset.videoId;
                    const videoName = event.currentTarget.dataset.videoName;
                    const videoThumbnailUrl = event.currentTarget.dataset.videoThumbnailUrl;

                    // Get the button itself to update its text
                    const downloadButton = event.currentTarget;

                    var data = {
                        videoUrl: videoUrl,
                        videoId: videoId,
                        videoName: videoName,
                        videoThumbnailUrl: videoThumbnailUrl
                    };

                    // Initial text when download starts
                    downloadButton.innerHTML =
                        '<i class="fa fa-download text-white"></i>&nbsp;&nbsp; Preparing...';
                    downloadButton.disabled = true; // Disable button during download

                    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                        navigator.serviceWorker.controller.postMessage({
                            type: 'CACHE_VIDEO',
                            data: data,
                        });
                    } else {
                        console.warn('Service Worker is not active. Cannot send caching message.');
                        downloadButton.innerHTML =
                            '<i class="fa fa-download text-white"></i>&nbsp;&nbsp; Not Available';
                        downloadButton.disabled = false; // Re-enable button
                    }
                });
            });

            // Listen for messages from the Service Worker
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data) {
                        const videoId = event.data.videoId;
                        const downloadButton = document.querySelector(
                            `.download-offline-button[data-video-id="${videoId}"]`);

                        if (downloadButton) {
                            if (event.data.type === 'DOWNLOAD_PROGRESS') {
                                const percentage = Math.round(event.data.progress);
                                downloadButton.innerHTML =
                                    `<i class="fa fa-download text-white"></i>&nbsp;&nbsp; ${percentage}%`;
                            } else if (event.data.type === 'VIDEO_CACHED') {
                                if (event.data.status === 'already_cached') {
                                    downloadButton.innerHTML =
                                        '<i class="fa fa-check text-white"></i>&nbsp;&nbsp; Downloaded';
                                } else {
                                    downloadButton.innerHTML =
                                        '<i class="fa fa-check text-white"></i>&nbsp;&nbsp; Downloaded!';
                                }
                                downloadButton.disabled = false; // Re-enable button
                                // Refresh the page after a short delay
                                setTimeout(() => {
                                    location.reload();
                                }, 1000); // 1 second delay
                            } else if (event.data.type === 'VIDEO_CACHE_FAILED') {
                                downloadButton.innerHTML =
                                    '<i class="fa fa-times text-white"></i>&nbsp;&nbsp; Failed';
                                downloadButton.disabled = false; // Re-enable button
                                console.error('Video caching failed:', event.data.url, event.data.error);
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
