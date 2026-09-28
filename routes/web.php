<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\Auth\AuthOtpController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\TrailerDetailsController;
use App\Http\Controllers\comingsoonController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\KidsController;
// use App\Http\Controllers\LiveTvController;
use App\Http\Controllers\MusicPlayerController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\ShortfilmController;
use App\Http\Controllers\MoodController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\SubscriptionController;
// use App\Http\Controllers\SubscriptionTestController;
use App\Http\Controllers\UpgradeSubscriptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ContactusController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\KhlupsController;
use App\Http\Controllers\PodcastsController;
use App\Http\Controllers\SpotifyMusicController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\SportsActivityController;
use App\Http\Controllers\WebSeriesController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ComedyShowController as WebComedyShowController;
use App\Http\Controllers\CreateSubscriptionController;
use App\Http\Controllers\TicketmartController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ViewDetailsController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\AppleLoginController;
use App\Http\Controllers\FestivalCompetitionController;
use App\Http\Controllers\VideoDownloadController;
use App\Http\Controllers\API\WebLoginController;
use App\Http\Controllers\API\ComedyShowController;
use App\Http\Controllers\MusicPlayer\HomeController as MusicPlayerHomeController;
use App\Http\Controllers\MusicPlayer\MusicPlaylistController;
use App\Http\Controllers\AllOttContentController;
use App\Http\Controllers\AppPopupController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\DeviceSession;
use App\Http\Controllers\MediaUploadController;

use App\Http\Controllers\Partners\PartnerDetailsController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use Illuminate\Support\Str;
use App\Models\Video;
use App\Models\Episodes;


// Email Signature - served as Blade view so WEBSITE_URL is dynamic
Route::get('/email-signature', function () {
    return view('email-signature');
})->name('email-signature');

Route::get('/update-video-urls', function () {
    // podcast 166 done
    // trailers 208 done

    // $videos = Video::whereNotNull('video_mp4')->get();

    // foreach ($videos as $video) {

    //     // Remove playlist.m3u8 from the URL
    //     $baseUrl = str_replace('playlist.m3u8', '', $video->video_mp4);

    //     $video->update([
    //         'video_320_url'  => $baseUrl . 'play_360p.mp4',
    //         'video_480_url'  => $baseUrl . 'play_480p.mp4',
    //         'video_720_url'  => $baseUrl . 'play_720p.mp4',
    //         'video_1080_url' => $baseUrl . 'play_1080p.mp4',
    //     ]);
    // }

    // return "Updated {$videos->count()} videos successfully.";

    $videos = Episodes::whereNotNull('episode_mp4')->get();

    foreach ($videos as $video) {

        // Remove playlist.m3u8 from the URL
        $baseUrl = str_replace('playlist.m3u8', '', $video->episode_mp4);

        $video->update([
            'episode_320_url'  => $baseUrl . 'play_360p.mp4',
            'episode_480_url'  => $baseUrl . 'play_480p.mp4',
            'episode_720_url'  => $baseUrl . 'play_720p.mp4',
            'episode_1080_url' => $baseUrl . 'play_1080p.mp4',
        ]);
    }

    return "Updated {$videos->count()} videos successfully.";
});

