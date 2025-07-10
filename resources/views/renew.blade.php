@extends('layouts.main')
@section('title')
    Alphastudioz | Renew
@endsection
@section('main-section')
<div class="row ms-0 me-0">
    <div class="col-sm-1 col-12">
        
    </div>
<div class="col-sm-11 col-12 mt-5 p-1">
    <section id="banners" class="pe-md-5 pe-1 ps-m-0 ps-1">
        <div class="banner-section">
         <div class="banner-holder" >
            <div class="row">
                <div class="col-md-6">
                    <div class="device-limit-text-holder">
                        <div class="content p-4">
                            <h2>Renew now to continue enjoying the service</h2>
                            <p>Please manage your subscription from your account settings.</p>
                           
                            <form action="{{ route('store.upgrade.transaction') }}" method="post" id="subscribe-form">
                                @csrf
                                <input type="hidden" name="package_id" id="packageId" value="">
                                <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $order->id }}">
                                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" >
                                <input type="hidden" name="razorpay_signature" id="razorpay_signature" >
                                <input type="hidden" name="amount" id="amount1" value="{{ $amount / 100 * 100 }}">
                                <input type="hidden" name="user_id" value="{{ session('user_id') }}">
                                <button type="button" class="btn-mood" id="rzp-button1">Renew Now</button>
                            </form>
                 
                        </div>
                    </div>
                </div>
                <div class="col-md-6 subscription-section">
                    @foreach ($package as $item)
                    @if ($item->price == 5)
                        <label for="packageId{{$item->id}}">
                            <div class="price-card mt-md-5 mt-1 mb-5">
                                <div class="price-card-content">
                                    <h3><del>₹ 49</del> ₹{{$item->price}} /<span>Month </span>  </h3>
                                    <input type="hidden" name="price" value="{{$item->price}}" class="price" id="price">
                                    {{-- <h4 class="theme-text-color">{{$item->name}}</h4> --}}
                                    <div class="line"></div>
                                    <span class="mt-2 d-block" style="font-size: 25px; font-weight: 600">Special Offer </span>
                                     <span class="mt-2 d-block">After Special offer subscription the price will be ₹49/Month </span>
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
                                            <li>Ad Free Movies and Show @if ($item->ads_free_movies_shows == 1)
                                                - Yes
                                                @else
                                                - No
                                            @endif </li>
                                            <li>{{$item->video_qulity}} Video Quality</li>
                                            <li>{{$item->no_of_device}} Device Login</li>
                                           
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <input type="radio" name="package" checked class="packageData d-none" id="packageId{{$item->id}}" value="{{$item->id}}">
                    @endif
                @endforeach
                </div>
            </div>
          
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <script>
           document.getElementById('rzp-button1').onclick = function(e) {
          e.preventDefault();
  
          var options = {
              "key": "{{ env('RAZORPAY_KEY') }}",
              "amount": "{{ $amount * 100 }}", // Razorpay accepts amount in paise
              "currency": "INR",
              "name": "Alphaneon Studioz",
              "description": "Subscription Payment",
              "order_id": "{{ $order->id }}", // Pass the Razorpay order ID
              "handler": function (response){
                  document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                  document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                  document.getElementById('razorpay_signature').value = response.razorpay_signature;
                  document.getElementById('packageId').value; // Set package ID if necessary
                  document.getElementById('subscribe-form').submit();
              },
            
              "theme": {
                  "color": "#3399cc"
              }
          };
  
          var rzp1 = new Razorpay(options);
          rzp1.open();
      }
            </script>
           
        </div>
       
        </section>
 
    </div>
</div>
@endsection