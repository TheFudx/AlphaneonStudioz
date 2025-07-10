@extends('layouts.main')
@section('title')
    Alphastudioz | Details
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
                                    <p class="mb-2 mt-2">{{ Str::words(strip_tags($video->description), 20, '...') }}</p>
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
                                            <a href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($video->id)]) }}">
                                                <button class="btn-watchnow watch-now" type="button">
                                                    <i class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch Now
                                                </button>
                                            </a>
                                        @endif
                                            @include('watchlist-button', ['id' => $video->id, 'type' => 'video', 'watchlist' => $watchlist])
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

    {{-- More like this  --}}
    @php
        if($video->type_id === config('constant.TYPES.SHORTFILMS')){
            $morelikethisData = $shortfilmsData;
        }elseif($video->type_id === config('constant.TYPES.PODCAST')){
            $morelikethisData = $poadcastData;
        }elseif($video->type_id === config('constant.TYPES.TRAILER')){
            $morelikethisData = $trailerData;
        }elseif($video->type_id === config('constant.TYPES.MUSIC')){
            $morelikethisData = $musicData;
        }else{
            $morelikethisData = $movieData;
        }
    @endphp

    @include('section-wise-common', ['class'=>'movies','title'=>'More Like This','viewall_route'=> route('movies.list'),'id' => 'webseriesslider','data' => $morelikethisData, 'view_route'=>'view.details'])    
  
@endsection
