@extends('layouts.app')

@php $adminView = true; @endphp

@section('title', 'Informe General de Operaciones')

@section('content')
<div class="report-page">

{{-- ══════════════════════════════════════════════════════════
     ENCABEZADO EJECUTIVO
══════════════════════════════════════════════════════════ --}}
<header class="report-header">
    <div class="report-header__brand">
        <div class="report-header__logo">
            <svg width="42" height="42" viewBox="0 0 42 42" fill="none">
                <rect width="42" height="42" rx="10" fill="#1e3a5f"/>
                <path d="M21 8L34 16V30H8V16L21 8Z" fill="#f59e0b" opacity=".9"/>
                <rect x="16" y="22" width="10" height="8" rx="1" fill="#1e3a5f"/>
                <rect x="18" y="14" width="6" height="6" rx="3" fill="#fff"/>
            </svg>
        </div>
        <div>
            <div class="report-header__hotel">Hotel Oasis</div>
            <div class="report-header__subtitle">Informe General de Operaciones</div>
        </div>
    </div>
    <div class="report-header__meta">
        <div class="report-header__date">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            {{ $now->format('d/m/Y H:i') }}
        </div>
        <a href="{{ route('admin.report.download') }}" class="report-header__pdf-btn" target="_blank">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Descargar PDF
        </a>
    </div>
</header>

{{-- ══════════════════════════════════════════════════════════
     RESUMEN EJECUTIVO – KPIs
══════════════════════════════════════════════════════════ --}}
<section class="report-section">
    <h2 class="section-title">
        <span class="section-title__icon section-title__icon--blue">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </span>
        Resumen Ejecutivo
    </h2>

    @php
        $ocupacionColor = $pctOcupacion >= 80 ? 'kpi--green' : ($pctOcupacion >= 40 ? 'kpi--yellow' : 'kpi--red');
        $mantColor      = $mantPendiente > 5  ? 'kpi--red'   : ($mantPendiente > 0  ? 'kpi--yellow' : 'kpi--green');
    @endphp

    <div class="kpi-grid">
        <div class="kpi-card {{ $ocupacionColor }}">
            <div class="kpi-card__icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div class="kpi-card__value">{{ $pctOcupacion }}%</div>
            <div class="kpi-card__label">% Ocupación</div>
            <div class="kpi-card__sub">{{ $habitacionesOcupadas }}/{{ $totalHabitaciones }} hab.</div>
        </div>

        <div class="kpi-card kpi--blue">
            <div class="kpi-card__icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="kpi-card__value">$ {{ number_format($totalIngresos, 0, ',', '.') }}</div>
            <div class="kpi-card__label">Total Ingresos (COP)</div>
            <div class="kpi-card__sub">Estancias + Minibar</div>
        </div>

        <div class="kpi-card {{ $habitacionesDisponibles > 0 ? 'kpi--green' : 'kpi--red' }}">
            <div class="kpi-card__icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v18H3z"/><path d="M9 3v18"/><path d="M3 9h18"/></svg>
            </div>
            <div class="kpi-card__value">{{ $habitacionesDisponibles }}</div>
            <div class="kpi-card__label">Hab. Disponibles</div>
            <div class="kpi-card__sub">{{ $habitacionesOcupadas }} ocupadas · {{ $habitacionesEnMant }} mant.</div>
        </div>

        <div class="kpi-card {{ $mantColor }}">
            <div class="kpi-card__icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            </div>
            <div class="kpi-card__value">{{ $mantPendiente + $mantProceso }}</div>
            <div class="kpi-card__label">Órdenes Activas</div>
            <div class="kpi-card__sub">{{ $mantUrgente }} urgentes</div>
        </div>

        <div class="kpi-card kpi--purple">
            <div class="kpi-card__icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
            <div class="kpi-card__value">{{ $totalVentasMinibar }}</div>
            <div class="kpi-card__label">Ventas Minibar</div>
            <div class="kpi-card__sub">$ {{ number_format($ingresosMinibar, 0, ',', '.') }} COP</div>
        </div>

        <div class="kpi-card kpi--teal">
            <div class="kpi-card__icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="kpi-card__value">{{ $huespedesEnCasa }}</div>
            <div class="kpi-card__label">Huéspedes en Casa</div>
            <div class="kpi-card__sub">{{ $checkinHoy }} entrada(s) hoy</div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     MÓDULO HABITACIONES
