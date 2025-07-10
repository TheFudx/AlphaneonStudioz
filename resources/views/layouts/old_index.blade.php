@extends('layouts.main')
@section('main-section')
{{-- <div id="page-loader" style="display: none;">
    <img src="{{url('/')}}/asset/images/loader.gif" alt="Loading...">
</div> --}}
<div class="row ms-0 me-0">
    <div class="col-sm-1 col-12">
        
    </div>
    <div class="col-sm-12 col-12 mt-0 p-0">
        <section id="banners" class="banner-section-main-class">
            <div class="banner-section">
                <div id="movie-banner" class="owl-carousel owl-theme" >
                    <div class="item"  data-bg-desktop="https://alphastudioz.in/admin_panel/public/images/video/651734103865.jpg" 
                    data-bg-mobile="https://alphastudioz.in/admin_panel/public/images/video/741734104221.jpg">
                       <div class="carousel-caption d-md-block overlay">
                        <div class="movie-short-description">
                            <div class="row">
                                <div class="col-sm-10 col-12">
                                    <div class="short-data position-absolute pb-5 col-md-9 col-6">
                                        <h5>Bobby Vats Podcast | Alpha Talks Podcast Episode 03 | Presented By Alphaneon Studiozz</h5>
                                        <div class="line"></div>
                                        <p class="mb-1 ">2024 | 30 min 30 Sec | Hindi</p>
                                       <ul class="mb-1">
                                          <li>Podcast |</li>
                                          <li>Feel-good </li>
                                       </ul>
                                      
                                       <div class="d-flex position-relative mt-3">
                                        <a href="https://alphastudioz.in/podcast/view/167/watch=true">     <button type="button" class="btn-common">
                                                <i class="icon-play"></i>Watch Now
                                            </button>
                                        </a>
                                    
                                  
                                    </div>
                                    
                                    </div>
                                    
                                </div>
                                <div class="col-sm-3 col-12 d-sm-block d-none">
                                             
                                </div>
                            </div>
                       </div>
                    
                    </div>
                  </div>
                  
                  
                    
                </div>
            </div>
        
        </section>
        @if (!empty($watchlist))
        <section id="Upcoming-Movies"  class="watchlist-margin-set">
            <div class="Upcoming-Movies-section">
                <div class="Upcoming-Movies-section-container">
                    <div class="row">
                       
                        <div class="col-md-5 col-6 ps-md-5 ps-1 ms-md-5 ms-1">
                            <span class="d-block">Your Watchlist</span>
                        </div>
                        <div class="col-md-6 col-6">
                            <p class="view-link me-sm-4 me-0 pt-sm-0 pt-1"><a href="{{route('watchlist.list')}}" class="d-flex">View all <i class="right-arrow-white"></i></a></p>
                        </div>
                    </div>
                    <div class="row">
                       
                        <div class="col-md-12 col-12 ps-md-5 ps-1 ms-md-5 ms-1">
                            <div id="watchlistsec" class="owl-carousel owl-theme">
                                @foreach ($watchlist as $key => $item)
                                @php
                                    $entity = null; // Ensure we clear previous entities
                                    if ($item['type'] === 'video') {
                                        $entity = App\Models\Video::find($item['id']);
                                    } elseif ($item['type'] === 'episode') {
                                        $entity = App\Models\Episodes::find($item['id']);
                                    }
                                @endphp
                            
                                @if ($entity)
                                    @php
                                        $isFirstCard = ($loop->index % 6 == 0);
                                        $isSixthCard = ($loop->index % 6 == 5);
                                    @endphp
                                    <div class="item">
                                        <div class="movie-card">
                                            <div class="image-container">
                                                @if($item['type'] === 'video')
                                            <img src="https://alphastudioz.in/admin_panel/public/images/video/{{ $entity->thumbnail }}" class="card-img-top thumbnail" alt="..." id="image">    
                                            <img src="https://alphastudioz.in/admin_panel/public/images/video/{{ $entity->landscape }}" class="card-img-top landscape" alt="..." id="image">
                                        @else
                                            <img src="https://alphastudioz.in/admin_panel/public/images/series/{{ $entity->thumbnail }}" class="card-img-top thumbnail" alt="..." id="image">    
                                            <img src="https://alphastudioz.in/admin_panel/public/images/series/{{ $entity->landscape }}" class="card-img-top landscape" alt="..." id="image">
                                        @endif   
                                            </div>
                                            <div class="card-body">
                                                <div class="glass-effect">
                                                    <div class="glass"></div>
                                                    <div class="card-main-content p-2">
                                                        <div class="d-flex position-relative">
                                                        
                                                            @if($item['type'] === 'video')
                                                        <a href="{{ route('view.details', ['id' => $entity->id]) }}">
                                                            <button type="button" class="btn-common">
                                                                <i class="icon-bl-play"></i>Watch Now
                                                            </button>
                                                        </a>
                                                    @else
                                                        <a href="">
                                                            <button type="button" class="btn-common">
                                                                <i class="icon-bl-play"></i>Watch Now
                                                            </button>
                                                        </a>
                                                    @endif
                                                       
                                                         @if(in_array(['id' => $entity->id, 'type' => 'video'], $watchlist) || in_array(['id' => $entity->id, 'type' => 'episode'], $watchlist))
                                                            <form action="{{ route('watchlist.remove') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="video_id" value="{{ $entity->id }}">
                                                                <button type="submit" class="btn-watchlist ms-2">
                                                                    <i class="correct-icon"></i>
                                                                    <div class="toast-message-box-rm">Remove from Watch List</div>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('watchlist') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="video_id" value="{{ $entity->id }}">
                                                                <button type="submit" class="btn-watchlist ms-2">
                                                                    <i class="plus-icon"></i>
                                                                    
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                
                                                      <ul class="position-relative">
                                                        <li><a href="#"><img src="{{url('/')}}/asset/icons/fill-star.png" alt=""></a></li>
                                                        <li>(4.2)</li>
                                                        <li> <p class="mb-0 ">{{ \Carbon\Carbon::parse($entity->release_date)->format('Y') }} |  @if($item['type'] === 'video')
                                                            @if($entity->video_duration)
                                                              {{ MillisecondsToTime($entity->video_duration) }}
                                                              @else 
                                                                  -
                                                                  @endif |
                                                                  {{ $entity->language_name }}
                                                            @else
                                                            @if($entity->episode_duration)
                                                            {{ MillisecondsToTime($entity->episode_duration) }}
                                                        @else 
                                                            -
                                                        @endif |
                                                        {{ $entity->language_name }}
                                                            @endif</p></li>
                                                       
                                                       </ul>
                                                       
                                                      <p class="card-text position-relative">{{$entity->description}}</p>
                                    
                                                    </div>
                                                   
                                                </div>
                                                
                                            </div>
                                          </div>
                                       
                                    </div>
                                @endif
                            @endforeach
                                
                            </div>
                        </div>
                       
                    </div>
                
                </div>
            </div>
        </section>
        @else
      
        @endif
      
  
        <section id="Upcoming-Movies"
        @if (!empty($watchlist))
        
        @else
        class="watchlist-margin-set"
        @endif
        >
            <div class="Upcoming-Movies-section">
                <div class="Upcoming-Movies-section-container">
                    <div class="row">
                      
                        <div class="col-md-5 col-6 ps-md-5 ps-1 ms-md-5 ms-0">
                            <span class="d-block">Trailers</span>
                        </div>
                        <div class="col-md-6 col-6">
                            <p class="view-link me-sm-4 me-0 pt-sm-0 pt-1"><a href="" class="d-flex">View all <i class="right-arrow-white"></i></a></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-1 col-12">
                            
                        </div>
                        <div class="col-md-12 col-12">
                            <div id="trailers" class="owl-carousel owl-theme">
                                @foreach ($trailer as $trailer)
                                <div class="item">
                                  <a href="{{ route('view.details', ['id' => $trailer->id]) }}">
                                  <div class="movie-card" aria-hidden="true">
                                      <div class="image-container">
                                          <img src="https://alphastudioz.in/admin_panel/public/images/video/{{$trailer->thumbnail}}" class="card-img-top thumbnail placeholder-glow" alt="..." id="image">    
                                          <img src="https://alphastudioz.in/admin_panel/public/images/video/{{$trailer->landscape}}" class="card-img-top landscape placeholder-glow" alt="..." id="image">    
                                        
                                          
                                      </div>
                                      <div class="card-body">
                                          <div class="glass-effect">
                                              <div class="glass"></div>
                                              <div class="card-main-content p-2">
                                                  <div class="d-flex position-relative">
                                                      <a href="{{ route('view.details', ['id' =>  $trailer->id]) }}">
                                                          <button type="button" class="btn-common">
                                                              <i class="icon-bl-play"></i>Watch Now
                                                          </button>
                                                      </a>
                                                  
                                                      @if(in_array($trailer->id, $watchlist))
                                                          <form action="{{ route('watchlist.remove') }}" method="POST">
                                                              @csrf
                                                              <input type="hidden" name="video_id" value="{{ $trailer->id }}">
                                                              <button type="submit" class="btn-watchlist ms-2">
                                                                  <i class="correct-icon"></i>
                                                                  <div class="toast-message-box-rm">Remove from Watch List</div>
                                                              </button>
                                                          </form>
                                                      @else
                                                          <form action="{{ route('watchlist') }}" method="POST">
                                                              @csrf
                                                              <input type="hidden" name="video_id" value="{{ $trailer->id }}">
                                                              <button type="submit" class="btn-watchlist ms-2">
                                                                  <i class="plus-icon"></i>
                                                                  
                                                              </button>
                                                          </form>
                                                      @endif
                                                  </div>
                          
                                                <ul class="position-relative">
                                                  <li><a href="#"><img src="{{url('/')}}/asset/icons/fill-star.png" alt=""></a></li>
                                                  <li>(4.2)</li>
                                                  <li> <p class="mb-0 ">{{ \Carbon\Carbon::parse($trailer->release_date)->format('Y') }} | @if($trailer->video_duration)
                                                      {{MillisecondsToTime($trailer->video_duration)}}
                                                  @else 
                                                      -
                                                  @endif | {{$trailer->language_name}}</p></li>
                                                 
                                                 </ul>
                                                 
                                                <p class="card-text position-relative">{{$trailer->description}}</p>
                              
                                              </div>
                                             
                                          </div>
                                 
                                      </div>
                                    </div>
                                  </a>
                                  </div>
                                @endforeach
                                              
                                         
                                     
                                             
                                            </div>
                        </div>
                    </div>
                   
                 
                </div>
            </div>
        </section>
        <section id="Upcoming-Movies">
            <div class="Upcoming-Movies-section">
                <div class="Upcoming-Movies-section-container">
                    <div class="row">
                        <div class="col-md-5 col-6 ps-md-5 ps-1 ms-md-5 ms-0">
                            <span class="d-block">Upcomings</span>
                        </div>
                        <div class="col-md-6 col-6">
                            <p class="view-link me-sm-4 me-0 pt-sm-0 pt-1 "><a href="/movies/list" class="d-flex">View all <i class="right-arrow-white"></i></a></p>
                        </div>
                    </div>
                    <div class="row">
                       
                        <div class="col-md-12 col-12 ps-md-5 ps-1 ms-md-5 ms-0">
                            <div id="upcommingMovies" class="owl-carousel owl-theme">
                                @foreach ($upcomming as $upcomming)
                                <div class="item">
                                  <a href="{{ route('view.details', ['id' => $upcomming->id]) }}">
                                  <div class="movie-card" >
                                      <div class="image-container" aria-hidden="true">
                                          <img src="https://alphastudioz.in/admin_panel/public/images/video/{{$upcomming->thumbnail}}" class="card-img-top thumbnail placeholder-glow" alt="..." id="image">    
                                          <img src="https://alphastudioz.in/admin_panel/public/images/video/{{$upcomming->landscape}}" class="card-img-top landscape placeholder-glow" alt="..." id="image">    
                                        
                                          
                                      </div>
                                      <div class="card-body">
                                          <div class="glass-effect">
                                              <div class="glass"></div>
                                              <div class="card-main-content p-2">
                                                  <div class="d-flex position-relative">
                                                      <a href="{{ route('view.details', ['id' =>  $upcomming->id]) }}">
                                                          <button type="button" class="btn-common">
                                                              <i class="icon-bl-play"></i>Watch Now
                                                          </button>
                                                      </a>
                                                  
                                                      @if(in_array($upcomming->id, $watchlist))
                                                          <form action="{{ route('watchlist.remove') }}" method="POST">
                                                              @csrf
                                                              <input type="hidden" name="video_id" value="{{ $upcomming->id }}">
                                                              <button type="submit" class="btn-watchlist ms-2">
                                                                  <i class="correct-icon"></i>
                                                                  <div class="toast-message-box-rm">Remove from Watch List</div>
                                                              </button>
                                                          </form>
                                                      @else
                                                          <form action="{{ route('watchlist') }}" method="POST">
                                                              @csrf
                                                              <input type="hidden" name="video_id" value="{{ $upcomming->id }}">
                                                              <button type="submit" class="btn-watchlist ms-2">
                                                                  <i class="plus-icon"></i>
                                                                  
                                                              </button>
                                                          </form>
                                                      @endif
                                                  </div>
                          
                                                <ul class="position-relative">
                                                  <li><a href="#"><img src="{{url('/')}}/asset/icons/fill-star.png" alt=""></a></li>
                                                  <li>(4.2)</li>
                                                  <li> <p class="mb-0 ">{{ \Carbon\Carbon::parse($upcomming->release_date)->format('Y') }} | @if($upcomming->video_duration)
                                                      {{MillisecondsToTime($upcomming->video_duration)}}
                                                  @else 
                                                      -
                                                  @endif | {{$upcomming->language_name}}</p></li>
                                                 
                                                 </ul>
                                                 
                                                <p class="card-text position-relative">{{$upcomming->description}}</p>
                              
                                              </div>
                                             
                                          </div>
                                 
                                      </div>
                                    </div>
                                  </a>
                                  </div>
                                @endforeach
                                              
                                         
                                     
                                             
                                            </div>
                        </div>
                    </div>
       
                </div>
            </div>
        </section>
        <section id="Upcoming-Movies">
            <div class="Upcoming-Movies-section">
                <div class="Upcoming-Movies-section-container">
                    <div class="row">
                        <div class="col-md-5 col-6 ps-md-5 ps-1 ms-md-5 ms-0">
                            <span class="d-block">Podcast</span>
                        </div>
                        <div class="col-md-6 col-6">
                            <p class="view-link me-sm-4 me-0 pt-sm-0 pt-1 "><a href="/movies/list" class="d-flex">View all <i class="right-arrow-white"></i></a></p>
                        </div>
                    </div>
                    <div class="row">
                       
                        <div class="col-md-12 col-12 ps-md-5 ps-1 ms-md-5 ms-0">
                            <div id="podcast" class="owl-carousel owl-theme">
                                @foreach ($poadcastData as $podcast)
                                <div class="item">
                                  <a href="{{ route('view.details', ['id' => $podcast->id]) }}">
                                  <div class="movie-card">
                                      <div class="image-container">
                                          <img src="https://alphastudioz.in/admin_panel/public/images/video/{{$podcast->thumbnail}}" class="card-img-top thumbnail" alt="..." id="image">    
                                          <img src="https://alphastudioz.in/admin_panel/public/images/video/{{$podcast->landscape}}" class="card-img-top landscape" alt="..." id="image">    
                                        
                                          
                                      </div>
                                      <div class="card-body">
                                          <div class="glass-effect">
                                              <div class="glass"></div>
                                              <div class="card-main-content p-2">
                                                  <div class="d-flex position-relative">
                                                      <a href="{{ route('view.details', ['id' =>  $podcast->id]) }}">
                                                          <button type="button" class="btn-common">
                                                              <i class="icon-bl-play"></i>Watch Now
                                                          </button>
                                                      </a>
                                                  
                                                      @if(in_array($podcast->id, $watchlist))
                                                          <form action="{{ route('watchlist.remove') }}" method="POST">
                                                              @csrf
                                                              <input type="hidden" name="video_id" value="{{ $podcast->id }}">
                                                              <button type="submit" class="btn-watchlist ms-2">
                                                                  <i class="correct-icon"></i>
                                                                  <div class="toast-message-box-rm">Remove from Watch List</div>
                                                              </button>
                                                          </form>
                                                      @else
                                                          <form action="{{ route('watchlist') }}" method="POST">
                                                              @csrf
                                                              <input type="hidden" name="video_id" value="{{ $podcast->id }}">
                                                              <button type="submit" class="btn-watchlist ms-2">
                                                                  <i class="plus-icon"></i>
                                                                  
                                                              </button>
                                                          </form>
                                                      @endif
                                                  </div>
                          
                                                <ul class="position-relative">
                                                  <li><a href="#"><img src="{{url('/')}}/asset/icons/fill-star.png" alt=""></a></li>
                                                  <li>(4.2)</li>
                                                  <li> <p class="mb-0 ">{{ \Carbon\Carbon::parse($podcast->release_date)->format('Y') }} | @if($podcast->video_duration)
                                                      {{MillisecondsToTime($podcast->video_duration)}}
                                                  @else 
                                                      -
                                                  @endif | {{$podcast->language_name}}</p></li>
                                                 
                                                 </ul>
                                                 
                                                <p class="card-text position-relative">{{$podcast->description}}</p>
                              
                                              </div>
                                             
                                          </div>
                                 
                                      </div>
                                    </div>
                                  </a>
                                  </div>
                                @endforeach
                                              
                                         
                                     
                                             
                                            </div>
                        </div>
                    </div>
       
                </div>
            </div>
        </section>
        <section id="Upcoming-Movies">
            <div class="Upcoming-Movies-section">
                <div class="Upcoming-Movies-section-container">
                    <div class="row">
                        
                        <div class="col-md-5 col-6 ps-md-5 ps-1 ms-md-5 ms-0">
                            <span class="d-block">Romance</span>
                        </div>
                        <div class="col-md-6 col-6">
                            <p class="view-link me-sm-4 me-0 pt-sm-0 pt-1 "><a href="/movies/list" class="d-flex">View all <i class="right-arrow-white"></i></a></p>
                        </div>
                    </div>
                    <div class="row">
                       
                        <div class="col-md-12 col-12 ps-md-5 ps-1 ms-md-5 ms-0">
                            <div id="romance" class="owl-carousel owl-theme">
                                @foreach ($romance as $romance)
                                <div class="item">
                                  <a href="{{ route('view.details', ['id' => $romance->id]) }}">
                                      <div class="movie-card">
                                          <div class="image-container">
                                              <img src="https://alphastudioz.in/admin_panel/public/images/video/{{$romance->thumbnail}}" class="card-img-top thumbnail" alt="..." id="image">    
                                              <img src="https://alphastudioz.in/admin_panel/public/images/video/{{$romance->landscape}}" class="card-img-top landscape" alt="..." id="image">    
                                            
                                              
                                          </div>
                                          <div class="card-body">
                                              <div class="glass-effect">
                                                  <div class="glass"></div>
                                                  <div class="card-main-content p-2">
                                                      <div class="d-flex position-relative">
                                                          <a href="{{ route('view.details', ['id' =>  $romance->id]) }}">
                                                              <button type="button" class="btn-common">
                                                                  <i class="icon-bl-play"></i>Watch Now
                                                              </button>
                                                          </a>
                                                      
                                                          @if(in_array($romance->id, $watchlist))
                                                              <form action="{{ route('watchlist.remove') }}" method="POST">
                                                                  @csrf
                                                                  <input type="hidden" name="video_id" value="{{ $romance->id }}">
                                                                  <button type="submit" class="btn-watchlist ms-2">
                                                                      <i class="correct-icon"></i>
                                                                      <div class="toast-message-box-rm">Remove from Watch List</div>
                                                                  </button>
                                                              </form>
                                                          @else
                                                              <form action="{{ route('watchlist') }}" method="POST">
                                                                  @csrf
                                                                  <input type="hidden" name="video_id" value="{{ $romance->id }}">
                                                                  <button type="submit" class="btn-watchlist ms-2">
                                                                      <i class="plus-icon"></i>
                                                                      
                                                                  </button>
                                                              </form>
                                                          @endif
                                                      </div>
                              
                                                    <ul class="position-relative">
                                                      <li><a href="#"><img src="{{url('/')}}/asset/icons/fill-star.png" alt=""></a></li>
                                                      <li>(4.2)</li>
                                                      <li> <p class="mb-0 ">{{ \Carbon\Carbon::parse($romance->release_date)->format('Y') }} | @if($romance->video_duration)
                                                          {{MillisecondsToTime($romance->video_duration)}}
                                                          @else 
                                                          -
                                                          @endif | {{$romance->language_name}}</p></li>
                                                     
                                                     </ul>
                                                     
                                                    <p class="card-text position-relative">{{$romance->description}}</p>
                                  
                                                  </div>
                                                 
                                              </div>
                                     
                                          </div>
                                        </div>
                                      </a>
                              </div>
                                @endforeach
                                              
                                         
                                     
                                             
                                            </div>
                        </div>
                    </div>
       
                </div>
            </div>
        </section>
        <section id="Upcoming-Movies">
            <div class="Upcoming-Movies-section">
                <div class="Upcoming-Movies-section-container">
                    <div class="row">
                      
                        <div class="col-md-5 col-6 ps-md-5 ps-1 ms-md-5 ms-0">
                            <span class="d-block">Action</span>
                        </div>
                        <div class="col-md-6 col-6">
                            <p class="view-link me-sm-4 me-0 pt-sm-0 pt-1"><a href="/movies/list" class="d-flex">View all <i class="right-arrow-white"></i></a></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-12 ps-md-5 ps-1 ms-md-5 ms-0">
                            <div id="action" class="owl-carousel owl-theme">
                                @foreach ($action as $action)
                                <div class="item">
                                  <a href="{{ route('view.details', ['id' => $action->id]) }}">
                                      <div class="movie-card">
                                          <div class="image-container">
                                              <img src="https://alphastudioz.in/admin_panel/public/images/video/{{$action->thumbnail}}" class="card-img-top thumbnail" alt="..." id="image">    
                                              <img src="https://alphastudioz.in/admin_panel/public/images/video/{{$action->landscape}}" class="card-img-top landscape" alt="..." id="image">    
                                            
                                              
                                          </div>
                                          <div class="card-body">
                                              <div class="glass-effect">
                                                  <div class="glass"></div>
                                                  <div class="card-main-content p-2">
                                                      <div class="d-flex position-relative">
                                                          <a href="{{ route('view.details', ['id' =>  $action->id]) }}">
                                                              <button type="button" class="btn-common">
                                                                  <i class="icon-bl-play"></i>Watch Now
                                                              </button>
                                                          </a>
                                                      
                                                          @if(in_array($action->id, $watchlist))
                                                              <form action="{{ route('watchlist.remove') }}" method="POST">
                                                                  @csrf
                                                                  <input type="hidden" name="video_id" value="{{ $action->id }}">
                                                                  <button type="submit" class="btn-watchlist ms-2">
                                                                      <i class="correct-icon"></i>
                                                                      <div class="toast-message-box-rm">Remove from Watch List</div>
                                                                  </button>
                                                              </form>
                                                          @else
                                                              <form action="{{ route('watchlist') }}" method="POST">
                                                                  @csrf
                                                                  <input type="hidden" name="video_id" value="{{ $action->id }}">
                                                                  <button type="submit" class="btn-watchlist ms-2">
                                                                      <i class="plus-icon"></i>
                                                                      
                                                                  </button>
                                                              </form>
                                                          @endif
                                                      </div>
                              
                                                    <ul class="position-relative">
                                                      <li><a href="#"><img src="{{url('/')}}/asset/icons/fill-star.png" alt=""></a></li>
                                                      <li>(4.2)</li>
                                                      <li> <p class="mb-0 ">{{ \Carbon\Carbon::parse($action->release_date)->format('Y') }} | 
                                                          {{MillisecondsToTime($action->video_duration)}}
                                                    | {{$action->language_name}}</p></li>
                                                     
                                                     </ul>
                                                     
                                                    <p class="card-text position-relative">{{$action->description}}</p>
                                  
                                                  </div>
                                                 
                                              </div>
                                     
                                          </div>
                                        </div>
                                      </a>
                              </div>
                                @endforeach
                                              
                                         
                                     
                                             
                                            </div>
                        </div>
                    </div>
 
                </div>
            </div>
        </section>
        <section id="Upcoming-Movies">
            <div class="Upcoming-Movies-section">
                <div class="Upcoming-Movies-section-container">
                    <div class="row">
                       
                        <div class="col-md-5 col-6 ps-md-5 ps-1 ms-md-5 ms-0">
                            <span class="d-block">Thrillers</span>
                        </div>
                        <div class="col-md-6 col-6">
                            <p class="view-link me-sm-4 me-0 pt-sm-0 pt-1 "><a href="/movies/list" class="d-flex">View all <i class="right-arrow-white"></i></a></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-12 ps-md-5 ps-1 ms-md-5 ms-0">
                            <div id="Thrillers" class="owl-carousel owl-theme">
                                @foreach ($thrill as $thrill)
                                <div class="item">
                                  <a href="{{ route('view.details', ['id' => $thrill->id]) }}">
                                      <div class="movie-card">
                                          <div class="image-container">
                                              <img src="https://alphastudioz.in/admin_panel/public/images/video/{{$thrill->thumbnail}}" class="card-img-top thumbnail" alt="..." id="image">    
                                              <img src="https://alphastudioz.in/admin_panel/public/images/video/{{$thrill->landscape}}" class="card-img-top landscape" alt="..." id="image">    
                                            
                                              
                                          </div>
                                          <div class="card-body">
                                              <div class="glass-effect">
                                                  <div class="glass"></div>
                                                  <div class="card-main-content p-2">
                                                      <div class="d-flex position-relative">
                                                          <a href="{{ route('view.details', ['id' =>  $thrill->id]) }}">
                                                              <button type="button" class="btn-common">
                                                                  <i class="icon-bl-play"></i>Watch Now
                                                              </button>
                                                          </a>
                                                      
                                                          @if(in_array($thrill->id, $watchlist))
                                                              <form action="{{ route('watchlist.remove') }}" method="POST">
                                                                  @csrf
                                                                  <input type="hidden" name="video_id" value="{{ $thrill->id }}">
                                                                  <button type="submit" class="btn-watchlist ms-2">
                                                                      <i class="correct-icon"></i>
                                                                      <div class="toast-message-box-rm">Remove from Watch List</div>
                                                                  </button>
                                                              </form>
                                                          @else
                                                              <form action="{{ route('watchlist') }}" method="POST">
                                                                  @csrf
                                                                  <input type="hidden" name="video_id" value="{{ $thrill->id }}">
                                                                  <button type="submit" class="btn-watchlist ms-2">
                                                                      <i class="plus-icon"></i>
                                                                      
                                                                  </button>
                                                              </form>
                                                          @endif
                                                      </div>
                              
                                                    <ul class="position-relative">
                                                      <li><a href="#"><img src="{{url('/')}}/asset/icons/fill-star.png" alt=""></a></li>
                                                      <li>(4.2)</li>
                                                      <li> <p class="mb-0 ">{{ \Carbon\Carbon::parse($thrill->release_date)->format('Y') }} | @if($thrill->video_duration)
                                                          {{MillisecondsToTime($thrill->video_duration)}}
                                                      @else 
                                                          -
                                                      @endif | {{$thrill->language_name}}</p></li>
                                                     
                                                     </ul>
                                                     
                                                    <p class="card-text position-relative">{{$thrill->description}}</p>
                                  
                                                  </div>
                                                 
                                              </div>
                                     
                                          </div>
                                        </div>
                                      </a>
                              </div>
                                @endforeach
                                              
                                         
                                     
                                             
                                            </div>
                        </div>
                    </div>
         
                </div>
            </div>
        </section>
    </div>
