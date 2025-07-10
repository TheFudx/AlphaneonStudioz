<!DOCTYPE html>
<html>
<head>
    <title>Upload Video</title>
</head>
<body>
    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form action="/upload" method="post" enctype="multipart/form-data">
        @csrf
        <label for="video">Choose a video to upload</label>
        <input type="file" name="video" id="video" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
