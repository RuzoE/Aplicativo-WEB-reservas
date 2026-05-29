@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col">
      <h2 class="mb-1"><i class="bi bi-pencil-square me-2"></i>Editar usuario</h2>
      <p class="text-muted mb-0">{{ $usuario->name }} {{ $usuario->last_name }} ({{ $usuario->email }})</p>
    </div>
    <div class="col-auto">
      <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary btn-lg">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>
  </div>

  <div class="row">
    <!-- Formulario de edición -->
    <div class="col-lg-8">
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información del usuario</h6>
        </div>
        <div class="card-body p-4">
          <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Fila 1: Nombre, Apellido, Email -->
            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <label class="form-label fw-semibold">Nombre *</label>
                <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                       value="{{ old('name', $usuario->name) }}" required>
                @error('name')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Apellido</label>
                <input type="text" name="last_name" class="form-control form-control-lg @error('last_name') is-invalid @enderror" 
                       value="{{ old('last_name', $usuario->last_name) }}">
                @error('last_name')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Email *</label>
                <div class="input-group input-group-lg">
                  <span class="input-group-text">@</span>
                  <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                         value="{{ old('email', $usuario->email) }}" required>
                </div>
                @error('email')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Fila 2: Teléfono y Rol -->
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Teléfono</label>
                <input type="text" name="phone" class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                       value="{{ old('phone', $usuario->phone) }}" inputmode="tel">
                @error('phone')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Rol de acceso</label>
                <select name="role_id" class="form-select form-select-lg @error('role_id') is-invalid @enderror">
                  <option value="">Mantener rol actual</option>
                  @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $currentRole?->id) == $role->id ? 'selected' : '' }}>
                      {{ ucfirst($role->name) }}
                    </option>
                  @endforeach
                </select>
                @error('role_id')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Estado -->
            <div class="mb-4">
              <label class="form-label fw-semibold mb-3">Estado</label>
              <div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="status" id="statusActive" 
                         value="active" {{ old('status', $usuario->status) === 'active' ? 'checked' : '' }}>
                  <label class="form-check-label" for="statusActive">
                    <span class="badge bg-success">Activo</span> - Acceso completo al sistema
                  </label>
                </div>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="status" id="statusInactive" 
                         value="inactive" {{ old('status', $usuario->status) === 'inactive' ? 'checked' : '' }}>
                  <label class="form-check-label" for="statusInactive">
                    <span class="badge bg-warning">Inactivo</span> - Sin acceso temporalmente
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="status" id="statusBlocked" 
                         value="blocked" {{ old('status', $usuario->status) === 'blocked' ? 'checked' : '' }}>
                  <label class="form-check-label" for="statusBlocked">
                    <span class="badge bg-danger">Bloqueado</span> - Acceso denegado permanentemente
                  </label>
                </div>
              </div>
            </div>

            <!-- Botones -->
            <div class="d-flex gap-2 pt-4 border-top">
              <button type="submit" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-check-circle me-2"></i>Guardar cambios
              </button>
              <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                <i class="bi bi-x-circle me-2"></i>Cancelar
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Panel lateral - Acciones rápidas -->
    <div class="col-lg-4">
      <!-- Información del usuario -->
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-person me-2"></i>Información</h6>
        </div>
        <div class="card-body p-3">
          <dl class="row small mb-0">
            <dt class="col-sm-6 fw-semibold">ID:</dt>
            <dd class="col-sm-6">{{ $usuario->id }}</dd>
            
            <dt class="col-sm-6 fw-semibold">Creado:</dt>
            <dd class="col-sm-6">{{ $usuario->created_at->format('d/m/Y H:i') }}</dd>
            
            <dt class="col-sm-6 fw-semibold">Último acceso:</dt>
            <dd class="col-sm-6">{{ $usuario->last_login_at ? $usuario->last_login_at->format('d/m/Y H:i') : 'Nunca' }}</dd>
            
            <dt class="col-sm-6 fw-semibold">IP última:</dt>
            <dd class="col-sm-6">{{ $usuario->last_login_ip ?? '-' }}</dd>
          </dl>
        </div>
      </div>

      <!-- Cambiar contraseña -->
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-key me-2"></i>Contraseña</h6>
        </div>
        <div class="card-body p-3">
          <p class="text-muted small mb-2">Gestionar contraseña del usuario</p>
          <button type="button" class="btn btn-outline-warning btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
            <i class="bi bi-pencil me-1"></i>Cambiar contraseña
          </button>
          <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="resetPassword({{ $usuario->id }})">
            <i class="bi bi-arrow-clockwise me-1"></i>Generar temporal
          </button>
        </div>
      </div>

      <!-- Actividad -->
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Actividad</h6>
        </div>
        <div class="card-body p-3">
          <p class="text-muted small mb-2">Ver historial y sesiones activas</p>
          <a href="{{ route('admin.usuarios.activity', $usuario) }}" class="btn btn-outline-info btn-sm w-100 mb-2">
            <i class="bi bi-list me-1"></i>Historial de actividad
          </a>
          <a href="{{ route('admin.usuarios.sessions', $usuario) }}" class="btn btn-outline-primary btn-sm w-100">
            <i class="bi bi-wifi me-1"></i>Sesiones activas
          </a>
        </div>
      </div>

      <!-- Peligro -->
      <div class="card shadow-sm border-0 border-danger">
        <div class="card-header bg-light border-danger">
          <h6 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Zona de peligro</h6>
        </div>
        <div class="card-body p-3">
          <p class="text-muted small mb-2">Acciones irreversibles</p>
          <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="deleteUser({{ $usuario->id }})">
            <i class="bi bi-trash me-1"></i>Eliminar usuario
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para cambiar contraseña -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-key me-2"></i>Cambiar contraseña</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.usuarios.update-password', $usuario) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Contraseña actual *</label>
            <input type="password" name="current_password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nueva contraseña *</label>
            <input type="password" name="password" class="form-control" required minlength="12">
            <small class="text-muted">Mín. 12 caracteres (mayúscula, minúscula, número, símbolo)</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirmar contraseña *</label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="12">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function deleteUser(userId) {
  if (confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción es irreversible.')) {
    if (confirm('Confirma: ¿Eliminar usuario definitivamente?')) {
      fetch(`/admin/usuarios/${userId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      }).then(() => window.location.href = '/admin/usuarios');
    }
  }
}

function resetPassword(userId) {
  if (confirm('¿Generar una nueva contraseña temporal para este usuario?')) {
    fetch(`/admin/usuarios/${userId}/resetear-contraseña`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
      }
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert(`Contraseña temporal: ${data.temporary_password}\n\nAsegúrate de copiar esta contraseña antes de cerrar el diálogo.`);
      }
    })
    .catch(e => alert('Error: ' + e));
  }
}
</script>
@endsection
