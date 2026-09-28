@extends('layouts.main')
@section('title')
    {{ config('app.project_name') }} | Webseries
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
                    <span class="l-head">Web Series</span>
                    <div class="category mt-md-3 mt-3">
                        <div class="category-carousel">
                            <div id="categories" class="owl-carousel owl-theme" data-skip-carousel="true">
                                <div class="item ">
                                    <input type="radio" name="category" value="All" id="cat0" class="d-none"
                                        checked>
                                    <label for="cat0">All</label>
                                </div>


                            </div>
                        </div>
                    </div>
                    <hr class="mb-3">
                    <div class="slider-container mt-4">
                        <div class="latest-release-slider movies-page-section">
                            <div class="row g-3">
                                @foreach ($series as $key => $videoItem)

                                @php
                                        $user = app('logged-in-user');
                                        $viewAllSeriesHref = !$user
                                            ? 'javascript:openSubscriptionModal()'
                                            : ($user->subscription !== 'Yes'
                                                ? 'javascript:openSubscriptionModal()'
                                                : route('webseries.episode.view.by.slug', [
                                                    'slug' => $videoItem->series->slug,
                                                    'season' => $videoItem->series->season_no,
                                                    'episode' => $videoItem->episode_no,
                                                ]));
                                    @endphp
                                    {{-- <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3"> --}}
                                    <div class="item movie-new-card col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 col-xxl-2 ps-md-2 ps-sm-1 pe-sm-1 pe-md-2 mb-2 pe-1 ps-1"
                                        data-category-id="{{ $videoItem->category_id }}" data-index="{{ $key }}">

                                        <div class="cards-container">
                                            <div class="alpha-card skeleton card-for-podcast">
                                                <div class="image-wrapper">
                                                    <a
                                                        href="{{ $viewAllSeriesHref }}">
                                                        <img src="{{ !empty($videoItem->series->landscape_image) ? config('app.website_url') . 'admin_panel/public/images/series/' . $videoItem->series->landscape_image : asset('asset/images/thumbnail-1.png') }}"
                                                            onerror="this.onerror=null;this.src='{{ asset('asset/images/thumbnail-1.png') }}';"
                                                            alt="{{ $videoItem->series->name ?? 'Series' }}" loading="lazy">
                                                    </a>
                                                </div>
                                                <div class="alpha-card-large {{ $videoItem->trailer_url ? 'movie-card' : '' }}"
                                                    id="movie-card">
                                                    <img src="{{ !empty($videoItem->series->landscape_image) ? config('app.website_url') . 'admin_panel/public/images/series/' . $videoItem->series->landscape_image : asset('asset/images/thumbnail-1.png') }}"
                                                        onerror="this.onerror=null;this.src='{{ asset('asset/images/thumbnail-1.png') }}';"
                                                        class="thumbnail" id="thumbnail"
                                                        alt="{{ $videoItem->series->name ?? 'Series' }}" loading="lazy">
                                                    <video class="video-holder" loop muted id="video-holder">
                                                        <source src="{{ $videoItem->series->trailer_url }}" type="video/mp4">
                                                    </video>
                                                    <div class="content-holder pt-5">

                                                        <div class="content">
                                                            <div class="button-holder row">
                                                                <div class="col-9">
                                                                    <a
                                                                        href="{{ $viewAllSeriesHref }}">
                                                                        <button class="btn-watchnow" type="button">
                                                                            <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                            Watch Now</button></a>

                                                                </div>
                                                                <div class="col-3 ps-0">
                                                                    @include('watchlist-button', [
                                                                        'id' => $videoItem->id,
                                                                        'type_id' => 10,
                                                                        'category_id' => 26,
                                                                        'type' => 'series',
                                                                        'watchlist' => $watchlist,
                                                                    ])
                                                                </div>

                                                            </div>
                                                            <ul class="animate__animated animate__fadeInUp mt-3">
                                                                <li>{{ \Carbon\Carbon::parse($videoItem->release_date)->format('Y') }}
                                                                </li>
                                                                <li>&#x2022;</li>
                                                                <li>{{ $videoItem->series->season }}</li>
                                                                <li>&#x2022;</li>
                                                                <li>Total Episodes - {{ $videoItem->series->total_episode }}</li>

                                                            </ul>
                                                            <p class="mt-3 mb-4 animate__animated animate__fadeInUp">
                                                                {{ Str::words(strip_tags($videoItem->series->description), 15, '...') }}
                                                            </p>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- </div> --}}
                                @endforeach
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <script src="{{ URL::to('asset/js/gridView.js') }}"></script>
@endsection
