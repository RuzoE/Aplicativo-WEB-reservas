@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.sidebar'; // Usa el sidebar simple con 3 botones
@endphp
<link rel="stylesheet" href="{{ asset('css/blade/admin/index--style1.css') }}">

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Panel de Administración</h1>
        <p>Hotel Oasis</p>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-container">
        <div class="stat-box reservas">
            <div class="icon-wrapper">
                <i class="bi bi-door-open-fill"></i>
            </div>
            <h3>{{ $totalRooms }}</h3>
            <p>Total Habitaciones</p>
            <p class="subtitle">Disponibles en el hotel</p>
        </div>

        <div class="stat-box reservas">
            <div class="icon-wrapper">
                <i class="bi bi-calendar-check-fill"></i>
            </div>
            <h3>{{ $reservedRoom }}</h3>
            <p>Reservas Activas</p>
            <p class="subtitle">En proceso actualmente</p>
        </div>

        <div class="stat-box minibar">
            <div class="icon-wrapper">
                <i class="bi bi-basket-fill"></i>
            </div>
            <h3>{{ $totalProductos }}</h3>
            <p>Productos Minibar</p>
            <p class="subtitle">En el catálogo</p>
        </div>

        <div class="stat-box minibar">
            <div class="icon-wrapper">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
            <h3>{{ $totalCompras }}</h3>
            <p>Ventas Realizadas</p>
            <p class="subtitle">Total de compras</p>
        </div>

        <div class="stat-box recepcion">
            <div class="icon-wrapper" style="background: linear-gradient(135deg, #FFC107 0%, #FFA000 100%);">
                <i class="bi bi-calendar-event"></i>
            </div>
            <h3>{{ $expectedArrivalsToday }}</h3>
            <p>Por Llegar Hoy</p>
            <p class="subtitle">Pendientes de ingreso</p>
        </div>

        <div class="stat-box recepcion">
            <div class="icon-wrapper">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h3>{{ $checkInsRealizadosHoy }}</h3>
            <p>Check-ins Hoy</p>
            <p class="subtitle">Ingresos completados</p>
        </div>

        <div class="stat-box recepcion">
            <div class="icon-wrapper">
                <i class="bi bi-people-fill"></i>
            </div>
            <h3>{{ $huespedesEnCasa }}</h3>
            <p>Huéspedes en Casa</p>
            <p class="subtitle">Estancias activas</p>
        </div>
    </div>

    <h2 class="section-title">Acceso a Paneles de Gestión</h2>

    <!-- Paneles de Gestión -->
    <div class="panels-container">
        <a href="{{ route('home') }}" class="panel-card inicio">
            <div class="panel-icon">
                <i class="bi bi-house-door-fill"></i>
            </div>
            <h2>Inicio</h2>
            <p>Ir a la página principal del Hotel Oasis</p>
            <div class="arrow">→</div>
        </a>

        <a href="{{ route('admin.habitaciones.dashboard') }}" class="panel-card reservas">
            <div class="panel-icon">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
            <h2>Panel de Reservas</h2>
            <p>Gestionar habitaciones, tipos de habitación y reservas de huéspedes</p>
            <div class="arrow">→</div>
        </a>

        <a href="{{ route('admin.minibar.dashboard') }}" class="panel-card minibar">
            <div class="panel-icon">
                <i class="bi bi-shop"></i>
            </div>
            <h2>Panel de Minibar</h2>
            <p>Administrar productos, bebidas, tipos y ventas del minibar</p>
            <div class="arrow">→</div>
        </a>

        <a href="{{ route('reception.dashboard') }}" class="panel-card recepcion">
            <div class="panel-icon">
                <i class="bi bi-reception-4"></i>
            </div>
            <h2>Panel de Recepción</h2>
            <p>Gestionar check-in, folios, cargos y check-out de huéspedes</p>
            <div class="arrow">→</div>
        </a>

        <a href="{{ route('admin.mantenimiento.dashboard') }}" class="panel-card mantenimiento">
            <div class="panel-icon">
                <i class="bi bi-tools"></i>
            </div>
            <h2>Panel de Mantenimiento</h2>
            <p>Supervisar órdenes urgentes, rutinas de reparación y estado de habitaciones</p>
            <div class="arrow">→</div>
        </a>

        <a href="{{ route('admin.report.download') }}" class="panel-card informe">
            <div class="panel-icon">
                <i class="bi bi-file-earmark-arrow-down-fill"></i>
            </div>
            <h2>Descargar Informe General</h2>
            <p>Exportar resumen de reservas, estancias, mantenimiento y minibar en PDF</p>
            <div class="arrow">→</div>
        </a>
    </div>
</div>
@endsection


