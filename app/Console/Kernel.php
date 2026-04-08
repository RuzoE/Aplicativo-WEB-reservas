<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        $this->scheduleBackups($schedule);

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

    private function scheduleBackups(Schedule $schedule): void
    {
        $backupEvent = $schedule
            ->command('app:generate-system-backup --source=automatic')
            ->withoutOverlapping()
            ->runInBackground();

        match ($this->resolveBackupFrequency()) {
            'weekly' => $backupEvent->weeklyOn(1, '02:00'),
            'monthly' => $backupEvent->monthlyOn(1, '02:00'),
            default => $backupEvent->dailyAt('02:00'),
        };
    }

    private function resolveBackupFrequency(): string
    {
        try {
            if (Schema::hasTable('backup_settings')) {
                $frequency = DB::table('backup_settings')->value('frequency');

                if (is_string($frequency) && in_array($frequency, ['daily', 'weekly', 'monthly'], true)) {
                    return $frequency;
                }
            }
        } catch (\Throwable) {
            // Si la base de datos aún no está disponible, se usa la configuración por defecto.
        }

        return (string) config('backup.default_frequency', 'daily');
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
