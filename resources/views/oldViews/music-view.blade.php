@extends('layouts.main')
@section('title')
    Alphastudioz | Music
@endsection
@section('main-section')
    
    @include('video-upload-type-common',['video'=>$video])

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
                                    <p class="mb-2 mt-2">{{ Str::words(strip_tags($video->description),20, '...') }}</p>
                                    <div class="d-flex position-relative">
                                        @if (app('logged-in-user')->subscription == 'No')
                                            <a href="{{ route('subscribe') }}">
                                                <button type="button" class="btn-watchnow "> <svg width="11"
                                                        height="14" viewBox="0 0 11 14" fill="#ffffff"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M0.830078 0.491211V13.0715L10.7146 6.78134L0.830078 0.491211Z"
                                                            fill="#FFFFFF" />
                                                    </svg> Subscribe To Watch</button>
                                            </a>
                                        @else
                                            <a href="#">
                                                <button type="button" class="btn-watchnow watch-now"> 
                                                    <i class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch Now</button>
                                            </a>
                                        @endif
                                        @if (in_array(['id' => $video->id, 'type' => 'video'], $watchlist) ||
                                                in_array(['id' => $video->id, 'type' => 'episode'], $watchlist))
                                            <form action="{{ route('watchlist.remove') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="video_id" value="{{ $video->id }}">
                                                <input type="hidden" name="type" value="video">
                                                <button type="submit" class="btn-watchlist ms-2" data-toggle="tooltip" data-placement="top"
                                                                                title="Remove from Watch List">
                                                    <svg width="24" height="13" viewBox="0 0 24 13" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="2.00845" height="9.87727" rx="1.00423"
                                                            transform="matrix(0.851668 -0.524082 0.556629 0.830761 0 4.08691)"
                                                            fill="white" />
                                                        <rect width="2.19463" height="21.0917" rx="1.09731"
                                                            transform="matrix(0.553502 0.832848 -0.853569 0.52098 22.7852 0)"
                                                            fill="white" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('watchlist') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="video_id" value="{{ $video->id }}">
                                                <input type="hidden" name="type" value="video">
                                                <button type="submit" class="btn-watchlist ms-2">
                                                    <svg width="18" height="20" viewBox="0 0 18 20" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <rect x="8.45654" y="0.34668" width="1.61563" height="18.8704"
                                                            rx="0.807817" fill="white" />
                                                        <rect x="17.7461" y="8.88379" width="1.79718" height="16.9642"
                                                            rx="0.89859" transform="rotate(90 17.7461 8.88379)"
                                                            fill="white" />
                                                    </svg>
                                                    
                                                </button>
                                            </form>
                                        @endif
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
                    <video controls autoplay playsinline poster="{{ $video->landscape_url }}" preload="auto"
                        id="player" style="display: {{ app('logged-in-user')->subscription == 'Yes' ? 'block' : 'none' }};"
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
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- More Like This --}}
    @include('section-wise-common', ['class'=>'music','title'=>'More Like This','viewall_route'=> route('musics.list'),'id' => 'webseriesslider','data' => $music, 'view_route'=>'view.details'])    
  
   
@endsection
