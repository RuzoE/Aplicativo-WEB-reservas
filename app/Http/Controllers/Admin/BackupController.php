<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBackupScheduleRequest;
use App\Services\Backups\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function __construct(private readonly BackupService $backupService)
    {
    }

    public function index()
    {
        return view('admin.backups.index', $this->backupService->getDashboardData());
    }

    public function status(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->backupService->getDashboardData());
    }

    public function generate(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        // Forzar tiempo de ejecución alto para backups síncronos
        set_time_limit(300);

        $result = $this->backupService->runBackupSync();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()
            ->route('admin.backups.index')
            ->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function download(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string'],
        ]);

        return $this->backupService->downloadBackup($validated['path']);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string'],
        ]);

        $deleted = $this->backupService->deleteBackup($validated['path']);

        return redirect()
            ->route('admin.backups.index')
            ->with($deleted ? 'success' : 'error', $deleted ? 'Backup eliminado correctamente.' : 'No se pudo eliminar el backup seleccionado.');
    }

    public function updateSchedule(UpdateBackupScheduleRequest $request): RedirectResponse
    {
        $this->backupService->updateSchedule($request->validated('frequency'));

        return redirect()
            ->route('admin.backups.index')
            ->with('success', 'Frecuencia automática de backups actualizada correctamente.');
    }

    public function restore(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string'],
        ]);

        $result = $this->backupService->restoreBackup($validated['path']);

        if ($result['ok']) {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            $result['requires_relogin'] = true;
            $result['redirect'] = route('login', [], false);
            $result['message'] = ($result['message'] ?? 'Restauración completada.')
                .' Por seguridad, vuelve a iniciar sesión para recargar usuarios, roles y permisos de la base restaurada.';
        }

        return response()->json($result, $result['ok'] ? 200 : 400);
    }

    public function resetStatus(): RedirectResponse
    {
        $this->backupService->resetStaleState();

        return redirect()
            ->route('admin.backups.index')
            ->with('message', 'El estado del backup ha sido reiniciado correctamente.');
    }
}
