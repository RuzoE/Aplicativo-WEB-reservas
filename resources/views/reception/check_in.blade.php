@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.sidebar';
@endphp

<style>
    .checkin-form-page {
        padding: 40px 60px;
        max-width: 100%;
    }

    .form-header {
        margin-bottom: 30px;
        border-bottom: 3px solid #2196F3;
        padding-bottom: 20px;
    }

    .form-header h2 {
        color: #2196F3;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        max-width: 900px;
        margin: 0 auto;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        max-width: 900px;
        margin: 0 auto;
        border-left: 5px solid #2196F3;
    }

    .info-card h4 {
        color: #2196F3;
        font-weight: 700;
        font-size: 1.2rem;
    }

    .info-card strong {
        color: #666;
        font-size: 0.9rem;
        display: block;
        margin-bottom: 5px;
    }

    .info-card p {
        color: #333;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .btn-submit {
        background: linear-gradient(135deg, #2196F3 0%, #1565C0 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
        color: white;
    }
</style>

<div class="checkin-form-page">
    <div class="form-header">
        <h2><i class="bi bi-door-open"></i> Completar Check-in</h2>
        <p class="text-muted">Reserva: RES-{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <!-- Información de la Reserva -->
    <div class="info-card mb-4">
        <h4 class="mb-3"><i class="bi bi-info-circle"></i> Información de la Reserva</h4>
        <div class="row g-3">
            <div class="col-md-3">
                <strong>Habitación:</strong>
                <p class="mb-0">{{ $reservation->room->room_number ?? 'Sin asignar' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Tipo:</strong>
                <p class="mb-0">{{ $reservation->room->roomtype->name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Check-in:</strong>
                <p class="mb-0">{{ $reservation->check_in->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-3">
                <strong>Check-out:</strong>
                <p class="mb-0">{{ $reservation->check_out->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-3">
                <strong>Noches:</strong>
                <p class="mb-0">{{ $reservation->stayDays }}</p>
            </div>
            <div class="col-md-3">
                <strong>Tarifa/noche:</strong>
                <p class="mb-0">${{ number_format($reservation->room->price ?? 0, 2) }}</p>
            </div>
            <div class="col-md-3">
                <strong>Total:</strong>
                <p class="mb-0 text-primary fw-bold">${{ number_format($reservation->stayDays * ($reservation->room->price ?? 0), 2) }}</p>
            </div>
            <div class="col-md-3">
                <strong>Usuario:</strong>
                <p class="mb-0">{{ $reservation->user->name }}</p>
            </div>
        </div>
    </div>

    <!-- Formulario de Datos del Huésped -->
    <div class="form-card">
        <h4 class="mb-3"><i class="bi bi-person-fill"></i> Datos del Huésped</h4>
        <form method="POST" action="{{ route('reception.checkin.store', $reservation->id) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                    <input name="first_name" class="form-control form-control-lg" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Apellido <span class="text-danger">*</span></label>
                    <input name="last_name" class="form-control form-control-lg" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tipo de documento</label>
                    <select name="document_type" class="form-select form-select-lg">
                        <option value="">Seleccione...</option>
                        <option value="DNI">DNI</option>
                        <option value="Pasaporte">Pasaporte</option>
                        <option value="Cédula">Cédula</option>
                        <option value="Carnet de Extranjería">Carnet de Extranjería</option>
                        <option value="RUC">RUC</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Número de documento</label>
                    <input name="document_number" class="form-control form-control-lg" />
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control form-control-lg" />
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Teléfono</label>
                    <input name="phone" class="form-control form-control-lg" />
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-circle"></i> Completar Check-in
                    </button>
                    <a href="{{ route('reception.dashboard') }}#checkin" class="btn btn-secondary ms-2">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

