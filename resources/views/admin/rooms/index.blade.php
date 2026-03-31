@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
@endphp
<link rel="stylesheet" href="{{ asset('css/blade/admin/rooms/index--style1.css') }}">

<div class="rooms-container">

  <div class="rooms-header">
    <div class="rooms-title">
      <i class="bi bi-door-closed"></i>
      {{ __('messages.rooms') }}
    </div>
    <a href="{{ route('admin.habitaciones.habitaciones.create') }}" class="btn-create-room">
      <i class="bi bi-plus-lg"></i> {{ __('messages.create') }} {{ __('messages.rooms') }}
    </a>
  </div>

  <div class="divider-line"></div>

  <div class="rooms-table-wrapper">
    @forelse($rooms as $room)
      @if($loop->first)
        <div class="table-responsive">
          <table class="table mb-0">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('messages.type') }}</th>
                <th scope="col">Total</th>
                <th scope="col">{{ __('messages.beds') }}</th>
                <th scope="col">{{ __('messages.price') }}</th>
                <th scope="col">{{ __('messages.image') }}</th>
                <th scope="col">{{ __('messages.status') }}</th>
                <th scope="col">{{ __('messages.action') }}</th>
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
                @if($room->status === 'ocupada')
                  <span class="status-inactive">Ocupada</span>
                @elseif($room->status === 'mantenimiento')
                  <span class="text-warning">Mantenimiento</span>
                @else
                  <span class="status-active">Disponible</span>
                @endif
              </td>
              <td>
                <div class="btn-actions">
                  <a href="{{ route('admin.habitaciones.habitaciones.edit', ['habitacione' => $room->id]) }}" class="btn-edit-room">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form method="post" action="{{ route('admin.habitaciones.habitaciones.destroy', ['habitacione' => $room->id]) }}" style="display: inline; margin: 0;" data-confirm-message="¿Estás seguro de que quieres eliminar esta habitación?">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn-delete-room">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
      @if($loop->last)
            </tbody>
          </table>
        </div>
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