Route::get('/update-slugs-with-episode', function () {
    $videos = Video::all();
    $updated = 0;
    $skipped = 0;
    $results = [];
    
    foreach ($videos as $video) {
        $name = $video->name;
        $slug = '';
        
        // Check if name contains pipe |
        if (strpos($name, '|') !== false) {
            // Extract text before the first pipe
            $parts = explode('|', $name);
            $titlePart = trim($parts[0]); // "Bachen Pachera"
            
            // Create slug from the title part
            $slug = \Illuminate\Support\Str::slug($titlePart);
            
            // Try to extract episode number from the full name
            preg_match('/episode[- ]?(\d+)/i', $name, $matches);
            $episodeNumber = $matches[1] ?? '';
            
            // Add episode number to slug if found
            if ($episodeNumber) {
                $slug = $slug . '-episode-' . $episodeNumber;
            }
        } else {
            // If no pipe, use full name as slug
            $slug = \Illuminate\Support\Str::slug($name);
            $skipped++;
        }
        
        // Make sure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (Video::where('slug', $slug)->where('id', '!=', $video->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        // Update the video
        $video->slug = $slug;
        $video->save();
        $updated++;
        
        $results[] = [
            'id' => $video->id,
            'old_name' => $video->name,
            'new_slug' => $slug,
            'extracted_title' => $titlePart ?? $name
        ];
    }
    
    return response()->json([
        'message' => 'Slugs updated with episode numbers',
        'total_videos' => $videos->count(),
        'updated' => $updated,
        'skipped' => $skipped,
        'results' => $results
    ]);
    
})->name('update.slugs.episode');
Route::post('/webhook/razorpay', [WebhookController::class, 'handleWebhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('userLocation',function(){
    $data = App\Models\DeviceSession::where('user_id','<>','')->get();
    foreach($data as $d){
        $geo = Http::withOptions(['verify' => false])->get("https://ipwho.is/{$d->device_ip}")->json();
        $city  = $geo['city'] ?? null;
        $state = $geo['region'] ?? null;
        App\Models\UserLocations::create([
            'user_id'=> $d->user_id ?? null,
            'country' => $geo['country'] ?? null,
            'state' => $geo['region'] ?? null,
            'city' => $geo['city'] ?? null,
            'country_code' => $geo['country_code'] ?? null,
            'region_code' => $geo['region_code'] ?? null,
            'latitude' => $geo['latitude'] ?? null,
            'longitude' => $geo['longitude'] ?? null,
            'plateform' => $d->platform ?? null,
            'device_ip' => $d->device_ip ?? null,
            'device_id' => $d->device_id ?? null,
            'device_name' => $d->device_name ?? null,
            'device_type' => $d->device_type ?? null,
        ]);
    }
    dd("DONE");
});
Route::get('/video-list', function () {
    return response()->json([
        'videos' => [
            [
                'id' => 1,
                'url' => config('app.website_url') . 'admin_panel/public/storage/videos/o_1j1g1sp0p142l1p9ahogji817oj18/240p.mp4',
                'title' => 'Nature Documentary',
                'thumbnail' => config('app.website_url') . 'admin_panel/public/storage/thumbnails/nature.jpg',
                'description' => 'A beautiful look at nature.'
            ],
            [
                'id' => 2,
                'url' => config('app.website_url') . 'admin_panel/public/images/video/o_1i7ldktjpcdlv1q19109su1svm1a.mp4',
                'title' => 'City Timelapse',
                'thumbnail' => config('app.website_url') . 'admin_panel/public/storage/thumbnails/city.jpg',
                'description' => 'Fast motion timelapse of a busy city.'
            ],
            [
                'id' => 3,
                'url' => config('app.website_url') . 'admin_panel/public/storage/videos/o_1j2chv8c51ld7r5o117u1prh17e418/240p.mp4',
                'title' => 'Wildlife Safari',
                'thumbnail' => config('app.website_url') . 'admin_panel/public/storage/thumbnails/safari.jpg',
                'description' => 'Wildlife safari tour highlights.'
            ],
        ]
    ]);
});
Route::get('/offline-video', function () {
    return view('offline-video');
});
Route::get('/offline-video-download', function () {
    return view('offline-video-downloaded');
});
Route::get('artisan', function () {
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return "config,cache,view,route - All Clear";
});
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
Route::get('/app-link', [AppPopupController::class, 'appLink'])->name('app.link');
Route::post('/app-redirect-track', [AppPopupController::class, 'trackRedirect'])->name('app.redirect.track');
//auth routes
Auth::routes();
//Reset password 03-06 Anjali
Route::post('auth/send-otp-on-mail',[ForgotPasswordController::class,'sendOtpOnMail'])->name('send.email');
Route::post('auth/verify-otp',[ForgotPasswordController::class,'verifyOtp'])->name('verify');
Route::post('auth/update-password',[ForgotPasswordController::class,'updatePassword'])->name('update.password');
Route::get('/auto-login-subscription', [WebLoginController::class, 'autoLoginAndRedirect'])->name('auto.login.subscribe');
Route::get('/ganpati-utsav-registration', [FestivalCompetitionController::class, 'registration'])->name('registration.competition');
Route::post('/store-competition-registration', [FestivalCompetitionController::class, 'storeRegistration'])->name('store.registration');
Route::get('/upload-media/{id}', [FestivalCompetitionController::class, 'uploadMedia']);
Route::post('/upload-media', [FestivalCompetitionController::class, 'storeMedia'])->name('upload.media');
Route::post('/save-watch-time', [HomeController::class, 'saveWatchTime'])->name('save.watch.time');
Route::post('/save-user-location', [HomeController::class, 'saveUserLocation'])->name('save.user.location');
Route::post('/session/clear-auto-payment', function () {
    session()->forget('auto_open_payment');
    return response()->json(['success' => true]);
})->middleware('auth')->name('session.clear.auto.payment');
//All OTT Content
// Route::group(['middleware' => ['auth']], function () {
        Route::get('all-ott-content',[AllOttContentController::class,'index'])->name('all.ott.content.index');
        Route::get('all-ott-platforms', [AllOttContentController::class, 'allPlatforms'])->name('all.ott.platforms');
        Route::post('/favorite/toggle', [AllOttContentController::class, 'toggle'])->name('favorite.toggle');
        Route::post('/continue-watching/toggle', [AllOttContentController::class, 'ContinueWatchingtoggle'])->name('continue.watching.toggle');
        Route::get('platform/{id}', [AllOttContentController::class, 'platformWise'])->name('platformWise');
        Route::get('all-ott-content/view/{type}',[AllOttContentController::class,'viewAllContent'])->name('all.ott.content.view.type');
        Route::get('/ajax/search-content', [AllOttContentController::class, 'ajaxSearch'])
                ->name('ajax.search.content');
