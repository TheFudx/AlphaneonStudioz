@php
    $category = app('category')->where('status', 1);
@endphp
<style>
    /* Add or update your CSS */
    .app-links-container {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        margin-top: -70px;
        margin-right: 20px;
        height: auto;
    }

    .youtube-links-container {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        float: left;
        margin-top: -70px;
        margin-left: 20px;
        height: auto;
    }

    .store-links {
        gap: 15px;
        flex-wrap: wrap;
    }

    .store-icon {
        height: 40px;
        width: 40px;
        object-fit: contain;
    }

    .youtube-store-icon {
        height: 80px;
        width: 80px;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .youtube-store-icon:hover {
        transform: scale(1.05);
    }

    .app-icon {
        height: 70px;
        width: 70px;
        border-radius: 20%;
        border: 2px solid #fff;
        padding: 4px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .app-icon:hover {
        transform: scale(1.05);
        /* Optional hover effect */
    }

    @media (max-width: 576px) {
        .store-icon,
        .app-icon {
            height: 45px;
            width: 45px;
        }

        .youtube-store-icon {
            height: 60px;
            width: 60px;
        }

        .youtube-links-container {
            float: none;
            align-items: center;
            margin-top: 10px;
            margin-left: 0;
        }

        .app-links-container {
            align-items: center;
            margin-top: 15px;
            margin-right: 0;
        }
    }

    .appStorePlaystore {
        border-radius: 18% !important;
        vertical-align: middle;
    }

    footer .footer-section .footer-section-container .quick-links-container ul {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        padding: 0;
        margin: 0 auto;
        list-style: none;
        gap: 10px 24px;
    }

    footer .footer-section .footer-section-container .quick-links-container ul li {
        display: inline-flex;
        margin: 0 !important;
        font-family: Rubic-Regular;
    }

    footer .footer-section .footer-section-container .quick-links-container ul li a {
        color: #ffffff;
        font-size: 14px;
        white-space: nowrap;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    footer .footer-section .footer-section-container .quick-links-container ul li a:hover {
        color: #ff3a1f;
    }

    footer .footer-section .footer-section-container .quick-links-container ul li.quick-link-break {
        display: none;
        flex-basis: 100%;
        width: 100%;
        height: 0;
        margin: 0 !important;
        padding: 0 !important;
    }

    @media (max-width: 576px) {
        footer .footer-section .footer-section-container .quick-links-container ul {
            gap: 8px 14px;
            padding: 0 10px;
        }

        footer .footer-section .footer-section-container .quick-links-container ul li.quick-link-break {
            display: block;
        }

        footer .footer-section .footer-section-container .quick-links-container ul li a {
            font-size: 12px;
        }
    }

    @media (max-width: 390px) {
        footer .footer-section .footer-section-container .quick-links-container ul {
            gap: 6px 10px;
            padding: 0 4px;
        }

        footer .footer-section .footer-section-container .quick-links-container ul li a {
            font-size: 11px;
        }
    }

    @media (max-width: 340px) {
        footer .footer-section .footer-section-container .quick-links-container ul {
            gap: 5px 6px;
            padding: 0 2px;
        }

        footer .footer-section .footer-section-container .quick-links-container ul li a {
            font-size: 10px;
        }
    }

    .watch-youtube-btn {
        transition: transform 0.2s ease, opacity 0.2s ease;
    }
    .watch-youtube-btn:hover {
        transform: scale(1.03);
        opacity: 0.9;
        color: #ffffff;
    }

    /* ============================================================
   Mood Modal — base styles
   ============================================================ */
    .mood-modal-content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }

    .mood-modal-header {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .mood-modal-header .modal-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0;
        text-align: center;
    }

    .mood-modal-body {
        padding: 20px;
        /* Smooth scrolling inside modal on mobile */
        -webkit-overflow-scrolling: touch;
    }

    /* Card */
    .mood-card {
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.2s ease;
        border: none;
        /* Fixed aspect ratio so all cards are equal height */
        aspect-ratio: 3 / 4;
    }

    .mood-card:hover {
        transform: scale(1.04);
    }

    .mood-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .mood-card-overlay {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, transparent 60%);
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding: 8px;
    }

    .mood-card-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: #fff;
        margin: 0;
        text-align: center;
        line-height: 1.2;
    }

    .mood-card-link {
        text-decoration: none;
        display: block;
    }


    /* ============================================================
   Mobile small (max 576px)
   ============================================================ */
    /* @media (max-width: 576px) {

  #mood-modal .modal-dialog {
    margin: 0;
    max-width: 100%;
    height: 100%;
  }

  #mood-modal .modal-dialog-centered {
    align-items: flex-end;
  }

  .mood-modal-content {
    border-radius: 16px 16px 0 0;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
  }

  #mood-modal .modal-dialog-scrollable .modal-body {
    overflow-y: auto;
  }

  .mood-modal-header .modal-title {
    font-size: 1.1rem;
  }

  .mood-modal-body {
    padding: 12px;
  }

  .mood-card-text {
    font-size: 0.75rem;
  }
} */


    /* ============================================================
   Tablet (577px – 768px)
   ============================================================ */
    /* @media (min-width: 577px) and (max-width: 768px) {
  #mood-modal .modal-dialog {
    max-width: 95%;
    margin: auto;
  }

  .mood-modal-header .modal-title {
    font-size: 1.2rem;
  }

  .mood-modal-body {
    padding: 16px;
  }

  .mood-card-text {
    font-size: 0.8rem;
  }
} */
    /* Remove the margin-left (ms-2) from the original HTML as it's not needed with flexbox spacing */
    /* .ms-2 in the original snippet could be replaced by margin: 0 10px on the anchor tags directly. */
