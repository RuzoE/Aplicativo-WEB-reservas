@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.sidebar';
@endphp

<div class="container-fluid py-4 px-md-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 gap-3">
        <div>
            <h1 class="display-4 fw-bold mb-2" style="color: #2196F3;">
                <i class="bi bi-cash-stack me-3"></i>Gestión de Anticipos
            </h1>
            <p class="text-muted fs-5 mb-0">Listado de reservas con pago de anticipo del 30% confirmado, pendientes de asignación de habitación.</p>
        </div>
        <div class="bg-white p-4 rounded-4 shadow-sm border-start border-5 d-flex align-items-center" style="border-left-color: #2196F3 !important;">
            <div class="me-3 p-3 rounded-circle text-primary" style="background-color: rgba(33, 150, 243, 0.1); color: #2196F3 !important;">
                <i class="bi bi-hourglass-split fs-3"></i>
            </div>
            <div>
                <div class="h2 mb-0 fw-black text-dark">{{ $reservations->count() }}</div>
                <div class="text-muted text-uppercase small fw-bold mt-n1">Pendientes</div>
            </div>
        </div>
    </div>

    @if($reservations->isEmpty())
        <div class="alert alert-light border-0 shadow-sm p-5 text-center rounded-4 border-dashed">
            <i class="bi bi-info-circle display-4 text-muted mb-4 d-block"></i>
            <h3 class="fw-bold text-dark">No hay anticipos pendientes</h3>
            <p class="fs-5 text-muted mb-0 mx-auto" style="max-width: 500px;">Todas las reservas con anticipo ya han sido procesadas o no hay nuevas llegadas registradas desde el sitio web.</p>
        </div>
    @else
        <!-- Desktop View: Table (Hidden on small screens) -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-none d-lg-block">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-white" style="background-color: #2196F3;">
                        <tr>
                            <th class="ps-4 py-4 text-uppercase fs-xs fw-bold border-0">Huésped / Cliente</th>
                            <th class="py-4 text-uppercase fs-xs fw-bold border-0">Tipo de Habitación</th>
                            <th class="py-4 text-uppercase fs-xs fw-bold border-0">Fecha Reserva</th>
                            <th class="py-4 text-uppercase fs-xs fw-bold border-0 text-center">Estado Pago</th>
                            <th class="pe-4 py-4 text-uppercase fs-xs fw-bold text-end border-0">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($reservations as $reserva)
                            <tr class="transition-all">
                                <td class="ps-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-light rounded-circle d-flex align-items-center justify-content-center me-3 border shadow-sm" style="width: 50px; height: 50px;">
                                            <i class="bi bi-person fs-4" style="color: #2196F3;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-5 mb-0">
                                                {{ $reserva->nombre_cliente ?: ($reserva->user->name ?? 'Invitado') }}
                                            </div>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <span class="small px-2 py-0 rounded text-dark fw-bold border" style="background-color: #f1f5f9; font-size: 0.75rem; border-color: #2196F3 !important; color: #2196F3 !important;">ID: #{{ str_pad($reserva->id, 5, '0', STR_PAD_LEFT) }}</span>
                                                <span class="text-muted" style="font-size: 0.75rem;">&bull; Web Reservation</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <span class="badge bg-white px-3 py-2 rounded-pill fw-semibold shadow-sm" style="color: #2196F3; border: 1px solid rgba(33, 150, 243, 0.25);">
                                        <i class="bi bi-door-closed me-2"></i>
                                        {{ $reserva->roomType->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <div class="text-dark fw-bold mb-0">
                                        {{ $reserva->check_in->format('d M, Y') }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-clock-history me-1"></i> {{ $reserva->check_in->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="py-4 text-center">
                                    <span class="badge border border-success border-opacity-25 px-3 py-2 rounded-pill fw-bold" style="background-color: rgba(25, 135, 84, 0.1); color: #198754;">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        Abono 30% Pagado
                                    </span>
                                </td>
                                <td class="pe-4 py-4 text-end">
                                    <a href="{{ route('reception.asignacion.index', ['reserva' => $reserva->id]) }}" 
                                       class="btn btn-blue btn-lg rounded-pill px-4 shadow-sm hover-elevate fw-bold transition-all btn-subtle-shade">
                                        <i class="bi bi-plus-circle me-2"></i>Asignar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile/Tablet View: Cards (Hidden on large screens) -->
        <div class="d-lg-none">
            <div class="row g-4">
                @foreach($reservations as $reserva)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 transition-all hover-elevate">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 55px; height: 55px; background-color: rgba(33, 150, 243, 0.1); color: #2196F3 !important;">
                                            <i class="bi bi-person fs-3"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold text-dark mb-0">{{ $reserva->nombre_cliente ?: ($reserva->user->name ?? 'Invitado') }}</h5>
                                            <span class="small px-2 py-0 rounded text-dark fw-bold border mt-1 d-inline-block" style="background-color: #f1f5f9; font-size: 0.7rem;">ID: #{{ str_pad($reserva->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                    <span class="badge py-2 px-3 rounded-pill fw-bold small" style="background-color: rgba(25, 135, 84, 0.1); color: #198754; border: 1px solid rgba(25, 135, 84, 0.25);">
                                        <i class="bi bi-check2-circle"></i> 30%
                                    </span>
                                </div>

                                <div class="bg-light rounded-4 p-3 mb-4">
                                    <div class="row align-items-center">
                                        <div class="col-6 border-end">
                                            <div class="text-muted small text-uppercase fw-bold mb-1">Habitación</div>
                                            <div class="fw-bold" style="color: #2196F3;">
                                                <i class="bi bi-door-closed me-1"></i>
                                                {{ $reserva->roomType->name ?? 'N/A' }}
                                            </div>
                                        </div>
                                        <div class="col-6 ps-3">
                                            <div class="text-muted small text-uppercase fw-bold mb-1">Check-in</div>
                                            <div class="fw-bold text-dark">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $reserva->check_in->format('d/m/y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <a href="{{ route('reception.asignacion.index', ['reserva' => $reserva->id]) }}" 
                                       class="btn btn-blue btn-lg rounded-pill shadow-sm py-3 fw-bold btn-subtle-shade">
                                        <i class="bi bi-building-up me-2"></i>Asignar Habitación
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<link rel="stylesheet" href="{{ asset('css/blade/reception/anticipos--style1.css') }}">
@endsection


