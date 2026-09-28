<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.project_name') }} | Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/asset/css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .password-field-wrapper {
            position: relative;
            width: 100%;
        }

        .password-field-wrapper .toggle-password-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
            z-index: 10;
            font-size: 16px;
        }

        .hint-badge {
            padding: 4px 10px;
            border-radius: 12px;
            background-color: #333333;
            color: #aaaaaa;
            font-size: 11px;
            display: inline-block;
            transition: all 0.2s ease;
            border: 1px solid #444;
        }

        .hint-badge.hint-valid {
            background-color: rgba(40, 167, 69, 0.2) !important;
            color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .hint-badge.hint-invalid {
            background-color: rgba(220, 53, 69, 0.15) !important;
            color: #ff6b6b !important;
            border-color: #dc3545 !important;
        }
    </style>
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

            {{-- Step 1: Send OTP to Email --}}
            <div class="col-md-6 content-column" id="sendlinkDiv">
                <div class="content-inner-wrapper">
                    <div class="logo mb-4">
                        <img src="{{ url('/') }}/asset/images/logo.png" class="app-logo" alt="App Logo">
                    </div>
                    <p class="welcome-text mb-2">Forgot Password</p>
                    <p class="enter-detail-text mt-4">Enter Your Registered Email</p>
                    <form class="w-100 d-flex flex-column align-items-center">
                        @csrf
                        <div class="alertError d-none text-danger mb-2"></div>
                        <input id="email" type="email"
                            class="form-control login-form-input @error('email') is-invalid @enderror" name="email"
                            value="{{ old('email') }}" placeholder="Enter Your Email ID" autocomplete="email" />

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <button type="submit" class="login-btn-continue" id="sendOtp">Send OTP</button>
                        <a href="{{ route('login') }}" class="login-btn-continue mt-2"><i class="fa fa-arrow-left"></i>
                            Back to Login</a>
                    </form>
                </div>
            </div>

            {{-- Step 2: Verify OTP --}}
            <div class="col-md-6 content-column d-none" id="otpVerifyDiv">
                <div class="content-inner-wrapper">
                    <div class="logo mb-4">
                        <img src="{{ url('/') }}/asset/images/logo.png" class="app-logo" alt="App Logo">
                    </div>
                    <p class="welcome-text mb-2">Verify OTP</p>
                    <p class="enter-detail-text mt-4">Enter OTP Sent to Email</p>
                    <form class="w-100 d-flex flex-column align-items-center">
                        @csrf
                        <div class="alertError d-none text-danger mb-2"></div>
                        <input type="hidden" name="user_id" id="user_id" value="" />
                        <input type="hidden" name="user_otp_id" id="user_otp_id" value="" />
                        <input id="otp" type="text"
                            class="form-control login-form-input @error('otp') is-invalid @enderror" name="otp"
                            value="{{ old('otp') }}" placeholder="Enter OTP Code" autocomplete="one-time-code" />

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <button type="submit" class="login-btn-continue" id="verifyOTP">Verify OTP</button>
                    </form>
                </div>
            </div>

            {{-- Step 3: Set New Password --}}
            <div class="col-md-6 content-column d-none" id="updatePasswordDiv">
                <div class="content-inner-wrapper">
                    <div class="logo mb-4">
                        <img src="{{ url('/') }}/asset/images/logo.png" class="app-logo" alt="App Logo">
                    </div>
                    <p class="welcome-text mb-2">Reset Password</p>
                    <p class="enter-detail-text mt-3">Create New Password</p>
                    <form class="w-100 d-flex flex-column align-items-center">
                        @csrf
                        <div class="alertError d-none text-danger mb-2"></div>
                        <input type="hidden" name="user_id" value="" id="update_password_user_id">

                        <div class="password-field-wrapper w-100 mb-2">
                            <input id="password" type="password"
                                class="form-control login-form-input @error('password') is-invalid @enderror"
                                name="password" placeholder="New Password" autocomplete="new-password" />
                            <i class="fa fa-eye-slash toggle-password-icon" data-target="password"></i>
                        </div>

                        <p class="mb-1 mt-1 text-white text-center" style="font-size:12px; font-weight: 500;">Strong Password Must Have:</p>
                        <div id="reset-password-hints" class="w-100 mt-1 mb-3 d-flex flex-wrap gap-2 justify-content-center">
                            <span id="reset-hint-length" class="hint-badge hint-invalid">✗ 8+ chars</span>
                            <span id="reset-hint-upper" class="hint-badge hint-invalid">✗ A-Z</span>
                            <span id="reset-hint-lower" class="hint-badge hint-invalid">✗ a-z</span>
                            <span id="reset-hint-number" class="hint-badge hint-invalid">✗ 0-9</span>
                            <span id="reset-hint-special" class="hint-badge hint-invalid">✗ @$!%*#?&</span>
                        </div>

                        <div class="password-field-wrapper w-100 mb-3">
                            <input id="password-confirm" type="password"
                                class="form-control login-form-input @error('password_confirmation') is-invalid @enderror"
                                name="password_confirmation" placeholder="Confirm Password" autocomplete="new-password" />
                            <i class="fa fa-eye-slash toggle-password-icon" data-target="password-confirm"></i>
                        </div>

                        <button type="submit" class="login-btn-continue" id="updatePasswordBtn">Reset Password</button>
                        <a href="{{ route('login') }}" class="login-btn-continue mt-2"><i class="fa fa-arrow-left"></i>
                            Back to Login</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        // Password Eye Toggle Handler
        $(document).on('click', '.toggle-password-icon', function() {
            var targetId = $(this).data('target');
            var input = $('#' + targetId);
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                input.attr('type', 'password');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });

        // Real-time Password Hints Validation
        $(document).on('keyup input', '#password', function() {
            var val = $(this).val();

            // 8+ characters
            if (val.length >= 8) {
                $('#reset-hint-length').addClass('hint-valid').removeClass('hint-invalid').text('✓ 8+ chars');
            } else {
                $('#reset-hint-length').addClass('hint-invalid').removeClass('hint-valid').text('✗ 8+ chars');
            }

            // Uppercase
            if (/[A-Z]/.test(val)) {
                $('#reset-hint-upper').addClass('hint-valid').removeClass('hint-invalid').text('✓ A-Z');
            } else {
                $('#reset-hint-upper').addClass('hint-invalid').removeClass('hint-valid').text('✗ A-Z');
            }

            // Lowercase
            if (/[a-z]/.test(val)) {
                $('#reset-hint-lower').addClass('hint-valid').removeClass('hint-invalid').text('✓ a-z');
            } else {
                $('#reset-hint-lower').addClass('hint-invalid').removeClass('hint-valid').text('✗ a-z');
            }

            // Number
            if (/[0-9]/.test(val)) {
                $('#reset-hint-number').addClass('hint-valid').removeClass('hint-invalid').text('✓ 0-9');
            } else {
                $('#reset-hint-number').addClass('hint-invalid').removeClass('hint-valid').text('✗ 0-9');
            }

            // Special Character
            if (/[@$!%*#?&]/.test(val)) {
                $('#reset-hint-special').addClass('hint-valid').removeClass('hint-invalid').text('✓ @$!%*#?&');
            } else {
                $('#reset-hint-special').addClass('hint-invalid').removeClass('hint-valid').text('✗ @$!%*#?&');
            }
        });

        // Step 1: Send OTP to Email
        $(document).on('click', '#sendOtp', function(e) {
            e.preventDefault();
            var $sendOtpButton = $(this);
            var email = $("#email").val().trim();

            if (!email) {
                Swal.fire({
                    icon: 'error',
                    title: 'Email Required',
                    text: 'Please enter your registered email address.',
                    background: '#1a1a1a',
                    color: '#ffffff',
                    confirmButtonColor: '#FF3A1F',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $sendOtpButton.prop('disabled', true).css({"background-color": "#6c757d", "border-color": "#6c757d", "cursor": "auto"}).text('Sending OTP...');
            $(".alertError").addClass('d-none');

            $.ajax({
                url: `{{ url('auth/send-otp-on-mail') }}`,
                type: 'post',
                dataType: 'JSON',
                data: { email: email },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.status === false) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Email Not Registered',
                            text: response.message || 'The email address you entered is not registered with us. Please check your email or log in using OTP.',
                            background: '#1a1a1a',
                            color: '#ffffff',
                            confirmButtonColor: '#FF3A1F',
                            confirmButtonText: 'OK'
                        });
                        $sendOtpButton.prop('disabled', false).css({'background-color':'', 'border-color':'', 'cursor':''}).text('Send OTP');
                    } else if (response.data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'OTP Sent',
                            text: response.message || 'An OTP has been sent to your registered email address.',
                            background: '#1a1a1a',
                            color: '#ffffff',
                            confirmButtonColor: '#FF3A1F',
                            confirmButtonText: 'Enter OTP'
                        }).then(() => {
                            $("#sendlinkDiv").addClass('d-none');
                            $("#otpVerifyDiv").removeClass('d-none');
                            $("#user_id").val(response.data.user_id);
                            $("#user_otp_id").val(response.data.user_otp_id);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while sending OTP. Please try again.',
                        background: '#1a1a1a',
                        color: '#ffffff',
                        confirmButtonColor: '#FF3A1F',
                        confirmButtonText: 'OK'
                    });
                    $sendOtpButton.prop('disabled', false).css({'background-color':'', 'border-color':'', 'cursor':''}).text('Send OTP');
                }
            });
        });

        // Step 2: Verify OTP
        $(document).on('click', '#verifyOTP', function(e) {
            e.preventDefault();
            var $verifyOtpButton = $(this);
            var user_id = $("#user_id").val();
            var user_otp_id = $("#user_otp_id").val();
            var otp = $("#otp").val().trim();

            if (!otp) {
                Swal.fire({
                    icon: 'error',
                    title: 'OTP Required',
                    text: 'Please enter the OTP code sent to your email.',
                    background: '#1a1a1a',
                    color: '#ffffff',
                    confirmButtonColor: '#FF3A1F',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $verifyOtpButton.prop('disabled', true).css({"background-color": "#6c757d", "border-color": "#6c757d", "cursor": "auto"}).text('Verifying...');
            $(".alertError").addClass('d-none');

            $.ajax({
                url: `{{ url('auth/verify-otp') }}`,
                type: 'post',
                dataType: 'JSON',
                data: {
                    user_id: user_id,
                    user_otp_id: user_otp_id,
                    otp: otp,
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.status === false) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid OTP',
                            text: response.message || 'Please enter a valid OTP code.',
                            background: '#1a1a1a',
                            color: '#ffffff',
                            confirmButtonColor: '#FF3A1F',
                            confirmButtonText: 'OK'
                        });
                        $verifyOtpButton.prop('disabled', false).css({'background-color':'', 'border-color':'', 'cursor':''}).text('Verify OTP');
                    } else if (response.data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'OTP Verified',
                            text: 'OTP verified successfully. Please enter your new password.',
                            background: '#1a1a1a',
                            color: '#ffffff',
                            confirmButtonColor: '#FF3A1F',
                            confirmButtonText: 'Reset Password'
                        }).then(() => {
                            $("#otpVerifyDiv").addClass('d-none');
                            $("#updatePasswordDiv").removeClass('d-none');
                            $("#update_password_user_id").val(response.data.userId);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while verifying OTP. Please try again.',
                        background: '#1a1a1a',
                        color: '#ffffff',
                        confirmButtonColor: '#FF3A1F',
                        confirmButtonText: 'OK'
                    });
                    $verifyOtpButton.prop('disabled', false).css({'background-color':'', 'border-color':'', 'cursor':''}).text('Verify OTP');
                }
            });
        });

        // Step 3: Reset Password
        $(document).on('click', '#updatePasswordBtn', function(e) {
            e.preventDefault();
            var $updatePasswordButton = $(this);
            var update_password_user_id = $("#update_password_user_id").val();
            var password = $("#password").val();
            var password_confirm = $("#password-confirm").val();

            if (!password) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Required',
                    text: 'Please enter a new password.',
                    background: '#1a1a1a',
                    color: '#ffffff',
                    confirmButtonColor: '#FF3A1F',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (password !== password_confirm) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'Confirm password does not match your new password.',
                    background: '#1a1a1a',
                    color: '#ffffff',
                    confirmButtonColor: '#FF3A1F',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $updatePasswordButton.prop('disabled', true).css({"background-color": "#6c757d", "border-color": "#6c757d", "cursor": "auto"}).text('Updating Password...');
            $(".alertError").addClass('d-none');

            $.ajax({
                url: `{{ url('auth/update-password') }}`,
                type: 'post',
                dataType: 'JSON',
                data: {
                    user_id: update_password_user_id,
                    password: password,
                    password_confirmation: password_confirm,
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.status === false) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: response.message || 'Please ensure your password meets all complexity requirements.',
                            background: '#1a1a1a',
                            color: '#ffffff',
                            confirmButtonColor: '#FF3A1F',
                            confirmButtonText: 'OK'
                        });
                        $updatePasswordButton.prop('disabled', false).css({'background-color':'', 'border-color':'', 'cursor':''}).text('Reset Password');
                    } else if (response.url || response.status === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Password Reset Successful',
                            text: response.message || 'Your password has been reset successfully.',
                            background: '#1a1a1a',
                            color: '#ffffff',
                            confirmButtonColor: '#FF3A1F',
                            confirmButtonText: 'Log In Now'
                        }).then(() => {
                            window.location = response.url || `{{ route('login') }}`;
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating password. Please try again.',
                        background: '#1a1a1a',
                        color: '#ffffff',
                        confirmButtonColor: '#FF3A1F',
                        confirmButtonText: 'OK'
                    });
                    $updatePasswordButton.prop('disabled', false).css({'background-color':'', 'border-color':'', 'cursor':''}).text('Reset Password');
                }
            });
        });
    </script>
</body>

</html>
