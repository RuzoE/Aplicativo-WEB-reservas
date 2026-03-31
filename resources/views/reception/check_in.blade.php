@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.sidebar';
@endphp

<link rel="stylesheet" href="{{ asset('css/blade/reception/check_in--style1.css') }}">

<div
    class="checkin-form-page"
    id="checkin-page-config"
    data-stay-nights="{{ (int) $reservation->stayDays }}"
    data-default-room-type="{{ $reservation->roomType->name ?? 'N/A' }}"
    data-default-room-price="{{ (float) ($reservation->total_amount / max(1, $reservation->stayDays)) }}"
>
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
                <p class="mb-0" id="reservation-room-display">{{ $reservation->room_number ?? 'Sin reservar' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Tipo:</strong>
                <p class="mb-0" id="reservation-room-type-display">{{ $reservation->roomType->name ?? 'N/A' }}</p>
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
                <p class="mb-0" id="reservation-rate-display">
                    @php
                        $nights = max(1, $reservation->stayDays);
                        $rate = (float)($reservation->total_amount / $nights);
                    @endphp
                    ${{ number_format($rate, 0, ',', '.') }}
                </p>
            </div>
            <div class="col-md-3">
                <strong>Total:</strong>
                <p class="mb-0 fw-bold" style="color: #2196F3;" id="reservation-total-display">${{ number_format($reservation->total_amount, 0, ',', '.') }}</p>
            </div>
            <div class="col-md-3">
                <strong>Usuario:</strong>
                <p class="mb-0">{{ $reservation->user->name ?? 'Sin asignar' }}</p>
            </div>
        </div>
    </div>

    <!-- Formulario de Datos del Huésped -->
    <div class="form-card">
        <h4 class="mb-3"><i class="bi bi-person-fill"></i> Datos del Huésped</h4>
        <form method="POST" action="{{ route('reception.checkin.store', $reservation->id) }}" novalidate>
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Habitación <span class="text-danger">*</span></label>
                    <select name="room_number" id="room-number-select" class="form-select form-select-lg @error('room_number') is-invalid @enderror" required>
                        <option value="">Seleccione una habitación...</option>
                        @foreach($roomNumberOptions as $roomOption)
                            <option value="{{ $roomOption['number'] }}"
                                data-status="{{ $roomOption['status'] }}"
                                data-room-type="{{ $roomOption['room_type'] }}"
                                data-room-price="{{ $roomOption['price'] }}"
                                @selected((string) old('room_number') === (string) $roomOption['number'])>
                                Habitación {{ $roomOption['number'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_number')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Se listan todas las habitaciones activas.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Estado de la habitación</label>
                    <input type="text" id="room-status-display" class="form-control form-control-lg status-badge" value="Seleccione una habitación" readonly tabindex="-1" />
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control form-control-lg @error('first_name') is-invalid @enderror" required value="{{ old('first_name') }}" />
                    @error('first_name')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Apellido <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control form-control-lg @error('last_name') is-invalid @enderror" required value="{{ old('last_name') }}" />
                    @error('last_name')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tipo de documento <span class="text-danger">*</span></label>
                    <select name="document_type" class="form-select form-select-lg @error('document_type') is-invalid @enderror" required>
                        <option value="">Seleccione un tipo...</option>
                        <option value="CC" @selected(old('document_type') === 'CC')>Cédula de Ciudadanía (CC)</option>
                        <option value="CE" @selected(old('document_type') === 'CE')>Cédula de Extranjería (CE)</option>
                        <option value="PA" @selected(old('document_type') === 'PA')>Pasaporte (PA)</option>
                        <option value="NIT" @selected(old('document_type') === 'NIT')>NIT</option>
                        <option value="TI" @selected(old('document_type') === 'TI')>Tarjeta de Identidad (TI)</option>
                    </select>
                    @error('document_type')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Número de documento <span class="text-danger">*</span></label>
                    <input type="text" name="document_number" class="form-control form-control-lg @error('document_number') is-invalid @enderror" required value="{{ old('document_number') }}" />
                    @error('document_number')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" required value="{{ old('email') }}" pattern="^[^\s@]+@(gmail\.com|hotmail\.com)$" title="Solo se permiten correos @gmail.com o @hotmail.com" />
                    @error('email')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Teléfono <span class="text-danger">*</span></label>
                    <input type="tel" name="phone" class="form-control form-control-lg @error('phone') is-invalid @enderror" required value="{{ old('phone') }}" inputmode="tel" minlength="10" maxlength="16" data-phone-sanitize="true" pattern="^(3\d{9}|(?:\+57|57)3\d{9}|\+\d{8,15}|\d{8,15})$" title="Si inicia en 3 debe tener 10 dígitos (Colombia). También se acepta formato internacional válido." />
                    @error('phone')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">País</label>
                    <input type="text" name="country" class="form-control form-control-lg @error('country') is-invalid @enderror" placeholder="Ej: Colombia" value="{{ old('country') }}" />
                    @error('country')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Notas adicionales</label>
                    <textarea name="notes" class="form-control form-control-lg @error('notes') is-invalid @enderror" rows="2" placeholder="Información adicional del huésped...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 mt-4 d-flex gap-2">
                    <button type="submit" id="submitBtn" class="btn-submit flex-grow-1" style="position: relative; overflow: hidden;">
                        <span class="spinner-border spinner-border-sm me-2" id="spinner" role="status" aria-hidden="true" style="display:none; width: 16px; height: 16px;"></span>
                        <i class="bi bi-check-circle" id="submitIcon"></i>
                        <span id="submitText">Completar Check-in</span>
                    </button>
                    <a href="{{ route('reception.dashboard') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                </div>

                <script src="{{ asset('js/reception-checkin.js') }}"></script>
            </div>
        </form>
    </div>
</div>

@endsection



