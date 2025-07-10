@extends('layouts.main')
@section('title')
    Alphastudioz | Subscription
@endsection
@section('main-section')
<div class="row ms-0 me-0">
    <div class="col-sm-1 col-12">
        
    </div>
<div class="col-sm-11 col-12 mt-5 p-1">
    <section id="banners" class="">
        <div class="banner-section">
         <div class="banner-holder" >
           <div class="subscription-section p-4">
            @if ($activeSubscription)
                <div class="alert alert-info" role="alert">
        You already have a subscription. Please manage your subscription from your account settings.
    </div>
            @else
            <h2>Get Your free subscription for one month</h2>
            <div class="row">
                <div class="col-md-5">
                    @foreach ($package as $item)
                    @if ($item->price == 0 || $activeSubscription)
                        <label for="packageId{{$item->id}}">
                            <div class="price-card mt-5">
                                <div class="price-card-content">
                                    <h3>₹{{$item->price}} /<span>Month </span>  </h3>
                                    <input type="hidden" name="price" value="{{$item->price}}" class="price" id="amountprice">
                                    {{-- <h4 class="theme-text-color">{{$item->name}}</h4> --}}
                                    <div class="line"></div>
                                    <span class="mt-2 d-block">After our free subscription the price will be ₹49/Month </span>
                                    <div class="benefits">
                                        <ul>
                                            <li>In this package you will get @foreach ($typeData as $typeItem)
                                                {{$typeItem->name}}
                                                
                                            @endforeach</li>
                                            <li>Watch On Tv & Laptop @if ($item->watch_on_laptop_tv == 1)
                                                - Yes
                                                @else
                                                - No
                                            @endif </li>
                                            <li>Ad Free Movies and Show @if ($item->watch_on_laptop_tv == 1)
                                                - Yes
                                                @else
                                                - No
                                            @endif </li>
                                            <li>{{$item->video_qulity}} Video Quality</li>
                                            <li>{{$item->video_qulity}} Video Quality</li>
                                            <li>{{$item->no_of_device}} Device Login </li>
                                        
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <input type="radio" name="package" checked class="packageData d-none" id="packageId{{$item->id}}" value="{{$item->id}}">
                    @endif
                @endforeach
                  
                </div>
                <div class="col-md-7">
                    <div class="form-holder">
                        <h3>Please Fill out the cards details </h3>
                        <form action="{{ route('transaction.store') }}" method="post" id="subscribe-form">
                            @csrf
                            <input type="hidden" name="package_id" id="packageId">
                            <input type="hidden" name="amount" id="amount">
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            <div class="mt-4">
                                <input id="cname" type="text" class="form-control" name="card_holder_name" required autofocus placeholder="Card Holder Name">
                            </div>
                            <div class="mt-4">
                                <input id="cnumber" type="text" class="form-control" name="card_number" required autofocus placeholder="Card Number">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <input id="expiry_date" type="text" class="form-control" name="expiry_date" required autofocus placeholder="Expiry Date">
                                </div>
                                <div class="col-md-6">
                                    <input id="security_code" type="text" class="form-control" name="security_code" required autofocus placeholder="Security Code">
                                </div>
                            </div>
                            <button type="submit" class="btn-mood">Submit</button>
                        </form>
                        
           
             
                        
                       
                    </div>
                </div>
            </div>
            @endif
          
           
           </div>
         
           
        </div>
       
        </section>
 
    </div>
</div>
@endsection