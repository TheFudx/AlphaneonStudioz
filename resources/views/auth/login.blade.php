<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alphastudioz | Login</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/') }}/asset/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/') }}/asset/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/') }}/asset/favicon_io/favicon-16x16.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/asset/css/login.css">
</head>

<body>
    <div class="container-fluid full-viewport-container">
        <div class="row g-0 flex-grow-1">
            <div class="col-md-6 continuous-scroll-wrapper">
                <div class="scroll-track">
                    <img src="{{ url('/') }}/asset/images/login-background-main.jpg" alt="Scrolling Image 1">
                    <img src="{{ url('/') }}/asset/images/login-background-main.jpg" alt="Scrolling Image 1">
                    <img src="{{ url('/') }}/asset/images/login-background-main.jpg" alt="Scrolling Image 1">
                    {{-- <img src="{{ url('/') }}/asset/images/login-background-image2.png" alt="Scrolling Image 2">
                    <img src="{{ url('/') }}/asset/images/login-background-image3.png" alt="Scrolling Image 3">
                    <img src="{{ url('/') }}/asset/images/login-background-image4.png" alt="Scrolling Image 4">
                    <img src="{{ url('/') }}/asset/images/login-background-image5.png" alt="Scrolling Image 5">
                    <img src="{{ url('/') }}/asset/images/login-background-image1.png" alt="Scrolling Image 1 (D)">
                    <img src="{{ url('/') }}/asset/images/login-background-image2.png" alt="Scrolling Image 2 (D)">
                    <img src="{{ url('/') }}/asset/images/login-background-image3.png" alt="Scrolling Image 3 (D)">
                    <img src="{{ url('/') }}/asset/images/login-background-image4.png" alt="Scrolling Image 4 (D)">
                    <img src="{{ url('/') }}/asset/images/login-background-image5.png"
                        alt="Scrolling Image 5 (D)"> --}}
                </div>
                <div class="image-overlay"></div>
                <div class="centered-image-text">
                    <p>"Unleash Your Cinematic Journey: <br>Explore, Watch, Repeat."</p>
                </div>
            </div>
            <div class="col-md-6 content-column" id="login-data">
                <div class="content-inner-wrapper">
                    <div class="logo mb-1">
                        <img src="{{ url('/') }}/asset/images/logo.png" class="app-logo" alt="App Logo">
                    </div>
                    <p class="welcome-text mb-2">Welcome Back</p>
                    <p class="enter-detail-text mt-2">Enter Your Details</p>
                    <form method="POST" action="{{ route('login') }}"
                        class="w-100 d-flex flex-column align-items-center">
                        @csrf
                        <input type="hidden" id="is_brave" name="is_brave" value="0">
                        <input id="email" type="email"
                            class="form-control login-form-input @error('email') is-invalid @enderror" name="email"
                            value="{{ old('email') }}" placeholder="Enter Your Email" autocomplete="email" required />
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <input id="password" type="password"
                            class="form-control login-form-input @error('password') is-invalid @enderror"
                            name="password" value="{{ old('password') }}" placeholder="Enter Your Password"
                            autocomplete="password" required />
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        {{-- <button type="submit" class="login-btn-continue">Continue With Email</button> --}}
                        <button type="submit" class="login-btn-continue">Login</button>
                    </form>
                    <a class="btn btn-link text-decoration-none text-white text-right forgotpass"
                        href="{{ route('password.request') }}">
                        Forgot Your Password?
                    </a>
                    <button type="submit" class="login-btn-continue"
                        onclick="window.open('{{ $AUTH_URL }}', 'peLoginWindow', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0, width=500,height=560, top=' + (screen.height - 600) / 2 + ', left=' + (screen.width - 500) / 2);">Continue
                        With OTP</button>
                    <div class="separator">OR</div>
                    <a href="{{ route('google.redirect') }}" class="login-btn-google">
                        <i class="login-icon-google"></i>Continue With Google
                    </a>
                    <div class="login-register-buttons d-flex justify-content-between gap-2 mt-2 w-100">
                        <button type="button" class="login-btn-continue flex-fill" onclick="goBack()">← Back</button>
                        <button type="button" class="login-btn-continue flex-fill"
                            id="register-data-display-button">Register</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 content-column d-none" id="register-data">
                <div class="content-inner-wrapper">
                    <div class="logo mb-4">
                        <img src="{{ url('/') }}/asset/images/logo.png" class="app-logo" alt="App Logo">
                    </div>
                    <p class="welcome-text mb-2">Welcome</p>
                    <p class="enter-detail-text mt-4">Enter Your Details</p>
                    <form method="POST" action="{{ route('register') }}"
                        class="w-100 d-flex flex-column align-items-center">
                        @csrf
                        <input type="hidden" id="is_brave" name="is_brave" value="0">
                        <input id="name" type="text"
                            class="form-control login-form-input @error('name') is-invalid @enderror" name="name"
                            value="{{ old('name') }}" placeholder="Enter Your name" autocomplete="name"
                            required />
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <input id="register_email" type="email"
                            class="form-control login-form-input @error('email') is-invalid @enderror" name="email"
                            value="{{ old('email') }}" placeholder="Enter Your Email" autocomplete="email"
                            required />
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <input id="mobile_no" type="text"
                            class="form-control login-form-input @error('mobile_no') is-invalid @enderror"
                            minlength="10" maxlength="10" name="mobile_no" value="{{ old('mobile_no') }}"
                            placeholder="Enter Your Mobile No" autocomplete="mobile_no" required />
                        @error('mobile_no')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <input id="register_password" type="password"
                            class="form-control login-form-input @error('password') is-invalid @enderror"
                            name="password" value="{{ old('password') }}" placeholder="Enter Your Password"
                            autocomplete="password" required />
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <input id="password_confirmation" type="password"
                            class="form-control login-form-input @error('password_confirmation') is-invalid @enderror"
                            name="password_confirmation" value="{{ old('password_confirmation') }}"
                            placeholder="Enter Your Confirmation Password" autocomplete="password_confirmation"
                            required />
                        @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        {{-- <button type="submit" class="login-btn-continue">Continue With Email</button> --}}
                        <button type="submit" class="login-btn-continue">Register</button>
                    </form>


                    {{-- <div class="separator">OR</div> --}}
                    {{-- <a href="{{ route('google.redirect') }}" class="login-btn-google">
                        <i class="login-icon-google"></i>Continue With Google
                    </a> --}}
                    <button type="button" class="login-btn-continue" id="login-data-display-button">Login</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>

    <script type="text/javascript"></script>
    <script>
        navigator.brave?.isBrave?.().then(function(result) {
            console.log("Is Brave:", result);
            $("#is_brave").val(result ? 1 : 0); // Convert true/false to 1/0 if needed
        });
    </script>
    <script>
        function goBack() {
            if (document.referrer) {
                window.history.back(); // Go back if there's a history
            } else {
                window.location.href = "{{ url('/') }}"; // Fallback to homepage
            }
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
</body>

</html>
