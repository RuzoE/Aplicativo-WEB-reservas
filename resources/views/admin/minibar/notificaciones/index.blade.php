{{-- resources/views/admin/minibar/notificaciones/index.blade.php --}}
@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Notificaciones de Stock')

@section('content')
<style>
  .notif-container {
    padding: 30px;
  }
  .page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 30px;
  }
  .page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin: 0;
  }
  .notif-summary-card {
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    gap: 20px;
    border: 1px solid;
  }
  .summary-danger {
    background: linear-gradient(135deg, #fff5f5 0%, #ffe3e3 100%);
    border-color: #ffc9c9;
    color: #c92a2a;
  }
  .summary-success {
    background: linear-gradient(135deg, #ebfbee 0%, #d3f9d8 100%);
    border-color: #b2f2bb;
    color: #2b8a3e;
  }
  .summary-icon {
    font-size: 2.5rem;
  }
  .notif-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
  }
  .notif-item-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    border: 1px solid #eee;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
  }
  .notif-item-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
  }
  .card-top {
    padding: 20px;
    display: flex;
    gap: 15px;
    align-items: center;
    border-bottom: 1px solid #f5f5f5;
  }
  .prod-img-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e9ecef;
  }
  .prod-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .prod-details {
    flex: 1;
  }
  .prod-name {
    font-weight: 700;
    font-size: 1.1rem;
    color: #212529;
    margin-bottom: 4px;
  }
  .prod-type {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    background: #e9ecef;
    color: #495057;
  }
  .card-body-content {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 15px;
  }
  .stock-status-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .stock-amount-label {
    font-size: 0.9rem;
    color: #868e96;
  }
  .stock-amount-val {
    font-size: 1.3rem;
    font-weight: 800;
  }
  .stock-badge-notif {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .badge-critical {
    background-color: #fff0f6;
    color: #d6336c;
    border: 1px solid #ffdeeb;
  }
  .badge-low {
    background-color: #fff9db;
    color: #f08c00;
    border: 1px solid #ffe3e3;
  }
  .btn-surtir {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
    color: white;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
  }
  .btn-surtir:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
    color: white;
  }
  .empty-state-card {
    background: white;
    border-radius: 12px;
    padding: 50px 30px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
    max-width: 600px;
    margin: 50px auto;
  }
  .success-icon-wrapper {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #ebfbee;
    color: #40c057;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    font-size: 3rem;
  }
</style>

<div class="notif-container">

  {{-- Header --}}
  <div class="page-header">
    <h1 class="page-title">
      <i class="bi bi-bell me-2" style="color: #FF9800;"></i>Notificaciones
    </h1>
  </div>

  @if($lowStockProducts->count() > 0)
    {{-- Summary banner --}}
    <div class="notif-summary-card summary-danger">
      <div class="summary-icon">
        <i class="bi bi-exclamation-triangle-fill"></i>
      </div>
      <div>
        <h4 class="fw-bold mb-1">Stock de Bebidas Bajo</h4>
        <p class="mb-0">Hay <strong>{{ $lowStockProducts->count() }}</strong> bebida(s) que tienen stock crítico o bajo y necesitan ser reabastecidas.</p>
      </div>
    </div>

    {{-- Grid layout --}}
    <div class="notif-grid">
      @foreach($lowStockProducts as $bebida)
        @php
          $isCritical = $bebida->stock <= 2;
        @endphp
        <div class="notif-item-card">
          <div class="card-top">
            <div class="prod-img-wrapper">
              @if($bebida->imagen)
                <img src="{{ $bebida->image_url }}" alt="{{ $bebida->nombre }}" class="prod-img">
              @else
                <i class="bi bi-cup-straw text-muted fs-3"></i>
              @endif
            </div>
            <div class="prod-details">
              <div class="prod-name">{{ $bebida->nombre }}</div>
              <span class="prod-type">{{ $bebida->type->nombre ?? 'Sin Tipo' }}</span>
            </div>
          </div>
          <div class="card-body-content">
            <div class="stock-status-info">
              <div>
                <span class="stock-amount-label">Cantidad actual:</span>
                <div class="stock-amount-val" style="color: {{ $isCritical ? '#d6336c' : '#f08c00' }}">
                  {{ $bebida->stock }} unidades
                </div>
              </div>
              <span class="stock-badge-notif {{ $isCritical ? 'badge-critical' : 'badge-low' }}">
                {{ $isCritical ? 'Crítico' : 'Bajo' }}
              </span>
            </div>
            
            <a href="{{ route('admin.minibar.bebidas.edit', $bebida) }}" class="btn-surtir">
              <i class="bi bi-arrow-repeat"></i> Surtir Inventario
            </a>
          </div>
        </div>
      @endforeach
    </div>
  @else
    {{-- Safe empty state --}}
    <div class="empty-state-card">
      <div class="success-icon-wrapper">
        <i class="bi bi-check-circle-fill"></i>
      </div>
      <h3 class="fw-bold mb-2" style="color: #2b8a3e;">¡Inventario al Día!</h3>
      <p class="text-muted fs-5 mb-0">Todas las bebidas en el minibar tienen stock suficiente en este momento. No hay alertas de reabastecimiento.</p>
    </div>
  @endif

</div>
@endsection
