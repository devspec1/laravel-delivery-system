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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call('App\Http\Controllers\Api\CronController@requestCars')->everyMinute();
        $schedule->call('App\Http\Controllers\Api\CronController@updateCurrency')->daily();
        $schedule->call('App\Http\Controllers\Api\CronController@updateReferralStatus')->daily();
        $schedule->call('App\Http\Controllers\Api\CronController@updateOfflineUsers')->everyFifteenMinutes();
        $schedule->call('App\Http\Controllers\Api\CronController@updatePaypalPayouts')->twiceDaily();
        $schedule->command('queue:work --tries=3 --once')->cron('* * * * *');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
