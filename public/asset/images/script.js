$(document).ready(function() {
    // $("#upcommingMovies").slick({
    //     slidesToShow:6,
    //     autoplay: true,
    //     slidesToScroll:1,
    //     dots: false,
    //     responsive:[
    //       {
    //         breakpoint: 768,
    //         settings: {
    //           slidesToShow: 2
    //         }
    //       },
    //       {
    //         breakpoint: 600,
    //         settings: {
    //           slidesToShow: 1
    //         }
    //       }
    //     ]
    // });
    $(document).ready(function(){
        $("#upcommingMovies").owlCarousel({
            stagePadding: 80,
            loop: true,
            margin: 20,
            nav: false,
            dots: false,
            autoplay: true,
            slideTransition: 'linear',
            autoplayTimeout: 3000,
            autoplaySpeed: 3000,
            autoplayHoverPause: true,
            responsive: {
                0: {
                    items: 3,
                    stagePadding: 0,
                    margin: 10,
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 6
                }
            }
        });
        
    
        $('#upcommingMovies .item').hover(
            function() {
                // On hover in
                 // Bring hovered item to the front
                $('#upcommingMovies .item').not(this).addClass('dimmed'); 
                $(this).addClass("hovered");

                if($(this).hasClass('dimmed')){
                    $(this).parent().css({'z-index':'-1'});
                }
                else{
                    $(this).parent().css({'z-index':'1'});
                    
                }
                if($(this).hasClass('hovered')){
                    $(".hovered .landscape").css("display","block")
                }
                else{
                  $(".hovered .landscape").css("display","none")
                   
                    
                }
            }, function() {
                // On hover out
                $(this).css({'z-index': 1, 'position':'relative'}); // Reset z-index of hovered item
                $('#upcommingMovies .item').removeClass('dimmed'); // Remove dimmed class from all items
                $('#upcommingMovies .item').removeClass("hovered");

                if($(this).hasClass('dimmed')){
                    $(this).parent().css({'z-index':'-1'});
                }
                else{
                    $(this).parent().css({'z-index':'-1'});

                }
                if($(this).hasClass('hovered')){
                    $(".landscape").css("display","none")
                }
                else{
                  $(".landscape").css("display","none")
                   
                    
                }
            }
        );

    });
    $(document).ready(function(){
        var $carousel = $("#watchlistsec").owlCarousel({
            stagePadding: 80,
            loop: false,
            margin: 20,
            nav: false,
            dots: false,
            autoplay: true, // Enable autoplay
            slideTransition: 'linear', // Custom slide transition
            autoplayTimeout: 3000, // Autoplay interval
            autoplayHoverPause: true,
            responsive: {
                0: {
                    items: 3,
                    stagePadding: 0,
                    margin: 10,
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 6
                }
            }
        });
        
    
        $('#watchlistsec .item').hover(
            function() {
                // On hover in
                 // Bring hovered item to the front
                $('#watchlistsec .item').not(this).addClass('dimmed'); 
                $(this).addClass("hovered");

                if($(this).hasClass('dimmed')){
                    $(this).parent().css({'z-index':'-1'});
                }
                else{
                    $(this).parent().css({'z-index':'1'});
                    
                }
                if($(this).hasClass('hovered')){
                    $(".hovered .landscape").css("display","block")
                }
                else{
                  $(".hovered .landscape").css("display","none")
                   
                    
                }
            }, function() {
                // On hover out
                $(this).css({'z-index': 1, 'position':'relative'}); // Reset z-index of hovered item
                $('#watchlistsec .item').removeClass('dimmed'); // Remove dimmed class from all items
                $('#watchlistsec .item').removeClass("hovered");

                if($(this).hasClass('dimmed')){
                    $(this).parent().css({'z-index':'-1'});
                }
                else{
                    $(this).parent().css({'z-index':'-1'});

                }
                if($(this).hasClass('hovered')){
                    $(".landscape").css("display","none")
                }
                else{
                  $(".landscape").css("display","none")
                   
                    
                }
            }
        );
    });
    

    $("#romance").owlCarousel({
    stagePadding:  80,
    loop:false,
    margin:20,
    nav:false,
    dots:false,
    autoplay: true,
    slideTransition: 'linear',
    autoplayTimeout: 3000,
    autoplaySpeed: 3000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items:3,
            stagePadding: 0,
            margin:10,
        },
        600:{
            items:4
        },
        1000:{
            items:6
        }
    }
    });




   $('#romance .item').hover(
            function() {
                // On hover in
                 // Bring hovered item to the front
                $('#romance .item').not(this).addClass('dimmed'); 
                $(this).addClass("hovered");

                if($(this).hasClass('dimmed')){
                    $(this).parent().css({'z-index':'-1'});
                }
                else{
                    $(this).parent().css({'z-index':'1'});
                    
                }
                if($(this).hasClass('hovered')){
                    $(".hovered .landscape").css("display","block")
                }
                else{
                  $(".hovered .landscape").css("display","none")
                   
                    
                }
            }, function() {
                // On hover out
                $(this).css({'z-index': 1, 'position':'relative'}); // Reset z-index of hovered item
                $('#romance .item').removeClass('dimmed'); // Remove dimmed class from all items
                $('#romance .item').removeClass("hovered");

                if($(this).hasClass('dimmed')){
                    $(this).parent().css({'z-index':'-1'});
                }
                else{
                    $(this).parent().css({'z-index':'-1'});

                }
                if($(this).hasClass('hovered')){
                    $(".landscape").css("display","none")
                }
                else{
                  $(".landscape").css("display","none")
                   
                    
                }
            }
    );



    $("#action").owlCarousel({
    stagePadding:  80,
    loop:false,
    margin:20,
    nav:false,
    dots:false,
    autoplay: true,
    slideTransition: 'linear',
    autoplayTimeout: 3000,
    autoplaySpeed: 3000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items:3,
            stagePadding: 0,
            margin:10,
        },
        600:{
            items:3
        },
        1000:{
            items:6
        }
    }
    });

    $('#action .item').hover(
        function() {
            // On hover in
             // Bring hovered item to the front
            $('#action .item').not(this).addClass('dimmed'); 
            $(this).addClass("hovered");

            if($(this).hasClass('dimmed')){
                $(this).parent().css({'z-index':'-1'});
            }
            else{
                $(this).parent().css({'z-index':'1'});
                
            }
            if($(this).hasClass('hovered')){
                $(".hovered .landscape").css("display","block")
            }
            else{
              $(".hovered .landscape").css("display","none")
               
                
            }
        }, function() {
            // On hover out
            $(this).css({'z-index': 1, 'position':'relative'}); // Reset z-index of hovered item
            $('#action .item').removeClass('dimmed'); // Remove dimmed class from all items
            $('#action .item').removeClass("hovered");

            if($(this).hasClass('dimmed')){
                $(this).parent().css({'z-index':'-1'});
            }
            else{
                $(this).parent().css({'z-index':'-1'});

            }
            if($(this).hasClass('hovered')){
                $(".landscape").css("display","none")
            }
            else{
              $(".landscape").css("display","none")
               
                
            }
        }
);

   

    $("#Thrillers").owlCarousel({
    stagePadding:  80,
    loop:false,
    margin:20,
    nav:false,
    dots:false,
    autoplay: true,
    slideTransition: 'linear',
    autoplayTimeout: 3000,
    autoplaySpeed: 3000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items:3,
            stagePadding: 0,
            margin:10,
        },
        600:{
            items:3
        },
        1000:{
            items:6
        }
    }
    });

    $('#Thrillers .item').hover(
        function() {
            // On hover in
             // Bring hovered item to the front
            $('#Thrillers .item').not(this).addClass('dimmed'); 
            $(this).addClass("hovered");

            if($(this).hasClass('dimmed')){
                $(this).parent().css({'z-index':'-1'});
            }
            else{
                $(this).parent().css({'z-index':'1'});
                
            }
            if($(this).hasClass('hovered')){
                $(".hovered .landscape").css("display","block")
            }
            else{
              $(".hovered .landscape").css("display","none")
               
                
            }
        }, function() {
            // On hover out
            $(this).css({'z-index': 1, 'position':'relative'}); // Reset z-index of hovered item
            $('#Thrillers .item').removeClass('dimmed'); // Remove dimmed class from all items
            $('#Thrillers .item').removeClass("hovered");

            if($(this).hasClass('dimmed')){
                $(this).parent().css({'z-index':'-1'});
            }
            else{
                $(this).parent().css({'z-index':'-1'});

            }
            if($(this).hasClass('hovered')){
                $(".landscape").css("display","none")
            }
            else{
              $(".landscape").css("display","none")
               
                
            }
        }
);

