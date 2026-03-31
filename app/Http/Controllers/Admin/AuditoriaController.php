<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => ['nullable', 'integer', 'exists:users,id'],
            'modulo' => ['nullable', 'string', 'max:50'],
            'accion' => ['nullable', 'string', 'max:20'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'in:10,20,50'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 20);

        $filteredBaseQuery = Auditoria::query()
            ->when(!empty($validated['usuario_id']), function ($query) use ($validated) {
                $query->where('usuario_id', (int) $validated['usuario_id']);
            })
            ->when(!empty($validated['modulo']), function ($query) use ($validated) {
                $query->where('modulo', (string) $validated['modulo']);
            })
            ->when(!empty($validated['accion']), function ($query) use ($validated) {
                $query->where('accion', strtoupper((string) $validated['accion']));
            })
            ->when(!empty($validated['desde']), function ($query) use ($validated) {
                $query->whereDate('created_at', '>=', (string) $validated['desde']);
            })
            ->when(!empty($validated['hasta']), function ($query) use ($validated) {
                $query->whereDate('created_at', '<=', (string) $validated['hasta']);
            });

        $auditorias = (clone $filteredBaseQuery)
            ->with(['usuario:id,name,last_name,email'])
            ->select(['id', 'usuario_id', 'accion', 'modulo', 'registro_id', 'descripcion', 'created_at'])
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $usuarios = User::query()
            ->select(['id', 'name', 'last_name', 'email'])
            ->orderBy('name')
            ->limit(500)
            ->get();

        $modulos = ['reservas', 'habitaciones', 'mantenimiento', 'minibar', 'usuarios', 'recepcion'];
        $acciones = ['CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'CHECK_IN', 'CHECK_OUT', 'CANCEL'];

        $eventosDestacados = (clone $filteredBaseQuery)
            ->with(['usuario:id,name,last_name,email'])
            ->select(['id', 'usuario_id', 'accion', 'modulo', 'descripcion', 'created_at'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $totalRegistros = Auditoria::count();
        $totalFiltrados = (clone $filteredBaseQuery)->count();
        $accionesHoy = (clone $filteredBaseQuery)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $moduloMasActivo = (clone $filteredBaseQuery)
            ->select('modulo', DB::raw('COUNT(*) as total'))
            ->groupBy('modulo')
            ->orderByDesc('total')
            ->first();

        $usuarioMasActivoQuery = (clone $filteredBaseQuery)
            ->whereNotNull('usuario_id')
            ->select('usuario_id', DB::raw('COUNT(*) as total'))
            ->groupBy('usuario_id')
            ->orderByDesc('total')
            ->first();

        $usuarioMasActivo = null;
        if ($usuarioMasActivoQuery) {
            $usuario = User::query()
                ->select(['id', 'name', 'last_name', 'email'])
                ->find($usuarioMasActivoQuery->usuario_id);

            if ($usuario) {
                $usuarioMasActivo = [
                    'usuario' => $usuario,
                    'total' => (int) $usuarioMasActivoQuery->total,
                ];
            }
        }

        return view('admin.auditorias.index', compact(
            'auditorias',
            'usuarios',
            'modulos',
            'acciones',
            'eventosDestacados',
            'totalRegistros',
            'totalFiltrados',
            'accionesHoy',
            'moduloMasActivo',
            'usuarioMasActivo',
            'perPage'
        ));
    }
}
