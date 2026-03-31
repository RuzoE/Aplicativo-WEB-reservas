{{-- resources/views/admin/minibar/bebida-types/index.blade.php --}}
@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Tipos de Bebida')

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/admin/minibar/bebida-types/index--style1.css') }}">

<div class="bebida-types-container">

  {{-- Header con título y botón flotante --}}
  <div class="page-header">
    <h1 class="page-title">
      <i class="bi bi-bookmark-fill me-2" style="color: #4CAF50;"></i>Tipos de Bebida
    </h1>
    <a href="{{ route('admin.minibar.bebida-types.create') }}" class="btn-create-floating" title="Crear nuevo tipo">
      +
    </a>
  </div>

  {{-- Tabla de tipos --}}
  <div class="types-card">
    <table class="types-table">
      <thead>
        <tr>
          <th style="width: 80px;">#</th>
          <th style="flex: 1;">Nombre</th>
          <th style="width: 150px;" class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($types as $type)
          <tr>
            <td class="fw-semibold">#{{ $loop->iteration }}</td>
            <td>
              <div class="type-name">{{ $type->nombre }}</div>
            </td>
            <td>
              <div class="actions-cell" style="justify-content: center;">
                <a href="{{ route('admin.minibar.bebida-types.edit', $type) }}"
                   class="btn-edit"
                   title="Editar">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('admin.minibar.bebida-types.destroy', $type) }}"
                      method="POST"
                      style="display: inline;"
                      data-confirm-message="¿Estás seguro de eliminar este tipo de bebida?">
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
            <td colspan="3">
              <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p style="font-size: 1.1rem; margin-top: 10px;">No hay tipos de bebida registrados</p>
                <p style="color: #ccc; margin-top: 5px;">Haz clic en el botón + para crear uno nuevo</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginación --}}
  @if($types->hasPages())
    <div class="mt-4">
      {{ $types->links() }}
    </div>
  @endif
</div>
@endsection


