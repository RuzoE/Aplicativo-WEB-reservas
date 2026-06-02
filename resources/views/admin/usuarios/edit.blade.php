@extends('layouts.app')
@php $adminView = true; @endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuarios/usuarios.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/tablas.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/edit.css') }}">
@endpush

@section('content')
@php
  $status     = old('status', $usuario->status);
  $initials   = strtoupper(substr($usuario->name ?? '', 0, 1) . substr($usuario->last_name ?? '', 0, 1));
  $statusMap  = ['active' => 'active', 'inactive' => 'inactive', 'blocked' => 'blocked'];
  $statusLabels = ['active' => 'Activo', 'inactive' => 'Inactivo', 'blocked' => 'Bloqueado'];
  $statusClass = $statusMap[$usuario->status] ?? 'inactive';
@endphp

<div class="user-edit-page">
  <div class="container-fluid px-4 py-4">

    {{-- ═══ HERO: Avatar + nombre + badges + volver ═══ --}}
    <div class="profile-hero-card mb-4">
      <div class="profile-avatar">{{ $initials ?: 'U' }}</div>

      <div class="profile-hero-info">
        <h1 class="profile-hero-name">{{ $usuario->name }} {{ $usuario->last_name }}</h1>
        <p class="profile-hero-email">{{ $usuario->email }}</p>
        <div class="profile-hero-badges">
          {{-- Badge de estado actual --}}
          <span class="status-badge {{ $statusClass }}">
            <span class="dot"></span>
            {{ $statusLabels[$usuario->status] ?? ucfirst($usuario->status) }}
          </span>
          {{-- Chip de rol --}}
          <span class="role-chip">{{ $usuario->display_role }}</span>
        </div>
      </div>

      <div class="profile-back-btn">
        <a href="{{ route('admin.usuarios.index') }}" class="btn-back-refined">
          <i class="fas fa-arrow-left"></i> VOLVER
        </a>
      </div>
    </div>

    {{-- ═══ STAT CARDS (Creado / Estado / Rol / Último acceso) ═══ --}}
    <div class="stat-cards-row mb-4">

      <div class="stat-card">
        <div class="stat-card-icon blue">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
          <p class="stat-card-label">Creado</p>
          <p class="stat-card-value">{{ $usuario->created_at->format('d/m/Y') }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon {{ $statusClass === 'active' ? 'green' : ($statusClass === 'blocked' ? 'red' : 'slate') }}">
          <i class="fas fa-{{ $statusClass === 'active' ? 'check-circle' : ($statusClass === 'blocked' ? 'ban' : 'user-slash') }}"></i>
        </div>
        <div>
          <p class="stat-card-label">Estado</p>
          <p class="stat-card-value">{{ $statusLabels[$usuario->status] ?? ucfirst($usuario->status) }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon orange">
          <i class="fas fa-id-badge"></i>
        </div>
        <div>
          <p class="stat-card-label">Rol asignado</p>
          <p class="stat-card-value">{{ $usuario->display_role }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon teal">
          <i class="fas fa-clock"></i>
        </div>
        <div>
          <p class="stat-card-label">Último acceso</p>
          <p class="stat-card-value">{{ $usuario->last_login_at ? $usuario->last_login_at->format('d/m/Y H:i') : 'Nunca' }}</p>
        </div>
      </div>

    </div>

    {{-- ═══ FILA PRINCIPAL: Formulario + Sidebar ═══ --}}
    <div class="row g-4">

      {{-- ── Información personal (formulario) ── --}}
      <div class="col-lg-7 col-form-panel">
        <div class="edit-panel-card">

          <div class="section-icon-header">
            <div class="section-icon-box orange">
              <i class="fas fa-user-circle"></i>
            </div>
            <div>
              <h2 class="section-icon-title">Información personal</h2>
              <p class="section-icon-subtitle">Actualiza los datos de usuario con un diseño premium y fluido.</p>
            </div>
          </div>

          <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nombre / Apellido --}}
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <div class="form-floating-custom">
                  <input id="name" type="text" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder=" "
                    value="{{ old('name', $usuario->name) }}" required>
                  <label for="name">Nombre *</label>
                </div>
                @error('name')
                  <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <div class="form-floating-custom">
                  <input id="last_name" type="text" name="last_name"
                    class="form-control @error('last_name') is-invalid @enderror"
                    placeholder=" "
                    value="{{ old('last_name', $usuario->last_name) }}">
                  <label for="last_name">Apellido</label>
                </div>
                @error('last_name')
                  <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>

            {{-- Email / Teléfono --}}
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <div class="form-floating-custom has-prefix">
                  <i class="fas fa-envelope input-prefix-icon"></i>
                  <input id="email" type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder=" "
                    value="{{ old('email', $usuario->email) }}" required>
                  <label for="email">Email *</label>
                </div>
                @error('email')
                  <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6">
                <div class="form-floating-custom has-phone-prefix">
                  <span class="phone-prefix-badge">COL +57</span>
                  @php
                    $phoneRaw = old('phone', $usuario->phone ?? '');
                    $phoneDisplay = preg_replace('/^\+?57/', '', $phoneRaw);
                  @endphp
                  <input id="phone" type="text" name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    placeholder=" "
                    value="{{ $phoneDisplay }}">
                  <label for="phone">Teléfono</label>
                </div>
                @error('phone')
                  <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
              </div>
            </div>

            {{-- Rol de acceso --}}
            <div class="mb-4">
              <div class="form-floating-custom has-prefix">
                <i class="fas fa-user-tag input-prefix-icon"></i>
                <select id="role_id" name="role_id"
                  class="form-select @error('role_id') is-invalid @enderror">
                  <option value="">Mantener rol actual</option>
                  @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                      {{ old('role_id', $currentRole?->id) == $role->id ? 'selected' : '' }}>
                      {{ ucfirst($role->name) }}
                    </option>
                  @endforeach
                </select>
                <label for="role_id">Rol de acceso</label>
              </div>
              @error('role_id')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- Estado del usuario --}}
            <div class="mb-4">
              <div class="form-section-title">Estado del usuario</div>
              <div class="status-pill-group">
                <div>
                  <input type="radio" class="btn-check" name="status" id="statusActive"
                    value="active" autocomplete="off" {{ $status === 'active' ? 'checked' : '' }}>
                  <label class="btn-pill btn-pill-success" for="statusActive">
                    <i class="fas fa-check-circle"></i> Activo
                  </label>
                </div>
                <div>
                  <input type="radio" class="btn-check" name="status" id="statusInactive"
                    value="inactive" autocomplete="off" {{ $status === 'inactive' ? 'checked' : '' }}>
                  <label class="btn-pill btn-pill-muted" for="statusInactive">
                    <i class="fas fa-user-slash"></i> Inactivo
                  </label>
                </div>
                <div>
                  <input type="radio" class="btn-check" name="status" id="statusBlocked"
                    value="blocked" autocomplete="off" {{ $status === 'blocked' ? 'checked' : '' }}>
                  <label class="btn-pill btn-pill-danger" for="statusBlocked">
                    <i class="fas fa-ban"></i> Bloqueado
                  </label>
                </div>
              </div>
            </div>

            {{-- Acciones --}}
            <div class="d-flex gap-3 pt-3" style="border-top:1px solid #f1f5f9;">
              <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Guardar cambios
              </button>
              <a href="{{ route('admin.usuarios.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Cancelar
              </a>
            </div>

          </form>
        </div>
      </div>

      {{-- ── Columna sidebar ── --}}
      <div class="col-lg-5 d-flex flex-column gap-4">

        {{-- Información de cuenta --}}
        <div class="edit-panel-card">
          <div class="section-icon-header">
            <div class="section-icon-box orange">
              <i class="fas fa-lock"></i>
            </div>
            <div>
              <h2 class="section-icon-title">Información de cuenta</h2>
              <p class="section-icon-subtitle">Detalles del perfil y su actividad.</p>
            </div>
          </div>

          <div class="account-info-list">
            <div class="account-info-row">
              <span class="label-col">ID</span>
              <span class="value-col">{{ $usuario->id }}</span>
            </div>
            <div class="account-info-row">
              <span class="label-col">Rol</span>
              <span class="value-col orange">{{ $usuario->display_role }}</span>
            </div>
            <div class="account-info-row">
              <span class="label-col">Tipo</span>
              <span class="value-col orange">{{ $usuario->is_employee ? 'Empleado' : 'Invitado' }}</span>
            </div>
            <div class="account-info-row">
              <span class="label-col">Creado</span>
              <span class="value-col">{{ $usuario->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="account-info-row">
              <span class="label-col">Último acceso</span>
              <span class="value-col">{{ $usuario->last_login_at ? $usuario->last_login_at->format('d/m/Y H:i') : 'Nunca' }}</span>
            </div>
            <div class="account-info-row">
              <span class="label-col">IP última</span>
              <span class="value-col">{{ $usuario->last_login_ip ?? '-' }}</span>
            </div>
          </div>
        </div>

        {{-- Seguridad --}}
        <div class="edit-panel-card">
          <div class="section-icon-header">
            <div class="section-icon-box blue">
              <i class="fas fa-shield-alt"></i>
            </div>
            <div>
              <h2 class="section-icon-title">Seguridad</h2>
              <p class="section-icon-subtitle">Controla accesos y contraseñas.</p>
            </div>
          </div>

          <button type="button" class="sidebar-action-btn orange"
            data-bs-toggle="modal" data-bs-target="#changePasswordModal">
            <i class="fas fa-key"></i> Cambiar contraseña
          </button>
          <button type="button" class="sidebar-action-btn red"
            onclick="generateTempPass({{ $usuario->id }})">
            <i class="fas fa-sync-alt"></i> Generar temporal
          </button>
        </div>

        {{-- Actividad --}}
        <div class="edit-panel-card">
          <div class="section-icon-header">
            <div class="section-icon-box teal">
              <i class="fas fa-history"></i>
            </div>
            <div>
              <h2 class="section-icon-title">Actividad</h2>
              <p class="section-icon-subtitle">Revisa historial y sesiones activas.</p>
            </div>
          </div>

          <a href="{{ route('admin.usuarios.activity', $usuario) }}" class="sidebar-action-btn teal">
            <i class="fas fa-list"></i> Ver historial
          </a>
          <a href="{{ route('admin.usuarios.sessions', $usuario) }}" class="sidebar-action-btn gold">
            <i class="fas fa-wifi"></i> Ver sesiones
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- Modal: Cambiar Contraseña --}}
<div class="modal fade" id="changePasswordModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title"><i class="fas fa-key me-2 text-warning"></i> Cambiar contraseña</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.usuarios.update-password', $usuario) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nueva contraseña *</label>
            <input type="password" name="password" class="form-control" required minlength="12">
            <small class="text-muted">Mín. 12 caracteres (mayúscula, minúscula, número, símbolo)</small>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Confirmar contraseña *</label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="12">
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function generateTempPass(userId) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: '¿Generar contraseña temporal?',
      text: "Esto cambiará la contraseña del usuario inmediatamente.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Sí, generar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        executeReset(userId);
      }
    });
  } else {
    if (confirm('¿Generar una nueva contraseña temporal para este usuario?')) {
      executeReset(userId);
    }
  }
}

function executeReset(userId) {
  fetch(`/admin/usuarios/${userId}/resetear-contraseña`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Content-Type': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: '¡Contraseña generada!',
          html: `La nueva contraseña temporal es:<br><br><b style="font-size: 1.5rem; color: #f97316;">${data.temporary_password}</b><br><br>Asegúrate de copiarla.`,
          icon: 'success'
        });
      } else {
        alert(`Contraseña temporal: ${data.temporary_password}\n\nAsegúrate de copiar esta contraseña antes de cerrar el diálogo.`);
      }
    } else {
      if (typeof Swal !== 'undefined') Swal.fire('Error', data.message || 'No se pudo generar.', 'error');
      else alert(data.message || 'No se pudo generar la contraseña temporal.');
    }
  })
  .catch(() => {
    if (typeof Swal !== 'undefined') Swal.fire('Error', 'Ocurrió un error en la conexión.', 'error');
    else alert('Ocurrió un error al generar la contraseña temporal.');
  });
}
</script>
@endsection
