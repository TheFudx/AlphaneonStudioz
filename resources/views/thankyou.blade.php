@extends('layouts.main')
@section('title')
    {{ config('app.project_name') }} | Thank you
@endsection
@section('main-section')
    <style>
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
    </style>
    <section id="section-home-newdes" class="podcast mt-5 pt-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid">
                    {{-- <span class="l-head">Coming Soon!</span> 

                    <hr class="mb-3"> --}}
                    <div class="slider-container mt-2">
                        <div class="latest-release-slider movies-page-section">
                            <div class="row g-1">
                                <div class="col-12 text-center">
                                    <img src="{{ URL::to('asset/images/thankyou.png') }}" alt="thankyou.png"
                                        class="img-fluid" style="max-width: 300px; margin-top: 10px;">
                                    <h3 class="text-white mt-3">Thank you</h3>
                                    @php
                                        $currentUser = $user ?? auth()->user();
                                        $amount = $transaction ? $transaction->amount : '15';
                                        $isSubscribed = ($currentUser && $currentUser->subscription === 'Yes') || ($transaction && $transaction->status == 1);
                                    @endphp
                                    @if($isSubscribed)
                                        <p class="text-white">Your ₹ {{ $amount }} Subscription is Activated. Enjoy your entertainment!</p>
                                    @else
                                        <p class="text-white">Your payment has been received and your subscription is active. Enjoy your entertainment!</p>
                                    @endif 
                                    <a href="http://{{ config('app.project_name') }}.in/get-app" class="mt-2">
                                        <button type="button" class="profile-page-btn">
                                            Go to Application
                                        </button>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