// });
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/payment/payu/initiate', [SubscriptionController::class, 'initiate'])->name('payu.initiate');
    Route::post('/payment/payu/success',  [SubscriptionController::class, 'success'])->name('payu.success');
    Route::post('/payment/payu/webhook',  [SubscriptionController::class, 'webhook'])->name('payu.webhook');

    // Khlups Public & Interaction Routes
    Route::get('/khlups/view/{tokengen?}', [KhlupsController::class, 'index'])->name('khlups.view');
    Route::post('/khlups/like', [KhlupsController::class, 'khlupsLikes'])->name('khlups.like');
    Route::get('/khlups/{id}/comments', [KhlupsController::class, 'getKhlupsComments'])->name('khlups.comments.get');
    Route::post('/khlups/comment', [KhlupsController::class, 'khlupsComments'])->name('khlups.comment');
    Route::delete('/khlups/comment/{id}', [KhlupsController::class, 'deleteKhlupsComment'])->name('khlups.comment.delete');
    Route::post('/khlups/comment/{id}/delete', [KhlupsController::class, 'deleteKhlupsComment'])->name('khlups.comment.delete.post');
    Route::put('/khlups/comment/{id}', [KhlupsController::class, 'updateKhlupsComment'])->name('khlups.comment.update');
    Route::post('/khlups/comment/{id}/update', [KhlupsController::class, 'updateKhlupsComment'])->name('khlups.comment.update.post');
    Route::post('/khlups/comment/{id}/react', [KhlupsController::class, 'khlupsCommentReact'])->name('khlups.comment.react');
    Route::post('/khlups/share', [KhlupsController::class, 'khlupsShare'])->name('khlups.share');
    Route::post('/khlups/comment-like', [KhlupsController::class, 'khlupsCommentlike'])->name('khlups.comment.like');
    Route::get('/khlups/comment/reply', [KhlupsController::class, 'khlupsCommentReplies'])->name('khlups.comment.reply');

