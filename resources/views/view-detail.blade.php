@extends('layouts.main')
@section('title')
    {{ $meta_title ?? config('app.project_name') . '| Details' }}
@endsection
@if ($previousVideo)
    <script>
        var previousEpisodeUrl = '{{ route('view.details', ['id' => $previousVideoId]) }}';
    </script>
@endif

@if ($nextVideo)
    <script>
        var nextEpisodeUrl = '{{ route('view.details', ['id' => $nextVideoId]) }}';
    </script>
@endif
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

    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
        color: white !important;
        background-color: #fd5949 !important;
    }

    .nav-pills .nav-link {
        color: white !important;
        background-color: transparent !important;
        border: 1px solid #fd5949 !important;
    }

    @media(max-width:540px) {
        #innerBanners {
            margin-top: 45px !important
        }

        .cast-image {
            /* height: 80px !important; width: 80px !important; */
        }

        .cast-name-container {
            height: 50px;
            /* Reduced height for smaller screens */
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

    /* Modal Background */
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

    /* Modal Content Box */
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

    /* Close Button */
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
@php
    $user = app('logged-in-user');
@endphp
@section('main-section')
    <link rel="stylesheet" href="{{ URL::to('/asset/css/plyr.css') }}" media="print" onload="this.media='all'">
    <script src="{{ url('/') }}/asset/js/plyr.js?v={{ time() }}"></script>
    <script>
        const resumeTime = {{ $lastWatchTime ?? 0 }};

        function saveWatchTime(seconds) {
            @if ($video->type_id == config('constant.TYPES.TRAILER', 8) || !empty($video->trailer_for))
                return;
            @endif

            fetch("{{ route('save.watch.time') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        user_id: "{{ auth()->id() }}",
                        video_id: "{{ $video->id }}",
                        type_id: "{{ $video->type_id }}",
                        category_id: "{{ $video->category_id }}",
                        watch_time: seconds,
                        type: "video"
                    })
                })
                .then(res => res.json())
                .then(data => {
                    console.log("Watch time saved:", seconds);
                })
                .catch(err => console.error("Error saving watch time:", err));
        }
    </script>
    @include('video-upload-type-common', ['video' => $video])
    @if (!empty($video))
        <section id="innerBanners" class="inner-banner">
            <div class="inner-banner-section">
                <div
                    class="inner-banner-holder {{ $user && ($user->subscription == 'Yes') ? 'd-none' : 'd-block' }}">
                    <img src="{{ config('app.website_url') }}admin_panel/public/images/video/{{ $video->landscape }}"
                        class="inner-landscape" width="100%" alt="{{ $video->landscape }}" loading="lazy">
                    <img src="{{ config('app.website_url') }}admin_panel/public/images/video/{{ $video->thumbnail }}"
                        class="inner-thumbnail" width="100%" alt="{{ $video->thumbnail }}" loading="lazy">
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
                                        <p class="mb-2 mt-2">{{ Str::words(strip_tags($video->description), 20, '...') }}
                                        </p>
                                        <div class="d-flex position-relative">
                                            @if ($user && $user->subscription == 'No')
                                                {{-- @if ($video->type_id == config('constant.TYPES.TRAILER'))
                                                    <a
                                                        href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($video->id)]) }}">
                                                        <button class="btn-watchnow watch-now" type="button">
                                                            <i class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch Now
                                                        </button>
                                                    </a>
                                                @else --}}
                                                    <a href="#">
                                                        <button type="button" onclick="openSubscriptionModal()"
                                                            class="btn-watchnow "> <i
                                                                class="fa-solid fa-play"></i>&nbsp;&nbsp; Subscribe To
                                                            Watch
                                                        </button>
                                                    </a>
                                                {{-- @endif --}}
                                            @elseif (($user && $user->subscription == 'Yes'))
                                                <a
                                                    href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($video->id)]) }}">
                                                    <button class="btn-watchnow watch-now" type="button">
                                                        <i class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch Now
                                                    </button>
                                                </a>
                                            @else
                                                <a href="#" onclick="openSubscriptionModal()">
                                                    <button type="button" class="btn-watchnow "> <i
                                                            class="fa-solid fa-play"></i>&nbsp;&nbsp; Watch Now
                                                    </button>
                                                </a>
                                            @endif

                                            @include('watchlist-button', [
                                                'id' => $video->id,
                                                'type_id' => $video->type_id,
                                                'category_id' => $video->category_id,
                                                'type' => 'video',
                                                'watchlist' => $watchlist,
                                            ])
                                            <a href="#" class="wList">
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
                @if ($video->video_upload_type == 'youtube')
                    <iframe
                        src="{{ $video->video_320 }}{{ $user && $user->subscription == 'Yes' ? '&autoplay=1' : '' }}&rel=0&showinfo=0&modestbranding=1"
                        height="600px" width="100%" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen id="player"
                        style="display: {{ $user && $user->subscription == 'Yes' ? 'block' : 'none' }};"></iframe>
                @elseif($video->video_upload_type == 'bn_stream_url')
                    <div style="position:relative;padding-top:56.25%; display: {{ $user && $user->subscription == 'Yes' ? 'block' : 'none' }};"
                        id="bunny-stream-container">
                        <iframe id="bunnyPlayerIframe"
                            src="{{ $video->video_320 }}?autoplay=true&loop=false&muted=false&preload=true&responsive=true&playerjs=true"
                            loading="lazy" style="border:0;position:absolute;top:0;height:100%;width:100%;"
                            allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;"
                            allowfullscreen="true"></iframe>
                    </div>
                @elseif($video->video_upload_type == 'server_video')
                    @if ($user && $user->subscription == 'Yes')
                        <video controls autoplay playsinline poster="{{ $video->landscape_url }}" preload="auto"
                            id="player" style="display: {{ $user && $user->subscription == 'Yes' ? 'block' : 'none' }};"
                            class="w-100">
                            <source
                                src="{{ asset(config('app.website_url') . 'admin_panel/public/images/video/' . $video->video_320) }}"
                                type="video/mp4" size="360">
                            <source
                                src="{{ asset(config('app.website_url') . 'admin_panel/public/images/video/' . $video->video_480) }}"
                                type="video/mp4" size="480">
                            <source
                                src="{{ asset(config('app.website_url') . 'admin_panel/public/images/video/' . $video->video_720) }}"
                                type="video/mp4" size="720">
                            <source
                                src="{{ asset(config('app.website_url') . 'admin_panel/public/images/video/' . $video->video_1080) }}"
                                type="video/mp4" size="1080">
                            Your browser does not support the video tag.
                        </video>
                    @endif
                @else
                    @if ($video->video_size === 1)
                        @if ($user && ($user->subscription == 'Yes'))
                            <video id="player" autoplay controls playsinline class="w-100"
                                poster="{{ $video->landscape_url }}" preload="auto"
                                style="display: {{ $user && ($user->subscription == 'Yes') ? 'block' : 'none' }};">
                                <source
                                    src="{{ asset(config('app.website_url') . 'admin_panel/public/images/video/' . $video->transcoded_video_path . '.mp4') }}"
                                    type="video/mp4">
                                <source
                                    src="{{ asset(config('app.website_url') . 'admin_panel/public/images/video/' . $video->transcoded_video_path . '.mp4') }}"
                                    type="video/mp4">
                                <source
                                    src="{{ asset(config('app.website_url') . 'admin_panel/public/images/video/' . $video->transcoded_video_path . '.mp4') }}"
                                    type="video/mp4">
                                <source
                                    src="{{ asset(config('app.website_url') . 'admin_panel/public/images/video/' . $video->transcoded_video_path . '.mp4') }}"
                                    type="video/mp4">
                            </video>
                        @endif
                    @else
                        @if ($user && ($user->subscription == 'Yes'))
                            <video id="transcodedplayer" autoplay controls playsinline class="w-100"
                                poster="{{ $video->landscape_url }}" preload="auto"
                                style="display: {{ $user && ($user->subscription == 'Yes') ? 'block' : 'none' }};">
                                <source src="" type="video/mp4">
                                <source src="" type="video/mp4">
                                <source src="" type="video/mp4">
                                <source src="" type="video/mp4">
                            </video>
                        @endif
                    @endif
                @endif
        </section>
    @endif
    <section id="webseries-details"
        style="display: {{ $user && ($user->subscription == 'Yes') ? 'block' : 'none' }};">
        <div class="webseries-details-container">
            <div class="webseries-data">
                <div class="row">
                    <div class="col-md-7">
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
                        @if ($video->type_id === config('constant.TYPES.TRAILER'))
                            <div class="row">
                                <div class="col-md-5">

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
                                </div>
                                {{-- <div class="col-md-6">
                                    @if ($user && $user->subscription == 'Yes')
                                        @if ($user_downloads)
                                            <a href="{{ route('downloded.videos') }}" target="_blank">
                                                <button type="button" class="btn-common">
                                                    <i class="fa fa-check"></i>&nbsp;&nbsp; Downloaded
                                               
                                                </button>
                                            </a>
                                        @else
                                            <button type="submit" class="btn-common download-offline-button"
                                                data-video-url="{{ route('video.proxy', ['externalUrl' => $video->video_320_url]) }}"
                                                data-video-id={{ $video->id }} data-video-name="{{ $video->name }}"
                                                data-video-thumbnail-url="{{ $video->landscape_url }}">
                                                <i class="fa fa-download text-white"></i>&nbsp;&nbsp; Download
                                            </button>
                                        @endif
                                        <p class="download-status-{{ $video->id }} text-white"></p>
                                    @endif

                                </div> --}}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-5 float-right text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">

                            <!-- Share Button -->
                            <button class="btn-share" onclick="openShareModal()">
                                <i class="fa fa-share"></i>&nbsp; Share
                            </button>
                            @php
                            $type = 'video';
                            @endphp
                            <!-- Watchlist Button (inlined, unique clases) -->
                            @if ($watchlist->contains('video_id', $video->id))
                                <form action="{{ route('watchlist.toggle') }}" class="details-watchlist-form"
                                    method="POST">
                                    @csrf
                                @if ($type == 'series')
                                    <input type="hidden" name="series_id" value="{{ $video->id }}">
                                @elseif($type == 'episode')
                                    <input type="hidden" name="episode_id" value="{{ $video->id }}">
                                @else
                                    <input type="hidden" name="video_id" value="{{ $video->id }}">
                                @endif
                                <input type="hidden" name="type_id" value="{{ $video->type_id }}">
                                <input type="hidden" name="category_id" value="{{ $video->category_id }}">
                                <input type="hidden" name="type" value="{{ $type }}">
                                    <input type="hidden" name="type" value="video">
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
                                <form action="{{ route('watchlist.toggle') }}" method="POST" class="details-watchlist-form">
                                    @csrf
                                @if ($type == 'series')
                                    <input type="hidden" name="series_id" value="{{ $video->id }}">
                                @elseif($type == 'episode')
                                    <input type="hidden" name="episode_id" value="{{ $video->id }}">
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
            $viewall_route = route('shortfilms.list');
        } elseif ($video->type_id === config('constant.TYPES.PODCAST')) {
            if ($video->language_id == 1) {
                $morelikethisData = $poadcastData;
                $subtitle = 'Podcasts';
            } elseif ($video->language_id == 2) {
                $morelikethisData = $marathipoadcastData;
                $subtitle = 'Marathi Podcasts';
            }
            $viewall_route = route('podcasts.list');
        } elseif ($video->type_id === config('constant.TYPES.TRAILER')) {
            $morelikethisData = $trailerData;
            $subtitle = 'Trailers';
            $viewall_route = route('trailers.list');
        } elseif ($video->type_id === config('constant.TYPES.MUSIC')) {
            $morelikethisData = $musicData;
            $subtitle = 'Musics';
            $viewall_route = route('musics.list');
        } elseif ($video->type_id === config('constant.TYPES.COMEDYSHOW')) {
            $morelikethisData = $comdeyData;
            $subtitle = 'Comedy Shows';
            $viewall_route = '#';
        } else {
            $morelikethisData = $movieData;
            $subtitle = 'Movies';
            $viewall_route = route('movies.list');
        }
    @endphp
    <br>
    @if ($video->type_id === config('constant.TYPES.COMEDYSHOW'))
        @if ($seasons->count())
            <section id="section-home-newdes" class="podcast">
                <div class="home-newdes-section">
                    <div class="home-newdes-section-container">
                        <div class="container-fluid">

                            {{-- ========== SEASON TABS ========== --}}
                            <ul class="nav nav-pills mb-3 mt-3" id="pills-tab" role="tablist">
                                @foreach ($seasons as $seasonNo => $episodes)
                                    <li class="nav-item" role="presentation">
                                        <button
                                            class="nav-link @if ($seasonNo == $currentSeasonNo) active @elseif($loop->first && !$seasons->has($currentSeasonNo)) active @endif"
                                            data-bs-toggle="pill" data-bs-target="#season-{{ $seasonNo }}"
                                            type="button" role="tab">
                                            Season {{ $seasonNo }}
                                        </button>
                                    </li>
                                    &nbsp;&nbsp;
                                @endforeach
                            </ul>

                            <span class="l-head">Episodes</span>

                            {{-- ========== TAB CONTENT ========== --}}
                            <div class="tab-content mt-4" id="pills-tabContent">
                                @foreach ($seasons as $seasonNo => $episodes)
                                    <div class="tab-pane fade @if ($seasonNo == $currentSeasonNo) show active @elseif($loop->first && !$seasons->has($currentSeasonNo)) show active @endif"
                                        id="season-{{ $seasonNo }}" role="tabpanel">

                                        <div class="slider-container">
                                            <div class="latest-release-slider owl-carousel owl-theme">

                                                @foreach ($episodes as $ep)
                                                    {{-- ✅ renamed to $ep to avoid $video conflict --}}
                                                    <div class="item">
                                                        <div class="cards-container">
                                                            <div class="alpha-card skeleton">

                                                                {{-- IMAGE --}}
                                                                <div class="image-wrapper">
                                                                    <a
                                                                        href="{{ route('view.details', ['id' => \App\Helpers\VideoHelper::encryptID($ep->id)]) }}">
                                                                        <img src="{{ config('app.website_url') }}admin_panel/public/images/video/{{ $ep->landscape }}"
                                                                            alt="" loading="lazy">
                                                                    </a>
                                                                    <p class="text-white">
                                                                        Season {{ $seasonNo }} | Episode
                                                                        {{ $ep->episode_no }}
                                                                    </p>
                                                                </div>

                                                                {{-- HOVER CARD --}}
                                                                <div
                                                                    class="alpha-card-large {{ $ep->trailer_url ? 'movie-card' : '' }}">

                                                                    <img src="{{ config('app.website_url') }}admin_panel/public/images/video/{{ $ep->landscape }}"
                                                                        class="thumbnail" loading="lazy">

                                                                    @if ($ep->trailer_url)
                                                                        <video class="video-holder" loop muted>
                                                                            <source
                                                                                src="{{ asset('storage/' . $ep->trailer_url) }}"
                                                                                type="video/mp4">
                                                                        </video>
                                                                    @endif

                                                                    <div class="content-holder pt-5">
                                                                        <div class="content">

                                                                            {{-- BUTTONS --}}
                                                                            <div class="button-holder row">
                                                                                <div class="col-9">
                                                                                    <a
                                                                                        href="{{ route('view.details', ['id' => \App\Helpers\VideoHelper::encryptID($ep->id)]) }}">
                                                                                        <button class="btn-watchnow">
                                                                                            <i
                                                                                                class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch
                                                                                            Now
                                                                                        </button>
                                                                                    </a>
                                                                                </div>
                                                                                <div class="col-3">
                                                                                    @include(
                                                                                        'watchlist-button',
                                                                                        [
                                                                                            'id' => $ep->id,
                                                                                            'type' => 'video',
                                                                                            'watchlist' =>
                                                                                                $watchlist ?? [],
                                                                                        ]
                                                                                    )
                                                                                </div>
                                                                            </div>

                                                                            {{-- META INFO --}}
                                                                            <ul class="mt-3">
                                                                                <li>{{ \Carbon\Carbon::parse($ep->release_date)->format('Y') }}
                                                                                </li>
                                                                                <li>&#x2022;</li>
                                                                                <li>
                                                                                    @if ($ep->duration)
                                                                                        {{ MillisecondsToTime($ep->duration) }}
                                                                                    @else
                                                                                        -
                                                                                    @endif
                                                                                </li>
                                                                                <li>&#x2022;</li>
                                                                                <li>Episode - {{ $ep->episode_no }}</li>
                                                                            </ul>

                                                                            <p class="mt-3 mb-4">
                                                                                {{ Str::words(strip_tags($ep->description), 15, '...') }}
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
                    </div>
                </div>
            </section>
        @endif
    @endif
    <hr>
    <br>
    @include('section-wise-common', [
        'class' => 'movies',
        'title' => 'More Like This',
        'sub_title' => $subtitle,
        'viewall_route' => $viewall_route,
        'id' => 'webseriesslider',
        'data' => $morelikethisData,
        'view_route' => 'view.details',
    ]);
    {{-- <script>
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
    </script> --}}
    <!-- Share Modal -->
    <div id="shareModal" class="share-modal">
        <div class="share-modal-content">
            <span class="close" onclick="closeShareModal()">&times;</span>
            <h3 class="text-lg font-semibold mb-4">Share</h3>

            <!-- Social Icons -->
            <div class="share-icons">
                <!-- WhatsApp -->
                <a href="https://wa.me/?text={{ urlencode(route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($video->id)])) }}"
                    target="_blank">
                    <i class="fa-brands fa-whatsapp" style="color:#25D366;"></i>
                    <span>WhatsApp</span>
                </a>

                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($video->id)])) }}"
                    target="_blank">
                    <i class="fa-brands fa-facebook" style="color:#1877F2;"></i>
                    <span>Facebook</span>
                </a>
                <!-- Instagram -->
                <a href="https://www.instagram.com/?url={{ urlencode(route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($video->id)])) }}"
                    target="_blank">
                    <i class="fa-brands fa-instagram"
                        style="background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);
                           -webkit-background-clip: text;
                           -webkit-text-fill-color: transparent;"></i>
                    <span>Instagram</span>
                </a>
                <!-- X (Twitter) -->
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($video->id)])) }}"
                    target="_blank">
                    <i class="fa-brands fa-x-twitter" style="color:#000;"></i>
                    <span>X</span>
                </a>


            </div>

            <!-- Copy Link Section -->
            <div class="copy-link-box">
                <input type="text" id="shareLink"
                    value="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($video->id)]) }}"
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
    <script>
        const video = document.querySelector('#player');

        if (video && video.tagName === 'VIDEO') {
            const sources = {
                '240': '{{ $video->video_240 ?? '' }}',
                '360': '{{ $video->video_320 ?? '' }}',
                '480': '{{ $video->video_480 ?? '' }}',
                '720': '{{ $video->video_720 ?? '' }}',
                '1080': '{{ $video->video_1080 ?? '' }}'
            };

            const player = new Plyr(video, {
                controls: [
                    'play-large', 'play', 'progress', 'current-time', 'duration',
                    'mute', 'volume', 'settings', 'fullscreen'
                ],
                settings: ['quality']
            });

            // ✅ QUALITY SWITCH
            function switchQuality(res) {
                const currentTime = player.currentTime || 0;
                const wasPlaying = !player.paused;

                const src = sources[res];
                if (!src) return;

                video.src = src;
                video.load();

                video.addEventListener('loadedmetadata', function _once() {
                    player.currentTime = currentTime;
                    if (wasPlaying) player.play().catch(() => {});
                    video.removeEventListener('loadedmetadata', _once);
                });
            }

            // ✅ WATCH TIME SAVE
            let lastSentTime = 0;
            player.on('timeupdate', () => {
                const sec = Math.floor(player.currentTime);
                if (sec !== lastSentTime) {
                    lastSentTime = sec;
                    saveWatchTime(sec);
                }
            });

            // ✅ RESUME TIME
            player.on('loadedmetadata', () => {
                if (resumeTime > 5) {
                    player.currentTime = resumeTime;
                }
            });

            // ✅ ADD CUSTOM CONTROLS (SAME LIKE HLS)
            addCustomControls(player);
        }

        function addCustomControls(player) {
            const controls = player.elements.controls;
            const container = player.elements.container;
            console.log('controls-->', controls, 'container-->', container);
            console.log('previousEpisodeUrl-->', typeof previousEpisodeUrl, previousEpisodeUrl);
            console.log('nextEpisodeUrl-->', typeof nextEpisodeUrl, nextEpisodeUrl);
            // ===== NEXT / PREVIOUS BUTTONS =====
            if (typeof previousEpisodeUrl !== 'undefined') {
                const prevBtn = document.createElement('button');
                prevBtn.className = 'plyr__prev-episode-btn';
                prevBtn.innerText = 'Previous';
                controls.appendChild(prevBtn);

                prevBtn.onclick = () => window.location.href = previousEpisodeUrl;
            }

            if (typeof nextEpisodeUrl !== 'undefined') {
                const nextBtn = document.createElement('button');
                nextBtn.className = 'plyr__next-episode-btn';
                nextBtn.innerText = 'Next';
                controls.appendChild(nextBtn);

                nextBtn.onclick = () => window.location.href = nextEpisodeUrl;

                // ===== COUNTDOWN =====
                const overlay = document.createElement('div');
                overlay.className = 'plyr__countdown-overlay';
                overlay.innerHTML = `
                <span class="up-next-label">Up Next</span>
                <span class="countdown-timer"></span>
            `;
                container.appendChild(overlay);

                const timer = overlay.querySelector('.countdown-timer');

                player.on('timeupdate', () => {
                    const remain = Math.floor(player.duration - player.currentTime);

                    if (remain <= 60 && remain > 0) {
                        overlay.style.display = 'block';
                        timer.innerText = `Next video in ${remain}s`;
                    } else {
                        overlay.style.display = 'none';
                    }
                });

                player.on('ended', () => {
                    window.location.href = nextEpisodeUrl;
                });
            }

            // ===== CENTER SKIP BUTTONS =====
const center = document.createElement('div');
center.className = 'plyr__center-controls';

const left = document.createElement('div');
left.className = 'plyr__center-skip';
left.innerHTML = `<div class="skip-text"><i class="fa-solid fa-angle-double-left"></i> 10 </div>
<div class="skip-icon"></div>`;

const right = document.createElement('div');
right.className = 'plyr__center-skip';
right.innerHTML = `<div class="skip-text">10 <i class="fa-solid fa-angle-double-right"></i></div>
<div class="skip-icon"></div>`;

center.appendChild(left);
center.appendChild(right);
container.appendChild(center);

left.onclick = () => player.currentTime = Math.max(player.currentTime - 10, 0);
right.onclick = () => player.currentTime = Math.min(player.currentTime + 10, player.duration);

// ✅ HIDE CENTER BUTTONS IN FULLSCREEN
player.on('enterfullscreen', () => {
    center.style.display = 'none';
});
player.on('exitfullscreen', () => {
    center.style.display = '';
});

// ===== DOUBLE CLICK SKIP (works in both modes) =====
container.addEventListener('dblclick', (e) => {
    const rect = container.getBoundingClientRect();
    const x = e.clientX - rect.left;

    if (x < rect.width / 2) {
        player.currentTime -= 10;
    } else {
        player.currentTime += 10;
    }
});
        }
    </script>
@endsection
