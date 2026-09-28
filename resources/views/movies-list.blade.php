@extends('layouts.main')
@section('title')
    {{ config('app.project_name') }} | Movies
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

    /* Mobile spacing optimizations for Movies page */
    @media (max-width: 768px) {
        #section-home-newdes {
            margin-top: 2.5rem !important;
            padding-top: 2.5rem !important;
            padding-bottom: 1rem !important;
            margin-left: 6px !important;
            margin-right: 6px !important;
        }

        #section-home-newdes .l-head {
            font-size: 35px !important;
            font-weight: 600;
            display: inline-block;
            text-align: left;
            margin-bottom: 2px;
        }

        #section-home-newdes .category {
            margin-top: 8px !important;
        }

        #section-home-newdes hr {
            margin-top: 10px !important;
            margin-bottom: 12px !important;
            opacity: 0.2;
        }

        #section-home-newdes .slider-container {
            margin-top: 4px !important;
        }
    }
</style>
@section('main-section')
    <section id="section-home-newdes" class="podcast mt-2 mt-md-5 pt-2 pt-md-4">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid">
                    <span class="l-head">Movies </span>
                    <div class="category mt-2 mt-md-3">
                        <div class="category-carousel">
                            <div id="categories" class="owl-carousel owl-theme" data-skip-carousel="true">
                                <div class="item">
                                    <a href="{{ route('movies.list') }}" class="text-decoration-none">
                                        <input type="radio" name="category"
                                            {{ empty($selectedCategory) ? 'checked' : '' }} class="d-none">
                                        <label>All</label>
                                    </a>
                                </div>

                                @foreach ($catlist as $item)
                                    <div class="item">
                                        <a href="{{ route('movies.list', ['category' => $item->cat_id]) }}" class="text-decoration-none">
                                            <input type="radio" name="category"
                                                {{ $selectedCategory == $item->cat_id ? 'checked' : '' }} class="d-none">
                                            <label>{{ $item->category_name }}</label>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <hr class="my-2 my-md-4">
                    <div class="slider-container mt-1 mt-md-3">
                        <div class="latest-release-slider movies-page-section">
                            <div class="row g-1">
                                @foreach ($video as $key => $videoItem)
                                    <div class="item movie-new-card col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 col-xxl-2 ps-md-2 ps-sm-1 pe-sm-1 pe-md-2 mb-2 pe-1 ps-1"
                                        data-category-id="{{ $videoItem->category_id }}" data-index="{{ $key }}">
                                        <div class="cards-container">
                                            <div class="alpha-card skeleton card-for-podcast">
                                                <div class="image-wrapper">
                                                    <a
                                                        href="{{ route('view.details.slug', ['content'=>'movies','slug' => $videoItem->slug]) }}">
                                                        <img src="{{ !empty($videoItem->landscape) ? config('app.website_url') . 'admin_panel/public/images/video/' . $videoItem->landscape : asset('asset/images/thumbnail-1.png') }}"
                                                            onerror="this.onerror=null;this.src='{{ asset('asset/images/thumbnail-1.png') }}';"
                                                            alt="{{ $videoItem->landscape ?? 'Movie' }}" loading="lazy">
                                                    </a>
                                                </div>
                                                <div class="alpha-card-large {{ $videoItem->trailer_url ? 'movie-card' : '' }}"
                                                    id="movie-card">
                                                    <img src="{{ !empty($videoItem->landscape) ? config('app.website_url') . 'admin_panel/public/images/video/' . $videoItem->landscape : asset('asset/images/thumbnail-1.png') }}"
                                                        onerror="this.onerror=null;this.src='{{ asset('asset/images/thumbnail-1.png') }}';"
                                                        class="thumbnail" id="thumbnail" alt="{{ $videoItem->landscape ?? 'Movie' }}"
                                                        loading="lazy">
                                                    <video class="video-holder" loop muted id="video-holder">
                                                        <source src="{{ $videoItem->trailer_url }}" type="video/mp4">
                                                    </video>
                                                    <div class="content-holder pt-5">
                                                        {{-- <img src="{{url('/')}}/asset/images/tere-sang.png" class="title mt-5" alt=""> --}}
                                                        <div class="content">
                                                            <div class="button-holder row">
                                                                <div class="col-9">
                                                                    <a
                                                                        href="{{ route('view.details.slug', ['content' => 'movies', 'slug' => $videoItem->slug]) }}">
                                                                        <button class="btn-watchnow" type="button">
                                                                            <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                            Watch Now</button></a>
                                                                </div>
                                                                <div class="col-3 ps-0">
                                                                    @include('watchlist-button', [
                                                                        'id' => $videoItem->id,
                                                                        'type_id' => $videoItem->type_id,
                                                                        'category_id' => $videoItem->category_id,
                                                                        'type' => 'video',
                                                                        'watchlist' => $watchlist,
                                                                    ])
                                                                </div>
                                                            </div>
                                                            <ul class="animate__animated animate__fadeInUp mt-3">
                                                                <li>{{ \Carbon\Carbon::parse($videoItem->release_date)->format('Y') }}
                                                                </li>
                                                                <li>&#x2022;</li>
                                                                <li>
                                                                    @if ($videoItem->video_duration)
                                                                        {{ MillisecondsToTime($videoItem->video_duration) }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </li>
                                                                <li>&#x2022;</li>
                                                                <li> {{ $videoItem->language_name }}</li>
                                                            </ul>
                                                            <p class="mt-3 mb-4 animate__animated animate__fadeInUp">
                                                                {{ Str::words(strip_tags($videoItem->description), 15, '...') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="pagination-links mt-4">
                                {{ $video->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="{{ URL::to('asset/js/gridView.js') }}"></script>
@endsection
