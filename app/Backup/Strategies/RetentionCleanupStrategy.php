<?php

namespace App\Backup\Strategies;

use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupCollection;
use Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy;

class RetentionCleanupStrategy extends DefaultStrategy
{
    public function deleteOldBackups(BackupCollection $backups): void
    {
        parent::deleteOldBackups($backups);

        $this->deleteBackupsExceedingCountLimit();
    }

    protected function deleteBackupsExceedingCountLimit(): void
    {
        $maximumBackupCount = (int) $this->config->get('backup.cleanup.max_backups', 0);

        if ($maximumBackupCount < 1) {
            return;
        }

        $existingBackups = $this->backupDestination()
            ->backups()
            ->filter(fn (Backup $backup) => $backup->exists())
            ->values();

        while ($existingBackups->count() > $maximumBackupCount) {
            $oldestBackup = $existingBackups->oldest();

            if ($oldestBackup === null) {
                return;
            }

            $oldestBackup->delete();

            $existingBackups = $existingBackups
                ->filter(fn (Backup $backup) => $backup->exists())
                ->values();
        }
    }
}
