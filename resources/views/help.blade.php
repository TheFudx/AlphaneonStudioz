@extends('layouts.main') 
@section('title')
    Alphastudioz | Help
@endsection
@section('main-section')
<div class="row ms-0 me-0">
    <div class="col-sm-1 col-12"></div>
    <div class="col-sm-12 col-12 mt-5 ps-md-5 ps-1 ms-md-4 ms-1">
        <div class="helphead">
            <h1>FAQ Help Centre</h1>
        </div>
        <div class="p-4">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button
                            class="accordion-button"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseOne"
                            aria-expanded="true"
                            aria-controls="collapseOne"
                        >
                            1. What is Alphaneon Studioz?
                        </button>
                    </h2>
                    <div
                        id="collapseOne"
                        class="accordion-collapse collapse show"
                        aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample"
                    >
                        <div class="accordion-body">
                            - Alphanneon Studioz is an online streaming platform
                            that offers a wide range of movies, TV shows, and
                            original content for subscribers to watch anytime,
                            anywhere.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo"
                            aria-expanded="false"
                            aria-controls="collapseTwo"
                        >
                            2. How does Alphaneon Studioz work?
                        </button>
                    </h2>
                    <div
                        id="collapseTwo"
                        class="accordion-collapse collapse"
                        aria-labelledby="headingTwo"
                        data-bs-parent="#accordionExample"
                    >
                        <div class="accordion-body">
                            - To use Alphaneon Studioz, simply sign up for an
                            account and choose a subscription plan. Once subscribed,
                            you can access our content library through our website
                            or dedicated mobile apps on various devices.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseThree"
                            aria-expanded="false"
                            aria-controls="collapseThree"
                        >
                            3. What devices can I use to watch Alphaneon Studioz?
                        </button>
                    </h2>
                    <div
                        id="collapseThree"
                        class="accordion-collapse collapse"
                        aria-labelledby="headingThree"
                        data-bs-parent="#accordionExample"
                    >
                        <div class="accordion-body">
                            - Alphaneon Studioz is available on a variety of devices
                            including smartphones, tablets, smart TVs, streaming
                            media players, and gaming consoles. You can find our app
                            on major app stores or visit our website on supported
                            web browsers.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseFour"
                            aria-expanded="false"
                            aria-controls="collapseFour"
                        >
                            4. Can I download content to watch offline?
                        </button>
                    </h2>
                    <div
                        id="collapseFour"
                        class="accordion-collapse collapse"
                        aria-labelledby="headingFour"
                        data-bs-parent="#accordionExample"
                    >
                        <div class="accordion-body">
                            - Yes, many titles on Alphaneon Studioz are available
                            for offline viewing. Simply look for the download icon
                            next to the content you want to watch offline and
                            download it to your device.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseFive"
                            aria-expanded="false"
                            aria-controls="collapseFive"
                        >
                            5. Is there a free trial available?
                        </button>
                    </h2>
                    <div
                        id="collapseFive"
                        class="accordion-collapse collapse"
                        aria-labelledby="headingFive"
                        data-bs-parent="#accordionExample"
                    >
                        <div class="accordion-body">
                            - Yes, we offer a 1 Month free trial period for new
                            subscribers. During this time, you can explore our
                            content library and features before committing to a
                            subscription.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseSix"
                            aria-expanded="false"
                            aria-controls="collapseSix"
                        >
                            6. How do I cancel my subscription?
                        </button>
                    </h2>
                    <div
                        id="collapseSix"
                        class="accordion-collapse collapse"
                        aria-labelledby="headingSix"
                        data-bs-parent="#accordionExample"
                    >
                        <div class="accordion-body">
                            - Subscribers have the flexibility to cancel their
                            subscription at any time. Upon cancelation, subscribers
                            will continue to have access to the service until the
                            end of their current billing cycle. However, no refunds
                            will be issued for the remaining days or months of the
                            subscription term.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSeven">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseSeven"
                            aria-expanded="false"
                            aria-controls="collapseSeven"
                        >
                            7. Is Alphaneon Studioz available in my country?
                        </button>
                    </h2>
                    <div
                        id="collapseSeven"
                        class="accordion-collapse collapse"
                        aria-labelledby="headingSeven"
                        data-bs-parent="#accordionExample"
                    >
                        <div class="accordion-body">
                            - Alphaneon Studioz is available in many countries
                            worldwide. To see if it's available in your country,
                            visit our website or contact our customer support team
                            for assistance.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEight">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseEight"
                            aria-expanded="false"
                            aria-controls="collapseEight"
                        >
                            8. What payment methods do you accept?
                        </button>
                    </h2>
                    <div
                        id="collapseEight"
                        class="accordion-collapse collapse"
                        aria-labelledby="headingEight"
                        data-bs-parent="#accordionExample"
                    >
                        <div class="accordion-body">
                            - We accept a variety of payment methods including
                            credit/debit cards, PayPal, and other regional payment
                            options depending on your location.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNine">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseNine"
                            aria-expanded="false"
                            aria-controls="collapseNine"
                        >
                            9. Are subtitles available for non-English content?
                        </button>
                    </h2>
                    <div
                        id="collapseNine"
                        class="accordion-collapse collapse"
                        aria-labelledby="headingNine"
                        data-bs-parent="#accordionExample"
                    >
                        <div class="accordion-body">
                            - Yes, we offer subtitles for many titles in multiple
                            languages. You can enable subtitles by selecting your
                            preferred language from the options provided while
                            watching content.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTen">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseTen"
                            aria-expanded="false"
                            aria-controls="collapseTen"
                        >
                            10. How do I contact customer support?
                        </button>
                    </h2>
                    <div
                        id="collapseTen"
                        class="accordion-collapse collapse"
                        aria-labelledby="headingTen"
                        data-bs-parent="#accordionExample"
                    >
                        <div class="accordion-body">
                            - If you have any questions or need assistance, you can
                            reach our customer support team through the contact form
                            on our website or via email at info@alphastudioz.in.
                            We're here to help!
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
    </div>
</div>
@endsection
