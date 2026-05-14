@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
@endphp
<link rel="stylesheet" href="{{ asset('css/blade/admin/rooms/edit--style1.css') }}">

<div class="form-container">

  <a href="{{ route('admin.habitaciones.habitaciones.index') }}" class="back-link">
    <i class="bi bi-arrow-left"></i> Volver a la lista
  </a>

  <div class="form-card">
    <div class="form-title">
      <i class="bi bi-pencil-square"></i>
      Editar Habitación
    </div>

    <form method="POST" action="{{ route('admin.habitaciones.habitaciones.update', ['habitacione' => $room->id]) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label for="room_type_id" class="form-label">Tipo de habitación</label>
        <select name="room_type_id" id="room_type_id" class="form-select @error('room_type_id') is-invalid @enderror">
          <option value="">Seleccione un tipo</option>
          @foreach($types as $type)
            <option value="{{ $type->id }}" @selected(old('room_type_id', $room->room_type_id) == $type->id)>
              {{ $type->name }}
            </option>
          @endforeach
        </select>
        @error('room_type_id')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="total_room" class="form-label">Total de habitaciones</label>
        <input type="number" name="total_room" id="total_room" class="form-input @error('total_room') is-invalid @enderror" value="{{ old('total_room', $room->total_room) }}" min="1">
        @error('total_room')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="no_beds" class="form-label">Número de camas</label>
        <input type="number" name="no_beds" id="no_beds" class="form-input @error('no_beds') is-invalid @enderror" value="{{ old('no_beds', $room->no_beds) }}" min="1">
        @error('no_beds')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="price" class="form-label">Precio</label>
        <input type="number" name="price" id="price" class="form-input @error('price') is-invalid @enderror" value="{{ old('price', $room->price) }}" min="0" step="0.01">
        @error('price')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="desc" class="form-label">Descripción</label>
        <textarea name="desc" id="desc" rows="4" class="form-textarea @error('desc') is-invalid @enderror">{{ old('desc', $room->desc) }}</textarea>
        @error('desc')
          <span class="error-text">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label">Imagen actual</label>
        @if($room->image)
          <div class="image-preview-container">
            <img src="{{ $room->image_url }}" alt="{{ $room->roomtype->name ?? 'Habitación' }}" class="image-preview">
            <div class="image-info">
              <span class="image-info-text">
                <i class="bi bi-image"></i> Imagen actual
              </span>
              <button type="button" class="btn-remove-image" data-action="toggle-image-field">
                <i class="bi bi-trash"></i> Cambiar imagen
              </button>
            </div>
          </div>
        @endif

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
        <input type="checkbox" id="status" name="status" value="1" @checked(old('status', $room->status === 'disponible'))>
        <label for="status">Activar esta habitación</label>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit">
          <i class="bi bi-check-circle"></i> Actualizar Habitación
        </button>
        <a href="{{ route('admin.habitaciones.habitaciones.index') }}" class="btn-cancel">
          <i class="bi bi-x-circle"></i> Cancelar
        </a>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/admin-habitaciones-form.js') }}"></script>
@endsection


