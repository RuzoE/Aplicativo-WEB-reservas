@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
@endphp
<style>
  .rooms-container {
    padding: 40px 20px;
    background: #f5f5f5;
    min-height: 100vh;
  }
  .rooms-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
  }
  .rooms-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .rooms-title i {
    color: #FFC107;
    font-size: 2rem;
  }
  .btn-create-room {
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
  .btn-create-room:hover {
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
  .rooms-table-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
  }
  .table {
    margin-bottom: 0;
  }
  .table thead {
    background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
  }
  .table thead th {
    color: white;
    font-weight: 600;
    border: none;
    padding: 16px 12px;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
  }
  .table tbody tr {
    border-top: 1px solid #f0f0f0;
    transition: background-color 0.2s;
  }
  .table tbody tr:hover {
    background-color: #fafafa;
    box-shadow: inset 0 0 10px rgba(255, 193, 7, 0.08);
  }
  .table tbody td {
    padding: 14px 12px;
    vertical-align: middle;
    color: #333;
  }
  .room-image {
    width: 50px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  .room-name {
    font-weight: 600;
    color: #FF9800;
    padding: 6px 12px;
    background: #fff3e0;
    border-radius: 6px;
    display: inline-block;
    border-left: 3px solid #FFC107;
  }
  .status-active {
    color: #2E7D32;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .status-active::before {
    content: '';
    width: 8px;
    height: 8px;
    background: #2E7D32;
    border-radius: 50%;
  }
  .status-inactive {
    color: #C62828;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .status-inactive::before {
    content: '';
    width: 8px;
    height: 8px;
    background: #C62828;
    border-radius: 50%;
  }
  .btn-edit-room {
    padding: 8px 12px;
    background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .btn-edit-room:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(255, 193, 7, 0.3);
    color: white;
    text-decoration: none;
  }
  .btn-delete-room {
    padding: 8px 12px;
    background: #EF5350;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .btn-delete-room:hover {
    transform: translateY(-1px);
    background: #E53935;
    box-shadow: 0 4px 10px rgba(239, 83, 80, 0.3);
  }
  .btn-actions {
    display: flex;
    gap: 8px;
  }
  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
  }
  .empty-state i {
    font-size: 3rem;
    color: #FFC107;
    margin-bottom: 15px;
    display: block;
  }
  .empty-state p {
    font-size: 1.1rem;
    font-weight: 600;
    color: #666;
    margin: 0;
  }
  @media (max-width: 768px) {
    .rooms-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 15px;
    }
    .table {
      font-size: 0.85rem;
    }
    .table thead th,
    .table tbody td {
      padding: 10px 8px;
    }
    .btn-actions {
      flex-direction: column;
    }
    .btn-edit-room,
    .btn-delete-room {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<div class="rooms-container">
  @include('components.show-success')

  <div class="rooms-header">
    <div class="rooms-title">
      <i class="bi bi-door-closed"></i>
      Habitaciones
    </div>
    <a href="{{ route('admin.habitaciones.rooms.create') }}" class="btn-create-room">
      <i class="bi bi-plus-lg"></i> Nueva Habitación
    </a>
  </div>

  <div class="divider-line"></div>

  <div class="rooms-table-wrapper">
    @forelse($rooms as $room)
      @if($loop->first)
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Tipo</th>
              <th scope="col">Total</th>
              <th scope="col">Camas</th>
              <th scope="col">Precio</th>
              <th scope="col">Imagen</th>
              <th scope="col">Estado</th>
              <th scope="col">Acciones</th>
            </tr>
          </thead>
          <tbody>
      @endif
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>
                <span class="room-name">{{ $room->roomtype->name }}</span>
              </td>
              <td>{{ $room->total_room }}</td>
              <td>{{ $room->no_beds }}</td>
              <td><strong>@cop($room->price)</strong></td>
              <td>
                @if($room->image)
                  <img src="{{ asset($room->image) }}" alt="{{ $room->roomtype->name }}" class="room-image">
                @else
                  <span style="color: #999; font-size: 0.85rem;">Sin imagen</span>
                @endif
              </td>
              <td>
                @if($room->status)
                  <span class="status-active">Activa</span>
                @else
                  <span class="status-inactive">Inactiva</span>
                @endif
              </td>
              <td>
                <div class="btn-actions">
                  <a href="{{ route('admin.habitaciones.rooms.edit', ['room' => $room->id]) }}" class="btn-edit-room">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form method="post" action="{{ route('admin.habitaciones.rooms.destroy', ['room' => $room->id]) }}" style="display: inline; margin: 0;">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn-delete-room" onclick="return confirm('¿Estás seguro de que quieres eliminar esta habitación?')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
      @if($loop->last)
          </tbody>
        </table>
      @endif
    @empty
      <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <p>No tienes creada ninguna habitación</p>
        <p style="font-size: 0.9rem; color: #999; margin-top: 10px;">Comienza creando tu primera habitación</p>
      </div>
    @endforelse
  </div>
</div>
@endsection
