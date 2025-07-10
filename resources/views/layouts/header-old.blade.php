 <script>
            $(document).ready(function() {
                $(".navbar-toggler").click(function() {
                    $('#collapsibleNavId').toggle();
                    if ($('#collapsibleNavId').is(':visible')) {
                        $(".navbar").css('background',
                            'linear-gradient(to top, #000000 70%, rgba(255, 0, 0, 0))');
                    } else {
                        $(".navbar").css('background', ''); // remove the background
                    }
                })
            })
        </script>
        <script>
            // document.querySelectorAll('.nav-item.dropdown').forEach(item => {
            //     item.addEventListener('click', function(event) {
            //         event.stopPropagation(); // Prevent unwanted bubbling
            //         let dropdownMenu = this.querySelector('.custom-dropdown-menu');
            //         // Hide all other dropdowns first
            //         document.querySelectorAll('.custom-dropdown-menu').forEach(menu => {
            //             if (menu !== dropdownMenu) {
            //                 menu.style.display = 'none';
            //             }
            //         });
            //         // Toggle the clicked dropdown
            //         dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
            //     });
            // });
            // Close dropdown when clicking the close button
            document.querySelectorAll('.close-dropdown').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation(); // Prevent click from triggering another dropdown
                    this.closest('#firstdropdownMenu').style.display = 'none';
                    
                    // this.closest('.custom-dropdown-menu').style.display = 'none';
                });
            });
            document.querySelectorAll('.profile-close-dropdown').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation(); // Prevent click from triggering another dropdown
                    // this.closest('.custom-dropdown-menu').style.display = 'none';
                    this.closest('#profiledropdownMenu').style.display = 'none';
                });
            });
            document.querySelectorAll('.notification-dropdown').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation(); // Prevent click from triggering another dropdown
                    // this.closest('.custom-dropdown-menu').style.display = 'none';
                    this.closest('#notificationdropdownMenu').style.display = 'none';
                });
            });
            // Close dropdown when clicking anywhere outside
            // document.addEventListener('click', function() {
            //     document.querySelectorAll('.custom-dropdown-menu').forEach(menu => {
            //         menu.style.display = 'none';
            //     });
            // });
            $(document).ready(function() {
                $(".owl-carousel .owl-nav").removeClass('disabled');
                $(".owl-carousel").each(function() {
                    var carousel = $(this);
                    // Check if the carousel has the data-skip-carousel attribute set to "true"
                    if (carousel.data("skip-carousel") === true) {
                        return true; // Equivalent to 'continue' in a standard for loop
                    }
                    // Only initialize if not already initialized
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
                                    items: 2, // Consider changing to 1 for 0-319px if only one item looks better
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
                            onTranslated: addActiveClasses, // Using onTranslated as discussed, it's usually more stable
                            onResized: addActiveClasses,
                            onRefreshed: addActiveClasses,
                            onDragged: addActiveClasses
                        });
                    }
                });
                function addActiveClasses(event) {
                    var carousel = $(event.target);
                    carousel.find(".owl-item").removeClass(
                        "first-active last-active"); // 1. Remove all previous active classes
                    var activeItems = carousel.find(
                        ".owl-item.active"); // 2. Get all currently active items (the visible ones)
                    if (activeItems.length > 0) {
                        // If there's only one active item, it's both the first and the last.
                        if (activeItems.length === 1) {
                            activeItems.first().addClass("first-active");
                        } else {
                            // For multiple active items, use your more robust logic for the first,
                            // and the standard .last() for the last.
                            // Find the truly first *visible* active item based on its position
                            var firstActive = null;
                            activeItems.each(function() {
                                var item = $(this);
                                var itemLeft = item.position().left;
                                var carouselWidth = carousel.find('.owl-stage-outer')
                                    .width(); // Correctly get the viewport width
                                // A small tolerance (e.g., 5px) can help if floats or sub-pixel rendering cause tiny deviations
                                // Check if the item's left edge is at or just slightly off the left boundary
                                if (itemLeft >= -5 && itemLeft < carouselWidth) { // Added -5px tolerance
                                    firstActive = item;
                                    return false; // Break out of .each() loop
                                }
                            });
                            // If a "firstActive" was found by position, use it. Otherwise, fallback to the first in the jQuery set.
                            if (firstActive) {
                                firstActive.addClass("first-active");
                            } else {
                                // Fallback for cases where positional logic might fail (e.g., all active items are partially off-screen at the start due to specific stagePadding/margin/responsive setup)
                                activeItems.first().addClass("first-active");
                            }
                            // Add 'last-active' to the last visible active item
                            activeItems.last().addClass("last-active");
                        }
                    }
                }
            });
        </script>