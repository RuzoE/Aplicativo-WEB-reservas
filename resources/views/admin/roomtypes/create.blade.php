@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
@endphp

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
    color: #FFC107;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 25px;
    transition: gap 0.2s;
    align-self: flex-start;
    max-width: 600px;
    width: 100%;
  }
  .back-link:hover {
    gap: 10px;
    color: #FF9800;
  }
  .form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 40px;
    width: 100%;
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
    color: #FFC107;
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
    font-family: inherit;
    box-sizing: border-box;
  }
  .form-input:focus {
    outline: none;
    border-color: #FFC107;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
  }
  .form-input.is-invalid {
    border-color: #EF5350;
    box-shadow: 0 0 0 3px rgba(239, 83, 80, 0.1);
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
    flex-wrap: wrap;
  }
  .btn-submit {
    padding: 12px 28px;
    background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
  }
  .btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 193, 7, 0.4);
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
  .info-box {
    background: #fff3e0;
    border-left: 4px solid #FFC107;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 25px;
    font-size: 0.9rem;
    color: #FF9800;
  }
  .info-box i {
    margin-right: 8px;
  }
  @media (max-width: 768px) {
    .form-title {
      font-size: 1.5rem;
    }
    .form-card {
      padding: 30px 20px;
    }
  }
</style>

<div class="form-container">
  <a href="{{ route('admin.habitaciones.roomtypes.index') }}" class="back-link">
    <i class="bi bi-arrow-left"></i>
    Volver a Tipos de Habitación
  </a>

  <div class="form-card">
    <div class="form-title">
      <i class="bi bi-plus-square"></i>
      Crear Tipo de Habitación
    </div>

    <form method="post" action="{{ route('admin.habitaciones.roomtypes.store') }}">
      @csrf

      <div class="info-box">
        <i class="bi bi-info-circle"></i>
        Define un nuevo tipo de habitación para tu hotel
      </div>

      {{-- Nombre --}}
      <div class="form-group">
        <label for="nombre" class="form-label">Nombre del Tipo</label>
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
          <i class="bi bi-check-lg me-1"></i> Crear Tipo
        </button>
        <a href="{{ route('admin.habitaciones.roomtypes.index') }}" class="btn-cancel">
          <i class="bi bi-x-lg me-1"></i> Cancelar
        </a>
      </div>
    </form>
  </div>
</div>

@endsection
