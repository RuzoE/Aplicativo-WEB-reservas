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
        // $schedule->command('inspire')->hourly();

        $schedule
            ->command('backup:run --only-db')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground();

        $schedule
            ->command('backup:clean')
            ->dailyAt('02:30')
            ->withoutOverlapping()
            ->runInBackground();

        $schedule
            ->command('backup:monitor')
            ->dailyAt('02:45')
            ->withoutOverlapping()
            ->runInBackground();

        if (config('auditoria.cleanup.enabled', true)) {
            $schedule
                ->command('auditoria:purge')
                ->dailyAt(config('auditoria.cleanup.schedule', '03:15'))
                ->withoutOverlapping();
        }
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
