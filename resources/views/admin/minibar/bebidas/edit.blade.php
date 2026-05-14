{{-- resources/views/admin/minibar/bebidas/edit.blade.php --}}
@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Editar Bebida')

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/admin/minibar/bebidas/edit--style1.css') }}">

<div class="form-container">
  <a href="{{ route('admin.minibar.bebidas.index') }}" class="back-link">
    <i class="bi bi-arrow-left"></i>
    Volver a Bebidas
  </a>

  <div class="form-card">
    <div class="form-title">
      <i class="bi bi-pencil-square"></i>
      Editar Bebida
    </div>

    <form action="{{ route('admin.minibar.bebidas.update', $bebida) }}"
          method="POST"
          enctype="multipart/form-data">
      @csrf
      @method('PUT')

      {{-- Nombre --}}
      <div class="form-group">
        <label for="nombre" class="form-label">Nombre de la Bebida</label>
        <input id="nombre"
               type="text"
               name="nombre"
               value="{{ old('nombre', $bebida->nombre) }}"
               class="form-input @error('nombre') is-invalid @enderror"
               placeholder="Ej: Aguila, Tequila, Ron Blanco...">
        @error('nombre')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      {{-- Tipo y Precio en fila --}}
      <div class="form-row">
        {{-- Tipo de Bebida --}}
        <div class="form-group">
          <label for="bebida_type_id" class="form-label">Tipo de Bebida</label>
          <select id="bebida_type_id"
                  name="bebida_type_id"
                  class="form-select @error('bebida_type_id') is-invalid @enderror">
            <option value="">-- Selecciona un tipo --</option>
            @foreach($bebidaTypes as $type)
              <option value="{{ $type->id }}"
                      @selected(old('bebida_type_id', $bebida->bebida_type_id) == $type->id)>
                {{ $type->nombre }}
              </option>
            @endforeach
          </select>
          @error('bebida_type_id')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>

        {{-- Precio --}}
        <div class="form-group">
          <label for="precio" class="form-label">Precio</label>
          <input id="precio"
                 type="number"
                 name="precio"
                 step="0.01"
                 value="{{ old('precio', $bebida->precio) }}"
                 class="form-input @error('precio') is-invalid @enderror"
                 placeholder="0.00">
          @error('precio')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>
      </div>

      {{-- Stock --}}
      <div class="form-group">
        <label for="stock" class="form-label">Stock Disponible</label>
        <input id="stock"
               type="number"
               name="stock"
               value="{{ old('stock', $bebida->stock) }}"
               class="form-input @error('stock') is-invalid @enderror"
               placeholder="0"
               min="0">
        @error('stock')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      {{-- Imagen Actual --}}
      @if($bebida->imagen)
        <div class="image-preview-section">
          <span class="image-preview-label">Imagen Actual</span>
          <div class="image-preview-container">
            <img src="{{ $bebida->image_url }}"
                 alt="{{ $bebida->nombre }}"
                 class="image-preview">
          </div>
        </div>
      @endif

      {{-- Imagen Nueva --}}
      <div class="form-group">
        <label class="form-label">Cambiar Imagen</label>
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
          <i class="bi bi-check-lg me-1"></i> Actualizar Bebida
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


