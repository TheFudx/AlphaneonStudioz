<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class CheckSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and deactivate expired subscriptions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredUsers = User::where('subscription_end_date', '<', Carbon::now())
                            ->where('subscription', 'Yes')
                            ->get();

        foreach ($expiredUsers as $user) {
            $user->update(['subscription' => 'No']);
            $this->info("Deactivated subscription for User ID: {$user->id}");
        }

        $this->info("Checked and updated expired subscriptions.");
        return 0;
    }
}
