@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.sidebar';
@endphp

<link rel="stylesheet" href="{{ asset('css/blade/reception/folio--style1.css') }}">

<div class="folio-page">
    @if(session('success'))
        <div class="success-alert">
            <i class="bi bi-check-circle"></i>
            <div>
                <h4>¡Check-in Completado!</h4>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="folio-header">
        <h2><i class="bi bi-receipt"></i> Folio de la Estancia</h2>
        <p class="text-muted">Gestion de cargos y pagos</p>
    </div>

    <div class="info-card">
        <h4><i class="bi bi-info-circle"></i> Informacion de la Estancia</h4>
        <div class="row g-3">
            <div class="col-md-3">
                <strong>Estancia ID:</strong>
                <p class="mb-0">{{ $stay->id }}</p>
            </div>
            <div class="col-md-3">
                <strong>Huesped:</strong>
                <p class="mb-0">{{ optional($stay->guest)->first_name }} {{ optional($stay->guest)->last_name }}</p>
            </div>
            <div class="col-md-3">
                <strong>Habitacion:</strong>
                <p class="mb-0 text-primary fw-bold">{{ $stay->assigned_room_number ?? 'N/A' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Estado:</strong>
                <p class="mb-0"><span class="badge bg-primary">{{ $stay->status == 'InHouse' ? 'En Casa' : $stay->status }}</span></p>
            </div>
        </div>
    </div>

    @if($folio)
        <div class="info-card">
            <h4><i class="bi bi-file-text"></i> Datos del Folio</h4>
            <div class="row g-3">
                <div class="col-md-3">
                    <strong>Numero de Folio:</strong>
                    <p class="mb-0 text-primary fw-bold">{{ $folio->number }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Estado:</strong>
                    <p class="mb-0"><span class="badge bg-warning">{{ $folio->status == 'Open' ? 'Abierto' : $folio->status }}</span></p>
                </div>
                <div class="col-md-3">
                    <strong>Moneda:</strong>
                    <p class="mb-0">{{ $folio->currency }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Balance:</strong>
                    <p class="mb-0 text-danger fw-bold">${{ number_format($folio->balance, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="folio-grid">
            <div class="folio-section">
                <h4><i class="bi bi-plus-circle"></i> Cargos</h4>
                @if($folio->charges->count() > 0)
                    <ul class="charges-list">
                        @foreach($folio->charges as $charge)
                            <li>
                                <strong>{{ $charge->description }}</strong><br>
                                <small>{{ $charge->posted_at?->format('d/m/Y H:i') ?? 'N/A' }}</small><br>
                                <strong class="text-danger">${{ number_format($charge->amount + ($charge->tax ?? 0), 2) }}</strong>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted"><i class="bi bi-info-circle"></i> Sin cargos registrados</p>
                @endif

                <h5 class="mt-4 mb-3" style="color: #333; font-weight: 600;">Agregar Cargo</h5>
                <form method="POST" action="{{ route('reception.folio.charge', $stay->id) }}">
                    @csrf
                    <div class="form-group">
                        <label>Fuente</label>
                        <select name="source" required>
                            <option value="Recepcion">Recepcion</option>
                            <option value="Minibar">Minibar</option>
                            <option value="Restaurante">Restaurante</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Descripcion</label>
                        <input type="text" name="description" placeholder="Ej: Minibar, Room Service..." required>
                    </div>
                    <div class="form-group">
                        <label>Monto</label>
                        <input type="number" name="amount" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label>Impuesto (opcional)</label>
                        <input type="number" name="tax" step="0.01" placeholder="0.00">
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-plus-circle"></i> Agregar Cargo
                    </button>
                </form>
            </div>

            <div class="folio-section">
                <h4><i class="bi bi-cash-coin"></i> Pagos</h4>
                @if($folio->payments->count() > 0)
                    <ul class="payments-list">
                        @foreach($folio->payments as $payment)
                            <li>
                                <strong>{{ $payment->method }}</strong> - <span>{{ $payment->description ?? 'Sin descripcion' }}</span><br>
                                <small>{{ $payment->received_at?->format('d/m/Y H:i') ?? 'N/A' }}</small> | <small>Ref: {{ $payment->external_ref ?? 'N/A' }}</small><br>
                                <strong class="text-success">${{ number_format($payment->amount, 2) }}</strong>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted"><i class="bi bi-info-circle"></i> Sin pagos registrados</p>
                @endif

                <h5 class="mt-4 mb-3" style="color: #333; font-weight: 600;">Registrar Pago</h5>
                <form method="POST" action="{{ route('reception.folio.payment', $stay->id) }}">
                    @csrf
                    <input type="hidden" name="currency" value="COP">
                    <div class="form-group">
                        <label>Metodo de Pago</label>
                        <select name="method" required>
                            <option value="">Seleccione...</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta Debito">Tarjeta Debito</option>
                            <option value="Tarjeta Credito">Tarjeta Credito</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Monto</label>
                        <input type="number" name="amount" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label>Descripcion</label>
                        <input type="text" name="description" placeholder="Abono a estancia, anticipo, etc.">
                    </div>
                    <div class="form-group">
                        <label>Referencia Externa (opcional)</label>
                        <input type="text" name="external_ref" placeholder="N de transaccion, comprobante...">
                    </div>
                    <button type="submit" class="btn-success">
                        <i class="bi bi-check-circle"></i> Registrar Pago
                    </button>
                </form>
            </div>
        </div>

        <div style="margin-top: 30px; text-align: center;">
            <form method="POST" action="{{ route('reception.checkout.store', $stay->id) }}" style="display: inline;" data-confirm-message="¿Completar check-out ahora? Esta accion registrara la salida.">
                @csrf
                <button type="submit" class="btn btn-lg btn-danger" style="padding: 15px 40px; font-weight: 600;">
                    <i class="bi bi-box-arrow-in-right"></i> Completar Check-out
                </button>
            </form>
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-triangle"></i> No se encontro folio abierto para esta estancia.
        </div>
    @endif
</div>

@endsection
