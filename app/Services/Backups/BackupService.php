<?php

namespace App\Services\Backups;

use App\Models\BackupSetting;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupService
{
    private ?string $lastInspectionError = null;

    /**
     * @return array<string, mixed>
     */
    public function getDashboardData(): array
    {
        $settings = $this->refreshStaleProcessingState(BackupSetting::current());
        $backups = $this->listBackups();
        $lastBackup = $backups->first();

        $lastStatus = $this->lastInspectionError
            ? 'Error'
            : ($settings->last_status ?: ($lastBackup ? 'Correcto' : 'Error'));

        $lastStatusColor = match ($lastStatus) {
            'Correcto' => 'success',
            'En proceso' => 'warning',
            default => 'danger',
        };

        return [
            'backups' => $backups,
            'settings' => $settings,
            'summary' => [
                'total' => $backups->count(),
                'last_backup_label' => $lastBackup['formatted_date'] ?? 'Sin backups registrados',
                'last_backup_human' => $lastBackup['human_diff'] ?? 'Aún no se ha generado ningún respaldo.',
                'last_status' => $lastStatus,
                'last_status_color' => $lastStatusColor,
                'storage' => 'Google Drive',
                'frequency' => $settings->frequency,
                'last_message' => $this->resolveSummaryMessage($settings, $lastStatus, $lastBackup !== null),
            ],
        ];
    }

    public function listBackups(): Collection
    {
        $this->lastInspectionError = null;
        $folder = trim((string) config('backup.ui_path', ''), '/');

        try {
            $disk = $this->disk();
            $paths = $folder !== '' ? $disk->allFiles($folder) : $disk->allFiles();
        } catch (\Throwable $exception) {
            Log::warning('No fue posible consultar Google Drive desde el dashboard de backups.', [
                'error' => $exception->getMessage(),
                'disk' => $this->diskName(),
            ]);

            $this->lastInspectionError = $this->formatFailureMessage($exception, 'Google Drive falló al consultar los backups');

            return collect();
        }

        return collect($paths)
            ->filter(fn (string $path) => !str_ends_with($path, '/'))
            ->filter(fn (string $path) => Str::endsWith(Str::lower($path), '.zip'))
            ->map(function (string $path) use ($disk) {
                $size = null;
                $timestamp = null;

                try {
                    $size = (int) $disk->size($path);
                } catch (\Throwable) {
                    $size = null;
                }

                try {
                    $timestamp = (int) $disk->lastModified($path);
                } catch (\Throwable) {
                    $timestamp = null;
                }

                $date = $timestamp ? Carbon::createFromTimestamp($timestamp) : null;
                $status = Str::contains(Str::lower($path), ['error', 'failed']) ? 'Error' : 'Correcto';

                return [
                    'path' => $path,
                    'name' => basename($path),
                    'formatted_date' => $date?->format('d/m/Y h:i A') ?? 'Sin fecha disponible',
                    'human_diff' => $date?->diffForHumans() ?? 'Sin información temporal',
                    'size_bytes' => $size,
                    'size_human' => $this->humanFileSize($size),
                    'location' => 'Google Drive',
                    'status' => $status,
                    'status_color' => $status === 'Correcto' ? 'success' : 'danger',
                    'timestamp' => $timestamp ?? 0,
                ];
            })
            ->sortByDesc('timestamp')
            ->values();
    }

    /**
     * @return array{ok: bool, message: string, output: string}
     */
    public function startManualBackup(string $source = 'manual'): array
    {
        $settings = BackupSetting::current();

        if ($this->isBackupMarkedAsRunning($settings)) {
            return [
                'ok' => false,
                'message' => 'Ya hay un backup en proceso. Espera unos segundos a que termine antes de volver a intentarlo.',
                'output' => '',
            ];
        }

        $settings = $this->refreshStaleProcessingState($settings);

        $settings->fill([
            'last_run_at' => now(),
            'last_status' => 'En proceso',
            'last_message' => $this->limitStatusMessage('La generación del backup se inició en segundo plano. Esta página se actualizará con el resultado en unos segundos.'),
        ])->save();

        registrarAuditoria(
            'CREATE',
            'backups',
            null,
            sprintf('Se inició un backup %s en segundo plano.', $source === 'manual' ? 'manual' : 'automatico')
        );

        // --- BACKGROUND EXECUTION (Windows Detached) ---
        $artisan = base_path('artisan');
        
        // Intentar obtener el ejecutable CLI de PHP (no el CGI de Apache)
        $php = str_ireplace('-cgi.exe', '.exe', PHP_BINARY);
        if (!str_ends_with(strtolower($php), 'php.exe')) {
             $php = 'C:\php\php.exe'; // Fallback común en Laragon
        }
        
        // Usar cmd /c con redirección total para asegurar desvinculación en Windows
        // Añadida bandera --only-db para que sea rápido y solo base de datos
        $cmd = sprintf('cmd /c "start /B "" "%s" "%s" backup:run --only-db --disable-notifications > NUL 2>&1"', $php, $artisan);
        
        try {
            pclose(popen($cmd, "r"));
        } catch (\Throwable $e) {
            Log::error('Fallo al iniciar backup en segundo plano: ' . $e->getMessage());
        }

        return [
            'ok' => true,
            'message' => 'La generación del backup se inició en segundo plano. Actualiza esta página en unos segundos para ver el resultado.',
            'output' => '',
        ];
    }

    /**
     * Versión sincrónica y rápida (solo DB) para respuesta inmediata al usuario.
     */
    public function runBackupSync(): array
    {
        $settings = BackupSetting::current();
        
        try {
            $settings->update([
                'last_status' => 'En proceso',
                'last_run_at' => now(),
                'last_message' => 'Generando respaldo de base de datos...',
            ]);

            // Artisan::call con opciones correctas - --only-db es una opción, no flag de valor
            $exitCode = \Artisan::call('backup:run --only-db --disable-notifications');

            $output = \Artisan::output();
            Log::info('Backup síncrono terminó con código ' . $exitCode . ': ' . $output);

            if ($exitCode === 0) {
                $settings->update([
                    'last_status' => 'Correcto',
                    'last_run_at' => now(),
                    'last_message' => 'Respaldo de base de datos generado con éxito y subido a Google Drive.',
                ]);
                return ['ok' => true, 'message' => '¡Respaldo completado exitosamente! La lista se actualizará ahora.'];
            }

            Log::error('Fallo en backup síncrono (Código ' . $exitCode . '): ' . $output);
            $settings->update([
                'last_status' => 'Error', 
                'last_message' => mb_substr('Error al ejecutar backup. Detalles: ' . $output, 0, 500),
            ]);
            return ['ok' => false, 'message' => 'Hubo un error al ejecutar el respaldo (Código ' . $exitCode . '). Revisa los logs.'];

        } catch (\Throwable $e) {
            Log::error('Excepción en backup síncrono: ' . $e->getMessage());
            $settings->update(['last_status' => 'Error', 'last_message' => mb_substr($e->getMessage(), 0, 500)]);
            return ['ok' => false, 'message' => 'Error inesperado: ' . mb_substr($e->getMessage(), 0, 150)];
        }
    }

    /**
     * @return array{ok: bool, message: string, output: string}
     */
    public function runBackup(string $source = 'manual'): array
    {
        $settings = BackupSetting::current();

        $this->configureRuntimeForBackup();

        try {
            [$exitCode, $output] = $this->runBackupCommandWithRetry($source);
            $isSuccessful = $exitCode === 0;

            if (! $isSuccessful) {
                Log::error('El comando backup:run terminó con error.', [
                    'source' => $source,
                    'disk' => $this->diskName(),
                    'exit_code' => $exitCode,
                    'output' => $output,
                ]);
            }

            $message = $this->messageFromBackupResult($isSuccessful, $output);

            $settings->fill([
                'last_run_at' => now(),
                'last_status' => $isSuccessful ? 'Correcto' : 'Error',
                'last_message' => $this->limitStatusMessage($message),
            ])->save();

            registrarAuditoria(
                $isSuccessful ? 'CREATE' : 'UPDATE',
                'backups',
                null,
                sprintf('Se ejecutó un backup %s con estado %s.', $source === 'manual' ? 'manual' : 'automatico', $isSuccessful ? 'correcto' : 'error')
            );

            return [
                'ok' => $isSuccessful,
                'message' => $message,
                'output' => $output,
            ];
        } catch (\Throwable $exception) {
            Log::error('La generación o subida del backup falló.', [
                'source' => $source,
                'disk' => $this->diskName(),
                'error' => $exception->getMessage(),
            ]);

            $message = $this->formatFailureMessage($exception, 'No se pudo generar el backup');

            $settings->fill([
                'last_run_at' => now(),
                'last_status' => 'Error',
                'last_message' => $this->limitStatusMessage($message),
            ])->save();

            registrarAuditoria(
                'UPDATE',
                'backups',
                null,
                Str::limit('Error al generar backup: '.$exception->getMessage(), 255, '...')
            );

            return [
                'ok' => false,
                'message' => $message,
                'output' => $exception->getMessage(),
            ];
        }
    }

    public function updateSchedule(string $frequency): BackupSetting
    {
        $settings = BackupSetting::current();

        $settings->fill([
            'frequency' => $frequency,
            'last_message' => $this->limitStatusMessage('Programación automática actualizada a '.$this->frequencyLabel($frequency).'.'),
        ])->save();

        registrarAuditoria(
            'UPDATE',
            'backups',
            null,
            'Programación de backups actualizada a '.$this->frequencyLabel($frequency).'.'
        );

        return $settings->refresh();
    }

    public function downloadBackup(string $encodedPath): StreamedResponse
    {
        $path = $this->decodePath($encodedPath);
        $disk = $this->disk();

        abort_unless($path !== '' && $disk->exists($path), 404, 'El backup solicitado no existe.');

        return response()->streamDownload(function () use ($disk, $path) {
            echo $disk->get($path);
        }, basename($path));
    }

    public function deleteBackup(string $encodedPath): bool
    {
        $path = $this->decodePath($encodedPath);
        $disk = $this->disk();

        abort_unless($path !== '' && $disk->exists($path), 404, 'El backup solicitado no existe.');

        $deleted = $disk->delete($path);

        if ($deleted) {
            registrarAuditoria('DELETE', 'backups', null, 'Se eliminó el backup '.basename($path).'.');
        }

        return $deleted;
    }

    private function disk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk($this->diskName());
    }

    private function diskName(): string
    {
        return (string) (config('backup.backup.destination.disks.0') ?? 'google');
    }

    private function decodePath(string $encodedPath): string
    {
        $decoded = base64_decode($encodedPath, true);

        return is_string($decoded) && $decoded !== '' ? $decoded : '';
    }

    private function humanFileSize(?int $bytes): string
    {
        if (empty($bytes)) {
            return 'No disponible';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = (float) $bytes;

        foreach ($units as $unit) {
            if ($size < 1024 || $unit === end($units)) {
                return number_format($size, $unit === 'B' ? 0 : 2).' '.$unit;
            }

            $size /= 1024;
        }

        return number_format((float) $bytes, 0).' B';
    }

    private function configureRuntimeForBackup(): void
    {
        if (function_exists('ignore_user_abort')) {
            ignore_user_abort(true);
        }

        @set_time_limit(0);

        $memoryLimit = (string) config('backup.runtime.memory_limit', '512M');

        if ($memoryLimit !== '') {
            @ini_set('memory_limit', $memoryLimit);
        }
    }

    private function refreshStaleProcessingState(BackupSetting $settings): BackupSetting
    {
        if ($settings->last_status === 'En proceso' && ! $this->isBackupMarkedAsRunning($settings)) {
            $settings->fill([
                'last_status' => 'Error',
                'last_message' => $this->limitStatusMessage('El último backup quedó marcado como en proceso durante demasiado tiempo. Ya puedes volver a ejecutarlo.'),
            ])->save();
        }

        return $settings->refresh();
    }

    private function limitStatusMessage(string $message): string
    {
        return Str::limit(trim($message), 180, '...');
    }

    private function resolveSummaryMessage(BackupSetting $settings, string $lastStatus, bool $hasBackups): string
    {
        if ($this->lastInspectionError) {
            return $this->lastInspectionError;
        }

        $message = trim((string) ($settings->last_message ?? ''));

        if ($message !== '' && ! ($hasBackups && $message === 'Aún no se ha generado ningún backup desde este panel.')) {
            return $message;
        }

        if ($hasBackups && $lastStatus === 'Correcto') {
            return 'Los backups están disponibles en Google Drive y el módulo está operativo.';
        }

        return $message !== '' ? $message : 'Los archivos se administran desde el disco "google" de Laravel Storage.';
    }

    public function resetStaleState(): void
    {
        $settings = BackupSetting::current();
        $settings->fill([
            'last_status' => 'Error',
            'last_message' => $this->limitStatusMessage('El estado del backup fue reiniciado manualmente por el administrador debido a un bloqueo detectado.'),
        ])->save();
    }

    private function isBackupMarkedAsRunning(BackupSetting $settings): bool
    {
        if ($settings->last_status !== 'En proceso') {
            return false;
        }

        if (! $settings->last_run_at) {
            return true;
        }

        return $settings->last_run_at->greaterThan(now()->subMinutes(5)); // Timeout de 5 min para evitar que el botón se quede bloqueado
    }

    /**
     * @return array{0:int,1:string}
     */
    private function runBackupCommandWithRetry(string $source): array
    {
        $attempt = 0;

        do {
            $attempt++;

            $exitCode = Artisan::call('backup:run', [
                '--disable-notifications' => true,
            ]);

            $output = trim(Artisan::output());

            if ($exitCode === 0) {
                return [$exitCode, $output];
            }

            if ($attempt < 2 && $this->shouldRetryAfterSocketFailure($output)) {
                Log::warning('El backup falló por un problema transitorio de socket con MySQL; se reintentará automáticamente.', [
                    'source' => $source,
                    'attempt' => $attempt,
                    'disk' => $this->diskName(),
                    'output' => $output,
                ]);

                continue;
            }

            return [$exitCode, $output];
        } while ($attempt < 2);

        return [$exitCode ?? 1, $output ?? ''];
    }

    private function shouldRetryAfterSocketFailure(string $output): bool
    {
        return Str::contains($output, [
            'mysqldump: Got error: 2004',
            'TCP/IP socket (10106)',
        ]);
    }

    private function frequencyLabel(string $frequency): string
    {
        return match ($frequency) {
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
            default => 'Diario',
        };
    }

    private function messageFromBackupResult(bool $isSuccessful, string $output): string
    {
        if ($isSuccessful) {
            return 'Backup subido correctamente a Google Drive.';
        }

        if (Str::contains($output, ['mysqldump: Got error: 2004', 'TCP/IP socket (10106)'])) {
            return 'No se pudo generar el backup porque `mysqldump` no logró conectarse a MySQL (error 2004 / socket TCP/IP 10106). Verifica que MySQL de Laragon esté iniciado y vuelve a intentarlo.';
        }

        if (Str::contains($output, ['invalid_grant', 'Token has been expired or revoked'])) {
            return 'Error al subir a Google Drive porque el refresh token expiró o fue revocado. Actualiza `GOOGLE_DRIVE_REFRESH_TOKEN` y vuelve a intentarlo.';
        }

        if (Str::contains($output, ['Allowed memory size', 'Out of memory'])) {
            return 'No se pudo subir el backup a Google Drive porque PHP agotó la memoria disponible durante el proceso. Aumenta `BACKUP_MEMORY_LIMIT` (por ejemplo `512M` o `1024M`) y vuelve a intentarlo.';
        }

        return 'El proceso de backup terminó con errores. Revisa `storage/logs/laravel.log` para más detalle.';
    }

    private function formatFailureMessage(\Throwable $exception, string $prefix): string
    {
        $rawMessage = $exception->getMessage();

        if (Str::contains($rawMessage, ['invalid_grant', 'Token has been expired or revoked'])) {
            return $prefix.': la conexión con Google Drive falló porque el refresh token expiró o fue revocado. Actualiza `GOOGLE_DRIVE_REFRESH_TOKEN`, `GOOGLE_DRIVE_CLIENT_ID` y `GOOGLE_DRIVE_CLIENT_SECRET` en el entorno.';
        }

        if (Str::contains($rawMessage, ['Allowed memory size', 'Out of memory'])) {
            return $prefix.': PHP agotó la memoria disponible durante la compresión o subida a Google Drive. Aumenta `BACKUP_MEMORY_LIMIT` (por ejemplo `512M` o `1024M`) y vuelve a intentarlo.';
        }

        return $prefix.': '.$rawMessage;
    }

    /**
     * Extrae un backup ZIP protegido por contraseña y restaura la BD.
     */
    public function restoreBackup(string $encodedPath): array
    {
        $path = $this->decodePath($encodedPath);
        $disk = $this->disk();

        if ($path === '' || !$disk->exists($path)) {
            return ['ok' => false, 'message' => 'El backup solicitado no existe en Google Drive.'];
        }

        // Crear primero un backup de seguridad automático (before-restore) obligatorio
        $safetyResult = $this->runBackup('before-restore');
        
        if (!$safetyResult['ok']) {
            return [
                'ok' => false, 
                'message' => 'No se pudo realizar el backup de seguridad previo. Abortando restauración por seguridad. Error: ' . $safetyResult['message']
            ];
        }

        $tempZipPath = storage_path('app/temp_restore_' . time() . '.zip');
        $extractPath = storage_path('app/temp_restore_dir_' . time());

        try {
            // 1. Descargar Zip de GDrive
            file_put_contents($tempZipPath, $disk->get($path));

            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0755, true);
            }

            // 2. Extraer usando ZipArchive nativo (con soporte de contraseña)
            $zip = new \ZipArchive();
            if ($zip->open($tempZipPath) === true) {
                // Leer la contraseña del entorno (ej. Oasis0102)
                $password = env('BACKUP_ARCHIVE_PASSWORD') ?: config('backup.backup.password');
                
                if ($password) {
                    $zip->setPassword($password);
                }

                // Extrayendo archivo por archivo (ZipArchive extractTo a veces falla con contraseñas en PHP puro si no se itera)
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entry = $zip->getNameIndex($i);
                    // Solo nos interesa extraer el archivo SQL (normalmente db-dumps/mysql-laravel.sql)
                    if (str_ends_with(strtolower($entry), '.sql')) {
                        $content = $zip->getFromIndex($i);
                        if ($content !== false) {
                            $sqlDestPath = $extractPath . '/' . basename($entry);
                            file_put_contents($sqlDestPath, $content);
                        }
                    }
                }
                $zip->close();
            } else {
                return ['ok' => false, 'message' => 'No se pudo abrir o leer el archivo ZIP del backup.'];
            }

            // 3. Buscar el archivo SQL extraído
            $sqlFile = null;
            $files = scandir($extractPath);
            foreach ($files as $file) {
                if (str_ends_with(strtolower($file), '.sql')) {
                    $sqlFile = $extractPath . '/' . $file;
                    break;
                }
            }

            if (!$sqlFile) {
                $this->cleanupRestoreFiles($tempZipPath, $extractPath);
                return ['ok' => false, 'message' => 'Contraseña incorrecta o no se encontró un script .sql en el backup.'];
            }

            // 4. Importar base de datos
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host', '127.0.0.1');
            $dbPort = config('database.connections.mysql.port', '3306');

            // Usar ejecutable mysql asumiendo misma ruta que mysqldump
            $mysqlPath = env('MYSQL_CLIENT_PATH', '');
            if ($mysqlPath === '') {
                $dumpPath = (string) env('MYSQLDUMP_PATH', '');
                $mysqlPath = $dumpPath ? str_replace('mysqldump.exe', 'mysql.exe', $dumpPath) : 'mysql';
            }

            $command = sprintf(
                '"%s" -h "%s" -P %s -u "%s" %s "%s" < "%s"',
                $mysqlPath,
                $dbHost,
                $dbPort,
                $dbUser,
                $dbPass ? '-p"' . $dbPass . '"' : '',
                $dbName,
                $sqlFile
            );

            exec($command . ' 2>&1', $output, $exitCode);

            // Cleanup
            $this->cleanupRestoreFiles($tempZipPath, $extractPath);

            if ($exitCode !== 0) {
                Log::error('Restauración de DB fallida:', ['command' => $command, 'output' => implode("\n", $output)]);
                return ['ok' => false, 'message' => 'No se pudo importar la base de datos (Error ' . $exitCode . ').'];
            }

            registrarAuditoria('UPDATE', 'backups', null, 'Se restauró el backup: ' . basename($path));
            return ['ok' => true, 'message' => 'Restauración completada. Base de datos actualizada con éxito.'];

        } catch (\Throwable $e) {
            $this->cleanupRestoreFiles($tempZipPath ?? '', $extractPath ?? '');
            Log::error('Error crítico al restaurar backup: ' . $e->getMessage());
            return ['ok' => false, 'message' => 'Ocurrió un error en el servidor durante la restauración.'];
        }
    }

    private function cleanupRestoreFiles(string $zipPath, string $dirPath): void
    {
        if (file_exists($zipPath)) {
            @unlink($zipPath);
        }
        if (is_dir($dirPath)) {
            $files = array_diff(scandir($dirPath), ['.', '..']);
            foreach ($files as $file) {
                @unlink("$dirPath/$file");
            }
            @rmdir($dirPath);
        }
    }
}
