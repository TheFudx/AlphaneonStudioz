@extends('layouts.main')
@section('title')
    Alphastudioz | Privacy & Policy
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
                    <span class="heading-text d-block mb-3">Privacy Policy</span>
                    <p>
                        This page informs you of our policies regarding the collection, use, and disclosure of personal
                        data when you use our Service and the choices you have associated with that data. We use your
                        data to provide and improve the Service. By using the Service, you agree to the collection and
                        use of information in accordance with this policy.
                    </p>
                    <hr class="mb-4">
                    <span class="heading-text d-block mb-3">Information Collection and Use</span>
                    <p>
                        We collect several different types of information for various purposes to provide and improve
                        our Service to you.
                    </p>
                    <span class="heading-text d-block mb-3">Types of Data Collected</span>
                    <p><strong>Personal Data:</strong></p>
                    <p>
                        While using our Service, we may ask you to provide us with certain personally identifiable
                        information that can be used to contact or identify you. Personally identifiable information may
                        include, but is not limited to:
                    </p>
                    <ul>
                        <li>First name and last name</li>
                        <li>Email address</li>
                        <li>Contact Number</li>
                        <li>Cookies and Usage Data</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
