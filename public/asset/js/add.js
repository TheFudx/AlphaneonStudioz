document.addEventListener("DOMContentLoaded", function () {
    const rootElement = document.querySelector("#root");

    if (rootElement) {
        const observer = new MutationObserver(function (mutationsList, observer) {
            const buttons = rootElement.querySelectorAll(".tracking-wide.overflow-hidden.capitalize.text-sm.flex.items-center.justify-center.outline-none.transition-colors");
            if (buttons.length > 1) { 
                // Check if there are at least two buttons
                buttons[1].style.display = "none"; // Hide the second button
                console.log("Second button hidden");
                observer.disconnect(); // Stop observing once done
            }
        });

        observer.observe(rootElement, { childList: true, subtree: true });
    } else {
        console.log("#root element not found");
    }
});
