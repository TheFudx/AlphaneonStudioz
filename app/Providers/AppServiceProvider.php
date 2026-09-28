<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DeviceService;
use App\Models\Category;
use App\Models\Notification;
use App\Models\UserWatchLaterList;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Helpers\VideoHelper; // Import your VideoHelper
use Exception; // Required for throwing exceptions


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        // $this->app->singleton(DeviceService::class, function ($app) {
        //     return new DeviceService();
        // });
        $this->app->singleton('logged-in-user',function($app){
            return auth()->user();
        });

        $this->app->singleton('category',function($app){
            return Category::orderBy('created_at', 'desc')->get();
        });

        $this->app->singleton('notification',function($app){
            return Notification::where('status',1)->orderBy('created_at', 'desc')->get();
        });

        $this->app->singleton('wishlist',function($app){
            // return session()->get('watchlist', []);
            return UserWatchLaterList::where('user_id', auth()->id())->get();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         view()->composer('*', function ($view) {
        $view->with('asset_version', time());
    });
    //    $videoKey = env('VIDEO_ENCRYPTION_KEY');

    //     if ($videoKey) {
    //         try {
    //             VideoHelper::setKey($videoKey);
    //         } catch (Exception $e) {
    //             // Log the error if the key is invalid
    //             \Log::error('VideoHelper Key Error: ' . $e->getMessage());
    //             // Depending on criticality, you might want to throw the exception
    //             // or just ensure your app handles potential encryption failures gracefully.
    //         }
    //     } else {
    //         \Log::error('VIDEO_ENCRYPTION_KEY is not set in .env. VideoHelper encryption may not function.');
    //     }
    }
}
