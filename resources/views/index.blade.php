@extends('layouts.main')
@section('title')
    {{ config('app.project_name') }} | Home
@endsection
@section('styles')
<style>
    .partner-img {
        max-height: 220px;
        /* adjust as needed */
        object-fit: contain;

    }

    /* Progress Bar Styles */
    .progress-bar-container {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 2;
    }

    .progress-bar {
        height: 100%;
        background-color: #FF4B2B !important;
        width: 0%;
        transition: width 0.3s ease !important;
    }

    .image-wrapper {
        position: relative;
        overflow: hidden;
    }

    .image-wrapper:hover .progress-bar-container {
        opacity: 0.8;
    }

    .watch-progress-info small {
        font-size: 11px;
        opacity: 0.7;
    }

    /* Optional: Add tooltip for progress */
    .alpha-card-large .watch-progress-info {
        margin-bottom: 20px;
    }

    /* Continue Watching Clear Button - Matching existing hover effects */
    .clear-continue-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        background: rgba(0, 0, 0, 0.7);
        border: none;
        border-radius: 50%;
        color: #fff;
        cursor: pointer;
        z-index: 15;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        opacity: 0;
        transform: scale(0.8);
        backdrop-filter: blur(4px);
    }

    .clear-continue-btn i {
        font-size: 16px;
    }

    .clear-continue-btn:hover {
        background: #ff4b2b;
        transform: scale(1.1);
        color: #fff;
    }

    /* Show clear button on card hover - matches your existing hover effect */
    .cards-container:hover .clear-continue-btn,
    .alpha-card:hover .clear-continue-btn {
        opacity: 1;
        transform: scale(1);
    }

    /* Clear All button styling */
    .clear-all-home-btn {
        transition: all 0.3s ease;
        padding: 5px 12px;
        border-radius: 20px;
    }

    .clear-all-home-btn:hover {
        background: rgba(255, 75, 43, 0.15);
        transform: scale(1.05);
    }

    /* ============================================================
       Mobile Spacing & Alignment — Comprehensive Fix
       ============================================================ */

    /* ---- TABLET & SMALL DESKTOP (≤ 768px) ---- */
    @media (max-width: 768px) {
        /* ── Global section rhythm ── */
        #section-home-newdes {
            margin: 8px 10px 18px 10px !important;
            padding: 0 !important;
        }
        #section-home-newdes.continue-watching {
            margin-top: 10px !important;
            margin-bottom: 18px !important;
        }
        #section-home-newdes.top10-section {
            margin-top: 8px !important;
            margin-bottom: 20px !important;
        }

        /* ── Remove Bootstrap container-fluid padding ── */
        #section-home-newdes .container-fluid {
            padding-left: 2px !important;
            padding-right: 2px !important;
        }

        /* ── Section Headings ── */
        #section-home-newdes .homepage-heading {
            margin-bottom: 8px !important;
            padding: 0 4px !important;
        }
        #section-home-newdes .homepage-heading .l-head {
            font-size: 16px !important;
            font-weight: 600 !important;
            color: #fff !important;
            letter-spacing: -0.2px;
            text-align: left !important;
            line-height: 1.3 !important;
        }
        #section-home-newdes .homepage-heading .l-sub-head {
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #ff3a1f !important;
            text-align: right !important;
            display: inline-flex !important;
            align-items: center;
            white-space: nowrap;
        }

        /* ── Card images ── */
        #section-home-newdes .alpha-card .image-wrapper img {
            border-radius: 6px !important;
            width: 100% !important;
        }

        /* ── Continue watching progress bar ── */
        .progress-bar-container {
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
            overflow: hidden;
        }

        /* ── Hero banner mobile alignment & layout (max-width: 768px) ── */
        #new-banner-section {
            margin-bottom: 8px !important;
            position: relative !important;
            overflow: hidden !important;
        }

        /* Hide desktop sync2 thumbnail bar on mobile */
        #new-banner-section .sync2-outer {
            display: none !important;
        }

        /* Hero images on mobile: maintain focal point at top/center where subjects/faces are */
        #new-banner-section .video-banner-mobile {
            display: block !important;
            width: 100% !important;
            height: 60vh !important;
            min-height: 400px !important;
            max-height: 520px !important;
            object-fit: cover !important;
            object-position: center top !important;
        }

        #new-banner-section .video-banner {
            display: none !important;
        }

        /* Fallback if mobile banner image not available */
        #new-banner-section .item:not(:has(.video-banner-mobile)) .video-banner {
            display: block !important;
            width: 100% !important;
            height: 60vh !important;
            min-height: 400px !important;
            max-height: 520px !important;
            object-fit: cover !important;
            object-position: center top !important;
        }

        #new-banner-section .banner-section-holder .content-holder,
        #new-banner-section .content-holder {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 60vh !important;
            min-height: 400px !important;
            max-height: 520px !important;
            z-index: 2 !important;
        }

        /* High-contrast smooth gradient protecting text at the bottom-left while keeping subjects clear */
        #new-banner-section .content-holder .overlay,
        #new-banner-section .banner-section-coontainer .banner-section-holder #new-banner .item .content-holder .overlay {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-end !important;
            align-items: flex-start !important;
            text-align: left !important;
            background: linear-gradient(
                to top,
                rgba(16, 16, 18, 0.98) 0%,
                rgba(16, 16, 18, 0.90) 24%,
                rgba(16, 16, 18, 0.55) 45%,
                rgba(16, 16, 18, 0.12) 65%,
                rgba(0, 0, 0, 0) 85%
            ),
            linear-gradient(
                to right,
                rgba(16, 16, 18, 0.85) 0%,
                rgba(16, 16, 18, 0.45) 50%,
                rgba(0, 0, 0, 0) 80%
            ) !important;
            padding: 0 16px 20px 16px !important;
            box-sizing: border-box !important;
            z-index: 3 !important;
        }

        /* Dock banner details strictly at the left side of the bottom */
        #new-banner-section .banner-section-coontainer .banner-section-holder #new-banner .item .content-holder .overlay .banner-details,
        #new-banner-section .banner-details,
        .banner-details {
            position: relative !important;
            bottom: auto !important;
            top: auto !important;
            left: 0 !important;
            right: auto !important;
            width: 100% !important;
            max-width: 85% !important;
            margin: 0 !important;
            margin-bottom: 0 !important;
            padding: 0 !important;
            text-align: left !important;
            z-index: 4 !important;
        }

        #new-banner-section .banner-details .row {
            margin: 0 !important;
            width: 100% !important;
            text-align: left !important;
        }

        #new-banner-section .banner-details .row > div,
        #new-banner-section .banner-details .col-md-6 {
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
            text-align: left !important;
        }

        #new-banner-section .banner-details h4 {
            font-size: clamp(15px, 4.2vw, 19px) !important;
            font-weight: 700 !important;
            line-height: 1.25 !important;
            margin-bottom: 4px !important;
            color: #ffffff !important;
            text-align: left !important;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.9) !important;
        }

        #new-banner-section .banner-details ul {
            padding-left: 0 !important;
            margin-left: 0 !important;
            margin-bottom: 4px !important;
            list-style: none !important;
            text-align: left !important;
        }

        #new-banner-section .banner-details ul li {
            font-size: 11.5px !important;
            color: #dfdfdf !important;
            display: inline-block !important;
            line-height: 1.3 !important;
            text-align: left !important;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.9) !important;
        }

        #new-banner-section .banner-details p {
            font-size: 11px !important;
            margin-top: 3px !important;
            margin-bottom: 10px !important;
            color: #ff3a1f !important;
            font-weight: 600 !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
            text-align: left !important;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.9) !important;
        }

        #new-banner-section .banner-details .button-holder {
            margin-top: 4px !important;
            text-align: left !important;
            display: flex !important;
            justify-content: flex-start !important;
        }

        #new-banner-section .banner-details .button-holder .d-flex {
            justify-content: flex-start !important;
            gap: 8px !important;
            margin-left: 0 !important;
        }

        #new-banner-section .banner-details .btn-watch-now {
            padding: 8px 18px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
        }

        #new-banner-section .banner-details .btn-watchlist {
            padding: 8px 14px !important;
            border-radius: 6px !important;
        }

        /* ── Footer spacing on mobile ── */
        footer {
            margin-top: 40px !important;
        }
    }

    /* ---- MOBILE PHONES (≤ 576px) ---- */
    @media (max-width: 576px) {
        /* ── Tighter section spacing on phones ── */
        #section-home-newdes {
            margin: 4px 8px 14px 8px !important;
            padding: 0 !important;
        }
        #section-home-newdes.continue-watching {
            margin-top: 6px !important;
            margin-bottom: 14px !important;
        }
        #section-home-newdes.top10-section {
            margin-top: 4px !important;
            margin-bottom: 16px !important;
        }

        /* ── Section Headings compact ── */
        #section-home-newdes .homepage-heading {
            margin-bottom: 6px !important;
            padding: 0 2px !important;
        }
        #section-home-newdes .homepage-heading .l-head {
            font-size: 15px !important;
        }
        #section-home-newdes .homepage-heading .l-sub-head {
            font-size: 12px !important;
        }

        /* ── Card height fix — remove fixed heights that distort ── */
        #section-home-newdes .alpha-card {
            height: auto !important;
            min-height: auto !important;
        }
        #section-home-newdes .alpha-card .image-wrapper {
            height: auto !important;
        }
        #section-home-newdes .alpha-card .image-wrapper img {
            border-radius: 5px !important;
            height: auto !important;
            aspect-ratio: 16/9;
            object-fit: cover;
        }

        /* ── Remove default margin/padding from container-fluid ── */
        #section-home-newdes .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* ── Banner section compacter on phones ── */
        #new-banner-section {
            margin-bottom: 4px !important;
        }
        #new-banner-section .content-holder .overlay {
            padding: 0 12px 16px 12px !important;
        }
        #new-banner-section .banner-details h4 {
            font-size: 15px !important;
            margin-bottom: 3px !important;
        }
        #new-banner-section .banner-details ul li {
            font-size: 11px !important;
        }
        #new-banner-section .banner-details p {
            font-size: 10px !important;
            margin-bottom: 8px !important;
        }

        /* ── Footer compact on phones ── */
        footer {
            margin-top: 28px !important;
        }
        footer .footer-section .footer-section-container .paragraph p {
            font-size: 10px !important;
            padding: 0 10px !important;
        }
    }

    /* ---- SMALL PHONES (≤ 390px) ---- */
    @media (max-width: 390px) {
        #section-home-newdes {
            margin: 2px 6px 12px 6px !important;
        }
        #section-home-newdes .homepage-heading {
            margin-bottom: 5px !important;
        }
        #section-home-newdes .homepage-heading .l-head {
            font-size: 14px !important;
        }
        #section-home-newdes .homepage-heading .l-sub-head {
            font-size: 11px !important;
        }

        /* ── Banner even tighter ── */
        #new-banner-section .content-holder .overlay {
            padding: 0 10px 14px 10px !important;
        }
        #new-banner-section .banner-details h4 {
            font-size: 14px !important;
        }
        #new-banner-section .banner-details .btn-watch-now {
            padding: 7px 16px !important;
            font-size: 12px !important;
        }
        #new-banner-section .banner-details .btn-watchlist {
            padding: 7px 12px !important;
        }
    }

    /* ---- EXTRA SMALL PHONES (≤ 320px) ---- */
    @media (max-width: 320px) {
        #section-home-newdes {
            margin: 2px 4px 10px 4px !important;
        }
        #section-home-newdes .homepage-heading .l-head {
            font-size: 13px !important;
        }
        #section-home-newdes .homepage-heading .l-sub-head {
            font-size: 10px !important;
        }
    }
