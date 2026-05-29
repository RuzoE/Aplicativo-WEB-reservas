@extends('layouts.app')

@section('content')
<div class="container-fluid usuarios-page">
  <!-- Header -->
  <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h2 class="mb-1"><i class="bi bi-shield-lock me-2"></i>Gestión de Usuarios</h2>
      <p class="text-muted mb-0">Los usuarios se crean automáticamente al registrar empleados o clientes. Aquí puedes gestionar su acceso y seguridad.</p>
    </div>
  </div>

  <!-- Filtros -->
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('admin.usuarios.index') }}" class="row g-3">
        <div class="col-md-4">
          <input type="text" name="search" class="form-control form-control-lg" placeholder="Buscar por nombre, email..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
          <select name="role" class="form-select form-select-lg">
            <option value="">Todos los roles</option>
            @foreach($roles as $role)
              <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="status" class="form-select form-select-lg">
            <option value="">Todos los estados</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
            <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Bloqueado</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-search"></i> Filtrar
          </button>
          <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-counterclockwise"></i>
          </a>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabla de usuarios -->
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="px-3" style="width: 60px;">ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Rol</th>
              <th>Estado</th>
              <th>Último acceso</th>
              <th class="text-center" style="width: 120px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($usuarios as $usuario)
              <tr>
                <td class="px-3 text-muted small">{{ $usuario->id }}</td>
                <td class="fw-semibold">
                  {{ $usuario->name }} {{ $usuario->last_name }}
                  @if($usuario->is_admin)
                    <span class="badge bg-danger ms-1">Admin</span>
                  @endif
                </td>
                <td>
                  <small class="text-muted">{{ $usuario->email }}</small>
                </td>
                <td>
                  <span class="badge role-badge-{{ strtolower($usuario->display_role) }} text-capitalize">{{ $usuario->display_role }}</span>
                </td>
                <td>
                  <span class="badge bg-{{ $usuario->status_color }}">{{ $usuario->status_label }}</span>
                </td>
                <td>
                  <small class="text-muted">{{ $usuario->last_login_at ? $usuario->last_login_formatted : 'Nunca' }}</small>
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-outline-primary" title="Editar">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="{{ route('admin.usuarios.activity', $usuario) }}" class="btn btn-outline-info" title="Actividad">
                      <i class="bi bi-clock-history"></i>
                    </a>
                    <a href="{{ route('admin.usuarios.sessions', $usuario) }}" class="btn btn-outline-warning" title="Sesiones">
                      <i class="bi bi-wifi"></i>
                    </a>
                    <button type="button" class="btn btn-outline-danger delete-btn" data-id="{{ $usuario->id }}" title="Eliminar">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5">
                  <p class="text-muted mb-0">No hay usuarios registrados</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Paginación -->
  <div class="d-flex justify-content-center">
    {{ $usuarios->links() }}
  </div>

  <!-- Actividades recientes del sistema -->
  @if($recentActivities->count() > 0)
  <div class="card shadow-sm border-0 mt-5">
    <div class="card-header bg-light border-bottom">
      <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Actividades recientes del sistema</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Usuario</th>
              <th>Acción</th>
              <th>Descripción</th>
              <th>Fecha y hora</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentActivities as $activity)
              <tr>
                <td class="small">{{ $activity->user->name ?? 'Sistema' }}</td>
                <td>
                  <span class="badge bg-secondary text-capitalize">{{ str_replace('_', ' ', $activity->action) }}</span>
                </td>
                <td class="small text-muted">{{ $activity->description }}</td>
                <td class="small text-muted">{{ $activity->created_at->diffForHumans() }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</div>

<!-- Formulario oculto para eliminar -->
<form id="deleteForm" action="" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>

<script>
document.querySelectorAll('.delete-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
      document.getElementById('deleteForm').action = `/admin/usuarios/${this.dataset.id}`;
      document.getElementById('deleteForm').submit();
    }
  });
});
</script>

<style>
  .role-badge-reservas { background-color: #FFC107 !important; color: #000 !important; }
  .role-badge-minibar { background-color: #28A745 !important; }
  .role-badge-recepcion { background-color: #007BFF !important; }
  .role-badge-mantenimiento { background-color: #DC3545 !important; }

  .usuarios-page h2 {
    color: #212529;
    font-weight: 600;
  }

  .btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
  }

  table thead {
    font-weight: 600;
    color: #495057;
    text-transform: uppercase;
    font-size: 0.85rem;
  }
</style>
@endsection
