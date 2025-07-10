window.addEventListener('load', function () {
    const loader = document.getElementById('page-loader');
    if (loader) {
        loader.style.display = 'none';
    }
    const allSkeleton = document.querySelectorAll('.skeleton'); // Ensure correct selector
    allSkeleton.forEach(item => {
        item.classList.remove('skeleton');
        
    }); 
    document.querySelectorAll('.movie-card').forEach(card => {
        let timeout;

        card.addEventListener("mouseenter", function(){
            timeout = setTimeout(() => {
                let thumbnail = this.querySelector(".thumbnail");
                let videoHolder = this.querySelector(".video-holder");
                
                if (thumbnail && videoHolder) {
                    thumbnail.style.opacity = 0;
                    videoHolder.play();
                }
            }, 3000);
        });

        card.addEventListener("mouseleave", function(){
            clearTimeout(timeout); // Clear the timeout to prevent delayed execution

            let thumbnail = this.querySelector(".thumbnail");
            let videoHolder = this.querySelector(".video-holder");

            if (thumbnail && videoHolder) {
                thumbnail.style.opacity = 1;
                videoHolder.pause();
                videoHolder.currentTime = 0;
            }
        });
    });

});


$(document).ready(function() {
 

$(document).ready(function() {


   
    $("#latestReleaseSlider").owlCarousel({
        items: 4,
        dots: false,
        nav: false,
        loop: false,
        autoplay: false,
        margin: 10,
        autoplayTimeout: 10000,
        autoplayHoverPause: true,
       
    });
    $("#podcastslider").owlCarousel({
        items: 4,
        dots: false,
        nav: false,
        loop: false,
        autoplay: false,
        margin: 10,
        autoplayTimeout: 10000,
        autoplayHoverPause: true,
       
    });
    $("#trailersupcoming").owlCarousel({
        items: 4,
        dots: false,
        nav: false,
        loop: false,
        autoplay: false,
        margin: 10,
        autoplayTimeout: 10000,
        autoplayHoverPause: true,
       
    });
    $("#shortfilmslider").owlCarousel({
        items: 4,
        dots: false,
        nav: false,
        loop: false,
        autoplay: false,
        margin: 10,
        autoplayTimeout: 10000,
        autoplayHoverPause: true,
       
    });

    $("#webseriesslider").owlCarousel({
        items: 4,
        dots: false,
        nav: false,
        loop: false,
        autoplay: false,
        margin: 10,
        autoplayTimeout: 10000,
        autoplayHoverPause: true,
       
    });
    $("#musicsslider").owlCarousel({
        items: 4,
        dots: false,
        nav: false,
        loop: false,
        autoplay: false,
        margin: 10,
        autoplayTimeout: 10000,
        autoplayHoverPause: true,
       
    });
    $("#movieslider").owlCarousel({
        items: 4,
        dots: false,
        nav: false,
        loop: false,
        autoplay: false,
        margin: 10,
        autoplayTimeout: 10000,
        autoplayHoverPause: true,
       
    });
    $("#moviesliderhome").owlCarousel({
        items: 4,
        dots: false,
        nav: false,
        loop: false,
        autoplay: false,
        margin: 10,
        autoplayTimeout: 10000,
        autoplayHoverPause: true,
    });
});

$(document).ready(function(){
    var itemsToShow = 6;
    if(window.innerWidth >= 1800) {  // Adjusted threshold
        itemsToShow = 9;
    } else if(window.innerWidth >= 3000) {
        itemsToShow = 12;
    }
    $("#khlup").owlCarousel({
        loop: false,
        margin: 5,
        nav: false,
        dots: false,
        items: itemsToShow,
        // autoplay: true,
        // slideTransition: 'linear',
        // autoplayTimeout: 8000,
        // autoplaySpeed: 8000,
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
                items: 4
            },
            1400: {
                items: 5
            },
            1600: {
                items: 5
            },
            1800: {  // Change from 1920 to 1800
                items: 7
            },
            3000: { 
                items: 12
            }
        }
        
        
    });

    

});


