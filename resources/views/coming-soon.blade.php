@extends('layouts.main')
@section('title')
    Alphastudioz | Coming Soon
@endsection
@section('main-section')
    <section id="section-home-newdes" class="podcast mt-5 pt-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid">
                    {{-- <span class="l-head">Coming Soon!</span> 

                    <hr class="mb-3"> --}}
                    <div class="slider-container mt-2">
                        <div class="latest-release-slider movies-page-section">
                            <div class="row g-1">
                                <div class="col-12 text-center">
                                    <img src="{{ URL::to('asset/images/coming-soon-2.png') }}" alt="Coming soon"
                                        class="img-fluid" style="max-width: 300px; margin-top: 10px;">
                                    <h3 class="text-white mt-3">Exciting Content on its Way</h3> {{-- Main heading --}}
                                    <p class="text-white">This section is currently under construction. Please check back
                                        soon for new and exciting videos!</p> {{-- Descriptive text --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection