<?php

namespace App\Listeners;

use App\Models\BackupSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Spatie\Backup\Events\BackupWasSuccessful;
use Spatie\Backup\Events\BackupHasFailed;

class UpdateBackupStatus
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(object $event): void
    {
        $settings = BackupSetting::current();

        if ($event instanceof BackupWasSuccessful) {
            $settings->update([
                'last_status' => 'Correcto',
                'last_run_at' => Carbon::now(),
                'last_message' => 'El respaldo se ha completado y subido con éxito.',
            ]);
            Log::info('BackupService Sync: Estado actualizado a Correcto vía Event Listener.');
        }

        if ($event instanceof BackupHasFailed) {
            $settings->update([
                'last_status' => 'Error',
                'last_run_at' => Carbon::now(),
                'last_message' => 'Fallo al generar el respaldo: ' . ($event->exception?->getMessage() ?? 'Error desconocido'),
            ]);
            Log::error('BackupService Sync: Estado actualizado a Error vía Event Listener.', [
                'error' => $event->exception?->getMessage()
            ]);
        }
    }
}
