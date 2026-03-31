@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
@endphp

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/admin/orders/index--style1.css') }}">

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
          $ingresosCOP = $orders->sum('total_amount');
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
              <td><span class="room-name">{{ $order->room->roomtype->name ?? ($order->roomType->name ?? 'N/A') }}</span></td>
              <td class="date-cell">
                <strong>{{ $order->check_in->format('d/m/Y') }}</strong>
              </td>
              <td class="date-cell">
                <strong>{{ $order->check_out->format('d/m/Y') }}</strong>
              </td>
              <td class="price-cell">{{ '$' . number_format($order->total_amount, 0, ',', '.') }}</td>
              <td class="reserved-date-cell">{{ $order->created_at->format('d/m/Y h:i A') }}</td>
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


