@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
@endphp

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/admin/roomtypes/index--style1.css') }}">

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
                  <form method="post" action="{{ route('admin.habitaciones.tipos-habitacion.destroy', ['tipos_habitacion' => $type->id]) }}" style="display: inline;" data-confirm-message="¿Está seguro de que desea eliminar este tipo de habitación?">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn-action btn-delete" title="{{ __('messages.delete') }}">
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