$(document).ready(function() {
    var $banners = $('.banner-section-main-class');

    // Initialize Owl Carousel
    $("#movie-banner").owlCarousel({
        items: 1,
        dots: false,
        nav: false,
        loop: true,
        autoplay: false,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        onTranslated: setParallaxBackground,
        onInitialized: setParallaxBackground
    });

    // Function to set parallax background
    function setParallaxBackground(event) {
        var item = event.item.index;
        var $currentItem = $(event.target).find(".owl-item").eq(item).find(".item");
        var bgImage;

        if ($(window).width() < 540) {
            bgImage = $currentItem.data("bg-mobile");
            $banners.css({'background-image':'url("' + bgImage + '")','height':'250px','background-size':'contain',});
        } else {
            bgImage = $currentItem.data("bg-desktop");
            $banners.css({'background-image':'url("' + bgImage + '")','height':'620px'});
        }

    }

    // Set initial background image
    setParallaxBackground({ item: { index: 0 }, target: $("#movie-banner") });

    // Recheck on window resize
    $(window).resize(function() {
        setParallaxBackground({ item: { index: $("#movie-banner").find('.active').index() }, target: $("#movie-banner") });
    });
});

    $("#starCast").owlCarousel({
     loop:false,
    nav:false,
    dots:false,
    responsive:{
        0:{
            items:3
        },
        600:{
            items:4
        },
        1000:{
            items:8
        }
    }
    });

    $("#categories").owlCarousel({
     loop:false,
    nav:false,
    dots:false,
    margin:10,
    responsive:{
        0:{
            items:3,
            autoplay: true,
    slideTransition: 'linear',
    autoplayTimeout: 3000,
    autoplaySpeed: 3000,
    autoplayHoverPause: true,
        },
        600:{
            items:4
        },
        1000:{
            items:8
            
        }
    }
    });
   
  });



  document.addEventListener("DOMContentLoaded", function() {
    const leftArrowBtn = document.getElementById('leftArrowBtn');
    const rightArrowBtn = document.getElementById('rightArrowBtn');

    // Disable the left arrow button if the user is on the home page
    if (window.location.pathname === '/') {
        leftArrowBtn.disabled = true;
    }

    leftArrowBtn.addEventListener('click', function() {
        window.history.back();
    });

    rightArrowBtn.addEventListener('click', function() {
        window.history.forward();
    });


   
});
   



