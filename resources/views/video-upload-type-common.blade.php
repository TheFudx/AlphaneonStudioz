@if ($video->video_upload_type === 'transcoded')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const video = document.getElementById("transcodedplayer");
            const source =
                'https://alphastudioz.in/admin_panel/public/storage/videos/{{ $video->transcoded_video_path }}/master.m3u8';
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
                hls.on(Hls.Events.MANIFEST_PARSED, function(event, data) {
                    const availableQualities = hls.levels.map((l) => l.height);
                    availableQualities.unshift(0); // Add 'Auto' option
                    defaultOptions.quality = {
                        default: 0, // Default to 'Auto'
                        options: availableQualities,
                        forced: true,
                        onChange: (newQuality) => updateQuality(newQuality),
                    };
                    defaultOptions.i18n = {
                        qualityLabel: {
                            0: 'Auto',
                        },
                    };
                    hls.on(Hls.Events.LEVEL_SWITCHED, function(event, data) {
                        const autoLabel = document.querySelector(
                            ".plyr__menu__container [data-plyr='quality'][value='0'] span"
                        );
                        if (hls.autoLevelEnabled) {
                            autoLabel.innerHTML = `AUTO (${hls.levels[data.level].height}p)`;
                        } else {
                            autoLabel.innerHTML = `AUTO`;
                        }
                    });
                    const player = new Plyr(video, defaultOptions);
                    player.on('loadedmetadata', () => {
                        const totalSegments = Math.ceil(player.duration / thumbnailInterval);
                        addThumbnailPreview(player, totalSegments);
                    });
                });
                hls.attachMedia(video);
                window.hls = hls;
            }

            function updateQuality(newQuality) {
                if (newQuality === 0) {
                    // Enable AUTO quality
                    const autoQualityIndex = window.hls.levels.findIndex((level) => level.height === 480);
                    window.hls.currentLevel = autoQualityIndex !== -1 ? autoQualityIndex : -
                        1; // Default to 480p if available
                } else {
                    // Set the specific quality
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
                thumbnailPreview.src = `{{ $video->landscape_url }}`; // Initial placeholder
                controlsContainer.appendChild(thumbnailPreview);
                const progressBar = player.elements.progress;
                progressBar.addEventListener('mousemove', (e) => {
                    const rect = progressBar.getBoundingClientRect();
                    const offsetX = e.clientX - rect.left;
                    const hoverTime = (offsetX / progressBar.offsetWidth) * player.duration;
                    const segmentIndex = Math.floor(hoverTime / thumbnailInterval);
                    const thumbnailUrl =
                        `https://alphastudioz.in/admin_panel/public/storage/videos/{{ $video->transcoded_video_path }}/thumbnails/480p_thumbnail_${String(segmentIndex).padStart(3, '0')}.jpg`;
                    thumbnailPreview.src = thumbnailUrl;
                    const isFullscreen = !!document.fullscreenElement;
                    thumbnailPreview.style.left = `${e.pageX - (isFullscreen ? 70 : 70)}px`;
                    thumbnailPreview.style.display = 'block';
                });
                progressBar.addEventListener('mouseleave', () => {
                    thumbnailPreview.style.display = 'none';
                });
            }
        });
    </script>
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
    </style>
@else
@endif