</style>
<div class="modal fade" id="mood-modal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0 text-center bg-dark">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body ps-5 pe-5 bg-dark">
                <h1 class="modal-title mb-5 text-center text-white" id="staticBackdropLabel">What is your Mood?</h1>
                @php $user = app('logged-in-user'); @endphp
                <div class="row row-cols-2 row-cols-sm-4 row-cols-md-5 row-cols-lg-5">
                    @foreach ($category as $cat)
                        @php
                            if ($cat->id == 1 && $cat->name === 'Khlups') {
                                $dedicatedUrl = route('khlups.view', ['tokengen' => 'default_token']);
                            } else {
                                $dedicatedUrl = route('mood.show', [
                                    'id' => App\Helpers\VideoHelper::encryptID($cat->id),
                                ]);
                            }

                            $moodHref = !$user
                                ? route('login')
                                : ($user->subscription !== 'Yes'
                                    ? route('subscribe')
                                    : $dedicatedUrl);
                        @endphp
                        <div class="col">
                            <a href="{{ $moodHref }}">
                                <div class="card text-bg-dark mb-4">
                                    <img src="{{ config('app.website_url') }}admin_panel/public/images/category/{{ $cat->image }}"
                                        class="card-img" alt="...">
                                    <div class="card-img-overlay overlay">
                                        <div class="movie-short-description p-2">
                                            <p class="card-text">{{ $cat->name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Downtime Modal -->
<div class="modal fade" id="downtimeModal" tabindex="-1" aria-labelledby="downtimeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="downtimeModalLabel">Scheduled Maintenance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Razorpay is undergoing scheduled maintenance right now.<br>
                Please try again after the maintenance window ends.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Okay</button>
            </div>
        </div>
    </div>
</div>

@include('subscription')
<footer>
    <div class="footer-section">
        <div class="footer-section-container">
            <div class="container-fluid">
                <div class="logo-container">
                    <a href="{{ url('/') }}"><img src="{{ url('/') }}/asset/images/logo.png" alt=""></a>
                </div>
                <div class="quick-links-container pb-3">
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('contact-us') }}">Contact</a></li>
                        <li><a href="{{ route('aboutus') }}">About</a></li>
                        <li class="quick-link-break"></li>
                        <li><a href="{{ route('footer.privacy') }}">Privacy & Policy</a></li>
                        <li><a href="{{ route('footer.refund') }}">Refund Policy</a></li>
                        <li><a href="{{ route('footer.terms&co') }}">Terms & Conditions</a></li>
                    </ul>
                </div>

                <div class="paragraph col-6 m-auto">
                    <p>All Rights Reserved @2025 - {{ config('app.project_name') }}</p>
                </div>

                <div class="youtube-links-container mb-4">
                    <p class="text-white mb-2">Watch on socials</p>
                    <div class="store-links d-flex align-items-center gap-2 flex-wrap">
                        <!-- YouTube -->
                        <a href="https://www.youtube.com/@AlphaNeonStudioz" target="_blank" title="YouTube">
                            <svg class="store-icon" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="28" height="28" rx="6" fill="#FF0000" />
                                <path d="M13 10.5L21 16L13 21.5V10.5Z" fill="white" />
                            </svg>
                        </a>
                        <!-- Instagram -->
                        <a href="https://www.instagram.com/alphaneonstudioz/" target="_blank" title="Instagram">
                            <svg class="store-icon" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <radialGradient id="ig-grad-footer" cx="30%" cy="107%" r="150%">
                                    <stop offset="0%" stop-color="#fdf497" />
                                    <stop offset="5%" stop-color="#fdf497" />
                                    <stop offset="45%" stop-color="#fd5949" />
                                    <stop offset="60%" stop-color="#d6249f" />
                                    <stop offset="90%" stop-color="#285AEB" />
                                </radialGradient>
                                <rect x="2" y="2" width="28" height="28" rx="6" fill="url(#ig-grad-footer)" />
                                <rect x="9.5" y="9.5" width="13" height="13" rx="3.5" stroke="white" stroke-width="1.8" fill="none" />
                                <circle cx="16" cy="16" r="3" stroke="white" stroke-width="1.8" fill="none" />
                                <circle cx="19.5" cy="12.5" r="0.9" fill="white" />
                            </svg>
                        </a>
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/people/AlphaNeon-Studioz/61558053870790/" target="_blank" title="Facebook">
                            <svg class="store-icon" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="28" height="28" rx="6" fill="#1877F2" />
                                <path d="M19.0486 27.3346V18.5757H21.4312L21.7844 15.422H19.0486V13.7284C19.0486 12.8589 19.236 12.335 20.4054 12.335C21.486 12.335 22.4761 12.3444 22.4761 12.3444L22.3996 9.39943C22.3996 9.39943 21.4287 9.27707 20.1157 9.27707C16.8727 9.27707 15.4653 11.2209 15.4653 13.3376V15.422H13.0828V18.5757H15.4653V27.3346H19.0486Z" fill="white" />
                            </svg>
                        </a>
                        <!-- X (Twitter) -->
                        <a href="https://x.com/AlphaStudioz_" target="_blank" title="X (Twitter)">
                            <svg class="store-icon" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="28" height="28" rx="6" fill="#000000" stroke="#444" stroke-width="1" />
                                <path d="M21.5375 7.98438H24.758L17.723 16.0454L26 27.0164H19.52L14.441 20.3639L8.636 27.0164H5.4125L12.9365 18.3914L5 7.98588H11.645L16.229 14.0654L21.5375 7.98438ZM20.405 25.0844H22.19L10.67 9.81588H8.756L20.405 25.0844Z" fill="white" />
                            </svg>
                        </a>
                        <!-- LinkedIn -->
                        <a href="https://www.linkedin.com/company/103164376/admin/feed/posts/?feedType=following" target="_blank" title="LinkedIn">
                            <svg class="store-icon" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="28" height="28" rx="6" fill="#0A66C2" />
                                <path d="M9.8436 24.3321V10.8381H5.26986V24.3321H9.8436ZM7.55732 8.99459C9.15227 8.99459 10.145 7.95841 10.145 6.66353C10.1153 5.33945 9.15233 4.33203 7.58759 4.33203C6.0231 4.33203 5 5.33947 5 6.66353C5 7.95847 5.99253 8.99459 7.52744 8.99459H7.55718H7.55732ZM12.3752 24.3321H16.9489V16.7964C16.9489 16.3931 16.9786 15.9902 17.0994 15.7019C17.43 14.8961 18.1826 14.0615 19.446 14.0615C21.1011 14.0615 21.7632 15.299 21.7632 17.113V24.332H26.3367V16.5946C26.3367 12.4498 24.0802 10.5213 21.071 10.5213C18.6036 10.5213 17.5203 11.8737 16.9185 12.7948H16.949V10.8378H12.3753C12.4353 12.104 12.3753 24.3318 12.3753 24.3318L12.3752 24.3321Z" fill="white" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="app-links-container mb-4">
                    <p class="text-white mb-2">Download our App</p>
                    <div class="store-links d-flex align-items-center gap-3 flex-wrap">
                        <!-- App Icon -->
                        <img src="{{ url('/') }}/asset/icons/alphaApp.jpg" class="app-icon"
                            alt="{{ config('app.project_name') }} App Icon">
                        <!-- Google Play -->
                        <a href="https://play.google.com/store/apps/details?id=com.{{ config('app.project_name') }}.alphaneonstudioz"
                            target="_blank">
                            <img src="{{ url('/') }}/asset/icons/play-store.png" class="store-icon"
                                alt="Google Play Store">
                        </a>

                        <!-- Apple Store -->
                        <a href="https://apps.apple.com/app/id6723896639" target="_blank">
                            <img src="{{ url('/') }}/asset/icons/app-store.png" class="store-icon"
                                alt="Apple App Store">
                        </a>


                    </div>
                </div>
                <div style="clear: both;"></div>
                {{-- <div class="block-des">
                    <div class="row">
                        <div class="col-md-5 p-0">
                            <div class="black-block-start"></div>
                        </div>
                        <div class="col-md-2">
                            <div class="social-links" style="min-height: 35px;"></div>
                        </div>
                        <div class="col-md-5 p-0">
                            <div class="black-block-end"></div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</footer>
</main>
{{-- <script src="{{ URL::to('asset/js/jquery.min.js') }}"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js?v={{ time() }}"></script>
<script src="{{ URL::to('asset/js/jquery.validate.min.js') }}"></script>
<script src="{{ URL::to('/asset/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::to('asset/js/popper.min.js') }}"></script>
<script src="{{ URL::to('asset/js/sweetalert2@11.js') }}"></script>
<style>
    /* Global Dark Website Theme for SweetAlert2 Popups */
    .swal2-popup {
        background-color: #1a1a1a !important;
        color: #ffffff !important;
        border: 1px solid #333333 !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7) !important;
    }
    .swal2-title {
        color: #ffffff !important;
    }
    .swal2-html-container, .swal2-content {
        color: #d1d5db !important;
    }
    .swal2-confirm {
        background-color: #FF3A1F !important;
        color: #ffffff !important;
        border-radius: 8px !important;
        padding: 10px 24px !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 12px rgba(255, 58, 31, 0.3) !important;
    }
    .swal2-confirm:hover {
        background-color: #e0321a !important;
    }
    .swal2-icon.swal2-info {
        border-color: #FF3A1F !important;
        color: #FF3A1F !important;
    }
    .swal2-icon.swal2-info .swal2-icon-content {
        color: #FF3A1F !important;
    }
    .swal2-icon.swal2-error {
        border-color: #FF3A1F !important;
        color: #FF3A1F !important;
    }
    .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
        background-color: #FF3A1F !important;
    }
</style>
<script src="{{ URL::to('asset/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::to('asset/js/owl.carousel.min.js?v=' . uniqid()) }}"></script>
<script src="{{ URL::to('asset/js/aos.js') }}"></script>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5792165796361069">
</script>
<script src="{{ URL::to('/asset/js/toastr.min.js') }}"></script>
<script src="{{ URL::to('asset/js/script.js?v=' . uniqid()) }}"></script>
<script src="{{ URL::to('asset/js/firebase-app-compat.js') }}"></script>
<script src="{{ URL::to('asset/js/firebase-messaging-compat.js') }}"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

{{-- <script src="https://checkout.razorpay.com/v1/checkout.js"></script> --}}

<script>
  
    const razorpayDowntimeWindows = [{
            start: new Date("2025-08-07T01:00:00+05:30"),
            end: new Date("2025-08-07T02:00:00+05:30"),
            message: "7th August 2025, 1:00 AM to 2:00 AM IST"
        },
        {
            start: new Date("2025-08-13T01:00:00+05:30"),
            end: new Date("2025-08-13T01:30:00+05:30"),
            message: "13th August 2025, 1:00 AM to 1:30 AM IST"
        },
        {
            start: new Date("2025-08-19T01:00:00+05:30"),
            end: new Date("2025-08-19T02:00:00+05:30"),
            message: "19th August 2025, 1:00 AM to 2:00 AM IST"
        },
        {
            start: new Date("2025-08-19T03:30:00+05:30"),
            end: new Date("2025-08-19T04:00:00+05:30"),
            message: "19th August 2025, 3:30 AM to 4:00 AM IST"
        }
    ];


    function getCurrentDowntimeWindow() {
        const now = new Date();
        const nowIST = new Date(now.toLocaleString("en-US", {
            timeZone: "Asia/Kolkata"
        }));

        return razorpayDowntimeWindows.find(({
            start,
            end
        }) => nowIST >= start && nowIST <= end);
    }
</script>
<script>
    let isAnySubscriptionProcessing = false;

    const showLoader = () => document.getElementById('loadingOverlay').style.display = 'flex';
    const hideLoader = () => document.getElementById('loadingOverlay').style.display = 'none';

    if (!window._rzpListenerAttached) {
        window._rzpListenerAttached = true;

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

            const currentDowntime = getCurrentDowntimeWindow();
            if (currentDowntime) {
                document.querySelector('#downtimeModal .modal-body').innerHTML = `
                                Razorpay is undergoing scheduled maintenance right now.<br>
                                <strong>Maintenance Window:</strong><br>${currentDowntime.message}<br>
                                Please try again after the window ends.
                            `;

                const modal = new bootstrap.Modal(document.getElementById('downtimeModal'));
                modal.show();
                return;
            }

            if (isAnySubscriptionProcessing || window._isRzpSubscriptionBusy) return;
            isAnySubscriptionProcessing = true;
            window._isRzpSubscriptionBusy = true;
            showLoader();

            const form = button.closest('form');
            const amount = button.dataset.amount;
            const planId = button.dataset.planId;
            const packageId = button.dataset.packageId;

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

                if (response.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }

                const data = await response.json();

                if (data.unauthenticated || data.redirect) {
                    window.location.href = data.redirect || "{{ route('login') }}";
                    return;
                }

                if (!data.subscription_id) {
                    alert("⚠️ Unable to create subscription. Please try again later.");
                    isAnySubscriptionProcessing = false;
                    hideLoader();
                    return;
                }

                const prefillObj = {};
                const uName = "{{ auth()->check() && auth()->user()->name ? auth()->user()->name : '' }}".trim();
                const uEmail = "{{ auth()->check() && auth()->user()->email ? auth()->user()->email : '' }}".trim();
                const rawContact = "{{ auth()->check() && auth()->user()->mobile_no ? auth()->user()->mobile_no : '' }}";
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
                        document.getElementById('loader-overlay').style.display = 'block';
                        if (form) {
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

                            form.submit();
                        }
                    },
                    theme: {
                        color: "#B01803"
                    },
                    modal: {
                        ondismiss: () => {
                            isAnySubscriptionProcessing = false;
                            window._isRzpSubscriptionBusy = false;
                            hideLoader();
                        }
                    }
                };

                const rzp = new Razorpay(options);
                rzp.on('payment.failed', function(resp) {
                    let desc = resp.error && resp.error.description ? resp.error.description : 'Payment failed.';
                    if (desc.includes('Please login') || desc.includes('failed')) {
                        desc = 'Payment could not be completed. If prompted by Razorpay, please enter the OTP or try another payment option (Card / NetBanking / UPI QR).';
                    }
                    alert(desc);
                    isAnySubscriptionProcessing = false;
                    window._isRzpSubscriptionBusy = false;
                    hideLoader();
                });
                rzp.open();
                hideLoader();
            } catch (error) {
                console.error('Error creating subscription:', error);
                alert("Something went wrong. Please check your internet and try again.");
                isAnySubscriptionProcessing = false;
                window._isRzpSubscriptionBusy = false;
                hideLoader();
            }
        });
    }
