@extends('layouts.main')
@section('title')
    Alphastudioz | About Us
@endsection
@section('main-section')
    <style>
        .heading-text {
            font-size: 25px;
            font-family: Rubic-Regular;
            color: #FF3A1F;
        }
        .form-control {
            background-color: rgba(84, 83, 83, 0.6);
            border: none;
            border-radius: 6px;
            outline: none;
            color: #fff;
            font-size: 16px;
            padding: 16px;
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
        .container p {
            color: #fff;
            text-align: justify;
            word-wrap: break-word;
        }
        .value {
            margin: 20px 0;
            padding: 20px;
            border-left: 5px solid whitesmoke;
            position: relative;
            overflow: hidden;
            border-radius: 5px;
            transition: transform 0.3s ease;
        }
        .value:hover {
            transform: scale(1.05);
            border-left-color: #FF3A1F;
        }
        .value h3 {
            margin: 0;
            font-size: 1.5em;
            color: #FF3A1F;
            font-weight: 300;
        }
        .value p {
            margin: 10px 0 0;
            color: whitesmoke;
        }
        .card-fixed-size {
            height: 100%;
        }
        .card:hover {
            transform: scale(1.05);
            transition: 0.3s ease;
        }
        hr {
            color: #b9a8a6
        }
    </style>
    <section id="section-home-newdes" class="podcast mt-5 pt-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container">
                    <span class="heading-text">About Us</span>
                    <p>Alphaneon Studioz is an OTT platform where we stream Movies, Web Series, TV Shows, Music,
                                Short Films, Trailers. Alphaneon Studioz is an entertainment platform where we are offering
                                a wide range of content. Alphaneon Studioz provides you the best video streaming experience.
                            </p>
                    <p>Enjoy all your favourite episodes of serials, movies, web series, interesting short clips and
                                more on your Web browser! Enjoy and browse through Hindi, English, Tamil, Kannada, Marathi,
                                Telugu, Bengali, Gujarati and Punjabi content. Also soon we are coming with our android and
                                IOS Application.</p>
                    <p>Alphaneon Studioz aims to revolutionize the way audiences consume entertainment. We pride
                                ourselves on offering a diverse range of content, including movies, web series, TV shows,
                                music, short films, and trailers, catering to a wide array of tastes and preferences. Our
                                platform provides a seamless and immersive viewing experience, allowing users to enjoy their
                                favorite content anytime, anywhere. With a focus on quality and innovation, we strive to
                                deliver engaging and memorable entertainment to our subscribers. Join us on Alphaneon
                                Studioz and explore a world of limitless entertainment possibilities.</p>
                    <hr class="mb-3">
                    <span class="heading-text">Overview</span>
                    <p>Alphaneon Studioz Private Limited is a burgeoning media and entertainment company based in
                                Mumbai, India. Established in 2023, the company has quickly positioned itself as a dynamic
                                player in the industry, focusing on innovative content creation and digital storytelling.
                            </p>
                    <hr class="mb-3">
                    <span class="heading-text">Vision</span>
                    <p>To become a leading entertainment studio known for groundbreaking and diverse content that
                                captivates global audiences.</p>
                    <hr class="mb-3">
                    <span class="heading-text">Mission</span>
                    <p>To produce high-quality, engaging, and culturally rich media content. To leverage
                                cutting-edge technology and creative talent to push the boundaries of traditional</p>
                    <hr class="mb-3">
                    <span class="heading-text">Core Values</span>
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="value bg-dark">
                                    <h3>Creativity</h3>
                                    <p>Encouraging out-of-the-box thinking and originality in every project.</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="value bg-dark">
                                    <h3>Quality</h3>
                                    <p>Commitment to excellence in production values and narrative.</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="value bg-dark">
                                    <h3>Innovation</h3>
                                    <p>Continually adopting new technologies and methodologies to stay ahead in the
                                        industry.</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="value bg-dark">
                                    <h3>Integrity</h3>
                                    <p>Maintaining ethical standards and transparency in all business practices.</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="value bg-dark">
                                    <h3>Collaboration</h3>
                                    <p>Building a diverse team and working with partners to enhance the creative process.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="mb-3">
                    <span class="heading-text">Services</span>
                    <div class="row mt-3 p-4">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card bg-dark p-4 card-fixed-size">
                                <h4 style="color: #FF3A1F;">Film Production</h4>
                                <p class="text-white">Creating feature films that range from mainstream commercial cinema to independent
                                        and art-house projects.</p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card bg-dark p-4 card-fixed-size">
                                <h4 style="color: #FF3A1F;">Television & Web Series</h4>
                                <p>Developing episodic content for TV and digital platforms, tailored to various genres
                                        and demographics.</p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card bg-dark p-4 card-fixed-size">
                                <h4 style="color: #FF3A1F;">Animation and VFX</h4>
                                <p>Providing top-notch animation and visual effects services for films, advertisements,
                                        and gaming industries.</p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card bg-dark p-4 card-fixed-size">
                                <h4 style="color: #FF3A1F;">Digital Content</h4>
                                <p>Producing short films, web content, and other digital media to engage audiences
                                        across online platforms.</p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card bg-dark p-4 card-fixed-size">
                                <h4 style="color: #FF3A1F;">Post-Production</h4>
                                <p>Offering comprehensive post-production services including editing, sound design, and
                                        color grading.</p>
                            </div>
                        </div>
                    </div>
                    <hr class="mb-3">
                    <div class="contact-delails">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-4 mb-4">
                                <div class="quick-contact">
                                    <div class="p-2 mt-3">
                                        <div class="row">
                                            <div class="col-2 border-end pe-4">
                                                <img src="{{ url('/') }}/asset/icons/telephone.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="col-10 d-flex align-items-center ps-4">
                                                <div class="phone-contact">
                                                    <p class="mb-0"><strong>Phone</strong></p>
                                                    <p class="mt-1 mb-0"><a class="text-decoration-none text-white" href="tel:+919819555357">+91 98195 55357</a></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4 mb-4">
                                <div class="quick-contact">
                                    <div class="p-2 mt-3">
                                        <div class="row">
                                            <div class="col-2 border-end pe-4">
                                                <img src="{{ url('/') }}/asset/icons/gmail.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="col-10 d-flex align-items-center ps-4">
                                                <div class="phone-contact">
                                                    <p class="mb-0"><strong>Email</strong></p>
                                                    <p class="mt-1 mb-0"><a class="text-decoration-none text-white" href="mailto:info@alphastudioz.in">info@alphastudioz.in</a></p>
                                                    <p class="mt-1 mb-0"><a class="text-decoration-none text-white" href="mailto:contact@alphaneonstudioz.in">contact@alphaneonstudioz.in</a></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4 mb-4">
                                <div class="quick-contact">
                                    <div class="p-2 mt-3">
                                        <div class="row">
                                            <div class="col-2 border-end pe-4">
                                                <img src="{{ url('/') }}/asset/icons/web.png" class="img-fluid" alt="">
                                            </div>
                                            <div class="col-10 d-flex align-items-center ps-4">
                                                <div class="phone-contact">
                                                    <p class="mb-0"><strong>Website</strong></p>
                                                    <p class="mt-1 mb-0"><a class="text-decoration-none text-white" href="https://alphastudioz.in/">https://alphastudioz.in/</a></p>
                                                </div>
                                            </div>
                                        </div>
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
