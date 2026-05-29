@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col">
      <h2 class="mb-1"><i class="bi bi-person-plus me-2"></i>Crear nuevo usuario</h2>
      <p class="text-muted mb-0">Registra un nuevo usuario con acceso al sistema</p>
    </div>
    <div class="col-auto">
      <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary btn-lg">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <form action="{{ route('admin.usuarios.store') }}" method="POST" autocomplete="off">
        @csrf

        <!-- Fila 1: Nombre, Apellido, Email -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Nombre *</label>
            <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                   placeholder="Ej: Carlos" value="{{ old('name') }}" required>
            @error('name')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block mt-1">Nombre del usuario</small>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Apellido *</label>
            <input type="text" name="last_name" class="form-control form-control-lg @error('last_name') is-invalid @enderror" 
                   placeholder="Ej: Pérez" value="{{ old('last_name') }}" required>
            @error('last_name')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block mt-1">Apellido del usuario</small>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Email *</label>
            <div class="input-group input-group-lg">
              <span class="input-group-text">@</span>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                     placeholder="correo@example.com" pattern="^[^\s@]+@(gmail\.com|hotmail\.com)$" 
                     title="Solo se permiten correos @gmail.com o @hotmail.com" value="{{ old('email') }}" required>
            </div>
            @error('email')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block mt-1">Email único (@gmail.com o @hotmail.com)</small>
          </div>
        </div>

        <!-- Fila 2: Teléfono, Contraseña, Confirmar -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Teléfono *</label>
            <input type="text" name="phone" class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                   placeholder="+57 3xx xxx xxxx" inputmode="tel" minlength="10" maxlength="16" 
                   data-phone-sanitize="true" pattern="^(3\d{9}|(?:\+57|57)3\d{9}|\+\d{8,15}|\d{8,15})$" 
                   title="Si inicia en 3 debe tener 10 dígitos. También se acepta formato internacional válido." 
                   value="{{ old('phone') }}" required>
            @error('phone')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block mt-1">Contacto del usuario</small>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Contraseña *</label>
            <div class="input-group input-group-lg">
              <input type="password" name="password" id="password" minlength="12" 
                     class="form-control @error('password') is-invalid @enderror" 
                     placeholder="••••••••" title="Mínimo 12 caracteres con mayúscula, minúscula, número y símbolo" required>
              <button class="btn btn-outline-secondary" type="button" id="togglePass" title="Ver/ocultar">
                <i class="bi bi-eye"></i>
              </button>
              <button class="btn btn-outline-primary" type="button" id="genPass" title="Generar segura">
                <i class="bi bi-shuffle me-1"></i>Generar
              </button>
            </div>
            <div class="progress mt-2" style="height:5px">
              <div id="passBar" class="progress-bar bg-danger" style="width:0%"></div>
            </div>
            @error('password')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block mt-1">Mín. 12 caracteres (mayúscula, minúscula, número, símbolo)</small>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Confirmar contraseña *</label>
            <input type="password" name="password_confirmation" minlength="12" 
                   class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" 
                   placeholder="••••••••" required>
            @error('password_confirmation')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block mt-1">Repite la contraseña</small>
          </div>
        </div>

        <!-- Rol y Estado -->
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-semibold mb-3">Rol de acceso *</label>
            <div class="role-options">
              @foreach($roles as $id => $role)
                <label class="role-card-custom p-3 border rounded-2 d-flex align-items-center gap-3 mb-2" style="cursor:pointer;transition:all .2s">
                  <input type="radio" name="role_id" value="{{ $role->id }}" class="form-check-input" 
                         style="width:20px;height:20px" {{ old('role_id') == $role->id ? 'checked' : '' }} required>
                  <div>
                    <div class="fw-bold text-capitalize" style="font-size:1rem">
                      @if($role->name === 'reservas')
                        <i class="bi bi-calendar-event me-2 text-warning"></i>Reservas
                      @elseif($role->name === 'recepcion')
                        <i class="bi bi-person-badge me-2 text-primary"></i>Recepción
                      @elseif($role->name === 'mantenimiento')
                        <i class="bi bi-tools me-2 text-danger"></i>Mantenimiento
                      @else
                        <i class="bi bi-shop me-2 text-success"></i>Minibar
                      @endif
                    </div>
                    <small class="text-muted d-block mt-1">
                      @if($role->name === 'reservas')
                        Gestión de habitaciones y reservas
                      @elseif($role->name === 'recepcion')
                        Gestión de check-in, folios y atención al huésped
                      @elseif($role->name === 'mantenimiento')
                        Gestión de órdenes de mantenimiento
                      @else
                        Gestión de productos minibar y ventas
                      @endif
                    </small>
                  </div>
                </label>
              @endforeach
              @error('role_id')
                <div class="text-danger small mt-2">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold mb-3">Estado inicial</label>
            <div>
              <div class="form-check form-check-inline mb-2">
                <input class="form-check-input" type="radio" name="status" id="statusActive" 
                       value="active" {{ old('status', 'active') === 'active' ? 'checked' : '' }}>
                <label class="form-check-label" for="statusActive">
                  <span class="badge bg-success">Activo</span> - Acceso inmediato al sistema
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="status" id="statusInactive" 
                       value="inactive" {{ old('status') === 'inactive' ? 'checked' : '' }}>
                <label class="form-check-label" for="statusInactive">
                  <span class="badge bg-warning">Inactivo</span> - Sin acceso temporalmente
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Botones de Acción -->
        <div class="d-flex gap-2 pt-4 border-top">
          <button type="submit" class="btn btn-success btn-lg px-4">
            <i class="bi bi-check-circle me-2"></i>Guardar usuario
          </button>
          <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary btn-lg px-4">
            <i class="bi bi-x-circle me-2"></i>Cancelar
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const passwordInput = document.getElementById('password');
  const toggleBtn = document.getElementById('togglePass');
  const genBtn = document.getElementById('genPass');
  const passBar = document.getElementById('passBar');

  // Toggle password visibility
  toggleBtn?.addEventListener('click', function() {
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;
    this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
  });

  // Generate secure password
  genBtn?.addEventListener('click', function() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
    let password = '';
    for (let i = 0; i < 14; i++) {
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    passwordInput.value = password;
    passwordInput.type = 'text';
    updatePasswordStrength();
  });

  // Password strength indicator
  passwordInput?.addEventListener('input', updatePasswordStrength);

  function updatePasswordStrength() {
    const pass = passwordInput.value;
    let strength = 0;

    if (pass.length >= 12) strength += 25;
    if (/[a-z]/.test(pass)) strength += 25;
    if (/[A-Z]/.test(pass)) strength += 25;
    if (/[0-9]/.test(pass)) strength += 25;

    passBar.style.width = strength + '%';
    passBar.className = 'progress-bar ' + (strength < 50 ? 'bg-danger' : strength < 75 ? 'bg-warning' : 'bg-success');
  }
</script>

<style>
  .role-card-custom:hover {
    background-color: #f8f9fa !important;
    border-color: #007bff !important;
  }

  .role-card-custom input[type="radio"]:checked + * {
    color: #007bff;
  }
</style>
@endsection