</script>

{{-- <script>
    // personal firebase project
    // const firebaseConfig = {
    //     apiKey: "AIzaSyAHtDt6aE4YIKLTJdwySCguuAqle59RNdU",
    //     authDomain: "{{ config('app.project_name') }}-48a4b.firebaseapp.com",
    //     projectId: "{{ config('app.project_name') }}-48a4b",
    //     storageBucket: "{{ config('app.project_name') }}-48a4b.firebasestorage.app",
    //     messagingSenderId: "962385260494",
    //     appId: "1:962385260494:web:e371cefb7d77a37ee8459f",
    //     measurementId: "G-9KP78JB4J0"
    // };

    // live firebase project
    const firebaseConfig = {
        apiKey: "AIzaSyD59IArjGi3kWKb4YAlj0lO88bX3r25wl4",
        authDomain: "{{ config('app.project_name') }}-425304.firebaseapp.com",
        projectId: "{{ config('app.project_name') }}-425304",
        storageBucket: "{{ config('app.project_name') }}-425304.firebasestorage.app",
        messagingSenderId: "1070377629333",
        appId: "1:1070377629333:web:3cb9bde2765a40dc750d9c",
        measurementId: "G-335VR5Z4C8"
    };


    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    messaging
        .requestPermission()
        .then(() => {
            console.log("Notification permission granted.");
            return messaging.getToken({
                // vapidKey: "BD5CP2v-FGRnktwSKk8JV_fQC85Q4phXefiP_fXv_YYeSk6Jn9Fs-v9WlRBtrI7bHnZrGpeYytiFxUKqmaYZNj8"
                vapidKey: "BFpXBa2Nws2reNU7a4HzCjTjsB9hk5-Cr0SPHcSPyzirdg1o0n64vT3jONJ8quMhdp9OyGDDJBRGdyfFXS3h8nc"
            });
        })
        .then((currentToken) => {
            if (currentToken) {
                console.log("FCM Token:", currentToken);

                // ✅ Now send the token to your Laravel backend
                fetch("{{ config('app.website_url') }}UAT/public/api/save-token", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            device_token: currentToken
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Token saved on server:", data);
                    })
                    .catch(error => {
                        console.error("Error sending token to server:", error);
                    });

            } else {
                console.log("No registration token available.");
            }
        })
        .catch((err) => {
            console.error("An error occurred while retrieving token.", err);
        });
</script>  --}}
<script>
    var user_id = "{{$user ? $user->id : ''}}";
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);

        if (params.get('open_modal') === 'subscription') {
            openSubscriptionModal();
        }

        const subModalEl = document.getElementById('subscriptionModal');
        if (subModalEl) {
            subModalEl.addEventListener('hidden.bs.modal', function () {
                document.querySelectorAll('.modal-backdrop').forEach(function(b) {
                    b.remove();
                });
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        }
    });

    function openSubscriptionModal() {
        let el = document.getElementById('subscriptionModal');
        if (el) {
            let modal = bootstrap.Modal.getOrCreateInstance(el);
            modal.show();
        }
    }
