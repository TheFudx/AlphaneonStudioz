<!doctype html>
<html lang="en">

<head>
    <title>
        @yield('title')
    </title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    {{-- <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Alphaneon Studioz is an entertainment platform where we are offering a wide range of content. Alphaneon Studioz provides you the best video streaming experience." />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Facebook Open Graph Tags -->
    <meta property="og:title" content="Alphastudioz" />
    <meta property="og:description" content="Alphaneon Studioz provides you the best video streaming experience." />
    <meta property="og:image" content="{{ url('/') }}/asset/images/logo.png" />
    <meta property="og:url" content="https://alphastudioz.in/" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Alphastudioz" />
    <meta property="og:locale" content="en_US" />

    <link rel="canonical" href="{{ url()->current() }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/') }}/asset/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/') }}/asset/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/') }}/asset/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="{{ url('/') }}/asset/favicon_io/site.webmanifest">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Bundle includes Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/asset/css/owl.carousel.min.css?v={{ time() }}">
    <link rel="stylesheet" href="{{ url('/') }}/asset/css/owl.theme.default.min.css?v={{ time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/asset/css/plyr.css">
    <link href="{{ URL::to('/asset/css/toastr.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ url('/') }}/asset/css/style_main.css?v={{ time() }}">
    {{-- <link rel="stylesheet" href="{{ url('/') }}/asset/css/alphastudioz.css?v={{ time() }}"> --}}
    @yield('styles')
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5792165796361069"
        crossorigin="anonymous"></script>
    <!-- Toastr -->
    <script src="{{ URL::to('/asset/js/toastr.min.js') }}"></script>

    <style>
        #profile img {
            width: 32px;
            height: 32px;
            margin-top: -7px;
            border-radius: 50%;
        }
    </style>
</head>

<body class="">
    @php
        $notify = app('notification');

    @endphp
    <main>
        <div id="page-loader">
            <img src="{{ url('/') }}/asset/images/loader.gif" alt="Loading...">
        </div>
        <header>
            <nav
                class="navbar navbar-expand-lg navbar-dark {{ request()->routeIs('khlups.view') && request('tokengen') ? 'fixed-top' : '' }}">
                <div class="container-fluid ms-md-4">
                    <a class="navbar-brand" href="{{ url('/') }}"><img
                            src="{{ url('/') }}/asset/images/logo.png" alt=""></a>
                    <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
                        id="navbar-toggler" data-bs-target="#collapsibleNavId" aria-controls="collapsibleNavId"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="collapsibleNavId">
                        <ul class="navbar-nav me-auto mt-2 mt-lg-0 first-ul">
                            <li class="nav-item">
                                <a class="nav-link home-link {{ request()->is('/') ? 'active' : '' }}"
                                    href="{{ url('/') }}" aria-current="page">Home
                                    <span class=""></span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link movies-link {{ request()->is('movies/list') ? 'active' : '' }}"
                                    href="{{ url('/') }}/movies/list">Movies</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link series-link {{ request()->is('webseries/list') ? 'active' : '' }}"
                                    href="{{ url('/') }}/webseries/list">Series</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link khlup-link {{ request()->routeIs('khlups.view') && request('tokengen') ? 'active' : '' }}"
                                    href="{{ route('khlups.view', ['tokengen' => 'default_token']) }}">Khlup</a>
                            </li>
                            {{-- menu dropdown  --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link " href="#" id="dropdownId">
                                    <svg width="24" height="24" viewBox="0 0 24 24" id="mainSquare"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="11" height="11" rx="1" fill="white" />
                                        <rect y="13" width="11" height="11" rx="1" fill="white" />
                                        <rect x="13" width="11" height="11" rx="1" id="box3"
                                            fill="white" />
                                        <rect x="13" y="13" width="11" height="11" id="box4"
                                            rx="1" fill="white" />
                                    </svg>
                                </a>
                                <div class="custom-dropdown-menu" aria-labelledby="dropdownId"
                                    id="firstdropdownMenu">
                                    <button class="close-dropdown">X</button>
                                    <a class="dropdown-item {{ request()->is('podcasts/list') ? 'active' : '' }}"
                                        href="{{ url('/') }}/podcasts/list">Podcasts</a>
                                    <a class="dropdown-item {{ request()->is('sports/list') ? 'active' : '' }}"
                                        href="{{ url('/') }}/sports/list">Sports</a>
                                    <a class="dropdown-item {{ request()->is('tvshows/list') ? 'active' : '' }}"
                                        href="{{ url('/') }}/tvshows/list">Tv Show</a>
                                    <a class="dropdown-item {{ request()->is('kids/list') ? 'active' : '' }}"
                                        href="{{ url('/') }}/kids/list">Kids</a>
                                    @if (!app('logged-in-user'))
                                        <a class="dropdown-item  {{ request()->is('musics/list') ? 'active' : '' }}"
                                            href="{{ route('subscribe') }}">Musics</a>
                                    @elseif (app('logged-in-user')->subscription === 'Yes')
                                        <a class="dropdown-item  {{ request()->is('musics/list') ? 'active' : '' }}"
                                            href="{{ url('/') }}/musics/list">Musics</a>
                                    @else
                                        <a class="dropdown-item  {{ request()->is('musics/list') ? 'active' : '' }}"
                                            href="{{ route('subscribe') }}">Musics</a>
                                    @endif
                                    <a class="dropdown-item {{ request()->is('shortfilms') ? 'active' : '' }}"
                                        href="{{ url('/') }}/shortfilms/list">Short Films</a>
                                </div>
                            </li>
                        </ul>
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0 second-ul">
                            <div class="d-flex justify-content-center flex-grow-1 buttonsDiv">
                                <li class="nav-item mx-2">
                                    <a href="#" class="nav-link"><button type="button"
                                            class="whats-your-mood-btn" data-bs-toggle="modal"
                                            data-bs-target="#mood-modal">
                                            <i class="fa-solid fa-face-smile"></i> &nbsp;&nbsp; Your Mood
                                            ?</button></a>
                                </li>
                                @if (!app('logged-in-user'))
                                    <li class="nav-item mx-2">
                                        <a href="{{ route('subscribe') }}" class="nav-link"><button type="button"
                                                class="subscribe-btn">
                                                <i class="fa-solid fa-crown"></i> &nbsp;&nbsp;
                                                Subscribe</button></a>
                                    </li>
                                @elseif (app('logged-in-user')->subscription === 'Yes')
                                @else
                                    <li class="nav-item mx-2">
                                        <a href="{{ route('subscribe') }}" class="nav-link"><button type="button"
                                                class="subscribe-btn">
                                                <i class="fa-solid fa-crown"></i> &nbsp;&nbsp;
                                                Subscribe</button></a>
                                    </li>
                                @endif
                            </div>
                            <div class="d-flex justify-content-center flex-grow-1 mt-2">
                                <li class="nav-item mx-2">
                                    <a href="{{ route('search', ['keyword' => '']) }}" class="nav-link">
                                        <i class="fa-solid fa-magnifying-glass text-white"></i></a>
                                </li>
                                {{-- notifcation dropdown  --}}
                                <li class="nav-item mx-2 dropdown">
                                    <a href="#" class="nav-link" id="dropdownNotificationId"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa-solid fa-bell text-white" style="font-size:25px"></i>
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
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
                                                <span
                                                    class="text text-message dropdown-item d-flex align-items-center">{{ $notification->message }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                </li>
                                @guest
                                    @if (Session::has('is_mobile_login') && Session::get('is_mobile_login'))
                                        {{-- profile dropdown  --}}
                                        <li class="nav-item mx-2 dropdown">
                                            <a class="nav-link " href="#" id="profileDropdownID"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <img src="{{ url('/') }}/asset/images/user-profile.png"
                                                    height="30px" width="30px" alt="">
                                            </a>
                                            <div class="custom-dropdown-menu" aria-labelledby="profileDropdownID"
                                                id="profiledropdownMenu">
                                                <button class="profile-close-dropdown">X</button>
                                                <a href="{{ route('watchlist.list') }}"
                                                    class="dropdown-item d-flex align-items-center">
                                                    <i class="fa-solid fa-plus"></i> &nbsp;&nbsp;&nbsp;
                                                    <span class="ms-2">Watchlist </span></a>
                                                @if (env('APP_ENV') === 'local')
                                                    <a href="{{ route('downloded.videos') }}"
                                                        class="dropdown-item d-flex align-items-center">
                                                        <i class="fa-solid fa-download"></i> &nbsp;&nbsp;&nbsp;
                                                        <span class="ms-2">Downloads </span></a>
                                                @endif
                                                <a href="{{ route('profile.view') }}"
                                                    class="dropdown-item d-flex align-items-center">
                                                    <i class="fa-solid fa-user"></i> &nbsp;&nbsp;&nbsp;
                                                    <span class="ms-2">Profile</span></a>
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('logout') }}"
                                                    onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                                    <i class="fa-solid fa-right-from-bracket"></i>&nbsp;&nbsp;&nbsp;
                                                    <span class="ms-2">Logout</span>
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                </form>
                                            </div>
                                        </li>
                                    @else
                                        @if (Route::has('login'))
                                            <li class="nav-item mx-2">
                                                <a class="nav-link" href="{{ route('login') }}">
                                                    <svg width="22" height="24" viewBox="0 0 18 18"
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
                                    <li class="nav-item mx-2 dropdown">
                                        @if (app('logged-in-user')->profile_picture)
                                            <a class="nav-link  text-white" href="#" id="profile"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <img src="{{ asset('storage/profile_pictures/' . app('logged-in-user')->profile_picture) }}"
                                                    class="profile-img" alt="Profile Picture">
                                            </a>
                                        @else
                                            <a class="nav-link user-header-avatar  text-white" href="#"
                                                id="profile" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                {{ app('logged-in-user')->email ? strtoupper(substr(app('logged-in-user')->email, 0, 1)) : strtoupper(substr(app('logged-in-user')->name, 0, 1)) }}
                                                {{-- {{ strtoupper(substr(app('logged-in-user')->email, 0, 1)) }} --}}
                                            </a>
                                        @endif
                                        <div class="custom-dropdown-menu" aria-labelledby="profile">
                                            <button class="profile-close-dropdown">X</button>
                                            <a href="{{ route('watchlist.list') }}"
                                                class="dropdown-item d-flex align-items-center">
                                                <i class="fa-solid fa-plus"></i> &nbsp;&nbsp;&nbsp;
                                                <span class="ms-2">Watchlist </span></a>
                                            @if (env('APP_ENV') === 'local')
                                                <a href="{{ route('downloded.videos') }}"
                                                    class="dropdown-item d-flex align-items-center">
                                                    <i class="fa-solid fa-download"></i> &nbsp;&nbsp;&nbsp;
                                                    <span class="ms-2">Downloads </span></a>
                                            @endif
                                            <a href="{{ route('profile.view') }}"
                                                class="dropdown-item d-flex align-items-center">
                                                <i class="fa-solid fa-user"></i> &nbsp;&nbsp;&nbsp;
                                                <span class="ms-2">Profile</span></a>
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{ route('logout') }}"
                                                onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                                <i class="fa-solid fa-right-from-bracket"></i>&nbsp;&nbsp;&nbsp;
                                                <span class="ms-2">Logout</span>
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                class="d-none">
                                                @csrf
                                            </form>
                                        </div>
                                    </li>
                                @endguest
                            </div>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
        <script>
            navigator.brave?.isBrave?.().then(function(result) {
                console.log("Is Brave:", result);
                $("#is_brave").val(result ? 1 : 0); // Convert true/false to 1/0 if needed
            });
        </script>
        <script>
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
