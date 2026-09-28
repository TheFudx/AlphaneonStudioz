<!doctype html>
<html lang="en">

<head>
    <script>
        // Global website URL - sourced from Laravel config (WEBSITE_URL in .env)
        window.WEBSITE_URL = "{{ config('app.website_url') }}";

        // Clear cookies for this domain
        // document.cookie.split(";").forEach(function(c) {
        //     document.cookie = c.trim().split("=")[0] +
        //         '=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/';
        // });

        // Clear local storage
        // localStorage.clear();

        // Clear session storage
        // sessionStorage.clear();
    </script>
    <title>
        @yield('title')
    </title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
     <meta property="og:title" content="{{ $meta_title ?? config('app.project_name') }}" />
    <meta property="og:description"
        content="{{ $meta_description ?? config('app.project_full_name') . ' provides you the best video streaming experience.' }}" />
    <meta property="og:image" content="{{ $meta_image ?? url('/') . '/asset/images/meta-logo.jpg' }}" />
    <meta property="og:url" content="{{ $meta_url ?? config('app.website_url') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="{{ config('app.project_name') }}" />
    <meta property="og:locale" content="en_US" />
    <link rel="canonical" href="{{ url()->current() }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ URL::to('/asset/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ URL::to('/asset/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ URL::to('/asset/favicon_io/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ URL::to('/asset/favicon_io/site.webmanifest') }}">
    <link rel="manifest" href="/manifest.json">
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="{{ URL::to('/asset/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Space+Grotesk:wght@300;400;500&display=swap"
        rel="stylesheet">
    <!-- Bootstrap Bundle includes Popper -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ URL::to('/asset/css/owl.carousel.min.css?v=' . uniqid()) }}" media="print"
        onload="this.media='all'">
    <link rel="stylesheet" href="{{ URL::to('/asset/css/owl.theme.default.min.css?v=' . uniqid()) }}">
    <link rel="stylesheet" href="{{ URL::to('/asset/css/slick.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('/asset/css/slick-theme.min.css') }}">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="{{ URL::to('/asset/css/toastr.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ URL::to('/asset/css/style_main.css?v=' . uniqid()) }}">
    <link rel="stylesheet" href="{{ URL::to('/asset/css/style_main_media.css?v=' . uniqid()) }}">


    @yield('styles')
    <style>
        #profile img {
            width: 32px;
            height: 32px;
            margin-top: -7px;
            border-radius: 50%;
        }

        .fab-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: transparent;
            /* No background, just image */
            border: none;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            z-index: 1000;
            padding: 0;
            animation: blinkZoom 1.5s infinite;
            cursor: pointer;
        }

        .fab-button:hover {
            background-color: transparent;
        }

        .fab-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        /* Animation for zoom-in/zoom-out */
        @keyframes blinkZoom {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.15);
                opacity: 0.85;
            }
        }

        #page-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            font-family: 'Space Grotesk', sans-serif;
            padding: 20px;
            box-sizing: border-box;
            backdrop-filter: blur(6px);
        }

        #page-loader .wordmark {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-shrink: 0;
            margin-bottom: 36px;
        }

        #page-loader .line-1,
        #page-loader .line-2 {
            display: flex;
            overflow: hidden;
            justify-content: center;
            position: relative;
        }

        /* Add underline for both lines */
        #page-loader .line-1::after,
        #page-loader .line-2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 3px;
            background: #ff3a1f;
            /* Theme color underline */
            transform: scaleX(0);
            transform-origin: left;
            animation: pl-underline 2.5s cubic-bezier(.16, 1, .3, 1) infinite;
        }

        /* Separate animation for each line underline */
        #page-loader .line-1::after {
            animation-delay: 0.1s;
        }

        #page-loader .line-2::after {
            animation-delay: 0.2s;
        }

        @keyframes pl-underline {
            0% {
                transform: scaleX(0);
                opacity: 0;
            }

            10% {
                transform: scaleX(0);
                opacity: 0;
            }

            20% {
                transform: scaleX(1);
                opacity: 1;
            }

            80% {
                transform: scaleX(1);
                opacity: 1;
            }

            90% {
                transform: scaleX(1);
                opacity: 0;
            }

            100% {
                transform: scaleX(0);
                opacity: 0;
            }
        }

        #page-loader .line-1 span,
        #page-loader .line-2 span {
            display: inline-block;
            opacity: 0;
            transform: translateY(0);
            animation: pl-letterUp 2.5s cubic-bezier(.16, 1, .3, 1) infinite;
        }

        @keyframes pl-letterUp {
            0% {
                opacity: 0;
                transform: translateY(20%);
            }

            8% {
                opacity: 1;
                transform: translateY(0%);
            }

            82% {
                opacity: 1;
                transform: translateY(0%);
            }

            92% {
                opacity: 0;
                transform: translateY(-20%);
            }

            100% {
                opacity: 0;
                transform: translateY(20%);
            }
        }

        #page-loader .line-1 span {
            font-family: 'Anton', sans-serif;
            font-size: clamp(2rem, 7vw, 3.6rem);
            letter-spacing: 0.02em;
            color: #f5f1e8;
            line-height: 1;
        }

        #page-loader .line-2 span {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 400;
            font-size: clamp(0.85rem, 2.2vw, 1.05rem);
            letter-spacing: 0.62em;
            color: #ff3a1f;
            /* Theme color */
            text-transform: uppercase;
            margin-left: 0.62em;
        }

        /* Progress bar - visible on load */
        #page-loader .progress {
            position: relative;
            width: 180px;
            height: 4px;
            background: rgba(245, 241, 232, 0.15);
            overflow: hidden;
            border-radius: 4px;
            flex-shrink: 0;
            opacity: 1;
            /* Always visible */
            animation: pl-progressFade 2.5s ease-in-out infinite;
        }

        @keyframes pl-progressFade {

            0%,
            10% {
                opacity: 0.3;
            }

            20%,
            80% {
                opacity: 1;
            }

            90%,
            100% {
                opacity: 0.3;
            }
        }

        #page-loader .progress i {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 40%;
            background: linear-gradient(90deg, transparent, #ff3a1f, #ff3a1f, transparent);
            animation: pl-sweep 2s cubic-bezier(.45, 0, .2, 1) infinite;
        }

        @keyframes pl-sweep {
            0% {
                transform: translateX(-120%);
                opacity: 0;
            }

            40% {
                opacity: 0;
            }

            45% {
                opacity: 1;
                transform: translateX(-120%);
            }

            85% {
                transform: translateX(260%);
                opacity: 1;
            }

            100% {
                transform: translateX(260%);
                opacity: 0;
            }
        }

        /* Remove all iris-related styles since image is gone */
    </style>
    <script src="{{ URL::to('asset/js/jquery.min.js') }}"></script>
