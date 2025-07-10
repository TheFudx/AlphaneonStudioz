@extends('layouts.main')
@section('title')
    Alphastudioz | Profile
@endsection
@section('styles')
    {{-- <link rel="stylesheet" href="{{url('/')}}/asset/css/login.css"> --}}
    <style>
         /* Hide page loader elements */
        #page-loader {
            display: none;
        }
        #page-loader img {
            display: none;
        }
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
            border: none;
            border-radius: 6px;
            outline: none;
            color: #fff;
            font-size: 16px;
            padding: 16px 16px;
            margin-top: 10px
        }

        .change-password-form-input::placeholder {
            color: #FFFFFF;
            opacity: 0.5;
        }

        .change-password-form-input::-webkit-input-placeholder {
            color: #FFFFFF;
        }

        .change-password-form-input::-moz-placeholder {
            color: #FFFFFF;
            opacity: 0.5;
        }

        .change-password-form-input:-ms-input-placeholder {
            color: #FFFFFF;
        }

        .change-password-form-input::ms-input-placeholder {
            color: #FFFFFF;
        }

        .profile-page-btn {
            background-color: #FF3A1F;
            border: none;
            border-radius: 6px;
            outline: none;
            color: #fff;
            font-size: 17px;
            padding: 12px 20px;
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
    </style>
@endsection
@section('main-section')
<div id="loader-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 1000; text-align: center;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #fff; text-align: center;">
        <div class="spinner" style="width: 50px; height: 50px; margin:auto; border: 5px solid #f3f3f3; border-top: 5px solid #ff3a1f; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        <p style="margin-top: 10px; font-size: 18px;">Please wait while processing your subscription... <br> Don't close the window</p>
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
                    @if (app('logged-in-user')->profile_picture !== null)
                        <img src="{{ asset('storage/profile_pictures/' . app('logged-in-user')->profile_picture) }}"
                            alt="profile">
                    @else
                        <div class="user-avatar">
                            {{ app('logged-in-user')->email ? strtoupper(substr(app('logged-in-user')->email, 0, 1)) : strtoupper(substr(app('logged-in-user')->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="user-info">
                        <h3 class="user-name">{{ app('logged-in-user')->name }}</h3>
                        <p class="user-phone">
                            {{ preg_replace('/(\d{2})(\d{1,4})(\d{0,4})/', '$1*****$3', app('logged-in-user')->mobile_no) }}
                        </p>
                        <p class="user-email">
                            {{ app('logged-in-user')->email }}</p>
                        <button type="submit" class="profile-page-btn mt-3" data-bs-toggle="modal"
                            data-bs-target="#updateDataModal">Update Data</button>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- <div class="container-fluid mt-4 p-3 ">
        <div class="card bg-dark p-3">
            <div class="card-body text-white clearfix">
                <button class="profile-page-btn mt-1 float-left" data-bs-toggle="modal" data-bs-target="#addYourKhulps">
                    <i class="fa fa-plus"></i> Add Your Khlup
                </button>
            </div>
        </div>
    </div> --}}
    @if (isset($khlup) && count($khlup) > 0)
        <section id="section-home-newdes" class="khulps">
            <div class="home-newdes-section">
                <div class="home-newdes-section-container">
                    <div class="container-fluid">
                        <div class="homepage-heading d-flex justify-content-between align-items-center pe-3">
                            <span class="l-head">Khlups</span>
                            {{-- <a href="#" class="l-sub-head text-white text-decoration-none">
                                View All
                                >
                            </a> --}}
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
                                                    {{-- <a href="#" class="dropdown-item" data-action="archive"
                                                        data-id="{{ $item->id }}">Archive</a> --}}
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
    @endif
    @if (app('logged-in-user')->type != 3)
        <div class="container-fluid mt-4 p-3">
            @if (session()->has('is_mobile_login'))
                No Profile
            @else
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
            @endif
            <div class="card bg-dark p-3">
                <div class="card-header text-white">
                    <h3>Change Password</h3>
                </div>
                <div class="card-body text-white col-md-9">
                    <form method="POST" action="{{ url('/profile/password') }}" class="w-100 d-flex flex-column">
                        @csrf
                        @method('PUT')
                        <input id="current_password" type="password"
                            class="form-control change-password-form-input @error('current_password') is-invalid @enderror"
                            name="current_password" value="{{ old('current_password') }}" placeholder="Current Password"
                            autocomplete="current_password" />
                        @error('current_password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <input id="password" type="password"
                            class="form-control change-password-form-input @error('password') is-invalid @enderror"
                            name="password" value="{{ old('password') }}" placeholder="New Password"
                            autocomplete="password" />
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <input id="password_confirmation" type="password"
                            class="form-control change-password-form-input @error('password_confirmation') is-invalid @enderror"
                            name="password_confirmation" value="{{ old('password_confirmation') }}"
                            placeholder="Confirm Password" autocomplete="password_confirmation" />
                        @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        {{-- <button type="submit" class="login-btn-continue">Continue With Email</button> --}}
                        <button type="submit" class="profile-page-btn mt-3 col-md-3">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
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
                        <a href="{{ route('subscribe') }}" class="btn btn-primary btn-lg">Activate your Subscription</a>
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
                                    <h3 class="display-4 mb-3">₹ {{ $item->amount }} /<span class="fs-5">{{$item->type}}</span>
                                    </h3>
                                    <input type="hidden" name="price" value="{{ $item->amount }}" class="price"
                                        id="price">

                                    @php
                                        $currentDate = new DateTime();
                                        $currentDate->modify('-1 day');
                                        $expiryDate = new DateTime($item->expiry_date);
                                        $interval = $currentDate->diff($expiryDate);
                                        $daysLeft = (int) $interval->format('%r%a');

                                        if ($daysLeft <= 0) {
                                            // This logic for updating DB should ideally be in a controller or service,
                                            // not directly in the Blade view. For the sake of replicating the provided code,
                                            // it's here, but it's not best practice.
                                            \DB::table('users')
                                                ->where('id', $item->user_id)
                                                ->update(['subscription' => 'No']);
                                            \DB::table('transaction')
                                                ->where('user_id', $item->user_id)
                                                ->update(['status' => '0']);
                                        }
                                    @endphp

                                    @if ($daysLeft <= 0)
                                        <p class="text-danger fs-5 mt-2">Your plan is expired. Please update the plan.</p>
                                        <button type="button" class="btn btn-warning btn-lg mt-3 w-75"
                                            data-bs-toggle="modal" data-bs-target="#subscribe-modal">Upgrade your
                                            plan</button>
                                    @else
                                        <p class="text-white-50 fs-5 mt-2">Your plan will expire in <strong
                                                class="text-white">{{ $daysLeft }}</strong> days!</p>

                                        @if ($item->amount > 15)
                                            <p class="mt-4 mb-4 text-white-50">Enjoy our entertainment platform. Your plan
                                                will auto-renew once it expires. You can cancel anytime.</p>
                                        @else
                                            <p class="mt-4 mb-4 text-white-50">Upgrade the plan at ₹ 149 per month and get
                                                more device login and best entertainment</p>
                                            <div class="mt-3">
                                                {{-- Flexbox container --}}
                                                <button type="button" class="btn btn-danger btn-lg flex-fill" 
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#subscribe-modal">Upgrade your
                                                    plan</button>
                                            </div>
                                        @endif
                                        <button type="button" class="btn btn-outline-light btn-lg flex-fill"
                                            data-bs-toggle="modal" data-bs-target="#deActivate">Deactivate Subscription</button>
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
            <div class="modal-content  bg-dark text-white">
                <div class="modal-header border-0 text-center">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5 bg-dark">
                    <h4>Update Data</h4>
                    <form method="POST" action="{{ url('/profile/update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-4">
                            <input id="name" type="text"
                                class="form-control change-password-form-input @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- <div class="form-group mb-4">
                                       
                                                        <input id="email" type="email" placeholder="Enter Email Id" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div> --}}
                        <div class="form-group mb-4">
                            <label for="profile_picture">Change
                                {{ __('Profile Picture') }}</label>
                            <input id="profile_picture" type="file"
                                class="form-control change-password-form-input @error('profile_picture') is-invalid @enderror"
                                name="profile_picture">
                            @error('profile_picture')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="profile-page-btn">Save Data</button>
                    </form>
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
    <div id="loader-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 1000; text-align: center;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #fff; text-align: center;">
        <div class="spinner" style="width: 50px; height: 50px; margin:auto; border: 5px solid #f3f3f3; border-top: 5px solid #ff3a1f; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        <p style="margin-top: 10px; font-size: 18px;">Please wait while Deactivating your subscription... <br> Don't close the window</p>
    </div>
</div>

<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
    <div class="modal fade" id="subscribe-modal" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl"> {{-- Changed to modal-xl for better content display --}}
            <div class="modal-content bg-dark text-white shadow-lg"> {{-- Added shadow for consistency --}}
                <div class="modal-header border-0 justify-content-end pt-3 pe-3"> {{-- Aligned close button to end, added padding --}}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body bg-dark text-white text-center pb-5 px-4"> {{-- Adjusted padding --}}
                    <div class="container-fluid"> {{-- Use container-fluid for responsive padding --}}
                        <div class="row justify-content-center"> {{-- Centered the content row --}}
                            <div class="col-12">
                                <h2 class="display-6 mb-3">Upgrade your plan for more entertainment</h2>
                                {{-- Larger heading --}}
                                <p class="lead text-white-50 mb-4">You will get more 2 device login and all categories
                                    entertainment</p> {{-- Styled lead text --}}
                            </div>
                        </div>

                        {{-- Dynamic Tabs for Monthly/Yearly plans --}}
                        <div class="row justify-content-center mb-4"> {{-- Centered tabs --}}
                            <div class="col-md-8 col-lg-6"> {{-- Constrained width of tabs --}}
                                @php
                                    $groupedPackages = $package->groupBy('type'); // Group packages by 'Month' or 'Year'
                                @endphp
                                <ul class="nav nav-pills nav-justified p-1" id="subscriptionTab" role="tablist">
                                    {{-- Pills style, justified, rounded background --}}
                                    @foreach ($groupedPackages as $type => $plans)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link text-white {{ $loop->first ? 'active' : '' }}"
                                                {{-- White text for non-active, active for selected --}} id="{{ strtolower($type) }}-tab"
                                                data-bs-toggle="tab" data-bs-target="#{{ strtolower($type) }}"
                                                type="button" role="tab">
                                                {{ $type }} (from ₹{{ $plans->min('price') }})
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- Dynamic Tab Content --}}
                        <div class="tab-content" id="subscriptionTabContent">
                            @foreach ($groupedPackages as $type => $plans)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                    id="{{ strtolower($type) }}" role="tabpanel">
                                    <div class="row justify-content-center"> {{-- Centered cards --}}
                                        @foreach ($plans as $item)
                                            <div class="col-md-6 col-lg-4 mb-4"> {{-- Adjusted column size for better layout with multiple cards --}}
                                                <div class="card h-100 bg-dark-subtle text-white border-primary shadow-sm">
                                                    {{-- Card styling --}}
                                                    <div class="card-body d-flex flex-column">
                                                        <h3 class="card-title text-center display-5 fw-bold mb-3">
                                                            ₹{{ $item->price }}<span class="fs-5 fw-normal"> /
                                                                {{ $type }}</span>
                                                        </h3>
                                                        <p class="card-text text-white-50 mb-3">Unlock exclusive content
                                                            and enjoy ad-free streaming.</p>

                                                        <ul class="list-unstyled text-start flex-grow-1">
                                                            {{-- Aligned list to start, allows content to push subscribe button down --}}
                                                            <li><i
                                                                    class="bi bi-check-circle-fill text-success me-2"></i>Content
                                                                Type:
                                                                @foreach ($typeData as $typeItem)
                                                                    {{ $typeItem->name }}@if (!$loop->last)
                                                                        ,
                                                                    @endif
                                                                @endforeach
                                                            </li>
                                                            <li><i
                                                                    class="bi bi-check-circle-fill text-success me-2"></i>Watch
                                                                on TV & Laptop:
                                                                {{ $item->watch_on_laptop_tv ? 'Yes' : 'No' }}
                                                            </li>
                                                            <li><i
                                                                    class="bi bi-check-circle-fill text-success me-2"></i>Ad-Free:
                                                                {{ $item->ads_free_movies_shows ? 'Yes' : 'No' }}
                                                            </li>
                                                            <li><i
                                                                    class="bi bi-check-circle-fill text-success me-2"></i>{{ $item->video_qulity }}
                                                                Video Quality
                                                            </li>
                                                            <li><i
                                                                    class="bi bi-check-circle-fill text-success me-2"></i>{{ $item->no_of_device }}
                                                                Device Login
                                                            </li>
                                                        </ul>

                                                        {{-- Subscribe Button Form --}}
                                                        <form action="{{ route('transaction.store') }}" method="POST"
                                                            class="subscription-form mt-auto"> {{-- mt-auto pushes button to bottom --}}
                                                            @csrf
                                                            <input type="hidden" name="package_id"
                                                                value="{{ $item->id }}">
                                                            <input type="hidden" name="razorpay_subscription_id"
                                                                class="razorpay_subscription_id">
                                                            <input type="hidden" name="razorpay_payment_id"
                                                                class="razorpay_payment_id">
                                                            <input type="hidden" name="razorpay_signature"
                                                                class="razorpay_signature">
                                                            <input type="hidden" name="amount" class="amount"
                                                                value="{{ $item->price * 100 }}">
                                                            <input type="hidden" name="user_id"
                                                                value="{{ auth()->id() }}">
                                                            <button type="button"
                                                                class="btn btn-danger btn-lg w-100 rzp-button"
                                                                {{-- Full width button --}}
                                                                data-amount="{{ $item->price * 100 }}"
                                                                data-package-id="{{ $item->id }}"
                                                                data-plan-id="{{ $item->razorpay_plan_id }}">
                                                                Subscribe ₹ {{ $item->price }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                        <script>
                            $(document).ready(function() {
                                let isAnySubscriptionProcessing = false;

                                document.querySelectorAll('.rzp-button').forEach(button => {
                                    button.addEventListener('click', async function(e) {
                                        e.preventDefault();
                                        console.log(isAnySubscriptionProcessing);
                                        if (isAnySubscriptionProcessing) return; // ⛔ block multiple clicks globally
                                        isAnySubscriptionProcessing = true; // ✅ Lock

                                        const form = this.closest('form');
                                        const amount = this.dataset.amount;
                                        const planId = this.dataset.planId;
                                        const packageId = this.dataset.packageId;
                                        console.log(amount);
                                        console.log(planId);
                                        console.log(packageId);
                                        try {
                                            const response = await fetch('{{ route('subscription.create') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                },
                                                body: JSON.stringify({
                                                    plan_id: planId,
                                                    package_id: packageId
                                                }),
                                            });

                                            const data = await response.json();

                                            if (!data.subscription_id) {
                                                alert("Error creating subscription.");
                                                isAnySubscriptionProcessing = false;
                                                return;
                                            }

                                            const options = {
                                                key: "{{ config('app.razorpay_key') }}",
                                                subscription_id: data.subscription_id,
                                                name: "Alphaneon Studioz",
                                                description: "Subscription Payment",
                                                handler: function(response) {
                                                    document.getElementById('loader-overlay').style.display = 'block';
                                                    form.querySelector('.razorpay_subscription_id').value =
                                                        response
                                                        .razorpay_subscription_id;
                                                    form.querySelector('.razorpay_payment_id').value =
                                                        response
                                                        .razorpay_payment_id;
                                                    form.querySelector('.razorpay_signature').value =
                                                        response
                                                        .razorpay_signature;
                                                    form.submit();
                                                },
                                                prefill: {
                                                    name: "{{ auth()->user()->name }}",
                                                    email: "{{ auth()->user()->email }}"
                                                },
                                                theme: {
                                                    color: "#B01803"
                                                },
                                                modal: {
                                                    ondismiss: () => {
                                                        alert("Payment popup closed.");
                                                        isAnySubscriptionProcessing =
                                                        false; // 🔁 Unlock on cancel
                                                    }
                                                }
                                            };

                                            const rzp = new Razorpay(options);
                                            rzp.open();

                                        } catch (error) {
                                            console.error('Error creating subscription:', error);
                                            alert("Failed to create subscription. Please try again.");
                                            isAnySubscriptionProcessing = false; // 🔁 Unlock on failure
                                        }
                                    });
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deactivateForm = document.getElementById('deactivateSubscriptionForm');
        const loaderOverlay = document.getElementById('loader-overlay');
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
