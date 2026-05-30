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
        <span class="badge rounded-pill role-badge-mantenimiento">Mantenimiento</span>
      </div>
      <button type="button" id="btnNuevoEmpleado" class="btn btn-success shadow-sm">
        <i class="bi bi-person-plus"></i> <span class="d-none d-sm-inline">Nuevo empleado</span>
      </button>
    </div>
  </div>
  <div class="alert alert-info py-2 px-3 small mb-3">
    Los roles disponibles para creación son: <strong>Reservas</strong>, <strong>Minibar</strong>, <strong>Recepción</strong> y <strong>Mantenimiento</strong>. Usa el selector para reasignar rápidamente el acceso al panel correspondiente.
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
              <input type="email" name="email" class="form-control" placeholder="correo@example.com" pattern="^[^\s@]+@(gmail\.com|hotmail\.com)$" title="Solo se permiten correos @gmail.com o @hotmail.com" required>
            </div>
            <small class="text-muted d-block mt-1">Email corporativo único</small>
          </div>
        </div>

        <!-- Fila 2: Teléfono, Contraseña, Confirmar -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Teléfono *</label>
            <input type="text" name="phone" class="form-control form-control-lg" placeholder="+57 3xx xxx xxxx" inputmode="tel" minlength="10" maxlength="16" data-phone-sanitize="true" pattern="^(3\d{9}|(?:\+57|57)3\d{9}|\+\d{8,15}|\d{8,15})$" title="Si inicia en 3 debe tener 10 dígitos (Colombia). También se acepta formato internacional válido." value="{{ old('phone') }}" required>
            <small class="text-muted d-block mt-1">Contacto del empleado</small>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Contraseña *</label>
            <div class="input-group input-group-lg">
              <input type="password" name="password" id="passwordInline" minlength="12" class="form-control" placeholder="••••••••" title="Mínimo 12 caracteres con mayúscula, minúscula, número y símbolo" required>
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
                <label class="role-card-custom p-3 border rounded-2 d-flex align-items-center gap-3 h-100" style="cursor:pointer;transition:all .2s">
                  <input type="radio" name="role_id" value="{{ $id }}" class="form-check-input" style="width:20px;height:20px" required @checked(old('role_id') == $id)>
                  <div>
                    <div class="fw-bold text-capitalize" style="font-size:1rem">
                      @if($rname === 'reservas')
                        <i class="bi bi-calendar-event me-2 text-warning"></i>Reservas
                      @elseif($rname === 'recepcion')
                        <i class="bi bi-person-badge me-2 text-primary"></i>Recepción
                      @elseif($rname === 'mantenimiento')
                        <i class="bi bi-tools me-2 text-danger"></i>Mantenimiento
                      @else
                        <i class="bi bi-shop me-2 text-success"></i>Minibar
                      @endif
                    </div>
                    <small class="text-muted d-block mt-1">
                      @if($rname === 'reservas')
                        Gestión de habitaciones y reservas
                      @elseif($rname === 'recepcion')
                        Gestión de check-in, folios y atención al huésped
                      @elseif($rname === 'mantenimiento')
                        Gestión de órdenes de mantenimiento y reparaciones
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
                @if(auth()->user()->hasRole('administrador') && $u->id !== auth()->id())
                  <form action="{{ route('admin.empleados.destroy', $u) }}" method="POST"
                    data-confirm-message="¿Estás seguro de eliminar a {{ $u->name }}?">
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

<link rel="stylesheet" href="{{ asset('css/blade/admin/empleados/index--style1.css') }}">

<script src="{{ asset('js/admin-empleados-form.js') }}"></script>
@endsection


