@extends('layouts.app')

@section('content')


<div class="login-part">
    <div class="container" id="container"> 
       <a href="{{url('/')}}"><img src="{{url('/')}}/asset/icons/alpha-og.png" class="img-fluid main-logo d-sm-block d-none" id="main-logo" alt=""></a> 
       <a href="{{url('/')}}"><img src="{{url('/')}}/asset/icons/alpha-og.png" class="img-fluid main-logo d-sm-none d-block"  alt=""></a> 
        <div class="form-container sign-up-container">
           
        </div>
        <div class="form-container sign-in-container">
          

            <form method="POST" class="d-md-flex align-items-center" action="{{ route('password.update') }}">
                @csrf
                <h1 class="mb-5">Add New Password</h1>
                <input type="hidden" name="token" value="{{ $token }}">

                
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    

                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                   
       
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                
            
                        <button type="submit" class="w-100">
                            {{ __('Reset Password') }}
                        </button>
                  
            </form>
        </div>

        <div class="d-flex w-100">
            <div class="overlay-container" id="overlay-container">
                <div class="overlay">
                 
                    <div class="overlay-panel overlay-right">
                        <h1>Forgot Your Password No worries we are here!!!</h1>
                        
                      
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
</div>


@endsection
