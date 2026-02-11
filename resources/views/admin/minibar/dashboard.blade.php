@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

<style>
    .minibar-dashboard {
        padding: 30px;
    }

    .page-header {
        margin-bottom: 40px;
        border-bottom: 3px solid #4CAF50;
        padding-bottom: 20px;
    }

    .page-header h1 {
        color: #4CAF50;
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
        border-left: 5px solid #4CAF50;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .stat-card .icon {
        font-size: 3rem;
        color: #4CAF50;
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
        background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
        color: white;
        border-radius: 10px;
        text-decoration: none;
        font-size: 1.2rem;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        color: white;
        text-decoration: none;
    }

    .action-btn i {
        margin-right: 10px;
        font-size: 1.5rem;
    }

    .action-btn.secondary {
        background: linear-gradient(135deg, #2196F3 0%, #1565C0 100%);
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
    }

    .action-btn.secondary:hover {
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
    }

    .action-btn.tertiary {
        background: linear-gradient(135deg, #FF9800 0%, #FF6F00 100%);
        box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
    }

    .action-btn.tertiary:hover {
        box-shadow: 0 6px 20px rgba(255, 152, 0, 0.4);
    }
</style>

<div class="minibar-dashboard">
    <div class="page-header">
        <h1><i class="bi bi-cup-straw"></i> Dashboard Minibar</h1>
        <p>Gestión de productos, tipos de bebidas y compras del minibar</p>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <div class="icon">
                <i class="bi bi-collection"></i>
            </div>
            <h3>{{ $totalProducts }}</h3>
            <p>Total de Productos</p>
        </div>

        <div class="stat-card">
            <div class="icon">
                <i class="bi bi-bag-check"></i>
            </div>
            <h3>{{ $totalCompras }}</h3>
            <p>Total de Compras</p>
        </div>
    </div>

    <h2 style="color: #333; margin-bottom: 20px; font-weight: 700;">
        <i class="bi bi-gear"></i> Gestión del Minibar
    </h2>

    <div class="action-buttons">
        <a href="{{ route('admin.minibar.ventas.index') }}" class="action-btn">
            <i class="bi bi-receipt"></i>
            Ver Todas las Compras
        </a>

        <a href="{{ route('admin.minibar.bebida-types.index') }}" class="action-btn secondary">
            <i class="bi bi-bookmark-fill"></i>
            Gestionar Tipos de Bebida
        </a>

        <a href="{{ route('admin.minibar.bebidas.index') }}" class="action-btn tertiary">
            <i class="bi bi-cup-straw"></i>
            Gestionar Bebidas
        </a>
    </div>
</div>
@endsection
