@if ($video->video_upload_type === 'transcoded')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const video = document.getElementById("transcodedplayer");
            const source =
                config('app.website_url') . 'admin_panel/public/storage/videos/{{ $video->transcoded_video_path }}/master.m3u8';
            const defaultOptions = {
                controls: [
                    'play-large',
                    'play',
                    'progress',
                    'current-time',
                    'duration',
                    'mute',
                    'volume',
                    'captions',
                    'settings',
                    'pip',
                    'airplay',
                    'fullscreen',
                ]
            };
            const thumbnailInterval = 4; // Interval between segments (matches your ffmpeg hls_time)
            if (!Hls.isSupported()) {
                video.src = source;
                new Plyr(video, defaultOptions);
            } else {
                const hls = new Hls({
                    startLevel: -1, // Start with the "Auto" mode
                    maxBufferSize: 30 * 1000 * 1000, // Allow large buffers for smoother playback
                    maxBufferLength: 30, // Buffer up to 30 seconds of video
                    capLevelToPlayerSize: true, // Ensure resolution matches the player size
                    abrEwmaDefaultEstimate: 5000000, // Adjust default bitrate estimate for faster networks
                });
                hls.loadSource(source);
                hls.attachMedia(video);
                
                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    const availableQualities = hls.levels.map((l) => l.height);
                    availableQualities.unshift(0);

                    defaultOptions.quality = {
                        default: 0,
                        options: availableQualities,
                        forced: true,
                        onChange: (newQuality) => updateQuality(newQuality),
                    };

                    const player = new Plyr(video, defaultOptions);

                    let lastSentTime = 0;

                    player.on('timeupdate', () => {
                        const currentSec = Math.floor(player.currentTime);
                        if (currentSec !== lastSentTime) {
                            lastSentTime = currentSec;
                            saveWatchTime(currentSec);
                        }
                    });

                    player.on('loadedmetadata', () => {
                        if (resumeTime > 5) {
                            player.currentTime = resumeTime;
                        }
                        const totalSegments = Math.ceil(player.duration / thumbnailInterval);
                        addThumbnailPreview(player, totalSegments);
                    });

                    addCustomControls(player);
                });
                window.hls = hls;
            }

            function updateQuality(newQuality) {
                if (newQuality === 0) {
                    const autoQualityIndex = window.hls.levels.findIndex((level) => level.height === 480);
                    window.hls.currentLevel = autoQualityIndex !== -1 ? autoQualityIndex : -1;
                } else {
                    window.hls.levels.forEach((level, levelIndex) => {
                        if (level.height === newQuality) {
                            window.hls.currentLevel = levelIndex;
                        }
                    });
                }
            }

            function addThumbnailPreview(player, totalSegments) {
                const controlsContainer = player.elements.container;
                const thumbnailPreview = document.createElement('img');
                thumbnailPreview.className = 'plyr__control thumbnail-preview';
                thumbnailPreview.id = 'thumbnailPreview';
                thumbnailPreview.src = `{{ $video->landscape_url }}`;
                controlsContainer.appendChild(thumbnailPreview);
                const progressBar = player.elements.progress;
                progressBar.addEventListener('mousemove', (e) => {
                    const rect = progressBar.getBoundingClientRect();
                    const offsetX = e.clientX - rect.left;
                    const hoverTime = (offsetX / progressBar.offsetWidth) * player.duration;
                    const segmentIndex = Math.floor(hoverTime / thumbnailInterval);
                    const thumbnailUrl =
                        `{{ config('app.website_url') }}admin_panel/public/storage/videos/{{ $video->transcoded_video_path }}/thumbnails/480p_thumbnail_${String(segmentIndex).padStart(3, '0')}.jpg`;
                    thumbnailPreview.src = thumbnailUrl;
                    const isFullscreen = !!document.fullscreenElement;
                    thumbnailPreview.style.left = `${e.pageX - (isFullscreen ? 70 : 70)}px`;
                    thumbnailPreview.style.display = 'block';
                });
                progressBar.addEventListener('mouseleave', () => {
                    thumbnailPreview.style.display = 'none';
                });
            }

            function addCustomControls(player) {
                const controlsContainer = player.elements.controls;
                const playerContainer = player.elements.container;
                let hideTimeout = null;
                let isMouseOverPlayer = false;
                let isMouseOverControls = false;

                // Countdown overlay
                const countdownOverlay = document.createElement('div');
                countdownOverlay.id = 'countdownOverlay';
                countdownOverlay.classList.add('plyr__countdown-overlay');
                countdownOverlay.innerHTML = `
                    <span class="up-next-label">Up Next</span>
                    <span class="countdown-timer" id="countdown-timer-text"></span>
                `;
                playerContainer.appendChild(countdownOverlay);

                // Previous button
                if (typeof previousEpisodeUrl !== 'undefined') {
                    const prevEpisodeBtn = document.createElement('button');
                    prevEpisodeBtn.classList.add('plyr__prev-episode-btn');
                    prevEpisodeBtn.innerHTML = 'Previous';
                    controlsContainer.appendChild(prevEpisodeBtn);
                    prevEpisodeBtn.addEventListener('click', () => playEpisode(previousEpisodeUrl));
                }
                console.log(previousEpisodeUrl);
                console.log(nextEpisodeUrl);
                // Next button
                if (typeof nextEpisodeUrl !== 'undefined') {
                    const nextEpisodeBtn = document.createElement('button');
                    nextEpisodeBtn.classList.add('plyr__next-episode-btn');
                    nextEpisodeBtn.innerHTML = 'Next';
                    controlsContainer.appendChild(nextEpisodeBtn);
                    nextEpisodeBtn.addEventListener('click', () => playEpisode(nextEpisodeUrl));

                    const timerText = document.getElementById('countdown-timer-text');

                    player.on('timeupdate', () => {
                        const timeRemaining = Math.floor(player.duration - player.currentTime);

                        if (timeRemaining <= 60 && timeRemaining > 0) {
                            countdownOverlay.style.display = 'block';
                            timerText.textContent = `Next video in ${timeRemaining}s`;
                        } else {
                            countdownOverlay.style.display = 'none';
                        }
                    });

                    player.on('ended', () => {
                        playEpisode(nextEpisodeUrl);
                    });
                }

                // Create center controls wrapper
                const centerControls = document.createElement('div');
                centerControls.classList.add('plyr__center-controls');

                // Left (-10) button
                const leftSkip = document.createElement('div');
                leftSkip.classList.add('plyr__center-skip');
                leftSkip.innerHTML = `
                    <div class="skip-text"><i class="fa-solid fa-angle-double-left"></i> 10</div>
                `;

                // Right (+10) button
                const rightSkip = document.createElement('div');
                rightSkip.classList.add('plyr__center-skip');
                rightSkip.innerHTML = `
                    <div class="skip-text">10 <i class="fa-solid fa-angle-double-right"></i></div>
                `;

                centerControls.appendChild(leftSkip);
                centerControls.appendChild(rightSkip);
                playerContainer.appendChild(centerControls);

                // Function to show center controls
                function showCenterControls() {
                    // Clear existing timeout
                    if (hideTimeout) {
                        clearTimeout(hideTimeout);
                    }

                    // Show controls
                    centerControls.style.opacity = '1';
                    centerControls.style.visibility = 'visible';
                    centerControls.style.pointerEvents = 'auto';

                    // Hide after 2.5 seconds
                    hideTimeout = setTimeout(() => {
                        if (!isMouseOverControls && !isMouseOverPlayer) {
                            hideCenterControls();
                        }
                    }, 2500);
                }

                // Function to hide center controls
                function hideCenterControls() {
                    if (hideTimeout) {
                        clearTimeout(hideTimeout);
                    }
                    centerControls.style.opacity = '0';
                    centerControls.style.visibility = 'hidden';
                    centerControls.style.pointerEvents = 'none';
                }

                // Click actions for skip buttons
                leftSkip.onclick = (e) => {
                    e.stopPropagation();
                    player.currentTime = Math.max(player.currentTime - 10, 0);
                    showCenterControls();
                };

                rightSkip.onclick = (e) => {
                    e.stopPropagation();
                    player.currentTime = Math.min(player.currentTime + 10, player.duration);
                    showCenterControls();
                };

                // Mouse enter/leave for player container
                playerContainer.addEventListener('mouseenter', () => {
                    isMouseOverPlayer = true;
                    showCenterControls();
                });

                playerContainer.addEventListener('mouseleave', () => {
                    isMouseOverPlayer = false;
                    if (!isMouseOverControls) {
                        hideCenterControls();
                    }
                });

                // Mouse enter/leave for center controls
                centerControls.addEventListener('mouseenter', () => {
                    isMouseOverControls = true;
                    if (hideTimeout) {
                        clearTimeout(hideTimeout);
                    }
                    centerControls.style.opacity = '1';
                    centerControls.style.visibility = 'visible';
                    centerControls.style.pointerEvents = 'auto';
                });

                centerControls.addEventListener('mouseleave', () => {
                    isMouseOverControls = false;
                    if (!isMouseOverPlayer) {
                        hideTimeout = setTimeout(() => {
                            hideCenterControls();
                        }, 2500);
                    }
                });

                // Handle fullscreen changes
                function handleFullscreenChange() {
                    const isFullscreen = !!document.fullscreenElement ||
                        document.webkitFullscreenElement ||
                        document.mozFullScreenElement ||
                        document.msFullscreenElement;

                    if (isFullscreen) {
                        // In fullscreen - hide immediately
                        hideCenterControls();
                        // Still allow showing on hover in fullscreen
                        playerContainer.addEventListener('mousemove', fullscreenMouseMoveHandler);
                    } else {
                        // Exit fullscreen - remove handler
                        playerContainer.removeEventListener('mousemove', fullscreenMouseMoveHandler);
                        if (isMouseOverPlayer) {
                            showCenterControls();
                        } else {
                            hideCenterControls();
                        }
                    }
                }

                // Mouse move handler for fullscreen
                let fullscreenTimer = null;
                function fullscreenMouseMoveHandler() {
                    if (fullscreenTimer) {
                        clearTimeout(fullscreenTimer);
                    }
                    showCenterControls();
                    fullscreenTimer = setTimeout(() => {
                        if (!isMouseOverControls) {
                            hideCenterControls();
                        }
                    }, 2500);
                }

                // Listen for fullscreen changes
                document.addEventListener('fullscreenchange', handleFullscreenChange);
                document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
                document.addEventListener('mozfullscreenchange', handleFullscreenChange);
                document.addEventListener('MSFullscreenChange', handleFullscreenChange);

                // Plyr fullscreen events
                player.on('enterfullscreen', () => {
                    hideCenterControls();
                    playerContainer.addEventListener('mousemove', fullscreenMouseMoveHandler);
                });

                player.on('exitfullscreen', () => {
                    playerContainer.removeEventListener('mousemove', fullscreenMouseMoveHandler);
                    if (fullscreenTimer) {
                        clearTimeout(fullscreenTimer);
                    }
                    if (isMouseOverPlayer) {
                        showCenterControls();
                    } else {
                        hideCenterControls();
                    }
                });

                // Initial hide
                hideCenterControls();
            }

            function playEpisode(url) {
                window.location.href = url;
            }
        });
    </script>
