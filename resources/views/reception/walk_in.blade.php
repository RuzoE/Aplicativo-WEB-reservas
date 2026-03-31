@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.sidebar';
@endphp

<link rel="stylesheet" href="{{ asset('css/blade/reception/walk_in--style1.css') }}">

<div class="checkin-form-page">
    <div class="form-header">
        <h2><i class="bi bi-person-plus"></i> Registro Directo (Walk-In)</h2>
        <p class="text-muted">Crear un nuevo registro para un huésped sin reserva previa</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <h4 class="mb-4"><i class="bi bi-person-lines-fill"></i> Datos del Huésped y Estadía</h4>

        <form method="POST" action="{{ route('reception.walkin.store') }}" novalidate>
            @csrf

            <div class="info-card">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Días de estadía <span class="text-danger">*</span></label>
                        <input type="number" name="stay_days" id="stay_days" class="form-control form-control-lg @error('stay_days') is-invalid @enderror" required value="{{ old('stay_days', 1) }}" min="1">
                        <small class="text-muted">Cantidad de noches que el huésped planea quedarse.</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Adultos <span class="text-danger">*</span></label>
                        <input type="number" name="adults" class="form-control form-control-lg @error('adults') is-invalid @enderror" required value="{{ old('adults', 1) }}" min="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Niños</label>
                        <input type="number" name="children" class="form-control form-control-lg @error('children') is-invalid @enderror" value="{{ old('children', 0) }}" min="0">
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Habitación <span class="text-danger">*</span></label>
                    <select name="room_number" id="room-number-select" class="form-select form-select-lg @error('room_number') is-invalid @enderror" required>
                        <option value="">Seleccione una habitación...</option>
                        @foreach($roomNumberOptions as $roomOption)
                            <option value="{{ $roomOption->number }}"
                                data-status="{{ $roomOption->status }}"
                                data-room-type="{{ $roomOption->room_type }}"
                                data-room-price="{{ $roomOption->price }}"
                                @selected((string) old('room_number') === (string) $roomOption->number)>
                                Habitación {{ $roomOption->number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Estado de la habitación</label>
                    <input type="text" id="room-status-display" class="form-control form-control-lg status-badge" value="Seleccione una habitación" readonly tabindex="-1" />
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label fw-bold">Tarifa por noche</label>
                    <input type="text" id="room-price-display" class="form-control form-control-lg" value="$0,00" readonly />
                </div>

                <!-- Datos Personales -->
                <div class="col-12 mt-4">
                    <hr>
                    <h5 class="fw-bold mb-3">Información Personal</h5>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control form-control-lg @error('first_name') is-invalid @enderror" required value="{{ old('first_name') }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Apellido <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control form-control-lg @error('last_name') is-invalid @enderror" required value="{{ old('last_name') }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tipo de documento <span class="text-danger">*</span></label>
                    <select name="document_type" class="form-select form-select-lg @error('document_type') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        <option value="CC" @selected(old('document_type') === 'CC')>Cédula de Ciudadanía (CC)</option>
                        <option value="CE" @selected(old('document_type') === 'CE')>Cédula de Extranjería (CE)</option>
                        <option value="PA" @selected(old('document_type') === 'PA')>Pasaporte (PA)</option>
                        <option value="NIT" @selected(old('document_type') === 'NIT')>NIT</option>
                        <option value="TI" @selected(old('document_type') === 'TI')>Tarjeta de Identidad (TI)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Número de documento <span class="text-danger">*</span></label>
                    <input type="text" name="document_number" class="form-control form-control-lg @error('document_number') is-invalid @enderror" required value="{{ old('document_number') }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" required value="{{ old('email') }}" pattern="^[^\s@]+@(gmail\.com|hotmail\.com)$" title="Solo se permiten correos @gmail.com o @hotmail.com" />
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Teléfono <span class="text-danger">*</span></label>
                    <input type="tel" name="phone" class="form-control form-control-lg @error('phone') is-invalid @enderror" required value="{{ old('phone') }}" inputmode="tel" minlength="10" maxlength="16" data-phone-sanitize="true" pattern="^(3\d{9}|(?:\+57|57)3\d{9}|\+\d{8,15}|\d{8,15})$" title="Si inicia en 3 debe tener 10 dígitos (Colombia). También se acepta formato internacional válido." />
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">País</label>
                    <input type="text" name="country" class="form-control form-control-lg @error('country') is-invalid @enderror" placeholder="Ej: Colombia" value="{{ old('country') }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Notas adicionales</label>
                    <textarea name="notes" class="form-control form-control-lg @error('notes') is-invalid @enderror" rows="1" placeholder="Información adicional...">{{ old('notes') }}</textarea>
                </div>

                <div class="col-12 mt-4 d-flex gap-2">
                    <button type="submit" id="submitBtn" class="btn-submit flex-grow-1" style="position: relative; overflow: hidden;">
                        <span class="spinner-border spinner-border-sm me-2" id="spinner" role="status" aria-hidden="true" style="display:none; width: 16px; height: 16px;"></span>
                        <i class="bi bi-check-circle" id="submitIcon"></i>
                        <span id="submitText">Registrar Huésped</span>
                    </button>
                    <a href="{{ route('reception.dashboard') }}" class="btn btn-secondary" style="padding: 12px 30px; font-weight: 600; border-radius: 8px;">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/blade/reception/walk_in--script1.js') }}"></script>

@endsection


