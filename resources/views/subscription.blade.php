@php
    $user = auth()->user();
    $package = App\Models\Package::all();
    $typeIds = $package->pluck('type_id')->flatMap(fn($ids) => explode(',', $ids))->unique()->toArray();
    $typeData = App\Models\Type::whereIn('id', $typeIds)->get();
    $activeSubscription = false;
    $activePackageId = null;
    // Read active gateway from settings
    $activeGateway = get_setting('active_gateway', 'razorpay'); // razorpay | payu | both
    if ($user) {
        $user_subscription = App\Models\UserSubscription::where('user_id', $user->id)->where('status', 1)->first();
        $activePackageId = $user_subscription?->package_id;
        try {
            if (\DB::getSchemaBuilder()->hasColumn('transactions', 'expiry_date')) {
                $activeSubscription = App\Models\Transction::where('user_id', $user->id)
                    ->where('expiry_date', '>', now())
                    ->where('status', 1)
                    ->exists();
            }
        } catch (\Exception $e) {
        }
    }
    $CLIENT_ID = '18549902682527194455';
    $REDIRECT_URL = url('/auth/subscription/callback');
    $AUTH_URL = 'https://auth.phone.email/log-in?client_id=' . $CLIENT_ID . '&redirect_url=' . $REDIRECT_URL;
