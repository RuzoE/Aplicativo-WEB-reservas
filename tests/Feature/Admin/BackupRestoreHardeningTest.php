<?php

namespace Tests\Feature\Admin;

use App\Services\Backups\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;
use ZipArchive;

class BackupRestoreHardeningTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function devuelve_un_error_claro_si_el_zip_esta_corrupto(): void
    {
        Storage::fake('google');
        Storage::disk('google')->put('backups/corrupto.zip', 'esto no es un zip valido');

        $service = Mockery::mock(BackupService::class, [app(\Illuminate\Filesystem\Filesystem::class)])->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('createSafetyBackupSnapshot')
            ->once()
            ->andReturn(['ok' => true, 'backup_path' => 'backups/safety.zip', 'message' => 'ok']);

        $result = $service->restoreBackup(base64_encode('backups/corrupto.zip'));

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('zip', mb_strtolower($result['message']));
    }

    /** @test */
    public function devuelve_un_error_claro_si_el_sql_extraido_esta_vacio(): void
    {
        Storage::fake('google');
        $path = $this->crearZipEnDisco('backups/sql-vacio.zip', [
            'db-dumps/backup.sql' => '',
        ]);

        $service = Mockery::mock(BackupService::class, [app(\Illuminate\Filesystem\Filesystem::class)])->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('createSafetyBackupSnapshot')
            ->once()
            ->andReturn(['ok' => true, 'backup_path' => 'backups/safety.zip', 'message' => 'ok']);
        $service->shouldNotReceive('importSqlDump');

        $result = $service->restoreBackup(base64_encode($path));

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('vac', mb_strtolower($result['message']));
    }

    /** @test */
    public function intenta_rollback_automatico_si_falla_la_restauracion_principal(): void
    {
        Storage::fake('google');
        $path = $this->crearZipEnDisco('backups/valido.zip', [
            'db-dumps/backup.sql' => 'CREATE TABLE prueba(id INT);',
        ]);

        $service = Mockery::mock(BackupService::class, [app(\Illuminate\Filesystem\Filesystem::class)])->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('createSafetyBackupSnapshot')
            ->once()
            ->andReturn(['ok' => true, 'backup_path' => 'backups/safety-before-restore.zip', 'message' => 'ok']);
        $service->shouldReceive('importSqlDump')
            ->once()
            ->andReturn(['ok' => false, 'message' => 'No se pudo importar la base de datos porque MySQL rechazó las credenciales configuradas.']);
        $service->shouldReceive('attemptRollbackRestore')
            ->once()
            ->with('backups/safety-before-restore.zip')
            ->andReturn(['ok' => true, 'message' => 'Rollback ejecutado correctamente.']);

        $result = $service->restoreBackup(base64_encode($path));

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('rollback', mb_strtolower($result['message']));
    }

    private function crearZipEnDisco(string $diskPath, array $entries): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'backup-test-');

        $zip = new ZipArchive();
        $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($entries as $name => $content) {
            $zip->addFromString($name, $content);
        }

        $zip->close();

        Storage::disk('google')->put($diskPath, file_get_contents($tempFile));
        @unlink($tempFile);

        return $diskPath;
    }
}
