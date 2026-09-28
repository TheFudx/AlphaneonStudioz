<?php

namespace App\Http\Controllers\MusicPlayer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Musics;
use App\Models\MusicWishlist;
use App\Models\Album;
use App\Models\Genre;
use App\Models\MusicAlbums;
use App\Models\MusicAlbumWishlist;
use App\Models\MusicLike;
use App\Models\AlbumLike;
use App\Models\MusicArtists;
use App\Models\MusicGeners;
use App\Helpers\SeoHelper;
use Illuminate\Support\Facades\Crypt;
use Str;
use DB;
class HomeController extends Controller
{
    //
    public function index(Request $request)
    {
        // return view('musicplayer.home');

        $music_albums = MusicAlbums::with('artists')->get();
        $album_wishlist = MusicAlbumWishlist::where('user_id', auth()->id())->pluck('album_id')->toArray();
        $album_likes = auth()->check() ? AlbumLike::where('user_id', auth()->id())->pluck('album_id')->toArray() : [];

        // Mark each album as wishlisted/liked
        foreach ($music_albums as $album) {
            $album->is_album_wishlisted = in_array($album->id, $album_wishlist);
            $album->is_album_liked = in_array($album->id, $album_likes);
        }

        $musics = Musics::with(['artists', 'albums', 'geners'])->whereNotNull('thumbnail_url')->inRandomOrder()->limit(12)->get();
        $song_wishlist = MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray();
        // Mark each album as wishlisted if it exists in session
        foreach ($musics as $song) {
            $song->is_song_wishlisted = in_array($song->id, $song_wishlist);
        }

        SeoHelper::setMeta(
            config('app.project_name') . 'Music Player - Listen to Your Favorite Tunes',
            'Discover and enjoy a vast collection of music albums across various genres. Create your wishlist and never miss out on your favorite tunes.',
            'https://alphastudioz.com/',
            'https://alphastudioz.com/images/alphastudioz-logo.png',
            url()->current()
        );
        if ($request->ajax()) {
            return response()->json([
                'html' => view('musicplayer.popular-album', compact('musics', 'music_albums'))->render(),
                'meta' => [
                    'title' => config('app.project_name') . 'Music Player - Listen to Your Favorite Tunes',
                    'description' => 'Discover and enjoy a vast collection of music albums...',
                    'url' => url()->current(),
                    'image' => 'https://alphastudioz.com/images/alphastudioz-logo.png',
                ]
            ]);
        }

        return view('musicplayer.popular-album', compact('music_albums', 'musics'));
    }
    public function popularAlbums(Request $request)
    {

        $music_albums = MusicAlbums::with('artists')->get();
        $album_wishlist = MusicAlbumWishlist::where('user_id', auth()->id())->pluck('album_id')->toArray();
        $album_likes = auth()->check() ? AlbumLike::where('user_id', auth()->id())->pluck('album_id')->toArray() : [];

        // Mark each album as wishlisted/liked
        foreach ($music_albums as $album) {
            $album->is_album_wishlisted = in_array($album->id, $album_wishlist);
            $album->is_album_liked = in_array($album->id, $album_likes);
        }

        $musics = Musics::with(['artists', 'albums', 'geners'])->whereNotNull('thumbnail_url')->inRandomOrder()->limit(12)->get();
        $song_wishlist = MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray();
        // Mark each album as wishlisted if it exists in session
        foreach ($musics as $song) {
            $song->is_song_wishlisted = in_array($song->id, $song_wishlist);
        }

        SeoHelper::setMeta(
            config('app.project_name') . 'Music Player - Listen to Your Favorite Tunes',
            'Discover and enjoy a vast collection of music albums across various genres. Create your wishlist and never miss out on your favorite tunes.',
            'https://alphastudioz.com/',
            'https://alphastudioz.com/images/alphastudioz-logo.png',
            url()->current()
        );
        if ($request->ajax()) {
            return response()->json([
                'html' => view('musicplayer.popular-album', compact('musics', 'music_albums'))->render(),
                'meta' => [
                    'title' => config('app.project_name') . 'Music Player - Listen to Your Favorite Tunes',
                    'description' => 'Discover and enjoy a vast collection of music albums...',
                    'url' => url()->current(),
                    'image' => 'https://alphastudioz.com/images/alphastudioz-logo.png',
                ]
            ]);
        }

        return view('musicplayer.popular-album', compact('music_albums', 'musics'));
    }
    public function popularSongs(Request $request, $song = null)
    {
        $perPage = 20;
        $page = $request->page ?? 1;

        $albums = MusicAlbums::with('artists')->get();

        $activeSong = null;
        if ($song) {
            if (is_numeric($song)) {
                $activeSong = Musics::with(['artists', 'albums', 'geners'])->find((int)$song);
            }
            if (!$activeSong) {
                $songSlug = Str::slug($song);
                $nameWithSpaces = str_replace('-', ' ', $song);
                $activeSong = Musics::with(['artists', 'albums', 'geners'])
                    ->where('title', $song)
                    ->orWhere('title', $nameWithSpaces)
                    ->first();
                if (!$activeSong) {
                    $allMusics = Musics::with(['artists', 'albums', 'geners'])->get();
                    foreach ($allMusics as $m) {
                        if (Str::slug($m->title) === $songSlug) {
                            $activeSong = $m;
                            break;
                        }
                    }
                }
            }
        }

        $isSingleSong = false;
        if ($activeSong) {
            $isSingleSong = true;
            $musics = collect([$activeSong]);
            $hasMore = false;
        } else {
            $musics = Musics::with(['artists', 'albums', 'geners'])
                ->orderBy('id', 'ASC')
                ->skip(($page - 1) * $perPage)
                ->take($perPage + 1) // ⭐ get one extra
                ->get();

            $hasMore = $musics->count() > $perPage;
            $musics = $musics->take($perPage);
        }

        $wishlist = MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray();
        $likes = MusicLike::where('user_id', auth()->id())->pluck('music_id')->toArray();
        foreach ($musics as $s) {
            $s->is_wishlisted = in_array($s->id, $wishlist);
            $s->is_liked = in_array($s->id, $likes);
        }
        $type = $isSingleSong ? 'single' : 'popular';
        $playlist_name = 'all';
        if ($request->ajax()) {
            return response()->json([
                'desktop' => view('musicplayer.songs._song_chunk', compact('musics','type','playlist_name'))->render(),
                'mobile' => view('musicplayer.songs._song_chunk_mobile', compact('musics','type','playlist_name'))->render(),
                'hasMore' => $hasMore, // ⭐ key
            ]);
        }

        if ($activeSong) {
            $metaTitle = $activeSong->title . ' - Popular Songs';
            $metaImage = $activeSong->thumbnail_url ?: 'https://alphastudioz.com/images/alphastudioz-logo.png';
            SeoHelper::setMeta(
                $metaTitle,
                'Listen to ' . $activeSong->title . ' on Popular Songs',
                'https://alphastudioz.com/',
                $metaImage,
                url()->current()
            );
        }

        return view('musicplayer.popular-songs', compact('musics', 'albums', 'activeSong', 'isSingleSong'));
    }
    public function albumDetails(Request $request, $album_identifier, $song_identifier = null)
    {
        $album = null;
        if (is_numeric($album_identifier)) {
            $album = MusicAlbums::with(['musics.artists', 'musics.albums', 'musics.geners'])->find((int)$album_identifier);
        }

        if (!$album) {
            $albumSlug = Str::slug($album_identifier);
            $allAlbums = MusicAlbums::with(['musics.artists', 'musics.albums', 'musics.geners'])->get();
            foreach ($allAlbums as $a) {
                if (Str::slug($a->album_name) === $albumSlug) {
                    $album = $a;
                    break;
                }
            }
        }
        if (!$album) {
            abort(404);
        }

        if (is_numeric($album_identifier) && !$request->ajax()) {
            $albumSlug = Str::slug($album->album_name);
            if ($albumSlug) {
                $targetUrl = $song_identifier ? url("/album/{$albumSlug}/{$song_identifier}") : url("/album/{$albumSlug}");
                return redirect()->to($targetUrl);
            }
        }

        $activeSong = null;
        if ($song_identifier) {
            if (is_numeric($song_identifier)) {
                $activeSong = $album->musics->firstWhere('id', (int)$song_identifier);
            }
            if (!$activeSong) {
                $songSlug = Str::slug($song_identifier);
                foreach ($album->musics as $s) {
                    if (Str::slug($s->title) === $songSlug) {
                        $activeSong = $s;
                        break;
                    }
                }
            }
        }

        $totalAlbumSongsCount = $album->musics->count();
        $isSingleSong = false;
        if ($activeSong) {
            $isSingleSong = true;
            $album->setRelation('musics', collect([$activeSong]));
        }

        $album->is_album_wishlisted = auth()->check() ? MusicAlbumWishlist::where('user_id', auth()->id())->where('album_id', $album->id)->exists() : false;
        $album->is_album_liked = auth()->check() ? AlbumLike::where('user_id', auth()->id())->where('album_id', $album->id)->exists() : false;
        $albums = MusicAlbums::get();
        $wishlist = MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray();
        $likes = MusicLike::where('user_id', auth()->id())->pluck('music_id')->toArray();
        foreach ($album->musics as $song) {
            $song->is_wishlisted = in_array($song->id, $wishlist);
            $song->is_liked = in_array($song->id, $likes);
        }

        $metaTitle = $activeSong ? ($activeSong->title . ' - ' . $album->album_name) : $album->album_name;
        $metaImage = ($activeSong && $activeSong->thumbnail_url) ? $activeSong->thumbnail_url : ($album->cover_image ?? 'https://alphastudioz.com/images/alphastudioz-logo.png');

        SeoHelper::setMeta(
            $metaTitle,
            'Discover and enjoy music from ' . $album->album_name . '. Listen to full tracks, create your wishlist, and enjoy great music.',
            'https://alphastudioz.com/',
            $metaImage,
            url()->current()
        );

        if ($request->ajax()) {
            return response()->json([
                'html' => view('musicplayer.album-details', compact('album', 'albums', 'activeSong', 'isSingleSong', 'totalAlbumSongsCount'))->render(),
                'meta' => [
                    'title' => $metaTitle,
                    'description' => 'Discover and enjoy music from ' . $album->album_name,
                    'url' => url()->current(),
                    'image' => $metaImage,
                ]
            ]);
        }

        return view('musicplayer.album-details', compact('album', 'albums', 'activeSong', 'isSingleSong', 'totalAlbumSongsCount'));
    }

