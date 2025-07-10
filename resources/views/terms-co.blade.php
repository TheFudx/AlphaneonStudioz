@extends('layouts.main')
@section('title')
    Alphastudioz | Terms & Conditions
@endsection
@section('main-section')
    <style>
        .heading-text {
            font-size: 25px;
            font-family: Rubic-Regular;
            color: #FF3A1F;
        }
        @media (max-width: 576px) {
            .heading-text {
                font-size: 20px;
            }
        }
        .container p {
            color: #fff;
            text-align: justify;
            word-wrap: break-word;
            font-size: 1rem;
            line-height: 1.7;
        }
        .container ul {
            padding-left: 1rem;
        }
        .container ul li {
            color: #fff;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        hr {
            color: #b9a8a6;
        }
    </style>
    <section id="section-home-newdes" class="podcast mt-5 pt-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container">
                    <span class="heading-text d-block mb-3">Terms & Conditions</span>
                    <p>
                        Welcome to Alphaneon Studioz! These Terms & Conditions govern your use of our over-the-top content
                        streaming services. By accessing or using our platform, you agree to comply with these Terms &
                        Conditions. If you do not agree with any part of these terms, please do not access the platform or
                        use our services.
                    </p>
                    <hr class="mb-4">
                    <span class="heading-text d-block mb-3">1. Introduction</span>
                    <p>1.1. Alphaneon Studioz provides a platform for streaming digital content over the internet.</p>
                    <p>1.2. Our services include but are not limited to streaming movies, TV shows, documentaries, and other
                        entertainment content.</p>
                    <span class="heading-text d-block mb-3">2. User Eligibility</span>
                    <p>2.1. You must be at least 18 years old to access or use our platform.</p>
                    <p>2.2. By using our platform, you represent and warrant that you have the legal right and capacity to
                        enter into these Terms & Conditions.</p>
                    <span class="heading-text d-block mb-3">3. Account Registration</span>
                    <p>3.1. In order to access certain features of our platform, you may be required to create an account.</p>
                    <p>3.2. You agree to provide accurate and complete information when creating your account.</p>
                    <p>3.3. You are solely responsible for maintaining the confidentiality of your account information and
                        password.</p>
                    <span class="heading-text d-block mb-3">4. Payment and Subscription</span>
                    <p>4.1. Some features of our platform may require payment or subscription.</p>
                    <p>4.2. By subscribing to our services, you agree to pay the applicable fees and charges.</p>
                    <p>4.3. Subscription fees are non-refundable unless otherwise specified.</p>
                    <span class="heading-text d-block mb-3">5. Contact Us</span>
                    <p>
                        5.1. If you have any questions or concerns about these Terms & Conditions, please contact us at
                        <a href="mailto:info@alphastudioz.in" style="color: #FF3A1F;">info@alphastudioz.in</a>.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
