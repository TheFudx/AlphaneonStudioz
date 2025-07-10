@extends('layouts.main')
@section('title')
    Alphastudioz | Your Mood
@endsection
@section('main-section')
    <section id="section-home-newdes" class="podcast mt-5 pt-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid">
                    <span class="l-head">
                        {{-- According your mood :  --}}
                        {{-- @foreach ($catlist as $item)
                                @if ($item->id == $id)
                                {{ $item->name }}
                            @endif
                        @endforeach --}} {{ $name }}
                    </span>

                    <hr class="mb-3">
                    <div class="slider-container mt-2">
                        <div class="latest-release-slider movies-page-section">
                            <div class="row g-1">
                                @if($video->isEmpty())
                                    <div class="col-12 text-center">
                                        {{-- Add your vector image here --}}
                                        <img src="{{ URL::to('asset/images/no-data-found.png') }}" alt="No Videos Found" class="img-fluid" style="max-width: 300px; margin-top: 10px;">
                                        <h3 class="text-white mt-3">No Videos Found</h3>
                                        <p class="text-white">It seems there are no videos for this mood yet. Please check back later!</p>
                                    </div>
                                @else
                                    @foreach ($video as $v)
                                    {{-- <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3"> --}}
                                        <div class="item movie-new-card col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 col-xxl-2 ps-md-2 ps-sm-1 pe-sm-1 pe-md-2 mb-2 pe-1 ps-1"
                                            data-category-id="{{ $v->category_id }}" >
                                            @if (isset($v->season))
                                                <div class="cards-container">
                                                <div class="alpha-card skeleton">
                                                    <div class="image-wrapper">
                                                        <a
                                                            href="{{ route('webseries.view', ['id' => App\Helpers\VideoHelper::encryptID($v->id)]) }}"><img
                                                                src="https://alphastudioz.in/admin_panel/public/images/series/{{ $v->landscape_image }}"
                                                                alt=""></a>
                                                    </div>
                                                    <div class="alpha-card-large {{ $v->trailer_url ? 'movie-card' : '' }}"
                                                        id="movie-card">
                                                        <img src="https://alphastudioz.in/admin_panel/public/images/series/{{ $v->landscape_image }}"
                                                            class="thumbnail" id="thumbnail" alt="">
                                                        <video class="video-holder" loop muted id="video-holder">
                                                            <source src="{{ $v->trailer_url }}" type="video/mp4">
                                                        </video>
                                                        <div class="content-holder">
                                                            {{-- <img src="{{url('/')}}/asset/images/tere-sang.png" class="title mt-5" alt=""> --}}
                                                            <div class="content">
                                                                <div class="button-holder row">
                                                                    <div class="col-9">
                                                                        <a
                                                                            href="{{ route('webseries.view', ['id' => App\Helpers\VideoHelper::encryptID($v->id)]) }}">
                                                                            <button class="btn-watchnow" type="button">
                                                                                <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                Watch Now</button></a>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        @include('watchlist-button', ['id' => $v->id, 'type' => 'series', 'watchlist' => $watchlist])
                                                                    </div>
                                                                </div>
                                                                <ul class="animate__animated animate__fadeInUp mt-3">
                                                                    <li>{{ \Carbon\Carbon::parse($v->release_date)->format('Y') }}
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li>{{ $v->season }}</li>
                                                                    <li>&#x2022;</li>
                                                                    <li>Total Episodes - {{$v->total_episode}}</li>
                                                                </ul>
                                                                <p class="mt-1 mb2 animate__animated animate__fadeInUp">
                                                                    {{ Str::words(strip_tags($v->description), 15, '...') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @else
                                                <div class="cards-container">
                                                    <div class="alpha-card skeleton">
                                                        <div class="image-wrapper">
                                                            <a
                                                                href="{{ route('view.details', ['name' => $v->name, 'id' => App\Helpers\VideoHelper::encryptID($v->id)]) }}">
                                                                <img src="https://alphastudioz.in/admin_panel/public/images/video/{{ $v->landscape }}"
                                                                    alt=""></a>
                                                        </div>
                                                        <div class="alpha-card-large {{ $v->trailer_url ? 'movie-card' : '' }}"
                                                            id="movie-card">
                                                            <img src="https://alphastudioz.in/admin_panel/public/images/video/{{ $v->landscape }}"
                                                                class="thumbnail" id="thumbnail" alt="">
                                                            <video class="video-holder" loop muted id="video-holder">
                                                                <source src="{{ $v->trailer_url }}" type="video/mp4">
                                                            </video>
                                                            <div class="content-holder pt-5">
                                                                {{-- <img src="{{url('/')}}/asset/images/tere-sang.png" class="title mt-5" alt=""> --}}
                                                                <div class="content">
                                                                    <div class="button-holder row">
                                                                        <div class="col-9">
                                                                            <a
                                                                                href="{{ route('view.details', ['name' => $v->name, 'id' => App\Helpers\VideoHelper::encryptID($v->id)]) }}">
                                                                                <button class="btn-watchnow" type="button">
                                                                                    <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                    Watch Now</button></a>
                                                                        </div>
                                                                        <div class="col-3">
                                                                        @include('watchlist-button', ['id' => $v->id, 'type' => 'video', 'watchlist' => $watchlist])
                                                                        </div>
                                                                    </div>
                                                                    <ul class="animate__animated animate__fadeInUp mt-3">
                                                                        <li>{{ \Carbon\Carbon::parse($v->release_date)->format('Y') }}
                                                                        </li>
                                                                        <li>&#x2022;</li>
                                                                        <li> {{ MillisecondsToTime($v->video_duration) }} </li>
                                                                        <li>&#x2022;</li>
                                                                    </ul>
                                                                    <p class="mt-3 mb-4 animate__animated animate__fadeInUp">
                                                                        {{ Str::words(strip_tags($v->description), 15, '...') }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    {{-- </div> --}}
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="{{URL::to('asset/js/gridView.js')}}"></script>
@endsection