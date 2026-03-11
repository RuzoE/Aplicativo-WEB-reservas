@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.sidebar';
@endphp

<style>
    .folio-page {
        padding: 40px 60px;
        max-width: 100%;
    }

    .success-alert {
        background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
        color: white;
        padding: 20px 25px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        border-left: 5px solid #1B7A27;
        display: flex;
        align-items: center;
        gap: 15px;
        animation: slideDown 0.4s ease-out;
    }

    .success-alert i {
        font-size: 1.8rem;
    }

    .success-alert h4 {
        margin: 0 0 5px 0;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .success-alert p {
        margin: 0;
        font-size: 0.95rem;
        opacity: 0.95;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .folio-header {
        margin-bottom: 30px;
        border-bottom: 3px solid #2196F3;
        padding-bottom: 20px;
    }

    .folio-header h2 {
        color: #2196F3;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 30px;
        border-left: 5px solid #2196F3;
    }

    .info-card h4 {
        color: #2196F3;
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 15px;
    }

    .folio-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }

    .folio-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .folio-section h4 {
        color: #333;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e0e0e0;
    }

    .charges-list, .payments-list {
        list-style: none;
        padding: 0;
        margin-bottom: 20px;
    }

    .charges-list li, .payments-list li {
        padding: 12px;
        background: #fafafa;
        margin-bottom: 10px;
        border-radius: 6px;
        border-left: 4px solid #FFC107;
        font-size: 0.9rem;
    }

    .payments-list li {
        border-left-color: #4CAF50;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2196F3 0%, #1565C0 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(33, 150, 243, 0.4);
    }

    .btn-success {
        background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(76, 175, 80, 0.4);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: #333;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .form-group input, .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }

    .form-group input:focus, .form-group select:focus {
        outline: none;
        border-color: #2196F3;
        box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
    }
</style>

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
        <p class="text-muted">Gestión de cargos y pagos</p>
    </div>

    <!-- Información del Folio -->
    <div class="info-card">
        <h4><i class="bi bi-info-circle"></i> Información de la Estancia</h4>
        <div class="row g-3">
            <div class="col-md-3">
                <strong>Estancia ID:</strong>
                <p class="mb-0">{{ $stay->id }}</p>
            </div>
            <div class="col-md-3">
                <strong>Huésped:</strong>
                <p class="mb-0">{{ optional($stay->guest)->first_name }} {{ optional($stay->guest)->last_name }}</p>
            </div>
            <div class="col-md-3">
                <strong>Habitación:</strong>
                <p class="mb-0">{{ optional($stay->room)->room_number ?? 'N/A' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Estado:</strong>
                <p class="mb-0"><span class="badge bg-info">{{ $stay->status }}</span></p>
            </div>
        </div>
    </div>

    @if($folio)
        <div class="info-card">
            <h4><i class="bi bi-file-text"></i> Datos del Folio</h4>
            <div class="row g-3">
                <div class="col-md-3">
                    <strong>Número de Folio:</strong>
                    <p class="mb-0 text-primary fw-bold">{{ $folio->number }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Estado:</strong>
                    <p class="mb-0"><span class="badge bg-warning">{{ $folio->status }}</span></p>
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
            <!-- Cargos -->
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
                        <label>Descripción</label>
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

            <!-- Pagos -->
            <div class="folio-section">
                <h4><i class="bi bi-cash-coin"></i> Pagos</h4>
                @if($folio->payments->count() > 0)
                    <ul class="payments-list">
                        @foreach($folio->payments as $payment)
                            <li>
                                <strong>{{ $payment->method }}</strong><br>
                                <small>{{ $payment->received_at?->format('d/m/Y H:i') ?? 'N/A' }}</small><br>
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
                    <div class="form-group">
                        <label>Método de Pago</label>
                        <select name="method" required>
                            <option value="">Seleccione...</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta Débito">Tarjeta Débito</option>
                            <option value="Tarjeta Crédito">Tarjeta Crédito</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Monto</label>
                        <input type="number" name="amount" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label>Referencia Externa (opcional)</label>
                        <input type="text" name="external_ref" placeholder="Nº de transacción, comprobante...">
                    </div>
                    <button type="submit" class="btn-success">
                        <i class="bi bi-check-circle"></i> Registrar Pago
                    </button>
                </form>
            </div>
        </div>

        <!-- Check-out -->
        <div style="margin-top: 30px; text-align: center;">
            <form method="POST" action="{{ route('reception.checkout.store', $stay->id) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-lg btn-danger" style="padding: 15px 40px; font-weight: 600;">
                    <i class="bi bi-box-arrow-in-right"></i> Completar Check-out
                </button>
            </form>
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-triangle"></i> No se encontró folio abierto para esta estancia.
        </div>
    @endif
</div>

@endsection

