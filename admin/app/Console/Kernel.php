<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('run:test-cron')->everyTwoMinutes();
        $schedule->command('run:autoresponder')->dailyAt(12);
        // $schedule->command('delete:demos')->everyMinute()->emailOutputOnFailure('rd1@teamioss.com');
        // $schedule->command('notify:incomplete-users')->dailyAt(12);
        // $schedule->command('send:customdemo-mails')->dailyAt(12);
        $schedule->command('clear:logs')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
