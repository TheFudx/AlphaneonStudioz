<div class="offline-video-container">
        <div class="container-fluid mt-5 text-white">
            <h1>Your Offline Videos</h1>
            <p>These videos should play even if you are offline, as they are stored in your browser's cache.</p>
            <button id="loadOfflineVideos" class="btn btn-primary mb-3">Load Cached Videos</button>
            <div id="videoList" class="row">
                <p id="loadingMessage">Click "Load Cached Videos" to see them...</p>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loadButton = document.getElementById('loadOfflineVideos');
            const videoListDiv = document.getElementById('videoList');
            const loadingMessage = document.getElementById('loadingMessage');
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/serviceworker.js')
                    .then(registration => {
                        console.log('Service Worker registered with scope:', registration.scope);
                    })
                    .catch(error => {
                        console.error('Service Worker registration failed:', error);
                    });
                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data && event.data.type === 'CACHED_VIDEOS_LIST') {
                        const videoUrls = event.data.urls;
                        console.log('Received cached video list from Service Worker:', videoUrls);
                        loadingMessage.style.display = 'none';
                        if (videoUrls.length === 0) {
                            videoListDiv.innerHTML =
                                '<p>No videos found in cache. Go back and download some!</p>';
                        } else {
                            videoListDiv.innerHTML = ''; // Clear previous content
                            videoUrls.forEach(url => {
                                const colDiv = document.createElement('div');
                                colDiv.className = 'col col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3 col-xxl-2 ps-md-2 ps-sm-1 pe-sm-1 pe-md-2 mb-2 pe-1 ps-1';
                                const videoContainer = document.createElement('div');
                                videoContainer.className = 'video-container';
                                // --- Create elements for thumbnail and video ---
                                const thumbnailImg = document.createElement('img');
                                thumbnailImg.className = 'video-thumbnail';
                                thumbnailImg.style.width = '100%';
                                thumbnailImg.style.height = '200px';
                                thumbnailImg.style.display = 'block'; // Initially show thumbnail
                                thumbnailImg.style.cursor = 'pointer'; // Indicates it's clickable
                                thumbnailImg.style.marginBottom = '10px';
                                thumbnailImg.alt = 'Video thumbnail';
                                // Placeholder image while the actual thumbnail is being generated
                                thumbnailImg.src =
                                    'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=';
                                // Or a path to a loading spinner: thumbnailImg.src = '{{ asset('images/loading.gif') }}';
                                const videoElement = document.createElement('video');
                                videoElement.setAttribute('controls',
                                ''); // Keep default controls for play/pause, volume, fullscreen
                                videoElement.setAttribute('controlsList',
                                'nodownload'); // Hide download button
                                // Note: controlsList does not natively hide playback speed or picture-in-picture.
                                // These might still appear depending on the browser.
                                // If you absolutely need to hide them, you'd need a fully custom player.
                                videoElement.setAttribute('preload',
                                'metadata'); // Important for thumbnail generation
                                videoElement.src = url;
                                videoElement.style.display =
                                'none'; // Initially hide the actual video
                                // const urlParagraph = document.createElement('p');
                                // urlParagraph.textContent = 'Cached URL: ' + url;
                                // urlParagraph.style.fontSize = '0.8em';
                                // urlParagraph.style.color = 'green';
                                // urlParagraph.style.wordBreak = 'break-all';
                                // Append elements to the container
                                videoContainer.appendChild(thumbnailImg);
                                videoContainer.appendChild(videoElement);
                                // videoContainer.appendChild(urlParagraph);
                                colDiv.appendChild(videoContainer);
                                videoListDiv.appendChild(colDiv);
                                // --- Thumbnail Generation Logic ---
                                // Listen for when video metadata is loaded
                                videoElement.onloadedmetadata = function() {
                                    // Try to seek to a very early point to get the first frame
                                    // Using a small non-zero value is more reliable than 0
                                    videoElement.currentTime = videoElement.duration * 0.05;
                                };
                                // Listen for when the video has successfully sought to the specified time
                                videoElement.onseeked = function() {
                                    // Ensure video data is ready to be drawn
                                    if (videoElement.readyState >= 2) { // HAVE_CURRENT_DATA
                                        const canvas = document.createElement('canvas');
                                        canvas.width = videoElement.videoWidth;
                                        canvas.height = videoElement.videoHeight;
                                        const ctx = canvas.getContext('2d');
                                        // Draw the current video frame onto the canvas
                                        ctx.drawImage(videoElement, 0, 0, canvas.width, canvas
                                            .height);
                                        // Set the thumbnail source from the canvas
                                        // Using 'image/jpeg' and a quality factor is efficient for thumbnails
                                        thumbnailImg.src = canvas.toDataURL('image/jpeg', 0.8);
                                        // Important: Pause the video after generating thumbnail if you don't want it playing
                                        videoElement.pause();
                                    }
                                };
                                // --- Playback Toggle Logic ---
                                // When the thumbnail is clicked, hide it and show/play the video
                                thumbnailImg.addEventListener('click', () => {
                                    thumbnailImg.style.display = 'none'; // Hide thumbnail
                                    videoElement.style.display = 'block'; // Show video
                                    videoElement.play(); // Start playing
                                });
                                // When the video ends, hide it and show the thumbnail again
                                videoElement.addEventListener('ended', () => {
                                    videoElement.style.display = 'none';
                                    thumbnailImg.style.display = 'block';
                                    videoElement.currentTime =
                                    0; // Reset video to the beginning
                                });
                            });
                        }
                    }
                });
            } else {
                loadingMessage.textContent =
                    'Service Workers are not supported in this browser. Offline features will not work.';
                loadButton.disabled = true;
            }
            loadButton.addEventListener('click', () => {
                if (navigator.serviceWorker.controller) {
                    loadingMessage.textContent = 'Loading cached videos...';
                    loadingMessage.style.display = 'block';
                    navigator.serviceWorker.controller.postMessage({
                        type: 'REQUEST_CACHED_VIDEOS'
                    });
                } else {
                    loadingMessage.textContent =
                        'Service Worker not active. Please ensure it is registered and try refreshing the page.';
                    loadButton.disabled = true;
                }
            });
        });
    </script>
change my design from this to below given code
