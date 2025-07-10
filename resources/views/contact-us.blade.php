@extends('layouts.main')
@section('title')
    Alphastudioz | Contact Us
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
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <form action="{{ route('contact.submit') }}" method="POST">
            @csrf
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <input type="text" class="form-control" name="firstname" placeholder="First name" required>
                </div>
                <div class="col-12 col-md-6">
                    <input type="text" class="form-control" name="lastname" placeholder="Last name" required>
                </div>
                <div class="col-12">
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                </div>
                <div class="col-12">
                    <input type="text" class="form-control" name="mobile" placeholder="Mobile No." required>
                </div>
                <div class="col-12">
                    <textarea rows="5" class="form-control" name="message" placeholder="Enter Message" required></textarea>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" name="submit" class="btn btn-primary w-100 contactus-page-btn">Send Message</button>
                </div>
            </div>
        </form>
    </div>
    <!-- Contact Information -->
    <div class="col-12 col-lg-6 text-white">
        <div class="quick-contact p-3 mb-4 bg-dark rounded">
            <div class="d-flex align-items-center">
                <img src="{{ url('/') }}/asset/icons/telephone.png" class="img-fluid me-3" style="max-width: 40px;" alt="">
                <div>
                    <p class="mb-0"><strong>Phone</strong></p>
                    <p class="mb-0"><a class="text-decoration-none text-white" href="tel:+919819555357">+91 98195 55357</a></p>
                </div>
            </div>
        </div>
        <div class="quick-contact p-3 mb-4 bg-dark rounded">
            <div class="d-flex align-items-center">
                <img src="{{ url('/') }}/asset/icons/gmail.png" class="img-fluid me-3" style="max-width: 40px;" alt="">
                <div>
                    <p class="mb-0"><strong>Email</strong></p>
                    <p class="mb-0"><a class="text-decoration-none text-white" href="mailto:info@alphastudioz.in">info@alphastudioz.in</a></p>
                </div>
            </div>
        </div>
        <div class="quick-contact p-3 bg-dark rounded">
            <div class="d-flex align-items-start">
                <img src="{{ url('/') }}/asset/icons/pin.png" class="img-fluid me-3" style="max-width: 40px;" alt="">
                <div>
                    <p class="mb-0"><strong>Address</strong></p>
                    <p class="mb-0"><a class="text-decoration-none text-white" href="https://maps.app.goo.gl/7TsjGLCPgc1JoNgd9" target="_blank">
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
@endsection
