@extends('layouts.main')
@section('title')
    {{ config('app.project_name') }} | Contact Us
@endsection
@section('main-section')
    <style>
        .form-control {
            background-color: rgba(84, 83, 83, 0.6);
            border: none;
            border-radius: 6px;
            outline: none;
            color: #fff;
            font-size: 16px;
            padding: 16px 16px;
            margin-top: 10px
        }

        .form-control::placeholder {
            color: #FFFFFF;
            opacity: 0.5;
        }

        .contactus-page-btn {
            background-color: #FF3A1F;
            border: none;
            border-radius: 6px;
            outline: none;
            color: #fff;
            font-size: 17px;
            padding: 12px 20px;
            font-weight: 600;
        }

        .quick-contact {
            background-color: #2a2a2a;
            border-radius: 10px;
        }

        .contactus-page-btn {
            background: linear-gradient(to top, #AA1E0B, #AA1E0B);
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .contactus-page-btn:hover {
            background: #cc2a1b;
        }
    </style>
   <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
    <section id="section-home-newdes" class="podcast mt-5 pt-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container">
                    <span class="l-head">Get in Touch with us</span>
                    <p class="text-white">We're here to help! Contact us anytime for support and inquiries.</p>
                    <hr class="mb-3">
                    <div class="row">
                        <!-- Contact Form -->
                        <div class="col-12 col-lg-6 mb-4">

                            {{-- Alert --}}
                            <div id="contact-alert" class="d-none mb-3"></div>

                            <form id="contactForm" action="{{ route('contact.submit') }}" method="POST">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="form-control" name="firstname" id="firstname"
                                            placeholder="First name" required>
                                        <div class="text-danger small mt-1 d-none" id="error-firstname"></div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="form-control" name="lastname" id="lastname"
                                            placeholder="Last name" required>
                                        <div class="text-danger small mt-1 d-none" id="error-lastname"></div>
                                    </div>
                                    <div class="col-12">
                                        <input type="email" class="form-control" name="email" id="email"
                                            placeholder="Email"required>
                                        <div class="text-danger small mt-1 d-none" id="error-email"></div>
                                    </div>
                                    <div class="col-12">
                                        <input type="number" class="form-control" name="mobile" id="mobile"
                                            placeholder="Mobile No." maxlength="10" required> 
                                        <div class="text-danger small mt-1 d-none" id="error-mobile"></div>
                                    </div>
                                    <div class="col-12">
                                        <textarea rows="5" class="form-control" name="message" id="message" placeholder="Enter Message"></textarea>
                                        <div class="text-danger small mt-1 d-none" id="error-message"></div>
                                    </div>
                                    <div class="col-12">
                                            <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                                            <div class="text-danger small mt-1 d-none" id="error-recaptcha"></div>
                                        </div>
                                    <div class="col-12 mt-3">
                                        <button type="submit" id="contactSubmitBtn"
                                            class="btn btn-primary w-100 contactus-page-btn">
                                            <span id="contactBtnText">Send Message</span>
                                            <span id="contactBtnSpinner" class="spinner-border spinner-border-sm d-none"
                                                role="status"></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-12 col-lg-6 text-white">
                            {{-- <div class="quick-contact p-3 mb-4 bg-dark rounded">
                                <div class="d-flex align-items-center">
                                    <img src="{{ url('/') }}/asset/icons/telephone.png" class="img-fluid me-3" style="max-width: 40px;" alt="telephone.png">
                                    <div>
                                        <p class="mb-0"><strong>Phone</strong></p>
                                        <p class="mb-0"><a class="text-decoration-none text-white" href="tel:+919819555357">+91 98195 55357</a></p>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="quick-contact p-3 mb-4 bg-dark rounded">
                                <div class="d-flex align-items-center">
                                    <img src="{{ url('/') }}/asset/icons/gmail.png" class="img-fluid me-3"
                                        style="max-width: 40px;" alt="gmail.png">
                                    <div>
                                        <p class="mb-0"><strong>Email</strong></p>
                                        <p class="mb-0"><a class="text-decoration-none text-white"
                                                href="mailto:{{ 'info@' . strtolower(config('app.project_name')) . '.in' }}">{{ 'info@' . strtolower(config('app.project_name')) . '.in' }}</a></p>
                                    </div>
                                </div>
                            </div>
                            <div class="quick-contact p-3 bg-dark rounded">
                                <div class="d-flex align-items-start">
                                    <img src="{{ url('/') }}/asset/icons/pin.png" class="img-fluid me-3"
                                        style="max-width: 40px;" alt="pin.png">
                                    <div>
                                        <p class="mb-0"><strong>Address</strong></p>
                                        <p class="mb-0"><a class="text-decoration-none text-white"
                                                href="https://maps.app.goo.gl/7TsjGLCPgc1JoNgd9" target="_blank">
                                                Naman Midtown, A 202, Senapati Bapat Marg,<br>
                                                Dadar West, Mumbai, Maharashtra 400013</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {

            $('#contactForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.text-danger').addClass('d-none').text('');
                $('.form-control').removeClass('is-invalid');
                $('#contact-alert').addClass('d-none').html('');

                // Show spinner
                $('#contactBtnText').text('Sending...');
                $('#contactBtnSpinner').removeClass('d-none');
                $('#contactSubmitBtn').prop('disabled', true);

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Show success
                        $('#contact-alert')
                            .removeClass('d-none alert-danger')
                            .addClass('alert alert-success')
                            .html(response.message || 'Message sent successfully!');

                        // Reset form
                        $('#contactForm')[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    // map 'g-recaptcha-response' → 'recaptcha'
                                    const fieldKey = key === 'g-recaptcha-response' ? 'recaptcha' : key;
                                    $('#error-' + fieldKey).removeClass('d-none').text(value[0]);
                                    $('#' + key).addClass('is-invalid');
                                });
                            } else {
                            $('#contact-alert')
                                .removeClass('d-none alert-success')
                                .addClass('alert alert-danger')
                                .html('Something went wrong. Please try again.');
                        }
                    },
                    complete: function() {
                        $('#contactBtnText').text('Send Message');
                        $('#contactBtnSpinner').addClass('d-none');
                        $('#contactSubmitBtn').prop('disabled', false);
                        if (typeof grecaptcha !== 'undefined') {
                            grecaptcha.reset(); // Reset on both success and error
                        }
                    }
                });
            });

        });
    </script>
@endsection
