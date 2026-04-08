<?php

namespace Tests\Feature\Admin;

use App\Models\BackupSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;
use Tests\TestCase;

class BackupRunErrorMessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function muestra_un_mensaje_claro_cuando_falla_el_dump_de_mysql(): void
    {
        BackupSetting::current();

        Artisan::shouldReceive('call')
            ->twice()
            ->with('backup:run', ['--disable-notifications' => true])
            ->andReturn(1, 1);

        Artisan::shouldReceive('output')
            ->twice()
            ->andReturn(
                "mysqldump: Got error: 2004: Can't create TCP/IP socket (10106) when trying to connect",
                "mysqldump: Got error: 2004: Can't create TCP/IP socket (10106) when trying to connect"
            );

        $result = app(\App\Services\Backups\BackupService::class)->runBackup('manual');

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('MySQL', $result['message']);
        $this->assertStringContainsString('10106', $result['message']);
    }

    /** @test */
    public function reintenta_automaticamente_si_mysqldump_falla_por_un_error_transitorio_de_socket(): void
    {
        BackupSetting::current();

        Artisan::shouldReceive('call')
            ->twice()
            ->with('backup:run', ['--disable-notifications' => true])
            ->andReturn(1, 0);

        Artisan::shouldReceive('output')
            ->twice()
            ->andReturn(
                "mysqldump: Got error: 2004: Can't create TCP/IP socket (10106) when trying to connect",
                'Backup completed!'
            );

        $result = app(\App\Services\Backups\BackupService::class)->runBackup('manual');

        $this->assertTrue($result['ok']);
        $this->assertSame('Backup subido correctamente a Google Drive.', $result['message']);
    }

    /** @test */
    public function muestra_un_mensaje_claro_si_la_subida_a_drive_se_queda_sin_memoria(): void
    {
        BackupSetting::current();

        Artisan::shouldReceive('call')
            ->once()
            ->with('backup:run', ['--disable-notifications' => true])
            ->andThrow(new RuntimeException('Allowed memory size of 134217728 bytes exhausted'));

        $result = app(\App\Services\Backups\BackupService::class)->runBackup('manual');

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('memoria', mb_strtolower($result['message']));
        $this->assertStringContainsString('BACKUP_MEMORY_LIMIT', $result['message']);
    }

    /** @test */
    public function no_inicia_otro_backup_manual_si_ya_hay_uno_en_proceso(): void
    {
        BackupSetting::current()->update([
            'last_status' => 'En proceso',
            'last_message' => 'La generación del backup se inició en segundo plano.',
        ]);

        $result = app(\App\Services\Backups\BackupService::class)->startManualBackup('manual');

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('Ya hay un backup en proceso', $result['message']);
    }
}
