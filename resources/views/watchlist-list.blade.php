@extends('layouts.main')
@section('title')
    Alphastudioz | Watchlist
@endsection
<style>
    .owl-carousel .owl-nav.disabled {
        display: none !important;
    }
    .owl-carousel:hover .owl-nav.disabled {
        display: none !important;
    }
    .btn-watchlist {
        padding: 14px 18px !important;
    }
</style>
@section('main-section')
    <section id="section-home-newdes" class="podcast mt-5 pt-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid">
                    <span class="l-head">Your Watchlist</span>
                    <hr class="mb-2">
                    <div class="slider-container mt-2">
                        <div class="latest-release-slider movies-page-section">
                            <div class="row g-1">
                                @if(empty($watchlist))
                                <div class="col-12 text-center">
                                        {{-- Add your vector image here --}}
                                        <img src="{{ URL::to('asset/images/no-data-found.png') }}" alt="No Videos Found" class="img-fluid" style="max-width: 300px; margin-top: 10px;">
                                        <h3 class="text-white mt-3">No Videos Found</h3>
                                        <p class="text-white">Your watchlist is empty! Start adding videos to keep track of what you want to watch next.</p>
                                    </div>
                                @else
                                @foreach ($watchlist as $key => $item)
                                    @php
                                        $entity = null; // Ensure we clear previous entities
                                        if ($item['type'] === 'video') {
                                            $entity = App\Models\Video::find($item['id']);
                                            $source ='https://alphastudioz.in/admin_panel/public/images/video/' .$entity->landscape;
                                            $trailer_url = $entity->trailer_url;
                                            $redirect_watch_now = route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]);
                                        } elseif ($item['type'] === 'episode') {
                                            $entity = App\Models\Episodes::find($item['id']);
                                            $source ='https://alphastudioz.in/admin_panel/public/images/series/' .$entity->landscape;
                                            $trailer_url = $entity->trailer_url;
                                            $redirect_watch_now = route('webseries.episodes.view', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]);
                                        }elseif ($item['type'] === 'staticbanner') {
                                            $entity = App\Models\StaticBanners::find($item['id']);
                                            $source = $entity->landscape_url;
                                            $trailer_url = $entity->url;
                                            $redirect_watch_now = $entity->url;
                                        } elseif ($item['type'] === 'series') {
                                            $entity = App\Models\Series::find($item['id']);
                                            $source = 'https://alphastudioz.in/admin_panel/public/images/series/' .$entity->landscape_image;
                                            $trailer_url = $entity->url;
                                            $redirect_watch_now = route('webseries.view', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]);
                                        }
                                    @endphp
                                    {{-- @dd($entity) --}}
                                    <div
                                        class="item movie-new-card col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 col-xxl-2 ps-md-2 ps-sm-1 pe-sm-1 pe-md-2 mb-2 pe-1 ps-1">
                                        <div class="cards-container">
                                            <div class="alpha-card skeleton card-for-podcast">
                                                <div class="image-wrapper">
                                                    <a href="{{$redirect_watch_now}}">
                                                        <img src="{{ $source }}" class="card-img-top landscape"
                                                        alt="watchlistimage" id="image">
                                                    </a>
                                                </div>
                                                <div class="alpha-card-large {{ $trailer_url ? 'movie-card' : '' }}"
                                                    id="movie-card">
                                                    <img src="{{ $source }}" class="thumbnail" id="thumbnail"
                                                        alt="thumbnail">
                                                    <video class="video-holder" loop muted id="video-holder">
                                                        <source src="{{ $trailer_url }}" type="video/mp4">
                                                    </video>
                                                    <div class="content-holder pt-5">
                                                        {{-- <img src="{{url('/')}}/asset/images/tere-sang.png" class="title mt-5" alt=""> --}}
                                                        <div class="content">
                                                            <div class="button-holder row">
                                                                <div class="col-9">
                                                                    @if ($item['type'] === 'video')
                                                                        @if ($entity->type_id === config('constant.TYPES.MUSIC'))
                                                                            <a
                                                                                href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]) }}">
                                                                                <button class="btn-watchnow" type="button">
                                                                                    <i
                                                                                        class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                    Watch Now</button>
                                                                            </a>
                                                                        @elseif($entity->type_id === config('constant.TYPES.TRAILER'))
                                                                            <a
                                                                                href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]) }}">
                                                                                <button class="btn-watchnow" type="button">
                                                                                    <svg width="11" height="14"
                                                                                        viewBox="0 0 11 14" fill="none"
                                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                                        <path
                                                                                            d="M0.830078 0.491211V13.0715L10.7146 6.78134L0.830078 0.491211Z"
                                                                                            fill="black" />
                                                                                    </svg>
                                                                                    Watch Now</button>
                                                                            </a>
                                                                        @elseif($entity->type_id === config('constant.TYPES.PODCAST'))
                                                                            <a
                                                                                href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]) }}">
                                                                                <button class="btn-watchnow" type="button">
                                                                                    <i
                                                                                        class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                    Watch Now</button>
                                                                            </a>
                                                                        @else
                                                                            <a
                                                                                href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]) }}">
                                                                                <button class="btn-watchnow" type="button">
                                                                                    <i
                                                                                        class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                    Watch Now</button>
                                                                            </a>
                                                                        @endif
                                                                    @elseif ($item['type'] === 'episode')
                                                                        <a
                                                                            href="{{ route('webseries.episodes.view', [
                                                                                'id' => App\Helpers\VideoHelper::encryptID($entity->id)
                                                                            ]) }}">
                                                                            <button type="button" class="btn-watchnow">
                                                                                <i
                                                                                    class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch
                                                                                Now
                                                                            </button>
                                                                        </a>
                                                                    @elseif ($item['type'] === 'staticbanner')
                                                                        <a href="{{ $trailer_url }}">
                                                                            <button type="button" class="btn-watchnow">
                                                                                <i
                                                                                    class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch
                                                                                Now
                                                                            </button>
                                                                        </a>
                                                                    @elseif ($item['type'] === 'series')
                                                                        <a
                                                                            href="{{ route('webseries.episodes.view', [
                                                                                'id' => App\Helpers\VideoHelper::encryptID($entity->id)
                                                                            ]) }}">
                                                                            <button type="button" class="btn-watchnow">
                                                                                <i
                                                                                    class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch
                                                                                Now
                                                                            </button>
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                                <div class="col-3">
                                                                    <?php
                                                                    if ($item['type'] === 'video') {
                                                                        $type = 'video';
                                                                    } elseif ($item['type'] === 'episode') {
                                                                        $type = 'episode';
                                                                    } elseif ($item['type'] === 'staticbanner') {
                                                                        $type = 'staticbanner';
                                                                    } elseif ($item['type'] === 'series') {
                                                                        $type = 'series';
                                                                    }
                                                                    
                                                                    ?>
                                                                   @include('watchlist-button', ['id' => $entity->id, 'type' => $type, 'watchlist' => $watchlist])
                                                                </div>
                                                            </div>
                                                            @if ($item['type'] === 'video')
                                                                <ul class="animate__animated animate__fadeInUp mt-3">
                                                                    <li>{{ \Carbon\Carbon::parse($entity->release_date)->format('Y') }}
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li> {{ MillisecondsToTime($entity->video_duration) }}
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li>{{ $entity->language_name }}</li>
                                                                </ul>
                                                                <p class="mt-3 mb-4 animate__animated animate__fadeInUp">
                                                                    {{ Str::words(strip_tags($entity->description), 15, '...') }}
                                                                </p>
                                                            @elseif ($item['type'] === 'episode')
                                                                <ul class="animate__animated animate__fadeInUp mt-3">
                                                                    <li>{{ \Carbon\Carbon::parse($entity->created_at)->format('Y') }}
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li> {{ MillisecondsToTime($entity->episode_duration) }}
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li>Episodes- {{ $entity->episode_no }}</li>
                                                                </ul>
                                                                <p class="mt-3 mb-4 animate__animated animate__fadeInUp">
                                                                    {{ Str::words(strip_tags($entity->description), 15, '...') }}
                                                                </p>
                                                            @elseif ($item['type'] === 'staticbanner')
                                                                <ul class="animate__animated animate__fadeInUp mt-3">
                                                                    <li>{{ $entity->release_description }}</li>
                                                                </ul>
                                                                <p class="mt-3 mb-4 animate__animated animate__fadeInUp">
                                                                    {{ Str::words(strip_tags($entity->title), 15, '...') }}
                                                                </p>
                                                            @elseif ($item['type'] === 'series')
                                                                <ul class="animate__animated animate__fadeInUp mt-3">
                                                                    <li>{{ \Carbon\Carbon::parse($entity->release_date)->format('Y') }}
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li>{{ $entity->season }}</li>
                                                                    <li>&#x2022;</li>
                                                                    <li>Total Episodes - {{ $entity->total_episode }}</li>
                                                                </ul>
                                                                <p class="mt-1 mb2 animate__animated animate__fadeInUp">
                                                                    {{ Str::words(strip_tags($entity->description), 15, '...') }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="{{ URL::to('asset/js/gridView.js') }}"></script>
@endsection
