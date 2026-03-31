@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.mantenimiento-sidebar';
@endphp

@section('content')
<div class="maintenance-container" style="padding: 40px; background: #f5f5f5; min-height: 100vh;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold text-dark mb-1">
                <i class="bi bi-clock-history me-2 text-danger"></i>
                Historial de Mantenimiento
            </h1>
            <p class="text-muted mb-0">Habitación {{ $room->number }} — {{ $room->roomtype->name ?? 'N/A' }}</p>
        </div>
        <a href="{{ route('admin.mantenimiento.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Volver a Gestión
        </a>
    </div>

    <div class="bg-white rounded-4 shadow-sm p-4 border-0">
        @include('components.admin.mantenimiento.history-list')
    </div>
</div>
@endsection
