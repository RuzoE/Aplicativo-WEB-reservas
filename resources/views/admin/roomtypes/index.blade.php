@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
@endphp

@section('content')
<style>
  .roomtypes-container {
    padding: 40px 20px;
    background: #f5f5f5;
    min-height: 100vh;
  }
  .roomtypes-header {
    margin-bottom: 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
  }
  .roomtypes-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .roomtypes-title i {
    color: #FFC107;
    font-size: 1.8rem;
  }
  .btn-create-type {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
    color: white !important;
    text-decoration: none;
    font-size: 1.5rem;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
    position: relative;
    overflow: hidden;
  }
  .btn-create-type i {
    color: white !important;
    font-size: 1.8rem !important;
  }
  .btn-create-type-text {
    font-weight: 700;
    font-size: 0.75rem;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    line-height: 1;
  }
  .btn-create-type:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 193, 7, 0.4);
    color: white;
  }
  .roomtypes-table-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
  }
  .table {
    margin-bottom: 0;
  }
  .table thead {
    background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
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
  .type-number {
    font-weight: 600;
    color: #999;
    font-size: 0.9rem;
  }
  .type-name {
    font-weight: 600;
    color: #FF9800;
    padding: 8px 12px;
    background: #fff3e0;
    border-radius: 6px;
    display: inline-block;
    border-left: 4px solid #FFC107;
  }
  .actions-cell {
    display: flex;
    gap: 8px;
    align-items: center;
  }
  .btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.9rem;
    text-decoration: none;
  }
  .btn-edit {
    background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
    color: white;
    box-shadow: 0 2px 6px rgba(255, 193, 7, 0.3);
  }
  .btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 193, 7, 0.4);
    color: white;
    text-decoration: none;
  }
  .btn-delete {
    background: linear-gradient(135deg, #EF5350 0%, #C62828 100%);
    color: white;
    box-shadow: 0 2px 6px rgba(239, 83, 80, 0.3);
    border: none;
  }
  .btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(239, 83, 80, 0.4);
    color: white;
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
    margin-bottom: 30px;
  }
  .btn-create-first {
    display: inline-block;
    padding: 12px 28px;
    background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
  }
  .btn-create-first:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 193, 7, 0.4);
    color: white;
    text-decoration: none;
  }
  .divider-line {
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, #FFC107 0%, #FF9800 100%);
    margin: 20px 0 30px;
    border-radius: 2px;
  }
  @media (max-width: 768px) {
    .roomtypes-header {
      flex-direction: column;
      align-items: flex-start;
    }
    .roomtypes-title {
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
    .actions-cell {
      flex-direction: column;
    }
  }
</style>

<div class="roomtypes-container">

  <div class="roomtypes-header">
    <div class="roomtypes-title">
      <i class="bi bi-collection"></i>
      {{ __('messages.roomtypes') }}
    </div>
    <a href="{{ route('admin.habitaciones.tipos-habitacion.create') }}" class="btn-create-type" title="{{ __('messages.create') }}">
      <span class="btn-create-type-text">{{ __('messages.create') }}</span>
    </a>
  </div>

  <div class="divider-line"></div>

  @if($types->count() > 0)
    <div class="roomtypes-table-card">
      <table class="table">
        <thead>
          <tr>
            <th scope="col" style="width: 50px;"><i class="bi bi-list-ol"></i></th>
            <th scope="col"><i class="bi bi-door-closed"></i>{{ __('messages.name') }}</th>
            <th scope="col" style="width: 150px;"><i class="bi bi-gear"></i>{{ __('messages.action') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($types as $type)
            <tr>
              <td class="type-number">{{ $loop->iteration }}</td>
              <td><span class="type-name">{{ $type->name }}</span></td>
              <td>
                <div class="actions-cell">
                  <a href="{{ route('admin.habitaciones.tipos-habitacion.edit', ['tipos_habitacion' => $type->id]) }}" class="btn-action btn-edit" title="{{ __('messages.edit') }}">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  <form method="post" action="{{ route('admin.habitaciones.tipos-habitacion.destroy', ['tipos_habitacion' => $type->id]) }}" style="display: inline;">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn-action btn-delete" title="{{ __('messages.delete') }}" onclick="return confirm('¿Está seguro de que desea eliminar este tipo de habitación?');">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
          @endforelse
        </tbody>
      </table>
    </div>
  @else
    <div class="roomtypes-table-card">
      <div class="empty-state-container">
        <div class="empty-state-icon">
          <i class="bi bi-inbox"></i>
        </div>
        <p class="empty-state-text">No hay tipos de habitación aún</p>
        <p class="empty-state-subtext">Crea tu primer tipo de habitación para comenzar</p>
        <a href="{{ route('admin.habitaciones.tipos-habitacion.create') }}" class="btn-create-first">
          <i class="bi bi-plus-lg me-2"></i>{{ __('messages.create') }} {{ __('messages.roomtypes') }}
        </a>
      </div>
    </div>
  @endif
</div>

@endsection
