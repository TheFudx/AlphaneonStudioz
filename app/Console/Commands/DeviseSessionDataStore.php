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
        // Log::info("--------------------------------------Cron start:  devise-session-data-store ----------------------------------------");
        $device_session = DeviceSession::where('browser',null)->get();
        // $device_session = DeviceSession::get();
        $count = 0;
        foreach ($device_session as $d) {

            $agent = $d->device_name;

            if (!is_string($agent) || trim($agent) === '') {
                $agent = 'Mozilla/5.0 (Compatible; Unknown Device)';
            }

            $browser = Browser::parse($agent);

            $d->browser          = $browser->browserFamily();
            $d->browser_version  = $browser->browserVersion();
            $d->platform         = $browser->platformFamily();
            $d->platform_version = $browser->platformVersion();
            $d->device_type      = $browser->deviceType();

            $d->save();
            $count++;
        }
        if($count != 0){
            Log::info("Record Updated {$count}");
        }
        $this->info("Record Updated {$count}");
        // Log::info("--------------------------------------Cron end:  devise-session-data-store ----------------------------------------");
    }
}