</script>
<script type="module">
    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
    import {
        getMessaging,
        getToken
    } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js";

    const firebaseConfig = {
        apiKey: "AIzaSyD59IArjGi3kWKb4YAlj0lO88bX3r25wl4",
        authDomain: "{{ config('app.project_name') }}-425304.firebaseapp.com",
        projectId: "{{ config('app.project_name') }}-425304",
        storageBucket: "{{ config('app.project_name') }}-425304.firebasestorage.app",
        messagingSenderId: "1070377629333",
        appId: "1:1070377629333:web:3cb9bde2765a40dc750d9c",
        measurementId: "G-335VR5Z4C8"
    };

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/firebase-messaging-sw.js')
                    .then((registration) => {

                        getToken(messaging, {
                            vapidKey: "BFpXBa2Nws2reNU7a4HzCjTjsB9hk5-Cr0SPHcSPyzirdg1o0n64vT3jONJ8quMhdp9OyGDDJBRGdyfFXS3h8nc",
                            serviceWorkerRegistration: registration
                        }).then((currentToken) => {

                            if (currentToken) {
                                console.log("FCM Token:", currentToken);
                            }

                        });

                    });
            }
            getToken(messaging, {
                vapidKey: "BFpXBa2Nws2reNU7a4HzCjTjsB9hk5-Cr0SPHcSPyzirdg1o0n64vT3jONJ8quMhdp9OyGDDJBRGdyfFXS3h8nc"
            }).then((currentToken) => {

                if (currentToken) {
                    console.log("FCM Token:", currentToken);
                    console.log("user_id:", user_id);

                    fetch("{{ config('app.website_url') }}public/api/save-token", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                device_token: currentToken,
                                user_id: user_id
                            })
                        })
                        .then(res => res.json())
                        .then(data => console.log("Token saved:", data))
                        .catch(err => console.error(err));
                }

            }).catch(err => {
                console.error("Token error:", err);
            });

        } else {
            console.log("Permission denied");
        }
    });
