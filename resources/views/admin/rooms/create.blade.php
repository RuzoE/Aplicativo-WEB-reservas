@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
@endphp
<link rel="stylesheet" href="{{ asset('css/blade/admin/rooms/create--style1.css') }}">

<div class="form-container">
  @if($types->isEmpty())
    <div style="max-width: 600px; width: 100%; margin-top: 20px;">
      <div class="alert-error">
        <i class="bi bi-exclamation-triangle"></i>
        <strong>Atención:</strong> Debes crear tipos de habitación primero
      </div>
      <a href="{{ route('admin.habitaciones.roomtypes.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Ir a Tipos de Habitación
      </a>
    </div>
  @else
    <a href="{{ route('admin.habitaciones.habitaciones.index') }}" class="back-link">
      <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
    </a>

    <div class="form-card">
      <div class="form-title">
        <i class="bi bi-door-closed"></i>
        {{ __('messages.create') }} {{ __('messages.rooms') }}
      </div>

      <div class="info-box">
        <i class="bi bi-info-circle"></i>
        Completa los datos para crear una nueva habitación
      </div>

      <form method="post" action="{{ route('admin.habitaciones.habitaciones.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
          <label class="form-label">{{ __('messages.roomtypes') }} *</label>
          <select name="room_type_id" class="form-select @error('room_type_id') is-invalid @enderror">
            <option value="">Selecciona un tipo...</option>
            @foreach($types as $type)
              <option value="{{ $type->id }}" @selected(old('room_type_id') == $type->id)>
                {{ $type->name }}
              </option>
            @endforeach
          </select>
          @error('room_type_id')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label class="form-label">Total de Habitaciones *</label>
          <input type="number" name="total_room" value="{{ old('total_room') }}" placeholder="Ej: 5"
                 class="form-input @error('total_room') is-invalid @enderror" min="1">
          @error('total_room')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label class="form-label">Número de camas *</label>
          <input type="number" name="no_beds" value="{{ old('no_beds') }}" placeholder="Ej: 2"
                 class="form-input @error('no_beds') is-invalid @enderror" min="1">
          @error('no_beds')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label class="form-label">Precio por Noche (COP) *</label>
          <input type="text" name="price" value="{{ old('price') }}" placeholder="Ej: 143.500"
                 class="form-input @error('price') is-invalid @enderror">
          @error('price')
            <span class="error-text">{{ $message }}</span>
          @enderror
          <small style="color:#666">Ingresa en pesos colombianos. Se permite 143.500, 143.900, 1.200.000, etc.</small>
        </div>

        <div class="form-group">
          <label class="form-label">Descripción</label>
          <textarea name="desc" placeholder="Describe características, amenidades, etc..."
                    class="form-textarea @error('desc') is-invalid @enderror" rows="4">{{ old('desc') }}</textarea>
          @error('desc')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label class="form-label">Imagen de Habitación</label>

          <!-- Upload área -->
          <div class="image-upload-wrapper" id="uploadWrapper">
            <i class="bi bi-cloud-arrow-up upload-icon"></i>
            <div class="upload-text">Arrastra tu imagen aquí o haz clic para seleccionar</div>
            <div class="upload-subtext">Formatos soportados: JPG, PNG, GIF (Max. 5MB)</div>
            <input type="file" id="imageInput" name="image" accept="image/*" class="@error('image') is-invalid @enderror">
          </div>

          @if($errors->has('image'))
            <span class="error-text">{{ $errors->first('image') }}</span>
          @endif

          <div id="newImagePreview" style="display: none; margin-top: 20px;">
            <div class="image-preview-container">
              <img id="previewImage" alt="Preview" class="image-preview">
              <div class="image-info">
                <span class="image-info-text">
                  <i class="bi bi-check-circle"></i> Nueva imagen seleccionada
                </span>
              </div>
            </div>
            <button type="button" class="btn-change-image" data-action="reset-image-upload">
              <i class="bi bi-arrow-counterclockwise"></i> Cancelar cambio
            </button>
          </div>
        </div>

        <div class="form-group checkbox">
          <input type="checkbox" id="status" name="status" value="1" checked>
          <label for="status">Activar esta habitación</label>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">
            <i class="bi bi-check-circle"></i> {{ __('messages.create') }}
          </button>
          <a href="{{ route('admin.habitaciones.habitaciones.index') }}" class="btn-cancel">
            <i class="bi bi-x-circle"></i> {{ __('messages.cancel') }}
          </a>
        </div>
      </form>
    </div>
  @endif
</div>

<script src="{{ asset('js/admin-habitaciones-form.js') }}"></script>
@endsection


