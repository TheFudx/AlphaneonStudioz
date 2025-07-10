// serviceworker.js

const CACHE_NAME = 'offline-video-cache-v1';
const PRECACHE_URLS = [
    // Add any essential static assets here (CSS, JS, images) that are used on the page itself
    // '/css/app.css',
    // '/js/app.js',
    // '/images/logo.png',
    '/' // Cache the homepage/root path
];

self.addEventListener('install', (event) => {
   console.log('[Service Worker] Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
           console.log('[Service Worker] Caching essential app shell assets');
            // Cache your application's static assets here
            return cache.addAll(PRECACHE_URLS).catch(err => {
                console.error('Failed to cache pre-defined URLs:', err);
            });
        })
    );
    self.skipWaiting(); // Activate the new service worker immediately
});

self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
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
       // console.log('[Service Worker] Fetching (and potentially caching) proxied video:', event.request.url);

        event.respondWith(
            caches.match(event.request).then((response) => {
                // Return cached response if found
                if (response) {
                    //console.log('[Service Worker] Serving from cache:', event.request.url);
                    return response;
                }

                // Otherwise, fetch from network and cache
                return fetch(event.request).then((networkResponse) => {
                    // Check if we received a valid response
                    if (!networkResponse || networkResponse.status !== 200) {
                        return networkResponse;
                    }

                    // For Range requests (video streaming), we need to handle them carefully.
                    // The standard Cache API can be tricky with partial responses.
                    // For now, let's cache the full response. If the networkResponse is a 206
                    // (partial content), caching it directly might not be what you want
                    // if you intend to cache the *entire* video.
                    // For full offline video, you'd typically download the whole thing.
                    // If you're streaming, you might only cache parts.
                    // For simplicity, we'll cache the network response as is.
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                        //console.log('[Service Worker] Cached new response:', event.request.url);
                    }).catch(error => {
                        console.error('[Service Worker] Failed to add to cache:', event.request.url, error);
                    });

                    return networkResponse;
                }).catch((error) => {
                    //console.error('[Service Worker] Fetch failed for:', event.request.url, error);
                    // You might want to serve a fallback video or a message here
                    // if the network is truly unavailable.
                    // For now, just re-throw or return new Response()
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
    if (event.data && event.data.type === 'CACHE_VIDEO') {
        const videoUrlToCache = event.data.url;
        //console.log('[Service Worker] Received message to cache video:', videoUrlToCache);

        event.waitUntil(
            caches.open(CACHE_NAME).then((cache) => {
                return cache.match(videoUrlToCache).then(cachedResponse => {
                    if (cachedResponse) {
                        //console.log(`[Service Worker] Video already in cache: ${videoUrlToCache}`);
                        // Send message back to main thread that it's already cached
                        event.source.postMessage({ type: 'VIDEO_CACHED', url: videoUrlToCache, status: 'already_cached' });
                        return cachedResponse;
                    } else {
                        return fetch(videoUrlToCache).then((response) => {
                          console.log(response)
                            if (!response || response.status !== 200) {
                               // console.error(`[Service Worker] Failed to fetch video for caching: ${response.status} ${videoUrlToCache}`);
                                event.source.postMessage({ type: 'VIDEO_CACHE_FAILED', url: videoUrlToCache, status: 'fetch_failed', error: `Status ${response.status}` });
                                return response;
                            }
                            // Clone the response because it can only be consumed once
                            const responseToCache = response.clone();
                            return cache.put(videoUrlToCache, responseToCache).then(() => {
                                console.log(`[Service Worker] Video cached: ${videoUrlToCache}`);
                                // Send message back to main thread
                                event.source.postMessage({ type: 'VIDEO_CACHED', url: videoUrlToCache, status: 'success' });
                                return response;
                            }).catch(error => {
                                //console.error(`[Service Worker] Failed to put video in cache: ${videoUrlToCache}`, error);
                                event.source.postMessage({ type: 'VIDEO_CACHE_FAILED', url: videoUrlToCache, status: 'put_failed', error: error.message });
                            });
                        }).catch(error => {
                            //console.error(`[Service Worker] Fetch error while caching video: ${videoUrlToCache}`, error);
                            event.source.postMessage({ type: 'VIDEO_CACHE_FAILED', url: videoUrlToCache, status: 'network_error', error: error.message });
                        });
                    }
                });
            })
        );
    }


    if (event.data && event.data.type === 'REQUEST_CACHED_VIDEOS') { // This is the NEW part
        //console.log('[Service Worker] Received request for cached videos list');
        event.waitUntil(
            caches.open(CACHE_NAME).then((cache) => {
                return cache.keys().then((requests) => {
                    // Filter requests to only include your proxied video URLs
                    const proxiedVideoUrls = requests
                        .filter(request => request.url.startsWith('http://127.0.0.1:8000/proxy-video/'))
                        .map(request => request.url); // Extract just the URLs

                    //console.log('[Service Worker] Sending cached video URLs:', proxiedVideoUrls);
                    // Send the list back to the client (your webpage) that requested it
                    event.source.postMessage({ type: 'CACHED_VIDEOS_LIST', urls: proxiedVideoUrls });
                });
            })
        );
    }
});