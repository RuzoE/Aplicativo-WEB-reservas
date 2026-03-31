@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.sidebar';
@endphp

<link rel="stylesheet" href="{{ asset('css/blade/reception/dashboard--style1.css') }}">

<div
    class="reception-dashboard"
    id="reception-dashboard-config"
    data-dashboard-url="{{ route('reception.dashboard') }}"
    data-show-checkin-section="{{ session('show_checkin_section') ? '1' : '0' }}"
>

    <div id="section-dashboard">
        <div class="page-header">
            <h1><i class="bi bi-reception-4"></i> Recepción — Tablero</h1>
            <p>Resumen rápido de llegadas, salidas y huéspedes en casa</p>
        </div>

        <div class="stats-row">
            <div class="stat-card" style="border-left-color: #2196F3;">
                <div class="icon" style="color: #2196F3;">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <h3>{{ $expectedArrivalsToday->count() ?? 0 }}</h3>
                <p>Por Llegar Hoy</p>
                <small class="text-muted">Reservas pendientes</small>
            </div>

            <div class="stat-card" style="border-left-color: #4CAF50;">
                <div class="icon" style="color: #4CAF50;">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h3>{{ $checkedInToday->count() ?? 0 }}</h3>
                <p>Check-ins Realizados</p>
                <small class="text-muted">Huéspedes que ya llegaron</small>
            </div>

            <div class="stat-card" style="border-left-color: #2196F3;">
                <div class="icon" style="color: #2196F3;">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <h3>{{ $departures->count() ?? 0 }}</h3>
                <p>Salidas de hoy</p>
            </div>

            <div class="stat-card" style="border-left-color: #2196F3;">
                <div class="icon" style="color: #2196F3;">
                    <i class="bi bi-people"></i>
                </div>
                <h3>{{ $inHouse->count() ?? 0 }}</h3>
                <p>Huéspedes en casa</p>
            </div>
        </div>

        <h2 style="color: #2196F3; margin-bottom: 20px; font-weight: 700;">
            <i class="bi bi-gear"></i> Gestión de Recepción
        </h2>

        <div class="action-buttons">
            <a href="#checkin" class="action-btn">
                <i class="bi bi-door-open"></i>
                Ir a Check-in
            </a>

            <a href="#folio" class="action-btn secondary">
                <i class="bi bi-receipt"></i>
                Ver Folios
            </a>

            <a href="#checkout" class="action-btn tertiary">
                <i class="bi bi-box-arrow-in-right"></i>
                Procesar Check-out
            </a>
        </div>
    </div>

    <div id="section-checkin" style="display:none;">
        @include('components.reception.checkin-form')
    </div>

    <div id="section-userlink" style="display:none;">
        @include('components.reception.user-link-form')
    </div>

    <div id="section-folio" style="display:none;">
        @include('components.reception.folio-form')
    </div>

    <div id="section-checkout" style="display:none;">
        @include('components.reception.checkout-form')
    </div>

    <script src="{{ asset('js/reception-dashboard.js') }}"></script>
    <script src="{{ asset('js/reception-user-link.js') }}"></script>

@endsection



