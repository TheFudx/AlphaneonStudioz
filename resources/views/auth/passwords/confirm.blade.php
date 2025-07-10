@extends('layouts.app')

@section('content')


<div class="login-part">
    <div class="container" id="container"> 
       <a href="{{url('/')}}"><img src="{{url('/')}}/asset/icons/alpha-og.png" class="img-fluid main-logo d-sm-block d-none" id="main-logo" alt=""></a> 
       <a href="{{url('/')}}"><img src="{{url('/')}}/asset/icons/alpha-og.png" class="img-fluid main-logo d-sm-none d-block"  alt=""></a> 
        <div class="form-container sign-up-container">
           
        </div>
        <div class="form-container sign-in-container">
          

           
            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf
                    <h1>Confrim your Password</h1>
              
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    

                        <button type="submit" class="btn btn-primary">
                            {{ __('Confirm Password') }}
                        </button>

                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                 
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

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Confirm Password') }}</div>

                <div class="card-body">
                    {{ __('Please confirm your password before continuing.') }}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
