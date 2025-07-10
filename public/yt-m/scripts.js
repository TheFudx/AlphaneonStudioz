const apiKey = 'AIzaSyDpt-QraIWcxa6udvXubFyWKHjxJ1tyWto';

function searchYouTube() {
    const query = document.getElementById('searchQuery').value;
    fetch(`https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&q=${query}&key=${apiKey}`)
        .then(response => response.json())
        .then(data => {
            const results = document.getElementById('results');
            results.innerHTML = '';
            data.items.forEach(item => {
                const videoId = item.id.videoId;
                const title = item.snippet.title;
                const thumbnail = item.snippet.thumbnails.default.url;

                const videoItem = document.createElement('div');
                videoItem.classList.add('video-item');
                videoItem.innerHTML = `
                    <img src="${thumbnail}" alt="${title}">
                    <h3>${title}</h3>
                `;
                videoItem.addEventListener('click', () => playVideo(videoId));
                results.appendChild(videoItem);
            });
        })
        .catch(error => console.error('Error fetching data:', error));
}

function playVideo(videoId) {
    const player = document.getElementById('player');
    player.innerHTML = `
        <iframe width="100%" height="400px" src="https://www.youtube.com/embed/${videoId}?&autoplay=1&muted=1" frameborder="0" allowfullscreen></iframe>
    `;
}