@elseif ($video->video_upload_type === 'bn_stream_url' || (isset($video->episode_upload_type) && $video->episode_upload_type === 'bn_stream_url'))
    <script src="https://assets.mediadelivery.net/playerjs/player-0.1.0.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const bunnyIframe = document.querySelector('#bunnyPlayerIframe') || 
                                document.querySelector('#bunny-stream-container iframe') || 
                                document.querySelector('iframe[src*="mediadelivery.net"]');
            if (!bunnyIframe) return;

            let lastSentTime = 0;
            let hasResumed = false;
            const targetResumeTime = (typeof resumeTime !== 'undefined') ? Number(resumeTime) : 0;

            function handleTimeUpdate(seconds) {
                const currentSec = Math.floor(seconds);
                if (currentSec > 0 && currentSec !== lastSentTime) {
                    lastSentTime = currentSec;
                    if (typeof saveWatchTime === 'function') {
                        saveWatchTime(currentSec);
                    }
                }
            }

            function sendBunnyMessage(method, value, listener) {
                if (!bunnyIframe || !bunnyIframe.contentWindow) return;
                try {
                    bunnyIframe.contentWindow.postMessage(JSON.stringify({
                        context: 'player.js',
                        version: '0.0.11',
                        method: method,
                        value: value,
                        listener: listener || ('sub_' + method)
                    }), '*');
                } catch (err) {}
            }

            function registerBunnyListeners() {
                sendBunnyMessage('addEventListener', 'timeupdate', 'timeupdate_listener');
                sendBunnyMessage('addEventListener', 'ready', 'ready_listener');
                sendBunnyMessage('addEventListener', 'ended', 'ended_listener');
                sendBunnyMessage('addEventListener', 'play', 'play_listener');
            }

            function performResume() {
                if (targetResumeTime > 5 && !hasResumed) {
                    hasResumed = true;
                    if (bunnyPlayer && typeof bunnyPlayer.setCurrentTime === 'function') {
                        bunnyPlayer.setCurrentTime(targetResumeTime);
                    }
                    sendBunnyMessage('setCurrentTime', targetResumeTime);
                }
            }

            // 1. Direct postMessage listener (Reliable event stream from Bunny Stream iframe)
            window.addEventListener('message', (e) => {
                try {
                    const data = typeof e.data === 'string' ? JSON.parse(e.data) : e.data;
                    if (!data) return;

                    if (data.context === 'player.js' || data.event) {
                        if (data.event === 'ready') {
                            registerBunnyListeners();
                            performResume();
                        } else if (data.event === 'timeupdate' && data.value && typeof data.value.seconds !== 'undefined') {
                            handleTimeUpdate(data.value.seconds);
                        } else if (data.event === 'ended') {
                            if (typeof nextEpisodeUrl !== 'undefined' && nextEpisodeUrl) {
                                window.location.href = nextEpisodeUrl;
                            }
                        }
                    }
                } catch (err) {}
            });

            // 2. Player.js SDK instance (if loaded from CDN)
            let bunnyPlayer = null;
            function initPlayerJs() {
                if (typeof playerjs !== 'undefined' && bunnyIframe && !bunnyPlayer) {
                    try {
                        bunnyPlayer = new playerjs.Player(bunnyIframe);
                        bunnyPlayer.on('ready', () => {
                            performResume();
                        });
                        bunnyPlayer.on('timeupdate', (data) => {
                            if (data && typeof data.seconds !== 'undefined') {
                                handleTimeUpdate(data.seconds);
                            }
                        });
                        bunnyPlayer.on('ended', () => {
                            if (typeof nextEpisodeUrl !== 'undefined' && nextEpisodeUrl) {
                                window.location.href = nextEpisodeUrl;
                            }
                        });
                    } catch (err) {
                        console.error('Player.js initialization error:', err);
                    }
                }
            }

            initPlayerJs();
            window.addEventListener('load', initPlayerJs);

            // 3. Register listeners immediately and at intervals to ensure handshake completes
            registerBunnyListeners();
            bunnyIframe.addEventListener('load', () => {
                registerBunnyListeners();
                setTimeout(performResume, 600);
            });
            setTimeout(registerBunnyListeners, 500);
            setTimeout(registerBunnyListeners, 1500);
            setTimeout(registerBunnyListeners, 3000);
        });
    </script>
