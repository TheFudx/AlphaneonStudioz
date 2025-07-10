@extends('layouts.app')

@section('content')
    <div class="login-part">
        <div class="container" id="container">
            <img src="{{ url('/') }}/asset/icons/alpha-og.png" class="img-fluid main-logo" id="main-logo" alt="">
            <div class="form-container sign-up-container">

            </div>

            <div class="form-container sign-in-container otp-container">
                <div class="message-container ps-5 pe-5">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert"> {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert"> {{ session('error') }}
                        </div>
                    @endif

                </div>


                <form method="POST" action="{{ route('otp.getlogin') }}">
                    @csrf
                    <h1 class="mb-5">OTP </h1>
                    <input type="hidden" name="user_id" value="{{ $user_id }}" />

                    <input id="otp" type="text" @error('otp') is-invalid @enderror" name="otp"
                        value="{{ old('otp') }}" required autocomplete="otp" autofocus placeholder="Enter OTP">
                    @error('otp')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <button type="submit">
                        {{ __('Login') }}
                    </button>

                </form>

            </div>
            <div class="d-flex w-100">
                <div class="overlay-container" id="overlay-container">
                    <div class="overlay">
                        <div class="overlay-panel overlay-left">
                            <h1>Welcome !</h1>
                            <p>"Unleash Your Cinematic Journey: Explore, Watch, Repeat."</p>
                            <img src="{{ url('/') }}/asset/images/Login-creative.png" class="img-fluid" alt="">
                            <button class="ghost" id="signIn">Login</button>
                        </div>
                        <div class="overlay-panel overlay-right">
                            <h1>Verify to get best entertainment</h1>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
