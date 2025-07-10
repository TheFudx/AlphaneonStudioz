
@extends('layouts.main')
@section('title')
    Alphastudioz | Device Limit
@endsection
<style>
    .btn-logout {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background-color: #ff3a1f;
        color: white;
        text-decoration: none;
        font-size: 16px;
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }

    .btn-logout:hover {
        background-color: #e63317;
    }
</style>
@section('main-section')
    <section id="section-home-newdes" class="podcast mt-5 pt-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid">
                    <div class="slider-container mt-2">
                        <div class="latest-release-slider movies-page-section">
                             {{-- @if (session('errors'))
                                <div class="alert alert-success">
                                    {{ session('errors') }}
                                </div>
                            @endif --}}
                            @if ($logedInDevice)
                                <div class="row text-center">
                                    <h3 class="text-white mt-3 ">You have reached your device limit</h3>
                                    {{-- Main heading --}}
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6">
                                        {{-- <p>if you want more devices login please upgrade your plan</p>
                            
                                            <form action="{{ route('store.upgrade.transaction') }}" method="post" id="subscribe-form">
                                                @csrf
                                                <input type="hidden" name="package_id" id="packageId" value="">
                                                <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $order->id }}">
                                                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" >
                                                <input type="hidden" name="razorpay_signature" id="razorpay_signature" >
                                                <input type="hidden" name="amount" id="amount1" value="{{ $amount / 100 * 100 }}">
                                                <input type="hidden" name="user_id" value="{{ session('user_id') }}">
                                                <button type="button" class="btn-mood" id="rzp-button1">Subscribe Now</button>
                                            </form> --}}

                                    
                                            <div class="card bg-dark text-white p-3 shadow-lg">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        @foreach ($logedInDevice as $item)
                                                            <div class="price-card-content">
                                                                <div class="row">
                                                                    <div class="col-9">
                                                                        {{-- <p class="text-light">{{ getActualDeviceNameAndBrowserFromUserAgent($item->device_name) }}</p> --}}
                                                                        <p class="text-light">{{ $item->browser }} |  {{$item->platform}} | {{$item->platform_version}} | {{    $item->device_type }}</p>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        {{-- <button class="btn-logout">Logout</button> --}}
                                                                        <form action="{{ route('device.logout', $item->id) }}" method="POST" id="devicelogout">
                                                                                            @csrf
                                                                                             <input type="hidden" id="is_brave" name="is_brave" value="0">
                                                                                            <input type="hidden" name="device_id" value="{{ $item->device_id }}">
                                                                                            <button class="btn-logout">Logout</button>
                                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    {{-- <div class="col-md-6 subscription-section">
                                        <div class="card bg-dark text-white p-3 shadow-lg">
                                            <div class="card-body">

                                                @foreach ($package as $item)
                                                    @if ($item->price == 149)
                                                        <label for="packageId{{ $item->id }}">
                                                            <div class="price-card mt-md-5 mt-1 mb-5">
                                                                <div class="price-card-content">
                                                                    <h3>₹{{ $item->price }} /<span>{{ $item->type }} </span>
                                                                    </h3>
                                                                    <input type="hidden" name="price"
                                                                        value="{{ $item->price }}" class="price"
                                                                        id="price">
                                                                    <h4 class="theme-text-color">{{ $item->name }}</h4>
                                                                    <div class="line"></div>
                                                                    <span class="mt-2 d-block">You will get more entertainment
                                                                        and options
                                                                        with this plan </span>
                                                                    <div class="benefits">
                                                                        <ul>
                                                                            <li>In this package you will get @foreach ($typeData as $typeItem)
                                                                                    {{ $typeItem->name }}
                                                                                @endforeach
                                                                            </li>
                                                                            <li>Watch On Tv & Laptop @if ($item->watch_on_laptop_tv == 1)
                                                                                    - Yes
                                                                                @else
                                                                                    - No
                                                                                @endif
                                                                            </li>
                                                                            <li>Ad Free Movies and Show @if ($item->ads_free_movies_shows == 1)
                                                                                    - Yes
                                                                                @else
                                                                                    - No
                                                                                @endif
                                                                            </li>
                                                                            <li>{{ $item->video_qulity }} Video Quality</li>
                                                                            <li>{{ $item->no_of_device }} Device Login</li>

                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </label>
                                                        <input type="radio" name="package" checked class="packageData d-none"
                                                            id="packageId{{ $item->id }}" value="{{ $item->id }}">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- <div class="row ms-0 me-0">
        <div class="col-sm-1 col-12">

        </div>
        <div class="col-sm-11 col-12 mt-5 p-1">
            <section id="banners" class="pe-md-5 pe-1 ps-m-0 ps-1">
                <div class="banner-section">
                    <div class="banner-holder">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="device-limit-text-holder">
                                    <div class="content p-4">
                                        <h2>You have reached your device limit</h2>
                                      
                                        @if ($logedInDevice)
                                            @foreach ($logedInDevice as $item)
                                                <div class="price-card rounded p-3 shadow-sm mt-4">
                                                    <div class="row">
                                                        <div class="col-9">
                                                            <p class="text-light">{{ $item->device_id }}</p>
                                                        </div>
                                                        <div class="col-3">
                                                            <button class="btn-logout">Logout</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 subscription-section">
                                @foreach ($package as $item)
                                    @if ($item->price == 49)
                                        <label for="packageId{{ $item->id }}">
                                            <div class="price-card mt-md-5 mt-1 mb-5">
                                                <div class="price-card-content">
                                                    <h3>₹{{ $item->price }} /<span>Month </span> </h3>
                                                    <input type="hidden" name="price" value="{{ $item->price }}"
                                                        class="price" id="price">
                                                    <h4 class="theme-text-color">{{$item->name}}</h4>
                                                    <div class="line"></div>
                                                    <span class="mt-2 d-block">You will get more entertainment and options
                                                        with this plan </span>
                                                    <div class="benefits">
                                                        <ul>
                                                            <li>In this package you will get @foreach ($typeData as $typeItem)
                                                                    {{ $typeItem->name }}
                                                                @endforeach
                                                            </li>
                                                            <li>Watch On Tv & Laptop @if ($item->watch_on_laptop_tv == 1)
                                                                    - Yes
                                                                @else
                                                                    - No
                                                                @endif
                                                            </li>
                                                            <li>Ad Free Movies and Show @if ($item->ads_free_movies_shows == 1)
                                                                    - Yes
                                                                @else
                                                                    - No
                                                                @endif
                                                            </li>
                                                            <li>{{ $item->video_qulity }} Video Quality</li>
                                                            <li>{{ $item->no_of_device }} Device Login</li>

                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                        <input type="radio" name="package" checked class="packageData d-none"
                                            id="packageId{{ $item->id }}" value="{{ $item->id }}">
                                    @endif
                                @endforeach
                            </div>
                        </div>


                    </div>

            </section>

        </div>
    </div> --}}
@endsection
