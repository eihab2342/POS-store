<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        /*
        |--------------------------------------------------------------------------
        | Daily Database Backup (MySQL)
        |--------------------------------------------------------------------------
        | - Creates a SQL or SQL.GZ backup
        | - Keeps last 14 backups only
        | - Runs once daily
        | - Safe for local POS usage
        */

        $schedule->command('db:backup --keep=14')
            ->dailyAt('3:15') //9
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