    public function songsByAlbum(Request $request, $albumId)
    {
        $perPage = 20;
        $page = $request->page ?? 1;

        $musics = Musics::with(['artists', 'albums', 'geners'])
            ->where('album_id', $albumId)
            ->orderBy('id', 'ASC')
            ->skip(($page - 1) * $perPage)
            ->take($perPage + 1) // ⭐ extra record
            ->get();

        $hasMore = $musics->count() > $perPage;
        $musics = $musics->take($perPage);

        $wishlist = MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray();
        $likes = MusicLike::where('user_id', auth()->id())->pluck('music_id')->toArray();
        foreach ($musics as $song) {
            $song->is_wishlisted = in_array($song->id, $wishlist);
            $song->is_liked = in_array($song->id, $likes);
        }
        $type = 'album';
        $playlist_name = 'all';
        return response()->json([
            'desktop' => view('musicplayer.songs._song_chunk', compact('musics', 'type', 'playlist_name'))->render(),
            'mobile' => view('musicplayer.songs._song_chunk_mobile', compact('musics', 'type', 'playlist_name'))->render(),
            'hasMore' => $hasMore, // ⭐ key
        ]);
    }
    public function song(Request $request,$album_name, $id){
        $songs = Musics::where('id', $id)->with('artists', 'albums')->get();
        if ($songs->isEmpty()) {
            $songs = Musics::where('album_name', $album_name)->where('id', $id)->with('artists', 'albums')->get();
        }
        $firstSong = $songs->first();
        if ($firstSong && !$request->ajax()) {
            $albumSlug = Str::slug($firstSong->albums->album_name ?? $firstSong->album_name ?? $album_name);
            $songSlug = Str::slug($firstSong->title);
            if ($albumSlug && $songSlug) {
                return redirect()->to(url("/album/{$albumSlug}/{$songSlug}"));
            }
        }
        $metaTitle = $firstSong->title;
        $metaImage = $firstSong && $firstSong->thumbnail_url
        ? $firstSong->thumbnail_url
        : 'https://alphastudioz.com/images/alphastudioz-logo.png';

        SeoHelper::setMeta(
                $metaTitle,
                'Discover and enjoy a vast collection of music albums across various genres. Create your wishlist and never miss out on your favorite tunes.',
                'https://alphastudioz.com/',
                $metaImage,
                url()->current()
            );

        if ($request->ajax()) {
            return response()->json([
                'html' => view('musicplayer.album-songs-list', compact('songs'))->render(),
                'meta' => [
                    'title' => $metaTitle,
                    'description' => 'Discover and enjoy a vast collection of music albums across various genres. Create your wishlist and never miss out on your favorite tunes.',
                    'url' => url()->current(),
                    'image' =>  $metaImage,
                ]
            ]);
        }
        return view('musicplayer.album-songs-list', compact('songs'));
    }

