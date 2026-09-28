@extends('layouts.main')
@section('title')
    {{ config('app.project_name') }} | Profile
@endsection
@section('styles')
    {{-- <link rel="stylesheet" href="{{url('/')}}/asset/css/login.css"> --}}
    <style>
        /* Hide page loader elements */
        /* #page-loader {
                                                                                                    display: block;
                                                                                                }

                                                                                                #page-loader img {
                                                                                                    display: block;
                                                                                                } */

        .user-banner-section {
            font-family: Arial, sans-serif;
        }

        .user-banner-wrapper {
            position: relative;
        }

        .user-banner-image-container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .user-banner-image {
            width: 100%;
            height: 410px;
            object-fit: cover;
            display: block;
        }

        .user-banner-image-reflection {
            width: 100%;
            height: 150px;
            object-fit: cover;
            transform: scaleY(-1);
            opacity: 0.3;
            filter: blur(2px);
            margin-top: -5px;
            mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), transparent);
            -webkit-mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), transparent);
        }

        .user-banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 410px;
            padding: 30px;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
        }

        .user-details-container {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-details-container img {
            height: 100px;
            width: 100px;
            border-radius: 50%;
        }

        .user-avatar {
            width: 115px;
            height: 115px;
            background-color: #f44336;
            color: white;
            border-radius: 50%;
            font-size: 60px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-info {
            color: white;
        }

        .user-name {
            margin: 0;
            font-size: 24px;
        }

        .user-phone {
            margin: 0;
            font-size: 16px;
            color: #ddd;
        }

        .user-email {
            margin: 0;
            font-size: 16px;
            color: #ddd;
        }

        .watchlist-section {
            background-color: #111;
            padding: 30px;
            color: white;
        }

        .watchlist-title {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .watchlist-grid {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .watchlist-item {
            width: 180px;
            border-radius: 10px;
        }

        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-wrapper .change-password-form-input {
            width: 100%;
            padding-right: 42px;
            background-color: rgba(84, 83, 83, 0.6);
            color: #fff;
            border: 1px solid #555;
        }

        .password-wrapper .change-password-form-input.is-invalid {
            background-image: none;
            padding-right: 42px;
        }

        .password-wrapper .change-password-form-input::placeholder {
            color: #aaa;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
            font-size: 15px;
            z-index: 10;
        }

        .toggle-password:hover {
            color: #fff;
        }

        .hint-badge {
            color: #aaa;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid #444;
            border-radius: 20px;
            padding: 2px 8px;
            transition: all 0.3s;
        }

        .hint-badge.valid {
            color: #4caf50;
            border-color: #4caf50;
            background: rgba(76, 175, 80, 0.1);
        }

        .change-password-form-input,
        .profile-page-btn,
        {
        width: 100%;
        max-width: 450px;
        height: 40px;
        margin: 12px 0;
        text-decoration: none
        }

        .change-password-form-input {
            background-color: rgba(84, 83, 83, 0.6);
            border: 1px solid #555;
            outline: none;
            color: #fff;
            font-size: 16px;
            padding: 16px 16px;
            margin-top: 10px
        }

        .change-password-form-input::placeholder {
            color: #aaaaaa !important;
            opacity: 0.8 !important;
        }

        .change-password-form-input::-webkit-input-placeholder {
            color: #aaaaaa !important;
            opacity: 0.8 !important;
        }

        .change-password-form-input::-moz-placeholder {
            color: #aaaaaa !important;
            opacity: 0.8 !important;
        }

        .change-password-form-input:-ms-input-placeholder {
            color: #aaaaaa !important;
            opacity: 0.8 !important;
        }

        .change-password-form-input::ms-input-placeholder {
            color: #aaaaaa !important;
            opacity: 0.8 !important;
        }

        .change-password-form-input[readonly] {
            background-color: #000;
            color: #888;
            cursor: not-allowed;
            border-color: #444;
        }

        .profile-page-btn {
            background-color: #FF3A1F;
            border: none;
            border-radius: 6px;
            outline: none;
            color: #fff;
            font-size: 17px;
            padding: 6px 20px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .user-banner-overlay {
                padding: 15px;
                justify-content: center;
                text-align: center;
            }

            .user-details-container {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .user-avatar {
                width: 50px;
                height: 50px;
                font-size: 27px;
            }

            .user-name {
                font-size: 18px;
            }

            .user-phone {
                font-size: 14px;
            }

            .user-email {
                font-size: 14px;
            }

            .watchlist-grid {
                justify-content: center;
            }

            .watchlist-item {
                width: 100%;
                max-width: 300px;
            }

            .profile-page-btn {
                font-size: 13px;
            }

            .change-password-form-input {
                font-size: 14px;
            }

            h3 {
                font-size: 18px;
                font-weight: 600
            }

            p {
                font-size: 15px
            }
        }

        .add-khlup-form-input {
            background-color: rgba(84, 83, 83, 0.6);
            border: none;
            border-radius: 6px;
            outline: none;
            color: #fff;
            font-size: 16px;
            padding: 10px 10px;
            margin-top: 10px
        }

        .add-khlup-form-input::placeholder {
            color: #FFFFFF;
            opacity: 0.5;
        }

        .khlup-options {
            position: absolute;
            top: 10px;
            /* Adjust as needed */
            right: 10px;
            /* Adjust as needed */
            z-index: 10;
            /* Ensure it's above other elements */
        }

        .three-dots-btn {
            background: none;
            border: none;
            color: white;
            /* Or whatever color fits your design */
            font-size: 20px;
            /* Adjust size as needed */
            cursor: pointer;
            padding: 5px;
        }

        .khlup-dropdown-menu {
            display: none;
            /* Hidden by default */
            position: absolute;
            /* Crucial for dropdown placement */
            background-color: #333;
            min-width: 120px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 100;
            /* Give it a high z-index */
            right: 0;
            /* Align to the right of the button */
            top: 30px;
            /* Adjust based on button height, place below it */
            border-radius: 5px;
            overflow: hidden;
        }

        .khlup-dropdown-menu.show {
            display: block;
            /* Show when the 'show' class is added by JavaScript */
        }

        .khlup-dropdown-menu .dropdown-item {
            color: white;
            /* Text color */
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .khlup-dropdown-menu .dropdown-item:hover {
            background-color: #555;
            /* Hover effect */
        }

        /* In your CSS file or within <style> tags */
        .progress-bar-custom-color {
            background-color: #FF3A1F !important;
            /* !important might be needed to override Bootstrap's default */
        }



        .card-header h3 {
            color: #ffffff;
            /* White text for header */
            font-size: 1.8rem;
            font-weight: 600;
        }

        .benefits-list {
            padding-left: 0;
            /* Remove default padding for ul */
            list-style: none;
            /* Remove bullet points */
        }

        .benefits-list li {
            padding: 8px 0;
            font-size: 1rem;
            color: #e0e0e0;
            /* Lighter white for list items */
            position: relative;
            padding-left: 25px;
            /* Space for the custom bullet */
        }

        .benefits-list li::before {
            content: "\2713";
            /* Checkmark icon */
            color: #4CAF50;
            /* Green color for checkmark */
            font-weight: bold;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
        }

        .price-details h3 {
            font-size: 3.5rem;
            /* Larger font for price */
            font-weight: 700;
            color: #FF3A1F;
            /* Primary blue for price, or a theme color */
        }

        .price-details h3 span {
            font-size: 1.2rem;
            /* Smaller font for "Month" */
            font-weight: 400;
            color: #e0e0e0;
        }

        .price-details .btn {
            margin-top: 15px;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 5px;
            /* Slightly rounded buttons */
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #FF3A1F;
            /* Primary blue */
            border-color: #FF3A1F;
        }

        .btn-primary:hover {
            background-color: #FF3A1F;
            border-color: #FF3A1F;
        }

        .btn-danger {
            background-color: #FF3A1F;
            /* Red for upgrade/deactivate */
            border-color: #FF3A1F;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #FF3A1F;
            border-color: #FF3A1F;
        }

        .btn-warning {
            background-color: #ffc107;
            /* Orange for expired plan upgrade */
            border-color: #ffc107;
            color: #212529;
            /* Dark text for warning button */
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        .btn-outline-light {
            color: #f8f9fa;
            border-color: #f8f9fa;
        }

        .btn-outline-light:hover {
            background-color: #FF3A1F;
            color: #f8f9fa;
            border-color: #FF3A1F;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.7) !important;
            /* Lighter white for descriptive text */
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .price-details {
                margin-top: 2rem;
                /* Add space between benefits and price on small screens */
            }

            .price-details h3 {
                font-size: 3rem;
            }

            .btn-lg {
                width: 100% !important;
                /* Full width buttons on small screens */
            }
        }

        /* Custom styles for the modal - adjust colors as needed */
        .modal-content {
            background-color: #212529 !important;
            /* Darker background for the modal content */
        }

        .modal-header .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
            /* Makes the close button white */
        }

        .nav-pills .nav-link {
            color: #f8f9fa;
            /* Text color for inactive tabs */
            background-color: transparent;
            /* No background for inactive tabs */
            transition: all 0.3s ease;
            /* Smooth transition */
        }

        .nav-pills .nav-link.active {
            background-color: #FF3A1F;
            /* Red background for active tab */
            color: #ffffff;
            /* White text for active tab */
            font-weight: bold;
        }

        .bg-dark-subtle {
            background-color: rgba(52, 58, 64, 0.5) !important;
            /* Slightly lighter dark for card background */
        }

        .card.border-primary {
            border-color: #dc3545 !important;
            /* Red border for cards */
        }

        .list-unstyled li {
            margin-bottom: 0.5rem;
            /* Space between list items */
            display: flex;
            /* For icon alignment */
            align-items: flex-start;
        }

        .list-unstyled li i {
            font-size: 1.1em;
            line-height: inherit;
            /* Align icon vertically with text */
        }

        /* Small adjustments for responsiveness if needed */
        @media (max-width: 767.98px) {
            .modal-body {
                padding: 1.5rem !important;
                /* Reduce padding on small screens */
            }
        }

        /* @keyframes spin {
                                                                                    0% {
                                                                                        transform: rotate(0deg);
                                                                                    }

                                                                                    100% {
                                                                                        transform: rotate(360deg);
                                                                                    }
                                                                                } */
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
@endsection
@section('main-section')
    <div id="loader-overlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 1000; text-align: center;">
        <div
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #fff; text-align: center;">
            <div class="spinner"
                style="width: 50px; height: 50px; margin:auto; border: 5px solid #f3f3f3; border-top: 5px solid #ff3a1f; border-radius: 50%; animation: spin 1s linear infinite;">
            </div>
            <p style="margin-top: 10px; font-size: 18px;">Please wait while processing your subscription... <br> Don't close
                the window</p>
        </div>
    </div>
    <section class="user-banner-section">
        <div class="user-banner-wrapper">
            <div class="user-banner-image-container">
                <img src="{{ url('/') }}/asset/images/profile-background-image.jpg" class="user-banner-image"
                    alt="">
            </div>
            <div class="user-banner-overlay">
                <div class="user-details-container p-5">
                    @php
                        $uName = trim(app('logged-in-user')->name ?? '');
                        $uEmail = trim(app('logged-in-user')->email ?? '');
                        $userInitial = 'G';
                        if ($uName !== '') {
                            $userInitial = strtoupper(mb_substr($uName, 0, 1));
                        } elseif ($uEmail !== '') {
                            $userInitial = strtoupper(mb_substr($uEmail, 0, 1));
                        }
                    @endphp
                    @if (app('logged-in-user')->profile_picture !== null)
                        <img src="{{ asset('storage/' . app('logged-in-user')->profile_picture) }}" alt="profile">
                    @else
                        <div class="user-avatar">
                            {{ $userInitial }}
                        </div>
                    @endif
                    <div class="user-info">
                        <h3 class="user-name">{{ !empty($uName) ? app('logged-in-user')->name : 'Guest' }}</h3>
                        <p class="user-phone">
                            {{-- {{ preg_replace('/(\d{2})(\d{1,4})(\d{0,4})/', '$1*****$3', app('logged-in-user')->mobile_no) }} --}}
                            {{ app('logged-in-user')->mobile_no }}
                        </p>
                        <p class="user-email">
                            {{ app('logged-in-user')->email }}</p>
                        <button class="profile-page-btn mt-3" data-bs-toggle="modal"
                            data-bs-target="#updateDataModal">Update Data</button>
                        @if (!empty(app('logged-in-user')->email) && !empty(app('logged-in-user')->password))
                            <button class="profile-page-btn mt-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                Change Password
                            </button>
                        @endif
                        <button type="button" class="profile-page-btn mt-3" onclick="if (typeof logoutUser === 'function') { logoutUser(event); } else { event.preventDefault(); document.getElementById('profile-logout-form').submit(); }">
                            Logout
                        </button>
                        <form id="profile-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
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
                                <button type="button"
                                    class="l-sub-head text-danger bg-transparent border-0 clear-all-home-btn"
                                    onclick="confirmClearAllHome()" style="cursor: pointer; font-size: 14px;">
                                    <i class="fa-regular fa-trash-can"></i> Clear All
                                </button>
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
                                                                style="width: {{ $item['progress_percentage'] }}%"></div>
                                                        </div>
                                                    </a>

                                                </div>
                                                <div class="alpha-card-large movie-card">
                                                    <img src="{{ $imageUrl }}" class="thumbnail lazyload"
                                                        onerror="this.onerror=null;this.src='{{ asset('asset/images/thumbnail-1.png') }}';"
                                                        alt="{{ $item['title'] ?? 'thumbnail' }}" loading="lazy">
                                                    <!-- Clear Item Button - Appears on hover like other buttons -->
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

                                                            @if ($item['series_name'])
                                                                <p class="mt-2 mb-1 text-white-50 small">
                                                                    {{ $item['series_name'] }}</p>
                                                                <h6 class="text-white mb-2">{{ $item['title'] }}</h6>
                                                            @else
                                                                <h6 class="text-white mt-3 mb-3">{{ $item['title'] }}</h6>
                                                            @endif

                                                            @if ($item['season'] && $item['episode_num'])
                                                                <ul class="animate__animated animate__fadeInUp mt-2">
                                                                    <li>Season {{ $item['season'] }}</li>
                                                                    <li>&#x2022;</li>
                                                                    <li>Episode {{ $item['episode_num'] }}</li>
                                                                </ul>
                                                            @endif

                                                            <div class="watch-progress-info mt-2">
                                                                <small class="text-white-50">
                                                                    {{ gmdate('H:i:s', $item['watch_time']) }} /
                                                                    {{ gmdate('H:i:s', $item['duration']) }}
                                                                    ({{ $item['progress_percentage'] }}%)
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

    {{-- Watch Later --}}
    @if (isset($watchLaterItems) && count($watchLaterItems) > 0)
        <section id="section-home-newdes" class="watchlist watch-later-section">
            <div class="home-newdes-section">
                <div class="home-newdes-section-container">
                    <div class="container-fluid">
                        <div class="homepage-heading d-flex justify-content-between align-items-center pe-3">
                            <span class="l-head">Watch Later</span>
                            <div class="d-flex gap-3 align-items-center">
                                <button type="button"
                                    class="l-sub-head text-danger bg-transparent border-0 clear-watchlist-btn"
                                    onclick="confirmClearAllWatchlist()" style="cursor: pointer; font-size: 14px;">
                                    <i class="fa-regular fa-trash-can"></i> Clear All
                                </button>
                                <a href="{{ route('watchlist.list') }}"
                                    class="l-sub-head text-white text-decoration-none">
                                    View All >
                                </a>
                            </div>
                        </div>
                        <div class="slider-container">
                            <div class="latest-release-slider owl-carousel owl-theme" id="watchLaterSlider">
                                @foreach ($watchLaterItems as $item)
                                    <div class="item">
                                        <div class="cards-container">
                                            <div class="alpha-card skeleton card-for-podcast">
                                                <div class="image-wrapper">
                                                    <a href="{{ $item['redirect_url'] }}">
                                                        <img class="lazyload" src="{{ $item['image_url'] }}"
                                                            alt="{{ $item['title'] }}" loading="lazy">
                                                    </a>
                                                </div>
                                                <div class="alpha-card-large {{ !empty($item['trailer_url']) ? 'movie-card' : '' }}"
                                                    id="movie-card">
                                                    <img src="{{ $item['image_url'] }}" class="thumbnail lazyload"
                                                        id="thumbnail" alt="{{ $item['title'] }}" loading="lazy">
                                                    @if (!empty($item['trailer_url']))
                                                        <video class="video-holder" loop muted id="video-holder">
                                                            <source src="{{ $item['trailer_url'] }}" type="video/mp4">
                                                        </video>
                                                    @endif
                                                    <div class="content-holder pt-5">
                                                        <div class="content">
                                                            <div class="button-holder row">
                                                                <div class="col-9">
                                                                    <a href="{{ $item['redirect_url'] }}">
                                                                        <button class="btn-watchnow" type="button">
                                                                            <i class="fa-solid fa-play"></i>&nbsp;&nbsp;
                                                                            Watch Now
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
                                                                <h6 class="text-white mb-2">{{ $item['title'] }}</h6>
                                                            @else
                                                                <h6 class="text-white mt-3 mb-3">{{ $item['title'] }}</h6>
                                                            @endif

                                                            <ul class="animate__animated animate__fadeInUp mt-2">
                                                                @if (!empty($item['release_date']))
                                                                    <li>{{ \Carbon\Carbon::parse($item['release_date'])->format('Y') }}</li>
                                                                    <li>&#x2022;</li>
                                                                @endif
                                                                @if ($item['type'] == 'episode')
                                                                    @if (!empty($item['duration']))
                                                                        <li>{{ MillisecondsToTime($item['duration']) }}</li>
                                                                        <li>&#x2022;</li>
                                                                    @endif
                                                                    @if (!empty($item['season']) && !empty($item['episode_num']))
                                                                        <li>S{{ $item['season'] }} E{{ $item['episode_num'] }}</li>
                                                                    @elseif (!empty($item['episode_num']))
                                                                        <li>Episode {{ $item['episode_num'] }}</li>
                                                                    @endif
                                                                @else
                                                                    @if (!empty($item['duration']))
                                                                        <li>{{ MillisecondsToTime($item['duration']) }}</li>
                                                                        <li>&#x2022;</li>
                                                                    @endif
                                                                    @if (!empty($item['language_name']))
                                                                        <li>{{ $item['language_name'] }}</li>
                                                                    @endif
                                                                @endif
                                                            </ul>

                                                            @if (!empty($item['description']))
                                                                <p class="mt-1 mb-2 animate__animated animate__fadeInUp small text-white-50">
                                                                    {{ Str::words(strip_tags($item['description']), 12, '...') }}
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
    {{-- <div class="container-fluid mt-4 p-3 ">
        <div class="card bg-dark p-3">
            <div class="card-body text-white clearfix">
                <button class="profile-page-btn mt-1 float-left" data-bs-toggle="modal" data-bs-target="#addYourKhulps">
                    <i class="fa fa-plus"></i> Add Your Khlup
                </button>
            </div>
        </div>
    </div> --}}
    {{--  @if (isset($khlup) && count($khlup) > 0)
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
                                        <div class="klup-card skeleton">
                                            <a href="{{ route('user.khlups.view', ['tokengen' => $item->id]) }}">
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
                                            </a>
                                            <div class="khlup-options">
                                                <button class="three-dots-btn">
                                                    <i class="fas fa-ellipsis-v"></i> </button>
                                                <div class="khlup-dropdown-menu">
                                                    <a href="#" id="delete_khlup" class="dropdown-item delete_khlup"
                                                        data-action="delete" data-id="{{ $item->id }}">Delete</a>
                                                    <a href="#" id="edit_khlup" class="dropdown-item edit_khlup"
                                                        data-action="edit" data-id="{{ $item->id }}">Edit</a>
                                                   <a href="#" class="dropdown-item" data-action="archive"
                                                        data-id="{{ $item->id }}">Archive</a> 
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
            </div>
            </div>
        </section>
    @endif --}}
    {{-- @if (app('logged-in-user')->type != 3)
        <div class="container-fluid mt-4 p-3">
            @if (session()->has('is_mobile_login'))
                No Profile
            @else
                <div id="ajax-alert" class="d-none mb-3"></div>
            @endif

            <div class="card bg-dark p-3">
                <div class="card-header text-white d-flex justify-content-between align-items-center">
                    <h3>Change Password</h3>
                    <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal"
                        data-bs-target="#changePasswordModal">
                        Change Password
                    </button>
                </div>
            </div>
        </div>

        @endif --}}


    <div class="container-fluid mt-4 p-3">
        <div class="card bg-dark text-white p-3 shadow-lg"> {{-- Added shadow for a slightly lifted effect --}}
            <div class="card-header pb-1 mb-1"> {{-- Added border for separation --}}
                <h3 class="mb-0">Subscription</h3>
            </div>
            <div class="card-body">
                @if (app('logged-in-user')->subscription === 'No')
                    <div class="text-center py-5">
                        <p class="lead mb-4">Get unlimited access to premium content with our best subscription plan — your
                            gateway to entertainment!</p>
                        <a href="#" onclick="openSubscriptionModal()" class="btn btn-primary btn-lg">Activate your
                            Subscription</a>
                    </div>
                @else
                    <div class="row align-items-center"> {{-- Added align-items-center for vertical alignment --}}
                        <div class="col-md-6 mb-4 mb-md-0"> {{-- Added margin-bottom for small screens --}}
                            <div class="price-card-content">
                                <ul class="list-unstyled benefits-list"> {{-- Using list-unstyled for no default list styling --}}
                                    @foreach ($activepack as $item)
                                        <li>
                                            In this package you will get
                                            @foreach ($typeData as $typeItem)
                                                {{ $typeItem->name }}@if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </li>
                                        <li>Watch On Tv & Laptop - @if ($item->package->watch_on_laptop_tv == 1)
                                                Yes
                                            @else
                                                No
                                            @endif
                                        </li>
                                        <li>Ad Free Movies and Show - @if ($item->package->watch_on_laptop_tv == 1)
                                                Yes
                                            @else
                                                No
                                            @endif
                                        </li>
                                        <li>{{ $item->package->video_qulity }} Video Quality</li>
                                        <li>{{ $item->package->no_of_device }} Device Login</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @foreach ($activepack as $item)
                                <div class="price-details text-center">
                                    <h3 class="display-4 mb-3">₹ {{ $item->amount }} /<span
                                            class="fs-5">{{ $item->type }}</span>
                                    </h3>
                                    <input type="hidden" name="price" value="{{ $item->amount }}" class="price"
                                        id="price">

                                    @php
                                        $currentDate = new DateTime();
                                        $expiryDate = new DateTime($item->expiry_date);
                                        $interval = $currentDate->diff($expiryDate);

                                        $days = (int) $interval->format('%r%a');
                                        $hours = (int) $interval->format('%h');
                                        $minutes = (int) $interval->format('%i');

                                        // Singular/plural formatting
                                        $dayText = $days === 1 ? '1 day' : "$days days";
                                        $hourText = $hours === 1 ? '1 hour' : "$hours hours";
                                        $minuteText = $minutes === 1 ? '1 minute' : "$minutes minutes";

                                        $fullTimeLeft = "$dayText, $hourText, $minuteText";
                                    @endphp

                                    @if ($days < 0)
                                        <p class="text-danger fs-5 mt-2">Your plan is expired. Please update the plan.</p>
                                        <button type="button" class="btn btn-warning btn-lg mt-3 w-75"
                                            data-bs-toggle="modal" data-bs-target="#subscriptionModal">Upgrade your
                                            plan</button>
                                    @else
                                        <p class="text-white-50 fs-5 mt-2">
                                            Your plan will expire in <strong
                                                class="text-white">{{ $fullTimeLeft }}</strong>!
                                        </p>

                                        @if ($item->amount > 15)
                                            <p class="mt-4 mb-4 text-white-50">Enjoy our entertainment platform. Your plan
                                                will auto-renew once it expires. You can cancel anytime.</p>
                                        @else
                                            <p class="mt-4 mb-4 text-white-50">Upgrade the plan at ₹ 149 per year and get
                                                more device login and best entertainment</p>
                                            <div class="mt-3">
                                                {{-- Flexbox container --}}
                                                <button type="button" class="btn btn-danger btn-lg flex-fill"
                                                    data-bs-toggle="modal" data-bs-target="#subscriptionModal">Upgrade your
                                                    plan</button>
                                            </div>
                                        @endif
                                        <button type="button" class="btn btn-outline-light btn-lg flex-fill"
                                            data-bs-toggle="modal" data-bs-target="#deActivate">Deactivate
                                            Subscription</button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="container-fluid mt-4 p-3">
        <div class="card bg-dark p-3">
            <div class="card-header text-white">
                <h3>Delete Account</h3>
            </div>
            <div class="card-body text-white col-md-9">
                <p> Are you sure you want to delete your account? This action cannot be undone.</p>
                <button type="submit" data-bs-toggle="modal" data-bs-target="#accountdeletmodal"
                    class="profile-page-btn mt-3 col-md-3">Delete Account</button>
            </div>
        </div>
    </div>
    <div class="modal fade" id="accountdeletmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content  bg-dark text-white">
                <div class="modal-header border-0 text-center">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5 bg-dark">
                    <h2>Account Deletion </h2>
                    <p>We're sorry to see you go!</p>
                    <p>Once you delete your account, all your data will be permanently removed immediately. If you have an
                        active subscription plan, it will be deactivated, and no refunds will be provided. Please read our
                        <a href=''>Refund Policy</a> for more details.
                    </p>
                    <form method="POST" action="{{ url('/profile/delete') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="profile-page-btn"
                            onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete Account') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateDataModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Update Data</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body bg-dark">

                    {{-- Success/Error Alert --}}
                    <div id="update-modal-alert" class="d-none mb-3"></div>

                    <form id="profileUpdateForm" method="POST" action="{{ url('/profile/update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Profile Picture Preview --}}
                        <div class="text-center mb-3">
                            <div id="profile-preview-wrapper" style="position: relative; display: inline-block;">

                                {{-- Default avatar (letter) — shown when no picture --}}
                                @php
                                    $uNameModal = trim($user->name ?? '');
                                    $uEmailModal = trim($user->email ?? '');
                                    $modalInitial = 'G';
                                    if ($uNameModal !== '') {
                                        $modalInitial = strtoupper(mb_substr($uNameModal, 0, 1));
                                    } elseif ($uEmailModal !== '') {
                                        $modalInitial = strtoupper(mb_substr($uEmailModal, 0, 1));
                                    }
                                @endphp
                                <div class="user-avatar" id="default-avatar"
                                    style="{{ $user->profile_picture ? 'display:none;' : '' }}">
                                    {{ $modalInitial }}
                                </div>

                                {{-- Profile image — always in DOM, hidden when no picture --}}
                                <img id="profile-preview"
                                    src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : '' }}"
                                    alt="Profile Picture"
                                    style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #555; {{ $user->profile_picture ? '' : 'display:none;' }}">

                                {{-- Remove button --}}
                                <button type="button" id="removeProfilePicture"
                                    class="{{ $user->profile_picture ? '' : 'd-none' }}"
                                    style="position: absolute; top: 0; right: 0; background: red; border: none; border-radius: 50%; width: 24px; height: 24px; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                            <p class="mt-1" style="font-size:11px; color:#aaa;">Click below to change picture</p>
                        </div>

                        {{-- Hidden flag to track removal --}}
                        <input type="hidden" name="remove_profile_picture" id="remove_profile_picture" value="0">
                        <input type="hidden" name="confirm_remove_email_and_password" id="confirm_remove_email_and_password" value="0">

                        {{-- Profile Picture Input --}}
                        <div class="password-wrapper mb-1">
                            <input id="profile_picture" type="file" class="form-control change-password-form-input"
                                accept="image/png, image/jpeg" name="profile_picture">
                        </div>
                        <div class="text-danger small mb-2 error-text d-none" id="error-profile_picture"></div>

                        {{-- Name --}}
                        <div class="password-wrapper mb-1">
                            <input id="name" type="text" class="form-control change-password-form-input"
                                name="name" placeholder="Enter Name" value="{{ old('name', ($user->name && trim($user->name) !== 'Guest') ? trim($user->name) : '') }}">
                        </div>
                        <div class="text-danger small mb-2 error-text d-none" id="error-name"></div>

                        {{-- Email --}}
                        <div class="password-wrapper mb-1">
                            <input id="email" type="email" class="form-control change-password-form-input"
                                name="email" placeholder="Enter Email Id" value="{{ old('email', $user->email) }}"
                                @if ($user->type == 5) readonly @endif>
                        </div>
                        <div class="text-danger small mb-2 error-text d-none" id="error-email"></div>

                        {{-- Mobile Number with Country Code --}}
                        @php
                            // Extract country code and number from stored format
                            $mobile = old('mobile_no', $user->mobile_no);
                            $countryCode = '+91'; // Default country code
                            $mobileNumber = '';

                            if ($mobile) {
                                // Remove any spaces and check if it starts with +
                                $mobile = preg_replace('/\s+/', '', $mobile);
                                if (strpos($mobile, '+') === 0) {
                                    // Extract country code (starts with + and has 1-3 digits after)
                                    if (preg_match('/^\+(\d{1,2})(.*)$/', $mobile, $matches)) {
                                        $countryCode = '+' . $matches[1];
                                        $mobileNumber = $matches[2];
                                    }
                                } else {
                                    $mobileNumber = $mobile;
                                }
                            }
                        @endphp

                        <div class="row g-0 mb-1">
                            <div class="col-4">
                                <div class="password-wrapper">
                                    <input type="text" class="form-control change-password-form-input"
                                        value="{{ $countryCode }}" readonly
                                        style="border-right: none; border-radius: 4px 0 0 4px; background-color: #2a2a2a; color: #aaa; cursor: not-allowed;"
                                        id="country_code_display">
                                    <input type="hidden" name="country_code" id="country_code_hidden"
                                        value="{{ $countryCode }}">
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="password-wrapper">
                                    <input id="mobile_no" type="text" class="form-control change-password-form-input"
                                        name="mobile_no" placeholder="Enter Mobile Number"
                                        value="{{ old('mobile_no', $mobileNumber) }}"
                                        @if ($user->type == 3) readonly @endif maxlength="10" minlength="10"
                                        style="border-left: none; border-radius: 0 4px 4px 0;">
                                </div>
                            </div>
                        </div>
                        <div class="text-danger small mb-2 error-text d-none" id="error-mobile_no"></div>

                        @if (empty($user->email) || empty($user->password))
                            <div class="password-wrapper mb-1">
                                <input id="password" type="password" class="form-control change-password-form-input"
                                    name="password" placeholder="Enter New Password">
                                <i class="fa fa-eye-slash toggle-password" data-target="password"></i>
                            </div>
                            <p class="mb-1 mt-1" style="font-size:11px; color:#aaa;">Strong Password Must Have:</p>
                            <div id="update-password-hints"
                                class="w-100 mt-1 mb-2 d-flex flex-wrap gap-2 justify-content-center"
                                style="font-size:11px;">
                                <span id="update-hint-length" class="hint-badge">✗ 8+ chars</span>
                                <span id="update-hint-upper" class="hint-badge">✗ A-Z</span>
                                <span id="update-hint-lower" class="hint-badge">✗ a-z</span>
                                <span id="update-hint-number" class="hint-badge">✗ 0-9</span>
                                <span id="update-hint-special" class="hint-badge">✗ @$!%*#?&</span>
                            </div>
                            <div class="text-danger small mb-2 error-text d-none" id="error-password"></div>

                            <div class="password-wrapper mb-1">
                                <input id="password_confirmation" type="password"
                                    class="form-control change-password-form-input" name="password_confirmation"
                                    placeholder="Confirm New Password">
                                <i class="fa fa-eye-slash toggle-password" data-target="password_confirmation"></i>
                            </div>
                            <div class="text-danger small mb-2 error-text d-none" id="error-password_confirmation"></div>
                        @endif

                        <button type="submit" class="profile-page-btn w-100 mt-3">
                            <span id="updateBtnText">Save Data</span>
                            <span id="updateBtnSpinner" class="spinner-border spinner-border-sm d-none"
                                role="status"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Change Password Modal --}}
    <div class="modal fade" id="changePasswordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    {{-- Alert inside modal --}}
                    <div id="modal-alert" class="d-none mb-3"></div>

                    <form id="changePasswordForm">
                        @csrf

                        {{-- Current Password --}}
                        <div class="password-wrapper mb-2">
                            <input id="current_password" type="password" data-password-field="true"
                                class="form-control change-password-form-input" name="current_password"
                                placeholder="Current Password" autocomplete="current_password" />
                            <i class="fa fa-eye-slash toggle-password" data-target="current_password"></i>
                        </div>
                        <div id="error-current_password" class="text-danger small mb-2 d-none"></div>

                        {{-- New Password --}}
                        <div class="password-wrapper mb-2">
                            <input id="new_password" type="password" data-password-field="true"
                                class="form-control change-password-form-input" name="password"
                                placeholder="New Password" autocomplete="new-password" />
                            <i class="fa fa-eye-slash toggle-password" data-target="new_password"></i>
                        </div>
                        <div id="error-password" class="text-danger small mb-2 d-none"></div>

                        {{-- Password Strength Hints --}}
                        <p class="mb-1 mt-1 text-center" style="font-size:11px; color:#aaa;">Strong Password Must
                            Have:</p>
                        <div id="password-hints" class="w-100 mt-1 mb-3 d-flex flex-wrap gap-2 justify-content-center"
                            style="font-size:11px;">
                            <span id="hint-length" class="hint-badge">✗ 8+ chars</span>
                            <span id="hint-upper" class="hint-badge">✗ A-Z</span>
                            <span id="hint-lower" class="hint-badge">✗ a-z</span>
                            <span id="hint-number" class="hint-badge">✗ 0-9</span>
                            <span id="hint-special" class="hint-badge">✗ @$!%*#?&</span>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="password-wrapper mb-2">
                            <input id="password_confirmation" type="password" data-password-field="true"
                                class="form-control change-password-form-input" name="password_confirmation"
                                placeholder="Confirm Password" autocomplete="password_confirmation" />
                            <i class="fa fa-eye-slash toggle-password" data-target="password_confirmation"></i>
                        </div>
                        <div id="error-password_confirmation" class="text-danger small mb-2 d-none"></div>

                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitChangePassword">
                        <span id="submitBtnText">Change Password</span>
                        <span id="submitBtnSpinner" class="spinner-border spinner-border-sm d-none"
                            role="status"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addYourKhulps" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0 text-center">
                    <h4 id="khlupModalTitle">Add Khlups Details</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body bg-dark">
                    <div id="khlupMessageContainer" class="mb-3"></div>
                    <form id="save_user_khlup" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="khlup_id" id="khlupId">
                        <div class="form-group mb-4">
                            <label for="title" class="text-white">Title</label>
                            <input id="title" type="text" class="form-control add-khlup-form-input"
                                name="title" placeholder="Enter Title" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control add-khlup-form-input" rows="3" id="description"
                                placeholder="Hello"></textarea>
                        </div>
                        <div class="form-group mb-4">
                            <label for="video">Video</label>
                            <input id="video" type="file" class="form-control add-khlup-form-input"
                                name="video" accept="video/*">
                            <div id="khlupProgressBarContainer" class="mt-3 mb-3 d-none">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated progress-bar-custom-color"
                                        role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                        aria-valuemax="100" id="khlupProgressBar">0%</div>
                                </div>
                            </div>
                            <div id="videoPreviewContainer" class="mt-3 d-none text-center">
                                <p id="videoPreviewLoading" class="text-white d-none">Loading video preview...</p>
                                <video id="videoPreview" controls width="40%" class="rounded"
                                    style="max-height: 200px; background-color: #000;"></video>
                            </div>
                        </div>
                        <div class="form-group mt-4 text-center">
                            <button type="submit" class="profile-page-btn addKhlupButton" id="addKhlupButton">Upload
                                Khlup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deActivate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0 text-center">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body bg-dark text-white text-center p-5">
                    <h2>Deactivate Subscription </h2>
                    <p>Once you deactivate your subscription will be canceled. For more information, please read our <a
                            href="{{ route('footer.refund') }}" style="color:#FF3A1F">Refund Policy</a>.</p>
                    <form action="{{ route('subscription.cancel') }}" method="POST" id="deactivateSubscriptionForm">
                        @csrf
                        <input type="hidden" name="subscription_id"
                            value="{{ auth()->user()->razorpay_subscription_id }}">
                        <button type="submit" class="profile-page-btn" data-bs-dismiss="modal" aria-label="Close">
                            Deactivate
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="loader-deactivate-overlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 1000; text-align: center;">
        <div
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #fff; text-align: center;">
            <div class="spinner"
                style="width: 50px; height: 50px; margin:auto; border: 5px solid #f3f3f3; border-top: 5px solid #ff3a1f; border-radius: 50%; animation: spin 1s linear infinite;">
            </div>
            <p style="margin-top: 10px; font-size: 18px;">Please wait while Deactivating your subscription... <br> Don't
                close the window</p>
        </div>
    </div>
@include('subscription-modal')
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>

{{-- Scripts --}}

<script>
    // Declare routes for JavaScript
    const removeWatchlistUrl = "{{ route('watchlist.remove') }}";
    const clearAllWatchlistUrl = "{{ route('watchlist.clear-all') }}";
    const clearContinueUrl = "{{ route('continue-watching.clear', '') }}/";
    const clearAllContinueUrl = "{{ route('continue-watching.clear-all') }}";
    const csrfToken = "{{ csrf_token() }}";

    // Show toast message
    function showToast(message, isError = false) {
        let toast = document.createElement('div');
        toast.className = 'toast-message';
        toast.style.backgroundColor = isError ? '#dc3545' : '#28a745';
        toast.innerHTML =
            '<i class="fa-solid fa-' +
            (isError ? 'circle-exclamation' : 'check-circle') +
            '"></i> ' + message;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Clear all watchlist items
    async function confirmClearAllWatchlist() {
        if (!confirm(
                '⚠️ Warning: This will remove ALL items from your Watchlist.\n\nAre you sure you want to continue?'
            )) {
            return;
        }

        let clearBtn = document.querySelector('.clear-watchlist-btn');
        let originalText = clearBtn ? clearBtn.innerHTML : '';
        if (clearBtn) {
            clearBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Clearing...';
            clearBtn.disabled = true;
        }

        try {
            const response = await fetch(clearAllWatchlistUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            });

            const data = await response.json();
            console.log('Clear all watchlist response:', data);

            if (data.success) {
                showToast(data.message || 'Watchlist cleared successfully');
                const wlSection = document.querySelector('.watch-later-section');
                if (wlSection) {
                    wlSection.remove();
                }
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast(data.message || 'Failed to clear watchlist', true);
                if (clearBtn) {
                    clearBtn.innerHTML = originalText;
                    clearBtn.disabled = false;
                }
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            showToast('Something went wrong', true);
            if (clearBtn) {
                clearBtn.innerHTML = originalText;
                clearBtn.disabled = false;
            }
        }
    }

    // Clear single continue watching item
    async function clearContinueItem(id, type, btn) {
        if (!confirm('Are you sure you want to remove this item from Continue Watching?')) {
            return;
        }

        // Show loading state on button
        let originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        btn.disabled = true;

        try {
            const response = await fetch(clearContinueUrl + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            console.log('Clear response:', data);

            if (data.success) {
                showToast(data.message);
                // Remove the card from UI with animation
                let card = btn.closest('.item');
                if (card) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        card.remove();
                        // Check if no items left
                        if (document.querySelectorAll('#continueWatchingSlider .item').length === 0) {
                            location.reload();
                        }
                    }, 300);
                } else {
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                showToast(data.message || 'Failed to remove item', true);
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            showToast('Item removed. Refreshing...', false);
            setTimeout(() => {
                location.reload();
            }, 1500);
        }
    }

    // Clear all continue watching items
    async function confirmClearAllHome() {
        if (!confirm(
                '⚠️ Warning: This will remove ALL items from Continue Watching. This action cannot be undone.\n\nAre you sure you want to continue?'
            )) {
            return;
        }

        let clearBtn = document.querySelector('.clear-all-home-btn') || document.querySelector('.clear-all-btn');
        let originalText = clearBtn ? clearBtn.innerHTML : '';
        if (clearBtn) {
            clearBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Clearing...';
            clearBtn.disabled = true;
        }

        try {
            const response = await fetch(clearAllContinueUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            console.log('Clear all continue watching response:', data);

            if (data.success) {
                showToast(data.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast(data.message || 'Failed to clear items', true);
                if (clearBtn) {
                    clearBtn.innerHTML = originalText;
                    clearBtn.disabled = false;
                }
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            showToast('Clearing items. Refreshing...', false);
            setTimeout(() => {
                location.reload();
            }, 1500);
        }
    }

    // Export aliases for compatibility
    window.confirmClearAllHome = confirmClearAllHome;
    window.confirmClearAll = confirmClearAllHome;
</script>
<script>
    $(document).ready(function() {
        // ---- Eye Toggle ----
        $(document).on('click', '.toggle-password', function() {
            const targetId = $(this).data('target');
            const input = $('#' + targetId);
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                input.attr('type', 'password');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });

        // ---- Password Strength Hints ----
        $(document).on('input', '#new_password', function() {
            const val = $(this).val();

            function check(id, condition) {
                const el = $('#' + id);
                const text = el.text().replace(/^[✓✗]\s*/, '');
                el.text((condition ? '✓ ' : '✗ ') + text);
                el.toggleClass('valid', condition);
            }

            check('hint-length', val.length >= 8);
            check('hint-upper', /[A-Z]/.test(val));
            check('hint-lower', /[a-z]/.test(val));
            check('hint-number', /[0-9]/.test(val));
            check('hint-special', /[@$!%*#?&]/.test(val));
        });
        $(document).on('input', '#password', function() {
            const val = $(this).val();

            function check(id, condition) {
                const el = $('#' + id);
                const text = el.text().replace(/^[✓✗]\s*/, '');
                el.text((condition ? '✓ ' : '✗ ') + text);
                el.toggleClass('valid', condition);
            }

            check('update-hint-length', val.length >= 8);
            check('update-hint-upper', /[A-Z]/.test(val));
            check('update-hint-lower', /[a-z]/.test(val));
            check('update-hint-number', /[0-9]/.test(val));
            check('update-hint-special', /[@$!%*#?&]/.test(val));
        });
        // ---- AJAX Submit ----
        $('#submitChangePassword').on('click', function() {
            // Clear previous errors
            $('[id^="error-"]').addClass('d-none').text('');
            $('.change-password-form-input').removeClass('is-invalid');
            $('#modal-alert').addClass('d-none').html('');

            var currentPass = $('#current_password').val();
            var newPass = $('#new_password').val();

            if (currentPass && newPass && currentPass === newPass) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Same Password',
                    text: 'New password cannot be the same as your current password. Please choose a different password.',
                    background: '#1a1a1a',
                    color: '#ffffff',
                    confirmButtonColor: '#FF3A1F',
                    confirmButtonText: 'OK'
                });
                $('#new_password').addClass('is-invalid');
                $('#error-password').removeClass('d-none').text('New password cannot be the same as current password.');
                return;
            }

            // Show spinner
            $('#submitBtnText').text('Updating...');
            $('#submitBtnSpinner').removeClass('d-none');
            $('#submitChangePassword').prop('disabled', true);

            $.ajax({
                url: '{{ url('/profile/password') }}',
                method: 'POST',
                data: {
                    _token: $('input[name="_token"]').val(),
                    _method: 'PUT',
                    current_password: $('#current_password').val(),
                    password: $('#new_password').val(),
                    password_confirmation: $('#password_confirmation').val(),
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Updated',
                        text: response.message || 'Your password has been changed successfully.',
                        background: '#1a1a1a',
                        color: '#ffffff',
                        confirmButtonColor: '#FF3A1F',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#changePasswordModal').modal('hide');
                        location.reload();
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        if (xhr.responseJSON && xhr.responseJSON.same_password) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Same Password',
                                text: xhr.responseJSON.message || 'New password cannot be the same as your current password.',
                                background: '#1a1a1a',
                                color: '#ffffff',
                                confirmButtonColor: '#FF3A1F',
                                confirmButtonText: 'OK'
                            });
                        }
                        const errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                        if (errors) {
                            // Show each field error
                            $.each(errors, function(field, messages) {
                                $('#error-' + field).removeClass('d-none').text(messages[0]);
                                $('#' + field).addClass('is-invalid');
                                // handle new_password field mapped to password
                                if (field === 'password') {
                                    $('#new_password').addClass('is-invalid');
                                    $('#error-password').removeClass('d-none').text(messages[0]);
                                }
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again.',
                            background: '#1a1a1a',
                            color: '#ffffff',
                            confirmButtonColor: '#FF3A1F',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                complete: function() {
                    $('#submitBtnText').text('Change Password');
                    $('#submitBtnSpinner').addClass('d-none');
                    $('#submitChangePassword').prop('disabled', false);
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Mobile number validation - only digits and exactly 10 digits
        $('#mobile_no').on('input', function(e) {
            // Remove any non-digit characters
            this.value = this.value.replace(/\D/g, '');

            // Limit to 10 digits
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }

            // Show/hide error message
            const errorDiv = document.getElementById('error-mobile_no');
            if (this.value.length > 0 && this.value.length !== 10) {
                errorDiv.textContent = 'Mobile number must be exactly 10 digits';
                errorDiv.classList.remove('d-none');
                this.style.borderColor = '#dc3545';
            } else if (this.value.length === 10) {
                errorDiv.classList.add('d-none');
                this.style.borderColor = '#28a745';
            } else {
                errorDiv.classList.add('d-none');
                this.style.borderColor = '';
            }
        });

        // Profile picture validation
        $('#profile_picture').on('change', function() {
            const file = this.files[0];
            const errorDiv = $('#error-profile_picture');

            if (file) {
                const validTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];
                const maxSize = 5 * 1024 * 1024; // 5MB

                if (!validTypes.includes(file.type)) {
                    errorDiv.text('Only image files (PNG, JPEG, GIF, WEBP) are allowed').removeClass(
                        'd-none');
                    this.value = '';
                    showModalAlert('Please select only image files (PNG, JPEG, GIF, WEBP)', 'danger');
                    resetProfilePreview();
                    return;
                }

                if (file.size > maxSize) {
                    errorDiv.text('Image size should be less than 5MB').removeClass('d-none');
                    this.value = '';
                    showModalAlert('Image size should be less than 5MB', 'danger');
                    resetProfilePreview();
                    return;
                }

                errorDiv.addClass('d-none');
                $('#update-modal-alert').addClass('d-none');

                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profile-preview').attr('src', e.target.result).show();
                    $('#default-avatar').hide();
                    $('#removeProfilePicture').removeClass('d-none');
                    $('#remove_profile_picture').val('0');
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        $('#profileUpdateForm').on('submit', function(e) {
            e.preventDefault();

            // Validate mobile number before submission
            const mobileInput = $('#mobile_no');
            const mobileValue = mobileInput.val().trim();
            const errorDiv = $('#error-mobile_no');

            if (mobileValue.length > 0 && mobileValue.length !== 10) {
                errorDiv.text('Mobile number must be exactly 10 digits').removeClass('d-none');
                mobileInput.addClass('is-invalid');
                mobileInput.focus();
                return false;
            }

            const form = this;
            const formData = new FormData(form);

            // Clear previous errors
            $('#updateDataModal').find('.error-text').addClass('d-none').text('');
            $('#updateDataModal').find('.form-control').removeClass('is-invalid');
            $('#update-modal-alert').addClass('d-none').html('');

            // Show spinner
            $('#updateBtnText').text('Saving...');
            $('#updateBtnSpinner').removeClass('d-none');
            $('button[type="submit"]', form).prop('disabled', true);

            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Profile details updated successfully.',
                            background: '#1a1a1a',
                            color: '#ffffff',
                            confirmButtonColor: '#FF3A1F',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#updateDataModal').modal('hide');
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        if (xhr.responseJSON && xhr.responseJSON.requires_email_remove_confirmation) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Remove Email & Password?',
                                text: xhr.responseJSON.message || 'If you keep your email blank, your password will also be removed and you will become an OTP-only login user.',
                                background: '#1a1a1a',
                                color: '#ffffff',
                                showCancelButton: true,
                                confirmButtonColor: '#FF3A1F',
                                cancelButtonColor: '#555555',
                                confirmButtonText: 'Yes, Remove Email & Password',
                                cancelButtonText: 'Keep Email & Password'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#confirm_remove_email_and_password').val('1');
                                    $('#profileUpdateForm').submit();
                                } else {
                                    $('#email').val(`{{ $user->email }}`);
                                }
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.email_required_for_password) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Email Required for Password',
                                text: xhr.responseJSON.message || 'To set up a password, an email address is required. Please add your email ID first.',
                                background: '#1a1a1a',
                                color: '#ffffff',
                                confirmButtonColor: '#FF3A1F',
                                confirmButtonText: 'Got It'
                            }).then(() => {
                                $('#email').focus();
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.password_required_for_email) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Password Required',
                                text: xhr.responseJSON.message || 'If you are adding an email address, setting a password is also required.',
                                background: '#1a1a1a',
                                color: '#ffffff',
                                confirmButtonColor: '#FF3A1F',
                                confirmButtonText: 'Create Password'
                            }).then(() => {
                                $('#password').focus();
                            });
                        } else {
                            let errorMessages = [];
                            const errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                            if (errors) {
                                $.each(errors, function(key, value) {
                                    $('#error-' + key).removeClass('d-none').text(value[0]);
                                    $('[name="' + key + '"]').addClass('is-invalid');
                                    errorMessages.push(value[0]);
                                });
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Please Check Required Fields',
                                html: errorMessages.length > 0 ? errorMessages.join('<br>') : 'Please check your details and try again.',
                                background: '#1a1a1a',
                                color: '#ffffff',
                                confirmButtonColor: '#FF3A1F',
                                confirmButtonText: 'OK'
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again.',
                            background: '#1a1a1a',
                            color: '#ffffff',
                            confirmButtonColor: '#FF3A1F',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                complete: function() {
                    $('#updateBtnText').text('Save Data');
                    $('#updateBtnSpinner').addClass('d-none');
                    $('button[type="submit"]', form).prop('disabled', false);
                }
            });
        });

        // Prompt user if trying to enter a password without an email address
        let alertShownForPasswordEmail = false;
        $(document).on('focus input', '#password', function() {
            let emailVal = $('#email').val() ? $('#email').val().trim() : '';
            if (!emailVal && !alertShownForPasswordEmail) {
                alertShownForPasswordEmail = true;
                Swal.fire({
                    icon: 'info',
                    title: 'Email Required for Password 😊',
                    text: 'To set up a password, an email address is required 😊. Since you logged in with OTP, please add your email ID first. Once added, you will be able to log in using both OTP and your email & password!',
                    background: '#1a1a1a',
                    color: '#ffffff',
                    confirmButtonColor: '#FF3A1F',
                    confirmButtonText: 'Understood 😊'
                }).then(() => {
                    $('#email').focus();
                    setTimeout(() => { alertShownForPasswordEmail = false; }, 3000);
                });
            }
        });

        // ---- Reset updateDataModal on close ----
        $('#updateDataModal').on('hidden.bs.modal', function() {
            const modal = $(this);
            modal.find('.error-text').addClass('d-none').text('');
            modal.find('.form-control').removeClass('is-invalid');
            $('#update-modal-alert').addClass('d-none').html('');
        });

        // ---- Profile picture live preview ----
        $(document).on('change', '#profile_picture', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profile-preview').attr('src', e.target.result).show();
                    $('#default-avatar').hide();
                    $('#removeProfilePicture').removeClass('d-none');
                    $('#remove_profile_picture').val('0');
                };
                reader.readAsDataURL(file);
            }
        });

        // ---- Remove profile picture ----
        $(document).on('click', '#removeProfilePicture', function() {
            $('#profile-preview').attr('src', '').hide();
            $('#default-avatar').show();
            $('#profile_picture').val('');
            $('#remove_profile_picture').val('1');
            $(this).addClass('d-none');
        });

        // Helper functions
        function showModalAlert(message, type) {
            const alertDiv = $('#update-modal-alert');
            alertDiv.removeClass('d-none alert-success alert-danger')
                .addClass('alert alert-' + type)
                .html(message);
        }

        function resetProfilePreview() {
            $('#profile-preview').attr('src', '').hide();
            $('#default-avatar').show();
            $('#removeProfilePicture').addClass('d-none');
            $('#remove_profile_picture').val('1');
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deactivateForm = document.getElementById('deactivateSubscriptionForm');
        const loaderOverlay = document.getElementById('loader-deactivate-overlay');
        const deactivateModal = new bootstrap.Modal(document.getElementById('deActivate'), {
            backdrop: 'static',
            keyboard: false
        }); // Re-initialize the Bootstrap modal object if you want to explicitly control it.

        deactivateForm.addEventListener('submit', function(event) {
            loaderOverlay.style.display = 'block';
            deactivateModal.hide();
        });
    });
</script>
<script>
    $(function() {
        $('.three-dots-btn').on('click', function(event) {
            event.stopPropagation();
            event.preventDefault();
            var $khlupOptions = $(this).closest('.khlup-options');
            var $dropdown = $khlupOptions.find('.khlup-dropdown-menu');
            $('.khlup-dropdown-menu').not($dropdown).removeClass('show');
            $dropdown.toggleClass('show');
        });
        // Close dropdown when clicking outside
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.khlup-options').length) {
                $('.khlup-dropdown-menu').removeClass('show');
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        const $khlupModal = $('#addYourKhulps');
        const $khlupModalTitle = $('#khlupModalTitle');
        const $khlupMessageContainer = $('#khlupMessageContainer');
        const $saveKhlupForm = $('#save_user_khlup');
        const $khlupIdInput = $('#khlupId');
        const $titleInput = $('#title');
        const $descriptionInput = $('#description');
        const $videoInput = $('#video');
        const $addKhlupButton = $('#addKhlupButton');
        // --- Upload Progress Bar Elements ---
        const $khlupProgressBarContainer = $('#khlupProgressBarContainer');
        const $khlupProgressBar = $('#khlupProgressBar');
        // --- Video Preview Elements ---
        const $videoPreviewContainer = $('#videoPreviewContainer');
        const $videoPreviewLoading = $('#videoPreviewLoading');
        const $videoPreview = $('#videoPreview');
        // --- Function to reset the modal form and state ---
        function resetKhlupModal() {
            $khlupMessageContainer.empty().removeClass('alert alert-success alert-danger');
            $saveKhlupForm[0].reset(); // Resets all form fields (including title/description)
            $khlupIdInput.val(''); // Clear the hidden ID
            // Reset and hide upload progress bar
            $khlupProgressBarContainer.addClass('d-none');
            $khlupProgressBar.css('width', '0%').text('0%').attr('aria-valuenow', 0);
            // Reset and hide video preview
            $videoPreviewContainer.addClass('d-none');
            $videoPreviewLoading.addClass('d-none');
            $videoPreview.attr('src', '').addClass('d-none');
            $videoInput.val(''); // Clear the file input for security/fresh upload (important for re-opening)
        }
        // --- Form Submission Logic (Unified for Add/Edit) ---
        $saveKhlupForm.submit(function(e) {
            e.preventDefault(); // Prevent default form submission
            const formData = new FormData(this); // 'this' refers to the form element
            let url = `{{ url('/khlup-save') }}`; // Default URL for adding (POST)
            let methodOverride = ''; // For Laravel's _method spoofing
            // Check if it's an update operation
            const khlupId = $khlupIdInput.val();
            if (khlupId) {
                // For updates, Laravel typically expects PUT/PATCH.
                // We'll append _method to spoof it since FormData sends POST.
                methodOverride = 'POST'; // or 'PATCH'
                formData.append('_method', methodOverride);
                url = `/khlup-update/${khlupId}`; // Adjust your update endpoint in Laravel
            }
            // Clear previous message and enable loading state
            $khlupMessageContainer.empty().removeClass('alert alert-success alert-danger');
            $addKhlupButton.prop('disabled', true)
                .css({
                    "background-color": "#6c757d",
                    "border-color": "#6c757d",
                    "cursor": "auto"
                })
                .text(khlupId ? 'Updating...' : 'Uploading...'); // Dynamic button text
            // Show global upload progress bar container
            $khlupProgressBarContainer.removeClass('d-none');
            $.ajax({
                type: 'POST', // Always POST for FormData with _method spoofing
                url: url,
                data: formData,
                contentType: false,
                processData: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.onprogress = function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = (evt.loaded / evt.total) * 100;
                            $khlupProgressBar.css('width', percentComplete + '%')
                                .text(Math.round(percentComplete) + '%')
                                .attr('aria-valuenow', percentComplete);
                        }
                    };
                    return xhr;
                },
                success: function(resp) {
                    console.log('Response:', resp);
                    if (resp.status === 'success') {
                        $khlupMessageContainer.html('<div class="alert alert-success">' +
                            resp.message + '</div>');
                        // Close modal and reload page after a short delay
                        setTimeout(() => {
                            $khlupModal.modal('hide');
                            location.reload();
                        }, 2000);
                    } else {
                        $khlupMessageContainer.html('<div class="alert alert-danger">' +
                            resp.message + '</div>');
                        resetSubmitButton(); // Re-enable button on client-side error
                    }
                    $khlupProgressBarContainer.addClass(
                        'd-none'); // Hide upload progress bar
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr);
                    let errorMessage = 'An unknown error occurred.';
                    if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON?.errors) {
                        errorMessage = '<ul>';
                        $.each(xhr.responseJSON.errors, function(key, messages) {
                            messages.forEach(msg => errorMessage += '<li>' + msg +
                                '</li>');
                        });
                        errorMessage += '</ul>';
                    }
                    $khlupMessageContainer.html('<div class="alert alert-danger">' +
                        errorMessage + '</div>');
                    resetSubmitButton(); // Re-enable button on AJAX error
                    $khlupProgressBarContainer.addClass(
                        'd-none'); // Hide upload progress bar
                }
            });
        });
        // Function to reset the submit button's state
        function resetSubmitButton() {
            const currentKhlupId = $khlupIdInput.val();
            const originalText = currentKhlupId ? 'Update Khlup' : 'Upload Khlup';
            $addKhlupButton.prop('disabled', false)
                .css({
                    "background-color": "",
                    "border-color": "",
                    "cursor": ""
                })
                .text(originalText);
        }
        // --- Video Preview Logic (for input file change) ---
        $videoInput.on('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.type.startsWith('video/')) {
                    $videoPreviewContainer.removeClass('d-none');
                    $videoPreviewLoading.removeClass('d-none');
                    $videoPreview.attr('src', '').addClass(
                        'd-none'); // Clear previous src and hide video
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $videoPreview.attr('src', e.target.result);
                        $videoPreview.removeClass('d-none');
                        $videoPreviewLoading.addClass('d-none');
                    };
                    reader.onerror = function() {
                        $videoPreviewLoading.text('Error loading video preview.');
                        $videoPreview.addClass('d-none');
                    };
                    reader.readAsDataURL(file); // Read the file locally
                } else {
                    // Not a video file
                    $videoPreviewContainer.addClass('d-none');
                    $videoPreviewLoading.addClass('d-none');
                    $videoPreview.attr('src', '').addClass('d-none');
                    alert('Please select a valid video file.');
                    $(this).val(''); // Clear the file input
                }
            } else {
                // No file selected, hide preview area
                $videoPreviewContainer.addClass('d-none');
                $videoPreviewLoading.addClass('d-none');
                $videoPreview.attr('src', '').addClass('d-none');
            }
        });
        //get edit Add New Khlup button is clicked.
        window.openEditKhlupModal = function(khlup) {
            resetKhlupModal(); // Always reset first
            // Set modal title and button text for Edit mode
            $khlupModalTitle.text('Edit Khlup Details');
            $addKhlupButton.text('Update Khlup');
            // Populate form fields with existing data
            $khlupIdInput.val(khlup.id);
            $titleInput.val(khlup.name);
            $descriptionInput.val(khlup.description);
            // Display existing video preview if video_url is available
            if (khlup.landscape_url) {
                $videoPreviewContainer.removeClass('d-none');
                $videoPreviewLoading.addClass('d-none'); // No loading message for direct URL
                $videoPreview.attr('src', khlup.landscape_url).removeClass('d-none');
                $videoPreview[0].load(); // Force video element to load the new source
            } else {
                // If no video_url exists for the khlup, ensure preview is hidden
                $videoPreviewContainer.addClass('d-none');
            }
            $khlupModal.modal('show'); // Show the modal
        };
        // --- Public Function: Open Modal for Adding ---
        // Add New Khlup button is clicked.
        window.openAddKhlupModal = function() {
            resetKhlupModal(); // Always reset first
            // Set modal title and button text for Add mode
            $khlupModalTitle.text('Add Khlups Details');
            $addKhlupButton.text('Upload Khlup');
            $khlupModal.modal('show'); // Show the modal
        };
        // --- Event Listener for Edit Button (from dropdown) ---
        // This listens for clicks on any element with class 'edit_khlup' (e.g., from your)
        $(document).on('click', '.edit_khlup', function(e) {
            e.preventDefault();
            const khlupId = $(this).data('id');
            console.log('Edit button clicked for Khlup ID:', khlupId);
            $khlupMessageContainer.empty().html(
                '<div class="alert alert-info">Loading khlup details...</div>');
            $addKhlupButton.prop('disabled', true); // Disable button while fetching data
            // Make an AJAX request to fetch the specific Khlup's details
            // IMPORTANT: Ensure this route exists in your Laravel backend and returns JSON!
            // Example: Route::get('/api/khlups/{khlup}', [KhlupController::class, 'show']);
            $.ajax({
                url: `/khlup-get/${khlupId}`, // Adjust this API endpoint to fetch a single khlup
                type: 'GET',
                success: function(response) {
                    console.log('Khlup details fetched:', response);
                    if (response.status === 'success' && response.khlup) {
                        openEditKhlupModal(response.khlup); // Open modal with fetched data
                        $khlupMessageContainer.empty(); // Clear loading message
                    } else {
                        $khlupMessageContainer.html('<div class="alert alert-danger">' + (
                                response.message || 'Failed to load khlup details.') +
                            '</div>');
                    }
                    $addKhlupButton.prop('disabled',
                        false); // Re-enable button regardless of success
                },
                error: function(xhr) {
                    console.error('Error fetching khlup details:', xhr);
                    let errorMessage = 'An error occurred while fetching khlup details.';
                    if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    $khlupMessageContainer.html('<div class="alert alert-danger">' +
                        errorMessage + '</div>');
                    $addKhlupButton.prop('disabled', false); // Re-enable button
                }
            });
        });
        // --- Optional: Handle modal close event to reset form state ---
        $khlupModal.on('hidden.bs.modal', function() {
            resetKhlupModal();
        });
        $(".delete_khlup").click(function(e) {
            e.preventDefault();
            if (confirm("Are you sure you want to Delete?")) {
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    url: `{{ url('/khlup-delete') }}`,
                    data: {
                        id: id,
                        _token: `{{ csrf_token() }}`
                    },
                    success: function(resp) {
                        console.log('Response:', resp);
                        if (resp.status === 'success') {
                            window.location.reload();
                        } else {
                            alert(resp.message);
                        }
                    }
                });
            }
        });
    });
</script>
