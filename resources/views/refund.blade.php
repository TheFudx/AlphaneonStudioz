@extends('layouts.main')
@section('title')
    Alphastudioz | Refund Policy
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
            margin-bottom: 8px;
            font-size: 1rem;
            line-height: 1.6;
        }
        hr {
            color: #b9a8a6;
        }
    </style>
    <section id="section-home-newdes" class="podcast mt-5 pt-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container">
                    <span class="heading-text d-block mb-3">Refund Policy</span>
                    <p>
                        Thank you for choosing Alphaneon Studioz for your entertainment needs. We strive to provide you with
                        a seamless and enjoyable streaming experience. Please read the following policy regarding refunds
                        carefully.
                    </p>
                    <hr class="mb-4">
                    <span class="heading-text d-block mb-3">1. Subscription Fees and Content Purchases</span>
                    <p>
                        Subscribers have the flexibility to cancel their subscription at any time. Upon cancellation,
                        subscribers will continue to have access to the service until the end of their current billing
                        cycle. However, no refunds will be issued for the remaining days or months of the subscription term.
                        This policy applies for monthly subscription plans as well as individual content purchases.
                    </p>
                    <span class="heading-text d-block mb-3">2. Access to Content</span>
                    <p>
                        Upon subscribing or purchasing content, you gain immediate access to our extensive library of
                        entertainment offerings. As such, refunds cannot be issued once access to content has been granted.
                        We encourage users to explore our content offerings and make informed decisions before subscribing
                        or making any purchases.
                    </p>
                    <span class="heading-text d-block mb-3">3. Changes to Subscription Plans or Content Availability</span>
                    <p>
                        We reserve the right to modify our subscription plans, content library, or features offered on our
                        platform at any time, with or without notice. These changes may affect the availability of certain
                        content or features included in your subscription. However, such changes do not entitle users to
                        refunds for subscription fees already paid.
                    </p>
                    <span class="heading-text d-block mb-3">4. Contact Us</span>
                    <p>
                        If you encounter any issues or have questions regarding our refund policy, please don't hesitate to
                        contact our customer support team at <a href="mailto:info@alphastudioz.in" style="color:#FF3A1F;">info@alphastudioz.in</a>.
                        We're here to assist you and address any concerns you may have.
                    </p>
                    <p>
                        By using Alphaneon Studioz, you agree to abide by the terms of this refund policy. We appreciate your
                        understanding and support as we continue to improve and enhance our services for our valued users.
                    </p>
                    <p><strong>Sincerely,<br>Alphaneon Studioz Team</strong></p>
                </div>
            </div>
        </div>
    </section>
@endsection
