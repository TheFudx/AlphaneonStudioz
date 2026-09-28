<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SubscriptionPackageController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\InAppPurchaseSuccessController;
use App\Http\Controllers\API\ProfileUpdateController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\HomeController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('/api/user', function () {
//     return app('logged-in-user');
//   })->middleware('auth:api');
// Route::post('/register-phone',[AuthController::class,'registerPhone']);
// Route::post('/login-with-session',[AuthController::class,'loginWithSession']);
// Route::post('/login-with-otp',[AuthController::class,'loginWithOtp']);
// Route::post('/login-with-google',[AuthController::class,'loginWithGoogle']);
// Route::post('/profile-logout',[AuthController::class,'profileLogout']);


Route::get('/version-list', [ApiController::class, 'versionList']);
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/create-subscription',[ApiController::class,'createSubscription']);
Route::post('/verify-subscription',[ApiController::class,'verifyRazorpayPayment']);
Route::post('/cancel-subscription',[ApiController::class,'cancelSubscription']);
Route::get('/verify-subscription-status', [ApiController::class, 'verifySubscriptionStatus']);

Route::middleware(['auth:sanctum'])->group(function () {
  Route::post('/check-session',[AuthController::class,'checkSession']);
  Route::post('/update-profile',[AuthController::class,'updateProfile']);
  Route::post('/logout',[AuthController::class,'logout']);

  Route::post('/delete-user-account', [AuthController::class, 'deleteUserAccount'])->name('deleteUserAccount');

  Route::get('subscription/status', [ApiController::class, 'getSubscriptionStatus']);
  Route::get('admin/subscriptions', [ApiController::class, 'getAllSubscriptions']);
  Route::post('admin/subscription/{subscriptionId}/cancel', [ApiController::class, 'adminCancelSubscription']);

  Route::get('/get-user-profile', [AuthController::class, 'getUserProfile'])->name('getUserProfile');
  Route::post('/change-password',[AuthController::class,'changePassword']);
  
  Route::get('/get-playlist', [ApiController::class, 'getPlaylists'])->name('getPlaylists');
  Route::post('/add-playlist',[ApiController::class,'addPlaylist']);
  Route::post('/add-song-playlist',[ApiController::class,'addSongToPlaylist']);
  Route::post('/remove-song-playlist',[ApiController::class,'removeSongFromPlaylist']);
  Route::post('/delete-playlist',[ApiController::class,'deletePlaylist']);
  Route::post('/update-playlist',[ApiController::class,'updatePlaylistDetails']);
  Route::post('/clear-playlist',[ApiController::class,'clearallsongfromplaylist']);

  Route::get('all-music-album-wishlist',[ApiController::class, 'musicAlbumWishlistList']);
  Route::get('all-music-song-wishlist',[ApiController::class, 'musicWishlistList']);
  Route::post('music-album-wishlist',[ApiController::class, 'toggleAlbumWishlist']);
  Route::post('music-wishlist',[ApiController::class, 'toggleMusicWishlist']);
  Route::post('clear-music-album-wishlist',[ApiController::class, 'clearAlbumWishlist']);
  Route::post('clear-music-wishlist',[ApiController::class, 'clearMusicWishlist']);

  
  });
  Route::get('/all-packages', [SubscriptionPackageController::class, 'packageData'])->name('packageData');
  Route::post('/device-logout',[AuthController::class,'logoutDevice']);
Route::post('/send-otp-on-mail',[ForgotPasswordController::class,'sendOtpOnMail']);
Route::post('/verify-otp',[ForgotPasswordController::class,'verifyOtp']);
Route::post('/update-password',[ForgotPasswordController::class,'updatePassword']);

Route::get('/all-movies-data',[ApiController::class,'getAllMovies']);
Route::get('/all-series-data',[ApiController::class,'getSeries']);
Route::get('/all-home-page-data',[ApiController::class,'homePage']);
Route::post('/view-all-categorywise',[ApiController::class,'viewAllCategoryWise']);
Route::post('/view-data',[ApiController::class,'viewData']);
Route::post('/watch-time/save', [ApiController::class, 'saveWatchTime']);
Route::post('/watch-time/batch-save', [ApiController::class, 'batchSaveWatchTime']);
Route::get('/watch-time/progress', [ApiController::class, 'getWatchProgress']);
Route::get('/continue-watching', [ApiController::class, 'continueWatching']);
Route::get('/continue-watching/counts', [ApiController::class, 'continueWatchingCounts']);
Route::delete('/continue-watching/{id}', [ApiController::class, 'clearContinueWatching']);
Route::delete('/continue-watching/clear/all', [ApiController::class, 'clearAllContinueWatching']);
Route::get('/category-list',[ApiController::class,'categoryList']);
Route::get('/type-list',[ApiController::class,'typeList']);
Route::get('/search-list',[ApiController::class,'searchList']);
Route::get('/most-trending',[ApiController::class,'mostTrending']);
Route::post('/top-ten-contents',[ApiController::class,'topTenContents']);
Route::post('/mood-wise-category-data',[ApiController::class,'moodWiseCategoryData']);
Route::get('/notification-list',[ApiController::class,'allNotifications']);
Route::get('/redirect-web-subscription', [ApiController::class, 'redirectWebSubscription'])
->name('redirectWebSubscription')->middleware('web');

// old routes
Route::post('/in-app-purchase-success', [InAppPurchaseSuccessController::class, 'success'])->name('inApp.success');
Route::post('/verify-ios-receipt', [InAppPurchaseSuccessController::class, 'verifyIOSReceipt']);
Route::post('/ios-webhook', [InAppPurchaseSuccessController::class, 'handleIOSWebhook']);

Route::post('/send-firebase', [HomeController::class, 'sendFirebaseNotification']);
Route::post('/save-token', [HomeController::class, 'saveToken']);
Route::post('/save-token-api', [HomeController::class, 'saveTokenAPI']);

Route::get('music-album-list',[ApiController::class, 'musicAlbumList']);
Route::get('music-list-new',[ApiController::class, 'musicsListNew']);
Route::get('musics-list',[ApiController::class, 'musicsList']);


Route::post('khlups-likes',[ApiController::class, 'khlupsLikes']);
Route::post('khlups-comments',[ApiController::class, 'khlupsComments']);

Route::get('watchlist',[ApiController::class, 'watchlist']);
Route::post('toggle-watchlist', [ApiController::class, 'toggleWatchlist']);
Route::post('remove-watchlist',[ApiController::class, 'removeFromWatchlist']);
Route::post('watchlist-details',[ApiController::class, 'watchlistDetails']);

Route::get('all-ott-platforms',[ApiController::class, 'AllOttPlatforms']);
Route::get('all-ott-content',[ApiController::class, 'AllOttContentLists']);

use App\Http\Controllers\API\RazorpaySubscriptionController;
use App\Http\Controllers\API\RazorpayStatusController;

Route::prefix('razorpay/subscriptions')->group(function () {
    // IMPORTANT: Put specific routes BEFORE the {status} parameter route
    
    // Get all subscriptions grouped by status
    Route::get('/all', [RazorpaySubscriptionController::class, 'getAllSubscriptions']);
    
    // Get summary counts for all statuses
    Route::get('/summary', [RazorpaySubscriptionController::class, 'getSubscriptionSummary']);
    
    // Get all subscription IDs
    Route::get('/all-ids', [RazorpaySubscriptionController::class, 'getAllSubscriptionIds']);
    
    // Compare Razorpay vs Database
    Route::get('/compare', [RazorpaySubscriptionController::class, 'compareSubscriptions']);
    
    // Get subscription IDs by specific status (MUST be last)
    Route::get('/ids/{status}', [RazorpaySubscriptionController::class, 'getSubscriptionIdsByStatus']);
    
    // Get subscriptions by specific status (MUST be last)
    Route::get('/status/{status}', [RazorpaySubscriptionController::class, 'getSubscriptionsByStatus']);

    // Extended subscription endpoints with date-range support
    Route::get('/summary-with-dates',  [RazorpayStatusController::class, 'subscriptionSummaryWithDates']);
    Route::get('/grouped-detail',      [RazorpayStatusController::class, 'subscriptionsGroupedDetail']);

    // Status-wise subscriptions enriched with local user details
    Route::get('/status-wise-users',   [RazorpayStatusController::class, 'subscriptionStatusWiseUsers']);
});

// ─── Payments status-wise ────────────────────────────────────────────────────
Route::prefix('razorpay/payments')->group(function () {
    Route::get('/summary',         [RazorpayStatusController::class, 'paymentSummary']);
    Route::get('/grouped',         [RazorpayStatusController::class, 'paymentsGrouped']);
    Route::get('/status/{status}', [RazorpayStatusController::class, 'paymentsByStatus']);
});

// ─── Orders status-wise ──────────────────────────────────────────────────────
Route::prefix('razorpay/orders')->group(function () {
    Route::get('/summary',         [RazorpayStatusController::class, 'orderSummary']);
    Route::get('/grouped',         [RazorpayStatusController::class, 'ordersGrouped']);
    Route::get('/status/{status}', [RazorpayStatusController::class, 'ordersByStatus']);
});

// ─── Refunds status-wise ─────────────────────────────────────────────────────
Route::prefix('razorpay/refunds')->group(function () {
    Route::get('/summary',         [RazorpayStatusController::class, 'refundSummary']);
    Route::get('/grouped',         [RazorpayStatusController::class, 'refundsGrouped']);
    Route::get('/status/{status}', [RazorpayStatusController::class, 'refundsByStatus']);
});