══════════════════════════════════════════════════════════ --}}
<section class="report-section">
    <h2 class="section-title">
        <span class="section-title__icon section-title__icon--indigo">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </span>
        Módulo de Habitaciones
    </h2>

    <div class="two-col">
        <div>
            <table class="data-table">
                <thead><tr><th>Indicador</th><th>Valor</th></tr></thead>
                <tbody>
                    <tr><td>Total de habitaciones</td><td><span class="badge badge-blue">{{ $totalHabitaciones }}</span></td></tr>
                    <tr><td>Disponibles</td><td><span class="badge badge-green">{{ $habitacionesDisponibles }}</span></td></tr>
                    <tr><td>Ocupadas</td><td><span class="badge badge-red">{{ $habitacionesOcupadas }}</span></td></tr>
                    <tr><td>En mantenimiento</td><td><span class="badge badge-yellow">{{ $habitacionesEnMant }}</span></td></tr>
                    <tr><td>Porcentaje de ocupación</td><td><strong>{{ $pctOcupacion }}%</strong></td></tr>
                </tbody>
            </table>
        </div>
        <div>
            <div class="mini-title">Distribución por Tipo</div>
            @foreach ($distribucionPorTipo as $tipo)
                @php
                    $pct = $totalHabitaciones > 0 ? round(($tipo->total / $totalHabitaciones) * 100) : 0;
                @endphp
                <div class="progress-row">
                    <div class="progress-row__label">{{ $tipo->name }}</div>
                    <div class="progress-row__bar-wrap">
                        <div class="progress-row__bar" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="progress-row__num">{{ $tipo->total }}</div>
                </div>
            @endforeach

            {{-- Barra visual de ocupación --}}
            <div class="mini-title" style="margin-top: 20px;">Estado Visual</div>
            <div class="occ-bar">
                @if($totalHabitaciones > 0)
                    <div class="occ-bar__seg occ-bar__seg--red"    style="width: {{ ($habitacionesOcupadas   / $totalHabitaciones) * 100 }}%" title="Ocupadas: {{ $habitacionesOcupadas }}"></div>
                    <div class="occ-bar__seg occ-bar__seg--yellow" style="width: {{ ($habitacionesEnMant     / $totalHabitaciones) * 100 }}%" title="Mantenimiento: {{ $habitacionesEnMant }}"></div>
                    <div class="occ-bar__seg occ-bar__seg--green"  style="width: {{ ($habitacionesDisponibles/ $totalHabitaciones) * 100 }}%" title="Disponibles: {{ $habitacionesDisponibles }}"></div>
                @endif
            </div>
            <div class="occ-legend">
                <span class="legend-dot legend-dot--red"></span> Ocupadas ({{ $habitacionesOcupadas }})
                <span class="legend-dot legend-dot--yellow" style="margin-left:12px"></span> Mant. ({{ $habitacionesEnMant }})
                <span class="legend-dot legend-dot--green"  style="margin-left:12px"></span> Libre ({{ $habitacionesDisponibles }})
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     MÓDULO RESERVAS
══════════════════════════════════════════════════════════ --}}
<section class="report-section">
    <h2 class="section-title">
        <span class="section-title__icon section-title__icon--orange">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </span>
        Módulo de Reservas
    </h2>

    <div class="stat-cards">
        <div class="stat-card"><div class="stat-card__num">{{ $totalReservas }}</div><div class="stat-card__lbl">Total</div></div>
        <div class="stat-card stat-card--green"><div class="stat-card__num">{{ $reservasActivas }}</div><div class="stat-card__lbl">Activas</div></div>
        <div class="stat-card stat-card--yellow"><div class="stat-card__num">{{ $reservasPendientes }}</div><div class="stat-card__lbl">Pendientes</div></div>
        <div class="stat-card stat-card--blue"><div class="stat-card__num">{{ $reservasFuturas }}</div><div class="stat-card__lbl">Futuras</div></div>
        <div class="stat-card stat-card--gray"><div class="stat-card__num">{{ $reservasCanceladas }}</div><div class="stat-card__lbl">Finalizadas</div></div>
        <div class="stat-card stat-card--teal"><div class="stat-card__num">{{ $checkinHoy }}</div><div class="stat-card__lbl">Check-in Hoy</div></div>
        <div class="stat-card stat-card--red"><div class="stat-card__num">{{ $checkoutHoy }}</div><div class="stat-card__lbl">Check-out Hoy</div></div>
    </div>

    @if($reservasRecientes->count())
    <div class="mini-title" style="margin-top: 24px;">Reservas Recientes</div>
    <table class="data-table">
        <thead><tr><th>#</th><th>Cliente</th><th>Tipo Hab.</th><th>Check-in</th><th>Check-out</th><th>Estado</th></tr></thead>
        <tbody>
        @foreach ($reservasRecientes as $r)
            @php
                $statusMap = [
                    'pendiente'      => ['lbl' => 'Pendiente',   'cls' => 'badge-yellow'],
                    'anticipo_pagado'=> ['lbl' => 'Anticipo',    'cls' => 'badge-blue'],
                    'reserva_previa' => ['lbl' => 'Asignada',    'cls' => 'badge-teal'],
                    'ocupada'        => ['lbl' => 'Ocupada',     'cls' => 'badge-red'],
                    'finalizada'     => ['lbl' => 'Finalizada',  'cls' => 'badge-gray'],
                ];
                $st = $statusMap[$r->status] ?? ['lbl' => ucfirst($r->status), 'cls' => 'badge-gray'];
            @endphp
            <tr>
                <td class="text-muted">#{{ $r->id }}</td>
                <td>{{ $r->nombre_cliente ?: '—' }}</td>
                <td>{{ $r->roomType->name ?? '—' }}</td>
                <td>{{ $r->check_in->format('d/m/Y') }}</td>
                <td>{{ $r->check_out->format('d/m/Y') }}</td>
                <td><span class="badge {{ $st['cls'] }}">{{ $st['lbl'] }}</span></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</section>

