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
        \App\Console\Commands\LowBalanceCheck::class,
        \App\Console\Commands\UpdateDelhiveryToken::class,
		\App\Console\Commands\KycPendingCheck::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    { 
		$schedule->command('lowbalance:check')->dailyAt('10:00');
		$schedule->command('kyc:pending-check')->dailyAt('11:00'); 
		$schedule->command('update:awb-number-shipmozo')->everyTwoMinutes(); 
		$schedule->command('shipment:update-status')->everyTenMinutes(); 
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