Route::group(['middleware' => ['device.limit']], function () {
    Route::group(['middleware' => ['mobile_auth', 'auth']], function () {
        // for detail page of podcast, trailer, etc.
        //Podcasts
        Route::get('/podcast', [PodcastsController::class, 'podcastsList'])->name('podcasts.list');
        Route::get('/marathipodcasts/list', [PodcastsController::class, 'marathipodcastsList'])->name('marathipodcasts.list');
        
        //Trailer
        Route::get('/trailers', [TrailerDetailsController::class, 'trailersList'])->name('trailers.list');
        // Route::get('/trailer/{slug}', [TrailerDetailsController::class, 'trailerDetails'])->name('trailer.details');
        //Webseries
        Route::get('/series', [WebSeriesController::class, 'webseriesList'])->name('webseries.list');
        //Sports
        Route::get('/sports/list', [comingsoonController::class, 'coming']);
        //music
        Route::get('/musics', [MusicPlayerController::class, 'index'])->name('musics.list');
        Route::get('/movies', [MoviesController::class, 'moviesList'])->name('movies.list');
        //tv shows
        Route::get('/tvshows/list', [comingsoonController::class, 'coming'])->name('tvshows.list');
        //Kids
        Route::get('/kids/list', [comingsoonController::class, 'coming'])->name('kids.list');
        //comedy shows
        Route::get('/comedy-show', [WebComedyShowController::class, 'index'])->name('comedy.show.list');
        //Shortfilms
        Route::get('/shortfilms', [ShortfilmController::class, 'index'])->name('shortfilms.list');
        //Watchlists
        Route::get('/watchlist/list', [WatchlistController::class, 'index'])->name('watchlist.list');
        //Profile
        Route::get('/profile/view', [ProfileController::class, 'index'])->name('profile.view');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
        Route::put('/profile/update', [ProfileController::class, 'update']);
        //Khlups User Management
        Route::get('/user/khlups/view/{tokengen}', [KhlupsController::class, 'viewUsersKhlups'])->name('user.khlups.view');
        Route::post('/khlup-save',[KhlupsController::class,'saveKhlup'])->name('kluph.save');
        Route::post('/khlup-delete',[KhlupsController::class,'deleteKhlup'])->name('kluph.delete');
        Route::post('/khlup-update/{id}',[KhlupsController::class,'updateKhlup'])->name('kluph.update');
        Route::get('/khlup-get/{id}',[KhlupsController::class,'getKhlup'])->name('kluph.get');
        
        
        //subscription
        Route::get('/subscription-failed', function () {
            return view('subscription.failed');
        })->name('subscription.failed');
        Route::post('/create-order', [RazorpayController::class, 'createOrder'])->name('razorpay.createOrder');
        Route::post('/payment-callback', [RazorpayController::class, 'paymentCallback'])->name('razorpay.paymentCallback');
        //test subscription
        // Route::get('/test-subscribe-and-enjoy', [SubscriptionTestController::class, 'index'])->name('test.subscribe');
        // Route::post('/test-store-transaction', [SubscriptionTestController::class, 'storeTransaction'])->name('testtransaction.store');
        //sports
        Route::get('/sports-activity', [SportsActivityController::class, 'index'])->name('sports.activity');
        Route::post('/register-arm-wrestling', [SportsActivityController::class, 'register'])->name('armwrestling.register');
        Route::get('/arm-wrestling/registration/success/{name}/{id}', [SportsActivityController::class, 'successArmReg'])->name('registration.success');
        Route::get('/arm-wrestling/registration/pdf/{id}', [SportsActivityController::class, 'downloadPdf'])->name('registration.pdf');
        Route::get('/webseries-coming-soon', [WebSeriesController::class, 'comingSoonPage'])->name('web.seriescoming');    
        // Downloads
        Route::get('/proxy-video/{externalUrl}',[VideoDownloadController::class, 'proxyVideo'])->name('video.proxy')->where('externalUrl', '.*');   
        Route::get('/downloded-videos', [VideoDownloadController::class, 'downlodedVideosTest'])->name('downloded.videos');
        Route::delete('/downloads/{downloadId}', [VideoDownloadController::class, 'deleteDownloadFromDatabase'])->name('downloads.delete');
        // Route::post('/check-thumbnails', 'VideoController@checkThumbnailsExist')->name('video.checkThumbnails');
        // Route::post('/khlup/{kluphs}/like', [LikeController::class, 'klike'])->name('videos.klike');


         Route::get('/continue-watching', [HomeController::class, 'continueWatchingList'])->name('continue-watching.list');
        Route::delete('/continue-watching/clear/{id}', [HomeController::class, 'clearContinueWatching'])->name('continue-watching.clear');
        Route::delete('/continue-watching/clear-all', [HomeController::class, 'clearAllContinueWatching'])->name('continue-watching.clear-all');

        
        Route::get('/renew-subscription', [SubscriptionController::class, 'renew'])->name('renew');
        Route::post('/store-upgrade-transaction', [UpgradeSubscriptionController::class, 'storeUpgradeTransaction'])->name('store.upgrade.transaction');
        Route::post('/video/view/{id}', [TrailerDetailsController::class, 'updateViewCount']);
        Route::post('/create-subscription-for-user', [CreateSubscriptionController::class, 'createSubscription']);
        });
       
    
    Route::get('/home/section/{type}', [HomeController::class, 'loadSection']);
    // Route::get('/video/progress/{videoName}', [HomeController::class, 'getProgress']);
    Route::get('/auth/callback', [LoginController::class, 'handleCallback'])->name('auth.callback');
    Route::get('/auth/subscription/callback', [LoginController::class, 'handleCallbackSubscription'])->name('auth.callback.subscription');
    // Auth middleware routes;
    Route::get('/view/details/{id}', [ViewDetailsController::class, 'index'])->name('view.details');
    Route::get('/webseries/view/{id}', [WebSeriesController::class, 'index'])->name('webseries.view'); // Encrypt ID before passing it to the route
    Route::get('/webseries/episode/view/{id}',[WebSeriesController::class, 'webseriesEpisodeView'])->name('webseries.episodes.view'); // Encrypt ID before passing it to the route
    
    Route::get('/series/{slug}/season-{season}/episode-{episode}', [WebSeriesController::class, 'webseriesEpisodeViewBySlug'])->name('webseries.episode.view.by.slug');
    Route::get('/comedy-show/{slug}/season-{season}/episode-{episode}', [WebComedyShowController::class, 'comedyShowEpisodeViewBySlug'])->name('comedy.show.episode.view.by.slug');
    Route::get('/comedy-show/{slug}', [WebComedyShowController::class, 'comedyShowEpisodeViewBySlug'])
    ->defaults('season', 1)
    ->defaults('episode', 1)
    ->name('comedyshow.detail');
    //search
    Route::get('/search', [SearchController::class, 'search'])->name('search');
    Route::get('/search/ajax', [SearchController::class, 'searchAjax'])->name('search.ajax');
    Route::get('/search/trending', [SearchController::class, 'getTrendingContent'])->name('search.trending');
    Route::get('/search/advanced', [SearchController::class, 'advancedSearch'])->name('search.advanced');
    //watchlist add remove
    Route::post('/watchlist-toggle', [WatchlistController::class, 'watchlistToggle'])->name('watchlist.toggle');
    Route::post('/watchlist/clear-all', [WatchlistController::class, 'clearAllWatchlist'])->name('watchlist.clear-all');
    Route::get('/watchlist/view/details/{id}/{type}', [WatchlistController::class, 'viewDetails'])->name('watchlist.view.details');
    Route::post('/watchlist/remove/view', [WatchlistController::class, 'watchlistRemove'])->name('watchlist.remove.view');

    Route::post('/watchlist', [WatchlistController::class, 'watchlistToggle'])->name('watchlist');
    Route::post('/watchlist/remove', [WatchlistController::class, 'removeFromWatchlist'])->name('watchlist.remove');
    Route::post('/clear-watchlist', [WatchlistController::class, 'clearWatchlist'])->name('watchlist.clear');
});
// Add this route for AJAX requests
Route::get('/top10/{type}', function($type) {
    $user = app('logged-in-user');
    $validTypes = ['movies', 'trailers', 'shortfilms', 'podcasts'];
    
    if (!in_array($type, $validTypes)) {
        return response()->json(['error' => 'Invalid type'], 400);
    }
    
    // Fetch top 10 based on type
    switch($type) {
        case 'movies':
            $data = \App\Models\Video::active()
                ->whereHas('category', function($q) {
                    $q->where('name', 'Movies');
                })
                ->orderByDesc('view')
                ->take(10)
                ->get();
            $viewRoute = 'view.details.slug';
            $content = 'movies';
            break;
            
        case 'trailers':
            $data = \App\Models\Video::active()
                ->whereHas('category', function($q) {
                    $q->where('name', 'Trailers');
                })
                ->orderByDesc('view')
                ->take(10)
                ->get();
            $viewRoute = 'view.details.slug';
            $content = 'trailers';
            break;
            
        case 'shortfilms':
            $data = \App\Models\Video::active()
                ->whereHas('category', function($q) {
                    $q->where('name', 'Short Films');
                })
                ->orderByDesc('view')
                ->take(10)
                ->get();
            $viewRoute = 'view.details.slug';
            $content = 'shortfilms';
            break;
            
        case 'podcasts':
            $data = \App\Models\Podcast::active()
                ->orderByDesc('view')
                ->take(10)
                ->get();
            $viewRoute = 'podcast.details';
            $content = 'podcasts';
            break;
    }
    
    // Generate HTML for the items
    $html = '';
    foreach ($data as $index => $d) {
        $rank = $index + 1;
        $watchlist = session('watchlist', []);
        
        // Build item HTML (you can extract this to a partial)
        $html .= view('partials.top10-item', [
            'd' => $d,
            'rank' => $rank,
            'viewRoute' => $viewRoute,
            'content' => $content,
            'user' => $user,
            'watchlist' => $watchlist
        ])->render();
    }
    
    return response()->json(['html' => $html]);
})->name('top10.by.type');
Route::get('/podcast/alpha-talks/{slug}', [PodcastsController::class, 'podcastDetails'])->name('podcast.details');
Route::get('/podcast/alpha-talks/marathi/{slug}', [PodcastsController::class, 'marathiPodcastDetails'])->name('marathi.podcast.details');
// Route::get('{content}/{slug}', [ViewDetailsController::class, 'slugAndContentDetails'])->name('view.details.slug');
Route::get('{content}/{slug}', [ViewDetailsController::class, 'slugAndContentDetails'])
    ->where('content', '^(?!album|podcast|mood|subscribe|playlist|search|watchlist|profile|khlups|wishlist|songs-wishlist|album-wishlist|popular-songs|popular-albums|musics-index|genres|qr-code-generation)([^/]+)$')
    ->name('view.details.slug');
