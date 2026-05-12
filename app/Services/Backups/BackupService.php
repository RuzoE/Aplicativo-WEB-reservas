<?php

namespace App\Services\Backups;

use App\Models\BackupSetting;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\Filesystem as LocalFilesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class BackupService
{
    private ?string $lastInspectionError = null;

    public function __construct(private readonly LocalFilesystem $files)
    {
    }

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
                'storage' => 'AWS S3 / Drive',
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
                    'location' => 'AWS S3 / Drive',
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
        $this->cleanupBackupTemporaryArtifacts();
        $tempDir = $this->prepareWritableTempDirectory();
        $php = $this->findPhpCli();
        $artisan = base_path('artisan');
        $memoryLimit = $this->cliMemoryLimit();

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

        Log::info('Inicio de backup manual en segundo plano.', [
            'source' => $source,
            'disk' => $this->diskName(),
            'temp_dir' => $tempDir,
            'memory_limit' => $memoryLimit,
        ]);

        $cmd = sprintf(
            'cmd /c "set TEMP=%s&& set TMP=%s&& set TMPDIR=%s&& start /B "" %s -d memory_limit=%s %s backup:run --only-db --disable-notifications > NUL 2>&1"',
            $this->escapeWindowsSetValue($tempDir),
            $this->escapeWindowsSetValue($tempDir),
            $this->escapeWindowsSetValue($tempDir),
            $this->quoteWindowsPath($php),
            $memoryLimit,
            $this->quoteWindowsPath($artisan)
        );

        try {
            pclose(popen($cmd, 'r'));
        } catch (\Throwable $e) {
            Log::error('Fallo al iniciar backup en segundo plano.', [
                'source' => $source,
                'error' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'message' => 'No se pudo iniciar el backup manual en segundo plano. Revisa `storage/logs/laravel.log` para más detalle.',
                'output' => $e->getMessage(),
            ];
        }

        return [
            'ok' => true,
            'message' => 'La generación del backup se inició en segundo plano. Actualiza esta página en unos segundos para ver el resultado.',
            'output' => '',
        ];
    }

    /**
     * @return array{ok: bool, message: string, output?: string}
     */
    public function runBackupSync(): array
    {
        return $this->executeManagedBackup('manual', '¡Respaldo completado exitosamente! La lista se actualizará ahora.');
    }

    /** Detecta el ejecutable PHP CLI en Laragon (no el php-cgi de Apache). */
    private function findPhpCli(): string
    {
        $binary = PHP_BINARY;
        if (! str_contains(strtolower($binary), '-cgi')) {
            return $binary;
        }

        $candidate = str_ireplace(['-cgi.exe', '-cgi'], ['.exe', ''], $binary);
        if (file_exists($candidate)) {
            return $candidate;
        }

        $phpDirs = glob('C:\\laragon\\bin\\php\\php-*\\php.exe') ?: [];
        if (! empty($phpDirs)) {
            rsort($phpDirs);

            return $phpDirs[0];
        }

        foreach (['C:\\laragon\\bin\\php\\php.exe', 'C:\\php\\php.exe'] as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return 'php';
    }

    /**
     * @param array<int, string> $arguments
     * @return array{0:int,1:string}
     */
    protected function executeBackupProcess(array $arguments, string $tempDir, int $timeout = 300): array
    {
        $phpBinary = $this->findPhpCli();
        $artisan = base_path('artisan');
        $memoryLimit = $this->cliMemoryLimit();
        $commandParts = array_map(
            fn (string $part): string => $this->quoteShellArgument($part),
            array_merge([$phpBinary, '-d', 'memory_limit='.$memoryLimit, $artisan, 'backup:run'], $arguments)
        );

        return $this->executeShellCommand(implode(' ', $commandParts), $tempDir, $timeout, 'backup-cli');
    }

    /**
     * @return array{ok: bool, message: string, output: string}
     */
    public function runBackup(string $source = 'manual'): array
    {
        return $this->executeManagedBackup($source, 'Backup subido correctamente a S3 y Drive.');
    }

    /**
     * @return array{ok: bool, message: string, output: string}
     */
    protected function executeManagedBackup(string $source, string $successMessage): array
    {
        $settings = BackupSetting::current();
        $startedAt = microtime(true);
        $operationSucceeded = false;

        $this->cleanupBackupTemporaryArtifacts();
        $this->configureRuntimeForBackup();
        $tempDir = $this->prepareWritableTempDirectory();

        Log::info('Inicio de backup de base de datos.', [
            'source' => $source,
            'disk' => $this->diskName(),
            'temp_dir' => $tempDir,
            'memory_limit' => $this->cliMemoryLimit(),
            'queue_ready' => true,
        ]);

        try {
            $settings->fill([
                'last_status' => 'En proceso',
                'last_run_at' => now(),
                'last_message' => $this->limitStatusMessage('Generando respaldo de base de datos...'),
            ])->save();

            [$exitCode, $output] = $this->runBackupCommandWithRetry($source, $tempDir);
            $operationSucceeded = $exitCode === 0;
            $message = $operationSucceeded ? $successMessage : $this->messageFromBackupResult(false, $output);

            if (! $operationSucceeded) {
                Log::error('El comando backup:run terminó con error.', [
                    'source' => $source,
                    'disk' => $this->diskName(),
                    'exit_code' => $exitCode,
                    'output' => $output,
                ]);
            }

            $settings->fill([
                'last_run_at' => now(),
                'last_status' => $operationSucceeded ? 'Correcto' : 'Error',
                'last_message' => $this->limitStatusMessage($message),
            ])->save();

            registrarAuditoria(
                $operationSucceeded ? 'CREATE' : 'UPDATE',
                'backups',
                null,
                sprintf('Se ejecutó un backup %s con estado %s.', $source === 'manual' ? 'manual' : 'automatico', $operationSucceeded ? 'correcto' : 'error')
            );

            return [
                'ok' => $operationSucceeded,
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
        } finally {
            $this->cleanupBackupTemporaryArtifacts();

            Log::info('Fin de backup de base de datos.', [
                'source' => $source,
                'disk' => $this->diskName(),
                'success' => $operationSucceeded,
                'duration_seconds' => $this->elapsedSeconds($startedAt),
            ]);
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

        $memoryLimit = $this->cliMemoryLimit();
        if ($memoryLimit !== '') {
            @ini_set('memory_limit', $memoryLimit);
        }

        $this->prepareWritableTempDirectory();
    }

    private function cliMemoryLimit(): string
    {
        return (string) config('backup.runtime.memory_limit', '1024M');
    }

    private function processTimeout(): int
    {
        return (int) config('backup.runtime.process_timeout', 600);
    }

    protected function prepareWritableTempDirectory(): string
    {
        $candidates = [
            storage_path('app/backup-temp/system-tmp'),
            storage_path('app/backup-temp'),
            storage_path('framework/cache'),
            storage_path('app'),
            sys_get_temp_dir(),
        ];

        foreach ($candidates as $candidate) {
            if (! is_string($candidate) || trim($candidate) === '') {
                continue;
            }

            if (! is_dir($candidate)) {
                @mkdir($candidate, 0755, true);
            }

            if (! is_dir($candidate) || ! is_writable($candidate)) {
                continue;
            }

            foreach (['TMP', 'TEMP', 'TMPDIR'] as $variable) {
                putenv($variable.'='.$candidate);
                $_ENV[$variable] = $candidate;
                $_SERVER[$variable] = $candidate;
            }

            @ini_set('sys_temp_dir', $candidate);

            return $candidate;
        }

        return storage_path('app');
    }

    /**
     * @return array{0:int,1:string}
     */
    protected function executeShellCommand(string $baseCommand, string $tempDir, int $timeout = 300, string $logPrefix = 'command'): array
    {
        $outputDir = storage_path('app/backup-temp/process-output');

        if (! is_dir($outputDir)) {
            @mkdir($outputDir, 0755, true);
        }

        $outputFile = $outputDir.DIRECTORY_SEPARATOR.$logPrefix.'-'.now()->format('Ymd-His-u').'.log';

        if (DIRECTORY_SEPARATOR === '\\') {
            $shellCommand = sprintf(
                'cmd /V:ON /C "set TEMP=%s&& set TMP=%s&& set TMPDIR=%s&& %s > %s 2>&1"',
                $this->escapeWindowsSetValue($tempDir),
                $this->escapeWindowsSetValue($tempDir),
                $this->escapeWindowsSetValue($tempDir),
                $baseCommand,
                $this->quoteWindowsPath($outputFile)
            );
        } else {
            $shellCommand = sprintf(
                'export TEMP=%s TMP=%s TMPDIR=%s; %s > %s 2>&1',
                $this->quoteShellArgument($tempDir),
                $this->quoteShellArgument($tempDir),
                $this->quoteShellArgument($tempDir),
                $baseCommand,
                $this->quoteShellArgument($outputFile)
            );
        }

        $output = [];
        $exitCode = 1;

        @set_time_limit(max($timeout + 30, 330));
        exec($shellCommand, $output, $exitCode);

        $capturedOutput = is_file($outputFile)
            ? trim((string) file_get_contents($outputFile))
            : trim(implode(PHP_EOL, $output));

        if (is_file($outputFile)) {
            @unlink($outputFile);
        }

        return [$exitCode, $capturedOutput];
    }

    protected function cleanupBackupTemporaryArtifacts(): void
    {
        $paths = array_merge(
            [
                storage_path('app/backup-temp/temp'),
                storage_path('app/backup-temp/process-output'),
                storage_path('app/temp_restore'),
            ],
            glob(storage_path('app/backup-temp/restore-*')) ?: [],
            glob(storage_path('app/temp_restore_*')) ?: [],
            glob(storage_path('app/temp_restore_dir_*')) ?: []
        );

        foreach (array_unique($paths) as $path) {
            $this->removeTemporaryPath($path);
        }

        $systemTempDir = storage_path('app/backup-temp/system-tmp');
        $this->cleanupDirectoryContents($systemTempDir);

        $backupRoot = storage_path('app/backup-temp');
        if (is_dir($backupRoot)) {
            $remainingItems = array_values(array_diff(scandir($backupRoot) ?: [], ['.', '..']));

            if ($remainingItems === ['system-tmp']) {
                $this->cleanupDirectoryContents($systemTempDir);
                $remainingItems = array_values(array_diff(scandir($backupRoot) ?: [], ['.', '..']));
            }

            if ($remainingItems === []) {
                @rmdir($backupRoot);
            }
        }
    }

    private function cleanupDirectoryContents(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $items = array_diff(scandir($directory) ?: [], ['.', '..']);
        foreach ($items as $item) {
            $this->removeTemporaryPath($directory.DIRECTORY_SEPARATOR.$item);
        }
    }

    private function removeTemporaryPath(string $path): void
    {
        if ($path === '' || ! file_exists($path)) {
            return;
        }

        try {
            if (is_dir($path)) {
                $this->files->deleteDirectory($path);
            } else {
                @unlink($path);
            }
        } catch (\Throwable $exception) {
            Log::warning('No se pudo limpiar un recurso temporal de backup.', [
                'path' => $path,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function quoteShellArgument(string $value): string
    {
        return escapeshellarg($value);
    }

    private function quoteWindowsPath(string $value): string
    {
        return '"'.str_replace('"', '""', $value).'"';
    }

    private function escapeWindowsSetValue(string $value): string
    {
        return str_replace(
            ['^', '&', '|', '<', '>'],
            ['^^', '^&', '^|', '^<', '^>'],
            $value
        );
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
            return 'Los backups están disponibles en S3 / Drive y el módulo está operativo.';
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

        return $settings->last_run_at->greaterThan(now()->subMinutes(5));
    }

    /**
     * @return array{0:int,1:string}
     */
    protected function runBackupCommandWithRetry(string $source, ?string $tempDir = null): array
    {
        $attempt = 0;
        $tempDir ??= $this->prepareWritableTempDirectory();
        $timeout = $this->processTimeout();

        do {
            $attempt++;
            [$exitCode, $output] = $this->executeBackupProcess([
                '--only-db',
                '--disable-notifications',
            ], $tempDir, $timeout);

            if ($exitCode === 0) {
                return [$exitCode, $output];
            }

            $retryDueToSocket = $this->shouldRetryAfterSocketFailure($output);
            $retryDueToTempCollision = $this->shouldRetryAfterTemporaryPathFailure($output);

            if ($attempt < 2 && ($retryDueToSocket || $retryDueToTempCollision)) {
                $this->cleanupBackupTemporaryArtifacts();

                Log::warning('El backup falló por una condición transitoria y se reintentará automáticamente.', [
                    'source' => $source,
                    'attempt' => $attempt,
                    'disk' => $this->diskName(),
                    'reason' => $retryDueToSocket ? 'mysql-socket' : 'temp-collision',
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

    private function shouldRetryAfterTemporaryPathFailure(string $output): bool
    {
        return Str::contains($output, [
            'already exists',
            'A temporary file could not be opened to write the process output',
            'Path `',
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
            return 'Backup subido correctamente a S3 y Drive.';
        }

        if (Str::contains($output, ['fwrite(): Argument #1 ($stream) must be of type resource, bool given', 'getTempFileHandle', 'A temporary file could not be opened to write the process output'])) {
            return 'No se pudo generar el backup porque PHP no tenía un directorio temporal válido para crear archivos de trabajo. El sistema ya limpia y fuerza un TEMP/TMP seguro dentro de `storage/app/backup-temp`; vuelve a intentarlo.';
        }

        if (Str::contains($output, ['Path `', 'already exists'])) {
            return 'No se pudo generar el backup porque quedó una carpeta temporal anterior bloqueando el proceso. El sistema la limpia automáticamente antes de reintentar.';
        }

        if (Str::contains($output, ['mysqldump: Got error: 2004', 'TCP/IP socket (10106)'])) {
            return 'No se pudo generar el backup porque `mysqldump` no logró conectarse a MySQL (error 2004 / socket TCP/IP 10106). Verifica que MySQL de Laragon esté iniciado y vuelve a intentarlo.';
        }

        if (Str::contains($output, ['invalid_grant', 'Token has been expired or revoked'])) {
            return 'Error al subir a Google Drive porque el refresh token expiró o fue revocado. Actualiza `GOOGLE_DRIVE_REFRESH_TOKEN` y vuelve a intentarlo.';
        }

        if (Str::contains($output, ['Allowed memory size', 'Out of memory'])) {
            return 'No se pudo subir el backup a S3 / Drive porque PHP agotó la memoria disponible durante el proceso. Aumenta `BACKUP_MEMORY_LIMIT` (por ejemplo `512M` o `1024M`) y vuelve a intentarlo.';
        }

        return 'El proceso de backup terminó con errores. Revisa `storage/logs/laravel.log` para más detalle.';
    }

    private function formatFailureMessage(\Throwable $exception, string $prefix): string
    {
        $rawMessage = $exception->getMessage();

        if (Str::contains($rawMessage, ['fwrite(): Argument #1 ($stream) must be of type resource, bool given', 'getTempFileHandle', 'A temporary file could not be opened to write the process output'])) {
            return $prefix.': PHP no tenía un directorio temporal válido para crear los archivos auxiliares del backup o restore. El sistema ahora usa un TEMP/TMP interno dentro de `storage/app/backup-temp` para evitar este bloqueo.';
        }

        if (Str::contains($rawMessage, ['Path `', 'already exists'])) {
            return $prefix.': quedó un directorio temporal viejo bloqueando el proceso. El sistema ya limpia estos residuos antes y después de cada operación.';
        }

        if (Str::contains($rawMessage, ['invalid_grant', 'Token has been expired or revoked'])) {
            return $prefix.': la conexión con Google Drive falló porque el refresh token expiró o fue revocado. Actualiza `GOOGLE_DRIVE_REFRESH_TOKEN`, `GOOGLE_DRIVE_CLIENT_ID` y `GOOGLE_DRIVE_CLIENT_SECRET` en el entorno.';
        }

        if (Str::contains($rawMessage, ['Allowed memory size', 'Out of memory'])) {
            return $prefix.': PHP agotó la memoria disponible durante la compresión o subida a Google Drive. Aumenta `BACKUP_MEMORY_LIMIT` (por ejemplo `512M` o `1024M`) y vuelve a intentarlo.';
        }

        return $prefix.': '.$rawMessage;
    }

    private function elapsedSeconds(float $startedAt): float
    {
        return round(max(microtime(true) - $startedAt, 0), 2);
    }

    /**
     * Restaura un backup ZIP protegido por contraseña y revierte automáticamente
     * al estado anterior si la importación principal falla.
     */
    public function restoreBackup(string $encodedPath): array
    {
        $path = $this->decodePath($encodedPath);

        if ($path === '') {
            return ['ok' => false, 'message' => 'La ruta del backup proporcionada no es válida.'];
        }

        return $this->restoreBackupFromPath($path, true, false);
    }

    /**
     * @return array{ok: bool, message: string}
     */
    protected function restoreBackupFromPath(string $path, bool $createSafetyBackup = true, bool $isRollback = false): array
    {
        $startedAt = microtime(true);
        $disk = $this->disk();
        $tempZipPath = '';
        $extractPath = '';
        $safetyBackupPath = null;
        $rollbackAllowed = $createSafetyBackup && ! $isRollback;
        $importStarted = false;

        Log::info('Inicio de restauración de backup.', [
            'path' => $path,
            'disk' => $this->diskName(),
            'mode' => $isRollback ? 'rollback' : 'restore',
            'queue_ready' => true,
        ]);

        try {
            $sourceValidation = $this->validateRestoreSource($disk, $path);
            if (! $sourceValidation['ok']) {
                return $sourceValidation;
            }

            $this->cleanupBackupTemporaryArtifacts();
            $this->configureRuntimeForBackup();

            if ($createSafetyBackup) {
                $safetyResult = $this->createSafetyBackupSnapshot();
                if (! ($safetyResult['ok'] ?? false)) {
                    return [
                        'ok' => false,
                        'message' => 'No se pudo realizar el backup de seguridad previo. Abortando restauración por seguridad. Error: '.($safetyResult['message'] ?? 'Sin detalle.'),
                    ];
                }

                $safetyBackupPath = $safetyResult['backup_path'] ?? null;
            }

            ['zip' => $tempZipPath, 'extract' => $extractPath] = $this->createRestoreWorkspace();

            $downloadResult = $this->downloadBackupToTempPath($disk, $path, $tempZipPath);
            if (! $downloadResult['ok']) {
                return $downloadResult;
            }

            $sqlFile = $this->extractSqlDumpFromArchive($tempZipPath, $extractPath);
            $importStarted = true;

            $importResult = $this->importSqlDump($sqlFile);
            if (! ($importResult['ok'] ?? false)) {
                if ($rollbackAllowed && $safetyBackupPath) {
                    $rollbackResult = $this->attemptRollbackRestore($safetyBackupPath);

                    return $this->formatRestoreFailureWithRollback(
                        (string) ($importResult['message'] ?? 'La restauración principal falló.'),
                        $rollbackResult
                    );
                }

                return [
                    'ok' => false,
                    'message' => (string) ($importResult['message'] ?? 'No se pudo completar la restauración.'),
                ];
            }

            registrarAuditoria('UPDATE', 'backups', null, ($isRollback ? 'Se ejecutó rollback automático usando el backup: ' : 'Se restauró el backup: ').basename($path));

            Log::info('Restauración de backup completada correctamente.', [
                'path' => $path,
                'mode' => $isRollback ? 'rollback' : 'restore',
                'duration_seconds' => $this->elapsedSeconds($startedAt),
            ]);

            return [
                'ok' => true,
                'message' => $isRollback
                    ? 'Rollback completado correctamente. Se restauró el estado anterior de la base de datos.'
                    : 'Restauración completada. Base de datos actualizada con éxito.',
            ];
        } catch (\Throwable $exception) {
            Log::error('Error crítico al restaurar backup.', [
                'path' => $path,
                'mode' => $isRollback ? 'rollback' : 'restore',
                'error' => $exception->getMessage(),
            ]);

            $message = $this->formatFailureMessage(
                $exception,
                $isRollback ? 'No se pudo completar el rollback automático' : 'Ocurrió un error en el servidor durante la restauración'
            );

            if ($rollbackAllowed && $importStarted && $safetyBackupPath) {
                $rollbackResult = $this->attemptRollbackRestore($safetyBackupPath);

                return $this->formatRestoreFailureWithRollback($message, $rollbackResult);
            }

            return ['ok' => false, 'message' => $message];
        } finally {
            $this->cleanupRestoreFiles($tempZipPath, $extractPath);
            $this->cleanupBackupTemporaryArtifacts();

            Log::info('Fin del proceso de restauración.', [
                'path' => $path,
                'mode' => $isRollback ? 'rollback' : 'restore',
                'duration_seconds' => $this->elapsedSeconds($startedAt),
            ]);
        }
    }

    /**
     * @return array{ok: bool, message: string, backup_path?: string}
     */
    protected function createSafetyBackupSnapshot(): array
    {
        $existingPaths = $this->listBackups()->pluck('path')->all();
        $result = $this->runBackupSync();

        if (! ($result['ok'] ?? false)) {
            return [
                'ok' => false,
                'message' => (string) ($result['message'] ?? 'No se pudo generar el backup de seguridad previo.'),
            ];
        }

        $backup = $this->listBackups()->first(function (array $backup) use ($existingPaths) {
            return ! in_array($backup['path'], $existingPaths, true);
        }) ?? $this->listBackups()->first();

        if (! $backup || empty($backup['path'])) {
            return [
                'ok' => false,
                'message' => 'El backup de seguridad previo se generó, pero no fue posible localizar el archivo resultante para un eventual rollback.',
            ];
        }

        return [
            'ok' => true,
            'message' => (string) ($result['message'] ?? 'Backup de seguridad previo generado correctamente.'),
            'backup_path' => $backup['path'],
        ];
    }

    /**
     * @return array{ok: bool, message: string}
     */
    protected function attemptRollbackRestore(string $safetyBackupPath): array
    {
        Log::warning('Iniciando rollback automático posterior a una restauración fallida.', [
            'safety_backup_path' => $safetyBackupPath,
            'disk' => $this->diskName(),
        ]);

        $result = $this->restoreBackupFromPath($safetyBackupPath, false, true);

        Log::warning('Resultado del rollback automático.', [
            'safety_backup_path' => $safetyBackupPath,
            'success' => $result['ok'] ?? false,
            'message' => $result['message'] ?? null,
        ]);

        return $result;
    }

    /**
     * @return array{ok: bool, message?: string, size_bytes?: int|null}
     */
    protected function validateRestoreSource(Filesystem $disk, string $path): array
    {
        if (! $disk->exists($path)) {
            return ['ok' => false, 'message' => 'El backup solicitado no existe en Google Drive.'];
        }

        try {
            $size = null;
            try {
                $size = (int) $disk->size($path);
            } catch (\Throwable) {
                $size = null;
            }

            if ($size !== null && $size <= 0) {
                return ['ok' => false, 'message' => 'El archivo ZIP del backup está vacío o incompleto.'];
            }

            $stream = method_exists($disk, 'readStream') ? $disk->readStream($path) : false;
            if ($stream !== false && is_resource($stream)) {
                fclose($stream);
            }

            return ['ok' => true, 'size_bytes' => $size];
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'message' => $this->formatFailureMessage($exception, 'No se pudo leer el archivo ZIP del backup'),
            ];
        }
    }

    /**
     * @return array{zip: string, extract: string}
     */
    protected function createRestoreWorkspace(): array
    {
        $workspace = storage_path('app/backup-temp/restore-'.now()->format('Ymd-His-').Str::lower(Str::random(6)));
        $extractPath = $workspace.DIRECTORY_SEPARATOR.'extracted';
        $tempZipPath = $workspace.DIRECTORY_SEPARATOR.'archive.zip';

        if (is_dir($workspace)) {
            $this->files->deleteDirectory($workspace);
        }

        $this->files->ensureDirectoryExists($extractPath);

        return [
            'zip' => $tempZipPath,
            'extract' => $extractPath,
        ];
    }

    /**
     * @return array{ok: bool, message?: string}
     */
    protected function downloadBackupToTempPath(Filesystem $disk, string $path, string $tempZipPath): array
    {
        try {
            $this->files->ensureDirectoryExists(dirname($tempZipPath));

            $inputStream = method_exists($disk, 'readStream') ? $disk->readStream($path) : false;
            if ($inputStream !== false && is_resource($inputStream)) {
                $outputStream = fopen($tempZipPath, 'wb');
                if ($outputStream === false) {
                    fclose($inputStream);

                    return ['ok' => false, 'message' => 'No se pudo crear el archivo temporal local para la restauración.'];
                }

                stream_copy_to_stream($inputStream, $outputStream);
                fclose($inputStream);
                fclose($outputStream);
            } else {
                $contents = $disk->get($path);
                file_put_contents($tempZipPath, $contents);
            }

            if (! file_exists($tempZipPath) || filesize($tempZipPath) <= 0) {
                return ['ok' => false, 'message' => 'El ZIP del backup no pudo descargarse correctamente o llegó vacío.'];
            }

            return ['ok' => true];
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'message' => $this->formatFailureMessage($exception, 'No se pudo descargar el ZIP del backup'),
            ];
        }
    }

    protected function extractSqlDumpFromArchive(string $tempZipPath, string $extractPath): string
    {
        $zip = new ZipArchive();
        $status = $zip->open($tempZipPath);

        if ($status !== true) {
            throw new \RuntimeException($this->zipOpenErrorMessage($status));
        }

        try {
            if ($zip->numFiles < 1) {
                throw new \RuntimeException('El archivo ZIP del backup no contiene archivos utilizables.');
            }

            $password = (string) (env('BACKUP_ARCHIVE_PASSWORD') ?: config('backup.backup.password', ''));
            if ($password !== '') {
                $zip->setPassword($password);
            }

            for ($index = 0; $index < $zip->numFiles; $index++) {
                $entryName = $zip->getNameIndex($index);
                if (! is_string($entryName) || ! str_ends_with(strtolower($entryName), '.sql')) {
                    continue;
                }

                $entryStats = $zip->statIndex($index) ?: [];
                $entrySize = (int) ($entryStats['size'] ?? 0);
                if ($entrySize <= 0) {
                    throw new \RuntimeException('El archivo .sql contenido en el backup está vacío y no se puede restaurar.');
                }

                $content = $zip->getFromIndex($index);
                if ($content === false) {
                    $zipRequiresPassword = $this->zipEntryAppearsEncrypted($zip, $index);

                    if ($zipRequiresPassword && $password === '') {
                        throw new \RuntimeException('El backup requiere contraseña y `BACKUP_ARCHIVE_PASSWORD` no está configurado en el entorno.');
                    }

                    if ($zipRequiresPassword) {
                        throw new \RuntimeException('La contraseña configurada para `BACKUP_ARCHIVE_PASSWORD` es incorrecta o el ZIP no pudo descifrarse.');
                    }

                    throw new \RuntimeException('No se pudo leer el archivo .sql dentro del ZIP. El backup podría estar corrupto.');
                }

                $sqlFile = $extractPath.DIRECTORY_SEPARATOR.basename($entryName);
                file_put_contents($sqlFile, $content);

                if (! file_exists($sqlFile) || filesize($sqlFile) <= 0) {
                    throw new \RuntimeException('El archivo .sql extraído del backup está vacío y la restauración fue cancelada.');
                }

                return $sqlFile;
            }
        } finally {
            $zip->close();
        }

        throw new \RuntimeException('El backup no contiene ningún archivo .sql válido para restaurar la base de datos.');
    }

    private function zipOpenErrorMessage(int|bool $status): string
    {
        return match ($status) {
            ZipArchive::ER_NOZIP => 'El archivo ZIP del backup está corrupto o no tiene un formato válido.',
            ZipArchive::ER_INCONS => 'El archivo ZIP del backup está inconsistente o dañado.',
            ZipArchive::ER_CRC => 'El archivo ZIP del backup está corrupto (CRC inválido).',
            ZipArchive::ER_MEMORY => 'No hay memoria suficiente para abrir el ZIP del backup.',
            default => 'No se pudo abrir o leer el archivo ZIP del backup.',
        };
    }

    private function zipEntryAppearsEncrypted(ZipArchive $zip, int $index): bool
    {
        $stats = $zip->statIndex($index) ?: [];
        $method = $stats['encryption_method'] ?? ZipArchive::EM_NONE;

        return (int) $method !== ZipArchive::EM_NONE;
    }

    /**
     * @return array{ok: bool, message: string, output?: string}
     */
    protected function importSqlDump(string $sqlFile): array
    {
        if (! file_exists($sqlFile)) {
            return ['ok' => false, 'message' => 'No se encontró el archivo .sql extraído del backup para iniciar la restauración.'];
        }

        if (filesize($sqlFile) <= 0) {
            return ['ok' => false, 'message' => 'El archivo .sql del backup está vacío y la restauración fue cancelada.'];
        }

        $dbName = trim((string) config('database.connections.mysql.database', ''));
        $dbUser = (string) config('database.connections.mysql.username', '');
        $dbPass = (string) config('database.connections.mysql.password', '');
        $dbHost = (string) config('database.connections.mysql.host', '127.0.0.1');
        $dbPort = (int) config('database.connections.mysql.port', 3306);
        $mysqlPath = $this->resolveMysqlClientPath();
        $tempDir = $this->prepareWritableTempDirectory();

        if ($dbName === '') {
            return ['ok' => false, 'message' => 'No hay una base de datos MySQL configurada en la conexión `mysql`.'];
        }

        if (DIRECTORY_SEPARATOR === '\\' && $mysqlPath !== 'mysql' && ! file_exists($mysqlPath)) {
            return ['ok' => false, 'message' => 'No se encontró el ejecutable `mysql.exe` configurado en `MYSQL_CLIENT_PATH`.'];
        }

        if (DIRECTORY_SEPARATOR === '\\') {
            $passwordParam = trim($dbPass) !== ''
                ? ' --password='.$this->quoteWindowsPath($dbPass)
                : '';

            $baseCommand = sprintf(
                '%s --protocol=tcp --host=%s --port=%d --user=%s%s %s < %s',
                $this->quoteWindowsPath($mysqlPath),
                $this->quoteWindowsPath($dbHost),
                $dbPort,
                $this->quoteWindowsPath($dbUser),
                $passwordParam,
                $this->quoteWindowsPath($dbName),
                $this->quoteWindowsPath($sqlFile)
            );
        } else {
            $passwordParam = trim($dbPass) !== ''
                ? ' --password='.$this->quoteShellArgument($dbPass)
                : '';

            $baseCommand = sprintf(
                '%s --protocol=tcp --host=%s --port=%d --user=%s%s %s < %s',
                $this->quoteShellArgument($mysqlPath),
                $this->quoteShellArgument($dbHost),
                $dbPort,
                $this->quoteShellArgument($dbUser),
                $passwordParam,
                $this->quoteShellArgument($dbName),
                $this->quoteShellArgument($sqlFile)
            );
        }

        [$exitCode, $output] = $this->executeShellCommand($baseCommand, $tempDir, $this->processTimeout(), 'mysql-import');

        if ($exitCode !== 0) {
            Log::error('Restauración de DB fallida.', [
                'command' => $baseCommand,
                'output' => $output,
                'exit_code' => $exitCode,
                'mysql_path' => $mysqlPath,
                'db_name' => $dbName,
                'db_user' => $dbUser,
                'db_host' => $dbHost,
                'db_port' => $dbPort,
            ]);

            return [
                'ok' => false,
                'message' => $this->messageFromRestoreCommandFailure($exitCode, $output),
                'output' => $output,
            ];
        }

        return [
            'ok' => true,
            'message' => 'Base de datos importada correctamente.',
            'output' => $output,
        ];
    }

    private function resolveMysqlClientPath(): string
    {
        $mysqlPath = (string) env('MYSQL_CLIENT_PATH', '');
        if ($mysqlPath !== '') {
            return $mysqlPath;
        }

        $dumpPath = (string) env('MYSQLDUMP_PATH', '');
        if ($dumpPath !== '') {
            return str_ireplace(['mysqldump.exe', 'mysqldump'], ['mysql.exe', 'mysql'], $dumpPath);
        }

        return 'mysql';
    }

    private function messageFromRestoreCommandFailure(int $exitCode, string $output): string
    {
        if (Str::contains($output, ['ERROR 1045', 'Access denied'])) {
            return 'No se pudo importar la base de datos porque MySQL rechazó las credenciales configuradas. Verifica `DB_USERNAME` y `DB_PASSWORD` en `.env`.';
        }

        if (Str::contains($output, ['ERROR 1049', 'Unknown database'])) {
            return 'No se pudo importar la base de datos porque la base configurada en `DB_DATABASE` no existe en MySQL.';
        }

        if (Str::contains($output, ['ERROR 1064', 'You have an error in your SQL syntax'])) {
            return 'El archivo SQL del backup contiene un error de sintaxis o está dañado, por lo que la restauración fue cancelada.';
        }

        if (Str::contains($output, ['is not recognized as an internal or external command', 'No such file or directory', 'The system cannot find the file specified'])) {
            return 'No se pudo ejecutar el cliente `mysql`. Verifica la ruta configurada en `MYSQL_CLIENT_PATH` o `MYSQLDUMP_PATH`.';
        }

        if (Str::contains($output, ['ERROR at line', 'Lost connection', 'Can\'t create/write to file'])) {
            return 'La importación SQL falló durante la restauración. Revisa el archivo del backup y el log para más detalle técnico.';
        }

        return 'No se pudo importar la base de datos (Error '.$exitCode.'). Revisa `storage/logs/laravel.log` para más detalles.';
    }

    /**
     * @param array{ok?: bool, message?: string} $rollbackResult
     * @return array{ok: bool, message: string}
     */
    private function formatRestoreFailureWithRollback(string $primaryMessage, array $rollbackResult): array
    {
        if (($rollbackResult['ok'] ?? false) === true) {
            return [
                'ok' => false,
                'message' => trim($primaryMessage).' Se ejecutó un rollback automático y el estado anterior fue recuperado correctamente.',
            ];
        }

        return [
            'ok' => false,
            'message' => trim($primaryMessage).' Además, el rollback automático también falló: '.($rollbackResult['message'] ?? 'sin detalle adicional').'.',
        ];
    }

    private function cleanupRestoreFiles(string $zipPath, string $dirPath): void
    {
        $workspace = $zipPath !== '' ? dirname($zipPath) : '';

        if ($zipPath !== '' && file_exists($zipPath)) {
            @unlink($zipPath);
        }

        if ($dirPath !== '' && is_dir($dirPath)) {
            $this->files->deleteDirectory($dirPath);
        }

        if ($workspace !== '' && is_dir($workspace)) {
            $this->files->deleteDirectory($workspace);
        }
    }
}
