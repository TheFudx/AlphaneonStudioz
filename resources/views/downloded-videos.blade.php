@extends('layouts.main')
@section('title')
    Alphastudioz | Downloads
@endsection
<style>
    .image-wrapper video{
        height: 190px;
        width: 100%;
        border-radius: 10px
    }
    .footer-pushed-down {
            margin-top: 350px !important; /* Or dynamically calculate this based on #searchResultsNew height */
        }
</style>
@section('main-section')
    <section id="section-home-newdes" class="podcast mt-5 pt-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid">
                    <span class="l-head">Your Downloaded Videos</span>
                    <hr class="mb-2">
                    <div class="slider-container mt-2">
                        <div class="latest-release-slider movies-page-section">
                            <div id="videoList" class="row g-1">
                                <p id="loadingMessage" class="text-white">Click "Load Cached Videos" to see them...</p>
                            </div>
                            {{-- <button id="loadOfflineVideos" class="btn btn-primary mt-3">Load Cached Videos</button> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const videoListDiv = document.getElementById('videoList'); // This is the row container where video cards will be appended
    const loadingMessage = document.getElementById('loadingMessage'); // Paragraph for status messages

    // --- Service Worker Registration ---
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/serviceworker.js', {
                scope: '/'
            }) // Ensure scope is correct
            .then(registration => {
                console.log('Service Worker registered with scope:', registration.scope);
            })
            .catch(error => {
                console.error('Service Worker registration failed:', error);
                loadingMessage.textContent =
                    'Service Workers are not supported or failed to register in this browser. Offline features will not work.';
            });

        // --- Service Worker Message Listener ---
        navigator.serviceWorker.addEventListener('message', async (event) => { // Added 'async' here
            if (event.data) {
                const videoId = event.data.videoId; // This videoId comes from the SW message
                const videoUrl = event.data.url; // Also useful for logging/context

                // Select the specific video card and remove button for precise updates
                const videoCardElement = document.querySelector(`.offline-video-card[data-video-id="${videoId}"]`);
                const removeButton = videoCardElement ? videoCardElement.querySelector('.remove-offline-button') : null;


                if (event.data.type === 'CACHED_VIDEOS_LIST') {
                    const cachedVideos = event.data.videos;
                    loadingMessage.style.display = 'none'; // Hide initial loading message
                    console.log('Cached videos received:', cachedVideos);
                    // Clear any existing dynamically added offline video elements before rendering
                    const currentOfflineVideos = videoListDiv.querySelectorAll('.offline-video-card');
                    currentOfflineVideos.forEach(card => card.remove());

                    if (cachedVideos.length === 0) {
                        $('.footer-section').addClass('footer-pushed-down');
                        videoListDiv.insertAdjacentHTML('beforeend',
                            '<p class="text-white col-12 offline-video-card" id="noOfflineVideosMessage">No offline videos found in cache. Go back and download some!</p>'
                        );
                    } else {
                        $('.footer-section').removeClass('footer-pushed-down');
                        const noVideosMessage = document.getElementById('noOfflineVideosMessage');
                        if (noVideosMessage) {
                            noVideosMessage.remove();
                        }

                        cachedVideos.forEach((videoMetadata) => {
                            const url = videoMetadata.videoUrl;
                            const videoIdFromSW = videoMetadata.videoId;
                            const videoName = videoMetadata.videoName;
                            const videoThumbnailUrl = videoMetadata.videoThumbnailUrl;

                            const colDiv = document.createElement('div');
                            colDiv.className =
                                'item movie-new-card col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 col-xxl-2 ps-md-2 ps-sm-1 pe-sm-1 pe-md-2 mb-2 pe-1 ps-1 offline-video-card';
                            colDiv.dataset.videoUrl = url;
                            colDiv.dataset.videoId = videoIdFromSW;

                            const cardsContainer = document.createElement('div');
                            cardsContainer.className = 'cards-container';

                            const alphaCard = document.createElement('div');
                            alphaCard.className = 'alpha-card skeleton card-for-podcast';

                            const imageWrapper = document.createElement('div');
                            imageWrapper.className = 'image-wrapper';

                            const videoElement = document.createElement('video');
                            videoElement.setAttribute('controls', '');
                            videoElement.setAttribute('controlsList', 'nodownload');
                            videoElement.src = url;
                            if (videoThumbnailUrl) {
                                videoElement.setAttribute('poster', videoThumbnailUrl);
                            } else {
                                videoElement.setAttribute('poster', '/path/to/default-poster.jpg');
                            }
                            videoElement.muted = false;
                            videoElement.loop = false;

                            imageWrapper.appendChild(videoElement);
                            alphaCard.appendChild(imageWrapper);

                            const nameParagraph = document.createElement('p');
                            nameParagraph.className = 'text-white mt-3';
                            nameParagraph.textContent = videoName || 'Unknown Video Title';

                            const removeButtonElement = document.createElement('button');
                            removeButtonElement.className = 'btn btn-danger btn-sm remove-offline-button';
                            removeButtonElement.textContent = 'Remove Download';
                            removeButtonElement.dataset.videoUrl = url;
                            removeButtonElement.dataset.videoId = videoIdFromSW;

                            
                            cardsContainer.appendChild(alphaCard);
                            cardsContainer.appendChild(nameParagraph);
                            cardsContainer.appendChild(removeButtonElement);
                            colDiv.appendChild(cardsContainer);
                            videoListDiv.appendChild(colDiv);
                        });
                        attachRemoveButtonListeners();
                    }
                } else if (event.data.type === 'VIDEO_DELETED') {
                    // Service Worker has successfully deleted from cache.
                    // Now, send a request to the server to delete from the database.
                    if (removeButton) {
                        removeButton.textContent = 'Syncing DB...'; // Inform user about database deletion
                    }

                    try {
                        const response = await fetch(`/downloads/${videoId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // For Laravel CSRF
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        console.log('Database deletion data:', data);
                        console.log('Database deletion response:', response);
                        if (response.ok && data.success) {
                            $('.footer-section').addClass('footer-pushed-down');
                            if (removeButton) {
                                removeButton.textContent = 'Removed!';
                                removeButton.disabled = true;
                                removeButton.classList.remove('btn-danger');
                                removeButton.classList.add('btn-success');
                            }
                            if (videoCardElement) {
                                videoCardElement.style.transition = 'opacity 0.5s ease-out';
                                videoCardElement.style.opacity = '0';
                                setTimeout(() => {
                                    videoCardElement.remove();
                                    console.log(`Video card removed for ${videoId}`);
                                    if (videoListDiv.querySelectorAll('.offline-video-card').length === 0) {
                                        videoListDiv.insertAdjacentHTML('beforeend',
                                            '<p class="text-white col-12 offline-video-card" id="noOfflineVideosMessage">No offline videos found in cache. Go back and download some!</p>'
                                        );
                                    }
                                }, 500);
                            }
                        } else {
                            $('.footer-section').removeClass('footer-pushed-down');
                            if (removeButton) {
                                removeButton.textContent = 'DB Delete Failed';
                                removeButton.disabled = false;
                                removeButton.classList.remove('btn-success');
                                removeButton.classList.add('btn-danger');
                            }
                            console.error('Database deletion failed:', data.message || 'Unknown error');
                        }
                    } catch (error) {
                        if (removeButton) {
                            removeButton.textContent = 'DB Request Error';
                            removeButton.disabled = false;
                            removeButton.classList.remove('btn-success');
                            removeButton.classList.add('btn-danger');
                        }
                        console.error('Error sending delete request to server:', error);
                    }
                } else if (event.data.type === 'VIDEO_DELETE_FAILED') {
                    // Service Worker failed to delete from cache.
                    if (removeButton) {
                        removeButton.textContent = `Cache Delete Failed: ${event.data.error || 'Unknown error'}`;
                        removeButton.disabled = false;
                        removeButton.classList.remove('btn-success');
                        removeButton.classList.add('btn-danger');
                    }
                    console.error('Video deletion from cache failed:', videoUrl, event.data.error);
                }
            }
        });
    } else {
        loadingMessage.textContent =
            'Service Workers are not supported in this browser. Offline features will not work.';
    }

    // --- Function to attach listeners to dynamically created remove buttons ---
    function attachRemoveButtonListeners() {
        document.querySelectorAll('.remove-offline-button').forEach(button => {
            button.onclick = null; // Clear existing click handlers to prevent duplicates
            button.addEventListener('click', (event) => {
                const videoUrl = event.currentTarget.dataset.videoUrl;
                const videoId = event.currentTarget.dataset.videoId;
                const removeButton = event.currentTarget; // The clicked button itself

                removeButton.textContent = 'Removing from Cache...';
                removeButton.disabled = true; // Disable button during removal

                if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                    console.log('Sending message to service worker to delete video from cache:', videoUrl);
                    navigator.serviceWorker.controller.postMessage({
                        type: 'DELETE_VIDEO',
                        url: videoUrl,
                        videoId: videoId
                    });
                } else {
                    console.warn(
                        'Service Worker is not active. Cannot send deletion message.');
                    removeButton.textContent = 'Failed (SW not active)';
                    removeButton.disabled = false;
                }
            });
        });
    }

    // --- Initial Load of Cached Videos when the page loads ---
    if (navigator.serviceWorker.controller) {
        loadingMessage.textContent = 'Loading cached videos...';
        loadingMessage.style.display = 'block';
        navigator.serviceWorker.controller.postMessage({
            type: 'REQUEST_CACHED_VIDEOS'
        });
    } else {
        loadingMessage.textContent =
            'Service Worker not active. Please ensure it is registered and try refreshing the page.';
    }

    // --- Initialize existing (online) video cards on page load ---
    const existingVideoCards = document.querySelectorAll('.item.movie-new-card:not(.offline-video-card)');
    existingVideoCards.forEach(card => {
        const videoElement = card.querySelector('video');
        if (videoElement) {
            // Placeholder for online video initialization if needed
        }
    });
});
</script>
@endsection
