@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Historial de Ventas')

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/admin/minibar/ventas/index--style1.css') }}">

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



