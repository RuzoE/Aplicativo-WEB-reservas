<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\CompraProducto;
use App\Models\Guest;
use App\Models\MaintenanceOrder;
use App\Models\MinibarProduct;
use App\Models\Order;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Recopila todos los datos del informe ejecutivo.
     */
    private function buildReportData(): array
    {
        $now   = Carbon::now();
        $today = $now->toDateString();

        // ── HABITACIONES ────────────────────────────────────────────────
        $totalHabitaciones  = (int) Room::sum('total_room');
        $habitacionesEnMant = (int) MaintenanceOrder::active()->distinct('room_id')->count('room_id');
        $huespedesEnCasa    = (int) Stay::whereNull('actual_check_out_at')
                                       ->whereNotNull('actual_check_in_at')->count();
        $habitacionesOcupadas   = $huespedesEnCasa;
        $habitacionesDisponibles = max(0, $totalHabitaciones - $habitacionesOcupadas - $habitacionesEnMant);
        $pctOcupacion = $totalHabitaciones > 0
            ? round(($habitacionesOcupadas / $totalHabitaciones) * 100, 1)
            : 0;

        $distribucionPorTipo = RoomType::leftJoin('rooms', 'room_types.id', '=', 'rooms.room_type_id')
            ->select('room_types.name', DB::raw('COALESCE(SUM(rooms.total_room),0) as total'))
            ->groupBy('room_types.id', 'room_types.name')
            ->get();

        // ── RESERVAS ─────────────────────────────────────────────────────
        $totalReservas     = Order::count();
        $reservasActivas   = Order::whereIn('status', [
            Order::STATUS_ANTICIPO_PAGADO,
            Order::STATUS_RESERVA_PREVIA,
            Order::STATUS_OCUPADA,
        ])->count();
        $reservasPendientes = Order::where('status', Order::STATUS_PENDIENTE)->count();
        $reservasFuturas   = Order::whereDoesntHave('stays')
                                  ->whereDate('check_in', '>', $today)->count();
        $reservasCanceladas = Order::where('status', Order::STATUS_FINALIZADA)->count();
        $checkinHoy  = Stay::whereDate('actual_check_in_at', $today)->count();
        $checkoutHoy = Stay::whereDate('actual_check_out_at', $today)->count();

        $reservasRecientes = Order::with('roomType')
            ->orderByDesc('created_at')->limit(6)->get();

        // ── RECEPCIÓN ────────────────────────────────────────────────────
        $huespedesActuales = Stay::with(['guest', 'room.roomtype'])
            ->whereNull('actual_check_out_at')
            ->whereNotNull('actual_check_in_at')
            ->orderByDesc('actual_check_in_at')
            ->limit(8)->get();

        $entradasRecientes = Stay::with(['guest', 'room.roomtype'])
            ->whereNotNull('actual_check_in_at')
            ->orderByDesc('actual_check_in_at')
            ->limit(5)->get();

        $salidasRecientes = Stay::with(['guest', 'room.roomtype'])
            ->whereNotNull('actual_check_out_at')
            ->orderByDesc('actual_check_out_at')
            ->limit(5)->get();

        // ── MANTENIMIENTO ────────────────────────────────────────────────
        $mantPendiente  = MaintenanceOrder::where('status', 'asignada')->count();
        $mantProceso    = MaintenanceOrder::where('status', 'en_proceso')->count();
        $mantCompletado = MaintenanceOrder::where('status', 'completada')->count();
        $totalMantOrdenes = $mantPendiente + $mantProceso + $mantCompletado;

        $mantUrgente = MaintenanceOrder::where('priority', 'urgente')
                          ->where('status', '!=', 'completada')->count();
        $mantBaja    = MaintenanceOrder::where('priority', 'baja')
                          ->where('status', '!=', 'completada')->count();
        $mantNormal  = MaintenanceOrder::where('priority', 'normal')
                          ->where('status', '!=', 'completada')->count();

        // Tiempo promedio de resolución (días)
        $tiempoPromedioMant = MaintenanceOrder::where('status', 'completada')
            ->whereNotNull('completed_at')
            ->selectRaw('AVG(DATEDIFF(completed_at, created_at)) as promedio')
            ->value('promedio');
        $tiempoPromedioMant = $tiempoPromedioMant ? round((float)$tiempoPromedioMant, 1) : 0;

        $ordenesMantRecientes = MaintenanceOrder::with('room')
            ->orderByDesc('created_at')->limit(6)->get();

        // ── MINIBAR ──────────────────────────────────────────────────────
        $totalVentasMinibar  = Compra::count();
        $ingresosMinibar     = (float) (Compra::sum('total') ?? 0);
        $ventasHoy           = Compra::whereDate('created_at', $today)->count();
        $ingresosHoy         = (float) (Compra::whereDate('created_at', $today)->sum('total') ?? 0);

        $topProductos = DB::table('compra_producto')
            ->join('bebidas', 'compra_producto.minibar_product_id', '=', 'bebidas.id')
            ->select('bebidas.nombre', DB::raw('SUM(compra_producto.cantidad) as total_qty'),
                     DB::raw('SUM(compra_producto.cantidad * compra_producto.precio_unitario) as total_ingreso'))
            ->groupBy('bebidas.id', 'bebidas.nombre')
            ->orderByDesc('total_qty')
            ->limit(5)->get();

        $ventasRecientes = Compra::with('user')
            ->orderByDesc('created_at')->limit(6)->get();

        // ── INVENTARIO ───────────────────────────────────────────────────
        $totalProductos     = MinibarProduct::count();
        $productosBajoStock = MinibarProduct::where('stock', '<=', 5)->count();
        $valorInventario    = (float) MinibarProduct::selectRaw('SUM(precio * stock) as total')->value('total') ?? 0;
        $inventarioDetalle  = MinibarProduct::orderBy('stock')->limit(8)->get();

        // ── PERSONAL ─────────────────────────────────────────────────────
        $rolesOperativos = ['administrador', 'recepcion', 'minibar', 'mantenimiento', 'reservas'];
        $totalEmpleados = User::whereHas('roles', fn($q) => $q->whereIn('name', $rolesOperativos))->count();
        $empleadosPorRol = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', $rolesOperativos)
            ->select('roles.name as rol', DB::raw('COUNT(*) as total'))
            ->groupBy('roles.id', 'roles.name')
            ->get();

        // ── KPIs EJECUTIVOS ───────────────────────────────────────────────
        $totalIngresosEstancias = Stay::whereNotNull('actual_check_out_at')
            ->selectRaw('SUM(daily_rate * GREATEST(DATEDIFF(COALESCE(actual_check_out_at, NOW()), actual_check_in_at), 1)) as total')
            ->value('total') ?? 0;
        $totalIngresos = round((float)$totalIngresosEstancias + $ingresosMinibar, 2);

        return compact(
            'now', 'today',
            // Habitaciones
            'totalHabitaciones', 'habitacionesOcupadas', 'habitacionesDisponibles',
            'habitacionesEnMant', 'pctOcupacion', 'distribucionPorTipo',
            // Reservas
            'totalReservas', 'reservasActivas', 'reservasPendientes', 'reservasFuturas',
            'reservasCanceladas', 'checkinHoy', 'checkoutHoy', 'reservasRecientes',
            // Recepción
            'huespedesEnCasa', 'huespedesActuales', 'entradasRecientes', 'salidasRecientes',
            // Mantenimiento
            'mantPendiente', 'mantProceso', 'mantCompletado', 'totalMantOrdenes',
            'mantUrgente', 'mantBaja', 'mantNormal', 'tiempoPromedioMant',
            'ordenesMantRecientes',
            // Minibar
            'totalVentasMinibar', 'ingresosMinibar', 'ventasHoy', 'ingresosHoy',
            'topProductos', 'ventasRecientes',
            // Inventario
            'totalProductos', 'productosBajoStock', 'valorInventario', 'inventarioDetalle',
            // Personal
            'totalEmpleados', 'empleadosPorRol',
            // KPIs
            'totalIngresos'
        );
    }

    /**
     * Vista web del informe ejecutivo.
     */
    public function preview()
    {
        $data = $this->buildReportData();
        $data['isPdf'] = false;
        return view('admin.report.preview', $data);
    }

    /**
     * Descarga el informe como PDF.
     */
    public function download()
    {
        $data = $this->buildReportData();
        $data['isPdf'] = true;
        $now  = $data['now'];

        $pdf = Pdf::loadView('admin.report.pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('informe-general-' . $now->format('Y-m-d') . '.pdf');
    }
}