</div>
@php
    // Check if the user is authenticated
    if (Auth::check()) {
        // Get the authenticated user
        $user = app('logged-in-user');
        // Fetch the latest transaction for the user
        $transaction = DB::table('transaction')
                        ->where('user_id', $user->id)
                        ->orderBy('expiry_date', 'desc')
                        ->first();
        
        $daysLeft = 0; // Default days left
        $subscriptionStatus = 'No'; // Default subscription status
        // Check if a transaction was found
        if ($transaction) {
            // Get the expiry date from the transaction
            $expiryDate = new DateTime($transaction->expiry_date);
            
            // Get the current date
            $currentDate = new DateTime();
            
            // Calculate the difference
            $interval = $currentDate->diff($expiryDate);
            
            // Get the number of days
            $daysLeft = $interval->format('%r%a'); // %r includes the sign to handle past dates correctly
            
            // Check if the plan is expired
            if ($daysLeft <= 0) {
                // Update the user's subscription status to 'No'
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['subscription' => 'No']);
                
                // Update the transaction status to '0'
                DB::table('transaction')
                    ->where('id', $transaction->id)
                    ->update(['status' => '0']);
                
                $subscriptionStatus = 'No';
            } else {
                $subscriptionStatus = 'Yes';
            }
        }
    }
@endphp
<!-- Pass PHP variables to JavaScript -->
<script>
    var daysLeft = {{ $daysLeft ?? 0 }};
    var subscriptionStatus = "{{ $subscriptionStatus ?? 'No' }}";
</script>
    
@endsection
       