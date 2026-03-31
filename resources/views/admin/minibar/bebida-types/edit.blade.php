{{-- resources/views/admin/minibar/bebida-types/edit.blade.php --}}
@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Editar Tipo de Bebida')

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/admin/minibar/bebida-types/edit--style1.css') }}">

<div class="form-container">
  <a href="{{ route('admin.minibar.bebida-types.index') }}" class="back-link">
    <i class="bi bi-arrow-left"></i>
    Volver a Tipos de Bebida
  </a>

  <div class="form-card">
    <div class="form-title">
      <i class="bi bi-pencil-square"></i>
      Editar Tipo de Bebida
    </div>

    <form action="{{ route('admin.minibar.bebida-types.update', $bebida_type) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label for="nombre" class="form-label">Nombre del Tipo</label>
        <input
          type="text"
          id="nombre"
          name="nombre"
          value="{{ old('nombre', $bebida_type->nombre) }}"
          required
          class="form-input"
          placeholder="Ej: Ron, Vodka, Cerveza..."
        />
        @error('nombre')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit">
          <i class="bi bi-check-lg me-1"></i> Actualizar
        </button>
        <a href="{{ route('admin.minibar.bebida-types.index') }}" class="btn-cancel">
          <i class="bi bi-x-lg me-1"></i> Cancelar
        </a>
      </div>
    </form>
  </div>
</div>
@endsection


