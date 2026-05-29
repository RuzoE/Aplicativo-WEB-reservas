@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col">
      <h2 class="mb-1"><i class="bi bi-key me-2"></i>Cambiar contraseña</h2>
      <p class="text-muted mb-0">{{ $usuario->name }} {{ $usuario->last_name }} ({{ $usuario->email }})</p>
    </div>
    <div class="col-auto">
      <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-outline-secondary btn-lg">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Actualizar contraseña</h6>
        </div>
        <div class="card-body p-4">
          <form action="{{ route('admin.usuarios.update-password', $usuario) }}" method="POST">
            @csrf

            @if ($errors->any())
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error de validación:</strong>
                <ul class="mb-0 mt-2">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            <div class="mb-4">
              <label class="form-label fw-semibold">Nueva contraseña *</label>
              <div class="input-group input-group-lg">
                <input type="password" name="password" id="password" minlength="12"
                       class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Nueva contraseña" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                  <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-outline-primary" type="button" onclick="generatePassword()">
                  <i class="bi bi-shuffle"></i> Generar
                </button>
              </div>
              <div class="progress mt-2" style="height:5px">
                <div id="passwordStrength" class="progress-bar bg-danger" style="width:0%"></div>
              </div>
              @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
              <small class="text-muted d-block mt-2">
                Mínimo 12 caracteres con mayúscula, minúscula, número y símbolo especial
              </small>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold">Confirmar nueva contraseña *</label>
              <div class="input-group input-group-lg">
                <input type="password" name="password_confirmation" id="passwordConfirm" minlength="12"
                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                       placeholder="Repite la nueva contraseña" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passwordConfirm')">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              @error('password_confirmation')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
              <small class="text-muted d-block mt-2">Debe coincidir con la nueva contraseña</small>
            </div>

            <!-- Requisitos de contraseña -->
            <div class="alert alert-info small mb-4">
              <h6 class="alert-heading mb-2">Requisitos de seguridad:</h6>
              <ul class="mb-0">
                <li><span class="badge bg-light text-dark">≥12</span> Mínimo 12 caracteres</li>
                <li><span class="badge bg-light text-dark">A-Z</span> Al menos una mayúscula</li>
                <li><span class="badge bg-light text-dark">a-z</span> Al menos una minúscula</li>
                <li><span class="badge bg-light text-dark">0-9</span> Al menos un número</li>
                <li><span class="badge bg-light text-dark">!@#</span> Al menos un símbolo especial</li>
              </ul>
            </div>

            <!-- Botones -->
            <div class="d-flex gap-2 pt-3 border-top">
              <button type="submit" class="btn btn-success btn-lg px-4 flex-grow-1">
                <i class="bi bi-check-circle me-2"></i>Guardar contraseña
              </button>
              <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-outline-secondary btn-lg px-4">
                <i class="bi bi-x-circle me-2"></i>Cancelar
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const passwordInput = document.getElementById('password');

  // Toggle password visibility
  function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.type === 'password' ? 'text' : 'password';
    field.type = type;
  }

  // Generate secure password
  function generatePassword() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
    let password = '';
    for (let i = 0; i < 16; i++) {
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    passwordInput.value = password;
    passwordInput.type = 'text';
    updatePasswordStrength();
  }

  // Update password strength
  passwordInput?.addEventListener('input', updatePasswordStrength);

  function updatePasswordStrength() {
    const pass = passwordInput.value;
    let strength = 0;

    if (pass.length >= 12) strength += 20;
    if (pass.length >= 16) strength += 10;
    if (/[a-z]/.test(pass)) strength += 20;
    if (/[A-Z]/.test(pass)) strength += 20;
    if (/[0-9]/.test(pass)) strength += 15;
    if (/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(pass)) strength += 15;

    const bar = document.getElementById('passwordStrength');
    bar.style.width = Math.min(strength, 100) + '%';
    bar.className = 'progress-bar ' + (strength < 50 ? 'bg-danger' : strength < 75 ? 'bg-warning' : 'bg-success');
  }
</script>
@endsection
