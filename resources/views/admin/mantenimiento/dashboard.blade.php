@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.mantenimiento-sidebar';
@endphp

<style>
    .mantenimiento-dashboard {
        padding: 30px;
    }

    .page-header {
        margin-bottom: 40px;
        border-bottom: 3px solid #ff6b6b;
        padding-bottom: 20px;
    }

    .page-header h1 {
        color: #ff6b6b;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .page-header p {
        color: #666;
        font-size: 1.1rem;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-left: 5px solid #ff6b6b;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .stat-card .icon {
        font-size: 3rem;
        color: #ff6b6b;
        margin-bottom: 15px;
    }

    .stat-card h3 {
        font-size: 3rem;
        font-weight: 800;
        color: #333;
        margin-bottom: 10px;
    }

    .stat-card p {
        color: #666;
        font-size: 1.1rem;
        margin: 0;
    }

    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 30px;
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        color: white;
        border-radius: 10px;
        text-decoration: none;
        font-size: 1.2rem;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(238, 90, 82, 0.3);
    }

    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(238, 90, 82, 0.4);
        color: white;
        text-decoration: none;
    }

    .action-btn i {
        margin-right: 10px;
        font-size: 1.5rem;
    }
</style>

<div class="mantenimiento-dashboard">
    <div class="page-header">
        <h1><i class="bi bi-tools"></i> Dashboard de Mantenimiento</h1>
        <p>Gestión de órdenes de mantenimiento, reparaciones y estado de habitaciones</p>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <div class="icon">
                <i class="bi bi-door-open"></i>
            </div>
            <h3>{{ $roomsCount }}</h3>
            <p>Total de Habitaciones</p>
        </div>

        <div class="stat-card">
            <div class="icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h3>{{ $activeOrdersCount }}</h3>
            <p>Órdenes Activas</p>
        </div>

        <div class="stat-card">
            <div class="icon">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
            <h3>{{ $urgentOrdersCount }}</h3>
            <p>Órdenes Urgentes</p>
        </div>
    </div>

    <h2 style="color: #333; margin-bottom: 20px; font-weight: 700;">
        <i class="bi bi-gear"></i> Control de Mantenimiento
    </h2>

    <div class="action-buttons">
        <a href="{{ route('admin.mantenimiento.index') }}" class="action-btn">
            <i class="bi bi-wrench-adjustable"></i>
            Gestión de Mantenimiento
        </a>
    </div>
</div>
@endsection
