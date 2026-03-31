{{-- resources/views/admin/minibar/bebidas/create.blade.php --}}
@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Crear Bebida')

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/admin/minibar/bebidas/create--style1.css') }}">

<div class="form-container">
  <a href="{{ route('admin.minibar.bebidas.index') }}" class="back-link">
    <i class="bi bi-arrow-left"></i>
    Volver a Bebidas
  </a>

  <div class="form-card">
    <div class="form-title">
      <i class="bi bi-plus-circle"></i>
      Crear Nueva Bebida
    </div>

    <form action="{{ route('admin.minibar.bebidas.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      {{-- Nombre --}}
      <div class="form-group">
        <label class="form-label">Nombre de la Bebida</label>
        <input type="text" name="nombre" value="{{ old('nombre') }}"
               class="form-input @error('nombre') is-invalid @enderror"
               placeholder="Ej: Aguila, Tequila, Ron Blanco...">
        @error('nombre')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      {{-- Tipo y Precio en fila --}}
      <div class="form-row">
        {{-- Tipo --}}
        <div class="form-group">
          <label class="form-label">Tipo de Bebida</label>
          <select name="bebida_type_id"
                  class="form-select @error('bebida_type_id') is-invalid @enderror">
            <option value="">-- Selecciona un tipo --</option>
            @foreach($bebidaTypes as $tipo)
              <option value="{{ $tipo->id }}"
                {{ old('bebida_type_id') == $tipo->id ? 'selected' : '' }}>
                {{ $tipo->nombre }}
              </option>
            @endforeach
          </select>
          @error('bebida_type_id')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>

        {{-- Precio --}}
        <div class="form-group">
          <label class="form-label">Precio</label>
          <input type="number" name="precio" step="0.01"
                 value="{{ old('precio') }}"
                 class="form-input @error('precio') is-invalid @enderror"
                 placeholder="0.00">
          @error('precio')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>
      </div>

      {{-- Stock --}}
      <div class="form-group">
        <label class="form-label">Stock Disponible</label>
        <input type="number" name="stock" value="{{ old('stock') }}" min="0"
               class="form-input @error('stock') is-invalid @enderror"
               placeholder="0">
        @error('stock')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      {{-- Imagen --}}
      <div class="form-group">
        <label class="form-label">Imagen de la Bebida</label>
        <div class="file-input-wrapper">
          <label for="imagen" class="file-input-label">
            <i class="bi bi-image"></i>
            <span class="file-text">Seleccionar imagen</span>
            <span class="file-subtext">PNG, JPG hasta 5MB</span>
          </label>
          <input type="file" name="imagen" id="imagen" accept="image/*"
                 class="file-input-hidden @error('imagen') is-invalid @enderror">
        </div>
        @error('imagen')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      {{-- Acciones --}}
      <div class="form-actions">
        <button type="submit" class="btn-submit">
          <i class="bi bi-plus-lg me-1"></i> Crear Bebida
        </button>
        <a href="{{ route('admin.minibar.bebidas.index') }}" class="btn-cancel">
          <i class="bi bi-x-lg me-1"></i> Cancelar
        </a>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/admin-bebidas-form.js') }}"></script>
@endsection


