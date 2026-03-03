@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Historial de Ventas')

@section('content')
<style>
  .ventas-container {
    padding: 30px;
  }
  .page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
  }
  .page-subtitle {
    color: #666;
    margin-bottom: 25px;
    font-size: 0.95rem;
  }
  .ventas-table {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
  }
  .ventas-table table {
    width: 100%;
    border-collapse: collapse;
  }
  .ventas-table thead {
    background: linear-gradient(135deg, #f5f5f5 0%, #ebebeb 100%);
    border-bottom: 2px solid #ddd;
  }
  .ventas-table thead th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .ventas-table tbody tr {
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s;
  }
  .ventas-table tbody tr:hover {
    background-color: #f9f9f9;
  }
  .ventas-table tbody td {
    padding: 14px 16px;
    color: #555;
  }
  .status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
  }
  .status-completado {
    background-color: #d4edda;
    color: #155724;
  }
  .status-pendiente {
    background-color: #fff3cd;
    color: #856404;
  }
  .btn-ver {
    display: inline-block;
    padding: 8px 14px;
    background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.85rem;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .btn-ver:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    color: white;
    text-decoration: none;
  }
  .empty-state {
    text-align: center;
    padding: 50px 20px;
    color: #999;
  }
  .empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
  }
</style>

<div class="ventas-container">
  <div class="page-title">
    <i class="bi bi-receipt" style="margin-right: 10px; color: #4CAF50;"></i>Historial de Ventas
  </div>
  <div class="page-subtitle">Gestión completa de todas las compras realizadas en el minibar</div>

  <div class="ventas-table">
    <div class="table-responsive">
      <table style="min-width: 750px; margin-bottom: 0;">
      <thead>
        <tr>
          <th style="width: 60px;">#</th>
          <th style="width: 200px;">Cliente</th>
          <th style="width: 120px;" class="text-center">Total</th>
          <th style="width: 100px;" class="text-center">Estado</th>
          <th style="width: 180px;" class="text-center">Fecha</th>
          <th style="width: 100px;" class="text-center">Acción</th>
        </tr>
      </thead>
      <tbody>
        @forelse($compras as $compra)
        <tr>
          <td class="fw-semibold">#{{ $compra->id }}</td>
          <td>{{ $compra->user?->name ?? 'Sin usuario' }}</td>
          <td class="text-center fw-semibold" style="color: #4CAF50;">${{ number_format($compra->total, 2) }}</td>
          <td class="text-center">
            <span class="status-badge status-{{ strtolower($compra->estado) }}">
              {{ ucfirst($compra->estado) }}
            </span>
          </td>
          <td class="text-center text-muted" style="font-size: 0.9rem;">
            {{ $compra->created_at->setTimezone('America/Bogota')->format('d/m/Y h:i A') }}
          </td>
          <td class="text-center">
            <a href="{{ route('admin.minibar.ventas.show', $compra) }}" class="btn-ver">
              <i class="bi bi-eye"></i> Ver
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <i class="bi bi-inbox"></i>
              <p style="margin-top: 10px; font-size: 1.1rem;">No hay ventas registradas</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>

  @if($compras instanceof \Illuminate\Pagination\Paginator && $compras->hasPages())
  <div class="mt-4">
    {{ $compras->links() }}
  </div>
  @endif
</div>
@endsection

