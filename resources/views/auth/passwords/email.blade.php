<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Alphastudioz | Reset Password</title>
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
                    <img src="{{ url('/') }}/asset/images/login-background-image1.png" alt="Scrolling Image 1">
                    <img src="{{ url('/') }}/asset/images/login-background-image2.png" alt="Scrolling Image 2">
                    <img src="{{ url('/') }}/asset/images/login-background-image3.png" alt="Scrolling Image 3">
                    <img src="{{ url('/') }}/asset/images/login-background-image4.png" alt="Scrolling Image 4">
                    <img src="{{ url('/') }}/asset/images/login-background-image5.png" alt="Scrolling Image 5">
                    <img src="{{ url('/') }}/asset/images/login-background-image1.png" alt="Scrolling Image 1 (D)">
                    <img src="{{ url('/') }}/asset/images/login-background-image2.png" alt="Scrolling Image 2 (D)">
                    <img src="{{ url('/') }}/asset/images/login-background-image3.png" alt="Scrolling Image 3 (D)">
                    <img src="{{ url('/') }}/asset/images/login-background-image4.png"
                        alt="Scrolling Image 4 (D)">
                    <img src="{{ url('/') }}/asset/images/login-background-image5.png"
                        alt="Scrolling Image 5 (D)">
                </div>
            </div>

            <div class="col-md-6 content-column"  id="sendlinkDiv">
                <div class="content-inner-wrapper">
                    <div class="logo mb-4">
                        <img src="{{ url('/') }}/asset/images/logo.png" class="app-logo" alt="App Logo">
                    </div>
                    <p class="welcome-text mb-2">Welcome Back</p>
                    <p class="enter-detail-text mt-4">Enter Your Details</p>
                    <form class="w-100 d-flex flex-column align-items-center">
                        @csrf
                        <div class="alertError d-none text-danger"></div>
                        <input id="email" type="email"
                            class="form-control login-form-input @error('email') is-invalid @enderror" name="email"
                            value="{{ old('email') }}" placeholder="Enter Your email" autocomplete="email" />

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <button type="submit" class="login-btn-continue" id="sendOtp">Send OTP</button>
                        <a href="{{ route('login') }}" class="login-btn-continue"><i class="fa fa-arrow-left"></i>
                            Back</a>

                    </form>
                    <div class="separator">OR</div>
                    <a href="{{ route('google.redirect') }}" class="login-btn-google">
                        <i class="login-icon-google"></i>Continue With Google
                    </a>
                </div>

            </div>

            <div class="col-md-6 content-column d-none"  id="otpVerifyDiv">
                <div class="content-inner-wrapper">
                    <div class="logo mb-4">
                        <img src="{{ url('/') }}/asset/images/logo.png" class="app-logo" alt="App Logo">
                    </div>
                    <p class="welcome-text mb-2">Welcome Back</p>
                    <p class="enter-detail-text mt-4">Enter Your Details</p>
                    <form class="w-100 d-flex flex-column align-items-center">
                        @csrf
                        <div class="alertError d-none text-danger"></div>
                        <input type="hidden" name="user_id" id ="user_id" value="" />
                    <input type="hidden" name="user_otp_id" id="user_otp_id" value="" />
                        <input id="otp" type="text"
                            class="form-control login-form-input @error('otp') is-invalid @enderror" name="otp"
                            value="{{ old('otp') }}" placeholder="Enter Your otp" autocomplete="otp" />

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <button type="submit" class="login-btn-continue" id="verifyOTP">Verify OTP</button>
                        {{-- <a href="{{ route('login') }}" class="login-btn-continue"><i class="fa fa-arrow-left"></i>
                            Back</a> --}}

                    </form>
                    <div class="separator">OR</div>
                    <a href="{{ route('google.redirect') }}" class="login-btn-google">
                        <i class="login-icon-google"></i>Continue With Google
                    </a>
                </div>

            </div>

            <div class="col-md-6 content-column d-none"  id="updatePasswordDiv">
                <div class="content-inner-wrapper">
                    <div class="logo mb-4">
                        <img src="{{ url('/') }}/asset/images/logo.png" class="app-logo" alt="App Logo">
                    </div>
                    <p class="welcome-text mb-2">Welcome Back</p>
                    <p class="enter-detail-text mt-4">Enter Your Details</p>
                    <form class="w-100 d-flex flex-column align-items-center">
                        @csrf
                        <div class="alertError d-none text-danger"></div>
                          <input type="hidden" name="user_id" value="" id="update_password_user_id">
                        <input id="password" type="password"
                            class="form-control login-form-input @error('password') is-invalid @enderror" name="password"
                            value="{{ old('password') }}" placeholder="Enter Your password" autocomplete="password" />

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <input id="password-confirm" type="password"
                            class="form-control login-form-input @error('password_confirmation') is-invalid @enderror" name="password_confirmation"
                            value="{{ old('password_confirmation') }}" placeholder="Enter Your password" autocomplete="password" />

                        @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <button type="submit" class="login-btn-continue" id="updatePasswordBtn">Reset Password</button>
                        <a href="{{ route('login') }}" class="login-btn-continue"><i class="fa fa-arrow-left"></i>
                            Back</a>

                    </form>
                    <div class="separator">OR</div>
                    <a href="{{ route('google.redirect') }}" class="login-btn-google">
                        <i class="login-icon-google"></i>Continue With Google
                    </a>
                </div>

            </div>
        </div>
    </div>



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>

    <script type="text/javascript"></script>

    <script>
        $(document).on('click', '#sendOtp', function(e) {
            e.preventDefault();
            // Get the button element
            var $sendOtpButton = $(this);

            // IMMEDIATELY disable the button
            $sendOtpButton.prop('disabled', true);
            $sendOtpButton.css("background-color", "#6c757d");
            $sendOtpButton.css("border-color", "#6c757d");
            $sendOtpButton.css('cursor', 'auto');
            $sendOtpButton.text('Loading...'); // Optional: Give feedback to the user

            $(".alertError").addClass('d-none');
            var email = $("#email").val();

            // Directly make the AJAX call, as the button is already disabled
            $.ajax({
                url: `{{ url('auth/send-otp-on-mail') }}`,
                type: 'post',
                dataType: 'JSON',
                data: {
                    email: email,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response);
                    if (response.status == false) {
                        $(".alertError").removeClass('d-none');
                        $(".alertError").html('<strong>' + response.message + '</strong>');
                        // Re-enable the button and reset its style on error
                        $sendOtpButton.prop('disabled', false);
                        $sendOtpButton.css("background-color", ""); // Reset to default or original
                        $sendOtpButton.css('cursor', ''); // Reset to default or original
                        $sendOtpButton.text('Send OTP'); // Reset text
                    }
                    if (response.data) {
                        $("#sendlinkDiv").addClass('d-none');
                        $("#otpVerifyDiv").removeClass('d-none');
                        $("#user_id").val(response.data.user_id);
                        $("#user_otp_id").val(response.data.user_otp_id);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors (e.g., network issues, server errors)
                    console.error("AJAX Error:", status, error);
                    $(".alertError").removeClass('d-none');
                    $(".alertError").html('<strong>An error occurred. Please try again.</strong>');
                    // Re-enable the button and reset its style on error
                    $sendOtpButton.prop('disabled', false);
                    $sendOtpButton.css("background-color", "");
                    $sendOtpButton.css('cursor', '');
                    $sendOtpButton.text('Send OTP');
                }
            });
        });


         $(document).on('click', '#verifyOTP', function(e) {
        e.preventDefault();

        // Get the button element
        var $verifyOtpButton = $(this); // Renamed for clarity

        // IMMEDIATELY disable the button
        $verifyOtpButton.prop('disabled', true);
        $verifyOtpButton.css("background-color", "#6c757d");
        $verifyOtpButton.css("border-color", "#6c757d");
        $verifyOtpButton.css('cursor', 'auto');
        $verifyOtpButton.text('Verifying...'); // Optional: Give feedback to the user

        $(".alertError").addClass('d-none');
        var user_id = $("#user_id").val();
        var user_otp_id = $("#user_otp_id").val();
        var otp = $("#otp").val();

        // Remove the redundant if condition, directly make the AJAX call
        $.ajax({
            url: `{{ url('auth/verify-otp') }}`,
            type: 'post',
            dataType: 'JSON',
            data: {
                user_id: user_id,
                user_otp_id: user_otp_id,
                otp: otp,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status == false) {
                    $(".alertError").removeClass('d-none');
                    $(".alertError").html('<strong>' + response.message + '</strong>');
                    // Re-enable the button and reset its style on error
                    $verifyOtpButton.prop('disabled', false);
                    $verifyOtpButton.css("background-color", ""); // Reset to default or original
                    $verifyOtpButton.css('cursor', ''); // Reset to default or original
                    $verifyOtpButton.text('Verify OTP'); // Reset text
                }
                if (response.data) {
                    $("#otpVerifyDiv").addClass('d-none');
                    $("#updatePasswordDiv").removeClass('d-none');
                    $("#update_password_user_id").val(response.data.userId);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                $(".alertError").removeClass('d-none');
                $(".alertError").html(
                    '<strong>An error occurred while verifying. Please try again.</strong>');
                // Re-enable the button and reset its style on error
                $verifyOtpButton.prop('disabled', false);
                $verifyOtpButton.css("background-color", "");
                $verifyOtpButton.css('cursor', '');
                $verifyOtpButton.text('Verify OTP');
            }
        });
    });
    
         $(document).on('click', '#updatePasswordBtn', function(e) {
        e.preventDefault();
        // Get the button element
        var $updatePasswordButton = $(this); // Renamed for clarity

        // IMMEDIATELY disable the button
        $updatePasswordButton.prop('disabled', true);
        $updatePasswordButton.css("background-color", "#6c757d");
        $updatePasswordButton.css("border-color", "#6c757d");
        $updatePasswordButton.css('cursor', 'auto');
        $updatePasswordButton.text('Updating Password...'); // Optional: Give feedback to the user

        $(".alertError").addClass('d-none');
        var update_password_user_id = $("#update_password_user_id").val();
        var password = $("#password").val();
        var password_confirm = $("#password-confirm").val();

        // Remove the redundant if condition, directly make the AJAX call
        $.ajax({
            url: `{{ url('auth/update-password') }}`,
            type: 'post',
            dataType: 'JSON',
            data: {
                user_id: update_password_user_id,
                password: password,
                password_confirmation: password_confirm,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status == false) {
                    $(".alertError").removeClass('d-none');
                    $(".alertError").html('<strong>' + response.message + '</strong>');
                    // Re-enable the button and reset its style on error
                    $updatePasswordButton.prop('disabled', false);
                    $updatePasswordButton.css("background-color",
                        ""); // Reset to default or original
                    $updatePasswordButton.css('cursor', ''); // Reset to default or original
                    $updatePasswordButton.text('Update Password'); // Reset text
                }
                if (response.url) {
                    window.location = response.url;
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                $(".alertError").removeClass('d-none');
                $(".alertError").html(
                    '<strong>An error occurred while updating password. Please try again.</strong>'
                );
                // Re-enable the button and reset its style on error
                $updatePasswordButton.prop('disabled', false);
                $updatePasswordButton.css("background-color", "");
                $updatePasswordButton.css('cursor', '');
                $updatePasswordButton.text('Update Password');
            }
        });
    });
    </script>
</body>

</html>
