<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('admin/artisan', function () {

    Artisan::call('config:clear');
    // Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return "All Clear";
});

// Login-Logout
Route::get('admin/login', 'Auth\AdminController@getLogin')->name('adminLogin');
Route::post('login', 'Auth\AdminController@postLogin')->name('adminLoginPost');
Route::get('admin/logout', 'Auth\AdminController@logout')->name('adminLogout');

// Pages
Route::get('pages/{id}', 'Admin\AdminController@Page');

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'authadmin'], function () {

    Route::group(['middleware' => 'checkadmin'], function () {

        // Type
        Route::post('type/save', 'TypeController@save')->name('typeSave');
        Route::post('type/update', 'TypeController@update')->name('typeUpdate');
        Route::get('type/delete/{id}', 'TypeController@delete')->name('deleteType');
        // Category
        Route::post('category/save', 'CategoryController@save')->name('categorySave');
        Route::post('category/update', 'CategoryController@update')->name('categoryUpdate');
        Route::get('category/delete/{id}', 'CategoryController@delete')->name('deleteCategory');
        // Avatar
        Route::post('avatar/save', 'AvatarController@save')->name('AvatarSave');
        Route::post('avatar/update', 'AvatarController@update')->name('AvatarUpdate');
        Route::get('avatar/delete/{id}', 'AvatarController@delete')->name('deleteAvatar');
        // Language
        Route::post('language/save', 'LanguageController@save')->name('languageSave');
        Route::post('language/update', 'LanguageController@update')->name('languageUpdate');
        Route::get('language/delete/{id}', 'LanguageController@delete')->name('deleteLanguage');
        // Session
        Route::post('session/save', 'SessionController@save')->name('sessionSave');
        Route::post('session/update', 'SessionController@update')->name('sessionUpdate');
        Route::get('session/delete/{id}', 'SessionController@delete')->name('deleteSession');
        // Page
        Route::post('page/update', 'PageController@update')->name('PageUpdate');
        // Channel
        Route::post('channel/save', 'ChannelController@save')->name('channelSave');
        Route::post('channel/update', 'ChannelController@update')->name('channelUpdate');
        Route::get('channel/delete/{id}', 'ChannelController@delete')->name('deleteChannel');
        // Channel Banner
        Route::post('Channel-Banner/save', 'ChannelBannerlController@save')->name('ChannelBannerSave');
        Route::post('Channel-Banner/update', 'ChannelBannerlController@update')->name('ChannelBannerUpdate');
        Route::get('Channel-Banner/delete/{id}', 'ChannelBannerlController@delete')->name('deleteChannelBanner');
        // User
        Route::post('user/save', 'UserController@save')->name('userSave');
        Route::post('user/update', 'UserController@update')->name('userUpdate');
        Route::get('user/delete/{id}', 'UserController@delete')->name('deleteUser');
        // Cast
        Route::post('cast/save', 'CastController@save')->name('castSave');
        Route::post('cast/update', 'CastController@update')->name('castUpdate');
        Route::get('cast/delete/{id}', 'CastController@delete')->name('deleteCast');
        
        // Web Series
        Route::post('series/save', 'SeriesController@save')->name('seriesSave');
        Route::post('series/update', 'SeriesController@update')->name('seriesUpdate');
        Route::get('series/delete/{id}', 'SeriesController@delete')->name('deleteSeries');

          // Web Season
          Route::post('season/save', 'SeasonController@save')->name('seasonSave');
          Route::post('season/update', 'SeasonController@update')->name('seasonUpdate');
          Route::get('season/delete/{id}', 'SeasonController@delete')->name('deleteSeason');
          
         // Web Series
         Route::post('arm-wrestling/save', 'SportsController@save')->name('ArmWrestlingSave');
         Route::post('arm-wrestling/update', 'SportsController@update')->name('ArmWrestlingUpdate');
         Route::get('arm-wrestling/delete/{id}', 'SportsController@delete')->name('deleteArmWrestling');


         // Episodes
         Route::post('episodes/save', 'EpisodesController@save')->name('episodesSave');
         Route::post('episodes/update', 'EpisodesController@update')->name('episodeUpdate');
         Route::get('episodes/delete/{id}', 'EpisodesController@delete')->name('deleteepisode');
 

        // Video
        Route::post('video/save', 'VideoController@save')->name('videoSave');
        Route::post('video/update', 'VideoController@update')->name('videoUpdate');
        Route::get('video/delete/{id}', 'VideoController@delete')->name('deleteVideo');

       
        // Chunk route
        Route::any('saveChunk', 'VideoController@saveChunk');
        // TV Show
        Route::post('TVShow/save', 'TVShowController@save')->name('TVShowSave');
        Route::post('TVShow/update', 'TVShowController@update')->name('TVShowUpdate');
        Route::get('TVShow/delete/{id}', 'TVShowController@delete')->name('deleteTVShow');
        // TV Show Video
        Route::post('TVShow/videos/save', 'TVShowController@TVShowvideosave')->name('TVShow_video_Save');
        Route::post('TVShow/video/update/{show_id}/{id}', 'TVShowController@TVShowvideoupdate')->name('TVShow_videoUpdate');
        Route::get('TVShow/video/delete/{id}', 'TVShowController@TVShowvideodelete')->name('deleteTVShow_video');
        // Rent
        Route::post('rent/save', 'RentVideoController@save')->name('RentVideoSave');
        Route::post('rent/update', 'RentVideoController@update')->name('RentVideoUpdate');
        Route::get('rent/delete/{id}', 'RentVideoController@delete')->name('deleteRentVideo');
        // Packages (plan Subscription)
        Route::post('packages/save', 'PackageController@save')->name('packageSave');
        Route::post('packages/update', 'PackageController@update')->name('packageUpdate');
        Route::get('packages/delete/{id}', 'PackageController@delete')->name('deletePackage');
        // Payment
        Route::get('payment/edit/{id}', 'PaymentOptionController@edit')->name('editPayment');
        Route::post('payment/update', 'PaymentOptionController@update')->name('PaymentUpdate');
        // Notification
        Route::post('notification/save', 'NotificationController@save')->name('notificationSave');
        Route::get('notification/setting', 'NotificationController@setting')->name('notificationSetting');
        Route::post('notification/setting/add', 'NotificationController@settingsave')->name('notificationSettingsave');
        Route::get('notification/delete/{id}', 'NotificationController@delete')->name('deleteNotification');
        // Coupons
        Route::post('coupon/save', 'CouponController@save')->name('couponSave');
        Route::post('coupon/random/save', 'CouponController@randomSave')->name('couponRandomSave');
        Route::post('coupon/update', 'CouponController@update')->name('couponUpdate');
        Route::get('coupon/delete/{id}', 'CouponController@delete')->name('deleteCoupon');
        // Upcoming Video
        Route::post('upcomingvideo/save', 'UpcomingVideoController@save')->name('upcomingvideoSave');
        Route::post('upcomingvideo/update', 'UpcomingVideoController@update')->name('upcomingvideoUpdate');
        Route::get('upcomingvideo/delete/{id}', 'UpcomingVideoController@delete')->name('upcomingvideoDelete');
        // Upcoming TV Show
        Route::post('upcomingtvshow/save', 'UpcomingTVShowController@save')->name('upcomingtvshowSave');
        Route::post('upcomingtvshow/update', 'UpcomingTVShowController@update')->name('upcomingtvshowUpdate');
        Route::get('upcomingtvshow/delete/{id}', 'UpcomingTVShowController@delete')->name('upcomingtvshowDelete');
        // Upcoming TV Show Video
        Route::post('upcomingtvshow/videos/save', 'UpcomingTVShowController@TVShowvideosave')->name('upcomingtvshowvideoSave');
        Route::post('upcomingtvshow/video/update/{show_id}/{id}', 'UpcomingTVShowController@TVShowvideoupdate')->name('upcomingtvshowvideoUpdate');
        Route::get('upcomingtvshow/video/delete/{id}', 'UpcomingTVShowController@TVShowvideodelete')->name('upcomingtvshowvideoDelete');

        Route::get('/upload-video', 'VideoController@create')->name('video.upload');
        Route::post('/upload-video-transcode', 'VideoController@store')->name('video.store');
        Route::post('/edit-upload-video', 'VideoController@editStore')->name('video.editstore'); 
        
        Route::post('/generate-thumbnails', 'VideoController@generateThumbnails')->name('video.generateThumbnails');
        Route::post('/check-thumbnails', 'VideoController@checkThumbnailsExist')->name('video.checkThumbnails');
    
        Route::post('/video/transcode/status', 'VideoController@checkTranscodeStatus')->name('video.checkTranscodeStatus');
        Route::post('/video/delete', 'VideoController@deleteVideo')->name('video.delete');
        Route::post('/continue-transcoding', 'VideoController@continueTranscoding')->name('video.resumetranscode');
        Route::post('/uploaded-vide-transcoding', 'VideoController@storedvideotranscode')->name('video.storedvideotranscode');


        
    });

    // Dashboard
    Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');

  


        
    // Type
    Route::get('type', 'TypeController@index')->name('type');
    Route::get('type/data', 'TypeController@data')->name('typeData');
    Route::get('type/add', 'TypeController@add')->name('typeAdd');
    Route::get('type/edit/{id}', 'TypeController@edit')->name('editType');
    // Category
    Route::get('category', 'CategoryController@index')->name('category');
    Route::get('category/data', 'CategoryController@data')->name('categoryData');
    Route::get('category/add', 'CategoryController@add')->name('categoryAdd');
    Route::get('category/edit/{id}', 'CategoryController@edit')->name('editCategory');
    // Avatar
    Route::get('avatar', 'AvatarController@index')->name('Avatar');
    Route::get('avatar/data', 'AvatarController@data')->name('AvatarData');
    Route::get('avatar/add', 'AvatarController@add')->name('AvatarAdd');
    Route::get('avatar/edit/{id}', 'AvatarController@edit')->name('editAvatar');
    // Language
    Route::get('language', 'LanguageController@index')->name('language');
    Route::get('language/data', 'LanguageController@data')->name('languageData');
    Route::get('language/add', 'LanguageController@add')->name('languageAdd');
    Route::get('language/edit/{id}', 'LanguageController@edit')->name('editLanguage');
    // Session
    Route::get('session', 'SessionController@index')->name('session');
    Route::get('session/data', 'SessionController@data')->name('sessionData');
    Route::get('session/add', 'SessionController@add')->name('sessionAdd');
    Route::get('session/edit/{id}', 'SessionController@edit')->name('editSession');
    // Page
    Route::get('page', 'PageController@index')->name('Page');
    Route::get('page/data', 'PageController@data')->name('PageData');
    Route::get('page/edit/{id}', 'PageController@edit')->name('editPage');
    // Video Banner
    Route::get('banner', 'BannerController@index')->name('Banner');
    Route::post('banner/typebyvideo', 'BannerController@TypeByVideo')->name('BannerTypeByVideo');
    Route::post('banner/list', 'BannerController@BannerList')->name('BannerList');
    Route::post('banner/save', 'BannerController@save')->name('BannerSave');
    Route::post('banner/delete/{id}', 'BannerController@delete')->name('deleteBanner');
    // Video Section
    Route::get('VideoSection', 'VideoSectionController@index')->name('VideoSection');
    Route::post('VideoSection/data', 'VideoSectionController@GetSectionData')->name('GetSectionData');
    Route::get('VideoSection/get_lang_or_cat', 'VideoSectionController@get_all_data')->name('GetLangOrCat');
    Route::post('VideoSection/save', 'VideoSectionController@save')->name('VideoSectionSave');
    Route::post('VideoSection/edit', 'VideoSectionController@edit')->name('VideoSectionUpdate');
    Route::post('VideoSection/update', 'VideoSectionController@update')->name('VideoSectionUpdate1');
    Route::get('VideoSection/delete/{id}', 'VideoSectionController@delete')->name('deleteVideoSection');
    // Static Banners
    Route::get('static-banners', 'StaticBannerController@index')->name('staticbanner');
    Route::get('static-banner/data', 'StaticBannerController@data')->name('staticBannerData');
    Route::get('static-banner/add', 'StaticBannerController@add')->name('staticBannerAdd');
    Route::get('static/edit/{id}', 'StaticBannerController@edit')->name('editStaticBanner');
    // Channel
    Route::get('channel', 'ChannelController@index')->name('channel');
    Route::get('channel/data', 'ChannelController@data')->name('channelData');
    Route::get('channel/add', 'ChannelController@add')->name('channelAdd');
    Route::get('channel/edit/{id}', 'ChannelController@edit')->name('editChannel');
    // Channel Banner
    Route::get('Channel-Banner', 'ChannelBannerlController@index')->name('ChannelBanner');
    Route::get('Channel-Banner/data', 'ChannelBannerlController@data')->name('ChannelBannerData');
    Route::get('Channel-Banner/add', 'ChannelBannerlController@add')->name('ChannelBannerAdd');
    Route::get('Channel-Banner/edit/{id}', 'ChannelBannerlController@edit')->name('editChannelBanner');
    // Channel Section
    Route::get('ChannelSection', 'ChannelSectionController@index')->name('ChannelSection');
    Route::post('ChannelSection/save', 'ChannelSectionController@save')->name('ChannelSectionSave');
    Route::post('ChannelSection/update', 'ChannelSectionController@update')->name('ChannelSectionUpdate');
    Route::post('ChannelSection/update1', 'ChannelSectionController@update1')->name('ChannelSectionUpdate1');
    Route::get('ChannelSection/delete/{id}', 'ChannelSectionController@delete')->name('deleteChannelSection');
    Route::get('ChannelSection/get_lang_or_cat', 'ChannelSectionController@get_lang_or_cat')->name('ChannelGetLangOrCat');
    Route::post('ChannelSection/data', 'ChannelSectionController@GetSectionData')->name('ChannelGetSectionData');
    // User
    Route::get('user', 'UserController@index')->name('user');
    Route::get('user/data', 'UserController@data')->name('userData');
    Route::get('user/add', 'UserController@add')->name('userAdd');
    Route::get('user/edit/{id}', 'UserController@edit')->name('editUser');
    // Cast
    Route::get('cast', 'CastController@index')->name('cast');
    Route::get('cast/data', 'CastController@data')->name('castData');
    Route::get('cast/add', 'CastController@add')->name('castAdd');
    Route::get('cast/edit/{id}', 'CastController@edit')->name('editCast');
    
     // Series
    Route::get('series', 'SeriesController@index')->name('series');
    Route::get('series/data', 'SeriesController@data')->name('seriesData');
    Route::get('series/add', 'SeriesController@add')->name('seriesAdd');
    Route::get('series/edit/{id}', 'SeriesController@edit')->name('editSeries');
     // Season
     Route::get('season', 'SeasonController@index')->name('season');
     Route::get('season/data', 'SeasonController@data')->name('seasonData');
     Route::get('season/add', 'SeasonController@add')->name('seasonAdd');
     Route::get('season/edit/{id}', 'SeasonController@edit')->name('editSeason');

     // Episodes
     Route::get('episodes', 'EpisodesController@index')->name('episodes');
     Route::get('episodes/data', 'EpisodesController@data')->name('episodeData');
     Route::get('episodes/add', 'EpisodesController@add')->name('episodeAdd');
     Route::get('episodes/edit/{id}', 'EpisodesController@edit')->name('editepisode');
     Route::get('episodes/details/{id}', 'EpisodesController@detail')->name('episodeDetail');
 
      // Sports
    Route::get('arm-wrestling', 'SportsController@index')->name('ArmWrestling');
    Route::get('arm-wrestling/data', 'SportsController@data')->name('ArmWrestlingData');
    Route::get('arm-wrestling/add', 'SportsController@add')->name('ArmWrestlingAdd');
    Route::get('arm-wrestling/edit/{id}', 'SportsController@edit')->name('editArmWrestling');

    // Video
    Route::get('video', 'VideoController@index')->name('video');
    Route::get('video/data', 'VideoController@data')->name('videoData');
    Route::get('video/add', 'VideoController@add')->name('videoAdd');
    Route::get('video/edit/{id}', 'VideoController@edit')->name('editVideo');
    Route::get('video/details/{id}', 'VideoController@detail')->name('videoDetail');


    // TV Show
    Route::get('TVShow', 'TVShowController@index')->name('TVShow');
    Route::get('TVShow/data', 'TVShowController@data')->name('TVShowData');
    Route::get('TVShow/add', 'TVShowController@add')->name('TVShowAdd');
    Route::get('TVShow/edit/{id}', 'TVShowController@edit')->name('editTVShow');
    Route::get('TVShow/details/{id}', 'TVShowController@TVShowDetail')->name('TVShowDetail');
    // TV Show Video
    Route::get('TVShow/videos/{id}', 'TVShowController@TVShowvideo')->name('TVShowvideo');
    Route::get('TVShow/videos/add/{id}', 'TVShowController@TVShowvideoadd')->name('TVShowvideoAdd');
    Route::get('TVShow/video/edit/{show_id}/{id}', 'TVShowController@TVShowvideoedit')->name('editTVShow_video');
    Route::post('TVShow/video/sortable', 'TVShowController@TVShowvideosortable')->name('TVShowVideoSortable');
    // Rent
    Route::get('rent', 'RentVideoController@index')->name('RentVideo');
    Route::get('rent/data', 'RentVideoController@data')->name('RentVideoData');
    Route::get('rent/add', 'RentVideoController@add')->name('RentVideoAdd');
    Route::get('rent/edit/{id}', 'RentVideoController@edit')->name('editRentVideo');
    // Setting (Master)
    Route::get('setting', 'SettingController@index')->name('setting');
    Route::post('setting/app', 'SettingController@app')->name('settingapp');
    Route::post('setting/currency', 'SettingController@currency')->name('settingcurrency');
    Route::post('setting/imdbkey', 'SettingController@saveImdbKey')->name('settingimdbkey');
    Route::post('setting/changepassword', 'SettingController@changepassword')->name('settingchangepassword');
    Route::post('setting/admob', 'SettingController@admob_android')->name('settingadmob_android');
    Route::post('setting/admob-ios', 'SettingController@admob_ios')->name('settingadmob_ios');
    Route::post('setting/facebookad', 'SettingController@facebookad')->name('settingfacebookad');
    Route::post('setting/facebookad-ios', 'SettingController@facebookad_ios')->name('settingfacebookad_ios');
    Route::post('setting/sociallink', 'SettingController@SaveSocialLink')->name('settingSocialLink');
    // SMTP
    Route::get('setting/smtp', 'SettingController@smtpindex')->name('settingsmtpindex');
    Route::post('setting/smtp', 'SettingController@smtp')->name('settingsmtp');
    // Packages (plan Subscription)
    Route::get('packages', 'PackageController@index')->name('package');
    Route::get('packages/data', 'PackageController@data')->name('packageData');
    // Payment
    Route::get('payment', 'PaymentOptionController@index')->name('Payment');
    Route::get('payment/data', 'PaymentOptionController@data')->name('PaymentData');
    Route::get('packages/add', 'PackageController@add')->name('packageAdd');
    Route::get('packages/edit/{id}', 'PackageController@edit')->name('editPackage');
    // Notification
    Route::get('notification', 'NotificationController@index')->name('notification');
    Route::get('notification/data', 'NotificationController@data')->name('notificationData');
    Route::get('notification/add', 'NotificationController@add')->name('notificationAdd');
    // IMDB (Video)
    Route::post('serachname/{txtVal}', 'VideoController@SerachName')->name('SerachName');
    Route::post('getdata/{id}', 'VideoController@GetData')->name('GetData');
    // IMDB (TVShow)
    Route::post('tvshow/serachname/{txtVal}', 'TVShowController@SerachName')->name('TVshowSerachName');
    Route::post('tvshow/getdata/{id}', 'TVShowController@GetData')->name('TVshowGetData');
    // Transaction
    Route::get('transaction', 'TransactionController@index')->name('transaction');
    Route::get('transaction-new-web', 'NewWebTransactionController@index')->name('transaction-new-web');
    Route::get('transaction/data', 'TransactionController@data')->name('TransactionData');
    Route::get('transaction/add', 'TransactionController@add')->name('transactionAdd');
    Route::any('transaction_save', 'TransactionController@save')->name('transactionSave');
    Route::any('search_user', 'UserController@searchUser')->name('searchUser');
    // Rent Transaction
    Route::get('renttransaction', 'RentTransactionController@index')->name('RentTransaction');
    Route::get('renttransaction/data', 'RentTransactionController@data')->name('RentTransactionData');
    Route::get('renttransaction/add', 'RentTransactionController@add')->name('RenttransactionAdd');
    Route::any('renttransaction_save', 'RentTransactionController@save')->name('RenttransactionSave');
    Route::any('rentsearchuser', 'RentTransactionController@searchUser')->name('rentSearchUser');
    // Coupons
    Route::get('coupon', 'CouponController@index')->name('coupon');
    Route::get('coupon/data', 'CouponController@data')->name('couponData');
    Route::get('coupon/add', 'CouponController@add')->name('couponAdd');
    Route::get('coupon/edit/{id}', 'CouponController@edit')->name('editCoupon');
    Route::get('coupon/random/add', 'CouponController@randomAdd')->name('couponRandomAdd');
    // Upcoming Video
    Route::get('upcomingvideo', 'UpcomingVideoController@index')->name('upcomingvideo');
    Route::get('upcomingvideo/data', 'UpcomingVideoController@data')->name('upcomingvideoData');
    Route::get('upcomingvideo/add', 'UpcomingVideoController@add')->name('upcomingvideoAdd');
    Route::get('upcomingvideo/edit/{id}', 'UpcomingVideoController@edit')->name('upcomingvideoEdit');
    Route::get('upcomingvideo/details/{id}', 'UpcomingVideoController@detail')->name('upcomingvideoDetail');
    // Upcoming TV Show
    Route::get('upcomingtvshow', 'UpcomingTVShowController@index')->name('upcomingTVShow');
    Route::get('upcomingtvshow/data', 'UpcomingTVShowController@data')->name('upcomingTVShowData');
    Route::get('upcomingtvshow/add', 'UpcomingTVShowController@add')->name('upcomingtvshowAdd');
    Route::get('upcomingtvshow/edit/{id}', 'UpcomingTVShowController@edit')->name('upcomingtvshowEdit');
    Route::get('upcomingtvshow/details/{id}', 'UpcomingTVShowController@TVShowDetail')->name('upcomingTVShowDetail');
    // Upcoming TV Show Video
    Route::get('upcomingtvshow/videos/{id}', 'UpcomingTVShowController@TVShowvideo')->name('upcomingTVShowVideo');
    Route::get('upcomingtvshow/videos/add/{id}', 'UpcomingTVShowController@TVShowvideoadd')->name('upcomingtvshowvideoAdd');
    Route::get('upcomingtvshow/video/edit/{show_id}/{id}', 'UpcomingTVShowController@TVShowvideoedit')->name('upcomingtvshowvideoEdit');
});
