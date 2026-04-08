<?php

namespace Tests\Feature\Admin;

use App\Models\BackupSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BackupModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('administrador', 'web');
        Role::findOrCreate('recepcion', 'web');
    }

    private function actingAsAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('administrador');

        $this->actingAs($user);

        return $user;
    }

    /** @test */
    public function administrador_puede_ver_el_modulo_de_backups()
    {
        Storage::fake('google');
        Storage::disk('google')->put('backup-2026-04-06.zip', 'contenido');

        BackupSetting::query()->create([
            'frequency' => 'weekly',
            'last_status' => 'Correcto',
            'last_run_at' => now(),
        ]);

        $this->actingAsAdmin();

        $response = $this->get(route('admin.backups.index'));

        $response->assertOk()
            ->assertSee('Gestión de Backups')
            ->assertSee('Google Drive')
            ->assertSee('backup-2026-04-06.zip');
    }

    /** @test */
    public function usuario_sin_rol_administrador_no_puede_entrar_al_modulo()
    {
        $user = User::factory()->create();
        $user->assignRole('recepcion');

        $this->actingAs($user)
            ->get(route('admin.backups.index'))
            ->assertForbidden();
    }

    /** @test */
    public function modulo_sigue_cargando_si_google_drive_no_esta_disponible()
    {
        $this->actingAsAdmin();

        Storage::shouldReceive('disk')
            ->once()
            ->with('google')
            ->andThrow(new RuntimeException('Token has been expired or revoked.'));

        $this->get(route('admin.backups.index'))
            ->assertOk()
            ->assertSee('Gestión de Backups')
            ->assertSee('Google Drive falló', false);
    }

    /** @test */
    public function administrador_puede_actualizar_la_programacion_de_backups()
    {
        $this->actingAsAdmin();

        $this->put(route('admin.backups.schedule'), [
            'frequency' => 'monthly',
        ])->assertRedirect(route('admin.backups.index'));

        $this->assertDatabaseHas('backup_settings', [
            'frequency' => 'monthly',
        ]);
    }

    /** @test */
    public function administrador_puede_eliminar_un_backup_del_disco_google()
    {
        Storage::fake('google');
        Storage::disk('google')->put('backup-eliminar.zip', 'contenido');

        $this->actingAsAdmin();

        $this->delete(route('admin.backups.destroy'), [
            'path' => base64_encode('backup-eliminar.zip'),
        ])->assertRedirect(route('admin.backups.index'));

        Storage::disk('google')->assertMissing('backup-eliminar.zip');
    }
}
