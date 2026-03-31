@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.mantenimiento-sidebar';
@endphp

<link rel="stylesheet" href="{{ asset('css/blade/admin/mantenimiento/dashboard--style1.css') }}">

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


