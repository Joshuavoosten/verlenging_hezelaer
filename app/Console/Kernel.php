<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\CampaignCustomersInviteEmailScheduler::class,
        Commands\ContractgegevensCode::class,
        Commands\MailTest::class,
        Commands\PricesCodes::class,
        Commands\PricesImport::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        // Campaign Customers Invite Email Scheduler
        $schedule->command('campaign_customers:invite_email_scheduler ')
                 ->everyFiveMinutes();

        // Import prices from the latest CSV.
        $schedule->command('prices:import')
                 ->hourly();
    }
}
