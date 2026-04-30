@extends('layouts.app')

@section('content')
@php
    use App\Support\AuditoriaHelper;

    $adminView = true;
    $sidebarView = 'admin.sidebar';

    $badgeClassByAction = [
        'ACCESS' => 'badge-login',
        'CREATE' => 'badge-create',
        'UPDATE' => 'badge-update',
        'DELETE' => 'badge-delete',
        'LOGIN' => 'badge-login',
        'LOGIN_FAILED' => 'badge-delete',
        'CHECK_IN' => 'badge-checkin',
        'CHECK_OUT' => 'badge-checkout',
        'CANCEL' => 'badge-cancel',
        'ROLE_CHANGE' => 'badge-update',
        'PASSWORD_CHANGE' => 'badge-update',
    ];

    $badgeClassByModulo = [
        'reservas' => 'badge-mod-reservas',
        'habitaciones' => 'badge-mod-habitaciones',
        'mantenimiento' => 'badge-mod-mantenimiento',
        'minibar' => 'badge-mod-minibar',
        'usuarios' => 'badge-mod-usuarios',
        'recepcion' => 'badge-mod-recepcion',
    ];

    $moduloTop = $moduloMasActivo?->modulo ? ucfirst($moduloMasActivo->modulo) : 'N/A';
    $moduloTopCount = (int) ($moduloMasActivo->total ?? 0);

    $usuarioTopName = 'N/A';
    $usuarioTopCount = 0;
    if (!is_null($usuarioMasActivo)) {
        $usuarioTopName = trim((($usuarioMasActivo['usuario']->name ?? '') . ' ' . ($usuarioMasActivo['usuario']->last_name ?? '')));
        $usuarioTopName = $usuarioTopName !== '' ? $usuarioTopName : ($usuarioMasActivo['usuario']->email ?? 'N/A');
        $usuarioTopCount = (int) ($usuarioMasActivo['total'] ?? 0);
    }

    $filtrosActivos = collect(['usuario_id', 'modulo', 'accion', 'desde', 'hasta'])->filter(fn ($f) => filled(request($f)))->count();
@endphp

<link rel="stylesheet" href="{{ asset('css/blade/admin/auditorias/index--style1.css') }}">

