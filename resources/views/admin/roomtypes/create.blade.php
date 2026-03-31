@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
@endphp

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/admin/roomtypes/create--style1.css') }}">

<div class="form-container">
  <a href="{{ route('admin.habitaciones.tipos-habitacion.index') }}" class="back-link">
    <i class="bi bi-arrow-left"></i>
    {{ __('messages.back') }} a {{ __('messages.roomtypes') }}
  </a>

  <div class="form-card">
    <div class="form-title">
      <i class="bi bi-plus-square"></i>
      {{ __('messages.create') }} {{ __('messages.roomtypes') }}
    </div>

    <form method="post" action="{{ route('admin.habitaciones.tipos-habitacion.store') }}">
      @csrf

      <div class="info-box">
        <i class="bi bi-info-circle"></i>
        Define un nuevo tipo de habitación para tu hotel
      </div>

      {{-- Nombre --}}
      <div class="form-group">
        <label for="nombre" class="form-label">{{ __('messages.name') }} del Tipo</label>
        <input id="nombre"
               type="text"
               name="name"
               value="{{ old('name') }}"
               class="form-input @error('name') is-invalid @enderror"
               placeholder="Ej: Standard, Deluxe, Suite, Premium...">
        @error('name')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      {{-- Acciones --}}
      <div class="form-actions">
        <button type="submit" class="btn-submit">
          <i class="bi bi-check-lg me-1"></i> {{ __('messages.create') }}
        </button>
        <a href="{{ route('admin.habitaciones.tipos-habitacion.index') }}" class="btn-cancel">
          <i class="bi bi-x-lg me-1"></i> {{ __('messages.cancel') }}
        </a>
      </div>
    </form>
  </div>
</div>

@endsection


