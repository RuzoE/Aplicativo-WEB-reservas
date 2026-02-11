{{-- resources/views/admin/minibar/bebidas/index.blade.php --}}
@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Bebidas')

@section('content')
<style>
  .bebidas-container {
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
  .btn-create-floating {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    transition: transform 0.2s, box-shadow 0.2s;
    text-decoration: none;
  }
  .btn-create-floating:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(76, 175, 80, 0.4);
    color: white;
  }
  .alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    border-radius: 8px;
    margin-bottom: 25px;
  }
  .bebidas-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
  }
  .bebidas-table {
    width: 100%;
    border-collapse: collapse;
  }
  .bebidas-table thead {
    background: linear-gradient(135deg, #f5f5f5 0%, #ebebeb 100%);
    border-bottom: 2px solid #ddd;
  }
  .bebidas-table thead th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .bebidas-table tbody tr {
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s;
  }
  .bebidas-table tbody tr:hover {
    background-color: #f9f9f9;
  }
  .bebidas-table tbody td {
    padding: 14px 16px;
    color: #555;
  }
  .bebida-name {
    font-weight: 600;
    color: #333;
  }
  .bebida-type {
    display: inline-block;
    padding: 4px 10px;
    background: #e8f5e9;
    color: #2e7d32;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
  }
  .bebida-price {
    color: #4CAF50;
    font-weight: 700;
  }
  .stock-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
  }
  .stock-good {
    background: #d4edda;
    color: #155724;
  }
  .stock-warning {
    background: #fff3cd;
    color: #856404;
  }
  .stock-low {
    background: #f8d7da;
    color: #721c24;
  }
  .bebida-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
  }
  .actions-cell {
    display: flex;
    gap: 8px;
    align-items: center;
  }
  .btn-edit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
    color: white;
    border-radius: 6px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    font-size: 0.9rem;
  }
  .btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 152, 0, 0.3);
    color: white;
    text-decoration: none;
  }
  .btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    background: linear-gradient(135deg, #EF5350 0%, #D32F2F 100%);
    color: white;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    font-size: 0.9rem;
  }
  .btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(239, 83, 80, 0.3);
  }
  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
  }
  .empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
  }
</style>

<div class="bebidas-container">
  {{-- Alerta de éxito --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 20px;">
      <i class="bi bi-check-circle me-2"></i>
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  {{-- Header con título y botón flotante --}}
  <div class="page-header">
    <h1 class="page-title">
      <i class="bi bi-cup-straw me-2" style="color: #4CAF50;"></i>Bebidas
    </h1>
    <a href="{{ route('admin.minibar.bebidas.create') }}" class="btn-create-floating" title="Crear nueva bebida">
      +
    </a>
  </div>

  {{-- Tabla de bebidas --}}
  <div class="bebidas-card">
    <div class="table-responsive">
      <table class="bebidas-table">
        <thead>
          <tr>
            <th style="width: 80px;">#</th>
            <th style="width: 180px;">Nombre</th>
            <th style="width: 140px;">Tipo</th>
            <th style="width: 120px;" class="text-right">Precio</th>
            <th style="width: 100px;" class="text-center">Stock</th>
            <th style="width: 80px;" class="text-center">Imagen</th>
            <th style="width: 120px;" class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bebidas as $bebida)
            <tr>
              <td class="fw-semibold">#{{ $loop->iteration }}</td>
              <td>
                <div class="bebida-name">{{ $bebida->nombre }}</div>
              </td>
              <td>
                <span class="bebida-type">{{ $bebida->type->nombre ?? '-' }}</span>
              </td>
              <td class="text-right bebida-price">${{ number_format($bebida->precio, 2) }}</td>
              <td class="text-center">
                <span class="stock-badge {{ $bebida->stock >= 20 ? 'stock-good' : ($bebida->stock >= 10 ? 'stock-warning' : 'stock-low') }}">
                  {{ $bebida->stock }}
                </span>
              </td>
              <td class="text-center">
                @if($bebida->imagen)
                  <img src="{{ asset('storage/' . $bebida->imagen) }}" alt="{{ $bebida->nombre }}" class="bebida-image">
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td>
                <div class="actions-cell" style="justify-content: center;">
                  <a href="{{ route('admin.minibar.bebidas.edit', $bebida) }}"
                     class="btn-edit"
                     title="Editar">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form action="{{ route('admin.minibar.bebidas.destroy', $bebida) }}"
                        method="POST"
                        style="display: inline;"
                        onsubmit="return confirm('¿Estás seguro de eliminar esta bebida?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn-delete" title="Eliminar">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7">
                <div class="empty-state">
                  <i class="bi bi-inbox"></i>
                  <p style="font-size: 1.1rem; margin-top: 10px;">No hay bebidas registradas</p>
                  <p style="color: #ccc; margin-top: 5px;">Haz clic en el botón + para crear una nueva</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Paginación --}}
  @if($bebidas->hasPages())
    <div class="mt-4">
      {{ $bebidas->links() }}
    </div>
  @endif
</div>
@endsection
