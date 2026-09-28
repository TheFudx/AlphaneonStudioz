<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.project_name') }} | Login</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/') }}/asset/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/') }}/asset/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/') }}/asset/favicon_io/favicon-16x16.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/asset/css/login.css">
    <style>
        .mobile-input-group {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .country-code {
            background: #2c2c2c;
            color: #fff;
            padding: 20px 12px;
            border-radius: 6px 0 0 6px;
            font-size: 14px;
            white-space: nowrap;
        }

        .mobile-input-group input {
            border-radius: 0 6px 6px 0 !important;
            border-left: none;
        }

        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-wrapper .login-form-input {
            width: 100%;
            padding-right: 42px;
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

        .password-wrapper .login-form-input.is-invalid {
            background-image: none;
            padding-right: 42px;
        }

        /* Dark website theme for SweetAlert popups */
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
</head>

<body>
    <div class="container-fluid full-viewport-container">
        <div class="row g-0 flex-grow-1">
            <div class="col-md-6 continuous-scroll-wrapper">
                <div class="scroll-track">
                    <img src="{{ url('/') }}/asset/images/login-background-main.jpg" alt="Scrolling Image 1"
                        loading="lazy">
                    <img src="{{ url('/') }}/asset/images/login-background-main.jpg" alt="Scrolling Image 1"
                        loading="lazy">
                    <img src="{{ url('/') }}/asset/images/login-background-main.jpg" alt="Scrolling Image 1"
                        loading="lazy">

                </div>
                <div class="image-overlay"></div>
                <div class="centered-image-text">
                    <p>"Unleash Your Cinematic Journey: <br>Explore, Watch, Repeat."</p>
                </div>
            </div>

            <!-- LOGIN COLUMN -->
            <div class="col-md-6 content-column" id="login-data">
                <div class="content-inner-wrapper">
                    <div class="logo mb-1">
                        <img src="{{ url('/') }}/asset/images/logo.png" class="app-logo" alt="App Logo"
                            loading="lazy">
                    </div>
                    <p class="welcome-text mb-2">Welcome Back</p>
                    <p class="enter-detail-text mt-2">Enter Your Details</p>

                    <!-- STEP 1: Choose login method -->
                    <div id="login-method-buttons" class="d-flex flex-column w-100 gap-2 mt-2">
                     <button type="button" class="login-btn-continue w-100" id="btn-login-otp"
                            onclick="window.open('{{ $AUTH_URL }}', 'peLoginWindow', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0, width=500,height=560, top=' + (screen.height - 600) / 2 + ', left=' + (screen.width - 500) / 2);">
                            Continue With OTP
                        </button>
               
                        <div class="separator">OR</div>
                             <button type="button" class="login-btn-continue w-100" id="btn-login-username">
                            Login With Username
                        </button>
                       
                    </div>

                    @if ($errors->has('AppleError'))
                        <span class="invalid-feedback d-block" role="alert">
                            {!! $errors->first('AppleError') !!}
                        </span>
                    @endif

                    <!-- STEP 2: Username/Password form (hidden until "Login With Username" is chosen) -->
                    <div id="username-login-form" class="w-100 d-none">
                        <form method="POST" action="{{ route('login') }}"
                            class="w-100 d-flex flex-column align-items-center">
                            @csrf
                            <input type="hidden" name="form_type" value="login">
                            <input type="hidden" id="is_brave" name="is_brave" value="0">
                            <input id="email" type="email"
                                class="form-control login-form-input @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" placeholder="Enter Your Email"
                                autocomplete="email" required />
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <div class="password-wrapper">
                                <input id="password" type="password"
                                    class="form-control login-form-input @error('password') is-invalid @enderror"
                                    name="password" value="{{ old('password') }}" placeholder="Enter Your Password"
                                    autocomplete="password" required />
                                <i class="fa fa-eye-slash toggle-password" data-target="password"></i>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <button type="submit" class="login-btn-continue">Login</button>
                        </form>

                        <a class="btn btn-link text-decoration-none text-white text-right forgotpass"
                            href="{{ route('password.request') }}">
                            Forgot Your Password?
                        </a>

                        <button type="button" class="login-btn-continue w-100 mt-2" id="btn-back-to-methods">
                            &larr; Choose Another Method
                        </button>
                    </div>

                    <div class="login-register-buttons d-flex justify-content-between gap-2 mt-2 w-100">
                        <button type="button" class="login-btn-continue flex-fill" onclick="goBack()">&larr;
                            Back</button>
                        {{-- <button type="button" class="login-btn-continue flex-fill"
                            id="register-data-display-button">Register</button> --}}
                    </div>
                </div>
            </div>

            <!-- REGISTER COLUMN (unchanged) -->
            <div class="col-md-6 content-column d-none" id="register-data">
                <div class="content-inner-wrapper">
                    <div class="logo mb-4">
                        <img src="{{ url('/') }}/asset/images/logo.png" class="app-logo" alt="App Logo"
                            loading="lazy">
                    </div>
                    <p class="welcome-text mb-2">Welcome</p>
                    <p class="enter-detail-text mt-4">Enter Your Details</p>
                    <form method="POST" action="{{ route('register') }}"
                        class="w-100 d-flex flex-column align-items-center">
                        @csrf
                        <input type="hidden" name="form_type" value="register">
                        <input type="hidden" id="is_brave" name="is_brave" value="0">
                        <input id="name" type="text"
                            class="form-control login-form-input @error('name', 'register') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" placeholder="Enter Your name"
                            autocomplete="name" required />
                        @error('name', 'register')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <input id="register_email" type="email"
                            class="form-control login-form-input @error('email', 'register') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" placeholder="Enter Your Email"
                            autocomplete="email" required />
                        @error('email', 'register')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="mobile-input-group">
                            <span class="country-code">
                                🇮🇳 +91
                            </span>

                            <input id="mobile_no" type="text"
                                class="form-control login-form-input @error('mobile_no', 'register') is-invalid @enderror"
                                name="mobile_no" value="{{ old('mobile_no') }}" placeholder="Enter Mobile No"
                                maxlength="10" autocomplete="mobile_no" required />
                        </div>

                        @error('mobile_no', 'register')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <div class="password-wrapper">
                            <input id="register_password" type="password"
                                class="form-control login-form-input @error('password', 'register') is-invalid @enderror"
                                name="password" value="{{ old('password') }}" placeholder="Enter Your Password"
                                autocomplete="password" required />
                            <i class="fa fa-eye-slash toggle-password" data-target="register_password"></i>
                        </div>
                        <p class="mb-1 mt-1" style="font-size:11px; color:#aaa;">Strong Password Must Have:</p>
                        <div id="password-hints" class="w-100 mt-1 mb-2 d-flex flex-wrap gap-2 justify-content-center"
                            style="font-size:11px;">
                            <span id="hint-length" class="hint-badge">✗ 8+ chars</span>
                            <span id="hint-upper" class="hint-badge">✗ A-Z</span>
                            <span id="hint-lower" class="hint-badge">✗ a-z</span>
                            <span id="hint-number" class="hint-badge">✗ 0-9</span>
                            <span id="hint-special" class="hint-badge">✗ @$!%*#?&</span>
                        </div>
                        @error('password', 'register')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <div class="password-wrapper">
                            <input id="password_confirmation" type="password"
                                class="form-control login-form-input @error('password_confirmation', 'register') is-invalid @enderror"
                                name="password_confirmation" value="{{ old('password_confirmation') }}"
                                placeholder="Enter Your Confirmation Password" autocomplete="password_confirmation"
                                required />
                            <i class="fa fa-eye-slash toggle-password" data-target="password_confirmation"></i>
                        </div>
                        @error('password_confirmation', 'register')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </form>

                    <button type="button" class="login-btn-continue" id="login-data-display-button">Login</button>
                </div>
            </div>
            {{-- @include('auth.auto-payment-modal') --}}
        </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script src="{{ URL::to('asset/js/sweetalert2@11.js') }}"></script>

    <script type="text/javascript"></script>
    <script>
        navigator.brave?.isBrave?.().then(function(result) {
            console.log("Is Brave:", result);
            $("#is_brave").val(result ? 1 : 0); // Convert true/false to 1/0 if needed
        });
    </script>
    <script>
        function goBack() {
            // if (document.referrer) {
            //     window.history.back(); // Go back if there's a history
            // } else {
            //     window.location.href = "{{ route('home') }}"; // Fallback to homepage
            // }
            window.location.href = "{{ route('home') }}";
        }
        $(document).on('click', '#login-data-display-button', function(e) {
            $("#login-data").removeClass('d-none');
            $("#register-data").addClass('d-none');
        });
        $(document).on('click', '#register-data-display-button', function(e) {
            $("#login-data").addClass('d-none');
            $("#register-data").removeClass('d-none');
        });
    </script>
    <script>
        $(document).ready(function() {

            // Auto open register if register errors exist
            @if ($errors->register->any())
                $("#register-data").removeClass('d-none');
                $("#login-data").addClass('d-none');
            @endif

            // Handle login error & OTP suggestion alerts
            @if (session('login_error_alert'))
                $("#login-method-buttons").addClass('d-none');
                $("#username-login-form").removeClass('d-none');
                Swal.fire({
                    icon: 'error',
                    title: '{{ session('login_error_alert')['title'] }}',
                    text: '{{ session('login_error_alert')['message'] }}',
                    background: '#1a1a1a',
                    color: '#ffffff',
                    confirmButtonColor: '#FF3A1F',
                    confirmButtonText: 'Try Again'
                });
            @elseif (session('otp_suggestion_alert'))
                Swal.fire({
                    icon: 'info',
                    title: '{{ session('otp_suggestion_alert')['title'] }}',
                    html: `If you are new or haven't set up a password yet, please log in using the <strong>Continue With OTP</strong> option.<br><br>After logging in, you can add your email ID and set a password in your <strong>Profile</strong> section to enable both OTP and password login!`,
                    background: '#1a1a1a',
                    color: '#ffffff',
                    confirmButtonColor: '#FF3A1F',
                    confirmButtonText: 'Continue With OTP',
                    preConfirm: () => {
                        // Trigger the exact same OTP login flow as the main
                        // "Continue With OTP" button. Using preConfirm keeps
                        // this within the trusted user-gesture chain so
                        // browsers won't block the Phone.email popup window.
                        document.getElementById('btn-login-otp').click();
                    }
                }).then(() => {
                    // Switch UI back to the login method buttons screen
                    // so the user sees the OTP button if they return here
                    $("#login-method-buttons").removeClass('d-none');
                    $("#username-login-form").addClass('d-none');
                });
            @elseif ($errors->has('email') || $errors->has('password') || $errors->has('login_error'))
                $("#login-method-buttons").addClass('d-none');
                $("#username-login-form").removeClass('d-none');
            @endif

        });
    </script>
    <script>
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
    </script>
    <script>
        $('#register_password').on('input', function() {
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
    </script>
    <script>
        // STEP toggle: choose login method vs username/password form
        $(document).on('click', '#btn-login-username', function() {
            $('#login-method-buttons').addClass('d-none');
            $('#username-login-form').removeClass('d-none');
        });

        $(document).on('click', '#btn-back-to-methods', function() {
            $('#username-login-form').addClass('d-none');
            $('#login-method-buttons').removeClass('d-none');
        });
    </script>
</body>

</html>
