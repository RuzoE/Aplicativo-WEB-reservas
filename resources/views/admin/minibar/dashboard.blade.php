@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

<link rel="stylesheet" href="{{ asset('css/blade/admin/minibar/dashboard--style1.css') }}">

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


