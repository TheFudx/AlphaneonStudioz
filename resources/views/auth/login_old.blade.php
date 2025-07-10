@extends('layouts.app')
@section('content')
<div class="login-part">
    <div class="container" id="container"> 
       <a href="{{url('/')}}"><img src="{{url('/')}}/asset/icons/logo.png" class="img-fluid main-logo d-sm-block d-none" id="main-logo" alt=""></a> 
       <a href="{{url('/')}}"><img src="{{url('/')}}/asset/icons/logo.png" class="img-fluid main-logo d-sm-none d-block"  alt=""></a> 
        <div class="form-container sign-up-container">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <h1 class="mb-5">Register</h1>
               
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Name">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                  
         
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email Id">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                   
             
                        <input id="mobile_no" type="tel" class="form-control @error('mobile_no') is-invalid @enderror" name="mobile_no" value="{{ old('mobile_no') }}" required autocomplete="mobile_no" autofocus placeholder="Mobile Number">
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
                        <a href="{{ route('google.redirect') }}" class="w-100">
                            <button class="google-signin mt-sm-5 mt-1 w-100" type="button">
                                <i class="icon-google"></i>Login With Google</button>
                        </a>
                        <button class="ghost d-md-none d-block w-100" id="signIn1" type="button">Login</button>
                    </form>
        </div>
        <div class="form-container sign-in-container">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <h1 class="mb-5">Login</h1>
               
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                   
          
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                  
                        {{-- <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div> --}}
                    
            
                        <button type="submit" class="w-100">
                            {{ __('Login') }}
                        </button>
                    
                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                        <a href="{{ route('google.redirect') }}" class="w-100"><button class="google-signin mt-1 w-100" type="button"><i class="icon-google"></i>Login With Google</button>
                        </a>
                        <button class="btn-block w-100 otp-button"name="btn_ph_login"
                         type="button" onclick="window.open('{{ $AUTH_URL }}', 'peLoginWindow', 
                         'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0, width=500,
                          height=560, top=' + (screen.height - 600) / 2 + ', left=' + (screen.width - 500) / 2);">
                          Login With OTP</button>
                        <button class="ghost d-md-none d-block w-100" id="signUp1" type="button">Register</button>
                    </form>
                    
                        {{-- <a href="{{ route('otp.login') }}"><button class="btn-block w-100 otp-button" type="button">Login With OTP</button></a>  --}}
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