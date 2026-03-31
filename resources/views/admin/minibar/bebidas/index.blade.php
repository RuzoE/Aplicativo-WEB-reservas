{{-- resources/views/admin/minibar/bebidas/index.blade.php --}}
@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Bebidas')

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/admin/minibar/bebidas/index--style1.css') }}">

<div class="bebidas-container">

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
                      data-confirm-message="¿Estás seguro de eliminar esta bebida?">
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