</script>
<script>
    const stateCityMap = {
        "Andaman and Nicobar Islands": [
            "Port Blair"
        ],
        "Andhra Pradesh": [
            "Adoni",
            "Amalapuram",
            "Anakapalle",
            "Anantapur",
            "Bapatla",
            "Bheemunipatnam",
            "Bhimavaram",
            "Bobbili",
            "Chilakaluripet",
            "Chirala",
            "Chittoor",
            "Dharmavaram",
            "Eluru",
            "Gudivada",
            "Gudur",
            "Guntakal",
            "Guntur",
            "Hindupur",
            "Jaggaiahpet",
            "Jammalamadugu",
            "Kadapa",
            "Kadiri",
            "Kakinada",
            "Kandukur",
            "Kavali",
            "Kovvur",
            "Kurnool",
            "Macherla",
            "Machilipatnam",
            "Madanapalle",
            "Mandapeta",
            "Markapur",
            "Naidupet",
            "Nagari",
            "Nandyal",
            "Narasapuram",
            "Narasaraopet",
            "Nidadavole",
            "Nuzvid",
            "Ongole",
            "Palacole",
            "Palasa Kasibugga",
            "Parvathipuram",
            "Pedana",
            "Peddapuram",
            "Pithapuram",
            "Ponnur",
            "Proddatur",
            "Punganur",
            "Puttur",
            "Rajam",
            "Rajampet",
            "Rajahmundry",
            "Ramachandrapuram",
            "Rayachoti",
            "Rayadurg",
            "Renigunta",
            "Repalle",
            "Salur",
            "Samalkot",
            "Sattenapalle",
            "Srikakulam",
            "Srikalahasti",
            "Srisailam Project (Right Flank Colony) Township",
            "Sullurpeta",
            "Tadepalligudem",
            "Tadpatri",
            "Tanuku",
            "Tenali",
            "Tirupati",
            "Tiruvuru",
            "Tuni",
            "Uravakonda",
            "Venkatagiri",
            "Vijayawada",
            "Vinukonda",
            "Visakhapatnam",
            "Vizianagaram",
            "Yemmiganur",
            "Yerraguntla"
        ],
        "Arunachal Pradesh": [
            "Naharlagun",
            "Pasighat"
        ],
        "Assam": [
            "Barpeta",
            "Bongaigaon City",
            "Dhubri",
            "Dibrugarh",
            "Diphu",
            "Goalpara",
            "Guwahati",
            "Jorhat",
            "Karimganj",
            "Lanka",
            "Lumding",
            "Mangaldoi",
            "Mankachar",
            "Margherita",
            "Mariani",
            "Marigaon",
            "Nagaon",
            "Nalbari",
            "North Lakhimpur",
            "Rangia",
            "Sibsagar",
            "Silchar",
            "Silapathar",
            "Tezpur",
            "Tinsukia"
        ],
        "Bihar": [
            "Araria",
            "Arrah",
            "Arwal",
            "Asarganj",
            "Aurangabad",
            "Bagaha",
            "Begusarai",
            "Bettiah",
            "Bhagalpur",
            "BhabUrban Agglomeration",
            "Buxar",
            "Chhapra",
            "Darbhanga",
            "Dehri-on-Sone",
            "Dumraon",
            "Forbesganj",
            "Gaya",
            "Gopalganj",
            "Hajipur",
            "Jamalpur",
            "Jamui",
            "Jehanabad",
            "Katihar",
            "Kishanganj",
            "Lakhisarai",
            "Lalganj",
            "Madhubani",
            "Madhepura",
            "Maharajganj",
            "Mahnar Bazar",
            "Makhdumpur",
            "Maner",
            "Manihari",
            "Marhaura",
            "Masaurhi",
            "Mirganj",
            "Mokameh",
            "Motihari",
            "Motipur",
            "Munger",
            "Murliganj",
            "Muzaffarpur",
            "Narkatiaganj",
            "Naugachhia",
            "Nawada",
            "Nokha",
            "Patna",
            "Piro",
            "Purnia",
            "Rafiganj",
            "Rajgir",
            "Ramnagar",
            "Raxaul Bazar",
            "Revelganj",
            "Rosera",
            "Saharsa",
            "Samastipur",
            "Sasaram",
            "Sheikhpura",
            "Sheohar",
            "Sherghati",
            "Silao",
            "Sitamarhi",
            "Siwan",
            "Sonepur",
            "Sugauli",
            "Sultanganj",
            "Supaul",
            "Warisaliganj"
        ],
        "Chandigarh": [
            "Chandigarh"
        ],
        "Chhattisgarh": [
            "Ambikapur",
            "Bhatapara",
            "Bhilai Nagar",
            "Bilaspur",
            "Chirmiri",
            "Dalli-Rajhara",
            "Dhamtari",
            "Durg",
            "Jagdalpur",
            "Korba",
            "Mahasamund",
            "Manendragarh",
            "Mungeli",
            "Naila Janjgir",
            "Raigarh",
            "Raipur",
            "Rajnandgaon",
            "Sakti",
            "Tilda Newra"
        ],
        "Dadra and Nagar Haveli": [
            "Silvassa"
        ],
        "Delhi": [
            "Delhi",
            "New Delhi"
        ],
        "Goa": [
            "Mapusa",
            "Margao",
            "Marmagao",
            "Panaji"
        ],
        "Gujarat": [
            "Adalaj",
            "Ahmedabad",
            "Amreli",
            "Anand",
            "Anjar",
            "Ankleshwar",
            "Bharuch",
            "Bhavnagar",
            "Bhuj",
            "Chhapra",
            "Deesa",
            "Dhoraji",
            "Gandhinagar",
            "Godhra",
            "Jamnagar",
            "Kadi",
            "Kapadvanj",
            "Keshod",
            "Khambhat",
            "Lathi",
            "Limbdi",
            "Lunawada",
            "Mahemdabad",
            "Mahuva",
            "Manavadar",
            "Mandvi",
            "Mangrol",
            "Mansa",
            "Mahesana",
            "Modasa",
            "Morvi",
            "Nadiad",
            "Navsari",
            "Padra",
            "Palanpur",
            "Palitana",
            "Pardi",
            "Patan",
            "Petlad",
            "Porbandar",
            "Radhanpur",
            "Rajkot",
            "Rajpipla",
            "Rajula",
            "Ranavav",
            "Rapar",
            "Salaya",
            "Sanand",
            "Savarkundla",
            "Sidhpur",
            "Sihor",
            "Songadh",
            "Surat",
            "Talaja",
            "Thangadh",
            "Tharad",
            "Umbergaon",
            "Umreth",
            "Una",
            "Unjha",
            "Upleta",
            "Vadnagar",
            "Vadodara",
            "Valsad",
            "Vapi",
            "Veraval",
            "Vijapur",
            "Viramgam",
            "Visnagar",
            "Vyara",
            "Wadhwan",
            "Wankaner"
        ],
        "Haryana": [
            "Bahadurgarh",
            "Bhiwani",
            "Charkhi Dadri",
            "Faridabad",
            "Fatehabad",
            "Gohana",
            "Gurgaon",
            "Hansi",
            "Hisar",
            "Jind",
            "Kaithal",
            "Karnal",
            "Ladwa",
            "Mandi Dabwali",
            "Mahendragarh",
            "Narwana",
            "Narnaul",
            "Palwal",
            "Panchkula",
            "Panipat",
            "Pehowa",
            "Pinjore",
            "Rania",
            "Ratia",
            "Rewari",
            "Rohtak",
            "Safidon",
            "Samalkha",
            "Sarsod",
            "Shahbad",
            "Sirsa",
            "Sohna",
            "Sonipat",
            "Taraori",
            "Thanesar",
            "Tohana",
            "Yamunanagar"
        ],
        "Himachal Pradesh": [
            "Kullu",
            "Mandi",
            "Nahan",
            "Palampur",
            "Shimla",
            "Solan",
            "Sundarnagar"
        ],
        "Himachal Praddesh": [
            "Manali"
        ],
        "Jammu and Kashmir": [
            "Anantnag",
            "Baramula",
            "Jammu",
            "KathUrban Agglomeration",
            "Punch",
            "Rajauri",
            "Sopore",
            "Srinagar",
            "Udhampur"
        ],
        "Jharkhand": [
            "Adityapur",
            "Bokaro Steel City",
            "Chaibasa",
            "Chatra",
            "Chirkunda",
            "Deoghar",
            "Dhanbad",
            "Dumka",
            "Giridih",
            "Gumia",
            "Hazaribag",
            "Jamshedpur",
            "Jhumri Tilaiya",
            "Lohardaga",
            "Madhupur",
            "Medininagar (Daltonganj)",
            "Mihijam",
            "Musabani",
            "Pakaur",
            "Patratu",
            "Phusro",
            "Ramgarh",
            "Ranchi",
            "Sahibganj",
            "Saunda",
            "Simdega",
            "Tenu dam-cum-Kathhara"
        ],
        "Karnataka": [
            "Adyar",
            "Afzalpur",
            "Arsikere",
            "Athni",
            "Ballari",
            "Belagavi",
            "Bengaluru",
            "Chikkamagaluru",
            "Davanagere",
            "Gokak",
            "Hubli-Dharwad",
            "Karwar",
            "Kolar",
            "Lakshmeshwar",
            "Lingsugur",
            "Maddur",
            "Madhugiri",
            "Madikeri",
            "Magadi",
            "Mahalingapura",
            "Malavalli",
            "Malur",
            "Mandya",
            "Mangaluru",
            "Manvi",
            "Mudalagi",
            "Mudabidri",
            "Muddebihal",
            "Mudhol",
            "Mulbagal",
            "Mundargi",
            "Mysore",
            "Nanjangud",
            "Nargund",
            "Navalgund",
            "Nelamangala",
            "Pavagada",
            "Piriyapatna",
            "Puttur",
            "Rabkavi Banhatti",
            "Ramanagaram",
            "Ramdurg",
            "Ranebennuru",
            "Ranibennur",
            "Raayachuru",
            "Robertson Pet",
            "Ron",
            "Sadalagi",
            "Sagara",
            "Sakaleshapura",
            "Sanduru",
            "Sankeshwara",
            "Saundatti-Yellamma",
            "Savanur",
            "Sedam",
            "Shahabad",
            "Shahpur",
            "Shiggaon",
            "Shikaripur",
            "Shivamogga",
            "Shrirangapattana",
            "Sidlaghatta",
            "Sindagi",
            "Sindhanur",
            "Sira",
            "Sirsi",
            "Siruguppa",
            "Srinivaspur",
            "Surapura",
            "Talikota",
            "Tarikere",
            "Tekkalakote",
            "Terdal",
            "Tiptur",
            "Tumkur",
            "Udupi",
            "Vijayapura",
            "Wadi",
            "Yadgir"
        ],
        "Kerala": [
            "Adoor",
            "Alappuzha",
            "Attingal",
            "Chalakudy",
            "Changanassery",
            "Cherthala",
            "Chittur-Thathamangalam",
            "Guruvayoor",
            "Kanhangad",
            "Kannur",
            "Kasaragod",
            "Kayamkulam",
            "Kochi",
            "Kodungallur",
            "Kollam",
            "Kottayam",
            "Koyilandy",
            "Kozhikode",
            "Kunnamkulam",
            "Malappuram",
            "Mattannur",
            "Mavelikkara",
            "Mavoor",
            "Muvattupuzha",
            "Nedumangad",
            "Neyyattinkara",
            "Nilambur",
            "Ottappalam",
            "Palakkad",
            "Palai",
            "Panamattom",
            "Panniyannur",
            "Pappinisseri",
            "Paravoor",
            "Pathanamthitta",
            "Perinthalmanna",
            "Peringathur",
            "Perumbavoor",
            "Ponnani",
            "Punalur",
            "Puthuppally",
            "Shoranur",
            "Taliparamba",
            "Thiruvananthapuram",
            "Thiruvalla",
            "Thodupuzha",
            "Thrissur",
            "Tirur",
            "Vaikom",
            "Varkala",
            "Vatakara"
        ],
        "Madhya Pradesh": [
            "Alirajpur",
            "Ashok Nagar",
            "Balaghat",
            "Bhopal",
            "Ganjbasoda",
            "Gwalior",
            "Indore",
            "Itarsi",
            "Jabalpur",
            "Lahar",
            "Maihar",
            "Malaj Khand",
            "Manasa",
            "Manawar",
            "Mandideep",
            "Mandla",
            "Mandsaur",
            "Mauganj",
            "Maharajpur",
            "Mahidpur",
            "Mhow Cantonment",
            "Mhowgaon",
            "Morena",
            "Multai",
            "Mundi",
            "Nagda",
            "Nainpur",
            "Narsinghgarh",
            "Narsinghgarh",
            "Neemuch",
            "Nepanagar",
            "Niwari",
            "Nowgong",
            "Nowrozabad (Khodargama)",
            "Pachore",
            "Pali",
            "Panagar",
            "Pandhurna",
            "Panna",
            "Pasan",
            "Phulabani",
            "Pipariya",
            "Pithampur",
            "Porsa",
            "Prithvipur",
            "Raghavgarh-Vijaypur",
            "Rahatgarh",
            "Raisen",
            "Rajgarh",
            "Ratlam",
            "Rau",
            "Rehli",
            "Rewa",
            "Sabalgarh",
            "Sagar",
            "Sanawad",
            "Sarangpur",
            "Sarni",
            "Satna",
            "Sausar",
            "Sehore",
            "Sendhwa",
            "Seoni",
            "Seoni-Malwa",
            "Shahdol",
            "Shajapur",
            "Shamgarh",
            "Sheopur",
            "Shivpuri",
            "Shujalpur",
            "Sidhi",
            "Sihora",
            "Singrauli",
            "Sironj",
            "Sohagpur",
            "Tarana",
            "Tikamgarh",
            "Umaria",
            "Ujjain",
            "Vidisha",
            "Vijaypur",
            "Wara Seoni"
        ],
        "Maharashtra": [
            "Achalpur",
            "Ahmednagar",
            "Akola",
            "Akot",
            "Amalner",
            "Ambejogai",
            "Amravati",
            "Anjangaon",
            "Arvi",
            "Aurangabad",
            "Bhiwandi",
            "Dhule",
            "Ichalkaranji",
            "Kalyan-Dombivali",
            "Karjat",
            "Latur",
            "Loha",
            "Lonar",
            "Lonavla",
            "Mahad",
            "Malegaon",
            "Malkapur",
            "Manjlegaon",
            "Manmad",
            "Manwath",
            "Mangalvedhe",
            "Mangrulpir",
            "Mehkar",
            "Mira-Bhayandar",
            "Morshi",
            "Murtijapur",
            "Mumbai",
            "Mukhed",
            "Mul",
            "Nanded-Waghala",
            "Nandgaon",
            "Nandura",
            "Nandurbar",
            "Narkhed",
            "Nashik",
            "Nawapur",
            "Nilanga",
            "Osmanabad",
            "Ozar",
            "Pachora",
            "Paithan",
            "Palghar",
            "Pandharkaoda",
            "Pandharpur",
            "Panvel",
            "Parbhani",
            "Parli",
            "Partur",
            "Patur",
            "Pauni",
            "Pen",
            "Phaltan",
            "Purna",
            "Pune",
            "Pusad",
            "Rahuri",
            "Rajura",
            "Ramtek",
            "Ratnagiri",
            "Raver",
            "Risod",
            "Sailu",
            "Sangamner",
            "Sangli",
            "Sangole",
            "Sasvad",
            "Satana",
            "Satara",
            "Savner",
            "Sawantwadi",
            "Shahade",
            "Shegaon",
            "Shendurjana",
            "Shirdi",
            "Shirpur-Warwade",
            "Shirur",
            "Shrigonda",
            "Shrirampur",
            "Sillod",
            "Sinnar",
            "Solapur",
            "Soyagaon",
            "Talegaon Dabhade",
            "Talode",
            "Tasgaon",
            "Thane",
            "Tirora",
            "Tuljapur",
            "Tumsar",
            "Uchgaon",
            "Udgir",
            "Umarga",
            "Umred",
            "Umarkhed",
            "Uran",
            "Uran Islampur",
            "Vaijapur",
            "Vasai-Virar",
            "Vita",
            "Vadgaon Kasba",
            "Wadgaon Road",
            "Wai",
            "Wani",
            "Warora",
            "Warud",
            "Washim",
            "Yavatmal",
            "Yawal",
            "Yevla"
        ],
        "Manipur": [
            "Imphal",
            "Lilong",
            "Mayang Imphal",
            "Thoubal"
        ],
        "Meghalaya": [
            "Nongstoin",
            "Shillong",
            "Tura"
        ],
        "Mizoram": [
            "Aizawl",
            "Lunglei",
            "Saiha"
        ],
        "Nagaland": [
            "Dimapur",
            "Kohima",
            "Mokokchung",
            "Tuensang",
            "Wokha",
            "Zunheboto"
        ],
        "Odisha": [
            "Baleshwar Town",
            "Balangir",
            "Barbil",
            "Bargarh",
            "Baripada Town",
            "Bhadrak",
            "Bhawanipatna",
            "Bhubaneswar",
            "Brahmapur",
            "Byasanagar",
            "Cuttack",
            "Dhenkanal",
            "Jatani",
            "Jharsuguda",
            "Kendrapara",
            "Kendujhar",
            "Malkangiri",
            "Nabarangapur",
            "Paradip",
            "Parlakhemundi",
            "Pattamundai",
            "Puri",
            "Rajagangapur",
            "Rairangpur",
            "Raurkela",
            "Rayagada",
            "Sambalpur",
            "Soro",
            "Sunabeda",
            "Sundargarh",
            "Talcher",
            "Tarbha",
            "Titlagarh"
        ],
        "Puducherry": [
            "Karaikal",
            "Mahe",
            "Pondicherry",
            "Yanam"
        ],
        "Punjab": [
            "Amritsar",
            "Barnala",
            "Batala",
            "Bathinda",
            "Dhuri",
            "Faridkot",
            "Fazilka",
            "Firozpur",
            "Firozpur Cantt.",
            "Gobindgarh",
            "Gurdaspur",
            "Hoshiarpur",
            "Jagraon",
            "Jalandhar",
            "Jalandhar Cantt.",
            "Kapurthala",
            "Kharar",
            "Khanna",
            "Kot Kapura",
            "Longowal",
            "Ludhiana",
            "Malerkotla",
            "Malout",
            "Mansa",
            "Moga",
            "Mohali",
            "Morinda, India",
            "Mukerian",
            "Muktsar",
            "Nabha",
            "Nakodar",
            "Nangal",
            "Nawanshahr",
            "Pathankot",
            "Patiala",
            "Patti",
            "Pattran",
            "Phagwara",
            "Phillaur",
            "Qadian",
            "Raikot",
            "Rajpura",
            "Rampura Phul",
            "Rupnagar",
            "Samana",
            "Sangrur",
            "Sirhind Fatehgarh Sahib",
            "Sujanpur",
            "Sunam",
            "Talwara",
            "Tarn Taran",
            "Urmar Tanda",
            "Zira",
            "Zirakpur"
        ],
        "Rajasthan": [
            "Ajmer",
            "Alwar",
            "Barmer",
            "Bharatpur",
            "Bhilwara",
            "Bikaner",
            "Jaipur",
            "Jodhpur",
            "Lachhmangarh",
            "Ladnu",
            "Lakheri",
            "Lalsot",
            "Losal",
            "Makrana",
            "Malpura",
            "Mandalgarh",
            "Mandawa",
            "Mangrol",
            "Merta City",
            "Mount Abu",
            "Nadbai",
            "Nagar",
            "Nagaur",
            "Nasirabad",
            "Nathdwara",
            "Neem-Ka-Thana",
            "Nimbahera",
            "Nohar",
            "Nokha",
            "Pali",
            "Phalodi",
            "Phulera",
            "Pilani",
            "Pilibanga",
            "Pipar City",
            "Pindwara",
            "Prantij",
            "Pratapgarh",
            "Rajakhera",
            "Rajaldesar",
            "Rajgarh (Alwar)",
            "Rajgarh (Churu)",
            "Rajsamand",
            "Ramganj Mandi",
            "Ramngarh",
            "Raisinghnagar",
            "Ratangarh",
            "Rawatbhata",
            "Rawatsar",
            "Reengus",
            "Sadri",
            "Sadulpur",
            "Sadulshahar",
            "Sagwara",
            "Sambhar",
            "Sanchore",
            "Sangaria",
            "Sardarshahar",
            "Sawai Madhopur",
            "Shahpura",
            "Shahpura",
            "Sheoganj",
            "Sikar",
            "Sirohi",
            "Sojat",
            "Sri Madhopur",
            "Sujangarh",
            "Sumerpur",
            "Suratgarh",
            "Takhatgarh",
            "Taranagar",
            "Todabhim",
            "Todaraisingh",
            "Tonk",
            "Udaipur",
            "Udaipurwati",
            "Vijainagar, Ajmer"
        ],
        "Tamil Nadu": [
            "Arakkonam",
            "Aruppukkottai",
            "Chennai",
            "Coimbatore",
            "Erode",
            "Gobichettipalayam",
            "Kancheepuram",
            "Karur",
            "Madurai",
            "Nagapattinam",
            "Nagercoil",
            "Namakkal",
            "Namagiripettai",
            "Nandivaram-Guduvancheri",
            "Nellikuppam",
            "Neyveli (TS)",
            "O' Valley",
            "Oddanchatram",
            "Pacode",
            "Padmanabhapuram",
            "Palani",
            "Palladam",
            "Pallapatti",
            "Pallikonda",
            "Panruti",
            "Paramakudi",
            "Parangipettai",
            "Pattukkottai",
            "Perambalur",
            "Peravurani",
            "Periyakulam",
            "Periyasemur",
            "Pernampattu",
            "Polur",
            "Pollachi",
            "Ponneri",
            "Pudukkottai",
            "Pudupattinam",
            "Puliyankudi",
            "Punjaipugalur",
            "Rajapalayam",
            "Ramanathapuram",
            "Ranipet",
            "Rasipuram",
            "Rameshwaram",
            "Salem",
            "Sankarankovil",
            "Sankari",
            "Sathyamangalam",
            "Sattur",
            "Shenkottai",
            "Sholavandan",
            "Sholingur",
            "Sirkali",
            "Sivaganga",
            "Sivagiri",
            "Sivakasi",
            "Surandai",
            "Suriyampalayam",
            "Tindivanam",
            "Thammampatti",
            "Thanjavur",
            "Tharamangalam",
            "Tharangambadi",
            "Theni Allinagaram",
            "Thiruvarur",
            "Thirumangalam",
            "Thiruppuvanam",
            "Thiruthuraipoondi",
            "Thiruvallur",
            "Tiruchendur",
            "Tiruchengode",
            "Tiruchirappalli",
            "Tirukalukundram",
            "Tirukkoyilur",
            "Tirunelveli",
            "Tirupathur",
            "Tirupathur",
            "Tiruppur",
            "Tiruttani",
            "Tiruvannamalai",
            "Tiruvethipuram",
            "Tittakudi",
            "Thuraiyur",
            "Udumalaipettai",
            "Udhagamandalam",
            "Unnamalaikadai",
            "Usilampatti",
            "Uthamapalayam",
            "Uthiramerur",
            "Vadalur",
            "Vadakkuvalliyur",
            "Vadipatti",
            "Valparai",
            "Vandavasi",
            "Vaniyambadi",
            "Vedaranyam",
            "Vellakoil",
            "Vellore",
            "Vikramasingapuram",
            "Viluppuram",
            "Virudhachalam",
            "Virudhunagar",
            "Viswanatham"
        ],
        "Telangana": [
            "Adilabad",
            "Bellampalle",
            "Bhadrachalam",
            "Bhainsa",
            "Bhongir",
            "Bodhan",
            "Farooqnagar",
            "Gadwal",
            "Hyderabad",
            "Jagtial",
            "Jangaon",
            "Kagaznagar",
            "Kamareddy",
            "Karimnagar",
            "Khammam",
            "Koratla",
            "Kothagudem",
            "Kyathampalle",
            "Mahbubnagar",
            "Mancherial",
            "Mandamarri",
            "Manuguru",
            "Medak",
            "Miryalaguda",
            "Nagarkurnool",
            "Narayanpet",
            "Nirmal",
            "Nizamabad",
            "Palwancha",
            "Ramagundam",
            "Sadasivpet",
            "Sangareddy",
            "Siddipet",
            "Sircilla",
            "Suryapet",
            "Tandur",
            "Vikarabad",
            "Wanaparthy",
            "Warangal",
            "Yellandu"
        ],
        "Tripura": [
            "Agartala",
            "Belonia",
            "Dharmanagar",
            "Kailasahar",
            "Khowai",
            "Pratapgarh",
            "Udaipur"
        ],
        "Uttar Pradesh": [
            "Achhnera",
            "Agra",
            "Aligarh",
            "Allahabad",
            "Amroha",
            "Azamgarh",
            "Bahraich",
            "Chandausi",
            "Etawah",
            "Fatehpur Sikri",
            "Firozabad",
            "Hapur",
            "Hardoi",
            "Jhansi",
            "Kalpi",
            "Kanpur",
            "Khair",
            "Lal Gopalganj Nindaura",
            "Lalganj",
            "Lakhimpur",
            "Lar",
            "Lalitpur",
            "Laharpur",
            "Loni",
            "Lucknow",
            "Mathura",
            "Meerut",
            "Mirzapur",
            "Modinagar",
            "Moradabad",
            "Nagina",
            "Najibabad",
            "Nakur",
            "Nanpara",
            "Naraura",
            "Nautanwa",
            "Nawabganj",
            "Nehtaur",
            "Niwai",
            "Noida",
            "Noorpur",
            "Obra",
            "Orai",
            "Padrauna",
            "Palia Kalan",
            "Parasi",
            "Phulpur",
            "Pihani",
            "Pilkhuwa",
            "Pilibhit",
            "Powayan",
            "Pukhrayan",
            "Puranpur",
            "PurqUrban Agglomerationzi",
            "Purwa",
            "Rae Bareli",
            "Rampur",
            "Rampur Maniharan",
            "Rasra",
            "Rath",
            "Renukoot",
            "Reoti",
            "Robertsganj",
            "Rudauli",
            "Rudrapur",
            "Sadabad",
            "Saharanpur",
            "Sahaspur",
            "Sahawar",
            "Sahjanwa",
            "Saidpur",
            "Sandi",
            "Sandila",
            "Samdhan",
            "Sambhal",
            "Sardhana",
            "Sahaswan",
            "Seohara",
            "Shahabad, Hardoi",
            "Shahabad, Rampur",
            "Shahganj",
            "Shahjahanpur",
            "Shamli",
            "Shamsabad, Agra",
            "Shamsabad, Farrukhabad",
            "Sherkot",
            "Shikarpur, Bulandshahr",
            "Shikohabad",
            "Shishgarh",
            "Siana",
            "Sikandra Rao",
            "Sikanderpur",
            "Sikandrabad",
            "Sirsi",
            "Sirsaganj",
            "Sitapur",
            "Soron",
            "SUrban Agglomerationr",
            "Sultanpur",
            "Sumerpur",
            "Tanda",
            "Thakurdwara",
            "Thana Bhawan",
            "Tilhar",
            "Tirwaganj",
            "Tundla",
            "Tulsipur",
            "Ujhani",
            "Unnao",
            "Utraula",
            "Varanasi",
            "Vrindavan",
            "Warhapur",
            "Zaidpur",
            "Zamania"
        ],
        "Uttarakhand": [
            "Bageshwar",
            "Dehradun",
            "Haldwani-cum-Kathgodam",
            "Hardwar",
            "Kashipur",
            "Manglaur",
            "Mussoorie",
            "Nagla",
            "Nainital",
            "Pauri",
            "Pithoragarh",
            "Ramnagar",
            "Rishikesh",
            "Roorkee",
            "Rudrapur",
            "Sitarganj",
            "Srinagar",
            "Tehri"
        ],
        "West Bengal": [
            "Adra",
            "AlipurdUrban Agglomerationr",
            "Arambagh",
            "Asansol",
            "Baharampur",
            "Balurghat",
            "Bankura",
            "Darjiling",
            "English Bazar",
            "Gangarampur",
            "Habra",
            "Hugli-Chinsurah",
            "Jalpaiguri",
            "Jhargram",
            "Kalimpong",
            "Kharagpur",
            "Kolkata",
            "Mainaguri",
            "Malda",
            "Mathabhanga",
            "Medinipur",
            "Memari",
            "Monoharpur",
            "Murshidabad",
            "Nabadwip",
            "Naihati",
            "Panchla",
            "PandUrban Agglomeration",
            "Paschim Punropara",
            "Purulia",
            "Raghunathganj",
            "Raghunathpur",
            "Raiganj",
            "Rampurhat",
            "Ranaghat",
            "Sainthia",
            "Santipur",
            "Siliguri",
            "Sonamukhi",
            "Srirampore",
            "Suri",
            "Taki",
            "Tamluk",
            "Tarakeswar"
        ]
    };
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded yet!');
            return;
        }

        const stateSelect = $('#state');
        const citySelect = $('#city');

        // Initialize Select2
        stateSelect.select2({
            placeholder: "Select State",
            width: '100%',
            minimumResultsForSearch: 0
        });

        citySelect.select2({
            placeholder: "Select City",
            width: '100%',
            minimumResultsForSearch: 0
        });

        // Populate State dropdown
        Object.keys(stateCityMap).forEach(function(state) {
            const option = new Option(state, state);
            stateSelect.append(option);
        });

        // Populate City dropdown when State changes
        stateSelect.on('change', function() {
            const cities = stateCityMap[this.value] || [];
            citySelect.empty().append(new Option("Select City", ""));

            cities.forEach(function(city) {
                const option = new Option(city, city);
                citySelect.append(option);
            });

            citySelect.trigger('change.select2'); // Refresh Select2
        });

        // Trigger once on page load if state already selected
        // if (selectedState) {
        //     stateSelect.trigger('change');
        // }
    });