Route::get('/subscribe', [SubscriptionController::class, 'index'])->name('subscribe');
        Route::post('/subscription-create', [SubscriptionController::class, 'createSubscription'])->name('subscription.create');
        Route::post('/subscription-cancel', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
        Route::post('/transaction-store', [SubscriptionController::class, 'storeTransaction'])->name('transaction.store');
        Route::get('/subscription-success', function () { return view('subscription.success'); })->name('subscription.success');

        Route::get('subscription-status', [SubscriptionController::class, 'getSubscriptionStatus'])->name('subscription.status');
        Route::get('transaction-history', [SubscriptionController::class, 'getTransactionHistory'])->name('transaction.history');

        Route::post('/razorpay/webhook', [WebhookController::class, 'handleWebhook'])->name('razorpay.webhook');

// QR Code & App Links
Route::get('/qr-code-generation/qrcode', [QrCodeController::class, 'index'])->name('qrcode.index');
Route::post('/qr-code-generation/qrcode', [QrCodeController::class, 'store'])->name('qrcode.store');
Route::delete('/qrcode/{id}', [QrCodeController::class, 'destroy'])->name('qrcode.destroy');
Route::get('/get-app', [QrCodeController::class, 'redirectToStore'])->name('get-app');
Route::get('/redirect-to-store', [QrCodeController::class, 'redirectToStore'])->name('qrcode.redirect');
Route::post('/store-app-links', [QrCodeController::class, 'storeAppLinks'])->name('qrcode.storeLinks');
Route::post('/qrcode/universal', [QrCodeController::class, 'storeUniversalQr'])->name('qrcode.storeUniversal');

//your mood - search
Route::get('/mood/{id}', [MoodController::class, 'index'])->name('mood.show');
Route::get('/mood-details/{id}/{category_id}', [MoodController::class, 'yourMoodDetails'])->name('mood.show.details');
//coming soon
Route::get('/coming-soon', [comingsoonController::class, 'coming'])->name('coming-soon');
//footer pages
Route::get('/contact-us', [ContactusController::class, 'contactUs'])->name('contact-us');
Route::post('/contact', [ContactController::class, 'submitForm'])->name('contact.submit');
Route::get('/about-us', [HomeController::class, 'aboutus'])->name('aboutus');
Route::get('/privacy-policy', [HomeController::class, 'privacypolicy'])->name('footer.privacy');
Route::get('/terms-and-conditions', [HomeController::class, 'termsandconditions'])->name('footer.terms&co');
Route::get('/refund-and-policy', [HomeController::class, 'refundandpolicy'])->name('footer.refund');
Route::get('/help-center', [HomeController::class, 'helpcenterfq'])->name('footer.help');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/debug-session', function (Request $request) {
    return response()->json($request->session()->get('show_order'));
});
Route::get('/thank-you-for-subscribe', [TransactionController::class, 'thankyou'])->name('subscribe.thankyou');
Route::get('/errorPage', function(){
    return view('errorPage');
})->name('errorPage');
  