$(document).ready(function () {
    var sync1 = $("#new-banner");
    var sync2 = $("#sync2");
    var slidesPerPage = 4; // Number of elements per page
    var syncedSecondary = true;

    sync1.owlCarousel({
        items: 1,
        slideSpeed: 4000,
        nav: false,
        autoplay: true,
        dots: false,
        loop: true,
        animateOut: 'fadeOut',
        responsiveRefreshRate: 200,
    }).on('changed.owl.carousel', function (event) {
        syncPosition(event);
        applyAnimation(event);
    });

    sync2
        .on('initialized.owl.carousel', function () {
            sync2.find(".owl-item").eq(0).addClass("current");
        })
        .owlCarousel({
            items: slidesPerPage,
            dots: false,
            nav: true,
            smartSpeed: 200,
            slideSpeed: 500,
            slideBy: slidesPerPage,
            stagePadding: 20,
            responsiveRefreshRate: 100,
            navText: [
                '<svg width="15" height="26" viewBox="0 0 15 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.4264 0.5723C14.7937 0.938854 15 1.43594 15 1.95425C15 2.47256 14.7937 2.96964 14.4264 3.3362L4.72885 13.0118L14.4264 22.6874C14.7833 23.0561 14.9807 23.5498 14.9763 24.0623C14.9718 24.5748 14.7658 25.0651 14.4025 25.4275C14.0393 25.7899 13.5479 25.9955 13.0343 25.9999C12.5206 26.0044 12.0257 25.8074 11.6562 25.4513L0.573598 14.3937C0.206323 14.0272 0 13.5301 0 13.0118C0 12.4935 0.206323 11.9964 0.573598 11.6299L11.6562 0.5723C12.0236 0.205857 12.5218 0 13.0413 0C13.5608 0 14.059 0.205857 14.4264 0.5723Z" fill="white"/></svg>',
                '<svg width="15" height="26" viewBox="0 0 15 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.5736 0.5723C0.206326 0.938854 0 1.43594 0 1.95425C0 2.47256 0.206326 2.96964 0.5736 3.3362L10.2711 13.0118L0.5736 22.6874C0.216734 23.0561 0.0192661 23.5498 0.0237303 24.0623C0.0281935 24.5748 0.234231 25.0651 0.597464 25.4275C0.960697 25.7899 1.45206 25.9955 1.96573 25.9999C2.4794 26.0044 2.97428 25.8074 3.34377 25.4513L14.4264 14.3937C14.7937 14.0272 15 13.5301 15 13.0118C15 12.4935 14.7937 11.9964 14.4264 11.6299L3.34377 0.5723C2.97638 0.205857 2.47817 0 1.95868 0C1.4392 0 0.940986 0.205857 0.5736 0.5723Z" fill="white"/></svg>',
            ],
        }).on('changed.owl.carousel', syncPosition2);

    function syncPosition(el) {
        var count = el.item.count - 1;
        var current = Math.round(el.item.index - el.item.count / 2 - 0.5);

        if (current < 0) {
            current = count;
        }
        if (current > count) {
            current = 0;
        }

        sync2
            .find(".owl-item")
            .removeClass("current")
            .eq(current)
            .addClass("current");

        var onscreen = sync2.find('.owl-item.active').length - 1;
        var start = sync2.find('.owl-item.active').first().index();
        var end = sync2.find('.owl-item.active').last().index();

        if (current > end) {
            sync2.data('owl.carousel').to(current, 100, true);
        }
        if (current < start) {
            sync2.data('owl.carousel').to(current - onscreen, 100, true);
        }
    }

    function syncPosition2(el) {
        if (syncedSecondary) {
            var number = el.item.index;
            sync1.data('owl.carousel').to(number, 100, true);
        }
    }

    sync2.on("click", ".owl-item", function (e) {
        e.preventDefault();
        var number = $(this).index();
        sync1.data('owl.carousel').to(number, 300, true);
    });

    // Function to apply animation
    function applyAnimation(event) {
        var items = $(".owl-item");
        items.find(".animate__animated").removeClass("animate__fadeInUp"); // Remove animation class
        var activeItem = items.eq(event.item.index).find(".animate__animated");
        activeItem.addClass("animate__fadeInUp"); // Add animation class to active item
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
            items:14,
            
        }
    }
    });
   
  });






 
   



