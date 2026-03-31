@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Detalle de la Compra')

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/admin/minibar/ventas/show--style1.css') }}">

<div class="detail-container">
  <a href="{{ route('admin.minibar.ventas.index') }}" class="back-link">
    <i class="bi bi-arrow-left"></i>
    Volver al historial
  </a>

  <div class="detail-card">
    <div class="detail-title">
      <i class="bi bi-receipt-cutoff"></i>
      Compra #{{ $compra->id }}
    </div>

    <div class="info-grid">
      <div class="info-item">
        <div class="info-label">Cliente</div>
        <div class="info-value">{{ $compra->user?->name ?? 'Sin usuario' }}</div>
      </div>
      <div class="info-item">
        <div class="info-label">Método de Pago</div>
        <div class="info-value">{{ ucfirst($compra->metodo_pago) }}</div>
      </div>
      <div class="info-item">
        <div class="info-label">Estado</div>
        <div class="info-value">
          <span class="badge" style="background: {{ $compra->estado === 'completado' ? '#4CAF50' : '#FFC107' }}; color: white; padding: 6px 12px; border-radius: 20px;">
            {{ ucfirst($compra->estado) }}
          </span>
        </div>
      </div>
      <div class="info-item">
        <div class="info-label">Fecha</div>
        <div class="info-value">{{ $compra->created_at->setTimezone('America/Bogota')->format('d/m/Y h:i A') }}</div>
      </div>
    </div>
  </div>

  <div class="detail-card">
    <h3 style="font-size: 1.3rem; font-weight: 700; color: #333; margin-bottom: 20px;">
      <i class="bi bi-bag" style="margin-right: 8px; color: #4CAF50;"></i>
      Detalles de Productos
    </h3>

    <div class="table-responsive">
      <table class="products-table">
        <thead>
          <tr>
            <th style="width: 40%;">Producto</th>
            <th style="width: 15%;" class="text-center">Cantidad</th>
            <th style="width: 20%;" class="text-right">Precio Unitario</th>
            <th style="width: 25%;" class="text-right">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($compra->productos as $producto)
            <tr>
              <td>{{ $producto->nombre }}</td>
              <td class="text-center">{{ $producto->pivot->cantidad }}</td>
              <td class="text-right">${{ number_format($producto->pivot->precio_unitario, 2) }}</td>
              <td class="text-right amount-value">
                ${{ number_format($producto->pivot->cantidad * $producto->pivot->precio_unitario, 2) }}
              </td>
            </tr>
          @endforeach
          <tr class="total-row">
            <td colspan="3" class="text-right">Total a Pagar:</td>
            <td class="text-right">${{ number_format($compra->total, 2) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection



