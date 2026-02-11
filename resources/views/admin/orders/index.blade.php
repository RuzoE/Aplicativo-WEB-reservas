@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
@endphp

@section('content')
<style>
  .reservas-admin-container {
    padding: 40px 20px;
    background: #f5f5f5;
    min-height: 100vh;
  }
  .reservas-admin-header {
    margin-bottom: 40px;
  }
  .reservas-admin-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .reservas-admin-title i {
    color: #FFC107;
    font-size: 1.8rem;
  }
  .reservas-admin-subtitle {
    font-size: 0.95rem;
    color: #666;
    margin-bottom: 0;
  }
  .divider-line {
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, #FFC107 0%, #FF9800 100%);
    margin: 20px 0;
    border-radius: 2px;
  }
  .reservas-table-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
  }
  .table {
    margin-bottom: 0;
  }
  .table thead {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
  }
  .table thead th {
    padding: 18px 16px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    vertical-align: middle;
  }
  .table thead th i {
    margin-right: 6px;
    opacity: 0.9;
  }
  .table tbody tr {
    border-bottom: 1px solid #e8e8e8;
    transition: all 0.2s;
  }
  .table tbody tr:hover {
    background-color: #f9f9f9;
    box-shadow: inset 0 0 8px rgba(255, 193, 7, 0.08);
  }
  .table tbody td {
    padding: 16px;
    vertical-align: middle;
    color: #333;
    font-size: 0.95rem;
  }
  .room-name {
    font-weight: 600;
    color: #2c3e50;
    padding: 8px 12px;
    background: #f0f0f0;
    border-radius: 6px;
    display: inline-block;
  }
  .date-cell {
    color: #666;
    font-size: 0.9rem;
  }
  .date-cell strong {
    display: block;
    color: #2c3e50;
  }
  .price-cell {
    font-weight: 700;
    color: #FFC107;
    font-size: 1.05rem;
  }
  .reserved-date-cell {
    color: #999;
    font-size: 0.85rem;
  }
  .empty-state-container {
    text-align: center;
    padding: 80px 40px;
  }
  .empty-state-icon {
    font-size: 4rem;
    color: #e0e0e0;
    margin-bottom: 20px;
  }
  .empty-state-text {
    font-size: 1.1rem;
    color: #999;
    margin-bottom: 10px;
  }
  .empty-state-subtext {
    font-size: 0.95rem;
    color: #bbb;
  }
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }
  .stat-card {
    background: white;
    border-radius: 12px;
    padding: 22px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-left: 4px solid #FFC107;
    transition: all 0.2s;
  }
  .stat-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    transform: translateY(-2px);
  }
  .stat-icon {
    font-size: 1.8rem;
    color: #FFC107;
    margin-bottom: 12px;
  }
  .stat-card:nth-child(2) .stat-icon {
    font-size: 2.2rem;
  }
  .stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #333;
  }
  .stat-label {
    font-size: 0.85rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 5px;
  }
  @media (max-width: 768px) {
    .reservas-admin-title {
      font-size: 1.5rem;
    }
    .table thead th {
      padding: 12px 8px;
      font-size: 0.75rem;
    }
    .table tbody td {
      padding: 12px 8px;
      font-size: 0.85rem;
    }
    .room-name {
      padding: 6px 10px;
      font-size: 0.9rem;
    }
  }
</style>

<div class="reservas-admin-container">
  <div class="reservas-admin-header">
    <div class="reservas-admin-title">
      <i class="bi bi-calendar-check"></i>
      Gestión de Reservas
    </div>
    <p class="reservas-admin-subtitle">Historial completo de todas las reservaciones del hotel</p>
    <div class="divider-line"></div>
  </div>

  @if($orders->count() > 0)
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="bi bi-calendar-event"></i>
        </div>
        <div class="stat-number">{{ $orders->count() }}</div>
        <div class="stat-label">Total Reservas</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="bi bi-currency-dollar"></i>
        </div>
        @php
          $ingresosCOP = $orders->sum(function($o) {
            return (float)$o->room->price * (int)$o->stayDays;
          });
        @endphp
        <div class="stat-number">{{ '$' . number_format($ingresosCOP, 0, ',', '.') }}</div>
        <div class="stat-label">Ingresos Total</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="bi bi-door-closed"></i>
        </div>
        <div class="stat-number">{{ $orders->count() }}</div>
        <div class="stat-label">Habitaciones</div>
      </div>
    </div>

    <div class="reservas-table-card">
      <table class="table">
        <thead>
          <tr>
            <th scope="col"><i class="bi bi-door-closed"></i>Nombre Habitación</th>
            <th scope="col"><i class="bi bi-box-arrow-in-right"></i>Check in</th>
            <th scope="col"><i class="bi bi-box-arrow-right"></i>Check out</th>
            <th scope="col"><i class="bi bi-cash-coin"></i>Precio Total</th>
            <th scope="col"><i class="bi bi-clock-history"></i>Reservado</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
            <tr>
              <td><span class="room-name">{{ $order->room->roomtype->name }}</span></td>
              <td class="date-cell">
                <strong>{{ $order->check_in->setTimezone('America/Bogota')->format('d/m/Y') }}</strong>
                <small>{{ $order->check_in->setTimezone('America/Bogota')->format('h:i A') }}</small>
              </td>
              <td class="date-cell">
                <strong>{{ $order->check_out->setTimezone('America/Bogota')->format('d/m/Y') }}</strong>
                <small>{{ $order->check_out->setTimezone('America/Bogota')->format('h:i A') }}</small>
              </td>
              <td class="price-cell">@cop($order->room->price, $order->stayDays)</td>
              <td class="reserved-date-cell">{{ $order->created_at->setTimezone('America/Bogota')->format('d/m/Y h:i A') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5">
                <div class="empty-state-container" style="padding: 60px 20px;">
                  <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                  </div>
                  <p class="empty-state-text">No hay reservas aún</p>
                  <p class="empty-state-subtext">Las nuevas reservaciones aparecerán aquí</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  @else
    <div class="reservas-table-card">
      <div class="empty-state-container">
        <div class="empty-state-icon">
          <i class="bi bi-inbox"></i>
        </div>
        <p class="empty-state-text">No hay reservas aún</p>
        <p class="empty-state-subtext">Las nuevas reservaciones aparecerán aquí</p>
      </div>
    </div>
  @endif
</div>

@endsection
