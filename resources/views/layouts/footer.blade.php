@php
    $category = app('category');
@endphp
<style>
    /* Add or update your CSS */
    .app-links-container {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        margin-top: -70px;
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
    }

    .appStorePlaystore {
        border-radius: 18% !important;
        vertical-align: middle;
    }

    /* You might want to adjust the original li styles if they conflict */
    /* For example, if you want your app store links to be separate from quick links */
    footer .footer-section .footer-section-container .quick-links-container ul li {
        /* Existing styles */
        display: inline-block;
        margin: 0px 20px;
        font-family: Rubic-Regular;
    }

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
                <div class="row row-cols-2 row-cols-sm-4 row-cols-md-5 row-cols-lg-5 ">
                    @foreach ($category as $cat)
                        <div class="col">
                            @php
                                if ($cat->id == 1 && $cat->name === 'Khlups') {
                                    $route = route('khlups.view', ['tokengen' => 'default_token']);
                                } else {
                                    $route = route('mood.show', ['id' => App\Helpers\VideoHelper::encryptID($cat->id)]);
                                }
                            @endphp
                            <a href="{{ $route }} ">
                                <div class="card text-bg-dark mb-4">
                                    <img src="https://alphastudioz.in/admin_panel/public/images/category/{{ $cat->image }}"
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
<footer>
    <div class="footer-section">
        <div class="footer-section-container">
            <div class="container-fluid">
                <div class="logo-container">
                    <img src="{{ url('/') }}/asset/images/logo.png" alt="">
                </div>
                <div class="quick-links-container pb-3">
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('contact-us') }}">Contact</a></li>
                        <li><a href="{{ route('aboutus') }}">About</a></li>
                        <li><a href="{{ route('footer.privacy') }}">Privacy & Policy</a></li>
                        <li><a href="{{ route('footer.refund') }}">Refund Policy</a></li>
                        <li><a href="{{ route('footer.terms&co') }}">Terms & Conditions</a></li>
                    </ul>
                </div>

                <div class="paragraph col-6 m-auto">
                    <p>All Rights Reserved @2025 - Alphastudioz</p>
                </div>
                <div class="app-links-container mb-3">
                    <p class="text-white mb-2">Download our App</p>
                    <div class="store-links d-flex align-items-center gap-3 flex-wrap">
                          <!-- App Icon -->
                        <img src="{{ url('/') }}/asset/icons/alphaApp.jpg" class="app-icon"
                            alt="Alphastudioz App Icon">
                        <!-- Google Play -->
                        <a href="https://play.google.com/store/apps/details?id=com.alphastudioz.alphaneonstudioz"
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
                <div class="block-des">
                    <div class="row">
                        <div class="col-md-5 p-0">
                            <div class="black-block-start"></div>
                        </div>
                        <div class="col-md-2">
                            <div class="social-links">
                                <ul class="p-0">
                                    <li><a href="https://www.facebook.com/people/AlphaNeon-Studioz/61558053870790/"
                                            target="_blank">
                                            <svg width="18" height="18" viewBox="0 0 10 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M3.0486 19.3346V10.5757H0.666016V7.42205H3.0486V4.72844C3.0486 2.61177 4.456 0.667969 7.69896 0.667969C9.01198 0.667969 9.98289 0.790329 9.98289 0.790329L9.90639 3.73529C9.90639 3.73529 8.91621 3.72592 7.83568 3.72592C6.66622 3.72592 6.47886 4.2498 6.47886 5.11932V7.42205H9.99935L9.84617 10.5757H6.47886V19.3346H3.0486Z"
                                                    fill="white" />
                                            </svg>
                                        </a></li>
                                    <li><a href="https://x.com/AlphaStudioz_" target="_blank">
                                            <svg width="15" height="15" viewBox="0 0 21 21" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M16.5375 0.984375H19.758L12.723 9.04538L21 20.0164H14.52L9.441 13.3639L3.636 20.0164H0.4125L7.9365 11.3914L0 0.985875H6.645L11.229 7.06537L16.5375 0.984375ZM15.405 18.0844H17.19L5.67 2.81587H3.756L15.405 18.0844Z"
                                                    fill="white" />
                                            </svg>
                                        </a></li>
                                    <li>
                                        <a href="https://www.linkedin.com/company/103164376/admin/feed/posts/?feedType=following"
                                            target="_blank">
                                            <svg width="17" height="17" viewBox="0 0 22 21" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M5.5096 20.3321V6.83807H0.935864V20.3321H5.5096ZM3.22332 4.99459C4.81827 4.99459 5.81104 3.95841 5.81104 2.66353C5.78132 1.33945 4.81833 0.332031 3.25359 0.332031C1.6891 0.332031 0.666016 1.33947 0.666016 2.66353C0.666016 3.95847 1.65855 4.99459 3.19346 4.99459H3.22318H3.22332ZM8.04115 20.3321H12.6149V12.7964C12.6149 12.3931 12.6446 11.9902 12.7654 11.7019C13.096 10.8961 13.8486 10.0615 15.112 10.0615C16.7671 10.0615 17.4292 11.299 17.4292 13.113V20.332H22.0027V12.5946C22.0027 8.44981 19.7462 6.52127 16.737 6.52127C14.2696 6.52127 13.1863 7.87371 12.5845 8.79485H12.615V6.83779H8.04125C8.10127 8.10399 8.04125 20.3318 8.04125 20.3318L8.04115 20.3321Z"
                                                    fill="white" />
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.instagram.com/alphaneonstudioz/" target="_blank">
                                            <svg width="18" height="18" viewBox="0 0 22 22" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M11.3371 5.60976C8.30853 5.60976 5.86568 8.01445 5.86568 10.9957C5.86568 13.9769 8.30853 16.3816 11.3371 16.3816C14.3657 16.3816 16.8085 13.9769 16.8085 10.9957C16.8085 8.01445 14.3657 5.60976 11.3371 5.60976ZM11.3371 14.4973C9.37996 14.4973 7.77996 12.9269 7.77996 10.9957C7.77996 9.06445 9.3752 7.49414 11.3371 7.49414C13.299 7.49414 14.8942 9.06445 14.8942 10.9957C14.8942 12.9269 13.2942 14.4973 11.3371 14.4973ZM18.3085 5.38945C18.3085 6.08789 17.7371 6.6457 17.0323 6.6457C16.3228 6.6457 15.7561 6.0832 15.7561 5.38945C15.7561 4.6957 16.3276 4.1332 17.0323 4.1332C17.7371 4.1332 18.3085 4.6957 18.3085 5.38945ZM21.9323 6.66445C21.8514 4.98164 21.4609 3.49101 20.2085 2.26289C18.9609 1.03477 17.4466 0.650391 15.7371 0.566016C13.9752 0.467578 8.69425 0.467578 6.93234 0.566016C5.22758 0.645703 3.7133 1.03008 2.46092 2.2582C1.20854 3.48633 0.822824 4.97695 0.737109 6.65976C0.637109 8.39414 0.637109 13.5926 0.737109 15.3269C0.818062 17.0098 1.20854 18.5004 2.46092 19.7285C3.7133 20.9566 5.22282 21.341 6.93234 21.4254C8.69425 21.5238 13.9752 21.5238 15.7371 21.4254C17.4466 21.3457 18.9609 20.9613 20.2085 19.7285C21.4561 18.5004 21.8466 17.0098 21.9323 15.3269C22.0323 13.5926 22.0323 8.39882 21.9323 6.66445ZM19.6561 17.1879C19.2847 18.1066 18.5657 18.8144 17.6276 19.1848C16.2228 19.7332 12.8895 19.6066 11.3371 19.6066C9.78472 19.6066 6.44663 19.7285 5.04663 19.1848C4.1133 18.8191 3.39425 18.1113 3.01806 17.1879C2.46092 15.8051 2.58949 12.5238 2.58949 10.9957C2.58949 9.46757 2.46568 6.18164 3.01806 4.80351C3.38949 3.88476 4.10854 3.17695 5.04663 2.80664C6.45139 2.2582 9.78472 2.38476 11.3371 2.38476C12.8895 2.38476 16.2276 2.26289 17.6276 2.80664C18.5609 3.17226 19.28 3.88008 19.6561 4.80351C20.2133 6.18633 20.0847 9.46757 20.0847 10.9957C20.0847 12.5238 20.2133 15.8098 19.6561 17.1879Z"
                                                    fill="white" />
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.youtube.com/@AlphaNeonStudioz" target="_blank">
                                            <svg width="25" height="25" viewBox="0 0 32 32" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M13.3335 11.334L21.3335 16.0007L13.3335 20.6673V11.334Z"
                                                    fill="white" />
                                                <path
                                                    d="M16 6.66602C28 6.66602 28 6.66602 28 15.9993C28 25.3327 28 25.3327 16 25.3327C4 25.3327 4 25.3327 4 15.9993C4 6.66602 4 6.66602 16 6.66602Z"
                                                    stroke="white" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-5 p-0">
                            <div class="black-block-end"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
</main>
<!-- Bootstrap JavaScript Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
    integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ url('/') }}/asset/js/owl.carousel.min.js?v={{ time() }}"></script>
<script src="{{ url('/') }}/asset/js/plyr.js?v={{ time() }}"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/plyr/3.6.4/plyr.js" integrity="sha512-M/AUlH5tMMuhvt9trN4rXBjsXq9HrOUmtblZHhesbx97sGycEnXX/ws1W7yyrF8zEjotdNhYNfHOd3WMu96eKA==" crossorigin="anonymous"></script> --}}
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="{{ url('/') }}/asset/js/script.js?v={{ time() }}"></script>
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip({
            container: 'body',
            placement: 'top',
            customClass: 'my-custom-tooltip'
        });
    });
</script>


@yield('scripts')
</body>

</html>