</style>
@endsection
@section('main-section')
    <script src="https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.min.js"></script>
    {{-- Banner  --}}
    <section id="new-banner-section">
        <div class="banner-section-coontainer">
            <div class="banner-section-holder">
                <div class="banner-slider owl-carousel owl-theme" data-skip-carousel="true" id="new-banner">
                    @foreach ($banner as $key => $b)
                        @php
                            $user = app('logged-in-user');
                            $watchHref = !$user
                                ? 'javascript:openSubscriptionModal()'
                                : ($user->subscription !== 'Yes'
                                    ? 'javascript:openSubscriptionModal()'
                                    : $b->url);
                        @endphp
                        <div class="item" data-index="{{ $key }}">
                            <img src="{{ $b->landscape_url }}" class="video-banner lazyload" alt="thumbnail-banner-image"
                                loading="lazy">
                            <img src="{{ $b->thumbnail_url ?: $b->landscape_url }}" class="video-banner-mobile d-none lazyload"
                                alt="thumbnail-image" loading="lazy">
                            <div class="content-holder">
                                <div class="overlay">
                                    <div class="banner-details">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h4 class="text-white mb-3 animate__animated animate__fadeInUp">
                                                    {{ $b->title }}
                                                </h4>
                                                <ul class="animate__animated animate__fadeInUp">
                                                    <li>{{ $b->release_description }}</li>
                                                </ul>
                                                <p class="mt-3 animate__animated animate__fadeInUp">{{ $b->category }}</p>
                                                <div class="button-holder animate__animated animate__fadeInUp">
                                                    <div class="d-flex align-items-center">
                                                        <a href="{{ $watchHref }}">
                                                            <button class="btn-watch-now" type="button">
                                                                <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                Watch Now
                                                            </button>
                                                        </a>

                                                        @php
                                                            $resolved = $b->watchlist_details;
                                                        @endphp
                                                        @include('watchlist-button', [
                                                            'id' => $resolved['id'],
                                                            'type_id' => $resolved['type_id'],
                                                            'category_id' => $resolved['category_id'],
                                                            'type' => $resolved['type'],
                                                            'watchlist' => $watchlist,
                                                        ])
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

                <div class="sync2-outer">
                    <button class="sync2-custom-prev">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" width="10" height="18" viewBox="0 0 10 18"
                            fill="none">
                            <path d="M9 1L1 9L9 17" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg> --}}
                    </button>

                    <div id="sync2" class="owl-carousel owl-theme" data-skip-carousel="true">
                        @foreach ($banner as $key => $b)
                            <div class="item" data-index="{{ $key }}">
                                <div class="thumbnail-holder">
                                    <img src="{{ $b->landscape_url }}" alt="" class="landscape-banner lazyload"
                                        loading="lazy">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button class="sync2-custom-next">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" width="10" height="18" viewBox="0 0 10 18"
                            fill="none">
                            <path d="M1 1L9 9L1 17" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg> --}}
                    </button>
                </div>
            </div>
        </div>
    </section>
    
    {{-- Continue Watching --}}
    @if ($continueWatching && count($continueWatching) > 0)
        <section id="section-home-newdes" class="continue-watching">
            <div class="home-newdes-section">
                <div class="home-newdes-section-container">
                    <div class="container-fluid">
                        <div class="homepage-heading d-flex justify-content-between align-items-center pe-3">
                            <span class="l-head">Continue Watching</span>
                            <div class="d-flex gap-3 align-items-center">
                                <a href="{{ route('continue-watching.list') }}"
                                    class="l-sub-head text-white text-decoration-none">
                                    View All >
                                </a>
                            </div>
                        </div>
                        <div class="slider-container">
                            <div class="latest-release-slider owl-carousel owl-theme" id="continueWatchingSlider">
                                @foreach ($continueWatching as $item)
                                    <div class="item">
                                        <div class="cards-container">
                                            <div class="alpha-card skeleton">
                                                <div class="image-wrapper">
                                                    @php
                                                        $website_url = rtrim(config('app.website_url', 'https://alphastudioz.in/'), '/') . '/';
                                                        $redirectUrl = $item['redirect_url'] ?? '#';
                                                        $itemType = $item['type'] ?? 'video';
                                                        $folder = ($itemType === 'episode') ? 'series/' : 'video/';

                                                        // 1. Direct image_url if provided
                                                        $imageUrl = $item['image_url'] ?? null;

                                                        // 2. Direct landscape_url or thumbnail_url
                                                        if (empty($imageUrl)) {
                                                            $imageUrl = !empty($item['landscape_url']) ? $item['landscape_url'] : (!empty($item['thumbnail_url']) ? $item['thumbnail_url'] : null);
                                                        }

                                                        // 3. Fallback to landscape or poster
                                                        if (empty($imageUrl)) {
                                                            $rawImage = !empty($item['landscape']) ? $item['landscape'] : (!empty($item['poster']) ? $item['poster'] : null);
                                                            if (!empty($rawImage)) {
                                                                if (str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://') || str_starts_with($rawImage, '//')) {
                                                                    $imageUrl = $rawImage;
                                                                } else {
                                                                    $imageUrl = $website_url . 'admin_panel/public/images/' . $folder . ltrim($rawImage, '/');
                                                                }
                                                            }
                                                        }

                                                        // 4. Default placeholder
                                                        if (empty($imageUrl)) {
                                                            $imageUrl = asset('asset/images/thumbnail-1.png');
                                                        }
                                                    @endphp
                                                    <a href="{{ $redirectUrl }}">
                                                        <img class="lazyload" src="{{ $imageUrl }}"
                                                            onerror="this.onerror=null;this.src='{{ asset('asset/images/thumbnail-1.png') }}';"
                                                            alt="{{ $item['title'] ?? 'Video' }}" loading="lazy">
                                                        <div class="progress-bar-container">
                                                            <div class="progress-bar"
                                                                style="width: {{ $item['progress_percentage'] ?? 0 }}%"></div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="alpha-card-large movie-card">
                                                    <img src="{{ $imageUrl }}" class="thumbnail lazyload"
                                                        onerror="this.onerror=null;this.src='{{ asset('asset/images/thumbnail-1.png') }}';"
                                                        alt="{{ $item['title'] ?? 'thumbnail' }}" loading="lazy">
                                                    <!-- Clear Item Button -->
                                                    <button class="clear-continue-btn"
                                                        onclick="event.preventDefault(); event.stopPropagation(); clearContinueItem({{ $item['id'] }}, '{{ $item['type'] }}', this)"
                                                        title="Remove from Continue Watching">
                                                        <i class="fa-regular fa-circle-xmark"></i>
                                                    </button>
                                                    <div class="content-holder pt-5">
                                                        <div class="content">
                                                            <div class="button-holder row">
                                                                <div class="col-9">
                                                                    <a href="{{ $redirectUrl }}">
                                                                        <button class="btn-watchnow" type="button">
                                                                            <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                            Resume
                                                                        </button>
                                                                    </a>
                                                                </div>
                                                                <div class="col-3 ps-0">
                                                                    @include('watchlist-button', [
                                                                        'id' => $item['id'],
                                                                        'type_id' => $item['type_id'],
                                                                        'category_id' => $item['category_id'],
                                                                        'type' => $item['type'],
                                                                        'watchlist' => $watchlist,
                                                                    ])
                                                                </div>
                                                            </div>

                                                            @if (!empty($item['series_name']))
                                                                <p class="mt-2 mb-1 text-white-50 small">
                                                                    {{ $item['series_name'] }}</p>
                                                                <h6 class="text-white mb-2">{{ $item['title'] ?? '' }}</h6>
                                                            @else
                                                                <h6 class="text-white mt-3 mb-3">{{ $item['title'] ?? '' }}</h6>
                                                            @endif

                                                            @if (!empty($item['season']) && !empty($item['episode_num']))
                                                                <ul class="animate__animated animate__fadeInUp mt-2">
                                                                    <li>Season {{ $item['season'] }}</li>
                                                                    <li>&#x2022;</li>
                                                                    <li>Episode {{ $item['episode_num'] }}</li>
                                                                </ul>
                                                            @endif

                                                            <div class="watch-progress-info mt-2">
                                                                <small class="text-white-50">
                                                                    {{ gmdate('H:i:s', (int) ($item['watch_time'] ?? 0)) }} /
                                                                    {{ gmdate('H:i:s', (int) ($item['duration'] ?? 0)) }}
                                                                    ({{ $item['progress_percentage'] ?? 0 }}%)
                                                                </small>
                                                            </div>
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
    
    {{-- Top 10 Section --}}
    @php
        $top10Items = $top10 ?? \App\Models\Video::active()->orderByDesc('view')->take(10)->get();
    @endphp
    @if (count($top10Items) > 0)
        @include('top10-section', [
            'class' => 'top10-section',
            'title' => 'Top 10 Contents',
            'id' => 'top10slider',
            'data' => $top10Items,
            'view_route' => 'view.details.slug',
        ])
    @endif
    {{-- upcoming --}}
    @if (count($upcomming) > 0)
        @include('section-wise-common', [
            'class' => 'upcoming',
            'title' => 'Upcomings',
            'viewall_route' => route('trailers.list'),
            'id' => 'upcomings',
            'data' => $upcomming,
            'view_route' => 'view.details',
        ])
    @endif
    {{-- trailer --}}
    @if (count($trailer) > 0)
        @include('content-details', [
            'class' => 'trailer',
            'title' => 'Trailers',
            'viewall_route' => route('trailers.list'),
            'id' => 'trailers',
            'data' => $trailer,
            'content' => 'trailers',
            'view_route' => 'view.details.slug',
        ])
    @endif
    {{-- podcast  --}}
    @if (
        (isset($poadcastData) && count($poadcastData) > 0) ||
            (isset($marathipoadcastData) && count($marathipoadcastData) > 0))
        @include('podcast-detail', [
            'class' => 'podcast',
            'title' => 'Podcasts',
            'viewall_route' => route('podcasts.list'),
            'id' => 'podcastslider',
            'data' => $poadcastData ?? [],
            'marathiData' => $marathipoadcastData ?? [],
            'view_route' => 'podcast.details',
        ])
    @endif
    {{-- Comedy Show --}}
    @if (count($comdeyData) > 0)
        @include('comedyshow-section-wise-common', [
            'class' => 'comedy',
            'title' => 'Comedy Shows',
            'viewall_route' => route('comedy.show.list'),
            'id' => 'comedyslider',
            'data' => $comdeyData,
            'content' => 'comedy',
            'view_route' => 'comedy.show.episode.view.by.slug',
        ])
    @endif
    {{-- short films  --}}
    @if (count($shortfilms) > 0)
        @include('content-details', [
            'class' => 'shortfilms',
            'title' => 'Short Films',
            'viewall_route' => route('shortfilms.list'),
            'id' => 'shortfilmslider',
            'data' => $shortfilms,
            'content' => 'shortfilms',
            'view_route' => 'view.details.slug',
        ])
    @endif
    {{-- music --}}
    @if (count($music) > 0)
        @include('content-details', [
            'class' => 'music',
            'title' => 'Music',
            'viewall_route' => route('musics.list'),
            'id' => 'musicsslider',
            'data' => $music,
            'content' => 'musics',
            'view_route' => 'view.details.slug',
        ])
    @endif
    {{-- movies --}}
    @if (count($movies) > 0)
        @include('content-details', [
            'class' => 'movies',
            'title' => 'Movies',
            'viewall_route' => route('movies.list'),
            'id' => 'moviesliderhome',
            'data' => $movies,
            'content' => 'movies',
            'view_route' => 'view.details.slug',
        ])
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
                                @foreach ($series as $item)
                                    @php
                                        $user = app('logged-in-user');
                                        $viewAllSeriesHref = !$user
                                            ? 'javascript:openSubscriptionModal()'
                                            : ($user->subscription !== 'Yes'
                                                ? 'javascript:openSubscriptionModal()'
                                                : route('webseries.episode.view.by.slug', [
                                                    'slug' => $item->series->slug,
                                                    'season' => $item->series->season_no,
                                                    'episode' => $item->episode_no,
                                                ]));
                                    @endphp
                                    <div class="item">
                                        <div class="cards-container">
                                            <div class="alpha-card skeleton">
                                                <div class="image-wrapper">
                                                    <a href="{{ $viewAllSeriesHref }}">
                                                        <img class="lazyload"
                                                            src="{{ config('app.website_url') }}admin_panel/public/images/series/{{ $item->series->landscape_image }}"
                                                            alt="{{ $item->series->landscape_image }}"></a>
                                                </div>
                                                <div class="alpha-card-large {{ $item->series->trailer_url ? 'movie-card' : '' }}"
                                                    id="movie-card">
                                                    <img src="{{ config('app.website_url') }}admin_panel/public/images/series/{{ $item->series->landscape_image }}"
                                                        class="thumbnail lazyload" id="thumbnail"
                                                        alt="{{ $item->series->landscape_image }}">

                                                    <div class="content-holder">
                                                        <div class="content">
                                                            <div class="button-holder row">
                                                                <div class="col-9">
                                                                    <a href="{{ $viewAllSeriesHref }}">
                                                                        <button class="btn-watchnow" type="button">
                                                                            <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                            Watch Now
                                                                        </button>
                                                                    </a>
                                                                </div>
                                                                <div class="col-3 ps-0">
                                                                    @include('watchlist-button', [
                                                                        'id' => $item->id,
                                                                        'type_id' => 10,
                                                                        'category_id' => 26,
                                                                        'type' => 'series',
                                                                        'watchlist' => $watchlist,
                                                                    ])
                                                                </div>
                                                            </div>
                                                            <ul class="animate__animated animate__fadeInUp mt-3">
                                                                <li>{{ \Carbon\Carbon::parse($item->series->release_date)->format('Y') }}
                                                                </li>
                                                                <li>&#x2022;</li>
                                                                <li>{{ $item->series->season }}</li>
                                                                <li>&#x2022;</li>
                                                                <li>Total Episodes - {{ $item->series->total_episode }}
                                                                </li>
                                                            </ul>
                                                            <p class="mt-1 mb2 animate__animated animate__fadeInUp">
                                                                {{ Str::words(strip_tags($item->series->description), 15, '...') }}
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
    {{-- @if (count($khlup) > 0)
        <section id="section-home-newdes" class="khulps">
            <div class="home-newdes-section">
                <div class="home-newdes-section-container">
                    <div class="container-fluid">
                        <div class="homepage-heading d-flex justify-content-between align-items-center pe-3">
                            <span class="l-head">Khlups</span>
                            <a href="{{ route('khlups.view', ['tokengen' => 'default_token']) }}" class="l-sub-head text-white text-decoration-none">
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
                                                    <img class="lazyload" src="{{ $item->thumbnail_url }}" alt="khlup-thumbnail">
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
        </section>
    @endif --}}
    @include('auth.auto-payment-modal')
