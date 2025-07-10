// serviceworker.js
const CACHE_NAME = 'offline-video-cache-v1'; // This must match the cache name where your videos are stored
const PRECACHE_URLS = [
    // Add any essential static assets here (CSS, JS, images) that are used on the page itself
    // '/css/app.css',
    // '/js/app.js',
    // '/images/logo.png',
    '/' // Cache the homepage/root path
];
self.addEventListener('install', (event) => {
    console.log('Service Worker:- Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('Service Worker:- Caching essential app shell assets');
            // Cache your application's static assets here
            return cache.addAll(PRECACHE_URLS).catch(err => {
                console.error('Failed to cache pre-defined URLs:', err);
            });
        })
    );
    self.skipWaiting(); // Activate the new service worker immediately
});
self.addEventListener('activate', (event) => {
    console.log('Service Worker:- Activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME && !cacheName.startsWith('video-metadata-cache')) { // Added condition to preserve metadata cache if it's named differently
                        console.log('Service Worker:- Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim(); // Take control of existing clients immediately
});
self.addEventListener('fetch', (event) => {
    // Intercept requests for your proxied video
    // Adjust this regex based on your actual Laravel proxy route pattern
    if (event.request.url.startsWith('http://127.0.0.1:8000/proxy-video/')) {
        event.respondWith(
            caches.match(event.request).then((response) => {
                // Return cached response if found
                if (response) {
                    console.log('Service Worker:- Serving from cache:', event.request.url);
                    return response;
                }
                // Otherwise, fetch from network and cache
                return fetch(event.request).then((networkResponse) => {
                    // Check if we received a valid response
                    if (!networkResponse || networkResponse.status !== 200) {
                        return networkResponse;
                    }
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                        console.log('Service Worker:- Cached new response during fetch:', event.request.url);
                    }).catch(error => {
                        console.error('Service Worker:- Failed to add to cache during fetch handler:', event.request.url, error);
                    });
                    return networkResponse;
                }).catch((error) => {
                    console.error('Service Worker:- Fetch failed for:', event.request.url, error);
                    return new Response('Network error or video not found.', { status: 404 });
                });
            })
        );
    } else {
        // For all other requests, just go to network or cache if needed
        event.respondWith(
            caches.match(event.request).then(response => {
                return response || fetch(event.request);
            })
        );
    }
});
// Listener for messages from the main thread (like your button click)
self.addEventListener('message', (event) => {
    // Ensure event.data exists and has a type property
    if (!event.data || !event.data.type) {
        console.warn('Service Worker:- Received message with no type or data:', event.data);
        return;
    }
    if (event.data.type === 'CACHE_VIDEO') {
        const { videoUrl, videoId, videoName, videoThumbnailUrl } = event.data.data;
        const client = event.source; // Get the client to send messages back to
        const metadataUrl = `${videoUrl}-metadata`; // Unique URL for metadata
        console.log('Service Worker:- Received message to cache video:', videoUrl);
        event.waitUntil(
            (async () => {
                const cache = await caches.open(CACHE_NAME);
                // Check if metadata already exists (implies video might be cached)
                const existingMetadataResponse = await cache.match(metadataUrl);
                const cachedVideoResponse = await cache.match(videoUrl);
                if (existingMetadataResponse && cachedVideoResponse) {
                    console.log(`Service Worker:- Video already in cache: ${videoUrl}`);
                    client.postMessage({ type: 'VIDEO_CACHED', url: videoUrl, videoId: videoId, videoName:videoName, videoThumbnailUrl: videoThumbnailUrl, status: 'already_cached' });
                    return; // Exit early as it's already cached
                }
                try {
                    const response = await fetch(videoUrl);
                    if (!response || response.status !== 200) {
                        console.error(`Service Worker:- Failed to fetch video for caching: ${response.status} ${videoUrl}`);
                        client.postMessage({ type: 'VIDEO_CACHE_FAILED', url: videoUrl, videoId: videoId,videoName:videoName, videoThumbnailUrl: videoThumbnailUrl, status: 'fetch_failed', error: `Status ${response.status}` });
                        return;
                    }
                    const contentLength = response.headers.get('content-length');
                    if (!contentLength) {
                        console.warn('Service Worker:- Content-Length header missing. Cannot report download progress for:', videoUrl);
                        await cache.put(videoUrl, response.clone());
                        // Store metadata as a separate response
                        const metadata = { videoId, videoUrl, videoName, videoThumbnailUrl };
                        const metadataResponse = new Response(JSON.stringify(metadata), {
                            headers: { 'Content-Type': 'application/json' }
                        });
                        await cache.put(metadataUrl, metadataResponse);
                        client.postMessage({ type: 'VIDEO_CACHED', url: videoUrl, videoId: videoId, videoName:videoName, videoThumbnailUrl: videoThumbnailUrl, status: 'success' });
                        return;
                    }
                    let receivedLength = 0;
                    const totalLength = parseInt(contentLength, 10);
                    const reader = response.body.getReader();
                    const newResponse = new Response(new ReadableStream({
                        async start(controller) {
                            while (true) {
                                const { done, value } = await reader.read();
                                if (done) {
                                    break;
                                }
                                receivedLength += value.length;
                                const progress = (receivedLength / totalLength) * 100;
                                // Send progress updates back to the client
                                if (client) {
                                    client.postMessage({
                                        type: 'DOWNLOAD_PROGRESS',
                                        url: videoUrl,
                                        videoId: videoId,
                                        videoName:videoName, videoThumbnailUrl: videoThumbnailUrl,
                                        progress: progress
                                    });
                                }
                                controller.enqueue(value);
                            }
                            controller.close();
                        }
                    }), {
                        headers: response.headers
                    });
                    await cache.put(videoUrl, newResponse.clone()); // Put the new response into cache
                    // Store metadata as a separate response in the cache
                    const metadata = { videoId, videoUrl, videoName, videoThumbnailUrl };
                    const metadataResponse = new Response(JSON.stringify(metadata), {
                        headers: { 'Content-Type': 'application/json' }
                    });
                    await cache.put(metadataUrl, metadataResponse);
                    console.log(`Service Worker:- Video cached with progress and metadata: ${videoUrl}`);
                    client.postMessage({ type: 'VIDEO_CACHED', url: videoUrl, videoId: videoId, status: 'success' });
                } catch (error) {
                    console.error(`Service Worker:- Error during video caching process for ${videoUrl}:`, error);
                    client.postMessage({ type: 'VIDEO_CACHE_FAILED', url: videoUrl, videoId: videoId, videoName:videoName, videoThumbnailUrl: videoThumbnailUrl,status: 'network_error', error: error.message });
                }
            })()
        );
    } else if (event.data.type === 'REQUEST_CACHED_VIDEOS') {
        const client = event.source;
        console.log('Service Worker:- Received request for cached videos list', event);
        event.waitUntil(
            caches.open(CACHE_NAME).then(async (cache) => {
                const requests = await cache.keys();
                const cachedVideosMetadata = [];
                for (const request of requests) {
                    // Identify metadata entries by their unique URL pattern
                    if (request.url.endsWith('-metadata')) {
                        try {
                            const metadataResponse = await cache.match(request);
                            if (metadataResponse) {
                                const metadata = await metadataResponse.json();
                                cachedVideosMetadata.push(metadata);
                            }
                        } catch (error) {
                            console.error(`Service Worker:- Error parsing metadata for ${request.url}:`, error);
                        }
                    }
                }
                console.log('Service Worker:- Sending cached video metadata:', cachedVideosMetadata.length, 'videos');
                client.postMessage({ type: 'CACHED_VIDEOS_LIST', videos: cachedVideosMetadata });
            }).catch(error => {
                console.error('Service Worker:- Error requesting cached videos list:', error);
                client.postMessage({ type: 'CACHED_VIDEOS_LIST_ERROR', error: error.message });
            })
        );
    } else if (event.data.type === 'DELETE_VIDEO') {
        const client = event.source;
        const { url: videoUrlToDelete, videoId } = event.data;
        const metadataUrlToDelete = `${videoUrlToDelete}-metadata`;
        console.log(`Service Worker:- Attempting to delete video: ${videoUrlToDelete} (videoId: ${videoId})`);
        event.waitUntil(
            (async () => {
                try {
                    const cache = await caches.open(CACHE_NAME);
                    const deletedVideo = await cache.delete(videoUrlToDelete);
                    const deletedMetadata = await cache.delete(metadataUrlToDelete);
                    // Send success message if either video or metadata was deleted,
                    // indicating that the primary goal (removing from cache) was achieved.
                    if (deletedVideo || deletedMetadata) {
                        console.log(`Service Worker:- Successfully deleted ${videoUrlToDelete} and/or its metadata from cache.`);
                        client.postMessage({
                            type: 'VIDEO_DELETED',
                            url: videoUrlToDelete,
                            videoId: videoId,
                            success: true // Explicitly indicate success for the client to proceed
                        });
                    } else {
                        // If neither was deleted, it means they weren't found in the cache.
                        // We can still consider this a "success" from a deletion perspective
                        // (i.e., the video is no longer in cache), but it's good to log.
                        console.warn(`Service Worker:- Video ${videoUrlToDelete} not found in cache for deletion.`);
                        client.postMessage({
                            type: 'VIDEO_DELETED', // Still send VIDEO_DELETED to trigger DB deletion
                            url: videoUrlToDelete,
                            videoId: videoId,
                            success: true, // Treat as success, as it's effectively "removed" from cache
                            message: 'Video not found in cache, but client should proceed with DB delete.'
                        });
                    }
                } catch (error) {
                    console.error(`Service Worker:- Error deleting ${videoUrlToDelete} from cache:`, error);
                    client.postMessage({
                        type: 'VIDEO_DELETE_FAILED',
                        url: videoUrlToDelete,
                        videoId: videoId,
                        error: error.message
                    });
                }
            })()
        );
    } else {
        console.log('Service Worker:- Unhandled message type:', event.data.type, event.data);
    }
});