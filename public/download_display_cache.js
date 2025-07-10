 document.addEventListener('DOMContentLoaded', () => {
            const videoListDiv = document.getElementById('videoList'); // This is the row container where video cards will be appended
            const loadingMessage = document.getElementById('loadingMessage'); // Paragraph for status messages

            // --- Function to initialize video metadata and set poster ---
            function initializeVideoCard(videoElement) {
                // Generate poster from the first frame once metadata is loaded
                console.log('videoElement:', videoElement);
                videoElement.onloadedmetadata = function() {
                    // Only generate if no valid poster is set or it's the transparent GIF
                    if (!videoElement.getAttribute('poster') || videoElement.getAttribute('poster') ===
                        'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=') {
                        videoElement.currentTime = 3; // Seek slightly into the video for a valid frame
                    }
                };

                videoElement.onseeked = function() {
                    if (videoElement.readyState >= 2) { // HAVE_CURRENT_DATA
                        // Ensure we only generate if needed
                        if (videoElement.getAttribute('poster') ===
                            'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=' || !videoElement
                            .getAttribute('poster')) {
                            const canvas = document.createElement('canvas');
                            canvas.width = videoElement.videoWidth;
                            canvas.height = videoElement.videoHeight;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
                            try {
                                const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                                videoElement.setAttribute('poster', dataUrl);
                            } catch (e) {
                                console.warn(
                                    "Could not generate video poster, likely due to CORS restrictions:", e);
                                // Fallback to a generic image if generation fails
                                videoElement.setAttribute('poster',
                                    '/images/default_video_poster.jpg'
                                    ); // Provide a generic fallback image path
                            }
                            videoElement.pause(); // Pause after generating poster
                        }
                    }
                };

                // Explicitly tell the video to load its metadata for poster generation
                // Add a small delay to ensure the event listeners are fully attached before loading
                setTimeout(() => {
                    videoElement.load();
                }, 100);
            }

            // --- Service Worker Registration ---
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/serviceworker.js', {
                        scope: '/'
                    }) // Ensure scope is correct
                    .then(registration => {
                        console.log('Service Worker registered with scope:', registration.scope);
                        // Once SW is ready, request cached video list
                        if (navigator.serviceWorker.controller) {
                            requestCachedVideosList();
                        } else {
                            // Listen for controllerchange if SW takes a moment to activate
                            navigator.serviceWorker.addEventListener('controllerchange', () => {
                                console.log(
                                    'Service Worker controller changed. New controller is active.');
                                requestCachedVideosList();
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Service Worker registration failed:', error);
                        loadingMessage.textContent =
                            'Service Workers are not supported or failed to register in this browser. Offline features will not work.';
                    });

                // Function to request cached video list from SW
                function requestCachedVideosList() {
                    if (navigator.serviceWorker.controller) {
                        loadingMessage.textContent = 'Checking local cache status for downloaded videos...';
                        loadingMessage.style.display = 'block';
                        navigator.serviceWorker.controller.postMessage({
                            type: 'REQUEST_CACHED_VIDEOS'
                        });
                    } else {
                        console.warn('Service Worker controller not available yet to request cached videos.');
                        loadingMessage.textContent =
                            'Service Worker is not yet active. Please refresh or try again.';
                    }
                }


                // --- Service Worker Message Listener ---
                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data) {
                        const videoUrlFromSW = event.data.url; // URL from Service Worker message
                        const videoIdFromSW = event.data.videoId; // ID from Service Worker message (for DELETE/CACHE actions)

                        // This selector works for VIDEO_DELETED/FAILED and any future single-card updates
                        const videoCardElement = document.querySelector(`.offline-video-card[data-video-id="${videoIdFromSW}"]`);
                        const removeStatusParagraph = videoCardElement ? videoCardElement.querySelector(
                            `.remove-status-${videoIdFromSW}`) : null;
                        const cacheStatusParagraph = videoCardElement ? videoCardElement.querySelector(
                            `.cache-status-${videoIdFromSW}`) : null;


                        if (event.data.type === 'CACHED_VIDEOS_LIST') {
                            const cachedUrls = event.data
                                .urls; // These are URLs actually in the browser cache
                            loadingMessage.style.display = 'none'; // Hide initial loading message

                            // Iterate over all pre-rendered video cards from the database
                            document.querySelectorAll('.offline-video-card').forEach(card => {
                                const cardVideoUrl = card.dataset.videoUrl;
                                const cardVideoId = card.dataset.videoId; // This is the database ID from Blade
                                const cardCacheStatus = card.querySelector(`.cache-status-${cardVideoId}`);

                                if (cardCacheStatus) {
                                    if (cachedUrls.includes(cardVideoUrl)) {
                                        cardCacheStatus.textContent = 'Downloaded (Offline)';
                                        cardCacheStatus.style.color = 'green';
                                    } else {
                                        cardCacheStatus.textContent = 'Not Cached Locally';
                                        cardCacheStatus.style.color = 'orange';
                                        // You might want to disable play button or show a warning here
                                    }
                                }
                            });

                            // Check if there are any downloaded videos from the database
                            const totalDatabaseDownloads = document.querySelectorAll('.offline-video-card')
                                .length;
                            if (totalDatabaseDownloads === 0) {
                                // If no videos were rendered by Blade (empty forelse), the primary loadingMessage will already be set
                                // No action needed here unless you explicitly want to change its text.
                            } else if (cachedUrls.length === 0 && totalDatabaseDownloads > 0) {
                                // If database has items, but none are locally cached
                                // Ensure this message is only added if not already present
                                if (!document.getElementById('noLocallyCachedMessage')) {
                                    videoListDiv.insertAdjacentHTML('beforeend',
                                        '<p class="text-white col-12 offline-video-card" id="noLocallyCachedMessage">Some videos are marked as downloaded in your account, but are not cached locally. Click play to re-download.</p>'
                                    );
                                }
                            } else {
                                // If videos are found and some are cached, ensure the "no locally cached" message is removed
                                const noLocallyCachedMessage = document.getElementById(
                                    'noLocallyCachedMessage');
                                if (noLocallyCachedMessage) {
                                    noLocallyCachedMessage.remove();
                                }
                            }

                        } else if (event.data.type === 'VIDEO_DELETED') {
                            // This message means the Service Worker successfully deleted from cache.
                            // Now, we can update the UI.
                            if (videoCardElement) {
                                if (cacheStatusParagraph) {
                                    cacheStatusParagraph.textContent = 'Status: Removed from Local Cache';
                                    cacheStatusParagraph.style.color = 'red';
                                }
                                if (removeStatusParagraph) {
                                    removeStatusParagraph.textContent = 'Removed from cache!';
                                    removeStatusParagraph.style.color = 'red';
                                }
                                console.log(`Video ${videoUrlFromSW} removed from local cache.`);

                                // Safely remove the card after a short delay for message visibility
                                setTimeout(() => {
                                    videoCardElement.remove();
                                    // Re-check for empty list after removal
                                    if (videoListDiv.querySelectorAll('.offline-video-card')
                                        .length === 0) {
                                        if (!document.getElementById('noOfflineVideosMessage')) {
                                            videoListDiv.insertAdjacentHTML('beforeend',
                                                '<p class="text-white col-12 offline-video-card" id="noOfflineVideosMessage">No offline videos found in cache. Go back and download some!</p>'
                                            );
                                        }
                                    }
                                }, 500); // Wait 0.5 seconds for the message to be seen

                            } else {
                                console.warn(
                                    `Could not find video card for videoId: ${videoIdFromSW} (URL: ${videoUrlFromSW}) to update after cache removal.`
                                );
                                // Even if UI element not found, we assume cache deletion was successful.
                                // The database deletion should have already been handled.
                            }
                        } else if (event.data.type === 'VIDEO_DELETE_FAILED') {
                            // This means the Service Worker failed to delete from cache.
                            if (removeStatusParagraph) {
                                removeStatusParagraph.textContent =
                                    `Failed to remove from cache: ${event.data.error || 'Unknown error'}.`;
                                removeStatusParagraph.style.color = 'red';
                            }
                            console.error('Video cache deletion failed:', event.data.url, event.data.error);
                        } else if (event.data.type === 'VIDEO_CACHED') {
                            // This handles the message from the main download page when a download completes
                            // and can also be used here if a download is initiated on this page
                            if (cacheStatusParagraph) { // Ensure this matches the video being updated
                                cacheStatusParagraph.textContent = 'Status: Downloaded (Offline)';
                                cacheStatusParagraph.style.color = 'green';
                            }
                            if (removeStatusParagraph) { // Clear "Removing..." status if it was there
                                removeStatusParagraph.textContent = '';
                            }
                            console.log(`Video ${videoUrlFromSW} is now cached.`);
                            // No need to request full list, just update this card if it exists
                        } else if (event.data.type === 'DOWNLOAD_PROGRESS') {
                            // If you want to show progress directly on this page for active downloads
                            // (e.g., if a user initiates a download on this page itself)
                            // This part isn't strictly necessary for the "Downloads" page but good to have for consistency
                            const downloadProgressText = videoCardElement?.querySelector(
                                '.download-progress-text');
                            if (downloadProgressText) {
                                downloadProgressText.textContent = `${Math.round(event.data.progress)}%`;
                            }
                            if (cacheStatusParagraph) {
                                cacheStatusParagraph.textContent =
                                    `Downloading... ${Math.round(event.data.progress)}%`;
                                cacheStatusParagraph.style.color = 'blue';
                            }
                        }
                    }
                });
            } else {
                loadingMessage.textContent =
                    'Service Workers are not supported in this browser. Offline features will not work.';
            }

            // --- NEW FUNCTION: Delete video record from database AND then from cache ---
            async function deleteVideoRecord(videoId, videoUrl, statusParagraph, videoCard) {
                if (!videoId) {
                    console.error('No video ID provided for deletion process.');
                    if (statusParagraph) statusParagraph.textContent = 'Error: No ID for deletion.';
                    return;
                }

                if (statusParagraph) statusParagraph.textContent = 'Deleting from database...';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content');
                    if (!csrfToken) {
                        console.error('CSRF token not found. Cannot proceed with database deletion.');
                        if (statusParagraph) statusParagraph.textContent = 'Error: CSRF token missing.';
                        return;
                    }

                    const response = await fetch(
                        `/downloads/${videoId}`, { // Adjust URL if your route is different
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken // Send CSRF token for Laravel protection
                            },
                        });

                    if (response.ok) {
                        const data = await response.json();
                        console.log('Database deletion successful:', data.message);
                        if (statusParagraph) {
                            statusParagraph.textContent = 'Deleted from DB. Removing from cache...';
                            statusParagraph.style.color = 'green';
                        }

                        // --- Database deletion successful, now instruct Service Worker to delete from cache ---
                        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                            navigator.serviceWorker.controller.postMessage({
                                type: 'DELETE_VIDEO',
                                url: videoUrl,
                                videoId: videoId // Pass the database ID to the SW
                            });
                            console.log(`Instructed Service Worker to delete ${videoUrl} from cache.`);
                        } else {
                            console.warn('Service Worker not active. Cannot remove from local cache.');
                            if (statusParagraph) {
                                statusParagraph.textContent =
                                    'Deleted from DB, but cache not removed (SW not active).';
                                statusParagraph.style.color = 'orange';
                            }
                            // Since SW is not active, remove the card visually now
                            if (videoCard) {
                                setTimeout(() => {
                                    videoCard.remove();
                                    if (videoListDiv.querySelectorAll('.offline-video-card').length ===
                                        0) {
                                        if (!document.getElementById('noOfflineVideosMessage')) {
                                            videoListDiv.insertAdjacentHTML('beforeend',
                                                '<p class="text-white col-12 offline-video-card" id="noOfflineVideosMessage">No offline videos found in cache. Go back and download some!</p>'
                                            );
                                        }
                                    }
                                }, 500);
                            }
                        }

                    } else {
                        const errorData = await response.json();
                        console.error('Database deletion failed:', response.status, errorData.message);
                        if (statusParagraph) {
                            statusParagraph.textContent =
                                `DB Delete Failed: ${errorData.message || 'Unknown error'}`;
                            statusParagraph.style.color = 'red';
                        }
                    }
                } catch (error) {
                    console.error('Error during database deletion fetch:', error);
                    if (statusParagraph) {
                        statusParagraph.textContent = `Network error during DB delete: ${error.message}`;
                        statusParagraph.style.color = 'red';
                    }
                }
            }


            // --- Function to attach listeners to dynamically created remove buttons ---
            function attachRemoveButtonListeners() {
                document.querySelectorAll('.remove-offline-button').forEach(button => {
                    button.onclick = null; // Clear previous listeners to prevent duplicates
                    button.addEventListener('click', (event) => {
                        const videoUrl = event.currentTarget.dataset.videoUrl;
                        const videoId = event.currentTarget.dataset.videoId; // This is the DB ID

                        const removeStatus = document.querySelector(`.remove-status-${videoId}`);
                        const videoCardElement = event.currentTarget.closest(
                            '.offline-video-card'); // Get the card element directly

                        // Start the deletion process by calling the new combined function
                        deleteVideoRecord(videoId, videoUrl, removeStatus, videoCardElement);
                    });
                });
            }

            // --- Initialize existing (online) video cards on page load (from DB) ---
            // This runs on page load for all video cards rendered by Blade
            document.querySelectorAll('.offline-video-card').forEach(card => {
                const videoElement = card.querySelector('.video-holder');
                if (videoElement) {
                    initializeVideoCard(videoElement);
                }
            });

            // Call this after the DOM is rendered to attach listeners to the Blade-rendered buttons
            attachRemoveButtonListeners();

            // The initial request to the Service Worker for cached videos list
            // is now handled within the SW registration promise, ensuring controller is ready.
        });