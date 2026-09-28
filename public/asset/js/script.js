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
$(document).on('click', "#navbar-toggler", function (e) {
    e.preventDefault();
    if (typeof openMobileDrawer === 'function') {
        openMobileDrawer();
    }
});

// Function to hide all custom dropdowns
function hideAllCustomDropdowns() {
    $('.custom-dropdown-menu').hide();

}

// 1. Handle clicks on dropdown triggers to toggle visibility
$(document).on('click', '.nav-item.dropdown > .nav-link', function (event) {
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
$(document).on('click', '.close-dropdown, .profile-close-dropdown, .notification-close-dropdown', function (
    event) {
    event
        .stopPropagation(); // Prevent click from bubbling up and re-opening/closing other dropdowns
    $(this).closest('.custom-dropdown-menu').hide();
});

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
        "#continueWatchingSlider", "#watchLaterSlider", "#top10MoviesSlider", "#top10TrailersSlider", "#top10ShortfilmsSlider", "#top10PodcastsSlider"
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
$(document).ready(function () {
    var sync1 = $("#new-banner");
    var sync2 = $("#sync2");
    var slidesPerPage = 4;
    var isSyncing = false;

    // Main banner
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
        if (isSyncing) return;
        syncPosition(event);
        applyAnimation(event);
    });

    // ✅ nav: false — no internal nav buttons, nothing to block clicks
    sync2.owlCarousel({
        items: slidesPerPage,
        dots: false,
        nav: false,
        smartSpeed: 200,
        slideSpeed: 4000,
        slideBy: 1,
        stagePadding: 0,
        loop: false,
        responsiveRefreshRate: 100,
    }).on('initialized.owl.carousel', function () {
        sync2.find(".owl-item").eq(0).addClass("current");
    });

    // ✅ Click on .thumbnail-holder — nothing can block this
    $(document).on("click", "#sync2 .thumbnail-holder", function (e) {
        var dataIndex = $(this).closest(".item").attr("data-index");

        console.log("Clicked data-index:", dataIndex);

        if (dataIndex === undefined || dataIndex === null) return;

        sync2.find(".owl-item").removeClass("current");
        $(this).closest(".owl-item").addClass("current");

        isSyncing = true;
        sync1.trigger('to.owl.carousel', [parseInt(dataIndex), 300, true]);
        setTimeout(function () { isSyncing = false; }, 500);
    });

    // ✅ Custom external prev/next
    $(document).on("click", ".sync2-custom-prev", function () {
        sync2.trigger('prev.owl.carousel');
    });

    $(document).on("click", ".sync2-custom-next", function () {
        sync2.trigger('next.owl.carousel');
    });

    function syncPosition(el) {
        var count = el.item.count - 1;
        var current = Math.round(el.item.index - el.item.count / 2 - 0.5);
        if (current < 0) current = count;
        if (current > count) current = 0;

        sync2.find(".owl-item").removeClass("current").eq(current).addClass("current");
        sync2.trigger('to.owl.carousel', [current, 100, true]);
    }

    function applyAnimation(event) {
        sync1.find(".owl-item").find(".animate__animated").removeClass("animate__fadeInUp");
        sync1.find(".owl-item").eq(event.item.index).find(".animate__animated").addClass("animate__fadeInUp");
    }
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
});

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
            // html += '<img src="' + window.WEBSITE_URL + 'admin_panel/public/images/video/'+ result.thumbnail + '" height="100px" width="auto">';
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
        var url = '';
        var type = result.type_id;
        var id = result.id;

        switch (type) {
            case 6:
                url = '/view/details/' + id;
                break;
            case 10:
                url = '/webseries/episode/view/' + id;
                break;
            case 8:
                url = '/view/details/' + id;
                break;
            case 5:
                url = '/view/details/' + id;
                break;
            case 11:
                url = '/view/details/' + id;
                break;
            default:
                url = '/view/details/' + id;
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

function playEpisode(nextUrl) {
    window.location.href = nextUrl;
}
document.addEventListener('DOMContentLoaded', function () {
    // Check if subscription data exists from Blade
    var subscriptionStatus = window.subscriptionStatus || '';
    var daysLeft = window.daysLeft || null;

    if (subscriptionStatus === 'No' || (daysLeft !== null && daysLeft !== undefined)) {
        let message = '';
        if (daysLeft <= 0) {
            message = "Your subscription has expired. Renew now to continue enjoying the service.";
        } else if (daysLeft > 0 && daysLeft <= 2) {
            message = "Your subscription will expire in " + daysLeft + " day(s). Renew now to continue enjoying the service.";
        }
        if (message) {
            // Show notification logic here if needed
            console.log('Subscription notification:', message);
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
    if (loader) {
        loader.style.display = 'none';
    }

    const allSkeleton = document.querySelectorAll('.skeleton');
    allSkeleton.forEach(item => {
        item.classList.remove('skeleton');
    });

    // Remove all console.log statements for production
    // The following lines were removed: console.warn, console.error, etc.
});

//DRM Management
// document.addEventListener('contextmenu', event => event.preventDefault());

// document.addEventListener('keydown', function (e) {
//     // Prevent PrintScreen
//     if (e.key === 'PrintScreen') {
//         navigator.clipboard.writeText('');
//         alert('Screenshot is disabled!');
//     }

//     // Prevent Ctrl+P (print)
//     if (e.ctrlKey && e.key === 'p') {
//         e.preventDefault();
//         alert('Printing is disabled!');
//     }
// });

// document.addEventListener("contextmenu", e => e.preventDefault());
// document.addEventListener("keydown", e => {
//     if (e.key === "F12" || (e.ctrlKey && e.shiftKey && e.key === "I")) {
//         e.preventDefault();
//     }
// });

// document.addEventListener('visibilitychange', function () {
//     if (document.hidden) {
//         console.warn('User switched tabs or minimized, potentially for screen recording.');
//     }
// });
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
                // const video = alphaCardLarge.querySelector('.video-holder');
                // if (video && video.paused) {
                //     video.play().catch(e => console.error("Video play failed:", e)); // Add error handling for play()
                // }
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