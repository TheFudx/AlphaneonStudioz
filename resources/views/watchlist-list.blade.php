@extends('layouts.main')
@section('title')
    {{ config('app.project_name') }} | Watchlist
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
<style>
    .filter-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin: 20px 0;
        align-items: center;
    }

    .filter-btn {
        padding: 8px 20px;
        border-radius: 25px;
        background: transparent;
        border: 1px solid #fd5949;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .filter-btn.active,
    .filter-btn:hover {
        background: #fd5949;
        color: white;
        border-color: #fd5949;
    }

    .clear-all-btn {
        background: rgba(255, 75, 43, 0.2);
        border-color: #ff4b2b;
        color: #ff4b2b;
        margin-left: auto;
    }

    .clear-all-btn:hover {
        background: #ff4b2b;
        color: white;
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

    /* Clear button styling - matches existing hover effects */
    .clear-continue-list-btn {
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

    .clear-continue-list-btn i {
        font-size: 16px;
    }

    .clear-continue-list-btn:hover {
        background: #ff4b2b;
        transform: scale(1.1);
        color: #fff;
    }

    .cards-container:hover .clear-continue-list-btn {
        opacity: 1;
        transform: scale(1);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        margin: 40px 0;
    }

    .empty-state i {
        font-size: 25px;
        color: #ffffff;
        margin-bottom: 0px;
    }

    .empty-state h3 {
        font-size: 24px;
        margin-bottom: 15px;
    }

    .empty-state p {
        color: #999;
        margin-bottom: 25px;
    }

    .btn-explore {
        background: #fd5949;
        color: white;
        padding: 10px 25px;
        border-radius: 25px;
        text-decoration: none;
        display: inline-block;
    }

    .btn-explore span {
        position: relative;
        top: -3px;
    }

    .toast-message {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #333;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>
@section('main-section')
    <section id="section-home-newdes" class="podcast mt-5 pt-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid">
                    <div class="filter-buttons">
                        <span class="l-head">Watch Later</span>
                        @if (count($watchlist) > 0)
                            <button type="button" class="filter-btn clear-all-btn" id="clearWatchlistBtn">
                                <i class="fa-regular fa-trash-can"></i> Clear All
                            </button>
                        @endif
                    </div>
                    <hr class="mb-2">
                    <div class="slider-container mt-2">
                        <div class="latest-release-slider movies-page-section">
                            <div class="row g-1">
                                @if ($watchlist->isEmpty())
                                    <div class="col-12 text-center">
                                        <img src="{{ URL::to('asset/images/no-data-found.png') }}" alt="No Videos Found"
                                            class="img-fluid" style="max-width: 300px; margin-top: 10px;">
                                        <h3 class="text-white mt-3">No Videos Found</h3>
                                        <p class="text-white">
                                            Watch Later is empty! Start adding videos to keep track
                                            of what you want to watch next.
                                        </p>
                                    </div>
                                @else
                                    {{-- @dd($watchlist) --}}
                                    @foreach ($watchlist as $key => $w)
                                        @php
                                            $item = null;
                                            $type = null;
                                            // VIDEO
                                            if (!empty($w->video_id)) {
                                                $item = \App\Models\Video::active()
                                                    ->select('video.*', 'language.name as language_name')
                                                    ->join('language', 'language.id', '=', 'video.language_id')
                                                    ->where('video.id', $w->video_id)
                                                    // ->where('status', 1)
                                                    ->first();
                                                $type = 'video';
                                            }
                                            // EPISODE
                                            elseif (!empty($w->episode_id)) {
                                                $item = \App\Models\Episodes::select(
                                                    'episodes.*',
                                                    'language.name as language_name',
                                                )
                                                    ->join('language', 'language.id', '=', 'episodes.language_id')
                                                    ->where('episodes.id', $w->episode_id)
                                                    // ->where('status', 1)
                                                    ->first();
                                                $type = 'episode';
                                            }

                                        @endphp
                                        @if ($item)
                                            @php

                                                $isSeries = $type == 'episode';

                                                $detailRoute = $isSeries
                                                    ? route('watchlist.view.details', [
                                                        'id' => App\Helpers\VideoHelper::encryptID($item->id),
                                                        'type' => 'episode',
                                                    ])
                                                    : route('watchlist.view.details', [
                                                        'id' => App\Helpers\VideoHelper::encryptID($item->id),
                                                        'type' => 'video',
                                                    ]);
                                                $imagePath = $isSeries
                                                    ? config('app.website_url') . 'admin_panel/public/images/series/'
                                                    : config('app.website_url') . 'admin_panel/public/images/video/';
                                                $image = $item->landscape ?? ($item->thumbnail ?? '');

                                            @endphp
                                            <div class="item movie-new-card col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 col-xxl-2 ps-md-2 ps-sm-1 pe-sm-1 pe-md-2 mb-2 pe-1 ps-1"
                                                data-category-id="{{ $item->category_id ?? '' }}"
                                                data-index="{{ $key }}">
                                                <div class="cards-container">
                                                    <div class="alpha-card skeleton card-for-podcast">
                                                        <div class="image-wrapper">
                                                            <a href="{{ $detailRoute }}">
                                                                <img src="{{ !empty($image) ? $imagePath . $image : asset('asset/images/thumbnail-1.png') }}"
                                                                    onerror="this.onerror=null;this.src='{{ asset('asset/images/thumbnail-1.png') }}';"
                                                                    alt="{{ $item->name ?? ($item->title ?? 'Video') }}" loading="lazy">
                                                            </a>
                                                        </div>
                                                        <div class="alpha-card-large {{ !empty($item->trailer_url) ? 'movie-card' : '' }}"
                                                            id="movie-card">
                                                            <img src="{{ !empty($image) ? $imagePath . $image : asset('asset/images/thumbnail-1.png') }}" class="thumbnail"
                                                                onerror="this.onerror=null;this.src='{{ asset('asset/images/thumbnail-1.png') }}';"
                                                                id="thumbnail" alt="{{ $item->name ?? ($item->title ?? 'Video') }}" loading="lazy">
                                                            <video class="video-holder" loop muted id="video-holder">
                                                                <source src="{{ $item->trailer_url }}" type="video/mp4">
                                                            </video>
                                                            <div class="content-holder pt-5">
                                                                <div class="content">
                                                                    <div class="button-holder row">
                                                                        <div class="col-9">
                                                                            <a href="{{ $detailRoute }}">
                                                                                <button class="btn-watchnow" type="button">
                                                                                    <i
                                                                                        class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                                    Watch Now
                                                                                </button>
                                                                            </a>
                                                                        </div>

                                                                        <div class="col-3 ps-0">
                                                                            @include('watchlist-button', [
                                                                                'id' => $item->id,
                                                                                'type_id' => $item->type_id ?? 10,
                                                                                'category_id' =>
                                                                                    $item->category_id ?? '',
                                                                                'type' => $isSeries
                                                                                    ? 'episode'
                                                                                    : 'video',
                                                                                'watchlist' => $watchlist,
                                                                            ])
                                                                        </div>
                                                                    </div>
                                                                    <ul class="animate__animated animate__fadeInUp mt-3">
                                                                        @if (!empty($item->release_date))
                                                                            <li>
                                                                                {{ \Carbon\Carbon::parse($item->release_date)->format('Y') }}
                                                                            </li>
                                                                            <li>&#x2022;</li>
                                                                        @endif
                                                                        @if ($isSeries)
                                                                            <li>
                                                                                @if ($item->episode_duration)
                                                                                    {{ MillisecondsToTime($item->episode_duration) }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </li>
                                                                            <li>&#x2022;</li>
                                                                            <li>Episode - {{ $item->episode_no }}</li>
                                                                        @else
                                                                            <li>{{ MillisecondsToTime($item->video_duration ?? '') }}
                                                                            </li>
                                                                            <li>&#x2022;</li>
                                                                            <li>{{ $item->language_name }}</li>
                                                                        @endif
                                                                    </ul>
                                                                    <p
                                                                        class="mt-3 mb-4 animate__animated animate__fadeInUp">
                                                                        {{ Str::words(strip_tags($item->description ?? ''), 15, '...') }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
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
    <script>
        document.getElementById('clearWatchlistBtn')?.addEventListener('click', function() {
            if (!confirm('Are you sure you want to clear all watchlist items?')) {
                return;
            }

            let btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Clearing...';

            fetch("{{ route('watchlist.clear-all') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({})
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // remove UI instantly
                        document.querySelectorAll('.movie-new-card').forEach(el => el.remove());

                        // show empty state
                        document.querySelector('.row.g-1').innerHTML = `
                    <div class="col-12 text-center">
                        <img src="{{ URL::to('asset/images/no-data-found.png') }}" class="img-fluid" style="max-width:300px;margin-top:10px;">
                        <h3 class="text-white mt-3">No Videos Found</h3>
                        <p class="text-white">Watch Later is empty! Start adding videos to keep track of what you want to watch next.</p>
                    </div>
                `;

                        btn.remove();
                    } else {
                        alert(data.message || "Failed to clear watchlist");
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-regular fa-trash-can"></i> Clear All';
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("Something went wrong!");
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-regular fa-trash-can"></i> Clear All';
                });
        });
    </script>
@endsection
