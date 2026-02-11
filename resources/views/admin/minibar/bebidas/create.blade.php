{{-- resources/views/admin/minibar/bebidas/create.blade.php --}}
@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Crear Bebida')

@section('content')
<style>
  .form-container {
    padding: 40px 20px;
    background: #f5f5f5;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #4CAF50;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 25px;
    transition: gap 0.2s;
    align-self: flex-start;
    max-width: 700px;
    width: 100%;
  }
  .back-link:hover {
    gap: 10px;
    color: #2E7D32;
  }
  .form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 40px;
    width: 100%;
    max-width: 700px;
  }
  .form-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .form-title i {
    color: #4CAF50;
    font-size: 1.5rem;
  }
  .form-group {
    margin-bottom: 25px;
  }
  .form-label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .form-input,
  .form-select {
    width: 100%;
    padding: 12px 14px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    color: #333;
    transition: border-color 0.3s, box-shadow 0.3s;
    font-family: inherit;
    box-sizing: border-box;
  }
  .form-input:focus,
  .form-select:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
  }
  .form-input.is-invalid,
  .form-select.is-invalid {
    border-color: #EF5350;
    box-shadow: 0 0 0 3px rgba(239, 83, 80, 0.1);
  }
  .error-text {
    color: #EF5350;
    font-size: 0.85rem;
    margin-top: 6px;
    display: block;
  }
  .file-input-wrapper {
    position: relative;
  }
  .file-input-label {
    display: block;
    padding: 20px;
    border: 2px dashed #4CAF50;
    border-radius: 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: #f9f9f9;
    box-sizing: border-box;
  }
  .file-input-label:hover {
    background: #f0f8f5;
    border-color: #2E7D32;
  }
  .file-input-label i {
    font-size: 2rem;
    color: #4CAF50;
    display: block;
    margin-bottom: 8px;
  }
  .file-input-label .file-text {
    color: #333;
    font-weight: 600;
    display: block;
  }
  .file-input-label .file-subtext {
    color: #999;
    font-size: 0.85rem;
    margin-top: 4px;
  }
  .file-input-hidden {
    display: none;
  }
  .form-actions {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-top: 30px;
    flex-wrap: wrap;
  }
  .btn-submit {
    padding: 12px 28px;
    background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
  }
  .btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(76, 175, 80, 0.4);
    color: white;
    text-decoration: none;
  }
  .btn-cancel {
    padding: 12px 28px;
    background: #f0f0f0;
    color: #333;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
    display: inline-block;
  }
  .btn-cancel:hover {
    background: #e8e8e8;
    color: #333;
    text-decoration: none;
  }
  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }
  @media (max-width: 768px) {
    .form-row {
      grid-template-columns: 1fr;
    }
    .form-card {
      padding: 30px 20px;
    }
  }
</style>

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
