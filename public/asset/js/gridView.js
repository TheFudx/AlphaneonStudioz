// ✅ Define globally
function getColumnsPerRow() {
    const windowWidth = $(window).width();
    if (windowWidth >= 1400) { // XXL breakpoint
        return 6; // col-xxl-2
    } else if (windowWidth >= 1200) { // XL breakpoint
        return 4; // col-xl-3
    } else if (windowWidth >= 992) { // LG breakpoint
        return 4; // col-lg-3
    } else if (windowWidth >= 768) { // MD breakpoint
        return 4; // col-md-3
    } else if (windowWidth >= 576) { // SM breakpoint
        return 2; // col-sm-6
    } else { // XS breakpoint
        return 1; // col-12
    }
}

function highlightRowCards() {
    $(".alpha-card-large").removeClass('first-active last-active');
    const columnsPerRow = getColumnsPerRow();
    const $visibleCards = $(".movie-new-card:visible");
    const totalCards = $visibleCards.length;

    $visibleCards.each(function (index) {
        const $currentCard = $(this);
        const $alphaCardLarge = $currentCard.find('.alpha-card-large');
        $alphaCardLarge.css('left', '');
        const isFirst = index % columnsPerRow === 0;
        const isLast = ((index + 1) % columnsPerRow === 0 || (index + 1) === totalCards);

        // If both first and last in row (single card in row)
        if (isFirst && isLast) {
            $alphaCardLarge.css('left', '-8%').addClass('first-active single-in-row');
        } else {
            if (isFirst) {
                $alphaCardLarge.css('left', '-8%').addClass('first-active');
            }
            if (isLast) {
                $alphaCardLarge.css('right', '-8%').addClass('last-active');
            }
        }
    });
}

$(document).ready(function () {
    highlightRowCards();

    let resizeTimer;
    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            highlightRowCards();
        }, 250);
    });
});

    document.addEventListener("DOMContentLoaded", function () {
        const radioButtons = document.querySelectorAll('input[name="category"]');
        const videos = document.querySelectorAll('.movie-new-card');
        radioButtons.forEach(function (radioButton) {
            radioButton.addEventListener('change', function () {
                const selectedCategoryId = this.value;
                
                videos.forEach(function (video) {
                    const categoryId = video.dataset.categoryId.split(','); // Split categoryId into an array
                    if (categoryId.includes(selectedCategoryId) || selectedCategoryId === 'All') {
                        video.style.display = 'block';
                        video.style.transition = '0.50s ease';
                    } else {
                        video.style.display = 'none';
                        video.style.transition = '0.50s ease';
                    }
                });
                // Wait a tick to allow display changes to apply, then recalculate layout
                setTimeout(() => {
                    highlightRowCards();
                }, 50);
            });
        });
    });