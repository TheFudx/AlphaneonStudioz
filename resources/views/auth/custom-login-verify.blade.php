@extends('layouts.app')
    
@section('content')
<div class="login-part">
    <div class="container" id="container"> 
        <img src="{{url('/')}}/asset/icons/alpha-og.png" class="img-fluid main-logo" id="main-logo" alt="">
        <div class="form-container sign-up-container">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <h1 class="mb-5">Register</h1>
               
                        <input id="name" type="text" class="@error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Name">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                  
         
                        <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email Id">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                   
             
                        <input id="mobile_no" type="tel" class="@error('mobile_no') is-invalid @enderror" name="mobile_no" value="{{ old('mobile_no') }}" required autocomplete="mobile_no" autofocus placeholder="Mobile Number">
                        @error('mobile_no')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                   
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                   
             
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confrim Password">
                   
       
                        <button type="submit" class="w-100">
                            {{ __('Register') }}
                        </button>
                        <button class="google-signin"><i class="icon-google"></i>Login With Google</button>
            </form>
           
        </div>
     
        <div class="form-container sign-in-container otp-container">
            @if (session('error'))
            <div class="alert alert-danger" role="alert"> {{session('error')}} 
            </div>
            @endif
            <form method="POST" action="{{ route('custom.login.verify.submit', ['mobile_number' => $mobile_number]) }}">
                @csrf
                <div>
                    <label for="otp">OTP</label>
                    <input type="text" id="otp" name="otp" required>
                </div>
                <button type="submit">Verify OTP</button>
            </form>
        </div>
        <div class="d-flex w-100">
            <div class="overlay-container" id="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <h1>Welcome !</h1>
                        <p>"Unleash Your Cinematic Journey: Explore, Watch, Repeat."</p>
                        <img src="{{url('/')}}/asset/images/Login-creative.png" class="img-fluid" alt="">
                        <button class="ghost" id="signIn">Login</button>
                    </div>
                    <div class="overlay-panel overlay-right">
                        <h1>Welcome Back!</h1>
                        <p>"Unleash Your Cinematic Journey: Explore, Watch, Repeat."</p>
                        <img src="{{url('/')}}/asset/images/Login-creative.png" class="img-fluid" alt="">
                        <button class="ghost" id="signUp">Register</button>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
</div>
@endsection