document.addEventListener("DOMContentLoaded", function() {
    const watchNowButton = document.querySelector('.watch-now');
    const videoPlayer = document.getElementById('player');
    let timeout;

    watchNowButton.addEventListener('click', function() {
        // Display the video player
        videoPlayer.style.display = 'block';
        document.querySelector('.banner-holder').style.display = "none";

        // Play the video
        videoPlayer.play();

        videoPlayer.addEventListener('pause', function() {
            watchNowButton.innerHTML = '<i class="icon-play"></i>Continue Watching';
            clearTimeout(timeout); // Clear the previous timeout
            timeout = setTimeout(() => {
                document.querySelector('.banner-holder').style.display = "block";
                videoPlayer.style.display = 'none';
            }, 10000);
        });

        videoPlayer.addEventListener('play', function() {
            clearTimeout(timeout); // Clear the timeout if the video is played again
            document.querySelector('.banner-holder').style.display = "none";
        });
    });

    // window.addEventListener('load', function() {
    //     videoPlayer.load();
    //   });

    
});



document.addEventListener("DOMContentLoaded", () => {
    const player = new Plyr('#player',{
        defaultQuality: '360',
    });
    const storedTime = localStorage.getItem('videoPlaybackTime');
    if (storedTime) {
        player.currentTime = parseFloat(storedTime);
    }

    // Listen for timeupdate event to track video progress
    player.on('timeupdate', () => {
        // Store current playback time in local storage
        localStorage.setItem('videoPlaybackTime', player.currentTime);
        const watchNowButton = document.querySelector('.watch-now');
        watchNowButton.innerHTML = '<i class="icon-play"></i>Continue Watching';
    });

    
  });



  $(document).ready(function(){
  
    // Toggle sidebar open/close
    $(".sidebar-open").click(function(){
        $(".sidebar-container").css("left", "0");
    });
    
    $(".close-sidebar").click(function(){
        $(".sidebar-container").css("left", "-400px");
    });
    // $(document).on('click touchstart', function(event) {
    //     if (!$(event.target).closest('.sidebar-container').length && !$(event.target).hasClass('sidebar-open')) {
    //         $(".sidebar-container").css("left", "-250px");
    //     }
    // });
    
  })

  $(document).ready(function(){
    let timeout;

    $('#slidebarlinks').hover(
        function() {
            // Mouse enter: Add the class
            $('#sidebar-sec').addClass('section-hover-sidebar');

            // Clear any previous timeout to prevent premature removal
            if (timeout) {
                clearTimeout(timeout);
            }
        }, 
        function() {
            // Mouse leave: Set a timeout to remove the class after 2 seconds
            timeout = setTimeout(function() {
                $('#sidebar-sec').removeClass('section-hover-sidebar');
            }, 500); // 1000 milliseconds = 2 seconds
        }
    );
});

  document.addEventListener("DOMContentLoaded", function() {
    const radioButtons = document.querySelectorAll('input[name="category"]');
    const videos = document.querySelectorAll('.movie-card');

    radioButtons.forEach(function(radioButton) {
        radioButton.addEventListener('change', function() {
            const selectedCategoryId = this.value;

            videos.forEach(function(video) {
                const categoryId = video.dataset.categoryId.split(','); // Split categoryId into an array

                if (categoryId.includes(selectedCategoryId) || selectedCategoryId === 'All') {
                    video.style.display = 'block';
                    video.style.transition = '0.50s ease';
                } else {
                    video.style.display = 'none';
                    video.style.transition = '0.50s ease';
                }
            });
        });
    });
});




