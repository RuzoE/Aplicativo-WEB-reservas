<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe General de Operaciones - Hotel Oasis</title>
    <style>
        @page {
            margin: 20px 22px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1e293b;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.35;
            background: #ffffff;
        }

        .no-print {
            display: none !important;
        }

        .header {
            background: #1e3a5f;
            color: #ffffff;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 14px;
        }

        .header-title {
            font-size: 21px;
            font-weight: 700;
            margin: 0;
        }

        .header-subtitle {
            font-size: 12px;
            margin-top: 2px;
            color: #dbeafe;
        }

        .header-date {
            margin-top: 8px;
            font-size: 10px;
            color: #cbd5e1;
        }

        .section {
            border: 1px solid #d7e2ee;
            border-radius: 8px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .section-title {
            margin: 0;
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 700;
            color: #1e3a5f;
            background: #f3f7fb;
            border-bottom: 1px solid #d7e2ee;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #e2e8f0;
            padding: 6px 7px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background: #f8fafc;
            color: #334155;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .label-col {
            width: 68%;
            color: #475569;
        }

        .value-col {
            width: 32%;
            text-align: right;
            font-weight: 700;
            color: #0f172a;
        }

        .grid-2 {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-top: 2px;
        }

        .grid-2 td {
            width: 50%;
            border: 0;
            padding: 0;
            vertical-align: top;
        }

        .kpi-block {
            border: 1px solid #d7e2ee;
            border-radius: 6px;
            padding: 8px;
            margin-bottom: 8px;
        }

        .kpi-title {
            margin: 0;
            color: #64748b;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.4px;
            font-weight: 700;
        }

        .kpi-value {
            margin: 2px 0 0;
            font-size: 16px;
            font-weight: 700;
            color: #1e3a5f;
        }

        .kpi-sub {
            margin-top: 1px;
            color: #64748b;
            font-size: 9px;
        }

        .pill {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .pill-green { background: #ecfdf5; color: #166534; border-color: #86efac; }
        .pill-yellow { background: #fefce8; color: #854d0e; border-color: #fcd34d; }
        .pill-red { background: #fff1f2; color: #991b1b; border-color: #fca5a5; }
        .pill-blue { background: #eff6ff; color: #1d4ed8; border-color: #93c5fd; }
        .pill-gray { background: #f8fafc; color: #475569; border-color: #cbd5e1; }

        .muted {
            color: #64748b;
            font-size: 10px;
        }

        .spacer {
            height: 3px;
        }

        .footer {
            margin-top: 10px;
            padding-top: 7px;
            border-top: 1px solid #d7e2ee;
            color: #64748b;
            font-size: 9px;
            text-align: right;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .section {
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <p class="header-title">Hotel Oasis</p>
        <p class="header-subtitle">Informe General de Operaciones</p>
        <p class="header-date">Generado: {{ $now->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h2 class="section-title">Resumen Ejecutivo</h2>
        <table class="grid-2">
            <tr>
                <td>
                    <div class="kpi-block">
                        <p class="kpi-title">% Ocupación</p>
                        <p class="kpi-value">{{ $pctOcupacion }}%</p>
                        <p class="kpi-sub">{{ $habitacionesOcupadas }}/{{ $totalHabitaciones }} habitaciones</p>
                    </div>
                    <div class="kpi-block">
                        <p class="kpi-title">Ingresos Totales (COP)</p>
                        <p class="kpi-value">$ {{ number_format($totalIngresos, 0, ',', '.') }}</p>
                        <p class="kpi-sub">Estancias + Minibar</p>
                    </div>
                    <div class="kpi-block">
                        <p class="kpi-title">Huéspedes en Casa</p>
                        <p class="kpi-value">{{ $huespedesEnCasa }}</p>
                        <p class="kpi-sub">Check-in hoy: {{ $checkinHoy }}</p>
                    </div>
                </td>
                <td>
                    <div class="kpi-block">
                        <p class="kpi-title">Habitaciones Disponibles</p>
                        <p class="kpi-value">{{ $habitacionesDisponibles }}</p>
                        <p class="kpi-sub">Ocupadas: {{ $habitacionesOcupadas }} | Mant.: {{ $habitacionesEnMant }}</p>
                    </div>
                    <div class="kpi-block">
                        <p class="kpi-title">Órdenes de Mantenimiento Activas</p>
                        <p class="kpi-value">{{ $mantPendiente + $mantProceso }}</p>
                        <p class="kpi-sub">Urgentes: {{ $mantUrgente }}</p>
                    </div>
                    <div class="kpi-block">
                        <p class="kpi-title">Ventas Minibar</p>
                        <p class="kpi-value">{{ $totalVentasMinibar }}</p>
                        <p class="kpi-sub">Ingresos minibar: $ {{ number_format($ingresosMinibar, 0, ',', '.') }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Módulo de Habitaciones</h2>
        <table>
            <tr><td class="label-col">Total de habitaciones</td><td class="value-col">{{ $totalHabitaciones }}</td></tr>
            <tr><td class="label-col">Disponibles</td><td class="value-col"><span class="pill pill-green">{{ $habitacionesDisponibles }}</span></td></tr>
            <tr><td class="label-col">Ocupadas</td><td class="value-col"><span class="pill pill-red">{{ $habitacionesOcupadas }}</span></td></tr>
            <tr><td class="label-col">En mantenimiento</td><td class="value-col"><span class="pill pill-yellow">{{ $habitacionesEnMant }}</span></td></tr>
            <tr><td class="label-col">Porcentaje de ocupación</td><td class="value-col">{{ $pctOcupacion }}%</td></tr>
        </table>
        <div class="spacer"></div>
        <table>
            <thead>
                <tr><th>Distribución por Tipo</th><th>Total</th></tr>
            </thead>
            <tbody>
                @foreach ($distribucionPorTipo as $tipo)
                    <tr>
                        <td>{{ $tipo->name }}</td>
                        <td class="value-col">{{ $tipo->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Módulo de Reservas</h2>
        <table>
            <tr><td class="label-col">Total reservas</td><td class="value-col">{{ $totalReservas }}</td></tr>
            <tr><td class="label-col">Activas</td><td class="value-col"><span class="pill pill-green">{{ $reservasActivas }}</span></td></tr>
            <tr><td class="label-col">Pendientes</td><td class="value-col"><span class="pill pill-yellow">{{ $reservasPendientes }}</span></td></tr>
            <tr><td class="label-col">Futuras</td><td class="value-col"><span class="pill pill-blue">{{ $reservasFuturas }}</span></td></tr>
            <tr><td class="label-col">Finalizadas</td><td class="value-col"><span class="pill pill-gray">{{ $reservasCanceladas }}</span></td></tr>
            <tr><td class="label-col">Check-in hoy</td><td class="value-col">{{ $checkinHoy }}</td></tr>
            <tr><td class="label-col">Check-out hoy</td><td class="value-col">{{ $checkoutHoy }}</td></tr>
        </table>
        <div class="spacer"></div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Tipo Hab.</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reservasRecientes as $r)
                    <tr>
                        <td>#{{ $r->id }}</td>
                        <td>{{ $r->nombre_cliente ?: '—' }}</td>
                        <td>{{ $r->roomType->name ?? '—' }}</td>
                        <td>{{ $r->check_in ? $r->check_in->format('d/m/Y') : '—' }}</td>
                        <td>{{ $r->check_out ? $r->check_out->format('d/m/Y') : '—' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $r->status)) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted">Sin reservas recientes</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Módulo de Recepción</h2>
        <table>
            <tr><td class="label-col">Huéspedes en casa</td><td class="value-col">{{ $huespedesEnCasa }}</td></tr>
            <tr><td class="label-col">Entradas recientes</td><td class="value-col">{{ $entradasRecientes->count() }}</td></tr>
            <tr><td class="label-col">Salidas recientes</td><td class="value-col">{{ $salidasRecientes->count() }}</td></tr>
        </table>
        <div class="spacer"></div>
        <table>
            <thead>
                <tr>
                    <th>Huésped</th>
                    <th>Hab.</th>
                    <th>Llegada</th>
                    <th>Salida Prev.</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($huespedesActuales as $s)
                    <tr>
                        <td>{{ $s->guest ? $s->guest->first_name . ' ' . $s->guest->last_name : '—' }}</td>
                        <td>{{ $s->assigned_room_number ?? '—' }}</td>
                        <td>{{ $s->actual_check_in_at ? $s->actual_check_in_at->format('d/m/Y H:i') : '—' }}</td>
                        <td>{{ $s->departure_at ? $s->departure_at->format('d/m/Y') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted">Sin huéspedes activos</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Módulo de Mantenimiento</h2>
        <table>
            <tr><td class="label-col">Órdenes pendientes</td><td class="value-col"><span class="pill pill-yellow">{{ $mantPendiente }}</span></td></tr>
            <tr><td class="label-col">Órdenes en proceso</td><td class="value-col"><span class="pill pill-blue">{{ $mantProceso }}</span></td></tr>
            <tr><td class="label-col">Órdenes completadas</td><td class="value-col"><span class="pill pill-green">{{ $mantCompletado }}</span></td></tr>
            <tr><td class="label-col">Urgentes activas</td><td class="value-col"><span class="pill pill-red">{{ $mantUrgente }}</span></td></tr>
            <tr><td class="label-col">Normal activas</td><td class="value-col">{{ $mantNormal }}</td></tr>
            <tr><td class="label-col">Baja activas</td><td class="value-col">{{ $mantBaja }}</td></tr>
            <tr><td class="label-col">Tiempo promedio resolución</td><td class="value-col">{{ $tiempoPromedioMant }} días</td></tr>
        </table>
        <div class="spacer"></div>
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Hab.</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ordenesMantRecientes as $o)
                    <tr>
                        <td>{{ \Illuminate\Support\Str::limit($o->description, 55) }}</td>
                        <td>{{ $o->room_number ?? '—' }}</td>
                        <td>{{ ucfirst($o->priority) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $o->status)) }}</td>
                        <td>{{ $o->created_at ? $o->created_at->format('d/m/Y') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted">Sin órdenes recientes</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Módulo de Minibar</h2>
        <table>
            <tr><td class="label-col">Total ventas</td><td class="value-col">{{ $totalVentasMinibar }}</td></tr>
            <tr><td class="label-col">Ingresos minibar (COP)</td><td class="value-col">$ {{ number_format($ingresosMinibar, 0, ',', '.') }}</td></tr>
            <tr><td class="label-col">Ventas hoy</td><td class="value-col">{{ $ventasHoy }}</td></tr>
            <tr><td class="label-col">Ingresos hoy (COP)</td><td class="value-col">$ {{ number_format($ingresosHoy, 0, ',', '.') }}</td></tr>
        </table>
        <div class="spacer"></div>
        <table>
            <thead>
                <tr><th>Top Productos</th><th>Unidades</th><th>Ingresos</th></tr>
            </thead>
            <tbody>
                @forelse ($topProductos as $p)
                    <tr>
                        <td>{{ $p->nombre }}</td>
                        <td class="value-col">{{ $p->total_qty }}</td>
                        <td class="value-col">$ {{ number_format($p->total_ingreso, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="muted">Sin datos de productos</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Inventario / Compras Minibar</h2>
        <table>
            <tr><td class="label-col">Total productos</td><td class="value-col">{{ $totalProductos }}</td></tr>
            <tr><td class="label-col">Productos bajo stock (<= 5)</td><td class="value-col">{{ $productosBajoStock }}</td></tr>
            <tr><td class="label-col">Valor inventario (COP)</td><td class="value-col">$ {{ number_format($valorInventario, 0, ',', '.') }}</td></tr>
        </table>
        <div class="spacer"></div>
        <table>
            <thead>
                <tr><th>Producto</th><th>Stock</th><th>Estado</th></tr>
            </thead>
            <tbody>
                @forelse ($inventarioDetalle as $prod)
                    <tr>
                        <td>{{ $prod->nombre }}</td>
                        <td class="value-col">{{ $prod->stock }}</td>
                        <td>
                            @if ($prod->stock <= 2)
                                <span class="pill pill-red">Crítico</span>
                            @elseif ($prod->stock <= 5)
                                <span class="pill pill-yellow">Bajo</span>
                            @else
                                <span class="pill pill-green">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="muted">Sin inventario</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Módulo de Personal</h2>
        <table>
            <tr><td class="label-col">Total empleados operativos</td><td class="value-col">{{ $totalEmpleados }}</td></tr>
        </table>
        <div class="spacer"></div>
        <table>
            <thead>
                <tr><th>Rol</th><th>Cantidad</th></tr>
            </thead>
            <tbody>
                @forelse ($empleadosPorRol as $ep)
                    <tr>
                        <td>{{ ucfirst($ep->rol) }}</td>
                        <td class="value-col">{{ $ep->total }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="muted">Sin datos de roles</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        Informe generado automáticamente por el sistema de gestión hotelera.
    </div>

</body>
</html>
