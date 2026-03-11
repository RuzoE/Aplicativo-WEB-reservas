@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.minibar-sidebar';
@endphp

@section('title', 'Crear Tipo de Bebida no alcohólica')

@section('content')
<style>
  .form-container {
    padding: 40px;
    background: #f5f5f5;
    min-height: 100vh;
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
    max-width: 600px;
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
  .form-input {
    width: 100%;
    padding: 12px 14px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    color: #333;
    transition: border-color 0.3s, box-shadow 0.3s;
  }
  .form-input:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
  }
  .error-text {
    color: #EF5350;
    font-size: 0.85rem;
    margin-top: 6px;
    display: block;
  }
  .form-actions {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-top: 30px;
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
</style>

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