$(document).ready(function() {
    $('#searchInput').keyup(function() {
        var keyword = $(this).val();
        if (keyword.trim() != '') {
            $('#searchResults').show();
            // Make an Ajax request to your Laravel route to fetch search results
            $.ajax({
                url: '/search', // Replace with your Laravel route for search
                method: 'GET',
                data: { keyword: keyword },
                success: function(response) {
                    // Update the UI with search results
                    displayResults(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
        else{
            $('#searchResults').hide();
        }
    });

    function displayResults(results) {
    var html = '';
 
    results.forEach(function(result) {
      
        html += '<div class="search-result">';
        html += '<a href="' + getURL(result) + '">';
        html += '<div class="row">';
        // html += '<div class="col-3">';
        // html += '<img src="https://alphastudioz.in/admin_panel/public/images/video/'+ result.thumbnail + '" height="100px" width="auto">';
        // html += '</div>';
        html += '<div class="col-9">';
        html += '<h3>' + result.name + '</h3>'; 
        html += '<p>' + result.description + '</p>'; 
        html += '</div>';
        html += '</div>';
        html += '</a>';
        html += '</div>';
    });
    if(results.length === 0) {
        html = '<p>No Such Result found</p>';
    }
    $('#searchResults').html(html);
    }

    function getURL(result) {
    
        console.log('Generating URL for result:', result);
        
        var id = result.id; 
        var type = result.type_id; 
        var url = '';
        switch(type) {
            case 6:
                url = '/view/details/' + id;
                break;
            case 8:
                url = '/view/details/' + id;
                break;
            case 5:
                url = '/view/details/' + id;
                break;
            
            default:
                
                break;
        }
        return url;

        
    }
    
$(document).ready(function(){
    // Click event handler for package radio buttons
    $(".packageData").click(function(){
        var value = $(this).val();
        $("#packageId").val(value);
        var price = $("#amountprice").val();
        $("#amount").val(price);
    });
    // Trigger click event on the checked radio button
    $(".packageData:checked").trigger('click');
});
});

// document.addEventListener("DOMContentLoaded", function() {
//     const form = document.querySelector("#subscribe-form");
//     form.addEventListener("submit", function(event) {
//         event.preventDefault(); 
//         fetch(form.action, {
//             method: form.method,
//             body: new FormData(form)
//         })
//         .then(response => {
//             if (response.ok) {
//                 $('#subscribe-success-modal').modal('show');
//         form.reset();
//         } else {
//           console.error('Form submission failed:', response.statusText);
//         }
//       })
//       .catch(error => {
//         console.error('Error submitting form:', error);
//       });
//     });
//   });

  document.addEventListener('DOMContentLoaded', function () {
    const movieCards = document.querySelectorAll('.movies-card-holder .movie-card');
    movieCards.forEach((card, index) => {
        if ($(window).width() < 540) {
            
        } else {
            if (index % 6 === 0) { // First card in each row
                card.addEventListener('mouseenter', () => {
                    card.style.left = '60px';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.left = '0';
                });
            } else if (index % 6 === 5) { // Sixth card in each row
                card.addEventListener('mouseenter', () => {
                    card.style.left = '-74px';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.left = '0';
                });
            }
        }
       
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const movieCards = document.querySelectorAll('#action .movie-card');
    movieCards.forEach((card, index) => {
        if ($(window).width() < 540) {
            
        } else {
            if (index % 6 === 0) { // First card in each row
                card.addEventListener('mouseenter', () => {
                    card.style.left = '80px';
                }); 
                card.addEventListener('mouseleave', () => {
                    card.style.left = '0';
                });
            } else if (index % 6 === 5) { // Sixth card in each row
                card.addEventListener('mouseenter', () => {
                    card.style.left = '-74px';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.left = '0';
                });
            }
        }
       
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const movieCards = document.querySelectorAll('#romance .movie-card');
    movieCards.forEach((card, index) => {
        if ($(window).width() < 540) {
            
        } else {
            if (index % 6 === 0) { // First card in each row
                card.addEventListener('mouseenter', () => {
                    card.style.left = '80px';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.left = '0';
                });
            } else if (index % 6 === 5) { // Sixth card in each row
                card.addEventListener('mouseenter', () => {
                    card.style.left = '-74px';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.left = '0';
                });
            }
        }
      
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const movieCards = document.querySelectorAll('#Thrillers .movie-card');
    movieCards.forEach((card, index) => {

        if ($(window).width() < 540) {
            
        } else {
            if (index % 6 === 0) { // First card in each row
                card.addEventListener('mouseenter', () => {
                    card.style.left = '80px';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.left = '0';
                });
            } else if (index % 6 === 5) { // Sixth card in each row
                card.addEventListener('mouseenter', () => {
                    card.style.left = '-74px';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.left = '0';
                });
            }
        }
      
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const movieCards = document.querySelectorAll('#watchlistsec .movie-card');
    movieCards.forEach((card, index) => {

        if ($(window).width() < 540) {
            
        } else {
            if (index % 6 === 0) { // First card in each row
                card.addEventListener('mouseenter', () => {
                    card.style.left = '80px';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.left = '0';
                });
            } else if (index % 6 === 5) { // Sixth card in each row
                card.addEventListener('mouseenter', () => {
                    card.style.left = '-74px';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.left = '0';
                });
            }
        }
      
    });
});