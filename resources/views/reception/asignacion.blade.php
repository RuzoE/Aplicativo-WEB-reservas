@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.sidebar';
@endphp

<div class="container-fluid px-4 py-5 font-sans">
    <div class="mb-5 border-bottom pb-4">
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <h1 class="display-5 fw-bold text-primary mb-1">
                    <i class="bi bi-building-fill me-2"></i>Tablero de Habitaciones
                </h1>
                <p class="text-muted fs-5 mb-0">Gestión visual del estado de las habitaciones y asignación de huéspedes.</p>
            </div>
        </div>
    </div>

    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($selectedOrder)
        <div class="alert alert-info border-0 rounded-4 p-4 mb-5 d-flex align-items-center shadow-sm bg-blue-50">
            <div class="p-3 bg-blue-100 rounded-circle me-4">
                <i class="bi bi-info-circle-fill text-blue-600 fs-2"></i>
            </div>
            <div class="flex-grow-1">
                <h4 class="alert-heading fw-black mb-1 text-blue-900">Modo Asignación Activo</h4>
                <p class="mb-0 fs-5 text-blue-800">Seleccione una habitación disponible (verde) para asignar a <strong>{{ $selectedOrder->nombre_cliente }}</strong>.</p>
                <div class="mt-2">
                    <span class="badge bg-blue-600 px-3 py-1 rounded-pill">Preferencia: {{ $selectedOrder->roomType->name }}</span>
                </div>
            </div>
            <a href="{{ route('reception.asignacion.index') }}" class="btn btn-outline-secondary ms-4 fw-bold px-4 rounded-xl">
                Cancelar
            </a>
        </div>
    @endif

    <!-- Vue Component Root -->
    <div
        id="room-assignment-board-root"
        data-rooms='@json($rooms)'
        data-selected-order='@json($selectedOrder)'
        data-assign-url="{{ route('reception.asignacion.confirm', ['reserva' => $selectedOrder->id ?? 0, 'room' => '__ROOM_ID__']) }}"
        data-csrf-token="{{ csrf_token() }}"
        data-rooms-by-date-url="{{ route('reception.asignacion.rooms_by_date') }}"
    ></div>

</div>

<!-- Scripts -->

@push('styles')
    @vite(['resources/css/app.css'])
@endpush

@push('scripts')
    @vite(['resources/js/app.js'])
@endpush

<link rel="stylesheet" href="{{ asset('css/blade/reception/asignacion--style1.css') }}">
@endsection