@endif

<style>
    /* Custom styling for the thumbnail preview */
    .thumbnail-preview {
        position: absolute;
        width: 150px;
        height: 100px;
        display: none;
        border-radius: 4px;
        border: 2px solid #FFFFFF;
        pointer-events: none;
        z-index: 99999;
        bottom: 100px;
        padding: 0px !important;
    }

    .plyr__prev-episode-btn,
    .plyr__next-episode-btn {
        background-color: rgba(0, 0, 0, 0.85);
        color: #fff;
        border: none;
        padding: 6px 12px;
        font-size: 12px !important;
        margin-left: 6px;
        border-radius: 4px;
        cursor: pointer;
        z-index: 9999;
        transition: background-color 0.3s;
        white-space: nowrap;
    }

    .plyr__prev-episode-btn:hover,
    .plyr__next-episode-btn:hover {
        background-color: rgba(255, 58, 31, 0.9);
    }

    /* Countdown Overlay */
    .plyr__countdown-overlay {
        display: none;
        position: absolute;
        bottom: 70px;
        right: 16px;
        background: rgba(0, 0, 0, 0.78);
        color: #fff;
        padding: 10px 16px;
        border-radius: 8px;
        z-index: 9999;
        text-align: center;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        pointer-events: none;
        min-width: 160px;
    }

    .plyr__countdown-overlay .up-next-label {
        font-size: 10px;
        opacity: 0.75;
        display: block;
        margin-bottom: 3px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .plyr__countdown-overlay .countdown-timer {
        font-size: 14px;
        font-weight: 700;
        display: block;
    }

    @media (max-width: 940px) {
        .plyr__prev-episode-btn,
        .plyr__next-episode-btn {
            padding: 5px 9px;
            font-size: 10px !important;
            margin-left: 4px;
        }

        .plyr__countdown-overlay {
            bottom: 60px;
            right: 10px;
            padding: 8px 12px;
            min-width: 130px;
        }
    }

    @media (max-width: 540px) {
        .plyr__prev-episode-btn,
        .plyr__next-episode-btn {
            padding: 4px 7px;
            font-size: 9px !important;
            margin-left: 3px;
        }

        .plyr__countdown-overlay {
            bottom: 52px;
            right: 8px;
            padding: 6px 10px;
            min-width: 110px;
        }

        .plyr__countdown-overlay .up-next-label {
            font-size: 8px;
        }

        .plyr__countdown-overlay .countdown-timer {
            font-size: 11px;
        }
    }

    /* Center Controls - Hidden by default, shows on hover */
    .plyr__center-controls {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        gap: 470px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        pointer-events: none;
        z-index: 9999;
    }

    .plyr__center-skip {
        width: 40px;
        height: 40px;
        background: rgba(0, 0, 0, 0.75);
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        color: #fff;
        backdrop-filter: blur(6px);
        pointer-events: auto;
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .plyr__center-skip:hover {
        background: rgba(255, 58, 31, 0.9);
        transform: scale(1.1);
    }

    .skip-text {
        font-size: 12px;
        font-weight: 700;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .plyr__center-controls {
            gap: 50px;
        }
        
        .plyr__center-skip {
            width: 50px;
            height: 50px;
        }
        
        .skip-text {
            font-size: 12px;
        }
    }

    @media (max-width: 540px) {
        .plyr__center-controls {
            gap: 30px;
        }
        
        .plyr__center-skip {
            width: 45px;
            height: 45px;
        }
        
        .skip-text {
            font-size: 11px;
        }
    }
</style>