</script>
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip({
            container: 'body',
            placement: 'top',
            customClass: 'my-custom-tooltip'
        });
    });
</script>
<noscript id="deferred-styles">
    <link rel="stylesheet" href="{{ URL::to('/asset/css/owl.carousel.min.css') }}">
</noscript>
<script>
    const loadDeferredStyles = () => {
        const addStyles = document.getElementById("deferred-styles");
        const div = document.createElement("div");
        div.innerHTML = addStyles.textContent;
        document.body.appendChild(div);
        addStyles.remove();
    };
    window.requestAnimationFrame ?
        requestAnimationFrame(() => setTimeout(loadDeferredStyles, 0)) :
        window.addEventListener("load", loadDeferredStyles);
</script>
<script>
    navigator.brave?.isBrave?.().then(function(result) {
        console.log("Is Brave:", result);
        $("#is_brave").val(result ? 1 : 0); // Convert true/false to 1/0 if needed
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lazyImages = document.querySelectorAll("img.lazy");
        const lazyVideos = document.querySelectorAll("video[data-lazy='true']");

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;

                    // Lazy Load Image
                    if (el.tagName.toLowerCase() === "img" && el.dataset.src) {
                        el.src = el.dataset.src;
                        el.classList.remove("lazy");
                        el.removeAttribute("data-src");
                    }

                    // Lazy Load Video
                    if (el.tagName.toLowerCase() === "video") {
                        let hasLazySource = false;
                        el.querySelectorAll("source").forEach(source => {
                            if (source.dataset.src) {
                                source.src = source.dataset.src;
                                source.removeAttribute("data-src");
                                hasLazySource = true;
                            }
                        });

                        if (hasLazySource) {
                            el.load();
                            el.play().catch(() => {});
                            el.removeAttribute("data-lazy");
                        }
                    }

                    observer.unobserve(el);
                }
            });
        }, {
            threshold: 0.25
        }); // when 25% visible

        lazyImages.forEach(img => observer.observe(img));
        lazyVideos.forEach(video => observer.observe(video));
    });
</script>
@yield('scripts')
</body>

</html>
