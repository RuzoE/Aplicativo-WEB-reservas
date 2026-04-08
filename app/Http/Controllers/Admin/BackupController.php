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
        // El script index--script1.js manda fetch JSON u form POST?
        // Ah, si, no verifiqué pero si es de Blade, podemos devolver JSON si es fetch.
        // Wait, voy a usar JSON normal para fetch.
        
        $validated = $request->validate([
            'path' => ['required', 'string'],
        ]);

        $result = $this->backupService->restoreBackup($validated['path']);

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