{{-- ══════════════════════════════════════════════════════════
     MÓDULO RECEPCIÓN
══════════════════════════════════════════════════════════ --}}
<section class="report-section">
    <h2 class="section-title">
        <span class="section-title__icon section-title__icon--teal">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </span>
        Módulo de Recepción
    </h2>

    <div class="two-col">
        <div>
            <div class="mini-title">Entradas Recientes</div>
            <table class="data-table">
                <thead><tr><th>Huésped</th><th>Hab.</th><th>Check-in</th></tr></thead>
                <tbody>
                @forelse ($entradasRecientes as $s)
                    <tr>
                        <td>{{ $s->guest ? $s->guest->first_name . ' ' . $s->guest->last_name : '—' }}</td>
                        <td>{{ $s->assigned_room_number ?? '—' }}</td>
                        <td>{{ $s->actual_check_in_at ? $s->actual_check_in_at->format('d/m H:i') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted text-center">Sin registros recientes</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div>
            <div class="mini-title">Salidas Recientes</div>
            <table class="data-table">
                <thead><tr><th>Huésped</th><th>Hab.</th><th>Check-out</th></tr></thead>
                <tbody>
                @forelse ($salidasRecientes as $s)
                    <tr>
                        <td>{{ $s->guest ? $s->guest->first_name . ' ' . $s->guest->last_name : '—' }}</td>
                        <td>{{ $s->assigned_room_number ?? '—' }}</td>
                        <td>{{ $s->actual_check_out_at ? $s->actual_check_out_at->format('d/m H:i') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted text-center">Sin salidas recientes</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($huespedesActuales->count())
    <div class="mini-title" style="margin-top: 24px;">Huéspedes Actualmente en Casa ({{ $huespedesEnCasa }})</div>
    <table class="data-table">
        <thead><tr><th>Nombre</th><th>Documento</th><th>Hab.</th><th>Tipo Hab.</th><th>Llegada</th><th>Salida Prev.</th></tr></thead>
        <tbody>
        @foreach ($huespedesActuales as $s)
            <tr>
                <td>{{ $s->guest ? $s->guest->first_name . ' ' . $s->guest->last_name : '—' }}</td>
                <td class="text-muted">{{ $s->guest->document_number ?? '—' }}</td>
                <td>{{ $s->assigned_room_number ?? '—' }}</td>
                <td>{{ $s->room->roomtype->name ?? '—' }}</td>
                <td>{{ $s->actual_check_in_at ? $s->actual_check_in_at->format('d/m/Y') : '—' }}</td>
                <td>{{ $s->departure_at ? $s->departure_at->format('d/m/Y') : '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</section>

{{-- ══════════════════════════════════════════════════════════
     MÓDULO MANTENIMIENTO
══════════════════════════════════════════════════════════ --}}
<section class="report-section">
    <h2 class="section-title">
        <span class="section-title__icon section-title__icon--red">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        </span>
        Módulo de Mantenimiento
    </h2>

    <div class="two-col">
        <div>
            <table class="data-table">
                <thead><tr><th>Indicador</th><th>Valor</th></tr></thead>
                <tbody>
                    <tr><td>Órdenes pendientes (asignadas)</td><td><span class="badge badge-yellow">{{ $mantPendiente }}</span></td></tr>
                    <tr><td>Órdenes en proceso</td><td><span class="badge badge-blue">{{ $mantProceso }}</span></td></tr>
                    <tr><td>Órdenes completadas</td><td><span class="badge badge-green">{{ $mantCompletado }}</span></td></tr>
                    <tr><td>Total histórico</td><td><strong>{{ $totalMantOrdenes }}</strong></td></tr>
                    <tr><td>Tiempo prom. de resolución</td><td><strong>{{ $tiempoPromedioMant }} días</strong></td></tr>
                </tbody>
            </table>
        </div>
        <div>
            <div class="mini-title">Órdenes Activas por Prioridad</div>
            <div class="priority-bars">
                <div class="priority-row priority-row--red">
                    <span>Urgente</span>
                    <div class="priority-bar-wrap"><div class="priority-bar" style="width: {{ $totalMantOrdenes > 0 ? round(($mantUrgente/($mantUrgente+$mantNormal+$mantBaja+0.01))*100) : 0 }}%"></div></div>
                    <strong>{{ $mantUrgente }}</strong>
                </div>
                <div class="priority-row priority-row--yellow">
                    <span>Normal</span>
                    <div class="priority-bar-wrap"><div class="priority-bar" style="width: {{ $totalMantOrdenes > 0 ? round(($mantNormal/($mantUrgente+$mantNormal+$mantBaja+0.01))*100) : 0 }}%"></div></div>
                    <strong>{{ $mantNormal }}</strong>
                </div>
                <div class="priority-row priority-row--blue">
                    <span>Baja</span>
                    <div class="priority-bar-wrap"><div class="priority-bar" style="width: {{ $totalMantOrdenes > 0 ? round(($mantBaja/($mantUrgente+$mantNormal+$mantBaja+0.01))*100) : 0 }}%"></div></div>
                    <strong>{{ $mantBaja }}</strong>
                </div>
            </div>
        </div>
    </div>

    @if ($ordenesMantRecientes->count())
    <div class="mini-title" style="margin-top: 24px;">Órdenes Recientes</div>
    <table class="data-table">
        <thead><tr><th>Descripción</th><th>Hab.</th><th>Prioridad</th><th>Estado</th><th>Creada</th></tr></thead>
        <tbody>
        @foreach ($ordenesMantRecientes as $o)
            @php
                $pClass = ['urgente' => 'badge-red', 'normal' => 'badge-blue', 'baja' => 'badge-gray'][$o->priority] ?? 'badge-gray';
                $sClass = ['asignada' => 'badge-yellow', 'en_proceso' => 'badge-blue', 'completada' => 'badge-green'][$o->status] ?? 'badge-gray';
                $sLabel = ['asignada' => 'Asignada', 'en_proceso' => 'En proceso', 'completada' => 'Completada'][$o->status] ?? ucfirst($o->status);
            @endphp
            <tr>
                <td>{{ Str::limit($o->description, 40) }}</td>
                <td>{{ $o->room_number ?? '—' }}</td>
                <td><span class="badge {{ $pClass }}">{{ ucfirst($o->priority) }}</span></td>
                <td><span class="badge {{ $sClass }}">{{ $sLabel }}</span></td>
                <td class="text-muted">{{ $o->created_at->format('d/m/Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</section>

{{-- ══════════════════════════════════════════════════════════
     MÓDULO MINIBAR
══════════════════════════════════════════════════════════ --}}
<section class="report-section">
    <h2 class="section-title">
        <span class="section-title__icon section-title__icon--green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </span>
        Módulo de Minibar
    </h2>

    <div class="stat-cards">
        <div class="stat-card stat-card--green"><div class="stat-card__num">{{ $totalVentasMinibar }}</div><div class="stat-card__lbl">Total Ventas</div></div>
        <div class="stat-card stat-card--blue"><div class="stat-card__num">$ {{ number_format($ingresosMinibar, 0, ',', '.') }}</div><div class="stat-card__lbl">Ingresos COP</div></div>
        <div class="stat-card stat-card--teal"><div class="stat-card__num">{{ $ventasHoy }}</div><div class="stat-card__lbl">Ventas Hoy</div></div>
        <div class="stat-card stat-card--yellow"><div class="stat-card__num">$ {{ number_format($ingresosHoy, 0, ',', '.') }}</div><div class="stat-card__lbl">Ingresos Hoy</div></div>
    </div>

    <div class="two-col" style="margin-top: 24px;">
        <div>
            <div class="mini-title">Top 5 Productos Más Vendidos</div>
            <table class="data-table">
                <thead><tr><th>#</th><th>Producto</th><th>Unidades</th><th>Ingresos</th></tr></thead>
                <tbody>
                @forelse ($topProductos as $i => $p)
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>{{ $p->nombre }}</td>
                        <td><strong>{{ $p->total_qty }}</strong></td>
                        <td>$ {{ number_format($p->total_ingreso, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted text-center">Sin datos</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div>
            <div class="mini-title">Ventas Recientes</div>
            <table class="data-table">
                <thead><tr><th>Fecha</th><th>Cliente</th><th>Total</th><th>Método</th></tr></thead>
                <tbody>
                @forelse ($ventasRecientes as $v)
                    <tr>
                        <td class="text-muted">{{ $v->created_at->format('d/m H:i') }}</td>
                        <td>{{ $v->user->name ?? 'Invitado' }}</td>
                        <td>$ {{ number_format($v->total, 0, ',', '.') }}</td>
                        <td>{{ $v->metodo_pago ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted text-center">Sin registros</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     MÓDULO INVENTARIO
══════════════════════════════════════════════════════════ --}}
<section class="report-section">
    <h2 class="section-title">
        <span class="section-title__icon section-title__icon--purple">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </span>
        Inventario / Compras Minibar
    </h2>

    <div class="two-col">
        <div>
            <table class="data-table">
                <thead><tr><th>Indicador</th><th>Valor</th></tr></thead>
                <tbody>
                    <tr><td>Total de productos registrados</td><td><span class="badge badge-blue">{{ $totalProductos }}</span></td></tr>
                    <tr><td>Productos con bajo stock (≤ 5)</td><td><span class="badge {{ $productosBajoStock > 0 ? 'badge-red' : 'badge-green' }}">{{ $productosBajoStock }}</span></td></tr>
                    <tr><td>Valor total del inventario</td><td><strong>$ {{ number_format($valorInventario, 0, ',', '.') }}</strong></td></tr>
                </tbody>
            </table>
        </div>
        <div>
            <div class="mini-title">Estado de Stock por Producto</div>
            <table class="data-table">
                <thead><tr><th>Producto</th><th>Stock</th><th>Alerta</th></tr></thead>
                <tbody>
                @forelse ($inventarioDetalle as $prod)
                    <tr>
                        <td>{{ $prod->nombre }}</td>
                        <td>{{ $prod->stock }}</td>
                        <td>
                            @if ($prod->stock <= 2)
                                <span class="badge badge-red">Crítico</span>
                            @elseif ($prod->stock <= 5)
                                <span class="badge badge-yellow">Bajo</span>
                            @else
                                <span class="badge badge-green">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted text-center">Sin productos</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     MÓDULO PERSONAL
══════════════════════════════════════════════════════════ --}}
<section class="report-section">
    <h2 class="section-title">
        <span class="section-title__icon section-title__icon--teal">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </span>
        Módulo de Personal
    </h2>

    <div class="two-col">
        <div>
            <table class="data-table">
                <thead><tr><th>Indicador</th><th>Valor</th></tr></thead>
                <tbody>
                    <tr><td>Total de empleados registrados</td><td><strong>{{ $totalEmpleados }}</strong></td></tr>
                </tbody>
            </table>
        </div>
        <div>
            <div class="mini-title">Personal por Área</div>
            <table class="data-table">
                <thead><tr><th>Área / Rol</th><th>Cant.</th></tr></thead>
                <tbody>
                @forelse ($empleadosPorRol as $ep)
                    <tr>
                        <td>{{ ucfirst($ep->rol) }}</td>
                        <td><span class="badge badge-blue">{{ $ep->total }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-muted text-center">Sin datos</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer class="report-footer">
    <span>Hotel Oasis &mdash; Informe generado automáticamente</span>
    <span>{{ $now->format('d/m/Y H:i:s') }}</span>
</footer>

</div>{{-- END .report-page --}}

<link rel="stylesheet" href="{{ asset('css/blade/admin/report/preview--style1.css') }}">
@endsection


