@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
@endphp
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
  .form-input,
  .form-select,
  .form-textarea {
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
  .form-select:focus,
  .form-textarea:focus {
    outline: none;
    border-color: #FFC107;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
  }
  .form-input.is-invalid,
  .form-select.is-invalid,
  .form-textarea.is-invalid {
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
  .form-group.checkbox {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 15px;
  }
  .form-group.checkbox input {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #FFC107;
  }
  .form-group.checkbox label {
    margin-bottom: 0;
    cursor: pointer;
    font-weight: 500;
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
  .image-upload-wrapper {
    border: 2px dashed #FFC107;
    border-radius: 10px;
    padding: 25px;
    background: #fffbf0;
    text-align: center;
    transition: all 0.3s;
    cursor: pointer;
    position: relative;
  }
  .image-upload-wrapper:hover {
    background: #fff8e6;
    border-color: #FF9800;
  }
  .image-upload-wrapper.dragover {
    background: #fff3e0;
    border-color: #FF9800;
    transform: scale(1.01);
  }
  .image-upload-wrapper input[type="file"] {
    display: none;
  }
  .upload-icon {
    font-size: 2.5rem;
    color: #FFC107;
    margin-bottom: 12px;
    display: block;
  }
  .upload-text {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
    margin-bottom: 5px;
  }
  .upload-subtext {
    font-size: 0.85rem;
    color: #999;
  }
  .image-preview-container {
    margin-top: 20px;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    border: 2px solid #f0f0f0;
    position: relative;
  }
  .image-preview {
    width: 100%;
    height: 250px;
    object-fit: cover;
    display: block;
  }
  .image-info {
    padding: 15px;
    background: #f9f9f9;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .image-info-text {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #2E7D32;
    font-weight: 600;
    font-size: 0.9rem;
  }
  .image-info-text i {
    font-size: 1.2rem;
  }
  .btn-remove-image {
    padding: 6px 12px;
    background: #EF5350;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .btn-remove-image:hover {
    background: #E53935;
    transform: translateY(-1px);
  }
  .btn-change-image {
    padding: 8px 16px;
    background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 12px;
    width: 100%;
  }
  .btn-change-image:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(255, 193, 7, 0.3);
  }
  .current-image-badge {
    background: #fff3e0;
    border: 1px solid #FFC107;
    color: #FF9800;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 0.85rem;
    margin-top: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  @media (max-width: 768px) {
    .form-card {
      padding: 25px;
    }
    .form-title {
      font-size: 1.5rem;
    }
    .image-preview {
      height: 200px;
    }
    .image-info {
      flex-direction: column;
      align-items: flex-start;
      gap: 10px;
    }
  }
</style>

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
            <img src="{{ asset($room->image) }}" alt="{{ $room->roomtype->name ?? 'Habitación' }}" class="image-preview">
            <div class="image-info">
              <span class="image-info-text">
                <i class="bi bi-image"></i> Imagen actual
              </span>
              <button type="button" class="btn-remove-image" onclick="toggleImageField()">
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
          <button type="button" class="btn-change-image" onclick="resetImageUpload()">
            <i class="bi bi-arrow-counterclockwise"></i> Cancelar cambio
          </button>
        </div>
      </div>

      <div class="form-group checkbox">
        <input type="checkbox" id="status" name="status" value="1" @checked(old('status', $room->status))>
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