@endphp
<style>
    :root {
        --primary: #FF3A1F;
        --primary-light: #ff6a00;
    }
    #subscriptionModal .modal-content {
        background: rgba(12, 12, 12, 0.96);
        backdrop-filter: blur(24px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    #subscriptionModal .modal-body::-webkit-scrollbar { width: 6px; }
    #subscriptionModal .modal-body::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #FF3A1F, #ff6a00);
        border-radius: 10px;
    }
    #subscriptionModal .modal-body {
        scrollbar-width: thin;
        scrollbar-color: #FF3A1F transparent;
        scroll-behavior: smooth;
    }

    /* ── Card shell ───────────────────────────────────────────── */
    .subscription-card {
        background: linear-gradient(160deg, #1e1e1e 0%, #141414 100%);
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.07);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
        min-height: 480px;
    }
    .subscription-card:hover {
        transform: translateY(-10px) scale(1.015);
        box-shadow: 0 24px 60px rgba(0,0,0,0.7);
        border-color: var(--primary);
        border-width: 2px;
        box-shadow: 0 0 28px rgba(255,58,31,0.35), 0 24px 60px rgba(0,0,0,0.7);
    }
    /* Red band on hover for non-active cards */
    .subscription-card:hover .pkg-band {
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
    }
    .subscription-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 20px;
        background: linear-gradient(120deg, transparent 30%, rgba(255,58,31,0.12) 60%, transparent 90%);
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }
    .subscription-card:hover::after { opacity: 1; }
    .subscription-card.active {
        border: 2px solid var(--primary);
        box-shadow: 0 0 28px rgba(255,58,31,0.35);
    }

    /* ── Top colour band ──────────────────────────────────────── */
    .pkg-band {
        height: 6px;
        width: 100%;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        border-radius: 20px 20px 0 0;
        flex-shrink: 0;
    }
    .subscription-card:not(.active) .pkg-band {
        background: linear-gradient(90deg, #333, #444);
    }

    /* ── Card body padding ────────────────────────────────────── */
    .pkg-body {
        padding: 22px 22px 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    /* ── Badge ────────────────────────────────────────────────── */
    .pkg-badge {
        position: absolute;
        top: 14px;
        right: 14px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .6px;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        z-index: 2;
    }

    /* ── Plan title ───────────────────────────────────────────── */
    .pkg-title {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: #aaa;
        margin-bottom: 8px;
    }

    /* ── Price block ──────────────────────────────────────────── */
    .pkg-price {
        display: flex;
        align-items: baseline;
        gap: 4px;
        margin-bottom: 4px;
    }
    .pkg-price .currency { font-size: 20px; font-weight: 700; color: #fff; margin-top: 4px; }
    .pkg-price .amount   { font-size: 42px; font-weight: 800; color: #fff; line-height: 1; }
    .pkg-period          { font-size: 12px; color: #666; margin-bottom: 18px; }

    /* ── Divider ──────────────────────────────────────────────── */
    .pkg-divider {
        height: 1px;
        background: rgba(255,255,255,0.07);
        margin-bottom: 18px;
    }

    /* ── Feature rows ─────────────────────────────────────────── */
    .pkg-features { flex: 1; margin-bottom: 20px; }
    .pkg-feature-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 7px 0;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .pkg-feature-row:last-child { border-bottom: none; }
    .pkg-feature-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: rgba(255,58,31,0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 13px;
    }
    .pkg-feature-text { display: flex; flex-direction: column; }
    .pkg-feature-label { font-size: 11px; color: #666; line-height: 1.2; }
    .pkg-feature-val   { font-size: 13px; font-weight: 600; color: #e0e0e0; line-height: 1.4; }

    /* ── Buttons ──────────────────────────────────────────────── */
    .btn-subscribe {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        border-radius: 50px;
        border: none;
        font-weight: 700;
        font-size: 14px;
        letter-spacing: .4px;
        transition: 0.3s;
        color: #fff;
        padding: 10px 0;
    }
    .btn-subscribe:hover {
        transform: scale(1.04);
        box-shadow: 0 10px 28px rgba(255, 58, 31, 0.5);
        color: #fff;
    }
    .btn-subscribe:focus,
    .btn-subscribe:visited,
    .btn-subscribe:active {
        color: #fff;
        text-decoration: none;
    }
    .btn-payu {
        background: linear-gradient(135deg, #FC6B21, #f0a500);
        border-radius: 50px;
        border: none;
        font-weight: 700;
        font-size: 14px;
        transition: 0.3s;
        color: #fff;
        padding: 10px 0;
    }
    .btn-payu:hover {
        transform: scale(1.04);
        box-shadow: 0 10px 28px rgba(252, 107, 33, 0.5);
        color: #fff;
    }
    .btn-payu:focus,
    .btn-payu:visited,
    .btn-payu:active {
        color: #fff;
        text-decoration: none;
    }
    .gw-loading {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        vertical-align: middle;
        margin-right: 6px;
    }
    .subscription-card::before { pointer-events: none; }
    @media(max-width:768px) {
        #subscriptionModal .modal-dialog { margin: 0; max-width: 100%; }
        #subscriptionModal .modal-content { border-radius: 0; height: 100vh; }
        .subscription-card { min-height: unset; }
    }
</style>
<div class="modal fade" id="subscriptionModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content text-white">
            <div class="modal-header border-0">
                <h5 class="modal-title">Subscribe Now</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="container py-1">
                    <div class="text-center mb-2">
                        @if ($activeSubscription)
                            <h4 class="fw-bold text-warning">You already have an active subscription 🎉</h4>
                        @else
                            <h3 class="fw-bold">Unlimited Movies, TV Shows & More</h3>
                        @endif
                        @if (session('payment_error'))
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Show error toast
                                    toastr.error("{{ session('payment_error') }}");
                                    // Re-open subscription modal so user can retry
                                    setTimeout(function() {
                                        var modal = new bootstrap.Modal(document.getElementById('subscriptionModal'));
                                        modal.show();
                                    }, 600);
                                });
                            </script>
                        @endif
                        @if (session('payment_success'))
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    toastr.success("{{ session('payment_success') }}");
                                });
                            </script>
                        @endif
                    </div>
                    <div class="row justify-content-center g-4">
                        
                        @foreach ($package as $p)

                            {{-- @if ($hasTaken15Plan && (int) $p->price === 15)
                                @continue
                            @endif --}}
                            @if ($p->id != $activePackageId)
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="subscription-card {{ $loop->first ? 'active' : '' }}">

                                        {{-- Top colour band --}}
                                        <div class="pkg-band"></div>

                                        {{-- Most popular badge --}}
                                        @if ($loop->first)
                                            <div class="pkg-badge">⭐ Most Popular</div>
                                        @endif

                                        <div class="pkg-body">

                                            {{-- Plan name --}}
                                            <div class="pkg-title">{{ $p->title }}</div>

                                            {{-- Price --}}
                                            <div class="pkg-price">
                                                <span class="currency">₹</span>
                                                <span class="amount">{{ number_format($p->price, 0) }}</span>
                                            </div>
                                            <div class="pkg-period">per {{ $p->name }}</div>

                                            <div class="pkg-divider"></div>

                                            {{-- Features --}}
                                            <div class="pkg-features">

                                                <div class="pkg-feature-row">
                                                    <div class="pkg-feature-icon">🎬</div>
                                                    <div class="pkg-feature-text">
                                                        <span class="pkg-feature-label">Content</span>
                                                        <span class="pkg-feature-val">
                                                            @foreach ($typeData as $typeItem)
                                                                {{ $typeItem->name }}@if(!$loop->last) &nbsp;|&nbsp; @endif
                                                            @endforeach
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="pkg-feature-row">
                                                    <div class="pkg-feature-icon">📺</div>
                                                    <div class="pkg-feature-text">
                                                        <span class="pkg-feature-label">Resolution</span>
                                                        <span class="pkg-feature-val">{{ $p->video_qulity }}</span>
                                                    </div>
                                                </div>

                                                <div class="pkg-feature-row">
                                                    <div class="pkg-feature-icon">📱</div>
                                                    <div class="pkg-feature-text">
                                                        <span class="pkg-feature-label">Devices</span>
                                                        <span class="pkg-feature-val">{{ $p->watch_on_laptop_tv ? 'Mobile + Laptop / TV' : 'Mobile only' }}</span>
                                                    </div>
                                                </div>

                                                <div class="pkg-feature-row">
                                                    <div class="pkg-feature-icon">🖥️</div>
                                                    <div class="pkg-feature-text">
                                                        <span class="pkg-feature-label">Simultaneous Screens</span>
                                                        <span class="pkg-feature-val">{{ $p->no_of_device }} {{ $p->no_of_device == 1 ? 'Screen' : 'Screens' }}</span>
                                                    </div>
                                                </div>

                                                <div class="pkg-feature-row">
                                                    <div class="pkg-feature-icon">{{ $p->ads_free_movies_shows ? '🚫' : '📢' }}</div>
                                                    <div class="pkg-feature-text">
                                                        <span class="pkg-feature-label">Ads</span>
                                                        <span class="pkg-feature-val" style="color:{{ $p->ads_free_movies_shows ? '#4ade80' : '#f87171' }}">
                                                            {{ $p->ads_free_movies_shows ? 'Ad-Free Experience' : 'With Ads' }}
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>{{-- /pkg-features --}}

                                            {{-- CTA buttons --}}
                                            <div class="d-flex flex-column gap-2">
                                                @if ($activeGateway === 'razorpay' || $activeGateway === 'both')
                                                    @if (!auth()->check())
                                                        <a href="{{ route('login') }}" class="btn btn-subscribe w-100 text-decoration-none text-center d-flex align-items-center justify-content-center">
                                                            {{ $activeGateway === 'both' ? 'Pay via Razorpay' : 'Get Started →' }}
                                                        </a>
                                                    @else
                                                        <form action="{{ route('transaction.store') }}" method="POST" class="rzp-form">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                                            <input type="hidden" name="package_id" value="{{ $p->id }}">
                                                            <input type="hidden" name="razorpay_subscription_id" class="razorpay_subscription_id">
                                                            <input type="hidden" name="razorpay_payment_id" class="razorpay_payment_id">
                                                            <input type="hidden" name="razorpay_signature" class="razorpay_signature">
                                                            <input type="hidden" name="amount" value="{{ $p->price * 100 }}">
                                                            <input type="hidden" name="gateway" value="razorpay">
                                                            <button type="button" class="btn btn-subscribe w-100 rzp-button"
                                                                data-amount="{{ $p->price * 100 }}"
                                                                data-package-id="{{ $p->id }}"
                                                                data-plan-id="{{ $p->razorpay_plan_id ?? '' }}">
                                                                {{ $activeGateway === 'both' ? 'Pay via Razorpay' : 'Get Started →' }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                                @if ($activeGateway === 'payu' || $activeGateway === 'both')
                                                    @if (!auth()->check())
                                                        <a href="{{ route('login') }}" class="btn btn-payu w-100 text-decoration-none text-center d-flex align-items-center justify-content-center">
                                                            {{ $activeGateway === 'both' ? 'Pay via PayU' : 'Get Started →' }}
                                                        </a>
                                                    @else
                                                        <form action="{{ route('payu.initiate') }}" method="POST" class="payu-form">
                                                            @csrf
                                                            <input type="hidden" name="package_id" value="{{ $p->id }}">
                                                            <button type="submit" class="btn btn-payu w-100">
                                                                {{ $activeGateway === 'both' ? 'Pay via PayU' : 'Get Started →' }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>

                                        </div>{{-- /pkg-body --}}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    // ── Hover active-card transfer ────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('.subscription-card');
        const defaultActive = document.querySelector('.subscription-card.active');

        cards.forEach(function (card) {
            card.addEventListener('mouseenter', function () {
                cards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');
            });
            card.addEventListener('mouseleave', function () {
                cards.forEach(c => c.classList.remove('active'));
                if (defaultActive) defaultActive.classList.add('active');
            });
        });
    });

    // Called by OTP popup after successful login
    window.handleAfterOtpLogin = function(packageId) {
        if (packageId) {
            // Auto-submit PayU form for the specific package
            const forms = document.querySelectorAll('.payu-form, .payu-guest-trigger');
            // Create and submit a hidden form directly
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('payu.initiate') }}';
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            const pkg = document.createElement('input');
            pkg.type = 'hidden';
            pkg.name = 'package_id';
            pkg.value = packageId;
            form.appendChild(csrf);
            form.appendChild(pkg);
            document.body.appendChild(form);
            form.submit(); // ✅ Directly goes to PayU
        } else {
            // No package pending — just reload so user sees logged-in state with modal open
            sessionStorage.setItem('reopen_subscription_modal', '1');
            window.location.reload();
        }
    };
    // On page load — reopen modal if flagged
    document.addEventListener('DOMContentLoaded', function() {
        if (sessionStorage.getItem('reopen_subscription_modal') === '1') {
            sessionStorage.removeItem('reopen_subscription_modal');
            setTimeout(function() {
                new bootstrap.Modal(document.getElementById('subscriptionModal')).show();
            }, 400);
        }
    });
    // ── Modal entrance animation ──────────────────────────────────────
    document.getElementById('subscriptionModal')
        .addEventListener('shown.bs.modal', function() {
            document.querySelectorAll('.subscription-card').forEach((el, i) => {
                el.style.opacity = 0;
                el.style.transform = 'translateY(40px)';
                setTimeout(() => {
                    el.style.transition = 'all 0.5s ease';
                    el.style.opacity = 1;
                    el.style.transform = 'translateY(0)';
                }, i * 120);
            });
        });
    // ── Loader helpers ────────────────────────────────────────────────
    function showModalLoader() {
        const el = document.getElementById('loadingOverlay') || document.getElementById('loader-overlay');
        if (el) el.style.display = 'flex';
    }
    function hideModalLoader() {
        const el = document.getElementById('loadingOverlay') || document.getElementById('loader-overlay');
        if (el) el.style.display = 'none';
    }
    // ── PayU: event delegation on document ───────────────────────────
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.classList.contains('payu-form')) return;
        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML =
                '<span style="display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.6s linear infinite;vertical-align:middle;margin-right:6px;"></span> Redirecting...';
        }
        showModalLoader();
    });
    // ── Razorpay: event delegation on document ────────────────────────
    let isProcessing = false;
    if (!window._rzpSubListenerAttached) {
        window._rzpSubListenerAttached = true;
        document.addEventListener('click', async function(e) {
            const button = e.target.closest('.rzp-button');
            if (!button) return;
            e.preventDefault();
            e.stopImmediatePropagation();

            const isUserLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
            if (!isUserLoggedIn) {
                window.location.href = "{{ route('login') }}";
                return;
            }

            // Prevent double click
            if (isProcessing || window._isRzpSubscriptionBusy) {
                console.log('Already processing, ignoring click');
                return;
            }
            window._isRzpSubscriptionBusy = true;
                const form = button.closest('form');
                const amount = button.dataset.amount;
                const planId = button.dataset.planId;
                const packageId = button.dataset.packageId;
                console.log('RZP button clicked', {
                    amount,
                    planId,
                    packageId
                }); // debug
                // Guard: check Razorpay is loaded
                if (typeof Razorpay === 'undefined') {
                    alert('Payment system not loaded. Please refresh and try again.');
                    return;
                }
                isProcessing = true;
                const origText = button.innerHTML;
                button.disabled = true;
                button.innerHTML =
                    '<span style="display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.6s linear infinite;vertical-align:middle;margin-right:6px;"></span> Please wait...';
                showModalLoader();
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

                    if (response.status === 401) {
                        window.location.href = "{{ route('login') }}";
                        return;
                    }

                    const data = await response.json();
                    console.log('Subscription create response:', data); // debug

                    if (data.unauthenticated || data.redirect) {
                        window.location.href = data.redirect || "{{ route('login') }}";
                        return;
                    }
                    if (data.status === 'active') {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(data.message || 'Subscription activated successfully!');
                        } else {
                            alert(data.message || 'Subscription activated successfully!');
                        }
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                        return;
                    }
                    console.log('-------------------data-----------------------')
                    console.log(data);
                    if (!data.subscription_id) {
                        alert('⚠️ Unable to create subscription. Please try again.');
                        button.disabled = false;
                        button.innerHTML = origText;
                        isProcessing = false;
                        hideModalLoader();
                        return;
                    }
                    const prefillObj = {};
                    const uName = "{{ $user && $user->name ? $user->name : '' }}".trim();
                    const uEmail = "{{ $user && $user->email ? $user->email : '' }}".trim();
                    const rawContact = "{{ $user && $user->mobile_no ? $user->mobile_no : '' }}";
                    const uContact = rawContact.replace(/\D/g, '').trim();

                    if (uName && uName !== 'Guest') prefillObj.name = uName;
                    if (uEmail && uEmail.includes('@')) prefillObj.email = uEmail;
                    if (uContact && uContact.length >= 10) prefillObj.contact = uContact;

                    const options = {
                        key: "{{ config('app.razorpay_key') ?: env('RAZORPAY_KEY') }}",
                        subscription_id: data.subscription_id,
                        name: "{{ config('app.project_full_name') }}",
                        description: "Subscription Payment",
                        prefill: prefillObj,
                        remember_customer: false,
                        handler: function(response) {
                            var finalSubId = (response && response.razorpay_subscription_id && response.razorpay_subscription_id !== 'undefined') 
                                ? response.razorpay_subscription_id 
                                : (data && data.subscription_id ? data.subscription_id : '');
                            var finalPayId = (response && response.razorpay_payment_id) ? response.razorpay_payment_id : '';
                            var finalSig = (response && response.razorpay_signature) ? response.razorpay_signature : '';

                            var subInput = form.querySelector('.razorpay_subscription_id');
                            var payInput = form.querySelector('.razorpay_payment_id');
                            var sigInput = form.querySelector('.razorpay_signature');

                            if (subInput) subInput.value = finalSubId;
                            if (payInput) payInput.value = finalPayId;
                            if (sigInput) sigInput.value = finalSig;

                            showModalLoader();
                            form.submit();
                        },
                        theme: {
                            color: '#B01803'
                        },
                        modal: {
                            ondismiss: function() {
                                button.disabled = false;
                                button.innerHTML = origText;
                                isProcessing = false;
                                window._isRzpSubscriptionBusy = false;
                                hideModalLoader();
                            }
                        }
                    };
                    const rzp = new Razorpay(options);
                    rzp.on('payment.failed', function(resp) {
                        alert('Payment failed: ' + resp.error.description);
                        button.disabled = false;
                        button.innerHTML = origText;
                        isProcessing = false;
                        window._isRzpSubscriptionBusy = false;
                        hideModalLoader();
                    });
                    hideModalLoader();
                    rzp.open();
                } catch (err) {
                    console.error('Error:', err);
                    alert('Something went wrong. Please try again.');
                    button.disabled = false;
                    button.innerHTML = origText;
                    isProcessing = false;
                    window._isRzpSubscriptionBusy = false;
                    hideModalLoader();
                }
            });
    }
</script>
