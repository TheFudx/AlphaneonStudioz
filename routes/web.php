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
use App\Http\Controllers\SubscriptionTestController;
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
use App\Http\Controllers\CreateSubscriptionController;
use App\Http\Controllers\TicketmartController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ViewDetailsController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\VideoDownloadController;
use App\Http\Controllers\MusicPlayer\HomeController as MusicPlayerHomeController;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\DeviceSession;
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

Route::get('artisan', function () {
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return "config,cache,view,route - All Clear";
});
Route::group(['middleware' => ['auth']], function () {
    Route::get('/musics-index',[MusicPlayerHomeController::class, 'index'])->name('musicplayer.index');
});

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

//auth routes
Auth::routes();
//Reset password 03-06 Anjali
Route::post('auth/send-otp-on-mail',[ForgotPasswordController::class,'sendOtpOnMail'])->name('send.email');
Route::post('auth/verify-otp',[ForgotPasswordController::class,'verifyOtp'])->name('verify');
Route::post('auth/update-password',[ForgotPasswordController::class,'updatePassword'])->name('update.password');
// Auth middleware routes;
Route::group(['middleware' => ['mobile_auth', 'device.limit','auth']], function () {
    // for detail page of podcast, trailer, etc.
    Route::get('/view/details/{id}', [ViewDetailsController::class, 'index'])->name('view.details');
    //Podcasts
    Route::get('/podcasts/list', [PodcastsController::class, 'podcastsList'])->name('podcasts.list');
    //Trailer
    Route::get('/trailers/list', [TrailerDetailsController::class, 'trailersList'])->name('trailers.list');
    //Webseries
    Route::get('/webseries/list', [WebSeriesController::class, 'webseriesList'])->name('webseries.list');
    Route::get('/webseries/view/{id}', [WebSeriesController::class, 'index'])->name('webseries.view'); // Encrypt ID before passing it to the route
    Route::get('/webseries/episode/view/{id}',[WebSeriesController::class, 'webseriesEpisodeView'])->name('webseries.episodes.view'); // Encrypt ID before passing it to the route
    //Sports
    Route::get('/sports/list', [comingsoonController::class, 'coming']);
    //music
    Route::get('/musics/list', [MusicPlayerController::class, 'index'])->name('musics.list');
    Route::get('/movies/list', [MoviesController::class, 'moviesList'])->name('movies.list');
    //tv shows
    Route::get('/tvshows/list', [comingsoonController::class, 'coming'])->name('tvshows.list');
    //Kids
    Route::get('/kids/list', [comingsoonController::class, 'coming'])->name('kids.list');
    //Shortfilms
    Route::get('/shortfilms/list', [ShortfilmController::class, 'index'])->name('shortfilms.list');
    //Watchlists
    Route::get('/watchlist/list', [WatchlistController::class, 'index'])->name('watchlist.list');
    //Profile
    Route::get('/profile/view', [ProfileController::class, 'index'])->name('profile.view');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::delete('/profile/delete', [ProfileController::class, 'deleteAccount']);
    //Khlups
    Route::get('/khlups/view/{tokengen}', [KhlupsController::class, 'index'])->name('khlups.view');
    Route::get('/user/khlups/view/{tokengen}', [KhlupsController::class, 'viewUsersKhlups'])->name('user.khlups.view');
    Route::post('/khlup-save',[KhlupsController::class,'saveKhlup'])->name('kluph.save');
    Route::post('/khlup-delete',[KhlupsController::class,'deleteKhlup'])->name('kluph.delete');
    Route::post('/khlup-update/{id}',[KhlupsController::class,'updateKhlup'])->name('kluph.update');
    Route::get('/khlup-get/{id}',[KhlupsController::class,'getKhlup'])->name('kluph.get');
    //subscription
    Route::get('/subscribe', [SubscriptionController::class, 'index'])->name('subscribe');
    Route::post('/subscription-create', [SubscriptionController::class, 'createSubscription'])->name('subscription.create');
    Route::post('/subscription-cancel', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::post('/transaction-store', [SubscriptionController::class, 'storeTransaction'])->name('transaction.store');
    Route::get('/subscription-success', function () {
        return view('subscription.success');
    })->name('subscription.success');
    
    Route::get('/subscription-failed', function () {
        return view('subscription.failed');
    })->name('subscription.failed');
    Route::post('/create-order', [RazorpayController::class, 'createOrder'])->name('razorpay.createOrder');
    Route::post('/payment-callback', [RazorpayController::class, 'paymentCallback'])->name('razorpay.paymentCallback');
    //test subscription
    Route::get('/test-subscribe-and-enjoy', [SubscriptionTestController::class, 'index'])->name('test.subscribe');
    Route::post('/test-store-transaction', [SubscriptionTestController::class, 'storeTransaction'])->name('testtransaction.store');
    //sports
    Route::get('/sports-activity', [SportsActivityController::class, 'index'])->name('sports.activity');
    Route::post('/register-arm-wrestling', [SportsActivityController::class, 'register'])->name('armwrestling.register');
    Route::get('/arm-wrestling/registration/success/{name}/{id}', [SportsActivityController::class, 'successArmReg'])->name('registration.success');
    Route::get('/arm-wrestling/registration/pdf/{id}', [SportsActivityController::class, 'downloadPdf'])->name('registration.pdf');
    Route::get('/webseries-coming-soon', [WebSeriesController::class, 'comingSoonPage'])->name('web.seriescoming');
    
    // Downloads
    Route::get('/proxy-video/{externalUrl}',[VideoDownloadController::class, 'proxyVideo'])->name('video.proxy')->where('externalUrl', '.*');   
    Route::get('/downloded-videos', [VideoDownloadController::class, 'downlodedVideos'])->name('downloded.videos');
    Route::delete('/downloads/{downloadId}', [VideoDownloadController::class, 'deleteDownloadFromDatabase'])->name('downloads.delete');
    // Route::post('/check-thumbnails', 'VideoController@checkThumbnailsExist')->name('video.checkThumbnails');
    // Route::post('/khlup/{kluphs}/like', [LikeController::class, 'klike'])->name('videos.klike');

    Route::get('/renew-subscription', [SubscriptionController::class, 'renew'])->name('renew');
    Route::post('/store-upgrade-transaction', [UpgradeSubscriptionController::class, 'storeUpgradeTransaction'])->name('store.upgrade.transaction');
    Route::post('/video/view/{id}', [TrailerDetailsController::class, 'updateViewCount']);
    Route::post('/razorpay/webhook', [WebhookController::class, 'handleWebhook'])->name('razorpay.webhook');
    Route::post('/create-subscription-for-user', [CreateSubscriptionController::class, 'createSubscription']);

});
//QR code
Route::get('/qr-code-generation/qrcode', [QrCodeController::class, 'index'])->name('qrcode.index');
Route::post('/qr-code-generation/qrcode', [QrCodeController::class, 'store'])->name('qrcode.store');
Route::delete('/qrcode/{id}', [QrCodeController::class, 'destroy'])->name('qrcode.destroy');
Route::get('/redirect-to-store', [QrCodeController::class, 'redirectToStore'])->name('qrcode.redirect');
Route::post('/store-app-links', [QrCodeController::class, 'storeAppLinks'])->name('qrcode.storeLinks');
Route::post('/qrcode/universal', [QrCodeController::class, 'storeUniversalQr'])->name('qrcode.storeUniversal');

//your mood - search
Route::get('/your-mood/{id}', [MoodController::class, 'index'])->name('mood.show');
//coming soon
Route::get('/coming-soon', [comingsoonController::class, 'coming'])->name('coming-soon');
//search
Route::get('/search', [SearchController::class, 'search'])->name('search');
//wathlist add remove
Route::post('/watchlist', [WatchlistController::class, 'addToWatchlist'])->name('watchlist');
Route::post('/watchlist/remove', [WatchlistController::class, 'removeFromWatchlist'])->name('watchlist.remove');
//footer pages
Route::get('/contact-us', [ContactusController::class, 'contactUs'])->name('contact-us');
Route::post('/contact', [ContactController::class, 'submitForm'])->name('contact.submit');
Route::get('/about-us', [HomeController::class, 'aboutus'])->name('aboutus');
Route::get('/privacy-policy', [HomeController::class, 'privacypolicy'])->name('footer.privacy');
Route::get('/terms-and-conditions', [HomeController::class, 'termsandconditions'])->name('footer.terms&co');
Route::get('/refund-and-policy', [HomeController::class, 'refundandpolicy'])->name('footer.refund');
Route::get('/help-center', [HomeController::class, 'helpcenterfq'])->name('footer.help');

Route::get('/debug-session', function (Request $request) {
    return response()->json($request->session()->get('show_order'));
});
Route::get('/thank-you-for-subscribe', [TransactionController::class, 'thankyou'])->name('subscribe.thankyou');
Route::get('/errorPage', function(){
    return view('errorPage');
})->name('errorPage');

Route::get('/', [HomeController::class, 'index']);
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/video/progress/{videoName}', [HomeController::class, 'getProgress']);
Route::get('/auth/callback', [LoginController::class, 'handleCallback'])->name('auth.callback');
  
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

Route::get('/reach-device-limit-test', [SubscriptionController::class, 'devicelimittest'])->name('devicelimittest');
Route::post('/device-logout/{id}', [LoginController::class, 'logoutDevice'])->name('device.logout')->middleware('preventDoubleSubmit');
Route::get('/reach-device-limit', [SubscriptionController::class, 'devicelimit'])->name('devicelimit');