document.addEventListener("DOMContentLoaded", function() {
    const watchNowButton = document.querySelector('.watch-now');
    const videoPlayer = document.getElementById('player');
    let timeout;

    if(watchNowButton){

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
    }

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

    // Function to check if the mouse is within the first 150px of the sidebar
    function isMouseWithinFirst150px(event) {
        let sidebarOffset = $('#sidebar-sec').offset();
        let mouseX = event.pageX;

        // Check if the mouse is still within 150px from the left of the sidebar
        return mouseX <= (sidebarOffset.left + 150);
    }

    // When the mouse enters the SVG links
    $('#slidebarlinks li a svg').hover(
        function() {
            // Add the class to expand sidebar
            $('#sidebar-sec').addClass('section-hover-sidebar');

            // Clear previous timeout to prevent premature removal
            if (timeout) {
                clearTimeout(timeout);
            }
        },
        function() {
            // Start a timeout to remove the class if the mouse exits beyond 150px
            timeout = setTimeout(function() {
                // Check if the mouse is still within the first 150px of the sidebar
                $(document).on('mousemove', function(event) {
                    if (!isMouseWithinFirst150px(event)) {
                        $('#sidebar-sec').removeClass('section-hover-sidebar');
                        $(document).off('mousemove'); // Stop tracking mouse once class is removed
                    }
                });
            }, 500); // Adjust the delay if necessary
        }
    );

    // Also track hover over the sidebar itself
    $('#sidebar-sec').hover(
        function() {
            // Clear the timeout when hovering within the sidebar
            if (timeout) {
                clearTimeout(timeout);
            }

            // Stop tracking the mouse as long as it's inside the sidebar
            $(document).off('mousemove');
        },
        function() {
            // Set the timeout to remove the class after a delay, but only if the mouse is outside the first 150px
            timeout = setTimeout(function() {
                $(document).on('mousemove', function(event) {
                    if (!isMouseWithinFirst150px(event)) {
                        $('#sidebar-sec').removeClass('section-hover-sidebar');
                        $(document).off('mousemove'); // Stop tracking once the class is removed
                    }
                });
            }, 500); // Adjust the delay if necessary
        }
    );
});


  document.addEventListener("DOMContentLoaded", function() {
    const radioButtons = document.querySelectorAll('input[name="category"]');
    const videos = document.querySelectorAll('.movie-new-card');

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
        html += '<p>' + truncateWords(result.description, 20) + '</p>'; 
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

function truncateWords(text, wordLimit) {
    const words = text.split(' '); // Split the text into an array of words
    if (words.length > wordLimit) {
        return words.slice(0, wordLimit).join(' ') + '...'; // Join the first 100 words and add ellipsis
    }
    return text; // If text has fewer than 100 words, return it as is
}

function getURL(result) {
   
    console.log('Generating URL for result:', result);
    
    var id = result.id; 
    var type = result.type_id; 
    var url = '';
    switch(type) {
        case 6:
            url = '/movie/view/' + id + '/watch=true';
            break;
        case 10:
            url = '/webseries/episode/view/' + 
                encodeURIComponent(result.name) + '/' +
                encodeURIComponent(result.season) + '/' +
                encodeURIComponent(result.name) + '/' +
                id + '/episod/' +
                encodeURIComponent(result.episode) + '/watch=true';
            break;
        case 8:
            url = '/trailer/view/' + id + '/watch=true';
            break;
        case 5:
            url = '/music/view/' + id + '/watch=true';
            break;
        case 11:
            url = '/podcast/view/' + id + '/watch=true';
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


// const button = document.querySelector(".show-notification"),
//       toasts = document.querySelectorAll(".toasts"), // select all .toasts elements
//       closeIcons = document.querySelectorAll(".close"),
//       progressBars = document.querySelectorAll(".progress");

// let timers = []; // to store multiple timers

// if(button){
//     button.addEventListener("click", () => {
//         toasts.forEach((toast, index) => {
//             let progress = progressBars[index]; // get the corresponding progress bar
    
//             // Activate toast and progress bar
//             toast.classList.add("active");
//             progress.classList.add("active");
    
//             // Set timers for each notification
//             timers[index] = {
//                 timer1: setTimeout(() => {
//                     toast.classList.remove("active");
//                 }, 5000), // 95 seconds
    
//                 timer2: setTimeout(() => {
//                     progress.classList.remove("active");
//                 }, 5300) // 95.3 seconds
//             };
//         });
//     });
// }

// Add close functionality for each notification individually
closeIcons = document.querySelectorAll(".close");
if(closeIcons){

    closeIcons.forEach((closeIcon, index) => {
        closeIcon.addEventListener("click", () => {
            let toast = toasts[index];
            let progress = progressBars[index];
    
            // Remove active classes
            toast.classList.remove("active");
            setTimeout(() => {
                progress.classList.remove("active");
            }, 300);
    
            // Clear timers for the specific notification
            clearTimeout(timers[index]?.timer1);
            clearTimeout(timers[index]?.timer2);
        });
    });
}



// Initialize Plyr with the configuration


const player = new Plyr('#webseriesplayer', {
    controls: [
        'play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'captions',
        'settings', 'pip', 'airplay', 'fullscreen'
    ],
    // Other configuration options...
});

// Find the Plyr controls container to inject the button into it
player.on('ready', () => {
    // Create the Skip Intro button
    const skipIntroBtn = document.createElement('button');
    skipIntroBtn.classList.add('plyr__skip-intro-btn'); // Custom class for styling
    skipIntroBtn.setAttribute('type', 'button');
    skipIntroBtn.setAttribute('aria-label', 'Skip Intro');
    skipIntroBtn.innerHTML = 'Skip Intro'; // Button label
    // Initially hidden
    skipIntroBtn.style.display = 'none'; 
    // Add the button into the controls container
    const controlsContainer = player.elements.controls;
    controlsContainer.appendChild(skipIntroBtn);
    
    
    const countdown = document.createElement('p');
    countdown.classList.add('plyr__control', 'plyr__countdown-text'); // Plyr button style + custom class
    countdown.setAttribute('id', 'countdown');
    countdown.innerHTML = 'Next episode will play in'; // Button label
    countdown.style.display = 'none';// Initially hidden
    
   
    
    // Create Previous Episode button if it exists
    if (typeof previousEpisodeUrl !== 'undefined') {
        const prevEpisodeBtn = document.createElement('button');
        prevEpisodeBtn.classList.add('plyr__control', 'plyr__prev-episode-btn');
        prevEpisodeBtn.setAttribute('type', 'button');
        prevEpisodeBtn.setAttribute('id', 'prevEpisodeBtn');
        prevEpisodeBtn.setAttribute('aria-label', 'Previous Episode');
        prevEpisodeBtn.innerHTML = 'Previous Episode';
        prevEpisodeBtn.style.display = 'block';

        // Add event listener for Previous Episode button
        prevEpisodeBtn.addEventListener('click', () => {
            playEpisode(previousEpisodeUrl); // Call the function to play the previous episode
        });
        
        // Append the Previous Episode button to the div block
        controlsContainer.appendChild(prevEpisodeBtn);
    }

    // Create Next Episode button if it exists
    if (typeof nextEpisodeUrl !== 'undefined') {
        const nextEpisodeBtn = document.createElement('button');
        nextEpisodeBtn.classList.add('plyr__control', 'plyr__next-episode-btn');
        nextEpisodeBtn.setAttribute('type', 'button');
        nextEpisodeBtn.setAttribute('id', 'nextEpisodeBtn');
        nextEpisodeBtn.setAttribute('aria-label', 'Next Episode');
        nextEpisodeBtn.innerHTML = 'Next Episode';
        // Add event listener for Next Episode button
        nextEpisodeBtn.addEventListener('click', () => {
            playEpisode(nextEpisodeUrl); // Call the function to play the next episode
        });
        // Append the Next Episode button to the div block
        controlsContainer.appendChild(nextEpisodeBtn);
    }
    else{
        
    }

    // // Logic to show or hide the button based on current video time
    // const skipToTime = 390; // Time to skip to (e.g., 6 minutes 30 seconds)
    // const buttonVisibleAfter = 330; // Show the button after 5 minutes 30 seconds

    // const video = player.media;
    // video.addEventListener('timeupdate', () => {
    //     if (video.currentTime >= buttonVisibleAfter && video.currentTime < skipToTime) {
    //         skipIntroBtn.style.display = 'block'; // Show button
    //     } else {
    //         skipIntroBtn.style.display = 'none'; // Hide button after skipToTime
    //     }
    // });

    // // Event listener for the "Skip Intro" button click
    // skipIntroBtn.addEventListener('click', () => {
    //     video.currentTime = skipToTime; // Skip to the defined time
    //     player.play(); // Continue playing
    //     skipIntroBtn.style.display = 'none'; // Hide the button after skipping
    // });

  
const videoPlayer = player.media
// Automatically navigate to the next episode 10 seconds before the video ends
videoPlayer.addEventListener('timeupdate', function() {
    const timeRemaining = videoPlayer.duration - videoPlayer.currentTime;
    if (timeRemaining <= 80){
        const controlsContent = player.elements.container;
        if (typeof nextEpisodeUrl !== 'undefined') {
            controlsContent.appendChild(nextEpisodeBtn);
            nextEpisodeBtn.classList.add('plyr__next-episode-btn', 'active');
        }

        if (typeof previousEpisodeUrl !== 'undefined') {
            controlsContent.appendChild(prevEpisodeBtn);
            prevEpisodeBtn.classList.add('plyr__prev-episode-btn', 'active');
        }
       
        
    }
   
    if (timeRemaining < 10 && nextEpisodeBtn) {
        const controlsContent = player.elements.container;
        controlsContent.appendChild(countdown);
        countdown.style.display = 'block';
        countdown.innerText = `Next episode will play in ${Math.floor(timeRemaining)} seconds`;
        // After 10 seconds, auto-play the next episode
        if (timeRemaining <= 0) {
            playEpisode(nextEpisodeUrl); 
            videoPlayer.play();

        }
    }
    else{
        countdown.style.display = 'none'; 
    }
});

   
});


function playEpisode(nextUrl) {
    window.location.href = nextUrl;
}


// const player = new Plyr('#webseriesplayer', {
//     controls: [
//         'play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'captions',
//         'settings', 'pip', 'airplay', 'fullscreen'
//     ],
//     // Other configuration options...
// });

// // Wait until the Plyr player is ready
// player.on('ready', () => {
//     // Get video and control container
//     const video = player.media;
//     const controlsContainer = player.elements.container;

//     // Create the Skip Intro button
//     const skipIntroBtn = document.createElement('button');
//     skipIntroBtn.classList.add('plyr__control', 'plyr__skip-intro-btn'); // Plyr button style + custom class
//     skipIntroBtn.setAttribute('type', 'button');
//     skipIntroBtn.setAttribute('aria-label', 'Skip Intro');
//     skipIntroBtn.innerHTML = 'Skip Intro'; // Button label
//     skipIntroBtn.style.display = 'none'; // Initially hidden

//     // Append the Skip Intro button to the Plyr controls
//     controlsContainer.appendChild(skipIntroBtn);

//     // Skip Intro Logic (show button after 5:30 and skip to 6:30)
//     const skipToTime = 390; // Skip to 6:30 (390 seconds)
//     const buttonVisibleAfter = 330; // Show button after 5:30 (330 seconds)

//     video.addEventListener('timeupdate', () => {
//         if (video.currentTime >= buttonVisibleAfter && video.currentTime < skipToTime) {
//             skipIntroBtn.style.display = 'block'; // Show Skip Intro button
//         } else {
//             skipIntroBtn.style.display = 'none'; // Hide button after skip time
//         }
//     });

//     // Event listener for "Skip Intro" button click
//     skipIntroBtn.addEventListener('click', () => {
//         video.currentTime = skipToTime; // Skip to the desired time
//         player.play(); // Continue playing
//         skipIntroBtn.style.display = 'none'; // Hide the button
//     });

//     // Create the Next Episode button
//     const nextEpisodeBtn = document.createElement('button');
//     nextEpisodeBtn.classList.add('plyr__control', 'plyr__next-episode-btn'); // Plyr button style + custom class
//     nextEpisodeBtn.setAttribute('type', 'button');
//     nextEpisodeBtn.setAttribute('aria-label', 'Next Episode');
//     nextEpisodeBtn.innerHTML = 'Next Episode'; // Button label
//     nextEpisodeBtn.style.display = 'none'; // Initially hidden

//     // Append the Next Episode button to the Plyr controls
//     controlsContainer.appendChild(nextEpisodeBtn);

//     // Check if the video is near the end and display Next Episode button
//     video.addEventListener('timeupdate', () => {
//         const timeRemaining = video.duration - video.currentTime;
//         if (timeRemaining <= 60) {
//             nextEpisodeBtn.style.display = 'block'; // Show Next Episode button 10 seconds before end
//         } else {
//             nextEpisodeBtn.style.display = 'none'; // Hide button until 10 seconds before end
//         }
//     });

//     // Event listener for "Next Episode" button click
//     nextEpisodeBtn.addEventListener('click', () => {
//         const nextEpisodeUrl = nextEpisodeBtn.getAttribute('data-next-url');
//         if (nextEpisodeUrl) {
//             window.location.href = nextEpisodeUrl; // Navigate to the next episode
//         }
//     });

//     // Add the next episode URL dynamically (make sure to pass the next episode URL in the blade template)
//     const nextEpisodeUrl = '{{ route("webseries.episodes.view", ["seriesname" => $series->name, "season" => $nextEpisode->season_id, "name" => $nextEpisode->name, "id" => $nextEpisode->id, "episode" => $nextEpisode->episode_no]) }}';
//     nextEpisodeBtn.setAttribute('data-next-url', nextEpisodeUrl);
// });


document.addEventListener('DOMContentLoaded', function() {
    // Check if the subscription status is active and daysLeft is defined
    var subscriptionStatus = '';
    var daysLeft = null;
    if (subscriptionStatus === 'No' || typeof daysLeft !== 'undefined' && daysLeft !== null) {
        let message = '';
         if (daysLeft <= 0) {
            message = "Your subscription has expired. Renew now to continue enjoying the service.";
        }

        if (message) {
            // document.getElementById('notification-message').innerText = message;
            // var myModal = new bootstrap.Modal(document.getElementById('notification-modal'));
            // myModal.show();
        }
    }
    else{
        let message = '';
        if (daysLeft > 0 && daysLeft <= 2) {
            message = "Your subscription will expire in " + daysLeft + " day(s). Renew now to continue enjoying the service.";
        }

        if (message) {
            // document.getElementById('notification-message').innerText = message;
            // var myModal = new bootstrap.Modal(document.getElementById('notification-modal'));
            // myModal.show();
        }
    }
});


    // document.addEventListener('DOMContentLoaded', function () {
    //     const descriptionDiv = document.getElementById('description');
    //     const toggleButton = document.getElementById('toggleDescription');

    //     toggleButton.addEventListener('click', function () {
    //         const isExpanded = descriptionDiv.classList.toggle('expanded');
    //         toggleButton.textContent = isExpanded ? 'Show Less' : 'Show More';
    //     });
    // });


    // document.addEventListener('DOMContentLoaded', function () {
    //     const loader = document.getElementById('page-loader');
    //     loader.style.display = 'flex';
    // });

  
  
    