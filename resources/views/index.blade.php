@extends('layouts.main')
@section('title')
    Alphastudioz | Home
@endsection
@section('main-section')
    {{-- Banner  --}}
    <section id="new-banner-section">
        <div class="banner-section-coontainer">
            <div class="banner-section-holder">
                <div class="banner-slider owl-carousel owl-theme" data-skip-carousel="true" id="new-banner">
                    @foreach ($banner as $b)
                        <div class="item">
                            <img src="{{ $b->landscape_url }}" class="video-banner" alt="">
                            <img src="{{ $b->thumbnail_url }}" class="video-banner-mobile d-none" alt="">
                            <div class="content-holder">
                                <div class="overlay">
                                    <div class="banner-details">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h4 class="text-white mb-3 animate__animated animate__fadeInUp">
                                                    {{ $b->title }}</h4>
                                                <ul class="animate__animated animate__fadeInUp">
                                                    <li> {{ $b->release_description }}</li>
                                                </ul>
                                                <p class="mt-3 animate__animated animate__fadeInUp">{{ $b->category }}
                                                </p>
                                                <div class="button-holder  animate__animated animate__fadeInUp">
                                                    <div class="row" style="display: ruby-text;">
                                                        {{-- <a href="{{ route('view.details', ['id' => $b->id]) }}"> --}}
                                                        <a href="{{ $b->url }}">
                                                            <button class="btn-watch-now" type="button">
                                                                <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                Watch Now</button></a>

                                                        @include('watchlist-button', ['id' => $b->id, 'type' => 'staticbanner', 'watchlist' => $watchlist])
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="sync2" class="owl-carousel owl-theme" data-skip-carousel="true">
                    @foreach ($banner as $b)
                        <div class="item">
                            <div class="thumbnail-holder">
                                <img src="{{ $b->landscape_url }}" alt="" class="landscape-banner">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <br>
    {{-- watchlist  --}}
    @if (!empty($watchlist))
        <section id="section-home-newdes" class="watchlist">
            <div class="home-newdes-section">
                <div class="home-newdes-section-container">
                    <div class="container-fluid">
                        <div class="homepage-heading d-flex justify-content-between align-items-center pe-3">
                            <span class="l-head">Your Watchlists</span>
                            <a href="{{ route('watchlist.list') }}" class="l-sub-head text-white text-decoration-none">
                                View All
                                >
                            </a>
                        </div>
                        <div class="slider-container">
                            <div class="latest-release-slider owl-carousel owl-theme" id="movieslider">
                               
                                @foreach ($watchlist as $key => $item)
                                    @php
                                        $entity = null; // Ensure we clear previous entities
                                        if ($item['type'] === 'video') {
                                            $entity = App\Models\Video::find($item['id']);
                                            $source = "https://alphastudioz.in/admin_panel/public/images/video/".$entity->landscape;
                                            $trailer_url =  $entity->trailer_url;
                                            $redirect_watch_now = route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]);
                                        }elseif ($item['type'] === 'episode') {
                                            $entity = App\Models\Episodes::find($item['id']);
                                            $source = "https://alphastudioz.in/admin_panel/public/images/series/".$entity->landscape;
                                            $trailer_url =  $entity->trailer_url;
                                            $redirect_watch_now = route('webseries.episodes.view', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]);
                                        } elseif($item['type'] === 'staticbanner'){
                                            $entity = App\Models\StaticBanners::find($item['id']);
                                            $source =  $entity->landscape_url;
                                            $trailer_url =  $entity->url;
                                            $redirect_watch_now = $entity->url;
                                        }elseif($item['type'] === 'series'){
                                            $entity = App\Models\Series::find($item['id']);
                                            $source =  ($entity->landscape_image !== null) ?  "https://alphastudioz.in/admin_panel/public/images/series/".$entity->landscape_image  : '';
                                            $trailer_url =  $entity->url;
                                            $redirect_watch_now = route('webseries.view', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]);
                                        }
                                    @endphp
                                        
                                    <div class="item">
                                        <div class="cards-container">
                                            <div class="alpha-card skeleton">
                                                <div class="image-wrapper">
                                                    <a href="{{$redirect_watch_now}}"><img src="{{$source}}" alt="watchlistimage"></a>
                                                </div>
                                                <div class="alpha-card-large {{ $trailer_url ? 'movie-card' : '' }}"
                                                    id="movie-card">
                                                    <img src="{{$source}}" class="thumbnail" id="thumbnail" alt="thumbnail">
                                                   
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
                                                                                <button class="btn-watchnow"
                                                                                    type="button">
                                                                                    <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                    Watch Now</button>
                                                                            </a>
                                                                        @elseif($entity->type_id === config('constant.TYPES.TRAILER'))
                                                                            <a
                                                                                href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]) }}">
                                                                                <button class="btn-watchnow"
                                                                                    type="button">
                                                                                    <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                    Watch Now</button>
                                                                            </a>
                                                                        @elseif($entity->type_id === config('constant.TYPES.PODCAST'))
                                                                            <a
                                                                                href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]) }}">
                                                                                <button class="btn-watchnow"
                                                                                    type="button">
                                                                                    <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                    Watch Now</button>
                                                                            </a>
                                                                        @else
                                                                            <a
                                                                                href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($entity->id)]) }}">
                                                                                <button class="btn-watchnow"
                                                                                    type="button">
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
                                                                                <i class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch Now
                                                                            </button>
                                                                        </a>
                                                                    @elseif ($item['type'] === 'staticbanner')
                                                                        <a
                                                                            href="{{ $trailer_url }}">
                                                                            <button type="button" class="btn-watchnow">
                                                                                <i class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch Now
                                                                            </button>
                                                                        </a>
                                                                    @elseif ($item['type'] === 'series')
                                                                        <a
                                                                            href="{{ route('webseries.view', [
                                                                                'id' => App\Helpers\VideoHelper::encryptID($entity->id)
                                                                            ]) }}"><button type="button" class="btn-watchnow">
                                                                                <i class="fa-solid fa-play"></i>&nbsp;&nbsp;Watch Now
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
                                                                <li>Total Episodes - {{$entity->total_episode}}</li>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    {{-- upcoming --}}
    @if (count($upcomming) > 0)
        @include('section-wise-common', ['class'=>'upcoming','title'=>'Upcomings','viewall_route'=> route('trailers.list'),'id' => 'upcomings','data' => $upcomming, 'view_route'=>'view.details'])
    @endif
    {{-- trailer --}}
    @if (count($trailer) > 0)
        @include('section-wise-common', ['class'=>'trailer','title'=>'Trailers','viewall_route'=> route('trailers.list'),'id' => 'trailers','data' => $trailer, 'view_route'=>'view.details'])
    @endif
    {{-- podcast  --}}
    @if (count($poadcastData) > 0)
        @include('section-wise-common', ['class'=>'podcast','title'=>'Podcasts','viewall_route'=> route('podcasts.list'),'id' => 'podcastslider','data' => $poadcastData, 'view_route'=>'view.details'])
    @endif
    {{-- short films --}}
    @if (count($shortfilms) > 0)
        @include('section-wise-common', ['class'=>'shortfilms','title'=>'Short Films','viewall_route'=> route('shortfilms.list'),'id' => 'shortfilmslider','data' => $shortfilms, 'view_route'=>'view.details'])    
    @endif
    {{-- webseries --}}
    @if (count($series) > 0)
        <section id="section-home-newdes" class="webseries">
            <div class="home-newdes-section">
                <div class="home-newdes-section-container">
                    <div class="container-fluid">
                        <div class="homepage-heading d-flex justify-content-between align-items-center pe-3">
                            <span class="l-head">Web Series </span>
                            <a href="{{ route('webseries.list') }}" class="l-sub-head text-white text-decoration-none">
                                View All
                                >
                            </a>
                        </div>
                        <div class="slider-container">
                            <div class="latest-release-slider owl-carousel owl-theme" id="webseriesslider">
                                {{-- @dd($series,$watchlist) --}}
                                @foreach ($series as $item)
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
                                                                    @include('watchlist-button', ['id' => $item->id, 'type' => 'series', 'watchlist' => $watchlist])
                                                                </div>
                                                            </div>
                                                            <ul class="animate__animated animate__fadeInUp mt-3">
                                                                <li>{{ \Carbon\Carbon::parse($item->release_date)->format('Y') }}
                                                                </li>
                                                                <li>&#x2022;</li>
                                                                <li>{{ $item->season }}</li>
                                                                <li>&#x2022;</li>
                                                                <li>Total Episodes - {{$item->total_episode}}</li>
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
    @endif
    {{-- khulps --}}
    @if (count($khlup) > 0)
        <section id="section-home-newdes" class="khulps">
            <div class="home-newdes-section">
                <div class="home-newdes-section-container">
                    <div class="container-fluid">
                        <div class="homepage-heading d-flex justify-content-between align-items-center pe-3">
                            <span class="l-head">Khlups</span>
                            <a href="#" class="l-sub-head text-white text-decoration-none">
                                View All
                                >
                            </a>
                        </div>
                        <div class="slider-container">
                            <div class="latest-release-slider owl-carousel owl-theme" id="khlup">
                                @foreach ($khlup as $item)
                                    <div class="item">
                                        <a href="{{ route('khlups.view', ['tokengen' => $item->id]) }}">
                                            <div class="klup-card skeleton">
                                                <div class="khlup-thumbnail">
                                                    <img src="{{ $item->thumbnail_url }}" alt="">
                                                </div>
                                                <div class="khlup-overlay">
                                                    <i class="icon-play khlup-play"></i>
                                                    <div class="content-holder">
                                                        <p class="mb-2">{{ $item->name }}</p>
                                                        <div class="desc">
                                                            {{ Str::words(strip_tags($item->description), 10, '...') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>
        </section>
    @endif
    {{-- music --}}
    @if (count($music) > 0)
        @include('section-wise-common', ['class'=>'music','title'=>'Music','viewall_route'=> '#','id' => 'musicsslider','data' => $music, 'view_route'=>'view.details'])    
    @endif
    {{-- movies --}}
    @if (count($movies) > 0)
        @include('section-wise-common', ['class'=>'movies','title'=>'Movies','viewall_route'=> route('movies.list'),'id' => 'moviesliderhome','data' => $movies, 'view_route'=>'view.details'])    
    @endif

    @php
        // Check if the user is authenticated
        if (Auth::check()) {
            // Get the authenticated user
            $user = app('logged-in-user');
            // Fetch the latest transaction for the user
            $transaction = DB::table('transaction')
                ->where('user_id', $user->id)
                ->orderBy('expiry_date', 'desc')
                ->first();
            $daysLeft = 0; // Default days left
            $subscriptionStatus = 'No'; // Default subscription status
            // Check if a transaction was found
            if ($transaction) {
                // Get the expiry date from the transaction
                $expiryDate = new DateTime($transaction->expiry_date);
                // Get the current date
                $currentDate = new DateTime();
                // Calculate the difference
                $interval = $currentDate->diff($expiryDate);
                // Get the number of days
                $daysLeft = $interval->format('%r%a'); // %r includes the sign to handle past dates correctly
                // Check if the plan is expired
                if ($daysLeft <= 0) {
                    // Update the user's subscription status to 'No'
            DB::table('users')
                ->where('id', $user->id)
                ->update(['subscription' => 'No']);
            // Update the transaction status to '0'
            DB::table('transaction')
                ->where('id', $transaction->id)
                ->update(['status' => '0']);
            $subscriptionStatus = 'No';
        } else {
            $subscriptionStatus = 'Yes';
                }
            }
        }
    @endphp
    <!-- Pass PHP variables to JavaScript -->
    <script>
        var daysLeft = {{ $daysLeft ?? 0 }};
        var subscriptionStatus = "{{ $subscriptionStatus ?? 'No' }}";
    </script>
@endsection