    public function genres(Request $request)
    {
        $musics = MusicGeners::get();
        return view('musicplayer.popular-genres', compact('musics'));

        // if ($request->ajax()) {
        // }
        //  return view('musicplayer.index_old', compact('musics'));
    }
    public function albumSongs($id, Request $request)
    {
        $songs = Musics::where('album_id', $id)->with('artists', 'albums')->get();
        $wishlist = MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray();
        $likes = MusicLike::where('user_id', auth()->id())->pluck('music_id')->toArray();
        foreach ($songs as $song) {
            $song->is_wishlisted = in_array($song->id, $wishlist);
            $song->is_liked = in_array($song->id, $likes);
        }

        $firstSong = $songs->first();
        $metaTitle = $firstSong->albums->album_name;
        $metaImage = $firstSong && $firstSong->albums->cover_image
        ? $firstSong->albums->cover_image
        : 'https://alphastudioz.com/images/alphastudioz-logo.png';

        SeoHelper::setMeta(
            $metaTitle,
            'Discover and enjoy a vast collection of music albums across various genres. Create your wishlist and never miss out on your favorite tunes.',
            'https://alphastudioz.com/',
            $metaImage,
            url()->current()
        );
        if ($request->ajax()) {
            return response()->json([
                'html' => view('musicplayer.album-songs', compact('songs'))->render(),
                'meta' => [
                    'title' => $metaTitle,
                    'description' => 'Discover and enjoy a vast collection of music albums across various genres. Create your wishlist and never miss out on your favorite tunes.',
                    'url' => url()->current(),
                    'image' =>  $metaImage,
                ]
            ]);
        }
        return view('musicplayer.album-songs', compact('songs'));
    }
    public function songsPlaying($id,$type=null,$playlist_name=null, Request $request)
    {
       
        $currentSong = Musics::with('artists', 'albums')->findOrFail($id);
        switch ($type) {

            case 'single':
                $songs = collect([$currentSong]);
                break;

            case 'wishlist':
                $targetUserId = auth()->id();
                if ($playlist_name && $playlist_name !== 'all') {
                    $decrypted = $this->decryptUserId($playlist_name);
                    if ($decrypted) {
                        $targetUserId = $decrypted;
                    }
                }
                $wishlistIds = MusicWishlist::where('user_id', $targetUserId)->pluck('music_id')->toArray();
                $songs = Musics::whereIn('id', $wishlistIds)
                    ->with('artists', 'albums')
                    ->orderBy('id')
                    ->get();
                break;

            case 'liked':
                $targetUserId = auth()->id();
                if ($playlist_name && $playlist_name !== 'all') {
                    $decrypted = $this->decryptUserId($playlist_name);
                    if ($decrypted) {
                        $targetUserId = $decrypted;
                    }
                }
                $likedSongIds = MusicLike::where('user_id', $targetUserId)->pluck('music_id')->toArray();
                $songs = Musics::whereIn('id', $likedSongIds)
                    ->with('artists', 'albums')
                    ->orderBy('id')
                    ->get();
                break;
            case 'playlist':
            // Get playlist name from request
                $musicIds = DB::table('music_playlist')
                    ->where('user_id', auth()->id())->where('playlist_name', $playlist_name)
                    ->whereNotNull('music_id')       // skip empty placeholder rows
                    ->pluck('music_id')
                    ->toArray();

                $songs = Musics::whereIn('id', $musicIds)
                    ->with('artists', 'albums')
                    ->orderBy('id')
                    ->get();
                break;

            case 'album':
            default:
                    $songs = Musics::where('album_id', $currentSong->album_id)
                        ->with('artists', 'albums')
                        ->orderBy('id')
                        ->get();
            break;
        }
        // Get ALL songs from same album
        // $songs = Musics::where('album_id', $currentSong->album_id)
        //     ->with('artists', 'albums')
        //     ->orderBy('id')
        //     ->get();

        $wishlist = MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray();
        $likes = MusicLike::where('user_id', auth()->id())->pluck('music_id')->toArray();
        foreach ($songs as $song) {
            $song->is_wishlisted = in_array($song->id, $wishlist);
            $song->is_liked = in_array($song->id, $likes);
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('musicplayer.album-songs', compact('songs'))->render()
            ]);
        }

        return view('musicplayer.album-songs', compact('songs'));
    }
    public function albumSongsList($id, Request $request)
    {
        $songs = Musics::where('album_id', $id)->with('artists', 'albums')->get();
        $wishlist = MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray();
        $likes = MusicLike::where('user_id', auth()->id())->pluck('music_id')->toArray();
        foreach ($songs as $song) {
            $song->is_wishlisted = in_array($song->id, $wishlist);
            $song->is_liked = in_array($song->id, $likes);
        }

        $firstSong = $songs->first();
                    $metaTitle = $firstSong->albums->album_name;
                    $metaImage = $firstSong && $firstSong->albums->cover_image
                    ? $firstSong->albums->cover_image
                    : 'https://alphastudioz.com/images/alphastudioz-logo.png';

        SeoHelper::setMeta(
            $metaTitle,
            'Discover and enjoy a vast collection of music albums across various genres. Create your wishlist and never miss out on your favorite tunes.',
            'https://alphastudioz.com/',
             $metaImage,
            url()->current()
        );

        if ($request->ajax()) {
            return response()->json([
                'html' => view('musicplayer.album-songs-list', compact('songs'))->render(),
                'meta' => [
                    'title' => $metaTitle,
                    'description' => 'Discover and enjoy a vast collection of music albums across various genres. Create your wishlist and never miss out on your favorite tunes.',
                    'url' => url()->current(),
                    'image' =>  $metaImage,
                ]
            ]);
        }
        return view('musicplayer.album-songs-list', compact('songs'));
    }

    private function encryptUserId($id)
    {
        if (!$id) return null;
        try {
            return \App\Helpers\VideoHelper::encryptID((int)$id);
        } catch (\Throwable $e) {
            return (string)$id;
        }
    }

    private function decryptUserId($token)
    {
        if (!$token) return null;
        try {
            $decrypted = \App\Helpers\VideoHelper::decryptID((string)$token);
            if ($decrypted) {
                return (int)$decrypted;
            }
            if (is_numeric($token)) {
                return (int)$token;
            }
            $b64 = strtr($token, '-_', '+/');
            $padded = str_pad($b64, strlen($b64) + (4 - strlen($b64) % 4) % 4, '=');
            $decryptedCrypt = Crypt::decryptString(base64_decode($padded));
            return is_numeric($decryptedCrypt) ? (int)$decryptedCrypt : null;
        } catch (\Throwable $e) {
            if (is_numeric($token)) return (int)$token;
            return null;
        }
    }

    public function albumWishlist(Request $request, $userId = null)
    {
        $targetUserId = $this->decryptUserId($userId) ?? $request->query('user_id') ?? auth()->id();
        $encryptedUserId = $this->encryptUserId($targetUserId);
        $sharedUser = $targetUserId ? \App\Models\User::find($targetUserId) : null;
        $wishlist = MusicAlbumWishlist::where('user_id', $targetUserId)->pluck('album_id')->toArray();
        $albums = MusicAlbums::whereIn('id', $wishlist)->with('artists')->get();
        SeoHelper::setMeta(
            config('app.project_name') . 'Music Player - Listen to Your Favorite Tunes',
            'Discover and enjoy a vast collection of music albums across various genres. Create your wishlist and never miss out on your favorite tunes.',
            'https://alphastudioz.com/',
            'https://alphastudioz.com/images/alphastudioz-logo.png',
            url()->current()
        );
        $album_wishlist = auth()->check() ? MusicAlbumWishlist::where('user_id', auth()->id())->pluck('album_id')->toArray() : [];
        $album_likes = auth()->check() ? AlbumLike::where('user_id', auth()->id())->pluck('album_id')->toArray() : [];

        // Mark each album as wishlisted/liked
        foreach ($albums as $album) {
            $album->is_album_wishlisted = in_array($album->id, $album_wishlist);
            $album->is_album_liked = in_array($album->id, $album_likes);
        }
        if ($request->ajax()) {
            return response()->json([
                'html' => view('musicplayer.wishlist-album', compact('albums', 'targetUserId', 'encryptedUserId', 'sharedUser'))->render(),
                'meta' => [
                    'title' => config('app.project_name') . 'Music Player - Listen to Your Favorite Tunes',
                    'description' => 'Discover and enjoy a vast collection of music albums...',
                    'url' => url()->current(),
                    'image' => 'https://alphastudioz.com/images/alphastudioz-logo.png',
                ]
            ]);
        }
        return view('musicplayer.wishlist-album', compact('albums', 'targetUserId', 'encryptedUserId', 'sharedUser'));
    }
    public function songWishlist(Request $request, $userId = null)
    {
        $targetUserId = $this->decryptUserId($userId) ?? $request->query('user_id') ?? auth()->id();
        $encryptedUserId = $this->encryptUserId($targetUserId);
        $sharedUser = $targetUserId ? \App\Models\User::find($targetUserId) : null;
        $songWishlist = MusicWishlist::where('user_id', $targetUserId)->pluck('music_id')->toArray();
        $songs = Musics::whereIn('id', $songWishlist)->with('artists', 'albums')->get();
        SeoHelper::setMeta(
            config('app.project_name') . 'Music Player - Listen to Your Favorite Tunes',
            'Discover and enjoy a vast collection of music albums across various genres. Create your wishlist and never miss out on your favorite tunes.',
            'https://alphastudioz.com/',
            'https://alphastudioz.com/images/alphastudioz-logo.png',
            url()->current()
        );
        $wishlist = auth()->check() ? MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray() : [];
        foreach ($songs as $song) {
            $song->is_wishlisted = in_array($song->id, $wishlist);
        }
        if ($request->ajax()) {
            return response()->json([
                'html' => view('musicplayer.wishlist-song', compact('songs', 'targetUserId', 'encryptedUserId', 'sharedUser'))->render(),
                'meta' => [
                    'title' => config('app.project_name') . 'Music Player - Listen to Your Favorite Tunes',
                    'description' => 'Discover and enjoy a vast collection of music albums...',
                    'url' => url()->current(),
                    'image' => 'https://alphastudioz.com/images/alphastudioz-logo.png',
                ]
            ]);
        }
        return view('musicplayer.wishlist-song', compact('songs', 'targetUserId', 'encryptedUserId', 'sharedUser'));
    }

    public function likedSongs(Request $request, $userId = null)
    {
        $targetUserId = $this->decryptUserId($userId) ?? $request->query('user_id') ?? auth()->id();
        $encryptedUserId = $this->encryptUserId($targetUserId);
        $sharedUser = $targetUserId ? \App\Models\User::find($targetUserId) : null;
        $likedSongIds = MusicLike::where('user_id', $targetUserId)->pluck('music_id')->toArray();
        $songs = Musics::whereIn('id', $likedSongIds)->with('artists', 'albums')->get();
        SeoHelper::setMeta(
            'Liked Songs - ' . config('app.project_name') . ' Music Player',
            'Discover and enjoy your liked songs on ' . config('app.project_name') . ' Music Player.',
            'https://alphastudioz.com/',
            'https://alphastudioz.com/images/alphastudioz-logo.png',
            url()->current()
        );
        $liked = auth()->check() ? MusicLike::where('user_id', auth()->id())->pluck('music_id')->toArray() : [];
        $wishlist = auth()->check() ? MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray() : [];
        foreach ($songs as $song) {
            $song->is_liked = in_array($song->id, $liked);
            $song->is_wishlisted = in_array($song->id, $wishlist);
        }
        if ($request->ajax()) {
            return response()->json([
                'html' => view('musicplayer.liked-songs', compact('songs', 'targetUserId', 'encryptedUserId', 'sharedUser'))->render(),
                'meta' => [
                    'title' => 'Liked Songs - ' . config('app.project_name') . ' Music Player',
                    'description' => 'Discover and enjoy your liked songs on ' . config('app.project_name') . ' Music Player.',
                    'url' => url()->current(),
                    'image' => 'https://alphastudioz.com/images/alphastudioz-logo.png',
                ]
            ]);
        }
        return view('musicplayer.liked-songs', compact('songs', 'targetUserId', 'encryptedUserId', 'sharedUser'));
    }

    public function clearLikedSongs(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        MusicLike::where('user_id', $userId)->delete();

        return response()->json([
            'status' => true,
            'message' => 'All liked songs cleared successfully.'
        ]);
    }

    public function likedAlbums(Request $request, $userId = null)
    {
        $targetUserId = $this->decryptUserId($userId) ?? $request->query('user_id') ?? auth()->id();
        $encryptedUserId = $this->encryptUserId($targetUserId);
        $sharedUser = $targetUserId ? \App\Models\User::find($targetUserId) : null;
        $likedAlbumIds = AlbumLike::where('user_id', $targetUserId)->pluck('album_id')->toArray();
        $albums = MusicAlbums::whereIn('id', $likedAlbumIds)->with('artists')->get();
        SeoHelper::setMeta(
            'Liked Albums - ' . config('app.project_name') . ' Music Player',
            'Discover and enjoy your liked music albums on ' . config('app.project_name') . ' Music Player.',
            'https://alphastudioz.com/',
            'https://alphastudioz.com/images/alphastudioz-logo.png',
            url()->current()
        );
        $userLikedIds = auth()->check() ? AlbumLike::where('user_id', auth()->id())->pluck('album_id')->toArray() : [];
        $userWishlistIds = auth()->check() ? MusicAlbumWishlist::where('user_id', auth()->id())->pluck('album_id')->toArray() : [];
        foreach ($albums as $album) {
            $album->is_album_liked = in_array($album->id, $userLikedIds);
            $album->is_album_wishlisted = in_array($album->id, $userWishlistIds);
        }
        if ($request->ajax()) {
            return response()->json([
                'html' => view('musicplayer.liked-albums', compact('albums', 'targetUserId', 'encryptedUserId', 'sharedUser'))->render(),
                'meta' => [
                    'title' => 'Liked Albums - ' . config('app.project_name') . ' Music Player',
                    'description' => 'Discover and enjoy your liked music albums on ' . config('app.project_name') . ' Music Player.',
                    'url' => url()->current(),
                    'image' => 'https://alphastudioz.com/images/alphastudioz-logo.png',
                ]
            ]);
        }
        return view('musicplayer.liked-albums', compact('albums', 'targetUserId', 'encryptedUserId', 'sharedUser'));
    }

    public function clearLikedAlbums(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        AlbumLike::where('user_id', $userId)->delete();

        return response()->json([
            'status' => true,
            'message' => 'All liked albums cleared successfully.'
        ]);
    }

    public function toggleAlbumWishlist(Request $request, $id)
    {
        $wishlist = MusicAlbumWishlist::where('user_id', auth()->id())->pluck('album_id')->toArray();

        if (in_array($id, $wishlist)) {
            // If album is already in wishlist, remove it
            MusicAlbumWishlist::where('user_id', auth()->id())->where('album_id', $id)->delete();
            return response()->json(['status' => 'removed', 'message' => 'Album removed from wishlist.']);
        } else {
            // If album is not in wishlist, add it
            MusicAlbumWishlist::create(['user_id' => auth()->id(), 'album_id' => $id, 'date_time' => now()]);
            return response()->json(['status' => 'added', 'message' => 'Album added to wishlist.']);
        }
    }

    public function clearAlbumWishlist(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        MusicAlbumWishlist::where('user_id', $userId)->delete();

        return response()->json([
            'status' => true,
            'message' => 'All albums cleared from wishlist successfully.'
        ]);
    }
    public function toggleSongWishlist(Request $request, $id)
    {
        $wishlist = MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray();
        if (in_array($id, $wishlist)) {
            // If song is already in wishlist, remove it
            MusicWishlist::where('user_id', auth()->id())->where('music_id', $id)->delete();
            return response()->json(['status' => 'removed', 'message' => 'Song removed from wishlist.']);
        } else {
            // If song is not in wishlist, add it
            $song = Musics::find($id);
            MusicWishlist::create(['user_id' => auth()->id(), 'album_id' => $song->album_id, 'music_id' => $id, 'date_time' => now()]);
            return response()->json(['status' => 'added', 'message' => 'Song added to wishlist.']);
        }
    }

    public function clearSongWishlist(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        MusicWishlist::where('user_id', $userId)->delete();

        return response()->json([
            'status' => true,
            'message' => 'All songs cleared from wishlist successfully.'
        ]);
    }

    public function toggleSongLike(Request $request, $id)
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['status' => false, 'message' => 'Please login to like songs.'], 401);
        }

        $liked = MusicLike::where('user_id', $userId)->where('music_id', $id)->first();
        if ($liked) {
            $liked->delete();
            $count = MusicLike::where('music_id', $id)->count();
            return response()->json(['status' => 'unliked', 'likes_count' => $count, 'message' => 'Song unliked.']);
        } else {
            $song = Musics::find($id);
            MusicLike::create([
                'user_id' => $userId,
                'music_id' => $id,
                'album_id' => $song->album_id ?? null,
            ]);
            $count = MusicLike::where('music_id', $id)->count();
            return response()->json(['status' => 'liked', 'likes_count' => $count, 'message' => 'Song liked!']);
        }
    }

    public function songStatus($id)
    {
        $userId = auth()->id();
        $isLiked = $userId ? MusicLike::where('user_id', $userId)->where('music_id', $id)->exists() : false;
        $isWishlisted = $userId ? MusicWishlist::where('user_id', $userId)->where('music_id', $id)->exists() : false;
        return response()->json([
            'is_liked' => $isLiked,
            'is_wishlisted' => $isWishlisted,
        ]);
    }

    public function toggleAlbumLike(Request $request, $id)
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['status' => false, 'message' => 'Please login to like albums.'], 401);
        }

        $liked = AlbumLike::where('user_id', $userId)->where('album_id', $id)->first();
        if ($liked) {
            $liked->delete();
            $count = AlbumLike::where('album_id', $id)->count();
            return response()->json(['status' => 'unliked', 'likes_count' => $count, 'message' => 'Album unliked.']);
        } else {
            AlbumLike::create([
                'user_id' => $userId,
                'album_id' => $id,
            ]);
            $count = AlbumLike::where('album_id', $id)->count();
            return response()->json(['status' => 'liked', 'likes_count' => $count, 'message' => 'Album liked!']);
        }
    }

    public function musicPlayCount($id)
    {
        $music = Musics::find($id);

        if ($music) {
            $music->increment('plays');
        }
        return response()->json(['status' => 'ok']);
    }

    public function shuffle($id)
    {
        $songs = DB::table('musics')
        ->where('album_id', $id)  // filter by album, not playlist
        ->inRandomOrder()
        ->get();

        // Get wishlist from session
        $wishlist = MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray();

        // Attach is_wishlisted flag
        foreach ($songs as $song) {
            $song->is_wishlisted = in_array($song->id, $wishlist);
        }
        $type = 'album';
        $playlist_name = 'all';
        // Render views AFTER adding wishlist flag
        $desktop = view('musicplayer.songs._song_chunk', ['musics' => $songs, 'type' => $type, 'playlist_name' => $playlist_name])->render();
        $mobile  = view('musicplayer.songs._song_chunk_mobile', ['musics' => $songs, 'type' => $type, 'playlist_name' => $playlist_name])->render();

        return response()->json([
            'status'  => true,
            'desktop' => $desktop,
            'mobile'  => $mobile
        ]);
    }

    public function wishlistSongs(Request $request, $userId = null){
        $targetUserId = $this->decryptUserId($userId) ?? $request->query('user_id') ?? auth()->id();
        $wishlist = MusicWishlist::where('user_id', $targetUserId)->pluck('music_id')->toArray();
        $songs = Musics::whereIn('id', $wishlist)->with('artists', 'albums')->get();
        $userWishlist = MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray();
        $likes = MusicLike::where('user_id', auth()->id())->pluck('music_id')->toArray();
        foreach ($songs as $song) {
            $song->is_wishlisted = in_array($song->id, $userWishlist);
            $song->is_liked = in_array($song->id, $likes);
        }
        // return view('musicplayer.wishlist-song', compact('songs'));
        $html = view('musicplayer.album-songs', ['songs' => $songs])->render();
        return response()->json([
                'html' => $html,
            ]);
    }

    public function allLikedSongs(Request $request, $userId = null)
    {
        $targetUserId = $this->decryptUserId($userId) ?? $request->query('user_id') ?? auth()->id();
        $likedSongIds = MusicLike::where('user_id', $targetUserId)->pluck('music_id')->toArray();
        $songs = Musics::whereIn('id', $likedSongIds)->with('artists', 'albums')->get();
        $userWishlist = auth()->check() ? MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray() : [];
        $likes = auth()->check() ? MusicLike::where('user_id', auth()->id())->pluck('music_id')->toArray() : [];
        foreach ($songs as $song) {
            $song->is_wishlisted = in_array($song->id, $userWishlist);
            $song->is_liked = in_array($song->id, $likes);
        }
        $html = view('musicplayer.album-songs', ['songs' => $songs])->render();
        return response()->json([
            'html' => $html,
        ]);
    }

    public function shuffleLikedSongs(Request $request, $userId = null)
    {
        $targetUserId = $this->decryptUserId($userId) ?? $request->query('user_id') ?? auth()->id();
        $likedSongIds = MusicLike::where('user_id', $targetUserId)->pluck('music_id')->toArray();
        $songs = Musics::whereIn('id', $likedSongIds)->with('artists', 'albums')->inRandomOrder()->get();
        $userWishlist = auth()->check() ? MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray() : [];
        $likes = auth()->check() ? MusicLike::where('user_id', auth()->id())->pluck('music_id')->toArray() : [];
        foreach ($songs as $song) {
            $song->is_wishlisted = in_array($song->id, $userWishlist);
            $song->is_liked = in_array($song->id, $likes);
        }
        $type = 'liked';
        $playlist_name = $userId ?? 'all';
        $desktop = view('musicplayer.songs._song_chunk', ['musics' => $songs, 'type' => $type, 'playlist_name' => $playlist_name])->render();
        $mobile  = view('musicplayer.songs._song_chunk_mobile', ['musics' => $songs, 'type' => $type, 'playlist_name' => $playlist_name])->render();
        $sidebar = view('musicplayer.album-songs', ['songs' => $songs])->render();

        return response()->json([
            'status'  => true,
            'desktop' => $desktop,
            'mobile'  => $mobile,
            'html'    => $sidebar,
        ]);
    }

    public function shuffleWishlistSongs(Request $request, $userId = null)
    {
        $targetUserId = $this->decryptUserId($userId) ?? $request->query('user_id') ?? auth()->id();
        $wishlistIds = MusicWishlist::where('user_id', $targetUserId)->pluck('music_id')->toArray();
        $songs = Musics::whereIn('id', $wishlistIds)->with('artists', 'albums')->inRandomOrder()->get();
        $userWishlist = auth()->check() ? MusicWishlist::where('user_id', auth()->id())->pluck('music_id')->toArray() : [];
        $likes = auth()->check() ? MusicLike::where('user_id', auth()->id())->pluck('music_id')->toArray() : [];
        foreach ($songs as $song) {
            $song->is_wishlisted = in_array($song->id, $userWishlist);
            $song->is_liked = in_array($song->id, $likes);
        }
        $type = 'wishlist';
        $playlist_name = $userId ?? 'all';
        $desktop = view('musicplayer.songs._song_chunk', ['musics' => $songs, 'type' => $type, 'playlist_name' => $playlist_name])->render();
        $mobile  = view('musicplayer.songs._song_chunk_mobile', ['musics' => $songs, 'type' => $type, 'playlist_name' => $playlist_name])->render();
        $sidebar = view('musicplayer.album-songs', ['songs' => $songs])->render();

        return response()->json([
            'status'  => true,
            'desktop' => $desktop,
            'mobile'  => $mobile,
            'html'    => $sidebar,
        ]);
    }
    public function searchMusic(Request $request)
    {
        $query = $request->search;

        if (!$query) {
            return response()->json([]);
        }

        $songs = Musics::where('title', 'LIKE', "%{$query}%")->orwhere('album_name', 'LIKE', "%{$query}%")->limit(50)->get();

        return response()->json($songs);
    }
    public function convertHindiToEnglish()
    {
        $musics = DB::table('musics')
            ->where('album_name', 'Devotional')
            ->whereNotNull('title')
            // ->where('id', 761) // ✅ remove after testing
            ->get();

        $updated = 0;

        foreach ($musics as $music) {

            $roman = $this->romanizeHindi($music->title);

            if ($roman) {
                DB::table('musics')
                    ->where('id', $music->id)
                    ->update([
                        'title' => $roman
                    ]);

                $updated++;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Hindi → English Roman (Title Case) completed',
            'total_records' => $musics->count(),
            'updated_records' => $updated
        ]);
    }
    private function romanizeHindi($text)
    {
        $url = 'https://translate.googleapis.com/translate_a/single?' . http_build_query([
            'client' => 'gtx',
            'sl' => 'hi',   // Hindi
            'tl' => 'en',   // English
            'dt' => 'rm',   // ✅ Romanization (NOT translation)
            'q' => $text
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false, // 🔴 SSL disabled
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'Mozilla/5.0'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        // Roman text comes here
        $roman = $result[0][0][3] ?? null;

        if (!$roman) {
            return null;
        }

        // ✅ Convert to Title Case
        return $this->toTitleCase($roman);
    }
    private function toTitleCase($string)
    {
        $string = strtolower($string);
        $string = preg_replace('/\s+/', ' ', $string); // remove extra spaces

        return mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
    }

    public function convertEnglishToHindi()
    {
        $musics = DB::table('musics')
            ->where('album_name', '!=','Devotional')
            ->whereNotNull('title')
            // ->where('id', 761) // keep for testing
            ->get();

        $updated = 0;

        foreach ($musics as $music) {

            $hindi = $this->translateEnglishToHindi($music->title);

            if ($hindi) {
                DB::table('musics')
                    ->where('id', $music->id)
                    ->update([
                        'title_hindi' => $hindi
                    ]);

                $updated++;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'English → Hindi conversion completed',
            'total_records' => $musics->count(),
            'updated_records' => $updated
        ]);
    }

    private function translateEnglishToHindi($text)
    {
        $url = 'https://translate.googleapis.com/translate_a/single?' . http_build_query([
            'client' => 'gtx',
            'sl' => 'en',   // ✅ English
            'tl' => 'hi',   // ✅ Hindi
            'dt' => 't',    // ✅ Translation (NOT romanization)
            'q' => $text
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false, // 🔴 SSL disabled as requested
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'Mozilla/5.0'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        return $result[0][0][0] ?? null;
    }

    
}
