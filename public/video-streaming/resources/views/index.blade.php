<!DOCTYPE html>
<html>
<head>
    <title>Videos</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</head>
<body>
    <h1>Videos</h1>
    @foreach ($videos as $video)
        <h2>Video ID: {{ $video->id }}</h2>
        <video id="video{{ $video->id }}" width="320" height="240" controls></video>
        <script>
            var video = document.getElementById('video{{ $video->id }}');
            if (Hls.isSupported()) {
                var hls = new Hls();
                hls.loadSource('{{ Storage::url("videos/{$video->id}/playlist.m3u8") }}');
                hls.attachMedia(video);
                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    video.play();
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = '{{ Storage::url("videos/{$video->id}/playlist.m3u8") }}';
                video.addEventListener('loadedmetadata', function() {
                    video.play();
                });
            }
        </script>
    @endforeach
</body>
</html>