</head>

<body class="">
    @php
        $notify = app('notification');

    @endphp
    <main>
        <div id="page-loader">
            <!-- Image completely removed -->
            <div class="wordmark">
                <div class="line-1" id="pl-line1"></div>
                <div class="line-2" id="pl-line2"></div>
            </div>
            <div class="progress"><i></i></div>
        </div>
        <!-- Floating Add Button -->
        {{-- <a href="#" class="fab-button" title="Add Ganpati Utsav">
            <img src="{{ asset('asset/images/ganpatibappa.png') }}" alt="Ganpati Bappa" class="fab-image">
        </a> --}}
        <header>
            {{-- ✅ Helper: resolves href based on auth + subscription status --}}
            @php
                $navHref = function (string $url): string {
                    $user = app('logged-in-user');
                    if (!$user) {
                        return 'javascript:openSubscriptionModal()'; // ✅ guests open modal directly
                    }
                    if ($user->subscription !== 'Yes') {
                        return 'javascript:openSubscriptionModal()'; // ✅ logged in but no sub → modal
                    }
                    return $url;
                };

                $user = app('logged-in-user');

                $userAvatarInitial = 'G';
                if ($user) {
                    $nameTrimmed = trim($user->name ?? '');
                    $emailTrimmed = trim($user->email ?? '');

                    if ($nameTrimmed !== '') {
                        $userAvatarInitial = strtoupper(mb_substr($nameTrimmed, 0, 1));
                    } elseif ($emailTrimmed !== '') {
                        $userAvatarInitial = strtoupper(mb_substr($emailTrimmed, 0, 1));
                    }
                }

                $searchHref = route('search');

                $watchlistHref = !$user ? route('login') : route('watchlist.list');

                $downloadHref = !$user
                    ? 'javascript:openSubscriptionModal()'
                    : ($user->subscription !== 'Yes'
                        ? 'javascript:openSubscriptionModal()'
                        : route('downloded.videos'));
            @endphp
        {{-- ═══════════════════════════════════════════════════════
             MOBILE DRAWER NAVIGATION BAR
             ═══════════════════════════════════════════════════════ --}}
        <div id="mobileDrawerBackdrop" class="mobile-drawer-backdrop" onclick="closeMobileDrawer()"></div>

        <aside id="mobileNavDrawer" class="mobile-nav-drawer" aria-hidden="true">
            {{-- 1. Drawer Header --}}
            <div class="drawer-header">
                <a href="{{ url('/') }}" class="drawer-logo" onclick="closeMobileDrawer()">
                    <img src="{{ url('/') }}/asset/images/logo.png" alt="{{ config('app.project_full_name') }}">
                </a>
                <button type="button" class="drawer-close-btn" onclick="closeMobileDrawer()" aria-label="Close menu">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- 2. User / Profile Status Card --}}
            <div class="drawer-user-section">
                @if ($user)
                    <a href="{{ route('profile.view') }}" class="drawer-user-card" onclick="closeMobileDrawer()">
                        <div class="drawer-user-avatar">
                            @if ($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}"
                                    onerror="this.onerror=null; this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <span style="display:none;">{{ $userAvatarInitial }}</span>
                            @else
                                <span>{{ $userAvatarInitial }}</span>
                            @endif
                        </div>
                        <div class="drawer-user-info">
                            <div class="drawer-user-name">{{ $user->name ?: 'User' }} <i class="fa-solid fa-chevron-right ms-1 text-muted" style="font-size: 9px;"></i></div>
                            <div class="drawer-user-email">{{ $user->email ?: $user->mobile_no }}</div>
                            @if ($user->subscription === 'Yes')
                                <span class="drawer-badge badge-vip"><i class="fa-solid fa-crown"></i> VIP Member</span>
                            @else
                                <span class="drawer-badge badge-free" onclick="event.preventDefault(); event.stopPropagation(); closeMobileDrawer(); openSubscriptionModal();">
                                    <i class="fa-solid fa-crown"></i> Subscribe Now
                                </span>
                            @endif
                        </div>
                    </a>
                @else
                    <div class="drawer-guest-box">
                        <div class="drawer-guest-title">Welcome to {{ config('app.project_full_name') }}</div>
                        <div class="drawer-guest-subtitle">Sign in to enjoy movies, podcasts & more</div>
                        <a href="{{ route('login') }}" class="drawer-guest-login-btn" onclick="closeMobileDrawer()">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Login / Sign Up
                        </a>
                    </div>
                @endif
            </div>

            {{-- 3. Scrollable Navigation List --}}
            <div class="drawer-body">
                {{-- Special Features Section --}}
                <div class="drawer-section-label">Special Features</div>
                <ul class="drawer-nav-list">
                    <li class="drawer-nav-item">
                        <a class="drawer-nav-link {{ request()->is('all-ott-content*') ? 'active' : '' }}" href="{{ url('/') . '/all-ott-content' }}" onclick="closeMobileDrawer()">
                            <span class="nav-icon-label"><i class="fa-solid fa-play"></i> All OTT</span>
                            <i class="fa-solid fa-chevron-right arrow-icon"></i>
                        </a>
                    </li>
                    <li class="drawer-nav-item">
                        @if(!$user)
                            <a class="drawer-nav-link" href="{{ route('login') }}" onclick="closeMobileDrawer()">
                                <span class="nav-icon-label"><i class="fa-solid fa-face-smile"></i> Your Mood ?</span>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                        @elseif($user->subscription === 'Yes')
                            <a class="drawer-nav-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#mood-modal" onclick="closeMobileDrawer()">
                                <span class="nav-icon-label"><i class="fa-solid fa-face-smile"></i> Your Mood ?</span>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                        @else
                            <a class="drawer-nav-link" href="javascript:void(0)" onclick="closeMobileDrawer(); openSubscriptionModal();">
                                <span class="nav-icon-label"><i class="fa-solid fa-face-smile"></i> Your Mood ?</span>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                        @endif
                    </li>
                </ul>

                <div class="drawer-section-label">Browse Content</div>
                <ul class="drawer-nav-list">
                    <li class="drawer-nav-item">
                        <a class="drawer-nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}" onclick="closeMobileDrawer()">
                            <span class="nav-icon-label"><i class="fa-solid fa-house"></i> Home</span>
                            <i class="fa-solid fa-chevron-right arrow-icon"></i>
                        </a>
                    </li>
                    <li class="drawer-nav-item">
                        <a class="drawer-nav-link {{ request()->is('podcast') || request()->is('podcast/*') ? 'active' : '' }}" href="{{ $navHref(url('/') . '/podcast') }}" onclick="closeMobileDrawer()">
                            <span class="nav-icon-label"><i class="fa-solid fa-podcast"></i> Podcasts</span>
                            <i class="fa-solid fa-chevron-right arrow-icon"></i>
                        </a>
                    </li>
                    <li class="drawer-nav-item">
                        <a class="drawer-nav-link {{ request()->is('movies') || request()->is('movies/*') ? 'active' : '' }}" href="{{ $navHref(url('/') . '/movies') }}" onclick="closeMobileDrawer()">
                            <span class="nav-icon-label"><i class="fa-solid fa-film"></i> Movies</span>
                            <i class="fa-solid fa-chevron-right arrow-icon"></i>
                        </a>
                    </li>
                    <li class="drawer-nav-item">
                        <a class="drawer-nav-link series-link {{ request()->is('series*') || request()->is('webseries*') || request()->routeIs('webseries.*') ? 'active' : '' }}" href="{{ $navHref(url('/') . '/series') }}" onclick="closeMobileDrawer()">
                            <span class="nav-icon-label"><i class="fa-solid fa-tv"></i> Series</span>
                            <i class="fa-solid fa-chevron-right arrow-icon"></i>
                        </a>
                    </li>
                    <li class="drawer-nav-item">
                        @if (!$user)
                            <a class="drawer-nav-link {{ request()->is('musics-index') ? 'active' : '' }}" href="{{ route('login') }}" onclick="closeMobileDrawer()">
                                <span class="nav-icon-label"><i class="fa-solid fa-music"></i> Music</span>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                        @else
                            <a class="drawer-nav-link {{ request()->is('musics-index') ? 'active' : '' }}" href="{{ url('/') }}/musics-index" onclick="closeMobileDrawer()">
                                <span class="nav-icon-label"><i class="fa-solid fa-music"></i> Music</span>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                        @endif
                    </li>
                    <li class="drawer-nav-item">
                        <a class="drawer-nav-link {{ request()->is('shortfilms') || request()->is('shortfilms/*') ? 'active' : '' }}" href="{{ $navHref(url('/') . '/shortfilms') }}" onclick="closeMobileDrawer()">
                            <span class="nav-icon-label"><i class="fa-solid fa-clapperboard"></i> Short Films</span>
                            <i class="fa-solid fa-chevron-right arrow-icon"></i>
                        </a>
                    </li>
                    <li class="drawer-nav-item">
                        <a class="drawer-nav-link khlup-link {{ request()->routeIs('khlups.view') || request()->is('khlups*') ? 'active' : '' }}" href="{{ route('khlups.view', ['tokengen' => 'default_token']) }}" onclick="closeMobileDrawer()">
                            <span class="nav-icon-label"><i class="fa-solid fa-fire text-danger"></i> Khlup</span>
                            <i class="fa-solid fa-chevron-right arrow-icon"></i>
                        </a>
                    </li>
                </ul>

                <div class="drawer-section-label">Account & Library</div>
                <ul class="drawer-nav-list">
                    <li class="drawer-nav-item">
                        <a class="drawer-nav-link" href="{{ $searchHref }}" onclick="closeMobileDrawer()">
                            <span class="nav-icon-label"><i class="fa-solid fa-magnifying-glass"></i> Search</span>
                            <i class="fa-solid fa-chevron-right arrow-icon"></i>
                        </a>
                    </li>
                    @if (env('APP_ENV') === 'local')
                        <li class="drawer-nav-item">
                            <a class="drawer-nav-link" href="{{ $downloadHref }}" onclick="closeMobileDrawer()">
                                <span class="nav-icon-label"><i class="fa-solid fa-download"></i> Downloads</span>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                        </li>
                    @endif
                    @if ($user)
                        <li class="drawer-nav-item">
                            <a class="drawer-nav-link {{ request()->is('profile*') ? 'active' : '' }}" href="{{ route('profile.view') }}" onclick="closeMobileDrawer()">
                                <span class="nav-icon-label"><i class="fa-solid fa-user"></i> Profile</span>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- 5. Drawer Footer --}}
            <div class="drawer-footer">
                @if ($user)
                    <button type="button" class="drawer-logout-btn" onclick="closeMobileDrawer(); logoutUser(event);">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                    </button>
                @else
                    <a href="{{ route('login') }}" class="drawer-logout-btn" style="background: rgba(255,255,255,0.06); color:#fff; border-color: rgba(255,255,255,0.15);" onclick="closeMobileDrawer()">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Sign In
                    </a>
                @endif
                <div class="drawer-version">{{ config('app.project_full_name') }} © {{ date('Y') }}</div>
            </div>
        </aside>

        {{-- ═══════════════════════════════════════════════════════
             MAIN NAVBAR (Desktop & Mobile Bar)
             ═══════════════════════════════════════════════════════ --}}
        <nav
            class="navbar navbar-expand-lg navbar-dark {{ request()->routeIs('khlups.view') && request('tokengen') ? 'fixed-top' : '' }}">
            <div class="container-fluid px-3 px-xl-4">
                <a class="navbar-brand me-3" href="{{ url('/') }}">
                    <img src="{{ url('/') }}/asset/images/logo.png" alt="{{ config('app.project_full_name') }}">
                </a>

                {{-- Mobile Top Bar: Subscribe button (if not VIP) --}}
                <div class="d-flex d-lg-none align-items-center ms-auto me-2 gap-2">
                    @if (!$user || $user->subscription !== 'Yes')
                        <button onclick="openSubscriptionModal()" type="button" class="subscribe-btn mobile-sub-btn">
                            <i class="fa-solid fa-crown"></i> &nbsp;Subscribe
                        </button>
                    @endif
                </div>

                {{-- Hamburger button opens drawer --}}
                <button class="navbar-toggler d-lg-none" type="button"
                    id="navbar-toggler" onclick="openMobileDrawer()" aria-controls="mobileNavDrawer"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                {{-- Desktop Navbar Menu --}}
                <div class="collapse navbar-collapse d-none d-lg-flex" id="collapsibleNavId">
                    <ul class="navbar-nav me-auto first-ul">

                        {{-- Home --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}"
                                href="{{ url('/') }}" aria-current="page">
                                Home
                            </a>
                        </li>
                        {{-- podcast --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('podcast') || request()->is('podcast/*') ? 'active' : '' }}"
                                href="{{ $navHref(url('/') . '/podcast') }}">Podcasts</a>
                        </li>
                        {{-- Movies --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('movies') || request()->is('movies/*') ? 'active' : '' }}"
                                href="{{ $navHref(url('/') . '/movies') }}">Movies</a>
                        </li>

                        {{-- Series --}}
                        <li class="nav-item">
                            <a class="nav-link series-link {{ request()->is('series*') || request()->is('webseries*') || request()->routeIs('webseries.*') ? 'active' : '' }}"
                                href="{{ $navHref(url('/') . '/series') }}">Series</a>
                        </li>

                        {{-- Music --}}
                        <li class="nav-item">
                            @if (!$user)
                                <a class="nav-link {{ request()->is('musics-index') ? 'active' : '' }}"
                                    href="{{ route('login') }}">Music</a>
                            @else
                                <a class="nav-link {{ request()->is('musics-index') ? 'active' : '' }}"
                                    href="{{ url('/') }}/musics-index">Music</a>
                            @endif
                        </li>

                        {{-- Short Films --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('shortfilms') || request()->is('shortfilms/*') ? 'active' : '' }}"
                                href="{{ $navHref(url('/') . '/shortfilms') }}">Short Films</a>
                        </li>

                        {{-- Khlup --}}
                        <li class="nav-item">
                            <a class="nav-link khlup-link {{ request()->routeIs('khlups.view') || request()->is('khlups*') ? 'active' : '' }}"
                                href="{{ route('khlups.view', ['tokengen' => 'default_token']) }}">Khlup</a>
                        </li>

                    </ul>

                    <ul class="navbar-nav ms-auto second-ul">
                        <div class="d-flex align-items-center flex-nowrap buttonsDiv">
                            <li class="nav-item">
                                <a href="{{ url('/') . '/all-ott-content' }}" class="nav-link p-0">
                                    <button type="button" class="all-ott-btn">
                                        All OTT
                                    </button>
                                </a>
                            </li>
                            @if(!$user)
                                <li class="nav-item">
                                    <a href="{{ route('login')}}" class="nav-link p-0">
                                        <button type="button" class="whats-your-mood-btn">
                                            <i class="fa-solid fa-face-smile"></i> &nbsp;Your Mood ?
                                        </button>
                                    </a>
                                </li>
                            @elseif($user->subscription === 'Yes')
                                <li class="nav-item">
                                    <a href="#" class="nav-link p-0">
                                        <button type="button" class="whats-your-mood-btn" data-bs-toggle="modal"
                                            data-bs-target="#mood-modal">
                                            <i class="fa-solid fa-face-smile"></i> &nbsp;Your Mood ?
                                        </button>
                                    </a>
                                </li>
                            @else
                                <li class="nav-item">
                                <a href="#" class="nav-link p-0">
                                    <button type="button" class="whats-your-mood-btn" onclick="openSubscriptionModal()">
                                        <i class="fa-solid fa-face-smile"></i> &nbsp;Your Mood ?
                                    </button>
                                </a>
                            </li>
                            @endif
                            {{-- Subscribe button: desktop only --}}
                            @if (!$user)
                                <li class="nav-item d-none d-lg-flex">
                                    <a href="#" class="nav-link p-0">
                                        <button onclick="openSubscriptionModal()" type="button"
                                            class="subscribe-btn">
                                            <i class="fa-solid fa-crown"></i> &nbsp;Subscribe
                                        </button>
                                    </a>
                                </li>
                            @elseif ($user->subscription === 'Yes')
                            @else
                                <li class="nav-item d-none d-lg-flex">
                                    <a href="#" class="nav-link p-0">
                                        <button onclick="openSubscriptionModal()" type="button"
                                            class="subscribe-btn">
                                            <i class="fa-solid fa-crown"></i> &nbsp;Subscribe
                                        </button>
                                    </a>
                                </li>
                            @endif
                        </div>
                        <div class="d-flex align-items-center flex-nowrap iconsDiv">

                            {{-- Search --}}
                            <li class="nav-item">
                                <a href="{{ $searchHref }}" class="nav-link p-1 text-white" title="Search">
                                    <i class="fa-solid fa-magnifying-glass" style="font-size:18px"></i>
                                </a>
                            </li>

                            {{-- Notification dropdown --}}
                            <li class="nav-item dropdown position-relative">
                                <a href="#" class="nav-link p-1 position-relative" id="dropdownNotificationId"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa-solid fa-bell text-white" style="font-size:20px"></i>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:9px; padding:2px 5px;">
                                        {{ $notify->count() }}
                                    </span>
                                </a>
                                <div class="custom-dropdown-menu" aria-labelledby="dropdownNotificationId"
                                    id="notificationdropdownMenu">
                                    <button class="notification-close-dropdown">X</button>
                                    @if ($notify->isEmpty())
                                        <span class="text text-warning dropdown-item d-flex align-items-center">No
                                            Notifications</span>
                                    @else
                                        @foreach ($notify as $notification)
                                            <span
                                                class="text text-warning dropdown-item d-flex align-items-center">{{ $notification->title }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </li>

                            {{-- Profile / Login: desktop only --}}
                            @guest
                                @if (Session::has('is_mobile_login') && Session::get('is_mobile_login'))
                                    <li class="nav-item dropdown d-none d-lg-flex">
                                        <a class="nav-link p-1" href="#" id="profileDropdownID"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <img src="{{ url('/') }}/asset/images/user-profile.png"
                                                height="30px" width="30px" alt="">
                                        </a>
                                        <div class="custom-dropdown-menu" aria-labelledby="profileDropdownID"
                                            id="profiledropdownMenu">
                                            <button class="profile-close-dropdown">X</button>
                                            @if (env('APP_ENV') === 'local')
                                                <a href="{{ $downloadHref }}"
                                                    class="dropdown-item d-flex align-items-center">
                                                    <i class="fa-solid fa-download"></i> &nbsp;&nbsp;&nbsp;<span
                                                        class="ms-2">Downloads</span>
                                                </a>
                                            @endif
                                            <a href="{{ route('profile.view') }}"
                                                class="dropdown-item d-flex align-items-center">
                                                <i class=""></i> &nbsp;&nbsp;&nbsp;<span
                                                    class="ms-2">Profile</span>
                                            </a>
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <i class="fa-solid fa-right-from-bracket"></i>&nbsp;&nbsp;&nbsp;<span
                                                    class="ms-2">Logout</span>
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                class="d-none">@csrf</form>
                                        </div>
                                    </li>
                                @else
                                    @if (Route::has('login'))
                                        <li class="nav-item d-none d-lg-flex">
                                            <a class="nav-link p-1" href="{{ route('login') }}" title="Login">
                                                <svg width="20" height="20" viewBox="0 0 18 18"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M5 4C5 2.93913 5.42143 1.92172 6.17157 1.17157C6.92172 0.421427 7.93913 0 9 0C10.0609 0 11.0783 0.421427 11.8284 1.17157C12.5786 1.92172 13 2.93913 13 4C13 5.06087 12.5786 6.07828 11.8284 6.82843C11.0783 7.57857 10.0609 8 9 8C7.93913 8 6.92172 7.57857 6.17157 6.82843C5.42143 6.07828 5 5.06087 5 4ZM5 10C3.67392 10 2.40215 10.5268 1.46447 11.4645C0.526784 12.4021 0 13.6739 0 15C0 15.7956 0.316071 16.5587 0.87868 17.1213C1.44129 17.6839 2.20435 18 3 18H15C15.7956 18 16.5587 17.6839 17.1213 17.1213C17.6839 16.5587 18 15.7956 18 15C18 13.6739 17.4732 12.4021 16.5355 11.4645C15.5979 10.5268 14.3261 10 13 10H5Z"
                                                        fill="#FFFFFF" />
                                                </svg>
                                            </a>
                                        </li>
                                    @endif
                                @endif
                            @else
                                <li class="nav-item dropdown d-none d-lg-flex">
                                    @if ($user->profile_picture)
                                        <a class="nav-link p-1 text-white" href="#" id="profile"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                                class="profile-img" alt="Profile Picture">
                                        </a>
                                    @else
                                        <a class="nav-link user-header-avatar text-white p-1" href="#"
                                            id="profile" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            {{ $userAvatarInitial }}
                                        </a>
                                    @endif
                                    <div class="custom-dropdown-menu" aria-labelledby="profile">
                                        <button class="profile-close-dropdown">X</button>
                                        @if (env('APP_ENV') === 'local')
                                            <a href="{{ $downloadHref }}"
                                                class="dropdown-item d-flex align-items-center">
                                                <i class="fa-solid fa-download"></i> &nbsp;&nbsp;&nbsp;<span
                                                    class="ms-2">Downloads</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('profile.view') }}"
                                            class="dropdown-item d-flex align-items-center">
                                            <i class="fa-solid fa-user"></i> &nbsp;&nbsp;&nbsp;<span
                                                class="ms-2">Profile</span>
                                        </a>
                                        <a class="dropdown-item d-flex align-items-center" href="#"
                                            onclick="logoutUser(event);">
                                            <i class="fa-solid fa-right-from-bracket"></i>&nbsp;&nbsp;&nbsp;<span
                                                class="ms-2">Logout</span>
                                        </a>
                                    </div>
                                </li>
                            @endguest
                        </div>
                    </ul>
                </div>
            </div>
        </nav>
        </header>
        <!-- Logout Confirmation Modal -->
        <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header">
                        <h5 class="modal-title text-light" id="logoutConfirmModalLabel">
                            <i class="fa-solid fa-right-from-bracket me-2 text-danger"></i>Confirm Logout
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <i class="fa-solid fa-circle-question text-warning" style="font-size: 50px;"></i>
                        </div>
                        <h5 class="text-light">Are you sure you want to logout?</h5>
                        <p class="text-light">You will need to login again to access your account.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa-solid fa-times me-1"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmLogoutBtn">
                            <i class="fa-solid fa-right-from-bracket me-1"></i> Yes, Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function plBuildLine(el, text, baseDelay, step) {
                if (!el) return;
                text.split('').forEach((ch, i) => {
                    const s = document.createElement('span');
                    s.textContent = ch === ' ' ? '\u00A0' : ch;
                    s.style.animationDelay = (baseDelay + i * step) + 's';
                    el.appendChild(s);
                });
            }
            // Start with minimal delay for faster appearance
            plBuildLine(document.getElementById('pl-line1'), 'ALPHANEON', 0.05, 0.02);
            plBuildLine(document.getElementById('pl-line2'), 'STUDIOZ', 0.1, 0.03);
        </script>
        <script>
            // ═══════════════════════════════════════════════════════
            // MOBILE DRAWER NAVIGATION CONTROLS
            // ═══════════════════════════════════════════════════════
            function openMobileDrawer() {
                var drawer = document.getElementById('mobileNavDrawer');
                var backdrop = document.getElementById('mobileDrawerBackdrop');
                var collapseMenu = document.getElementById('collapsibleNavId');
                if (collapseMenu && window.innerWidth < 992) {
                    collapseMenu.style.setProperty('display', 'none', 'important');
                }
                if (drawer && backdrop) {
                    drawer.classList.add('active');
                    backdrop.classList.add('active');
                    document.body.classList.add('drawer-open');
                    drawer.setAttribute('aria-hidden', 'false');
                }
            }

            function closeMobileDrawer() {
                var drawer = document.getElementById('mobileNavDrawer');
                var backdrop = document.getElementById('mobileDrawerBackdrop');
                if (drawer && backdrop) {
                    drawer.classList.remove('active');
                    backdrop.classList.remove('active');
                    document.body.classList.remove('drawer-open');
                    drawer.setAttribute('aria-hidden', 'true');
                }
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    closeMobileDrawer();
                }
            });

            // Swipe left on drawer to close
            (function() {
                var drawer = document.getElementById('mobileNavDrawer');
                if (!drawer) return;
                var startX = 0;
                drawer.addEventListener('touchstart', function(e) {
                    if (e.touches.length === 1) {
                        startX = e.touches[0].clientX;
                    }
                }, { passive: true });

                drawer.addEventListener('touchmove', function(e) {
                    if (e.touches.length === 1) {
                        var diffX = startX - e.touches[0].clientX;
                        if (diffX > 60) {
                            closeMobileDrawer();
                        }
                    }
                }, { passive: true });
            })();
        </script>
        <script>
            function logoutUser(event) {
                event.preventDefault();

                // Show the modal
                $('#logoutConfirmModal').modal('show');
            }

            // Handle confirm logout button click
            $(document).on('click', '#confirmLogoutBtn', function() {
                // Close the modal
                $('#logoutConfirmModal').modal('hide');

                // Show loading indicator in the button
                $(this).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Logging out...');
                $(this).prop('disabled', true);

                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: "{{ route('logout') }}",
                    type: "POST",
                    data: {
                        _token: csrfToken
                    },
                    success: function(response) {
                        // Show toastr success message
                        toastr.options.positionClass = "toast-top-center";
                        toastr.success('You have been logged out successfully!', 'Logout Successful');

                        // Close dropdowns
                        $('.custom-dropdown-menu').hide();

                        // Redirect after a short delay
                        setTimeout(function() {
                            window.location.href = "{{ route('login') }}";
                        }, 1500);
                    },
                    error: function(xhr, status, error) {
                        toastr.options.positionClass = "toast-top-center";
                        toastr.error('Failed to logout. Please try again.', 'Error');

                        // Reset button
                        $('#confirmLogoutBtn').html(
                            '<i class="fa-solid fa-right-from-bracket me-1"></i> Yes, Logout');
                        $('#confirmLogoutBtn').prop('disabled', false);
                    }
                });
            });
            $(document).ready(function() {
                let user = `{{ app('logged-in-user') }}`;
                let userId = `{{ app('logged-in-user') ? app('logged-in-user')->id : '' }}`;



                if (user && userId) {

                    navigator.geolocation.getCurrentPosition(async position => {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;

                        try {
                            const res = await fetch(
                                `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`
                            );
                            const data = await res.json();

                            let city = data.address.city;
                            let state = data.address.state;
                            let country = data.address.country;
                            let country_code = data.address.country_code;
                            let latitude = data.lat;
                            let longitude = data.lon;

                            console.log("City:", data);
                            $.ajax({
                                url: "/save-user-location", // your route
                                type: "POST",
                                data: {
                                    user_id: userId,
                                    city: city,
                                    state: state,
                                    country: country,
                                    country_code: country_code,
                                    latitude: latitude,
                                    longitude: longitude,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    console.log("Location saved:", response);
                                },
                                error: function(xhr) {
                                    console.log("Error saving:", xhr);
                                }
                            });
                        } catch (error) {

                            console.error("Error fetching reverse geocode:", error);
                        }
                    }, error => {

                        console.error("Geolocation error:", error);
                    });
                }
            });
            // $(document).ready(function() {
            // $("#navbar-toggler").click(function(e) {
            //     e.preventDefault();
            //     $('#collapsibleNavId').toggle();
            //     if ($('#collapsibleNavId').is(':visible')) {
            //         $(".navbar").css('background',
            //             'linear-gradient(to top, #000000 70%, rgba(255, 0, 0, 0))');
            //     } else {
            //         $(".navbar").css('background', ''); // remove the background
            //     }
            // });

            // // Function to hide all custom dropdowns
            // function hideAllCustomDropdowns() {
            //     $('.custom-dropdown-menu').hide();

            // }

            // // 1. Handle clicks on dropdown triggers to toggle visibility
            // $('.nav-item.dropdown > .nav-link').on('click', function(event) {
            //     event.preventDefault(); // Prevent default link behavior
            //     event
            //         .stopPropagation(); // Stop propagation to prevent document click from closing immediately

            //     const clickedDropdownMenu = $(this).siblings('.custom-dropdown-menu');

            //     // Hide all other dropdowns before showing the clicked one
            //     $('.custom-dropdown-menu').not(clickedDropdownMenu).hide();

            //     // Toggle the clicked dropdown
            //     clickedDropdownMenu.toggle();
            // });


            // // 2. Handle close button clicks within dropdowns
            // $('.close-dropdown, .profile-close-dropdown, .notification-close-dropdown').on('click', function(
            //     event) {
            //     event
            //         .stopPropagation(); // Prevent click from bubbling up and re-opening/closing other dropdowns
            //     $(this).closest('.custom-dropdown-menu').hide();
            // });


            // // 3. Close dropdown when clicking anywhere outside a custom-dropdown-menu or its trigger
            // $(document).on('click', function(event) {
            //     // Check if the click occurred outside any custom dropdown menu
            //     // and also outside any of the dropdown trigger links/buttons
            //     if (!$(event.target).closest('.custom-dropdown-menu').length &&
            //         !$(event.target).closest('.nav-item.dropdown > .nav-link').length) {
            //         const dropdown = document.querySelector('#dropdownId');
            //         const svg = dropdown.querySelector('svg');
            //         const box3 = svg.querySelector('#box3');
            //         const box4 = svg.querySelector('#box4');
            //         svg.style.transform = 'rotate(0deg)';
            //         box3.style.fill = 'white'; // Reset to original or use original color
            //         box4.style.fill = 'white';
            //         hideAllCustomDropdowns();
            //     }
            // });
            // // --- Existing Owl Carousel Initialization ---
            // $(".owl-carousel .owl-nav").removeClass('disabled');
            // $(".owl-carousel").each(function() {
            //     var carousel = $(this);
            //     if (carousel.data("skip-carousel") === true) {
            //         return true;
            //     }
            //     if (!carousel.hasClass("owl-loaded")) {
            //         carousel.owlCarousel({
            //             dots: false,
            //             nav: false,
            //             loop: false,
            //             autoplay: false,
            //             margin: 10,
            //             autoplayTimeout: 10000,
            //             autoplayHoverPause: true,
            //             responsive: {
            //                 0: {
            //                     items: 2,
            //                     stagePadding: 0,
            //                     margin: 10
            //                 },
            //                 320: {
            //                     items: 2,
            //                     stagePadding: 0,
            //                     margin: 10
            //                 },
            //                 425: {
            //                     items: 2
            //                 },
            //                 576: {
            //                     items: 2
            //                 },
            //                 768: {
            //                     items: 2
            //                 },
            //                 992: {
            //                     items: 5
            //                 },
            //                 1200: {
            //                     items: 5
            //                 },
            //                 1700: {
            //                     items: 7
            //                 },
            //                 1800: {
            //                     items: 7
            //                 },
            //                 2732: {
            //                     items: 12
            //                 }
            //             },
            //             onInitialized: addActiveClasses,
            //             onTranslated: addActiveClasses,
            //             onResized: addActiveClasses,
            //             onRefreshed: addActiveClasses,
            //             onDragged: addActiveClasses
            //         });
            //     }
            // });

            // function addActiveClasses(event) {
            //     var carousel = $(event.target);
            //     carousel.find(".owl-item").removeClass(
            //         "first-active last-active");

            //     var activeItems = carousel.find(
            //         ".owl-item.active");
            //     if (activeItems.length > 0) {
            //         if (activeItems.length === 1) {
            //             activeItems.first().addClass("first-active");
            //         } else {
            //             var firstActive = null;
            //             activeItems.each(function() {
            //                 var item = $(this);
            //                 var itemLeft = item.position().left;
            //                 var carouselWidth = carousel.find('.owl-stage-outer')
            //                     .width();
            //                 if (itemLeft >= -5 && itemLeft < carouselWidth) {
            //                     firstActive = item;
            //                     return false;
            //                 }
            //             });
            //             if (firstActive) {
            //                 firstActive.addClass("first-active");
            //             } else {
            //                 activeItems.first().addClass("first-active");
            //             }
            //             activeItems.last().addClass("last-active");
            //         }
            //     }
            // }
            // });
        </script>
