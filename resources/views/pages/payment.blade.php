@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Pago de Anticipo (30%)</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Detalles de la Reserva</h5>
                            <p class="mb-1"><strong>Habitación:</strong> {{ $order->roomType->name ?? 'Estándar' }}</p>
                            <p class="mb-1"><strong>Check-in:</strong> {{ $order->check_in->format('d/m/Y') }}</p>
                            <p class="mb-1"><strong>Check-out:</strong> {{ $order->check_out->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5>Resumen de Pago</h5>
                            <p class="mb-1">Total Estancia: @cop($order->total_amount)</p>
                            <h3 class="text-primary mt-2">Anticipo: @cop($order->down_payment_amount)</h3>
                        </div>
                    </div>

                    <hr>

                    <div class="alert alert-info py-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Esta es una página de simulación de pago. Al hacer clic en el botón, el sistema registrará el pago del anticipo de forma automática.
                    </div>

                    <form action="{{ route('orders.confirm_payment', ['token' => $order->payment_token]) }}" method="POST">
                        @csrf
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-credit-card me-2"></i> Simular Pago Exitoso
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center bg-light">
                    <p class="mb-0 text-muted">¿Necesitas ayuda? Llámanos al +57 300 123 4567</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
