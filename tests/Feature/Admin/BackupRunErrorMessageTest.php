<?php

namespace Tests\Feature\Admin;

use App\Models\BackupSetting;
use App\Services\Backups\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class BackupRunErrorMessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function muestra_un_mensaje_claro_cuando_falla_el_dump_de_mysql(): void
    {
        BackupSetting::current();

        $service = $this->mockBackupServiceWithResponses([
            [1, "mysqldump: Got error: 2004: Can't create TCP/IP socket (10106) when trying to connect"],
            [1, "mysqldump: Got error: 2004: Can't create TCP/IP socket (10106) when trying to connect"],
        ]);

        $result = $service->runBackup('manual');

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('MySQL', $result['message']);
        $this->assertStringContainsString('10106', $result['message']);
    }

    /** @test */
    public function reintenta_automaticamente_si_mysqldump_falla_por_un_error_transitorio_de_socket(): void
    {
        BackupSetting::current();

        $service = $this->mockBackupServiceWithResponses([
            [1, "mysqldump: Got error: 2004: Can't create TCP/IP socket (10106) when trying to connect"],
            [0, 'Backup completed!'],
        ]);

        $result = $service->runBackup('manual');

        $this->assertTrue($result['ok']);
        $this->assertSame('Backup subido correctamente a Google Drive.', $result['message']);
    }

    /** @test */
    public function muestra_un_mensaje_claro_si_la_subida_a_drive_se_queda_sin_memoria(): void
    {
        BackupSetting::current();

        $service = Mockery::mock(BackupService::class, [app(\Illuminate\Filesystem\Filesystem::class)])->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('prepareWritableTempDirectory')->zeroOrMoreTimes()->andReturn(storage_path('app/backup-temp/system-tmp'));
        $service->shouldReceive('executeBackupProcess')
            ->once()
            ->andThrow(new RuntimeException('Allowed memory size of 134217728 bytes exhausted'));

        $result = $service->runBackup('manual');

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

        $result = app(BackupService::class)->startManualBackup('manual');

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('Ya hay un backup en proceso', $result['message']);
    }

    /**
     * @param array<int, array{0:int,1:string}> $responses
     */
    private function mockBackupServiceWithResponses(array $responses): BackupService
    {
        $service = Mockery::mock(BackupService::class, [app(\Illuminate\Filesystem\Filesystem::class)])->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('prepareWritableTempDirectory')->zeroOrMoreTimes()->andReturn(storage_path('app/backup-temp/system-tmp'));
        $service->shouldReceive('executeBackupProcess')->times(count($responses))->andReturn(...$responses);

        return $service;
    }
}
