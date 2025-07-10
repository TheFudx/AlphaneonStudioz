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
        card.addEventListener("mouseenter", function () {
            timeout = setTimeout(() => {
                let thumbnail = this.querySelector(".thumbnail");
                let videoHolder = this.querySelector(".video-holder");
                if (thumbnail && videoHolder) {
                    thumbnail.style.opacity = 0;
                    videoHolder.play();
                }
            }, 3000);
        });
        card.addEventListener("mouseleave", function () {
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
// ------------------------------------------------------------------------------------------
$(document).on('click',"#navbar-toggler",function (e) {
    e.preventDefault();
    $('#collapsibleNavId').toggle();
    if ($('#collapsibleNavId').is(':visible')) {
        $(".navbar").css('background',
            'linear-gradient(to top, #000000 70%, rgba(255, 0, 0, 0))');
    } else {
        $(".navbar").css('background', ''); // remove the background
    }
});

// Function to hide all custom dropdowns
function hideAllCustomDropdowns() {
    $('.custom-dropdown-menu').hide();

}

// 1. Handle clicks on dropdown triggers to toggle visibility
$(document).on('click','.nav-item.dropdown > .nav-link',function (event) {
    event.preventDefault(); // Prevent default link behavior
    event
        .stopPropagation(); // Stop propagation to prevent document click from closing immediately

    const clickedDropdownMenu = $(this).siblings('.custom-dropdown-menu');

    // Hide all other dropdowns before showing the clicked one
    $('.custom-dropdown-menu').not(clickedDropdownMenu).hide();

    // Toggle the clicked dropdown
    clickedDropdownMenu.toggle();
});


// 2. Handle close button clicks within dropdowns
$(document).on('click', '.close-dropdown, .profile-close-dropdown, .notification-close-dropdown',function (
    event) {
    event
        .stopPropagation(); // Prevent click from bubbling up and re-opening/closing other dropdowns
    $(this).closest('.custom-dropdown-menu').hide();
});


// 3. Close dropdown when clicking anywhere outside a custom-dropdown-menu or its trigger
// $(document).on('click', function (event) {
//     // Check if the click occurred outside any custom dropdown menu
//     // and also outside any of the dropdown trigger links/buttons
//     if (!$(event.target).closest('.custom-dropdown-menu').length &&
//         !$(event.target).closest('.nav-item.dropdown > .nav-link').length) {
//         const dropdown = document.querySelector('#dropdownId');
//         const svg = dropdown.querySelector('svg');
//         const box3 = svg.querySelector('#box3');
//         const box4 = svg.querySelector('#box4');
//         svg.style.transform = 'rotate(0deg)';
//         box3.style.fill = 'white'; // Reset to original or use original color
//         box4.style.fill = 'white';
//         hideAllCustomDropdowns();
//     }
// });
// --- Existing Owl Carousel Initialization ---
$(".owl-carousel .owl-nav").removeClass('disabled');
$(".owl-carousel").each(function () {
    var carousel = $(this);
    if (carousel.data("skip-carousel") === true) {
        return true;
    }
    if (!carousel.hasClass("owl-loaded")) {
        carousel.owlCarousel({
            dots: false,
            nav: false,
            loop: false,
            autoplay: false,
            margin: 10,
            autoplayTimeout: 10000,
            autoplayHoverPause: true,
            responsive: {
                0: {
                    items: 2,
                    stagePadding: 0,
                    margin: 10
                },
                320: {
                    items: 2,
                    stagePadding: 0,
                    margin: 10
                },
                425: {
                    items: 2
                },
                576: {
                    items: 2
                },
                768: {
                    items: 2
                },
                992: {
                    items: 5
                },
                1200: {
                    items: 5
                },
                1700: {
                    items: 7
                },
                1800: {
                    items: 7
                },
                2732: {
                    items: 12
                }
            },
            onInitialized: addActiveClasses,
            onTranslated: addActiveClasses,
            onResized: addActiveClasses,
            onRefreshed: addActiveClasses,
            onDragged: addActiveClasses
        });
    }
});

function addActiveClasses(event) {
    var carousel = $(event.target);
    carousel.find(".owl-item").removeClass(
        "first-active last-active");

    var activeItems = carousel.find(
        ".owl-item.active");
    if (activeItems.length > 0) {
        if (activeItems.length === 1) {
            activeItems.first().addClass("first-active");
        } else {
            var firstActive = null;
            activeItems.each(function () {
                var item = $(this);
                var itemLeft = item.position().left;
                var carouselWidth = carousel.find('.owl-stage-outer')
                    .width();
                if (itemLeft >= -5 && itemLeft < carouselWidth) {
                    firstActive = item;
                    return false;
                }
            });
            if (firstActive) {
                firstActive.addClass("first-active");
            } else {
                activeItems.first().addClass("first-active");
            }
            activeItems.last().addClass("last-active");
        }
    }
}
// ------------------------------------------------------------------------------------------
$(document).ready(function () {

    //  "#latestReleaseSlider",
    //     "#podcastslider",
    //     "#upcomings",
    //     "#trailers",
    //     "#shortfilmslider",
    //     "#webseriesslider",
    //     "#musicsslider",
    //     "#movieslider",
    //     "#moviesliderhome"
    // $("#shortfilmslider").hover(function () {
    //     $(this).find(".owl-nav.disabled").css("display", "block");
    // });
    // $("#podcastslider").hover(function () {
    //     $(this).find(".owl-nav.disabled").css("display", "block");
    // });


    let otheritemsToShow = 6;
    if (window.innerWidth >= 3000) {
        otheritemsToShow = 12;
    } else if (window.innerWidth >= 1800) {
        otheritemsToShow = 9;
    }
    const commonSettings = {
        dots: false,
        nav: true,
        navText: ["<div class='nav-btn prev-slide'></div>", "<div class='nav-btn next-slide'></div>"],
        loop: false,
        items: otheritemsToShow,
        autoplay: false,
        autoplayTimeout: 10000,
        autoplayHoverPause: true,
        responsiveClass: true,
    };

    const standardResponsive = {
        // 0: { items: 1 },
        // 576: { items: 2 },
        // 768: { items: 2 },
        // 992: { items: 3 },
        // 1200: { items: 4 }

        // 0:  { items: 2, stagePadding: 0, margin: 10 },
        // 320: {items: 1, stagePadding: 0, margin: 10 },
        // 425: {items: 2, stagePadding: 0, margin: 10},
        // 576: { items: 2 , stagePadding: 0, margin: 10},
        // 768: { items: 2 , stagePadding: 0, margin: 10},
        // 992: { items: 5 , stagePadding: 0, margin: 10},
        // 1200: { items: 5 , stagePadding: 0, margin: 10},
        // 1800: { items: 6 , stagePadding: 0, margin: 10},
        // 0: { items: 3, stagePadding: 0, margin: 10 },
        // 600: { items: 4 },
        // 992: { items: 5 },
        // 1200: { items: 5 },
        // 1400: { items: 7 },
        // 1800: { items: 9 },
        // 3000: { items: 12 }

        0: { items: 2, stagePadding: 0, margin: 10 },
        320: { items: 1, stagePadding: 0, margin: 10 },
        425: { items: 1 },
        576: { items: 2 },
        768: { items: 2 },
        992: { items: 5 },
        1200: { items: 5 },
        1700: { items: 7 },
        1800: { items: 7 },
        2732: { items: 12 }
    };

    const threeStepResponsive = {
        0: { items: 1 },
        600: { items: 2 },
        900: { items: 3 },
        1200: { items: 4 },
        1800: { items: 6 },
    };

    const multiSliders = [
        "#latestReleaseSlider",
        "#podcastslider",
        "#upcomings",
        "#trailers",
        "#shortfilmslider",
        "#webseriesslider",
        "#musicsslider",
        "#movieslider",
        "#moviesliderhome",
    ];

    // Apply common owlCarousel setup to all the above sliders
    multiSliders.forEach(selector => {
        $(selector).owlCarousel({
            ...commonSettings,
            responsive: standardResponsive
        });
    });

    // khlup special case with extended responsive config
    let itemsToShow = 6;
    if (window.innerWidth >= 3000) {
        itemsToShow = 12;
    } else if (window.innerWidth >= 1800) {
        itemsToShow = 9;
    }

    $("#khlup").owlCarousel({
        ...commonSettings,
        margin: 5,
        items: itemsToShow,
        responsive: {
            0: { items: 3, stagePadding: 0, margin: 10 },
            600: { items: 4 },
            992: { items: 5 },
            1200: { items: 6 },
            1400: { items: 7 },
            1800: { items: 9 },
            3000: { items: 12 }
        }
    });

    // Banner Sync (new-banner & sync2)
    var sync1 = $("#new-banner");
    var sync2 = $("#sync2");
    var slidesPerPage = 4;

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
                '<svg>...left arrow svg...</svg>',
                '<svg>...right arrow svg...</svg>'
            ]
        }).on('changed.owl.carousel', syncPosition2);

    function syncPosition(el) {
        var count = el.item.count - 1;
        var current = Math.round(el.item.index - el.item.count / 2 - 0.5);
        if (current < 0) current = count;
        if (current > count) current = 0;
        sync2.find(".owl-item").removeClass("current").eq(current).addClass("current");
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

    function applyAnimation(event) {
        $(".owl-item").find(".animate__animated").removeClass("animate__fadeInUp");
        $(".owl-item").eq(event.item.index).find(".animate__animated").addClass("animate__fadeInUp");
    }

    // Categories Carousel
    $("#categories").owlCarousel({
        loop: false,
        nav: false,
        dots: false,
        margin: 5,
        responsive: {
            0: {
                items: 3,
                autoplay: true,
                slideTransition: 'linear',
                autoplayTimeout: 3000,
                autoplaySpeed: 3000,
                autoplayHoverPause: true
            },
            600: {
                items: 4
            },
            992: {
                items: 8
            },
            1200: {
                items: 12
            },
            1400: {
                items: 14
            }
        }
    });

    //star cast

    $('#starCastCarousel').owlCarousel({
        loop: false,
        margin: 5,
        nav: false,
        dots: false,
        responsive: {
            0: {
                items: 3,
                autoplay: true,
                slideTransition: 'linear',
                autoplayTimeout: 3000,
                autoplaySpeed: 3000,
                autoplayHoverPause: true
            },
            576: {
                items: 3
            },
            768: {
                items: 4
            },
            992: {
                items: 7
            },
            1200: {
                items: 7
            }
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const watchNowButton = document.querySelector('.watch-now');
    const videoPlayer = document.getElementById('player');
    let timeout;
    if (watchNowButton) {
        watchNowButton.addEventListener('click', function () {
            // Display the video player
            videoPlayer.style.display = 'block';
            document.querySelector('.banner-holder').style.display = "none";
            // Play the video
            videoPlayer.play();
            videoPlayer.addEventListener('pause', function () {
                watchNowButton.innerHTML = '<i class="icon-play"></i>Continue Watching';
                clearTimeout(timeout); // Clear the previous timeout
                timeout = setTimeout(() => {
                    document.querySelector('.banner-holder').style.display = "block";
                    videoPlayer.style.display = 'none';
                }, 10000);
            });
            videoPlayer.addEventListener('play', function () {
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
    const player = new Plyr('#player', {
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
$(document).ready(function () {
    // Toggle sidebar open/close
    $(".sidebar-open").click(function () {
        $(".sidebar-container").css("left", "0");
    });
    $(".close-sidebar").click(function () {
        $(".sidebar-container").css("left", "-400px");
    });
    // $(document).on('click touchstart', function(event) {
    //     if (!$(event.target).closest('.sidebar-container').length && !$(event.target).hasClass('sidebar-open')) {
    //         $(".sidebar-container").css("left", "-250px");
    //     }
    // });
})
$(document).ready(function () {
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
        function () {
            // Add the class to expand sidebar
            $('#sidebar-sec').addClass('section-hover-sidebar');
            // Clear previous timeout to prevent premature removal
            if (timeout) {
                clearTimeout(timeout);
            }
        },
        function () {
            // Start a timeout to remove the class if the mouse exits beyond 150px
            timeout = setTimeout(function () {
                // Check if the mouse is still within the first 150px of the sidebar
                $(document).on('mousemove', function (event) {
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
        function () {
            // Clear the timeout when hovering within the sidebar
            if (timeout) {
                clearTimeout(timeout);
            }
            // Stop tracking the mouse as long as it's inside the sidebar
            $(document).off('mousemove');
        },
        function () {
            // Set the timeout to remove the class after a delay, but only if the mouse is outside the first 150px
            timeout = setTimeout(function () {
                $(document).on('mousemove', function (event) {
                    if (!isMouseWithinFirst150px(event)) {
                        $('#sidebar-sec').removeClass('section-hover-sidebar');
                        $(document).off('mousemove'); // Stop tracking once the class is removed
                    }
                });
            }, 500); // Adjust the delay if necessary
        }
    );
});


$(document).ready(function () {
    $('#searchInput').keyup(function () {
        var keyword = $(this).val();
        if (keyword.trim() != '') {
            $('#searchResults').show();
            // Make an Ajax request to your Laravel route to fetch search results
            $.ajax({
                url: '/search', // Replace with your Laravel route for search
                method: 'GET',
                data: { keyword: keyword },
                success: function (response) {
                    // Update the UI with search results
                    displayResults(response);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
        else {
            $('#searchResults').hide();
        }
    });
    function displayResults(results) {
        var html = '';
        results.forEach(function (result) {
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
        if (results.length === 0) {
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
        var id = '{{App\Helpers\VideoHelper::encryptID(' + result.id + ')}}';
        var type = result.type_id;
        var url = '';
        switch (type) {
            case 6:
                url = '/view/details/' + id + '/watch=true';
                break;
            case 10:
                url = '/webseries/episode/view/' + id + '/watch=true';
                break;
            case 8:
                url = '/view/details/' + id + '/watch=true';
                break;
            case 5:
                url = '/view/details/' + id + '/watch=true';
                break;
            case 11:
                url = '/view/details/' + id + '/watch=true';
                break;
            default:
                break;
        }
        return url;
    }
    $(document).ready(function () {
        // Click event handler for package radio buttons
        $(".packageData").click(function () {
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
if (closeIcons) {
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
    else {
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
    videoPlayer.addEventListener('timeupdate', function () {
        const timeRemaining = videoPlayer.duration - videoPlayer.currentTime;
        if (timeRemaining <= 80) {
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
        else {
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
document.addEventListener('DOMContentLoaded', function () {
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
    else {
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
document.addEventListener('DOMContentLoaded', function () {
    const descriptionDiv = document.getElementById('description');
    const toggleButton = document.getElementById('toggleDescription');
    if (toggleButton) {
        toggleButton.addEventListener('click', function () {
            const isExpanded = descriptionDiv.classList.toggle('expanded');
            toggleButton.textContent = isExpanded ? 'Show Less' : 'Show More';
        });
    }
});
document.addEventListener('DOMContentLoaded', function () {
    const loader = document.getElementById('page-loader');
    loader.style.display = 'flex';
});



//DRM Management
document.addEventListener('contextmenu', event => event.preventDefault());

document.addEventListener('keydown', function (e) {
    // Prevent PrintScreen
    if (e.key === 'PrintScreen') {
        navigator.clipboard.writeText('');
        alert('Screenshot is disabled!');
    }

    // Prevent Ctrl+P (print)
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        alert('Printing is disabled!');
    }
});
document.addEventListener("contextmenu", e => e.preventDefault());
document.addEventListener("keydown", e => {
    if (e.key === "F12" || (e.ctrlKey && e.shiftKey && e.key === "I")) {
        e.preventDefault();
    }
});

document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
        console.warn('User switched tabs or minimized, potentially for screen recording.');
    }
});


// Hover on image
document.addEventListener('DOMContentLoaded', function () {
    // Select all image wrappers (which contain the <a> and <img> tags)
    const imageWrappers = document.querySelectorAll('#section-home-newdes .image-wrapper');

    imageWrappers.forEach(imageWrapper => {
        const alphaCard = imageWrapper.closest('.alpha-card'); // Get the parent .alpha-card
        // Find the .alpha-card-large within the same .alpha-card
        const alphaCardLarge = alphaCard ? alphaCard.querySelector('.alpha-card-large') : null;
        const thumbnailImage = imageWrapper.querySelector('img'); // The actual image tag

        if (alphaCardLarge && thumbnailImage) {
            let hoverTimeout; // To manage the delay for hiding

            // When mouse enters the thumbnail image
            thumbnailImage.addEventListener('mouseenter', function () {
                clearTimeout(hoverTimeout); // Clear any pending hide action
                alphaCardLarge.classList.add('is-hovered');
                alphaCard.classList.add('has-hovered-large'); // Add a class to the parent for additional styling if needed (e.g., hiding skeleton)

                // Play video if available
                const video = alphaCardLarge.querySelector('.video-holder');
                if (video && video.paused) {
                    video.play().catch(e => console.error("Video play failed:", e)); // Add error handling for play()
                }
            });

            // When mouse leaves the thumbnail image
            thumbnailImage.addEventListener('mouseleave', function () {
                // Start a timeout to hide the card. This helps if the mouse briefly leaves the image
                // but immediately enters the alphaCardLarge itself.
                hoverTimeout = setTimeout(() => {
                    alphaCardLarge.classList.remove('is-hovered');
                    alphaCard.classList.remove('has-hovered-large');

                    // Pause and reset video
                    const video = alphaCardLarge.querySelector('.video-holder');
                    if (video) {
                        video.pause();
                        video.currentTime = 0; // Reset video to start
                    }
                }, 50); // Small delay to prevent immediate hide when moving to the large card
            });

            // Crucially, when mouse enters the large card itself, keep it visible
            alphaCardLarge.addEventListener('mouseenter', function () {
                clearTimeout(hoverTimeout); // Prevent the card from hiding if cursor moves onto it
                alphaCardLarge.classList.add('is-hovered');
                alphaCard.classList.add('has-hovered-large');

                // Play video if available
                const video = alphaCardLarge.querySelector('.video-holder');
                if (video && video.paused) {
                    video.play().catch(e => console.error("Video play failed:", e));
                }
            });

            // When mouse leaves the large card, hide it
            alphaCardLarge.addEventListener('mouseleave', function () {
                hoverTimeout = setTimeout(() => {
                    alphaCardLarge.classList.remove('is-hovered');
                    alphaCard.classList.remove('has-hovered-large');

                    // Pause and reset video
                    const video = alphaCardLarge.querySelector('.video-holder');
                    if (video) {
                        video.pause();
                        video.currentTime = 0;
                    }
                }, 50); // Small delay for smoother transition
            });
        }
    });
});