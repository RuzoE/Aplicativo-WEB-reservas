@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Crear Tipo de Bebida no alcohólica')

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/admin/minibar/bebida-types-na/create--style1.css') }}">

<div class="form-container">
  <a href="{{ route('admin.minibar.bebida-types-na.index') }}" class="back-link">
    <i class="bi bi-arrow-left"></i>
    Volver a Tipos de Bebidas no alcohólicas
  </a>

  <div class="form-card">
    <div class="form-title">
      <i class="bi bi-plus-circle"></i>
      Crear Nuevo Tipo no alcohólico
    </div>

    <form action="{{ route('admin.minibar.bebida-types-na.store') }}" method="POST">
      @csrf

      <div class="form-group">
        <label for="nombre" class="form-label">Nombre del Tipo</label>
        <input
          type="text"
          name="nombre"
          id="nombre"
          value="{{ old('nombre') }}"
          required
          class="form-input"
          placeholder="Ej: Gaseosa, Jugo, Agua..."
        >
        @error('nombre')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit">
          <i class="bi bi-plus-lg me-1"></i> Crear Tipo
        </button>
        <a href="{{ route('admin.minibar.bebida-types-na.index') }}" class="btn-cancel">
          <i class="bi bi-x-lg me-1"></i> Cancelar
        </a>
      </div>
    </form>
  </div>
</div>
@endsection


