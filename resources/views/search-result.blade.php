@extends('layouts.main')
@section('title')
    Alphastudioz | Search
@endsection
@section('main-section')
    <style>
        .search-bar {
            background-color: #242631;
            /* Dark background */
            border-radius: 8px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 100%;
            color: #aaa;
        }

        .search-bar i {
            font-size: 18px;
            margin-right: 10px;
            color: #8e8e9c;
            font-size: 20px;
        }

        .search-bar input {
            border: none;
            background: transparent;
            color: #ccc;
            width: 100%;
            outline: none;
            font-size: 20px;
        }

        .search-bar input::placeholder {
            opacity: 0.3;
        }

        /* search result  */
        #searchResultsNew {
            /* position: absolute; */
            /* background: #151515; */
            color: white;
            padding: 20px;
            margin-left: 5%;
            margin-top: 4px;
            width: 90%;
            max-width: 90%;
            align-items: center;
            height: 300px;
            /* Or set a fixed height for a scrollbar to appear consistently */
            /* top: 200px; */
            /* left: 8%; */
            display: none;
            overflow: auto;
            /* This is the key property */
        }

        #searchResultsNew .search-result-new {
            border-bottom: 1px solid #B01803;
            padding-top: 15px;
        }

        #searchResultsNew .search-result-new a {
            color: #FFFFFF;
            text-decoration: none;
        }

        #searchResultsNew .search-result-new h3 {
            font-size: 18px;
            font-weight: 500;
        }

        #searchResultsNew .search-result-new p {
            font-size: 13px;
        }

        @media (max-width: 540px) {
            #searchResultsNew {
                width: 100%;
            }

            .search-bar input {
                font-size: 15px;
            }

            #searchResultsNew .search-result-new h3 {
                font-size: 10px;
            }

            #searchResultsNew .search-result-new p {
                font-size: 9px;
            }

            #searchResultsNew .search-result-new ul li {
                font-size: 9px;
            }
        }

        @media (max-width: 767.98px) {
            #searchResultsNew .search-result-new h3 {
                font-size: 10px;
            }

            #searchResultsNew .search-result-new p {
                font-size: 9px;
            }

            #searchResultsNew .search-result-new ul li {
                font-size: 9px;
            }
        }

        /* Or dynamically calculate this based on #searchResultsNew height */
        /* .footer-pushed-down {
                margin-top: 350px !important;
            } */
    </style>
    @php
        if (isset($_GET['keyword'])) {
            $value = $_GET['keyword'];
        } else {
            $value = '';
        }
    @endphp
    <section id="section-home-newdes" class="podcast mt-5 pt-5 mb-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid mt-2">
                    <form action="{{ route('search') }}" method="GET">
                        <div class="search-bar mx-auto">
                            <i class="fas fa-search"></i>
                            <input type="text" name="keyword" id="searchInputNew" value="{{ $value }}"
                                placeholder="Movies, shows and more" autocomplete="off">
                            <i class="fa fa-times" aria-hidden="true" onclick="ClearInput();"></i>
                        </div>
                    </form>
                    <div id="searchResultsNew" class="bg-dark">
                        <!-- Search results will be displayed here -->
                    </div>
                    <div class="col-12 text-center" id="searchimageDiv">
                        {{-- Add your vector image here --}}
                        <img src="{{ URL::to('asset/images/searching.png') }}" alt="Search" class="img-fluid"
                            style="max-width: 300px; margin-top: 20px;">
                        <h3 class="text-white mt-3">Ready to Discover Something New?</h3>
                        <p class="text-white">Enter a movie title, a show, or a podcast to find your next favorite video!</p>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
    @if ($value != '')
        {{-- trailers  --}}
        @if (!empty($trailers) && count($trailers) > 0)
            <section id="section-home-newdes" class="podcast ">
                <div class="home-newdes-section">
                    <div class="home-newdes-section-container">
                        <div class="container-fluid">
                            <div class="d-flex justify-content-between align-items-center pe-3">
                                <span class="l-head">Search Result for Trailers</span>
                                </p>
                            </div>
                            <div class="slider-container mt-4">
                                <div class="latest-release-slider owl-carousel owl-theme" id="trailersupcoming">
                                    @foreach ($trailers as $p)
                                        <div class="item">
                                            <div class="cards-container">
                                                <div class="alpha-card skeleton">
                                                    <div class="image-wrapper">
                                                        <a
                                                            href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($p->id)]) }}"><img
                                                                src="https://alphastudioz.in/admin_panel/public/images/video/{{ $p->landscape }}"
                                                                alt=""></a>
                                                    </div>
                                                    <div class="alpha-card-large {{ $p->trailer_url ? 'movie-card' : '' }}"
                                                        id="movie-card">
                                                        <img src="https://alphastudioz.in/admin_panel/public/images/video/{{ $p->landscape }}"
                                                            class="thumbnail" id="thumbnail" alt="">
                                                        <video class="video-holder" loop muted id="video-holder">
                                                            <source src="{{ $p->trailer_url }}" type="video/mp4">
                                                        </video>
                                                        <div class="content-holder pt-5">
                                                            {{-- <img src="{{url('/')}}/asset/images/tere-sang.png" class="title mt-5" alt=""> --}}
                                                            <div class="content">
                                                                <div class="button-holder row">
                                                                    <div class="col-9">
                                                                        <a
                                                                            href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($p->id)]) }}">
                                                                            <button class="btn-watchnow" type="button">
                                                                                <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                Watch Now</button></a>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        @include('watchlist-button', [
                                                                            'id' => $p->id,
                                                                            'type' => 'video',
                                                                            'watchlist' => $watchlist,
                                                                        ])
                                                                    </div>
                                                                </div>
                                                                <ul class="animate__animated animate__fadeInUp mt-3">
                                                                    <li>{{ \Carbon\Carbon::parse($p->release_date)->format('Y') }}
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li>
                                                                        @if ($p->video_duration)
                                                                            {{ MillisecondsToTime($p->video_duration) }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li> {{ $p->language_name }}</li>
                                                                </ul>
                                                                <p class="mt-3 mb-4 animate__animated animate__fadeInUp">
                                                                    {{ Str::words(strip_tags($p->description), 15, '...') }}
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
        {{-- podcast  --}}
        @if (!empty($podcast) && count($podcast) > 0)
            <section id="section-home-newdes" class="podcast ">
                <div class="home-newdes-section">
                    <div class="home-newdes-section-container">
                        <div class="container-fluid">
                            <div class="d-flex justify-content-between align-items-center pe-3">
                                <span class="l-head">Search Result for Podcast</span>
                            </div>
                            <div class="slider-container mt-4">
                                <div class="latest-release-slider owl-carousel owl-theme" id="podcastslider">
                                    @foreach ($podcast as $p)
                                        <div class="item">
                                            <div class="cards-container">
                                                <div class="alpha-card skeleton">
                                                    <div class="image-wrapper">
                                                        <a
                                                            href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($p->id)]) }}"><img
                                                                src="https://alphastudioz.in/admin_panel/public/images/video/{{ $p->landscape }}"
                                                                alt=""></a>
                                                    </div>
                                                    <div class="alpha-card-large {{ $p->trailer_url ? 'movie-card' : '' }}"
                                                        id="movie-card">
                                                        <img src="https://alphastudioz.in/admin_panel/public/images/video/{{ $p->landscape }}"
                                                            class="thumbnail" id="thumbnail" alt="">
                                                        <video class="video-holder" loop muted id="video-holder">
                                                            <source src="{{ $p->trailer_url }}" type="video/mp4">
                                                        </video>
                                                        <div class="content-holder pt-5">
                                                            {{-- <img src="{{url('/')}}/asset/images/tere-sang.png" class="title mt-5" alt=""> --}}
                                                            <div class="content">
                                                                <div class="button-holder row">
                                                                    <div class="col-9">
                                                                        <a
                                                                            href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($p->id)]) }}">
                                                                            <button class="btn-watchnow" type="button">
                                                                                <i
                                                                                    class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                Watch Now</button></a>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        @include('watchlist-button', [
                                                                            'id' => $p->id,
                                                                            'type' => 'video',
                                                                            'watchlist' => $watchlist,
                                                                        ])
                                                                    </div>
                                                                </div>
                                                                <ul class="animate__animated animate__fadeInUp mt-3">
                                                                    <li>{{ \Carbon\Carbon::parse($p->release_date)->format('Y') }}
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li>
                                                                        @if ($p->video_duration)
                                                                            {{ MillisecondsToTime($p->video_duration) }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li> {{ $p->language_name }}</li>
                                                                </ul>
                                                                <p class="mt-3 mb-4 animate__animated animate__fadeInUp">
                                                                    {{ Str::words(strip_tags($p->description), 15, '...') }}
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
        {{-- music --}}
        @if (!empty($music) && count($music) > 0)
            <section id="section-home-newdes" class="podcast ">
                <div class="home-newdes-section">
                    <div class="home-newdes-section-container">
                        <div class="container-fluid">
                            <div class="d-flex justify-content-between align-items-center pe-3">
                                <span class="l-head">Search Result for Music </span>
                            </div>
                            <div class="slider-container mt-4">
                                <div class="latest-release-slider owl-carousel owl-theme" id="musicsslider">
                                    @foreach ($music as $songs)
                                        <div class="item">
                                            <div class="cards-container">
                                                <div class="alpha-card skeleton">
                                                    <div class="image-wrapper">
                                                        <a
                                                            href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($songs->id)]) }}"><img
                                                                src="https://alphastudioz.in/admin_panel/public/images/video/{{ $songs->landscape }}"
                                                                alt=""></a>
                                                    </div>
                                                    <div class="alpha-card-large {{ $songs->trailer_url ? 'movie-card' : '' }}"
                                                        id="movie-card">
                                                        <img src="https://alphastudioz.in/admin_panel/public/images/video/{{ $songs->landscape }}"
                                                            class="thumbnail" id="thumbnail" alt="">
                                                        <video class="video-holder" loop muted id="video-holder">
                                                            <source
                                                                src="https://alphastudioz.in/admin_panel/public/images/video/o_1i70n3m6f1ep61cbotslp6114eq1e.mp4"
                                                                type="video/mp4">
                                                        </video>
                                                        <div class="content-holder pt-5">
                                                            {{-- <img src="{{url('/')}}/asset/images/tere-sang.png" class="title mt-5" alt=""> --}}
                                                            <div class="content">
                                                                <div class="button-holder row">
                                                                    <div class="col-9">
                                                                        <a
                                                                            href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($songs->id)]) }}">
                                                                            <button class="btn-watchnow" type="button">
                                                                                <i
                                                                                    class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                Watch Now</button></a>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        @include('watchlist-button', [
                                                                            'id' => $songs->id,
                                                                            'type' => 'video',
                                                                            'watchlist' => $watchlist,
                                                                        ])
                                                                    </div>
                                                                </div>
                                                                <ul class="animate__animated animate__fadeInUp mt-3">
                                                                    <li>{{ \Carbon\Carbon::parse($songs->release_date)->format('Y') }}
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li>
                                                                        @if ($songs->video_duration)
                                                                            {{ MillisecondsToTime($songs->video_duration) }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li> {{ $songs->language_name }}</li>
                                                                </ul>
                                                                <p class="mt-3 mb-4 animate__animated animate__fadeInUp">
                                                                    {{ Str::words(strip_tags($songs->description), 15, '...') }}
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
        {{-- webseries --}}
        @if (!empty($series) && count($series) > 0)
            <section id="section-home-newdes" class="podcast ">
                <div class="home-newdes-section">
                    <div class="home-newdes-section-container">
                        <div class="container-fluid">
                            <div class="d-flex justify-content-between align-items-center pe-3">
                                <span class="l-head">Search Result for Web Series </span>
                            </div>
                            <div class="slider-container mt-4">
                                <div class="latest-release-slider owl-carousel owl-theme" id="webseriesslider">
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
                                                        <div class="content-holder pt-5">
                                                            {{-- <img src="{{url('/')}}/asset/images/tere-sang.png" class="title mt-5" alt=""> --}}
                                                            <div class="content">
                                                                <div class="button-holder row">
                                                                    <div class="col-9">
                                                                        <a
                                                                            href="{{ route('webseries.view', ['id' => App\Helpers\VideoHelper::encryptID($item->id)]) }}">
                                                                            <button class="btn-watchnow" type="button">
                                                                                <i
                                                                                    class="fa-solid fa-play"></i>&nbsp;&nbsp;
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
                                                                    <li>
                                                                        @if ($item->video_duration)
                                                                            {{ MillisecondsToTime($item->video_duration) }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li> {{ $item->language_name }}</li>
                                                                </ul>
                                                                <p class="mt-3 mb-4 animate__animated animate__fadeInUp">
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
        @if (!empty($video) && count($video) > 0)
            <section id="section-home-newdes" class="podcast ">
                <div class="home-newdes-section">
                    <div class="home-newdes-section-container">
                        <div class="container-fluid">
                            <div class="d-flex justify-content-between align-items-center pe-3">
                                <span class="l-head">Search Result for Movies</span>
                            </div>
                            <div class="slider-container mt-4">
                                <div class="latest-release-slider owl-carousel owl-theme" id="moviesliderhome">
                                    @foreach ($video as $podcast)
                                        <div class="item">
                                            <div class="cards-container">
                                                <div class="alpha-card skeleton">
                                                    <div class="image-wrapper">
                                                        <a
                                                            href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($podcast->id)]) }}"><img
                                                                src="https://alphastudioz.in/admin_panel/public/images/video/{{ $podcast->landscape }}"
                                                                alt=""></a>
                                                    </div>
                                                    <div class="alpha-card-large {{ $podcast->trailer_url ? 'movie-card' : '' }}"
                                                        id="movie-card">
                                                        <img src="https://alphastudioz.in/admin_panel/public/images/video/{{ $podcast->landscape }}"
                                                            class="thumbnail" id="thumbnail" alt="">
                                                        <video class="video-holder" loop muted id="video-holder">
                                                            <source src="{{ $podcast->trailer_url }}" type="video/mp4">
                                                        </video>
                                                        <div class="content-holder pt-5">
                                                            {{-- <img src="{{url('/')}}/asset/images/tere-sang.png" class="title mt-5" alt=""> --}}
                                                            <div class="content">
                                                                <div class="button-holder row">
                                                                    <div class="col-9">
                                                                        <a
                                                                            href="{{ route('view.details', ['id' => App\Helpers\VideoHelper::encryptID($podcast->id)]) }}">
                                                                            <button class="btn-watchnow" type="button">
                                                                                <i
                                                                                    class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                Watch Now</button></a>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        @include('watchlist-button', [
                                                                            'id' => $podcast->id,
                                                                            'type' => 'video',
                                                                            'watchlist' => $watchlist,
                                                                        ])
                                                                    </div>
                                                                </div>
                                                                <ul class="animate__animated animate__fadeInUp mt-3">
                                                                    <li>{{ \Carbon\Carbon::parse($podcast->release_date)->format('Y') }}
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li>
                                                                        @if ($podcast->video_duration)
                                                                            {{ MillisecondsToTime($podcast->video_duration) }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </li>
                                                                    <li>&#x2022;</li>
                                                                    <li> {{ $podcast->language_name }}</li>
                                                                </ul>
                                                                <p class="mt-3 mb-4 animate__animated animate__fadeInUp">
                                                                    {{ Str::words(strip_tags($podcast->description), 15, '...') }}
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
    @endif
    <script>
        function ClearInput() {
            let url = new URL(window.location.href);
            let params = new URLSearchParams(url.search);
            params.delete('keyword');
            url.search = params.toString();
            window.history.replaceState({}, document.title, url);
            $("#searchInputNew").val('');
            $('.footer-section').addClass('footer-pushed-down');
            window.location.reload();
        }
        $(document).ready(function() {
            let url = new URL(window.location.href);
            let params = new URLSearchParams(url.search);
            if (params.has('keyword') && params.get('keyword').trim() !== '') {
                $('.footer-section').removeClass('footer-pushed-down');
            } else {
                $('.footer-section').addClass('footer-pushed-down');
            }
            $('#searchInputNew').keyup(function() {
                var keyword = $(this).val();
                if (keyword.trim() != '') {
                    $('#searchimageDiv').hide();
                    $('#searchResultsNew').show();
                    $('.footer-section').removeClass('footer-pushed-down');
                    // Make an Ajax request to your Laravel route to fetch search results
                    $.ajax({
                        url: `{{ route('search') }}`, // Replace with your Laravel route for search
                        method: 'GET',
                        data: {
                            keyword: keyword
                        },
                        success: function(response) {
                            // Update the UI with search results
                            displayResultsNew(response);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    $('#searchimageDiv').show();
                    $('#searchResultsNew').hide();
                    $('.footer-section').addClass('footer-pushed-down');
                }
            });

            function displayResultsNew(results) {
                var html = '';
                results.forEach(function(result) {
                    var description = result.description;
                    if (description.length > 50) {
                        description = description.substring(0, 50) + '...';
                    }
                    html += '<div class="search-result-new">';
                    html += '<a href="' + getURLNew(result) + '">';
                    html += '<div class="row">';
                    // html += '<div class="col-3">';
                    // html += '<img src="https://alphastudioz.in/admin_panel/public/images/video/'+ result.thumbnail + '" height="100px" width="auto">';
                    // html += '</div>';
                    html += '<div class="col-9">';
                    html += '<h3>' + result.name + '</h3>';
                    html += '<p>' + description + '</p>';
                    html += '</div>';
                    html += '</div>';
                    html += '</a>';
                    html += '</div>';
                });
                if (results.length === 0) {
                    html = '<p>No Such Result found</p>';
                }
                $('#searchResultsNew').html(html);
            }

            function getURLNew(result) {
                var id = result.id;
                var type = result.type_id;
                var url = '';
                const viewDetailsRoute = "{{ route('view.details', ['id' => 'REPLACE_ID', 'watch' => true]) }}";
                switch (type) {
                    case 6:
                        url = viewDetailsRoute.replace('REPLACE_ID', id);
                        break;
                    case 8:
                        url = viewDetailsRoute.replace('REPLACE_ID', id);
                        break;
                    case 5:
                        url = viewDetailsRoute.replace('REPLACE_ID', id);
                        break;
                    default:
                        break;
                }
                return url;
            }
            // $(document).on('click', function(event) {
            //     if (!$(event.target).closest('.search-bar, #searchResultsNew').length) {
            //         $('#searchResultsNew').hide();
            //         $('.footer-section').removeClass('footer-pushed-down');
            //     }
            // });
            // if ($("#searchInputNew").val().trim() === '') {
            //     $('#searchResultsNew').hide();
            //     $('.footer-section').removeClass('footer-pushed-down');
            // }
        });
    </script>
@endsection