Route::controller(AuthOtpController::class)->group(function(){
    Route::get('otp/login', 'login')->name('otp.login');
    Route::post('otp/generate', 'generate')->name('otp.generate');
    Route::get('otp/verification/{user_id}', 'verification')->name('otp.verification');
    Route::post('otp/login', 'loginWithOtp')->name('otp.getlogin');
});
Route::get('auth/custom-login', [AuthController::class, 'showCustomLoginForm'])->name('custom.login');
Route::post('auth/custom-login-submit', [AuthController::class,'handleCustomCallback'])->name('custom.login.submit');
Route::get('auth/custom-login/verify/{mobile_number}',[AuthController::class,'showCustomLoginVerifyForm'])->name('custom.login.verify');
Route::post('auth/custom-login/verify/{mobile_number}',[AuthController::class,'handleCustomLoginVerifySubmit'])->name('custom.login.verify.submit');
Route::get('/google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('auth/apple/redirect', [AppleLoginController::class, 'redirectToApple'])->name('apple.redirect');
Route::match(['get', 'post'], 'auth/apple/callback', [AppleLoginController::class, 'handleAppleCallback']);
Route::get('/reach-device-limit-test', [SubscriptionController::class, 'devicelimittest'])->name('devicelimittest');
Route::post('/device-logout/{id}', [LoginController::class, 'logoutDevice'])->name('device.logout')->middleware('preventDoubleSubmit');
Route::get('/reach-device-limit', [SubscriptionController::class, 'devicelimit'])->name('devicelimit');
Route::group(['middleware' => ['auth']], function () {
    Route::get('/musics-index',[MusicPlayerHomeController::class, 'index'])->name('musicplayer.index');
    Route::get('/popular-songs/{song?}', [MusicPlayerHomeController::class, 'popularSongs'])->name('popular.songs');
    Route::get('/popular-albums', [MusicPlayerHomeController::class, 'popularAlbums'])->name('popular.albums');
    Route::get('/genres', [MusicPlayerHomeController::class, 'genres'])->name('genres');
    Route::get('/album/{id}/songs', [MusicPlayerHomeController::class, 'albumSongs'])->name('album.songs');
    Route::get('{album_name}/song/{id}', [MusicPlayerHomeController::class, 'song'])->name('song');
    Route::get('/album/{id}/songs-list', [MusicPlayerHomeController::class, 'albumSongsList'])->name('album.songs.list');
    Route::post('/album/{id}/wishlist', [MusicPlayerHomeController::class, 'toggleAlbumWishlist'])->name('album.wishlist');
    Route::get('/album-wishlist/{userId?}', [MusicPlayerHomeController::class, 'albumWishlist'])->name('album.wishlist');
    Route::post('/album-wishlist/clear', [MusicPlayerHomeController::class, 'clearAlbumWishlist'])->name('album.wishlist.clear');
    Route::get('/songs-wishlist/{userId?}', [MusicPlayerHomeController::class, 'songWishlist'])->name('songs.wishlist');
    Route::post('/song/{id}/wishlist', [MusicPlayerHomeController::class, 'toggleSongWishlist'])->name('song.wishlist');
    Route::post('/song-wishlist/clear', [MusicPlayerHomeController::class, 'clearSongWishlist'])->name('song.wishlist.clear');
    Route::get('/liked-songs/{userId?}', [MusicPlayerHomeController::class, 'likedSongs'])->name('songs.liked');
    Route::post('/liked-songs/clear', [MusicPlayerHomeController::class, 'clearLikedSongs'])->name('songs.liked.clear');
    Route::get('/liked-albums/{userId?}', [MusicPlayerHomeController::class, 'likedAlbums'])->name('albums.liked');
    Route::post('/liked-albums/clear', [MusicPlayerHomeController::class, 'clearLikedAlbums'])->name('albums.liked.clear');
    Route::post('/song/{id}/like', [MusicPlayerHomeController::class, 'toggleSongLike'])->name('song.like');
    Route::get('/song/{id}/status', [MusicPlayerHomeController::class, 'songStatus'])->name('song.status');
    Route::post('/album/{id}/like', [MusicPlayerHomeController::class, 'toggleAlbumLike'])->name('album.like');
    Route::post('/music/{id}/play', [MusicPlayerHomeController::class, 'musicPlayCount'])->name('music.count');
    Route::get('/search-music', [MusicPlayerHomeController::class, 'searchMusic'])->name('search.music');
    
    Route::get('/songs/{id}/{type}/{playlist_name}', [MusicPlayerHomeController::class, 'songsPlaying'])->name('songs.playing');
    Route::get('/album/shuffle/{id}',[MusicPlayerHomeController::class,'shuffle']);
    Route::get('/album-click/{id}/songs', [MusicPlayerHomeController::class, 'songsByAlbum']);
    Route::get('/album/{album}/{song?}', [MusicPlayerHomeController::class, 'albumDetails'])->name('album.details');
    // Route::get('/playlist', [MusicPlaylistController::class, 'playlist'])->name('playlist');
    // Route::get('/playlist/songs/{id}', [MusicPlaylistController::class, 'playlistSongs'])->name('playlist.songs');
    Route::post('/playlist/add-song',[MusicPlaylistController::class,'addSong'])->name('playlist.add.song');
    Route::post('/playlist/create',[MusicPlaylistController::class,'createPlaylist'])->name('playlist.create');
    Route::get('/playlist/list',[MusicPlaylistController::class,'getPlaylists'])->name('playlist.list');
    Route::get('/playlist/{name}/{userId?}', [MusicPlaylistController::class,'viewPlaylist'])->name('playlist.view');
    Route::post('/playlist/update-name',[MusicPlaylistController::class,'updateName']);
    Route::post('/playlist/delete',[MusicPlaylistController::class,'delete']);
    Route::post('/playlist/update-image',[MusicPlaylistController::class,'updateImage']);
    Route::get('/playlist/shuffle/{name}/{userId?}',[MusicPlaylistController::class,'shuffle']);
    Route::get('/playlist/{name}/songs/{userId?}', [MusicPlaylistController::class,'playlistsongs']);
    Route::post('/playlist/add', [MusicPlaylistController::class, 'addToPlaylist']);
    Route::post('/playlist/remove', [MusicPlaylistController::class, 'removeFromPlaylist']);
    Route::post('/playlist/clear', [MusicPlaylistController::class, 'clearPlaylist'])->name('playlist.clear');
    Route::post('/playlist/update-details',[MusicPlaylistController::class,'updateDetails']);  
    
    Route::get('/wishlist/all-songs/{userId?}', [MusicPlayerHomeController::class,'wishlistSongs'])->name('wishlist.all.songs');
    Route::get('/wishlist/shuffle/{userId?}', [MusicPlayerHomeController::class, 'shuffleWishlistSongs'])->name('wishlist.shuffle');
    Route::get('/liked/all-songs/{userId?}', [MusicPlayerHomeController::class, 'allLikedSongs'])->name('liked.all.songs');
    Route::get('/liked/shuffle/{userId?}', [MusicPlayerHomeController::class, 'shuffleLikedSongs'])->name('liked.shuffle');
});
Route::get('/convert-hindi-to-english', [MusicPlayerHomeController::class, 'convertHindiToEnglish']);
Route::get('/convert-english-to-hindi', [MusicPlayerHomeController::class, 'convertEnglishToHindi']);
// ---------------------------------------------------------- Test -----------------------------------------------------------
//testing
Route::get('device_session',function(){
    
    $device_session = DeviceSession::get();
    foreach($device_session as $d){
        $browser = Browser::parse($d->device_name);
        $d->browser =  $browser->browserFamily();      // e.g. "Edge"
        $d->browser_version =  $browser->browserVersion();// e.g. "138.0.0.0"
        $d->platform =  $browser->platformFamily();    // e.g. "Windows"
        $d->platform_version =  $browser->platformVersion();// e.g. "10.0"
        $d->device_type =  $browser->deviceType();     // e.g. "desktop", "mobile", "tablet"
        $d->update();
    }
    dd($device_session);
    // Set a custom user agent string (device_name)
    // return [
    //     'browser' => $browser->browserFamily(),       // e.g. "Edge"
    //     'browser_version' => $browser->browserVersion(), // e.g. "138.0.0.0"
    //     'platform' => $browser->platformFamily(),     // e.g. "Windows"
    //     'platform_version' => $browser->platformVersion(), // e.g. "10.0"
    //     'device_type' => $browser->deviceType(),      // e.g. "desktop", "mobile", "tablet"
    // ];
    // phpinfo();
    // $itemToRemove = ['id'=> "18", 'type' => 'series'];
    // $watchlist = session()->get('watchlist', []);
    // foreach ($watchlist as $key => $existingItem) {
    //     if ($existingItem['id'] === $itemToRemove['id'] && $existingItem['type'] === $itemToRemove['type']) {
    //         unset($watchlist[$key]);
    //         session(['watchlist' => array_values($watchlist)]);  // Reset array keys after removal
    //         return redirect()->back()->with('success', 'Item removed from watchlist successfully.');
    //     }
    // }
});

Route::get('time',function(){
    dd(Carbon::now()->addYear()->format('Y-m-d H:i:s'));
});
Route::get('encrypt_id',function(){
    $id=231;
    dd(App\Helpers\VideoHelper::encryptID($id));
});
Route::get('thumbnail',[HomeController::class,'thumbnail_create']);
use App\Models\User;
//old recored update expiry_date
Route::get('/update-expiry-dates', [WebhookController::class, 'updateAllSubscriptionExpiries']);
// get invoicepaid - auto renew recored manuall
Route::get('/test/invoice-paid', function () {
    $user = User::find(443); // test user
    $subscriptionId = $user->razorpay_subscription_id;
    $mockRequest = new Illuminate\Http\Request([
        'event' => 'invoice.paid',
        'payload' => [
            'invoice' => [
                'entity' => [
                    'subscription_id' => $subscriptionId,
                    'amount_paid' => 1500,
                ]
            ],
            'payment' => [
                'entity' => [
                    'id' => 'pay_QiGlVORFdtLKxm'
                ]
            ]
        ]
    ]);
    app(\App\Http\Controllers\WebhookController::class)->handleInvoicePaid($mockRequest);
    return 'Simulated invoice.paid triggered.';
});
// ---------------------------------------------------------- Test -----------------------------------------------------------