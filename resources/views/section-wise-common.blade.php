<section id="section-home-newdes" class="{{ $class }}" loading="lazy">
    <div class="home-newdes-section">
        <div class="home-newdes-section-container">
            <div class="container-fluid">
                @php
                    $user = app('logged-in-user');
                    $viewAllHref = !$user
                        ? 'javascript:openSubscriptionModal()'
                        : ($user->subscription !== 'Yes'
                            ? 'javascript:openSubscriptionModal()'
                            : $viewall_route);
                @endphp
                <div class="homepage-heading d-flex justify-content-between align-items-center pe-3">
                    <span class="l-head">{{ $title }} {{ isset($sub_title) ? '-' . $sub_title : '' }}</span>
                    <a href="{{ $viewAllHref }}" class="l-sub-head text-white text-decoration-none">
                        View All >
                    </a>
                </div>
                <div class="slider-container">
                    <div class="latest-release-slider owl-carousel owl-theme" id="{{ $id }}">
                        @foreach ($data as $d)
                            @php
                                $dedicatedUrl = route($view_route, [
                                    'id' => App\Helpers\VideoHelper::encryptID($d->id),
                                ]);
                                $watchHref = !$user
                                    ? 'javascript:openSubscriptionModal()'
                                    : ($user->subscription !== 'Yes'
                                        ? 'javascript:openSubscriptionModal()'
                                        : $dedicatedUrl);
                            @endphp
                            <div class="item">
                                <div class="cards-container">
                                    <div class="alpha-card skeleton">
                                        <div class="image-wrapper">
                                            <a href="{{ $watchHref }}">
                                                <img class="lazyload"
                                                    src="{{ config('app.website_url') }}admin_panel/public/images/video/{{ $d->landscape }}"
                                                    alt="{{ $d->landscape }}" loading="lazy">
                                            </a>
                                        </div>
                                        <div class="alpha-card-large {{ $d->trailer_url ? 'movie-card' : '' }}"
                                            id="movie-card">
                                            <img src="{{ config('app.website_url') }}admin_panel/public/images/video/{{ $d->landscape }}"
                                                class="thumbnail lazyload" id="thumbnail" alt="{{ $d->landscape }}"
                                                loading="lazy">
                                            <div class="content-holder">
                                                <div class="content">
                                                    <div class="button-holder row">
                                                        <div class="col-9">
                                                            <a href="{{ $watchHref }}">
                                                                <button class="btn-watchnow" type="button">
                                                                    <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                    Watch Now
                                                                </button>
                                                            </a>
                                                        </div>
                                                        <div class="col-3 ps-0">
                                                            @include('watchlist-button', [
                                                                'id' => $d->id,
                                                                'type_id' => $d->type_id,
                                                                'category_id' => $d->category_id,
                                                                'type' => 'video',
                                                                'watchlist' => $watchlist,
                                                            ])
                                                        </div>
                                                    </div>
                                                    <ul class="animate__animated animate__fadeInUp mt-3">
                                                        <li>{{ \Carbon\Carbon::parse($d->release_date)->format('Y') }}
                                                        </li>
                                                        <li>&#x2022;</li>
                                                        <li>
                                                            @if ($d->video_duration)
                                                                {{ MillisecondsToTime($d->video_duration) }}
                                                            @else
                                                                -
                                                            @endif
                                                        </li>
                                                        <li>&#x2022;</li>
                                                        <li>{{ $d->language->name }}</li>
                                                    </ul>
                                                    <p class="mt-1 mb2 animate__animated animate__fadeInUp">
                                                        {{ Str::words(strip_tags($d->description), 15, '...') }}
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
