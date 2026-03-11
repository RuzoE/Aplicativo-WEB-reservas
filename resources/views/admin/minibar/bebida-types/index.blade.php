{{-- resources/views/admin/minibar/bebida-types/index.blade.php --}}
@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Tipos de Bebida')

@section('content')
<style>
  .bebida-types-container {
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
  .types-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
  }
  .types-table {
    width: 100%;
    border-collapse: collapse;
  }
  .types-table thead {
    background: linear-gradient(135deg, #f5f5f5 0%, #ebebeb 100%);
    border-bottom: 2px solid #ddd;
  }
  .types-table thead th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .types-table tbody tr {
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s;
  }
  .types-table tbody tr:hover {
    background-color: #f9f9f9;
  }
  .types-table tbody td {
    padding: 14px 16px;
    color: #555;
  }
  .type-name {
    font-weight: 600;
    color: #333;
    font-size: 1rem;
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
                      onsubmit="return confirm('¿Estás seguro de eliminar este tipo de bebida?');">
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
