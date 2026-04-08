<?php

namespace App\Console\Commands;

use App\Services\Backups\BackupService;
use Illuminate\Console\Command;

class GenerateSystemBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-system-backup {--source=automatic : Indica si el backup es manual o automatico}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un backup completo del sistema y lo sincroniza con Google Drive';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService): int
    {
        $result = $backupService->runBackup((string) $this->option('source'));

        $this->line($result['message']);

        if (!empty($result['output'])) {
            $this->newLine();
            $this->line($result['output']);
        }

        return $result['ok'] ? self::SUCCESS : self::FAILURE;
    }
}
