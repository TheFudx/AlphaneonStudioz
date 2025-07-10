@extends('layouts.main')
@section('title')
    Alphastudioz | Subscribe
@endsection
<style>
    .overlay-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.6);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .overlay-loader .spinner-border {
        width: 3rem;
        height: 3rem;
        color: white;
    }
</style>
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
    <!-- Section with background image and dark overlay -->
    <section class="position-relative text-white"
        style="
        background: url('{{ URL::to('asset/images/subscription-page-background-image.png') }}') no-repeat center center / cover;
        min-height: 100vh;">
        <!-- ✅ Dark semi-transparent overlay (only for background) -->
        {{-- <div class="position-absolute top-0 start-0 w-100 h-100" style="background-color: rgba(0,0,0,0.7); z-index: 1;"></div> --}}
        <!-- ✅ Foreground content with higher z-index -->
        <div class="overlay-loader" id="loadingOverlay">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="container position-relative" style=" padding-top: 10rem; padding-bottom: 5rem; margin-top:0px">
            <div class="text-center mb-5">
                @if ($activeSubscription)
                    {{-- <div class="alert alert-info" role="alert"> --}}
                        <h4 class="fw-bold">You already have a subscription. Please manage your subscription from your account settings.</h4>
                    {{-- </div> --}}
                @else
                    <h4 class="fw-bold">Subscribe NOW! Unlock the Magic of Cinema</h4>
                @endif
            </div>
            <div class="row justify-content-center g-4">
                @foreach ($package as $p)
                    
                    @if($p->id != $activePackageId)
                        <div class="col-md-4">
                            <div class="card bg-dark text-white border-0 shadow h-100 p-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="card-title">BASIC PLAN</h5>
                                        <span class="badge bg-secondary">SAVE 80%</span>
                                    </div>
                                    <p class="fs-5 mt-2">₹ {{ number_format($p->price, 2, '.', ',') }}
                                        <span class="fs-6">/ {{ $p->name }}</span>
                                    </p>
                                    <hr class="bg-secondary">
                                    <div class="mb-2">
                                        <p class="mb-0 small">Content Type:</p>
                                        <p class="fw-bold">
                                            @foreach ($typeData as $typeItem)
                                                {{ $typeItem->name }} |
                                            @endforeach
                                        </p>
                                    </div>
                                    <div class="mb-2">
                                        <p class="mb-0 small">Resolution</p>
                                        <p class="fw-bold">{{ $p->video_qulity }}</p>
                                    </div>
                                    <div class="mb-2">
                                        <p class="mb-0 small">Video & Sound Quality</p>
                                        <p class="fw-bold">Fair</p>
                                    </div>
                                    <div class="mb-2">
                                        <p class="mb-0 small">Supported Devices</p>
                                        <p class="fw-bold">
                                            {{ $p->watch_on_laptop_tv == 1 ? 'Mobile phone, laptop' : 'Mobile phone' }}
                                        </p>
                                    </div>
                                    <div class="mb-2">
                                        <p class="mb-0 small">Connect Devices</p>
                                        <p class="fw-bold">{{ $p->no_of_device }}</p>
                                    </div>
                                    <div class="mb-2">
                                        <p class="mb-0 small">Ad Free Movies And Show</p>
                                        <p class="fw-bold">{{ $p->ads_free_movies_shows == 1 ? 'Yes' : 'No' }}</p>
                                    </div>
                                    {{-- 
                                    <a href="#" class="btn btn-danger w-100 mt-3" id="rzp-button">
                                        Continue <i class="fas fa-arrow-right"></i>
                                    </a> --}}
                                    <form action="{{ route('transaction.store') }}" method="POST"
                                        class="subscription-form mt-3">
                                        @csrf
                                        <input type="hidden" name="package_id" value="{{ $p->id }}">
                                        <input type="hidden" name="razorpay_subscription_id" class="razorpay_subscription_id">
                                        <input type="hidden" name="razorpay_payment_id" class="razorpay_payment_id">
                                        <input type="hidden" name="razorpay_signature" class="razorpay_signature">
                                        <input type="hidden" name="amount" class="amount" value="{{ $p->price * 100 }}">
                                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                        <button type="button" class="btn btn-danger w-100 mt-3 rzp-button"
                                            data-amount="{{ $p->price * 100 }}" data-package-id="{{ $p->id }}"
                                            data-plan-id="{{ $p->razorpay_plan_id }}">
                                            Continue <i class="fas fa-arrow-right"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <script>
                let isAnySubscriptionProcessing = false;

                const showLoader = () => document.getElementById('loadingOverlay').style.display = 'flex';
                const hideLoader = () => document.getElementById('loadingOverlay').style.display = 'none';

                document.querySelectorAll('.rzp-button').forEach(button => {
                    button.addEventListener('click', async function(e) {
                        e.preventDefault();

                        if (isAnySubscriptionProcessing) return;
                        isAnySubscriptionProcessing = true;
                        showLoader();

                        const form = this.closest('form');
                        const amount = this.dataset.amount;
                        const planId = this.dataset.planId;
                        const packageId = this.dataset.packageId;

                        try {
                            const response = await fetch('{{ route('subscription.create') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': `{{ csrf_token() }}`,
                                },
                                body: JSON.stringify({
                                    plan_id: planId,
                                    package_id: packageId
                                }),
                            });

                            const data = await response.json();

                            if (!data.subscription_id) {
                                alert("⚠️ Unable to create subscription. Please try again later.");
                                isAnySubscriptionProcessing = false;
                                hideLoader();
                                return;
                            }

                            const options = {
                                key: "{{ config('app.razorpay_key') }}",
                                subscription_id: data.subscription_id,
                                name: "Alphaneon Studioz",
                                description: "Subscription Payment",
                                handler: function(response) {
                                    document.getElementById('loader-overlay').style.display = 'block';
                                    form.querySelector('.razorpay_subscription_id').value = response
                                        .razorpay_subscription_id;
                                    form.querySelector('.razorpay_payment_id').value = response
                                        .razorpay_payment_id;
                                    form.querySelector('.razorpay_signature').value = response
                                        .razorpay_signature;
                                    form.submit();
                                },
                                prefill: {
                                    name: "{{ app('logged-in-user')->name }}",
                                    email: "{{ app('logged-in-user')->email }}"
                                },
                                theme: {
                                    color: "#B01803"
                                },
                                modal: {
                                    ondismiss: () => {
                                        alert("Payment cancelled.");
                                        isAnySubscriptionProcessing = false;
                                        hideLoader();
                                    }
                                }
                            };

                            const rzp = new Razorpay(options);
                            rzp.open();
                            hideLoader(); // Optional, because Razorpay takes over UI
                        } catch (error) {
                            console.error('Error creating subscription:', error);
                            alert("Something went wrong. Please check your internet and try again.");
                            isAnySubscriptionProcessing = false;
                            hideLoader();
                        }
                    });
                });
            </script>

        </div>
    </section>
@endsection