{{-- ========== MAIN WRAPPER ========== --}}
<div class="audit-wrapper">
    <div class="audit-container">

        {{-- ========== HERO HEADER ========== --}}
        <section class="audit-hero">
            <div class="audit-hero-inner">
                <div style="max-width: 640px;">
                    <div class="audit-hero-badge">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3L4 7V12C4 16.8 7.4 21.3 12 22C16.6 21.3 20 16.8 20 12V7L12 3Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Seguridad y Cumplimiento
                    </div>
                    <h1>Auditoria del Sistema</h1>
                    <p class="audit-hero-desc">
                        Dashboard de monitoreo para analizar trazabilidad operativa, actividad de usuarios y eventos criticos de negocio.
                    </p>
                </div>
                <div class="audit-hero-filter-count">
                    <p class="label">Filtros Activos</p>
                    <p class="value">{{ $filtrosActivos }}</p>
                </div>
            </div>
            <div class="audit-hero-divider"></div>
        </section>

        {{-- ========== STATS CARDS ========== --}}
        <section class="audit-stats-grid">
            {{-- Total Registros --}}
            <article class="audit-stat-card">
                <div class="audit-stat-header">
                    <p class="audit-stat-label">Total de Registros</p>
                    <span class="audit-stat-icon blue">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M4 4H20V8H4V4ZM4 10H20V14H4V10ZM4 16H14V20H4V16Z" fill="currentColor"/></svg>
                    </span>
                </div>
                <p class="audit-stat-value">{{ number_format($totalRegistros) }}</p>
                <p class="audit-stat-sub">{{ number_format($totalFiltrados) }} en resultado actual</p>
            </article>

            {{-- Acciones Hoy --}}
            <article class="audit-stat-card">
                <div class="audit-stat-header">
                    <p class="audit-stat-label">Acciones Hoy</p>
                    <span class="audit-stat-icon green">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M3 12H7L10 4L14 20L17 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </div>
                <p class="audit-stat-value">{{ number_format($accionesHoy) }}</p>
                <p class="audit-stat-sub">Actividad registrada en la fecha actual</p>
            </article>

            {{-- Modulo mas Activo --}}
            <article class="audit-stat-card">
                <div class="audit-stat-header">
                    <p class="audit-stat-label">Modulo mas Activo</p>
                    <span class="audit-stat-icon purple">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M4 19V11M10 19V5M16 19V13M22 19V8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </span>
                </div>
                <p class="audit-stat-value" style="font-size: 24px;">{{ $moduloTop }}</p>
                <p class="audit-stat-sub">{{ number_format($moduloTopCount) }} eventos acumulados</p>
            </article>

            {{-- Usuario mas Activo --}}
            <article class="audit-stat-card">
                <div class="audit-stat-header">
                    <p class="audit-stat-label">Usuario mas Activo</p>
                    <span class="audit-stat-icon amber">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M20 21V19C20 17.9 19.1 17 18 17H6C4.9 17 4 17.9 4 19V21" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/></svg>
                    </span>
                </div>
                <p class="audit-stat-value" style="font-size: 24px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $usuarioTopName }}">{{ $usuarioTopName }}</p>
                <p class="audit-stat-sub">{{ number_format($usuarioTopCount) }} acciones registradas</p>
            </article>
        </section>

        {{-- ========== FILTERS ========== --}}
        <section class="audit-filters-card">
            <div class="audit-filters-header">
                <div>
                    <h2>Filtros de Auditoria</h2>
                    <p>Combina criterios para ubicar eventos especificos y reducir ruido operacional.</p>
                </div>
                @if($filtrosActivos > 0)
                    <span class="audit-active-badge">{{ $filtrosActivos }} filtro(s) aplicados</span>
                @endif
            </div>

            <form id="audit-filter-form" method="GET" action="{{ route('admin.auditorias.index') }}" class="audit-filter-grid">
                {{-- Usuario --}}
                <div class="span-2">
                    <label for="usuario_id" class="audit-filter-label">Usuario</label>
                    <div class="audit-field-wrap">
                        <span class="field-icon">
                            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/><path d="M4 20C4 16.7 7 14 12 14C17 14 20 16.7 20 20" stroke="currentColor" stroke-width="2"/></svg>
                        </span>
                        <select name="usuario_id" id="usuario_id">
                            <option value="">Todos</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" @selected((string) request('usuario_id') === (string) $usuario->id)>
                                    {{ trim(($usuario->name ?? '') . ' ' . ($usuario->last_name ?? '')) ?: $usuario->email }} (ID {{ $usuario->id }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Modulo --}}
                <div>
                    <label for="modulo" class="audit-filter-label">Modulo</label>
                    <div class="audit-field-wrap">
                        <span class="field-icon">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M4 5H20V11H4V5Z" stroke="currentColor" stroke-width="2"/><path d="M4 13H11V19H4V13Z" stroke="currentColor" stroke-width="2"/><path d="M13 13H20V19H13V13Z" stroke="currentColor" stroke-width="2"/></svg>
                        </span>
                        <select name="modulo" id="modulo">
                            <option value="">Todos</option>
                            @foreach($modulos as $modulo)
                                <option value="{{ $modulo }}" @selected(request('modulo') === $modulo)>{{ ucfirst($modulo) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Accion --}}
                <div>
                    <label for="accion" class="audit-filter-label">Accion</label>
                    <div class="audit-field-wrap">
                        <span class="field-icon">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M7 13L10 16L17 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <select name="accion" id="accion">
                            <option value="">Todas</option>
                            @foreach($acciones as $accionFiltro)
                                <option value="{{ $accionFiltro }}" @selected(request('accion') === $accionFiltro)>{{ AuditoriaHelper::traducirAccion($accionFiltro) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Desde --}}
                <div>
                    <label for="desde" class="audit-filter-label">Desde</label>
                    <div class="audit-field-wrap">
                        <span class="field-icon">
                            <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 2V6M16 2V6M3 10H21" stroke="currentColor" stroke-width="2"/></svg>
                        </span>
                        <input type="date" id="desde" name="desde" value="{{ request('desde') }}">
                    </div>
                </div>

                {{-- Hasta --}}
                <div>
                    <label for="hasta" class="audit-filter-label">Hasta</label>
                    <div class="audit-field-wrap">
                        <span class="field-icon">
                            <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 2V6M16 2V6M3 10H21" stroke="currentColor" stroke-width="2"/></svg>
                        </span>
                        <input type="date" id="hasta" name="hasta" value="{{ request('hasta') }}">
                    </div>
                </div>

                {{-- Registros --}}
                <div>
                    <label for="per_page" class="audit-filter-label">Registros</label>
                    <div class="audit-field-no-icon">
                        <select name="per_page" id="per_page">
                            <option value="9" @selected((string) $perPage === '9')>9 resultados</option>
                            <option value="20" @selected((string) $perPage === '20')>20 resultados</option>
                            <option value="50" @selected((string) $perPage === '50')>50 resultados</option>
                            <option value="100" @selected((string) $perPage === '100')>100 resultados</option>
                        </select>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="span-6">
                    <div class="audit-filter-actions">
                        <button id="filter-submit-btn" type="submit" class="audit-btn-primary">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M4 6H20M7 12H17M10 18H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            Filtrar
                        </button>
                        <a href="{{ route('admin.auditorias.index') }}" class="audit-btn-secondary">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M6 6L18 18M6 18L18 6" stroke="currentColor" stroke-width="2"/></svg>
                            Limpiar
                        </a>
                        <span id="filter-feedback" class="audit-feedback-text">Aplicando filtros, por favor espera...</span>
                    </div>
                </div>
            </form>
        </section>

        {{-- ========== MAIN CONTENT: TABLE + SIDEBAR ========== --}}
        <section class="audit-main-grid">
            {{-- TABLE --}}
            <article class="audit-table-card">
                <header class="audit-table-header">
                    <div>
                        <h2>Logs de Auditoria</h2>
                        <p>Vista cronologica con trazabilidad de usuario, accion y modulo.</p>
                    </div>
                    <span class="audit-results-badge">{{ number_format($totalFiltrados) }} resultado(s)</span>
                </header>

                <div class="audit-table-scroll">
                    <table class="audit-table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Accion</th>
                                <th>Modulo</th>
                                <th>Descripcion</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auditorias as $item)
                                @php
                                    $accion = strtoupper((string) $item->accion);
                                    $modulo = strtolower((string) $item->modulo);
                                    $badgeAccion = $badgeClassByAction[$accion] ?? 'badge-default';
                                    $badgeModulo = $badgeClassByModulo[$modulo] ?? 'badge-default';
                                    $nombreUsuario = trim((($item->usuario->name ?? '') . ' ' . ($item->usuario->last_name ?? '')));
                                    $nombreUsuario = $nombreUsuario !== '' ? $nombreUsuario : ($item->usuario->email ?? 'Sistema/Automatico');
                                    $accionTraducida = AuditoriaHelper::traducirAccion($accion);
                                    $descripcionHumana = AuditoriaHelper::humanizarDescripcion($item);
                                @endphp
                                <tr>
                                    <td>
                                        <p class="user-name">{{ $nombreUsuario }}</p>
                                        <p class="user-id">ID {{ $item->usuario_id ?? 'N/A' }}</p>
                                    </td>
                                    <td>
                                        <span class="audit-badge {{ $badgeAccion }}" title="{{ $accion }}">{{ $accionTraducida }}</span>
                                    </td>
                                    <td>
                                        <span class="audit-badge {{ $badgeModulo }}" title="Modulo de origen">{{ ucfirst($modulo) }}</span>
                                    </td>
                                    <td>
                                        <div class="desc-text">
                                            {{ $descripcionHumana }}
                                            @if(!is_null($item->registro_id))
                                                <p class="registro-id">Registro ID: {{ $item->registro_id }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <p class="fecha-main">{{ optional($item->created_at)->format('d M Y - h:i A') }}</p>
                                        <p class="fecha-ago">{{ optional($item->created_at)->diffForHumans() }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="audit-empty">
                                            <span class="audit-empty-icon">
                                                <svg viewBox="0 0 24 24" fill="none"><path d="M4 4H20V16H4V4Z" stroke="currentColor" stroke-width="2"/><path d="M8 20H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                            </span>
                                            <p class="audit-empty-title">No hay resultados para esta busqueda</p>
                                            <p class="audit-empty-sub">Ajusta filtros o limpia criterios para mostrar actividad.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <footer class="audit-table-footer">
                    {{ $auditorias->links() }}
                </footer>
            </article>

            {{-- SIDEBAR --}}
            <aside class="audit-sidebar">
                {{-- Últimos Eventos --}}
                <article class="audit-sidebar-card">
                    <h3>Ultimos Eventos Destacados</h3>
                    <p class="sub">Resumen rapido de los eventos mas recientes.</p>
                    <ul class="audit-event-list">
                        @forelse($eventosDestacados as $evento)
                            @php
                                $evtAccion = strtoupper((string) $evento->accion);
                                $evtModulo = strtolower((string) $evento->modulo);
                                $evtBadgeAccion = $badgeClassByAction[$evtAccion] ?? 'badge-default';
                                $evtBadgeModulo = $badgeClassByModulo[$evtModulo] ?? 'badge-default';
                                $evtUsuario = trim((($evento->usuario->name ?? '') . ' ' . ($evento->usuario->last_name ?? '')));
                                $evtUsuario = $evtUsuario !== '' ? $evtUsuario : ($evento->usuario->email ?? 'Sistema/Automatico');
                                $evtAccionTraducida = AuditoriaHelper::traducirAccion($evtAccion);
                                $evtDescHumana = AuditoriaHelper::humanizarDescripcion($evento);
                            @endphp
                            <li class="audit-event-item">
                                <div class="audit-event-top">
                                    <div class="audit-event-badges">
                                        <span class="audit-badge {{ $evtBadgeAccion }}">{{ $evtAccionTraducida }}</span>
                                        <span class="audit-badge {{ $evtBadgeModulo }}">{{ ucfirst($evtModulo) }}</span>
                                    </div>
                                    <span class="audit-event-time">{{ optional($evento->created_at)->format('h:i A') }}</span>
                                </div>
                                <p class="audit-event-user">{{ $evtUsuario }}</p>
                                <p class="audit-event-desc">{{ \Illuminate\Support\Str::limit($evtDescHumana, 110) }}</p>
                                <p class="audit-event-ago">{{ optional($evento->created_at)->diffForHumans() }}</p>
                            </li>
                        @empty
                            <li class="audit-event-item" style="text-align: center; color: #64748b;">Sin eventos destacados por el momento.</li>
                        @endforelse
                    </ul>
                </article>

                {{-- Estado del Panel --}}
                <article class="audit-sidebar-card">
                    <h3>Estado del Panel</h3>
                    <p class="audit-panel-info">Panel de solo lectura con enfoque forense: acceso exclusivo administrador, sin acciones de edicion o eliminacion sobre logs.</p>
                </article>
            </aside>
        </section>

    </div>
</div>

{{-- ========== LOADER OVERLAY ========== --}}
<div id="audit-filter-loader" class="audit-loader-overlay">
    <div class="audit-loader-box">
        <span class="audit-spinner"></span>
        <span class="audit-loader-text">Aplicando filtros...</span>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/blade/admin/auditorias/index--script1.js') }}"></script>
@endpush
@endsection


