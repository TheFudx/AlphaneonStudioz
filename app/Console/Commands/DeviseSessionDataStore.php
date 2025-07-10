<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DeviceSession;
use Browser;
use Log;

class DeviseSessionDataStore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:devise-session-data-store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info("--------------------------------------Cron start:  devise-session-data-store ----------------------------------------");
        $device_session = DeviceSession::where('browser',null)->get();
        // $device_session = DeviceSession::get();
        $count = 0;
        foreach($device_session as $d){
            $browser = Browser::parse($d->device_name);

            $d->browser =  $browser->browserFamily();      // e.g. "Edge"
            $d->browser_version =  $browser->browserVersion();// e.g. "138.0.0.0"
            $d->platform =  $browser->platformFamily();    // e.g. "Windows"
            $d->platform_version =  $browser->platformVersion();// e.g. "10.0"
            $d->device_type =  $browser->deviceType();     // e.g. "desktop", "mobile", "tablet"
            $d->update();
            $count++;
        }
        Log::info("Recored Updated {$count}");
        $this->info("Recored Updated {$count}");
        Log::info("--------------------------------------Cron end:  devise-session-data-store ----------------------------------------");
    }
}
