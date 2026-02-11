@extends('layouts.app')

@section('content')
<div class="container-fluid empleados-page">
  <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <h2 class="mb-0">Empleados</h2>
    <div class="d-flex align-items-center gap-2">
      <div class="role-legend d-flex gap-2 me-2">
        <span class="badge rounded-pill role-badge-reservas">Reservas</span>
        <span class="badge rounded-pill role-badge-minibar">Minibar</span>
        <span class="badge rounded-pill role-badge-recepcion">Recepción</span>
      </div>
      <button type="button" id="btnNuevoEmpleado" class="btn btn-success shadow-sm">
        <i class="bi bi-person-plus"></i> <span class="d-none d-sm-inline">Nuevo empleado</span>
      </button>
    </div>
  </div>
  <div class="alert alert-info py-2 px-3 small mb-3">
    Los roles disponibles son fijos: <strong>Reservas</strong>, <strong>Minibar</strong> y <strong>Recepción</strong>. Usa el selector para reasignar rápidamente el acceso al panel correspondiente.
  </div>

  <!-- Formulario inline oculto -->
  <div id="empleadoFormWrapper" class="card shadow-sm border-0 mb-4 d-none empleado-form-card">
    <div class="card-header bg-gradient py-3 d-flex justify-content-between align-items-center">
      <div>
        <h5 class="mb-1"><i class="bi bi-person-plus me-2"></i>Nuevo empleado</h5>
        <small class="text-muted">Completa los datos para crear un nuevo usuario</small>
      </div>
      <button type="button" id="btnCerrarForm" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-x-lg"></i> Cerrar
      </button>
    </div>
    <div class="card-body p-4">
      <form action="{{ route('admin.empleados.store') }}" method="POST" autocomplete="off" id="empleadoCreateForm">
        @csrf

        <!-- Fila 1: Nombre, Apellido, Email -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Nombre *</label>
            <input type="text" name="name" class="form-control form-control-lg" placeholder="Ej: Carlos" required>
            <small class="text-muted d-block mt-1">Nombre del empleado</small>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Apellido *</label>
            <input type="text" name="last_name" class="form-control form-control-lg" placeholder="Ej: Pérez" required>
            <small class="text-muted d-block mt-1">Apellido del empleado</small>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Email *</label>
            <div class="input-group input-group-lg">
              <span class="input-group-text">@</span>
              <input type="email" name="email" class="form-control" placeholder="correo@example.com" required>
            </div>
            <small class="text-muted d-block mt-1">Email corporativo único</small>
          </div>
        </div>

        <!-- Fila 2: Teléfono, Contraseña, Confirmar -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Teléfono *</label>
            <input type="text" name="phone" class="form-control form-control-lg" placeholder="+57 3xx xxx xxxx" required>
            <small class="text-muted d-block mt-1">Contacto del empleado</small>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Contraseña *</label>
            <div class="input-group input-group-lg">
              <input type="password" name="password" id="passwordInline" minlength="8" class="form-control" placeholder="••••••••" required>
              <button class="btn btn-outline-secondary" type="button" id="togglePassInline" title="Ver/ocultar">
                <i class="bi bi-eye"></i>
              </button>
              <button class="btn btn-outline-primary" type="button" id="genPassInline" title="Generar segura">
                <i class="bi bi-shuffle me-1"></i>Generar
              </button>
            </div>
            <div class="progress mt-2" style="height:5px">
              <div id="passBarInline" class="progress-bar bg-danger" style="width:0%"></div>
            </div>
            <small class="text-muted d-block mt-1">Mín. 8 caracteres</small>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Confirmar *</label>
            <input type="password" name="password_confirmation" minlength="8" class="form-control form-control-lg" placeholder="••••••••" required>
            <small class="text-muted d-block mt-1">Repite la contraseña</small>
          </div>
        </div>

        <!-- Acceso a Panel -->
        <div class="mb-4">
          <label class="form-label fw-semibold mb-3">Acceso a panel *</label>
          <div class="row g-3">
            @foreach($rolesCreate as $id => $rname)
              <div class="col-md-6">
                <label class="role-card-custom p-3 border rounded-2 d-flex align-items-center gap-3" style="cursor:pointer;transition:all .2s">
                  <input type="radio" name="role_id" value="{{ $id }}" class="form-check-input" style="width:20px;height:20px" required>
                  <div>
                    <div class="fw-bold text-capitalize" style="font-size:1rem">
                      @if($rname === 'reservas')
                        <i class="bi bi-calendar-event me-2 text-warning"></i>Reservas
                      @elseif($rname === 'recepcion')
                        <i class="bi bi-person-badge me-2 text-primary"></i>Recepción
                      @else
                        <i class="bi bi-shop me-2 text-success"></i>Minibar
                      @endif
                    </div>
                    <small class="text-muted d-block">
                      @if($rname === 'reservas')
                        Gestión de habitaciones y reservas
                      @elseif($rname === 'recepcion')
                        Gestión de check-in, folios y atención al huésped
                      @else
                        Gestión de productos y ventas
                      @endif
                    </small>
                  </div>
                </label>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Botones de Acción -->
        <div class="d-flex gap-2 pt-3 border-top">
          <button type="submit" class="btn btn-success btn-lg px-4">
            <i class="bi bi-check-circle me-2"></i>Guardar empleado
          </button>
          <button type="button" class="btn btn-outline-secondary btn-lg px-4" id="btnResetEmpleado">
            <i class="bi bi-arrow-counterclockwise me-2"></i>Limpiar
          </button>
        </div>
      </form>
    </div>
  </div>

  <div id="empleadosTableWrapper" class="card shadow-sm border-0">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 empleados-table">
          <thead>
            <tr class="text-uppercase small text-muted">
              <th class="px-3" style="width:70px">ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Rol actual</th>
              <th class="text-end" style="width:320px">Reasignar acceso</th>
              <th class="text-center" style="width:100px">Acciones</th>
            </tr>
          </thead>
          <tbody>
          @forelse($empleados as $u)
            <tr>
              <td class="px-3 fw-semibold">{{ $u->id }}</td>
              <td>{{ $u->name }}{{ $u->last_name ? ' '.$u->last_name : '' }}</td>
              <td class="text-muted">{{ $u->email }}</td>
              <td class="roles-cell">
                @php($names = $u->roles->pluck('name'))
                @if($names->isEmpty())
                  <span class="text-muted">—</span>
                @else
                  @foreach($names as $r)
                    <span class="badge rounded-pill role-badge-{{ $r }} me-1">{{ ucfirst($r) }}</span>
                  @endforeach
                @endif
              </td>
              <td class="text-end">
                <form action="{{ route('admin.empleados.roles.assign', $u) }}" method="POST" class="d-inline-flex gap-2 align-items-center">
                  @csrf
                  <select name="role" class="form-select form-select-sm w-auto" required>
                    <option value="" disabled selected>Elegir…</option>
                    @foreach($roles as $rname)
                      <option value="{{ $rname }}">{{ ucfirst($rname) }}</option>
                    @endforeach
                  </select>
                  <button class="btn btn-sm btn-warning shadow-sm">
                    <i class="bi bi-arrow-repeat"></i> Asignar
                  </button>
                </form>
              </td>
              <td class="text-center">
                @if($u->id !== auth()->id())
                  <form action="{{ route('admin.empleados.destroy', $u) }}" method="POST"
                        onsubmit="return confirm('¿Estás seguro de eliminar a {{ $u->name }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Eliminar empleado">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                @else
                  <span class="text-muted small">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">No hay empleados registrados.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if ($empleados instanceof \Illuminate\Contracts\Pagination\Paginator)
      <div class="card-footer bg-white py-2">
        {{ $empleados->links() }}
      </div>
    @endif
  </div>

  @if(session('success'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
      <div class="toast align-items-center text-bg-success show" role="alert">
        <div class="d-flex">
          <div class="toast-body">{{ session('success') }}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger mt-3">
      <ul class="mb-0 small">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif
</div>

<style>
  .empleados-page .role-badge-reservas { background: linear-gradient(135deg,#FFE0B2,#FFB74D); color:#000 !important; font-weight:600; }
  .empleados-page .role-badge-minibar { background: linear-gradient(135deg,#C8E6C9,#81C784); color:#000 !important; font-weight:600; }
  .empleados-page .role-badge-recepcion { background: linear-gradient(135deg,#D1ECFF,#90E0EF); color:#000 !important; font-weight:600; }
  .empleados-page .role-badge-administrador { background: linear-gradient(135deg,#E0F2FF,#90CAF9); color:#000 !important; font-weight:600; }
  .empleados-page .role-badge-invitado { background: linear-gradient(135deg,#F3E5F5,#CE93D8); color:#000 !important; font-weight:600; }
  .empleados-page .badge[class*="role-badge-"] { color:#000 !important; }
  .empleados-page td.roles-cell .badge { color:#000 !important; }
  .empleados-table thead tr { background: #f8f9fa; }
  .empleados-table tbody tr:hover { background:#fdf7ed; }
  .empleados-page .card { overflow:hidden; }
  .empleados-page .card-body { padding:0; }
  .empleados-page .table > :not(caption) > * > * { padding:0.85rem 0.75rem; }

  /* Estilos mejorados del formulario -->
  .empleado-form-card {
    animation: slideDown 0.3s ease-out;
  }
  @keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .empleado-form-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white;
    border-bottom: 3px solid #667eea;
  }
  .empleado-form-card .card-header h5 {
    color: white;
    margin: 0;
  }
  .empleado-form-card .card-header small {
    color: rgba(255,255,255,0.85) !important;
  }
  .empleado-form-card .btn-outline-secondary {
    color: white;
    border-color: rgba(255,255,255,0.3);
  }
  .empleado-form-card .btn-outline-secondary:hover {
    background: rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.5);
    color: white;
  }
  .empleado-form-card .form-label {
    color: #333;
    margin-bottom: 8px;
  }
  .empleado-form-card .form-control,
  .empleado-form-card .form-control-lg {
    border: 1px solid #ddd;
    border-radius: 6px;
  }
  .empleado-form-card .form-control:focus,
  .empleado-form-card .form-control-lg:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
  }
  .role-card-custom {
    border: 2px solid #e9ecef !important;
    background: #f8f9fa;
    transition: all 0.2s;
  }
  .role-card-custom:hover {
    border-color: #667eea !important;
    background: #f0f4ff;
  }
  .role-card-custom input[type="radio"]:checked + div ~ * {
    border-color: #667eea !important;
    background: #e8eef9 !important;
  }
  .empleado-form-card .progress-bar {
    background: linear-gradient(90deg, #ef4444, #f59e0b, #22c55e) !important;
    transition: width 0.3s;
  }
  .empleado-form-card .btn-success {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    font-weight: 600;
  }
  .empleado-form-card .btn-success:hover {
    background: linear-gradient(135deg, #5568d3 0%, #6a3f91 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  }

  @media (max-width: 992px) {
    .empleados-page .empleados-table thead tr th:nth-child(3),
    .empleados-page .empleados-table tbody tr td:nth-child(3) { display:none; }
  }
</style>

<script src="{{ asset('js/admin-empleados-form.js') }}"></script>
@endsection
