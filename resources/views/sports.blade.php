@extends('layouts.main')
@section('title')
    Alphastudioz | Sports
@endsection
@section('main-section')
<div class="row ms-0 me-0">
    <div class="col-sm-1 col-12">
        
    </div>
<div class="col-sm-11 col-12 mt-5 p-1">
    <section id="banners" class="">
        <div class="banner-section">
            <div class="banner-holder d-block" >
                <img src="{{url('/')}}/asset/images/arm-wrestling.png" class="inner-landscape" width="100%" alt="">
                <img src="{{url('/')}}/asset/images/arm-wrestling-thumb.png" class="inner-thumbnail" width="100%" alt="">
                <div class="overlay position-absolute">
                    <div class="movie-short-description ">
                        <div class="row">
                            <div class="col-9">
                                <div class="short-data position-absolute bottom-0 pb-5 col-sm-6 col-10">
                                    <h5>ARM WRESTLING</h5>
                                    <p class="mb-1 ">Lorem ipsum dolor sit amet consectetur adipisicing elit. Temporibus cum incidunt magni eos natus laborum animi odio voluptates, </p>
                                    <div class="line"></div>
                                    <ul class="mb-3">
                                        <li>Reality | </li>
                                        <li>Competition </li>
                                        
                                     </ul>
                                  
                                   {{-- <p>Director : <span class="bold-600">Sohail Khan</span> </p> --}}
                                  
                                   <div class="d-flex position-relative">
                                  
                                    <a href="#">
                                      <button type="button" class="btn-common watch-now"><i class="icon-play"></i>Coming Soon</button>
                                  </a>
                                 
                                 
                        
                              </div>
                                
                              
                                </div>
                                
                            </div>
                            
                        </div>
                   </div>
                </div>
               
            </div>
       
        </section>
       <section id="registration">
        <div class="registration-section ">
            <div class="registration-section-container pt-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <span class="heading d-block ">Register Here</span>
                            <div class="form-holder mt-4">
                                @if(session('success'))
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: '{{ session('success') }}',
                                            confirmButtonColor: '#3085d6',
                                            confirmButtonText: 'OK'
                                        });
                                    });
                                </script>
                            @endif
                            
                            @if(session('error'))
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: '{{ session('error') }}',
                                            confirmButtonColor: '#d33',
                                            confirmButtonText: 'OK'
                                        });
                                    });
                                </script>
                            @endif
                            
                                <form id="registrationForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="fullname">Full name*</label>
                                            <input type="text" id="fullname" name="fullname" placeholder='e.g. "John Due"' value="{{ old('firstname') }}">
                                            @error('firstname')
                                                <div class="theme-text-color">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <label for="fullname">Email Id*</label>
                                            <input type="email" id="email" name="email" placeholder='e.g. "john@gmail.com"' value="{{ old('email') }}">
                                            @error('email')
                                                <div class="theme-text-color">{{ $message }}</div>
                                            @enderror
                                        </div>
                                       
                                    </div>
                                  
                                
                                    <div class="row">
                                        <div class="col-md-6 mt-3">
                                            <label for="age">Age*</label>
                                            <input type="number" id="age" name="age" placeholder='e.g. "35"' value="{{ old('age') }}">
                                            @error('age')
                                                <div class="theme-text-color">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <label for="weight">Weight (Kg)*</label>
                                            <input type="number" step="0.1" id="weight" name="weight" placeholder='e.g. "85 kg"' value="{{ old('weight') }}">
                                            @error('weight')
                                                <div class="theme-text-color">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <label for="dhand">Dominant hand*</label>
                                            <input type="text"  id="dhand" name="dhand" placeholder='e.g. "Right Hand"' value="{{ old('dhand') }}">
                                            @error('dhand')
                                                <div class="theme-text-color">{{ $message }}</div>
                                            @enderror
                                        
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <label for="experiance">Experience Level*</label>
                                            <div class="select-option-container">
                                                <select name="experience" class="" id="experience">
                                                    <option value="Beginner">Beginner</option>
                                                    <option value="Intermediate">Intermediate</option>
                                                    <option value="Advance">Advance</option>
                                                </select>
                                                @error('experience')
                                                <div class="theme-text-color">{{ $message }}</div>
                                            @enderror
                                            </div>
                                           
                                        </div>
                                      
                                        <div class="col-md-6 mt-3">
                                            <label for="mobile">Phone Number*</label>
                                            <input type="tel" id="mobile" maxlength="10" minlength="10" name="mobile" placeholder='e.g. "9876543210' value="{{ old('mobile') }}">
                                            @error('mobile')
                                                <div class="theme-text-color">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mt-3 injury-field">
                                            <label for="injuryf" class="d-block">Injury History*</label>
                                            <div class="injury-fieild-holder mt-3">
                                                <label for="injuryYes">
                                                    <input type="radio" name="injury" id="injuryYes" value="Yes">
                                                    <span>Yes</span>
                                            </label>
                                            <label for="injuryNo">
                                                    <input type="radio" name="injury" id="injuryNo" value="No">
                                                    <span>No</span>
                                            </label>
                                            </div>
                                         
                                            @error('injury')
                                                <div class="theme-text-color">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <input type="hidden" id="amount" name="amount" value="50">
                                
                                    <label for="" class="d-block my-4 amount">Registration Fee ₹ 50 /-</label>
                                
                                    <button type="submit" id="pay&register" class="btn-mood ps-5 pe-5">Pay & Register</button>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="content-holder">
                              
                                <span class="ruleshead d-block mb-4">Rules & Regulation</span>
                                <ul>
                                    <li>Starting Position: Elbows on the pads, hands gripped, shoulders square  </li>
                                    <li>Grip: Fair grip checked by the referee; no shifting before "Ready, Go."</li>
                                    <li>Pinning: Win when the opponent's hand touches the pad/table. </li>
                                    <li>Elbow Fouls: Lifting or moving the elbow off the pad is a foul (2 fouls = loss)</li>
                                    <li>Straps: Used if hands slip apart without advantage</li>
                                    <li>Fouls: Intentional elbow lifts, slipping, or unsportsmanlike conduct = foul</li>
                                    <li>Referee’s Decision: Final and non-appealable</li>
                                    <li>Safety: Follow all safety rules; medical clearance may be required</li>
                                    
                                </ul>
                             <div class="pricing-container">
                                <span class="ruleshead d-block mt-3 mb-3"> Pricing Details</span>
                                <ul>
                                    <li><img src="{{url('/')}}/asset/icons/first-price.png" height="25px" alt=""> 1st Price is ₹ 1,00,000 </li>
                                    <li><img src="{{url('/')}}/asset/icons/scond-price.png" height="25px" alt=""> 2nd Price is ₹ 51,000 </li>
                                    <li><img src="{{url('/')}}/asset/icons/third-price.png" height="25px" alt=""> 3rd Price is ₹ 11,000 </li>
                                </ul>
                             </div>
                            </div>
                            
                        </div>
                    
                    </div>
                    <div class="total-count-text p-5">
                        <div class="registration-info">
                            <p class="registration-text text-center">
                               <strong>{{ $totalRegistrations }}</strong> arm-wrestling enthusiasts who have already registered! 
                                Don’t miss out on this exciting event. Get ready to flex those muscles and compete with the best. Register now!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       </section>
    </div>
