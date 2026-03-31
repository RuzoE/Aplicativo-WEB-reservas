@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
@endphp

<link rel="stylesheet" href="{{ asset('css/blade/admin/habitaciones/dashboard--style1.css') }}">

<div class="reservas-dashboard">
    <div class="page-header">
        <h1><i class="bi bi-calendar-check"></i> {{ __('messages.dashboard_reservations_title') }}</h1>
        <p>Gestión de habitaciones, tipos de habitación y reservas del hotel</p>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <div class="icon">
                <i class="bi bi-door-open"></i>
            </div>
            <h3>{{ $totalRooms }}</h3>
            <p>Total de Habitaciones</p>
        </div>

        <div class="stat-card">
            <div class="icon">
                <i class="bi bi-calendar-check"></i>
            </div>
            <h3>{{ $reservedRoom }}</h3>
            <p>Reservas Activas</p>
        </div>

        <div class="stat-card">
            <div class="icon">
                <i class="bi bi-door-closed"></i>
            </div>
            <h3>{{ $totalRooms - $reservedRoom }}</h3>
            <p>Habitaciones Disponibles</p>
        </div>
    </div>

    <h2 style="color: #333; margin-bottom: 20px; font-weight: 700;">
        <i class="bi bi-gear"></i> Gestión de Reservas
    </h2>

    <div class="action-buttons">
        <a href="{{ route('admin.habitaciones.reservas.index') }}" class="action-btn">
            <i class="bi bi-list-check"></i>
            {{ __('messages.orders') }}
        </a>

        <a href="{{ route('admin.habitaciones.tipos-habitacion.index') }}" class="action-btn secondary">
            <i class="bi bi-grid-3x3"></i>
            {{ __('messages.roomtypes') }}
        </a>

        <a href="{{ route('admin.habitaciones.habitaciones.index') }}" class="action-btn tertiary">
            <i class="bi bi-house-door"></i>
            {{ __('messages.rooms') }}
        </a>
    </div>
</div>
@endsection


