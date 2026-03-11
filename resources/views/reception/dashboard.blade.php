@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.sidebar';
@endphp

<style>
    .reception-dashboard {
        padding: 40px 60px;
        max-width: 100%;
        width: 100%;
    }

    .page-header {
        margin-bottom: 40px;
        border-bottom: 3px solid #2196F3;
        padding-bottom: 20px;
    }

    .page-header h1 {
        color: #2196F3;
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
        border-left: 5px solid #2196F3;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .stat-card .icon {
        font-size: 3rem;
        color: #2196F3;
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
        background: linear-gradient(135deg, #2196F3 0%, #1565C0 100%);
        color: white;
        border-radius: 10px;
        text-decoration: none;
        font-size: 1.2rem;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
    }

    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
        color: white;
        text-decoration: none;
    }

    .action-btn i {
        margin-right: 10px;
        font-size: 1.5rem;
    }

    .action-btn.secondary {
        background: linear-gradient(135deg, #64B5F6 0%, #1976D2 100%);
        box-shadow: 0 4px 12px rgba(100, 181, 246, 0.3);
    }

    .action-btn.secondary:hover {
        box-shadow: 0 6px 20px rgba(100, 181, 246, 0.4);
    }

    .action-btn.tertiary {
        background: linear-gradient(135deg, #1565C0 0%, #0D47A1 100%);
        box-shadow: 0 4px 12px rgba(21, 101, 192, 0.3);
    }

    .action-btn.tertiary:hover {
        box-shadow: 0 6px 20px rgba(21, 101, 192, 0.4);
    }
</style>

<div class="reception-dashboard">

    <div id="section-dashboard">
        <div class="page-header">
            <h1><i class="bi bi-reception-4"></i> Recepción — Tablero</h1>
            <p>Resumen rápido de llegadas, salidas y huéspedes en casa</p>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <div class="icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <h3>{{ $arrivals->count() ?? 0 }}</h3>
                <p>Llegadas de hoy</p>
            </div>

            <div class="stat-card">
                <div class="icon">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <h3>{{ $departures->count() ?? 0 }}</h3>
                <p>Salidas de hoy</p>
            </div>

            <div class="stat-card">
                <div class="icon">
                    <i class="bi bi-people"></i>
                </div>
                <h3>{{ $inHouse->count() ?? 0 }}</h3>
                <p>Huéspedes en casa</p>
            </div>
        </div>

        <h2 style="color: #333; margin-bottom: 20px; font-weight: 700;">
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
        @include('reception.partials.checkin-form')
    </div>

    <div id="section-folio" style="display:none;">
        @include('reception.partials.folio-form')
    </div>

    <div id="section-checkout" style="display:none;">
        @include('reception.partials.checkout-form')
    </div>

    <script>
    window.ReceptionConfig = {
        dashboardUrl:       '{{ route('reception.dashboard') }}',
        showCheckinSection: @json((bool) session('show_checkin_section'))
    };
    </script>
    <script src="{{ asset('js/reception-dashboard.js') }}"></script>

@endsection

