@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Detalle de la Compra')

@section('content')
<style>
  .detail-container {
    padding: 30px;
    background: #f5f5f5;
    min-height: 100vh;
  }
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #4CAF50;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 20px;
    transition: gap 0.2s;
  }
  .back-link:hover {
    gap: 12px;
    color: #2E7D32;
  }
  .detail-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 30px;
    margin-bottom: 20px;
  }
  .detail-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .detail-title i {
    color: #4CAF50;
  }
  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }
  .info-item {
    padding: 15px;
    background: #f9f9f9;
    border-radius: 8px;
    border-left: 4px solid #4CAF50;
  }
  .info-label {
    font-size: 0.8rem;
    color: #999;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 5px;
    letter-spacing: 0.5px;
  }
  .info-value {
    font-size: 1.1rem;
    color: #333;
    font-weight: 600;
  }
  .products-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
  }
  .products-table thead {
    background: linear-gradient(135deg, #f5f5f5 0%, #ebebeb 100%);
    border-bottom: 2px solid #ddd;
  }
  .products-table th {
    padding: 14px 16px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 0.85rem;
    text-transform: uppercase;
  }
  .products-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #eee;
    color: #555;
  }
  .products-table tbody tr:hover {
    background-color: #f9f9f9;
  }
  .products-table tbody tr:last-child td {
    border-bottom: none;
  }
  .total-row {
    background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
    color: white !important;
    font-weight: 700;
    font-size: 1.1rem;
  }
  .total-row td {
    border: none;
    color: white;
  }
  .amount-value {
    color: #4CAF50;
    font-weight: 700;
    font-size: 1.05rem;
  }
</style>

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

