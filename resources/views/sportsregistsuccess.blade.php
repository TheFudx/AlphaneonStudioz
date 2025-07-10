@extends('layouts.main')
@section('title')
    Alphastudioz | Sports Register
@endsection
@section('main-section')
<div class="row ms-0 me-0">
    <div class="col-sm-1 col-12">
        
    </div>
<div class="col-sm-11 col-12 mt-5 p-1">
   
       <section id="registration">
        <div class="registration-section ">
            <div class="registration-section-container pt-5">
                <div class="container">
                    <div class="row">
                        <div class="col-6">
                            <span class="heading d-block ">Register Details</span>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <a href="{{ route('registration.pdf', $registration->id) }}" class="btn-mood ps-5 pe-5 d-inline-block mt-2" style="text-decoration: none;">Download PDF</a>
                            </div>
                        </div>
                    </div>
                    
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
                   
                    <div class="registration-details">
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-0"><strong>Full Name:</strong> </p>
                                <p>{{ $registration->fullname }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-0"><strong>Email:</strong></p>
                                <p> {{ $registration->email }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-0"><strong>Mobile:</strong></p>
                                <p> {{ $registration->mobile }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Age:</strong> {{ $registration->age }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Weight:</strong> {{ $registration->weight }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Injury:</strong> {{ $registration->injury }}</p>
                            </div>
                           
                        </div>
                        <div class="detail-container">
                            <div class="row">
                                <div class="col-md-6 ">
                                    <p class="mb-0"><strong>Dominant Hand:</strong> </p>
                                    <p >{{ $registration->dhand }}</p>
                                </div>
                                <div class="col-md-6 ">
                                    <p class="mb-0"><strong>Experience:</strong> </p>
                                    <p>{{ $registration->experience }}</p>
                                </div>
                            </div>
    
                        </div>
                      
                        <div class="venu-details">
                            <h3 class="text-center mb-5">Venue and Time Details </h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-0"><strong>Venu:</strong> </p>
                                    <p>Mumbai</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-0"><strong>Date:</strong> </p>
                                    <p>30 SEP, 2024</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-0"><strong>Time:</strong> </p>
                                    <p>11.00 AM to 5.00 PM</p>
                                </div>
                            </div>
                        </div>
                       
                        
                        
                        
                       
                       
                     
                     <div class="amount text-end">
                         <p class="mt-4"><strong>Amount Paid:</strong> ₹ {{ $registration->amount }}</p>
                    </div>   
                
                        
                      </div>
                      <div class="text-center">
                          <a href="{{ route('registration.pdf', $registration->id) }}" class="btn-mood ps-5 pe-5 d-inline-block mt-5" style="text-decoration: none;">Download PDF</a>
                      </div>
                    
                    </div>
                </div>
            </div>
        </div>
       </section>
    </div>
</div>
   
@endsection