@extends('layouts.main')
@section('title')
    {{ config('app.project_name') }} | Podcasts
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
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <span class="l-head">Podcasts</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 d-flex justify-content-end">
                            <div class="category mt-md-3 mt-3 w-100">
                                <div class="category-carousel">
                                    <div id="languages" class="owl-carousel owl-theme">
    
                                        {{-- All Languages --}}
                                        <div class="item">
                                            <input type="radio" name="language" value="" class="d-none"
                                                id="lang0" {{ empty($selectedLanguage) ? 'checked' : '' }}>
    
                                            <label for="lang0">
                                                <a href="{{ route('podcasts.list', ['category' => $selectedCategory]) }}"
                                                    class="text-decoration-none text-white">
                                                    All
                                                </a>
                                            </label>
                                        </div>
    
                                        @foreach ($language as $item)
                                            <div class="item">
                                                <input type="radio" name="language" value="{{ $item->id }}"
                                                    class="d-none" id="lang{{ $item->id }}"
                                                    {{ $selectedLanguage == $item->id ? 'checked' : '' }}>
    
                                                <label for="lang{{ $item->id }}">
                                                    <a href="{{ route('podcasts.list', [
                                                        'category' => $selectedCategory,
                                                        'language' => $item->id,
                                                    ]) }}"
                                                        class="text-decoration-none text-white">
    
                                                        {{ $item->name }}
    
                                                    </a>
                                                </label>
                                            </div>
                                        @endforeach
    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-md-6">
                            <div class="category mt-md-3 mt-3">
                                <div class="category-carousel">
    
                                    <div id="categories" class="owl-carousel owl-theme">
    
                                     
                                        <div class="item">
                                            <input type="radio" name="category" value="All" id="cat0" class="d-none"
                                                {{ ($selectedCategory ?? 'All') == 'All' ? 'checked' : '' }}>
    
                                            <label for="cat0">
                                                <a href="{{ route('podcasts.list', [
                                                    'language' => $selectedLanguage,
                                                ]) }}"
                                                    class="text-decoration-none text-white">
    
                                                    All
    
                                                </a>
                                            </label>
                                        </div>
    
                                        @foreach ($catlist as $item)
                                        @if($item->category_name != 'Podcast')
                                            <div class="item">
    
                                                <input type="radio" name="category" value="{{ $item->cat_id }}"
                                                    class="d-none" id="cat{{ $item->cat_id }}"
                                                    {{ $selectedCategory == $item->cat_id ? 'checked' : '' }}>
    
                                                <label for="cat{{ $item->cat_id }}">
    
                                                    <a href="{{ route('podcasts.list', [
                                                        'category' => $item->cat_id,
                                                        'language' => $selectedLanguage,
                                                    ]) }}"
                                                        class="text-decoration-none text-white">
    
                                                        {{ $item->category_name }}
    
                                                    </a>
    
                                                </label>
    
                                            </div>
                                        @endif
                                        @endforeach
    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <hr class="mb-3">
                    <div class="slider-container mt-4">
                        <div class="latest-release-slider movies-page-section">
                            <div class="row g-1">
                                @foreach ($video as $key => $videoItem)
                                    {{-- <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3"> --}}
                                    <div class="item movie-new-card col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 col-xxl-2 ps-md-2 ps-sm-1 pe-sm-1 pe-md-2 mb-2 pe-1 ps-1"
                                        data-category-id="{{ $videoItem->category_id }}" data-index="{{ $key }}">
                                        <div class="cards-container">
                                            <div class="alpha-card skeleton card-for-podcast">
                                                <div class="image-wrapper">
                                                    <a
                                                         href="{{ route('podcast.details', ['slug' => $videoItem->slug]) }}"><img
                                                            src="{{ !empty($videoItem->landscape) ? config('app.website_url') . 'admin_panel/public/images/video/' . $videoItem->landscape : asset('asset/images/thumbnail-1.png') }}"
                                                            onerror="this.onerror=null;this.src='{{ asset('asset/images/thumbnail-1.png') }}';"
                                                            alt="{{ $videoItem->landscape ?? 'Podcast' }}" loading="lazy"></a>

                                                </div>
                                                <div class="alpha-card-large {{ $videoItem->trailer_url ? 'movie-card' : '' }}"
                                                    id="movie-card">
                                                    <img src="{{ !empty($videoItem->landscape) ? config('app.website_url') . 'admin_panel/public/images/video/' . $videoItem->landscape : asset('asset/images/thumbnail-1.png') }}"
                                                        onerror="this.onerror=null;this.src='{{ asset('asset/images/thumbnail-1.png') }}';"
                                                        class="thumbnail" id="thumbnail" alt="{{ $videoItem->landscape ?? 'Podcast' }}"
                                                        loading="lazy">
                                                    <video class="video-holder" loop muted id="video-holder">
                                                        <source src="{{ $videoItem->trailer_url }}" type="video/mp4">
                                                    </video>

                                                    <div class="content-holder pt-5">
                                                        <div class="content">
                                                            <div class="button-holder row">
                                                                <div class="col-9">
                                                                    <a
                                                                        href="{{ route('podcast.details', ['slug' => $videoItem->slug]) }}">
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
                                            {{-- ... existing card content ... --}}
                                            {{-- <button class="btn btn-dark text-white mt-5 download-offline-button"
                                                data-video-url="{{ route('video.proxy', ['externalUrl' => $videoItem->video_320_url]) }}">
                                                Download Video for Offline
                                            </button>
                                            <p class="download-status-{{ $videoItem->id }} text-white"></p> --}}
                                        </div>
                                    </div>
                                    {{-- </div> --}}
                                @endforeach
                            </div>
                            <!-- Pagination -->
                            <div class="pagination-links mt-4">
                                {{ $video->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => { // This is important!
                navigator.serviceWorker.register('/serviceworker.js')
                    .then(registration => {
                        console.log('Service Worker registered:', registration);
                    })
                    .catch(error => {
                        console.error('Service Worker registration failed:', error);
                    });
            });
        } else {
            console.warn('Service Workers are not supported by this browser.');
        }
    </script>
    <script src="{{ URL::to('asset/js/gridView.js') }}"></script>
    <script>
        // Get all buttons with the class 'download-offline-button'
        document.addEventListener('DOMContentLoaded', () => {
            // ... your existing Service Worker registration logic ...

            const downloadButtons = document.querySelectorAll('.download-offline-button');

            downloadButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    const videoUrl = event.currentTarget.dataset.videoUrl;
                    // Assuming videoItem->id is available in the DOM via a data attribute or similar
                    const videoItemId = event.currentTarget.closest('.video-item-container')
                        ?.dataset.videoId; // Example: assuming a parent div with data-video-id
                    const statusParagraph = document.querySelector(
                        `.download-status-${videoItemId}`);

                    if (statusParagraph) {
                        statusParagraph.textContent = 'Preparing for download...';
                    }

                    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                        console.log('Sending message to service worker to cache video:', videoUrl);
                        navigator.serviceWorker.controller.postMessage({
                            type: 'CACHE_VIDEO',
                            url: videoUrl,
                            videoId: videoItemId // Pass video ID for status update
                        });
                    } else {
                        console.warn('Service Worker is not active. Cannot send caching message.');
                        if (statusParagraph) {
                            statusParagraph.textContent =
                                'Offline download not available (Service Worker not active).';
                        }
                    }
                });
            });

            // Listen for messages from the Service Worker
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data) {
                        const videoUrl = event.data.url;
                        const videoId = event.data.videoId; // Get the video ID back
                        const statusParagraph = document.querySelector(`.download-status-${videoId}`);

                        if (statusParagraph) {
                            if (event.data.type === 'VIDEO_CACHED') {
                                if (event.data.status === 'already_cached') {
                                    statusParagraph.textContent =
                                        'Video already downloaded for offline use.';
                                } else {
                                    statusParagraph.textContent = 'Video downloaded for offline use!';
                                }
                            } else if (event.data.type === 'VIDEO_CACHE_FAILED') {
                                statusParagraph.textContent =
                                    `Failed to download video: ${event.data.error || 'Unknown error'}.`;
                                console.error('Video caching failed:', event.data.url, event.data.error);
                            }
                        }
                    }
                });
            }
        });
    </script> --}}
@endsection