@endsection
<script>
    // Declare routes for JavaScript
    const clearContinueUrl = "{{ route('continue-watching.clear', '') }}/";
    const clearAllContinueUrl = "{{ route('continue-watching.clear-all') }}";
    const csrfToken = "{{ csrf_token() }}";

    // Show toast message
    function showToast(message, isError = false) {
        let toast = document.createElement('div');
        toast.className = 'toast-message';
        toast.style.backgroundColor = isError ? '#dc3545' : '#28a745';
        toast.innerHTML = '<i class="fa-solid fa-' + (isError ? 'circle-exclamation' : 'check-circle') + '"></i> ' +
            message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

async function clearContinueItem(id, type, btn) {

    if (!confirm('Are you sure you want to remove this item from Continue Watching?')) {
        return;
    }

    // Prevent multiple clicks
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    btn.disabled = true;

    try {

        const response = await fetch(clearContinueUrl + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {

            showToast(data.message);

            // Find the Owl Carousel item
            const item = btn.closest('.item');

            if (item) {

                const slider = document.querySelector('#continueWatchingSlider');

                if (slider && $(slider).hasClass('owl-loaded')) {

                    // Find the carousel item index
                    const owlItem = item.closest('.owl-item');

                    if (owlItem) {

                        const index = $(owlItem).index();

                        // Remove item from Owl Carousel
                        $(slider).trigger('remove.owl.carousel', [index])
                            .trigger('refresh.owl.carousel');

                    }

                } else {

                    // Fallback if Owl Carousel is not initialized
                    item.remove();

                }

            }

            // Check if Continue Watching is now empty
            setTimeout(function () {

                const slider = document.querySelector('#continueWatchingSlider');

                if (slider) {

                    const remainingItems = slider.querySelectorAll('.owl-item:not(.cloned)');

                    if (remainingItems.length === 0) {

                        // Hide Continue Watching section
                        const section = document.querySelector('.continue-watching');

                        if (section) {
                            section.remove();
                        }

                    }

                }

            }, 500);

        } else {

            showToast(
                data.message || 'Failed to remove item',
                true
            );

            // Restore button
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }

    } catch (error) {

        console.error('Clear Continue Watching Error:', error);

        showToast(
            'Something went wrong. Please try again.',
            true
        );

        // Restore button
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    }
}


    // Clear all items
    async function confirmClearAll() {
        if (!confirm(
                '⚠️ Warning: This will remove ALL items from Continue Watching. This action cannot be undone.\n\nAre you sure you want to continue?'
            )) {
            return;
        }

        let clearBtn = document.querySelector('.clear-all-btn');
        let originalText = clearBtn.innerHTML;
        clearBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Clearing...';
        clearBtn.disabled = true;

        try {
            const response = await fetch(clearAllContinueUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            console.log('Clear all response:', data);

            if (data.success) {
                showToast(data.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast(data.message || 'Failed to clear items', true);
                clearBtn.innerHTML = originalText;
                clearBtn.disabled = false;
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            showToast('Clearing items. Refreshing...', false);
            setTimeout(() => {
                location.reload();
            }, 1500);
        }
    }
</script>
{{-- <script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.getRegistrations().then(registrations => {
    registrations.forEach(reg => reg.unregister());
  });
}
</script> --}}