</div>
<!-- Include Razorpay script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Get form values
    var fullname = document.getElementById('fullname').value;
    var email     = document.getElementById('email').value;
    var age       = document.getElementById('age').value;
    var weight    = document.getElementById('weight').value;
    var dhand    = document.getElementById('dhand').value;
    var experience    = document.getElementById('experience').value;
    var injuryNo    = document.getElementById('injuryNo').value;
    var injuryYes    = document.getElementById('injuryYes').value;
    var mobile    = document.getElementById('mobile').value;
    var amount    = document.getElementById('amount').value * 100; // Convert to paisa
    // Basic validation
    if (!fullname  || !email || !mobile || !age || !weight || !dhand ||  !experience || !injuryYes || !injuryYes) {
        alert('Please fill all required fields.');
        return;
    }
    if(mobile.length < 10){
        alert('Please enter a valid mobile number.');
        return;
    }
    // Razorpay payment options
    var options = {
        "key": "rzp_test_FZs1ciE3h1febU",
        "amount": amount,
        "currency": "INR",
        "name": "Alphastudioz",
        "description": "Registration Fee",
        "handler": function (response){
            // Append payment ID to form
            var form = document.getElementById('registrationForm');
            var paymentIdInput = document.createElement('input');
            paymentIdInput.type = 'hidden';
            paymentIdInput.name = 'razorpay_payment_id';
            paymentIdInput.value = response.razorpay_payment_id;
            form.appendChild(paymentIdInput);
            // Disable submit button to prevent multiple submissions
            var submitButton = document.getElementById('pay&register');
            submitButton.disabled = true;
            // Set the form action and method
            form.action = "{{ route('armwrestling.register') }}";
            form.method = "POST";
            
            // Submit the form
            form.submit();
        },
        "prefill": {
            "name": fullname,
            "email": email,
            "contact": mobile
        },
        "theme": {
            "color": "#3399cc"
        }
    };
    var rzp = new Razorpay(options);
    rzp.open();
});
</script>
    
@endsection